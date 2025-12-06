-- Quick Check: Why Reimb SST is 0.00 for SST Record 96
-- Run this to see the exact reason

SELECT 
    im.invoice_no,
    -- Reimbursement SST data
    COALESCE(im.reimbursement_sst, 0) as reimbursement_sst,
    COALESCE(im.transferred_reimbursement_sst_amt, 0) as transferred_reimb_sst,
    GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_reimb_sst,
    -- Check if invoice has reimbursement items
    (SELECT COUNT(*) 
     FROM loan_case_invoice_details ild
     INNER JOIN account_item ai ON ai.id = ild.account_item_id
     WHERE ild.invoice_main_id = im.id
       AND ai.account_cat_id = 4
       AND ild.status <> 99) as reimbursement_item_count,
    -- Reason why Reimb SST is 0
    CASE 
        WHEN (SELECT COUNT(*) 
              FROM loan_case_invoice_details ild
              INNER JOIN account_item ai ON ai.id = ild.account_item_id
              WHERE ild.invoice_main_id = im.id
                AND ai.account_cat_id = 4
                AND ild.status <> 99) = 0 
        THEN '❌ No reimbursement items in invoice'
        
        WHEN COALESCE(im.reimbursement_sst, 0) = 0 
        THEN '❌ reimbursement_sst field is 0 (needs calculation)'
        
        WHEN COALESCE(im.transferred_reimbursement_sst_amt, 0) >= COALESCE(im.reimbursement_sst, 0) 
        THEN '⚠️ Already fully transferred to another SST record'
        
        ELSE '✅ Should show Reimb SST > 0'
    END as reason
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY remaining_reimb_sst DESC, im.invoice_no;




