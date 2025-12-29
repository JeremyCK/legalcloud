# Why `bln_invoice` is Inconsistent Between Tables

## Root Cause Analysis

The `bln_invoice` field becomes inconsistent because:

1. **Invoice Creation Doesn't Set `bln_invoice`**
2. **Bill Conversion Doesn't Update Invoice Records**
3. **No Automatic Sync Mechanism**

---

## Issue 1: Invoice Creation Missing `bln_invoice`

**Location**: `app/Http/Controllers/CaseController.php` (Line ~7879-7897)

**Problem**: When creating a new `LoanCaseInvoiceMain` record, the code does NOT set `bln_invoice`:

```php
$loanCaseInvoiceMain = new LoanCaseInvoiceMain();
$loanCaseInvoiceMain->loan_case_main_bill_id = $id;
$loanCaseInvoiceMain->invoice_no = $LoanCaseBillMain->invoice_no;
// ... other fields ...
// ❌ MISSING: $loanCaseInvoiceMain->bln_invoice = 1;
$loanCaseInvoiceMain->status = 1;
$loanCaseInvoiceMain->save();
```

**Result**: `bln_invoice` defaults to `0` in `loan_case_invoice_main`, even though the bill has `bln_invoice = 1`.

---

## Issue 2: Bill Conversion Doesn't Update Invoice Records

**Location**: `app/Http/Controllers/CaseController.php` (Lines ~12521, ~12556)

**Problem**: When converting a quotation to invoice, only the bill-level `bln_invoice` is updated:

```php
// Convert Quotation to Invoice
$LoanCaseBillMain->bln_invoice = 1;  // ✅ Bill updated
$LoanCaseBillMain->save();

// ❌ MISSING: Update corresponding LoanCaseInvoiceMain records
// LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
//     ->update(['bln_invoice' => 1]);
```

**Result**: Bill has `bln_invoice = 1`, but invoice records still have `bln_invoice = 0`.

---

## Issue 3: Invoice Reversion Doesn't Update Invoice Records

**Location**: `app/Http/Controllers/CaseController.php` (Line ~12590)

**Problem**: When reverting invoice to quotation, only the bill-level `bln_invoice` is updated:

```php
// Revert Invoice to Quotation
$LoanCaseBillMain->bln_invoice = 0;  // ✅ Bill updated
$LoanCaseBillMain->save();

if ($request->input('is_reserve_runningno') == 0) {
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->delete();
} else {
    // ❌ MISSING: Update bln_invoice to 0
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
        ->update(['bill_party_id' => 0]);
    // Should also update: ->update(['bln_invoice' => 0]);
}
```

**Result**: If invoices are not deleted, they still have `bln_invoice = 1` even though bill has `bln_invoice = 0`.

---

## Solutions

### Solution 1: Fix Invoice Creation (Recommended)

**File**: `app/Http/Controllers/CaseController.php` (Line ~7893)

**Add**:
```php
$loanCaseInvoiceMain->bln_invoice = $LoanCaseBillMain->bln_invoice ?? 1; // Sync with bill
```

**Full code**:
```php
if (!$LoanCaseInvoiceMain) {
    $loanCaseInvoiceMain = new LoanCaseInvoiceMain();
    $loanCaseInvoiceMain->loan_case_main_bill_id = $id;
    $loanCaseInvoiceMain->invoice_no = $LoanCaseBillMain->invoice_no;
    $loanCaseInvoiceMain->bill_party_id = 0;
    $loanCaseInvoiceMain->remark = "";
    $loanCaseInvoiceMain->Invoice_date = $LoanCaseBillMain->Invoice_date;
    $loanCaseInvoiceMain->amount = $LoanCaseBillMain->total_amt;
    $loanCaseInvoiceMain->pfee1_inv = $LoanCaseBillMain->pfee1_inv;
    $loanCaseInvoiceMain->pfee2_inv = $LoanCaseBillMain->pfee2_inv;
    $loanCaseInvoiceMain->sst_inv = $LoanCaseBillMain->sst;
    $loanCaseInvoiceMain->reimbursement_amount = $LoanCaseBillMain->reimbursement_amount ?? 0;
    $loanCaseInvoiceMain->reimbursement_sst = $LoanCaseBillMain->reimbursement_sst ?? 0;
    $loanCaseInvoiceMain->bln_invoice = $LoanCaseBillMain->bln_invoice ?? 1; // ✅ ADD THIS
    $loanCaseInvoiceMain->created_by = $current_user->id;
    $loanCaseInvoiceMain->status = 1;
    $loanCaseInvoiceMain->created_at = date('Y-m-d H:i:s');
    $loanCaseInvoiceMain->save();
}
```

