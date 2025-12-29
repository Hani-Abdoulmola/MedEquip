# Quick Testing Guide - Step by Step

**Start Here:** Follow these steps in order to test all improvements.

---

## 🚀 Pre-Testing Setup

### 1. Clear Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 2. Verify Routes
```bash
php artisan route:list | grep rfqs.items
php artisan route:list | grep quotations.compare
```

**Expected:** Should see 5 RFQ items routes and 1 comparison route.

---

## 📝 Test 1: RFQ Items Management (15 minutes)

### Step 1.1: Create an RFQ Item

1. **Login as Admin**
   - URL: `/admin/login`
   - Use admin credentials

2. **Navigate to RFQs**
   - Go to: `/admin/rfqs`
   - Click on an existing RFQ (or create new one)
   - Status should be "draft" or "open"

3. **Add Item**
   - Look for "إضافة عنصر" button in the "العناصر المطلوبة" section
   - Click it
   - **Expected:** Form opens at `/admin/rfqs/{rfq_id}/items/create`

4. **Fill Form:**
   ```
   Product: (Optional - leave empty or select one)
   Item Name: جهاز أشعة سينية محمول
   Quantity: 5
   Unit: قطعة
   Specifications: جهاز أشعة سينية محمول للمستشفيات - مواصفات تقنية عالية
   ```

5. **Submit**
   - Click "إضافة العنصر"
   - **Expected:**
     - ✅ Redirect to RFQ show page
     - ✅ Success message: "✅ تم إضافة البند بنجاح"
     - ✅ Item appears in items table

**✅ Mark as Pass/Fail:** ⬜

---

### Step 1.2: Edit RFQ Item

1. **Find the item you just created**
   - In RFQ show page, items table
   - Look for edit icon (✏️) next to the item

2. **Click Edit**
   - **Expected:** Form opens at `/admin/rfqs/{rfq_id}/items/{item_id}/edit`

3. **Change Quantity**
   - Change from 5 to 10
   - Update specifications

4. **Save**
   - Click "حفظ التعديلات"
   - **Expected:**
     - ✅ Redirect to RFQ show page
     - ✅ Success message
     - ✅ Quantity updated to 10 in table

**✅ Mark as Pass/Fail:** ⬜

---

### Step 1.3: Test Validation

1. **Try to create item with empty name**
   - Go to create form
   - Leave "Item Name" empty
   - Fill other fields
   - Submit
   - **Expected:** ❌ Validation error in Arabic

2. **Try quantity = 0**
   - Set quantity to 0
   - Submit
   - **Expected:** ❌ Validation error

**✅ Mark as Pass/Fail:** ⬜

---

### Step 1.4: Delete Item (No Quotations)

1. **Find an item with NO quotations**
   - In RFQ show page
   - Click delete icon (🗑️)
   - Confirm deletion
   - **Expected:**
     - ✅ Item deleted
     - ✅ Success message
     - ✅ Item removed from table

**✅ Mark as Pass/Fail:** ⬜

---

### Step 1.5: Test Item Protection

1. **Create a quotation for an RFQ item**
   - Login as Supplier
   - Create quotation with items
   - Logout

2. **Try to delete that item**
   - Login as Admin
   - Go to RFQ
   - Try to delete item that has quotations
   - **Expected:**
     - ❌ Error: "لا يمكن حذف البند - يوجد عروض أسعار مرتبطة به"
     - ✅ Item NOT deleted

**✅ Mark as Pass/Fail:** ⬜

---

## 📊 Test 2: Quotation Comparison (10 minutes)

### Step 2.1: Access Comparison View

1. **Navigate to RFQ with multiple quotations**
   - Go to `/admin/rfqs`
   - Find RFQ with 2+ quotations
   - Click on RFQ

2. **Open Comparison**
   - Look for "مقارنة العروض" button/link
   - Click it
   - **Expected:** Comparison table displays

**✅ Mark as Pass/Fail:** ⬜

---

### Step 2.2: Test Sorting

