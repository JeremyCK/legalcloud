# How to Fix SST Record 96 (and Other SST Records)

## Problem
The stored amount in `sst_main.amount` doesn't match the calculated total (SST + Reimbursement SST), causing a mismatch between the edit page and listing page.

## Solution Options

### Option 1: Quick Fix via Edit Page (Recommended for Single Record)
**Easiest method - No code changes needed**

1. Go to: http://127.0.0.1:8000/sst-v2-edit/96
2. Verify the "Transfer Total Amount" field shows the correct value
3. Click the **"Update SST"** button (even without making any changes)
4. The system will automatically recalculate and save the correct total
5. Check the listing page to verify it now shows the correct amount

**Why this works:** The `updateSSTV2()` function has been updated to calculate the total correctly including reimbursement SST.

---

### Option 2: PHP Script Fix (For Single or Multiple Records)
**Best for fixing multiple records**

1. Open Laravel Tinker:
   ```bash
   php artisan tinker
   ```

2. Load the fix script:
   ```php
   require 'fix_sst_record_amounts.php';
   ```

3. Fix single record:
   ```php
   fixSSTRecord(96);
   ```

4. Or fix all records:
   ```php
   fixAllSSTRecords();
   ```

This will:
- Show you the current vs calculated amounts
- Update the stored amount if there's a difference
- Provide a detailed report

---

### Option 3: SQL Direct Fix (For Database Administrators)
**Fastest for bulk fixes**

1. Open your database client (phpMyAdmin, MySQL Workbench, etc.)

2. Run the queries in `fix_sst_amounts.sql`:
   - First query: Shows what needs to be fixed
   - Second query: Updates the amounts
   - Third query: Verifies the fix

3. For record 96 specifically:
   ```sql
   UPDATE sst_main sm
   INNER JOIN (
       SELECT 
           sd.sst_main_id,
           SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
       FROM sst_details sd
       LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
       WHERE sd.sst_main_id = 96
       GROUP BY sd.sst_main_id
   ) calculated ON calculated.sst_main_id = sm.id
   SET sm.amount = calculated.calculated_total,
       sm.updated_at = NOW()
   WHERE sm.id = 96;
   ```

---

### Option 4: Add Route for Web-Based Fix (Future Enhancement)
**For non-technical users**

Add this route to `routes/web.php`:
```php
Route::post('recalculate-sst/{id}', [App\Http\Controllers\SSTV2Controller::class, 'recalculateSSTAmount'])->name('sst-v2.recalculate');
```

Then create a button on the edit page that calls this route.

---

## Verification

After fixing, verify the fix worked:

1. **Edit Page Check:**
   - Go to: http://127.0.0.1:8000/sst-v2-edit/96
   - "Transfer Total Amount" should match the footer "Total SST" in Current Invoices table

2. **Listing Page Check:**
   - Go to: http://127.0.0.1:8000/sst-v2-list
   - "Total SST Paid" for record 96 should match the edit page amount

3. **Database Check:**
   ```sql
   SELECT 
       sm.id,
       sm.amount as stored_amount,
       SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
   FROM sst_main sm
   LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
   LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
   WHERE sm.id = 96
   GROUP BY sm.id, sm.amount;
   ```
   The `stored_amount` and `calculated_total` should match (difference < 0.01).

---

## Prevention

To prevent this issue in the future:
1. ✅ The `updateSSTV2()` function now correctly calculates totals
2. ✅ The edit page JavaScript automatically updates the total when invoices change
3. ⚠️  Existing records created before the fix may still have incorrect amounts

**Recommendation:** Run `fixAllSSTRecords()` periodically or after the fix is deployed to correct all existing records.

---

## Summary

**For SST Record 96 specifically:**
- **Easiest:** Use Option 1 (Edit page + Update button)
- **Most Reliable:** Use Option 2 (PHP script)
- **Fastest for Multiple:** Use Option 3 (SQL script)

All methods will recalculate: `Total = Sum of (SST + Remaining Reimbursement SST)` for all invoices in the record.










