-- =====================================================
-- MySQL Script: Get Taxable Invoice Details
-- =====================================================
-- This script retrieves invoice details for taxable items:
-- - Category 1: Professional fees
-- - Category 4: Reimbursement
-- =====================================================

SELECT 
    ild.id,
    ild.invoice_main_id,
    ild.loan_case_main_bill_id,
    ild.account_item_id,
    ai.name AS account_item_name,
    ai.account_cat_id,
    ac.category AS account_category_name,
    ild.amount,
    ild.sst,
    ild.quo_amount,
    ild.ori_invoice_amt,
    ild.status,
    ild.created_at,
    ild.updated_at,
    im.invoice_no,
    bm.bill_no,
    bm.sst_rate,
    CASE 
        WHEN ild.sst IS NULL OR ild.sst = 0 OR ild.sst = '' THEN 'MISSING SST'
        ELSE 'HAS SST'
    END AS sst_status
FROM 
    loan_case_invoice_details AS ild
    INNER JOIN account_item AS ai ON ild.account_item_id = ai.id
    INNER JOIN account_category AS ac ON ai.account_cat_id = ac.id
    INNER JOIN loan_case_invoice_main AS im ON ild.invoice_main_id = im.id
    INNER JOIN loan_case_bill_main AS bm ON im.loan_case_main_bill_id = bm.id
WHERE 
    ild.status <> 99  -- Exclude deleted items
    AND ai.account_cat_id IN (1, 4)  -- Professional fees (1) and Reimbursement (4)
ORDER BY 
    ild.invoice_main_id,
    ai.account_cat_id,
    ai.name;

-- =====================================================
-- Alternative: Count missing SST values
-- =====================================================

SELECT 
    ai.account_cat_id,
    ac.category AS account_category_name,
    COUNT(*) AS total_items,
    SUM(CASE WHEN ild.sst IS NULL OR ild.sst = 0 OR ild.sst = '' THEN 1 ELSE 0 END) AS missing_sst_count,
    SUM(CASE WHEN ild.sst IS NOT NULL AND ild.sst > 0 THEN 1 ELSE 0 END) AS has_sst_count
FROM 
    loan_case_invoice_details AS ild
    INNER JOIN account_item AS ai ON ild.account_item_id = ai.id
    INNER JOIN account_category AS ac ON ai.account_cat_id = ac.id
WHERE 
    ild.status <> 99
    AND ai.account_cat_id IN (1, 4)
GROUP BY 
    ai.account_cat_id,
    ac.category;

-- =====================================================
-- Alternative: Get items with missing SST only
-- =====================================================

SELECT 
    ild.id,
    ild.invoice_main_id,
    ild.account_item_id,
    ai.name AS account_item_name,
    ai.account_cat_id,
    ac.category AS account_category_name,
    ild.amount,
    ild.sst,
    im.invoice_no,
    bm.bill_no,
    bm.sst_rate,
    ROUND(ild.amount * (bm.sst_rate / 100), 2) AS calculated_sst
FROM 
    loan_case_invoice_details AS ild
    INNER JOIN account_item AS ai ON ild.account_item_id = ai.id
    INNER JOIN account_category AS ac ON ai.account_cat_id = ac.id
    INNER JOIN loan_case_invoice_main AS im ON ild.invoice_main_id = im.id
    INNER JOIN loan_case_bill_main AS bm ON im.loan_case_main_bill_id = bm.id
WHERE 
    ild.status <> 99
    AND ai.account_cat_id IN (1, 4)
    AND (ild.sst IS NULL OR ild.sst = 0 OR ild.sst = '')
ORDER BY 
    ild.invoice_main_id,
    ai.account_cat_id,
    ai.name;



