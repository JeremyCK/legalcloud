-- =====================================================
-- MIGRATION SCRIPT: Migrate Invoice Amounts from Bill to Invoice
-- =====================================================
-- Purpose: Migrate pfee1_inv, pfee2_inv, sst_inv from loan_case_bill_main to loan_case_invoice_main
-- Logic: When multiple invoices come from same bill, divide amounts equally
-- =====================================================

-- Step 1: Create backup table for safety
CREATE TABLE IF NOT EXISTS loan_case_invoice_main_backup_20241221 AS 
SELECT * FROM loan_case_invoice_main;

-- Step 2: Create temporary table to calculate divided amounts
CREATE TEMPORARY TABLE temp_invoice_amounts AS
SELECT 
    im.id as invoice_id,
    im.loan_case_main_bill_id,
    b.pfee1_inv as bill_pfee1,
    b.pfee2_inv as bill_pfee2,
    b.sst_inv as bill_sst,
    COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) as invoice_count_per_bill,
    -- Divide amounts by number of invoices per bill
    CASE 
        WHEN COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) > 0 
        THEN b.pfee1_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id)
        ELSE 0 
    END as divided_pfee1,
    CASE 
        WHEN COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) > 0 
        THEN b.pfee2_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id)
        ELSE 0 
    END as divided_pfee2,
    CASE 
        WHEN COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id) > 0 
        THEN b.sst_inv / COUNT(*) OVER (PARTITION BY im.loan_case_main_bill_id)
        ELSE 0 
    END as divided_sst
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
WHERE im.status <> 99  -- Exclude deleted records
  AND b.id IS NOT NULL; -- Only process invoices with valid bill records

-- Step 3: Show preview of what will be updated
SELECT 
    'PREVIEW - Amounts to be migrated:' as info,
    COUNT(*) as total_invoices_to_update,
    SUM(divided_pfee1) as total_pfee1_to_migrate,
    SUM(divided_pfee2) as total_pfee2_to_migrate,
    SUM(divided_sst) as total_sst_to_migrate
FROM temp_invoice_amounts;

-- Step 4: Show sample data for verification
SELECT 
    'SAMPLE DATA - First 10 records:' as info,
    invoice_id,
    loan_case_main_bill_id,
    invoice_count_per_bill,
    bill_pfee1,
    bill_pfee2,
    bill_sst,
    ROUND(divided_pfee1, 2) as divided_pfee1,
    ROUND(divided_pfee2, 2) as divided_pfee2,
    ROUND(divided_sst, 2) as divided_sst
FROM temp_invoice_amounts 
LIMIT 10;

-- Step 5: Show bills with multiple invoices for verification
SELECT 
    'BILLS WITH MULTIPLE INVOICES:' as info,
    loan_case_main_bill_id,
    invoice_count_per_bill,
    bill_pfee1,
    bill_pfee2,
    bill_sst,
    ROUND(divided_pfee1, 2) as divided_pfee1,
    ROUND(divided_pfee2, 2) as divided_pfee2,
    ROUND(divided_sst, 2) as divided_sst
FROM temp_invoice_amounts 
WHERE invoice_count_per_bill > 1
ORDER BY invoice_count_per_bill DESC, loan_case_main_bill_id
LIMIT 20;

-- Step 6: Update the invoice table with divided amounts
UPDATE loan_case_invoice_main im
INNER JOIN temp_invoice_amounts temp ON im.id = temp.invoice_id
SET 
    im.pfee1_inv = temp.divided_pfee1,
    im.pfee2_inv = temp.divided_pfee2,
    im.sst_inv = temp.divided_sst,
    im.updated_at = NOW()
WHERE im.status <> 99;

-- Step 7: Verify the migration results
SELECT 
    'MIGRATION RESULTS:' as info,
    COUNT(*) as total_invoices_updated,
    SUM(pfee1_inv) as total_pfee1_after_migration,
    SUM(pfee2_inv) as total_pfee2_after_migration,
    SUM(sst_inv) as total_sst_after_migration,
    AVG(pfee1_inv) as avg_pfee1,
    AVG(pfee2_inv) as avg_pfee2,
    AVG(sst_inv) as avg_sst
FROM loan_case_invoice_main 
WHERE status <> 99 
  AND (pfee1_inv > 0 OR pfee2_inv > 0 OR sst_inv > 0);

-- Step 8: Show sample of updated records
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

-- Step 9: Clean up temporary table
DROP TEMPORARY TABLE IF EXISTS temp_invoice_amounts;

-- Step 10: Summary
SELECT 
    'MIGRATION COMPLETE!' as status,
    NOW() as completed_at,
    'Check the results above to verify the migration was successful.' as next_steps;

-- =====================================================
-- ROLLBACK SCRIPT (if needed)
-- =====================================================
-- To rollback, restore from backup:
-- DROP TABLE loan_case_invoice_main;
-- RENAME TABLE loan_case_invoice_main_backup_20241221 TO loan_case_invoice_main;
-- =====================================================
