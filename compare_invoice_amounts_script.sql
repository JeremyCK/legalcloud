-- =====================================================
-- COMPARISON SCRIPT: loan_case_invoice_details vs loan_case_invoice_main
-- =====================================================
-- Purpose: Compare summed amounts from loan_case_invoice_details 
--          with pfee1_inv, pfee2_inv, sst_inv in loan_case_invoice_main
--          to find discrepancies
-- =====================================================

-- =====================================================
-- STEP 1: UNDERSTAND THE DATA STRUCTURE
-- =====================================================
-- First, let's see what columns are available in loan_case_invoice_details
DESCRIBE loan_case_invoice_details;

-- Check sample data structure
SELECT 
    'SAMPLE DATA FROM loan_case_invoice_details:' as info,
    *
FROM loan_case_invoice_details 
LIMIT 5;

-- =====================================================
-- STEP 2: ANALYZE ACCOUNT CATEGORIES
-- =====================================================
-- Check what account categories exist and their amounts
SELECT 
    'ACCOUNT CATEGORIES IN loan_case_invoice_details:' as info,
    account_cat_id,
    COUNT(*) as record_count,
    SUM(amount) as total_amount,
    AVG(amount) as avg_amount,
    MIN(amount) as min_amount,
    MAX(amount) as max_amount
FROM loan_case_invoice_details 
WHERE status <> 99  -- Exclude deleted records
GROUP BY account_cat_id
ORDER BY account_cat_id;

-- =====================================================
-- STEP 3: CALCULATE SUMMED AMOUNTS FROM DETAILS
-- =====================================================
-- Create a temporary table to calculate totals from details
CREATE TEMPORARY TABLE temp_invoice_details_totals AS
SELECT 
    loan_case_invoice_main_id,
    -- Sum all amounts (assuming all are professional fees)
    SUM(amount) as total_details_amount,
    -- Sum amounts by account category (if account_cat_id exists)
    SUM(CASE WHEN account_cat_id = 1 THEN amount ELSE 0 END) as cat1_amount,
    SUM(CASE WHEN account_cat_id = 2 THEN amount ELSE 0 END) as cat2_amount,
    SUM(CASE WHEN account_cat_id = 3 THEN amount ELSE 0 END) as cat3_amount,
    SUM(CASE WHEN account_cat_id = 4 THEN amount ELSE 0 END) as cat4_amount,
    SUM(CASE WHEN account_cat_id = 5 THEN amount ELSE 0 END) as cat5_amount,
    -- Count records
    COUNT(*) as detail_record_count
FROM loan_case_invoice_details 
WHERE status <> 99  -- Exclude deleted records
GROUP BY loan_case_invoice_main_id;

-- =====================================================
-- STEP 4: COMPARE DETAILS TOTALS WITH MAIN TABLE
-- =====================================================
-- Main comparison query
SELECT 
    'COMPARISON RESULTS:' as info,
    im.id as invoice_id,
    im.invoice_no,
    im.loan_case_main_bill_id,
    
    -- Main table amounts
    im.pfee1_inv as main_pfee1,
    im.pfee2_inv as main_pfee2,
    im.sst_inv as main_sst,
    (im.pfee1_inv + im.pfee2_inv + im.sst_inv) as main_total,
    
    -- Details table amounts
    COALESCE(dt.total_details_amount, 0) as details_total,
    COALESCE(dt.cat1_amount, 0) as details_cat1,
    COALESCE(dt.cat2_amount, 0) as details_cat2,
    COALESCE(dt.cat3_amount, 0) as details_cat3,
    COALESCE(dt.cat4_amount, 0) as details_cat4,
    COALESCE(dt.cat5_amount, 0) as details_cat5,
    COALESCE(dt.detail_record_count, 0) as detail_count,
    
    -- Calculate differences
    (im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0) as difference,
    
    -- Status indicators
    CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) < 0.01 
        THEN 'MATCH' 
        ELSE 'MISMATCH' 
    END as status,
    
    -- Additional info
    im.status as invoice_status,
    im.created_at,
    im.updated_at

FROM loan_case_invoice_main im
LEFT JOIN temp_invoice_details_totals dt ON im.id = dt.loan_case_invoice_main_id
WHERE im.status <> 99  -- Exclude deleted invoices
ORDER BY 
    CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) < 0.01 
        THEN 1 
        ELSE 0 
    END,
    ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) DESC;

