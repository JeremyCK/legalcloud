<?php

/**
 * Fix E-Invoice Client Profile Status - PHP Script for Server
 * 
 * This is the PHP version of the fix that can be run via command line on the server.
 * Usage: php fix_einvoice_client_profile_status_php.php
 * 
 * This script does the same thing as the SQL script but uses Laravel models.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;
use App\Models\LoanCaseInvoiceMain;

echo "=== Fixing E-Invoice Client Profile Status ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Update einvoice_details.client_profile_completed
echo "Step 1: Updating einvoice_details.client_profile_completed...\n";

$details = EInvoiceDetails::where('status', '<>', 99)->get();
$detailsUpdated = 0;

foreach ($details as $detail) {
    $billId = $detail->loan_case_main_bill_id;
    $invoiceId = $detail->loan_case_invoice_id;
    
    if (!$billId || !$invoiceId) {
        continue;
    }
    
    $invoice = LoanCaseInvoiceMain::find($invoiceId);
    $shouldBeCompleted = false;
    
    // Check invoice's billing party first (this is what the detail page shows)
    if ($invoice && $invoice->bill_party_id) {
        $invoiceBillingParty = InvoiceBillingParty::find($invoice->bill_party_id);
        if ($invoiceBillingParty && ($invoiceBillingParty->completed ?? 0) == 1) {
            $shouldBeCompleted = true;
        }
    }
    
    // If invoice's billing party is not completed, check all billing parties for the bill
    if (!$shouldBeCompleted) {
        $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
        
        if ($billingParties->count() > 0) {
            $allCompleted = true;
            foreach ($billingParties as $party) {
                if (($party->completed ?? 0) != 1) {
                    $allCompleted = false;
                    break;
                }
            }
            $shouldBeCompleted = $allCompleted;
        } else {
            // No billing parties - mark as completed by default
            $shouldBeCompleted = true;
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

echo "  Updated {$detailsUpdated} detail records\n\n";

// Step 2: Update einvoice_main.client_profile_completed
echo "Step 2: Updating einvoice_main.client_profile_completed...\n";

$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)->get();
$mainUpdated = 0;

foreach ($einvoiceMainRecords as $main) {
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)
        ->where('status', '<>', 99)
        ->get();
    
    if ($details->count() == 0) {
        continue;
    }
    
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
        $mainUpdated++;
    }
}

echo "  Updated {$mainUpdated} main records\n\n";

// Step 3: Verification
echo "Step 3: Verification...\n";

$totalDetails = EInvoiceDetails::where('status', '<>', 99)->count();
$completedDetails = EInvoiceDetails::where('status', '<>', 99)
    ->where('client_profile_completed', 1)
    ->count();
$pendingDetails = $totalDetails - $completedDetails;

$totalMain = EInvoiceMain::where('status', '<>', 99)->count();
$completedMain = EInvoiceMain::where('status', '<>', 99)
    ->where('client_profile_completed', 1)
    ->count();
$pendingMain = $totalMain - $completedMain;

echo "  Details: {$completedDetails} completed, {$pendingDetails} pending (out of {$totalDetails} total)\n";
echo "  Main: {$completedMain} completed, {$pendingMain} pending (out of {$totalMain} total)\n\n";

echo "=== Summary ===\n";
echo "Details updated: {$detailsUpdated}\n";
echo "Main records updated: {$mainUpdated}\n";
echo "Completed at: " . date('Y-m-d H:i:s') . "\n";
echo "\n✅ Fix completed successfully!\n";
