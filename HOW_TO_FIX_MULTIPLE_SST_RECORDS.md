# How to Fix Multiple SST Records

## Option 1: Fix One Record at a Time (Recommended)

**File:** `FIX_SST_AMOUNTS_FOR_SPECIFIC_RECORD.sql`

**Usage:**
1. Open the file
2. Change line 5: `SET @sst_record_id = 96;` to your SST record ID
3. Run the SQL script
4. Repeat for each SST record

**Example:**
```sql
-- Fix SST record 96
SET @sst_record_id = 96;
-- ... rest of script

-- Fix SST record 97
SET @sst_record_id = 97;
-- ... rest of script
```

## Option 2: Fix All Records at Once

**File:** `FIX_SST_AMOUNTS_FOR_ALL_RECORDS.sql`

**Usage:**
1. Open the file
2. For specific record: Set `SET @sst_main_id = 96;` (line 8)
3. For all records: Set `SET @sst_main_id = NULL;` (line 11)
4. Run the SQL script

**⚠️ WARNING:** Fixing all records at once will update ALL SST records in the database. Use with caution!

## Option 3: Fix Multiple Specific Records

**File:** `FIX_SST_AMOUNTS_FOR_ALL_RECORDS.sql`

**Usage:**
Modify the WHERE clause to include specific IDs:

```sql
-- Fix specific SST records (e.g., 96, 97, 98)
WHERE sd.sst_main_id IN (96, 97, 98)
```

## Which Option to Use?

- **Option 1 (Recommended):** Use when you want to fix records one by one and verify each fix
- **Option 2:** Use when you're confident all records need fixing and want to do it in one go
- **Option 3:** Use when you want to fix a specific list of records

## Quick Reference

### Fix Single Record:
```sql
SET @sst_record_id = 96;  -- Change this
-- Then run FIX_SST_AMOUNTS_FOR_SPECIFIC_RECORD.sql
```

### Fix All Records:
```sql
SET @sst_main_id = NULL;  -- This fixes all
-- Then run FIX_SST_AMOUNTS_FOR_ALL_RECORDS.sql
```

### Fix Multiple Specific Records:
```sql
-- Modify WHERE clause in FIX_SST_AMOUNTS_FOR_ALL_RECORDS.sql
WHERE sd.sst_main_id IN (96, 97, 98, 99)
```

## What Gets Fixed

1. **sst_details.amount** - Updated from `invoice.sst_inv`
2. **sst_main.amount** - Recalculated to include SST + reimbursement SST

## Verification

After running, check the verification query at the end of each script to confirm the fix worked.











