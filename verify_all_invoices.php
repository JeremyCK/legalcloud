<?php
/**
 * Standalone Invoice Verification Script
 * Run this from command line: php verify_all_invoices.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;

// All invoice numbers to verify
$invoiceNumbers = [
    'DP20000817', 'DP20000826', 'DP20000829', 'DP20000840', 'DP20000844', 'DP20000845', 'DP20000849',
    'DP20000964', 'DP20000965', 'DP20000966',
    'DP20000869', 'DP20000873', 'DP20000874', 'DP20000875', 'DP20000877', 'DP20000878', 'DP20000879',
    'DP20000941', 'DP20000942', 'DP20000943', 'DP20000884', 'DP20000885', 'DP20000986', 'DP20000987',
    'DP20000870', 'DP20000896', 'DP20000897', 'DP20000871', 'DP20000891', 'DP20000892', 'DP20000934',
    'DP20000935', 'DP20000936', 'DP20000944', 'DP20000945', 'DP20000948', 'DP20000949', 'DP20000952',
    'DP20000953', 'DP20000982', 'DP20000995', 'DP20000998',
    'DP20001168', 'DP20001170',
    'DP20001000', 'DP20001012', 'DP20001013', 'DP20001014', 'DP20001015', 'DP20001025', 'DP20001026',
    'DP20001035', 'DP20001040', 'DP20001041', 'DP20001042', 'DP20001057', 'DP20001058', 'DP20001065',
    'DP20001066', 'DP20001067', 'DP20001070', 'DP20001071', 'DP20001074', 'DP20001085', 'DP20001089',
    'DP20001090', 'DP20001091', 'DP20001092', 'DP20001095', 'DP20001096', 'DP20001110', 'DP20001111',
    'DP20001119', 'DP20001130', 'DP20001142', 'DP20001145', 'DP20001146', 'DP20001147', 'DP20001148',
    'DP20001149', 'DP20001150', 'DP20001151', 'DP20001155', 'DP20001158', 'DP20001159', 'DP20001160'
];

// Function to calculate invoice amounts from details
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
            // Professional fee
            if ($detail->pfee1_item == 1) {
                $pfee1 += $detail->amount;
            } else {
                $pfee2 += $detail->amount;
            }
            
            // Apply special rounding rule for SST: round DOWN if 3rd decimal is 5
            $sst_calculation = $detail->amount * $sstRateDecimal;
            $sst_string = number_format($sst_calculation, 3, '.', '');
            
            if (substr($sst_string, -1) == '5') {
                $row_sst = floor($sst_calculation * 100) / 100; // Round down
            } else {
                $row_sst = round($sst_calculation, 2); // Normal rounding
            }
            
            $sst += $row_sst;
            $total += $detail->amount + $row_sst;
        } elseif ($detail->account_cat_id == 4) {
            // Reimbursement
            $reimbursement_amount += $detail->amount;
            
            // Apply special rounding rule for reimbursement SST too
            $sst_calculation = $detail->amount * $sstRateDecimal;
            $sst_string = number_format($sst_calculation, 3, '.', '');
            
            if (substr($sst_string, -1) == '5') {
                $row_sst = floor($sst_calculation * 100) / 100; // Round down
            } else {
                $row_sst = round($sst_calculation, 2); // Normal rounding
            }
            
            $reimbursement_sst += $row_sst;
            $total += $detail->amount + $row_sst;
        } else {
            // Other categories
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

echo "========================================\n";
echo "INVOICE VERIFICATION REPORT\n";
echo "========================================\n\n";
echo "Total Invoices to Verify: " . count($invoiceNumbers) . "\n\n";

$results = [];
$fixedCount = 0;
$issueCount = 0;
$notFoundCount = 0;
$tolerance = 0.01;

foreach ($invoiceNumbers as $invoiceNo) {
    $invoice = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as lc', 'lc.id', '=', 'bm.case_id')
        ->where('im.invoice_no', $invoiceNo)
        ->where('im.status', '<>', 99)
        ->select(
            'im.id',
            'im.invoice_no',
            'im.pfee1_inv',
            'im.pfee2_inv',
            'im.sst_inv',
            'im.reimbursement_amount',
            'im.reimbursement_sst',
            'im.amount',
            'bm.sst_rate',
            'lc.case_ref_no'
        )
        ->first();
    
    if (!$invoice) {
        $results[] = [
            'invoice_no' => $invoiceNo,
            'status' => 'not_found',
            'message' => 'Invoice not found'
        ];
        $notFoundCount++;
        continue;
    }
    
    // Calculate expected values
    $calculated = calculateInvoiceAmountsFromDetails($invoice->id, $invoice->sst_rate);
    
    $issues = [];
    
    // Check each field
    if (abs($invoice->pfee1_inv - $calculated['pfee1']) > $tolerance) {
        $issues[] = [
            'field' => 'pfee1_inv',
            'stored' => $invoice->pfee1_inv,
            'calculated' => $calculated['pfee1'],
            'difference' => round($invoice->pfee1_inv - $calculated['pfee1'], 2)
        ];
    }
    
    if (abs($invoice->pfee2_inv - $calculated['pfee2']) > $tolerance) {
        $issues[] = [
            'field' => 'pfee2_inv',
            'stored' => $invoice->pfee2_inv,
            'calculated' => $calculated['pfee2'],
            'difference' => round($invoice->pfee2_inv - $calculated['pfee2'], 2)
        ];
    }
    
    if (abs($invoice->sst_inv - $calculated['sst']) > $tolerance) {
        $issues[] = [
            'field' => 'sst_inv',
            'stored' => $invoice->sst_inv,
            'calculated' => $calculated['sst'],
            'difference' => round($invoice->sst_inv - $calculated['sst'], 2)
        ];
    }
    
    $storedReimb = $invoice->reimbursement_amount ?? 0;
    if (abs($storedReimb - $calculated['reimbursement_amount']) > $tolerance) {
        $issues[] = [
            'field' => 'reimbursement_amount',
            'stored' => $storedReimb,
            'calculated' => $calculated['reimbursement_amount'],
            'difference' => round($storedReimb - $calculated['reimbursement_amount'], 2)
        ];
    }
    
    $storedReimbSst = $invoice->reimbursement_sst ?? 0;
    if (abs($storedReimbSst - $calculated['reimbursement_sst']) > $tolerance) {
        $issues[] = [
            'field' => 'reimbursement_sst',
            'stored' => $storedReimbSst,
            'calculated' => $calculated['reimbursement_sst'],
            'difference' => round($storedReimbSst - $calculated['reimbursement_sst'], 2)
        ];
    }
    
    if (abs($invoice->amount - $calculated['total']) > $tolerance) {
        $issues[] = [
            'field' => 'amount',
            'stored' => $invoice->amount,
            'calculated' => $calculated['total'],
            'difference' => round($invoice->amount - $calculated['total'], 2)
        ];
    }
    
    $status = count($issues) === 0 ? 'fixed' : 'has_issues';
    $message = count($issues) === 0 
        ? 'All values match calculated values' 
        : count($issues) . ' field(s) have mismatches';
    
    $results[] = [
        'invoice_no' => $invoiceNo,
        'case_ref_no' => $invoice->case_ref_no,
        'status' => $status,
        'message' => $message,
        'issues' => $issues,
        'stored' => [
            'pfee1' => $invoice->pfee1_inv,
            'pfee2' => $invoice->pfee2_inv,
            'sst' => $invoice->sst_inv,
            'reimbursement' => $storedReimb,
            'reimbursement_sst' => $storedReimbSst,
            'amount' => $invoice->amount
        ],
        'calculated' => $calculated
    ];
    
    if ($status === 'fixed') {
        $fixedCount++;
    } else {
        $issueCount++;
    }
}

// Print Summary
echo "========================================\n";
echo "VERIFICATION SUMMARY\n";
echo "========================================\n";
echo "✅ Fixed:           {$fixedCount}\n";
echo "⚠️  With Issues:     {$issueCount}\n";
echo "❌ Not Found:        {$notFoundCount}\n";
echo "📋 Total:            " . count($invoiceNumbers) . "\n";
echo "========================================\n\n";

// Print Detailed Results
if ($issueCount > 0 || $notFoundCount > 0) {
    echo "DETAILED RESULTS\n";
    echo "========================================\n\n";
    
    foreach ($results as $result) {
        if ($result['status'] === 'fixed') {
            echo "✅ {$result['invoice_no']}";
            if ($result['case_ref_no']) {
                echo " ({$result['case_ref_no']})";
            }
            echo " - {$result['message']}\n";
        } elseif ($result['status'] === 'not_found') {
            echo "❌ {$result['invoice_no']} - {$result['message']}\n";
        } else {
            echo "⚠️  {$result['invoice_no']}";
            if ($result['case_ref_no']) {
                echo " ({$result['case_ref_no']})";
            }
            echo " - {$result['message']}\n";
            
            foreach ($result['issues'] as $issue) {
                echo "   • {$issue['field']}: Stored={$issue['stored']}, Calculated={$issue['calculated']}, Diff={$issue['difference']}\n";
            }
        }
        echo "\n";
    }
} else {
    echo "🎉 ALL INVOICES ARE FIXED!\n";
    echo "All " . count($invoiceNumbers) . " invoices have been verified and are correct.\n";
}

// Save results to file
$outputFile = 'verification_results_' . date('Y-m-d_His') . '.txt';
$fileContent = ob_get_contents();
if ($fileContent === false) {
    // If output buffering not active, capture manually
    $fileContent = "INVOICE VERIFICATION REPORT\n";
    $fileContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $fileContent .= "Summary:\n";
    $fileContent .= "✅ Fixed: {$fixedCount}\n";
    $fileContent .= "⚠️  With Issues: {$issueCount}\n";
    $fileContent .= "❌ Not Found: {$notFoundCount}\n\n";
    
    foreach ($results as $result) {
        $fileContent .= "{$result['invoice_no']} - {$result['status']} - {$result['message']}\n";
        if (!empty($result['issues'])) {
            foreach ($result['issues'] as $issue) {
                $fileContent .= "  {$issue['field']}: Stored={$issue['stored']}, Calculated={$issue['calculated']}, Diff={$issue['difference']}\n";
            }
        }
        $fileContent .= "\n";
    }
}
file_put_contents($outputFile, $fileContent);
echo "\n\nResults also saved to: {$outputFile}\n";
