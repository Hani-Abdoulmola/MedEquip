<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyFactoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:factory-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify if factory-generated data (suppliers, products) exists in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verifying Factory Data...');
        $this->info('');

        // Check prerequisites
        $this->checkPrerequisites();

        // Check suppliers
        $this->checkSuppliers();

        // Check products
        $this->checkProducts();

        // Check relationships
        $this->checkRelationships();

        $this->info('');
        $this->info('✅ Verification complete!');
    }

    /**
     * Check prerequisites (categories, manufacturers)
     */
    private function checkPrerequisites(): void
    {
        $this->info('📋 Checking Prerequisites:');
        
        $categories = \App\Models\ProductCategory::count();
        $activeCategories = \App\Models\ProductCategory::where('is_active', true)->count();
        $manufacturers = \App\Models\Manufacturer::count();
        $activeManufacturers = \App\Models\Manufacturer::where('is_active', true)->count();

        if ($categories > 0) {
            $this->info("   ✓ Product Categories: {$categories} total ({$activeCategories} active)");
        } else {
            $this->error('   ❌ No product categories found');
            $this->warn('      Run: php artisan db:seed --class=ProductCategorySeeder');
        }

        if ($manufacturers > 0) {
            $this->info("   ✓ Manufacturers: {$manufacturers} total ({$activeManufacturers} active)");
        } else {
            $this->error('   ❌ No manufacturers found');
            $this->warn('      Run: php artisan db:seed --class=ManufacturerSeeder');
        }

        $this->info('');
    }

    /**
     * Check suppliers data
     */
    private function checkSuppliers(): void
    {
        $this->info('👥 Checking Suppliers:');
        
        $totalSuppliers = \App\Models\Supplier::count();
        $verifiedSuppliers = \App\Models\Supplier::where('is_verified', true)->count();
        $activeSuppliers = \App\Models\Supplier::where('is_active', true)->count();
        $suppliersWithUsers = \App\Models\Supplier::whereNotNull('user_id')->count();

        if ($totalSuppliers > 0) {
            $this->info("   ✓ Total Suppliers: {$totalSuppliers}");
            $this->info("   ✓ Verified Suppliers: {$verifiedSuppliers}");
            $this->info("   ✓ Active Suppliers: {$activeSuppliers}");
            $this->info("   ✓ Suppliers with User Accounts: {$suppliersWithUsers}");

            if ($totalSuppliers > 0 && $totalSuppliers <= 5) {
                $this->info('   Sample Suppliers:');
                \App\Models\Supplier::take(3)->get()->each(function ($supplier) {
                    $status = $supplier->is_verified ? '✓' : '✗';
                    $this->info("      {$status} {$supplier->company_name} ({$supplier->city})");
                });
            }
        } else {
            $this->error('   ❌ No suppliers found');
            $this->warn('      Run: php artisan db:seed --class=ProductCatalogSeeder');
        }

        $this->info('');
    }

    /**
     * Check products data
     */
    private function checkProducts(): void
    {
        $this->info('📦 Checking Products:');
        
        $totalProducts = \App\Models\Product::count();
        $activeProducts = \App\Models\Product::where('is_active', true)->count();
        $approvedProducts = \App\Models\Product::where('review_status', \App\Models\Product::REVIEW_APPROVED)->count();
        $productsWithCategory = \App\Models\Product::whereNotNull('category_id')->count();
        $productsWithManufacturer = \App\Models\Product::whereNotNull('manufacturer_id')->count();

        if ($totalProducts > 0) {
            $this->info("   ✓ Total Products: {$totalProducts}");
            $this->info("   ✓ Active Products: {$activeProducts}");
            $this->info("   ✓ Approved Products: {$approvedProducts}");
            $this->info("   ✓ Products with Category: {$productsWithCategory}");
            $this->info("   ✓ Products with Manufacturer: {$productsWithManufacturer}");

            if ($totalProducts > 0 && $totalProducts <= 5) {
                $this->info('   Sample Products:');
                \App\Models\Product::take(3)->get()->each(function ($product) {
                    $status = $product->is_active ? '✓' : '✗';
                    $this->info("      {$status} {$product->name}");
                });
            }
        } else {
            $this->error('   ❌ No products found');
            $this->warn('      Run: php artisan db:seed --class=ProductCatalogSeeder');
        }

        $this->info('');
    }

    /**
     * Check relationships
     */
    private function checkRelationships(): void
    {
        $this->info('🔗 Checking Relationships:');
        
        $totalOffers = \Illuminate\Support\Facades\DB::table('product_supplier')->count();
        $availableOffers = \Illuminate\Support\Facades\DB::table('product_supplier')->where('status', 'available')->count();
        $productsWithSuppliers = \App\Models\Product::whereHas('suppliers')->count();
        $suppliersWithProducts = \App\Models\Supplier::whereHas('products')->count();

        if ($totalOffers > 0) {
            $this->info("   ✓ Total Product-Supplier Offers: {$totalOffers}");
            $this->info("   ✓ Available Offers: {$availableOffers}");
            $this->info("   ✓ Products with Suppliers: {$productsWithSuppliers}");
            $this->info("   ✓ Suppliers with Products: {$suppliersWithProducts}");

            // Show average offers per product
            if ($productsWithSuppliers > 0) {
                $avgOffers = round($totalOffers / $productsWithSuppliers, 2);
                $this->info("   ✓ Average Offers per Product: {$avgOffers}");
            }
        } else {
            $this->error('   ❌ No product-supplier relationships found');
            $this->warn('      Run: php artisan db:seed --class=ProductCatalogSeeder');
        }

        $this->info('');
    }
}
