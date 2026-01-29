<?php

/**
 * Check E-Invoice 41 Status in Detail
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;

echo "=== Checking E-Invoice 41 Status ===\n\n";

$main = EInvoiceMain::find(41);

if (!$main) {
    echo "❌ E-Invoice Main ID 41 not found\n";
    exit(1);
}

echo "E-Invoice Main ID: {$main->id}\n";
echo "Ref No: {$main->ref_no}\n";
echo "Transaction ID: {$main->transaction_id}\n";
echo "Current client_profile_completed: " . ($main->client_profile_completed == 1 ? "Completed (1)" : "Pending (0)") . "\n\n";

// Get all EInvoiceDetails for this main record
$details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();

echo "Total Details Records: " . $details->count() . "\n\n";

$pendingDetails = [];
$completedDetails = [];
$detailsWithNoBillingParties = [];

foreach ($details as $detail) {
    $detailStatus = $detail->client_profile_completed ?? 0;
    $billId = $detail->loan_case_main_bill_id;
    
    $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
    
    $allPartiesCompleted = true;
    if ($billingParties->count() == 0) {
        $allPartiesCompleted = false;
        $detailsWithNoBillingParties[] = [
            'detail_id' => $detail->id,
            'bill_id' => $billId,
            'invoice_id' => $detail->loan_case_invoice_id
        ];
    } else {
        foreach ($billingParties as $party) {
            if (($party->completed ?? 0) != 1) {
                $allPartiesCompleted = false;
                break;
            }
        }
    }
    
    $shouldBeCompleted = $allPartiesCompleted && $billingParties->count() > 0;
    
    $info = [
        'detail_id' => $detail->id,
        'bill_id' => $billId,
        'invoice_id' => $detail->loan_case_invoice_id,
        'current_status' => $detailStatus,
        'should_be' => $shouldBeCompleted ? 1 : 0,
        'billing_parties_count' => $billingParties->count(),
        'all_parties_completed' => $allPartiesCompleted,
        'mismatch' => $detailStatus != ($shouldBeCompleted ? 1 : 0)
    ];
    
    if ($detailStatus == 1) {
        $completedDetails[] = $info;
    } else {
        $pendingDetails[] = $info;
    }
}

echo "=== Summary ===\n";
echo "Completed Details: " . count($completedDetails) . "\n";
echo "Pending Details: " . count($pendingDetails) . "\n";
echo "Details with No Billing Parties: " . count($detailsWithNoBillingParties) . "\n\n";

if (count($pendingDetails) > 0) {
    echo "=== Pending Details (showing first 10) ===\n";
    $shown = 0;
    foreach ($pendingDetails as $pending) {
        if ($shown >= 10) break;
        
        echo "Detail ID {$pending['detail_id']}: ";
        echo "Bill ID={$pending['bill_id']}, ";
        echo "Invoice ID={$pending['invoice_id']}, ";
        echo "Billing Parties={$pending['billing_parties_count']}, ";
        echo "All Completed=" . ($pending['all_parties_completed'] ? "Yes" : "No");
        if ($pending['mismatch']) {
            echo " ⚠️ MISMATCH (should be " . ($pending['should_be'] == 1 ? "Completed" : "Pending") . ")";
        }
        echo "\n";
        $shown++;
    }
    if (count($pendingDetails) > 10) {
        echo "... and " . (count($pendingDetails) - 10) . " more\n";
    }
    echo "\n";
}

if (count($detailsWithNoBillingParties) > 0) {
    echo "=== Details with No Billing Parties (these can't be completed) ===\n";
    $shown = 0;
    foreach ($detailsWithNoBillingParties as $noParty) {
        if ($shown >= 10) break;
        echo "Detail ID {$noParty['detail_id']}: Bill ID={$noParty['bill_id']}, Invoice ID={$noParty['invoice_id']}\n";
        $shown++;
    }
    if (count($detailsWithNoBillingParties) > 10) {
        echo "... and " . (count($detailsWithNoBillingParties) - 10) . " more\n";
    }
    echo "\n";
}

// Check if main should be completed
$allDetailsCompleted = true;
foreach ($details as $detail) {
    if (($detail->client_profile_completed ?? 0) != 1) {
        $allDetailsCompleted = false;
        break;
    }
}

echo "=== Main Status Analysis ===\n";
echo "All details completed: " . ($allDetailsCompleted ? "Yes" : "No") . "\n";
echo "Main should be: " . ($allDetailsCompleted ? "Completed (1)" : "Pending (0)") . "\n";
echo "Main currently is: " . ($main->client_profile_completed == 1 ? "Completed (1)" : "Pending (0)") . "\n";

if ($allDetailsCompleted && $main->client_profile_completed != 1) {
    echo "\n⚠️  ISSUE: All details are completed but main is still Pending!\n";
    echo "This is why it shows as Pending in the list.\n";
    
    // Fix it
    $main->client_profile_completed = 1;
    $main->save();
    echo "\n✅ Fixed! Updated main status to Completed.\n";
} elseif (!$allDetailsCompleted) {
    echo "\n⚠️  ISSUE: Not all details are completed.\n";
    echo "There are " . count($pendingDetails) . " details that are still Pending.\n";
    if (count($detailsWithNoBillingParties) > 0) {
        echo "Note: " . count($detailsWithNoBillingParties) . " details have no billing parties, so they can't be completed.\n";
    }
}
