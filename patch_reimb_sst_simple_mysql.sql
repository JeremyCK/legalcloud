-- ============================================================================
-- SIMPLE PATCH: Update transferred_reimbursement_sst_amt for SST Record 96
-- MySQL Format - Execute these UPDATE statements
-- ============================================================================

-- Update transferred_reimbursement_sst_amt to match reimbursement_sst
-- This marks the reimbursement SST as transferred for invoices in SST 96
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_reimbursement_sst_amt = im.reimbursement_sst,
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0
  AND COALESCE(im.transferred_reimbursement_sst_amt, 0) < im.reimbursement_sst;

-- Also update transferred_sst_amt if needed (for consistency)
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_sst_amt = COALESCE(im.sst_inv, 0),
    im.bln_sst = 1,
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND COALESCE(im.transferred_sst_amt, 0) < COALESCE(im.sst_inv, 0);

-- Sync bln_sst to bill records
UPDATE loan_case_bill_main b
INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    b.bln_sst = 1,
    b.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND b.bln_sst = 0;

-- Recalculate SST main total
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












