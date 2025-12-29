# Phase 5: Fix OrderController View Selection - Detailed Explanation

**Priority:** 🟡 **HIGH**  
**Estimated Time:** 1 day  
**Status:** 📋 **TO BE IMPLEMENTED**

---

## 🎯 Problem Statement

The `OrderController::index()` method currently **always** returns the admin view (`admin.orders.index`), regardless of the user's role. This means:

- ❌ **Buyers** cannot see their orders (they see admin view or get errors)
- ❌ **Suppliers** cannot see their orders (they see admin view or get errors)
- ✅ **Admins** can see all orders (this works correctly)

---

## 📊 Current Implementation (BROKEN)

### **OrderController::index() - Current Code**

```php
public function index()
{
    $query = Order::with(['quotation.rfq', 'buyer', 'supplier', 'items']);
    
    // ... filtering logic ...
    
    $orders = $query->latest('id')->paginate(15);
    
    // Stats calculation
    $stats = [
        'total_orders' => Order::count(),
        'pending_orders' => Order::where('status', 'pending')->count(),
        'processing_orders' => Order::where('status', 'processing')->count(),
        'delivered_orders' => Order::where('status', 'delivered')->count(),
    ];
    
    $buyers = Buyer::pluck('organization_name', 'id');
    $suppliers = Supplier::pluck('company_name', 'id');
    
    // ❌ PROBLEM: Always returns admin view, regardless of user role
    return view('admin.orders.index', compact('orders', 'stats', 'buyers', 'suppliers'));
}
```

**Issues:**
1. Hardcoded to `admin.orders.index` view
2. No role-based filtering (shows all orders to everyone)
3. Buyers and suppliers see admin interface
4. Stats include all orders, not filtered by user

---

## ✅ Expected Implementation (CORRECT)

### **Reference: InvoiceController - Working Example**

```php
public function index()
{
    $query = Invoice::with(['order.buyer', 'order.supplier']);
    
    // ... filtering logic ...
    
    $invoices = $query->latest('invoice_date')->paginate(20)->withQueryString();
    
    // Calculate stats
    $stats = [
        'total' => $stats->total ?? 0,
        'total_amount' => $stats->total_amount ?? 0,
        // ... more stats
    ];
    
    // ✅ SOLUTION: Dynamically select view based on user role
    $view = auth()->user()->hasRole('Admin') 
        ? 'admin.invoices.index' 
        : 'invoices.index';
    
    return view($view, compact('invoices', 'stats'));
}
```

**Key Features:**
1. ✅ Checks user role with `hasRole('Admin')`
2. ✅ Selects appropriate view dynamically
3. ✅ Works for both Admin and Supplier roles

---

## 🔧 Solution: Fix OrderController

### **Step 1: Add Role-Based View Selection**

```php
public function index()
{
    $query = Order::with(['quotation.rfq', 'buyer', 'supplier', 'items']);
    
    // ✅ ADD: Role-based filtering
    $user = auth()->user();
    
    if ($user->hasRole('Buyer')) {
        // Buyers only see their own orders
        $query->where('buyer_id', $user->buyerProfile->id);
    } elseif ($user->hasRole('Supplier')) {
        // Suppliers only see orders assigned to them
        $query->where('supplier_id', $user->supplierProfile->id);
    }
    // Admins see all orders (no filter needed)
    
    // ... existing filtering logic ...
    
    $orders = $query->latest('id')->paginate(15);
    
    // ✅ ADD: Role-based stats calculation
    if ($user->hasRole('Buyer')) {
        $buyerId = $user->buyerProfile->id;
        $stats = [
            'total_orders' => Order::where('buyer_id', $buyerId)->count(),
            'pending_orders' => Order::where('buyer_id', $buyerId)->where('status', 'pending')->count(),
            'processing_orders' => Order::where('buyer_id', $buyerId)->where('status', 'processing')->count(),
            'delivered_orders' => Order::where('buyer_id', $buyerId)->where('status', 'delivered')->count(),
        ];
    } elseif ($user->hasRole('Supplier')) {
        $supplierId = $user->supplierProfile->id;
        $stats = [
            'total_orders' => Order::where('supplier_id', $supplierId)->count(),
            'pending_orders' => Order::where('supplier_id', $supplierId)->where('status', 'pending')->count(),
            'processing_orders' => Order::where('supplier_id', $supplierId)->where('status', 'processing')->count(),
            'delivered_orders' => Order::where('supplier_id', $supplierId)->where('status', 'delivered')->count(),
        ];
    } else {
        // Admin stats (all orders)
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
        ];
    }
    
    // ✅ ADD: Dynamic view selection
    if ($user->hasRole('Admin')) {
        $view = 'admin.orders.index';
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        return view($view, compact('orders', 'stats', 'buyers', 'suppliers'));
    } elseif ($user->hasRole('Buyer')) {
        $view = 'buyer.orders.index';
        return view($view, compact('orders', 'stats'));
    } else {
        // Supplier view (already exists)
        $view = 'supplier.orders.index';
        return view($view, compact('orders', 'stats'));
    }
}
```

