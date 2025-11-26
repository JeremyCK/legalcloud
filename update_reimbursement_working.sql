-- WORKING VERSION - Handles all cases
-- This will update transferred_reimbursement_amt and transferred_reimbursement_sst_amt
-- based on what's actually in transfer_fee_details table

-- IMPORTANT: First run the diagnostic query to see if transfer_fee_details has records
-- If transfer_detail_count = 0, it means no transfer has been done yet for that invoice

-- UPDATE statement (will set to 0 if no transfer_fee_details exist)
UPDATE loan_case_invoice_main im
LEFT JOIN (
    SELECT 
        loan_case_invoice_main_id,
        SUM(COALESCE(reimbursement_amount, 0)) as total_reimbursement,
        SUM(COALESCE(reimbursement_sst_amount, 0)) as total_reimbursement_sst
    FROM transfer_fee_details
    WHERE status <> 99
      AND loan_case_invoice_main_id IS NOT NULL
    GROUP BY loan_case_invoice_main_id
) totals ON im.id = totals.loan_case_invoice_main_id
SET 
    im.transferred_reimbursement_amt = IFNULL(totals.total_reimbursement, 0),
    im.transferred_reimbursement_sst_amt = IFNULL(totals.total_reimbursement_sst, 0),
    im.updated_at = NOW()
WHERE im.invoice_no IN (
    '20002388','20002389','20002392','20002395','20002396','20002397','20002398','20002399',
    '20002400','20002401','20002403','20002404','20002405','20002406','20002408','20002409',
    '20002410','20002411','20002412','20002413','20002414','20002415','20002416','20002417',
    '20002418','20002419','20002420','20002423','20002424','20002425','20002426','20002427',
    '20002428','20002429','20002430','20002431','20002432','20002433','20002434','20002435',
    '20002438','20002439','20002440','20002441','20002442','20002443','20002444','20002445',
    '20002446','20002447','20002448','20002454','20002455','20002456','20002458','20002459',
    '20002460','20002462','20002463','20002465','20002466','20002468','20002469','20002470',
    '20002471','20002472','20002473','20002474','20002475','20002478','20002479','20002480'
);

-- Check one specific invoice to verify
SELECT 
    im.id,
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    im.transferred_reimbursement_amt,
    im.transferred_reimbursement_sst_amt,
    (SELECT COUNT(*) FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as transfer_count,
    (SELECT COALESCE(SUM(reimbursement_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as sum_from_tfd_amt,
    (SELECT COALESCE(SUM(reimbursement_sst_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as sum_from_tfd_sst
FROM loan_case_invoice_main im
WHERE im.invoice_no = '20002388';

