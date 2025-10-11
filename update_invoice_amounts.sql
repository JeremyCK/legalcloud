-- Update script to fix all mismatched invoice amounts
-- This script calculates the correct amount using the formula and updates the invoice_main table
-- Formula: (cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)

UPDATE loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 1
    GROUP BY d.invoice_main_id
) cat1 ON cat1.invoice_main_id = im.id
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 2
    GROUP BY d.invoice_main_id
) cat2 ON cat2.invoice_main_id = im.id
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 3
    GROUP BY d.invoice_main_id
) cat3 ON cat3.invoice_main_id = im.id
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 4
    GROUP BY d.invoice_main_id
) cat4 ON cat4.invoice_main_id = im.id
SET im.amount = ROUND(
    (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
    COALESCE(cat2.total, 0) + 
    COALESCE(cat3.total, 0) + 
    (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
)
WHERE im.status = 1
AND ABS(im.amount - ROUND(
    (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
    COALESCE(cat2.total, 0) + 
    COALESCE(cat3.total, 0) + 
    (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
)) > 0.01;