---

## 📋 Required Changes

### **1. Update `index()` Method**

**Changes:**
- ✅ Add role-based query filtering
- ✅ Add role-based stats calculation
- ✅ Add dynamic view selection
- ✅ Conditionally include `$buyers` and `$suppliers` (only for admin)

### **2. Update `show()` Method**

**Current:**
```php
public function show(Order $order)
{
    $order->load(['quotation.rfq', 'buyer', 'supplier', 'invoices', 'payments', 'deliveries']);
    
    return view('admin.orders.show', compact('order'));
}
```

**Should Be:**
```php
public function show(Order $order)
{
    $user = auth()->user();
    
    // ✅ ADD: Authorization check
    if ($user->hasRole('Buyer') && $order->buyer_id !== $user->buyerProfile->id) {
        abort(403, 'Unauthorized');
    }
    
    if ($user->hasRole('Supplier') && $order->supplier_id !== $user->supplierProfile->id) {
        abort(403, 'Unauthorized');
    }
    
    $order->load(['quotation.rfq', 'buyer', 'supplier', 'invoices', 'payments', 'deliveries']);
    
    // ✅ ADD: Dynamic view selection
    $view = $user->hasRole('Admin') 
        ? 'admin.orders.show' 
        : ($user->hasRole('Buyer') 
            ? 'buyer.orders.show' 
            : 'supplier.orders.show');
    
    return view($view, compact('order'));
}
```

### **3. Update `create()` Method**

**Current:**
```php
public function create()
{
    $quotations = \App\Models\Quotation::where('status', 'accepted')->pluck('reference_code', 'id');
    $buyers = Buyer::pluck('organization_name', 'id');
    $suppliers = Supplier::pluck('company_name', 'id');
    
    return view('orders.form', [
        'order' => new Order,
        'quotations' => $quotations,
        'buyers' => $buyers,
        'suppliers' => $suppliers,
    ]);
}
```

**Should Be:**
```php
public function create()
{
    $user = auth()->user();
    
    // ✅ ADD: Role-based data filtering
    if ($user->hasRole('Buyer')) {
        $quotations = \App\Models\Quotation::where('status', 'accepted')
            ->where('buyer_id', $user->buyerProfile->id)
            ->pluck('reference_code', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        
        $view = 'buyer.orders.create';
        return view($view, [
            'order' => new Order,
            'quotations' => $quotations,
            'suppliers' => $suppliers,
        ]);
    } else {
        // Admin view
        $quotations = \App\Models\Quotation::where('status', 'accepted')->pluck('reference_code', 'id');
        $buyers = Buyer::pluck('organization_name', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');
        
        $view = 'admin.orders.create';
        return view($view, [
            'order' => new Order,
            'quotations' => $quotations,
            'buyers' => $buyers,
            'suppliers' => $suppliers,
        ]);
    }
}
```

