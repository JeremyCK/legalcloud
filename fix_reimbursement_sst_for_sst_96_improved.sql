-- IMPROVED Fix Script for Reimbursement SST - SST Record 96
-- This version handles edge cases better

-- STEP 1: DIAGNOSTIC - Check what needs to be fixed
-- Run this first to see what's wrong
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.loan_case_main_bill_id,
    b.id as bill_id,
    b.sst_rate,
    im.reimbursement_amount as current_reimb_amount,
    im.reimbursement_sst as current_reimb_sst,
    COALESCE(reimb_details.total_reimb, 0) as calculated_reimb_amount,
    ROUND(COALESCE(reimb_details.total_reimb, 0) * COALESCE(b.sst_rate, 0) / 100, 2) as calculated_reimb_sst,
    CASE 
        WHEN b.sst_rate IS NULL OR b.sst_rate = 0 THEN 'NO SST RATE'
        WHEN reimb_details.total_reimb IS NULL OR reimb_details.total_reimb = 0 THEN 'NO REIMB DETAILS'
        WHEN ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(reimb_details.total_reimb * b.sst_rate / 100, 2)) < 0.01 THEN 'OK'
        ELSE 'NEEDS UPDATE'
    END as status
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
) reimb_details ON reimb_details.invoice_main_id = im.id
WHERE sd.sst_main_id = 96
ORDER BY status DESC, im.invoice_no;

-- STEP 2: FIX - Update reimbursement amounts and SST
-- Only updates invoices that have reimbursement details AND SST rate
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
INNER JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN (
    SELECT 
        ild.invoice_main_id,
        SUM(ild.amount) as total_reimb
    FROM loan_case_invoice_details ild
    INNER JOIN account_item ai ON ai.id = ild.account_item_id
    WHERE ai.account_cat_id = 4
      AND ild.status <> 99
    GROUP BY ild.invoice_main_id
) reimb_details ON reimb_details.invoice_main_id = im.id
SET 
    im.reimbursement_amount = COALESCE(reimb_details.total_reimb, 0),
    im.reimbursement_sst = ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2),
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND b.sst_rate IS NOT NULL 
  AND b.sst_rate > 0
  AND reimb_details.total_reimb IS NOT NULL
  AND reimb_details.total_reimb > 0
  AND (
    ABS(COALESCE(im.reimbursement_amount, 0) - COALESCE(reimb_details.total_reimb, 0)) > 0.01
    OR ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2)) > 0.01
  );

-- STEP 3: VERIFY - Check what was updated
SELECT 
    'After Update' as section,
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    COALESCE(reimb_details.total_reimb, 0) as calculated_reimb_amount,
    ROUND(COALESCE(reimb_details.total_reimb, 0) * COALESCE(b.sst_rate, 0) / 100, 2) as calculated_reimb_sst,
    CASE 
        WHEN ABS(im.reimbursement_sst - ROUND(COALESCE(reimb_details.total_reimb, 0) * COALESCE(b.sst_rate, 0) / 100, 2)) < 0.01 
        THEN '✅ OK' 
        WHEN reimb_details.total_reimb IS NULL OR reimb_details.total_reimb = 0 
        THEN '⚠️ No reimbursement details'
        WHEN b.sst_rate IS NULL OR b.sst_rate = 0 
        THEN '⚠️ No SST rate'
        ELSE '❌ MISMATCH' 
    END as status
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
) reimb_details ON reimb_details.invoice_main_id = im.id
WHERE sd.sst_main_id = 96
ORDER BY im.invoice_no;

-- STEP 4: RECALCULATE SST RECORD TOTAL
-- Run this after fixing reimbursement SST
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = 96
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = 96;

-- STEP 5: FINAL VERIFICATION
SELECT 
    'Final Check' as section,
    sm.id as sst_main_id,
    sm.amount as stored_amount,
    SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    ABS(sm.amount - SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) as difference,
    CASE 
        WHEN ABS(sm.amount - SUM(sd.amount + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) < 0.01 
        THEN '✅ OK' 
        ELSE '❌ MISMATCH' 
    END as status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;











