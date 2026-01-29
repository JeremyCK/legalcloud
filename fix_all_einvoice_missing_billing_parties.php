<?php

/**
 * Fix ALL E-Invoice Records with Missing Billing Parties Issue
 * 
 * This script fixes the issue where:
 * - Details have no billing parties for their bill_id
 * - But the invoice's bill_party_id points to a completed billing party
 * - These should be marked as "Completed"
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;
use App\Models\LoanCaseInvoiceMain;

echo "=== Fixing ALL E-Invoice Records with Missing Billing Parties ===\n\n";

// Get all E-Invoice Main records
$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)->get();

echo "Processing " . $einvoiceMainRecords->count() . " E-Invoice Main records...\n\n";

$totalDetailsUpdated = 0;
$totalMainUpdated = 0;
$recordsFixed = [];

foreach ($einvoiceMainRecords as $main) {
    // Get all details for this main record
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    
    if ($details->count() == 0) {
        continue;
    }
    
    $detailsUpdated = 0;
    $mainWasPending = ($main->client_profile_completed ?? 0) != 1;
    
    // Step 1: Fix each detail
    foreach ($details as $detail) {
        $billId = $detail->loan_case_main_bill_id;
        $invoiceId = $detail->loan_case_invoice_id;
        
        if (!$billId || !$invoiceId) {
            continue;
        }
        
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
                }
            } else {
                // No billing parties and no bill_party_id - mark as completed by default
                // (assuming these don't need billing parties)
                $shouldBeCompleted = true;
            }
        }
        
        $currentStatus = $detail->client_profile_completed ?? 0;
        $newStatus = $shouldBeCompleted ? 1 : 0;
        
        if ($currentStatus != $newStatus) {
            $detail->client_profile_completed = $newStatus;
            $detail->save();
            $detailsUpdated++;
            $totalDetailsUpdated++;
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
        $totalMainUpdated++;
        
        if ($mainWasPending && $newMainStatus == 1) {
            $recordsFixed[] = [
                'id' => $main->id,
                'ref_no' => $main->ref_no,
                'details_updated' => $detailsUpdated
            ];
        }
    }
}

echo "=== Summary ===\n";
echo "Total E-Invoice Main records processed: " . $einvoiceMainRecords->count() . "\n";
echo "Total details updated: {$totalDetailsUpdated}\n";
echo "Total main records updated: {$totalMainUpdated}\n\n";

if (count($recordsFixed) > 0) {
    echo "=== Records Fixed (Pending → Completed) ===\n";
    foreach ($recordsFixed as $record) {
        echo "✅ E-Invoice ID {$record['id']} ({$record['ref_no']}): Updated {$record['details_updated']} details, Main status → Completed\n";
    }
    echo "\n";
}

echo "✅ All E-Invoice records have been processed!\n";
echo "Refresh the E-Invoice list page to see the updated statuses.\n";
