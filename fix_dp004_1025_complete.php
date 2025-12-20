<?php
/**
 * Complete Fix for DP004-1025 Transfer Fee
 * This script:
 * 1. Fixes all invoices in the transfer fee
 * 2. Recalculates transfer fee details
 * 3. Verifies totals match expected values
 * 
 * Run: php fix_dp004_1025_complete.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;

$transferFeeId = 472; // DP004-1025

echo "========================================\n";
echo "COMPLETE FIX FOR DP004-1025\n";
echo "========================================\n\n";

// Step 1: Get all invoice IDs from transfer fee
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique()
    ->filter();

$invoiceIds = $details->toArray();
echo "Step 1: Found " . count($invoiceIds) . " invoices in transfer fee\n\n";

// Step 2: Fix each invoice using the same logic as InvoiceFixController
echo "Step 2: Fixing invoices...\n";
$fixedCount = 0;
$errorCount = 0;

foreach ($invoiceIds as $invoiceId) {
    try {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            echo "⚠️  Invoice ID {$invoiceId} not found\n";
            $errorCount++;
            continue;
        }
        
        $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
        if (!$bill) {
            echo "⚠️  Bill not found for invoice {$invoice->invoice_no}\n";
            $errorCount++;
            continue;
        }
        
        // Calculate correct amounts from details
        $calculated = calculateInvoiceAmountsFromDetails($invoiceId, $bill->sst_rate);
        
        // Update invoice amounts
        $invoice->pfee1_inv = $calculated['pfee1'];
        $invoice->pfee2_inv = $calculated['pfee2'];
        $invoice->sst_inv = $calculated['sst'];
        $invoice->reimbursement_amount = $calculated['reimbursement_amount'];
        $invoice->reimbursement_sst = $calculated['reimbursement_sst'];
        $invoice->amount = $calculated['total'];
        $invoice->save();
        
        $fixedCount++;
        
        if ($fixedCount % 20 == 0) {
            echo "  Fixed {$fixedCount} invoices...\n";
        }
    } catch (\Exception $e) {
        echo "❌ Error fixing invoice {$invoiceId}: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\nFixed: {$fixedCount}, Errors: {$errorCount}\n\n";

// Step 3: Recalculate transfer fee details
echo "Step 3: Recalculating transfer fee details...\n";

$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

$invoiceGroups = [];
foreach ($details as $detail) {
    $invoiceId = $detail->loan_case_invoice_main_id;
    if (!$invoiceId) continue;
    
    if (!isset($invoiceGroups[$invoiceId])) {
        $invoiceGroups[$invoiceId] = [];
    }
    $invoiceGroups[$invoiceId][] = $detail;
}

$totalPfee = 0;
$totalSst = 0;
$totalReimb = 0;
$totalReimbSst = 0;

DB::beginTransaction();

try {
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
            
            $totalPfee += $detail->transfer_amount;
            $totalSst += $detail->sst_amount;
            $totalReimb += $detail->reimbursement_amount;
            $totalReimbSst += $detail->reimbursement_sst_amount;
        } else {
            // Split invoice - distribute
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
                
                $totalPfee += $detail->transfer_amount;
                $totalSst += $detail->sst_amount;
                $totalReimb += $detail->reimbursement_amount;
                $totalReimbSst += $detail->reimbursement_sst_amount;
            }
        }
    }
    
    $transferFee = TransferFeeMain::find($transferFeeId);
    $transferFee->transfer_amount = round($totalPfee + $totalSst + $totalReimb + $totalReimbSst, 2);
    $transferFee->save();
    
    DB::commit();
    
    echo "✅ Transfer fee details updated\n\n";
    
    // Step 4: Verify totals
    echo "Step 4: Verifying totals...\n\n";
    
    echo "========================================\n";
    echo "FINAL TOTALS\n";
    echo "========================================\n";
    echo "Professional Fee:     " . number_format($totalPfee, 2) . "\n";
    echo "SST:                  " . number_format($totalSst, 2) . "\n";
    echo "Reimbursement:        " . number_format($totalReimb, 2) . "\n";
    echo "Reimbursement SST:    " . number_format($totalReimbSst, 2) . "\n\n";
    
    echo "Expected Values:\n";
    echo "  Professional Fee:    521,831.74\n";
    echo "  SST:                 41,746.47\n";
    echo "  Reimbursement:       66,373.63\n";
    echo "  Reimbursement SST:   5,309.91\n\n";
    
    $pfeeDiff = $totalPfee - 521831.74;
    $sstDiff = $totalSst - 41746.47;
    $reimbDiff = $totalReimb - 66373.63;
    $reimbSstDiff = $totalReimbSst - 5309.91;
    
    echo "Differences:\n";
    echo "  Professional Fee:    " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
    echo "  SST:                 " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
    echo "  Reimbursement:       " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
    echo "  Reimbursement SST:   " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";
    
    if (abs($pfeeDiff) <= 0.01 && abs($sstDiff) <= 0.01 && abs($reimbDiff) <= 0.01 && abs($reimbSstDiff) <= 0.01) {
        echo "✅ SUCCESS! All totals match expected values (within 0.01 tolerance)!\n";
    } else {
        echo "⚠️  Small differences remain. These may be due to rounding in split invoices.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}

// Helper function (same as InvoiceFixController)
function calculateInvoiceAmountsFromDetails($invoiceId, $sstRate) {
    $details = DB::table('loan_case_invoice_details as ild')
        ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
        ->where('ild.invoice_main_id', $invoiceId)
        ->where('ild.status', '<>', 99)
        ->select('ild.amount', 'ild.id as detail_id', 'ai.account_cat_id', 'ai.pfee1_item')
        ->get();

    $pfee1 = 0;
    $pfee2 = 0;
    $sst = 0;
    $reimbursement_amount = 0;
    $reimbursement_sst = 0;
    $total = 0;
    $sstRateDecimal = $sstRate / 100;

    foreach ($details as $detail) {
        if ($detail->account_cat_id == 1) {
            if ($detail->pfee1_item == 1) {
                $pfee1 += $detail->amount;
            } else {
                $pfee2 += $detail->amount;
            }
            
            $sst_calculation = $detail->amount * $sstRateDecimal;
            $sst_string = number_format($sst_calculation, 3, '.', '');
            
            if (substr($sst_string, -1) == '5') {
                $row_sst = floor($sst_calculation * 100) / 100;
            } else {
                $row_sst = round($sst_calculation, 2);
            }
            
            $sst += $row_sst;
            $total += $detail->amount + $row_sst;
        } elseif ($detail->account_cat_id == 4) {
            $reimbursement_amount += $detail->amount;
            
            $sst_calculation = $detail->amount * $sstRateDecimal;
            $sst_string = number_format($sst_calculation, 3, '.', '');
            
            if (substr($sst_string, -1) == '5') {
                $row_sst = floor($sst_calculation * 100) / 100;
            } else {
                $row_sst = round($sst_calculation, 2);
            }
            
            $reimbursement_sst += $row_sst;
            $total += $detail->amount + $row_sst;
        } else {
            $total += $detail->amount;
        }
    }

    return [
        'pfee1' => round($pfee1, 2),
        'pfee2' => round($pfee2, 2),
        'sst' => round($sst, 2),
        'reimbursement_amount' => round($reimbursement_amount, 2),
        'reimbursement_sst' => round($reimbursement_sst, 2),
        'total' => round($total, 2)
    ];
}

