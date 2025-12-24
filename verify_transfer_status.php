<?php
/**
 * Verify Transfer Status - Check "to Transfer" amounts
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 472;

echo "========================================\n";
echo "VERIFY TRANSFER STATUS\n";
echo "========================================\n\n";

// Get all invoices in this transfer fee
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique();

$invoices = LoanCaseInvoiceMain::whereIn('id', $details)
    ->where('status', '<>', 99)
    ->get();

echo "Total Invoices: " . $invoices->count() . "\n\n";

$issues = [];
$totalPfeeToTransfer = 0;
$totalSstToTransfer = 0;
$totalReimbToTransfer = 0;
$totalReimbSstToTransfer = 0;

foreach ($invoices as $invoice) {
    // Get transferred amounts from transfer_fee_details
    $transferred = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('transfer_fee_main_id', $transferFeeId)
        ->where('status', '<>', 99)
        ->select(
            DB::raw('SUM(transfer_amount) as pfee'),
            DB::raw('SUM(sst_amount) as sst'),
            DB::raw('SUM(reimbursement_amount) as reimb'),
            DB::raw('SUM(reimbursement_sst_amount) as reimb_sst')
        )
        ->first();
    
    $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $invoiceSst = $invoice->sst_inv ?? 0;
    $invoiceReimb = $invoice->reimbursement_amount ?? 0;
    $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
    
    $transferredPfee = $transferred->pfee ?? 0;
    $transferredSst = $transferred->sst ?? 0;
    $transferredReimb = $transferred->reimb ?? 0;
    $transferredReimbSst = $transferred->reimb_sst ?? 0;
    
    $pfeeToTransfer = $invoicePfee - $transferredPfee;
    $sstToTransfer = $invoiceSst - $transferredSst;
    $reimbToTransfer = $invoiceReimb - $transferredReimb;
    $reimbSstToTransfer = $invoiceReimbSst - $transferredReimbSst;
    
    $totalPfeeToTransfer += $pfeeToTransfer;
    $totalSstToTransfer += $sstToTransfer;
    $totalReimbToTransfer += $reimbToTransfer;
    $totalReimbSstToTransfer += $reimbSstToTransfer;
    
    if (abs($pfeeToTransfer) > 0.01 || abs($sstToTransfer) > 0.01 || 
        abs($reimbToTransfer) > 0.01 || abs($reimbSstToTransfer) > 0.01) {
        $issues[] = [
            'invoice' => $invoice->invoice_no,
            'pfee_to_transfer' => $pfeeToTransfer,
            'sst_to_transfer' => $sstToTransfer,
            'reimb_to_transfer' => $reimbToTransfer,
            'reimb_sst_to_transfer' => $reimbSstToTransfer
        ];
    }
}

echo "========================================\n";
echo "TOTAL 'TO TRANSFER' AMOUNTS\n";
echo "========================================\n";
echo "Professional Fee to Transfer: " . number_format($totalPfeeToTransfer, 2) . "\n";
echo "SST to Transfer:              " . number_format($totalSstToTransfer, 2) . "\n";
echo "Reimbursement to Transfer:    " . number_format($totalReimbToTransfer, 2) . "\n";
echo "Reimbursement SST to Transfer: " . number_format($totalReimbSstToTransfer, 2) . "\n\n";

if (count($issues) > 0) {
    echo "========================================\n";
    echo "INVOICES WITH 'TO TRANSFER' > 0.01\n";
    echo "========================================\n";
    echo "Total: " . count($issues) . " invoices\n\n";
    
    foreach (array_slice($issues, 0, 20) as $issue) {
        echo "{$issue['invoice']}: Pfee={$issue['pfee_to_transfer']}, SST={$issue['sst_to_transfer']}, Reimb={$issue['reimb_to_transfer']}, ReimbSST={$issue['reimb_sst_to_transfer']}\n";
    }
    
    if (count($issues) > 20) {
        echo "... and " . (count($issues) - 20) . " more\n";
    }
} else {
    echo "✅ All 'to Transfer' amounts are 0.00 (or < 0.01)!\n";
}



