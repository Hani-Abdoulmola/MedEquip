# Vendor (Supplier) Fixes - Comprehensive Testing Guide

**Date:** 2025-01-27  
**Version:** 1.0  
**Status:** Ready for Testing

---

## 📋 Overview

This guide provides step-by-step testing procedures for all vendor-side fixes implemented in the RFQ and Quotation system. Follow each section systematically to ensure all functionality works correctly.

---

## ✅ Pre-Testing Checklist

Before starting, ensure:
- [ ] Database migrations are run (`php artisan migrate`)
- [ ] At least one verified Supplier account exists
- [ ] At least one Admin account exists
- [ ] At least one Buyer account exists
- [ ] At least one RFQ exists (preferably with items)
- [ ] Supplier has access to the RFQ (assigned or public)

---

## 🧪 Test Suite 1: Items Array Validation

### Test 1.1: Valid Items Submission ✅

**Objective:** Verify suppliers can submit quotations with valid items array.

**Steps:**
1. Login as Supplier
2. Navigate to an RFQ (`/supplier/rfqs/{rfq_id}`)
3. Click "Create Quotation"
4. Fill in quotation form:
   - Total Price: `10000`
   - Valid Until: Future date
   - Add items:
     - Item 1: Select RFQ item, Unit Price: `500`, Lead Time: `7 days`, Warranty: `1 year`
     - Item 2: Select RFQ item, Unit Price: `300`, Lead Time: `5 days`
5. Submit quotation

**Expected Result:**
- ✅ Quotation created successfully
- ✅ Items saved correctly
- ✅ Total price calculated from items
- ✅ Success message displayed

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 1.2: Invalid Item ID (Security Test) 🔒

**Objective:** Verify system rejects items that don't belong to the RFQ.

**Steps:**
1. Login as Supplier
2. Navigate to RFQ A (`/supplier/rfqs/{rfq_a_id}`)
3. Open browser DevTools → Network tab
4. Intercept form submission
5. Modify `items[0].rfq_item_id` to an item from a different RFQ
6. Submit

**Expected Result:**
- ✅ Validation error: "البند لا ينتمي إلى هذا الطلب"
- ✅ Quotation NOT created
- ✅ Error message displayed

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 1.3: Negative Price Validation 🔒

**Objective:** Verify system rejects negative prices.

**Steps:**
1. Login as Supplier
2. Create quotation with item having `unit_price: -100`
3. Submit

**Expected Result:**
- ✅ Validation error: "سعر الوحدة لا يمكن أن يكون سالباً"
- ✅ Quotation NOT created

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 1.4: Missing Required Fields

**Objective:** Verify validation for required item fields.

**Steps:**
1. Login as Supplier
2. Create quotation with items array missing `rfq_item_id` or `unit_price`
3. Submit

**Expected Result:**
- ✅ Validation errors for missing fields
- ✅ Quotation NOT created

**Status:** ⬜ Pass / ⬜ Fail

---

## 🔔 Test Suite 2: Notifications

### Test 2.1: Notification on Quotation Creation 📧

**Objective:** Verify admin and buyer receive notifications when supplier creates quotation.

**Steps:**
1. Login as Supplier
2. Create a new quotation for an RFQ
3. Logout
4. Login as Admin
5. Check notifications/dashboard

**Expected Result:**
- ✅ Admin receives notification: "📋 عرض سعر جديد"
- ✅ Notification includes supplier name and RFQ title
- ✅ Notification links to quotation detail page
- ✅ Buyer (if exists) receives notification: "💰 تم استلام عرض سعر جديد"

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 2.2: Notification on Quotation Update 📧

**Objective:** Verify notifications are sent when supplier updates quotation.

**Steps:**
1. Login as Supplier
2. Edit existing quotation
3. Update price or items
4. Save changes
5. Logout
6. Login as Admin
7. Check notifications

