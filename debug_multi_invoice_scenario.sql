-- Debug script for multi-invoice scenarios
-- This script helps investigate how invoices are divided when multiple invoices exist under the same loan_case_bill_main

-- 1. Find bills that have multiple invoices
SELECT 
    b.id as bill_id,
    b.total_amt_inv as bill_total_amt_inv,
    b.sst_rate,
    COUNT(im.id) as invoice_count,
    GROUP_CONCAT(im.invoice_no) as invoice_numbers,
    SUM(im.amount) as sum_of_invoice_amounts
FROM loan_case_bill_main b
LEFT JOIN loan_case_invoice_main im ON b.id = im.loan_case_main_bill_id
    AND im.status <> 99
WHERE b.status <> 99
GROUP BY b.id, b.total_amt_inv, b.sst_rate
HAVING COUNT(im.id) > 1
ORDER BY COUNT(im.id) DESC
LIMIT 10;

-- 2. Detailed breakdown for a specific bill (replace {BILL_ID} with actual bill ID)
-- Example: Replace {BILL_ID} with the bill ID from step 1
SELECT 
    '=== BILL INFORMATION ===' as section,
    b.id as bill_id,
    b.total_amt_inv as bill_total_amt_inv,
    b.sst_rate,
    b.pfee1_inv,
    b.pfee2_inv,
    b.sst_inv
FROM loan_case_bill_main b
WHERE b.id = {BILL_ID}  -- Replace with actual bill ID
AND b.status <> 99

UNION ALL

SELECT 
    '=== INVOICE COUNT ===' as section,
    COUNT(*) as invoice_count,
    NULL as bill_total_amt_inv,
    NULL as sst_rate,
    NULL as pfee1_inv,
    NULL as pfee2_inv,
    NULL as sst_inv
FROM loan_case_invoice_main im
WHERE im.loan_case_main_bill_id = {BILL_ID}  -- Replace with actual bill ID
AND im.status <> 99

UNION ALL

SELECT 
    '=== INVOICE DETAILS ===' as section,
    im.id as invoice_id,
    im.amount as invoice_amount,
    im.pfee1_inv,
    im.pfee2_inv,
    im.sst_inv,
    NULL as sst_rate
FROM loan_case_invoice_main im
WHERE im.loan_case_main_bill_id = {BILL_ID}  -- Replace with actual bill ID
AND im.status <> 99;

-- 3. Calculate what the amounts should be based on details for a specific bill
-- Replace {BILL_ID} with actual bill ID
SELECT 
    '=== DETAILS CALCULATION ===' as section,
    NULL as invoice_id,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE ild.amount END), 0), 2) as calculated_total,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 1 THEN ild.amount ELSE 0 END), 0), 2) as calculated_pfee1,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 0 THEN ild.amount ELSE 0 END), 0), 2) as calculated_pfee2,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * (b.sst_rate/100) ELSE 0 END), 0), 2) as calculated_sst,
    NULL as sst_rate
FROM loan_case_invoice_details ild
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_invoice_main im ON ild.invoice_main_id = im.id
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE im.loan_case_main_bill_id = {BILL_ID}  -- Replace with actual bill ID
AND ild.status <> 99
AND im.status <> 99;

-- 4. Show how amounts are split between invoices
-- Replace {BILL_ID} with actual bill ID
SELECT 
    im.invoice_no,
    im.amount as current_invoice_amount,
    ROUND(calculated_total / invoice_count, 2) as expected_invoice_amount,
    ROUND(im.amount - (calculated_total / invoice_count), 2) as difference,
    calculated_total,
    invoice_count
FROM loan_case_invoice_main im
CROSS JOIN (
    SELECT 
        COUNT(*) as invoice_count,
        ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE ild.amount END), 0), 2) as calculated_total
    FROM loan_case_invoice_details ild
    LEFT JOIN account_item ai ON ild.account_item_id = ai.id
    LEFT JOIN loan_case_invoice_main im2 ON ild.invoice_main_id = im2.id
    LEFT JOIN loan_case_bill_main b ON im2.loan_case_main_bill_id = b.id
    WHERE im2.loan_case_main_bill_id = {BILL_ID}  -- Replace with actual bill ID
    AND ild.status <> 99
    AND im2.status <> 99
) calc
WHERE im.loan_case_main_bill_id = {BILL_ID}  -- Replace with actual bill ID
AND im.status <> 99;

-- 5. Show all invoice details for a specific bill
-- Replace {BILL_ID} with actual bill ID
SELECT 
    im.invoice_no,
    ild.amount as detail_amount,
    ild.ori_invoice_amt,
    ai.name as item_name,
    ai.account_cat_id,
    ai.pfee1_item,
    CASE 
        WHEN ai.account_cat_id = 1 THEN 'Professional Fee'
        WHEN ai.account_cat_id = 2 THEN 'Disbursement'
        WHEN ai.account_cat_id = 3 THEN 'Disbursement'
        ELSE 'Other'
    END as category_type
FROM loan_case_invoice_details ild
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_invoice_main im ON ild.invoice_main_id = im.id
WHERE im.loan_case_main_bill_id = {BILL_ID}  -- Replace with actual bill ID
AND ild.status <> 99
AND im.status <> 99
ORDER BY im.invoice_no, ild.id;
