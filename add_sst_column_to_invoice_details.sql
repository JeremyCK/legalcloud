-- Add sst column to loan_case_invoice_details table
-- This allows users to manually set SST values without auto-calculation

-- Check if column already exists before adding
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'loan_case_invoice_details'
    AND COLUMN_NAME = 'sst'
);

-- Add column if it doesn't exist
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `loan_case_invoice_details` 
     ADD COLUMN `sst` DECIMAL(20,2) NULL 
     COMMENT ''Custom SST amount (if manually set, otherwise NULL to auto-calculate)'' 
     AFTER `amount`',
    'SELECT ''Column sst already exists'' AS message'
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
AND COLUMN_NAME = 'sst';

