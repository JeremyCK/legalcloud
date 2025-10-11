-- Update sst_details table to populate loan_case_invoice_main_id
-- This script links existing SST details to invoice records through the bill relationship

-- Step 1: Check current state of sst_details table
SELECT 
    'Current sst_details structure' as info,
    COUNT(*) as total_records,
    COUNT(loan_case_invoice_main_id) as records_with_invoice_id,
    COUNT(loan_case_main_bill_id) as records_with_bill_id
FROM sst_details;

-- Step 2: Show sample data before update
SELECT 
    'Sample data before update' as info,
    sd.id,
    sd.sst_main_id,
    sd.loan_case_main_bill_id,
    sd.loan_case_invoice_main_id,
    sd.amount,
    b.id as bill_id,
    im.id as invoice_id,
    im.invoice_no
FROM sst_details sd
LEFT JOIN loan_case_bill_main b ON b.id = sd.loan_case_main_bill_id
LEFT JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
WHERE sd.loan_case_invoice_main_id IS NULL
LIMIT 5;

-- Step 3: Update sst_details to populate loan_case_invoice_main_id
-- This links through: sst_details -> loan_case_bill_main -> loan_case_invoice_main
UPDATE sst_details sd
INNER JOIN loan_case_bill_main b ON b.id = sd.loan_case_main_bill_id
INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
SET sd.loan_case_invoice_main_id = im.id
WHERE sd.loan_case_invoice_main_id IS NULL
  AND sd.loan_case_main_bill_id IS NOT NULL;

-- Step 4: Show results after update
SELECT 
    'Results after update' as info,
    COUNT(*) as total_records,
    COUNT(loan_case_invoice_main_id) as records_with_invoice_id,
    COUNT(loan_case_main_bill_id) as records_with_bill_id
FROM sst_details;

-- Step 5: Show sample data after update
SELECT 
    'Sample data after update' as info,
    sd.id,
    sd.sst_main_id,
    sd.loan_case_main_bill_id,
    sd.loan_case_invoice_main_id,
    sd.amount,
    im.invoice_no,
    im.Invoice_date,
    im.amount as invoice_amount
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.loan_case_invoice_main_id IS NOT NULL
LIMIT 5;

-- Step 6: Verify the relationship chain works
SELECT 
    'Verification - Relationship chain' as info,
    sd.id as sst_detail_id,
    sd.loan_case_main_bill_id,
    b.id as bill_id,
    b.case_id,
    im.id as invoice_id,
    im.invoice_no,
    l.case_ref_no,
    c.name as client_name
FROM sst_details sd
INNER JOIN loan_case_bill_main b ON b.id = sd.loan_case_main_bill_id
INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
INNER JOIN loan_case l ON l.id = b.case_id
INNER JOIN client c ON c.id = l.customer_id
WHERE sd.loan_case_invoice_main_id IS NOT NULL
LIMIT 5;
