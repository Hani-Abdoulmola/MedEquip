# Validation Messages Audit Report

## Analysis of SupplierProductRequest Validation Messages

### Current Messages Coverage

**✅ Has Messages:**
- `action.required`
- `action.in`
- `name.required_if` (for store only)
- `category_id.required_if` (for store only)
- `product_id.required_if`
- `product_id.exists`
- `manufacturer_id.exists`
- `price.required`
- `stock_quantity.required`
- `images.*.mimes`
- `images.*.max`

### ❌ Missing Messages

**Pivot Data Fields:**
- `price.numeric` - "السعر يجب أن يكون رقماً."
- `price.min` - "السعر يجب أن يكون أكبر من أو يساوي صفر."
- `stock_quantity.integer` - "الكمية يجب أن تكون رقماً صحيحاً."
- `stock_quantity.min` - "الكمية يجب أن تكون أكبر من أو تساوي صفر."
- `lead_time.max` - "مدة التوصيل يجب ألا تتجاوز 100 حرف."
- `warranty.max` - "الضمان يجب ألا يتجاوز 100 حرف."
- `status.required` - "يجب اختيار حالة المنتج."
- `status.in` - "حالة المنتج غير صالحة."
- `notes.max` - "الملاحظات يجب ألا تتجاوز 2000 حرف."

**Base Product Fields (for needs_update scenario):**
- `name.required` - "اسم المنتج مطلوب."
- `name.string` - "اسم المنتج يجب أن يكون نصاً."
- `name.max` - "اسم المنتج يجب ألا يتجاوز 255 حرفاً."
- `model.max` - "الموديل يجب ألا يتجاوز 100 حرف."
- `brand.max` - "العلامة التجارية يجب ألا تتجاوز 100 حرف."
- `category_id.required` - "يجب اختيار فئة المنتج."
- `category_id.exists` - "الفئة المختارة غير موجودة."
- `description.max` - "الوصف يجب ألا يتجاوز 6000 حرف."
- `specifications.max` - "المواصفات يجب ألا تتجاوز 6000 حرف."
- `features.max` - "المميزات يجب ألا تتجاوز 6000 حرف."
- `technical_data.max` - "البيانات التقنية يجب ألا تتجاوز 6000 حرف."
- `certifications.max` - "الشهادات يجب ألا تتجاوز 6000 حرف."
- `installation_requirements.max` - "متطلبات التثبيت يجب ألا تتجاوز 5000 حرف."
- `images.array` - "الصور يجب أن تكون مصفوفة."
- `images.*.image` - "الملف المرفوع يجب أن يكون صورة."

**Store Scenario (additional):**
- `name.string` - "اسم المنتج يجب أن يكون نصاً."
- `name.max` - "اسم المنتج يجب ألا يتجاوز 255 حرفاً."
- `model.max` - "الموديل يجب ألا يتجاوز 100 حرف."
- `brand.max` - "العلامة التجارية يجب ألا تتجاوز 100 حرف."
- `category_id.exists` - "الفئة المختارة غير موجودة."
- `description.max` - "الوصف يجب ألا يتجاوز 6000 حرف."

