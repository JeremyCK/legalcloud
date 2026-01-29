<?php

/**
 * Fix ALL E-Invoice Client Profile Status
 * 
 * 1. Updates einvoice_details.client_profile_completed based on billing parties
 * 2. Updates einvoice_main.client_profile_completed based on all details
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;

echo "=== Fixing ALL E-Invoice Client Profile Status ===\n\n";

// Get all E-Invoice Main records
$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)->get();

echo "Processing " . $einvoiceMainRecords->count() . " E-Invoice Main records...\n\n";

$detailsUpdated = 0;
$mainUpdated = 0;
$mainSkipped = 0;

foreach ($einvoiceMainRecords as $main) {
    // Get all EInvoiceDetails for this main record
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    
    if ($details->count() == 0) {
        continue;
    }
    
    // Step 1: Update each detail based on billing parties
    foreach ($details as $detail) {
        $billId = $detail->loan_case_main_bill_id;
        if (!$billId) {
            continue;
        }
        
        $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
        
        // If no billing parties, can't be completed
        if ($billingParties->count() == 0) {
            $shouldBeCompleted = false;
        } else {
            // Check if all billing parties are completed
            $shouldBeCompleted = true;
            foreach ($billingParties as $party) {
                if (($party->completed ?? 0) != 1) {
                    $shouldBeCompleted = false;
                    break;
                }
            }
        }
        
        $currentStatus = $detail->client_profile_completed ?? 0;
        $newStatus = $shouldBeCompleted ? 1 : 0;
        
        if ($currentStatus != $newStatus) {
            $detail->client_profile_completed = $newStatus;
            $detail->save();
            $detailsUpdated++;
        }
    }
    
    // Step 2: Refresh details and check if all are completed
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
    
    // Step 3: Update main status
    $currentMainStatus = $main->client_profile_completed ?? 0;
    $newMainStatus = $allDetailsCompleted ? 1 : 0;
    
    if ($currentMainStatus != $newMainStatus) {
        $main->client_profile_completed = $newMainStatus;
        $main->save();
        $mainUpdated++;
        
        echo "✅ Updated Main ID {$main->id} ({$main->ref_no}): ";
        echo ($currentMainStatus == 1 ? "Completed" : "Pending") . " → ";
        echo ($newMainStatus == 1 ? "Completed" : "Pending") . "\n";
    } else {
        $mainSkipped++;
    }
}

echo "\n=== Summary ===\n";
echo "Total E-Invoice Main records: " . $einvoiceMainRecords->count() . "\n";
echo "Details records updated: {$detailsUpdated}\n";
echo "Main records updated: {$mainUpdated}\n";
echo "Main records (no change): {$mainSkipped}\n";
echo "\n✅ All E-Invoice client profile statuses have been fixed!\n";
