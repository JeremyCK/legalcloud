-- Visualize How Reimbursement SST Data Flows
-- This query shows the exact data that the listing uses

-- This is what the controller query retrieves (simplified)
SELECT 
    'Controller Query Result' AS section,
    sd.id AS sst_detail_id,
    sd.sst_main_id,
    sd.loan_case_invoice_main_id,
    sd.amount AS sst_amount_from_details,
    im.invoice_no,
    -- Reimbursement SST fields (from loan_case_invoice_main)
    im.reimbursement_sst AS total_reimbursement_sst,
    im.transferred_reimbursement_sst_amt AS transferred_amount,
    -- This is what the view calculates
    GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))) AS remaining_reimb_sst,
    -- This is what shows in "Reimb SST" column
    CASE 
        WHEN GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))) = 0 
        THEN 'Shows 0.00 (already transferred)'
        ELSE CONCAT('Shows ', GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))))
    END AS what_shows_in_column,
    -- Total SST row calculation
    sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))) AS total_sst_row
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY im.invoice_no
LIMIT 10;

-- Summary: How totals are calculated
SELECT 
    'Total Calculation' AS section,
    COUNT(*) AS invoice_count,
    SUM(sd.amount) AS total_sst,
    SUM(im.reimbursement_sst) AS total_reimbursement_sst,
    SUM(im.transferred_reimbursement_sst_amt) AS total_transferred,
    SUM(GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS total_remaining_reimb_sst,
    SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS grand_total,
    sm.amount AS sst_main_stored_amount
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
INNER JOIN sst_main sm ON sm.id = sd.sst_main_id
WHERE sd.sst_main_id = 96
GROUP BY sm.amount;




