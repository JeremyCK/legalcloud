<?php

/**
 * Check ALL invoices in transfer fee 502 for SST discrepancies
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

echo "=== Checking ALL Invoices in Transfer Fee 502 ===\n\n";

$transferFeeId = 502;

// Get all transfer fee details for this transfer fee
$transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->with('loanCaseInvoiceMain')
    ->get();

echo "Total invoices: " . $transferFeeDetails->count() . "\n\n";

$discrepancies = [];
$fixed = 0;

foreach ($transferFeeDetails as $tfd) {
    $invoice = $tfd->loanCaseInvoiceMain;
    
    if (!$invoice) {
        continue;
    }
    
    // Current values from invoice
    $invoiceSst = $invoice->sst_inv ?? 0;
    $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
    $invoiceTotalSst = $invoiceSst + $invoiceReimbSst;
    
    // Current values from transfer_fee_details
    $transferredSst = $tfd->sst_amount ?? 0;
    $transferredReimbSst = $tfd->reimbursement_sst_amount ?? 0;
    $transferredTotalSst = $transferredSst + $transferredReimbSst;
    
    $difference = abs($transferredTotalSst - $invoiceTotalSst);
    
    if ($difference > 0.01) {
        $discrepancies[] = [
            'invoice_no' => $invoice->invoice_no,
            'invoice_id' => $invoice->id,
            'tfd_id' => $tfd->id,
            'invoice_sst' => $invoiceSst,
            'invoice_reimb_sst' => $invoiceReimbSst,
            'invoice_total' => $invoiceTotalSst,
            'transferred_sst' => $transferredSst,
            'transferred_reimb_sst' => $transferredReimbSst,
            'transferred_total' => $transferredTotalSst,
            'difference' => $difference
        ];
        
        // Fix it
        $tfd->sst_amount = $invoiceSst;
        $tfd->reimbursement_sst_amount = $invoiceReimbSst;
        $tfd->save();
        $fixed++;
    }
}

if (count($discrepancies) > 0) {
    echo "⚠️  Found " . count($discrepancies) . " invoices with discrepancies:\n\n";
    
    foreach ($discrepancies as $disc) {
        echo "Invoice: {$disc['invoice_no']}\n";
        echo "  Invoice SST: " . number_format($disc['invoice_sst'], 2) . "\n";
        echo "  Invoice Reimb SST: " . number_format($disc['invoice_reimb_sst'], 2) . "\n";
        echo "  Expected Total: " . number_format($disc['invoice_total'], 2) . "\n";
        echo "  Transferred SST: " . number_format($disc['transferred_sst'], 2) . "\n";
        echo "  Transferred Reimb SST: " . number_format($disc['transferred_reimb_sst'], 2) . "\n";
        echo "  Displayed Total: " . number_format($disc['transferred_total'], 2) . "\n";
        echo "  Difference: " . number_format($disc['difference'], 2) . "\n";
        echo "  ✅ Fixed\n\n";
    }
    
    echo "Total fixed: {$fixed} invoices\n";
} else {
    echo "✅ No discrepancies found. All invoices match!\n";
}
