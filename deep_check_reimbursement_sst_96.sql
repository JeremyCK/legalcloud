-- Deep Diagnostic for Reimbursement SST - SST Record 96
-- This will show exactly why Reimb SST is 0.00

-- STEP 1: Check if invoices have reimbursement items (account_cat_id = 4)
SELECT 
    'STEP 1: Invoice Reimbursement Items' as section,
    im.id as invoice_id,
    im.invoice_no,
    COUNT(ild.id) as reimbursement_item_count,
    COALESCE(SUM(ild.amount), 0) as total_reimbursement_amount,
    COALESCE(SUM(ild.amount), 0) * COALESCE(b.sst_rate, 6) / 100 as calculated_reimbursement_sst,
    b.sst_rate as bill_sst_rate,
    CASE 
        WHEN COUNT(ild.id) = 0 THEN '❌ NO REIMBURSEMENT ITEMS'
        WHEN b.sst_rate IS NULL OR b.sst_rate = 0 THEN '⚠️ NO SST RATE'
        ELSE '✅ HAS REIMBURSEMENT ITEMS'
    END as status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case_invoice_details ild ON ild.invoice_main_id = im.id
LEFT JOIN account_item ai ON ai.id = ild.account_item_id AND ai.account_cat_id = 4
WHERE sd.sst_main_id = 96
  AND ild.status <> 99
GROUP BY im.id, im.invoice_no, b.sst_rate
ORDER BY reimbursement_item_count DESC, im.invoice_no;

-- STEP 2: Check current reimbursement_sst values in invoice_main
SELECT 
    'STEP 2: Current Reimbursement SST Values' as section,
    im.id as invoice_id,
    im.invoice_no,
    COALESCE(im.reimbursement_amount, 0) as reimbursement_amount,
    COALESCE(im.reimbursement_sst, 0) as reimbursement_sst,
    COALESCE(im.transferred_reimbursement_sst_amt, 0) as transferred_reimb_sst,
    GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_reimb_sst,
    CASE 
        WHEN COALESCE(im.reimbursement_sst, 0) = 0 THEN '❌ reimbursement_sst is 0 or NULL'
        WHEN COALESCE(im.transferred_reimbursement_sst_amt, 0) >= COALESCE(im.reimbursement_sst, 0) THEN '⚠️ Already fully transferred'
        ELSE '✅ Has remaining reimbursement SST'
    END as status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY remaining_reimb_sst DESC, im.invoice_no;

-- STEP 3: Compare calculated vs stored reimbursement SST
SELECT 
    'STEP 3: Calculated vs Stored Comparison' as section,
    im.id as invoice_id,
    im.invoice_no,
    COALESCE(im.reimbursement_sst, 0) as stored_reimbursement_sst,
    COALESCE(calculated_reimb.total_reimb, 0) as calculated_reimb_amount,
    COALESCE(b.sst_rate, 6) as sst_rate,
    ROUND(COALESCE(calculated_reimb.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2) as calculated_reimbursement_sst,
    ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(COALESCE(calculated_reimb.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2)) as difference,
    CASE 
        WHEN calculated_reimb.total_reimb IS NULL OR calculated_reimb.total_reimb = 0 THEN '❌ No reimbursement items found'
        WHEN ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(COALESCE(calculated_reimb.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2)) < 0.01 THEN '✅ Matches'
        ELSE '⚠️ MISMATCH - needs update'
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
) calculated_reimb ON calculated_reimb.invoice_main_id = im.id
WHERE sd.sst_main_id = 96
ORDER BY calculated_reimb.total_reimb DESC, im.invoice_no;

-- STEP 4: Show sample reimbursement items
SELECT 
    'STEP 4: Sample Reimbursement Items' as section,
    im.invoice_no,
    ild.id as detail_id,
    ild.amount as detail_amount,
    ai.name as account_item_name,
    ai.account_cat_id,
    ild.status as detail_status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
INNER JOIN loan_case_invoice_details ild ON ild.invoice_main_id = im.id
INNER JOIN account_item ai ON ai.id = ild.account_item_id
WHERE sd.sst_main_id = 96
  AND ai.account_cat_id = 4
  AND ild.status <> 99
ORDER BY im.invoice_no, ild.id
LIMIT 20;






