-- FORCE FIX Reimbursement SST for SST Record 96
-- This script will recalculate reimbursement SST from scratch
-- Run this if the previous fix didn't work

-- STEP 1: Show what will be updated
SELECT 
    'BEFORE FIX' as section,
    im.id as invoice_id,
    im.invoice_no,
    COALESCE(im.reimbursement_amount, 0) as current_reimb_amount,
    COALESCE(im.reimbursement_sst, 0) as current_reimb_sst,
    COALESCE(calculated_reimb.total_reimb, 0) as calculated_reimb_amount,
    COALESCE(b.sst_rate, 6) as sst_rate,
    ROUND(COALESCE(calculated_reimb.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2) as calculated_reimb_sst
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN (
    SELECT 
        ild.invoice_main_id,
        SUM(ild.amount) as total_reimb
    FROM loan_case_invoice_details ild
    INNER JOIN account_item ai ON ai.id = ild.account_item_id
    WHERE ai.account_cat_id = 4
      AND ild.status <> 99
    GROUP BY ild.invoice_main_id
) calculated_reimb ON calculated_reimb.invoice_main_id = im.id
WHERE sd.sst_main_id = 96
ORDER BY im.invoice_no;

-- STEP 2: Update reimbursement_amount and reimbursement_sst
-- This will update ALL invoices in SST 96, even if they don't have reimbursement items (will set to 0)
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN (
    SELECT 
        ild.invoice_main_id,
        SUM(ild.amount) as total_reimb
    FROM loan_case_invoice_details ild
    INNER JOIN account_item ai ON ai.id = ild.account_item_id
    WHERE ai.account_cat_id = 4
      AND ild.status <> 99
    GROUP BY ild.invoice_main_id
) calculated_reimb ON calculated_reimb.invoice_main_id = im.id
SET 
    im.reimbursement_amount = COALESCE(calculated_reimb.total_reimb, 0),
    im.reimbursement_sst = ROUND(COALESCE(calculated_reimb.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2),
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96;

-- STEP 3: Reset transferred_reimbursement_sst_amt if it's greater than reimbursement_sst
-- This ensures remaining reimbursement SST shows correctly
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_reimbursement_sst_amt = LEAST(
        COALESCE(im.transferred_reimbursement_sst_amt, 0),
        COALESCE(im.reimbursement_sst, 0)
    ),
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
AND COALESCE(im.transferred_reimbursement_sst_amt, 0) > COALESCE(im.reimbursement_sst, 0);

-- STEP 4: Show AFTER update
SELECT 
    'AFTER FIX' as section,
    im.id as invoice_id,
    im.invoice_no,
    COALESCE(im.reimbursement_amount, 0) as reimbursement_amount,
    COALESCE(im.reimbursement_sst, 0) as reimbursement_sst,
    COALESCE(im.transferred_reimbursement_sst_amt, 0) as transferred_reimb_sst,
    GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_reimb_sst,
    CASE 
        WHEN COALESCE(im.reimbursement_sst, 0) = 0 THEN 'No reimbursement SST'
        WHEN COALESCE(im.transferred_reimbursement_sst_amt, 0) >= COALESCE(im.reimbursement_sst, 0) THEN 'Already fully transferred'
        ELSE '✅ Has remaining reimbursement SST'
    END as status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY remaining_reimb_sst DESC, im.invoice_no;

-- STEP 5: Recalculate SST main total
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

-- STEP 6: Final verification
SELECT 
    'FINAL VERIFICATION' as section,
    sm.id as sst_main_id,
    sm.amount as stored_amount,
    COUNT(sd.id) as invoice_count,
    SUM(COALESCE(sd.amount, 0)) as total_sst,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) < 0.01 
        THEN '✅ OK' 
        ELSE '❌ MISMATCH' 
    END as status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;






