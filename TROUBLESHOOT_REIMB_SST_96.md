# Troubleshooting Reimbursement SST Still Showing 0.00

## Quick Diagnostic Steps

### Step 1: Run Deep Diagnostic
First, check if invoices actually have reimbursement items:

```sql
SOURCE deep_check_reimbursement_sst_96.sql;
```

This will show:
- Which invoices have reimbursement items (account_cat_id = 4)
- Current reimbursement_sst values
- Calculated vs stored comparison
- Sample reimbursement items

### Step 2: Check Results

**If Step 1 shows "❌ NO REIMBURSEMENT ITEMS":**
- This means the invoices don't have reimbursement items
- Reimb SST = 0.00 is **CORRECT** - there's nothing to reimburse
- No fix needed - this is expected behavior

**If Step 1 shows reimbursement items exist but reimbursement_sst = 0:**
- The reimbursement_sst field needs to be calculated
- Proceed to Step 3

**If Step 1 shows reimbursement_sst > 0 but remaining_reimb_sst = 0:**
- Check if `transferred_reimbursement_sst_amt >= reimbursement_sst`
- If yes, it means reimbursement SST was already transferred to another SST record
- This is also correct - it's already been included elsewhere

### Step 3: Force Fix
If invoices have reimbursement items but reimbursement_sst is still 0, run:

```sql
SOURCE force_fix_reimbursement_sst_96.sql;
```

This script will:
1. Recalculate reimbursement_sst for ALL invoices (even if 0)
2. Reset transferred_reimbursement_sst_amt if it's too high
3. Recalculate SST main total
4. Show verification results

## Common Issues

### Issue 1: No Reimbursement Items
**Symptom:** All invoices show 0.00 for Reimb SST
**Cause:** Invoices don't have account_cat_id = 4 items
**Solution:** This is correct - no fix needed

### Issue 2: Reimbursement Items Exist But Not Calculated
**Symptom:** Diagnostic shows reimbursement items but reimbursement_sst = 0
**Cause:** reimbursement_sst field was never calculated
**Solution:** Run `force_fix_reimbursement_sst_96.sql`

### Issue 3: Already Fully Transferred
**Symptom:** reimbursement_sst > 0 but remaining_reimb_sst = 0
**Cause:** transferred_reimbursement_sst_amt >= reimbursement_sst
**Solution:** Check if this reimbursement SST was included in another SST record. If not, reset transferred_reimbursement_sst_amt

### Issue 4: SST Rate Missing
**Symptom:** Reimbursement items exist but reimbursement_sst = 0
**Cause:** bill.sst_rate is NULL or 0
**Solution:** Check loan_case_bill_main.sst_rate - default to 6% if missing

## After Running Fix

1. Refresh the page: `http://127.0.0.1:8000/sst-v2-edit/96`
2. Check Reimb SST column - should show values > 0 for invoices with reimbursement items
3. Check Total SST column - should be SST + Reimb SST
4. Verify Transfer Total Amount matches the sum

## Still Not Working?

If Reimb SST is still 0.00 after running the force fix:

1. **Check if invoices actually have reimbursement items:**
   ```sql
   SELECT im.invoice_no, COUNT(ild.id) as reimb_items
   FROM sst_details sd
   INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
   LEFT JOIN loan_case_invoice_details ild ON ild.invoice_main_id = im.id
   LEFT JOIN account_item ai ON ai.id = ild.account_item_id AND ai.account_cat_id = 4
   WHERE sd.sst_main_id = 96
   GROUP BY im.invoice_no
   HAVING COUNT(ild.id) > 0;
   ```

2. **Check SST rate:**
   ```sql
   SELECT im.invoice_no, b.sst_rate
   FROM sst_details sd
   INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
   LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
   WHERE sd.sst_main_id = 96;
   ```

3. **Manually check one invoice:**
   ```sql
   -- Replace INVOICE_ID with an actual invoice ID from SST 96
   SELECT 
       ild.*,
       ai.name,
       ai.account_cat_id
   FROM loan_case_invoice_details ild
   INNER JOIN account_item ai ON ai.id = ild.account_item_id
   WHERE ild.invoice_main_id = INVOICE_ID
     AND ai.account_cat_id = 4
     AND ild.status <> 99;
   ```








