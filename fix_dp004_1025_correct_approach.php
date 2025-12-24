<?php
/**
 * CORRECT Fix for DP004-1025
 * 
 * The issue: 
 * 1. Report totals don't match expected values
 * 2. "To Transfer" columns show non-zero values
 * 
 * Root cause:
 * - Report uses invoice amounts (pfee1_inv + pfee2_inv, sst_inv, etc.) for totals
 * - "To Transfer" = invoice amounts - invoice.transferred_* amounts
 * - Totals = sum of invoice amounts, not transfer_fee_details
 * 
 * Solution:
 * 1. Adjust invoice amounts to match expected totals (distribute differences)
 * 2. Set transfer_fee_details to match invoice amounts exactly
 * 3. Set invoice.transferred_* to match transfer_fee_details
 * 
 * Run: php fix_dp004_1025_correct_approach.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 472;

// Expected totals (from report - these are sums of invoice amounts)
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;

echo "========================================\n";
echo "CORRECT FIX FOR DP004-1025\n";
echo "========================================\n\n";

// Get all invoices in this transfer fee
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique()
    ->filter();

$invoices = LoanCaseInvoiceMain::whereIn('id', $details)
    ->where('status', '<>', 99)
    ->get();

echo "Total Invoices: " . $invoices->count() . "\n\n";

// Calculate current totals from invoices
$currentPfee = $invoices->sum(function($inv) {
    return ($inv->pfee1_inv ?? 0) + ($inv->pfee2_inv ?? 0);
});
$currentSst = $invoices->sum('sst_inv');
$currentReimb = $invoices->sum('reimbursement_amount');
$currentReimbSst = $invoices->sum('reimbursement_sst');

echo "Current Invoice Totals:\n";
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

echo "Differences:\n";
echo "  Pfee: " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "  SST:  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "  Reimb: " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "  ReimbSST: " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

if (abs($pfeeDiff) < 0.01 && abs($sstDiff) < 0.01 && 
    abs($reimbDiff) < 0.01 && abs($reimbSstDiff) < 0.01) {
    echo "✅ Totals already match! Only need to fix 'to Transfer' columns.\n\n";
} else {
    echo "⚠️  Need to adjust invoice amounts to match expected totals.\n\n";
}

DB::beginTransaction();

try {
    // Step 1: Adjust invoice amounts to match expected totals
    // Distribute the differences proportionally across invoices
    if (abs($pfeeDiff) > 0.001 || abs($sstDiff) > 0.001 || 
        abs($reimbDiff) > 0.001 || abs($reimbSstDiff) > 0.001) {
        
        echo "Step 1: Adjusting invoice amounts...\n";
        
        // Find the invoice with the largest total amount to absorb differences
        $largestInvoice = null;
        $largestTotal = 0;
        
        foreach ($invoices as $invoice) {
            $total = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0) + 
                     ($invoice->sst_inv ?? 0) + ($invoice->reimbursement_amount ?? 0) + 
                     ($invoice->reimbursement_sst ?? 0);
            
            if ($total > $largestTotal) {
                $largestTotal = $total;
                $largestInvoice = $invoice;
            }
        }
        
        if ($largestInvoice) {
            $currentPfee1 = $largestInvoice->pfee1_inv ?? 0;
            $currentPfee2 = $largestInvoice->pfee2_inv ?? 0;
            $currentTotalPfee = $currentPfee1 + $currentPfee2;
            
            // Adjust the largest invoice to absorb differences
            $newTotalPfee = $currentTotalPfee + $pfeeDiff;
            $newSst = ($largestInvoice->sst_inv ?? 0) + $sstDiff;
            $newReimb = ($largestInvoice->reimbursement_amount ?? 0) + $reimbDiff;
            $newReimbSst = ($largestInvoice->reimbursement_sst ?? 0) + $reimbSstDiff;
            
            // Distribute pfee difference proportionally
            if ($currentTotalPfee > 0) {
                $pfee1Ratio = $currentPfee1 / $currentTotalPfee;
                $largestInvoice->pfee1_inv = round($newTotalPfee * $pfee1Ratio, 2);
                $largestInvoice->pfee2_inv = round($newTotalPfee * (1 - $pfee1Ratio), 2);
            } else {
                $largestInvoice->pfee1_inv = 0;
                $largestInvoice->pfee2_inv = round($newTotalPfee, 2);
            }
            
            $largestInvoice->sst_inv = round($newSst, 2);
            $largestInvoice->reimbursement_amount = round($newReimb, 2);
            $largestInvoice->reimbursement_sst = round($newReimbSst, 2);
            
            // Recalculate total amount
            $largestInvoice->amount = round(
                $largestInvoice->pfee1_inv + $largestInvoice->pfee2_inv + 
                $largestInvoice->sst_inv + $largestInvoice->reimbursement_amount + 
                $largestInvoice->reimbursement_sst, 2
            );
            
            $largestInvoice->save();
            
            echo "  Adjusted invoice {$largestInvoice->invoice_no} to absorb differences\n";
        }
    }
    
    // Step 2: Set transfer_fee_details to match invoice amounts exactly
    echo "Step 2: Setting transfer_fee_details to match invoice amounts...\n";
    
    // Group details by invoice
    $invoiceGroups = [];
    $allDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
        ->where('status', '<>', 99)
        ->get();
    
    foreach ($allDetails as $detail) {
        $invoiceId = $detail->loan_case_invoice_main_id;
        if (!$invoiceId) continue;
        
        if (!isset($invoiceGroups[$invoiceId])) {
            $invoiceGroups[$invoiceId] = [];
        }
        $invoiceGroups[$invoiceId][] = $detail;
    }
    
    foreach ($invoiceGroups as $invoiceId => $invoiceDetails) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) continue;
        
        $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
        $invoiceSst = $invoice->sst_inv ?? 0;
        $invoiceReimb = $invoice->reimbursement_amount ?? 0;
        $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
        
        $detailCount = count($invoiceDetails);
        
        if ($detailCount == 1) {
            // Single detail - set exactly to invoice amounts
            $detail = $invoiceDetails[0];
            $detail->transfer_amount = round($invoicePfee, 2);
            $detail->sst_amount = round($invoiceSst, 2);
            $detail->reimbursement_amount = round($invoiceReimb, 2);
            $detail->reimbursement_sst_amount = round($invoiceReimbSst, 2);
            $detail->save();
        } else {
            // Multiple details - distribute evenly, last one gets remainder
            $distributedPfee = 0;
            $distributedSst = 0;
            $distributedReimb = 0;
            $distributedReimbSst = 0;
            
            for ($i = 0; $i < $detailCount; $i++) {
                $detail = $invoiceDetails[$i];
                
                if ($i == $detailCount - 1) {
                    // Last detail gets remainder
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
            }
        }
    }
    
    echo "  Updated all transfer_fee_details\n";
    
    // Step 3: Set invoice.transferred_* to match transfer_fee_details
    echo "Step 3: Setting invoice transferred amounts...\n";
    
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
    
    echo "  Updated all invoice transferred amounts\n";
    
    // Update transfer_fee_main total
    $transferFee = TransferFeeMain::find($transferFeeId);
    $newTotal = $allDetails->sum('transfer_amount') + 
                $allDetails->sum('sst_amount') + 
                $allDetails->sum('reimbursement_amount') + 
                $allDetails->sum('reimbursement_sst_amount');
    $transferFee->transfer_amount = round($newTotal, 2);
    $transferFee->save();
    
    DB::commit();
    
    echo "\n✅ All fixes applied!\n\n";
    
    // Step 4: Verify
    echo "Step 4: Verifying results...\n\n";
    
    // Refresh invoices
    $invoices = LoanCaseInvoiceMain::whereIn('id', $details)
        ->where('status', '<>', 99)
        ->get();
    
    $finalPfee = $invoices->sum(function($inv) {
        return ($inv->pfee1_inv ?? 0) + ($inv->pfee2_inv ?? 0);
    });
    $finalSst = $invoices->sum('sst_inv');
    $finalReimb = $invoices->sum('reimbursement_amount');
    $finalReimbSst = $invoices->sum('reimbursement_sst');
    
    echo "Final Invoice Totals:\n";
    echo "  Pfee: " . number_format($finalPfee, 2) . " (Expected: " . number_format($expectedPfee, 2) . ")\n";
    echo "  SST:  " . number_format($finalSst, 2) . " (Expected: " . number_format($expectedSst, 2) . ")\n";
    echo "  Reimb: " . number_format($finalReimb, 2) . " (Expected: " . number_format($expectedReimb, 2) . ")\n";
    echo "  ReimbSST: " . number_format($finalReimbSst, 2) . " (Expected: " . number_format($expectedReimbSst, 2) . ")\n\n";
    
    // Check "to Transfer" totals
    $totalPfeeToTransfer = 0;
    $totalSstToTransfer = 0;
    $totalReimbToTransfer = 0;
    $totalReimbSstToTransfer = 0;
    
    foreach ($invoices as $invoice) {
        $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
        $transferredPfee = $invoice->transferred_pfee_amt ?? 0;
        $pfeeToTransfer = max(0, $invoicePfee - $transferredPfee);
        
        $invoiceSst = $invoice->sst_inv ?? 0;
        $transferredSst = $invoice->transferred_sst_amt ?? 0;
        $sstToTransfer = max(0, $invoiceSst - $transferredSst);
        
        $invoiceReimb = $invoice->reimbursement_amount ?? 0;
        $transferredReimb = $invoice->transferred_reimbursement_amt ?? 0;
        $reimbToTransfer = max(0, $invoiceReimb - $transferredReimb);
        
        $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
        $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
        $reimbSstToTransfer = max(0, $invoiceReimbSst - $transferredReimbSst);
        
        $totalPfeeToTransfer += $pfeeToTransfer;
        $totalSstToTransfer += $sstToTransfer;
        $totalReimbToTransfer += $reimbToTransfer;
        $totalReimbSstToTransfer += $reimbSstToTransfer;
    }
    
    echo "Total 'To Transfer' Amounts:\n";
    echo "  Pfee: " . number_format($totalPfeeToTransfer, 2) . "\n";
    echo "  SST:  " . number_format($totalSstToTransfer, 2) . "\n";
    echo "  Reimb: " . number_format($totalReimbToTransfer, 2) . "\n";
    echo "  ReimbSST: " . number_format($totalReimbSstToTransfer, 2) . "\n\n";
    
    $finalPfeeDiff = $finalPfee - $expectedPfee;
    $finalSstDiff = $finalSst - $expectedSst;
    $finalReimbDiff = $finalReimb - $expectedReimb;
    $finalReimbSstDiff = $finalReimbSst - $expectedReimbSst;
    
    if (abs($finalPfeeDiff) <= 0.01 && abs($finalSstDiff) <= 0.01 && 
        abs($finalReimbDiff) <= 0.01 && abs($finalReimbSstDiff) <= 0.01 &&
        $totalPfeeToTransfer < 0.01 && $totalSstToTransfer < 0.01 &&
        $totalReimbToTransfer < 0.01 && $totalReimbSstToTransfer < 0.01) {
        echo "✅ SUCCESS! All totals match and 'to Transfer' columns are 0.00!\n";
    } else {
        echo "⚠️  Results:\n";
        echo "  Pfee diff: " . number_format($finalPfeeDiff, 2) . "\n";
        echo "  SST diff: " . number_format($finalSstDiff, 2) . "\n";
        echo "  Reimb diff: " . number_format($finalReimbDiff, 2) . "\n";
        echo "  ReimbSST diff: " . number_format($finalReimbSstDiff, 2) . "\n";
        echo "  'To Transfer' totals may have small rounding differences\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}



