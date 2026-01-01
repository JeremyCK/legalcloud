<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 491;

echo "=== Detailed Transfer Fee Investigation ===\n";
echo "Transfer Fee ID: {$transferFeeId}\n\n";

$transferFeeMain = TransferFeeMain::find($transferFeeId);
if (!$transferFeeMain) {
    die("Transfer fee not found!\n");
}

echo "Transaction ID: {$transferFeeMain->transaction_id}\n";
echo "Transfer Date: {$transferFeeMain->transfer_date}\n\n";

// Get all transfer fee details for this transfer fee
$transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

echo "=== Invoice-by-Invoice Analysis ===\n\n";

$allDiscrepancies = [];
$summary = [
    'total_invoices' => 0,
    'fully_transferred_elsewhere' => 0,
    'partially_transferred_elsewhere' => 0,
    'not_transferred_elsewhere' => 0,
    'discrepancies' => 0
];

foreach ($transferFeeDetails as $detail) {
    $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
    if (!$invoice) {
        continue;
    }
    
    $summary['total_invoices']++;
    
    // Get invoice amounts
    $pfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $sst = $invoice->sst_inv ?? 0;
    $reimb = $invoice->reimbursement_amount ?? 0;
    $reimbSst = $invoice->reimbursement_sst ?? 0;
    $expectedTotal = $pfee + $sst + $reimb + $reimbSst;
    
    // Get amounts transferred in THIS transfer fee
    $thisTransferredPfee = $detail->transfer_amount ?? 0;
    $thisTransferredSst = $detail->sst_amount ?? 0;
    $thisTransferredReimb = $detail->reimbursement_amount ?? 0;
    $thisTransferredReimbSst = $detail->reimbursement_sst_amount ?? 0;
    $thisTransferredTotal = $thisTransferredPfee + $thisTransferredSst + $thisTransferredReimb + $thisTransferredReimbSst;
    
    // Get amounts transferred in OTHER transfer fees
    $otherTransfers = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
        ->where('transfer_fee_main_id', '<>', $transferFeeId)
        ->where('status', '<>', 99)
        ->get();
    
    $otherTransferredPfee = $otherTransfers->sum('transfer_amount');
    $otherTransferredSst = $otherTransfers->sum('sst_amount');
    $otherTransferredReimb = $otherTransfers->sum('reimbursement_amount');
    $otherTransferredReimbSst = $otherTransfers->sum('reimbursement_sst_amount');
    $otherTransferredTotal = $otherTransferredPfee + $otherTransferredSst + $otherTransferredReimb + $otherTransferredReimbSst;
    
    // Get total transferred across ALL transfer fees
    $totalTransferredPfee = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
        ->where('status', '<>', 99)
        ->sum('transfer_amount');
    $totalTransferredSst = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
        ->where('status', '<>', 99)
        ->sum('sst_amount');
    $totalTransferredReimb = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
        ->where('status', '<>', 99)
        ->sum('reimbursement_amount');
    $totalTransferredReimbSst = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
        ->where('status', '<>', 99)
        ->sum('reimbursement_sst_amount');
    $totalTransferred = $totalTransferredPfee + $totalTransferredSst + $totalTransferredReimb + $totalTransferredReimbSst;
    
    // Calculate remaining
    $remainingPfee = $pfee - $totalTransferredPfee;
    $remainingSst = $sst - $totalTransferredSst;
    $remainingReimb = $reimb - $totalTransferredReimb;
    $remainingReimbSst = $reimbSst - $totalTransferredReimbSst;
    $remainingTotal = $remainingPfee + $remainingSst + $remainingReimb + $remainingReimbSst;
    
    // Check status
    $isFullyTransferredElsewhere = ($otherTransferredTotal > 0 && $thisTransferredTotal == 0);
    $isPartiallyTransferredElsewhere = ($otherTransferredTotal > 0 && $thisTransferredTotal > 0);
    $isNotTransferredElsewhere = ($otherTransferredTotal == 0);
    
    if ($isFullyTransferredElsewhere) {
        $summary['fully_transferred_elsewhere']++;
    } elseif ($isPartiallyTransferredElsewhere) {
        $summary['partially_transferred_elsewhere']++;
    } else {
        $summary['not_transferred_elsewhere']++;
    }
    
    // Check for discrepancies
    $calculatedTotal = $totalTransferred + $remainingTotal;
    $hasDiscrepancy = abs($expectedTotal - $calculatedTotal) > 0.01;
    
    if ($hasDiscrepancy) {
        $summary['discrepancies']++;
    }
    
    // Get case ref
    $caseRef = DB::table('loan_case_bill_main')
        ->where('id', $invoice->loan_case_main_bill_id)
        ->value('case_id');
    $caseRefNo = $caseRef ? DB::table('loan_case')->where('id', $caseRef)->value('case_ref_no') : 'N/A';
    
    // Store for detailed report
    $allDiscrepancies[] = [
        'invoice_id' => $invoice->id,
        'invoice_no' => $invoice->invoice_no ?? 'N/A',
        'case_ref_no' => $caseRefNo,
        'pfee' => $pfee,
        'sst' => $sst,
        'reimb' => $reimb,
        'reimb_sst' => $reimbSst,
        'expected_total' => $expectedTotal,
        'this_transferred_pfee' => $thisTransferredPfee,
        'this_transferred_sst' => $thisTransferredSst,
        'this_transferred_reimb' => $thisTransferredReimb,
        'this_transferred_reimb_sst' => $thisTransferredReimbSst,
        'this_transferred_total' => $thisTransferredTotal,
        'other_transferred_pfee' => $otherTransferredPfee,
        'other_transferred_sst' => $otherTransferredSst,
        'other_transferred_reimb' => $otherTransferredReimb,
        'other_transferred_reimb_sst' => $otherTransferredReimbSst,
        'other_transferred_total' => $otherTransferredTotal,
        'total_transferred_pfee' => $totalTransferredPfee,
        'total_transferred_sst' => $totalTransferredSst,
        'total_transferred_reimb' => $totalTransferredReimb,
        'total_transferred_reimb_sst' => $totalTransferredReimbSst,
        'total_transferred' => $totalTransferred,
        'remaining_pfee' => $remainingPfee,
        'remaining_sst' => $remainingSst,
        'remaining_reimb' => $remainingReimb,
        'remaining_reimb_sst' => $remainingReimbSst,
        'remaining_total' => $remainingTotal,
        'has_discrepancy' => $hasDiscrepancy,
        'is_fully_transferred_elsewhere' => $isFullyTransferredElsewhere,
        'is_partially_transferred_elsewhere' => $isPartiallyTransferredElsewhere,
        'other_transfer_fee_ids' => $otherTransfers->pluck('transfer_fee_main_id')->unique()->toArray(),
        'other_transfer_details' => $otherTransfers->map(function($t) {
            $tf = TransferFeeMain::find($t->transfer_fee_main_id);
            return [
                'transfer_fee_id' => $t->transfer_fee_main_id,
                'transaction_id' => $tf ? $tf->transaction_id : 'N/A',
                'transfer_date' => $tf ? $tf->transfer_date : 'N/A',
                'pfee' => $t->transfer_amount,
                'sst' => $t->sst_amount,
                'reimb' => $t->reimbursement_amount,
                'reimb_sst' => $t->reimbursement_sst_amount
            ];
        })->toArray()
    ];
}

