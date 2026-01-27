# New Product Request Workflow

## Overview
When a supplier selects "إضافة منتج جديد" (Add New Product) in the create form, the system creates a **ProductRequest** record (not a Product record) that requires admin approval before it becomes a canonical product in the catalog.

---

## 🔄 Complete Workflow

### **Step 1: Supplier Creates Product Request**

**Location:** `resources/views/supplier/products/create.blade.php`

1. **Form Display:**
   - Supplier sees two options:
     - ✅ **"ربط منتج موجود"** (Link Existing Product) - `action='existing'`
     - ✅ **"إضافة منتج جديد"** (Add New Product) - `action='new'`

2. **When "New Product" is Selected:**
   - Alpine.js shows the new product fields (`x-show="action === 'new'"`)
   - Supplier fills in:
     - Product name, model, brand
     - Category, manufacturer
     - Description, specifications, features
     - Images
     - **Offer information** (price, stock, lead time, warranty)

3. **Form Submission:**
   ```html
   <form method="POST" action="{{ route('supplier.products.store') }}" enctype="multipart/form-data">
       <input type="radio" name="action" value="new" x-model="action">
       <!-- ... product fields ... -->
   </form>
   ```
   - Sends `action='new'` to `SupplierProductController::store()`

---

### **Step 2: Controller Processing**

**Location:** `app/Http/Controllers/Web/Suppliers/SupplierProductController.php`

**Method:** `store(SupplierProductRequest $request)`

```php
if ($request->action === 'new') {
    // 1. Prepare ProductRequest data
    $productRequestData = [
        'supplier_id' => $supplier->id,
        'name' => $request->name,
        'model' => $request->model,
        'brand' => $request->brand,
        'category_id' => $request->category_id,
        'manufacturer_id' => $request->manufacturer_id,
        'description' => $request->description,
        'specifications' => [...], // Array from textarea
        'features' => [...], // Array from textarea
        'proposed_price' => $request->price,      // From offer section
        'proposed_stock' => $request->stock_quantity,  // From offer section
        'proposed_lead_time' => $request->lead_time,    // From offer section
        'proposed_warranty' => $request->warranty,      // From offer section
        'status' => ProductRequest::STATUS_PENDING,     // 'pending'
    ];

    // 2. Create ProductRequest record (NOT a Product record)
    $productRequest = ProductRequest::create($productRequestData);

    // 3. Upload images to ProductRequest (using Spatie MediaLibrary)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $productRequest->addMedia($image)
                ->toMediaCollection('product_request_images');
        }
    }

    // 4. Commit transaction
    DB::commit();

    // 5. Notify all admins
    NotificationService::notifyAdmins(
        'طلب منتج جديد',
        "طلب المورد {$supplier->company_name} إضافة منتج جديد: {$productRequest->name}",
        route('admin.product-requests.review', $productRequest->id)
    );

    // 6. Log activity
    activity('product_requests')
        ->performedOn($productRequest)
        ->log('📦 أنشأ المورد طلب منتج جديد (بانتظار المراجعة)');

    // 7. Redirect with success message
    return redirect()
        ->route('supplier.products.index')
        ->with('success', '✔ تم إرسال طلب المنتج بنجاح — بانتظار مراجعة الإدارة');
}
```

**Key Points:**
- ✅ Creates `ProductRequest` record in `product_requests` table
- ✅ Status is set to `'pending'`
- ✅ Images are stored with the ProductRequest (not Product)
- ✅ Admins are notified immediately
- ✅ Supplier sees success message

---

### **Step 3: Admin Reviews Product Request**

**Location:** `app/Http/Controllers/Web/AdminProductRequestController.php`

**Route:** `/admin/product-requests`

1. **Admin Views Requests:**
   ```php
   public function index(Request $request): View
   {
       $query = ProductRequest::with(['supplier.user', 'category', 'manufacturer']);
       
       // Filter by status (default shows pending first)
       if (!$request->filled('status')) {
           $query->orderByRaw("FIELD(status, 'pending', 'duplicate') DESC");
       }
       
       $requests = $query->latest()->paginate(15);
       
       return view('admin.product-requests.index', compact('requests', 'stats'));
   }
   ```

2. **Admin Reviews Individual Request:**
   ```php
   public function review(ProductRequest $productRequest): View
   {
       // Load relationships
       $productRequest->load(['supplier.user', 'category', 'manufacturer']);
       
       // Find potential duplicates
       $potentialDuplicates = $this->catalogService->findDuplicates(...);
       
       // Get existing products for merge option
       $existingProducts = Product::approved()->where('is_active', true)->get();
       
       return view('admin.product-requests.review', compact(
           'productRequest',
           'potentialDuplicates',
           'existingProducts'
       ));
   }
   ```

---

### **Step 4: Admin Actions**

Admin has three options:

#### **Option A: Approve (Create New Product)**

**Method:** `AdminProductRequestController::approve()`

