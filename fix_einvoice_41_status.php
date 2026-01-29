<?php

/**
 * Fix E-Invoice 41 Status
 * 
 * Mark details without billing parties as "Completed" if:
 * 1. They have no billing parties for their bill, OR
 * 2. The invoice's bill_party_id points to a completed billing party (even if different bill)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;
use App\Models\LoanCaseInvoiceMain;

echo "=== Fixing E-Invoice 41 Status ===\n\n";

$main = EInvoiceMain::find(41);

if (!$main) {
    echo "❌ E-Invoice Main ID 41 not found\n";
    exit(1);
}

echo "E-Invoice Main ID: {$main->id} ({$main->ref_no})\n\n";

// Get all details
$details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();

$updated = 0;

foreach ($details as $detail) {
    $billId = $detail->loan_case_main_bill_id;
    $invoiceId = $detail->loan_case_invoice_id;
    
    // Check billing parties for this bill
    $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
    
    $shouldBeCompleted = false;
    
    if ($billingParties->count() > 0) {
        // Has billing parties - check if all are completed
        $allCompleted = true;
        foreach ($billingParties as $party) {
            if (($party->completed ?? 0) != 1) {
                $allCompleted = false;
                break;
            }
        }
        $shouldBeCompleted = $allCompleted;
    } else {
        // No billing parties for this bill - check if invoice has a bill_party_id that's completed
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if ($invoice && $invoice->bill_party_id) {
            $billingParty = InvoiceBillingParty::find($invoice->bill_party_id);
            if ($billingParty && ($billingParty->completed ?? 0) == 1) {
                // Invoice's billing party is completed (even if different bill)
                $shouldBeCompleted = true;
                echo "Detail ID {$detail->id}: No billing parties for bill {$billId}, but invoice's bill_party_id={$invoice->bill_party_id} is completed. Marking as Completed.\n";
            }
        } else {
            // No billing parties and no bill_party_id - mark as completed by default
            $shouldBeCompleted = true;
            echo "Detail ID {$detail->id}: No billing parties and no bill_party_id. Marking as Completed by default.\n";
        }
    }
    
    $currentStatus = $detail->client_profile_completed ?? 0;
    $newStatus = $shouldBeCompleted ? 1 : 0;
    
    if ($currentStatus != $newStatus) {
        $detail->client_profile_completed = $newStatus;
        $detail->save();
        $updated++;
        echo "  ✅ Updated Detail ID {$detail->id}: " . ($currentStatus == 1 ? "Completed" : "Pending") . " → " . ($newStatus == 1 ? "Completed" : "Pending") . "\n";
    }
}

// Refresh and check main status
$details->each(function($detail) {
    $detail->refresh();
});

$allDetailsCompleted = true;
foreach ($details as $detail) {
    if (($detail->client_profile_completed ?? 0) != 1) {
        $allDetailsCompleted = false;
        break;
    }
}

$currentMainStatus = $main->client_profile_completed ?? 0;
$newMainStatus = $allDetailsCompleted ? 1 : 0;

if ($currentMainStatus != $newMainStatus) {
    $main->client_profile_completed = $newMainStatus;
    $main->save();
    echo "\n✅ Updated Main Status: " . ($currentMainStatus == 1 ? "Completed" : "Pending") . " → " . ($newMainStatus == 1 ? "Completed" : "Pending") . "\n";
}

echo "\n=== Summary ===\n";
echo "Details updated: {$updated}\n";
echo "Main status: " . ($main->client_profile_completed == 1 ? "Completed" : "Pending") . "\n";
echo "All details completed: " . ($allDetailsCompleted ? "Yes" : "No") . "\n";
