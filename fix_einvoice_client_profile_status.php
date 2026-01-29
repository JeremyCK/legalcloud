<?php

/**
 * Fix E-Invoice Client Profile Status
 * 
 * Recalculates and updates einvoice_main.client_profile_completed based on all einvoice_details
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;

echo "=== Fixing E-Invoice Client Profile Status ===\n\n";

// Get all E-Invoice Main records
$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)->get();

echo "Processing " . $einvoiceMainRecords->count() . " E-Invoice Main records...\n\n";

$updated = 0;
$skipped = 0;

foreach ($einvoiceMainRecords as $main) {
    // Get all EInvoiceDetails for this main record
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    
    if ($details->count() == 0) {
        $skipped++;
        continue;
    }
    
    // Check if all details are completed
    $allDetailsCompleted = true;
    foreach ($details as $detail) {
        $detailStatus = $detail->client_profile_completed ?? 0;
        if ($detailStatus != 1) {
            $allDetailsCompleted = false;
            break;
        }
    }
    
    $currentMainStatus = $main->client_profile_completed ?? 0;
    $newMainStatus = $allDetailsCompleted ? 1 : 0;
    
    if ($currentMainStatus != $newMainStatus) {
        $main->client_profile_completed = $newMainStatus;
        $main->save();
        
        echo "✅ Updated E-Invoice Main ID {$main->id} ({$main->ref_no}): ";
        echo ($currentMainStatus == 1 ? "Completed" : "Pending") . " → ";
        echo ($newMainStatus == 1 ? "Completed" : "Pending") . "\n";
        echo "   Details: {$details->count()} records, ";
        echo "All completed: " . ($allDetailsCompleted ? "Yes" : "No") . "\n\n";
        
        $updated++;
    } else {
        $skipped++;
    }
}

echo "=== Summary ===\n";
echo "Total records: " . $einvoiceMainRecords->count() . "\n";
echo "Updated: {$updated}\n";
echo "Skipped (no change needed): {$skipped}\n";
