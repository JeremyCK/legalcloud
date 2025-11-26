-- Diagnostic Query for SST Record 96
-- This will show what data exists and what's missing

-- 1. Check SST Main Record
SELECT 
    'SST Main Record' as check_type,
    id,
    payment_date,
    transaction_id,
    amount as stored_amount,
    status,
    is_recon,
    created_at,
    updated_at
FROM sst_main
WHERE id = 96;

-- 2. Check SST Details Records
SELECT 
    'SST Details' as check_type,
    sd.id as sst_detail_id,
    sd.sst_main_id,
    sd.loan_case_invoice_main_id,
    sd.amount as sst_details_amount,
    sd.status,
    sd.created_at,
    sd.updated_at,
    -- Invoice data
    im.invoice_no,
    im.sst_inv as invoice_sst_amount,
    im.reimbursement_sst,
    im.transferred_sst_amt,
    im.transferred_reimbursement_sst_amt,
    im.bln_sst,
    -- Bill data
    b.collected_amt,
    b.payment_receipt_date,
    -- Case data
    l.case_ref_no,
    c.name as client_name
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
LEFT JOIN client c ON c.id = l.customer_id
WHERE sd.sst_main_id = 96
ORDER BY sd.id;

-- 3. Summary: Compare sst_details.amount vs invoice.sst_inv
SELECT 
    'Summary Comparison' as check_type,
    COUNT(*) as total_records,
    SUM(sd.amount) as total_sst_details_amount,
    SUM(COALESCE(im.sst_inv, 0)) as total_invoice_sst_amount,
    SUM(CASE WHEN sd.amount = 0 OR sd.amount IS NULL THEN 1 ELSE 0 END) as records_with_zero_amount,
    SUM(CASE WHEN im.sst_inv > 0 AND (sd.amount = 0 OR sd.amount IS NULL) THEN 1 ELSE 0 END) as records_needing_fix
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96;

-- 4. Detailed records that need fixing
SELECT 
    'Records Needing Fix' as check_type,
    sd.id as sst_detail_id,
    sd.loan_case_invoice_main_id,
    sd.amount as current_sst_details_amount,
    im.invoice_no,
    im.sst_inv as invoice_sst_amount,
    CASE 
        WHEN sd.amount = 0 OR sd.amount IS NULL THEN 'NEEDS FIX'
        WHEN sd.amount != im.sst_inv THEN 'MISMATCH'
        ELSE 'OK'
    END as status
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
AND (sd.amount = 0 OR sd.amount IS NULL OR sd.amount != COALESCE(im.sst_inv, 0))
ORDER BY sd.id;
