<?php
/**
 * Script to recalculate transferred_reimbursement_amt and transferred_reimbursement_sst_amt
 * 
 * Usage:
 * 1. Via Artisan Tinker: php artisan tinker < recalculate_reimbursement_amounts.php
 * 2. Or run directly: php recalculate_reimbursement_amounts.php (requires Laravel bootstrap)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\LoanCaseInvoiceMain;
use App\Models\TransferFeeDetails;

// Option 1: Update for a specific invoice
$invoiceNo = '20002388'; // Change this to the invoice number you want to update

echo "Recalculating transferred reimbursement amounts for invoice: {$invoiceNo}\n";
echo str_repeat('=', 60) . "\n";

$invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();

if (!$invoice) {
    echo "ERROR: Invoice {$invoiceNo} not found!\n";
    exit(1);
}

echo "Invoice ID: {$invoice->id}\n";
echo "Current transferred_reimbursement_amt: {$invoice->transferred_reimbursement_amt}\n";
echo "Current transferred_reimbursement_sst_amt: {$invoice->transferred_reimbursement_sst_amt}\n";
echo "\n";

// Get all transfer fee details for this invoice
$transferDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
    ->where('status', '<>', 99)
    ->get();

echo "Found " . $transferDetails->count() . " transfer fee detail(s)\n\n";

// Calculate totals
$totalTransferredReimbursement = 0;
$totalTransferredReimbursementSst = 0;

foreach ($transferDetails as $detail) {
    $reimbursement = $detail->reimbursement_amount ?? 0;
    $reimbursementSst = $detail->reimbursement_sst_amount ?? 0;
    
    $totalTransferredReimbursement += $reimbursement;
    $totalTransferredReimbursementSst += $reimbursementSst;
    
    echo "Transfer Fee Detail ID {$detail->id}: Reimbursement = {$reimbursement}, SST = {$reimbursementSst}\n";
}

echo "\n";
echo "Calculated transferred_reimbursement_amt: {$totalTransferredReimbursement}\n";
echo "Calculated transferred_reimbursement_sst_amt: {$totalTransferredReimbursementSst}\n";
echo "\n";

// Update the invoice
$invoice->transferred_reimbursement_amt = $totalTransferredReimbursement;
$invoice->transferred_reimbursement_sst_amt = $totalTransferredReimbursementSst;
$invoice->save();

echo "✓ Successfully updated invoice {$invoiceNo}\n";
echo "\n";
echo "New transferred_reimbursement_amt: {$invoice->transferred_reimbursement_amt}\n";
echo "New transferred_reimbursement_sst_amt: {$invoice->transferred_reimbursement_sst_amt}\n";

// Option 2: Uncomment below to update ALL invoices
/*
echo "\n\n";
echo "Updating ALL invoices...\n";
echo str_repeat('=', 60) . "\n";

$invoices = LoanCaseInvoiceMain::where('transferred_pfee_amt', '>', 0)->get();
$fixedCount = 0;

foreach ($invoices as $invoice) {
    $transferDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('status', '<>', 99)
        ->get();
    
    if ($transferDetails->count() > 0) {
        $totalTransferredReimbursement = 0;
        $totalTransferredReimbursementSst = 0;
        
        foreach ($transferDetails as $detail) {
            $totalTransferredReimbursement += $detail->reimbursement_amount ?? 0;
            $totalTransferredReimbursementSst += $detail->reimbursement_sst_amount ?? 0;
        }
        
        $invoice->transferred_reimbursement_amt = $totalTransferredReimbursement;
        $invoice->transferred_reimbursement_sst_amt = $totalTransferredReimbursementSst;
        $invoice->save();
        
        $fixedCount++;
    }
}

echo "✓ Successfully updated {$fixedCount} invoices\n";
*/

