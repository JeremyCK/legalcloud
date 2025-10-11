-- =====================================================
-- SIMPLE COMPARISON SCRIPT: loan_case_invoice_details vs loan_case_invoice_main
-- =====================================================
-- Purpose: Quick comparison assuming standard account category mappings
-- Assumptions:
--   - account_cat_id = 1: Professional Fee 1 (pfee1_inv)
--   - account_cat_id = 2: Professional Fee 2 (pfee2_inv)  
--   - account_cat_id = 3: SST (sst_inv)
--   - account_cat_id = 4: Reimbursement (separate handling)
-- =====================================================

-- =====================================================
-- QUICK COMPARISON WITH ASSUMED MAPPINGS
-- =====================================================
SELECT 
    'INVOICE AMOUNT COMPARISON:' as info,
    im.id as invoice_id,
    im.invoice_no,
    
    -- Main table amounts
    ROUND(im.pfee1_inv, 2) as main_pfee1,
    ROUND(im.pfee2_inv, 2) as main_pfee2,
    ROUND(im.sst_inv, 2) as main_sst,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as main_total,
    
    -- Details amounts (assuming standard mapping)
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 1 THEN ild.amount ELSE 0 END), 0), 2) as details_pfee1,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 2 THEN ild.amount ELSE 0 END), 0), 2) as details_pfee2,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id = 3 THEN ild.amount ELSE 0 END), 0), 2) as details_sst,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0), 2) as details_total,
    
    -- Differences
    ROUND(im.pfee1_inv - COALESCE(SUM(CASE WHEN ild.account_cat_id = 1 THEN ild.amount ELSE 0 END), 0), 2) as pfee1_diff,
    ROUND(im.pfee2_inv - COALESCE(SUM(CASE WHEN ild.account_cat_id = 2 THEN ild.amount ELSE 0 END), 0), 2) as pfee2_diff,
    ROUND(im.sst_inv - COALESCE(SUM(CASE WHEN ild.account_cat_id = 3 THEN ild.amount ELSE 0 END), 0), 2) as sst_diff,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0), 2) as total_diff,
    
    -- Status
    CASE 
        WHEN ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0)) < 0.01 
        THEN 'MATCH' 
        ELSE 'MISMATCH' 
    END as status,
    
    -- Additional info
    COUNT(ild.id) as detail_count,
    im.created_at

FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99
GROUP BY im.id, im.invoice_no, im.pfee1_inv, im.pfee2_inv, im.sst_inv, im.created_at
HAVING ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0)) >= 0.01
ORDER BY ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0)) DESC;

-- =====================================================
-- SUMMARY OF MISMATCHES
-- =====================================================
SELECT 
    'MISMATCH SUMMARY:' as info,
    COUNT(*) as total_mismatched_invoices,
    SUM(ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0))) as total_difference,
    ROUND(AVG(ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0))), 2) as avg_difference,
    MAX(ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0))) as max_difference
FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99
GROUP BY im.id, im.pfee1_inv, im.pfee2_inv, im.sst_inv
HAVING ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0)) >= 0.01;

-- =====================================================
-- TOP 10 LARGEST MISMATCHES
-- =====================================================
SELECT 
    'TOP 10 LARGEST MISMATCHES:' as info,
    im.id as invoice_id,
    im.invoice_no,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as main_total,
    ROUND(COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0), 2) as details_total,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0), 2) as difference
FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.loan_case_invoice_main_id 
    AND ild.status <> 99
WHERE im.status <> 99
GROUP BY im.id, im.invoice_no, im.pfee1_inv, im.pfee2_inv, im.sst_inv
HAVING ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0)) >= 0.01
ORDER BY ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ild.account_cat_id IN (1,2,3) THEN ild.amount ELSE 0 END), 0)) DESC
LIMIT 10;

