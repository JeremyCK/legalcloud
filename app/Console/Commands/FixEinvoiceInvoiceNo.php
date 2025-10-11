<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixEinvoiceInvoiceNo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:fix-invoice-no {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix incorrect invoice_no values in einvoice_details table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // First, let's see what records have incorrect invoice_no
        $incorrectRecords = DB::select("
            SELECT 
                ed.id,
                ed.invoice_no as einvoice_details_invoice_no,
                im.invoice_no as actual_invoice_no,
                ed.einvoice_main_id
            FROM einvoice_details ed
            INNER JOIN loan_case_invoice_main im ON ed.loan_case_invoice_id = im.id
            WHERE ed.invoice_no != im.invoice_no
        ");

        if (count($incorrectRecords) == 0) {
            $this->info("✅ No incorrect invoice_no records found. All records are consistent.");
            return 0;
        }

        $this->info("Found " . count($incorrectRecords) . " records with incorrect invoice_no:");
        
        $table = $this->table(['ID', 'E-Invoice Details Invoice No', 'Actual Invoice No', 'E-Invoice Main ID'], 
            collect($incorrectRecords)->map(function($record) {
                return [
                    $record->id,
                    $record->einvoice_details_invoice_no,
                    $record->actual_invoice_no,
                    $record->einvoice_main_id
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->warn("🔍 DRY RUN: No changes were made. Use without --dry-run to apply changes.");
            return 0;
        }

        if (!$this->confirm("Do you want to fix these " . count($incorrectRecords) . " records?")) {
            $this->info("Operation cancelled.");
            return 0;
        }

        // Fix the incorrect records
        $updated = DB::statement("
            UPDATE einvoice_details ed
            INNER JOIN loan_case_invoice_main im ON ed.loan_case_invoice_id = im.id
            SET ed.invoice_no = im.invoice_no
            WHERE ed.invoice_no != im.invoice_no
        ");

        $this->info("✅ Updated " . $updated . " records with correct invoice numbers.");

        // Verify the fix
        $remainingIncorrect = DB::select("
            SELECT 
                ed.id,
                ed.invoice_no as einvoice_details_invoice_no,
                im.invoice_no as actual_invoice_no
            FROM einvoice_details ed
            INNER JOIN loan_case_invoice_main im ON ed.loan_case_invoice_id = im.id
            WHERE ed.invoice_no != im.invoice_no
        ");

        if (count($remainingIncorrect) == 0) {
            $this->info("✅ All invoice_no records are now consistent!");
        } else {
            $this->error("❌ Still found " . count($remainingIncorrect) . " incorrect records.");
            return 1;
        }

        return 0;
    }
}

