-- Fix SST Record 96: Update sst_details.amount from invoice.sst_inv
-- This script will update the sst_details.amount field to match the invoice's sst_inv field
-- where the amount is currently 0, NULL, or doesn't match

-- STEP 1: Preview what will be updated (run this first to see what will change)
SELECT 
    sd.id as sst_detail_id,
    sd.loan_case_invoice_main_id,
    sd.amount as current_amount,
    im.sst_inv as invoice_sst_amount,
    im.invoice_no,
    CASE 
        WHEN sd.amount = 0 OR sd.amount IS NULL THEN 'Will set to invoice_sst_inv'
        WHEN sd.amount != im.sst_inv THEN 'Will update to match invoice_sst_inv'
        ELSE 'No change needed'
    END as action
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
AND (
    sd.amount = 0 
    OR sd.amount IS NULL 
    OR sd.amount != COALESCE(im.sst_inv, 0)
)
ORDER BY sd.id;

-- STEP 2: Update sst_details.amount from invoice.sst_inv
-- Only update records where amount is 0, NULL, or doesn't match
UPDATE sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
SET 
    sd.amount = COALESCE(im.sst_inv, 0),
    sd.updated_at = NOW()
WHERE sd.sst_main_id = 96
AND (
    sd.amount = 0 
    OR sd.amount IS NULL 
    OR sd.amount != COALESCE(im.sst_inv, 0)
);

-- STEP 3: Recalculate and update sst_main.amount
-- This includes both SST amount and reimbursement SST
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0)) as total_sst,
        SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
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

-- STEP 4: Verify the fix
SELECT 
    'Verification' as check_type,
    sm.id as sst_main_id,
    sm.amount as sst_main_amount,
    COUNT(sd.id) as invoice_count,
    SUM(sd.amount) as total_sst_details_amount,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
    SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    CASE 
        WHEN ABS(sm.amount - SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) < 0.01 THEN 'OK'
        ELSE 'MISMATCH'
    END as status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;







