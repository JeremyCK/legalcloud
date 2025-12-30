<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillOriInvoiceSST extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:backfill-ori-sst 
                            {--dry-run : Run without making changes}
                            {--force : Force update even if ori_invoice_sst already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill ori_invoice_sst column based on ori_invoice_amt * sst_rate from loan_case_bill_main';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Starting ori_invoice_sst backfill process...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Check if ori_invoice_sst column exists
        $hasOriSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'loan_case_invoice_details' 
            AND COLUMN_NAME = 'ori_invoice_sst'");
        
        if (!isset($hasOriSstColumn[0]) || $hasOriSstColumn[0]->count == 0) {
            $this->error('ori_invoice_sst column does not exist in loan_case_invoice_details table!');
            $this->info('Please run the migration or SQL script to add the column first.');
            return 1;
        }

        // Get all invoice details that need ori_invoice_sst
        $query = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
            ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->select(
                'ild.id as detail_id',
                'ild.ori_invoice_amt',
                'ild.ori_invoice_sst',
                'bm.sst_rate',
                'ai.account_cat_id',
                'ai.name as account_name'
            )
            ->where('ild.status', '<>', 99)
            ->where('ild.ori_invoice_amt', '>', 0)
            ->whereNotNull('bm.sst_rate')
            ->where('bm.sst_rate', '>', 0)
            ->whereIn('ai.account_cat_id', [1, 4]); // Professional fees and Reimbursement

        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('ild.ori_invoice_sst')
                  ->orWhere('ild.ori_invoice_sst', '=', 0);
            });
        }

        $invoiceDetails = $query->get();

        $this->info("Found {$invoiceDetails->count()} invoice details to process");

        $updated = 0;
        $errors = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($invoiceDetails->count());
        $bar->start();

        foreach ($invoiceDetails as $detail) {
            try {
                // Calculate ori_invoice_sst = ori_invoice_amt * (sst_rate / 100)
                $sstRate = $detail->sst_rate ?? 8;
                $sstRaw = $detail->ori_invoice_amt * ($sstRate / 100);
                
                // Apply special rounding rule (round down if ends in 5)
                $sstString = number_format($sstRaw, 3, '.', '');
                if (substr($sstString, -1) == '5') {
                    $oriInvoiceSst = floor($sstRaw * 100) / 100; // Round down
                } else {
                    $oriInvoiceSst = round($sstRaw, 2); // Normal rounding
                }

                // Update the ori_invoice_sst value
                if (!$dryRun) {
                    DB::table('loan_case_invoice_details')
                        ->where('id', $detail->detail_id)
                        ->update(['ori_invoice_sst' => $oriInvoiceSst]);
                    
                    $updated++;
                } else {
                    $updated++; // Count for dry run
                }

            } catch (\Exception $e) {
                $this->error("\nError processing detail ID {$detail->detail_id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("=== Summary ===");
        $this->info("Total processed: {$invoiceDetails->count()}");
        $this->info("Updated: {$updated}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");

        if ($dryRun) {
            $this->warn("\nThis was a DRY RUN. No changes were made.");
            $this->info("Run without --dry-run to apply changes.");
        } else {
            $this->info("\nori_invoice_sst backfill completed successfully!");
        }

        return 0;
    }
}





