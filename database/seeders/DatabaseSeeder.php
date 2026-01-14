<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserTypeSeeder::class,
            UnifiedRolePermissionSeeder::class, // Use unified seeder instead of RolePermissionSeeder
            AdminSeeder::class,
            ProductCategorySeeder::class,
            ManufacturerSeeder::class,
        ]);
    }
}