**Expected Result:**
- ✅ Admin receives notification: "📦 تم تحديث عرض سعر"
- ✅ Buyer receives notification: "📦 تم تحديث عرض السعر"
- ✅ Notifications include updated quotation link

**Status:** ⬜ Pass / ⬜ Fail

---

## 👁️ Test Suite 3: Quotation Detail View

### Test 3.1: View Quotation Details ✅

**Objective:** Verify supplier can view full quotation details.

**Steps:**
1. Login as Supplier
2. Navigate to "My Quotations" (`/supplier/quotations`)
3. Click on a quotation
4. Verify all details are displayed

**Expected Result:**
- ✅ Quotation overview displayed (price, dates, terms)
- ✅ All quotation items shown with pricing breakdown
- ✅ Related RFQ information displayed
- ✅ Status badge displayed correctly
- ✅ Rejection reason shown (if rejected)
- ✅ Edit/Delete buttons visible (if pending)

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 3.2: Access Control - Own Quotations Only 🔒

**Objective:** Verify supplier can only view their own quotations.

**Steps:**
1. Login as Supplier A
2. Note a quotation ID from Supplier B (if accessible via URL)
3. Try to access: `/supplier/quotations/{supplier_b_quotation_id}`

**Expected Result:**
- ✅ 403 Forbidden error
- ✅ Or redirect to quotations index
- ✅ Policy prevents unauthorized access

**Status:** ⬜ Pass / ⬜ Fail

---

## ⏰ Test Suite 4: RFQ Deadline Validation

### Test 4.1: Create Quotation Before Deadline ✅

**Objective:** Verify supplier can create quotation before deadline.

**Steps:**
1. Create RFQ with deadline: Tomorrow
2. Login as Supplier
3. Navigate to RFQ
4. Click "Create Quotation"

**Expected Result:**
- ✅ Quotation form loads successfully
- ✅ No deadline error message

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 4.2: Prevent Quotation After Deadline 🔒

**Objective:** Verify system prevents quotation creation after deadline.

**Steps:**
1. Create RFQ with deadline: Yesterday
2. Login as Supplier
3. Navigate to RFQ
4. Click "Create Quotation"

**Expected Result:**
- ✅ Error message: "انتهت فترة تقديم العروض لهذا الطلب"
- ✅ Redirected to RFQ detail page
- ✅ Quotation form NOT accessible

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 4.3: Prevent Quotation Submission After Deadline 🔒

**Objective:** Verify system prevents quotation submission even if form is accessed.

**Steps:**
1. Create RFQ with deadline: Yesterday
2. Login as Supplier
3. Manually access quotation create URL (if possible)
4. Try to submit quotation

**Expected Result:**
- ✅ Validation error on submission
- ✅ Quotation NOT created
- ✅ Error message displayed

**Status:** ⬜ Pass / ⬜ Fail

---

## 🔍 Test Suite 5: RFQ Status Filter

### Test 5.1: Only Open RFQs Shown ✅

**Objective:** Verify only open RFQs are displayed to suppliers.

**Steps:**
1. Create multiple RFQs with different statuses:
   - RFQ 1: Status = `open`
   - RFQ 2: Status = `closed`
   - RFQ 3: Status = `awarded`
   - RFQ 4: Status = `cancelled`
2. Login as Supplier
3. Navigate to RFQs list (`/supplier/rfqs`)

**Expected Result:**
- ✅ Only RFQ 1 (open) is displayed
- ✅ Closed, awarded, cancelled RFQs are NOT shown
- ✅ List is filtered correctly

**Status:** ⬜ Pass / ⬜ Fail

---

## 🔐 Test Suite 6: Policy Authorization

### Test 6.1: Index Method Authorization ✅

**Objective:** Verify policy check on RFQs index.

**Steps:**
1. Login as Supplier
2. Navigate to `/supplier/rfqs`
3. Check browser console for errors

