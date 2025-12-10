<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;
use App\Models\TransferFeeDetails;

class InvoiceFixController extends Controller
{
    public function index()
    {
        $current_user = auth()->user();
        
        return view('dashboard.invoice-fix.index', [
            'current_user' => $current_user,
            'locales' => [],
            'appLocale' => 'en'
        ]);
    }

    /**
     * Get invoices with wrong pfee2_inv (includes reimbursement)
     */
    public function getWrongInvoices(Request $request)
    {
        Log::info('InvoiceFixController::getWrongInvoices called');
        $limit = $request->get('limit', 50);
        $offset = $request->get('offset', 0);
        $search = $request->get('search', '');
        $showAll = $request->get('show_all', false); // New parameter to show all invoices even if no issue
        
        // Find invoices where pfee2_inv doesn't match calculated value
        $query = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as lc', 'lc.id', '=', 'bm.case_id')
            ->select(
                'im.id as invoice_id',
                'im.invoice_no',
                'im.pfee1_inv',
                'im.pfee2_inv',
                'im.reimbursement_amount',
                'im.reimbursement_sst',
                'im.sst_inv',
                'bm.case_id',
                'bm.id as bill_id',
                'bm.sst_rate',
                'lc.case_ref_no',
                // Calculated values
                DB::raw('(
                    SELECT COALESCE(SUM(ild.amount), 0)
                    FROM loan_case_invoice_details ild
                    INNER JOIN account_item ai ON ai.id = ild.account_item_id
                    WHERE ild.invoice_main_id = im.id
                    AND ai.account_cat_id = 1
                    AND ai.pfee1_item = 1
                    AND ild.status <> 99
                ) as calculated_pfee1'),
                DB::raw('(
                    SELECT COALESCE(SUM(ild.amount), 0)
                    FROM loan_case_invoice_details ild
                    INNER JOIN account_item ai ON ai.id = ild.account_item_id
                    WHERE ild.invoice_main_id = im.id
                    AND ai.account_cat_id = 1
                    AND ai.pfee1_item = 0
                    AND ild.status <> 99
                ) as calculated_pfee2'),
                DB::raw('(
                    SELECT COALESCE(SUM(ild.amount), 0)
                    FROM loan_case_invoice_details ild
                    INNER JOIN account_item ai ON ai.id = ild.account_item_id
                    WHERE ild.invoice_main_id = im.id
                    AND ai.account_cat_id = 4
                    AND ild.status <> 99
                ) as calculated_reimb')
            )
            ->where('im.status', '<>', 99);
        
        // Add search functionality
        if (!empty($search)) {
            $searchTerms = preg_split('/[,\s]+/', $search);
            $searchTerms = array_filter(array_map('trim', $searchTerms));
            Log::info('Searching for invoices: ' . implode(', ', $searchTerms));
            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    if (!empty($term)) {
                        $q->orWhere('im.invoice_no', 'LIKE', '%' . $term . '%')
                          ->orWhere('lc.case_ref_no', 'LIKE', '%' . $term . '%');
                    }
                }
            });
        }
        
        $query->orderBy('im.id', 'desc')
            ->limit($limit)
            ->offset($offset);

        try {
            $invoices = $query->get();
            Log::info('Found ' . $invoices->count() . ' invoices');
            
            $results = [];
            $notFound = [];
            
            // If searching, track which search terms didn't find invoices
            if (!empty($search)) {
                $searchTerms = preg_split('/[,\s]+/', $search);
                $searchTerms = array_filter(array_map('trim', $searchTerms));
                $foundInvoiceNos = $invoices->pluck('invoice_no')->toArray();
                $foundCaseRefs = $invoices->pluck('case_ref_no')->filter()->toArray();
                
                foreach ($searchTerms as $term) {
                    $found = false;
                    foreach ($foundInvoiceNos as $invNo) {
                        if (stripos($invNo, $term) !== false) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        foreach ($foundCaseRefs as $caseRef) {
                            if (stripos($caseRef, $term) !== false) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found && !empty($term)) {
                        $notFound[] = $term;
                    }
                }
            }
            
            foreach ($invoices as $invoice) {
                // Check if pfee2_inv doesn't match calculated value
                $pfee2Diff = abs($invoice->pfee2_inv - $invoice->calculated_pfee2);
                $reimbDiff = abs(($invoice->reimbursement_amount ?? 0) - $invoice->calculated_reimb);
                $hasIssue = ($pfee2Diff > 0.01 || $reimbDiff > 0.01);
                
                // If showAll is true OR invoice has issue OR we're searching (show even if no issue)
                if ($showAll || $hasIssue || !empty($search)) {
                    $results[] = [
                        'invoice' => $invoice,
                        'pfee2_diff' => $pfee2Diff,
                        'reimb_diff' => $reimbDiff,
                        'has_issue' => $hasIssue,
                        'has_transfer_fee' => $this->hasTransferFee($invoice->invoice_id)
                    ];
                }
            }
            
            Log::info('Found ' . count($results) . ' invoices (with issues: ' . count(array_filter($results, function($r) { return $r['has_issue']; })) . ')');
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => count($results),
                'not_found' => $notFound,
                'message' => !empty($notFound) ? 'Some search terms did not find any invoices: ' . implode(', ', $notFound) : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getWrongInvoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fix single invoice
     */
    public function fixSingleInvoice(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoiceNo = $request->get('invoice_no'); // Optional: for stored procedure
        
        try {
            // If invoice_no is provided, use stored procedure
            if ($invoiceNo) {
                $result = $this->fixInvoiceByNumber($invoiceNo);
            } else {
                $result = $this->fixInvoice($invoiceId);
            }
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fixing invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fix invoice by invoice number (using PHP logic, no stored procedure needed)
     */
    public function fixInvoiceByNumber($invoiceNo)
    {
        try {
            // Find invoice
            $invoice = DB::table('loan_case_invoice_main as im')
                ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
                ->where('im.invoice_no', $invoiceNo)
                ->where('im.status', '<>', 99)
                ->select('im.*', 'bm.sst_rate', 'bm.case_id')
                ->first();
            
            if (!$invoice) {
                return [
                    'success' => false,
                    'message' => "Invoice {$invoiceNo} not found"
                ];
            }
            
            // Use the existing fixInvoice method
            $result = $this->fixInvoice($invoice->id);
            
            if ($result['success']) {
                $result['message'] = "Invoice {$invoiceNo} fixed successfully";
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Error fixing invoice by number {$invoiceNo}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fix multiple invoices by invoice numbers
     */
    public function fixMultipleInvoices(Request $request)
    {
        $invoiceNumbers = $request->get('invoice_numbers'); // Comma-separated
        
        if (empty($invoiceNumbers)) {
            return response()->json([
                'success' => false,
                'message' => 'No invoice numbers provided'
            ]);
        }
        
        try {
            // Parse invoice numbers
            $invoiceList = array_map('trim', explode(',', $invoiceNumbers));
            $invoiceList = array_filter($invoiceList); // Remove empty values
            
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            
            // Fix each invoice one by one
            foreach ($invoiceList as $invoiceNo) {
                try {
                    $result = $this->fixInvoiceByNumber($invoiceNo);
                    
                    $resultData = [
                        'invoice_no' => $invoiceNo,
                        'status' => $result['success'] ? 'SUCCESS' : 'ERROR',
                        'message' => $result['message']
                    ];
                    
                    // Include detailed data if available
                    if ($result['success'] && isset($result['data'])) {
                        $resultData['details'] = $result['data'];
                    }
                    
                    $results[] = $resultData;
                    
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $results[] = [
                        'invoice_no' => $invoiceNo,
                        'status' => 'ERROR',
                        'message' => 'Error: ' . $e->getMessage()
                    ];
                    $errorCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Fixed {$successCount} invoice(s) successfully. {$errorCount} error(s).",
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fixing multiple invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fix all invoices (batch)
     */
    public function fixAllInvoices(Request $request)
    {
        $limit = $request->get('limit', 100);
        
        try {
            // Get all wrong invoices
            $wrongInvoices = $this->getWrongInvoicesList($limit);
            
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($wrongInvoices as $invoiceId) {
                $result = $this->fixInvoice($invoiceId);
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                $results[] = [
                    'invoice_id' => $invoiceId,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => "Fixed {$successCount} invoices successfully. {$errorCount} errors.",
                'data' => [
                    'total' => count($wrongInvoices),
                    'success' => $successCount,
                    'errors' => $errorCount,
                    'details' => $results
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fixing all invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Core fix function - fixes both invoice and ledger entries
     */
    private function fixInvoice($invoiceId)
    {
        DB::beginTransaction();
        
        try {
            $invoice = LoanCaseInvoiceMain::find($invoiceId);
            if (!$invoice) {
                return ['success' => false, 'message' => 'Invoice not found'];
            }
            
            $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
            if (!$bill) {
                return ['success' => false, 'message' => 'Bill not found'];
            }
            
            // Fix rounding issues in invoice details first (for split invoices)
            // This fixes ALL invoices for the bill, not just this one
            $this->fixRoundingInInvoiceDetails($invoiceId, $bill->id);
            
            // IMPORTANT: Clear any cached query results to ensure we read fresh data
            // The invoice details were just updated, so we need fresh data for SST calculation
            DB::connection()->getPdo()->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            
            // Calculate correct amounts from details (now using fixed amounts)
            // This will read the freshly updated invoice details
            $calculated = $this->calculateInvoiceAmountsFromDetails($invoiceId, $bill->sst_rate);
            
            // Update invoice amounts
            $invoice->pfee1_inv = $calculated['pfee1'];
            $invoice->pfee2_inv = $calculated['pfee2'];
            $invoice->sst_inv = $calculated['sst'];
            $invoice->reimbursement_amount = $calculated['reimbursement_amount'];
            $invoice->reimbursement_sst = $calculated['reimbursement_sst'];
            $invoice->amount = $calculated['total'];
            $invoice->save();
            
            // Update transfer_fee_details to match corrected invoice amounts
            $totalPfee = $calculated['pfee1'] + $calculated['pfee2'];
            $transferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
                ->where('status', '<>', 99)
                ->get();
            
            if ($transferFeeDetails->count() > 0) {
                // If only one record, update it directly with correct amounts
                if ($transferFeeDetails->count() == 1) {
                    $tfd = $transferFeeDetails->first();
                    $tfd->transfer_amount = round($totalPfee, 2);
                    $tfd->sst_amount = round($calculated['sst'], 2);
                    $tfd->reimbursement_amount = round($calculated['reimbursement_amount'], 2);
                    $tfd->reimbursement_sst_amount = round($calculated['reimbursement_sst'], 2);
                    $tfd->updated_at = now();
                    $tfd->save();
                } else {
                    // Multiple records: preserve original distribution ratio, but ensure totals are correct
                    $totalOriginalPfee = $transferFeeDetails->sum('transfer_amount');
                    $totalOriginalSst = $transferFeeDetails->sum('sst_amount');
                    $totalOriginalReimb = $transferFeeDetails->sum('reimbursement_amount');
                    $totalOriginalReimbSst = $transferFeeDetails->sum('reimbursement_sst_amount');
                    
                    $totalDistributedPfee = 0;
                    $totalDistributedSst = 0;
                    $totalDistributedReimb = 0;
                    $totalDistributedReimbSst = 0;
                    
                    foreach ($transferFeeDetails as $index => $tfd) {
                        // If original totals are 0 or very small, distribute equally
                        if ($totalOriginalPfee > 0.01) {
                            $ratio = $tfd->transfer_amount / $totalOriginalPfee;
                            $tfd->transfer_amount = round($totalPfee * $ratio, 2);
                        } else {
                            $tfd->transfer_amount = round($totalPfee / $transferFeeDetails->count(), 2);
                        }
                        $totalDistributedPfee += $tfd->transfer_amount;
                        
                        if ($totalOriginalSst > 0.01) {
                            $ratio = $tfd->sst_amount / $totalOriginalSst;
                            $tfd->sst_amount = round($calculated['sst'] * $ratio, 2);
                        } else {
                            $tfd->sst_amount = round($calculated['sst'] / $transferFeeDetails->count(), 2);
                        }
                        $totalDistributedSst += $tfd->sst_amount;
                        
                        if ($totalOriginalReimb > 0.01) {
                            $ratio = $tfd->reimbursement_amount / $totalOriginalReimb;
                            $tfd->reimbursement_amount = round($calculated['reimbursement_amount'] * $ratio, 2);
                        } else {
                            $tfd->reimbursement_amount = round($calculated['reimbursement_amount'] / $transferFeeDetails->count(), 2);
                        }
                        $totalDistributedReimb += $tfd->reimbursement_amount;
                        
                        if ($totalOriginalReimbSst > 0.01) {
                            $ratio = $tfd->reimbursement_sst_amount / $totalOriginalReimbSst;
                            $tfd->reimbursement_sst_amount = round($calculated['reimbursement_sst'] * $ratio, 2);
                        } else {
                            $tfd->reimbursement_sst_amount = round($calculated['reimbursement_sst'] / $transferFeeDetails->count(), 2);
                        }
                        $totalDistributedReimbSst += $tfd->reimbursement_sst_amount;
                        
                        $tfd->updated_at = now();
                        $tfd->save();
                    }
                    
                    // Ensure last record gets remainder to avoid rounding errors
                    if ($transferFeeDetails->count() > 1) {
                        $lastTfd = $transferFeeDetails->last();
                        $lastTfd->transfer_amount = round($totalPfee - ($totalDistributedPfee - $lastTfd->transfer_amount), 2);
                        $lastTfd->sst_amount = round($calculated['sst'] - ($totalDistributedSst - $lastTfd->sst_amount), 2);
                        $lastTfd->reimbursement_amount = round($calculated['reimbursement_amount'] - ($totalDistributedReimb - $lastTfd->reimbursement_amount), 2);
                        $lastTfd->reimbursement_sst_amount = round($calculated['reimbursement_sst'] - ($totalDistributedReimbSst - $lastTfd->reimbursement_sst_amount), 2);
                        $lastTfd->save();
                    }
                }
            }
            
            // Recalculate transferred amounts
            $invoice->transferred_pfee_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
                ->where('status', '<>', 99)
                ->sum('transfer_amount');
            $invoice->transferred_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
                ->where('status', '<>', 99)
                ->sum('sst_amount');
            $invoice->transferred_reimbursement_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
                ->where('status', '<>', 99)
                ->sum('reimbursement_amount');
            $invoice->transferred_reimbursement_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
                ->where('status', '<>', 99)
                ->sum('reimbursement_sst_amount');
            $invoice->save();
            
            // Fix ledger entries (now uses updated transfer_fee_details)
            $ledgerUpdateResult = $this->fixLedgerEntries($invoiceId);
            
            DB::commit();
            
            // Get updated values for display
            $invoice->refresh();
            $transferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
                ->where('status', '<>', 99)
                ->get();
            
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->whereIn('key_id_2', $transferFeeDetails->pluck('id'))
                ->where('status', '<>', 99)
                ->whereIn('type', ['TRANSFER_OUT', 'TRANSFER_IN', 'SST_OUT', 'SST_IN', 'REIMB_OUT', 'REIMB_IN', 'REIMB_SST_OUT', 'REIMB_SST_IN'])
                ->get();
            
            Log::info("Fixed invoice {$invoiceId}", [
                'invoice_no' => $invoice->invoice_no,
                'pfee1' => $calculated['pfee1'],
                'pfee2' => $calculated['pfee2'],
                'sst' => $calculated['sst'],
                'reimbursement' => $calculated['reimbursement_amount'],
                'ledger_updated' => $ledgerUpdateResult['updated_count']
            ]);
            
            return [
                'success' => true,
                'message' => "Invoice {$invoice->invoice_no} fixed successfully",
                'data' => [
                    'invoice_id' => $invoiceId,
                    'invoice_no' => $invoice->invoice_no,
                    'pfee1' => $calculated['pfee1'],
                    'pfee2' => $calculated['pfee2'],
                    'sst' => $calculated['sst'],
                    'reimbursement_amount' => $calculated['reimbursement_amount'],
                    'reimbursement_sst' => $calculated['reimbursement_sst'],
                    'total' => $calculated['total'],
                    'stored_pfee1' => $invoice->pfee1_inv,
                    'stored_pfee2' => $invoice->pfee2_inv,
                    'stored_sst' => $invoice->sst_inv,
                    'stored_reimbursement' => $invoice->reimbursement_amount,
                    'transfer_fee_details' => $transferFeeDetails->map(function($tfd) {
                        return [
                            'id' => $tfd->id,
                            'transfer_amount' => $tfd->transfer_amount,
                            'sst_amount' => $tfd->sst_amount,
                            'reimbursement_amount' => $tfd->reimbursement_amount,
                            'reimbursement_sst_amount' => $tfd->reimbursement_sst_amount
                        ];
                    }),
                    'ledger_entries_updated' => $ledgerUpdateResult['updated_count'],
                    'ledger_entries' => $ledgerEntries->map(function($le) {
                        return [
                            'id' => $le->id,
                            'type' => $le->type,
                            'amount' => $le->amount,
                            'transaction_id' => $le->transaction_id
                        ];
                    })
                ]
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error fixing invoice {$invoiceId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate invoice amounts from details
     * Uses the same rounding rule as the invoice display: round DOWN if 3rd decimal is 5
     * For split invoices: calculates SST from total pfee to ensure correct distribution
     */
    private function calculateInvoiceAmountsFromDetails($invoiceId, $sstRate)
    {
        // Get invoice and bill info
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            return ['pfee1' => 0, 'pfee2' => 0, 'sst' => 0, 'reimbursement_amount' => 0, 'reimbursement_sst' => 0, 'total' => 0];
        }
        
        $billId = $invoice->loan_case_main_bill_id;
        $sstRateDecimal = $sstRate / 100;
        
        // Check if this is a split invoice
        $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->count();
        
        $isSplitInvoice = $invoiceCount > 1;
        
        // Get individual invoice details
        $invoiceDetails = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->where('ai.account_cat_id', 1)
            ->select('ild.amount', 'ai.pfee1_item')
            ->get();
        
        $pfee1 = 0;
        $pfee2 = 0;
        
        // Calculate pfee1 and pfee2
        foreach ($invoiceDetails as $detail) {
            if ($detail->pfee1_item == 1) {
                $pfee1 += $detail->amount;
            } else {
                $pfee2 += $detail->amount;
            }
        }
        
        $pfee1 = round($pfee1, 2);
        $pfee2 = round($pfee2, 2);
        $totalPfee = $pfee1 + $pfee2;
        
        // Calculate SST based on whether it's a split invoice
        if ($isSplitInvoice) {
            // For split invoices: Calculate from total pfee of ALL invoices, then distribute
            // Get all invoices for this bill
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
                ->where('status', '<>', 99)
                ->get();
            
            // Calculate total pfee across all invoices
            $totalPfeeAllInvoices = 0;
            $invoicePfees = [];
            
            foreach ($allInvoices as $inv) {
                $invDetails = DB::table('loan_case_invoice_details as ild')
                    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                    ->where('ild.invoice_main_id', $inv->id)
                    ->where('ild.status', '<>', 99)
                    ->where('ai.account_cat_id', 1)
                    ->select('ild.amount', 'ai.pfee1_item')
                    ->get();
                
                $invPfee1 = 0;
                $invPfee2 = 0;
                foreach ($invDetails as $invDetail) {
                    if ($invDetail->pfee1_item == 1) {
                        $invPfee1 += $invDetail->amount;
                    } else {
                        $invPfee2 += $invDetail->amount;
                    }
                }
                $invTotalPfee = round($invPfee1 + $invPfee2, 2);
                $invoicePfees[$inv->id] = $invTotalPfee;
                $totalPfeeAllInvoices += $invTotalPfee;
            }
            
            // Calculate total SST from total pfee (applying special rounding rule)
            $totalSstRaw = $totalPfeeAllInvoices * $sstRateDecimal;
            $totalSstString = number_format($totalSstRaw, 3, '.', '');
            if (substr($totalSstString, -1) == '5') {
                $totalSstAllInvoices = floor($totalSstRaw * 100) / 100; // Round down
            } else {
                $totalSstAllInvoices = round($totalSstRaw, 2); // Normal rounding
            }
            
            // Calculate SST for each invoice from its own pfee
            $calculatedSsts = [];
            $totalCalculatedSst = 0;
            
            // Sort invoices by pfee (descending) so higher pfee gets processed first
            $sortedInvoices = collect($allInvoices)->sortByDesc(function($inv) use ($invoicePfees) {
                return $invoicePfees[$inv->id];
            })->values();
            
            foreach ($sortedInvoices as $inv) {
                $invPfee = $invoicePfees[$inv->id];
                $invSstRaw = $invPfee * $sstRateDecimal;
                $invSstString = number_format($invSstRaw, 3, '.', '');
                
                if (substr($invSstString, -1) == '5') {
                    $invSst = floor($invSstRaw * 100) / 100;
                } else {
                    $invSst = round($invSstRaw, 2);
                }
                
                $calculatedSsts[$inv->id] = $invSst;
                $totalCalculatedSst += $invSst;
            }
            
            // Adjust to match total SST exactly - add difference to invoice with highest pfee
            $difference = $totalSstAllInvoices - $totalCalculatedSst;
            if (abs($difference) > 0.001) {
                $highestPfeeInvoice = $sortedInvoices->first();
                $calculatedSsts[$highestPfeeInvoice->id] = round($calculatedSsts[$highestPfeeInvoice->id] + $difference, 2);
            }
            
            // Get SST for current invoice
            $sst = $calculatedSsts[$invoiceId];
        } else {
            // For single invoice: calculate SST from individual detail items (matches invoice display)
            $sst = 0;
            foreach ($invoiceDetails as $detail) {
                // Apply special rounding rule: round DOWN if 3rd decimal is 5
                $sst_calculation = $detail->amount * $sstRateDecimal;
                $sst_string = number_format($sst_calculation, 3, '.', '');
                
                if (substr($sst_string, -1) == '5') {
                    $row_sst = floor($sst_calculation * 100) / 100; // Round down
                } else {
                    $row_sst = round($sst_calculation, 2); // Normal rounding
                }
                
                $sst += $row_sst;
            }
            $sst = round($sst, 2);
        }
        
        // Get reimbursement details
        $reimbDetails = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->where('ai.account_cat_id', 4)
            ->select('ild.amount')
            ->get();
        
        $reimbursement_amount = 0;
        $reimbursement_sst = 0;
        
        // Calculate reimbursement amount and SST based on whether it's a split invoice
        if ($isSplitInvoice) {
            // For split invoices: Calculate total reimbursement across all invoices, then distribute equally
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
                ->where('status', '<>', 99)
                ->get();
            
            // Calculate total reimbursement across all invoices
            $totalReimbAllInvoices = 0;
            foreach ($allInvoices as $inv) {
                $invReimbDetails = DB::table('loan_case_invoice_details as ild')
                    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                    ->where('ild.invoice_main_id', $inv->id)
                    ->where('ild.status', '<>', 99)
                    ->where('ai.account_cat_id', 4)
                    ->select('ild.amount')
                    ->get();
                
                $invReimb = 0;
                foreach ($invReimbDetails as $invReimbDetail) {
                    $invReimb += $invReimbDetail->amount;
                }
                $totalReimbAllInvoices += round($invReimb, 2);
            }
            
            // Distribute reimbursement equally across invoices
            $reimbPerInvoice = round($totalReimbAllInvoices / $invoiceCount, 2);
            $totalDistributedReimb = $reimbPerInvoice * ($invoiceCount - 1);
            $lastReimb = round($totalReimbAllInvoices - $totalDistributedReimb, 2);
            
            // Find current invoice index
            $sortedInvoices = collect($allInvoices)->sortBy('id')->values();
            $currentIndex = 0;
            foreach ($sortedInvoices as $idx => $inv) {
                if ($inv->id == $invoiceId) {
                    $currentIndex = $idx;
                    break;
                }
            }
            
            // Last invoice gets remainder, others get equal share
            if ($currentIndex == $invoiceCount - 1) {
                $reimbursement_amount = $lastReimb;
            } else {
                $reimbursement_amount = $reimbPerInvoice;
            }
            
            // Calculate total reimbursement SST from total reimbursement (applying special rounding rule)
            // For split invoices: Calculate total reimbursement SST from total reimbursement of ALL invoices
            // Then distribute equally (since reimbursement should be divided equally)
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
                ->where('status', '<>', 99)
                ->get();
            
            // Calculate total reimbursement across all invoices
            $totalReimbAllInvoices = 0;
            foreach ($allInvoices as $inv) {
                $invReimbDetails = DB::table('loan_case_invoice_details as ild')
                    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                    ->where('ild.invoice_main_id', $inv->id)
                    ->where('ild.status', '<>', 99)
                    ->where('ai.account_cat_id', 4)
                    ->select('ild.amount')
                    ->get();
                
                $invReimb = 0;
                foreach ($invReimbDetails as $invReimbDetail) {
                    $invReimb += $invReimbDetail->amount;
                }
                $totalReimbAllInvoices += round($invReimb, 2);
            }
            
            // Calculate total reimbursement SST from total reimbursement (applying special rounding rule)
            $totalReimbSstRaw = $totalReimbAllInvoices * $sstRateDecimal;
            $totalReimbSstString = number_format($totalReimbSstRaw, 3, '.', '');
            if (substr($totalReimbSstString, -1) == '5') {
                $totalReimbSstAllInvoices = floor($totalReimbSstRaw * 100) / 100; // Round down
            } else {
                $totalReimbSstAllInvoices = round($totalReimbSstRaw, 2); // Normal rounding
            }
            
            // Distribute reimbursement SST equally across invoices
            $reimbSstPerInvoice = round($totalReimbSstAllInvoices / $invoiceCount, 2);
            $totalDistributedReimbSst = $reimbSstPerInvoice * ($invoiceCount - 1);
            $lastReimbSst = round($totalReimbSstAllInvoices - $totalDistributedReimbSst, 2);
            
            // Use the same currentIndex from above
            if ($currentIndex == $invoiceCount - 1) {
                $reimbursement_sst = $lastReimbSst;
            } else {
                $reimbursement_sst = $reimbSstPerInvoice;
            }
        } else {
            // For single invoice: calculate reimbursement amount and SST from individual detail items
            foreach ($reimbDetails as $detail) {
                $reimbursement_amount += $detail->amount;
                
                // Apply special rounding rule for reimbursement SST
                $sst_calculation = $detail->amount * $sstRateDecimal;
                $sst_string = number_format($sst_calculation, 3, '.', '');
                
                if (substr($sst_string, -1) == '5') {
                    $row_sst = floor($sst_calculation * 100) / 100; // Round down
                } else {
                    $row_sst = round($sst_calculation, 2); // Normal rounding
                }
                
                $reimbursement_sst += $row_sst;
            }
            $reimbursement_amount = round($reimbursement_amount, 2);
            $reimbursement_sst = round($reimbursement_sst, 2);
        }
        
        $reimbDetails = (object)[
            'reimbursement_amount' => $reimbursement_amount,
            'reimbursement_sst' => $reimbursement_sst
        ];
        
        $otherDetails = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->whereNotIn('ai.account_cat_id', [1, 4])
            ->select(DB::raw('SUM(ild.amount) as other_amount'))
            ->first();
        
        $other_amount = round($otherDetails->other_amount ?? 0, 2);
        
        // Calculate total: (pfee1 + pfee2) + sst + reimbursement_amount + reimbursement_sst + other_amount
        $total = $pfee1 + $pfee2 + $sst + $reimbursement_amount + $reimbursement_sst + $other_amount;

        return [
            'pfee1' => $pfee1,
            'pfee2' => $pfee2,
            'sst' => $sst,
            'reimbursement_amount' => $reimbursement_amount,
            'reimbursement_sst' => $reimbursement_sst,
            'total' => round($total, 2)
        ];
    }

    /**
     * Fix ledger entries for an invoice
     * Returns count of updated entries
     */
    private function fixLedgerEntries($invoiceId)
    {
        // Get all transfer fee details for this invoice
        $transferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
            ->where('status', '<>', 99)
            ->get();
        
        $updatedCount = 0;
        
        foreach ($transferFeeDetails as $tfd) {
            // Update LedgerEntriesV2 - TRANSFER_OUT/IN
            // IMPORTANT: Update ALL ledger entries for this transfer_fee_detail (key_id_2)
            // Don't filter by invoice_id because ledger entries are linked via transfer_fee_details
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['TRANSFER_OUT', 'TRANSFER_IN'])
                ->update(['amount' => $tfd->transfer_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update LedgerEntriesV2 - SST_OUT/IN
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['SST_OUT', 'SST_IN'])
                ->update(['amount' => $tfd->sst_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update LedgerEntriesV2 - REIMB_OUT/IN
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['REIMB_OUT', 'REIMB_IN'])
                ->update(['amount' => $tfd->reimbursement_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update LedgerEntriesV2 - REIMB_SST_OUT/IN
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['REIMB_SST_OUT', 'REIMB_SST_IN'])
                ->update(['amount' => $tfd->reimbursement_sst_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update old LedgerEntries table - TRANSFEROUT/IN
            $count = DB::table('ledger_entries')
                ->where('key_id', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['TRANSFEROUT', 'TRANSFERIN'])
                ->update(['amount' => $tfd->transfer_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update old LedgerEntries table - SSTOUT/IN
            $count = DB::table('ledger_entries')
                ->where('key_id', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['SSTOUT', 'SSTIN'])
                ->update(['amount' => $tfd->sst_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update old LedgerEntries table - REIMBOUT/IN
            $count = DB::table('ledger_entries')
                ->where('key_id', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['REIMBOUT', 'REIMBIN'])
                ->update(['amount' => $tfd->reimbursement_amount, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update old LedgerEntries table - REIMBSSTOUT/IN
            $count = DB::table('ledger_entries')
                ->where('key_id', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['REIMBSSTOUT', 'REIMBSSTIN'])
                ->update(['amount' => $tfd->reimbursement_sst_amount, 'updated_at' => now()]);
            $updatedCount += $count;
        }
        
        return ['updated_count' => $updatedCount];
    }

    /**
     * Check if invoice has transfer fee
     */
    private function hasTransferFee($invoiceId)
    {
        return TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
            ->where('status', '<>', 99)
            ->exists();
    }

    /**
     * Fix rounding issues in invoice details for split invoices
     * Fixes BOTH professional fees AND reimbursement amounts
     * Excel way: Use the CORRECT invoice base total / invoice_count
     */
    private function fixRoundingInInvoiceDetails($invoiceId, $billId)
    {
        // Get invoice count for this bill
        $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->count();
        
        if ($invoiceCount <= 1) {
            return; // No rounding issue for single invoice
        }
        
        // Get all bill details to calculate correct totals per account category
        $billDetails = DB::table('loan_case_bill_details')
            ->where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->get();
        
        // For each bill detail item, redistribute properly across all invoices
        foreach ($billDetails as $billDetail) {
            // Get all invoice details for this bill detail item
            $invoiceDetails = DB::table('loan_case_invoice_details')
                ->where('quotation_item_id', $billDetail->id)
                ->where('status', '<>', 99)
                ->orderBy('invoice_main_id')
                ->get();
            
            if ($invoiceDetails->count() != $invoiceCount) {
                continue; // Skip if count doesn't match
            }
            
            // Get the CORRECT invoice base total for this item
            // Use ori_invoice_amt (which should be the invoice base amount, not quotation)
            // If not available, use the sum of current amounts as fallback
            $invoiceBaseTotal = $invoiceDetails->first()->ori_invoice_amt ?? $invoiceDetails->sum('amount');
            
            // If ori_invoice_amt is 0 or null, try to get from bill detail
            if ($invoiceBaseTotal == 0 || $invoiceBaseTotal == null) {
                $billDetailRecord = DB::table('loan_case_bill_details')
                    ->where('id', $billDetail->id)
                    ->first();
                if ($billDetailRecord) {
                    // Try to get invoice base amount from bill detail
                    // If bill has invoice amounts stored, use those
                    $invoiceBaseTotal = $billDetailRecord->ori_invoice_amt ?? $billDetailRecord->quo_amount_no_sst ?? $invoiceDetails->sum('amount');
                }
            }
            
            // Redistribute using the same logic as distributeAmountForInvoice
            // This ensures consistency with split invoice logic
            $baseAmount = $invoiceBaseTotal / $invoiceCount;
            $totalDistributed = 0;
            $index = 0;
            
            foreach ($invoiceDetails as $detail) {
                if ($index < $invoiceCount - 1) {
                    // First N-1 invoices: get rounded division
                    $newAmount = round($baseAmount, 2);
                    $totalDistributed += $newAmount;
                } else {
                    // Last invoice: get remainder to ensure exact total matches original
                    $newAmount = round($invoiceBaseTotal - $totalDistributed, 2);
                }
                
                // Always update to ensure correct distribution
                DB::table('loan_case_invoice_details')
                    ->where('id', $detail->id)
                    ->update([
                        'amount' => $newAmount,
                        'updated_at' => now()
                    ]);
                
                $index++;
            }
        }
    }

    /**
     * Get list of invoice IDs with wrong pfee2_inv
     */
    private function getWrongInvoicesList($limit = 100)
    {
        $invoices = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
            ->select('im.id')
            ->where('im.status', '<>', 99)
            ->limit($limit)
            ->get();
        
        $wrongIds = [];
        foreach ($invoices as $inv) {
            $calculated = $this->calculateInvoiceAmountsFromDetails(
                $inv->id,
                DB::table('loan_case_bill_main')->where('id', DB::table('loan_case_invoice_main')->where('id', $inv->id)->value('loan_case_main_bill_id'))->value('sst_rate') ?? 6
            );
            
            $stored = DB::table('loan_case_invoice_main')->where('id', $inv->id)->first();
            $pfee2Diff = abs($stored->pfee2_inv - $calculated['pfee2']);
            $reimbDiff = abs(($stored->reimbursement_amount ?? 0) - $calculated['reimbursement_amount']);
            
            if ($pfee2Diff > 0.01 || $reimbDiff > 0.01) {
                $wrongIds[] = $inv->id;
            }
        }
        
        return $wrongIds;
    }
}

