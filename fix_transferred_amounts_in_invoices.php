<?php
/**
 * Fix Transferred Amounts in Invoices
 * Updates invoice.transferred_* fields from transfer_fee_details
 * This ensures "to Transfer" columns show 0.00
 * 
 * Run: php fix_transferred_amounts_in_invoices.php [transfer_fee_id]
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = $argv[1] ?? 472; // DP004-1025

echo "========================================\n";
echo "FIX TRANSFERRED AMOUNTS IN INVOICES\n";
echo "========================================\n\n";
echo "Transfer Fee ID: {$transferFeeId}\n\n";

// Get all transfer fee details
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

echo "Total Transfer Fee Details: " . $details->count() . "\n\n";

// Group by invoice to sum up transferred amounts
$invoiceTransfers = [];

foreach ($details as $detail) {
    $invoiceId = $detail->loan_case_invoice_main_id;
    if (!$invoiceId) continue;
    
    if (!isset($invoiceTransfers[$invoiceId])) {
        $invoiceTransfers[$invoiceId] = [
            'pfee' => 0,
            'sst' => 0,
            'reimbursement' => 0,
            'reimbursement_sst' => 0
        ];
    }
    
    $invoiceTransfers[$invoiceId]['pfee'] += $detail->transfer_amount ?? 0;
    $invoiceTransfers[$invoiceId]['sst'] += $detail->sst_amount ?? 0;
    $invoiceTransfers[$invoiceId]['reimbursement'] += $detail->reimbursement_amount ?? 0;
    $invoiceTransfers[$invoiceId]['reimbursement_sst'] += $detail->reimbursement_sst_amount ?? 0;
}

echo "Invoices to update: " . count($invoiceTransfers) . "\n\n";

DB::beginTransaction();

try {
    $updatedCount = 0;
    $totalPfee = 0;
    $totalSst = 0;
    $totalReimb = 0;
    $totalReimbSst = 0;
    
    foreach ($invoiceTransfers as $invoiceId => $amounts) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            echo "⚠️  Invoice ID {$invoiceId} not found\n";
            continue;
        }
        
        // Round amounts
        $pfee = round($amounts['pfee'], 2);
        $sst = round($amounts['sst'], 2);
        $reimb = round($amounts['reimbursement'], 2);
        $reimbSst = round($amounts['reimbursement_sst'], 2);
        
        // Check if update is needed
        $needsUpdate = false;
        if (abs($invoice->transferred_pfee_amt - $pfee) > 0.001 ||
            abs(($invoice->transferred_sst_amt ?? 0) - $sst) > 0.001 ||
            abs(($invoice->transferred_reimbursement_amt ?? 0) - $reimb) > 0.001 ||
            abs(($invoice->transferred_reimbursement_sst_amt ?? 0) - $reimbSst) > 0.001) {
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            $invoice->transferred_pfee_amt = $pfee;
            $invoice->transferred_sst_amt = $sst;
            $invoice->transferred_reimbursement_amt = $reimb;
            $invoice->transferred_reimbursement_sst_amt = $reimbSst;
            $invoice->save();
            
            $updatedCount++;
            
            // Calculate what "to transfer" should be
            $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
            $invoiceSst = $invoice->sst_inv ?? 0;
            $invoiceReimb = $invoice->reimbursement_amount ?? 0;
            $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
            
            $pfeeToTransfer = $invoicePfee - $pfee;
            $sstToTransfer = $invoiceSst - $sst;
            $reimbToTransfer = $invoiceReimb - $reimb;
            $reimbSstToTransfer = $invoiceReimbSst - $reimbSst;
            
            if (abs($pfeeToTransfer) > 0.01 || abs($sstToTransfer) > 0.01 || 
                abs($reimbToTransfer) > 0.01 || abs($reimbSstToTransfer) > 0.01) {
                echo "Updated {$invoice->invoice_no}: PfeeToTransfer={$pfeeToTransfer}, SSTToTransfer={$sstToTransfer}, ReimbToTransfer={$reimbToTransfer}, ReimbSSTToTransfer={$reimbSstToTransfer}\n";
            }
        }
        
        $totalPfee += $pfee;
        $totalSst += $sst;
        $totalReimb += $reimb;
        $totalReimbSst += $reimbSst;
    }
    
    DB::commit();
    
    echo "\n========================================\n";
    echo "SUMMARY\n";
    echo "========================================\n";
    echo "Updated Invoices: {$updatedCount}\n";
    echo "Total Transferred Professional Fee: " . number_format($totalPfee, 2) . "\n";
    echo "Total Transferred SST:              " . number_format($totalSst, 2) . "\n";
    echo "Total Transferred Reimbursement:    " . number_format($totalReimb, 2) . "\n";
    echo "Total Transferred Reimbursement SST: " . number_format($totalReimbSst, 2) . "\n\n";
    
    echo "✅ Invoice transferred amounts updated!\n";
    echo "The 'to Transfer' columns should now show 0.00 (or very small amounts due to rounding)\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}



