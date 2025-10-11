-- Debug script for invoice DP20000790
-- This script will help investigate why total_amt_inv shows 1280.01 instead of 2800

-- 1. Find the invoice and its bill information
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.amount as invoice_amount,
    im.pfee1_inv,
    im.pfee2_inv,
    im.sst_inv,
    b.id as bill_id,
    b.sst_rate,
    b.total_amt_inv as bill_total_amt_inv,
    b.pfee1_inv as bill_pfee1_inv,
    b.pfee2_inv as bill_pfee2_inv,
    b.sst_inv as bill_sst_inv
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE im.invoice_no = 'DP20000790'
AND im.status <> 99;

-- 2. Get all invoice details for this invoice
SELECT 
    ild.id as detail_id,
    ild.amount as detail_amount,
    ild.ori_invoice_amt,
    ai.name as item_name,
    ai.account_cat_id,
    ai.pfee1_item,
    CASE 
        WHEN ai.account_cat_id = 1 THEN 'Professional Fee'
        WHEN ai.account_cat_id = 2 THEN 'Disbursement'
        WHEN ai.account_cat_id = 3 THEN 'Disbursement'
        ELSE 'Other'
    END as category_type
FROM loan_case_invoice_details ild
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_invoice_main im ON ild.invoice_main_id = im.id
WHERE im.invoice_no = 'DP20000790'
AND ild.status <> 99
ORDER BY ild.id;

-- 3. Calculate what the amounts should be based on details
SELECT 
    im.invoice_no,
    im.id as invoice_id,
    b.sst_rate,
    
    -- Current values in invoice_main
    im.amount as current_invoice_amount,
    im.pfee1_inv as current_pfee1,
    im.pfee2_inv as current_pfee2,
    im.sst_inv as current_sst,
    
    -- Calculated values from details
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 1 THEN ild.amount ELSE 0 END), 0), 2) as calculated_pfee1,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 0 THEN ild.amount ELSE 0 END), 0), 2) as calculated_pfee2,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * (b.sst_rate/100) ELSE 0 END), 0), 2) as calculated_sst,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE 0 END), 0), 2) as calculated_total,
    
    -- Differences
    ROUND(im.pfee1_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 1 THEN ild.amount ELSE 0 END), 0), 2) as pfee1_diff,
    ROUND(im.pfee2_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 0 THEN ild.amount ELSE 0 END), 0), 2) as pfee2_diff,
    ROUND(im.sst_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * (b.sst_rate/100) ELSE 0 END), 0), 2) as sst_diff,
    ROUND(im.amount - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE 0 END), 0), 2) as total_diff

FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.invoice_main_id  
    AND ild.status <> 99
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE im.invoice_no = 'DP20000790'
AND im.status <> 99
GROUP BY im.id, im.invoice_no, im.amount, im.pfee1_inv, im.pfee2_inv, im.sst_inv, b.sst_rate;

-- 4. Check all invoices for this bill to see the bill total calculation
SELECT 
    b.id as bill_id,
    b.total_amt_inv as current_bill_total,
    COUNT(im.id) as invoice_count,
    ROUND(SUM(im.amount), 2) as sum_of_invoice_amounts,
    ROUND(SUM(im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as sum_of_invoice_totals
FROM loan_case_bill_main b
LEFT JOIN loan_case_invoice_main im ON b.id = im.loan_case_main_bill_id
    AND im.status <> 99
WHERE b.id = (
    SELECT loan_case_main_bill_id 
    FROM loan_case_invoice_main 
    WHERE invoice_no = 'DP20000790' 
    AND status <> 99
)
GROUP BY b.id, b.total_amt_inv;

-- 5. Check party count for this bill
SELECT 
    b.id as bill_id,
    COUNT(ibp.id) as party_count,
    GROUP_CONCAT(ibp.customer_code) as customer_codes
FROM loan_case_bill_main b
LEFT JOIN invoice_billing_party ibp ON b.id = ibp.loan_case_main_bill_id
WHERE b.id = (
    SELECT loan_case_main_bill_id 
    FROM loan_case_invoice_main 
    WHERE invoice_no = 'DP20000790' 
    AND status <> 99
)
GROUP BY b.id;
