<?php
/**
 * Fix Transfer Fee Details from Corrected Invoice Amounts
 * This recalculates transfer_fee_details from the corrected invoice amounts
 * Run: php fix_transfer_fee_details_from_invoices.php [transfer_fee_id]
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

// Get transfer fee ID from command line
$transferFeeId = $argv[1] ?? 472; // Default to 472 (DP004-1025)

$transferFee = TransferFeeMain::find($transferFeeId);
if (!$transferFee) {
    echo "Transfer Fee ID {$transferFeeId} not found\n";
    exit(1);
}

echo "========================================\n";
echo "FIX TRANSFER FEE DETAILS FROM INVOICES\n";
echo "========================================\n\n";
echo "Transfer Fee ID: {$transferFeeId}\n";
if (isset($transferFee->transaction_id)) {
    echo "Transaction ID: {$transferFee->transaction_id}\n";
}
echo "\n";

// Get all transfer fee details
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

echo "Total Details: " . $details->count() . "\n\n";

if ($details->count() == 0) {
    echo "No details found for this transfer fee.\n";
    exit(1);
}

// Group details by invoice to handle split invoices
$invoiceGroups = [];
foreach ($details as $detail) {
    $invoiceId = $detail->loan_case_invoice_main_id;
    if (!$invoiceId) {
        echo "⚠️  Warning: Detail ID {$detail->id} has no invoice_main_id, skipping...\n";
        continue;
    }
    
    if (!isset($invoiceGroups[$invoiceId])) {
        $invoiceGroups[$invoiceId] = [];
    }
    $invoiceGroups[$invoiceId][] = $detail;
}

echo "Invoices in transfer fee: " . count($invoiceGroups) . "\n\n";

$updatedCount = 0;
$totalPfee = 0;
$totalSst = 0;
$totalReimb = 0;
$totalReimbSst = 0;

DB::beginTransaction();

try {
    foreach ($invoiceGroups as $invoiceId => $invoiceDetails) {
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            echo "⚠️  Invoice ID {$invoiceId} not found, skipping...\n";
            continue;
        }
        
        // Get invoice totals
        $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
        $invoiceSst = $invoice->sst_inv ?? 0;
        $invoiceReimb = $invoice->reimbursement_amount ?? 0;
        $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
        
        // If this invoice is split across multiple transfer fee details, distribute proportionally
        $detailCount = count($invoiceDetails);
        
        if ($detailCount == 1) {
            // Single detail - use invoice amounts directly
            $detail = $invoiceDetails[0];
            
            $oldPfee = $detail->transfer_amount ?? 0;
            $oldSst = $detail->sst_amount ?? 0;
            $oldReimb = $detail->reimbursement_amount ?? 0;
            $oldReimbSst = $detail->reimbursement_sst_amount ?? 0;
            
            $detail->transfer_amount = round($invoicePfee, 2);
            $detail->sst_amount = round($invoiceSst, 2);
            $detail->reimbursement_amount = round($invoiceReimb, 2);
            $detail->reimbursement_sst_amount = round($invoiceReimbSst, 2);
            $detail->save();
            
            $totalPfee += $detail->transfer_amount;
            $totalSst += $detail->sst_amount;
            $totalReimb += $detail->reimbursement_amount;
            $totalReimbSst += $detail->reimbursement_sst_amount;
            
            $updatedCount++;
            
            if (abs($oldPfee - $detail->transfer_amount) > 0.01 || 
                abs($oldSst - $detail->sst_amount) > 0.01 ||
                abs($oldReimb - $detail->reimbursement_amount) > 0.01 ||
                abs($oldReimbSst - $detail->reimbursement_sst_amount) > 0.01) {
                echo "Updated Invoice {$invoice->invoice_no}: Pfee={$detail->transfer_amount}, SST={$detail->sst_amount}, Reimb={$detail->reimbursement_amount}, ReimbSST={$detail->reimbursement_sst_amount}\n";
            }
        } else {
            // Multiple details for same invoice - distribute proportionally
            // Calculate total of current amounts to get proportions
            $currentTotalPfee = 0;
            $currentTotalSst = 0;
            $currentTotalReimb = 0;
            $currentTotalReimbSst = 0;
            
            foreach ($invoiceDetails as $d) {
                $currentTotalPfee += $d->transfer_amount ?? 0;
                $currentTotalSst += $d->sst_amount ?? 0;
                $currentTotalReimb += $d->reimbursement_amount ?? 0;
                $currentTotalReimbSst += $d->reimbursement_sst_amount ?? 0;
            }
            
            // Distribute amounts proportionally, ensuring last one gets remainder
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
                    // Distribute proportionally
                    if ($currentTotalPfee > 0) {
                        $proportion = ($detail->transfer_amount ?? 0) / $currentTotalPfee;
                        $detail->transfer_amount = round($invoicePfee * $proportion, 2);
                    } else {
                        $detail->transfer_amount = round($invoicePfee / $detailCount, 2);
                    }
                    
                    if ($currentTotalSst > 0) {
                        $proportion = ($detail->sst_amount ?? 0) / $currentTotalSst;
                        $detail->sst_amount = round($invoiceSst * $proportion, 2);
                    } else {
                        $detail->sst_amount = round($invoiceSst / $detailCount, 2);
                    }
                    
                    if ($currentTotalReimb > 0) {
                        $proportion = ($detail->reimbursement_amount ?? 0) / $currentTotalReimb;
                        $detail->reimbursement_amount = round($invoiceReimb * $proportion, 2);
                    } else {
                        $detail->reimbursement_amount = round($invoiceReimb / $detailCount, 2);
                    }
                    
                    if ($currentTotalReimbSst > 0) {
                        $proportion = ($detail->reimbursement_sst_amount ?? 0) / $currentTotalReimbSst;
                        $detail->reimbursement_sst_amount = round($invoiceReimbSst * $proportion, 2);
                    } else {
                        $detail->reimbursement_sst_amount = round($invoiceReimbSst / $detailCount, 2);
                    }
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
                
                $updatedCount++;
            }
            
            echo "Updated Split Invoice {$invoice->invoice_no} ({$detailCount} details): Pfee={$invoicePfee}, SST={$invoiceSst}, Reimb={$invoiceReimb}, ReimbSST={$invoiceReimbSst}\n";
        }
    }
    
    // Update transfer_fee_main total
    $transferFee->transfer_amount = round($totalPfee + $totalSst + $totalReimb + $totalReimbSst, 2);
    $transferFee->save();
    
    DB::commit();
    
    echo "\n========================================\n";
    echo "SUMMARY\n";
    echo "========================================\n";
    echo "Updated Details: {$updatedCount}\n";
    echo "Total Professional Fee: " . number_format($totalPfee, 2) . "\n";
    echo "Total SST:              " . number_format($totalSst, 2) . "\n";
    echo "Total Reimbursement:    " . number_format($totalReimb, 2) . "\n";
    echo "Total Reimbursement SST: " . number_format($totalReimbSst, 2) . "\n";
    echo "Total Transfer Amount:  " . number_format($transferFee->transfer_amount, 2) . "\n\n";
    
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
        echo "✅ All totals match expected values (within 0.01 tolerance)!\n";
    } else {
        echo "⚠️  Some differences remain. This may be due to rounding in split invoices.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
    exit(1);
}



