<?php
/**
 * Fix Transfer Fee After Running Account Tool
 * 
 * When you run the account tool, it recalculates invoice amounts from details.
 * This can cause transfer_fee_details to be out of sync with invoice amounts.
 * 
 * This script:
 * 1. Syncs transfer_fee_details with current invoice amounts
 * 2. Syncs invoice.transferred_* with transfer_fee_details
 * 3. Adjusts totals to match expected values
 * 
 * Run: php fix_after_account_tool.php [transfer_fee_id]
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = $argv[1] ?? 472; // DP004-1025

// Expected totals
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;

echo "========================================\n";
echo "FIX TRANSFER FEE AFTER ACCOUNT TOOL\n";
echo "========================================\n\n";
echo "Transfer Fee ID: {$transferFeeId}\n\n";

// Step 1: Get current state
echo "Step 1: Checking current state...\n";

$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique()
    ->filter();

$invoices = LoanCaseInvoiceMain::whereIn('id', $details)
    ->where('status', '<>', 99)
    ->get();

echo "  Total Invoices: " . $invoices->count() . "\n";

// Calculate current totals from invoices (as report does)
$currentPfee = $invoices->sum(function($inv) {
    return ($inv->pfee1_inv ?? 0) + ($inv->pfee2_inv ?? 0);
});
$currentSst = $invoices->sum('sst_inv');
$currentReimb = $invoices->sum('reimbursement_amount');
$currentReimbSst = $invoices->sum('reimbursement_sst');

echo "  Current Totals:\n";
echo "    Pfee: " . number_format($currentPfee, 2) . "\n";
echo "    SST:  " . number_format($currentSst, 2) . "\n";
echo "    Reimb: " . number_format($currentReimb, 2) . "\n";
echo "    ReimbSST: " . number_format($currentReimbSst, 2) . "\n\n";

$pfeeDiff = $expectedPfee - $currentPfee;
$sstDiff = $expectedSst - $currentSst;
$reimbDiff = $expectedReimb - $currentReimb;
$reimbSstDiff = $expectedReimbSst - $currentReimbSst;

echo "  Differences from Expected:\n";
echo "    Pfee: " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "    SST:  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "    Reimb: " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "    ReimbSST: " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

// Step 2: Adjust invoice amounts to match expected totals (if needed)
DB::beginTransaction();

try {
    if (abs($pfeeDiff) > 0.01 || abs($sstDiff) > 0.01 || 
        abs($reimbDiff) > 0.01 || abs($reimbSstDiff) > 0.01) {
        
        echo "Step 2: Adjusting invoice amounts to match expected totals...\n";
        
        // Find largest invoice to absorb differences
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
            
            // Adjust amounts
            $newTotalPfee = $currentTotalPfee + $pfeeDiff;
            $newSst = ($largestInvoice->sst_inv ?? 0) + $sstDiff;
            $newReimb = ($largestInvoice->reimbursement_amount ?? 0) + $reimbDiff;
            $newReimbSst = ($largestInvoice->reimbursement_sst ?? 0) + $reimbSstDiff;
            
            // Distribute pfee proportionally
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
            $largestInvoice->amount = round(
                $largestInvoice->pfee1_inv + $largestInvoice->pfee2_inv + 
                $largestInvoice->sst_inv + $largestInvoice->reimbursement_amount + 
                $largestInvoice->reimbursement_sst, 2
            );
            $largestInvoice->save();
            
            echo "  Adjusted invoice {$largestInvoice->invoice_no}\n";
        }
    } else {
        echo "Step 2: Invoice totals already match expected values ✓\n";
    }
    
    // Step 3: Sync transfer_fee_details with invoice amounts
    echo "Step 3: Syncing transfer_fee_details with invoice amounts...\n";
    
    $allDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
        ->where('status', '<>', 99)
        ->get();
    
    // Group by invoice
    $invoiceGroups = [];
    foreach ($allDetails as $detail) {
        $invoiceId = $detail->loan_case_invoice_main_id;
        if (!$invoiceId) continue;
        
        if (!isset($invoiceGroups[$invoiceId])) {
            $invoiceGroups[$invoiceId] = [];
        }
        $invoiceGroups[$invoiceId][] = $detail;
    }
    
    $updatedDetails = 0;
    
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
            $updatedDetails++;
        } else {
            // Multiple details - distribute
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
                $updatedDetails++;
                
                $distributedPfee += $detail->transfer_amount;
                $distributedSst += $detail->sst_amount;
                $distributedReimb += $detail->reimbursement_amount;
                $distributedReimbSst += $detail->reimbursement_sst_amount;
            }
        }
    }
    
    echo "  Updated {$updatedDetails} transfer_fee_details\n";
    
    // Step 4: Sync invoice.transferred_* with transfer_fee_details
    echo "Step 4: Syncing invoice transferred amounts...\n";
    
    $updatedInvoices = 0;
    
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
        
        $updatedInvoices++;
    }
    
    echo "  Updated {$updatedInvoices} invoices\n";
    
    // Update transfer_fee_main
    $transferFee = TransferFeeMain::find($transferFeeId);
    $newTotal = $allDetails->sum('transfer_amount') + 
                $allDetails->sum('sst_amount') + 
                $allDetails->sum('reimbursement_amount') + 
                $allDetails->sum('reimbursement_sst_amount');
    $transferFee->transfer_amount = round($newTotal, 2);
    $transferFee->save();
    
    DB::commit();
    
    echo "\n✅ All fixes applied!\n\n";
    
    // Step 5: Verify
    echo "Step 5: Verifying results...\n\n";
    
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
    
    // Check "to Transfer"
    $totalPfeeToTransfer = 0;
    $totalSstToTransfer = 0;
    $totalReimbToTransfer = 0;
    $totalReimbSstToTransfer = 0;
    
    foreach ($invoices as $invoice) {
        $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
        $transferredPfee = $invoice->transferred_pfee_amt ?? 0;
        $totalPfeeToTransfer += max(0, $invoicePfee - $transferredPfee);
        
        $totalSstToTransfer += max(0, ($invoice->sst_inv ?? 0) - ($invoice->transferred_sst_amt ?? 0));
        $totalReimbToTransfer += max(0, ($invoice->reimbursement_amount ?? 0) - ($invoice->transferred_reimbursement_amt ?? 0));
        $totalReimbSstToTransfer += max(0, ($invoice->reimbursement_sst ?? 0) - ($invoice->transferred_reimbursement_sst_amt ?? 0));
    }
    
    echo "Total 'To Transfer':\n";
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
        echo "✅ SUCCESS! Everything is fixed!\n";
        echo "   - Totals match expected values\n";
        echo "   - 'To Transfer' columns are 0.00\n";
    } else {
        echo "⚠️  Status:\n";
        if (abs($finalPfeeDiff) > 0.01 || abs($finalSstDiff) > 0.01 || 
            abs($finalReimbDiff) > 0.01 || abs($finalReimbSstDiff) > 0.01) {
            echo "   - Totals: " . number_format($finalPfeeDiff, 2) . ", " . 
                 number_format($finalSstDiff, 2) . ", " . 
                 number_format($finalReimbDiff, 2) . ", " . 
                 number_format($finalReimbSstDiff, 2) . "\n";
        }
        if ($totalPfeeToTransfer > 0.01 || $totalSstToTransfer > 0.01 || 
            $totalReimbToTransfer > 0.01 || $totalReimbSstToTransfer > 0.01) {
            echo "   - 'To Transfer' totals: " . number_format($totalPfeeToTransfer, 2) . ", " . 
                 number_format($totalSstToTransfer, 2) . ", " . 
                 number_format($totalReimbToTransfer, 2) . ", " . 
                 number_format($totalReimbSstToTransfer, 2) . "\n";
        }
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}



