-- Script to add loan_case_invoice_main_id column to sst_details table
-- This is needed for SST v2 system to work with invoices instead of bills

-- Step 1: Add the loan_case_invoice_main_id column to sst_details table
ALTER TABLE `sst_details` 
ADD COLUMN `loan_case_invoice_main_id` BIGINT UNSIGNED NULL 
COMMENT 'Reference to loan_case_invoice_main table for SST v2' 
AFTER `loan_case_main_bill_id`;

-- Step 2: Add index for better performance
ALTER TABLE `sst_details` 
ADD INDEX `idx_sst_details_invoice_id` (`loan_case_invoice_main_id`);

-- Step 3: Add foreign key constraint (optional, for data integrity)
-- ALTER TABLE `sst_details` 
-- ADD CONSTRAINT `fk_sst_details_invoice` 
-- FOREIGN KEY (`loan_case_invoice_main_id`) 
-- REFERENCES `loan_case_invoice_main`(`id`) 
-- ON DELETE CASCADE ON UPDATE CASCADE;

-- Step 4: Verify the changes
DESCRIBE `sst_details`;

-- Step 5: Show sample of updated table structure
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'sst_details' 
AND TABLE_SCHEMA = DATABASE()
ORDER BY ORDINAL_POSITION;
