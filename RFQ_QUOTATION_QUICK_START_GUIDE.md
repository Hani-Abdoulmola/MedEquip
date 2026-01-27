# 🚀 RFQ & Quotation Workflow - Quick Start Guide

**For**: Developers maintaining the MedEquip system  
**Updated**: January 22, 2026

---

## ✅ **What Changed**

The entire RFQ & Quotation workflow has been redesigned from the ground up:

| Aspect | Old System | New System |
|--------|------------|------------|
| State Management | ❌ None | ✅ State Machines |
| Race Conditions | ❌ Vulnerable | ✅ Database Locking |
| Quotation Drafts | ❌ Not supported | ✅ Supported |
| Auto-Expiration | ⚠️ RFQ only | ✅ RFQ + Quotations |
| Audit Trail | ⚠️ Limited | ✅ Complete (12 fields) |
| Controller Logic | 🔴 85 lines | 🟢 30 lines |
| Business Logic | 🔴 Controllers | 🟢 Services |
| Tests | ⚠️ Minimal | ✅ 27 comprehensive |

---

## 🏗️ **New Architecture**

```
HTTP Request
    ↓
Controller (Thin - 20-30 lines)
    ├── Authorization (Policy)
    └── Delegation ↓
         ↓
Workflow Service (Business Logic)
    ├── State Machine (Validates transition)
    ├── Database Locking (Prevents races)
    ├── State Transition
    ├── Side Effects (notifications, logging)
    └── Return Result
         ↓
HTTP Response
```

---

## 📖 **How to Use the New System**

### **1. Accepting a Quotation** (Buyer)

**Controller Method**: `BuyerQuotationController::accept()`

```php
public function accept(
    Request $request, 
    Quotation $quotation, 
    QuotationWorkflowService $workflowService
): RedirectResponse {
    // 1. Check authorization (policy)
    $this->authorize('accept', $quotation);
    
    // 2. Check ownership
    if ($quotation->rfq->buyer_id !== auth()->user()->buyerProfile->id) {
        abort(403);
    }
    
    try {
        // 3. Delegate to workflow service
        $quotation = $workflowService->acceptQuotation($quotation, auth()->user());
        
        // 4. Create order (separate concern)
        $order = $this->createOrderFromQuotation($quotation);
        
        // 5. Return response
        return redirect()->to($order)->with('success', 'Accepted!');
        
    } catch (\InvalidArgumentException $e) {
        // Business rule violation (e.g., "Already awarded")
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}
```

**What Happens Inside `acceptQuotation()`**:
```php
DB::transaction(function() {
    // 1. LOCK RFQ row (prevents race conditions)
    $rfq = Rfq::where(...)->lockForUpdate()->first();
    
    // 2. Validate business rules
    if ($rfq->status === 'awarded') {
        throw new Exception('Already awarded');
    }
    
    // 3. Transition quotation state (draft/pending → accepted)
    $stateMachine->transition($quotation, 'accepted');
    
    // 4. Auto-reject other quotations
    Quotation::where(...)
        ->update(['status' => 'rejected', ...]);
    
    // 5. Transition RFQ state (open/closed → awarded)
    $rfqStateMachine->transition($rfq, 'awarded');
    
    // 6. Send notifications
    NotificationService::send(...);
});
```

---

### **2. Submitting a Quotation** (Supplier)

**Controller Method**: `SupplierRfqController::storeQuote()`

```php
public function storeQuote(
    SupplierQuotationRequest $request, 
    Rfq $rfq, 
    QuotationWorkflowService $workflowService
): RedirectResponse {
    // 1. Check authorization
    $this->authorize('createQuotation', $rfq);
    
    // 2. Check for duplicates
    if ($rfq->hasQuotationFrom($supplier->id)) {
        return redirect()->with('error', 'Already quoted');
    }
    
    try {
        // 3. Create quotation in draft status
        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->id,
            'status' => 'draft', // Start as draft
            'total_price' => $calculatedTotal,
            // ... other fields
        ]);
        
        // 4. Add items
        foreach ($items as $item) {
            QuotationItem::create([...]);
        }
        
        // 5. Submit via workflow service (draft → pending)
        $quotation = $workflowService->submitQuotation($quotation);
        
        // 6. Return
        return redirect()->with('success', 'Submitted!');
        
    } catch (\InvalidArgumentException $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}
```

**What Happens Inside `submitQuotation()`**:
```php
DB::transaction(function() {
    // 1. Validate RFQ can accept quotations
    if (!$rfqStateMachine->canAcceptQuotations($rfq)) {
        throw new Exception('RFQ not accepting quotations');
    }
    
    // 2. Check for duplicates
    if ($rfq->hasQuotationFrom($supplier->id)) {
        throw new Exception('Duplicate quotation');
    }
    
    // 3. Transition state (draft → pending)
    $stateMachine->transition($quotation, 'pending');
    
    // 4. Notify buyer
    NotificationService::send(...);
});
```

