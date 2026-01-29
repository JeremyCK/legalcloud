<?php

/**
 * Fix E-Invoice Detail Status Logic
 * 
 * Update details to be "Completed" if the invoice's billing party is completed,
 * even if not all billing parties for the bill are completed.
 * This matches what the detail page shows.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;
use App\Models\LoanCaseInvoiceMain;

echo "=== Fixing E-Invoice Detail Status Logic ===\n\n";

// Get all E-Invoice Main records
$einvoiceMainRecords = EInvoiceMain::where('status', '<>', 99)->get();

echo "Processing " . $einvoiceMainRecords->count() . " E-Invoice Main records...\n\n";

$totalDetailsUpdated = 0;
$totalMainUpdated = 0;
$recordsFixed = [];

foreach ($einvoiceMainRecords as $main) {
    $details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();
    
    if ($details->count() == 0) {
        continue;
    }
    
    $detailsUpdated = 0;
    $mainWasPending = ($main->client_profile_completed ?? 0) != 1;
    
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
                // Invoice's billing party is completed - mark detail as completed
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
            $totalDetailsUpdated++;
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
}

echo "\n✅ All E-Invoice records have been processed!\n";
echo "The detail status now matches what the detail page shows.\n";
