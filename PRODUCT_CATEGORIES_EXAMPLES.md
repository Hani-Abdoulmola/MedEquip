# PRODUCT CATEGORIES - USAGE EXAMPLES
## Medical Equipment Category Hierarchy for MediTrust

**Date:** 2025-11-14  
**Purpose:** Example category structure and usage patterns

---

## 🏥 EXAMPLE CATEGORY HIERARCHY

### **Suggested Medical Equipment Categories**

```
📦 Medical Imaging Equipment (التصوير الطبي)
    ├── 📷 X-Ray Machines (أجهزة الأشعة السينية)
    │   ├── Digital X-Ray (الأشعة السينية الرقمية)
    │   ├── Portable X-Ray (الأشعة السينية المحمولة)
    │   └── Dental X-Ray (أشعة الأسنان)
    ├── 🔬 CT Scanners (أجهزة التصوير المقطعي)
    ├── 🧲 MRI Machines (أجهزة الرنين المغناطيسي)
    └── 🔊 Ultrasound Equipment (أجهزة الموجات فوق الصوتية)

📦 Surgical Equipment (المعدات الجراحية)
    ├── 🔪 Surgical Instruments (الأدوات الجراحية)
    │   ├── Scalpels & Blades (المشارط والشفرات)
    │   ├── Forceps & Clamps (الملاقط والمشابك)
    │   └── Scissors & Retractors (المقصات والمباعدات)
    ├── 💡 Surgical Lights (إضاءة العمليات)
    ├── 🛏️ Operating Tables (طاولات العمليات)
    └── 🔌 Electrosurgical Units (وحدات الجراحة الكهربائية)

📦 Laboratory Equipment (معدات المختبرات)
    ├── 🧪 Analyzers (أجهزة التحليل)
    │   ├── Blood Analyzers (محللات الدم)
    │   ├── Chemistry Analyzers (محللات الكيمياء)
    │   └── Urine Analyzers (محللات البول)
    ├── 🔬 Microscopes (المجاهر)
    ├── 🌡️ Incubators (الحاضنات)
    └── ⚗️ Centrifuges (أجهزة الطرد المركزي)

📦 Patient Monitoring (مراقبة المرضى)
    ├── 💓 Vital Signs Monitors (أجهزة قياس العلامات الحيوية)
    ├── 📊 ECG Machines (أجهزة تخطيط القلب)
    ├── 🫁 Pulse Oximeters (أجهزة قياس الأكسجين)
    └── 🩺 Blood Pressure Monitors (أجهزة قياس ضغط الدم)

📦 Life Support Equipment (معدات دعم الحياة)
    ├── 🫁 Ventilators (أجهزة التنفس الصناعي)
    ├── 💉 Infusion Pumps (مضخات الحقن)
    ├── 🩹 Defibrillators (أجهزة إزالة الرجفان)
    └── 🛏️ ICU Equipment (معدات العناية المركزة)

📦 Sterilization Equipment (معدات التعقيم)
    ├── 🔥 Autoclaves (أجهزة التعقيم بالبخار)
    ├── 🧼 Washers & Disinfectors (الغسالات والمطهرات)
    └── 📦 Sterilization Containers (حاويات التعقيم)
```

---

## 💻 SEEDER EXAMPLE

