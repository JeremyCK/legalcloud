-- COMPLETE UPDATE - Handles both loan_case_invoice_main_id and loan_case_main_bill_id links
-- Some older records might be linked via bill_id instead of invoice_id

-- Step 1: Check how records are linked
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.loan_case_main_bill_id as bill_id,
    COUNT(DISTINCT tfd1.id) as records_by_invoice_id,
    COUNT(DISTINCT tfd2.id) as records_by_bill_id,
    COALESCE(SUM(tfd1.reimbursement_amount), 0) + COALESCE(SUM(tfd2.reimbursement_amount), 0) as total_reimbursement,
    COALESCE(SUM(tfd1.reimbursement_sst_amount), 0) + COALESCE(SUM(tfd2.reimbursement_sst_amount), 0) as total_reimbursement_sst
FROM loan_case_invoice_main im
LEFT JOIN transfer_fee_details tfd1 ON tfd1.loan_case_invoice_main_id = im.id AND tfd1.status <> 99
LEFT JOIN transfer_fee_details tfd2 ON tfd2.loan_case_main_bill_id = im.loan_case_main_bill_id 
    AND tfd2.loan_case_invoice_main_id IS NULL AND tfd2.status <> 99
WHERE im.invoice_no = '20002388'
GROUP BY im.id, im.invoice_no, im.loan_case_main_bill_id;

-- Step 2: UPDATE considering both linking methods
UPDATE loan_case_invoice_main im
LEFT JOIN (
    SELECT 
        COALESCE(tfd1.loan_case_invoice_main_id, im2.id) as invoice_id,
        SUM(COALESCE(COALESCE(tfd1.reimbursement_amount, tfd2.reimbursement_amount), 0)) as total_reimbursement,
        SUM(COALESCE(COALESCE(tfd1.reimbursement_sst_amount, tfd2.reimbursement_sst_amount), 0)) as total_reimbursement_sst
    FROM loan_case_invoice_main im2
    LEFT JOIN transfer_fee_details tfd1 ON tfd1.loan_case_invoice_main_id = im2.id AND tfd1.status <> 99
    LEFT JOIN transfer_fee_details tfd2 ON tfd2.loan_case_main_bill_id = im2.loan_case_main_bill_id 
        AND tfd2.loan_case_invoice_main_id IS NULL AND tfd2.status <> 99
    WHERE im2.invoice_no IN (
        '20002388','20002389','20002392','20002395','20002396','20002397','20002398','20002399',
        '20002400','20002401','20002403','20002404','20002405','20002406','20002408','20002409',
        '20002410','20002411','20002412','20002413','20002414','20002415','20002416','20002417',
        '20002418','20002419','20002420','20002423','20002424','20002425','20002426','20002427',
        '20002428','20002429','20002430','20002431','20002432','20002433','20002434','20002435',
        '20002438','20002439','20002440','20002441','20002442','20002443','20002444','20002445',
        '20002446','20002447','20002448','20002454','20002455','20002456','20002458','20002459',
        '20002460','20002462','20002463','20002465','20002466','20002468','20002469','20002470',
        '20002471','20002472','20002473','20002474','20002475','20002478','20002479','20002480'
    )
    GROUP BY COALESCE(tfd1.loan_case_invoice_main_id, im2.id)
) totals ON im.id = totals.invoice_id
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

-- Step 3: SIMPLER VERSION - Just use loan_case_invoice_main_id (most common case)
UPDATE loan_case_invoice_main im
SET 
    transferred_reimbursement_amt = (
        SELECT IFNULL(SUM(reimbursement_amount), 0)
        FROM transfer_fee_details
        WHERE loan_case_invoice_main_id = im.id
          AND status <> 99
    ),
    transferred_reimbursement_sst_amt = (
        SELECT IFNULL(SUM(reimbursement_sst_amount), 0)
        FROM transfer_fee_details
        WHERE loan_case_invoice_main_id = im.id
          AND status <> 99
    ),
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
    '20002471','20002472','20002473','20002474','20002475','20002478','20002479','20002480'
);

-- Step 4: Verify
SELECT 
    invoice_no,
    transferred_reimbursement_amt,
    transferred_reimbursement_sst_amt
FROM loan_case_invoice_main
WHERE invoice_no = '20002388';

