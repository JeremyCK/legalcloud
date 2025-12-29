# Batch Processing Guide for Invoice Corrections

## Problem
Processing 79 invoices at once causes a timeout error (60 seconds exceeded).

## Solution
The `fixMultipleInvoices` function has been updated to:
1. **Increase timeout limit** to 5 minutes (300 seconds)
2. **Process in batches** of 20 invoices by default
3. **Add progress logging** to track progress
4. **Reset timeout** for each invoice to prevent mid-processing timeouts

## How to Use

### Option 1: Process All Invoices at Once (Recommended)
The updated function should now handle all 79 invoices without timeout. Simply run your correction tool with all invoice numbers.

### Option 2: Process in Smaller Batches (If Still Timing Out)
If you still encounter timeout issues, you can process invoices in smaller batches:

#### Batch 1: Issue 1 & 2 (7 invoices)
```
DP20000817,DP20000826,DP20000829,DP20000840,DP20000844,DP20000845,DP20000849
```

#### Batch 2: Issue 3 (3 invoices)
```
DP20000964,DP20000965,DP20000966
```

#### Batch 3: Issue 4 - Part 1 (15 invoices)
```
DP20000869,DP20000873,DP20000874,DP20000875,DP20000877,DP20000878,DP20000879,DP20000941,DP20000942,DP20000943,DP20000884,DP20000885,DP20000986,DP20000987,DP20000870
```

#### Batch 4: Issue 4 - Part 2 (16 invoices)
```
DP20000896,DP20000897,DP20000871,DP20000891,DP20000892,DP20000934,DP20000935,DP20000936,DP20000944,DP20000945,DP20000948,DP20000949,DP20000952,DP20000953,DP20000982,DP20000995,DP20000998
```

#### Batch 5: Issue 5 (2 invoices)
```
DP20001168,DP20001170
```

#### Batch 6: Issue 6 - Part 1 (20 invoices)
```
DP20001000,DP20001012,DP20001013,DP20001014,DP20001015,DP20001025,DP20001026,DP20001035,DP20001040,DP20001041,DP20001042,DP20001057,DP20001058,DP20001065,DP20001066,DP20001067,DP20001070,DP20001071,DP20001074,DP20001085
```

#### Batch 7: Issue 6 - Part 2 (16 invoices)
```
DP20001089,DP20001090,DP20001091,DP20001092,DP20001095,DP20001096,DP20001110,DP20001111,DP20001119,DP20001130,DP20001142,DP20001145,DP20001146,DP20001147,DP20001148,DP20001149,DP20001150,DP20001151,DP20001155,DP20001158,DP20001159,DP20001160
```

## Additional Configuration

If you need to adjust the batch size, you can pass a `batch_size` parameter:
- Default: 20 invoices per batch
- Smaller batches (10): Use `batch_size=10` if you have very complex invoices
- Larger batches (30): Use `batch_size=30` if your server can handle it

## Monitoring Progress

The function now logs progress to Laravel logs:
- Check `storage/logs/laravel.log` for progress updates
- Logs show: "Processing batch X of Y"
- Progress logged every 10 invoices

## Troubleshooting

### If Still Getting Timeout:
1. Reduce `batch_size` to 10 or even 5
2. Process invoices one issue at a time
3. Check server PHP configuration (`php.ini`):
   - `max_execution_time` should be at least 300
   - `memory_limit` should be at least 256M

### If Getting Memory Errors:
1. Reduce `batch_size` further
2. Process in even smaller batches
3. Increase `memory_limit` in `php.ini`

## Alternative: Command Line Script

If web interface continues to timeout, consider creating a Laravel Artisan command that can run from command line with unlimited execution time.




