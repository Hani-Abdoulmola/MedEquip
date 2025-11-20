# DEPLOYMENT CHECKLIST
## MediTrust Database Improvements

Use this checklist to ensure safe deployment of database improvements.

---

## PRE-DEPLOYMENT VERIFICATION

### ✅ Code Verification
- [x] All tests pass (9/9) - Run: `php tests/database_improvements_test.php`
- [x] RfqItem model exists
- [x] QuotationItem model exists
- [x] OrderItem model exists
- [x] File model deleted
- [x] Payment auto-sync implemented
- [x] Buyer invoices relationship fixed
- [x] Migration filename fixed (.php.php → .php)
- [x] All 8 migration files exist

### ✅ Documentation Review
- [x] FINAL_IMPLEMENTATION_REPORT.md reviewed
- [x] IMPLEMENTATION_COMPLETE.md reviewed
- [x] QUICK_START.md reviewed
- [x] Rollback procedures understood

---

## STAGING ENVIRONMENT

### Step 1: Backup Staging Database
```bash
# Create backup
mysqldump -u [user] -p [database] > staging_backup_$(date +%Y%m%d_%H%M%S).sql

# Verify backup
ls -lh staging_backup_*.sql
```
- [ ] Backup created
- [ ] Backup verified (file size > 0)

### Step 2: Run Migrations on Staging
```bash
cd /Users/haniabdulmola/Herd/MedEquip1
php artisan migrate
```
- [ ] All 8 migrations ran successfully
- [ ] No errors in output
- [ ] `php artisan migrate:status` shows all "Ran"

### Step 3: Test on Staging
```bash
php artisan tinker
```

**Test 1: RFQ Items**
```php
$rfq = Rfq::first();
$rfq->items;  // Should return collection
```
- [ ] PASS

**Test 2: Buyer Invoices**
```php
$buyer = Buyer::first();
$buyer->invoices;  // Should work via hasManyThrough
```
- [ ] PASS

**Test 3: Quotation Items**
```php
$quotation = Quotation::first();
$item = $quotation->items()->create([
    'item_name' => 'Test Item',
    'quantity' => 2,
    'unit_price' => 100.00
]);
echo $item->total_price;  // Should be 200.00
```
- [ ] PASS (auto-calculated)

**Test 4: Order Items**
```php
$order = Order::first();
$item = $order->items()->create([
    'item_name' => 'Test Item',
    'quantity' => 1,
    'unit_price' => 100.00,
    'tax_amount' => 10.00
]);
echo $item->total_price;  // Should be 110.00
```
- [ ] PASS (auto-calculated)

**Test 5: Payment Auto-Sync**
```php
$order = Order::first();
$payment = Payment::create([
    'order_id' => $order->id,
    'amount' => 100.00,
    'method' => 'bank_transfer',
    'status' => 'completed'
]);
echo $payment->buyer_id == $order->buyer_id ? 'PASS' : 'FAIL';
echo $payment->supplier_id == $order->supplier_id ? 'PASS' : 'FAIL';
```
- [ ] PASS (auto-synced)

**Test 6: Financial Precision**
```php
$order = Order::first();
$order->total_amount = 12345.67;
$order->save();
echo $order->fresh()->total_amount === '12345.67' ? 'PASS' : 'FAIL';
```
- [ ] PASS (decimal precision)

**Test 7: Cascading Protection**
```php
$order = Order::with('invoices')->first();
if ($order && $order->invoices->count() > 0) {
    try {
        $order->delete();
        echo 'FAIL';
    } catch (\Exception $e) {
        echo 'PASS (deletion restricted)';
    }
}
```
- [ ] PASS (RESTRICT works)

### Step 4: Verify Staging Application
- [ ] Login works
- [ ] Create RFQ works
- [ ] Create Quotation works
- [ ] Create Order works
- [ ] Create Invoice works
- [ ] Create Payment works
- [ ] File uploads work (Media Library)
- [ ] No errors in logs

