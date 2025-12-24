<?php
/**
 * Fix Invoice Amounts to Match Transferred Amounts
 * This ensures "to Transfer" columns are 0.00
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
echo "FIX INVOICE AMOUNTS TO MATCH TRANSFERRED\n";
echo "========================================\n\n";

// Get invoice DP20000896
$invoice = LoanCaseInvoiceMain::where('invoice_no', 'DP20000896')->first();

if (!$invoice) {
    echo "Invoice DP20000896 not found\n";
    exit(1);
}

echo "Invoice: {$invoice->invoice_no}\n";
echo "Current Invoice Amounts:\n";
echo "  Pfee: " . (($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0)) . "\n";
echo "  SST: " . ($invoice->sst_inv ?? 0) . "\n";
echo "  Reimb: " . ($invoice->reimbursement_amount ?? 0) . "\n";
echo "  ReimbSST: " . ($invoice->reimbursement_sst ?? 0) . "\n\n";

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

$transferredPfee = $transferred->pfee ?? 0;
$transferredSst = $transferred->sst ?? 0;
$transferredReimb = $transferred->reimb ?? 0;
$transferredReimbSst = $transferred->reimb_sst ?? 0;

echo "Transferred Amounts:\n";
echo "  Pfee: " . $transferredPfee . "\n";
echo "  SST: " . $transferredSst . "\n";
echo "  Reimb: " . $transferredReimb . "\n";
echo "  ReimbSST: " . $transferredReimbSst . "\n\n";

$currentPfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
$pfeeDiff = $transferredPfee - $currentPfee;
$sstDiff = $transferredSst - ($invoice->sst_inv ?? 0);
$reimbDiff = $transferredReimb - ($invoice->reimbursement_amount ?? 0);
$reimbSstDiff = $transferredReimbSst - ($invoice->reimbursement_sst ?? 0);

echo "Differences:\n";
echo "  Pfee: " . ($pfeeDiff >= 0 ? '+' : '') . $pfeeDiff . "\n";
echo "  SST: " . ($sstDiff >= 0 ? '+' : '') . $sstDiff . "\n";
echo "  Reimb: " . ($reimbDiff >= 0 ? '+' : '') . $reimbDiff . "\n";
echo "  ReimbSST: " . ($reimbSstDiff >= 0 ? '+' : '') . $reimbSstDiff . "\n\n";

// Adjust invoice amounts to match transferred amounts
// Distribute the difference between pfee1 and pfee2 proportionally
$currentPfee1 = $invoice->pfee1_inv ?? 0;
$currentPfee2 = $invoice->pfee2_inv ?? 0;
$currentTotalPfee = $currentPfee1 + $currentPfee2;

DB::beginTransaction();

try {
    if ($currentTotalPfee > 0) {
        // Distribute proportionally
        $pfee1Ratio = $currentPfee1 / $currentTotalPfee;
        $pfee2Ratio = $currentPfee2 / $currentTotalPfee;
        
        $invoice->pfee1_inv = round($transferredPfee * $pfee1Ratio, 2);
        $invoice->pfee2_inv = round($transferredPfee * $pfee2Ratio, 2);
    } else {
        // If no current pfee, put all in pfee2
        $invoice->pfee1_inv = 0;
        $invoice->pfee2_inv = round($transferredPfee, 2);
    }
    
    $invoice->sst_inv = round($transferredSst, 2);
    $invoice->reimbursement_amount = round($transferredReimb, 2);
    $invoice->reimbursement_sst = round($transferredReimbSst, 2);
    
    // Recalculate total amount
    $invoice->amount = round($invoice->pfee1_inv + $invoice->pfee2_inv + 
                            $invoice->sst_inv + $invoice->reimbursement_amount + 
                            $invoice->reimbursement_sst, 2);
    
    // Update transferred amounts to match
    $invoice->transferred_pfee_amt = round($transferredPfee, 2);
    $invoice->transferred_sst_amt = round($transferredSst, 2);
    $invoice->transferred_reimbursement_amt = round($transferredReimb, 2);
    $invoice->transferred_reimbursement_sst_amt = round($transferredReimbSst, 2);
    
    $invoice->save();
    
    DB::commit();
    
    echo "✅ Invoice amounts updated!\n\n";
    echo "New Invoice Amounts:\n";
    echo "  Pfee1: " . $invoice->pfee1_inv . "\n";
    echo "  Pfee2: " . $invoice->pfee2_inv . "\n";
    echo "  Total Pfee: " . ($invoice->pfee1_inv + $invoice->pfee2_inv) . "\n";
    echo "  SST: " . $invoice->sst_inv . "\n";
    echo "  Reimb: " . $invoice->reimbursement_amount . "\n";
    echo "  ReimbSST: " . $invoice->reimbursement_sst . "\n";
    echo "  Total Amount: " . $invoice->amount . "\n\n";
    
    echo "Now 'to Transfer' should be 0.00 for this invoice.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}



