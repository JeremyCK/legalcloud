<?php

/**
 * Check E-Invoice 40 Status in Detail
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;
use App\Models\LoanCaseInvoiceMain;

echo "=== Checking E-Invoice 40 Status ===\n\n";

$main = EInvoiceMain::find(40);

if (!$main) {
    echo "❌ E-Invoice Main ID 40 not found\n";
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

foreach ($details as $detail) {
    $detailStatus = $detail->client_profile_completed ?? 0;
    $billId = $detail->loan_case_main_bill_id;
    $invoiceId = $detail->loan_case_invoice_id;
    
    $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
    
    // Get invoice first
    $invoice = LoanCaseInvoiceMain::find($invoiceId);
    
    $allPartiesCompleted = true;
    $hasBillingParties = $billingParties->count() > 0;
    
    if ($hasBillingParties) {
        foreach ($billingParties as $party) {
            if (($party->completed ?? 0) != 1) {
                $allPartiesCompleted = false;
                break;
            }
        }
    } else {
        // No billing parties - check invoice's bill_party_id
        if ($invoice && $invoice->bill_party_id) {
            $billingParty = InvoiceBillingParty::find($invoice->bill_party_id);
            if ($billingParty && ($billingParty->completed ?? 0) == 1) {
                $allPartiesCompleted = true; // Invoice's billing party is completed
            } else {
                $allPartiesCompleted = false;
            }
        } else {
            $allPartiesCompleted = false; // No billing parties and no bill_party_id
        }
    }
    
    $shouldBeCompleted = $allPartiesCompleted && ($hasBillingParties || ($invoice && $invoice->bill_party_id));
    
    $info = [
        'detail_id' => $detail->id,
        'bill_id' => $billId,
        'invoice_id' => $invoiceId,
        'invoice_no' => $invoice ? $invoice->invoice_no : 'N/A',
        'current_status' => $detailStatus,
        'should_be' => $shouldBeCompleted ? 1 : 0,
        'billing_parties_count' => $billingParties->count(),
        'all_parties_completed' => $allPartiesCompleted,
        'mismatch' => $detailStatus != ($shouldBeCompleted ? 1 : 0),
        'reason' => ''
    ];
    
    if (!$hasBillingParties) {
        if ($invoice && $invoice->bill_party_id) {
            $info['reason'] = "No billing parties for bill, but invoice has bill_party_id=" . $invoice->bill_party_id;
        } else {
            $info['reason'] = "No billing parties and no bill_party_id";
        }
    } else {
        if (!$allPartiesCompleted) {
            $pendingParties = [];
            foreach ($billingParties as $party) {
                if (($party->completed ?? 0) != 1) {
                    $pendingParties[] = "Party ID {$party->id}";
                }
            }
            $info['reason'] = "Has billing parties but not all completed: " . implode(", ", $pendingParties);
        }
    }
    
    if ($detailStatus == 1) {
        $completedDetails[] = $info;
    } else {
        $pendingDetails[] = $info;
    }
}

echo "=== Summary ===\n";
echo "Completed Details: " . count($completedDetails) . "\n";
echo "Pending Details: " . count($pendingDetails) . "\n\n";

if (count($pendingDetails) > 0) {
    echo "=== Pending Details ===\n";
    foreach ($pendingDetails as $pending) {
        echo "Detail ID {$pending['detail_id']}: ";
        echo "Invoice {$pending['invoice_no']}, ";
        echo "Bill ID={$pending['bill_id']}, ";
        echo "Billing Parties={$pending['billing_parties_count']}, ";
        echo "Status=" . ($pending['current_status'] == 1 ? "Completed" : "Pending") . ", ";
        echo "Should Be=" . ($pending['should_be'] == 1 ? "Completed" : "Pending");
        if ($pending['mismatch']) {
            echo " ⚠️ MISMATCH";
        }
        echo "\n";
        echo "  Reason: {$pending['reason']}\n";
        echo "\n";
    }
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

if (!$allDetailsCompleted) {
    echo "\n⚠️  ISSUE: Not all details are completed.\n";
    echo "There are " . count($pendingDetails) . " details that are still Pending.\n";
    echo "These need to be fixed before the main can be marked as Completed.\n";
}
