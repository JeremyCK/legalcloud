-- =====================================================
-- SQL Script: Backfill ori_invoice_sst Column
-- =====================================================
-- This script calculates and fills ori_invoice_sst based on:
-- ori_invoice_amt * (sst_rate / 100) from loan_case_bill_main
-- =====================================================

-- Step 1: Update ori_invoice_sst for all invoice details
-- Only update where ori_invoice_amt > 0 and sst_rate exists
UPDATE 
    loan_case_invoice_details AS ild
    INNER JOIN loan_case_invoice_main AS im ON ild.invoice_main_id = im.id
    INNER JOIN loan_case_bill_main AS bm ON im.loan_case_main_bill_id = bm.id
    INNER JOIN account_item AS ai ON ild.account_item_id = ai.id
SET 
    ild.ori_invoice_sst = ROUND(ild.ori_invoice_amt * (bm.sst_rate / 100), 2)
WHERE 
    ild.status <> 99
    AND ild.ori_invoice_amt > 0
    AND bm.sst_rate IS NOT NULL
    AND bm.sst_rate > 0
    AND ai.account_cat_id IN (1, 4);  -- Only Professional fees and Reimbursement (taxable items)

-- Step 2: Verify the update
SELECT 
    COUNT(*) AS total_updated,
    SUM(CASE WHEN ori_invoice_sst IS NOT NULL THEN 1 ELSE 0 END) AS has_ori_sst,
    SUM(CASE WHEN ori_invoice_sst IS NULL THEN 1 ELSE 0 END) AS missing_ori_sst
FROM 
    loan_case_invoice_details AS ild
    INNER JOIN loan_case_invoice_main AS im ON ild.invoice_main_id = im.id
    INNER JOIN loan_case_bill_main AS bm ON im.loan_case_main_bill_id = bm.id
    INNER JOIN account_item AS ai ON ild.account_item_id = ai.id
WHERE 
    ild.status <> 99
    AND ild.ori_invoice_amt > 0
    AND ai.account_cat_id IN (1, 4);

-- Step 3: Show sample of updated records
SELECT 
    ild.id,
    ild.invoice_main_id,
    im.invoice_no,
    ai.name AS account_item_name,
    ild.ori_invoice_amt,
    bm.sst_rate,
    ild.ori_invoice_sst,
    ROUND(ild.ori_invoice_amt * (bm.sst_rate / 100), 2) AS calculated_sst,
    CASE 
        WHEN ABS(ild.ori_invoice_sst - ROUND(ild.ori_invoice_amt * (bm.sst_rate / 100), 2)) < 0.01 THEN 'MATCH'
        ELSE 'MISMATCH'
    END AS verification
FROM 
    loan_case_invoice_details AS ild
    INNER JOIN loan_case_invoice_main AS im ON ild.invoice_main_id = im.id
    INNER JOIN loan_case_bill_main AS bm ON im.loan_case_main_bill_id = bm.id
    INNER JOIN account_item AS ai ON ild.account_item_id = ai.id
WHERE 
    ild.status <> 99
    AND ild.ori_invoice_amt > 0
    AND ai.account_cat_id IN (1, 4)
LIMIT 20;



