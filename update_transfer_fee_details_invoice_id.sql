-- =====================================================
-- TRANSFER FEE DETAILS - INVOICE ID CONSOLIDATION SCRIPT
-- =====================================================
-- Purpose: Copy data from invoice_main_id to loan_case_invoice_main_id
-- Date: December 2024
-- =====================================================

-- Step 1: Check current data status
SELECT 
    'Current Data Status' as info,
    COUNT(*) as total_records,
    COUNT(invoice_main_id) as records_with_invoice_main_id,
    COUNT(loan_case_invoice_main_id) as records_with_loan_case_invoice_main_id,
    COUNT(CASE WHEN invoice_main_id IS NOT NULL AND loan_case_invoice_main_id IS NULL THEN 1 END) as records_to_update
FROM transfer_fee_details;

-- Step 2: Show sample records that need updating
SELECT 
    id,
    transfer_fee_main_id,
    invoice_main_id,
    loan_case_invoice_main_id,
    'NEEDS UPDATE' as status
FROM transfer_fee_details 
WHERE invoice_main_id IS NOT NULL 
AND loan_case_invoice_main_id IS NULL
LIMIT 10;

-- Step 3: Backup current data (optional but recommended)
-- CREATE TABLE transfer_fee_details_backup_20241221 AS 
-- SELECT * FROM transfer_fee_details;

-- Step 4: Update loan_case_invoice_main_id with invoice_main_id values
-- Only update where loan_case_invoice_main_id is NULL and invoice_main_id is NOT NULL
UPDATE transfer_fee_details 
SET loan_case_invoice_main_id = invoice_main_id 
WHERE loan_case_invoice_main_id IS NULL 
AND invoice_main_id IS NOT NULL;

-- Step 5: Verify the update was successful
SELECT 
    'After Update Verification' as info,
    COUNT(*) as total_records,
    COUNT(invoice_main_id) as records_with_invoice_main_id,
    COUNT(loan_case_invoice_main_id) as records_with_loan_case_invoice_main_id,
    COUNT(CASE WHEN invoice_main_id IS NOT NULL AND loan_case_invoice_main_id IS NULL THEN 1 END) as records_still_null
FROM transfer_fee_details;

-- Step 6: Show updated records
SELECT 
    id,
    transfer_fee_main_id,
    invoice_main_id,
    loan_case_invoice_main_id,
    'UPDATED' as status
FROM transfer_fee_details 
WHERE invoice_main_id IS NOT NULL 
AND loan_case_invoice_main_id IS NOT NULL
AND invoice_main_id = loan_case_invoice_main_id
LIMIT 10;

-- Step 7: Verify data integrity - check for orphaned records
SELECT 
    'Data Integrity Check' as info,
    COUNT(*) as orphaned_records
FROM transfer_fee_details tfd
LEFT JOIN loan_case_invoice_main lcim ON tfd.loan_case_invoice_main_id = lcim.id
WHERE tfd.loan_case_invoice_main_id IS NOT NULL 
AND lcim.id IS NULL;

-- Step 8: Show any orphaned records (if any)
SELECT 
    tfd.id,
    tfd.transfer_fee_main_id,
    tfd.loan_case_invoice_main_id,
    'ORPHANED RECORD - No matching invoice found' as issue
FROM transfer_fee_details tfd
LEFT JOIN loan_case_invoice_main lcim ON tfd.loan_case_invoice_main_id = lcim.id
WHERE tfd.loan_case_invoice_main_id IS NOT NULL 
AND lcim.id IS NULL;

-- =====================================================
-- ROLLBACK SCRIPT (if needed)
-- =====================================================
-- Uncomment and run if you need to rollback the changes:

/*
-- Rollback: Reset loan_case_invoice_main_id to NULL where it was updated
UPDATE transfer_fee_details 
SET loan_case_invoice_main_id = NULL 
WHERE invoice_main_id IS NOT NULL 
AND loan_case_invoice_main_id = invoice_main_id;
*/

-- =====================================================
-- FINAL VERIFICATION
-- =====================================================
-- Run this to get a final summary
SELECT 
    'FINAL SUMMARY' as info,
    COUNT(*) as total_records,
    SUM(CASE WHEN loan_case_invoice_main_id IS NOT NULL THEN 1 ELSE 0 END) as records_with_loan_case_invoice_main_id,
    SUM(CASE WHEN loan_case_invoice_main_id IS NULL THEN 1 ELSE 0 END) as records_without_loan_case_invoice_main_id,
    SUM(CASE WHEN invoice_main_id IS NOT NULL THEN 1 ELSE 0 END) as records_with_invoice_main_id
FROM transfer_fee_details;

