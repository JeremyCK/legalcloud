# How to Modify Invoice Amounts - DP20001170 Example

## Current Situation

**Problem:** All 3 invoices (rows 16, 17, 18) show the same values:
- Total amt: **3,388.33**
- Collected amt: **3,388.33**

**Why this happens:**
- All 3 invoices share the **same bill** (`loan_case_main_bill_id`)
- Current calculation divides the bill's collected amount by invoice count
- Formula: `bill_collected_amt / invoice_count = 3,388.33`

## Data Flow Analysis

### Step 1: Data Retrieved (Lines 902-932)
```php
// From loan_case_bill_main (b)
$detail->bill_collected_amt  // Total collected for the BILL (shared by all 3 invoices)
$detail->bill_total_amt      // Total amount for the BILL

// From loan_case_invoice_main (im) - PER INVOICE
$detail->invoice_amount      // Invoice DP20001170's actual amount
$detail->invoice_no          // "DP20001170"
```

### Step 2: Current Calculation (Lines 1009-1018)
```php
// Count how many invoices share this bill
$invoiceCount = 3;  // All 3 invoices share the same bill

// Divide bill collected amount equally
$totalAmount = $detail->bill_collected_amt ?? 0;  // e.g., 10,164.99
$calculatedAmount = round($totalAmount / $invoiceCount, 2);  // 10,164.99 / 3 = 3,388.33

// Apply same value to ALL invoices
$detail->bill_total_amt_divided = $calculatedAmount;      // 3,388.33
$detail->bill_collected_amt_divided = $calculatedAmount; // 3,388.33
```

## Available Data Sources

### For Invoice DP20001170 Specifically:

1. **Invoice Amount** (`loan_case_invoice_main.amount`)
   - This is the **actual invoice total** for DP20001170
   - Already retrieved as: `$detail->invoice_amount`
   - **This is the most accurate value for Total amt**

2. **Bill Collected Amount** (`loan_case_bill_main.collected_amt`)
   - This is the **total collected for the entire bill**
   - Shared by all invoices linked to the same bill
   - Already retrieved as: `$detail->bill_collected_amt`

3. **Invoice Components** (can calculate total from these):
   - `pfee1_inv + pfee2_inv + sst_inv + reimbursement_amount + reimbursement_sst`
   - Already retrieved in the query

## Solution Options

### Option 1: Use Invoice Amount for Total Amt (Recommended)

**Change:** Use each invoice's own `amount` field instead of dividing bill amount

**Code Change:**
```php
// Lines 1009-1018 - REPLACE WITH:

// Use invoice amount for Total amt (per invoice, not divided)
$detail->bill_total_amt_divided = round($detail->invoice_amount ?? 0, 2);

// Use collected amount divided by invoice count for Collected amt
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);
```

**Result:**
- Each invoice shows its **own invoice amount** for Total amt
- All invoices still share the **collected amount** (divided equally)

### Option 2: Calculate Total from Invoice Components

**Change:** Calculate total from pfee + sst + reimbursement components

**Code Change:**
```php
// Calculate total from invoice components
$totalFromComponents = ($detail->pfee1_inv ?? 0) 
                     + ($detail->pfee2_inv ?? 0) 
                     + ($detail->sst_inv ?? 0) 
                     + ($detail->reimbursement_amount ?? 0) 
                     + ($detail->reimbursement_sst ?? 0);
$detail->bill_total_amt_divided = round($totalFromComponents, 2);

// Collected amount (divided)
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);
```

### Option 3: Use Bill Total Amt (Divided)

**Change:** Use bill total amount divided by invoice count

**Code Change:**
```php
// Use bill total amount divided by invoice count
$billTotal = $detail->bill_total_amt ?? 0;
$detail->bill_total_amt_divided = round($billTotal / $invoiceCount, 2);

// Collected amount (divided)
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);
```

## Implementation Steps

### Step 1: Check Current Data

First, verify what data exists for invoice DP20001170:

```sql
-- Check invoice amount
SELECT id, invoice_no, amount, pfee1_inv, pfee2_inv, sst_inv, 
       reimbursement_amount, reimbursement_sst
FROM loan_case_invoice_main 
WHERE invoice_no = 'DP20001170';

-- Check bill data (shared by all 3 invoices)
SELECT b.id, b.total_amt, b.collected_amt,
       COUNT(im.id) as invoice_count
FROM loan_case_bill_main b
LEFT JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
WHERE b.id = (SELECT loan_case_main_bill_id FROM loan_case_invoice_main WHERE invoice_no = 'DP20001170')
GROUP BY b.id;
```

### Step 2: Modify Controller Code

**File:** `app/Http/Controllers/TransferFeeV3Controller.php`  
**Function:** `transferFeeEditV3()`  
**Lines:** 1009-1018

**Current Code:**
```php
// Lines 1009-1018 - CURRENT (PROBLEMATIC)
$totalAmount = $detail->bill_collected_amt ?? 0;
$calculatedAmount = round($totalAmount / $invoiceCount, 2);
$detail->bill_total_amt_divided = $calculatedAmount;  // ❌ Overwrites correct value
$detail->bill_collected_amt_divided = $calculatedAmount;
```

**Recommended Fix:**
```php
// Lines 1009-1018 - RECOMMENDED FIX
// Use invoice amount for Total amt (already calculated at line 984, don't overwrite)
// The value from line 984 is correct, just remove the overwrite at line 1017

// Only calculate Collected amt from collected amount
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);

// Remove line 1017 - don't overwrite bill_total_amt_divided
// It's already correctly set at line 984 using invoice_amount
```

### Step 3: Test the Change

After modification:
1. Visit: `http://127.0.0.1:8001/transferfee/479/edit`
2. Check if invoice DP20001170 shows different values
3. Verify totals still add up correctly

## Impact Analysis

### Before Change:
- All 3 invoices: Total amt = 3,388.33, Collected amt = 3,388.33
- Values are identical because they share the same bill

### After Change (Option 1):
- Invoice DP20001170: Total amt = **its own invoice amount**, Collected amt = 3,388.33
- Other invoices: Total amt = **their own invoice amounts**, Collected amt = 3,388.33
- Each invoice shows its **actual invoice total** instead of divided bill amount

## Important Notes

1. **Invoice Amount** (`im.amount`) is the most accurate source
   - It's calculated from invoice details when invoice is created
   - Already stored in database
   - Already retrieved in the query (line 912)

2. **Bill Collected Amount** should remain divided
   - This represents actual payments received
   - Multiple invoices can share the same payment
   - Division is necessary for accurate accounting

3. **The fix is simple:**
   - Remove line 1017 that overwrites `bill_total_amt_divided`
   - Keep the calculation from line 984 (uses `invoice_amount`)
   - Only calculate `bill_collected_amt_divided` from collected amount

## Recommendation

**Use Option 1** - It's the cleanest solution:
- ✅ Uses actual invoice amounts (most accurate)
- ✅ Minimal code change (just remove one line)
- ✅ Each invoice shows its correct total
- ✅ Collected amount logic remains unchanged


