# Complete Fix: Sync `bln_invoice` and `bln_sst` Between Tables

## Overview
Whenever `bln_invoice` or `bln_sst` is updated in `loan_case_bill_main`, it should also be updated in `loan_case_invoice_main` to maintain consistency.

---

## Part 1: `bln_invoice` Sync Fixes

### Fix 1: Invoice Creation - Set `bln_invoice`
**File**: `app/Http/Controllers/CaseController.php` (Line ~7893)

**Current Code**:
```php
$loanCaseInvoiceMain = new LoanCaseInvoiceMain();
$loanCaseInvoiceMain->loan_case_main_bill_id = $id;
// ... other fields ...
// ❌ MISSING: bln_invoice
$loanCaseInvoiceMain->status = 1;
$loanCaseInvoiceMain->save();
```

**Fix**:
```php
$loanCaseInvoiceMain->bln_invoice = $LoanCaseBillMain->bln_invoice ?? 1; // ✅ ADD THIS
```

---

### Fix 2: Convert Quotation to Invoice
**File**: `app/Http/Controllers/CaseController.php` (Line ~12525)

**Current Code**:
```php
$LoanCaseBillMain->bln_invoice = 1;
$LoanCaseBillMain->invoice_branch_id = $LoanCase->branch_id;
$LoanCaseBillMain->invoice_date = date('Y-m-d H:i:s');
$LoanCaseBillMain->invoice_to = $LoanCaseBillMain->bill_to;
$LoanCaseBillMain->save();
// ❌ MISSING: Update invoice records
```

**Fix** - Add after `$LoanCaseBillMain->save();`:
```php
// ✅ ADD THIS: Sync bln_invoice to invoice records
LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
    ->update(['bln_invoice' => 1]);
```

**Also fix in**: `app/Http/Controllers/CaseController.php` (Line ~12560) - Same function `SplitInvoice`

---

### Fix 3: Revert Invoice to Quotation
**File**: `app/Http/Controllers/CaseController.php` (Line ~12590)

**Current Code**:
```php
$LoanCaseBillMain->bln_invoice = 0;
$LoanCaseBillMain->total_amt_inv = 0;

if ($request->input('is_reserve_runningno') == 0) {
    $LoanCaseBillMain->invoice_no = '';
    $revert_invoice_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->count();
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->delete();
} else {
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
        ->update(['bill_party_id' => 0]);
    // ❌ MISSING: Update bln_invoice to 0
}
```

**Fix**:
```php
} else {
    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
        ->update([
            'bill_party_id' => 0,
            'bln_invoice' => 0  // ✅ ADD THIS
        ]);
}
```

---

### Fix 4: Admin Controller - Same Functions
**File**: `app/Http/Controllers/admin/CaseController.php`

Apply the same fixes to:
- Line ~12360: `ConvertQuotationToInvoice`
- Line ~12395: `SplitInvoice`
- Line ~12425: `RevertInvoiceBacktoQuotation`

---

## Part 2: `bln_sst` Sync Fixes

### Fix 5: Convert to SST
**File**: `app/Http/Controllers/CaseController.php` (Line ~12664)

**Current Code**:
```php
$LoanCaseBillMain->bln_sst = 1;
$LoanCaseBillMain->save();
// ❌ MISSING: Update invoice records
```

**Fix** - Add after `$LoanCaseBillMain->save();`:
```php
// ✅ ADD THIS: Sync bln_sst to invoice records
LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
    ->update(['bln_sst' => 1]);
```

**Also fix in**: `app/Http/Controllers/admin/CaseController.php` (Line ~12500)

---

### Fix 6: SST V1 - Create SST Record
**File**: `app/Http/Controllers/AccountController.php` (Line ~2558)

**Current Code**:
```php
LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
// ❌ MISSING: Update invoice records
```

**Fix** - Add after the update:
```php
LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['bln_sst' => 1]);

// ✅ ADD THIS: Sync bln_sst to invoice records
LoanCaseInvoiceMain::where('loan_case_main_bill_id', $add_bill[$i]['id'])
    ->update(['bln_sst' => 1]);
```

**Also fix in**:
- `app/Http/Controllers/AccountController.php` (Line ~2652)
- `app/Http/Controllers/EInvoiceContoller.php` (Line ~2615, ~2709)
- `app/Http/Controllers/EInvoiceContollerV2.php` (Line ~2277, ~2371)
- `app/Http/Controllers/admin/EInvoiceContollerV2.php` (Line ~1836, ~1930)
- All backup/copy files (if still in use)

---

### Fix 7: SST V1 - Delete SST Detail
**File**: `app/Http/Controllers/AccountController.php` (Line ~2690)

