<?php

/**
 * Check E-Invoice Details Status
 * 
 * Check if einvoice_details.client_profile_completed is set correctly
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;

echo "=== Checking E-Invoice Details Status ===\n\n";

// Get recent E-Invoice Main records
$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get();

foreach ($einvoiceMainRecords as $main) {
    echo "E-Invoice Main ID: {$main->id} ({$main->ref_no})\n";
    echo "  Main client_profile_completed: " . ($main->client_profile_completed == 1 ? "Completed" : "Pending") . "\n\n";
    
    // Get all EInvoiceDetails for this main record
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    
    echo "  Details Records:\n";
    $detailsNeedingUpdate = [];
    
    foreach ($details as $detail) {
        $detailStatus = $detail->client_profile_completed ?? 0;
        
        // Check billing parties for this detail
        $billId = $detail->loan_case_main_bill_id;
        $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
        
        $allPartiesCompleted = true;
        if ($billingParties->count() == 0) {
            // No billing parties - can't be completed
            $allPartiesCompleted = false;
        } else {
            foreach ($billingParties as $party) {
                if (($party->completed ?? 0) != 1) {
                    $allPartiesCompleted = false;
                    break;
                }
            }
        }
        
        $shouldBeCompleted = $allPartiesCompleted && $billingParties->count() > 0;
        
        if ($detailStatus != ($shouldBeCompleted ? 1 : 0)) {
            $detailsNeedingUpdate[] = [
                'detail_id' => $detail->id,
                'current_status' => $detailStatus,
                'should_be' => $shouldBeCompleted ? 1 : 0,
                'bill_id' => $billId,
                'billing_parties_count' => $billingParties->count(),
                'all_parties_completed' => $allPartiesCompleted
            ];
        }
        
        if (count($detailsNeedingUpdate) <= 5) { // Only show first 5
            echo "    Detail ID {$detail->id}: ";
            echo "Status=" . ($detailStatus == 1 ? "Completed" : "Pending") . ", ";
            echo "Bill ID={$billId}, ";
            echo "Billing Parties=" . $billingParties->count() . ", ";
            echo "All Completed=" . ($allPartiesCompleted ? "Yes" : "No") . ", ";
            echo "Should Be=" . ($shouldBeCompleted ? "Completed" : "Pending");
            if ($detailStatus != ($shouldBeCompleted ? 1 : 0)) {
                echo " ⚠️ MISMATCH";
            }
            echo "\n";
        }
    }
    
    if (count($detailsNeedingUpdate) > 0) {
        echo "\n  ⚠️  Found " . count($detailsNeedingUpdate) . " details that need status update\n";
        
        // Update them
        foreach ($detailsNeedingUpdate as $update) {
            $detail = EInvoiceDetails::find($update['detail_id']);
            if ($detail) {
                $detail->client_profile_completed = $update['should_be'];
                $detail->save();
                echo "    ✅ Updated Detail ID {$update['detail_id']}: " . 
                     ($update['current_status'] == 1 ? "Completed" : "Pending") . " → " . 
                     ($update['should_be'] == 1 ? "Completed" : "Pending") . "\n";
            }
        }
        
        // Now recalculate main status
        $allDetailsCompleted = true;
        foreach ($details as $detail) {
            $detail->refresh();
            if (($detail->client_profile_completed ?? 0) != 1) {
                $allDetailsCompleted = false;
                break;
            }
        }
        
        $newMainStatus = $allDetailsCompleted ? 1 : 0;
        if ($main->client_profile_completed != $newMainStatus) {
            $main->client_profile_completed = $newMainStatus;
            $main->save();
            echo "    ✅ Updated Main Status: " . 
                 ($main->client_profile_completed == 1 ? "Completed" : "Pending") . "\n";
        }
    }
    
    echo "\n";
}