// Print summary
echo "=== Summary ===\n";
echo "Total Invoices: {$summary['total_invoices']}\n";
echo "Fully Transferred Elsewhere: {$summary['fully_transferred_elsewhere']}\n";
echo "Partially Transferred Elsewhere: {$summary['partially_transferred_elsewhere']}\n";
echo "Not Transferred Elsewhere: {$summary['not_transferred_elsewhere']}\n";
echo "Discrepancies: {$summary['discrepancies']}\n\n";

// Print invoices that don't match
echo "=== Invoices That Don't Match Formula ===\n";
echo "Formula: pfee + sst + reimb + reimbsst = transferredBal + transferredSst + remaining\n\n";

$mismatched = [];
foreach ($allDiscrepancies as $disc) {
    // Calculate as shown in view
    $transferredBal = $disc['this_transferred_pfee'] + $disc['this_transferred_reimb'];
    $transferredSst = $disc['this_transferred_sst'] + $disc['this_transferred_reimb_sst'];
    $remaining = $disc['remaining_pfee'] + $disc['remaining_sst'] + $disc['remaining_reimb'] + $disc['remaining_reimb_sst'];
    
    $expected = $disc['expected_total'];
    $actual = $transferredBal + $transferredSst + $remaining;
    $difference = abs($expected - $actual);
    
    if ($difference > 0.01 || $disc['is_fully_transferred_elsewhere'] || $disc['is_partially_transferred_elsewhere']) {
        $mismatched[] = [
            'disc' => $disc,
            'transferred_bal' => $transferredBal,
            'transferred_sst' => $transferredSst,
            'remaining' => $remaining,
            'expected' => $expected,
            'actual' => $actual,
            'difference' => $difference
        ];
    }
}

echo "Found " . count($mismatched) . " invoices that don't match or have other transfers\n\n";