**Current Code**:
```php
LoanCaseBillMain::where('id', '=', $SSTDetails->loan_case_main_bill_id)->update(['bln_sst' => 0]);
// ❌ MISSING: Update invoice records
```

**Fix** - Add after the update:
```php
LoanCaseBillMain::where('id', '=', $SSTDetails->loan_case_main_bill_id)->update(['bln_sst' => 0]);

// ✅ ADD THIS: Sync bln_sst to invoice records
LoanCaseInvoiceMain::where('loan_case_main_bill_id', $SSTDetails->loan_case_main_bill_id)
    ->update(['bln_sst' => 0]);
```

**Also fix in**:
- `app/Http/Controllers/EInvoiceContoller.php` (Line ~2747)
- `app/Http/Controllers/EInvoiceContollerV2.php` (Line ~2409)
- `app/Http/Controllers/admin/EInvoiceContollerV2.php` (Line ~1968)
- All backup/copy files (if still in use)

---

### Fix 8: SST V2 - Already Correct! ✅
**File**: `app/Http/Controllers/SSTV2Controller.php` (Line ~626-629)

**Current Code** (Already correct):
```php
LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['id'])->update([
    'transferred_sst_amt' => $add_bill[$i]['value'],
    'bln_sst' => 1
]);
```

**Note**: SST V2 updates invoice records directly, but we should also update bill records for consistency.

**Enhancement** - Add after invoice update:
```php
LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['id'])->update([
    'transferred_sst_amt' => $add_bill[$i]['value'],
    'bln_sst' => 1
]);

// ✅ ADD THIS: Also sync to bill record
$invoice = LoanCaseInvoiceMain::find($add_bill[$i]['id']);
if ($invoice && $invoice->loan_case_main_bill_id) {
    LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)
        ->update(['bln_sst' => 1]);
}
```

**Also check**: `updateSSTV2` method - ensure it syncs both ways

---

### Fix 9: SST V2 - Delete SST Detail
**File**: `app/Http/Controllers/SSTV2Controller.php` (Line ~816-820)

**Current Code**:
```php
LoanCaseInvoiceMain::where('id', $invoiceMainId)->update([
    'bln_sst' => 0,
    'transferred_sst_amt' => 0,
    'transferred_reimbursement_sst_amt' => 0
]);
// ❌ MISSING: Update bill record
```

**Fix** - Add after invoice update:
```php
// ✅ ADD THIS: Also sync to bill record
$invoice = LoanCaseInvoiceMain::find($invoiceMainId);
if ($invoice && $invoice->loan_case_main_bill_id) {
    // Check if other invoices for this bill still have bln_sst = 1
    $otherInvoicesWithSst = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
        ->where('id', '!=', $invoiceMainId)
        ->where('bln_sst', 1)
        ->exists();
    
    if (!$otherInvoicesWithSst) {
        LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)
            ->update(['bln_sst' => 0]);
    }
}
```

---

## Summary of All Fixes Needed

### `bln_invoice` Sync (4 fixes):
1. ✅ Invoice creation - Set `bln_invoice` from bill
2. ✅ Convert quotation to invoice - Update invoice records
3. ✅ Revert invoice to quotation - Update invoice records
4. ✅ Admin controller - Same fixes

### `bln_sst` Sync (5 fixes):
1. ✅ Convert to SST - Update invoice records
2. ✅ SST V1 create - Update invoice records (multiple files)
3. ✅ SST V1 delete - Update invoice records (multiple files)
4. ✅ SST V2 create - Update bill records (enhancement)
5. ✅ SST V2 delete - Update bill records

---

## Implementation Priority

**High Priority** (Affects current functionality):
- Fix 1: Invoice creation
- Fix 2 & 3: Convert/revert invoice
- Fix 6 & 7: SST V1 create/delete

**Medium Priority** (Enhancement):
- Fix 5: Convert to SST
- Fix 8 & 9: SST V2 enhancements

**Low Priority** (Backup files):
- Fix 4: Admin controller (if actively used)
- Backup/copy files (if still in use)

---

## Testing Checklist

After implementing fixes, test:
- [ ] Create new invoice - `bln_invoice` should be synced
- [ ] Convert quotation to invoice - invoice records should have `bln_invoice = 1`
- [ ] Revert invoice to quotation - invoice records should have `bln_invoice = 0`
- [ ] Create SST V1 record - invoice records should have `bln_sst = 1`
- [ ] Delete SST V1 detail - invoice records should have `bln_sst = 0`
- [ ] Create SST V2 record - both invoice and bill should have `bln_sst = 1`
- [ ] Delete SST V2 detail - both invoice and bill should have `bln_sst = 0` (if no other invoices)







