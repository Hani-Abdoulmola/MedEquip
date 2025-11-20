<?php

/**
 * 🧪 Authentication Registration Test Suite
 * 
 * Tests the dual user type registration system (Buyers & Suppliers)
 * for the MediTrust B2B medical equipment platform.
 * 
 * @package MediTrust
 * @version 1.0.0
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\UserType;
use App\Models\Buyer;
use App\Models\Supplier;

// 🔧 Bootstrap Laravel Application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 🎨 Test Output Formatting
class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function test(string $name, callable $callback): void
    {
        try {
            $callback();
            $this->passed++;
            echo "✅ {$name}\n";
        } catch (Exception $e) {
            $this->failed++;
            $this->failures[] = ['test' => $name, 'error' => $e->getMessage()];
            echo "❌ {$name}\n";
            echo "   Error: {$e->getMessage()}\n";
        }
    }

    public function summary(): void
    {
        $total = $this->passed + $this->failed;
        $percentage = $total > 0 ? round(($this->passed / $total) * 100, 2) : 0;

        echo "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 TEST SUMMARY\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ Passed: {$this->passed}\n";
        echo "❌ Failed: {$this->failed}\n";
        echo "📈 Total:  {$total}\n";
        echo "🎯 Success Rate: {$percentage}%\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        if ($this->failed > 0) {
            echo "\n❌ FAILED TESTS:\n";
            foreach ($this->failures as $failure) {
                echo "   • {$failure['test']}\n";
                echo "     {$failure['error']}\n";
            }
        }
    }
}

// 🧪 Initialize Test Runner
$test = new TestRunner();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     🧪 AUTHENTICATION REGISTRATION TEST SUITE                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 📋 Section 1: User Types Verification
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

echo "📋 Section 1: User Types Verification\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$test->test('User types table has correct records', function () {
    $userTypes = UserType::all();
    if ($userTypes->count() !== 3) {
        throw new Exception("Expected 3 user types, found {$userTypes->count()}");
    }
});

$test->test('Admin user type exists with id=1', function () {
    $admin = UserType::where('slug', 'admin')->first();
    if (!$admin || $admin->id !== 1) {
        throw new Exception("Admin user type not found or has incorrect id");
    }
});

$test->test('Supplier user type exists with id=2', function () {
    $supplier = UserType::where('slug', 'supplier')->first();
    if (!$supplier || $supplier->id !== 2) {
        throw new Exception("Supplier user type not found or has incorrect id");
    }
});

$test->test('Buyer user type exists with id=3', function () {
    $buyer = UserType::where('slug', 'buyer')->first();
    if (!$buyer || $buyer->id !== 3) {
        throw new Exception("Buyer user type not found or has incorrect id");
    }
});

echo "\n";

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 📋 Section 2: Request Validation Classes
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

echo "📋 Section 2: Request Validation Classes\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$test->test('BuyerRegistrationRequest class exists', function () {
    if (!class_exists('App\Http\Requests\BuyerRegistrationRequest')) {
        throw new Exception("BuyerRegistrationRequest class not found");
    }
});

$test->test('SupplierRegistrationRequest class exists', function () {
    if (!class_exists('App\Http\Requests\SupplierRegistrationRequest')) {
        throw new Exception("SupplierRegistrationRequest class not found");
    }
});

echo "\n";

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 📋 Section 3: Controller Methods
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

echo "📋 Section 3: Controller Methods\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$test->test('RegisteredUserController has storeBuyer method', function () {
    $controller = new \App\Http\Controllers\Auth\RegisteredUserController();
    if (!method_exists($controller, 'storeBuyer')) {
        throw new Exception("storeBuyer method not found in RegisteredUserController");
    }
});

$test->test('RegisteredUserController has storeSupplier method', function () {
    $controller = new \App\Http\Controllers\Auth\RegisteredUserController();
    if (!method_exists($controller, 'storeSupplier')) {
        throw new Exception("storeSupplier method not found in RegisteredUserController");
    }
});

echo "\n";

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 📋 Section 4: Routes Verification
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

echo "📋 Section 4: Routes Verification\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$test->test('register.buyer route exists', function () {
    if (!Route::has('register.buyer')) {
        throw new Exception("register.buyer route not found");
    }
});

$test->test('register.supplier route exists', function () {
    if (!Route::has('register.supplier')) {
        throw new Exception("register.supplier route not found");
    }
});

$test->test('register route exists', function () {
    if (!Route::has('register')) {
        throw new Exception("register route not found");
    }
});

$test->test('login route exists', function () {
    if (!Route::has('login')) {
        throw new Exception("login route not found");
    }
});

echo "\n";

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 📋 Section 5: View Files Verification
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

echo "📋 Section 5: View Files Verification\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$test->test('auth.register view exists', function () {
    if (!view()->exists('auth.register')) {
        throw new Exception("auth.register view not found");
    }
});

$test->test('auth.login view exists', function () {
    if (!view()->exists('auth.login')) {
        throw new Exception("auth.login view not found");
    }
});

$test->test('layouts.auth layout exists', function () {
    if (!view()->exists('layouts.auth')) {
        throw new Exception("layouts.auth layout not found");
    }
});

echo "\n";

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 📋 Section 6: Model Relationships
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

echo "📋 Section 6: Model Relationships\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$test->test('User model has type relationship', function () {
    $user = new User();
    if (!method_exists($user, 'type')) {
        throw new Exception("User model missing type relationship");
    }
});

$test->test('User model has buyerProfile relationship', function () {
    $user = new User();
    if (!method_exists($user, 'buyerProfile')) {
        throw new Exception("User model missing buyerProfile relationship");
    }
});

$test->test('User model has supplierProfile relationship', function () {
    $user = new User();
    if (!method_exists($user, 'supplierProfile')) {
        throw new Exception("User model missing supplierProfile relationship");
    }
});

$test->test('Buyer model has user relationship', function () {
    $buyer = new Buyer();
    if (!method_exists($buyer, 'user')) {
        throw new Exception("Buyer model missing user relationship");
    }
});

$test->test('Supplier model has user relationship', function () {
    $supplier = new Supplier();
    if (!method_exists($supplier, 'user')) {
        throw new Exception("Supplier model missing user relationship");
    }
});

echo "\n";

// 📊 Display Summary
$test->summary();

echo "\n";

