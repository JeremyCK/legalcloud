-- Detailed comparison: loan_case_invoice_details vs loan_case_invoice_main
-- Compares each field individually and then totals
-- Joins with account_item to get account_cat_id

SELECT 
    im.id as invoice_id,
    im.invoice_no,
    
    -- Main table amounts
    ROUND(im.pfee1_inv, 2) as main_pfee1,
    ROUND(im.pfee2_inv, 2) as main_pfee2,
    ROUND(im.sst_inv, 2) as main_sst,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv), 2) as main_total,
    
    -- Details amounts (corrected logic)
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 1 THEN ild.amount ELSE 0 END), 0), 2) as details_pfee1,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 0 THEN ild.amount ELSE 0 END), 0), 2) as details_pfee2,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * (b.sst_rate/100) ELSE 0 END), 0), 2) as details_sst,
    ROUND(COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE 0 END), 0), 2) as details_total,
    
    -- Differences
    ROUND(im.pfee1_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 1 THEN ild.amount ELSE 0 END), 0), 2) as pfee1_diff,
    ROUND(im.pfee2_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 0 THEN ild.amount ELSE 0 END), 0), 2) as pfee2_diff,
    ROUND(im.sst_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * (b.sst_rate/100) ELSE 0 END), 0), 2) as sst_diff,
    ROUND((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE 0 END), 0), 2) as total_diff

FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details ild ON im.id = ild.invoice_main_id  
    AND ild.status <> 99
LEFT JOIN account_item ai ON ild.account_item_id = ai.id
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE im.status <> 99
GROUP BY im.id, im.invoice_no, im.pfee1_inv, im.pfee2_inv, im.sst_inv
HAVING 
    ABS(im.pfee1_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 1 THEN ild.amount ELSE 0 END), 0)) > 0.01
    OR ABS(im.pfee2_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 AND ai.pfee1_item = 0 THEN ild.amount ELSE 0 END), 0)) > 0.01
    OR ABS(im.sst_inv - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * (b.sst_rate/100) ELSE 0 END), 0)) > 0.01
    OR ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE 0 END), 0)) > 0.01
ORDER BY ABS((im.pfee1_inv + im.pfee2_inv + im.sst_inv) - COALESCE(SUM(CASE WHEN ai.account_cat_id = 1 THEN ild.amount * ((b.sst_rate/100) + 1) ELSE 0 END), 0)) DESC;
