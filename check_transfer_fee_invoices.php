<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\LoanCaseInvoiceMain;
use App\Models\TransferFeeDetails;

$invoiceNos = ['DP20000817', 'DP20000816'];
$transferFeeMainId = 447;

echo "========================================\n";
echo "CHECKING TRANSFER FEE INVOICES\n";
echo "========================================\n\n";

foreach ($invoiceNos as $invoiceNo) {
    $invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();
    
    if (!$invoice) {
        echo "{$invoiceNo}: Invoice not found\n\n";
        continue;
    }
    
    echo "{$invoiceNo} - Invoice ID: {$invoice->id}\n";
    echo "  Invoice Amounts:\n";
    echo "    pfee: " . number_format(($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0), 2) . "\n";
    echo "    reimbursement: " . number_format($invoice->reimbursement_amount ?? 0, 2) . "\n";
    echo "    reimbursement_sst: " . number_format($invoice->reimbursement_sst ?? 0, 2) . "\n";
    echo "\n";
    echo "  Transferred Amounts (from invoice table):\n";
    echo "    transferred_pfee_amt: " . number_format($invoice->transferred_pfee_amt ?? 0, 2) . "\n";
    echo "    transferred_reimbursement_amt: " . number_format($invoice->transferred_reimbursement_amt ?? 0, 2) . "\n";
    echo "\n";
    
    // Check transfer fee details for this transfer fee main
    $tfd = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('transfer_fee_main_id', $transferFeeMainId)
        ->where('status', '<>', 99)
        ->first();
    
    if ($tfd) {
        echo "  Transfer Fee Detail (Transfer Fee Main ID: {$transferFeeMainId}):\n";
        echo "    transfer_amount: " . number_format($tfd->transfer_amount ?? 0, 2) . "\n";
        echo "    reimbursement_amount: " . number_format($tfd->reimbursement_amount ?? 0, 2) . "\n";
        echo "    Transferred Bal (transfer_amount + reimbursement_amount): " . number_format(($tfd->transfer_amount ?? 0) + ($tfd->reimbursement_amount ?? 0), 2) . "\n";
    } else {
        echo "  Transfer Fee Detail: NOT FOUND for transfer_fee_main_id {$transferFeeMainId}\n";
    }
    
    // Check ALL transfer fee details for this invoice
    $allTfd = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('status', '<>', 99)
        ->get();
    
    if ($allTfd->count() > 0) {
        echo "\n  All Transfer Fee Details for this invoice:\n";
        foreach ($allTfd as $detail) {
            echo "    Transfer Fee Main ID: {$detail->transfer_fee_main_id}\n";
            echo "      transfer_amount: " . number_format($detail->transfer_amount ?? 0, 2) . "\n";
            echo "      reimbursement_amount: " . number_format($detail->reimbursement_amount ?? 0, 2) . "\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

