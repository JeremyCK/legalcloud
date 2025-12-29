-- ============================================================================
-- RESET Reimbursement SST to Show in SST 96
-- This will reset transferred_reimbursement_sst_amt to 0 so reimbursement SST shows
-- ============================================================================
-- IMPORTANT: This assumes reimbursement SST should be included in SST 96
-- If reimbursement SST was already transferred to another SST record, this will cause double-counting
-- ============================================================================

-- STEP 1: Check current state (BEFORE)
SELECT 
    'BEFORE RESET' AS section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt AS current_transferred,
    0 AS new_transferred,
    GREATEST(0, (im.reimbursement_sst - 0)) AS new_remaining_reimb_sst
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
ORDER BY im.invoice_no
LIMIT 10;

-- STEP 2: Reset transferred_reimbursement_sst_amt to 0 for invoices in SST 96
-- This will make reimbursement SST show in the Reimb SST column
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_reimbursement_sst_amt = 0,
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0;

-- STEP 3: Verify the reset (AFTER)
SELECT 
    'AFTER RESET' AS section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt AS transferred,
    GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) AS remaining_reimb_sst,
    CASE 
        WHEN GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) > 0 
        THEN 'Will show Reimb SST'
        ELSE 'Still 0'
    END AS status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
ORDER BY remaining_reimb_sst DESC, im.invoice_no
LIMIT 10;

-- STEP 4: Recalculate SST main total to include reimbursement SST
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = 96
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET 
    sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = 96;

-- STEP 5: Final summary
SELECT 
    'FINAL SUMMARY' AS section,
    COUNT(*) AS total_invoices,
    SUM(CASE WHEN im.reimbursement_sst > 0 THEN 1 ELSE 0 END) AS invoices_with_reimb_sst,
    SUM(COALESCE(im.reimbursement_sst, 0)) AS total_reimbursement_sst,
    SUM(COALESCE(im.transferred_reimbursement_sst_amt, 0)) AS total_transferred,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS total_remaining_reimb_sst,
    SUM(COALESCE(sd.amount, 0)) AS total_sst,
    SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS grand_total,
    sm.amount AS sst_main_amount
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
INNER JOIN sst_main sm ON sm.id = sd.sst_main_id
WHERE sd.sst_main_id = 96
GROUP BY sm.amount;











