# Revert Guide: Option 1 ↔ Option 2

## Current Implementation: **OPTION 2**

**Behavior:**
- ✅ Each invoice shows its own `invoice_amount` for **Total amt**
- ✅ **Collected amt** = `bill_collected_amt / invoice_count` (divided equally)
- ⚠️ **Total amt ≠ Collected amt** (they won't match)

## How to Revert to Option 1 (Matching Amounts)

### Step 1: Edit File
**File:** `app/Http/Controllers/TransferFeeV3Controller.php`

### Step 2: Location 1 - Edit Function (around line 1009)

**Find this code:**
```php
// ====================================================================
// OPTION 2: Show individual invoice amounts (Total amt ≠ Collected amt)
// To revert to OPTION 1 (matching amounts), uncomment the code below
// ====================================================================

// Use invoice amount for Total amt (already calculated at line 984)
// Keep the invoice amount - don't overwrite it
// Note: bill_total_amt_divided is already set correctly at line 984 using invoice_amount

// Calculate Collected amt from bill collected amount (divided equally)
$totalAmount = $detail->bill_collected_amt ?? 0;
$calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
$detail->bill_collected_amt_divided = $calculatedCollectedAmount;

// ====================================================================
// OPTION 1 (REVERT CODE): Uncomment below to make both amounts match
// ====================================================================
// $totalAmount = $detail->bill_collected_amt ?? 0;
// $calculatedAmount = round($totalAmount / $invoiceCount, 2);
// $detail->bill_total_amt_divided = $calculatedAmount;
// $detail->bill_collected_amt_divided = $calculatedAmount;
```

**Replace with:**
```php
// ====================================================================
// OPTION 1: Matching amounts (Total amt = Collected amt)
// To revert to OPTION 2 (individual amounts), uncomment the code below
// ====================================================================

$totalAmount = $detail->bill_collected_amt ?? 0;
$calculatedAmount = round($totalAmount / $invoiceCount, 2);
$detail->bill_total_amt_divided = $calculatedAmount;
$detail->bill_collected_amt_divided = $calculatedAmount;

// ====================================================================
// OPTION 2 (REVERT CODE): Uncomment below to show individual invoice amounts
// ====================================================================
// // Use invoice amount for Total amt (already calculated at line 984)
// // Note: bill_total_amt_divided is already set correctly at line 984 using invoice_amount
// $totalAmount = $detail->bill_collected_amt ?? 0;
// $calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
// $detail->bill_collected_amt_divided = $calculatedCollectedAmount;
```

### Step 3: Location 2 - Export Function (around line 2472)

**Find this code:**
```php
// ====================================================================
// OPTION 2: Show individual invoice amounts (Total amt ≠ Collected amt)
// To revert to OPTION 1 (matching amounts), uncomment the code below
// ====================================================================

// Use invoice amount for Total amt (already calculated at line 2447)
// Keep the invoice amount - don't overwrite it
// Note: bill_total_amt_divided is already set correctly at line 2447 using invoice_amount

// Calculate Collected amt from bill collected amount (divided equally)
$totalAmount = $detail->bill_collected_amt ?? 0;
if ($totalAmount == 0 && $invoiceCount == 1 && ($detail->invoice_amount ?? 0) > 0) {
    $totalAmount = $detail->invoice_amount ?? 0;
}
$calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
$detail->bill_collected_amt_divided = $calculatedCollectedAmount;

// ====================================================================
// OPTION 1 (REVERT CODE): Uncomment below to make both amounts match
// ====================================================================
// $totalAmount = $detail->bill_collected_amt ?? 0;
// if ($totalAmount == 0 && $invoiceCount == 1 && ($detail->invoice_amount ?? 0) > 0) {
//     $totalAmount = $detail->invoice_amount ?? 0;
// }
// $calculatedAmount = round($totalAmount / $invoiceCount, 2);
// $detail->bill_total_amt_divided = $calculatedAmount;
// $detail->bill_collected_amt_divided = $calculatedAmount;
```

**Replace with:**
```php
// ====================================================================
// OPTION 1: Matching amounts (Total amt = Collected amt)
// To revert to OPTION 2 (individual amounts), uncomment the code below
// ====================================================================

$totalAmount = $detail->bill_collected_amt ?? 0;
if ($totalAmount == 0 && $invoiceCount == 1 && ($detail->invoice_amount ?? 0) > 0) {
    $totalAmount = $detail->invoice_amount ?? 0;
}
$calculatedAmount = round($totalAmount / $invoiceCount, 2);
$detail->bill_total_amt_divided = $calculatedAmount;
$detail->bill_collected_amt_divided = $calculatedAmount;

// ====================================================================
// OPTION 2 (REVERT CODE): Uncomment below to show individual invoice amounts
// ====================================================================
// // Use invoice amount for Total amt (already calculated at line 2447)
// // Note: bill_total_amt_divided is already set correctly at line 2447 using invoice_amount
// $totalAmount = $detail->bill_collected_amt ?? 0;
// if ($totalAmount == 0 && $invoiceCount == 1 && ($detail->invoice_amount ?? 0) > 0) {
//     $totalAmount = $detail->invoice_amount ?? 0;
// }
// $calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
// $detail->bill_collected_amt_divided = $calculatedCollectedAmount;
```

### Step 4: Save and Test

1. Save the file
2. Clear cache if needed
3. Test at: `http://127.0.0.1:8001/transferfee/479/edit`
4. Verify both Total amt and Collected amt match

## Quick Summary

| Option | Total Amt | Collected Amt | Match? |
|--------|-----------|---------------|--------|
| **Option 1** | `bill_collected_amt / count` | `bill_collected_amt / count` | ✅ Yes |
| **Option 2** | `invoice_amount` | `bill_collected_amt / count` | ❌ No |

## Current Status: **OPTION 2 ACTIVE**

To revert, follow steps above and swap the active/commented code sections.



