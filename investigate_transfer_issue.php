<?php
/**
 * Investigate Transfer Fee Issues
 * 1. Why "to Transfer" columns show non-zero values
 * 2. Why totals don't match expected values
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
echo "INVESTIGATE TRANSFER FEE ISSUES\n";
echo "========================================\n\n";

// Get all invoices in transfer fee
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique();

$invoices = LoanCaseInvoiceMain::whereIn('id', $details)
    ->where('status', '<>', 99)
    ->get();

echo "Total Invoices: " . $invoices->count() . "\n\n";

// Calculate totals from invoices
$totalInvoicePfee = 0;
$totalInvoiceSst = 0;
$totalInvoiceReimb = 0;
$totalInvoiceReimbSst = 0;

// Calculate totals from transfer_fee_details
$totalTransferredPfee = 0;
$totalTransferredSst = 0;
$totalTransferredReimb = 0;
$totalTransferredReimbSst = 0;

// Track "to Transfer" amounts
$totalPfeeToTransfer = 0;
$totalSstToTransfer = 0;
$totalReimbToTransfer = 0;
$totalReimbSstToTransfer = 0;

$issues = [];

foreach ($invoices as $invoice) {
    $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $invoiceSst = $invoice->sst_inv ?? 0;
    $invoiceReimb = $invoice->reimbursement_amount ?? 0;
    $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
    
    $totalInvoicePfee += $invoicePfee;
    $totalInvoiceSst += $invoiceSst;
    $totalInvoiceReimb += $invoiceReimb;
    $totalInvoiceReimbSst += $invoiceReimbSst;
    
    // Get transferred amounts from transfer_fee_details for THIS transfer fee
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
    
    $transferredPfee = $transferred->pfee ?? 0;
    $transferredSst = $transferred->sst ?? 0;
    $transferredReimb = $transferred->reimb ?? 0;
    $transferredReimbSst = $transferred->reimb_sst ?? 0;
    
    $totalTransferredPfee += $transferredPfee;
    $totalTransferredSst += $transferredSst;
    $totalTransferredReimb += $transferredReimb;
    $totalTransferredReimbSst += $transferredReimbSst;
    
    // Calculate "to Transfer" amounts
    $pfeeToTransfer = $invoicePfee - $transferredPfee;
    $sstToTransfer = $invoiceSst - $transferredSst;
    $reimbToTransfer = $invoiceReimb - $transferredReimb;
    $reimbSstToTransfer = $invoiceReimbSst - $transferredReimbSst;
    
    $totalPfeeToTransfer += $pfeeToTransfer;
    $totalSstToTransfer += $sstToTransfer;
    $totalReimbToTransfer += $reimbToTransfer;
    $totalReimbSstToTransfer += $reimbSstToTransfer;
    
    if (abs($pfeeToTransfer) > 0.001 || abs($sstToTransfer) > 0.001 || 
        abs($reimbToTransfer) > 0.001 || abs($reimbSstToTransfer) > 0.001) {
        $issues[] = [
            'invoice' => $invoice->invoice_no,
            'invoice_pfee' => $invoicePfee,
            'transferred_pfee' => $transferredPfee,
            'pfee_to_transfer' => $pfeeToTransfer,
            'invoice_sst' => $invoiceSst,
            'transferred_sst' => $transferredSst,
            'sst_to_transfer' => $sstToTransfer,
            'invoice_reimb' => $invoiceReimb,
            'transferred_reimb' => $transferredReimb,
            'reimb_to_transfer' => $reimbToTransfer,
            'invoice_reimb_sst' => $invoiceReimbSst,
            'transferred_reimb_sst' => $transferredReimbSst,
            'reimb_sst_to_transfer' => $reimbSstToTransfer
        ];
    }
}

echo "========================================\n";
echo "TOTALS FROM INVOICES\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($totalInvoicePfee, 2) . "\n";
echo "SST:                  " . number_format($totalInvoiceSst, 2) . "\n";
echo "Reimbursement:        " . number_format($totalInvoiceReimb, 2) . "\n";
echo "Reimbursement SST:    " . number_format($totalInvoiceReimbSst, 2) . "\n\n";

echo "========================================\n";
echo "TOTALS FROM TRANSFER FEE DETAILS\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($totalTransferredPfee, 2) . "\n";
echo "SST:                  " . number_format($totalTransferredSst, 2) . "\n";
echo "Reimbursement:        " . number_format($totalTransferredReimb, 2) . "\n";
echo "Reimbursement SST:    " . number_format($totalTransferredReimbSst, 2) . "\n\n";

echo "========================================\n";
echo "TOTAL 'TO TRANSFER' AMOUNTS\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($totalPfeeToTransfer, 2) . "\n";
echo "SST:                  " . number_format($totalSstToTransfer, 2) . "\n";
echo "Reimbursement:        " . number_format($totalReimbToTransfer, 2) . "\n";
echo "Reimbursement SST:    " . number_format($totalReimbSstToTransfer, 2) . "\n\n";

echo "========================================\n";
echo "EXPECTED VALUES\n";
echo "========================================\n";
echo "Professional Fee:     521,831.74\n";
echo "SST:                  41,746.47\n";
echo "Reimbursement:        66,373.63\n";
echo "Reimbursement SST:    5,309.91\n\n";

echo "========================================\n";
echo "DIFFERENCES (Current vs Expected)\n";
echo "========================================\n";
$pfeeDiff = $totalInvoicePfee - 521831.74;
$sstDiff = $totalInvoiceSst - 41746.47;
$reimbDiff = $totalInvoiceReimb - 66373.63;
$reimbSstDiff = $totalInvoiceReimbSst - 5309.91;

echo "Professional Fee:     " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "SST:                  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "Reimbursement:        " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "Reimbursement SST:    " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

if (count($issues) > 0) {
    echo "========================================\n";
    echo "INVOICES WITH 'TO TRANSFER' ISSUES\n";
    echo "========================================\n";
    echo "Total: " . count($issues) . " invoices\n\n";
    
    foreach (array_slice($issues, 0, 10) as $issue) {
        echo "{$issue['invoice']}:\n";
        echo "  Pfee: Invoice={$issue['invoice_pfee']}, Transferred={$issue['transferred_pfee']}, ToTransfer={$issue['pfee_to_transfer']}\n";
        echo "  SST: Invoice={$issue['invoice_sst']}, Transferred={$issue['transferred_sst']}, ToTransfer={$issue['sst_to_transfer']}\n";
        echo "  Reimb: Invoice={$issue['invoice_reimb']}, Transferred={$issue['transferred_reimb']}, ToTransfer={$issue['reimb_to_transfer']}\n";
        echo "  ReimbSST: Invoice={$issue['invoice_reimb_sst']}, Transferred={$issue['transferred_reimb_sst']}, ToTransfer={$issue['reimb_sst_to_transfer']}\n\n";
    }
    
    if (count($issues) > 10) {
        echo "... and " . (count($issues) - 10) . " more\n";
    }
}