### **Create ProductCategorySeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Medical Imaging Equipment
        $medicalImaging = ProductCategory::create([
            'name' => 'Medical Imaging Equipment',
            'name_ar' => 'معدات التصوير الطبي',
            'slug' => 'medical-imaging',
            'description' => 'Diagnostic imaging equipment including X-Ray, CT, MRI, and Ultrasound',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // 1.1 X-Ray Machines
        $xray = ProductCategory::create([
            'name' => 'X-Ray Machines',
            'name_ar' => 'أجهزة الأشعة السينية',
            'slug' => 'xray-machines',
            'parent_id' => $medicalImaging->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'name' => 'Digital X-Ray',
            'name_ar' => 'الأشعة السينية الرقمية',
            'slug' => 'digital-xray',
            'parent_id' => $xray->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'name' => 'Portable X-Ray',
            'name_ar' => 'الأشعة السينية المحمولة',
            'slug' => 'portable-xray',
            'parent_id' => $xray->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // 1.2 CT Scanners
        ProductCategory::create([
            'name' => 'CT Scanners',
            'name_ar' => 'أجهزة التصوير المقطعي',
            'slug' => 'ct-scanners',
            'parent_id' => $medicalImaging->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // 1.3 MRI Machines
        ProductCategory::create([
            'name' => 'MRI Machines',
            'name_ar' => 'أجهزة الرنين المغناطيسي',
            'slug' => 'mri-machines',
            'parent_id' => $medicalImaging->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // 2. Surgical Equipment
        $surgical = ProductCategory::create([
            'name' => 'Surgical Equipment',
            'name_ar' => 'المعدات الجراحية',
            'slug' => 'surgical-equipment',
            'description' => 'Surgical instruments, lights, tables, and electrosurgical units',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // 2.1 Surgical Instruments
        $instruments = ProductCategory::create([
            'name' => 'Surgical Instruments',
            'name_ar' => 'الأدوات الجراحية',
            'slug' => 'surgical-instruments',
            'parent_id' => $surgical->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'name' => 'Scalpels & Blades',
            'name_ar' => 'المشارط والشفرات',
            'slug' => 'scalpels-blades',
            'parent_id' => $instruments->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // 3. Laboratory Equipment
        $laboratory = ProductCategory::create([
            'name' => 'Laboratory Equipment',
            'name_ar' => 'معدات المختبرات',
            'slug' => 'laboratory-equipment',
            'description' => 'Laboratory analyzers, microscopes, incubators, and centrifuges',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Add more categories as needed...
    }
}
```

**Register in DatabaseSeeder:**
```php
public function run(): void
{
    $this->call([
        UserTypeSeeder::class,
        RolePermissionSeeder::class,
        AdminSeeder::class,
        ProductCategorySeeder::class, // Add this
    ]);
}
```

---

## 🔍 QUERY EXAMPLES

### **Get All Root Categories**
```php
$rootCategories = ProductCategory::roots()->active()->ordered()->get();

foreach ($rootCategories as $category) {
    echo $category->name . "\n";
    foreach ($category->children as $child) {
        echo "  └── " . $child->name . "\n";
    }
}
```

### **Get Category with All Descendants**
```php
$category = ProductCategory::with('children.children.children')->find(1);

// Recursive function to display hierarchy
function displayCategory($category, $level = 0) {
    echo str_repeat('  ', $level) . $category->name . "\n";
    foreach ($category->children as $child) {
        displayCategory($child, $level + 1);
    }
}

displayCategory($category);
```

### **Get All Products in Category and Subcategories**
```php
function getProductsRecursive($category) {
    $products = $category->products;
    
    foreach ($category->children as $child) {
        $products = $products->merge(getProductsRecursive($child));
    }
    
    return $products;
}

$medicalImaging = ProductCategory::where('slug', 'medical-imaging')->first();
$allProducts = getProductsRecursive($medicalImaging);
```

### **Build Breadcrumb Navigation**
```php
function getBreadcrumbs($category) {
    $breadcrumbs = [$category];
    $parent = $category->parent;
    
    while ($parent) {
        array_unshift($breadcrumbs, $parent);
        $parent = $parent->parent;
    }
    
    return $breadcrumbs;
}

$digitalXray = ProductCategory::where('slug', 'digital-xray')->first();
$breadcrumbs = getBreadcrumbs($digitalXray);

// Output: Medical Imaging > X-Ray Machines > Digital X-Ray
echo implode(' > ', array_map(fn($c) => $c->name, $breadcrumbs));
```

---

## 🎨 FRONTEND EXAMPLES

### **Category Dropdown (Hierarchical)**
```php
function getCategoryOptions($parentId = null, $level = 0) {
    $categories = ProductCategory::where('parent_id', $parentId)
        ->active()
        ->ordered()
        ->get();
    
    $options = [];
    foreach ($categories as $category) {
        $options[$category->id] = str_repeat('—', $level) . ' ' . $category->name;
        $options = array_merge($options, getCategoryOptions($category->id, $level + 1));
    }
    
    return $options;
}

// In Blade:
<select name="category_id">
    <option value="">Select Category</option>
    @foreach(getCategoryOptions() as $id => $name)
        <option value="{{ $id }}">{{ $name }}</option>
    @endforeach
</select>
```

### **Category Tree (Nested List)**
```blade
@foreach($rootCategories as $category)
    <li>
        <a href="/products?category={{ $category->slug }}">
            {{ $category->name }}
            @if($category->name_ar)
                ({{ $category->name_ar }})
            @endif
        </a>
        @if($category->children->count() > 0)
            <ul>
                @include('partials.category-tree', ['categories' => $category->children])
            </ul>
        @endif
    </li>
@endforeach
```

---

## 📊 REPORTING EXAMPLES

### **Products Count by Category**
```php
$categories = ProductCategory::withCount('products')->get();

foreach ($categories as $category) {
    echo "{$category->name}: {$category->products_count} products\n";
}
```

### **Most Popular Categories**
```php
$popularCategories = ProductCategory::withCount('products')
    ->having('products_count', '>', 0)
    ->orderByDesc('products_count')
    ->limit(10)
    ->get();
```

---

## 🚀 DEPLOYMENT CHECKLIST

- [ ] Run migrations: `php artisan migrate:fresh`
- [ ] Create ProductCategorySeeder
- [ ] Seed initial categories: `php artisan db:seed --class=ProductCategorySeeder`
- [ ] Verify category hierarchy: `php artisan tinker` → `ProductCategory::with('children')->roots()->get()`
- [ ] Assign categories to existing products (if any)
- [ ] Update product forms to use category dropdown
- [ ] Add category filter to product listing
- [ ] Test category breadcrumbs
- [ ] Verify soft delete behavior
- [ ] Test auto-slug generation

---

**Ready to use!** 🎉

