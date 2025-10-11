<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBlnSstColumnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sst:add-bln-sst-column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add bln_sst column to loan_case_invoice_main table and update it based on matching invoice numbers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting bln_sst column addition and update process...');

        try {
            // Step 1: Add the bln_sst column to loan_case_invoice_main table
            $this->info('Step 1: Adding bln_sst column to loan_case_invoice_main table...');
            
            if (!Schema::hasColumn('loan_case_invoice_main', 'bln_sst')) {
                DB::statement("ALTER TABLE `loan_case_invoice_main` 
                    ADD COLUMN `bln_sst` TINYINT(1) NOT NULL DEFAULT 0 
                    COMMENT 'SST transfer flag: 0=not transferred, 1=transferred' 
                    AFTER `transferred_sst_amt`");
                $this->info('✅ Column bln_sst added successfully.');
            } else {
                $this->warn('⚠️  Column bln_sst already exists.');
            }

            // Step 2: Update bln_sst in loan_case_invoice_main based on matching invoice numbers
            $this->info('Step 2: Updating bln_sst based on matching invoice numbers...');
            
            $updatedCount = DB::update("
                UPDATE `loan_case_invoice_main` im
                INNER JOIN `loan_case_bill_main` bm ON im.invoice_no = bm.invoice_no
                SET im.bln_sst = 1
                WHERE bm.bln_sst = 1 
                AND im.status <> 99 
                AND bm.status <> 99
            ");
            
            $this->info("✅ Updated {$updatedCount} records based on matching invoice numbers.");

            // Step 3: Update bln_sst in loan_case_invoice_main based on existing SST transfers
            $this->info('Step 3: Updating bln_sst based on existing transferred_sst_amt...');
            
            $updatedCount2 = DB::update("
                UPDATE `loan_case_invoice_main` 
                SET `bln_sst` = 1 
                WHERE `transferred_sst_amt` > 0 
                AND `status` <> 99
            ");
            
            $this->info("✅ Updated {$updatedCount2} records based on existing transferred_sst_amt.");

            // Step 4: Get statistics
            $this->info('Step 4: Getting statistics...');
            
            $billStats = DB::select("
                SELECT 
                    COUNT(*) as total_records,
                    SUM(CASE WHEN bln_sst = 1 THEN 1 ELSE 0 END) as transferred_count,
                    SUM(CASE WHEN bln_sst = 0 THEN 1 ELSE 0 END) as not_transferred_count
                FROM `loan_case_bill_main` 
                WHERE status <> 99
            ")[0];
            
            $invoiceStats = DB::select("
                SELECT 
                    COUNT(*) as total_records,
                    SUM(CASE WHEN bln_sst = 1 THEN 1 ELSE 0 END) as transferred_count,
                    SUM(CASE WHEN bln_sst = 0 THEN 1 ELSE 0 END) as not_transferred_count
                FROM `loan_case_invoice_main` 
                WHERE status <> 99
            ")[0];
            
            $this->info("\n📊 Statistics:");
            $this->info("loan_case_bill_main: {$billStats->total_records} total, {$billStats->transferred_count} transferred, {$billStats->not_transferred_count} not transferred");
            $this->info("loan_case_invoice_main: {$invoiceStats->total_records} total, {$invoiceStats->transferred_count} transferred, {$invoiceStats->not_transferred_count} not transferred");

            // Step 5: Show sample of updated records
            $this->info("\nStep 5: Sample of updated records:");
            
            $sampleRecords = DB::select("
                SELECT 
                    im.id,
                    im.invoice_no,
                    im.bln_sst,
                    im.transferred_sst_amt,
                    bm.id as bill_id,
                    bm.bln_sst as bill_bln_sst
                FROM `loan_case_invoice_main` im
                LEFT JOIN `loan_case_bill_main` bm ON im.invoice_no = bm.invoice_no
                WHERE im.status <> 99
                ORDER BY im.id DESC
                LIMIT 10
            ");
            
            $headers = ['ID', 'Invoice No', 'Invoice bln_sst', 'Transferred SST', 'Bill ID', 'Bill bln_sst'];
            $rows = [];
            
            foreach ($sampleRecords as $record) {
                $rows[] = [
                    $record->id,
                    $record->invoice_no,
                    $record->bln_sst,
                    $record->transferred_sst_amt,
                    $record->bill_id,
                    $record->bill_bln_sst
                ];
            }
            
            $this->table($headers, $rows);

            $this->info("\n✅ Script completed successfully!");
            $this->info("The bln_sst column has been added and updated in loan_case_invoice_main table.");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Please check the error and run the script again.");
            return 1;
        }
    }
}
