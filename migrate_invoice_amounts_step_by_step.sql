-- =====================================================
-- STEP-BY-STEP MIGRATION SCRIPT: Invoice Amounts Migration
-- =====================================================
-- Purpose: Migrate pfee1_inv, pfee2_inv, sst_inv from bill to invoice
-- Usage: Run each section separately and verify results before proceeding
-- =====================================================

-- =====================================================
-- STEP 1: CREATE BACKUP (Run this first!)
-- =====================================================
CREATE TABLE loan_case_invoice_main_backup_20241221 AS 
SELECT * FROM loan_case_invoice_main;

-- Verify backup was created:
SELECT COUNT(*) as backup_record_count FROM loan_case_invoice_main_backup_20241221;

-- =====================================================
-- STEP 2: ANALYZE CURRENT DATA (Review this output)
-- =====================================================
-- Check current invoice amounts
SELECT 
    'CURRENT INVOICE AMOUNTS:' as info,
    COUNT(*) as total_invoices,
    SUM(CASE WHEN pfee1_inv > 0 THEN 1 ELSE 0 END) as invoices_with_pfee1,
    SUM(CASE WHEN pfee2_inv > 0 THEN 1 ELSE 0 END) as invoices_with_pfee2,
    SUM(CASE WHEN sst_inv > 0 THEN 1 ELSE 0 END) as invoices_with_sst,
    SUM(pfee1_inv) as total_pfee1,
    SUM(pfee2_inv) as total_pfee2,
    SUM(sst_inv) as total_sst
FROM loan_case_invoice_main 
WHERE status <> 99;

-- Check bill amounts that will be migrated
SELECT 
    'BILL AMOUNTS TO MIGRATE:' as info,
    COUNT(DISTINCT b.id) as total_bills,
    SUM(b.pfee1_inv) as total_bill_pfee1,
    SUM(b.pfee2_inv) as total_bill_pfee2,
    SUM(b.sst_inv) as total_bill_sst
FROM loan_case_bill_main b
INNER JOIN loan_case_invoice_main im ON b.id = im.loan_case_main_bill_id
WHERE im.status <> 99;

-- =====================================================
-- STEP 3: ANALYZE INVOICE DISTRIBUTION (Review this output)
-- =====================================================
-- Check how many invoices per bill
SELECT 
    'INVOICES PER BILL:' as info,
    invoice_count,
    COUNT(*) as bill_count,
    SUM(total_pfee1) as total_pfee1_for_this_count,
    SUM(total_pfee2) as total_pfee2_for_this_count,
    SUM(total_sst) as total_sst_for_this_count
FROM (
    SELECT 
        im.loan_case_main_bill_id,
        COUNT(*) as invoice_count,
        b.pfee1_inv as total_pfee1,
        b.pfee2_inv as total_pfee2,
        b.sst_inv as total_sst
    FROM loan_case_invoice_main im
    INNER JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
    WHERE im.status <> 99
    GROUP BY im.loan_case_main_bill_id, b.pfee1_inv, b.pfee2_inv, b.sst_inv
) grouped_data
GROUP BY invoice_count
ORDER BY invoice_count;

-- =====================================================
-- STEP 4: PREVIEW MIGRATION (Review this output carefully!)
-- =====================================================
-- Show what the divided amounts will look like
SELECT 
    'MIGRATION PREVIEW - FIRST 20 RECORDS:' as info,
    im.id as invoice_id,
    im.invoice_no,
    im.loan_case_main_bill_id,
    COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) as invoices_per_bill,
    b.pfee1_inv as original_pfee1,
    b.pfee2_inv as original_pfee2,
    b.sst_inv as original_sst,
    ROUND(b.pfee1_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id), 2) as divided_pfee1,
    ROUND(b.pfee2_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id), 2) as divided_pfee2,
    ROUND(b.sst_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id), 2) as divided_sst
FROM loan_case_invoice_main im
INNER JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
WHERE im.status <> 99
ORDER BY im.loan_case_main_bill_id, im.id
LIMIT 20;

-- =====================================================
-- STEP 5: PERFORM THE MIGRATION (Run this when ready!)
-- =====================================================
-- Update invoice amounts with divided bill amounts
UPDATE loan_case_invoice_main im
INNER JOIN (
    SELECT 
        im.id as invoice_id,
        b.pfee1_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) as divided_pfee1,
        b.pfee2_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) as divided_pfee2,
        b.sst_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) as divided_sst
    FROM loan_case_invoice_main im
    INNER JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
    WHERE im.status <> 99
) divided_amounts ON im.id = divided_amounts.invoice_id
SET 
    im.pfee1_inv = divided_amounts.divided_pfee1,
    im.pfee2_inv = divided_amounts.divided_pfee2,
    im.sst_inv = divided_amounts.divided_sst,
    im.updated_at = NOW();

-- =====================================================
-- STEP 6: VERIFY MIGRATION RESULTS (Review this output)
-- =====================================================
-- Check migration results
SELECT 
    'MIGRATION RESULTS:' as info,
    COUNT(*) as total_invoices_updated,
    SUM(pfee1_inv) as total_pfee1_after,
    SUM(pfee2_inv) as total_pfee2_after,
    SUM(sst_inv) as total_sst_after,
    AVG(pfee1_inv) as avg_pfee1,
    AVG(pfee2_inv) as avg_pfee2,
    AVG(sst_inv) as avg_sst
FROM loan_case_invoice_main 
WHERE status <> 99 
  AND (pfee1_inv > 0 OR pfee2_inv > 0 OR sst_inv > 0);

-- Show sample updated records
SELECT 
    'SAMPLE UPDATED RECORDS:' as info,
    id,
    invoice_no,
    loan_case_main_bill_id,
    ROUND(pfee1_inv, 2) as pfee1_inv,
    ROUND(pfee2_inv, 2) as pfee2_inv,
    ROUND(sst_inv, 2) as sst_inv,
    updated_at
FROM loan_case_invoice_main 
WHERE status <> 99 
  AND (pfee1_inv > 0 OR pfee2_inv > 0 OR sst_inv > 0)
ORDER BY updated_at DESC
LIMIT 10;

-- =====================================================
-- STEP 7: VALIDATE DATA INTEGRITY (Review this output)
-- =====================================================
-- Verify that total amounts match between bill and invoice
SELECT 
    'DATA INTEGRITY CHECK:' as info,
    'Bill totals vs Invoice totals' as comparison,
    SUM(b.pfee1_inv) as total_bill_pfee1,
    SUM(im.pfee1_inv) as total_invoice_pfee1,
    SUM(b.pfee2_inv) as total_bill_pfee2,
    SUM(im.pfee2_inv) as total_invoice_pfee2,
    SUM(b.sst_inv) as total_bill_sst,
    SUM(im.sst_inv) as total_invoice_sst
FROM loan_case_bill_main b
INNER JOIN loan_case_invoice_main im ON b.id = im.loan_case_main_bill_id
WHERE im.status <> 99;

-- =====================================================
-- ROLLBACK INSTRUCTIONS (if needed)
-- =====================================================
-- If you need to rollback, run these commands:
-- 1. Find your backup table name:
--    SHOW TABLES LIKE 'loan_case_invoice_main_backup_%';
--
-- 2. Restore from backup (replace YYYYMMDD_HHMMSS with your actual backup name):
--    DROP TABLE loan_case_invoice_main;
--    RENAME TABLE loan_case_invoice_main_backup_20241221 TO loan_case_invoice_main;
-- =====================================================
