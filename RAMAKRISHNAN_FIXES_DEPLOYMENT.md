# Ramakrishnan Branch Invoice Fixes - Deployment Summary

## Overview
This document lists all the fixes needed to resolve the issue where Ramakrishnan branch invoices were not appearing in the SST v2 create page.

## Files Modified

### 1. `app/Http/Controllers/SSTV2Controller.php`

#### Change 1: Type Conversion for Branch Filter (Line ~282-288)
**Purpose**: Ensure branch filter parameter is properly converted to integer for comparison

```php
$filterBranch = $request->input('filter_branch');
// Convert to integer if not empty, to ensure proper comparison with branch IDs
if ($filterBranch !== null && $filterBranch !== '') {
    $filterBranch = (int) $filterBranch;
} else {
    $filterBranch = null;
}
```

#### Change 2: Fixed Branch Filtering Logic (Line ~332-372)
**Purpose**: Check selected branch first before applying accessible branches filter

**Before**: Branch access filter was applied first, which could exclude selected branch
**After**: If user selects a specific branch, check access and filter by that branch first

```php
// Get accessible branches for user (already handles admin/account roles)
$accessibleBranches = \App\Services\BranchAccessService::getAccessibleBranchIds($current_user);

// Apply branch filtering
// If user selects a specific branch from dropdown, check if they have access and apply that filter
// Otherwise, apply the user's accessible branches filter
if ($filterBranch && $filterBranch != 0) {
    // User selected a specific branch - check if they have access
    if (in_array($filterBranch, $accessibleBranches)) {
        // User has access to selected branch - filter by that branch with fallback to case branch_id
        $query->where(function($q) use ($filterBranch) {
            $q->where('b.invoice_branch_id', $filterBranch)
              ->orWhere(function($subQ) use ($filterBranch) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', $filterBranch);
              });
        });
    } else {
        // User doesn't have access to selected branch - return empty result
        $query->whereRaw('1 = 0'); // Force no results
    }
} else {
    // No specific branch selected - apply user's accessible branches filter with fallback to case branch_id
    // ... (existing logic)
}
```

#### Change 3: Added `im.bln_invoice` Check (Line ~327-328)
**Purpose**: Ensure both bill and invoice level `bln_invoice` flags are checked

```php
->where('b.bln_invoice', '=', 1)  // Bill is an invoice
->where('im.bln_invoice', '=', 1)  // Invoice flag is set (should match bill level)
```

#### Change 4: Removed `im.sst_inv > 0` Check (Line ~329)
**Purpose**: Allow invoices with only reimbursement SST to appear

**Removed**:
```php
->where('im.sst_inv', '>', 0)  // Invoice has SST amount
```

#### Change 5: Removed Remaining SST Check (Line ~429-431)
**Purpose**: Since `bln_sst = 0` already indicates SST not transferred, no need to check remaining amounts

**Removed**:
```php
// For normal selection and 'add' type, exclude invoices with zero remaining SST (including reimbursement SST)
// Remaining SST = (sst_inv - transferred_sst_amt) + (reimbursement_sst - transferred_reimbursement_sst_amt)
$query = $query->whereRaw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0');
```

**Replaced with**:
```php
// For normal selection and 'add' type, bln_sst = 0 is sufficient to indicate SST hasn't been transferred
// No need to check remaining SST amounts as bln_sst flag already handles this
```

---

### 2. `resources/views/dashboard/sst-v2/table/tbl-sst-invoice-list.blade.php`

#### Change: Fixed SST Display Logic (Line ~27-33)
**Purpose**: Show full SST amounts when `bln_sst = 0` (not transferred), not remaining amounts

**Before**:
```php
$totalSst = $row->sst_inv ?? 0;
$remainingSst = max(0, $totalSst - ($row->transferred_sst_amt ?? 0));
$totalReimbSst = $row->reimbursement_sst ?? 0;
$remainingReimbSst = max(0, $totalReimbSst - ($row->transferred_reimbursement_sst_amt ?? 0));
$totalSstAmount = $remainingSst + $remainingReimbSst;
```

