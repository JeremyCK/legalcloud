-- ============================================================================
-- SERVER PATCH: Fix SST Amounts for SST Record 96
-- Run this on the server to fix missing SST amounts
-- ============================================================================

-- STEP 1: Update sst_details.amount from invoice.sst_inv
-- This fixes the issue where SST shows 0.00
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

-- STEP 2: Recalculate SST main total to include both SST and full reimbursement SST
-- Note: Using full reimbursement_sst (not remaining) to match the code changes
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = 96
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET 
    sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = 96;

-- STEP 3: Verify the fix
SELECT 
    'Verification' AS section,
    COUNT(*) AS total_invoices,
    SUM(COALESCE(sd.amount, 0)) AS total_sst,
    SUM(COALESCE(im.reimbursement_sst, 0)) AS total_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS grand_total,
    sm.amount AS sst_main_amount,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0))) < 0.01 
        THEN 'OK' 
        ELSE 'MISMATCH' 
    END AS status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;

-- STEP 4: Sample check - Show a few invoices to verify SST amounts are updated
SELECT 
    'Sample Check' AS section,
    im.invoice_no,
    sd.amount AS sst_amount,
    im.sst_inv AS invoice_sst_inv,
    im.reimbursement_sst,
    CASE 
        WHEN sd.amount = COALESCE(im.sst_inv, 0) THEN 'OK'
        ELSE 'NEEDS FIX'
    END AS sst_status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY im.invoice_no
LIMIT 10;

