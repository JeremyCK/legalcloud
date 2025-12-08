-- Diagnostic: Why Reimb SST still shows 0.00 after patch
-- This will check if the patch worked and why Reimb SST is still 0

-- STEP 1: Check if transferred_reimbursement_sst_amt was updated
SELECT 
    'Check 1: Transferred Amount Status' AS check_type,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt,
    CASE 
        WHEN im.transferred_reimbursement_sst_amt >= im.reimbursement_sst THEN 'Already transferred'
        WHEN im.transferred_reimbursement_sst_amt < im.reimbursement_sst THEN 'NOT fully transferred'
        ELSE 'No reimbursement SST'
    END AS transfer_status,
    GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) AS remaining_reimb_sst
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
ORDER BY im.invoice_no
LIMIT 10;

-- STEP 2: Check SST main total calculation
SELECT 
    'Check 2: SST Main Total Calculation' AS check_type,
    sm.id AS sst_main_id,
    sm.amount AS stored_amount,
    SUM(COALESCE(sd.amount, 0)) AS total_sst_from_details,
    SUM(COALESCE(im.reimbursement_sst, 0)) AS total_reimbursement_sst,
    SUM(COALESCE(im.transferred_reimbursement_sst_amt, 0)) AS total_transferred_reimb_sst,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS total_remaining_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS calculated_total,
    CASE 
        WHEN SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) = 0 
        THEN 'Reimb SST already included (transferred)'
        ELSE 'Reimb SST NOT included (not transferred)'
    END AS reimb_sst_status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;

-- STEP 3: Check if reimbursement SST should be showing
-- The view shows: remaining_reimb_sst = reimbursement_sst - transferred_reimbursement_sst_amt
-- If transferred_reimbursement_sst_amt = reimbursement_sst, then remaining = 0 (correct)
-- But maybe the issue is that reimbursement SST should NOT have been transferred yet?
SELECT 
    'Check 3: Should Reimb SST Show?' AS check_type,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt,
    GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) AS remaining_reimb_sst,
    CASE 
        WHEN im.transferred_reimbursement_sst_amt >= im.reimbursement_sst 
        THEN 'Reimb SST already transferred - showing 0.00 is CORRECT'
        ELSE 'Reimb SST NOT transferred - should show value'
    END AS explanation
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
ORDER BY im.invoice_no
LIMIT 10;

-- STEP 4: Check if reimbursement SST is included in SST main total
-- If reimbursement SST should be in SST 96, the total should include it
SELECT 
    'Check 4: Is Reimb SST in Total?' AS check_type,
    sm.amount AS current_total,
    SUM(COALESCE(sd.amount, 0)) AS sst_only,
    SUM(COALESCE(im.reimbursement_sst, 0)) AS reimb_sst_total,
    SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS sst_plus_reimb,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0))) < 0.01 
        THEN 'Reimb SST IS included in total'
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0))) < 0.01 
        THEN 'Reimb SST NOT included in total'
        ELSE 'Unknown'
    END AS status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;






