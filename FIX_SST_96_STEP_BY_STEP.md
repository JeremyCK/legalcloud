# Step-by-Step Guide to Fix SST Record 96

## 🎯 Quick Fix (Easiest Method - 2 Minutes)

### Step 1: Open the Edit Page
Go to: **http://127.0.0.1:8000/sst-v2-edit/96**

### Step 2: Check the Amount
Look at the **"Transfer Total Amount"** field at the top of the page.

### Step 3: Click Update
Click the green **"Update SST"** button at the bottom of the page (even if you don't make any changes).

### Step 4: Verify
- The page will refresh
- Check that the "Transfer Total Amount" matches the footer "Total SST" in the Current Invoices table
- Go to the listing page to verify it shows the correct amount

**✅ Done!** This automatically recalculates and saves the correct total.

---

## 🔧 Alternative: Run PHP Script (For Multiple Records)

If you want to fix multiple records or see detailed information:

### Step 1: Open Terminal/Command Prompt
Navigate to your project directory:
```bash
cd C:\Users\Hp\OneDrive\Desktop\Dock\Projects\Attrotech\LegalCloud\LegalcloudMainV2\legalcloud
```

### Step 2: Run Laravel Tinker
```bash
php artisan tinker
```

### Step 3: Load and Run the Fix Script
```php
require 'fix_sst_record_amounts.php';
fixSSTRecord(96);
```

This will:
- Show you the current amount vs calculated amount
- Update the record if there's a difference
- Display a detailed report

### Step 4: To Fix All Records
```php
fixAllSSTRecords();
```

---

## 💾 Alternative: Run SQL Script (For Database Access)

If you have direct database access:

### Step 1: Open Your Database Tool
(phpMyAdmin, MySQL Workbench, HeidiSQL, etc.)

### Step 2: Run This SQL Query

**First, check what needs to be fixed:**
```sql
SELECT 
    sm.id as sst_main_id,
    sm.amount as current_stored_amount,
    SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    (sm.amount - SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) as difference
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;
```

**Then, fix it:**
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

**Verify the fix:**
```sql
SELECT 
    sm.id,
    sm.amount as new_amount,
    SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;
```

---

## 📋 Summary: Which Method to Use?

| Method | Time | Difficulty | Best For |
|--------|------|------------|----------|
| **Edit Page + Update Button** | 2 min | ⭐ Easy | Single record, quick fix |
| **PHP Script** | 5 min | ⭐⭐ Medium | Multiple records, detailed report |
| **SQL Script** | 3 min | ⭐⭐⭐ Advanced | Database admins, bulk fixes |

---

## ✅ Recommended: Use Method 1 (Edit Page)

**Just do this:**
1. Go to: http://127.0.0.1:8000/sst-v2-edit/96
2. Click "Update SST" button
3. Done!

The system will automatically:
- Recalculate the total (SST + Reimbursement SST)
- Save the correct amount
- Update the record

---

## 🔍 What Gets Fixed?

The fix recalculates:
```
Total = Sum of (SST Amount + Remaining Reimbursement SST)
```

For each invoice:
- **SST Amount**: The SST being transferred
- **Remaining Reimb SST**: `reimbursement_sst - transferred_reimbursement_sst_amt`

This ensures the stored amount matches what's displayed on the edit page.

---

## ❓ Need Help?

If you encounter any issues:
1. Check the browser console for JavaScript errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify database connection
4. Make sure you have permission to update SST records






