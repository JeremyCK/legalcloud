# Manual Update Instructions for Invoice R20000214

## The Problem
- Invoice R20000214 has `bln_invoice = 0` in the database
- But its bill has `bln_invoice = 1`
- The controller correctly filters for `im.bln_invoice = 1`
- So the invoice doesn't appear in search results

## What to Update

### Option 1: Update via SQL (Recommended)
Run this SQL directly on your server database:

```sql
UPDATE loan_case_invoice_main 
SET bln_invoice = 1 
WHERE invoice_no = 'R20000214' 
  AND status <> 99;
```

### Option 2: Update via Laravel Tinker
On your server, run:
```bash
php artisan tinker
```

Then:
```php
$invoice = \App\Models\LoanCaseInvoiceMain::where('invoice_no', 'R20000214')->first();
if ($invoice) {
    $invoice->bln_invoice = 1;
    $invoice->save();
    echo "Updated invoice R20000214: bln_invoice = 1\n";
} else {
    echo "Invoice not found\n";
}
```

### Option 3: Update via Database Admin Tool
1. Open your database admin tool (phpMyAdmin, MySQL Workbench, etc.)
2. Find table: `loan_case_invoice_main`
3. Find row where `invoice_no = 'R20000214'`
4. Change `bln_invoice` from `0` to `1`
5. Save

## Verify the Fix
After updating, verify:
```sql
SELECT invoice_no, bln_invoice 
FROM loan_case_invoice_main 
WHERE invoice_no = 'R20000214';
```

Should show: `bln_invoice = 1`

## Summary
- ✅ Controller code: Already correct (no changes needed)
- ⚠️ Database data: Needs manual update (set `bln_invoice = 1` for R20000214)
