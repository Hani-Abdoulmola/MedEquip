<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Product Catalog Seeder
 *
 * Seeds the product catalog with dummy data including products,
 * product-supplier relationships with prices and stock quantities.
 */
class ProductCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding product catalog with dummy data...');
        $this->command->info('');

        try {
            // Check prerequisites
            if (!Manufacturer::exists()) {
                $this->command->error('❌ No manufacturers found. Please run ManufacturerSeeder first.');
                $this->command->info('   Run: php artisan db:seed --class=ManufacturerSeeder');
                return;
            }

            $manufacturerCount = Manufacturer::count();
            $this->command->info("   ✓ Found {$manufacturerCount} manufacturers");

            if (!ProductCategory::exists()) {
                $this->command->error('❌ No product categories found. Please run ProductCategorySeeder first.');
                $this->command->info('   Run: php artisan db:seed --class=ProductCategorySeeder');
                return;
            }

            $categoryCount = ProductCategory::count();
            $this->command->info("   ✓ Found {$categoryCount} product categories");
            $this->command->info('');

            // Get admin user for created_by/updated_by fields
            $adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first();
            if (!$adminUser) {
                $adminUser = User::first();
            }

            // Create suppliers if they don't exist
            if (!Supplier::exists()) {
                $this->command->info("   👥 Creating suppliers...");

                try {
                    // Get or create supplier user type
                    $supplierType = UserType::where('slug', 'supplier')->first();
                    if (!$supplierType) {
                        $supplierType = UserType::create([
                            'name' => 'Supplier',
                            'slug' => 'supplier',
                            'is_active' => true,
                        ]);
                        $this->command->info("   ✓ Created Supplier user type");
                    }

                    // Create users first, then suppliers with user_id
                    $faker = \Faker\Factory::create();
                    $suppliers = collect();
                    $userCount = 0;

                    // Expanded name pools for more variety
                    $nameAdjectives = ['المتقدمة', 'الحديثة', 'العالمية', 'المتميزة', 'المتطورة', 'الرائدة', 'النموذجية', 'المثالية', 'المبتكرة', 'الرائعة', 'المتخصصة', 'الاحترافية'];
                    $nameTypes = ['الطبية', 'الصحية', 'العلاجية', 'التشخيصية'];
                    $suffixes = ['للأجهزة الطبية', 'للتجهيزات الطبية', 'للمعدات الطبية', 'للخدمات الطبية', 'للأدوات الطبية', 'للتقنيات الطبية', 'للمستلزمات الطبية', 'للأنظمة الطبية'];

                    for ($i = 0; $i < 20; $i++) {
                        try {
                            // Generate unique supplier data with more variety
                            $prefix = $faker->randomElement(['شركة', 'مؤسسة', 'مجموعة']);
                            $nameType = $faker->randomElement($nameTypes);
                            $adjective = $faker->randomElement($nameAdjectives);
                            $suffix = $faker->randomElement($suffixes);

                            // Create unique company name
                            $baseName = $prefix . ' ' . $nameType . ' ' . $adjective . ' ' . $suffix;

                            // Check if name exists, if so add unique identifier
                            $companyName = $baseName;
                            $attempts = 0;
                            while (Supplier::where('company_name', $companyName)->exists() && $attempts < 10) {
                                $companyName = $baseName . ' - ' . $faker->unique()->numberBetween(1, 999);
                                $attempts++;
                            }

                            $contactEmail = $faker->unique()->companyEmail();
                            $contactPhone = '09' . $faker->unique()->numerify('#######');

                            // Create user first
                            $user = User::create([
                                'user_type_id' => $supplierType->id,
                                'name' => $companyName,
                                'email' => $contactEmail,
                                'phone' => $contactPhone,
                                'password' => Hash::make('1234567890'),
                                'status' => 'active',
                            ]);

                            $user->assignRole('Supplier');
                            $userCount++;

                            // Create supplier with user_id
                            $supplier = Supplier::create([
                                'user_id' => $user->id,
                                'company_name' => $companyName,
                                'commercial_register' => 'CR-' . $faker->unique()->numerify('######'),
                                'tax_number' => 'TAX-' . $faker->unique()->numerify('######'),
                                'country' => 'ليبيا',
                                'city' => $faker->randomElement(['طرابلس', 'بنغازي', 'مصراتة', 'سبها', 'زليتن', 'البيضاء', 'أجدابيا', 'درنة', 'طبرق', 'الخمس', 'غريان', 'يفرن', 'زوارة', 'صبراتة']),
                                'address' => $faker->address(),
                                'contact_email' => $contactEmail,
                                'contact_phone' => $contactPhone,
                                'is_verified' => true,
                                'verified_at' => now(),
                                'is_active' => true,
                                'created_by' => $adminUser?->id,
                                'updated_by' => $adminUser?->id,
                            ]);

                            $suppliers->push($supplier);
                        } catch (\Exception $e) {
                            $this->command->warn("   ⚠️  Failed to create supplier {$i}: {$e->getMessage()}");
                        }
                    }

                    $this->command->info("   ✓ Created {$userCount} user accounts");
                    $this->command->info("   ✓ Created {$suppliers->count()} supplier records");
                    $this->command->info("   ✅ Total suppliers created: {$suppliers->count()}");
                } catch (\Exception $e) {
                    $this->command->error("   ❌ Failed to create suppliers: {$e->getMessage()}");
                    throw $e;
                }
            } else {
                $supplierCount = Supplier::count();
                $this->command->info("   ✓ Found {$supplierCount} existing suppliers");
            }

            if (!$adminUser) {
                $this->command->error('❌ No admin user found. Please create an admin user first.');
                return;
            }

            $this->command->info("   ✓ Using admin user: {$adminUser->name}");
            $this->command->info('');

            DB::transaction(function () use ($adminUser) {
                // Get all active categories and manufacturers
                $categories = ProductCategory::where('is_active', true)->get();
                $manufacturers = Manufacturer::where('is_active', true)->get();
                $suppliers = Supplier::where('is_verified', true)->get();

                if ($categories->isEmpty()) {
                    $this->command->error('❌ No active categories found.');
                    throw new \Exception('No active categories available');
                }

                if ($manufacturers->isEmpty()) {
                    $this->command->error('❌ No active manufacturers found.');
                    throw new \Exception('No active manufacturers available');
                }

                if ($suppliers->isEmpty()) {
                    $this->command->error('❌ No verified suppliers found.');
                    throw new \Exception('No verified suppliers available');
                }

                $this->command->info("   ✓ Found {$categories->count()} active categories");
                $this->command->info("   ✓ Found {$manufacturers->count()} active manufacturers");
                $this->command->info("   ✓ Found {$suppliers->count()} verified suppliers");
                $this->command->info('');

                $this->command->info("   📦 Creating products...");

                $faker = \Faker\Factory::create();

                // Create products
                $products = Product::factory()
                    ->count(200) // Create 200 products
                    ->approved() // Most products should be approved
                    ->active()
                    ->create([
                        'created_by' => $adminUser->id,
                        'updated_by' => $adminUser->id,
                    ]);

                if ($products->isEmpty()) {
                    throw new \Exception('Failed to create products');
                }

                $this->command->info("   ✅ Created {$products->count()} products");

            // Attach products to suppliers with prices and stock
            $this->command->info("   🔗 Attaching products to suppliers...");

            $attachedCount = 0;
            foreach ($products as $product) {
                // Each product should be available from 1-5 random suppliers
                $productSuppliers = $suppliers->random(rand(1, 5));

                foreach ($productSuppliers as $supplier) {
                    // Generate realistic price (in Libyan Dinar)
                    $basePrice = $this->generateRealisticPrice($product);
                    $price = $basePrice + ($faker->numberBetween(-10, 10) * 100); // Add some variation

                    // Generate stock quantity
                    $stockQuantity = $faker->randomElement([
                        $faker->numberBetween(0, 0), // Out of stock (20%)
                        $faker->numberBetween(1, 10), // Low stock (30%)
                        $faker->numberBetween(11, 50), // Medium stock (30%)
                        $faker->numberBetween(51, 200), // High stock (20%)
                    ]);

                    // Generate lead time (in days)
                    $leadTime = $faker->randomElement([
                        $faker->numberBetween(1, 7), // 1-7 days (40%)
                        $faker->numberBetween(8, 14), // 8-14 days (30%)
                        $faker->numberBetween(15, 30), // 15-30 days (20%)
                        $faker->numberBetween(31, 60), // 31-60 days (10%)
                    ]);

                    // Generate warranty period
                    $warranty = $faker->randomElement([
                        '6 months',
                        '1 year',
                        '2 years',
                        '3 years',
                        '5 years',
                    ]);

                    // Determine status based on stock
                    $status = $stockQuantity > 0 ? 'available' : 'out_of_stock';

                    // Attach product to supplier
                    $product->suppliers()->attach($supplier->id, [
                        'price' => $price,
                        'stock_quantity' => $stockQuantity,
                        'lead_time' => $leadTime,
                        'warranty' => $warranty,
                        'status' => $status,
                        'notes' => $faker->optional(0.3)->sentence(),
                    ]);

                    $attachedCount++;
                }
            }

                $this->command->info("   ✅ Attached {$attachedCount} product-supplier relationships");

                // Validate data was created
                $totalProducts = Product::count();
                $activeProducts = Product::where('is_active', true)->count();
                $approvedProducts = Product::where('review_status', Product::REVIEW_APPROVED)->count();
                $productsWithSuppliers = Product::whereHas('suppliers')->count();
                $totalOffers = DB::table('product_supplier')->count();

                if ($totalProducts === 0) {
                    throw new \Exception('Products were not created successfully');
                }

                if ($totalOffers === 0) {
                    throw new \Exception('Product-supplier relationships were not created successfully');
                }

                $this->command->info('');
                $this->command->info('📊 Product Catalog Summary:');
                $this->command->info("   • Total Products: {$totalProducts}");
                $this->command->info("   • Active Products: {$activeProducts}");
                $this->command->info("   • Approved Products: {$approvedProducts}");
                $this->command->info("   • Products with Suppliers: {$productsWithSuppliers}");
                $this->command->info("   • Total Supplier Offers: {$totalOffers}");
                $this->command->info('');
                $this->command->info('✅ Product catalog seeded successfully!');
            });

        } catch (\Exception $e) {
            $this->command->error('');
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            $this->command->error('   Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Generate realistic price based on product type
     */
    private function generateRealisticPrice(Product $product): float
    {
        $faker = \Faker\Factory::create();
        $name = strtolower($product->name);

        // High-end equipment (50,000 - 500,000 LYD)
        if (str_contains($name, 'mri') ||
            str_contains($name, 'ct scanner') ||
            str_contains($name, 'dialysis')) {
            return $faker->numberBetween(50000, 500000);
        }

        // Medium-high equipment (10,000 - 100,000 LYD)
        if (str_contains($name, 'x-ray') ||
            str_contains($name, 'ultrasound') ||
            str_contains($name, 'ventilator') ||
            str_contains($name, 'anesthesia') ||
            str_contains($name, 'c-arm')) {
            return $faker->numberBetween(10000, 100000);
        }

        // Medium equipment (2,000 - 20,000 LYD)
        if (str_contains($name, 'monitor') ||
            str_contains($name, 'defibrillator') ||
            str_contains($name, 'endoscope') ||
            str_contains($name, 'surgical light') ||
            str_contains($name, 'operating table')) {
            return $faker->numberBetween(2000, 20000);
        }

        // Lower-medium equipment (500 - 5,000 LYD)
        if (str_contains($name, 'ecg') ||
            str_contains($name, 'infusion pump') ||
            str_contains($name, 'autoclave') ||
            str_contains($name, 'centrifuge') ||
            str_contains($name, 'microscope')) {
            return $faker->numberBetween(500, 5000);
        }

        // Low-end equipment (50 - 1,000 LYD)
        if (str_contains($name, 'blood pressure') ||
            str_contains($name, 'pulse oximeter') ||
            str_contains($name, 'thermometer')) {
            return $faker->numberBetween(50, 1000);
        }

        // Default range (100 - 10,000 LYD)
        return $faker->numberBetween(100, 10000);
    }
}