**After**:
```php
// If bln_sst = 0, invoice hasn't been transferred, so show full SST amounts
// If bln_sst = 1, invoice has been transferred, so show remaining SST
$totalSst = $row->sst_inv ?? 0;
$totalReimbSst = $row->reimbursement_sst ?? 0;

if (($row->bln_sst ?? 0) == 0) {
    // Not transferred yet - show full amounts
    $remainingSst = $totalSst;
    $remainingReimbSst = $totalReimbSst;
} else {
    // Already transferred - show remaining amounts
    $remainingSst = max(0, $totalSst - ($row->transferred_sst_amt ?? 0));
    $remainingReimbSst = max(0, $totalReimbSst - ($row->transferred_reimbursement_sst_amt ?? 0));
}

$totalSstAmount = $remainingSst + $remainingReimbSst;
```

---

### 3. `resources/views/dashboard/sst-v2/create.blade.php`

#### Change: Added CSRF Token to AJAX Request (Line ~1057-1069)
**Purpose**: Fix CSRF token mismatch error when saving SST records

**Before**:
```php
var form_data = new FormData();
form_data.append("add_bill", JSON.stringify(invoiceData));
form_data.append("trx_id", $("#trx_id").val());
form_data.append("payment_date", $("#payment_date").val());
form_data.append("remark", $("#remark").val());
form_data.append("branch", $("#branch_sst").val());

$.ajax({
    type: 'POST',
    url: '/createNewSSTRecordV2',
    data: form_data,
    processData: false,
    contentType: false,
```

**After**:
```php
var form_data = new FormData();
form_data.append("add_bill", JSON.stringify(invoiceData));
form_data.append("trx_id", $("#trx_id").val());
form_data.append("payment_date", $("#payment_date").val());
form_data.append("remark", $("#remark").val());
form_data.append("branch", $("#branch_sst").val());
// Add CSRF token
form_data.append("_token", $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val());

$.ajax({
    type: 'POST',
    url: '/createNewSSTRecordV2',
    data: form_data,
    processData: false,
    contentType: false,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
    },
```

---

## Summary of Changes

1. **Branch Filter Type Conversion**: Fixed string to integer conversion for branch filter parameter
2. **Branch Filtering Logic**: Fixed order of branch filtering to check selected branch first
3. **Invoice Flag Check**: Added `im.bln_invoice = 1` check to ensure consistency
4. **Removed SST Amount Check**: Removed `im.sst_inv > 0` requirement (allows invoices with only reimbursement SST)
5. **Removed Remaining SST Check**: Removed remaining SST calculation check (relies on `bln_sst` flag instead)
6. **Fixed SST Display**: Show full SST amounts when `bln_sst = 0`, not remaining amounts
7. **Fixed CSRF Token**: Added CSRF token to AJAX FormData and headers

## Testing Checklist

After deployment, verify:
- [ ] Ramakrishnan branch invoices appear when branch filter is selected
- [ ] SST, Reimb SST, and Total SST columns show correct values (not 0.00)
- [ ] Invoices can be selected and added to transfer list
- [ ] SST record can be saved without CSRF token error
- [ ] Other branches still work correctly
- [ ] Invoices with `bln_sst = 1` are still hidden (not shown in list)

## Database Requirements

No database migrations required. However, you may want to sync data:
- `bln_invoice` should be synced between `loan_case_invoice_main` and `loan_case_bill_main`
- `sst_inv` should be synced from `loan_case_bill_main.sst` to `loan_case_invoice_main.sst_inv`

(Optional scripts provided: `sync_bln_invoice.php` and `sync_sst_inv_from_bill.php`)

## Deployment Steps

1. Backup current files
2. Deploy modified files to server
3. Clear Laravel cache: `php artisan cache:clear`
4. Clear view cache: `php artisan view:clear`
5. Test the SST v2 create page with Ramakrishnan branch filter
6. Verify invoices appear and SST values are correct
7. Test saving an SST record

## Rollback Plan

If issues occur, revert these 3 files:
- `app/Http/Controllers/SSTV2Controller.php`
- `resources/views/dashboard/sst-v2/table/tbl-sst-invoice-list.blade.php`
- `resources/views/dashboard/sst-v2/create.blade.php`







