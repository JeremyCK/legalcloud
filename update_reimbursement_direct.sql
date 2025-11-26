-- DIRECT UPDATE - Copy reimbursement_amount and reimbursement_sst to transferred columns
-- For the 73 invoices highlighted in red

-- Step 1: Update loan_case_invoice_main table
-- Set transferred_reimbursement_amt = reimbursement_amount
-- Set transferred_reimbursement_sst_amt = reimbursement_sst
UPDATE loan_case_invoice_main
SET 
    transferred_reimbursement_amt = reimbursement_amount,
    transferred_reimbursement_sst_amt = reimbursement_sst,
    updated_at = NOW()
WHERE invoice_no IN (
    '20002388','20002389','20002392','20002395','20002396','20002397','20002398','20002399',
    '20002400','20002401','20002403','20002404','20002405','20002406','20002408','20002409',
    '20002410','20002411','20002412','20002413','20002414','20002415','20002416','20002417',
    '20002418','20002419','20002420','20002423','20002424','20002425','20002426','20002427',
    '20002428','20002429','20002430','20002431','20002432','20002433','20002434','20002435',
    '20002438','20002439','20002440','20002441','20002442','20002443','20002444','20002445',
    '20002446','20002447','20002448','20002454','20002455','20002456','20002458','20002459',
    '20002460','20002462','20002463','20002465','20002466','20002468','20002469','20002470',
    '20002471','20002472','20002473','20002474','20002475','20002477','20002478','20002479','20002480'
);

-- Step 2: Update transfer_fee_details table
-- Set reimbursement_amount = loan_case_invoice_main.reimbursement_amount
-- Set reimbursement_sst_amount = loan_case_invoice_main.reimbursement_sst
UPDATE transfer_fee_details tfd
INNER JOIN loan_case_invoice_main im ON tfd.loan_case_invoice_main_id = im.id
SET 
    tfd.reimbursement_amount = im.reimbursement_amount,
    tfd.reimbursement_sst_amount = im.reimbursement_sst
WHERE im.invoice_no IN (
    '20002388','20002389','20002392','20002395','20002396','20002397','20002398','20002399',
    '20002400','20002401','20002403','20002404','20002405','20002406','20002408','20002409',
    '20002410','20002411','20002412','20002413','20002414','20002415','20002416','20002417',
    '20002418','20002419','20002420','20002423','20002424','20002425','20002426','20002427',
    '20002428','20002429','20002430','20002431','20002432','20002433','20002434','20002435',
    '20002438','20002439','20002440','20002441','20002442','20002443','20002444','20002445',
    '20002446','20002447','20002448','20002454','20002455','20002456','20002458','20002459',
    '20002460','20002462','20002463','20002465','20002466','20002468','20002469','20002470',
    '20002471','20002472','20002473','20002474','20002475','20002477','20002478','20002479','20002480'
)
AND tfd.status <> 99;

-- Step 3: VERIFICATION - Check results
SELECT 
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    im.transferred_reimbursement_amt,
    im.transferred_reimbursement_sst_amt,
    CASE 
        WHEN im.transferred_reimbursement_amt = im.reimbursement_amount 
         AND im.transferred_reimbursement_sst_amt = im.reimbursement_sst
        THEN 'OK'
        ELSE 'MISMATCH'
    END as invoice_status,
    COUNT(tfd.id) as transfer_detail_count,
    SUM(tfd.reimbursement_amount) as tfd_reimbursement_amt,
    SUM(tfd.reimbursement_sst_amount) as tfd_reimbursement_sst_amt
FROM loan_case_invoice_main im
LEFT JOIN transfer_fee_details tfd ON tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99
WHERE im.invoice_no IN (
    '20002388','20002389','20002392','20002395','20002396','20002397','20002398','20002399',
    '20002400','20002401','20002403','20002404','20002405','20002406','20002408','20002409',
    '20002410','20002411','20002412','20002413','20002414','20002415','20002416','20002417',
    '20002418','20002419','20002420','20002423','20002424','20002425','20002426','20002427',
    '20002428','20002429','20002430','20002431','20002432','20002433','20002434','20002435',
    '20002438','20002439','20002440','20002441','20002442','20002443','20002444','20002445',
    '20002446','20002447','20002448','20002454','20002455','20002456','20002458','20002459',
    '20002460','20002462','20002463','20002465','20002466','20002468','20002469','20002470',
    '20002471','20002472','20002473','20002474','20002475','20002477','20002478','20002479','20002480'
)
GROUP BY im.invoice_no, im.reimbursement_amount, im.reimbursement_sst, 
         im.transferred_reimbursement_amt, im.transferred_reimbursement_sst_amt
ORDER BY im.invoice_no;

