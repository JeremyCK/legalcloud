# SST V2 Total Amount Calculation - Current Status

## Analysis of Create and Update Methods

### 1. CREATE (createNewSSTRecordV2) - Line 615

**Current Code:**
```php
$total_amount += $add_bill[$i]['value'];  // Only SST amount
$SSTMain->amount = $total_amount;  // Saves only SST
```

**Status:** ❌ **Does NOT include reimbursement SST**

### 2. UPDATE (updateSSTV2) - Lines 686-690

**Current Code:**
```php
$remaining_reimbursement_sst = max(0, $reimbursement_sst - $transferred_reimbursement_sst);
$invoice_total = $transfer_amount + $remaining_reimbursement_sst;
$total_amount += $invoice_total;  // Includes remaining reimbursement SST
```

**Status:** ✅ **Includes remaining reimbursement SST** (but uses remaining, not full)

### 3. UPDATE - New Invoices (Lines 708-712)

**Current Code:**
```php
$remaining_reimbursement_sst = max(0, $reimbursement_sst - $transferred_reimbursement_sst);
$invoice_total = $sst_amount + $remaining_reimbursement_sst;
$total_amount += $invoice_total;  // Includes remaining reimbursement SST
```

**Status:** ✅ **Includes remaining reimbursement SST** (but uses remaining, not full)

## Issue

Since we changed the view to show **full reimbursement_sst** (not remaining), the controller should also use **full reimbursement_sst** to match.

## Recommendation

Update both CREATE and UPDATE methods to use **full reimbursement_sst** instead of remaining.






