-- SQL Script to Fix Reimbursement SST for Invoices in SST Record 96
-- This calculates and updates reimbursement_sst based on invoice details

-- Step 1: Check current status
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.reimbursement_sst as current_reimb_sst,
    im.reimbursement_amount as current_reimb_amount,
    b.sst_rate,
    COALESCE(reimb_details.total_reimb, 0) as calculated_reimb_amount,
    ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2) as calculated_reimb_sst,
    ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2)) as difference
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
ORDER BY difference DESC;

-- Step 2: Update reimbursement amounts and SST
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
  AND (
    ABS(COALESCE(im.reimbursement_amount, 0) - COALESCE(reimb_details.total_reimb, 0)) > 0.01
    OR ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2)) > 0.01
  );

-- Step 3: Verify the update
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    b.sst_rate,
    COALESCE(reimb_details.total_reimb, 0) as calculated_reimb_amount,
    ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2) as calculated_reimb_sst,
    CASE 
        WHEN ABS(im.reimbursement_sst - ROUND(COALESCE(reimb_details.total_reimb, 0) * b.sst_rate / 100, 2)) < 0.01 
        THEN 'OK' 
        ELSE 'MISMATCH' 
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

-- Step 4: After fixing reimbursement SST, recalculate SST record total
-- Run this after Step 2:
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = 96
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = 96;






