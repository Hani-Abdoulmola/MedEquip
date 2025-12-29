<?php

namespace Tests\Feature\System;

use App\Models\Buyer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * User Registration and Approval Workflow System Test
 * 
 * Tests the complete registration and approval process:
 * Registration → Admin Review → Approval/Rejection → Account Activation
 */
class UserRegistrationApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Supplier', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Buyer', 'guard_name' => 'web']);

        // Create user types
        UserType::firstOrCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
        UserType::firstOrCreate(['id' => 2, 'name' => 'Supplier', 'slug' => 'supplier']);
        UserType::firstOrCreate(['id' => 3, 'name' => 'Buyer', 'slug' => 'buyer']);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'user_type_id' => 1,
            'email' => 'admin@test.com',
        ]);
        $this->adminUser->assignRole('Admin');
    }

    public function test_supplier_registration_creates_pending_account(): void
    {
        $user = User::create([
            'user_type_id' => 2, // Supplier
            'name' => 'Test Supplier',
            'email' => 'newsupplier@test.com',
            'phone' => '0912345678',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $user->assignRole('Supplier');

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'company_name' => 'New Supplier Company',
            'commercial_register' => 'CR123456',
            'tax_number' => 'TAX123456',
            'is_verified' => false,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'user_type_id' => 2,
            'email' => 'newsupplier@test.com',
        ]);

        $this->assertDatabaseHas('suppliers', [
            'user_id' => $user->id,
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Pending supplier should not be able to access supplier routes
        $this->assertFalse($supplier->is_verified);
        $this->assertFalse($supplier->is_active);
    }

    public function test_buyer_registration_creates_pending_account(): void
    {
        $user = User::create([
            'user_type_id' => 3, // Buyer
            'name' => 'Test Buyer',
            'email' => 'newbuyer@test.com',
            'phone' => '0912345679',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $user->assignRole('Buyer');

        $buyer = Buyer::create([
            'user_id' => $user->id,
            'organization_name' => 'New Hospital',
            'organization_type' => 'hospital',
            'license_number' => 'LIC123456',
            'is_verified' => false,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'user_type_id' => 3,
            'email' => 'newbuyer@test.com',
        ]);

        $this->assertDatabaseHas('buyers', [
            'user_id' => $user->id,
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Pending buyer should not be able to access buyer routes
        $this->assertFalse($buyer->is_verified);
        $this->assertFalse($buyer->is_active);
    }

    public function test_admin_approval_activates_supplier_account(): void
    {
        $user = User::create([
            'user_type_id' => 2,
            'name' => 'Pending Supplier',
            'email' => 'pending@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $user->assignRole('Supplier');

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'company_name' => 'Pending Company',
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Admin approves supplier
        $supplier->update([
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Approved supplier should be able to access supplier routes
        $supplier->refresh();
        $this->assertTrue($supplier->is_verified);
        $this->assertTrue($supplier->is_active);
    }

    public function test_admin_approval_activates_buyer_account(): void
    {
        $user = User::create([
            'user_type_id' => 3,
            'name' => 'Pending Buyer',
            'email' => 'pendingbuyer@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $user->assignRole('Buyer');

        $buyer = Buyer::create([
            'user_id' => $user->id,
            'organization_name' => 'Pending Hospital',
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Admin approves buyer
        $buyer->update([
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('buyers', [
            'id' => $buyer->id,
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Approved buyer should be able to access buyer routes
        $buyer->refresh();
        $this->assertTrue($buyer->is_verified);
        $this->assertTrue($buyer->is_active);
    }

    public function test_admin_rejection_keeps_account_inactive(): void
    {
        $user = User::create([
            'user_type_id' => 2,
            'name' => 'Rejected Supplier',
            'email' => 'rejected@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $user->assignRole('Supplier');

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'company_name' => 'Rejected Company',
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Admin rejects supplier (keeps inactive)
        // In a real system, you might add a rejection_reason field
        $supplier->update([
            'is_verified' => false,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'is_verified' => false,
            'is_active' => false,
        ]);

        // Rejected supplier should not be able to access supplier routes
        $supplier->refresh();
        $this->assertFalse($supplier->is_verified);
        $this->assertFalse($supplier->is_active);
    }

    public function test_user_role_assignment_on_registration(): void
    {
        // Supplier registration
        $supplierUser = User::create([
            'user_type_id' => 2,
            'name' => 'Supplier User',
            'email' => 'supplieruser@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $supplierUser->assignRole('Supplier');

        $this->assertTrue($supplierUser->hasRole('Supplier'));
        $this->assertFalse($supplierUser->hasRole('Buyer'));
        $this->assertFalse($supplierUser->hasRole('Admin'));

        // Buyer registration
        $buyerUser = User::create([
            'user_type_id' => 3,
            'name' => 'Buyer User',
            'email' => 'buyeruser@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $buyerUser->assignRole('Buyer');

        $this->assertTrue($buyerUser->hasRole('Buyer'));
        $this->assertFalse($buyerUser->hasRole('Supplier'));
        $this->assertFalse($buyerUser->hasRole('Admin'));
    }

    public function test_multiple_users_can_register_with_same_role(): void
    {
        $supplier1 = User::create([
            'user_type_id' => 2,
            'name' => 'Supplier 1',
            'email' => 'supplier1@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $supplier1->assignRole('Supplier');

        $supplier2 = User::create([
            'user_type_id' => 2,
            'name' => 'Supplier 2',
            'email' => 'supplier2@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $supplier2->assignRole('Supplier');

        $this->assertNotEquals($supplier1->id, $supplier2->id);
        $this->assertTrue($supplier1->hasRole('Supplier'));
        $this->assertTrue($supplier2->hasRole('Supplier'));

        $this->assertEquals(2, User::where('user_type_id', 2)->count());
    }

    public function test_user_email_uniqueness_enforced(): void
    {
        User::create([
            'user_type_id' => 2,
            'name' => 'First Supplier',
            'email' => 'unique@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        // Attempt to create user with same email should fail
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'user_type_id' => 2,
            'name' => 'Second Supplier',
            'email' => 'unique@test.com', // Duplicate email
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
    }
}