1. **Sort by Price (Ascending)**
   - Find "ترتيب حسب" dropdown
   - Select "السعر: من الأقل للأعلى"
   - **Expected:**
     - ✅ Table refreshes
     - ✅ Lowest price in first column
     - ✅ Highest price in last column

2. **Sort by Price (Descending)**
   - Select "السعر: من الأعلى للأقل"
   - **Expected:**
     - ✅ Highest price first

3. **Sort by Date**
   - Select "التاريخ: من الأحدث للأقدم"
   - **Expected:**
     - ✅ Most recent quotation first

**✅ Mark as Pass/Fail:** ⬜

---

### Step 2.3: Test Filtering

1. **Filter by Status**
   - Find "فلترة حسب الحالة" dropdown
   - Select "قيد المراجعة"
   - **Expected:**
     - ✅ Only pending quotations shown
     - ✅ Statistics recalculated

2. **Reset Filters**
   - Click "إعادة تعيين"
   - **Expected:**
     - ✅ All quotations shown
     - ✅ Filters cleared

**✅ Mark as Pass/Fail:** ⬜

---

### Step 2.4: Check Statistics

1. **Look at Statistics Section**
   - Should see 4 boxes:
     - أقل سعر (Minimum Price)
     - أعلى سعر (Maximum Price)
     - متوسط السعر (Average Price)
     - نطاق السعر (Price Range)
   - **Expected:**
     - ✅ All values calculated correctly
     - ✅ Values formatted with currency

**✅ Mark as Pass/Fail:** ⬜

---

### Step 2.5: Check Visual Indicators

1. **Check Price Row**
   - Look for green highlighting on lowest price
   - Look for red highlighting on highest price
   - **Expected:**
     - ✅ "✓ أقل سعر" badge on lowest
     - ✅ "أعلى سعر" badge on highest

2. **Check Lead Time Row**
   - Look for fastest delivery highlighted
   - **Expected:**
     - ✅ "✓ أسرع توصيل" badge

**✅ Mark as Pass/Fail:** ⬜

---

## 📋 Test 3: Activity Logging (5 minutes)

### Step 3.1: Check RFQ Update Log

1. **Edit an RFQ**
   - Change title or status
   - Save

2. **Check Activity Logs**
   - Navigate to activity logs (if available)
   - Or check database: `activity_log` table
   - Find latest entry
   - **Expected:**
     - ✅ Log includes RFQ ID, title, reference code
     - ✅ Log includes status, buyer_id
     - ✅ Log includes all changed fields
     - ✅ Log message includes RFQ title

**✅ Mark as Pass/Fail:** ⬜

---

### Step 3.2: Check Item Creation Log

1. **Create an RFQ item**
   - Follow Step 1.1

2. **Check Activity Logs**
   - Find entry with log_name = "admin_rfq_items"
   - **Expected:**
     - ✅ Log includes RFQ ID, item name, quantity
     - ✅ Log message: "تم إضافة بند جديد إلى الطلب"

**✅ Mark as Pass/Fail:** ⬜

---

## 🐛 Common Issues & Fixes

### Issue: Routes not found
**Fix:**
```bash
php artisan route:clear
php artisan config:clear
```

### Issue: Views not found
**Fix:**
```bash
php artisan view:clear
# Verify files exist:
ls -la resources/views/admin/rfqs/items/
```

### Issue: 403 Forbidden on items
**Fix:** Check RfqPolicy - ensure `update` method allows admin

### Issue: Statistics not showing
**Fix:** Check if RFQ has quotations. Statistics only show if quotations exist.

---

## ✅ Final Checklist

- [ ] All RFQ items routes working
- [ ] Create item works
- [ ] Edit item works
- [ ] Delete item works (with protection)
- [ ] Validation works
- [ ] Comparison view loads
- [ ] Sorting works
- [ ] Filtering works
- [ ] Statistics display correctly
- [ ] Visual indicators work
- [ ] Activity logs created

---

## 📝 Notes Section

**Issues Found:**
1. _________________
2. _________________
3. _________________

**Suggestions:**
1. _________________
2. _________________

---

**Happy Testing! 🎉**

