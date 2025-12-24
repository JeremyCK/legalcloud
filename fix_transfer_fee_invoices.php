<?php
/**
 * Fix all invoices in a transfer fee that have rounding differences
 * Run: php fix_transfer_fee_invoices.php [transfer_fee_id]
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;

// Get transfer fee ID
$transferFeeId = $argv[1] ?? 472;

$transferFee = TransferFeeMain::find($transferFeeId);
if (!$transferFee) {
    echo "Transfer Fee ID {$transferFeeId} not found\n";
    exit(1);
}

echo "========================================\n";
echo "FIX INVOICES IN TRANSFER FEE\n";
echo "========================================\n\n";
echo "Transfer Fee ID: {$transferFeeId}\n";
if (isset($transferFee->transaction_id)) {
    echo "Transaction ID: {$transferFee->transaction_id}\n";
}
echo "\n";

// Get all invoice IDs from transfer fee details
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique()
    ->filter();

$invoiceIds = $details->toArray();
echo "Total Invoices: " . count($invoiceIds) . "\n\n";

// Use InvoiceFixController to fix each invoice
$controller = new \App\Http\Controllers\InvoiceFixController();

$fixedCount = 0;
$issueCount = 0;

foreach ($invoiceIds as $invoiceId) {
    try {
        $result = $controller->fixInvoice($invoiceId);
        
        if ($result['success']) {
            $fixedCount++;
            $invoice = LoanCaseInvoiceMain::find($invoiceId);
            if ($invoice) {
                echo "✅ Fixed: {$invoice->invoice_no}\n";
            }
        } else {
            $issueCount++;
            $invoice = LoanCaseInvoiceMain::find($invoiceId);
            if ($invoice) {
                echo "⚠️  Issue with {$invoice->invoice_no}: {$result['message']}\n";
            }
        }
    } catch (\Exception $e) {
        $issueCount++;
        echo "❌ Error fixing invoice {$invoiceId}: " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "Fixed: {$fixedCount}\n";
echo "Issues: {$issueCount}\n\n";

echo "Now recalculating transfer fee details from fixed invoices...\n\n";

// Now recalculate transfer fee details
require __DIR__ . '/fix_transfer_fee_details_from_invoices.php';