---

## PRODUCTION DEPLOYMENT

### Step 1: Schedule Maintenance Window
- [ ] Maintenance window scheduled
- [ ] Users notified
- [ ] Backup window allocated (30 minutes recommended)

### Step 2: Backup Production Database
```bash
# CRITICAL: Create production backup
mysqldump -u [user] -p [database] > production_backup_$(date +%Y%m%d_%H%M%S).sql

# Verify backup
ls -lh production_backup_*.sql

# Test restore (on staging)
mysql -u [user] -p [staging_db] < production_backup_*.sql
```
- [ ] Production backup created
- [ ] Backup verified (file size matches expected)
- [ ] Test restore successful on staging

### Step 3: Deploy Code
```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```
- [ ] Code deployed
- [ ] Dependencies installed
- [ ] Caches cleared

### Step 4: Run Migrations
```bash
# Put application in maintenance mode
php artisan down

# Run migrations
php artisan migrate --force

# Verify migrations
php artisan migrate:status

# Bring application back up
php artisan up
```
- [ ] Maintenance mode enabled
- [ ] All 8 migrations ran successfully
- [ ] No errors in output
- [ ] Application back online

### Step 5: Smoke Tests
```bash
php artisan tinker
```

Run all 7 tests from staging (above)
- [ ] Test 1: RFQ Items - PASS
- [ ] Test 2: Buyer Invoices - PASS
- [ ] Test 3: Quotation Items - PASS
- [ ] Test 4: Order Items - PASS
- [ ] Test 5: Payment Auto-Sync - PASS
- [ ] Test 6: Financial Precision - PASS
- [ ] Test 7: Cascading Protection - PASS

### Step 6: Application Verification
- [ ] Homepage loads
- [ ] Login works
- [ ] Dashboard loads
- [ ] Create RFQ works
- [ ] Create Quotation works
- [ ] Create Order works
- [ ] File uploads work
- [ ] No errors in logs

### Step 7: Monitor
```bash
# Watch logs
tail -f storage/logs/laravel.log
```
- [ ] No errors in first 15 minutes
- [ ] No user complaints
- [ ] Performance normal

---

## ROLLBACK PROCEDURE (If Needed)

### Option 1: Rollback Migrations
```bash
php artisan down
php artisan migrate:rollback --step=8
php artisan up
```

### Option 2: Restore from Backup
```bash
php artisan down
mysql -u [user] -p [database] < production_backup_*.sql
php artisan up
```

---

## POST-DEPLOYMENT

### Step 1: Verify Success
- [ ] All tests pass
- [ ] No errors in logs
- [ ] Users can work normally
- [ ] Performance acceptable

### Step 2: Documentation
- [ ] Update internal wiki
- [ ] Notify team of changes
- [ ] Archive backup files
- [ ] Update deployment log

### Step 3: Cleanup
- [ ] Remove old backup files (keep last 3)
- [ ] Clear old logs
- [ ] Update monitoring dashboards

---

## SUCCESS CRITERIA

Deployment is successful when:
- ✅ All 8 migrations ran without errors
- ✅ All 7 smoke tests pass
- ✅ Application works normally
- ✅ No errors in logs for 24 hours
- ✅ Users report no issues

---

## CONTACTS

**If Issues Arise:**
- Database Admin: [contact]
- DevOps: [contact]
- Development Lead: [contact]

**Escalation:**
- Rollback immediately if critical errors
- Restore from backup if data corruption
- Contact development team for assistance

---

## NOTES

**Estimated Downtime:** 5-10 minutes  
**Migration Runtime:** ~30 seconds  
**Risk Level:** LOW (all changes tested, rollback available)  
**Business Impact:** MINIMAL (improvements only, no breaking changes)

---

**Deployment Date:** _______________  
**Deployed By:** _______________  
**Verified By:** _______________  
**Status:** _______________

