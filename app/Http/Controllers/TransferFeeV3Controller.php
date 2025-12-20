<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountCode;
use App\Models\AccountLog;
use App\Models\BankReconRecord;
use App\Models\Branch;
use App\Models\CaseAccountTransaction;
use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\caseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\Customer;
use App\Models\EInvoiceDetails;
use App\Models\EInvoiceMain;
use App\Models\InvoiceBillingParty;
use App\Models\JournalEntryDetails;
use App\Models\JournalEntryMain;
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseBillDetails;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\LoanCaseInvoiceMain;
use App\Models\OfficeBankAccount;
use App\Models\Parameter;
use App\Models\SSTDetails;
use App\Models\SSTDetailsDelete;
use App\Models\SSTMain;
use App\Models\TransferFeeDetails;
use App\Models\TransferFeeDetailsDelete;
use App\Models\TransferFeeMain;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BranchController;
use App\Services\BranchAccessService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferFeeV3Controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getAccessCode()
    {
        return 'TransferFeePermission';
    }

    public static function getSSTAccessCode()
    {
        return 'SSTPermission';
    }

    public static function getJEAccessCode()
    {
        return 'JournalEntryPermission';
    }

                /**
             * Display Transfer Fee V3 listing page - SIMPLE APPROACH
             */
            public function transferFeeListV3(Request $request)
            {
                $current_user = auth()->user();
                $branchInfo = BranchController::manageBranchAccess();

                if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
                    return redirect()->route('dashboard.index');
                }

                // Build the query
                $query = DB::table('transfer_fee_main as m')
                    ->leftJoin('office_bank_account as b1', 'b1.id', '=', 'm.transfer_from')
                    ->leftJoin('office_bank_account as b2', 'b2.id', '=', 'm.transfer_to')
                    ->leftJoin('users as u', 'u.id', '=', 'm.transfer_by')
                    ->leftJoin('branch as br', 'br.id', '=', 'm.branch_id')
                    ->select(
                        'm.*',
                        'b1.name as transfer_from_bank',
                        'b2.name as transfer_to_bank',
                        'b1.account_no as transfer_from_bank_acc_no',
                        'b2.account_no as transfer_to_bank_acc_no',
                        'u.name as created_by_name',
                        'br.name as branch_name'
                    )
                    ->where('m.status', '<>', 99);

                // Apply filters
                if ($request->input("transaction_id")) {
                    $query = $query->where('m.transaction_id', 'LIKE', '%' . $request->input("transaction_id") . '%');
                }
                if ($request->input("transfer_date_from")) {
                    $query = $query->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
                }
                if ($request->input("transfer_date_to")) {
                    $query = $query->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
                }
                if ($request->input("branch_id") && $request->input("branch_id") != '0') {
                    $query = $query->where('m.branch_id', '=', $request->input("branch_id"));
                }

                // User access control
                if (in_array($current_user->menuroles, ['maker'])) {
                    if (in_array($current_user->branch_id, [5,6])) {
                        $query = $query->whereIn('b2.branch_id', [5,6]);
                    } else {
                        $query = $query->where('b2.branch_id', '=', $current_user->branch_id);
                    }
                } else if (in_array($current_user->menuroles, ['sales'])) {
                    if (in_array($current_user->id, [51, 32])) {
                        $query = $query->whereIn('b2.branch_id', [5, 6]);
                    }
                } else {
                    if (in_array($current_user->id, [13])) {
                        $query = $query->whereIn('b2.branch_id', [$current_user->branch_id]);
                    }
                }

                // Handle sorting
                $sortBy = $request->input('sort_by', 'created_at');
                $sortOrder = $request->input('sort_order', 'DESC');
                
                // Validate sort fields
                $allowedSortFields = ['created_at', 'transfer_date', 'transaction_id', 'transfer_amount', 'branch_name', 'created_by_name'];
                if (!in_array($sortBy, $allowedSortFields)) {
                    $sortBy = 'created_at';
                }
                
                // Validate sort order
                if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) {
                    $sortOrder = 'DESC';
                }
                
                // Apply sorting
                if ($sortBy === 'created_at') {
                    $query = $query->orderBy('m.created_at', $sortOrder);
                } elseif ($sortBy === 'transfer_date') {
                    $query = $query->orderBy('m.transfer_date', $sortOrder);
                } elseif ($sortBy === 'transaction_id') {
                    $query = $query->orderBy('m.transaction_id', $sortOrder);
                } elseif ($sortBy === 'transfer_amount') {
                    $query = $query->orderBy('m.transfer_amount', $sortOrder);
                } elseif ($sortBy === 'branch_name') {
                    $query = $query->orderBy('br.name', $sortOrder);
                } elseif ($sortBy === 'created_by_name') {
                    $query = $query->orderBy('u.name', $sortOrder);
                }
                
                // Get per page parameter with validation
                $perPage = $request->input('per_page', 10);
                $allowedPerPage = [5, 10, 25, 50, 100];
                if (!in_array($perPage, $allowedPerPage)) {
                    $perPage = 10;
                }
                
                // Debug logging
                \Illuminate\Support\Facades\Log::info('Transfer Fee V3 - All request parameters: ' . json_encode($request->all()));
                \Illuminate\Support\Facades\Log::info('Transfer Fee V3 - Per page requested: ' . $request->input('per_page') . ', Using: ' . $perPage);
                
                // Force per_page to 50 for testing - REMOVED FOR PRODUCTION
                // if ($request->input('per_page') == '50') {
                //     $perPage = 50;
                //     \Illuminate\Support\Facades\Log::info('Transfer Fee V3 - FORCED per_page to 50 for testing');
                // }
                
                $TransferFeeMain = $query->paginate($perPage);
                
                // Set the pagination path to ensure proper URL generation
                $TransferFeeMain->setPath($request->url());
                
                // Also set the query parameters
                $TransferFeeMain->appends($request->query());

                // Check if this is an AJAX request
                if ($request->ajax()) {
                    $tableBody = view('dashboard.transfer-fee-v3.partials.table-body', [
                        'TransferFeeMain' => $TransferFeeMain
                    ])->render();
                    
                    // Generate custom AJAX pagination
                    $queryParams = $request->query();
                    $pagination = $this->generateAjaxPagination($TransferFeeMain, $queryParams);
                    
                    // Debug: Log the pagination HTML to see what's being generated
                    \Illuminate\Support\Facades\Log::info('Generated pagination type: ' . gettype($pagination));
                    \Illuminate\Support\Facades\Log::info('Generated pagination HTML: ' . $pagination);
                    
                                         $hasFilters = $request->input("transaction_id") || 
                                  $request->input("transfer_date_from") || 
                                  $request->input("transfer_date_to") || 
                                  ($request->input("branch_id") && $request->input("branch_id") != '0');
                    
                    // Generate entries info
                    $entriesInfo = '<small><i class="fa fa-info-circle"></i> Showing ' . 
                        ($TransferFeeMain->firstItem() ?? 0) . ' to ' . 
                        ($TransferFeeMain->lastItem() ?? 0) . ' of ' . 
                        ($TransferFeeMain->total() ?? 0) . ' entries</small>';
                    
                    return response()->json([
                        'tableBody' => $tableBody,
                        'pagination' => $pagination,
                        'hasFilters' => $hasFilters,
                        'sortBy' => $sortBy,
                        'sortOrder' => $sortOrder,
                        'perPage' => $perPage,
                        'entriesInfo' => $entriesInfo,
                        'debug' => [
                            'requested_per_page' => $request->input('per_page'),
                            'actual_per_page' => $perPage,
                            'total_items' => $TransferFeeMain->total(),
                            'current_page' => $TransferFeeMain->currentPage(),
                            'per_page_from_paginator' => $TransferFeeMain->perPage()
                        ]
                    ]);
                }

                                 return view('dashboard.transfer-fee-v3.index', [
                     'TransferFeeMain' => $TransferFeeMain,
                     'current_user' => $current_user,
                     'Branchs' => $branchInfo['branch'],
                     'filters' => [
                         'transaction_id' => $request->input("transaction_id"),
                         'transfer_date_from' => $request->input("transfer_date_from"),
                         'transfer_date_to' => $request->input("transfer_date_to"),
                         'branch_id' => $request->input("branch_id")
                     ],
                     'sortBy' => $sortBy,
                     'sortOrder' => $sortOrder
                 ]);
            }

    /**
     * Display Transfer Fee V3 creation page
     */
    public function transferFeeCreateV3()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5,6])->get();
            } else  {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        return view('dashboard.transfer-fee-v3.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch']
        ]);
    }



    /**
     * Get available invoices for transfer (V3 - Invoice-based) - SIMPLIFIED VERSION
     */
    public function getTransferInvoiceListV3(Request $request)
    {
        try {
            $current_user = auth()->user();
            
            // Get page number and per page from request
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20); // Default to 20, but allow user to change
            
            // Get search parameters
            $searchInvoiceNo = $request->input('search_invoice_no');
            $searchCaseRef = $request->input('search_case_ref');
            $searchClient = $request->input('search_client');
            $searchBillingParty = $request->input('search_billing_party');
            $filterBranch = $request->input('filter_branch');
            $filterStartDate = $request->input('filter_start_date');
            $filterEndDate = $request->input('filter_end_date');
            
            // Get sorting parameters
            $sortField = $request->input('sort_field');
            $sortOrder = $request->input('sort_order', 'asc');
            
            // SIMPLE VERSION - Using bill data for amounts since invoice amounts might be zero
            $query = DB::table('loan_case_invoice_main as im')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
                ->select(
                    'im.id',
                    'im.invoice_no',
                    DB::raw('DATE(im.Invoice_date) as invoice_date'),
                    'im.status',
                    'im.loan_case_main_bill_id',
                    'im.transferred_pfee_amt',
                    'im.transferred_sst_amt',
                    'im.pfee1_inv', // Use invoice data directly
                    'im.pfee2_inv', // Use invoice data directly
                    'im.sst_inv',   // Use invoice data directly
                    'im.reimbursement_amount', // Add reimbursement amount
                    'im.reimbursement_sst', // Add reimbursement SST
                    'im.transferred_reimbursement_amt', // Add transferred reimbursement amount
                    'im.transferred_reimbursement_sst_amt', // Add transferred reimbursement SST amount
                    'b.payment_receipt_date',
                'b.collected_amt as bill_collected_amt',
                'b.total_amt as bill_total_amt', // Payment receipt date from bill table
                    'b.invoice_no as bill_invoice_no',
                    DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                    'b.invoice_branch_id', // Add branch ID for filtering
                    'l.case_ref_no',
                    'l.id as case_id', // Add case_id for hyperlink
                    'c.name as client_name',
                    'ibp.customer_code',
                    'ibp.customer_name as billing_party_name'
                )
                ->where('im.status', '<>', 99)
                ->where('im.transferred_to_office_bank', '=', 0) // Only show invoices that haven't been transferred
                ->whereNotNull('im.loan_case_main_bill_id') // Only show invoices with valid bill IDs
                ->where('im.loan_case_main_bill_id', '>', 0); // Ensure bill ID is greater than 0

            // Apply centralized branch filtering
            BranchAccessService::applyBranchFilter($query, $current_user, 'b.invoice_branch_id');
            
            // DEBUG: Log the query for troubleshooting
            \Illuminate\Support\Facades\Log::info('Transfer Fee V3 Query Debug:', [
                'user_id' => $current_user->id,
                'user_role' => $current_user->menuroles,
                'user_branch' => $current_user->branch_id,
                'search_invoice_no' => $searchInvoiceNo,
                'accessible_branches' => \App\Services\BranchAccessService::getAccessibleBranchIds($current_user)
            ]);

            // Exclude invoices that are already added to the current transfer fee (for edit mode)
            $currentTransferFeeId = $request->input('current_transfer_fee_id');
            if ($currentTransferFeeId) {
                $existingInvoiceIds = TransferFeeDetails::where('transfer_fee_main_id', $currentTransferFeeId)
                    ->pluck('loan_case_invoice_main_id')
                    ->toArray();
                
                if (!empty($existingInvoiceIds)) {
                    $query = $query->whereNotIn('im.id', $existingInvoiceIds);
                }
            }

            // Apply search filters
            if ($searchInvoiceNo) {
                // Handle multiple invoice numbers (comma-separated or new line separated)
                $invoiceNumbers = array_filter(array_map('trim', 
                    preg_split('/[,\n\r]+/', $searchInvoiceNo)
                ));
                
                if (!empty($invoiceNumbers)) {
                    $query = $query->where(function($q) use ($invoiceNumbers) {
                        foreach ($invoiceNumbers as $index => $invoiceNo) {
                            if ($index === 0) {
                                $q->where('im.invoice_no', 'LIKE', '%' . $invoiceNo . '%');
                            } else {
                                $q->orWhere('im.invoice_no', 'LIKE', '%' . $invoiceNo . '%');
                            }
                        }
                    });
                }
                
                // DEBUG: Special check for DP20000820
                if (in_array('DP20000820', $invoiceNumbers)) {
                    \Illuminate\Support\Facades\Log::info('Searching for DP20000820 - Query before search filter:', [
                        'sql' => $query->toSql(),
                        'bindings' => $query->getBindings()
                    ]);
                }
            }
            if ($searchCaseRef) {
                $query = $query->where('l.case_ref_no', 'LIKE', '%' . $searchCaseRef . '%');
            }
            if ($searchClient) {
                $query = $query->where('c.name', 'LIKE', '%' . $searchClient . '%');
            }
            if ($searchBillingParty) {
                $query = $query->where('ibp.customer_name', 'LIKE', '%' . $searchBillingParty . '%');
            }

            // Apply additional filters
            if ($filterBranch) {
                $query = $query->where('b.invoice_branch_id', '=', $filterBranch);
            }
            
            if ($filterStartDate) {
                $query = $query->where('b.payment_receipt_date', '>=', $filterStartDate);
            }
            
            if ($filterEndDate) {
                $query = $query->where('b.payment_receipt_date', '<=', $filterEndDate);
            }

            // Apply sorting
            if ($sortField && in_array($sortOrder, ['asc', 'desc'])) {
                switch ($sortField) {
                    case 'case_ref_no':
                        $query = $query->orderBy('l.case_ref_no', $sortOrder);
                        break;
                    case 'invoice_no':
                        $query = $query->orderBy('im.invoice_no', $sortOrder);
                        break;
                    case 'invoice_date':
                        $query = $query->orderBy('im.Invoice_date', $sortOrder);
                        break;
                    case 'total_amount':
                        $query = $query->orderByRaw("(im.pfee1_inv + im.pfee2_inv + im.sst_inv) {$sortOrder}");
                        break;
                    case 'collected_amount':
                        $query = $query->orderByRaw("(im.pfee1_inv + im.pfee2_inv + im.sst_inv) {$sortOrder}");
                        break;
                    case 'pfee':
                        $query = $query->orderByRaw("(im.pfee1_inv + im.pfee2_inv) {$sortOrder}");
                        break;
                    case 'sst':
                        $query = $query->orderBy('im.sst_inv', $sortOrder);
                        break;
                    case 'pfee_to_transfer':
                        $query = $query->orderByRaw("(im.pfee1_inv + im.pfee2_inv - im.transferred_pfee_amt) {$sortOrder}");
                        break;
                    case 'sst_to_transfer':
                        $query = $query->orderByRaw("(im.sst_inv - im.transferred_sst_amt) {$sortOrder}");
                        break;
                    case 'reimbursement':
                        $query = $query->orderByRaw("(im.reimbursement_amount + im.reimbursement_sst) {$sortOrder}");
                        break;
                    case 'reimbursement_to_transfer':
                        $query = $query->orderByRaw("(im.reimbursement_amount - im.transferred_reimbursement_amt) {$sortOrder}");
                        break;
                    case 'reimbursement_sst_to_transfer':
                        $query = $query->orderByRaw("(im.reimbursement_sst - im.transferred_reimbursement_sst_amt) {$sortOrder}");
                        break;
                    case 'transferred_bal':
                        $query = $query->orderBy('im.transferred_pfee_amt', $sortOrder);
                        break;
                    case 'transferred_sst':
                        $query = $query->orderBy('im.transferred_sst_amt', $sortOrder);
                        break;
                    case 'transferred_reimbursement':
                        $query = $query->orderBy('im.transferred_reimbursement_amt', $sortOrder);
                        break;
                    case 'transferred_reimbursement_sst':
                        $query = $query->orderBy('im.transferred_reimbursement_sst_amt', $sortOrder);
                        break;
                    case 'payment_date':
                        $query = $query->orderBy('b.payment_receipt_date', $sortOrder);
                        break;
                    default:
                        // Default sorting by invoice date if no valid sort field
                        $query = $query->orderBy('im.Invoice_date', 'desc');
                        break;
                }
            } else {
                // Default sorting by invoice date
                $query = $query->orderBy('im.Invoice_date', 'desc');
            }

            // Get total count for pagination
            $totalCount = $query->count();
            
            // DEBUG: Final query check for DP20000820
            if ($searchInvoiceNo && strpos($searchInvoiceNo, 'DP20000820') !== false) {
                \Illuminate\Support\Facades\Log::info('Final Query for DP20000820:', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                    'total_count' => $totalCount
                ]);
            }
            
            // Get paginated results
            $rows = $query->offset(($page - 1) * $perPage)
                         ->limit($perPage)
                         ->get();

            $invoiceList = view('dashboard.transfer-fee-v3.table.tbl-transfer-invoice-list', [
                'rows' => $rows,
                'current_user' => auth()->user(),
                'currentPage' => $page,
                'totalPages' => ceil($totalCount / $perPage),
                'totalCount' => $totalCount
            ])->render();

            return response()->json([
                'status' => 1,
                'invoiceList' => $invoiceList,
                'count' => count($rows),
                'totalCount' => $totalCount,
                'currentPage' => $page,
                'totalPages' => ceil($totalCount / $perPage)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new transfer fee (V3 - Invoice-based)
     * Complete implementation following original system flow
     */
    public function createNewTransferFeeV3(Request $request)
    {
        try {
            $current_user = auth()->user();
            $total_amount = 0;

            // Validate required fields
            if (!$request->input("transfer_date") || !$request->input("trx_id") || 
                !$request->input("transfer_from") || !$request->input("transfer_to") || 
                !$request->input("purpose")) {
                return response()->json(['status' => 0, 'message' => 'All required fields must be filled']);
            }

            // Validate that transfer_from and transfer_to are different
            if ($request->input("transfer_from") == $request->input("transfer_to")) {
                return response()->json(['status' => 0, 'message' => 'Transfer from and transfer to accounts cannot be the same']);
            }

            // Validate that invoices are selected
            if (!$request->input('add_invoice')) {
                return response()->json(['status' => 0, 'message' => 'Please select at least one invoice for transfer']);
            }

            // Create main transfer fee record
            $TransferFeeMain = new TransferFeeMain();
            $TransferFeeMain->transfer_date = $request->input("transfer_date");
            $TransferFeeMain->transfer_by = $current_user->id;
            $TransferFeeMain->transaction_id = $request->input("trx_id");
            $TransferFeeMain->receipt_no = '';
            $TransferFeeMain->voucher_no = '';
            $TransferFeeMain->transfer_from = $request->input("transfer_from");
            $TransferFeeMain->transfer_to = $request->input("transfer_to");
            $TransferFeeMain->purpose = $request->input("purpose");
            $TransferFeeMain->status = 1;
            $TransferFeeMain->created_at = date('Y-m-d H:i:s');

            // Get branch from destination bank account
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('id', $request->input("transfer_to"))->first();
            if ($OfficeBankAccount) {
                $TransferFeeMain->branch_id = $OfficeBankAccount->branch_id;
            }

            $TransferFeeMain->save();

            // Process selected invoices
            $add_invoices = json_decode($request->input('add_invoice'), true);
            
            // Validate invoice data structure
            if (!is_array($add_invoices)) {
                return response()->json(['status' => 0, 'message' => 'Invalid invoice data format']);
            }
            
            // Validate that all required invoice fields are present
            foreach ($add_invoices as $index => $invoice) {
                if (!isset($invoice['id']) || !isset($invoice['bill_id']) || !isset($invoice['value']) || !isset($invoice['sst'])) {
                    return response()->json(['status' => 0, 'message' => 'Missing required invoice data at index ' . $index]);
                }
                
                // Validate reimbursement fields if present
                if (isset($invoice['reimbursement']) && !is_numeric($invoice['reimbursement'])) {
                    return response()->json(['status' => 0, 'message' => 'Invalid reimbursement amount at index ' . $index]);
                }
                if (isset($invoice['reimbursement_sst']) && !is_numeric($invoice['reimbursement_sst'])) {
                    return response()->json(['status' => 0, 'message' => 'Invalid reimbursement SST amount at index ' . $index]);
                }
                
                // Validate that bill ID is valid
                if (!$invoice['bill_id'] || $invoice['bill_id'] == 0 || $invoice['bill_id'] == '0') {
                    return response()->json(['status' => 0, 'message' => 'Invalid bill ID for invoice at index ' . $index . '. Bill ID cannot be 0 or null.']);
                }
                
                // Validate that invoice exists in database
                $invoiceExists = LoanCaseInvoiceMain::where('id', $invoice['id'])->exists();
                if (!$invoiceExists) {
                    return response()->json(['status' => 0, 'message' => 'Invoice with ID ' . $invoice['id'] . ' not found in database']);
                }
                
                // Validate that bill exists in database
                $billExists = LoanCaseBillMain::where('id', $invoice['bill_id'])->exists();
                if (!$billExists) {
                    return response()->json(['status' => 0, 'message' => 'Bill with ID ' . $invoice['bill_id'] . ' not found in database']);
                }
            }
            
            // Log the validated invoice data for debugging
            \Illuminate\Support\Facades\Log::info('Validated invoice data for creation:', [
                'count' => count($add_invoices),
                'invoice_ids' => array_column($add_invoices, 'id'),
                'sample_data' => array_slice($add_invoices, 0, 2) // Log first 2 invoices as sample
            ]);

            if (count($add_invoices) > 0) {
                for ($i = 0; $i < count($add_invoices); $i++) {
                    // Validate and convert values to ensure they are valid numbers
                    $invoiceValue = $this->safeBcNumber($add_invoices[$i]['value'] ?? 0);
                    $invoiceSst = $this->safeBcNumber($add_invoices[$i]['sst'] ?? 0);
                    $invoiceReimbursement = $this->safeBcNumber($add_invoices[$i]['reimbursement'] ?? 0);
                    $invoiceReimbursementSst = $this->safeBcNumber($add_invoices[$i]['reimbursement_sst'] ?? 0);
                    
                    // Create transfer fee details record
                    $TransferFeeDetails = new TransferFeeDetails();
                    $total_amount += $invoiceValue + $invoiceSst + $invoiceReimbursement + $invoiceReimbursementSst;

                    $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                    $TransferFeeDetails->loan_case_invoice_main_id = $add_invoices[$i]['id'];
                    $TransferFeeDetails->loan_case_main_bill_id = $add_invoices[$i]['bill_id'];
                    $TransferFeeDetails->created_by = $current_user->id;
                    $TransferFeeDetails->transfer_amount = $invoiceValue; // Only professional fee amount

                    if ($invoiceSst > 0) {
                        $TransferFeeDetails->sst_amount = $invoiceSst;
                    }
                    
                    if ($invoiceReimbursement > 0) {
                        $TransferFeeDetails->reimbursement_amount = $invoiceReimbursement;
                    }
                    
                    if ($invoiceReimbursementSst > 0) {
                        $TransferFeeDetails->reimbursement_sst_amount = $invoiceReimbursementSst;
                    }

                    $TransferFeeDetails->status = 1;
                    $TransferFeeDetails->created_at = date('Y-m-d H:i:s');
                    $TransferFeeDetails->save();

                    // Update invoice record (loan_case_invoice_main)
                    $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $add_invoices[$i]['id'])->first();
                    if ($LoanCaseInvoiceMain) {
                        // Calculate total transferred amount for this invoice
                        $TransferFeeDetailsSum = TransferFeeDetails::where('loan_case_invoice_main_id', '=', $add_invoices[$i]['id'])->get();
                        
                        $SumTransferFee = 0;
                        if (count($TransferFeeDetailsSum) > 0) {
                            for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                                // transfer_amount now contains only the professional fee amount
                                $SumTransferFee += $TransferFeeDetailsSum[$j]->transfer_amount;
                            }
                        }
                        
                        $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFee;
                        $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                        
                        // Calculate total transferred SST amount for this invoice
                        $TransferFeeDetailsSumSst = TransferFeeDetails::where('loan_case_invoice_main_id', '=', $add_invoices[$i]['id'])->get();
                        $SumTransferSst = 0;
                        if (count($TransferFeeDetailsSumSst) > 0) {
                            for ($j = 0; $j < count($TransferFeeDetailsSumSst); $j++) {
                                $SumTransferSst += $TransferFeeDetailsSumSst[$j]->sst_amount ?? 0;
                            }
                        }
                        
                        $LoanCaseInvoiceMain->transferred_sst_amt = $SumTransferSst;
                        
                        // Calculate total transferred reimbursement amounts for this invoice
                        $TransferFeeDetailsSumReimbursement = TransferFeeDetails::where('loan_case_invoice_main_id', '=', $add_invoices[$i]['id'])->get();
                        $SumTransferReimbursement = 0;
                        $SumTransferReimbursementSst = 0;
                        if (count($TransferFeeDetailsSumReimbursement) > 0) {
                            for ($j = 0; $j < count($TransferFeeDetailsSumReimbursement); $j++) {
                                $SumTransferReimbursement += $TransferFeeDetailsSumReimbursement[$j]->reimbursement_amount ?? 0;
                                $SumTransferReimbursementSst += $TransferFeeDetailsSumReimbursement[$j]->reimbursement_sst_amount ?? 0;
                            }
                        }
                        
                        $LoanCaseInvoiceMain->transferred_reimbursement_amt = $SumTransferReimbursement;
                        $LoanCaseInvoiceMain->transferred_reimbursement_sst_amt = $SumTransferReimbursementSst;
                        
                        // Check if all amounts (pfee, SST, reimbursement, reimbursement SST) are fully transferred
                        $remaining_pfee = bcsub($inv_pfee, $SumTransferFee, 2);
                        $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSst, 2);
                        $remaining_reimbursement = bcsub($LoanCaseInvoiceMain->reimbursement_amount, $SumTransferReimbursement, 2);
                        $remaining_reimbursement_sst = bcsub($LoanCaseInvoiceMain->reimbursement_sst, $SumTransferReimbursementSst, 2);
                        
                        // Mark as fully transferred only if all amounts are <= 0
                        if ($remaining_pfee <= 0 && $remaining_sst <= 0 && $remaining_reimbursement <= 0 && $remaining_reimbursement_sst <= 0) {
                            $LoanCaseInvoiceMain->transferred_to_office_bank = 1;
                        } else {
                            $LoanCaseInvoiceMain->transferred_to_office_bank = 0;
                        }
                        
                        $LoanCaseInvoiceMain->save();
                    }

                    // Update bill record (loan_case_bill_main) for backward compatibility
                    $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $add_invoices[$i]['bill_id'])->first();
                    if ($LoanCaseBillMain) {
                        // Calculate total transferred amount for this bill
                        $TransferFeeDetailsSumBill = TransferFeeDetails::where('loan_case_main_bill_id', '=', $add_invoices[$i]['bill_id'])->get();
                        
                        $SumTransferFeeBill = 0;
                        if (count($TransferFeeDetailsSumBill) > 0) {
                            for ($j = 0; $j < count($TransferFeeDetailsSumBill); $j++) {
                                // transfer_amount now contains only the professional fee amount
                                $SumTransferFeeBill += $TransferFeeDetailsSumBill[$j]->transfer_amount;
                            }
                        }
                        
                        $LoanCaseBillMain->transferred_pfee_amt = $SumTransferFeeBill;
                        // Use invoice data for consistency, but still update bill record for backward compatibility
                        $inv_pfee_bill = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                        
                        // Calculate total transferred SST amount for this bill
                        $TransferFeeDetailsSumSstBill = TransferFeeDetails::where('loan_case_main_bill_id', '=', $add_invoices[$i]['bill_id'])->get();
                        $SumTransferSstBill = 0;
                        if (count($TransferFeeDetailsSumSstBill) > 0) {
                            for ($j = 0; $j < count($TransferFeeDetailsSumSstBill); $j++) {
                                $SumTransferSstBill += $TransferFeeDetailsSumSstBill[$j]->sst_amount ?? 0;
                            }
                        }
                        
                        $LoanCaseBillMain->transferred_sst_amt = $SumTransferSstBill;
                        
                        // Note: Reimbursement amounts are only tracked in loan_case_invoice_main
                        // loan_case_bill_main is kept for backward compatibility with pfee and sst only
                        
                        // Check if pfee and SST are fully transferred (reimbursement is handled in invoice table)
                        $remaining_pfee_bill = bcsub($inv_pfee_bill, $SumTransferFeeBill, 2);
                        $remaining_sst_bill = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSstBill, 2);
                        
                        // Mark as fully transferred only if pfee and SST are <= 0
                        if ($remaining_pfee_bill <= 0 && $remaining_sst_bill <= 0) {
                            $LoanCaseBillMain->transferred_to_office_bank = 1;
                        } else {
                            $LoanCaseBillMain->transferred_to_office_bank = 0;
                        }
                        
                        $LoanCaseBillMain->save();
                    }

                    // Create ledger entries for accounting
                    $this->addLedgerEntriesV3($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $invoiceSst, $invoiceValue, $invoiceReimbursement, $invoiceReimbursementSst);
                }
            }

            // Update total amount in main record
            $TransferFeeMain->transfer_amount = $total_amount;
            $TransferFeeMain->save();

            // Update transfer fee main amount (for consistency with original system)
            $this->updateTransferFeeMainAmt($TransferFeeMain->id);
            
            // Recalculate all invoice totals to ensure consistency
            $this->recalculateAllInvoiceTotals($TransferFeeMain->id);

            // Create account log entry
            $this->addAccountLogEntry('CREATE', $TransferFeeMain, $add_invoices);

            return response()->json([
                'status' => 1, 
                'message' => 'Transfer fee created successfully',
                'data' => $total_amount,
                'transfer_id' => $TransferFeeMain->id
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Transfer Fee V3 Creation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0, 
                'message' => 'Error creating transfer fee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Transfer Fee Main Amount (for consistency with original system)
     */
    public function updateTransferFeeMainAmt($id)
    {
        $SumTransferFee = 0;

        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        if (!$TransferFeeMain) {
            return;
        }
        
        $TransferFeeDetailsSum = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();

        if (count($TransferFeeDetailsSum) > 0) {
            for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                // Include all amount components: transfer_amount + sst_amount + reimbursement_amount + reimbursement_sst_amount
                $SumTransferFee += $TransferFeeDetailsSum[$j]->transfer_amount ?? 0;
                $SumTransferFee += $TransferFeeDetailsSum[$j]->sst_amount ?? 0;
                $SumTransferFee += $TransferFeeDetailsSum[$j]->reimbursement_amount ?? 0;
                $SumTransferFee += $TransferFeeDetailsSum[$j]->reimbursement_sst_amount ?? 0;
            }
        }

        $oldAmount = $TransferFeeMain->transfer_amount;
        $TransferFeeMain->transfer_amount = round($SumTransferFee, 2);
        $TransferFeeMain->save();
        
        if (abs($oldAmount - $TransferFeeMain->transfer_amount) > 0.01) {
            \Illuminate\Support\Facades\Log::info("Updated transfer_fee_main amount", [
                'transfer_fee_main_id' => $id,
                'old_amount' => $oldAmount,
                'new_amount' => $TransferFeeMain->transfer_amount
            ]);
        }
    }



    /**
     * Show Transfer Fee V3
     */
    public function transferFeeShowV3($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (!$TransferFeeMain) {
            return redirect()->route('transferfee.index')->with('error', 'Transfer fee record not found.');
        }

        // Get existing transfer fee details
        $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
            ->select(
                'transfer_fee_details.*',
                'im.invoice_no',
                DB::raw('DATE(im.Invoice_date) as invoice_date'),
                'im.transferred_pfee_amt',
                'im.transferred_sst_amt',
                'im.pfee1_inv',
                'im.pfee2_inv',
                'im.sst_inv',
                'im.reimbursement_amount',
                'im.reimbursement_sst',
                'im.transferred_reimbursement_amt',
                'im.transferred_reimbursement_sst_amt',
                'b.invoice_no as bill_invoice_no',
                DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                'b.payment_receipt_date',
                'b.collected_amt as bill_collected_amt',
                'b.total_amt as bill_total_amt',
                'l.case_ref_no',
                'l.id as case_id',
                'c.name as client_name',
                'ibp.customer_name as billing_party_name'
            )
            ->get();

        return view('dashboard.transfer-fee-v3.show', [
            'TransferFeeMain' => $TransferFeeMain,
            'TransferFeeDetails' => $TransferFeeDetails
        ]);
    }

    /**
     * Edit Transfer Fee V3
     */
    public function transferFeeEditV3($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (!$TransferFeeMain) {
            return redirect()->route('transferfee.index')->with('error', 'Transfer fee record not found.');
        }

        // Get existing transfer fee details
        $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
            ->select(
                'transfer_fee_details.*',
                'im.invoice_no',
                DB::raw('DATE(im.Invoice_date) as invoice_date'),
                'im.amount as invoice_amount',
                'im.transferred_pfee_amt',
                'im.transferred_sst_amt',
                'im.pfee1_inv',
                'im.pfee2_inv',
                'im.sst_inv',
                'im.reimbursement_amount',
                'im.reimbursement_sst',
                'im.transferred_reimbursement_amt',
                'im.transferred_reimbursement_sst_amt',
                'b.invoice_no as bill_invoice_no',
                DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                'b.payment_receipt_date',
                'b.collected_amt as bill_collected_amt',
                'b.total_amt as bill_total_amt',
                'l.case_ref_no',
                'l.id as case_id',
                'c.name as client_name',
                'ibp.customer_name as billing_party_name'
            )
            ->get();

        // Calculate correct available amounts considering ALL transfer records
        foreach ($TransferFeeDetails as $detail) {
            // Get total transferred amounts from ALL transfer records for this invoice
            $totalTransferredPfee = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
                ->where('transfer_fee_main_id', '!=', $id) // Exclude current transfer record
                ->sum('transfer_amount');
            
            $totalTransferredSst = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
                ->where('transfer_fee_main_id', '!=', $id) // Exclude current transfer record
                ->sum('sst_amount');
            
            $totalTransferredReimbursement = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
                ->where('transfer_fee_main_id', '!=', $id) // Exclude current transfer record
                ->sum('reimbursement_amount');
            
            $totalTransferredReimbursementSst = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->loan_case_invoice_main_id)
                ->where('transfer_fee_main_id', '!=', $id) // Exclude current transfer record
                ->sum('reimbursement_sst_amount');
            
            // Calculate available amounts
            $originalPfee = ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);
            $originalSst = $detail->sst_inv ?? 0;
            $originalReimbursement = $detail->reimbursement_amount ?? 0;
            $originalReimbursementSst = $detail->reimbursement_sst ?? 0;
            
            $detail->available_pfee = max(0, $originalPfee - $totalTransferredPfee);
            $detail->available_sst = max(0, $originalSst - $totalTransferredSst);
            $detail->available_reimbursement = max(0, $originalReimbursement - $totalTransferredReimbursement);
            $detail->available_reimbursement_sst = max(0, $originalReimbursementSst - $totalTransferredReimbursementSst);
            
            // Store current transfer amounts for reference
            $detail->current_transfer_pfee = $detail->transfer_amount ?? 0;
            $detail->current_transfer_sst = $detail->sst_amount ?? 0;
            $detail->current_transfer_reimbursement = $detail->reimbursement_amount ?? 0;
            $detail->current_transfer_reimbursement_sst = $detail->reimbursement_sst_amount ?? 0;
            
            // Use invoice amount directly from loan_case_invoice_main table (no division needed)
            $detail->invoice_total_amt = $detail->invoice_amount ?? 0;
            
            // Count invoices per bill for collected amount division (collected amount needs to be divided)
            $invoiceCount = \App\Models\LoanCaseInvoiceMain::where('loan_case_main_bill_id', $detail->loan_case_main_bill_id)
                ->where('status', 1)
                ->count();
            $invoiceCount = max(1, $invoiceCount); // Avoid division by zero
            
            // For backward compatibility, keep bill amounts but use invoice amounts for display
            // Use the actual invoice amount from database if available (most accurate)
            // Otherwise calculate using correct formula: (cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)
            if ($detail->invoice_amount && $detail->invoice_amount > 0) {
                // Use the stored invoice amount directly to avoid rounding errors
                $detail->bill_total_amt_divided = round($detail->invoice_amount, 2);
            } else {
                // Fallback: Calculate if invoice amount is not available
                // Get SST rate from bill
                $bill = \App\Models\LoanCaseBillMain::find($detail->loan_case_main_bill_id);
                $sstRate = $bill ? ($bill->sst_rate / 100) : 0;
                
                // Get category totals from invoice details
                $details = \DB::table('loan_case_invoice_details as d')
                    ->leftJoin('account_item as ai', 'ai.id', '=', 'd.account_item_id')
                    ->where('d.invoice_main_id', $detail->loan_case_invoice_main_id)
                    ->select('d.amount', 'ai.account_cat_id')
                    ->get();
                
                $sumCat1 = $details->where('account_cat_id', 1)->sum('amount');
                $sumCat2 = $details->where('account_cat_id', 2)->sum('amount');
                $sumCat3 = $details->where('account_cat_id', 3)->sum('amount');
                $sumCat4 = $details->where('account_cat_id', 4)->sum('amount');
                
                // Calculate total first, then round once to avoid rounding errors
                // Formula: (cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)
                $totalUnrounded = ($sumCat1 + ($sumCat1 * $sstRate)) + $sumCat2 + $sumCat3 + ($sumCat4 + ($sumCat4 * $sstRate));
                $detail->bill_total_amt_divided = round($totalUnrounded, 2);
            }
            
            // ====================================================================
            // OPTION 2: Show individual invoice amounts (Total amt ≠ Collected amt)
            // To revert to OPTION 1 (matching amounts), uncomment the code below
            // ====================================================================
            
            // Use invoice amount for Total amt (already calculated at line 984)
            // Keep the invoice amount - don't overwrite it
            // Note: bill_total_amt_divided is already set correctly at line 984 using invoice_amount
            
            // Check for custom total amount override (set by user via edit icon)
            $customTotalAmt = \Cache::get("transfer_fee_detail_{$detail->id}_custom_total_amt");
            if ($customTotalAmt === null) {
                $customTotalAmt = session("transfer_fee_detail_{$detail->id}_custom_total_amt");
            }
            
            // Use custom total amount if set, otherwise use calculated value
            if ($customTotalAmt !== null) {
                $detail->bill_total_amt_divided = round($customTotalAmt, 2);
            }
            // Note: bill_total_amt_divided is already set at line 984 if no custom value
            
            // Calculate Collected amt from bill collected amount (divided equally)
            $totalAmount = $detail->bill_collected_amt ?? 0;
            $calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
            $detail->bill_collected_amt_divided = $calculatedCollectedAmount;
            
            // ====================================================================
            // OPTION 1 (REVERT CODE): Uncomment below to make both amounts match
            // ====================================================================
            // $totalAmount = $detail->bill_collected_amt ?? 0;
            // $calculatedAmount = round($totalAmount / $invoiceCount, 2);
            // $detail->bill_total_amt_divided = $calculatedAmount;
            // $detail->bill_collected_amt_divided = $calculatedAmount;
        }

        // Keep individual amounts at 1340.67, round total in frontend display

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [2,5])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id',[$current_user->branch_id, 6])->get();
            } else  {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        }else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();
        
        return view('dashboard.transfer-fee-v3.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'TransferFeeMain' => $TransferFeeMain,
            'TransferFeeDetails' => $TransferFeeDetails,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    /**
     * Update Transfer Fee V3
     */
    public function transferFeeUpdateV3(Request $request, $id)
    {
        try {
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Transfer Fee Update Request Data:', [
                'id' => $id,
                'add_invoice' => $request->input('add_invoice'),
                'all_data' => $request->all()
            ]);
            
            // Decode and log the invoice data for debugging
            if ($request->input('add_invoice')) {
                $add_invoices = json_decode($request->input('add_invoice'), true);
                \Illuminate\Support\Facades\Log::info('Decoded invoice data:', [
                    'count' => count($add_invoices),
                    'invoice_ids' => array_column($add_invoices, 'id')
                ]);
            }
            
            $current_user = auth()->user();
            $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

            if (!$TransferFeeMain) {
                return response()->json(['status' => 0, 'message' => 'Transfer fee record not found']);
            }

            // Store old amount for account log
            $oldAmount = $TransferFeeMain->transfer_amount;

            // Validate required fields
            if (!$request->input("transfer_date") || !$request->input("trx_id") || 
                !$request->input("transfer_from") || !$request->input("transfer_to") || 
                !$request->input("purpose")) {
                return response()->json(['status' => 0, 'message' => 'All required fields must be filled']);
            }

            // Validate that transfer_from and transfer_to are different
            if ($request->input("transfer_from") == $request->input("transfer_to")) {
                return response()->json(['status' => 0, 'message' => 'Transfer from and transfer to accounts cannot be the same']);
            }

            // Update main record
            $TransferFeeMain->transfer_date = $request->input("transfer_date");
            $TransferFeeMain->transaction_id = $request->input("trx_id");
            $TransferFeeMain->transfer_from = $request->input("transfer_from");
            $TransferFeeMain->transfer_to = $request->input("transfer_to");
            $TransferFeeMain->purpose = $request->input("purpose");
            $TransferFeeMain->updated_at = date('Y-m-d H:i:s');

            // Get branch from destination bank account
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('id', $request->input("transfer_to"))->first();
            if ($OfficeBankAccount) {
                $TransferFeeMain->branch_id = $OfficeBankAccount->branch_id;
            }

            $TransferFeeMain->save();

            // Process new invoices only (existing invoices remain untouched)
            if ($request->input('add_invoice')) {
                $add_invoices = json_decode($request->input('add_invoice'), true);
                
                // Validate invoice data structure
                if (!is_array($add_invoices)) {
                    return response()->json(['status' => 0, 'message' => 'Invalid invoice data format']);
                }
                
                // Validate that all required invoice fields are present
                foreach ($add_invoices as $index => $invoice) {
                    if (!isset($invoice['id']) || !isset($invoice['bill_id']) || !isset($invoice['value']) || !isset($invoice['sst'])) {
                        return response()->json(['status' => 0, 'message' => 'Missing required invoice data at index ' . $index]);
                    }
                    
                    // Validate reimbursement fields if present
                    if (isset($invoice['reimbursement']) && !is_numeric($invoice['reimbursement'])) {
                        return response()->json(['status' => 0, 'message' => 'Invalid reimbursement amount at index ' . $index]);
                    }
                    if (isset($invoice['reimbursement_sst']) && !is_numeric($invoice['reimbursement_sst'])) {
                        return response()->json(['status' => 0, 'message' => 'Invalid reimbursement SST amount at index ' . $index]);
                    }
                    
                    // Validate that bill ID is valid
                    if (!$invoice['bill_id'] || $invoice['bill_id'] == 0 || $invoice['bill_id'] == '0') {
                        return response()->json(['status' => 0, 'message' => 'Invalid bill ID for invoice at index ' . $index . '. Bill ID cannot be 0 or null.']);
                    }
                    
                    // Validate that invoice exists in database
                    $invoiceExists = LoanCaseInvoiceMain::where('id', $invoice['id'])->exists();
                    if (!$invoiceExists) {
                        return response()->json(['status' => 0, 'message' => 'Invoice with ID ' . $invoice['id'] . ' not found in database']);
                    }
                    
                    // Validate that bill exists in database
                    $billExists = LoanCaseBillMain::where('id', $invoice['bill_id'])->exists();
                    if (!$billExists) {
                        return response()->json(['status' => 0, 'message' => 'Bill with ID ' . $invoice['bill_id'] . ' not found in database']);
                    }
                }
                
                // Log the new invoice data for debugging
                \Illuminate\Support\Facades\Log::info('New invoices to add:', [
                    'count' => count($add_invoices),
                    'invoice_ids' => array_column($add_invoices, 'id'),
                    'sample_data' => array_slice($add_invoices, 0, 2),
                    'transfer_fee_id' => $id
                ]);
                
                // Get existing invoices count and total for logging
                $existingInvoices = TransferFeeDetails::where('transfer_fee_main_id', $id)->get();
                $existingTotal = $existingInvoices->sum('transfer_amount') + $existingInvoices->sum('sst_amount');
                
                \Illuminate\Support\Facades\Log::info('Current state before adding new invoices:', [
                    'transfer_fee_id' => $id,
                    'existing_invoice_count' => $existingInvoices->count(),
                    'existing_total_amount' => $existingTotal
                ]);
                
                if (count($add_invoices) > 0) {
                    
                    $new_total_amount = 0;
                    
                    // Process only NEW invoices (existing ones remain untouched)
                    for ($i = 0; $i < count($add_invoices); $i++) {
                        // Validate and convert values to ensure they are valid numbers
                        $invoiceValue = $this->safeBcNumber($add_invoices[$i]['value'] ?? 0);
                        $invoiceSst = $this->safeBcNumber($add_invoices[$i]['sst'] ?? 0);
                        $invoiceReimbursement = $this->safeBcNumber($add_invoices[$i]['reimbursement'] ?? 0);
                        $invoiceReimbursementSst = $this->safeBcNumber($add_invoices[$i]['reimbursement_sst'] ?? 0);
                        
                        $invoiceId = $add_invoices[$i]['id'];
                        $billId = $add_invoices[$i]['bill_id'];
                        
                        // Check if this invoice is already in this transfer fee
                        $existingDetail = TransferFeeDetails::where('transfer_fee_main_id', $id)
                            ->where('loan_case_invoice_main_id', $invoiceId)
                            ->first();
                        
                        if ($existingDetail) {
                            // Skip if already exists - don't modify existing records
                            \Illuminate\Support\Facades\Log::info('Invoice ' . $invoiceId . ' already exists in transfer, skipping');
                            continue;
                        }
                        
                        // Create new invoice record
                        \Illuminate\Support\Facades\Log::info('Creating new invoice ' . $invoiceId . ' in transfer');
                        
                        $TransferFeeDetails = new TransferFeeDetails();
                        $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                        $TransferFeeDetails->loan_case_invoice_main_id = $invoiceId;
                        $TransferFeeDetails->loan_case_main_bill_id = $billId;
                        $TransferFeeDetails->created_by = $current_user->id;
                        $TransferFeeDetails->transfer_amount = $invoiceValue; // Only professional fee amount
                        
                        if ($invoiceSst > 0) {
                            $TransferFeeDetails->sst_amount = $invoiceSst;
                        }
                        
                        if ($invoiceReimbursement > 0) {
                            $TransferFeeDetails->reimbursement_amount = $invoiceReimbursement;
                        }
                        
                        if ($invoiceReimbursementSst > 0) {
                            $TransferFeeDetails->reimbursement_sst_amount = $invoiceReimbursementSst;
                        }
                        
                        $TransferFeeDetails->status = 1;
                        $TransferFeeDetails->created_at = date('Y-m-d H:i:s');
                        $TransferFeeDetails->save();
                        
                        $new_total_amount += $invoiceValue + $invoiceSst + $invoiceReimbursement + $invoiceReimbursementSst;
                        
                        // Update invoice record (loan_case_invoice_main) - add new amounts to existing totals
                        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $invoiceId)->first();
                        if ($LoanCaseInvoiceMain) {
                            // Add new amounts to existing transferred amounts
                            $newTransferredPfee = $LoanCaseInvoiceMain->transferred_pfee_amt + $invoiceValue;
                            $newTransferredSst = $LoanCaseInvoiceMain->transferred_sst_amt + $invoiceSst;
                            $newTransferredReimbursement = $LoanCaseInvoiceMain->transferred_reimbursement_amt + $invoiceReimbursement;
                            $newTransferredReimbursementSst = $LoanCaseInvoiceMain->transferred_reimbursement_sst_amt + $invoiceReimbursementSst;
                            
                            $LoanCaseInvoiceMain->transferred_pfee_amt = $newTransferredPfee;
                            $LoanCaseInvoiceMain->transferred_sst_amt = $newTransferredSst;
                            $LoanCaseInvoiceMain->transferred_reimbursement_amt = $newTransferredReimbursement;
                            $LoanCaseInvoiceMain->transferred_reimbursement_sst_amt = $newTransferredReimbursementSst;
                            
                            // Check if fully transferred
                            $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                            $remaining_pfee = bcsub($inv_pfee, $newTransferredPfee, 2);
                            $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $newTransferredSst, 2);
                            $remaining_reimbursement = bcsub($LoanCaseInvoiceMain->reimbursement_amount, $newTransferredReimbursement, 2);
                            $remaining_reimbursement_sst = bcsub($LoanCaseInvoiceMain->reimbursement_sst, $newTransferredReimbursementSst, 2);
                            
                            if ($remaining_pfee <= 0 && $remaining_sst <= 0 && $remaining_reimbursement <= 0 && $remaining_reimbursement_sst <= 0) {
                                $LoanCaseInvoiceMain->transferred_to_office_bank = 1;
                            } else {
                                $LoanCaseInvoiceMain->transferred_to_office_bank = 0;
                            }
                            
                            $LoanCaseInvoiceMain->save();
                            
                            \Illuminate\Support\Facades\Log::info('Updated invoice ' . $invoiceId . ' totals:', [
                                'old_transferred_pfee' => $LoanCaseInvoiceMain->transferred_pfee_amt - $invoiceValue,
                                'new_transferred_pfee' => $newTransferredPfee,
                                'old_transferred_sst' => $LoanCaseInvoiceMain->transferred_sst_amt - $invoiceSst,
                                'new_transferred_sst' => $newTransferredSst
                            ]);
                        }

                        // Update bill record (loan_case_bill_main) for backward compatibility
                        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $billId)->first();
                        if ($LoanCaseBillMain) {
                            // Add new amounts to existing transferred amounts
                            $newTransferredPfeeBill = $LoanCaseBillMain->transferred_pfee_amt + $invoiceValue;
                            $newTransferredSstBill = $LoanCaseBillMain->transferred_sst_amt + $invoiceSst;
                            
                            $LoanCaseBillMain->transferred_pfee_amt = $newTransferredPfeeBill;
                            $LoanCaseBillMain->transferred_sst_amt = $newTransferredSstBill;
                            
                            // Note: Reimbursement amounts are only tracked in loan_case_invoice_main
                            // loan_case_bill_main is kept for backward compatibility with pfee and sst only
                            
                            // Check if fully transferred (only pfee and sst for bill table)
                            $inv_pfee_bill = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                            $remaining_pfee_bill = bcsub($inv_pfee_bill, $newTransferredPfeeBill, 2);
                            $remaining_sst_bill = bcsub($LoanCaseInvoiceMain->sst_inv, $newTransferredSstBill, 2);
                            
                            // Mark as fully transferred only if pfee and SST are <= 0
                            // (reimbursement is handled in invoice table only)
                            if ($remaining_pfee_bill <= 0 && $remaining_sst_bill <= 0) {
                                $LoanCaseBillMain->transferred_to_office_bank = 1;
                            } else {
                                $LoanCaseBillMain->transferred_to_office_bank = 0;
                            }
                            
                            $LoanCaseBillMain->save();
                        }

                        // Create ledger entries for accounting
                        $this->addLedgerEntriesV3($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $invoiceSst, $invoiceValue, $invoiceReimbursement, $invoiceReimbursementSst);
                    }
                    
                    // Calculate new total (existing + new)
                    $finalTotal = $existingTotal + $new_total_amount;
                    
                    // Update total amount in main record
                    $TransferFeeMain->transfer_amount = $finalTotal;
                    $TransferFeeMain->save();

                    // Update transfer fee main amount (for consistency with original system)
                    $this->updateTransferFeeMainAmt($TransferFeeMain->id);
                    
                    // Log the final state after adding new invoices
                    \Illuminate\Support\Facades\Log::info('Transfer fee update completed (new invoices added):', [
                        'transfer_fee_id' => $id,
                        'existing_total_amount' => $existingTotal,
                        'new_invoices_amount' => $new_total_amount,
                        'final_total_amount' => $finalTotal,
                        'existing_invoice_count' => $existingInvoices->count(),
                        'new_invoice_count' => count($add_invoices),
                        'final_invoice_count' => TransferFeeDetails::where('transfer_fee_main_id', $id)->count()
                    ]);
                }
            }

            // Create account log entry for update
            $this->addAccountLogEntry('UPDATE', $TransferFeeMain, $add_invoices ?? [], $oldAmount);

            return response()->json([
                'status' => 1, 
                'message' => 'Transfer fee updated successfully',
                'data' => $TransferFeeMain->transfer_amount ?? 0,
                'transfer_id' => $TransferFeeMain->id
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Transfer Fee V3 Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0, 
                'message' => 'Error updating transfer fee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Transfer Fee V3
     */
    public function transferFeeDeleteV3($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (!$TransferFeeMain) {
            return response()->json(['status' => 0, 'message' => 'Transfer fee record not found']);
        }

        // Check if transfer fee is reconciled - if so, prevent updates
        if ($TransferFeeMain->is_recon == '1') {
            return response()->json(['status' => 0, 'message' => 'Cannot modify reconciled transfer fee records']);
        }

        // Check if user has permission to delete
        $allowedRoles = ['admin', 'account', 'maker'];
        if (!in_array($current_user->menuroles, $allowedRoles)) {
            return response()->json(['status' => 0, 'message' => 'You do not have permission to delete transfer fee records']);
        }

        // Check if transfer fee is reconciled - if so, prevent deletion
        if ($TransferFeeMain->is_recon == '1') {
            return response()->json(['status' => 0, 'message' => 'Cannot delete reconciled transfer fee records']);
        }

        // Get all transfer fee details before deletion
        $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $id)->get();

        // Delete ledger entries for each detail record
        foreach ($TransferFeeDetails as $detail) {
            // Delete LedgerEntriesV2 (new system) - delete by key_id_2 (TransferFeeDetails ID)
            $deletedLedgerV2 = LedgerEntriesV2::where('key_id_2', '=', $detail->id)
                ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT', 'SST_IN', 'SST_OUT', 'REIMB_IN', 'REIMB_OUT', 'REIMB_SST_IN', 'REIMB_SST_OUT'])->delete();
            
            // Delete LedgerEntries (old system) - delete by key_id (TransferFeeDetails ID)
            $deletedLedger = LedgerEntries::where('key_id', '=', $detail->id)
                ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'SSTIN', 'SSTOUT', 'REIMBIN', 'REIMBOUT', 'REIMBSSTIN', 'REIMBSSTOUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON', 'SSTINRECON', 'SSTOUTRECON', 'REIMBINRECON', 'REIMBOUTRECON', 'REIMBSSTINRECON', 'REIMBSSTOUTRECON'])->delete();
            
            \Illuminate\Support\Facades\Log::info('Deleted ledger entries for TransferFeeDetails ID ' . $detail->id . ': LedgerEntriesV2=' . $deletedLedgerV2 . ', LedgerEntries=' . $deletedLedger);

            // Update bill and invoice records to reverse transferred amounts
            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $detail->loan_case_main_bill_id)->first();
            if ($LoanCaseBillMain) {
                if ($LoanCaseBillMain->transferred_pfee_amt > 0) {
                    $LoanCaseBillMain->transferred_pfee_amt -= $detail->transfer_amount;
                    if ($detail->sst_amount > 0) {
                        $LoanCaseBillMain->transferred_sst_amt -= $detail->sst_amount;
                    }
                }

                // Recalculate total transferred amounts for this bill after deletion
                $TransferFeeDetailsSumBill = TransferFeeDetails::where('loan_case_main_bill_id', '=', $detail->loan_case_main_bill_id)
                    ->where('transfer_fee_main_id', '<>', $id) // Exclude the record being deleted
                    ->get();
                
                $SumTransferFeeBill = 0;
                $SumTransferSstBill = 0;
                if (count($TransferFeeDetailsSumBill) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSumBill); $j++) {
                        // transfer_amount now contains only the professional fee amount
                        $SumTransferFeeBill += $TransferFeeDetailsSumBill[$j]->transfer_amount;
                        $SumTransferSstBill += $TransferFeeDetailsSumBill[$j]->sst_amount ?? 0;
                    }
                }
                
                $LoanCaseBillMain->transferred_pfee_amt = $SumTransferFeeBill;
                $LoanCaseBillMain->transferred_sst_amt = $SumTransferSstBill;
                
                // Check if all amounts (pfee, SST, reimbursement, reimbursement SST) are still fully transferred
                $inv_pfee_bill = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;
                $remaining_pfee_bill = bcsub($inv_pfee_bill, $SumTransferFeeBill, 2);
                $remaining_sst_bill = bcsub($LoanCaseBillMain->sst_inv, $SumTransferSstBill, 2);
                
                // Mark as not fully transferred if any amount has remaining balance
                // OR if no transfer fee details remain (all deleted)
                if ($remaining_pfee_bill > 0 || $remaining_sst_bill > 0 || $SumTransferFeeBill == 0) {
                    $LoanCaseBillMain->transferred_to_office_bank = 0;
                }
                
                $LoanCaseBillMain->save();
            }

            // Update invoice record
            $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $detail->loan_case_invoice_main_id)->first();
            if ($LoanCaseInvoiceMain) {
                if ($LoanCaseInvoiceMain->transferred_pfee_amt > 0) {
                    $LoanCaseInvoiceMain->transferred_pfee_amt -= $detail->transfer_amount;
                    if ($detail->sst_amount > 0) {
                        $LoanCaseInvoiceMain->transferred_sst_amt -= $detail->sst_amount;
                    }
                }

                // Recalculate total transferred amounts for this invoice after deletion
                $TransferFeeDetailsSumInvoice = TransferFeeDetails::where('loan_case_invoice_main_id', '=', $detail->loan_case_invoice_main_id)
                    ->where('transfer_fee_main_id', '<>', $id) // Exclude the record being deleted
                    ->get();
                
                $SumTransferFeeInvoice = 0;
                $SumTransferSstInvoice = 0;
                $SumTransferReimbursementInvoice = 0;
                $SumTransferReimbursementSstInvoice = 0;
                if (count($TransferFeeDetailsSumInvoice) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSumInvoice); $j++) {
                        // transfer_amount now contains only the professional fee amount
                        $SumTransferFeeInvoice += $TransferFeeDetailsSumInvoice[$j]->transfer_amount;
                        $SumTransferSstInvoice += $TransferFeeDetailsSumInvoice[$j]->sst_amount ?? 0;
                        $SumTransferReimbursementInvoice += $TransferFeeDetailsSumInvoice[$j]->reimbursement_amount ?? 0;
                        $SumTransferReimbursementSstInvoice += $TransferFeeDetailsSumInvoice[$j]->reimbursement_sst_amount ?? 0;
                    }
                }
                
                $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFeeInvoice;
                $LoanCaseInvoiceMain->transferred_sst_amt = $SumTransferSstInvoice;
                $LoanCaseInvoiceMain->transferred_reimbursement_amt = $SumTransferReimbursementInvoice;
                $LoanCaseInvoiceMain->transferred_reimbursement_sst_amt = $SumTransferReimbursementSstInvoice;
                
                // Check if all amounts (pfee, SST, reimbursement, reimbursement SST) are still fully transferred
                $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                $remaining_pfee = bcsub($inv_pfee, $SumTransferFeeInvoice, 2);
                $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSstInvoice, 2);
                $remaining_reimbursement = bcsub($LoanCaseInvoiceMain->reimbursement_amount, $SumTransferReimbursementInvoice, 2);
                $remaining_reimbursement_sst = bcsub($LoanCaseInvoiceMain->reimbursement_sst, $SumTransferReimbursementSstInvoice, 2);
                
                // Mark as not fully transferred if any amount has remaining balance
                if ($remaining_pfee > 0 || $remaining_sst > 0 || $remaining_reimbursement > 0 || $remaining_reimbursement_sst > 0) {
                    $LoanCaseInvoiceMain->transferred_to_office_bank = 0;
                }
                
                $LoanCaseInvoiceMain->save();
            }
        }

        // Delete associated transfer fee details
        TransferFeeDetails::where('transfer_fee_main_id', $id)->delete();

        // Log the deletion to AccountLog
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = 0; // No specific case for transfer fee
        $AccountLog->bill_id = null;
        $AccountLog->action = 'DELETE';
        $AccountLog->desc = 'Transfer Fee Deleted - Transaction ID: ' . $TransferFeeMain->transaction_id . 
                           ', Amount: ' . $TransferFeeMain->transfer_amount . 
                           ', Purpose: ' . $TransferFeeMain->purpose . 
                           ', Details Count: ' . count($TransferFeeDetails);
        $AccountLog->status = '1';
        $AccountLog->ori_amt = $TransferFeeMain->transfer_amount ?? 0;
        $AccountLog->new_amt = 0; // Deleted record
        $AccountLog->object_id = $id; // Transfer fee main ID
        $AccountLog->object_id_2 = count($TransferFeeDetails); // Number of details
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        // Delete the main record
        $TransferFeeMain->delete();

        return response()->json(['status' => 1, 'message' => 'Transfer fee deleted successfully']);
    }

    /**
     * Delete Transfer Fee Detail V3
     */
    public function deleteTransferFeeDetailV3($id)
    {
        try {
            $current_user = auth()->user();
            $TransferFeeDetail = TransferFeeDetails::where('id', '=', $id)->first();

            if (!$TransferFeeDetail) {
                return response()->json(['status' => 0, 'message' => 'Transfer fee detail record not found']);
            }

            // Check if the transfer fee is reconciled
            $TransferFeeMain = TransferFeeMain::where('id', '=', $TransferFeeDetail->transfer_fee_main_id)->first();
            if ($TransferFeeMain && $TransferFeeMain->is_recon == '1') {
                return response()->json(['status' => 0, 'message' => 'Cannot delete transfer record from a reconciled transfer fee']);
            }

            // Check if user has permission to delete
            $allowedRoles = ['admin', 'account', 'maker'];
            if (!in_array($current_user->menuroles, $allowedRoles)) {
                return response()->json(['status' => 0, 'message' => 'You do not have permission to delete transfer fee records']);
            }

            // Delete ledger entries for this detail record
            $deletedLedgerV2 = LedgerEntriesV2::where('key_id_2', '=', $TransferFeeDetail->id)
                ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT', 'SST_IN', 'SST_OUT', 'REIMB_IN', 'REIMB_OUT', 'REIMB_SST_IN', 'REIMB_SST_OUT'])->delete();
            
            $deletedLedger = LedgerEntries::where('key_id', '=', $TransferFeeDetail->id)
                ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'SSTIN', 'SSTOUT', 'REIMBIN', 'REIMBOUT', 'REIMBSSTIN', 'REIMBSSTOUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON', 'SSTINRECON', 'SSTOUTRECON', 'REIMBINRECON', 'REIMBOUTRECON', 'REIMBSSTINRECON', 'REIMBSSTOUTRECON'])->delete();

            // Update bill and invoice records to reverse transferred amounts
            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $TransferFeeDetail->loan_case_main_bill_id)->first();
            if ($LoanCaseBillMain) {
                if ($LoanCaseBillMain->transferred_pfee_amt > 0) {
                    $LoanCaseBillMain->transferred_pfee_amt -= $TransferFeeDetail->transfer_amount;
                    if ($TransferFeeDetail->sst_amount > 0) {
                        $LoanCaseBillMain->transferred_sst_amt -= $TransferFeeDetail->sst_amount;
                    }
                }

                // Recalculate total transferred amounts for this bill after deletion
                $TransferFeeDetailsSumBill = TransferFeeDetails::where('loan_case_main_bill_id', '=', $TransferFeeDetail->loan_case_main_bill_id)
                    ->where('id', '<>', $id) // Exclude the record being deleted
                    ->get();
                
                $SumTransferFeeBill = 0;
                $SumTransferSstBill = 0;
                if (count($TransferFeeDetailsSumBill) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSumBill); $j++) {
                        // transfer_amount now contains only the professional fee amount
                        $SumTransferFeeBill += $TransferFeeDetailsSumBill[$j]->transfer_amount;
                        $SumTransferSstBill += $TransferFeeDetailsSumBill[$j]->sst_amount ?? 0;
                    }
                }
                
                $LoanCaseBillMain->transferred_pfee_amt = $SumTransferFeeBill;
                $LoanCaseBillMain->transferred_sst_amt = $SumTransferSstBill;
                
                // Check if all amounts (pfee, SST, reimbursement, reimbursement SST) are still fully transferred
                $inv_pfee_bill = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;
                $remaining_pfee_bill = bcsub($inv_pfee_bill, $SumTransferFeeBill, 2);
                $remaining_sst_bill = bcsub($LoanCaseBillMain->sst_inv, $SumTransferSstBill, 2);
                
                // Mark as not fully transferred if any amount has remaining balance
                // OR if no transfer fee details remain (all deleted)
                if ($remaining_pfee_bill > 0 || $remaining_sst_bill > 0 || $SumTransferFeeBill == 0) {
                    $LoanCaseBillMain->transferred_to_office_bank = 0;
                }
                
                $LoanCaseBillMain->save();
            }

            // Update invoice record
            $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $TransferFeeDetail->loan_case_invoice_main_id)->first();
            if ($LoanCaseInvoiceMain) {
                if ($LoanCaseInvoiceMain->transferred_pfee_amt > 0) {
                    $LoanCaseInvoiceMain->transferred_pfee_amt -= $TransferFeeDetail->transfer_amount;
                    if ($TransferFeeDetail->sst_amount > 0) {
                        $LoanCaseInvoiceMain->transferred_sst_amt -= $TransferFeeDetail->sst_amount;
                    }
                }

                // Recalculate total transferred amounts for this invoice after deletion
                $TransferFeeDetailsSumInvoice = TransferFeeDetails::where('loan_case_invoice_main_id', '=', $TransferFeeDetail->loan_case_invoice_main_id)
                    ->where('id', '<>', $id) // Exclude the record being deleted
                    ->get();
                
                $SumTransferFeeInvoice = 0;
                $SumTransferSstInvoice = 0;
                $SumTransferReimbursementInvoice = 0;
                $SumTransferReimbursementSstInvoice = 0;
                if (count($TransferFeeDetailsSumInvoice) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSumInvoice); $j++) {
                        // transfer_amount now contains only the professional fee amount
                        $SumTransferFeeInvoice += $TransferFeeDetailsSumInvoice[$j]->transfer_amount;
                        $SumTransferSstInvoice += $TransferFeeDetailsSumInvoice[$j]->sst_amount ?? 0;
                        $SumTransferReimbursementInvoice += $TransferFeeDetailsSumInvoice[$j]->reimbursement_amount ?? 0;
                        $SumTransferReimbursementSstInvoice += $TransferFeeDetailsSumInvoice[$j]->reimbursement_sst_amount ?? 0;
                    }
                }
                
                $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFeeInvoice;
                $LoanCaseInvoiceMain->transferred_sst_amt = $SumTransferSstInvoice;
                $LoanCaseInvoiceMain->transferred_reimbursement_amt = $SumTransferReimbursementInvoice;
                $LoanCaseInvoiceMain->transferred_reimbursement_sst_amt = $SumTransferReimbursementSstInvoice;
                
                // Check if all amounts (pfee, SST, reimbursement, reimbursement SST) are still fully transferred
                $inv_pfee_invoice = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                $remaining_pfee_invoice = bcsub($inv_pfee_invoice, $SumTransferFeeInvoice, 2);
                $remaining_sst_invoice = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSstInvoice, 2);
                $remaining_reimbursement_invoice = bcsub($LoanCaseInvoiceMain->reimbursement_amount, $SumTransferReimbursementInvoice, 2);
                $remaining_reimbursement_sst_invoice = bcsub($LoanCaseInvoiceMain->reimbursement_sst, $SumTransferReimbursementSstInvoice, 2);
                
                // Mark as not fully transferred if any amount has remaining balance
                // OR if no transfer fee details remain (all deleted)
                if ($remaining_pfee_invoice > 0 || $remaining_sst_invoice > 0 || $remaining_reimbursement_invoice > 0 || $remaining_reimbursement_sst_invoice > 0 || $SumTransferFeeInvoice == 0) {
                    $LoanCaseInvoiceMain->transferred_to_office_bank = 0;
                }
                
                $LoanCaseInvoiceMain->save();
            }

            // Delete the detail record
            $TransferFeeDetail->delete();

            // Update the main transfer fee amount
            $this->updateTransferFeeMainAmt($TransferFeeMain->id);

            return response()->json(['status' => 1, 'message' => 'Transfer fee detail deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Reconcile Transfer Fee V3
     */
    public function reconTransferFeeV3($id)
    {
        try {
            $current_user = auth()->user();
            $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

            if (!$TransferFeeMain) {
                return response()->json(['status' => 0, 'message' => 'Transfer fee record not found']);
            }

            // Check if already reconciled
            if ($TransferFeeMain->is_recon == '1') {
                return response()->json(['status' => 0, 'message' => 'Transfer fee is already reconciled']);
            }

            // Get all transfer fee details for this transfer
            $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)->get();

            // Set reconciliation date to last day of previous month
            $end = new Carbon('last day of last month');

            if (count($TransferFeeDetails) > 0) {
                for ($j = 0; $j < count($TransferFeeDetails); $j++) {
                    $detailId = $TransferFeeDetails[$j]->id;
                    
                    // Log the reconciliation process
                    \Illuminate\Support\Facades\Log::info('Reconciling TransferFeeDetails ID: ' . $detailId);
                    
                    // Update LedgerEntries (old system) - change types to RECON versions
                    $oldLedgerUpdated = LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['TRANSFERIN'])->update(['type' => 'TRANSFERINRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['TRANSFEROUT'])->update(['type' => 'TRANSFEROUTRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['SSTIN'])->update(['type' => 'SSTINRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['SSTOUT'])->update(['type' => 'SSTOUTRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBIN'])->update(['type' => 'REIMBINRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBOUT'])->update(['type' => 'REIMBOUTRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBSSTIN'])->update(['type' => 'REIMBSSTINRECON']);
                    $oldLedgerUpdated += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBSSTOUT'])->update(['type' => 'REIMBSSTOUTRECON']);
                    
                    \Illuminate\Support\Facades\Log::info('Old LedgerEntries updated: ' . $oldLedgerUpdated . ' records');

                    // Update LedgerEntriesV2 (new system) - set is_recon = 1
                    // Use key_id_2 for TransferFeeDetails ID
                    $newLedgerUpdated = LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['TRANSFER_IN'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['TRANSFER_OUT'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['SST_IN'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['SST_OUT'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_IN'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_OUT'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_SST_IN'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    $newLedgerUpdated += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_SST_OUT'])->update(['is_recon' => 1, 'recon_date' => $end]);
                    
                    \Illuminate\Support\Facades\Log::info('New LedgerEntriesV2 updated: ' . $newLedgerUpdated . ' records');
                    
                    // Log the actual ledger entries found for this detail
                    $ledgerEntries = LedgerEntriesV2::where('key_id_2', $detailId)->get();
                    \Illuminate\Support\Facades\Log::info('Found ' . $ledgerEntries->count() . ' LedgerEntriesV2 records for detail ID ' . $detailId);
                    foreach ($ledgerEntries as $entry) {
                        \Illuminate\Support\Facades\Log::info('Ledger Entry: ID=' . $entry->id . ', Type=' . $entry->type . ', Key_ID=' . $entry->key_id . ', Key_ID_2=' . $entry->key_id_2);
                    }
                }
            }

            // Set is_recon = 1 on TransferFeeMain and TransferFeeDetails
            TransferFeeMain::where('id', $id)->update(['is_recon' => 1]);
            TransferFeeDetails::where('transfer_fee_main_id', $id)->update(['is_recon' => 1]);

            // Create account log entry for reconciliation
            $this->addAccountLogEntry('RECONCILE', $TransferFeeMain, $TransferFeeDetails);

            return response()->json(['status' => 1, 'message' => 'Transfer fee reconciled successfully']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Transfer Fee V3 Reconciliation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0, 
                'message' => 'Error reconciling transfer fee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revert Transfer Fee V3 Reconciliation
     */
    public function revertReconTransferFeeV3($id)
    {
        try {
            $current_user = auth()->user();
            
            // Check if user has permission to revert reconciliation
            $allowedRoles = ['admin', 'account'];
            if (!in_array($current_user->menuroles, $allowedRoles)) {
                return response()->json(['status' => 0, 'message' => 'You do not have permission to revert reconciliation']);
            }

            $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
            if (!$TransferFeeMain) {
                return response()->json(['status' => 0, 'message' => 'Transfer fee record not found']);
            }

            // Check if transfer fee is actually reconciled
            if ($TransferFeeMain->is_recon != '1') {
                return response()->json(['status' => 0, 'message' => 'Transfer fee is not reconciled']);
            }

            $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)->get();
            
            \Illuminate\Support\Facades\Log::info('Reverting Transfer Fee V3 Reconciliation for ID: ' . $id);

            if (count($TransferFeeDetails) > 0) {
                for ($j = 0; $j < count($TransferFeeDetails); $j++) {
                    $detailId = $TransferFeeDetails[$j]->id;
                    
                    // Log the revert process
                    \Illuminate\Support\Facades\Log::info('Reverting TransferFeeDetails ID: ' . $detailId);
                    
                    // Revert LedgerEntries (old system) - change types back from RECON versions
                    $oldLedgerReverted = LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['TRANSFERINRECON'])->update(['type' => 'TRANSFERIN']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['TRANSFEROUTRECON'])->update(['type' => 'TRANSFEROUT']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['SSTINRECON'])->update(['type' => 'SSTIN']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['SSTOUTRECON'])->update(['type' => 'SSTOUT']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBINRECON'])->update(['type' => 'REIMBIN']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBOUTRECON'])->update(['type' => 'REIMBOUT']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBSSTINRECON'])->update(['type' => 'REIMBSSTIN']);
                    $oldLedgerReverted += LedgerEntries::where('key_id', $detailId)
                        ->whereIn('type', ['REIMBSSTOUTRECON'])->update(['type' => 'REIMBSSTOUT']);
                    
                    \Illuminate\Support\Facades\Log::info('Old LedgerEntries reverted: ' . $oldLedgerReverted . ' records');

                    // Revert LedgerEntriesV2 (new system) - set is_recon = 0
                    $newLedgerReverted = LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['TRANSFER_IN'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['TRANSFER_OUT'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['SST_IN'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['SST_OUT'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_IN'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_OUT'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_SST_IN'])->update(['is_recon' => 0, 'recon_date' => null]);
                    $newLedgerReverted += LedgerEntriesV2::where('key_id_2', $detailId)
                        ->whereIn('type', ['REIMB_SST_OUT'])->update(['is_recon' => 0, 'recon_date' => null]);
                    
                    \Illuminate\Support\Facades\Log::info('New LedgerEntriesV2 reverted: ' . $newLedgerReverted . ' records');
                }
            }

            // Set is_recon = 0 on TransferFeeMain and TransferFeeDetails
            TransferFeeMain::where('id', $id)->update(['is_recon' => 0]);
            TransferFeeDetails::where('transfer_fee_main_id', $id)->update(['is_recon' => 0]);

            // Create account log entry for revert reconciliation
            $this->addAccountLogEntry('REVERT_RECONCILE', $TransferFeeMain, $TransferFeeDetails);

            return response()->json(['status' => 1, 'message' => 'Transfer fee reconciliation reverted successfully']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Transfer Fee V3 Revert Reconciliation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0, 
                'message' => 'Error reverting transfer fee reconciliation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create account log entry for transfer fee operations
     */
    private function addAccountLogEntry($action, $TransferFeeMain, $TransferFeeDetails = null, $oldAmount = null)
    {
        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = 0; // Transfer fee is not case-specific
        $AccountLog->bill_id = null;
        $AccountLog->action = $action;
        $AccountLog->object_id = $TransferFeeMain->id;
        $AccountLog->object_id_2 = $TransferFeeDetails ? count($TransferFeeDetails) : 0;
        
        // Set amounts based on action
        if ($action === 'CREATE') {
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = $TransferFeeMain->transfer_amount ?? 0;
            $AccountLog->desc = 'Transfer Fee Created - Transaction ID: ' . $TransferFeeMain->transaction_id . 
                               ', Amount: ' . number_format($TransferFeeMain->transfer_amount ?? 0, 2) . 
                               ', Purpose: ' . $TransferFeeMain->purpose . 
                               ', Details Count: ' . ($TransferFeeDetails ? count($TransferFeeDetails) : 0);
        } elseif ($action === 'UPDATE') {
            $AccountLog->ori_amt = $oldAmount ?? 0;
            $AccountLog->new_amt = $TransferFeeMain->transfer_amount ?? 0;
            $AccountLog->desc = 'Transfer Fee Updated - Transaction ID: ' . $TransferFeeMain->transaction_id . 
                               ', Old Amount: ' . number_format($oldAmount ?? 0, 2) . 
                               ', New Amount: ' . number_format($TransferFeeMain->transfer_amount ?? 0, 2) . 
                               ', Purpose: ' . $TransferFeeMain->purpose . 
                               ', Details Count: ' . ($TransferFeeDetails ? count($TransferFeeDetails) : 0);
        } elseif ($action === 'RECONCILE') {
            $AccountLog->ori_amt = $TransferFeeMain->transfer_amount ?? 0;
            $AccountLog->new_amt = $TransferFeeMain->transfer_amount ?? 0; // Same amount, just reconciled
            $AccountLog->desc = 'Transfer Fee Reconciled - Transaction ID: ' . $TransferFeeMain->transaction_id . 
                               ', Amount: ' . number_format($TransferFeeMain->transfer_amount ?? 0, 2) . 
                               ', Purpose: ' . $TransferFeeMain->purpose . 
                               ', Details Count: ' . ($TransferFeeDetails ? count($TransferFeeDetails) : 0) . 
                               ', Reconciled Date: ' . date('Y-m-d');
        } elseif ($action === 'REVERT_RECONCILE') {
            $AccountLog->ori_amt = $TransferFeeMain->transfer_amount ?? 0;
            $AccountLog->new_amt = $TransferFeeMain->transfer_amount ?? 0; // Same amount, just reverted
            $AccountLog->desc = 'Transfer Fee Reconciliation Reverted - Transaction ID: ' . $TransferFeeMain->transaction_id . 
                               ', Amount: ' . number_format($TransferFeeMain->transfer_amount ?? 0, 2) . 
                               ', Purpose: ' . $TransferFeeMain->purpose . 
                               ', Details Count: ' . ($TransferFeeDetails ? count($TransferFeeDetails) : 0) . 
                               ', Reverted Date: ' . date('Y-m-d');
        }
        
        $AccountLog->status = '1';
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();
    }

    /**
     * Create ledger entries for V3
     */
    private function addLedgerEntriesV3($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $sst_amount, $transfer_amount, $reimbursement_amount = 0, $reimbursement_sst_amount = 0)
    {
        // Get the bill record for case information
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $LoanCaseInvoiceMain->loan_case_main_bill_id)->first();
        
        if (!$LoanCaseBillMain) {
            return;
        }

        // Create ledger entries for transfer out (OLD SYSTEM - LedgerEntries)
        $LedgerEntries = new LedgerEntries();
        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
        $LedgerEntries->user_id = auth()->user()->id;
        $LedgerEntries->key_id = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'C';
        $LedgerEntries->amount = $transfer_amount;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFEROUT';
        $LedgerEntries->save();

        // Create ledger entries for transfer out (NEW SYSTEM - LedgerEntriesV2)
        $LedgerEntries = new LedgerEntriesV2();
        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
        $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
        $LedgerEntries->user_id = auth()->user()->id;
        $LedgerEntries->key_id = $TransferFeeMain->id;  // ✅ TransferFeeMain ID
        $LedgerEntries->key_id_2 = $TransferFeeDetails->id;  // ✅ TransferFeeDetails ID
        $LedgerEntries->transaction_type = 'C';
        $LedgerEntries->amount = $transfer_amount;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->status = 1;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFER_OUT';
        $LedgerEntries->save();

        // Create ledger entries for transfer in (OLD SYSTEM - LedgerEntries)
        $LedgerEntries = new LedgerEntries();
        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
        $LedgerEntries->user_id = auth()->user()->id;
        $LedgerEntries->key_id = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $transfer_amount;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFERIN';
        $LedgerEntries->save();

        // Create ledger entries for transfer in (NEW SYSTEM - LedgerEntriesV2)
        $LedgerEntries = new LedgerEntriesV2();
        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
        $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
        $LedgerEntries->user_id = auth()->user()->id;
        $LedgerEntries->key_id = $TransferFeeMain->id;  // ✅ TransferFeeMain ID
        $LedgerEntries->key_id_2 = $TransferFeeDetails->id;  // ✅ TransferFeeDetails ID
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $transfer_amount;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->status = 1;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFER_IN';
        $LedgerEntries->save();

        // Create SST ledger entries if applicable
        if ($sst_amount > 0) {
            // SST Out (OLD SYSTEM - LedgerEntries)
            $LedgerEntries = new LedgerEntries();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SSTOUT';
            $LedgerEntries->save();

            // SST Out (NEW SYSTEM - LedgerEntriesV2)
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;  // ✅ TransferFeeMain ID
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;  // ✅ TransferFeeDetails ID
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SST_OUT';
            $LedgerEntries->save();

            // SST In (OLD SYSTEM - LedgerEntries)
            $LedgerEntries = new LedgerEntries();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SSTIN';
            $LedgerEntries->save();

            // SST In (NEW SYSTEM - LedgerEntriesV2)
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;  // ✅ TransferFeeMain ID
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;  // ✅ TransferFeeDetails ID
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SST_IN';
            $LedgerEntries->save();
        }

        // Create reimbursement ledger entries if applicable
        if ($reimbursement_amount > 0) {
            // Reimbursement Out (OLD SYSTEM - LedgerEntries)
            $LedgerEntries = new LedgerEntries();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $reimbursement_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMBOUT';
            $LedgerEntries->save();

            // Reimbursement Out (NEW SYSTEM - LedgerEntriesV2)
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $reimbursement_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMB_OUT';
            $LedgerEntries->save();

            // Reimbursement In (OLD SYSTEM - LedgerEntries)
            $LedgerEntries = new LedgerEntries();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $reimbursement_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMBIN';
            $LedgerEntries->save();

            // Reimbursement In (NEW SYSTEM - LedgerEntriesV2)
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $reimbursement_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMB_IN';
            $LedgerEntries->save();
        }

        // Create reimbursement SST ledger entries if applicable
        if ($reimbursement_sst_amount > 0) {
            // Reimbursement SST Out (OLD SYSTEM - LedgerEntries)
            $LedgerEntries = new LedgerEntries();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $reimbursement_sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMBSSTOUT';
            $LedgerEntries->save();

            // Reimbursement SST Out (NEW SYSTEM - LedgerEntriesV2)
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $reimbursement_sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMB_SST_OUT';
            $LedgerEntries->save();

            // Reimbursement SST In (OLD SYSTEM - LedgerEntries)
            $LedgerEntries = new LedgerEntries();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $reimbursement_sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMBSSTIN';
            $LedgerEntries->save();

            // Reimbursement SST In (NEW SYSTEM - LedgerEntriesV2)
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $reimbursement_sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'REIMB_SST_IN';
            $LedgerEntries->save();
        }
    }

    /**
     * Test method to create a sample transfer fee record (for testing only)
     */
    public function createTestRecordV3()
    {
        try {
            $current_user = auth()->user();
            
            // Create a test transfer fee record
            $TransferFeeMain = new TransferFeeMain();
            $TransferFeeMain->transfer_date = date('Y-m-d');
            $TransferFeeMain->transfer_by = $current_user->id;
            $TransferFeeMain->transaction_id = 'TEST-V3-' . date('YmdHis');
            $TransferFeeMain->receipt_no = '';
            $TransferFeeMain->voucher_no = '';
            $TransferFeeMain->transfer_from = 1; // Default bank account
            $TransferFeeMain->transfer_to = 2; // Default bank account
            $TransferFeeMain->purpose = 'Test Transfer Fee V3 Record';
            $TransferFeeMain->transfer_amount = 1000.00;
            $TransferFeeMain->status = 1;
            $TransferFeeMain->branch_id = $current_user->branch_id ?? 1;
            $TransferFeeMain->created_at = date('Y-m-d H:i:s');
            $TransferFeeMain->save();

            return response()->json([
                'status' => 1, 
                'message' => 'Test record created successfully',
                'record_id' => $TransferFeeMain->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0, 
                'message' => 'Error creating test record: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test method to debug invoice loading (for testing only)
     */
    public function testInvoiceLoadingV3()
    {
        try {
            $current_user = auth()->user();
            
            // Simple query to test
            $count = DB::table('loan_case_invoice_main')->count();
            
                         // Test the full query
             $rows = DB::table('loan_case_invoice_main as im')
                 ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
                 ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                 ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                 ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
                 ->select(
                     'im.*',
                     'b.invoice_no as bill_invoice_no',
                     DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                     'l.case_ref_no',
                     'c.name as client_name',
                     'ibp.customer_code',
                     'ibp.customer_name as billing_party_name'
                 )
                 ->where('im.transferred_to_office_bank', '=', 0) // Only show invoices that haven't been transferred
                 ->where('im.status', '<>', 99)
                 ->where('im.bln_invoice', '=', 1)
                 ->limit(5)
                 ->get();

            return response()->json([
                'status' => 1,
                'total_invoices' => $count,
                'available_invoices' => count($rows),
                'sample_data' => $rows
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error testing invoice loading: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Simple test method to check if AJAX is working (for testing only)
     */
    public function simpleTestV3()
    {
        try {
            $current_user = auth()->user();
            
            return response()->json([
                'status' => 1,
                'message' => 'AJAX is working!',
                'user_id' => $current_user->id,
                'user_name' => $current_user->name,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * SUPER SIMPLE invoice test - No middleware, no permissions, just pure data
     */
    public function superSimpleInvoiceTest()
    {
        try {
                         // Just get some basic invoice data
             $invoices = DB::table('loan_case_invoice_main')
                 ->select('id', 'invoice_no', 'invoice_date', 'pfee1_inv', 'pfee2_inv')
                 ->where('status', '<>', 99)
                 ->where('transferred_to_office_bank', '=', 0) // Only show invoices that haven't been transferred
                 ->limit(5)
                 ->get();

            return response()->json([
                'status' => 1,
                'message' => 'Super simple test working!',
                'invoices' => $invoices,
                'count' => count($invoices)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update Total Amount for a specific transfer fee detail
     */
    public function updateTotalAmtV3(Request $request, $detailId)
    {
        try {
            $current_user = auth()->user();
            
            // Check permissions - admin, maker and account users can edit
            if (!in_array($current_user->menuroles, ['admin', 'maker', 'account'])) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied. Only admin, maker and account users can edit total amounts.'
                ], 403);
            }
            
            // Validate input
            $request->validate([
                'total_amt' => 'required|numeric|min:0',
                'invoice_id' => 'required|integer'
            ]);
            
            // Get transfer fee detail
            $transferFeeDetail = TransferFeeDetails::where('id', $detailId)->first();
            
            if (!$transferFeeDetail) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Transfer fee detail not found'
                ], 404);
            }
            
            // Check if transfer fee is reconciled (read-only)
            $transferFeeMain = TransferFeeMain::where('id', $transferFeeDetail->transfer_fee_main_id)->first();
            if ($transferFeeMain && $transferFeeMain->is_recon == '1') {
                return response()->json([
                    'status' => 0,
                    'message' => 'Cannot edit reconciled transfer fee'
                ], 403);
            }
            
            // Store custom total amount in a JSON field or cache
            // For now, we'll store it in a session/cache keyed by detail ID
            // In production, you may want to add a 'custom_total_amt' column to transfer_fee_details table
            $customTotalAmt = round($request->input('total_amt'), 2);
            
            // Store in cache with a long expiration (or use database column)
            \Cache::put("transfer_fee_detail_{$detailId}_custom_total_amt", $customTotalAmt, now()->addYears(1));
            
            // Also store in session for immediate use
            session(["transfer_fee_detail_{$detailId}_custom_total_amt" => $customTotalAmt]);
            
            return response()->json([
                'status' => 1,
                'message' => 'Total amount updated successfully',
                'data' => [
                    'detail_id' => $detailId,
                    'total_amt' => $customTotalAmt
                ]
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Update Total Amount Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error updating total amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update amounts (pfee, sst, reimb, reimb_sst) for a transfer fee detail
     * Updates: transfer_fee_details, transfer_fee_main, ledger_entries_v2, account_log
     */
    public function updateAmountsV3(Request $request, $detailId)
    {
        try {
            DB::beginTransaction();
            
            $current_user = auth()->user();
            
            // Check permissions
            if (!in_array($current_user->menuroles, ['admin', 'maker', 'account'])) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied. Only admin, maker and account users can edit amounts.'
                ], 403);
            }
            
            // Get transfer fee detail
            $transferFeeDetail = TransferFeeDetails::where('id', $detailId)->first();
            
            if (!$transferFeeDetail) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Transfer fee detail not found'
                ], 404);
            }
            
            // Check if transfer fee is reconciled
            $transferFeeMain = TransferFeeMain::where('id', $transferFeeDetail->transfer_fee_main_id)->first();
            if ($transferFeeMain && $transferFeeMain->is_recon == '1') {
                return response()->json([
                    'status' => 0,
                    'message' => 'Cannot edit reconciled transfer fee'
                ], 403);
            }
            
            // Get invoice
            $invoice = LoanCaseInvoiceMain::find($request->input('invoice_id'));
            if (!$invoice) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invoice not found'
                ], 404);
            }
            
            // Get bill
            $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
            if (!$bill) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bill not found'
                ], 404);
            }
            
            $field = $request->input('field');
            $oldValues = [
                'pfee1' => $invoice->pfee1_inv ?? 0,
                'pfee2' => $invoice->pfee2_inv ?? 0,
                'sst' => $invoice->sst_inv ?? 0,
                'reimb' => $invoice->reimbursement_amount ?? 0,
                'reimb_sst' => $invoice->reimbursement_sst ?? 0
            ];
            
            // Update invoice amounts based on field
            if ($field === 'pfee') {
                $newPfee1 = round($request->input('pfee1', $oldValues['pfee1']), 2);
                $newPfee2 = round($request->input('pfee2', $oldValues['pfee2']), 2);
                $invoice->pfee1_inv = $newPfee1;
                $invoice->pfee2_inv = $newPfee2;
            } elseif ($field === 'sst') {
                // Get the SST value and ensure proper rounding
                $sstValue = $request->input('sst');
                // Handle both 'sst' key and direct value
                if ($sstValue === null) {
                    $sstValue = $request->input($field, $oldValues['sst']);
                }
                $invoice->sst_inv = round((float)$sstValue, 2);
            } elseif ($field === 'reimb') {
                $invoice->reimbursement_amount = round($request->input('reimb', $oldValues['reimb']), 2);
            } elseif ($field === 'reimb_sst') {
                $invoice->reimbursement_sst = round($request->input('reimb_sst', $oldValues['reimb_sst']), 2);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid field specified'
                ], 400);
            }
            
            $invoice->save();
            
            // Recalculate invoice total
            $invoice->amount = round(
                ($invoice->pfee1_inv ?? 0) + 
                ($invoice->pfee2_inv ?? 0) + 
                ($invoice->sst_inv ?? 0) + 
                ($invoice->reimbursement_amount ?? 0) + 
                ($invoice->reimbursement_sst ?? 0), 
                2
            );
            $invoice->save();
            
            // Update transfer_fee_details to match new invoice amounts
            $totalPfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
            $totalSst = $invoice->sst_inv ?? 0;
            $totalReimb = $invoice->reimbursement_amount ?? 0;
            $totalReimbSst = $invoice->reimbursement_sst ?? 0;
            
            // Get all transfer fee details for this invoice
            $allTransferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->get();
            
            if ($allTransferFeeDetails->count() == 1) {
                // Single transfer record - update directly
                $transferFeeDetail->transfer_amount = round($totalPfee, 2);
                $transferFeeDetail->sst_amount = round($totalSst, 2);
                $transferFeeDetail->reimbursement_amount = round($totalReimb, 2);
                $transferFeeDetail->reimbursement_sst_amount = round($totalReimbSst, 2);
                $transferFeeDetail->save();
            } else {
                // Multiple transfer records - update proportionally
                $totalTransferredPfee = $allTransferFeeDetails->sum('transfer_amount');
                $totalTransferredSst = $allTransferFeeDetails->sum('sst_amount');
                $totalTransferredReimb = $allTransferFeeDetails->sum('reimbursement_amount');
                $totalTransferredReimbSst = $allTransferFeeDetails->sum('reimbursement_sst_amount');
                
                foreach ($allTransferFeeDetails as $tfd) {
                    if ($totalTransferredPfee > 0) {
                        $proportion = $tfd->transfer_amount / $totalTransferredPfee;
                        $tfd->transfer_amount = round($totalPfee * $proportion, 2);
                    }
                    if ($totalTransferredSst > 0) {
                        $proportion = $tfd->sst_amount / $totalTransferredSst;
                        $tfd->sst_amount = round($totalSst * $proportion, 2);
                    }
                    if ($totalTransferredReimb > 0) {
                        $proportion = $tfd->reimbursement_amount / $totalTransferredReimb;
                        $tfd->reimbursement_amount = round($totalReimb * $proportion, 2);
                    }
                    if ($totalTransferredReimbSst > 0) {
                        $proportion = $tfd->reimbursement_sst_amount / $totalTransferredReimbSst;
                        $tfd->reimbursement_sst_amount = round($totalReimbSst * $proportion, 2);
                    }
                    $tfd->save();
                }
                
                // Ensure last record gets remainder to avoid rounding errors
                if ($allTransferFeeDetails->count() > 1) {
                    $lastTfd = $allTransferFeeDetails->last();
                    $lastTfd->transfer_amount = round($totalPfee - ($allTransferFeeDetails->slice(0, -1)->sum('transfer_amount')), 2);
                    $lastTfd->sst_amount = round($totalSst - ($allTransferFeeDetails->slice(0, -1)->sum('sst_amount')), 2);
                    $lastTfd->reimbursement_amount = round($totalReimb - ($allTransferFeeDetails->slice(0, -1)->sum('reimbursement_amount')), 2);
                    $lastTfd->reimbursement_sst_amount = round($totalReimbSst - ($allTransferFeeDetails->slice(0, -1)->sum('reimbursement_sst_amount')), 2);
                    $lastTfd->save();
                }
            }
            
            // Recalculate transferred amounts in invoice
            $invoice->transferred_pfee_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->sum('transfer_amount');
            $invoice->transferred_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->sum('sst_amount');
            $invoice->transferred_reimbursement_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->sum('reimbursement_amount');
            $invoice->transferred_reimbursement_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->sum('reimbursement_sst_amount');
            $invoice->save();
            
            // Update bill totals (sum of all invoice amounts)
            $this->updateBillTotalsFromInvoices($bill->id);
            
            // Update transfer_fee_main amount
            $this->updateTransferFeeMainAmt($transferFeeMain->id);
            
            // IMPORTANT: Re-fetch transfer fee details to get updated values from database
            // The $allTransferFeeDetails collection may have stale data in memory
            $updatedTransferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->get();
            
            // Update ledger entries V2 with fresh data
            $this->updateLedgerEntriesForTransferFeeDetails($invoice->id, $updatedTransferFeeDetails);
            
            // Create account log entry
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $bill->case_id ?? 0;
            $AccountLog->bill_id = $bill->id;
            $AccountLog->action = 'UPDATE';
            $AccountLog->object_id = $transferFeeMain->id;
            $AccountLog->object_id_2 = $detailId;
            
            $fieldNames = [
                'pfee' => 'Professional Fee',
                'sst' => 'SST',
                'reimb' => 'Reimbursement',
                'reimb_sst' => 'Reimbursement SST'
            ];
            
            $oldAmount = 0;
            $newAmount = 0;
            if ($field === 'pfee') {
                $oldAmount = $oldValues['pfee1'] + $oldValues['pfee2'];
                $newAmount = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
            } else {
                $oldAmount = $oldValues[$field] ?? 0;
                $newAmount = $request->input($field, $oldAmount);
            }
            
            $AccountLog->ori_amt = $oldAmount;
            $AccountLog->new_amt = $newAmount;
            $AccountLog->desc = "Transfer Fee Amount Updated - {$fieldNames[$field]}: Invoice {$invoice->invoice_no}, Transfer Fee Detail ID: {$detailId}";
            $AccountLog->status = '1';
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
            
            DB::commit();
            
            \Illuminate\Support\Facades\Log::info("Updated transfer fee amounts", [
                'detail_id' => $detailId,
                'invoice_id' => $invoice->id,
                'field' => $field,
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount
            ]);
            
            return response()->json([
                'status' => 1,
                'message' => 'Amounts updated successfully',
                'data' => [
                    'detail_id' => $detailId,
                    'invoice_id' => $invoice->id,
                    'field' => $field,
                    'pfee1' => $invoice->pfee1_inv ?? 0,
                    'pfee2' => $invoice->pfee2_inv ?? 0,
                    'pfee_total' => ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0),
                    'sst' => $invoice->sst_inv ?? 0,
                    'reimbursement' => $invoice->reimbursement_amount ?? 0,
                    'reimbursement_sst' => $invoice->reimbursement_sst ?? 0
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Update Amounts Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error updating amounts: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update ledger entries V2 when transfer fee details are updated
     */
    private function updateLedgerEntriesForTransferFeeDetails($invoiceId, $transferFeeDetails)
    {
        $updatedCount = 0;
        $createdCount = 0;
        
        // Get invoice and bill information
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            return ['updated_count' => 0, 'created_count' => 0];
        }
        
        $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
        if (!$bill) {
            return ['updated_count' => 0, 'created_count' => 0];
        }
        
        foreach ($transferFeeDetails as $tfd) {
            // Get TransferFeeMain for transaction details
            $transferFeeMain = TransferFeeMain::find($tfd->transfer_fee_main_id);
            if (!$transferFeeMain) {
                continue;
            }
            
            // Update LedgerEntriesV2 - TRANSFER_OUT/IN (professional fee)
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['TRANSFER_OUT', 'TRANSFER_IN'])
                ->update(['amount' => $tfd->transfer_amount ?? 0, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update or Create LedgerEntriesV2 - SST_OUT/IN (professional fee SST)
            if (($tfd->sst_amount ?? 0) > 0) {
                $sstOutExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'SST_OUT')
                    ->exists();
                
                $sstInExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'SST_IN')
                    ->exists();
                
                if ($sstOutExists || $sstInExists) {
                    $count = DB::table('ledger_entries_v2')
                        ->where('key_id_2', $tfd->id)
                        ->where('status', '<>', 99)
                        ->whereIn('type', ['SST_OUT', 'SST_IN'])
                        ->update(['amount' => $tfd->sst_amount ?? 0, 'updated_at' => now()]);
                    $updatedCount += $count;
                } else {
                    // Create SST_OUT entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'C',
                        'amount' => $tfd->sst_amount,
                        'bank_id' => $transferFeeMain->transfer_from,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'SST_OUT'
                    ]);
                    $createdCount++;
                    
                    // Create SST_IN entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'D',
                        'amount' => $tfd->sst_amount,
                        'bank_id' => $transferFeeMain->transfer_to,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'SST_IN'
                    ]);
                    $createdCount++;
                }
            } else {
                // If SST amount is 0, still try to update existing entries
                $count = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->whereIn('type', ['SST_OUT', 'SST_IN'])
                    ->update(['amount' => 0, 'updated_at' => now()]);
                $updatedCount += $count;
            }
            
            // Update or Create LedgerEntriesV2 - REIMB_OUT/IN (reimbursement amount)
            if (($tfd->reimbursement_amount ?? 0) > 0) {
                $reimbOutExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_OUT')
                    ->exists();
                
                $reimbInExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_IN')
                    ->exists();
                
                if ($reimbOutExists || $reimbInExists) {
                    $count = DB::table('ledger_entries_v2')
                        ->where('key_id_2', $tfd->id)
                        ->where('status', '<>', 99)
                        ->whereIn('type', ['REIMB_OUT', 'REIMB_IN'])
                        ->update(['amount' => $tfd->reimbursement_amount, 'updated_at' => now()]);
                    $updatedCount += $count;
                } else {
                    // Create REIMB_OUT entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'C',
                        'amount' => $tfd->reimbursement_amount,
                        'bank_id' => $transferFeeMain->transfer_from,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_OUT'
                    ]);
                    $createdCount++;
                    
                    // Create REIMB_IN entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'D',
                        'amount' => $tfd->reimbursement_amount,
                        'bank_id' => $transferFeeMain->transfer_to,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_IN'
                    ]);
                    $createdCount++;
                }
            }
            
            // Update or Create LedgerEntriesV2 - REIMB_SST_OUT/IN (reimbursement SST)
            if (($tfd->reimbursement_sst_amount ?? 0) > 0) {
                $reimbSstOutExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_SST_OUT')
                    ->exists();
                
                $reimbSstInExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_SST_IN')
                    ->exists();
                
                if ($reimbSstOutExists || $reimbSstInExists) {
                    $count = DB::table('ledger_entries_v2')
                        ->where('key_id_2', $tfd->id)
                        ->where('status', '<>', 99)
                        ->whereIn('type', ['REIMB_SST_OUT', 'REIMB_SST_IN'])
                        ->update(['amount' => $tfd->reimbursement_sst_amount, 'updated_at' => now()]);
                    $updatedCount += $count;
                } else {
                    // Create REIMB_SST_OUT entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'C',
                        'amount' => $tfd->reimbursement_sst_amount,
                        'bank_id' => $transferFeeMain->transfer_from,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_SST_OUT'
                    ]);
                    $createdCount++;
                    
                    // Create REIMB_SST_IN entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'D',
                        'amount' => $tfd->reimbursement_sst_amount,
                        'bank_id' => $transferFeeMain->transfer_to,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_SST_IN'
                    ]);
                    $createdCount++;
                }
            }
        }
        
        return [
            'updated_count' => $updatedCount,
            'created_count' => $createdCount
        ];
    }

    /**
     * Update bill totals from sum of all invoice amounts
     */
    private function updateBillTotalsFromInvoices($billId)
    {
        $bill = LoanCaseBillMain::find($billId);
        if (!$bill) {
            return;
        }
        
        // Get all invoices for this bill
        $invoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->get();
        
        // Sum all invoice amounts
        $total_pfee1 = $invoices->sum('pfee1_inv');
        $total_pfee2 = $invoices->sum('pfee2_inv');
        $total_sst = $invoices->sum('sst_inv');
        $total_reimbursement_amount = $invoices->sum('reimbursement_amount');
        $total_reimbursement_sst = $invoices->sum('reimbursement_sst');
        $total_amount = $invoices->sum('amount');
        
        // Update the bill record
        $bill->pfee1_inv = round($total_pfee1, 2);
        $bill->pfee2_inv = round($total_pfee2, 2);
        $bill->sst_inv = round($total_sst, 2);
        $bill->reimbursement_amount = round($total_reimbursement_amount, 2);
        $bill->reimbursement_sst = round($total_reimbursement_sst, 2);
        $bill->total_amt_inv = round($total_amount, 2);
        $bill->save();
        
        \Illuminate\Support\Facades\Log::info("Updated bill totals from invoices", [
            'bill_id' => $billId,
            'total_pfee1' => $total_pfee1,
            'total_pfee2' => $total_pfee2,
            'total_sst' => $total_sst,
            'total_reimbursement_amount' => $total_reimbursement_amount,
            'total_reimbursement_sst' => $total_reimbursement_sst,
            'total_amount' => $total_amount
        ]);
    }

    /**
     * Export Transfer Fee Invoices to Excel or PDF
     */
    public function exportTransferFeeInvoices(Request $request)
    {
        try {
            $current_user = auth()->user();
            
            // Check permissions
            if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied'
                ], 403);
            }

            $transferFeeId = $request->input('transfer_fee_id');
            $format = $request->input('format', 'excel');
            $sortBy = $request->input('sort_by', 'invoice_no'); // Default sort by invoice_no
            $sortOrder = $request->input('sort_order', 'asc'); // Default ascending order
            
            // Get transfer fee details
            $transferFee = TransferFeeMain::find($transferFeeId);
            if (!$transferFee) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Transfer fee not found'
                ], 404);
            }

            // Get actual transfer fee details from database instead of using frontend data
            $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
                ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
                ->select(
                    'transfer_fee_details.*',
                    'im.invoice_no',
                    DB::raw('DATE(im.Invoice_date) as invoice_date'),
                    'im.amount as invoice_amount',
                    'im.transferred_pfee_amt',
                    'im.transferred_sst_amt',
                    'im.pfee1_inv',
                    'im.pfee2_inv',
                    'im.sst_inv',
                    'im.reimbursement_amount',
                    'im.reimbursement_sst',
                    'im.transferred_reimbursement_amt',
                    'im.transferred_reimbursement_sst_amt',
                    'b.invoice_no as bill_invoice_no',
                    DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                    'b.payment_receipt_date',
                    'b.collected_amt as bill_collected_amt',
                    'b.total_amt as bill_total_amt',
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name',
                    'ibp.customer_name as billing_party_name'
                )
                ->get();

            if ($transferFeeDetails->isEmpty()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No invoices found for export'
                ], 404);
            }

            // Calculate invoice amounts for each detail
            foreach ($transferFeeDetails as $detail) {
                // Use invoice amount directly from loan_case_invoice_main table (no division needed)
                $detail->invoice_total_amt = $detail->invoice_amount ?? 0;
                
                // Count invoices per bill for collected amount division (collected amount needs to be divided)
                $invoiceCount = \App\Models\LoanCaseInvoiceMain::where('loan_case_main_bill_id', $detail->loan_case_main_bill_id)
                    ->where('status', 1)
                    ->count();
                $invoiceCount = max(1, $invoiceCount); // Avoid division by zero
                
                // For backward compatibility, keep bill amounts but use invoice amounts for display
                // Use the actual invoice amount from database if available (most accurate)
                // Otherwise calculate using correct formula: (cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)
                if ($detail->invoice_amount && $detail->invoice_amount > 0) {
                    // Use the stored invoice amount directly to avoid rounding errors
                    $detail->bill_total_amt_divided = round($detail->invoice_amount, 2);
                } else {
                    // Fallback: Calculate if invoice amount is not available
                    // Get SST rate from bill
                    $bill = \App\Models\LoanCaseBillMain::find($detail->loan_case_main_bill_id);
                    $sstRate = $bill ? ($bill->sst_rate / 100) : 0;
                    
                    // Get category totals from invoice details
                    $details = \DB::table('loan_case_invoice_details as d')
                        ->leftJoin('account_item as ai', 'ai.id', '=', 'd.account_item_id')
                        ->where('d.invoice_main_id', $detail->loan_case_invoice_main_id)
                        ->select('d.amount', 'ai.account_cat_id')
                        ->get();
                    
                    $sumCat1 = $details->where('account_cat_id', 1)->sum('amount');
                    $sumCat2 = $details->where('account_cat_id', 2)->sum('amount');
                    $sumCat3 = $details->where('account_cat_id', 3)->sum('amount');
                    $sumCat4 = $details->where('account_cat_id', 4)->sum('amount');
                    
                    // Calculate total first, then round once to avoid rounding errors
                    // Formula: (cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)
                    $totalUnrounded = ($sumCat1 + ($sumCat1 * $sstRate)) + $sumCat2 + $sumCat3 + ($sumCat4 + ($sumCat4 * $sstRate));
                    $detail->bill_total_amt_divided = round($totalUnrounded, 2);
                }
                
                // ====================================================================
                // OPTION 2: Show individual invoice amounts (Total amt ≠ Collected amt)
                // To revert to OPTION 1 (matching amounts), uncomment the code below
                // ====================================================================
                
                // Use invoice amount for Total amt (already calculated at line 2447)
                // Keep the invoice amount - don't overwrite it
                // Note: bill_total_amt_divided is already set correctly at line 2447 using invoice_amount
                
                // Calculate Collected amt from bill collected amount (divided equally)
                $totalAmount = $detail->bill_collected_amt ?? 0;
                if ($totalAmount == 0 && $invoiceCount == 1 && ($detail->invoice_amount ?? 0) > 0) {
                    // Use invoice amount when there's no collected amount recorded but invoice exists
                    $totalAmount = $detail->invoice_amount ?? 0;
                }
                $calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
                $detail->bill_collected_amt_divided = $calculatedCollectedAmount;
                
                // ====================================================================
                // OPTION 1 (REVERT CODE): Uncomment below to make both amounts match
                // ====================================================================
                // $totalAmount = $detail->bill_collected_amt ?? 0;
                // if ($totalAmount == 0 && $invoiceCount == 1 && ($detail->invoice_amount ?? 0) > 0) {
                //     $totalAmount = $detail->invoice_amount ?? 0;
                // }
                // $calculatedAmount = round($totalAmount / $invoiceCount, 2);
                // $detail->bill_total_amt_divided = $calculatedAmount;
                // $detail->bill_collected_amt_divided = $calculatedAmount;
            }

            // Keep individual amounts at 1340.67, round total in frontend display

            // Sort invoices based on the specified criteria
            $sortedDetails = $transferFeeDetails->sortBy(function($detail) use ($sortBy, $sortOrder) {
                $value = '';
                
                switch ($sortBy) {
                    case 'invoice_no':
                        $value = $detail->invoice_no ?? '';
                        // Extract numeric part for proper sorting
                        $value = (int)preg_replace('/[^0-9]/', '', $value);
                        break;
                    case 'invoice_date':
                        $value = strtotime($detail->invoice_date ?? '');
                        break;
                    case 'case_ref_no':
                        $value = $detail->case_ref_no ?? '';
                        break;
                    case 'total_amount':
                        $value = $detail->bill_total_amt_divided ?? 0;
                        break;
                    case 'collected_amount':
                        $value = $detail->bill_collected_amt_divided ?? 0;
                        break;
                    case 'pfee':
                        $value = ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);
                        break;
                    case 'sst':
                        $value = $detail->sst_inv ?? 0;
                        break;
                    case 'pfee_to_transfer':
                        $value = $detail->transfer_amount ?? 0;
                        break;
                    case 'sst_to_transfer':
                        $value = $detail->sst_amount ?? 0;
                        break;
                    case 'transferred_bal':
                        $value = $detail->transfer_amount ?? 0;
                        break;
                    case 'transferred_sst':
                        $value = $detail->sst_amount ?? 0;
                        break;
                    case 'payment_date':
                        $value = strtotime($detail->payment_receipt_date ?? '');
                        break;
                    default:
                        $value = $detail->invoice_no ?? '';
                        break;
                }
                
                return $value;
            });

            if ($sortOrder === 'desc') {
                $sortedDetails = $sortedDetails->reverse();
            }

            // Prepare data for export using the actual database data
            $exportData = [];
            $rowNumber = 1; // Start numbering from 1
            
            foreach ($sortedDetails as $detail) {
                // Match frontend calculation: use bill_total_amt_divided for Total Amount
                $totalAmount = $detail->bill_total_amt_divided ?? 0;
                
                // Use proper rounding to match frontend display
                $collectedAmount = round($detail->bill_collected_amt_divided ?? 0, 2);
                $pfeeAmount = ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);
                $reimbursementAmount = $detail->reimbursement_amount ?? 0;
                $reimbursementSstAmount = $detail->reimbursement_sst ?? 0;
                
                // Calculate available amounts to transfer (same logic as edit page)
                $pfeeToTransfer = max(0, $pfeeAmount - ($detail->transferred_pfee_amt ?? 0));
                $sstToTransfer = max(0, ($detail->sst_inv ?? 0) - ($detail->transferred_sst_amt ?? 0));
                $reimbursementToTransfer = max(0, $reimbursementAmount - ($detail->transferred_reimbursement_amt ?? 0));
                $reimbursementSstToTransfer = max(0, $reimbursementSstAmount - ($detail->transferred_reimbursement_sst_amt ?? 0));
                
                // Handle invoice date - use bill invoice date if invoice date is null/empty
                $invoiceDate = $detail->invoice_date ?? $detail->bill_invoice_date ?? 'N/A';
                
                // Format the date properly if it's not N/A
                if ($invoiceDate !== 'N/A' && !empty($invoiceDate)) {
                    try {
                        $invoiceDate = date('Y-m-d', strtotime($invoiceDate));
                    } catch (\Exception $e) {
                        $invoiceDate = 'N/A';
                    }
                } else {
                    $invoiceDate = 'N/A';
                }
                
                // Log for debugging if invoice date is N/A but we have data
                if ($invoiceDate === 'N/A' && ($detail->invoice_no ?? false)) {
                    \Illuminate\Support\Facades\Log::info('Export: Invoice date is N/A for invoice', [
                        'invoice_no' => $detail->invoice_no,
                        'invoice_date' => $detail->invoice_date,
                        'bill_invoice_date' => $detail->bill_invoice_date,
                        'transfer_fee_id' => $transferFeeId,
                        'all_fields' => array_keys((array)$detail) // Log all available fields for debugging
                    ]);
                }
                
                // Handle payment date formatting
                $paymentDate = $detail->payment_receipt_date ?? 'N/A';
                if ($paymentDate !== 'N/A' && !empty($paymentDate)) {
                    try {
                        $paymentDate = date('Y-m-d', strtotime($paymentDate));
                    } catch (\Exception $e) {
                        $paymentDate = 'N/A';
                    }
                }
                
                $exportData[] = [
                    'No' => $rowNumber,
                    'Case Ref No' => $detail->case_ref_no ?? 'N/A',
                    'Invoice No' => $detail->invoice_no ?? 'N/A',
                    'Invoice Date' => $invoiceDate,
                    'Total Amount' => $totalAmount,
                    'Collected Amount' => $collectedAmount,
                    'Professional Fee' => $pfeeAmount,
                    'SST' => $detail->sst_inv ?? 0,
                    'Reimbursement' => $reimbursementAmount,
                    'Reimbursement SST' => $reimbursementSstAmount,
                    'Pfee to Transfer' => $pfeeToTransfer,
                    'SST to Transfer' => $sstToTransfer,
                    'Reimbursement to Transfer' => $reimbursementToTransfer,
                    'Reimbursement SST to Transfer' => $reimbursementSstToTransfer,
                    'Transferred Balance' => $detail->transfer_amount ?? 0,
                    'Transferred SST' => $detail->sst_amount ?? 0,
                    'Transferred Reimbursement' => $detail->reimbursement_amount ?? 0,
                    'Transferred Reimbursement SST' => $detail->reimbursement_sst_amount ?? 0,
                    'Payment Date' => $paymentDate
                ];
                
                $rowNumber++; // Increment row number for next record
            }

            // Add totals row
            $totals = [
                'No' => 'TOTAL',
                'Case Ref No' => '',
                'Invoice No' => '',
                'Invoice Date' => '',
                'Total Amount' => array_sum(array_column($exportData, 'Total Amount')),
                'Collected Amount' => array_sum(array_column($exportData, 'Collected Amount')),
                'Professional Fee' => array_sum(array_column($exportData, 'Professional Fee')),
                'SST' => array_sum(array_column($exportData, 'SST')),
                'Reimbursement' => array_sum(array_column($exportData, 'Reimbursement')),
                'Reimbursement SST' => array_sum(array_column($exportData, 'Reimbursement SST')),
                'Pfee to Transfer' => array_sum(array_column($exportData, 'Pfee to Transfer')),
                'SST to Transfer' => array_sum(array_column($exportData, 'SST to Transfer')),
                'Reimbursement to Transfer' => array_sum(array_column($exportData, 'Reimbursement to Transfer')),
                'Reimbursement SST to Transfer' => array_sum(array_column($exportData, 'Reimbursement SST to Transfer')),
                'Transferred Balance' => array_sum(array_column($exportData, 'Transferred Balance')),
                'Transferred SST' => array_sum(array_column($exportData, 'Transferred SST')),
                'Transferred Reimbursement' => array_sum(array_column($exportData, 'Transferred Reimbursement')),
                'Transferred Reimbursement SST' => array_sum(array_column($exportData, 'Transferred Reimbursement SST')),
                'Payment Date' => ''
            ];
            $exportData[] = $totals;

            if ($format === 'excel') {
                return $this->exportToExcel($exportData, $transferFee, $sortBy, $sortOrder);
            } else {
                return $this->exportToPDF($exportData, $transferFee, $sortBy, $sortOrder);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to Excel
     */
    private function exportToExcel($data, $transferFee, $sortBy = null, $sortOrder = null)
    {
        $filename = 'transfer_fee_invoices_' . $transferFee->transaction_id . '_' . date('Y-m-d') . '.xlsx';
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Transfer Fee Invoices Report');
        $sheet->mergeCells('A1:W1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set subtitle
        $sheet->setCellValue('A2', 'Transaction ID: ' . ($transferFee->transaction_id ?? 'N/A'));
        $sheet->mergeCells('A2:W2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A3', 'Transfer Date: ' . ($transferFee->transfer_date ?? 'N/A'));
        $sheet->mergeCells('A3:W3');
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A4', 'Purpose: ' . ($transferFee->purpose ?? 'N/A'));
        $sheet->mergeCells('A4:W4');
        $sheet->getStyle('A4')->getFont()->setSize(12);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
        
        // Add sorting information if available
        if ($sortBy && $sortOrder) {
            $sheet->setCellValue('A5', 'Sorted by: ' . ucfirst(str_replace('_', ' ', $sortBy)) . ' (' . strtoupper($sortOrder) . ')');
            $sheet->mergeCells('A5:W5');
            $sheet->getStyle('A5')->getFont()->setSize(12);
            $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
            $headerStartRow = 6;
        } else {
            $headerStartRow = 5;
        }
        
        // Add empty row
        $sheet->setCellValue('A' . $headerStartRow, '');
        
        // Write headers starting from the calculated row
        $headers = array_keys($data[0]);
        $col = 'A';
        $headerRow = $headerStartRow + 1;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $sheet->getStyle($col . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle($col . $headerRow)->getAlignment()->setHorizontal('center');
            $col++;
        }
        
        // Write data
        $row = $headerRow + 1;
        foreach ($data as $rowData) {
            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns - now we have more columns (A to W for all the new reimbursement columns)
        foreach (range('A', 'W') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set number format for amount columns (E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W)
        $sheet->getStyle('E7:W' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Style the totals row
        if (!empty($data) && end($data)['No'] === 'TOTAL') {
            $lastRow = $row - 1;
            $sheet->getStyle('A' . $lastRow . ':W' . $lastRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $lastRow . ':W' . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
        }
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        
        return response($content, 200, $headers);
    }

    /**
     * Export data to PDF
     */
    private function exportToPDF($data, $transferFee, $sortBy = null, $sortOrder = null)
    {
        $filename = 'transfer_fee_invoices_' . $transferFee->transaction_id . '_' . date('Y-m-d') . '.pdf';
        
        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('dashboard.transfer-fee-v3.export.pdf', [
            'data' => $data,
            'transferFee' => $transferFee,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);
        
        // Set paper size and orientation - use A3 landscape for better table fit
        $pdf->setPaper('A3', 'landscape');
        
        // Download the PDF
        return $pdf->download($filename);
    }

    /**
     * Helper function to safely convert values to numbers for bcmath functions
     */
    private function safeBcNumber($value, $default = 0)
    {
        if (is_numeric($value)) {
            // Convert scientific notation to decimal
            $floatValue = (float) $value;
            // Round to 2 decimal places to avoid precision issues
            return number_format($floatValue, 2, '.', '');
        }
        return number_format($default, 2, '.', '');
    }

    /**
     * Recalculate all invoice totals to ensure consistency after updates
     */
    private function recalculateAllInvoiceTotals($transferFeeId)
    {
        try {
            // Get all invoices involved in this transfer fee
            $transferDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)->get();
            
            $processedInvoices = [];
            $processedBills = [];
            
            foreach ($transferDetails as $detail) {
                $invoiceId = $detail->loan_case_invoice_main_id;
                $billId = $detail->loan_case_main_bill_id;
                
                // Process invoice if not already processed
                if (!in_array($invoiceId, $processedInvoices)) {
                    $this->recalculateInvoiceTotal($invoiceId);
                    $processedInvoices[] = $invoiceId;
                }
                
                // Process bill if not already processed
                if (!in_array($billId, $processedBills)) {
                    $this->recalculateBillTotal($billId);
                    $processedBills[] = $billId;
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Recalculated totals for transfer fee ' . $transferFeeId . ':', [
                'processed_invoices' => $processedInvoices,
                'processed_bills' => $processedBills
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error recalculating invoice totals: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate total transferred amounts for a specific invoice
     */
    private function recalculateInvoiceTotal($invoiceId)
    {
        try {
            $invoice = LoanCaseInvoiceMain::where('id', $invoiceId)->first();
            if (!$invoice) return;
            
            // Get ALL transfer fee details for this invoice across ALL transfer fees
            $allTransferDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)->get();
            
            // Calculate total transferred amounts
            $totalTransferredPfee = 0;
            $totalTransferredSst = 0;
            $totalTransferredReimbursement = 0;
            $totalTransferredReimbursementSst = 0;
            
            foreach ($allTransferDetails as $detail) {
                $totalTransferredPfee += $detail->transfer_amount;
                $totalTransferredSst += ($detail->sst_amount ?? 0);
                $totalTransferredReimbursement += ($detail->reimbursement_amount ?? 0);
                $totalTransferredReimbursementSst += ($detail->reimbursement_sst_amount ?? 0);
            }
            
            // Update invoice totals
            $invoice->transferred_pfee_amt = $totalTransferredPfee;
            $invoice->transferred_sst_amt = $totalTransferredSst;
            $invoice->transferred_reimbursement_amt = $totalTransferredReimbursement;
            $invoice->transferred_reimbursement_sst_amt = $totalTransferredReimbursementSst;
            
            // Check if all amounts (pfee, SST, reimbursement, reimbursement SST) are fully transferred
            $inv_pfee = $invoice->pfee1_inv + $invoice->pfee2_inv;
            $remaining_pfee = bcsub($inv_pfee, $totalTransferredPfee, 2);
            $remaining_sst = bcsub($invoice->sst_inv, $totalTransferredSst, 2);
            $remaining_reimbursement = bcsub($invoice->reimbursement_amount ?? 0, $totalTransferredReimbursement, 2);
            $remaining_reimbursement_sst = bcsub($invoice->reimbursement_sst ?? 0, $totalTransferredReimbursementSst, 2);
            
            // Mark as fully transferred only if all amounts are <= 0
            if ($remaining_pfee <= 0 && $remaining_sst <= 0 && $remaining_reimbursement <= 0 && $remaining_reimbursement_sst <= 0) {
                $invoice->transferred_to_office_bank = 1;
            } else {
                $invoice->transferred_to_office_bank = 0;
            }
            
            $invoice->save();
            
            \Illuminate\Support\Facades\Log::info('Recalculated invoice ' . $invoiceId . ' totals:', [
                'total_transferred_pfee' => $totalTransferredPfee,
                'total_transferred_sst' => $totalTransferredSst,
                'total_transferred_reimbursement' => $totalTransferredReimbursement,
                'total_transferred_reimbursement_sst' => $totalTransferredReimbursementSst,
                'remaining_pfee' => $remaining_pfee,
                'remaining_sst' => $remaining_sst,
                'remaining_reimbursement' => $remaining_reimbursement,
                'remaining_reimbursement_sst' => $remaining_reimbursement_sst,
                'transferred_to_office_bank' => $invoice->transferred_to_office_bank
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error recalculating invoice ' . $invoiceId . ' total: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate total transferred amounts for a specific bill
     */
    private function recalculateBillTotal($billId)
    {
        try {
            $bill = LoanCaseBillMain::where('id', $billId)->first();
            if (!$bill) return;
            
            // Get ALL transfer fee details for this bill across ALL transfer fees
            $allTransferDetails = TransferFeeDetails::where('loan_case_main_bill_id', $billId)->get();
            
            // Calculate total transferred amounts
            $totalTransferredPfee = 0;
            $totalTransferredSst = 0;
            
            foreach ($allTransferDetails as $detail) {
                $totalTransferredPfee += $detail->transfer_amount;
                $totalTransferredSst += ($detail->sst_amount ?? 0);
            }
            
            // Update bill totals
            $bill->transferred_pfee_amt = $totalTransferredPfee;
            $bill->transferred_sst_amt = $totalTransferredSst;
            
            // Check if fully transferred (use invoice data for consistency)
            $invoice = LoanCaseInvoiceMain::where('id', $detail->loan_case_invoice_main_id)->first();
            if ($invoice) {
                $inv_pfee = $invoice->pfee1_inv + $invoice->pfee2_inv;
                $remaining_pfee = bcsub($inv_pfee, $totalTransferredPfee, 2);
                $remaining_pfee = bcsub($inv_pfee, $totalTransferredPfee, 2);
                $remaining_sst = bcsub($invoice->sst_inv, $totalTransferredSst, 2);
                
                if ($remaining_pfee <= 0 && $remaining_sst <= 0) {
                    $bill->transferred_to_office_bank = 1;
                } else {
                    $bill->transferred_to_office_bank = 0;
                }
            }
            
            $bill->save();
            
            \Illuminate\Support\Facades\Log::info('Recalculated bill ' . $billId . ' totals:', [
                'total_transferred_pfee' => $totalTransferredPfee,
                'total_transferred_sst' => $totalTransferredSst,
                'transferred_to_office_bank' => $bill->transferred_to_office_bank
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error recalculating bill ' . $billId . ' total: ' . $e->getMessage());
        }
    }

    /**
     * Fix existing transferred_pfee_amt data in the database
     * This method recalculates all existing transferred amounts to fix the incorrect calculations
     */
    public function fixExistingTransferredAmounts()
    {
        try {
            // Get all invoices that have transferred amounts
            $invoices = \App\Models\LoanCaseInvoiceMain::where('transferred_pfee_amt', '>', 0)->get();
            
            $fixedCount = 0;
            
            foreach ($invoices as $invoice) {
                // Get all transfer fee details for this invoice
                $transferDetails = \App\Models\TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)->get();
                
                if ($transferDetails->count() > 0) {
                    // Calculate correct transferred amounts
                    $correctTransferredPfee = 0;
                    $correctTransferredSst = 0;
                    $correctTransferredReimbursement = 0;
                    $correctTransferredReimbursementSst = 0;
                    
                    foreach ($transferDetails as $detail) {
                        // transfer_amount now contains only the professional fee amount
                        $correctTransferredPfee += $detail->transfer_amount;
                        $correctTransferredSst += $detail->sst_amount ?? 0;
                        $correctTransferredReimbursement += $detail->reimbursement_amount ?? 0;
                        $correctTransferredReimbursementSst += $detail->reimbursement_sst_amount ?? 0;
                    }
                    
                    // Update the invoice with correct amounts
                    $invoice->transferred_pfee_amt = $correctTransferredPfee;
                    $invoice->transferred_sst_amt = $correctTransferredSst;
                    $invoice->transferred_reimbursement_amt = $correctTransferredReimbursement;
                    $invoice->transferred_reimbursement_sst_amt = $correctTransferredReimbursementSst;
                    $invoice->save();
                    
                    $fixedCount++;
                    
                    \Illuminate\Support\Facades\Log::info('Fixed invoice ' . $invoice->id . ' transferred amounts:', [
                        'old_transferred_pfee' => $invoice->transferred_pfee_amt,
                        'new_transferred_pfee' => $correctTransferredPfee,
                        'old_transferred_sst' => $invoice->transferred_sst_amt,
                        'new_transferred_sst' => $correctTransferredSst
                    ]);
                }
            }
            
            return response()->json([
                'status' => 1,
                'message' => "Successfully fixed transferred amounts for {$fixedCount} invoices",
                'fixed_count' => $fixedCount
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fixing transferred amounts: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error fixing transferred amounts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current invoices for display (existing invoices that cannot be edited)
     */
    public function getCurrentInvoices($transferFeeId)
    {
        try {
            $currentInvoices = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
                ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
                ->select(
                    'transfer_fee_details.*',
                    'im.invoice_no',
                    DB::raw('DATE(im.Invoice_date) as invoice_date'),
                    'im.transferred_pfee_amt',
                    'im.transferred_sst_amt',
                    'im.pfee1_inv',
                    'im.pfee2_inv',
                    'im.sst_inv',
                    'b.invoice_no as bill_invoice_no',
                    DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                    'l.case_ref_no',
                    'c.name as client_name',
                    'ibp.customer_name as billing_party_name'
                )
                ->get();

            $totalAmount = $currentInvoices->sum('transfer_amount') + $currentInvoices->sum('sst_amount');
            
            return response()->json([
                'status' => 1,
                'current_invoices' => $currentInvoices,
                'total_amount' => $totalAmount,
                'invoice_count' => $currentInvoices->count()
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting current invoices: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error getting current invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate AJAX-friendly pagination HTML
     */
    private function generateAjaxPagination($paginator, $queryParams = [])
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $total = $paginator->total();
        
        if ($lastPage <= 1) {
            return '<div class="text-center text-muted">No pagination needed</div>';
        }
        
        $html = '<nav><ul class="pagination justify-content-center">';
        
        // Previous button
        if ($currentPage > 1) {
            $html .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . ($currentPage - 1) . '">Previous</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }
        
        // Page numbers
        $start = max(1, $currentPage - 2);
        $end = min($lastPage, $currentPage + 2);
        
        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="1">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a></li>';
            }
        }
        
        if ($end < $lastPage) {
            if ($end < $lastPage - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . $lastPage . '">' . $lastPage . '</a></li>';
        }
        
        // Next button
        if ($currentPage < $lastPage) {
            $html .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . ($currentPage + 1) . '">Next</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
}
