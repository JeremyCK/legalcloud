# Complete Guide to Fix SST Record 96

## 🔍 Step 1: DIAGNOSE the Problem

**First, run the diagnostic script to see what's wrong:**

Run this SQL query in your database:

```sql
-- Run diagnose_sst_96_issue.sql
-- OR run this query directly:
```

Open `diagnose_sst_96_issue.sql` and run **all 4 queries** to see:
1. SST Main record info
2. Invoice details and reimbursement status
3. Summary of issues
4. Which invoices have reimbursement details

**Look for:**
- ❌ "NO SST RATE" - Invoice doesn't have SST rate
- ❌ "NO REIMB DETAILS" - Invoice has no reimbursement items (account_cat_id = 4)
- ✅ "OK" - Invoice is correct
- ⚠️ "NEEDS UPDATE" - Invoice needs fixing

---

## 🔧 Step 2: FIX Based on Diagnosis

### If invoices have "NO REIMB DETAILS":
**This means the invoices don't have reimbursement items, so 0.00 is CORRECT.**

These invoices simply don't have reimbursement expenses, so showing 0.00 is expected.

### If invoices have "NO SST RATE":
**The invoice's bill doesn't have an SST rate set.**

You need to:
1. Check the bill record: `loan_case_bill_main` table
2. Set the `sst_rate` field (usually 6 for 6%)

### If invoices show "NEEDS UPDATE":
**Run the improved fix script:**

```sql
-- Run fix_reimbursement_sst_for_sst_96_improved.sql
-- OR run Step 2 from that file
```

---

## 📋 Step-by-Step Fix Process

### Option A: If Most Invoices Show 0.00 (No Reimbursement)

**This might be CORRECT!** Many invoices don't have reimbursement expenses.

**To verify:**
1. Pick one invoice that shows 0.00
2. Check if it has reimbursement details:
   ```sql
   SELECT 
       ild.*,
       ai.account_cat_id,
       ai.name as account_item_name
   FROM loan_case_invoice_details ild
   INNER JOIN account_item ai ON ai.id = ild.account_item_id
   WHERE ild.invoice_main_id = [INVOICE_ID]
     AND ai.account_cat_id = 4
     AND ild.status <> 99;
   ```
3. If this returns no rows, then 0.00 is correct!

### Option B: If Some Invoices Should Have Reimbursement

**Run the improved fix script:**

1. **First, run diagnostic:**
   ```sql
   -- From diagnose_sst_96_issue.sql - Query 4
   -- This shows invoices that SHOULD have reimbursement
   ```

2. **Then run the fix:**
   ```sql
   -- From fix_reimbursement_sst_for_sst_96_improved.sql - Step 2
   -- This updates only invoices that have reimbursement details AND SST rate
   ```

3. **Verify:**
   ```sql
   -- From fix_reimbursement_sst_for_sst_96_improved.sql - Step 3
   -- Check what was updated
   ```

4. **Recalculate SST total:**
   ```sql
   -- From fix_reimbursement_sst_for_sst_96_improved.sql - Step 4
   -- Update the SST record total
   ```

---

## 🎯 Quick Fix (If Scripts Don't Work)

### Method 1: Check One Invoice Manually

```sql
-- Replace [INVOICE_ID] with an actual invoice ID from SST 96
SELECT 
    im.id,
    im.invoice_no,
    im.reimbursement_sst,
    b.sst_rate,
    (SELECT SUM(ild.amount) 
     FROM loan_case_invoice_details ild
     INNER JOIN account_item ai ON ai.id = ild.account_item_id
     WHERE ild.invoice_main_id = im.id
       AND ai.account_cat_id = 4
       AND ild.status <> 99) as reimb_amount,
    ROUND((SELECT SUM(ild.amount) 
           FROM loan_case_invoice_details ild
           INNER JOIN account_item ai ON ai.id = ild.account_item_id
           WHERE ild.invoice_main_id = im.id
             AND ai.account_cat_id = 4
             AND ild.status <> 99) * b.sst_rate / 100, 2) as should_be_reimb_sst
FROM loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
WHERE sd.sst_main_id = 96
  AND im.id = [INVOICE_ID];
```

### Method 2: Update via Edit Page

1. Go to: http://127.0.0.1:8000/sst-v2-edit/96
2. The page should show the correct calculated values
3. Click "Update SST" to save
4. This will recalculate based on current database values

---

## ❓ Common Issues & Solutions

### Issue 1: Script runs but nothing updates
**Cause:** Invoices don't have reimbursement details (account_cat_id = 4)
**Solution:** This is correct - those invoices don't have reimbursement expenses

### Issue 2: "NO SST RATE" errors
**Cause:** Bill records missing SST rate
**Solution:** Update bill records:
```sql
UPDATE loan_case_bill_main b
INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET b.sst_rate = 6  -- or whatever your SST rate is
WHERE sd.sst_main_id = 96
  AND (b.sst_rate IS NULL OR b.sst_rate = 0);
```

### Issue 3: Reimbursement SST calculated but not showing
**Cause:** Values updated but page not refreshed
**Solution:** 
1. Clear browser cache
2. Refresh the edit page
3. Click "Update SST" to recalculate

---

## ✅ Final Verification

After running fixes, verify:

```sql
-- Check SST record total
SELECT 
    sm.amount as stored_amount,
    SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.amount;
```

The `stored_amount` and `calculated_total` should match (difference < 0.01).

---

## 📝 Summary

1. **Run diagnostic first** (`diagnose_sst_96_issue.sql`)
2. **Check if 0.00 is correct** (many invoices don't have reimbursement)
3. **Fix only invoices that need it** (`fix_reimbursement_sst_for_sst_96_improved.sql`)
4. **Recalculate SST total** (Step 4 of improved script)
5. **Verify on edit page** (refresh and check)

**Remember:** If invoices don't have reimbursement expenses (account_cat_id = 4), showing 0.00 is CORRECT!










