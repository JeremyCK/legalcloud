-- MySQL Script to Recalculate transferred_reimbursement_amt and transferred_reimbursement_sst_amt
-- 
-- COLUMN MAPPING:
-- ===============
-- SOURCE TABLE: transfer_fee_details
--   - reimbursement_amount          → Sum these values
--   - reimbursement_sst_amount      → Sum these values
--
-- TARGET TABLE: loan_case_invoice_main
--   - transferred_reimbursement_amt      ← Update with SUM(reimbursement_amount)
--   - transferred_reimbursement_sst_amt   ← Update with SUM(reimbursement_sst_amount)
--
-- LOGIC:
-- For each invoice in loan_case_invoice_main:
--   1. Find all transfer_fee_details records for that invoice (where status <> 99)
--   2. SUM all reimbursement_amount values → update transferred_reimbursement_amt
--   3. SUM all reimbursement_sst_amount values → update transferred_reimbursement_sst_amt

-- ============================================================================
-- STEP 1: IDENTIFY INVOICES WITH DISCREPANCIES (Run this first to see what needs fixing)
-- ============================================================================
-- This query will show you all invoices where the stored values don't match 
-- the calculated values from transfer_fee_details
SELECT 
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    im.transferred_reimbursement_amt as stored_transferred_reimbursement,
    im.transferred_reimbursement_sst_amt as stored_transferred_reimbursement_sst,
    (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as calculated_transferred_reimbursement,
    (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as calculated_transferred_reimbursement_sst,
    CASE 
        WHEN ABS(im.transferred_reimbursement_amt - (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
                                                      FROM transfer_fee_details tfd 
                                                      WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99)) > 0.01
             OR ABS(im.transferred_reimbursement_sst_amt - (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
                                                             FROM transfer_fee_details tfd 
                                                             WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99)) > 0.01
        THEN 'NEEDS UPDATE'
        ELSE 'OK'
    END as status
FROM loan_case_invoice_main im
WHERE im.id IN (
    SELECT DISTINCT loan_case_invoice_main_id 
    FROM transfer_fee_details 
    WHERE status <> 99 AND loan_case_invoice_main_id IS NOT NULL
)
HAVING status = 'NEEDS UPDATE'
ORDER BY im.invoice_no;

-- ============================================================================
-- STEP 2: UPDATE OPTIONS (Choose one based on your needs)
-- ============================================================================

-- OPTION A: Update MULTIPLE SPECIFIC invoices (All invoices highlighted in red from Excel)
-- This will update all 73 invoices that were highlighted in red
-- SIMPLIFIED VERSION - More efficient
UPDATE loan_case_invoice_main im
INNER JOIN (
    SELECT 
        tfd.loan_case_invoice_main_id,
        SUM(COALESCE(tfd.reimbursement_amount, 0)) as total_reimbursement,
        SUM(COALESCE(tfd.reimbursement_sst_amount, 0)) as total_reimbursement_sst
    FROM transfer_fee_details tfd
    INNER JOIN loan_case_invoice_main lcim ON tfd.loan_case_invoice_main_id = lcim.id
    WHERE lcim.invoice_no IN (
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
    AND tfd.status <> 99
    GROUP BY tfd.loan_case_invoice_main_id
) totals ON im.id = totals.loan_case_invoice_main_id
SET 
    im.transferred_reimbursement_amt = totals.total_reimbursement,
    im.transferred_reimbursement_sst_amt = totals.total_reimbursement_sst,
    im.updated_at = NOW();

-- OPTION B: Update ALL invoices that have discrepancies (RECOMMENDED - fixes everything automatically)
-- This will update all invoices where stored values don't match calculated values
UPDATE loan_case_invoice_main im
INNER JOIN (
    SELECT 
        tfd.loan_case_invoice_main_id,
        COALESCE(SUM(tfd.reimbursement_amount), 0) as total_transferred_reimbursement,
        COALESCE(SUM(tfd.reimbursement_sst_amount), 0) as total_transferred_reimbursement_sst
    FROM transfer_fee_details tfd
    WHERE tfd.status <> 99
    GROUP BY tfd.loan_case_invoice_main_id
) reimbursement_totals ON im.id = reimbursement_totals.loan_case_invoice_main_id
SET 
    im.transferred_reimbursement_amt = reimbursement_totals.total_transferred_reimbursement,
    im.transferred_reimbursement_sst_amt = reimbursement_totals.total_transferred_reimbursement_sst,
    im.updated_at = NOW()
WHERE im.id IN (
    SELECT DISTINCT loan_case_invoice_main_id 
    FROM transfer_fee_details 
    WHERE status <> 99 AND loan_case_invoice_main_id IS NOT NULL
);

-- OPTION C: Update ALL invoices that have transfer fee details (even if they match)
-- Use this if you want to ensure everything is recalculated from scratch
/*
UPDATE loan_case_invoice_main im
LEFT JOIN (
    SELECT 
        tfd.loan_case_invoice_main_id,
        COALESCE(SUM(tfd.reimbursement_amount), 0) as total_transferred_reimbursement,
        COALESCE(SUM(tfd.reimbursement_sst_amount), 0) as total_transferred_reimbursement_sst
    FROM transfer_fee_details tfd
    WHERE tfd.status <> 99
    GROUP BY tfd.loan_case_invoice_main_id
) reimbursement_totals ON im.id = reimbursement_totals.loan_case_invoice_main_id
SET 
    im.transferred_reimbursement_amt = COALESCE(reimbursement_totals.total_transferred_reimbursement, 0),
    im.transferred_reimbursement_sst_amt = COALESCE(reimbursement_totals.total_transferred_reimbursement_sst, 0),
    im.updated_at = NOW()
WHERE im.id IN (
    SELECT DISTINCT loan_case_invoice_main_id 
    FROM transfer_fee_details 
    WHERE status <> 99 AND loan_case_invoice_main_id IS NOT NULL
);
*/

-- OPTION D: Set to 0 for invoices with no transfer fee details
/*
UPDATE loan_case_invoice_main im
LEFT JOIN transfer_fee_details tfd ON im.id = tfd.loan_case_invoice_main_id AND tfd.status <> 99
SET 
    im.transferred_reimbursement_amt = 0,
    im.transferred_reimbursement_sst_amt = 0,
    im.updated_at = NOW()
WHERE tfd.id IS NULL 
AND (im.transferred_reimbursement_amt > 0 OR im.transferred_reimbursement_sst_amt > 0);
*/

-- ============================================================================
-- STEP 3: VERIFICATION - Check results after update
-- ============================================================================

-- Verification for ALL invoices with transfer fee details
SELECT 
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    im.transferred_reimbursement_amt,
    im.transferred_reimbursement_sst_amt,
    (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as calculated_transferred_reimbursement,
    (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as calculated_transferred_reimbursement_sst,
    CASE 
        WHEN ABS(im.transferred_reimbursement_amt - (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
                                                      FROM transfer_fee_details tfd 
                                                      WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99)) <= 0.01
             AND ABS(im.transferred_reimbursement_sst_amt - (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
                                                              FROM transfer_fee_details tfd 
                                                              WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99)) <= 0.01
        THEN '✓ CORRECT'
        ELSE '✗ MISMATCH'
    END as verification_status
FROM loan_case_invoice_main im
WHERE im.id IN (
    SELECT DISTINCT loan_case_invoice_main_id 
    FROM transfer_fee_details 
    WHERE status <> 99 AND loan_case_invoice_main_id IS NOT NULL
)
ORDER BY im.invoice_no;

-- Verification for SPECIFIC invoices (the 73 invoices highlighted in red)
SELECT 
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    im.transferred_reimbursement_amt as stored_amt,
    im.transferred_reimbursement_sst_amt as stored_sst_amt,
    (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as calculated_amt,
    (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as calculated_sst_amt,
    CASE 
        WHEN ABS(im.transferred_reimbursement_amt - (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
                                                      FROM transfer_fee_details tfd 
                                                      WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99)) <= 0.01
             AND ABS(im.transferred_reimbursement_sst_amt - (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
                                                              FROM transfer_fee_details tfd 
                                                              WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99)) <= 0.01
        THEN '✓ CORRECT'
        ELSE '✗ MISMATCH'
    END as verification_status
FROM loan_case_invoice_main im
WHERE im.invoice_no IN (
    '20002388', '20002389', '20002392', '20002395', '20002396', '20002397', '20002398', '20002399',
    '20002400', '20002401', '20002403', '20002404', '20002405', '20002406', '20002408', '20002409',
    '20002410', '20002411', '20002412', '20002413', '20002414', '20002415', '20002416', '20002417',
    '20002418', '20002419', '20002420', '20002423', '20002424', '20002425', '20002426', '20002427',
    '20002428', '20002429', '20002430', '20002431', '20002432', '20002433', '20002434', '20002435',
    '20002438', '20002439', '20002440', '20002441', '20002442', '20002443', '20002444', '20002445',
    '20002446', '20002447', '20002448', '20002454', '20002455', '20002456', '20002458', '20002459',
    '20002460', '20002462', '20002463', '20002465', '20002466', '20002468', '20002469', '20002470',
    '20002471', '20002472', '20002473', '20002474', '20002475', '20002478', '20002479', '20002480'
)
ORDER BY im.invoice_no;

