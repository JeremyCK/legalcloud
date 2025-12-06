-- ============================================================================
-- FIX SST AMOUNTS FOR ALL SST RECORDS (or specific record)
-- This script can be used for any SST record or all records
-- ============================================================================

-- OPTION 1: Fix specific SST record (change the ID)
-- Set @sst_main_id to the record you want to fix
SET @sst_main_id = 96;  -- Change this to the SST record ID you want to fix

-- OPTION 2: Fix ALL SST records
-- Uncomment the line below and comment out the SET @sst_main_id line above
-- SET @sst_main_id = NULL;  -- NULL means fix all records

-- ============================================================================
-- STEP 1: Preview what will be updated
-- ============================================================================
SELECT 
    'Preview - Records to Update' AS section,
    sd.sst_main_id,
    sd.id AS sst_detail_id,
    sd.loan_case_invoice_main_id,
    sd.amount AS current_amount,
    im.sst_inv AS invoice_sst_amount,
    im.invoice_no,
    CASE 
        WHEN sd.amount = 0 OR sd.amount IS NULL THEN 'Will set to invoice_sst_inv'
        WHEN sd.amount != COALESCE(im.sst_inv, 0) THEN 'Will update to match invoice_sst_inv'
        ELSE 'No change needed'
    END AS action
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE (@sst_main_id IS NULL OR sd.sst_main_id = @sst_main_id)
AND (
    sd.amount = 0 
    OR sd.amount IS NULL 
    OR sd.amount != COALESCE(im.sst_inv, 0)
)
ORDER BY sd.sst_main_id, sd.id
LIMIT 50;

-- ============================================================================
-- STEP 2: Update sst_details.amount from invoice.sst_inv
-- ============================================================================
UPDATE sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
SET 
    sd.amount = COALESCE(im.sst_inv, 0),
    sd.updated_at = NOW()
WHERE (@sst_main_id IS NULL OR sd.sst_main_id = @sst_main_id)
AND (
    sd.amount = 0 
    OR sd.amount IS NULL 
    OR sd.amount != COALESCE(im.sst_inv, 0)
);

-- ============================================================================
-- STEP 3: Recalculate SST main total for affected records
-- ============================================================================
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE (@sst_main_id IS NULL OR sd.sst_main_id = @sst_main_id)
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET 
    sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE (@sst_main_id IS NULL OR sm.id = @sst_main_id);

-- ============================================================================
-- STEP 4: Verification - Show summary of fixed records
-- ============================================================================
SELECT 
    'Verification Summary' AS section,
    sm.id AS sst_main_id,
    sm.payment_date,
    sm.transaction_id,
    COUNT(sd.id) AS invoice_count,
    SUM(COALESCE(sd.amount, 0)) AS total_sst,
    SUM(COALESCE(im.reimbursement_sst, 0)) AS total_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0)) AS calculated_total,
    sm.amount AS stored_amount,
    ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0))) AS difference,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + COALESCE(im.reimbursement_sst, 0))) < 0.01 
        THEN 'OK' 
        ELSE 'MISMATCH' 
    END AS status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE (@sst_main_id IS NULL OR sm.id = @sst_main_id)
GROUP BY sm.id, sm.payment_date, sm.transaction_id, sm.amount
ORDER BY sm.id;