### **4. Update `store()` Method Redirects**

**Current:**
```php
return redirect()
    ->route('admin.orders')
    ->with('success', '✅ تم إنشاء أمر الشراء بنجاح.');
```

**Should Be:**
```php
$user = auth()->user();
$redirectRoute = $user->hasRole('Admin') 
    ? 'admin.orders' 
    : ($user->hasRole('Buyer') 
        ? 'buyer.orders.index' 
        : 'supplier.orders.index');

return redirect()
    ->route($redirectRoute)
    ->with('success', '✅ تم إنشاء أمر الشراء بنجاح.');
```

### **5. Update `update()` Method Redirects**

**Similar changes needed for redirect routes.**

---

## 📁 Required Views (If Missing)

### **Buyer Views:**
- ✅ `resources/views/buyer/orders/index.blade.php` - Already exists (via SupplierOrderController)
- ❌ `resources/views/buyer/orders/show.blade.php` - May need to create
- ❌ `resources/views/buyer/orders/create.blade.php` - May need to create

### **Supplier Views:**
- ✅ `resources/views/supplier/orders/index.blade.php` - Already exists
- ✅ `resources/views/supplier/orders/show.blade.php` - Already exists

---

## 🔍 Comparison with Working Controllers

### **InvoiceController - ✅ Working**

```php
// Line 90
$view = auth()->user()->hasRole('Admin') ? 'admin.invoices.index' : 'invoices.index';
return view($view, compact('invoices', 'stats'));
```

### **PaymentController - ✅ Working**

```php
// Similar pattern
$view = auth()->user()->hasRole('Admin') ? 'admin.payments.index' : 'payments.index';
return view($view, compact('payments', 'stats'));
```

### **DeliveryController - ✅ Working**

```php
// Similar pattern
$view = auth()->user()->hasRole('Admin') ? 'admin.deliveries.index' : 'deliveries.index';
return view($view, compact('deliveries', 'stats'));
```

### **OrderController - ❌ Needs Fix**

```php
// Line 67 - Always admin view
return view('admin.orders.index', compact('orders', 'stats', 'buyers', 'suppliers'));
```

---

## 🎯 Implementation Checklist

- [ ] Update `OrderController::index()` - Add role-based filtering and view selection
- [ ] Update `OrderController::show()` - Add authorization and view selection
- [ ] Update `OrderController::create()` - Add role-based data filtering
- [ ] Update `OrderController::store()` - Fix redirect routes
- [ ] Update `OrderController::update()` - Fix redirect routes
- [ ] Update `OrderController::edit()` - Add authorization and view selection
- [ ] Verify buyer views exist (create if missing)
- [ ] Test with Admin user
- [ ] Test with Buyer user
- [ ] Test with Supplier user

---

## 🧪 Testing Scenarios

### **Test 1: Admin User**
- ✅ Should see all orders
- ✅ Should see admin view (`admin.orders.index`)
- ✅ Should see all buyers and suppliers in filters

### **Test 2: Buyer User**
- ✅ Should only see their own orders
- ✅ Should see buyer view (`buyer.orders.index`)
- ✅ Stats should only reflect their orders
- ✅ Should not see other buyers' orders

### **Test 3: Supplier User**
- ✅ Should only see orders assigned to them
- ✅ Should see supplier view (`supplier.orders.index`)
- ✅ Stats should only reflect their orders
- ✅ Should not see other suppliers' orders

---

## 📝 Summary

**Problem:** `OrderController` always shows admin view to all users.

**Solution:** Implement dynamic view selection based on user role, similar to `InvoiceController`, `PaymentController`, and `DeliveryController`.

**Impact:** 
- ✅ Buyers can see their orders
- ✅ Suppliers can see their orders
- ✅ Admins continue to see all orders
- ✅ Consistent behavior across all controllers

**Time Estimate:** 1 day (including testing)

---

**Document Created:** 2025-01-27  
**Status:** 📋 Ready for Implementation

