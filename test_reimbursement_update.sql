-- Test script to add some reimbursement data for testing
-- First, let's see what account categories exist
SELECT DISTINCT ai.account_cat_id, ai.account_item_name 
FROM account_item ai 
WHERE ai.account_cat_id = 4;

-- Let's see if there are any invoices with reimbursement details
SELECT 
    im.invoice_no,
    COUNT(id.id) as reimbursement_entries,
    SUM(id.amount) as total_reimbursement
FROM loan_case_invoice_main im
LEFT JOIN loan_case_invoice_details id ON im.id = id.invoice_main_id
LEFT JOIN account_item ai ON id.account_item_id = ai.id AND ai.account_cat_id = 4
GROUP BY im.invoice_no
HAVING total_reimbursement > 0
LIMIT 10;

-- If no reimbursement data exists, we can manually add some for testing
-- (This is just for testing purposes)
INSERT INTO loan_case_invoice_details (
    invoice_main_id, 
    account_item_id, 
    amount, 
    created_at, 
    updated_at
)
SELECT 
    im.id,
    ai.id,
    100.00, -- Test reimbursement amount
    NOW(),
    NOW()
FROM loan_case_invoice_main im
CROSS JOIN account_item ai
WHERE im.invoice_no = 'DP20000830'
AND ai.account_cat_id = 4
LIMIT 1;












