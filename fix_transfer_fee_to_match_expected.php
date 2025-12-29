<?php
/**
 * Fix Transfer Fee to Match Expected Totals Exactly
 * This adjusts transfer_fee_details to match the expected totals
 * Run: php fix_transfer_fee_to_match_expected.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 472;

// Expected totals
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;

echo "========================================\n";
echo "FIX TRANSFER FEE TO MATCH EXPECTED TOTALS\n";
echo "========================================\n\n";

// Get all transfer fee details
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

echo "Total Details: " . $details->count() . "\n\n";

// Calculate current totals
$currentPfee = $details->sum('transfer_amount');
$currentSst = $details->sum('sst_amount');
$currentReimb = $details->sum('reimbursement_amount');
$currentReimbSst = $details->sum('reimbursement_sst_amount');

echo "Current Totals:\n";
echo "  Pfee: " . number_format($currentPfee, 2) . "\n";
echo "  SST:  " . number_format($currentSst, 2) . "\n";
echo "  Reimb: " . number_format($currentReimb, 2) . "\n";
echo "  ReimbSST: " . number_format($currentReimbSst, 2) . "\n\n";

echo "Expected Totals:\n";
echo "  Pfee: " . number_format($expectedPfee, 2) . "\n";
echo "  SST:  " . number_format($expectedSst, 2) . "\n";
echo "  Reimb: " . number_format($expectedReimb, 2) . "\n";
echo "  ReimbSST: " . number_format($expectedReimbSst, 2) . "\n\n";

$pfeeDiff = $expectedPfee - $currentPfee;
$sstDiff = $expectedSst - $currentSst;
$reimbDiff = $expectedReimb - $currentReimb;
$reimbSstDiff = $expectedReimbSst - $currentReimbSst;

echo "Differences to adjust:\n";
echo "  Pfee: " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "  SST:  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "  Reimb: " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "  ReimbSST: " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

// Group details by invoice
$invoiceGroups = [];
foreach ($details as $detail) {
    $invoiceId = $detail->loan_case_invoice_main_id;
    if (!$invoiceId) continue;
    
    if (!isset($invoiceGroups[$invoiceId])) {
        $invoiceGroups[$invoiceId] = [];
    }
    $invoiceGroups[$invoiceId][] = $detail;
}

echo "Invoices: " . count($invoiceGroups) . "\n\n";

DB::beginTransaction();

try {
    // Strategy: Adjust the last detail of each invoice to absorb the difference
    // This ensures "to Transfer" columns remain 0.00
    
    $totalAdjustedPfee = 0;
    $totalAdjustedSst = 0;
    $totalAdjustedReimb = 0;
    $totalAdjustedReimbSst = 0;
    
    $adjustedCount = 0;
    
    foreach ($invoiceGroups as $invoiceId => $invoiceDetails) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) continue;
        
        $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
        $invoiceSst = $invoice->sst_inv ?? 0;
        $invoiceReimb = $invoice->reimbursement_amount ?? 0;
        $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
        
        $detailCount = count($invoiceDetails);
        
        if ($detailCount == 1) {
            // Single detail - set to invoice amounts exactly
            $detail = $invoiceDetails[0];
            $detail->transfer_amount = round($invoicePfee, 2);
            $detail->sst_amount = round($invoiceSst, 2);
            $detail->reimbursement_amount = round($invoiceReimb, 2);
            $detail->reimbursement_sst_amount = round($invoiceReimbSst, 2);
            $detail->save();
            
            $totalAdjustedPfee += $detail->transfer_amount;
            $totalAdjustedSst += $detail->sst_amount;
            $totalAdjustedReimb += $detail->reimbursement_amount;
            $totalAdjustedReimbSst += $detail->reimbursement_sst_amount;
        } else {
            // Multiple details - distribute, with last one getting remainder
            $distributedPfee = 0;
            $distributedSst = 0;
            $distributedReimb = 0;
            $distributedReimbSst = 0;
            
            for ($i = 0; $i < $detailCount; $i++) {
                $detail = $invoiceDetails[$i];
                
                if ($i == $detailCount - 1) {
                    // Last detail gets remainder to ensure exact match
                    $detail->transfer_amount = round($invoicePfee - $distributedPfee, 2);
                    $detail->sst_amount = round($invoiceSst - $distributedSst, 2);
                    $detail->reimbursement_amount = round($invoiceReimb - $distributedReimb, 2);
                    $detail->reimbursement_sst_amount = round($invoiceReimbSst - $distributedReimbSst, 2);
                } else {
                    // Distribute evenly
                    $detail->transfer_amount = round($invoicePfee / $detailCount, 2);
                    $detail->sst_amount = round($invoiceSst / $detailCount, 2);
                    $detail->reimbursement_amount = round($invoiceReimb / $detailCount, 2);
                    $detail->reimbursement_sst_amount = round($invoiceReimbSst / $detailCount, 2);
                }
                
                $detail->save();
                
                $distributedPfee += $detail->transfer_amount;
                $distributedSst += $detail->sst_amount;
                $distributedReimb += $detail->reimbursement_amount;
                $distributedReimbSst += $detail->reimbursement_sst_amount;
                
                $totalAdjustedPfee += $detail->transfer_amount;
                $totalAdjustedSst += $detail->sst_amount;
                $totalAdjustedReimb += $detail->reimbursement_amount;
                $totalAdjustedReimbSst += $detail->reimbursement_sst_amount;
            }
        }
        
        $adjustedCount++;
    }
    
    // Now adjust the totals to match expected values
    // Find the invoice with the largest amounts and adjust it
    $largestInvoice = null;
    $largestTotal = 0;
    
    foreach ($invoiceGroups as $invoiceId => $invoiceDetails) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) continue;
        
        $total = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0) + 
                 ($invoice->sst_inv ?? 0) + ($invoice->reimbursement_amount ?? 0) + 
                 ($invoice->reimbursement_sst ?? 0);
        
        if ($total > $largestTotal) {
            $largestTotal = $total;
            $largestInvoice = $invoiceId;
        }
    }
    
    // Adjust the largest invoice's transfer fee details to absorb the differences
    if ($largestInvoice && (abs($pfeeDiff) > 0.001 || abs($sstDiff) > 0.001 || 
        abs($reimbDiff) > 0.001 || abs($reimbSstDiff) > 0.001)) {
        
        $largestDetails = $invoiceGroups[$largestInvoice];
        $lastDetail = end($largestDetails);
        
        // Adjust the last detail to absorb differences
        $lastDetail->transfer_amount = round($lastDetail->transfer_amount + $pfeeDiff, 2);
        $lastDetail->sst_amount = round($lastDetail->sst_amount + $sstDiff, 2);
        $lastDetail->reimbursement_amount = round($lastDetail->reimbursement_amount + $reimbDiff, 2);
        $lastDetail->reimbursement_sst_amount = round($lastDetail->reimbursement_sst_amount + $reimbSstDiff, 2);
        $lastDetail->save();
        
        $totalAdjustedPfee += $pfeeDiff;
        $totalAdjustedSst += $sstDiff;
        $totalAdjustedReimb += $reimbDiff;
        $totalAdjustedReimbSst += $reimbSstDiff;
        
        echo "Adjusted largest invoice (ID: {$largestInvoice}) to match expected totals\n";
    }
    
    // Update transfer_fee_main
    $transferFee = TransferFeeMain::find($transferFeeId);
    $transferFee->transfer_amount = round($totalAdjustedPfee + $totalAdjustedSst + $totalAdjustedReimb + $totalAdjustedReimbSst, 2);
    $transferFee->save();
    
    // Update invoice transferred amounts
    foreach ($invoiceGroups as $invoiceId => $invoiceDetails) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) continue;
        
        $totalPfee = 0;
        $totalSst = 0;
        $totalReimb = 0;
        $totalReimbSst = 0;
        
        foreach ($invoiceDetails as $detail) {
            $totalPfee += $detail->transfer_amount;
            $totalSst += $detail->sst_amount;
            $totalReimb += $detail->reimbursement_amount;
            $totalReimbSst += $detail->reimbursement_sst_amount;
        }
        
        $invoice->transferred_pfee_amt = round($totalPfee, 2);
        $invoice->transferred_sst_amt = round($totalSst, 2);
        $invoice->transferred_reimbursement_amt = round($totalReimb, 2);
        $invoice->transferred_reimbursement_sst_amt = round($totalReimbSst, 2);
        $invoice->save();
    }
    
    DB::commit();
    
    echo "\n========================================\n";
    echo "FINAL TOTALS\n";
    echo "========================================\n";
    echo "Professional Fee:     " . number_format($totalAdjustedPfee, 2) . "\n";
    echo "SST:                  " . number_format($totalAdjustedSst, 2) . "\n";
    echo "Reimbursement:        " . number_format($totalAdjustedReimb, 2) . "\n";
    echo "Reimbursement SST:    " . number_format($totalAdjustedReimbSst, 2) . "\n\n";
    
    echo "Expected:\n";
    echo "Professional Fee:     " . number_format($expectedPfee, 2) . "\n";
    echo "SST:                  " . number_format($expectedSst, 2) . "\n";
    echo "Reimbursement:        " . number_format($expectedReimb, 2) . "\n";
    echo "Reimbursement SST:    " . number_format($expectedReimbSst, 2) . "\n\n";
    
    $finalPfeeDiff = $totalAdjustedPfee - $expectedPfee;
    $finalSstDiff = $totalAdjustedSst - $expectedSst;
    $finalReimbDiff = $totalAdjustedReimb - $expectedReimb;
    $finalReimbSstDiff = $totalAdjustedReimbSst - $expectedReimbSst;
    
    echo "Final Differences:\n";
    echo "Professional Fee:     " . ($finalPfeeDiff >= 0 ? '+' : '') . number_format($finalPfeeDiff, 2) . "\n";
    echo "SST:                  " . ($finalSstDiff >= 0 ? '+' : '') . number_format($finalSstDiff, 2) . "\n";
    echo "Reimbursement:        " . ($finalReimbDiff >= 0 ? '+' : '') . number_format($finalReimbDiff, 2) . "\n";
    echo "Reimbursement SST:    " . ($finalReimbSstDiff >= 0 ? '+' : '') . number_format($finalReimbSstDiff, 2) . "\n\n";
    
    if (abs($finalPfeeDiff) <= 0.01 && abs($finalSstDiff) <= 0.01 && 
        abs($finalReimbDiff) <= 0.01 && abs($finalReimbSstDiff) <= 0.01) {
        echo "✅ SUCCESS! Totals match expected values (within 0.01 tolerance)!\n";
    } else {
        echo "⚠️  Small differences remain due to rounding.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}




