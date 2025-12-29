-- =====================================================
-- SQL Script: Add ori_invoice_sst Column
-- =====================================================
-- This script adds the ori_invoice_sst column to 
-- loan_case_invoice_details table
-- Similar to ori_invoice_amt, this stores the original
-- SST total across all split invoices
-- =====================================================

-- Check if column already exists
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'loan_case_invoice_details'
    AND COLUMN_NAME = 'ori_invoice_sst'
);

-- Add column if it doesn't exist
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `loan_case_invoice_details` 
     ADD COLUMN `ori_invoice_sst` DECIMAL(20,2) NULL 
     COMMENT ''Original invoice SST total across all split invoices for this account_item_id'' 
     AFTER `ori_invoice_amt`',
    'SELECT ''Column ori_invoice_sst already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify the column was added
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'loan_case_invoice_details'
AND COLUMN_NAME = 'ori_invoice_sst';




