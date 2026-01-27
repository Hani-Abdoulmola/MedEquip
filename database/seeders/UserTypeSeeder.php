<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure correct IDs: Admin=1, Supplier=2, Buyer=3, Staff=4
        // Use DB facade to avoid model constraints
        $types = [
            ['id' => 1, 'name' => 'Admin', 'slug' => 'admin', 'description' => 'مدير النظام'],
            ['id' => 2, 'name' => 'Supplier', 'slug' => 'supplier', 'description' => 'مورد المعدات الطبية'],
            ['id' => 3, 'name' => 'Buyer', 'slug' => 'buyer', 'description' => 'مشتري أو جهة طبية'],
            ['id' => 4, 'name' => 'Staff', 'slug' => 'staff', 'description' => 'موظف إداري'],
        ];

        foreach ($types as $type) {
            // Check if record exists by slug
            $existing = \DB::table('user_types')->where('slug', $type['slug'])->first();
            
            if ($existing) {
                // Record exists - update if ID is different
                if ($existing->id != $type['id']) {
                    // Check if target ID is taken
                    $targetIdTaken = \DB::table('user_types')->where('id', $type['id'])->first();
                    if ($targetIdTaken && $targetIdTaken->id != $existing->id) {
                        // Swap IDs using temp ID
                        $tempId = 9999;
                        while (\DB::table('user_types')->where('id', $tempId)->exists()) {
                            $tempId++;
                        }
                        \DB::table('user_types')->where('id', $type['id'])->update(['id' => $tempId]);
                        \DB::table('users')->where('user_type_id', $type['id'])->update(['user_type_id' => $tempId]);
                    }
                    // Update existing to target ID
                    \DB::table('user_types')->where('slug', $type['slug'])->update(['id' => $type['id']]);
                    \DB::table('users')->where('user_type_id', $existing->id)->update(['user_type_id' => $type['id']]);
                    if ($targetIdTaken && $targetIdTaken->id != $existing->id) {
                        \DB::table('user_types')->where('id', $tempId)->update(['id' => $existing->id]);
                        \DB::table('users')->where('user_type_id', $tempId)->update(['user_type_id' => $existing->id]);
                    }
                }
                // Update other fields
                \DB::table('user_types')->where('slug', $type['slug'])->update([
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'updated_at' => now(),
                ]);
            } else {
                // Record doesn't exist - check if target ID is available
                $targetIdTaken = \DB::table('user_types')->where('id', $type['id'])->first();
                if ($targetIdTaken) {
                    // Target ID is taken - update it
                    \DB::table('user_types')->where('id', $type['id'])->update([
                        'name' => $type['name'],
                        'slug' => $type['slug'],
                        'description' => $type['description'],
                        'updated_at' => now(),
                    ]);
                } else {
                    // Target ID is free - create new
                    \DB::table('user_types')->insert(array_merge($type, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }
    }
}
