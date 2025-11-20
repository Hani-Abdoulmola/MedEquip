<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'مدير النظام'],
            ['name' => 'Supplier', 'slug' => 'supplier', 'description' => 'مورد المعدات الطبية'],
            ['name' => 'Buyer', 'slug' => 'buyer', 'description' => 'مشتري أو جهة طبية'],
        ];

        foreach ($types as $type) {
            UserType::updateOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
