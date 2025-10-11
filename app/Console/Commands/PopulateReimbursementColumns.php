<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateReimbursementColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:reimbursement-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate reimbursement_amount and reimbursement_sst columns in loan_case_invoice_main table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to populate reimbursement columns...');

        // Get all invoices
        $invoices = DB::table('loan_case_invoice_main')->get();
        $totalInvoices = count($invoices);
        $this->info("Found {$totalInvoices} invoices to process");

        $bar = $this->output->createProgressBar($totalInvoices);
        $bar->start();

        foreach ($invoices as $invoice) {
            // Calculate reimbursement amounts for this invoice
            $reimbursementDetails = DB::table('loan_case_invoice_details as lcid')
                ->join('account_item as ai', 'ai.id', '=', 'lcid.account_item_id')
                ->join('loan_case_bill_main as b', 'b.id', '=', 'lcid.loan_case_main_bill_id')
                ->where('lcid.invoice_main_id', $invoice->id)
                ->where('ai.account_cat_id', 4) // Reimbursement category
                ->where('lcid.status', 1) // Active records only
                ->select('lcid.amount', 'b.sst_rate')
                ->get();

            $reimbursementAmount = 0;
            $reimbursementSst = 0;

            foreach ($reimbursementDetails as $detail) {
                $reimbursementAmount += $detail->amount ?? 0;
                // Calculate SST based on the bill's SST rate
                $sstRate = ($detail->sst_rate ?? 6) * 0.01; // Default to 6% if not set
                $reimbursementSst += ($detail->amount ?? 0) * $sstRate;
            }

            // Update the invoice record
            DB::table('loan_case_invoice_main')
                ->where('id', $invoice->id)
                ->update([
                    'reimbursement_amount' => $reimbursementAmount,
                    'reimbursement_sst' => $reimbursementSst
                ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Reimbursement columns populated successfully!');
    }
}
