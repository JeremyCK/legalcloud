<?php

/**
 * Check E-Invoice 40 - Detail Page vs Main Status
 * 
 * The detail page shows all as "Completed" but main list shows "Pending"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;

echo "=== Checking E-Invoice 40 Detail vs Main Status ===\n\n";

$main = EInvoiceMain::find(40);

if (!$main) {
    echo "❌ E-Invoice Main ID 40 not found\n";
    exit(1);
}

echo "E-Invoice Main ID: {$main->id} ({$main->ref_no})\n";
echo "Main client_profile_completed (from database): " . ($main->client_profile_completed == 1 ? "Completed (1)" : "Pending (0)") . "\n\n";

// Get all details
$details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();

echo "Total Details: " . $details->count() . "\n\n";

$completedCount = 0;
$pendingCount = 0;

foreach ($details as $detail) {
    $detailStatus = $detail->client_profile_completed ?? 0;
    
    if ($detailStatus == 1) {
        $completedCount++;
    } else {
        $pendingCount++;
        
        // Check why it's pending
        $billId = $detail->loan_case_main_bill_id;
        $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
        
        echo "Detail ID {$detail->id} is Pending:\n";
        echo "  Bill ID: {$billId}\n";
        echo "  Billing Parties: " . $billingParties->count() . "\n";
        
        if ($billingParties->count() > 0) {
            foreach ($billingParties as $party) {
                $partyStatus = $party->completed ?? 0;
                echo "    Party ID {$party->id}: " . ($partyStatus == 1 ? "Completed" : "Pending") . "\n";
            }
        } else {
            echo "    No billing parties found for this bill\n";
        }
        echo "\n";
    }
}

echo "=== Summary ===\n";
echo "Details marked as Completed: {$completedCount}\n";
echo "Details marked as Pending: {$pendingCount}\n";
echo "Main status: " . ($main->client_profile_completed == 1 ? "Completed" : "Pending") . "\n\n";

// Check if all details are actually completed
$allDetailsCompleted = true;
foreach ($details as $detail) {
    if (($detail->client_profile_completed ?? 0) != 1) {
        $allDetailsCompleted = false;
        break;
    }
}

if ($allDetailsCompleted && $main->client_profile_completed != 1) {
    echo "⚠️  ISSUE FOUND!\n";
    echo "All details are marked as 'Completed' in the database,\n";
    echo "but the main record is still marked as 'Pending'.\n";
    echo "This is a sync issue - the main status wasn't updated.\n\n";
    
    echo "Fixing main status...\n";
    $main->client_profile_completed = 1;
    $main->save();
    
    echo "✅ Fixed! Main status updated to Completed.\n";
} elseif (!$allDetailsCompleted) {
    echo "⚠️  Not all details are completed.\n";
    echo "There are {$pendingCount} details still marked as Pending.\n";
    echo "The main status correctly shows as Pending.\n";
} else {
    echo "✅ Status is correct - all details and main are Completed.\n";
}
