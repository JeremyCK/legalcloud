-- =====================================================
-- FLEXIBLE COMPARISON SCRIPT: loan_case_invoice_details vs loan_case_invoice_main
-- =====================================================
-- Purpose: Compare without assuming specific account category mappings
-- This script will show you the actual account categories and help you determine the correct mapping
-- =====================================================

-- =====================================================
-- STEP 1: EXPLORE ACCOUNT CATEGORIES
-- =====================================================
-- First, let's see what account categories actually exist
SELECT 
    'ACCOUNT CATEGORIES FOUND:' as info,
    account_cat_id,
    COUNT(*) as record_count,
    ROUND(SUM(amount), 2) as total_amount,
    ROUND(AVG(amount), 2) as avg_amount,
    ROUND(MIN(amount), 2) as min_amount,
    ROUND(MAX(amount), 2) as max_amount
FROM loan_case_invoice_details 
WHERE status <> 99
GROUP BY account_cat_id
ORDER BY account_cat_id;

-- =====================================================
-- STEP 2: SAMPLE DATA BY ACCOUNT CATEGORY
-- =====================================================
-- Show sample records for each account category
SELECT 
    'SAMPLE RECORDS BY ACCOUNT CATEGORY:' as info,
    account_cat_id,
    loan_case_invoice_main_id,
    ROUND(amount, 2) as amount,
    description,
    created_at
FROM loan_case_invoice_details 
WHERE status <> 99
ORDER BY account_cat_id, amount DESC
LIMIT 20;

-- =====================================================
-- STEP 3: COMPARE TOTAL AMOUNTS (ALL CATEGORIES)
-- =====================================================
-- Compare total of all details vs main table totals
SELECT 
    'TOTAL AMOUNT COMPARISON:' as info,
    im.id as invoice_id,
    im.invoice_no,
    
    -- Main table total
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as main_total,
    
    -- All details total (regardless of category)
    ROUND(COALESCE(SUM(ild.amount), 0), 2) as details_total,
    
    -- Difference
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0), 2) as difference,
    
    -- Status
    CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) < 0.01 
        THEN 'MATCH' 
        ELSE 'MISMATCH' 
    END as status,
    
    -- Detail count
    COUNT(ild.id) as detail_count

FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99
GROUP BY im.id, im.invoice_no, im.pfee1_inv, im.pfee2_inv, im.sst_inv
ORDER BY ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) DESC;

-- =====================================================
-- STEP 4: BREAKDOWN BY ACCOUNT CATEGORY
-- =====================================================
-- Show breakdown of amounts by account category for each invoice
SELECT 
    'BREAKDOWN BY ACCOUNT CATEGORY:' as info,
    im.id as invoice_id,
    im.invoice_no,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as main_total,
    
    -- Show amounts for each account category
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 1 THEN ild.amount ELSE 0 END), 0), 2) as cat1_total,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 2 THEN ild.amount ELSE 0 END), 0), 2) as cat2_total,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 3 THEN ild.amount ELSE 0 END), 0), 2) as cat3_total,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 4 THEN ild.amount ELSE 0 END), 0), 2) as cat4_total,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 5 THEN ild.amount ELSE 0 END), 0), 2) as cat5_total,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id > 5 THEN ild.amount ELSE 0 END), 0), 2) as other_cats_total,
    
    -- Total of all details
    ROUND(COALESCE(SUM(ild.amount), 0), 2) as all_details_total,
    
    -- Difference
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0), 2) as difference

FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99
GROUP BY im.id, im.invoice_no, im.pfee1_inv, im.pfee2_inv, im.sst_inv
HAVING ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) >= 0.01
ORDER BY ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) DESC
LIMIT 20;

-- =====================================================
-- STEP 5: FIND POTENTIAL MAPPINGS
-- =====================================================
-- Try to find which account categories might correspond to pfee1, pfee2, sst
-- by looking at invoices where only one category has amounts

-- Find invoices with only one account category
SELECT 
    'INVOICES WITH SINGLE ACCOUNT CATEGORY:' as info,
    im.id as invoice_id,
    im.invoice_no,
    ROUND(im.pfee1_inv, 2) as main_pfee1,
    ROUND(im.pfee2_inv, 2) as main_pfee2,
    ROUND(im.sst_inv, 2) as main_sst,
    ild.account_cat_id,
    ROUND(SUM(ild.amount), 2) as category_total,
    COUNT(*) as record_count
FROM loan_case_invoice_main im
INNER JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99
GROUP BY im.id, im.invoice_no, im.pfee1_inv, im.pfee2_inv, im.sst_inv, ild.account_cat_id
HAVING COUNT(DISTINCT ild.account_cat_id) = 1
ORDER BY ild.account_cat_id, SUM(ild.amount) DESC
LIMIT 30;

-- =====================================================
-- STEP 6: SUMMARY STATISTICS
-- =====================================================
SELECT 
    'SUMMARY STATISTICS:' as info,
    COUNT(*) as total_invoices,
    SUM(CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) < 0.01 
        THEN 1 
        ELSE 0 
    END) as matching_invoices,
    SUM(CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) >= 0.01 
        THEN 1 
        ELSE 0 
    END) as mismatched_invoices,
    SUM(CASE WHEN ild.loan_case_invoice_main_id IS NULL THEN 1 ELSE 0 END) as invoices_without_details,
    ROUND(
        (SUM(CASE 
            WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(ild.amount), 0)) < 0.01 
            THEN 1 
            ELSE 0 
        END) * 100.0 / COUNT(*)), 2
    ) as match_percentage
FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99;

-- =====================================================
-- STEP 7: RECOMMENDATIONS
-- =====================================================
SELECT 
    'NEXT STEPS:' as info,
    '1. Review account categories found above' as step1,
    '2. Check if any categories should be excluded (e.g., reimbursements)' as step2,
    '3. Determine correct mapping for pfee1, pfee2, sst' as step3,
    '4. Update the comparison logic based on findings' as step4,
    '5. Consider if some invoices should have zero amounts' as step5;

