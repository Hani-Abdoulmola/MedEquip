<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RemoveProductPlaceholderImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:remove-placeholder-images {--all : Remove all images, not just placeholders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove placeholder images from products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️  Removing placeholder images from products...');
        $this->info('');

        $removeAll = $this->option('all');
        
        if ($removeAll) {
            $this->warn('⚠️  Removing ALL images from all products!');
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Get products with images
        $products = Product::whereHas('media', function ($query) use ($removeAll) {
            $query->where('collection_name', 'product_images');
            if (!$removeAll) {
                $query->where('custom_properties->is_placeholder', true);
            }
        })->get();

        if ($products->isEmpty()) {
            $this->info('✅ No products with ' . ($removeAll ? 'images' : 'placeholder images') . ' found!');
            return 0;
        }

        $this->info("Found {$products->count()} products with " . ($removeAll ? 'images' : 'placeholder images'));
        $this->info('');

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $removedCount = 0;
        $totalImagesRemoved = 0;

        foreach ($products as $product) {
            try {
                if ($removeAll) {
                    // Remove all images
                    $images = $product->getMedia('product_images');
                    $totalImagesRemoved += $images->count();
                    $product->clearMediaCollection('product_images');
                } else {
                    // Remove only placeholder images
                    $placeholderImages = $product->getMedia('product_images')
                        ->filter(function ($media) {
                            return $media->getCustomProperty('is_placeholder') === true;
                        });
                    
                    $totalImagesRemoved += $placeholderImages->count();
                    
                    foreach ($placeholderImages as $image) {
                        $image->delete();
                    }
                }
                
                $removedCount++;
            } catch (\Exception $e) {
                $this->warn("\n⚠️  Failed to remove images for product {$product->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('');
        $this->info("✅ Successfully removed images from {$removedCount} products");
        $this->info("   Total images removed: {$totalImagesRemoved}");

        return 0;
    }
}
