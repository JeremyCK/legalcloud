<?php

/**
 * Sync E-Invoice 40 Detail Status with Billing Party Status
 * 
 * The detail page shows billing party status, but einvoice_details.client_profile_completed
 * should reflect whether all billing parties for that bill are completed.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;

echo "=== Syncing E-Invoice 40 Detail Status ===\n\n";

$main = EInvoiceMain::find(40);

if (!$main) {
    echo "❌ E-Invoice Main ID 40 not found\n";
    exit(1);
}

echo "E-Invoice Main ID: {$main->id} ({$main->ref_no})\n\n";

// Get the problematic detail
$detail = EInvoiceDetails::find(1589);

if (!$detail) {
    echo "❌ Detail ID 1589 not found\n";
    exit(1);
}

echo "Detail ID: {$detail->id}\n";
echo "Bill ID: {$detail->loan_case_main_bill_id}\n";
echo "Current client_profile_completed: " . ($detail->client_profile_completed == 1 ? "Completed" : "Pending") . "\n\n";

// Get all billing parties for this bill
$billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $detail->loan_case_main_bill_id)->get();

echo "Billing Parties for Bill ID {$detail->loan_case_main_bill_id}:\n";
$allCompleted = true;
foreach ($billingParties as $party) {
    $status = $party->completed ?? 0;
    echo "  Party ID {$party->id}: " . ($status == 1 ? "Completed" : "Pending") . "\n";
    if ($status != 1) {
        $allCompleted = false;
    }
}

echo "\nAll billing parties completed: " . ($allCompleted ? "Yes" : "No") . "\n";

if ($allCompleted) {
    echo "\n⚠️  All billing parties are completed, but detail is still marked as Pending!\n";
    echo "This is why the main shows as Pending even though detail page shows Completed.\n\n";
    
    echo "Updating detail status...\n";
    $detail->client_profile_completed = 1;
    $detail->save();
    echo "✅ Detail updated to Completed\n\n";
    
    // Now check if main should be updated
    $allDetails = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    $allDetailsCompleted = true;
    foreach ($allDetails as $d) {
        if (($d->client_profile_completed ?? 0) != 1) {
            $allDetailsCompleted = false;
            break;
        }
    }
    
    if ($allDetailsCompleted && $main->client_profile_completed != 1) {
        $main->client_profile_completed = 1;
        $main->save();
        echo "✅ Main status updated to Completed\n";
    }
} else {
    echo "\n⚠️  Not all billing parties are completed.\n";
    echo "Billing party 899 is still Pending (missing id_no and tin).\n";
    echo "The detail correctly shows as Pending.\n";
    echo "\nTo fix this, you need to complete billing party 899 by filling in:\n";
    echo "  - id_no (ID Number)\n";
    echo "  - tin (Tax Identification Number)\n";
}
