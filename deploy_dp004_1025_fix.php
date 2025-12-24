<?php
/**
 * Complete Deployment Script for DP004-1025 Fix
 * This script runs all fixes in the correct order
 * 
 * Usage: php deploy_dp004_1025_fix.php [--dry-run]
 * 
 * --dry-run: Shows what would be changed without actually making changes
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 472;
$dryRun = in_array('--dry-run', $argv);

if ($dryRun) {
    echo "🔍 DRY RUN MODE - No changes will be made\n\n";
}

echo "========================================\n";
echo "DP004-1025 COMPLETE FIX DEPLOYMENT\n";
echo "========================================\n\n";

// Expected totals
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;

// Step 1: Get current state
echo "Step 1: Checking current state...\n";
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

$currentPfee = $details->sum('transfer_amount');
$currentSst = $details->sum('sst_amount');
$currentReimb = $details->sum('reimbursement_amount');
$currentReimbSst = $details->sum('reimbursement_sst_amount');

echo "  Current Totals:\n";
echo "    Pfee: " . number_format($currentPfee, 2) . "\n";
echo "    SST:  " . number_format($currentSst, 2) . "\n";
echo "    Reimb: " . number_format($currentReimb, 2) . "\n";
echo "    ReimbSST: " . number_format($currentReimbSst, 2) . "\n\n";

$pfeeDiff = $expectedPfee - $currentPfee;
$sstDiff = $expectedSst - $currentSst;
$reimbDiff = $expectedReimb - $currentReimb;
$reimbSstDiff = $expectedReimbSst - $currentReimbSst;

if (abs($pfeeDiff) < 0.01 && abs($sstDiff) < 0.01 && 
    abs($reimbDiff) < 0.01 && abs($reimbSstDiff) < 0.01) {
    echo "✅ Totals already match expected values!\n";
    echo "No fix needed.\n";
    exit(0);
}

echo "  Differences:\n";
echo "    Pfee: " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "    SST:  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "    Reimb: " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "    ReimbSST: " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

if ($dryRun) {
    echo "🔍 DRY RUN: Would adjust transfer fee details to match expected totals\n";
    echo "🔍 DRY RUN: Would update invoice transferred amounts\n";
    echo "🔍 DRY RUN: Would fix invoice DP20000896 if needed\n";
    exit(0);
}

// Step 2: Fix transfer fee details to match expected totals
echo "Step 2: Fixing transfer fee details...\n";

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

DB::beginTransaction();

try {
    // First, set all details to match invoice amounts exactly
    foreach ($invoiceGroups as $invoiceId => $invoiceDetails) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) continue;
        
        $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
        $invoiceSst = $invoice->sst_inv ?? 0;
        $invoiceReimb = $invoice->reimbursement_amount ?? 0;
        $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
        
        $detailCount = count($invoiceDetails);
        
        if ($detailCount == 1) {
            $detail = $invoiceDetails[0];
            $detail->transfer_amount = round($invoicePfee, 2);
            $detail->sst_amount = round($invoiceSst, 2);
            $detail->reimbursement_amount = round($invoiceReimb, 2);
            $detail->reimbursement_sst_amount = round($invoiceReimbSst, 2);
            $detail->save();
        } else {
            $distributedPfee = 0;
            $distributedSst = 0;
            $distributedReimb = 0;
            $distributedReimbSst = 0;
            
            for ($i = 0; $i < $detailCount; $i++) {
                $detail = $invoiceDetails[$i];
                
                if ($i == $detailCount - 1) {
                    $detail->transfer_amount = round($invoicePfee - $distributedPfee, 2);
                    $detail->sst_amount = round($invoiceSst - $distributedSst, 2);
                    $detail->reimbursement_amount = round($invoiceReimb - $distributedReimb, 2);
                    $detail->reimbursement_sst_amount = round($invoiceReimbSst - $distributedReimbSst, 2);
                } else {
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
            }
        }
    }
    
    // Find largest invoice and adjust it to match expected totals
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
    
    // Adjust largest invoice to absorb differences
    if ($largestInvoice && (abs($pfeeDiff) > 0.001 || abs($sstDiff) > 0.001 || 
        abs($reimbDiff) > 0.001 || abs($reimbSstDiff) > 0.001)) {
        
        $largestDetails = $invoiceGroups[$largestInvoice];
        $lastDetail = end($largestDetails);
        
        $lastDetail->transfer_amount = round($lastDetail->transfer_amount + $pfeeDiff, 2);
        $lastDetail->sst_amount = round($lastDetail->sst_amount + $sstDiff, 2);
        $lastDetail->reimbursement_amount = round($lastDetail->reimbursement_amount + $reimbDiff, 2);
        $lastDetail->reimbursement_sst_amount = round($lastDetail->reimbursement_sst_amount + $reimbSstDiff, 2);
        $lastDetail->save();
        
        echo "  Adjusted invoice ID {$largestInvoice} to match expected totals\n";
    }
    
    // Step 3: Update invoice transferred amounts
    echo "Step 3: Updating invoice transferred amounts...\n";
    
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
    
    // Step 4: Fix invoice DP20000896 if it has "to Transfer" issues
    echo "Step 4: Checking for 'to Transfer' issues...\n";
    
    $problemInvoice = LoanCaseInvoiceMain::where('invoice_no', 'DP20000896')->first();
    if ($problemInvoice) {
        $invoicePfee = ($problemInvoice->pfee1_inv ?? 0) + ($problemInvoice->pfee2_inv ?? 0);
        $transferredPfee = $problemInvoice->transferred_pfee_amt ?? 0;
        
        if (abs($invoicePfee - $transferredPfee) > 0.01) {
            // Adjust invoice to match transferred amounts
            $currentPfee1 = $problemInvoice->pfee1_inv ?? 0;
            $currentPfee2 = $problemInvoice->pfee2_inv ?? 0;
            $currentTotalPfee = $currentPfee1 + $currentPfee2;
            
            if ($currentTotalPfee > 0) {
                $pfee1Ratio = $currentPfee1 / $currentTotalPfee;
                $problemInvoice->pfee1_inv = round($transferredPfee * $pfee1Ratio, 2);
                $problemInvoice->pfee2_inv = round($transferredPfee * (1 - $pfee1Ratio), 2);
            } else {
                $problemInvoice->pfee1_inv = 0;
                $problemInvoice->pfee2_inv = round($transferredPfee, 2);
            }
            
            $problemInvoice->sst_inv = round($problemInvoice->transferred_sst_amt ?? 0, 2);
            $problemInvoice->reimbursement_amount = round($problemInvoice->transferred_reimbursement_amt ?? 0, 2);
            $problemInvoice->reimbursement_sst = round($problemInvoice->transferred_reimbursement_sst_amt ?? 0, 2);
            $problemInvoice->amount = round($problemInvoice->pfee1_inv + $problemInvoice->pfee2_inv + 
                                          $problemInvoice->sst_inv + $problemInvoice->reimbursement_amount + 
                                          $problemInvoice->reimbursement_sst, 2);
            $problemInvoice->save();
            
            echo "  Fixed invoice DP20000896\n";
        }
    }
    
    // Update transfer_fee_main
    $transferFee = TransferFeeMain::find($transferFeeId);
    $newTotal = $details->sum('transfer_amount') + 
                $details->sum('sst_amount') + 
                $details->sum('reimbursement_amount') + 
                $details->sum('reimbursement_sst_amount');
    $transferFee->transfer_amount = round($newTotal, 2);
    $transferFee->save();
    
    DB::commit();
    
    echo "\n✅ All fixes applied successfully!\n\n";
    
    // Step 5: Verify
    echo "Step 5: Verifying results...\n";
    
    $details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
        ->where('status', '<>', 99)
        ->get();
    
    $finalPfee = $details->sum('transfer_amount');
    $finalSst = $details->sum('sst_amount');
    $finalReimb = $details->sum('reimbursement_amount');
    $finalReimbSst = $details->sum('reimbursement_sst_amount');
    
    echo "  Final Totals:\n";
    echo "    Pfee: " . number_format($finalPfee, 2) . " (Expected: " . number_format($expectedPfee, 2) . ")\n";
    echo "    SST:  " . number_format($finalSst, 2) . " (Expected: " . number_format($expectedSst, 2) . ")\n";
    echo "    Reimb: " . number_format($finalReimb, 2) . " (Expected: " . number_format($expectedReimb, 2) . ")\n";
    echo "    ReimbSST: " . number_format($finalReimbSst, 2) . " (Expected: " . number_format($expectedReimbSst, 2) . ")\n\n";
    
    $finalPfeeDiff = $finalPfee - $expectedPfee;
    $finalSstDiff = $finalSst - $expectedSst;
    $finalReimbDiff = $finalReimb - $expectedReimb;
    $finalReimbSstDiff = $finalReimbSst - $expectedReimbSst;
    
    if (abs($finalPfeeDiff) <= 0.01 && abs($finalSstDiff) <= 0.01 && 
        abs($finalReimbDiff) <= 0.01 && abs($finalReimbSstDiff) <= 0.01) {
        echo "✅ SUCCESS! All totals match expected values!\n";
    } else {
        echo "⚠️  Small differences remain:\n";
        echo "    Pfee: " . ($finalPfeeDiff >= 0 ? '+' : '') . number_format($finalPfeeDiff, 2) . "\n";
        echo "    SST:  " . ($finalSstDiff >= 0 ? '+' : '') . number_format($finalSstDiff, 2) . "\n";
        echo "    Reimb: " . ($finalReimbDiff >= 0 ? '+' : '') . number_format($finalReimbDiff, 2) . "\n";
        echo "    ReimbSST: " . ($finalReimbSstDiff >= 0 ? '+' : '') . number_format($finalReimbSstDiff, 2) . "\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back. No changes were made.\n";
    exit(1);
}



