# Why Reimbursement SST Shows 0.00

## The Issue

After fixing SST amounts, you noticed that **Reimb SST** column shows **0.00** for all invoices in SST Record 96.

## Root Cause

The Reimb SST column displays the **remaining reimbursement SST** that hasn't been transferred yet. It's calculated as:

```
Reimb SST = reimbursement_sst - transferred_reimbursement_sst_amt
```

If Reimb SST shows 0.00, it means one of these scenarios:

### Scenario 1: No Reimbursement SST in Invoice
- The invoice doesn't have any reimbursement items (account_cat_id = 4)
- `reimbursement_sst` field is NULL or 0
- **This is normal** - not all invoices have reimbursement SST

### Scenario 2: Reimbursement SST Already Fully Transferred
- The invoice has `reimbursement_sst` > 0
- But `transferred_reimbursement_sst_amt` equals or exceeds `reimbursement_sst`
- All reimbursement SST has already been transferred to other SST records
- **This is normal** - it means the reimbursement SST was already included in a previous SST transfer

### Scenario 3: Reimbursement SST Not Calculated
- The invoice has reimbursement items (account_cat_id = 4) in `loan_case_invoice_details`
- But `reimbursement_sst` field is NULL or 0 (not calculated)
- **This needs to be fixed** - reimbursement SST should be calculated from invoice details

## How to Check

Run the diagnostic query:

```sql
-- Check reimbursement SST for SST Record 96
SOURCE check_reimbursement_sst_for_sst_96.sql;
```

This will show:
- Which invoices have reimbursement SST
- Which invoices have it already fully transferred
- Which invoices need reimbursement SST to be calculated

## How to Fix

### Option 1: Complete Fix (Recommended)
Run the complete fix script that fixes both SST amounts AND reimbursement SST:

```sql
SOURCE fix_sst_96_complete.sql;
```

This script will:
1. Fix SST amounts (from `invoice.sst_inv`)
2. Calculate reimbursement SST from invoice details (account_cat_id = 4)
3. Recalculate SST main total
4. Verify everything is correct

### Option 2: Reimbursement SST Only
If SST amounts are already fixed, just run the reimbursement SST fix:

```sql
SOURCE fix_reimbursement_sst_for_sst_96_improved.sql;
```

## Understanding Reimbursement SST

Reimbursement SST is calculated from:
- Invoice details where `account_cat_id = 4` (reimbursement items)
- Formula: `SUM(reimbursement_amount) * sst_rate / 100`

For example:
- If an invoice has reimbursement items totaling 1,000.00
- And SST rate is 6%
- Then `reimbursement_sst = 1,000.00 * 6 / 100 = 60.00`

## Expected Result After Fix

After running the fix:
- ✅ Invoices with reimbursement items will show Reimb SST > 0
- ✅ Invoices without reimbursement items will show Reimb SST = 0.00 (this is correct)
- ✅ Invoices where reimbursement SST was already transferred will show Reimb SST = 0.00 (this is also correct)
- ✅ Total SST column = SST + Reimb SST
- ✅ Transfer Total Amount includes both SST and Reimb SST

## Important Notes

1. **Not all invoices have reimbursement SST** - This is normal and expected
2. **Reimbursement SST can be transferred separately** - It may have been included in a different SST record
3. **The fix only calculates reimbursement SST if invoice details exist** - If there are no reimbursement items, Reimb SST will remain 0.00

## Verification

After running the fix, verify:
1. Check which invoices should have reimbursement SST (have account_cat_id = 4 items)
2. Verify those invoices show Reimb SST > 0
3. Verify Total SST = SST + Reimb SST for each row
4. Verify Transfer Total Amount matches the sum of all Total SST values