---

### **3. Checking State Transitions**

**In Controller** (before attempting action):
```php
$stateMachine = app(RfqStateMachine::class);

// Get allowed transitions
$allowed = $stateMachine->getAllowedTransitions($rfq);
// Returns: ['closed', 'cancelled'] (if rfq is 'open')

// Check if specific transition is allowed
if ($stateMachine->canTransition($rfq, 'closed')) {
    // Show "Close RFQ" button
}

// Get error message
if (!$stateMachine->canTransition($rfq, 'awarded')) {
    $error = $stateMachine->getTransitionError($rfq, 'awarded');
    // Returns: "Cannot award RFQ: no accepted quotation found"
}
```

**In Blade View**:
```blade
@php
    $stateMachine = app(\App\Services\RfqStateMachine::class);
    $allowedTransitions = $stateMachine->getAllowedTransitions($rfq);
@endphp

@if(in_array('closed', $allowedTransitions))
    <button>Close RFQ</button>
@endif

@if(in_array('awarded', $allowedTransitions))
    <button>Award RFQ</button>
@endif
```

---

## 🔧 **Common Operations**

### **Close an RFQ Manually**
```php
$stateMachine = app(RfqStateMachine::class);
$stateMachine->transition($rfq, 'closed');
// Sets: closed_at = now(), status = 'closed'
```

### **Expire Quotations Manually**
```php
$workflowService = app(QuotationWorkflowService::class);
$expired = $workflowService->expireQuotations();
// Returns: number of quotations expired
```

### **Reject a Quotation**
```php
$workflowService = app(QuotationWorkflowService::class);
$workflowService->rejectQuotation(
    $quotation, 
    auth()->user(), 
    'السعر مرتفع جداً'
);
// Sets: status = 'rejected', rejected_at = now(), rejected_by = user_id
```

---

## 🧪 **Running Tests**

```bash
# Run all RFQ/Quotation tests
vendor/bin/phpunit tests/Unit/RfqStateMachineTest.php
vendor/bin/phpunit tests/Unit/QuotationStateMachineTest.php
vendor/bin/phpunit tests/Feature/RfqQuotationWorkflowTest.php

# Or with Pest (if installed)
vendor/bin/pest tests/Unit/RfqStateMachineTest.php
vendor/bin/pest tests/Feature/RfqQuotationWorkflowTest.php

# Or run all tests
vendor/bin/phpunit
```

---

## 🐛 **Troubleshooting**

### **Issue**: "Cannot transition RFQ from 'open' to 'awarded'"
**Cause**: No accepted quotation exists  
**Fix**: Accept a quotation first, then RFQ will auto-transition to 'awarded'

### **Issue**: "Another quotation already accepted for this RFQ"
**Cause**: Attempting to accept second quotation  
**Fix**: This is correct behavior. Only ONE quotation can be accepted per RFQ.

### **Issue**: "Cannot submit quotation: RFQ is not open"
**Cause**: RFQ is closed/cancelled/awarded  
**Fix**: RFQ must be in 'open' status to accept quotations.

### **Issue**: "Cannot edit quotation"
**Cause**: Quotation is in terminal state (accepted/rejected/expired)  
**Fix**: Quotations can only be edited in 'draft' or 'pending' status.

---

## 📊 **State Reference**

### **RFQ States**
| State | Description | Can Edit? | Can Delete? | Can Accept Quotes? |
|-------|-------------|-----------|-------------|-------------------|
| draft | Being composed | ✅ | ✅ | ❌ |
| open | Published, accepting quotes | ⚠️ Limited | ❌ | ✅ |
| closed | No longer accepting quotes | ❌ | ❌ | ❌ |
| awarded | Winner selected | ❌ | ❌ | ❌ |
| cancelled | Cancelled by buyer | ❌ | ❌ | ❌ |

### **Quotation States**
| State | Description | Can Edit? | Can Delete? | Can Accept? |
|-------|-------------|-----------|-------------|-------------|
| draft | Being composed | ✅ | ✅ | ❌ |
| pending | Submitted, awaiting decision | ⚠️ Limited | ❌ | ✅ |
| accepted | Accepted by buyer | ❌ | ❌ | N/A |
| rejected | Rejected by buyer | ❌ | ❌ | ❌ |
| expired | Deadline/validity passed | ❌ | ❌ | ❌ |
| withdrawn | Cancelled by supplier | ❌ | ❌ | ❌ |
| converted | Order created | ❌ | ❌ | N/A |

---

## 🔐 **Security Features**

