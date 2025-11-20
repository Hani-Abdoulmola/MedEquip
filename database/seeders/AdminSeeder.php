<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminType = UserType::where('slug', 'admin')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@MedEquip.com'], // ← نفس الإيميل
            [
                'user_type_id' => $adminType?->id,
                'name' => 'System Administrator',
                'email' => 'admin@MedEquip.com',
                'phone' => '0910000000',
                'password' => '1234567890',
                'status' => 'active',
            ]
        );

        $admin->assignRole('Admin');
    }
}