---

### Solution 2: Fix Bill Conversion

**File**: `app/Http/Controllers/CaseController.php` (Lines ~12521, ~12556)

**Add after** `$LoanCaseBillMain->save();`:
```php
// Sync bln_invoice to invoice records
LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
    ->update(['bln_invoice' => 1]);
```

**Full code**:
```php
$LoanCaseBillMain->bln_invoice = 1;
$LoanCaseBillMain->invoice_branch_id = $LoanCase->branch_id;
$LoanCaseBillMain->invoice_date = date('Y-m-d H:i:s');
$LoanCaseBillMain->invoice_to = $LoanCaseBillMain->bill_to;
$LoanCaseBillMain->save();

// ✅ ADD THIS: Sync bln_invoice to invoice records
LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
    ->update(['bln_invoice' => 1]);
```

---

### Solution 3: Fix Invoice Reversion

**File**: `app/Http/Controllers/CaseController.php` (Line ~12590)

**Update**:
```php
if ($request->input('is_reserve_runningno') == 0) {
    $LoanCaseBillMain->invoice_no = '';
    $revert_invoice_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->count();
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->delete();
} else {
    // ✅ UPDATE THIS: Also set bln_invoice to 0
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
        ->update([
            'bill_party_id' => 0,
            'bln_invoice' => 0  // ✅ ADD THIS
        ]);
}
```

---

## Solution 4: Add Model Observer (Long-term Solution)

Create a model observer to automatically sync `bln_invoice` whenever a `LoanCaseBillMain` is updated:

**File**: `app/Observers/LoanCaseBillMainObserver.php` (Create new file)

```php
<?php

namespace App\Observers;

use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseInvoiceMain;

class LoanCaseBillMainObserver
{
    /**
     * Handle the LoanCaseBillMain "updated" event.
     */
    public function updated(LoanCaseBillMain $loanCaseBillMain)
    {
        // Sync bln_invoice when bill's bln_invoice changes
        if ($loanCaseBillMain->isDirty('bln_invoice')) {
            LoanCaseInvoiceMain::where('loan_case_main_bill_id', $loanCaseBillMain->id)
                ->update(['bln_invoice' => $loanCaseBillMain->bln_invoice]);
        }
    }
}
```

**Register in** `app/Providers/EventServiceProvider.php`:
```php
use App\Models\LoanCaseBillMain;
use App\Observers\LoanCaseBillMainObserver;

public function boot()
{
    LoanCaseBillMain::observe(LoanCaseBillMainObserver::class);
}
```

---

## Immediate Fix: Run Sync SQL

Until the code fixes are deployed, run the sync SQL periodically:

```sql
UPDATE loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
SET im.bln_invoice = bm.bln_invoice
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;
```

---

## Summary

**Root Causes**:
1. Invoice creation doesn't set `bln_invoice`
2. Bill conversion doesn't update invoice records
3. Invoice reversion doesn't update invoice records (when not deleted)

**Recommended Fixes**:
1. ✅ Add `bln_invoice` when creating invoices (Solution 1)
2. ✅ Sync `bln_invoice` when converting/reverting (Solutions 2 & 3)
3. ✅ Add model observer for automatic sync (Solution 4 - long-term)

**Immediate Action**: Run sync SQL to fix existing data











