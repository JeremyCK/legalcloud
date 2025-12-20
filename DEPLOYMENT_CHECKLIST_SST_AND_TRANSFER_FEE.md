# Quick Deployment Checklist - Custom SST & Editable Transfer Fee

## Pre-Deployment
- [ ] Backup database
- [ ] Review all changed files
- [ ] Test on local/staging environment (if available)

## Deployment Steps

### 1. Upload Files
- [ ] `app/Http/Controllers/InvoiceController.php`
- [ ] `app/Http/Controllers/CaseController.php`
- [ ] `app/Http/Controllers/EInvoiceContoller.php`
- [ ] `app/Http/Controllers/TransferFeeV3Controller.php`
- [ ] `resources/views/dashboard/invoice/details.blade.php`
- [ ] `resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php`
- [ ] `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`
- [ ] `resources/views/dashboard/case/d-invoice-print.blade.php`
- [ ] `resources/views/dashboard/case/d-invoice-print-pdf.blade.php`
- [ ] `resources/views/dashboard/case/d-invoice-print-simple.blade.php`
- [ ] `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
- [ ] `routes/web.php`
- [ ] `database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php`

### 2. Run Database Migration
```bash
php artisan migrate
```
OR manually run SQL:
```sql
ALTER TABLE `loan_case_invoice_details` 
ADD COLUMN `sst` DECIMAL(20,2) NULL 
COMMENT 'Custom SST amount (if manually set, otherwise NULL to auto-calculate)' 
AFTER `amount`;
```

### 3. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 4. Verify Route
```bash
php artisan route:list | grep updateAmounts
```
Should show: `POST transferfee/update-amounts/{detailId}`

### 5. Test Features
- [ ] Edit SST in invoice details page
- [ ] Verify SST persists after save
- [ ] Check SST displays correctly in case details
- [ ] Test invoice print/download with custom SST
- [ ] Edit transfer fee amounts (pfee, sst, reimb, reimb sst)
- [ ] Verify transfer fee main totals update
- [ ] Check ledger entries are updated
- [ ] Verify account log is created

### 6. Monitor Logs
```bash
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

## Post-Deployment
- [ ] Monitor for errors
- [ ] Verify all features working
- [ ] Document any issues encountered

## Rollback (if needed)
- [ ] Restore previous code files
- [ ] Run: `php artisan migrate:rollback --step=1`
- [ ] OR manually: `ALTER TABLE loan_case_invoice_details DROP COLUMN sst;`

