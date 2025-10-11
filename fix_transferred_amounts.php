<?php

/**
 * Script to fix existing transferred_pfee_amt data in the database
 * This script recalculates all existing transferred amounts to fix the incorrect calculations
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\LoanCaseInvoiceMain;
use App\Models\TransferFeeDetails;

try {
    echo "Starting to fix transferred amounts...\n";
    
    // Get all invoices that have transferred amounts
    $invoices = LoanCaseInvoiceMain::where('transferred_pfee_amt', '>', 0)->get();
    
    $fixedCount = 0;
    
    foreach ($invoices as $invoice) {
        // Get all transfer fee details for this invoice
        $transferDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)->get();
        
        if ($transferDetails->count() > 0) {
            // Calculate correct transferred amounts
            $correctTransferredPfee = 0;
            $correctTransferredSst = 0;
            $correctTransferredReimbursement = 0;
            $correctTransferredReimbursementSst = 0;
            
                    foreach ($transferDetails as $detail) {
                        // transfer_amount now contains only the professional fee amount
                        $correctTransferredPfee += $detail->transfer_amount;
                $correctTransferredSst += $detail->sst_amount ?? 0;
                $correctTransferredReimbursement += $detail->reimbursement_amount ?? 0;
                $correctTransferredReimbursementSst += $detail->reimbursement_sst_amount ?? 0;
            }
            
            // Show before and after values
            echo "Invoice {$invoice->id} ({$invoice->invoice_no}):\n";
            echo "  Before - Transferred Pfee: {$invoice->transferred_pfee_amt}, Transferred SST: {$invoice->transferred_sst_amt}\n";
            echo "  After  - Transferred Pfee: {$correctTransferredPfee}, Transferred SST: {$correctTransferredSst}\n";
            
            // Update the invoice with correct amounts
            $invoice->transferred_pfee_amt = $correctTransferredPfee;
            $invoice->transferred_sst_amt = $correctTransferredSst;
            $invoice->transferred_reimbursement_amt = $correctTransferredReimbursement;
            $invoice->transferred_reimbursement_sst_amt = $correctTransferredReimbursementSst;
            $invoice->save();
            
            $fixedCount++;
        }
    }
    
    echo "\nSuccessfully fixed transferred amounts for {$fixedCount} invoices\n";
    
} catch (Exception $e) {
    echo "Error fixing transferred amounts: " . $e->getMessage() . "\n";
}
