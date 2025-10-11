-- Find invoices where the amount doesn't match the correct calculation formula
-- Formula: (cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)

SELECT 
    im.invoice_no,
    im.amount as invoice_amount,
    b.sst_rate,
    -- Calculate category totals
    COALESCE(cat1.total, 0) as cat1_amount,
    COALESCE(cat2.total, 0) as cat2_amount,
    COALESCE(cat3.total, 0) as cat3_amount,
    COALESCE(cat4.total, 0) as cat4_amount,
    -- Apply the formula and round to 2 decimal places
    ROUND(
        (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
        COALESCE(cat2.total, 0) + 
        COALESCE(cat3.total, 0) + 
        (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
    ) as calculated_amount,
    -- Calculate difference using rounded calculated amount
    im.amount - ROUND(
        (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
        COALESCE(cat2.total, 0) + 
        COALESCE(cat3.total, 0) + 
        (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
    ) as difference,
    im.created_at
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
-- Category 1 (P.Fee)
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 1
    GROUP BY d.invoice_main_id
) cat1 ON cat1.invoice_main_id = im.id
-- Category 2 (Disbursement)
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 2
    GROUP BY d.invoice_main_id
) cat2 ON cat2.invoice_main_id = im.id
-- Category 3 (Reimbursement)
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 3
    GROUP BY d.invoice_main_id
) cat3 ON cat3.invoice_main_id = im.id
-- Category 4 (SST)
LEFT JOIN (
    SELECT 
        d.invoice_main_id,
        SUM(d.amount) as total
    FROM loan_case_invoice_details d
    LEFT JOIN account_item ai ON ai.id = d.account_item_id
    WHERE ai.account_cat_id = 4
    GROUP BY d.invoice_main_id
) cat4 ON cat4.invoice_main_id = im.id
WHERE im.status = 1
-- Filter out invoices where difference is more than 0.01 (allowing for small rounding differences)
HAVING ABS(difference) > 0.01
ORDER BY ABS(difference) DESC, im.created_at DESC
-- LIMIT 50; -- Removed to show all mismatched invoices
