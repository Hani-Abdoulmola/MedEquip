<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AddProductPlaceholderImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:add-placeholder-images {--force : Force add images even if products already have images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add placeholder images to products that don\'t have images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Adding placeholder images to products...');
        $this->info('');

        $force = $this->option('force');
        
        // Get products without images or all products if force is used
        if ($force) {
            $products = Product::all();
            $this->warn('⚠️  Force mode: Adding images to all products (may create duplicates)');
        } else {
            $products = Product::whereDoesntHave('media', function ($query) {
                $query->where('collection_name', 'product_images');
            })->get();
        }

        if ($products->isEmpty()) {
            $this->info('✅ All products already have images!');
            return 0;
        }

        $this->info("Found {$products->count()} products without images");
        $this->info('');

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($products as $product) {
            try {
                // Extract English name only for cleaner text
                $nameParts = explode(' / ', $product->name);
                $productNameShort = substr($nameParts[0] ?? $product->name, 0, 25);
                
                // Use picsum.photos which is more reliable
                $width = 800;
                $height = 600;
                
                // Add 1-2 images per product
                $numImages = rand(1, 2);
                $addedImages = 0;
                
                for ($i = 0; $i < $numImages; $i++) {
                    try {
                        // Use picsum.photos with unique seed based on product ID
                        $imageUrl = "https://picsum.photos/seed/{$product->id}{$i}/{$width}/{$height}";
                        
                        // Download image first using HTTP client
                        $response = Http::timeout(10)->get($imageUrl);
                        
                        if ($response->successful()) {
                            // Save to temporary file
                            $tempPath = sys_get_temp_dir() . '/product_' . $product->id . '_' . $i . '_' . time() . '.jpg';
                            file_put_contents($tempPath, $response->body());
                            
                            // Add to media collection
                            $product->addMedia($tempPath)
                                ->withCustomProperties([
                                    'is_placeholder' => true,
                                    'order' => $i + 1,
                                ])
                                ->toMediaCollection('product_images');
                            
                            // Clean up temp file
                            @unlink($tempPath);
                            $addedImages++;
                        } else {
                            // Fallback: create local placeholder
                            $this->createLocalPlaceholderImage($product, $i + 1, $width, $height, $productNameShort);
                            $addedImages++;
                        }
                    } catch (\Exception $urlException) {
                        // Fallback: create local placeholder
                        try {
                            $this->createLocalPlaceholderImage($product, $i + 1, $width, $height, $productNameShort);
                            $addedImages++;
                        } catch (\Exception $localException) {
                            // Both methods failed, skip this image
                            Log::warning("Failed to add image for product {$product->id}, image {$i}: " . $urlException->getMessage());
                        }
                    }
                }
                
                if ($addedImages > 0) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (\Exception $e) {
                $failCount++;
                Log::warning("Failed to add placeholder image for product {$product->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('');
        $this->info("✅ Successfully added images to {$successCount} products");
        
        if ($failCount > 0) {
            $this->warn("⚠️  Failed to add images to {$failCount} products (check logs for details)");
        }

        return 0;
    }

    /**
     * Create a local placeholder image using GD library
     */
    private function createLocalPlaceholderImage(Product $product, int $order, int $width, int $height, string $text): void
    {
        try {
            // Check if GD is available
            if (!extension_loaded('gd')) {
                throw new \Exception('GD extension is not available');
            }

            // Create image
            $image = imagecreatetruecolor($width, $height);
            
            // Colors
            $bgColor = imagecolorallocate($image, 0, 105, 175); // Medical blue #0069af
            $textColor = imagecolorallocate($image, 255, 255, 255); // White
            
            // Fill background
            imagefill($image, 0, 0, $bgColor);
            
            // Add text (simplified - no font loading for now)
            // For better text rendering, you'd need to load a font file
            // For now, we'll just create a colored rectangle
            imagestring($image, 5, ($width / 2) - (strlen($text) * 5), ($height / 2) - 10, $text, $textColor);
            
            // Save to temporary file
            $tempPath = sys_get_temp_dir() . '/product_' . $product->id . '_' . $order . '.png';
            imagepng($image, $tempPath);
            imagedestroy($image);
            
            // Add to media collection
            $product->addMedia($tempPath)
                ->withCustomProperties([
                    'is_placeholder' => true,
                    'order' => $order,
                ])
                ->toMediaCollection('product_images');
            
            // Clean up temp file
            @unlink($tempPath);
        } catch (\Exception $e) {
            Log::warning("Failed to create local placeholder for product {$product->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