### **1. Database-Level Locking**
```php
// In QuotationWorkflowService::acceptQuotation()
$rfq = Rfq::where('id', $quotation->rfq_id)
    ->lockForUpdate() // ← This prevents concurrent accepts
    ->first();
```

**Benefit**: Even if two admins click "Accept" at the exact same time, only ONE will succeed.

### **2. Unique Constraint**
```sql
-- In database
CREATE UNIQUE INDEX quotations_unique_accepted_per_rfq 
ON quotations(rfq_id) 
WHERE status = 'accepted'
```

**Benefit**: Database-level guarantee that only one quotation per RFQ can be accepted.

### **3. State Machine Validation**
```php
// Before any transition
if (!$stateMachine->canTransition($rfq, 'awarded')) {
    throw new Exception('Invalid transition');
}
```

**Benefit**: Impossible to reach invalid states (e.g., awarded without accepted quotation).

---

## 📅 **Scheduled Jobs**

```bash
# In routes/console.php:

# Close expired RFQs (and expire their quotations)
Schedule::command('rfqs:close-expired')->hourly();

# Expire quotations past valid_until date
Schedule::command('quotations:expire')->hourly();

# Send deadline reminders
Schedule::command('rfqs:send-reminders --hours=24')->everySixHours();
```

---

## 🎯 **Best Practices**

### **DO**:
✅ Use workflow services for state transitions  
✅ Check `canTransition()` before showing action buttons  
✅ Wrap multi-step operations in transactions  
✅ Use state machines for validation  
✅ Log all state changes via activity log  

### **DON'T**:
❌ Update status directly (`$rfq->update(['status' => 'closed'])`)  
❌ Check business rules in policies (that's for services)  
❌ Mix authorization with validation  
❌ Skip state machine validation  
❌ Forget to use database locking for critical operations  

---

## 📚 **Complete File Reference**

### **Core Services**:
- `app/Services/RfqStateMachine.php` - RFQ state transitions
- `app/Services/QuotationStateMachine.php` - Quotation state transitions
- `app/Services/QuotationWorkflowService.php` - Quotation business logic
- `app/Services/RfqWorkflowService.php` - RFQ business logic (updated)

### **Controllers** (Refactored):
- `app/Http/Controllers/Web/Buyers/BuyerQuotationController.php`
- `app/Http/Controllers/Web/Suppliers/SupplierRfqController.php`

### **Policies** (Updated):
- `app/Policies/RfqPolicy.php` (auth only, no business rules)
- `app/Policies/QuotationPolicy.php` (auth only, no business rules)

### **Migrations**:
- `database/migrations/2026_01_22_213407_add_workflow_fields_to_rfqs_table.php`
- `database/migrations/2026_01_22_213408_add_workflow_fields_to_quotations_table.php`
- `database/migrations/2026_01_22_213409_update_quotation_status_enum.php`
- `database/migrations/2026_01_22_213443_add_unique_accepted_quotation_constraint.php`

### **Tests**:
- `tests/Unit/RfqStateMachineTest.php` (14 tests)
- `tests/Unit/QuotationStateMachineTest.php` (14 tests)
- `tests/Feature/RfqQuotationWorkflowTest.php` (13 tests)

### **Commands**:
- `app/Console/Commands/CloseExpiredRfqs.php` (updated)
- `app/Console/Commands/ExpireQuotations.php` (new)

---

## 🎉 **Quick Reference Card**

### **Accept a Quotation** (Buyer):
```php
$workflowService = app(QuotationWorkflowService::class);
$quotation = $workflowService->acceptQuotation($quotation, auth()->user());
// Result: Quotation accepted, RFQ awarded, others rejected, notifications sent
```

### **Reject a Quotation** (Buyer):
```php
$workflowService = app(QuotationWorkflowService::class);
$quotation = $workflowService->rejectQuotation($quotation, auth()->user(), $reason);
// Result: Quotation rejected, supplier notified
```

### **Submit a Quotation** (Supplier):
```php
$workflowService = app(QuotationWorkflowService::class);
$quotation = $workflowService->submitQuotation($quotation);
// Result: Quotation submitted (draft → pending), buyer notified
```

### **Close an RFQ**:
```php
$stateMachine = app(RfqStateMachine::class);
$rfq = $stateMachine->transition($rfq, 'closed');
// Result: RFQ closed, pending quotations expired
```

### **Check Allowed Transitions**:
```php
$stateMachine = app(RfqStateMachine::class);
$allowed = $stateMachine->getAllowedTransitions($rfq);
// Returns: ['closed', 'cancelled'] (for open RFQ)
```

---

**END OF GUIDE**

For complete analysis and architecture details, see:
- `RFQ_QUOTATION_SYSTEM_ANALYSIS.md` (full analysis)
- `RFQ_QUOTATION_IMPLEMENTATION_COMPLETE.md` (implementation details)