**Expected Result:**
- ✅ Page loads successfully
- ✅ Policy check passes
- ✅ No authorization errors

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 6.2: View RFQ Authorization ✅

**Objective:** Verify policy check on RFQ view.

**Steps:**
1. Login as Supplier
2. Navigate to an accessible RFQ
3. Check authorization

**Expected Result:**
- ✅ RFQ details displayed
- ✅ Policy check passes
- ✅ No 403 errors for authorized RFQs

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 6.3: Quotations Index Authorization ✅

**Objective:** Verify policy check on quotations index.

**Steps:**
1. Login as Supplier
2. Navigate to `/supplier/quotations`
3. Check authorization

**Expected Result:**
- ✅ Quotations list displayed
- ✅ Policy check passes
- ✅ Only own quotations shown

**Status:** ⬜ Pass / ⬜ Fail

---

## 🧹 Test Suite 7: Code Quality (Helper Methods)

### Test 7.1: Price Calculation Helper ✅

**Objective:** Verify price calculation works correctly.

**Steps:**
1. Login as Supplier
2. Create quotation with items:
   - Item 1: Unit Price = 100, Quantity = 5 → Total = 500
   - Item 2: Unit Price = 200, Quantity = 3 → Total = 600
3. Submit quotation

**Expected Result:**
- ✅ Total price = 1100 (calculated from items)
- ✅ Individual item totals correct
- ✅ Helper method works correctly

**Status:** ⬜ Pass / ⬜ Fail

---

### Test 7.2: Item Creation Helper ✅

**Objective:** Verify quotation items are created correctly.

**Steps:**
1. Login as Supplier
2. Create quotation with multiple items
3. Submit
4. View quotation details

**Expected Result:**
- ✅ All items saved correctly
- ✅ Item details (lead_time, warranty, notes) preserved
- ✅ Helper method works correctly

**Status:** ⬜ Pass / ⬜ Fail

---

## 💬 Test Suite 8: Error Messages

### Test 8.1: Improved Error Messages ✅

**Objective:** Verify error messages are user-friendly.

**Steps:**
1. Login as Supplier
2. Create quotation with invalid data
3. Trigger validation errors
4. Check error messages

**Expected Result:**
- ✅ Error messages are clear and helpful
- ✅ Messages guide user to fix issues
- ✅ No technical/internal error details exposed

**Status:** ⬜ Pass / ⬜ Fail

---

## 📊 Test Results Summary

| Test Suite | Tests | Passed | Failed | Status |
|------------|-------|--------|--------|--------|
| 1. Items Validation | 4 | ⬜ | ⬜ | ⬜ |
| 2. Notifications | 2 | ⬜ | ⬜ | ⬜ |
| 3. Quotation Detail View | 2 | ⬜ | ⬜ | ⬜ |
| 4. Deadline Validation | 3 | ⬜ | ⬜ | ⬜ |
| 5. Status Filter | 1 | ⬜ | ⬜ | ⬜ |
| 6. Policy Authorization | 3 | ⬜ | ⬜ | ⬜ |
| 7. Code Quality | 2 | ⬜ | ⬜ | ⬜ |
| 8. Error Messages | 1 | ⬜ | ⬜ | ⬜ |
| **TOTAL** | **18** | **⬜** | **⬜** | **⬜** |

---

## 🐛 Bug Reporting Template

If you find any issues, document them using this template:

```markdown
### Bug Report: [Title]

**Test Suite:** [Number and Name]
**Test Case:** [Specific test]
**Severity:** Critical / High / Medium / Low

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Result:**
- 

**Actual Result:**
- 

**Screenshots/Logs:**
[Attach if available]

**Environment:**
- Browser: 
- PHP Version: 
- Laravel Version: 
```

---

## ✅ Sign-Off

**Tester Name:** _________________  
**Date:** _________________  
**Overall Status:** ⬜ Pass / ⬜ Fail  
**Notes:** _________________

---

**Ready for Production:** ⬜ Yes / ⬜ No

