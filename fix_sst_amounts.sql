-- SQL Script to Fix SST Record Amounts
-- This recalculates the total amount for SST records including reimbursement SST
-- 
-- Usage:
-- 1. For single record: Run the query with WHERE sm.id = 96
-- 2. For all records: Remove the WHERE clause or use WHERE sm.id IN (96, 97, ...)

-- First, let's see what needs to be fixed
SELECT 
    sm.id as sst_main_id,
    sm.payment_date,
    sm.transaction_id,
    sm.amount as current_stored_amount,
    COUNT(sd.id) as invoice_count,
    SUM(sd.amount) as total_sst,
    SUM(GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
    SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    (sm.amount - SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) as difference
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96  -- Change this to fix specific record, or remove to see all
GROUP BY sm.id, sm.payment_date, sm.transaction_id, sm.amount
HAVING ABS(difference) > 0.01  -- Only show records with significant difference
ORDER BY sm.id;

-- Now, update the SST main records with the correct calculated amounts
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = 96  -- Change this to fix specific record, or remove WHERE to fix all
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = 96;  -- Change this to fix specific record, or remove to fix all

-- Verify the fix
SELECT 
    sm.id as sst_main_id,
    sm.payment_date,
    sm.amount as new_stored_amount,
    COUNT(sd.id) as invoice_count,
    SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    ABS(sm.amount - SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) as difference
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96  -- Change this to verify specific record
GROUP BY sm.id, sm.payment_date, sm.amount
HAVING ABS(difference) > 0.01;

-- If the query above returns no rows, the fix was successful!