```php
public function approve(Request $request, ProductRequest $productRequest): RedirectResponse
{
    // Uses ProductCatalogService to create the Product
    $product = $this->catalogService->approveRequest(
        $productRequest,
        Auth::user(),
        $request->admin_notes
    );
    
    // Product is created with:
    // - All product data from ProductRequest
    // - review_status = 'approved'
    // - is_active = true
    // - source = 'supplier_request'
    
    // ProductRequest status updated to 'approved'
    // existing_product_id set to the new Product ID
    
    return redirect()
        ->route('admin.product-requests.index')
        ->with('success', "✅ تمت الموافقة على المنتج: {$product->name}");
}
```

**What Happens:**
1. ✅ New `Product` record is created from ProductRequest data
2. ✅ ProductRequest status → `'approved'`
3. ✅ ProductRequest `existing_product_id` → new Product ID
4. ✅ Supplier is notified
5. ✅ Product appears in catalog

#### **Option B: Merge (Link to Existing Product)**

**Method:** `AdminProductRequestController::merge()`

```php
public function merge(Request $request, ProductRequest $productRequest): RedirectResponse
{
    $existingProduct = Product::findOrFail($request->existing_product_id);
    
    $this->catalogService->mergeRequest(
        $productRequest,
        $existingProduct,
        Auth::user(),
        $request->admin_notes
    );
    
    // ProductRequest status → 'merged'
    // existing_product_id → existing Product ID
    // Supplier is linked to existing product with their offer data
    
    return redirect()
        ->route('admin.product-requests.index')
        ->with('success', "🔗 تم دمج الطلب مع المنتج: {$existingProduct->name}");
}
```

**What Happens:**
1. ✅ ProductRequest status → `'merged'`
2. ✅ Supplier is linked to existing Product via `product_supplier` pivot
3. ✅ Supplier's offer data (price, stock, etc.) is saved in pivot table
4. ✅ Supplier is notified

#### **Option C: Reject**

**Method:** `AdminProductRequestController::reject()`

```php
public function reject(Request $request, ProductRequest $productRequest): RedirectResponse
{
    $this->catalogService->rejectRequest(
        $productRequest,
        Auth::user(),
        $request->rejection_reason,
        $request->admin_notes
    );
    
    // ProductRequest status → 'rejected'
    // rejection_reason saved
    
    return redirect()
        ->route('admin.product-requests.index')
        ->with('success', '❌ تم رفض الطلب');
}
```

**What Happens:**
1. ✅ ProductRequest status → `'rejected'`
2. ✅ Rejection reason saved
3. ✅ Supplier is notified with reason
4. ✅ No Product is created

---

## 📊 Database Tables

### **product_requests Table**
Stores supplier product requests:
- `id`, `supplier_id`, `name`, `model`, `brand`
- `category_id`, `manufacturer_id`
- `description`, `specifications`, `features`
- `proposed_price`, `proposed_stock`, `proposed_lead_time`, `proposed_warranty`
- `status` (pending, approved, merged, rejected, duplicate, cancelled)
- `existing_product_id` (set when approved/merged)
- `reviewed_by`, `reviewed_at`, `rejection_reason`

### **products Table**
Stores canonical products (created only after admin approval):
- `id`, `name`, `model`, `brand`
- `category_id`, `manufacturer_id`
- `review_status` (pending, approved, needs_update, rejected)
- `is_active`, `source` (e.g., 'supplier_request')

### **product_supplier Table** (Pivot)
Links suppliers to products with offer data:
- `product_id`, `supplier_id`
- `price`, `stock_quantity`, `lead_time`, `warranty`
- `status` (available, out_of_stock, suspended)

---

## 🔍 Key Differences

| Aspect | ProductRequest | Product |
|--------|---------------|---------|
| **Created By** | Supplier | Admin (after approval) |
| **Status** | pending → approved/merged/rejected | approved/needs_update/rejected |
| **Visibility** | Admin only | Visible to buyers |
| **Table** | `product_requests` | `products` |
| **Images** | `product_request_images` collection | `product_images` collection |
| **Purpose** | Request for new product | Canonical product in catalog |

---

## ✅ Validation Rules

**Location:** `app/Http/Requests/Suppliers/SupplierProductRequest.php`

When `action='new'`:
- `name` - required, string, max:255
- `category_id` - required, exists:product_categories,id
- `manufacturer_id` - nullable, exists:manufacturers,id
- `price` - required, numeric, min:0
- `stock_quantity` - required, integer, min:0
- `images.*` - nullable, image, mimes:jpg,jpeg,png,webp, max:5120

---

## 🎯 Summary

1. **Supplier** selects "New Product" → fills form → submits
2. **System** creates `ProductRequest` with status `'pending'`
3. **Admins** are notified
4. **Admin** reviews request in `/admin/product-requests`
5. **Admin** chooses: Approve (create Product) | Merge (link to existing) | Reject
6. **System** creates/links Product or rejects request
7. **Supplier** is notified of the decision

**Important:** Suppliers **cannot** create Product records directly. They must go through the ProductRequest workflow for admin approval.