foreach ($mismatched as $index => $m) {
    $d = $m['disc'];
    echo "--- Invoice #" . ($index + 1) . " ---\n";
    echo "Invoice ID: {$d['invoice_id']}\n";
    echo "Invoice No: {$d['invoice_no']}\n";
    echo "Case Ref: {$d['case_ref_no']}\n\n";
    
    echo "Original Amounts:\n";
    echo "  Pfee: " . number_format($d['pfee'], 2) . "\n";
    echo "  SST: " . number_format($d['sst'], 2) . "\n";
    echo "  Reimb: " . number_format($d['reimb'], 2) . "\n";
    echo "  Reimb SST: " . number_format($d['reimb_sst'], 2) . "\n";
    echo "  Expected Total: " . number_format($d['expected_total'], 2) . "\n\n";
    
    echo "This Transfer Fee ({$transferFeeId}):\n";
    echo "  Transferred Pfee: " . number_format($d['this_transferred_pfee'], 2) . "\n";
    echo "  Transferred SST: " . number_format($d['this_transferred_sst'], 2) . "\n";
    echo "  Transferred Reimb: " . number_format($d['this_transferred_reimb'], 2) . "\n";
    echo "  Transferred Reimb SST: " . number_format($d['this_transferred_reimb_sst'], 2) . "\n";
    echo "  Transferred Bal (view): " . number_format($m['transferred_bal'], 2) . "\n";
    echo "  Transferred SST (view): " . number_format($m['transferred_sst'], 2) . "\n";
    echo "  Total: " . number_format($d['this_transferred_total'], 2) . "\n\n";
    
    if ($d['is_fully_transferred_elsewhere'] || $d['is_partially_transferred_elsewhere']) {
        echo "⚠️  TRANSFERRED IN OTHER TRANSFER FEES:\n";
        echo "  Other Transfer Fee IDs: " . implode(', ', $d['other_transfer_fee_ids']) . "\n";
        echo "  Other Transferred Pfee: " . number_format($d['other_transferred_pfee'], 2) . "\n";
        echo "  Other Transferred SST: " . number_format($d['other_transferred_sst'], 2) . "\n";
        echo "  Other Transferred Reimb: " . number_format($d['other_transferred_reimb'], 2) . "\n";
        echo "  Other Transferred Reimb SST: " . number_format($d['other_transferred_reimb_sst'], 2) . "\n";
        echo "  Other Transferred Total: " . number_format($d['other_transferred_total'], 2) . "\n\n";
        
        foreach ($d['other_transfer_details'] as $other) {
            echo "    Transfer Fee ID: {$other['transfer_fee_id']}\n";
            echo "      Transaction: {$other['transaction_id']}, Date: {$other['transfer_date']}\n";
            echo "      Amounts: Pfee=" . number_format($other['pfee'], 2) . 
                 ", SST=" . number_format($other['sst'], 2) . 
                 ", Reimb=" . number_format($other['reimb'], 2) . 
                 ", ReimbSST=" . number_format($other['reimb_sst'], 2) . "\n";
        }
        echo "\n";
    }
    
    echo "Total Transferred (ALL transfer fees):\n";
    echo "  Pfee: " . number_format($d['total_transferred_pfee'], 2) . "\n";
    echo "  SST: " . number_format($d['total_transferred_sst'], 2) . "\n";
    echo "  Reimb: " . number_format($d['total_transferred_reimb'], 2) . "\n";
    echo "  Reimb SST: " . number_format($d['total_transferred_reimb_sst'], 2) . "\n";
    echo "  Total: " . number_format($d['total_transferred'], 2) . "\n\n";
    
    echo "Remaining (not yet transferred):\n";
    echo "  Pfee: " . number_format($d['remaining_pfee'], 2) . "\n";
    echo "  SST: " . number_format($d['remaining_sst'], 2) . "\n";
    echo "  Reimb: " . number_format($d['remaining_reimb'], 2) . "\n";
    echo "  Reimb SST: " . number_format($d['remaining_reimb_sst'], 2) . "\n";
    echo "  Total: " . number_format($d['remaining_total'], 2) . "\n\n";
    
    echo "Formula Check:\n";
    echo "  Expected: " . number_format($m['expected'], 2) . "\n";
    echo "  Actual (transferredBal + transferredSst + remaining): " . number_format($m['actual'], 2) . "\n";
    echo "  Difference: " . number_format($m['difference'], 2) . "\n";
    
    if ($m['difference'] > 0.01) {
        echo "  ❌ MISMATCH!\n";
    } else {
        echo "  ✅ Matches\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
}

echo "=== End of Report ===\n";

