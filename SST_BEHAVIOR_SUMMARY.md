# SST Behavior Summary: `ori_invoice_sst` Updates

## Current Behavior

### ✅ **When Splitting Invoice:**
1. **New detail created** (InvoiceController.php line 1473-1489):
   - Copies `ori_invoice_sst` from original detail if it exists
   - Sets `ori_invoice_amt` from original

2. **SST Recalculation** (InvoiceController.php line 1491-1597):
   - Calculates total SST from `ori_invoice_amt * sst_rate`
   - Distributes SST proportionally to each split invoice's `sst` column
   - **Updates `ori_invoice_sst`** for ALL split invoices with same `account_item_id` (line 1589)
   - Formula: `ori_invoice_sst = sum of all individual sst values`

### ⚠️ **When Creating New Invoice:**
1. **New detail created** (CaseController.php line 8258-8272):
   - Sets `ori_invoice_amt` and `amount`
   - **Does NOT set `ori_invoice_sst` initially** (missing)

2. **Auto-calculation** (CaseController.php line 7597-7625) - **NEWLY ADDED**:
   - If `ori_invoice_sst` is NULL or 0, calculates from `ori_invoice_amt * sst_rate`
   - **Updates database** to persist the calculation
   - Updates both `sst` and `ori_invoice_sst` columns

### ✅ **Auto-Calculation on Display:**
1. **View fallback** (tab-invoice.blade.php line 213-237) - **IMPROVED**:
   - Checks `sst` column first (individual SST)
   - Then checks `ori_invoice_sst` (total SST for split invoices)
   - **Falls back to calculate** from `ori_invoice_amt * sst_rate` if both are missing/0
   - Ensures SST is always displayed correctly

## Summary

| Action | Updates `ori_invoice_sst`? | Auto-calculates if missing? |
|--------|----------------------------|------------------------------|
| **Split Invoice** | ✅ YES (after SST distribution) | ✅ YES (via display logic) |
| **Create Invoice** | ❌ NO (initially) | ✅ YES (via auto-calculation) |
| **Edit Invoice Amount** | ✅ YES (if split invoice) | ✅ YES (via display logic) |
| **Edit Invoice SST** | ✅ YES (manual edit) | N/A |

## Key Points:

1. ✅ **Split invoice**: `ori_invoice_sst` is updated after splitting (sum of distributed SST values)
2. ✅ **Create invoice**: `ori_invoice_sst` is NOT set initially, but will be auto-calculated when displayed
3. ✅ **Auto-calculation**: If `ori_invoice_sst` is NULL or 0, it's calculated from `ori_invoice_amt * sst_rate` and saved to database
4. ✅ **Display**: View will always show SST correctly, calculating from `ori_invoice_amt` if needed

## Recommendation:

For **creating new invoices**, we should also set `ori_invoice_sst` initially when creating the detail, similar to how `ori_invoice_amt` is set. This would prevent the need for auto-calculation later.

