<?php
/**
 * Diagnostic script to check SST Record ID 96
 * Run this from Laravel tinker: php artisan tinker
 * Then: require 'check_sst_record_96.php';
 */

use Illuminate\Support\Facades\DB;
use App\Models\SSTMain;
use App\Models\SSTDetails;
use App\Models\LoanCaseInvoiceMain;

echo "=== CHECKING SST RECORD ID 96 ===\n\n";

// 1. Check SST Main Record
echo "1. SST MAIN RECORD:\n";
$sstMain = SSTMain::find(96);
if ($sstMain) {
    echo "   ID: {$sstMain->id}\n";
    echo "   Payment Date: {$sstMain->payment_date}\n";
    echo "   Transaction ID: {$sstMain->transaction_id}\n";
    echo "   Amount (stored): " . number_format($sstMain->amount ?? 0, 2) . "\n";
    echo "   Status: {$sstMain->status}\n";
    echo "   Is Reconciled: " . ($sstMain->is_recon ?? 0) . "\n";
    echo "   Created At: {$sstMain->created_at}\n";
    echo "   Updated At: {$sstMain->updated_at}\n\n";
} else {
    echo "   ❌ SST Record 96 not found!\n\n";
    exit;
}

// 2. Check SST Details
echo "2. SST DETAILS (Invoices in this SST record):\n";
$sstDetails = SSTDetails::where('sst_main_id', 96)->get();
echo "   Total Invoices: " . $sstDetails->count() . "\n\n";

if ($sstDetails->count() > 0) {
    $calculatedTotalSST = 0;
    $calculatedTotalReimbSST = 0;
    $calculatedGrandTotal = 0;
    
    foreach ($sstDetails as $index => $detail) {
        $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
        
        if ($invoice) {
            $sstAmount = $detail->amount ?? 0;
            $reimbursementSst = $invoice->reimbursement_sst ?? 0;
            $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
            $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
            $rowTotal = $sstAmount + $remainingReimbSst;
            
            $calculatedTotalSST += $sstAmount;
            $calculatedTotalReimbSST += $remainingReimbSst;
            $calculatedGrandTotal += $rowTotal;
            
            echo "   Invoice #" . ($index + 1) . ":\n";
            echo "      Invoice ID: {$detail->loan_case_invoice_main_id}\n";
            echo "      Invoice No: " . ($invoice->invoice_no ?? 'N/A') . "\n";
            echo "      SST Amount (from sst_details): " . number_format($sstAmount, 2) . "\n";
            echo "      Reimbursement SST (full): " . number_format($reimbursementSst, 2) . "\n";
            echo "      Transferred Reimb SST: " . number_format($transferredReimbSst, 2) . "\n";
            echo "      Remaining Reimb SST: " . number_format($remainingReimbSst, 2) . "\n";
            echo "      Row Total (SST + Remaining Reimb): " . number_format($rowTotal, 2) . "\n";
            echo "      ---\n";
        } else {
            echo "   Invoice #" . ($index + 1) . ": ❌ Invoice ID {$detail->loan_case_invoice_main_id} not found!\n";
        }
    }
    
    echo "\n3. CALCULATED TOTALS:\n";
    echo "   Total SST: " . number_format($calculatedTotalSST, 2) . "\n";
    echo "   Total Remaining Reimb SST: " . number_format($calculatedTotalReimbSST, 2) . "\n";
    echo "   Grand Total (SST + Reimb SST): " . number_format($calculatedGrandTotal, 2) . "\n\n";
    
    echo "4. COMPARISON:\n";
    echo "   Stored Amount (sst_main.amount): " . number_format($sstMain->amount ?? 0, 2) . "\n";
    echo "   Calculated Grand Total: " . number_format($calculatedGrandTotal, 2) . "\n";
    
    $difference = abs(($sstMain->amount ?? 0) - $calculatedGrandTotal);
    if ($difference < 0.01) {
        echo "   ✅ Amounts match!\n";
    } else {
        echo "   ⚠️  MISMATCH! Difference: " . number_format($difference, 2) . "\n";
        echo "   💡 Suggestion: Run updateSSTV2 to recalculate and save the correct total.\n";
    }
} else {
    echo "   ⚠️  No invoices found in this SST record!\n";
}

echo "\n5. QUERY TO FIX (if needed):\n";
echo "   Visit: http://127.0.0.1:8000/sst-v2-edit/96\n";
echo "   Click 'Update SST' button to recalculate and save the total.\n\n";

echo "=== END OF DIAGNOSTIC ===\n";





