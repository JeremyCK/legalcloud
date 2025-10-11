<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixEinvoiceDetailsInvoiceNoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

        if (count($incorrectRecords) > 0) {
            $this->command->info("Found " . count($incorrectRecords) . " records with incorrect invoice_no:");
            
            foreach ($incorrectRecords as $record) {
                $this->command->line("ID: {$record->id}, E-Invoice Details: {$record->einvoice_details_invoice_no}, Actual: {$record->actual_invoice_no}");
            }

            // Fix the incorrect records
            $updated = DB::statement("
                UPDATE einvoice_details ed
                INNER JOIN loan_case_invoice_main im ON ed.loan_case_invoice_id = im.id
                SET ed.invoice_no = im.invoice_no
                WHERE ed.invoice_no != im.invoice_no
            ");

            $this->command->info("Updated " . $updated . " records with correct invoice numbers.");
        } else {
            $this->command->info("No incorrect invoice_no records found. All records are consistent.");
        }

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
            $this->command->info("✅ All invoice_no records are now consistent!");
        } else {
            $this->command->error("❌ Still found " . count($remainingIncorrect) . " incorrect records.");
        }
    }
}

