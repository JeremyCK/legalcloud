<?php

/**
 * Check E-Invoice Client Profile Status Discrepancy
 * 
 * Issue: List shows "Pending" but details records show "Completed"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;

echo "=== Checking E-Invoice Client Profile Status ===\n\n";

// Get all E-Invoice Main records
$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

echo "Checking " . $einvoiceMainRecords->count() . " E-Invoice Main records...\n\n";

foreach ($einvoiceMainRecords as $main) {
    echo "E-Invoice Main ID: {$main->id}\n";
    echo "  Ref No: {$main->ref_no}\n";
    echo "  Transaction ID: {$main->transaction_id}\n";
    
    // Check if client_profile_completed column exists
    $hasColumn = DB::getSchemaBuilder()->hasColumn('einvoice_main', 'client_profile_completed');
    echo "  Has client_profile_completed column: " . ($hasColumn ? "Yes" : "No") . "\n";
    
    if ($hasColumn) {
        $mainStatus = $main->client_profile_completed ?? null;
        echo "  Main client_profile_completed: " . ($mainStatus === 1 ? "Completed (1)" : ($mainStatus === 0 ? "Pending (0)" : "NULL")) . "\n";
    }
    
    // Get all EInvoiceDetails for this main record
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    echo "  Number of details records: " . $details->count() . "\n";
    
    if ($details->count() > 0) {
        $allDetailsCompleted = true;
        $detailsWithStatus = [];
        
        foreach ($details as $detail) {
            $detailStatus = $detail->client_profile_completed ?? null;
            $detailsWithStatus[] = [
                'id' => $detail->id,
                'status' => $detailStatus,
                'loan_case_invoice_id' => $detail->loan_case_invoice_id,
                'loan_case_main_bill_id' => $detail->loan_case_main_bill_id
            ];
            
            if ($detailStatus != 1) {
                $allDetailsCompleted = false;
            }
        }
        
        echo "  All details completed: " . ($allDetailsCompleted ? "Yes" : "No") . "\n";
        
        // Check billing parties
        $billIds = $details->pluck('loan_case_main_bill_id')->unique()->filter();
        echo "  Unique bill IDs: " . $billIds->count() . "\n";
        
        foreach ($billIds as $billId) {
            $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
            echo "    Bill ID {$billId}: " . $billingParties->count() . " billing parties\n";
            
            foreach ($billingParties as $party) {
                $partyCompleted = $party->completed ?? 0;
                echo "      Party ID {$party->id}: " . ($partyCompleted == 1 ? "Completed" : "Pending") . "\n";
            }
        }
        
        // Check if main status matches details status
        if ($hasColumn) {
            $mainStatus = $main->client_profile_completed ?? 0;
            if ($allDetailsCompleted && $mainStatus != 1) {
                echo "  ⚠️  MISMATCH: All details are completed but main shows " . ($mainStatus == 1 ? "Completed" : "Pending") . "\n";
                echo "  → Main should be updated to Completed (1)\n";
            } elseif (!$allDetailsCompleted && $mainStatus == 1) {
                echo "  ⚠️  MISMATCH: Main shows Completed but some details are not completed\n";
            } else {
                echo "  ✅ Status matches\n";
            }
        }
    }
    
    echo "\n";
}

// Check database schema
echo "=== Database Schema Check ===\n";
$columns = DB::select("SHOW COLUMNS FROM einvoice_main LIKE 'client_profile_completed'");
if (count($columns) > 0) {
    echo "Column exists in einvoice_main table\n";
    print_r($columns[0]);
} else {
    echo "❌ Column 'client_profile_completed' does NOT exist in einvoice_main table!\n";
    echo "This is why the list always shows 'Pending' - the column is missing.\n";
}
