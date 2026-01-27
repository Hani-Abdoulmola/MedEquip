<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ensures user types have correct IDs:
     * - Admin: ID = 1
     * - Supplier: ID = 2
     * - Buyer: ID = 3
     * - Staff: ID = 4
     */
    public function up(): void
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Get existing user types
        $adminType = DB::table('user_types')->where('slug', 'admin')->first();
        $supplierType = DB::table('user_types')->where('slug', 'supplier')->first();
        $buyerType = DB::table('user_types')->where('slug', 'buyer')->first();
        $staffType = DB::table('user_types')->where('slug', 'staff')->first();

        // Helper function to update user type ID
        $updateUserTypeId = function ($currentId, $targetId, $slug) {
            if ($currentId == $targetId) {
                return; // Already correct
            }

            // Check if target ID is taken
            $existingAtTarget = DB::table('user_types')->where('id', $targetId)->first();
            
            if ($existingAtTarget && $existingAtTarget->id != $currentId) {
                // Target ID is taken by another type - swap them
                $tempId = 9999;
                // Find available temp ID
                while (DB::table('user_types')->where('id', $tempId)->exists()) {
                    $tempId++;
                }
                
                // Move existing at target to temp
                DB::table('user_types')->where('id', $targetId)->update(['id' => $tempId]);
                DB::table('users')->where('user_type_id', $targetId)->update(['user_type_id' => $tempId]);
                
                // Move current to target
                DB::table('user_types')->where('id', $currentId)->update(['id' => $targetId]);
                DB::table('users')->where('user_type_id', $currentId)->update(['user_type_id' => $targetId]);
                
                // Move temp back to original current ID
                DB::table('user_types')->where('id', $tempId)->update(['id' => $currentId]);
                DB::table('users')->where('user_type_id', $tempId)->update(['user_type_id' => $currentId]);
            } else {
                // Target ID is free - just update
                DB::table('user_types')->where('id', $currentId)->update(['id' => $targetId]);
                DB::table('users')->where('user_type_id', $currentId)->update(['user_type_id' => $targetId]);
            }
        };

        // Update Admin to ID = 1
        if ($adminType) {
            if ($adminType->id != 1) {
                $updateUserTypeId($adminType->id, 1, 'admin');
            }
        } else {
            // Check if ID 1 exists with different slug
            $existingAt1 = DB::table('user_types')->where('id', 1)->first();
            if ($existingAt1) {
                // ID 1 is taken - update it to be Admin
                DB::table('user_types')->where('id', 1)->update([
                    'name' => 'Admin',
                    'slug' => 'admin',
                    'description' => 'مدير النظام',
                    'updated_at' => now(),
                ]);
            } else {
                // ID 1 is free - insert Admin only if it doesn't exist
                if (!DB::table('user_types')->where('slug', 'admin')->exists()) {
                    DB::table('user_types')->insert([
                        'id' => 1,
                        'name' => 'Admin',
                        'slug' => 'admin',
                        'description' => 'مدير النظام',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Update Supplier to ID = 2
        if ($supplierType) {
            if ($supplierType->id != 2) {
                $updateUserTypeId($supplierType->id, 2, 'supplier');
            }
        } else {
            // Supplier doesn't exist
            $existingAt2 = DB::table('user_types')->where('id', 2)->first();
            if ($existingAt2) {
                // ID 2 is taken - update it to be Supplier
                DB::table('user_types')->where('id', 2)->update([
                    'name' => 'Supplier',
                    'slug' => 'supplier',
                    'description' => 'مورد المعدات الطبية',
                    'updated_at' => now(),
                ]);
            } else {
                // ID 2 is free - insert Supplier only if it doesn't exist
                if (!DB::table('user_types')->where('slug', 'supplier')->exists()) {
                    DB::table('user_types')->insert([
                        'id' => 2,
                        'name' => 'Supplier',
                        'slug' => 'supplier',
                        'description' => 'مورد المعدات الطبية',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Update Buyer to ID = 3
        if ($buyerType) {
            if ($buyerType->id != 3) {
                $updateUserTypeId($buyerType->id, 3, 'buyer');
            }
        } else {
            // Buyer doesn't exist
            $existingAt3 = DB::table('user_types')->where('id', 3)->first();
            if ($existingAt3) {
                // ID 3 is taken - update it to be Buyer
                DB::table('user_types')->where('id', 3)->update([
                    'name' => 'Buyer',
                    'slug' => 'buyer',
                    'description' => 'مشتري أو جهة طبية',
                    'updated_at' => now(),
                ]);
            } else {
                // ID 3 is free - insert Buyer only if it doesn't exist
                if (!DB::table('user_types')->where('slug', 'buyer')->exists()) {
                    DB::table('user_types')->insert([
                        'id' => 3,
                        'name' => 'Buyer',
                        'slug' => 'buyer',
                        'description' => 'مشتري أو جهة طبية',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Update Staff to ID = 4
        if ($staffType) {
            if ($staffType->id != 4) {
                $updateUserTypeId($staffType->id, 4, 'staff');
            }
        } else {
            // Staff doesn't exist
            $existingAt4 = DB::table('user_types')->where('id', 4)->first();
            if ($existingAt4) {
                // ID 4 is taken - update it to be Staff
                DB::table('user_types')->where('id', 4)->update([
                    'name' => 'Staff',
                    'slug' => 'staff',
                    'description' => 'موظف إداري',
                    'updated_at' => now(),
                ]);
            } else {
                // ID 4 is free - insert Staff only if it doesn't exist
                if (!DB::table('user_types')->where('slug', 'staff')->exists()) {
                    DB::table('user_types')->insert([
                        'id' => 4,
                        'name' => 'Staff',
                        'slug' => 'staff',
                        'description' => 'موظف إداري',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible safely
        // User types should maintain their IDs for data integrity
    }
};