-- =====================================================
-- STEP 5: SUMMARY STATISTICS
-- =====================================================
-- Overall summary
SELECT 
    'SUMMARY STATISTICS:' as info,
    COUNT(*) as total_invoices,
    SUM(CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) < 0.01 
        THEN 1 
        ELSE 0 
    END) as matching_invoices,
    SUM(CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) >= 0.01 
        THEN 1 
        ELSE 0 
    END) as mismatched_invoices,
    SUM(CASE WHEN dt.loan_case_invoice_main_id IS NULL THEN 1 ELSE 0 END) as invoices_without_details,
    ROUND(
        (SUM(CASE 
            WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) < 0.01 
            THEN 1 
            ELSE 0 
        END) * 100.0 / COUNT(*)), 2
    ) as match_percentage
FROM loan_case_invoice_main im
LEFT JOIN temp_invoice_details_totals dt ON im.id = dt.loan_case_invoice_main_id
WHERE im.status <> 99;

-- =====================================================
-- STEP 6: DETAILED MISMATCH ANALYSIS
-- =====================================================
-- Show only mismatched records with detailed breakdown
SELECT 
    'MISMATCHED RECORDS:' as info,
    im.id as invoice_id,
    im.invoice_no,
    im.loan_case_main_bill_id,
    
    -- Main amounts
    ROUND(im.pfee1_inv, 2) as main_pfee1,
    ROUND(im.pfee2_inv, 2) as main_pfee2,
    ROUND(im.sst_inv, 2) as main_sst,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as main_total,
    
    -- Details amounts
    ROUND(COALESCE(dt.total_details_amount, 0), 2) as details_total,
    ROUND(COALESCE(dt.cat1_amount, 0), 2) as details_cat1,
    ROUND(COALESCE(dt.cat2_amount, 0), 2) as details_cat2,
    ROUND(COALESCE(dt.cat3_amount, 0), 2) as details_cat3,
    ROUND(COALESCE(dt.cat4_amount, 0), 2) as details_cat4,
    ROUND(COALESCE(dt.cat5_amount, 0), 2) as details_cat5,
    
    -- Difference
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0), 2) as difference,
    
    -- Detail count
    COALESCE(dt.detail_record_count, 0) as detail_count

FROM loan_case_invoice_main im
LEFT JOIN temp_invoice_details_totals dt ON im.id = dt.loan_case_invoice_main_id
WHERE im.status <> 99
  AND ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) >= 0.01
ORDER BY ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(dt.total_details_amount, 0)) DESC;

-- =====================================================
-- STEP 7: INVOICES WITHOUT DETAILS
-- =====================================================
-- Show invoices that have no corresponding details
SELECT 
    'INVOICES WITHOUT DETAILS:' as info,
    im.id as invoice_id,
    im.invoice_no,
    im.loan_case_main_bill_id,
    ROUND(im.pfee1_inv, 2) as pfee1,
    ROUND(im.pfee2_inv, 2) as pfee2,
    ROUND(im.sst_inv, 2) as sst,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as total,
    im.created_at,
    im.updated_at
FROM loan_case_invoice_main im
LEFT JOIN temp_invoice_details_totals dt ON im.id = dt.loan_case_invoice_main_id
WHERE im.status <> 99
  AND dt.loan_case_invoice_main_id IS NULL
  AND (im.pfee1_inv > 0 OR im.pfee2_inv > 0 OR im.sst_inv > 0)
ORDER BY (im.pfee1_inv + im.pfee2_inv + im.sst_inv) DESC;

-- =====================================================
-- STEP 8: DETAILS WITHOUT INVOICES
-- =====================================================
-- Show details that don't have corresponding invoices
SELECT 
    'DETAILS WITHOUT INVOICES:' as info,
    ild.loan_case_invoice_main_id,
    COUNT(*) as detail_count,
    ROUND(SUM(ild.amount), 2) as total_amount,
    ROUND(AVG(ild.amount), 2) as avg_amount
FROM loan_case_invoice_details ild
LEFT JOIN loan_case_invoice_main im ON ild.loan_case_invoice_main_id = im.id
WHERE ild.status <> 99
  AND (im.id IS NULL OR im.status = 99)
GROUP BY ild.loan_case_invoice_main_id
ORDER BY total_amount DESC;

-- =====================================================
-- STEP 9: CLEANUP
-- =====================================================
-- Drop temporary table
DROP TEMPORARY TABLE IF EXISTS temp_invoice_details_totals;

-- =====================================================
-- STEP 10: RECOMMENDATIONS
-- =====================================================
SELECT 
    'RECOMMENDATIONS:' as info,
    '1. Review mismatched records above' as step1,
    '2. Check if account_cat_id mapping is correct' as step2,
    '3. Verify if some amounts should be excluded from comparison' as step3,
    '4. Consider if SST should be calculated differently' as step4,
    '5. Update loan_case_invoice_main amounts if details are correct' as step5;

-- =====================================================
-- END OF SCRIPT
-- =====================================================

