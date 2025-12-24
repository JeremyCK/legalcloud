# Solution: Proportional Allocation of Collected Amount

## Problem Statement

**Current Issue:**
- All 3 invoices show: Total amt = 3,388.33, Collected amt = 3,388.33
- User wants: Each invoice to show its own invoice amount
- Constraint: Total amt and Collected amt must match/tally

## Solution Options

### Option 1: Proportional Allocation (Recommended)

**Concept:** Allocate the bill's collected amount proportionally based on each invoice's amount.

**Formula:**
```
For each invoice:
  Total amt = invoice_amount
  Collected amt = (invoice_amount / sum_of_all_invoice_amounts) × bill_collected_amt
```

**Example:**
- Bill collected amount: 10,164.99
- Invoice 1 amount: 3,500.00
- Invoice 2 amount: 3,200.00  
- Invoice 3 amount: 3,464.99
- Sum: 10,164.99

Allocation:
- Invoice 1: Collected = (3,500.00 / 10,164.99) × 10,164.99 = 3,500.00
- Invoice 2: Collected = (3,200.00 / 10,164.99) × 10,164.99 = 3,200.00
- Invoice 3: Collected = (3,464.99 / 10,164.99) × 10,164.99 = 3,464.99

**Result:**
- Each invoice shows its own amount
- Total amt = Collected amt (they match!)
- Sum of all collected amounts = bill_collected_amt

### Option 2: Use Invoice Amount for Both (If Invoice Amounts Sum to Collected Amount)

**Concept:** If sum of invoice amounts equals bill collected amount, use invoice amount for both.

**Formula:**
```
For each invoice:
  Total amt = invoice_amount
  Collected amt = invoice_amount
```

**Limitation:** Only works if sum of invoice amounts = bill collected amount

### Option 3: Keep Current Method (No Change)

**Concept:** Keep dividing bill collected amount equally.

**Result:**
- All invoices show same amount
- Total amt = Collected amt (they match)
- But doesn't show individual invoice amounts

## Recommended Implementation: Option 1

This solution:
- ✅ Shows each invoice's actual amount
- ✅ Keeps Total amt = Collected amt (matching)
- ✅ Ensures sum of collected amounts = bill collected amount
- ✅ Handles rounding properly

## Implementation Code

```php
// Calculate sum of all invoice amounts for this bill
$allInvoicesForBill = \App\Models\LoanCaseInvoiceMain::where('loan_case_main_bill_id', $detail->loan_case_main_bill_id)
    ->where('status', 1)
    ->get();

$sumOfInvoiceAmounts = $allInvoicesForBill->sum('amount');
$sumOfInvoiceAmounts = max(0.01, $sumOfInvoiceAmounts); // Avoid division by zero

// Use invoice amount for Total amt
$detail->bill_total_amt_divided = round($detail->invoice_amount ?? 0, 2);

// Allocate collected amount proportionally
$billCollectedAmt = $detail->bill_collected_amt ?? 0;
if ($sumOfInvoiceAmounts > 0) {
    $proportionalCollected = ($detail->invoice_amount / $sumOfInvoiceAmounts) * $billCollectedAmt;
    $detail->bill_collected_amt_divided = round($proportionalCollected, 2);
} else {
    // Fallback: divide equally if no invoice amounts
    $detail->bill_collected_amt_divided = round($billCollectedAmt / $invoiceCount, 2);
}
```

## Edge Cases to Handle

1. **Sum of invoice amounts = 0:** Fallback to equal division
2. **Sum of invoice amounts ≠ bill collected amount:** Proportional allocation handles this
3. **Rounding differences:** Last invoice gets adjusted to ensure sum matches bill collected amount

## Testing

After implementation, verify:
- [ ] Each invoice shows its own invoice amount for Total amt
- [ ] Collected amt is proportional to invoice amount
- [ ] Total amt ≈ Collected amt (may have small rounding differences)
- [ ] Sum of all collected amounts = bill collected amount







