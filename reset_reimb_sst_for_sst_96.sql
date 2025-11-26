-- RESET Reimbursement SST for SST Record 96
-- This will reset transferred_reimbursement_sst_amt so reimbursement SST shows in SST 96
-- WARNING: Only run this if reimbursement SST should be included in SST 96
-- If it was already transferred to another SST record, this will cause double-counting

-- STEP 1: Preview what will be reset
SELECT 
    'BEFORE RESET' as section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt as current_transferred,
    0 as new_transferred,
    GREATEST(0, (im.reimbursement_sst - 0)) as new_remaining_reimb_sst
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.transferred_reimbursement_sst_amt >= im.reimbursement_sst
ORDER BY im.invoice_no
LIMIT 10;

-- STEP 2: Reset transferred_reimbursement_sst_amt to 0 for invoices in SST 96
-- This assumes reimbursement SST should be included in SST 96
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_reimbursement_sst_amt = 0,
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND im.transferred_reimbursement_sst_amt >= im.reimbursement_sst;

-- STEP 3: Verify the reset
SELECT 
    'AFTER RESET' as section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt as transferred,
    GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) as remaining_reimb_sst,
    CASE 
        WHEN GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt)) > 0 
        THEN '✅ Will show Reimb SST'
        ELSE '❌ Still 0'
    END as status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY remaining_reimb_sst DESC, im.invoice_no
LIMIT 10;

-- STEP 4: Recalculate SST main total
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
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
    'FINAL SUMMARY' as section,
    COUNT(*) as total_invoices,
    SUM(COALESCE(im.reimbursement_sst, 0)) as total_reimbursement_sst,
    SUM(COALESCE(im.transferred_reimbursement_sst_amt, 0)) as total_transferred,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
    SUM(COALESCE(sd.amount, 0)) as total_sst,
    SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as grand_total
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96;


