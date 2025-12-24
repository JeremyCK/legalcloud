-- ============================================================================
-- FIX SST AMOUNTS FOR SPECIFIC SST RECORD
-- Simple version - just change the SST record ID
-- ============================================================================

-- CHANGE THIS: Set the SST record ID you want to fix
SET @sst_record_id = 96;

-- ============================================================================
-- Update sst_details.amount from invoice.sst_inv
-- ============================================================================
UPDATE sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
SET 
    sd.amount = COALESCE(im.sst_inv, 0),
    sd.updated_at = NOW()
WHERE sd.sst_main_id = @sst_record_id
AND (
    sd.amount = 0 
    OR sd.amount IS NULL 
    OR sd.amount != COALESCE(im.sst_inv, 0)
);

-- ============================================================================
-- Recalculate SST main total
-- ============================================================================
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = @sst_record_id
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET 
    sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = @sst_record_id;

-- ============================================================================
-- Verify the fix
-- ============================================================================
SELECT 
    sm.id AS sst_main_id,
    COUNT(sd.id) AS invoice_count,
    SUM(COALESCE(sd.amount, 0)) AS total_sst,
    SUM(COALESCE(im.reimbursement_sst, 0)) AS total_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS calculated_total,
    sm.amount AS stored_amount,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0))) < 0.01 
        THEN 'OK' 
        ELSE 'MISMATCH' 
    END AS status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = @sst_record_id
GROUP BY sm.id, sm.amount;










