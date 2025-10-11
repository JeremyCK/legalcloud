-- DIAGNOSTIC QUERIES - Run these first to understand the data

-- 1. Check current state of loan_case_bill_details for the specified IDs
SELECT 
    lcbm.id as bill_main_id,
    lcbd.id as bill_detail_id,
    lcbd.account_item_id,
    ai.name as account_item_name,
    ai.account_cat_id,
    lcbm.bln_invoice,
    lcbm.status as main_status,
    lcbd.status as detail_status
FROM loan_case_bill_details lcbd
JOIN loan_case_bill_main lcbm ON lcbd.loan_case_main_bill_id = lcbm.id
JOIN account_item ai ON lcbd.account_item_id = ai.id
WHERE lcbm.id IN (8215, 8407, 8406, 8924, 8890, 8989, 9242)
ORDER BY lcbm.id, lcbd.id;

-- 2. Check what account items exist in both categories (disbursement=3, reimbursement=4)
SELECT 
    ai1.name,
    ai1.id as disbursement_id,
    ai1.account_cat_id as disbursement_cat,
    ai2.id as reimbursement_id,
    ai2.account_cat_id as reimbursement_cat
FROM account_item ai1
LEFT JOIN account_item ai2 ON ai1.name = ai2.name AND ai2.account_cat_id = 4
WHERE ai1.account_cat_id = 3
ORDER BY ai1.name;

-- 3. Check if there are any reimbursement items (cat_id=4) in the specified bill details
SELECT 
    lcbm.id as bill_main_id,
    lcbd.id as bill_detail_id,
    ai.name as item_name,
    ai.account_cat_id
FROM loan_case_bill_details lcbd
JOIN loan_case_bill_main lcbm ON lcbd.loan_case_main_bill_id = lcbm.id
JOIN account_item ai ON lcbd.account_item_id = ai.id
WHERE lcbm.id IN (8215, 8407, 8406, 8924, 8890, 8989, 9242)
  AND ai.account_cat_id = 4  -- Reimbursement items
ORDER BY lcbm.id, lcbd.id;

-- REVERT QUERY (run after diagnostics)
-- UPDATE loan_case_bill_details lcbd
-- JOIN account_item ai_current ON lcbd.account_item_id = ai_current.id
-- JOIN account_item ai_revert ON ai_current.name = ai_revert.name
-- JOIN loan_case_bill_main lcbm ON lcbd.loan_case_main_bill_id = lcbm.id
-- SET lcbd.account_item_id = ai_revert.id
-- WHERE ai_current.account_cat_id = 4  -- Current reimbursement (what we want to change FROM)
--   AND ai_revert.account_cat_id = 3   -- Target disbursement (what we want to change TO)
--   AND ai_current.name = ai_revert.name
--   AND lcbm.bln_invoice = 0
--   AND lcbm.status != 99
--   AND lcbd.status != 99
--   AND lcbm.id IN (8215, 8407, 8406, 8924, 8890, 8989, 9242);

-- Optional: Add a verification query to check the results
-- SELECT 
--     lcbm.id as bill_main_id,
--     lcbd.id as bill_detail_id,
--     ai_current.name as item_name,
--     ai_current.account_cat_id as current_category,
--     ai_revert.account_cat_id as reverted_category
-- FROM loan_case_bill_details lcbd
-- JOIN account_item ai_current ON lcbd.account_item_id = ai_current.id
-- JOIN loan_case_bill_main lcbm ON lcbd.loan_case_main_bill_id = lcbm.id
-- WHERE lcbm.id IN (8215, 8407, 8406, 8924, 8890, 8989, 9242)
--   AND ai_current.account_cat_id = 3  -- Should show disbursement items after revert
-- ORDER BY lcbm.id, lcbd.id;
