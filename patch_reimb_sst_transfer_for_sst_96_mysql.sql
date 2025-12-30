-- ============================================================================
-- PATCH: Update transferred_reimbursement_sst_amt for SST Record 96
-- MySQL Format - Ready to execute
-- ============================================================================
-- This will properly mark reimbursement SST as transferred when invoices are in SST 96
-- This fixes the issue where reimbursement SST wasn't updated during transfer
-- ============================================================================

-- STEP 1: Check current state (BEFORE PATCH)
SELECT 
    'BEFORE PATCH' AS section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt AS current_transferred,
    im.reimbursement_sst AS should_be_transferred,
    CASE 
        WHEN im.transferred_reimbursement_sst_amt >= im.reimbursement_sst THEN 'Already transferred'
        WHEN im.transferred_reimbursement_sst_amt < im.reimbursement_sst THEN 'Needs update'
        ELSE 'Not transferred'
    END AS status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
ORDER BY im.invoice_no
LIMIT 10;

-- STEP 2: Update transferred_reimbursement_sst_amt to match reimbursement_sst
-- This marks the reimbursement SST as transferred for invoices in SST 96
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_reimbursement_sst_amt = im.reimbursement_sst,
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
  AND COALESCE(im.transferred_reimbursement_sst_amt, 0) < im.reimbursement_sst;

-- STEP 3: Also update transferred_sst_amt if needed (for consistency)
-- Make sure regular SST is also marked as transferred
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_sst_amt = COALESCE(im.sst_inv, 0),
    im.bln_sst = 1,
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND COALESCE(im.transferred_sst_amt, 0) < COALESCE(im.sst_inv, 0);

-- STEP 4: Sync bln_sst to bill records
UPDATE loan_case_bill_main b
INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    b.bln_sst = 1,
    b.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND b.bln_sst = 0;

-- STEP 5: Verify the patch (AFTER PATCH)
SELECT 
    'AFTER PATCH' AS section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt AS transferred,
    GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) AS remaining_reimb_sst,
    CASE 
        WHEN im.transferred_reimbursement_sst_amt >= im.reimbursement_sst THEN 'Correctly transferred'
        ELSE 'Still needs update'
    END AS status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
ORDER BY im.invoice_no
LIMIT 10;

-- STEP 6: Summary
SELECT 
    'SUMMARY' AS section,
    COUNT(*) AS total_invoices,
    SUM(CASE WHEN im.reimbursement_sst > 0 THEN 1 ELSE 0 END) AS invoices_with_reimb_sst,
    SUM(im.reimbursement_sst) AS total_reimbursement_sst,
    SUM(im.transferred_reimbursement_sst_amt) AS total_transferred_reimb_sst,
    SUM(GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt))) AS total_remaining_reimb_sst,
    CASE 
        WHEN SUM(GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt))) = 0 
        THEN 'All reimbursement SST correctly transferred'
        ELSE 'Some reimbursement SST not transferred'
    END AS status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96;

-- STEP 7: Recalculate SST main total (should remain the same since reimbursement SST is already included)
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

-- STEP 8: Final verification - Show SST main total
SELECT 
    'FINAL VERIFICATION' AS section,
    sm.id AS sst_main_id,
    sm.amount AS stored_amount,
    COUNT(sd.id) AS invoice_count,
    SUM(COALESCE(sd.amount, 0)) AS total_sst,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS total_remaining_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) AS calculated_total,
    ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) AS difference,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) < 0.01 
        THEN 'OK' 
        ELSE 'MISMATCH' 
    END AS status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;












