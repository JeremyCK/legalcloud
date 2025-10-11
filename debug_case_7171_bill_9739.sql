-- Debug Case 7171, Bill 9739 - Pfee2 Calculation Issue
-- Expected: Pfee2 should be 1800, but system shows different value

-- 1. Check the current bill data
SELECT 
    'Current Bill Data' as section,
    id,
    case_id,
    pfee1,
    pfee2,
    pfee,
    sst,
    sst_rate,
    total_amt_inv,
    pfee1_inv,
    pfee2_inv,
    sst_inv
FROM loan_case_bill_main 
WHERE id = 9739;

-- 2. Check bill details that should contribute to pfee2
SELECT 
    'Bill Details for Pfee2' as section,
    bd.id,
    bd.account_item_id,
    ai.name as account_name,
    ai.account_cat_id,
    ai.pfee1_item,
    bd.quo_amount_no_sst,
    bd.quo_amount,
    bd.amount,
    bd.status
FROM loan_case_bill_details bd
LEFT JOIN account_item ai ON bd.account_item_id = ai.id
WHERE bd.loan_case_main_bill_id = 9739
AND bd.status <> 99
AND (ai.account_cat_id = 1 OR ai.account_cat_id = 4)
AND ai.pfee1_item = 0  -- pfee1_item = 0 means it's pfee2
ORDER BY bd.id;

-- 3. Check all bill details for this bill
SELECT 
    'All Bill Details' as section,
    bd.id,
    bd.account_item_id,
    ai.name as account_name,
    ai.account_cat_id,
    ai.pfee1_item,
    bd.quo_amount_no_sst,
    bd.quo_amount,
    bd.amount,
    bd.status
FROM loan_case_bill_details bd
LEFT JOIN account_item ai ON bd.account_item_id = ai.id
WHERE bd.loan_case_main_bill_id = 9739
AND bd.status <> 99
ORDER BY bd.id;

-- 4. Check invoice data for this bill
SELECT 
    'Invoice Data' as section,
    im.id,
    im.invoice_no,
    im.pfee1_inv,
    im.pfee2_inv,
    im.sst_inv,
    im.amount,
    im.status
FROM loan_case_invoice_main im
WHERE im.loan_case_main_bill_id = 9739
AND im.status <> 99;

-- 5. Check invoice details that should contribute to pfee2
SELECT 
    'Invoice Details for Pfee2' as section,
    ild.id,
    ild.invoice_main_id,
    ild.account_item_id,
    ai.name as account_name,
    ai.account_cat_id,
    ai.pfee1_item,
    ild.amount,
    ild.ori_invoice_amt,
    ild.status
FROM loan_case_invoice_details ild
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_invoice_main im ON ild.invoice_main_id = im.id
WHERE im.loan_case_main_bill_id = 9739
AND ild.status <> 99
AND (ai.account_cat_id = 1 OR ai.account_cat_id = 4)
AND ai.pfee1_item = 0  -- pfee1_item = 0 means it's pfee2
ORDER BY ild.id;

-- 6. Manual calculation of what pfee2 should be
SELECT 
    'Manual Pfee2 Calculation' as section,
    SUM(CASE 
        WHEN ai.pfee1_item = 0 AND (ai.account_cat_id = 1 OR ai.account_cat_id = 4) 
        THEN ild.amount 
        ELSE 0 
    END) as calculated_pfee2_from_invoice_details,
    SUM(CASE 
        WHEN ai.pfee1_item = 0 AND (ai.account_cat_id = 1 OR ai.account_cat_id = 4) 
        THEN bd.quo_amount_no_sst 
        ELSE 0 
    END) as calculated_pfee2_from_bill_details
FROM loan_case_invoice_details ild
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_invoice_main im ON ild.invoice_main_id = im.id
LEFT JOIN loan_case_bill_details bd ON bd.loan_case_main_bill_id = im.loan_case_main_bill_id 
    AND bd.account_item_id = ild.account_item_id
WHERE im.loan_case_main_bill_id = 9739
AND ild.status <> 99
AND bd.status <> 99;
