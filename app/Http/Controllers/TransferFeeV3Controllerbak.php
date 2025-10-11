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
use Barryvdh\DomPDF\Facade as PDF;

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
                
                $TransferFeeMain = $query->paginate(10);

                // Check if this is an AJAX request
                if ($request->ajax()) {
                    $tableBody = view('dashboard.transfer-fee-v3.partials.table-body', [
                        'TransferFeeMain' => $TransferFeeMain
                    ])->render();
                    
                    $pagination = $TransferFeeMain->appends($request->query())->render();
                    
                                         $hasFilters = $request->input("transaction_id") || 
                                  $request->input("transfer_date_from") || 
                                  $request->input("transfer_date_to") || 
                                  ($request->input("branch_id") && $request->input("branch_id") != '0');
                    
                    return response()->json([
                        'tableBody' => $tableBody,
                        'pagination' => $pagination,
                        'hasFilters' => $hasFilters,
                        'sortBy' => $sortBy,
                        'sortOrder' => $sortOrder
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
                    'im.Invoice_date as invoice_date',
                    'im.status',
                    'im.loan_case_main_bill_id',
                    'im.transferred_pfee_amt',
                    'im.transferred_sst_amt',
                    'im.pfee1_inv', // Use invoice data directly
                    'im.pfee2_inv', // Use invoice data directly
                    'im.sst_inv',   // Use invoice data directly
                    'b.payment_receipt_date', // Payment receipt date from bill table
                    'b.invoice_no as bill_invoice_no',
                    'b.invoice_date as bill_invoice_date',
                    'b.invoice_branch_id', // Add branch ID for filtering
                    'l.case_ref_no',
                    'l.id as case_id', // Add case_id for hyperlink
                    'c.name as client_name',
                    'ibp.customer_code',
                    'ibp.customer_name as billing_party_name'
                )
                ->where('im.status', '<>', 99)
                ->where('im.transferred_to_office_bank', '=', 0); // Only show invoices that haven't been transferred

            // Apply centralized branch filtering ONLY for non-admin/account roles
            if (!in_array($current_user->menuroles, ['admin', 'account'])) {
                \Illuminate\Support\Facades\Log::info('Branch filtering before (non-admin/account):', [
                    'user_id' => $current_user->id,
                    'user_branch_id' => $current_user->branch_id,
                    'user_menuroles' => $current_user->menuroles,
                    'query_sql' => $query->toSql(),
                    'query_bindings' => $query->getBindings()
                ]);
                
                // Get accessible branches for the user
                $accessibleBranches = [];
                if (in_array($current_user->menuroles, ['maker'])) {
                    if (in_array($current_user->branch_id, [5,6])) {
                        $accessibleBranches = [5, 6];
                    } else {
                        $accessibleBranches = [$current_user->branch_id];
                    }
                } else if (in_array($current_user->menuroles, ['lawyer'])) {
                    $accessibleBranches = [$current_user->branch_id];
                } else {
                    $accessibleBranches = [$current_user->branch_id];
                }
                
                // Apply branch filtering with fallback to case branch if invoice_branch_id is NULL
                $query->where(function($q) use ($accessibleBranches) {
                    $q->whereIn('b.invoice_branch_id', $accessibleBranches)
                      ->orWhere(function($subQ) use ($accessibleBranches) {
                          $subQ->whereNull('b.invoice_branch_id')
                                ->whereIn('l.branch_id', $accessibleBranches);
                      });
                });
                
                \Illuminate\Support\Facades\Log::info('Branch filtering after (non-admin/account):', [
                    'query_sql' => $query->toSql(),
                    'query_bindings' => $query->getBindings(),
                    'accessible_branches' => $accessibleBranches
                ]);
            } else {
                \Illuminate\Support\Facades\Log::info('Skipping branch filtering for admin/account role:', [
                    'user_id' => $current_user->id,
                    'user_branch_id' => $current_user->branch_id,
                    'user_menuroles' => $current_user->menuroles
                ]);
            }

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
                
                \Illuminate\Support\Facades\Log::info('getTransferInvoiceListV3 - Invoice search filter applied:', [
                    'search_invoice_no' => $searchInvoiceNo,
                    'parsed_invoice_numbers' => $invoiceNumbers,
                    'count' => count($invoiceNumbers)
                ]);
                
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
                    
                    \Illuminate\Support\Facades\Log::info('getTransferInvoiceListV3 - Query after invoice filter:', [
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
                    case 'transferred_bal':
                        $query = $query->orderBy('im.transferred_pfee_amt', $sortOrder);
                        break;
                    case 'transferred_sst':
                        $query = $query->orderBy('im.transferred_sst_amt', $sortOrder);
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

            // Log the final query before execution
            \Illuminate\Support\Facades\Log::info('getTransferInvoiceListV3 - Final query before execution:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'user_id' => $current_user->id,
                'user_branch' => $current_user->branch_id,
                'search_invoice_no' => $searchInvoiceNo
            ]);
            
            // Get total count for pagination
            $totalCount = $query->count();
            
            \Illuminate\Support\Facades\Log::info('getTransferInvoiceListV3 - Total count result:', [
                'total_count' => $totalCount
            ]);
            
            // Get paginated results
            $rows = $query->offset(($page - 1) * $perPage)
                         ->limit($perPage)
                         ->get();
                         
            \Illuminate\Support\Facades\Log::info('getTransferInvoiceListV3 - Paginated results:', [
                'count' => count($rows),
                'first_few_results' => $rows->take(3)->toArray()
            ]);

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

            if (count($add_invoices) > 0) {
                for ($i = 0; $i < count($add_invoices); $i++) {
                    // Create transfer fee details record
                    $TransferFeeDetails = new TransferFeeDetails();
                    $total_amount += $add_invoices[$i]['value'];

                    $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                    $TransferFeeDetails->loan_case_invoice_main_id = $add_invoices[$i]['id'];
                    $TransferFeeDetails->loan_case_main_bill_id = $add_invoices[$i]['bill_id'];
                    $TransferFeeDetails->created_by = $current_user->id;
                    $TransferFeeDetails->transfer_amount = $add_invoices[$i]['value'];

                    if ($add_invoices[$i]['sst'] > 0) {
                        $TransferFeeDetails->sst_amount = $add_invoices[$i]['sst'];
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
                        
                        // Check if both pfee and SST are fully transferred
                        $remaining_pfee = bcsub($inv_pfee, $SumTransferFee, 2);
                        $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSst, 2);
                        
                        // Mark as fully transferred only if both pfee and SST are <= 0
                        if ($remaining_pfee <= 0 && $remaining_sst <= 0) {
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
                        
                        // Check if both pfee and SST are fully transferred
                        $remaining_pfee_bill = bcsub($inv_pfee_bill, $SumTransferFeeBill, 2);
                        $remaining_sst_bill = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSstBill, 2);
                        
                        // Mark as fully transferred only if both pfee and SST are <= 0
                        if ($remaining_pfee_bill <= 0 && $remaining_sst_bill <= 0) {
                            $LoanCaseBillMain->transferred_to_office_bank = 1;
                        } else {
                            $LoanCaseBillMain->transferred_to_office_bank = 0;
                        }
                        
                        $LoanCaseBillMain->save();
                    }

                    // Create ledger entries for accounting
                    $this->addLedgerEntriesV3($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $add_invoices[$i]['sst'], $add_invoices[$i]['value']);
                }
            }

            // Update total amount in main record
            $TransferFeeMain->transfer_amount = $total_amount;
            $TransferFeeMain->save();

            // Update transfer fee main amount (for consistency with original system)
            $this->updateTransferFeeMainAmt($TransferFeeMain->id);

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
        $TransferFeeDetailsSum = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)->get();

        if (count($TransferFeeDetailsSum) > 0) {
            for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                $SumTransferFee += $TransferFeeDetailsSum[$j]->transfer_amount + $TransferFeeDetailsSum[$j]->sst_amount;
            }
        }

        $TransferFeeMain->transfer_amount = $SumTransferFee;
        $TransferFeeMain->save();
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
                'im.Invoice_date as invoice_date',
                'im.transferred_pfee_amt',
                'im.transferred_sst_amt',
                'im.pfee1_inv',
                'im.pfee2_inv',
                'im.sst_inv',
                'b.invoice_no as bill_invoice_no',
                'b.Invoice_date as bill_invoice_date',
                'b.payment_receipt_date',
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
                'im.Invoice_date as invoice_date',
                'im.transferred_pfee_amt',
                'im.transferred_sst_amt',
                'im.pfee1_inv',
                'im.pfee2_inv',
                'im.sst_inv',
                'b.invoice_no as bill_invoice_no',
                'b.Invoice_date as bill_invoice_date',
                'b.payment_receipt_date',
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
            
            // Calculate available amounts
            $originalPfee = ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);
            $originalSst = $detail->sst_inv ?? 0;
            
            $detail->available_pfee = max(0, $originalPfee - $totalTransferredPfee);
            $detail->available_sst = max(0, $originalSst - $totalTransferredSst);
            
            // Store current transfer amounts for reference
            $detail->current_transfer_pfee = $detail->transfer_amount ?? 0;
            $detail->current_transfer_sst = $detail->sst_amount ?? 0;
        }



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
            
            // Additional validation for invoice data
            if ($request->input('add_invoice')) {
                $add_invoices = json_decode($request->input('add_invoice'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json(['status' => 0, 'message' => 'Invalid invoice data format']);
                }
                
                // Validate that all required invoice fields are present
                foreach ($add_invoices as $index => $invoice) {
                    if (!isset($invoice['id']) || !isset($invoice['bill_id'])) {
                        return response()->json(['status' => 0, 'message' => 'Missing required invoice data at index ' . $index]);
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
            }
            
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

            // Process invoice changes if provided
            if ($request->input('add_invoice')) {
                $add_invoices = json_decode($request->input('add_invoice'), true);
                
                // Get original transfer details BEFORE deletion for proper calculation
                $originalTransferDetails = TransferFeeDetails::where('transfer_fee_main_id', $id)->get();
                $originalDetailsMap = [];
                
                foreach ($originalTransferDetails as $originalDetail) {
                    $key = $originalDetail->loan_case_invoice_main_id;
                    $originalDetailsMap[$key] = [
                        'id' => $originalDetail->id, // Store the TransferFeeDetails ID
                        'transfer_amount' => $originalDetail->transfer_amount,
                        'sst_amount' => $originalDetail->sst_amount ?? 0
                    ];
                }
                
                // Delete existing transfer fee details
                TransferFeeDetails::where('transfer_fee_main_id', $id)->delete();
                
                // Handle removed invoices - subtract their transfer amounts from loan_case_invoice_main
                $currentInvoiceIds = array_column($add_invoices, 'id');
                \Illuminate\Support\Facades\Log::info('Invoice comparison:', [
                    'original_invoice_ids' => array_column($originalTransferDetails->toArray(), 'loan_case_invoice_main_id'),
                    'current_invoice_ids' => $currentInvoiceIds,
                    'removed_invoice_ids' => array_diff(array_column($originalTransferDetails->toArray(), 'loan_case_invoice_main_id'), $currentInvoiceIds)
                ]);
                
                foreach ($originalTransferDetails as $originalDetail) {
                    if (!in_array($originalDetail->loan_case_invoice_main_id, $currentInvoiceIds)) {
                        // This invoice was removed, subtract its transfer amounts
                        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $originalDetail->loan_case_invoice_main_id)->first();
                        if ($LoanCaseInvoiceMain) {
                            $newTransferredPfee = $LoanCaseInvoiceMain->transferred_pfee_amt - $originalDetail->transfer_amount;
                            $newTransferredSst = $LoanCaseInvoiceMain->transferred_sst_amt - ($originalDetail->sst_amount ?? 0);
                            
                            // Ensure values don't go below 0
                            $newTransferredPfee = max(0, $newTransferredPfee);
                            $newTransferredSst = max(0, $newTransferredSst);
                            
                            $LoanCaseInvoiceMain->transferred_pfee_amt = $newTransferredPfee;
                            $LoanCaseInvoiceMain->transferred_sst_amt = $newTransferredSst;
                            
                            $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                            
                            // Check if both pfee and SST are fully transferred
                            $remaining_pfee = bcsub($inv_pfee, $newTransferredPfee, 2);
                            $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $newTransferredSst, 2);
                            
                            // Mark as fully transferred only if both pfee and SST are <= 0
                            if ($remaining_pfee <= 0 && $remaining_sst <= 0) {
                                $LoanCaseInvoiceMain->transferred_to_office_bank = 1;
                            } else {
                                $LoanCaseInvoiceMain->transferred_to_office_bank = 0;
                            }
                            
                            $LoanCaseInvoiceMain->save();
                            
                            // Clean up ledger entries for removed invoice
                            // Delete LedgerEntriesV2 (new system) - delete by key_id_2 (TransferFeeDetails ID)
                            $deletedLedgerV2 = LedgerEntriesV2::where('key_id_2', $originalDetail->id)
                                ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT', 'SST_IN', 'SST_OUT'])
                                ->delete();
                            
                            // Delete LedgerEntries (old system) - delete by key_id (TransferFeeDetails ID)
                            $deletedLedger = LedgerEntries::where('key_id', $originalDetail->id)
                                ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'SSTIN', 'SSTOUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON', 'SSTINRECON', 'SSTOUTRECON'])
                                ->delete();
                            
                            // Debug logging for removed invoice
                            \Illuminate\Support\Facades\Log::info('Removed invoice ' . $originalDetail->loan_case_invoice_main_id . ' from transfer:', [
                                'originalTransferAmount' => $originalDetail->transfer_amount,
                                'originalSstAmount' => $originalDetail->sst_amount ?? 0,
                                'newTransferredPfee' => $newTransferredPfee,
                                'newTransferredSst' => $newTransferredSst,
                                'deletedLedgerV2' => $deletedLedgerV2,
                                'deletedLedger' => $deletedLedger
                            ]);
                        }
                        
                        // Also update bill record for backward compatibility
                        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $originalDetail->loan_case_main_bill_id)->first();
                        if ($LoanCaseBillMain) {
                            $newTransferredPfeeBill = $LoanCaseBillMain->transferred_pfee_amt - $originalDetail->transfer_amount;
                            $newTransferredSstBill = $LoanCaseBillMain->transferred_sst_amt - ($originalDetail->sst_amount ?? 0);
                            
                            // Ensure values don't go below 0
                            $newTransferredPfeeBill = max(0, $newTransferredPfeeBill);
                            $newTransferredSstBill = max(0, $newTransferredSstBill);
                            
                            $LoanCaseBillMain->transferred_pfee_amt = $newTransferredPfeeBill;
                            $LoanCaseBillMain->transferred_sst_amt = $newTransferredSstBill;
                            
                            $inv_pfee_bill = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                            
                            // Check if both pfee and SST are fully transferred
                            $remaining_pfee_bill = bcsub($inv_pfee_bill, $newTransferredPfeeBill, 2);
                            $remaining_sst_bill = bcsub($LoanCaseInvoiceMain->sst_inv, $newTransferredSstBill, 2);
                            
                            // Mark as fully transferred only if both pfee and SST are <= 0
                            if ($remaining_pfee_bill <= 0 && $remaining_sst_bill <= 0) {
                                $LoanCaseBillMain->transferred_to_office_bank = 1;
                            } else {
                                $LoanCaseBillMain->transferred_to_office_bank = 0;
                            }
                            
                            $LoanCaseBillMain->save();
                        }
                    }
                }
                
                if (count($add_invoices) > 0) {
                    
                    $total_amount = 0;
                    
                    // Create new transfer fee details
                    for ($i = 0; $i < count($add_invoices); $i++) {
                        $TransferFeeDetails = new TransferFeeDetails();
                        $total_amount += $add_invoices[$i]['value'] + $add_invoices[$i]['sst'];

                        $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                        $TransferFeeDetails->loan_case_invoice_main_id = $add_invoices[$i]['id'];
                        $TransferFeeDetails->loan_case_main_bill_id = $add_invoices[$i]['bill_id'];
                        $TransferFeeDetails->created_by = $current_user->id;
                        $TransferFeeDetails->transfer_amount = $add_invoices[$i]['value'];

                        if ($add_invoices[$i]['sst'] > 0) {
                            $TransferFeeDetails->sst_amount = $add_invoices[$i]['sst'];
                        }

                        $TransferFeeDetails->status = 1;
                        $TransferFeeDetails->created_at = date('Y-m-d H:i:s');
                        $TransferFeeDetails->save();

                        // Delete existing ledger entries for this invoice before creating new ones
                        // This prevents duplicate ledger entries when updating
                        
                        // Delete LedgerEntriesV2 (new system) - delete by key_id_2 (TransferFeeDetails ID)
                        $originalDetailId = isset($originalDetailsMap[$add_invoices[$i]['id']]) ? $originalDetailsMap[$add_invoices[$i]['id']]['id'] : null;
                        
                        if ($originalDetailId) {
                            $deletedLedgerV2 = LedgerEntriesV2::where('key_id_2', $originalDetailId)
                                ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT', 'SST_IN', 'SST_OUT'])
                                ->delete();
                        } else {
                            $deletedLedgerV2 = 0;
                        }
                        
                        \Illuminate\Support\Facades\Log::info('Deleted ' . $deletedLedgerV2 . ' existing LedgerEntriesV2 records for invoice ' . $add_invoices[$i]['id']);
                        
                        // Delete LedgerEntries (old system) - delete by key_id (TransferFeeDetails ID)
                        $originalDetailId = isset($originalDetailsMap[$add_invoices[$i]['id']]) ? $originalDetailsMap[$add_invoices[$i]['id']]['id'] : null;
                        
                        if ($originalDetailId) {
                            $deletedLedger = LedgerEntries::where('key_id', $originalDetailId)
                                ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'SSTIN', 'SSTOUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON', 'SSTINRECON', 'SSTOUTRECON'])
                                ->delete();
                            
                            \Illuminate\Support\Facades\Log::info('Deleted ' . $deletedLedger . ' existing LedgerEntries records for TransferFeeDetails ID ' . $originalDetailId);
                        } else {
                            \Illuminate\Support\Facades\Log::info('No original TransferFeeDetails found for invoice ' . $add_invoices[$i]['id'] . ', skipping LedgerEntries deletion');
                        }

                        // Update invoice record (loan_case_invoice_main)
                        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $add_invoices[$i]['id'])->first();
                        if ($LoanCaseInvoiceMain) {
                            // Get the original transferred amounts before this update
                            $originalTransferredPfee = $LoanCaseInvoiceMain->transferred_pfee_amt;
                            $originalTransferredSst = $LoanCaseInvoiceMain->transferred_sst_amt;
                            
                            // Get the original transfer amounts for this specific transfer fee record from our map
                            $originalTransferAmount = isset($originalDetailsMap[$add_invoices[$i]['id']]) ? $originalDetailsMap[$add_invoices[$i]['id']]['transfer_amount'] : 0;
                            $originalSstAmount = isset($originalDetailsMap[$add_invoices[$i]['id']]) ? $originalDetailsMap[$add_invoices[$i]['id']]['sst_amount'] : 0;
                            
                            // Calculate new total transferred amounts
                            // Subtract the original amounts from this transfer and add the new amounts
                            $newTransferredPfee = $originalTransferredPfee - $originalTransferAmount + $add_invoices[$i]['value'];
                            $newTransferredSst = $originalTransferredSst - $originalSstAmount + $add_invoices[$i]['sst'];
                            
                            // Debug logging for calculation
                            \Illuminate\Support\Facades\Log::info('Transfer calculation for invoice ' . $add_invoices[$i]['id'] . ':', [
                                'originalTransferredPfee' => $originalTransferredPfee,
                                'originalTransferAmount' => $originalTransferAmount,
                                'newValue' => $add_invoices[$i]['value'],
                                'newTransferredPfee' => $newTransferredPfee,
                                'originalTransferredSst' => $originalTransferredSst,
                                'originalSstAmount' => $originalSstAmount,
                                'newSstValue' => $add_invoices[$i]['sst'],
                                'newTransferredSst' => $newTransferredSst
                            ]);
                            
                            $LoanCaseInvoiceMain->transferred_pfee_amt = $newTransferredPfee;
                            $LoanCaseInvoiceMain->transferred_sst_amt = $newTransferredSst;
                            
                            $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                            
                            // Check if both pfee and SST are fully transferred
                            $remaining_pfee = bcsub($inv_pfee, $newTransferredPfee, 2);
                            $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $newTransferredSst, 2);
                            
                            // Mark as fully transferred only if both pfee and SST are <= 0
                            if ($remaining_pfee <= 0 && $remaining_sst <= 0) {
                                $LoanCaseInvoiceMain->transferred_to_office_bank = 1;
                            } else {
                                $LoanCaseInvoiceMain->transferred_to_office_bank = 0;
                            }
                            
                            $LoanCaseInvoiceMain->save();
                        }

                        // Update bill record (loan_case_bill_main) for backward compatibility
                        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $add_invoices[$i]['bill_id'])->first();
                        if ($LoanCaseBillMain) {
                            // Get the original transferred amounts before this update
                            $originalTransferredPfeeBill = $LoanCaseBillMain->transferred_pfee_amt;
                            $originalTransferredSstBill = $LoanCaseBillMain->transferred_sst_amt;
                            
                            // Get the original transfer amounts for this specific transfer fee record from our map
                            $originalTransferAmountBill = isset($originalDetailsMap[$add_invoices[$i]['id']]) ? $originalDetailsMap[$add_invoices[$i]['id']]['transfer_amount'] : 0;
                            $originalSstAmountBill = isset($originalDetailsMap[$add_invoices[$i]['id']]) ? $originalDetailsMap[$add_invoices[$i]['id']]['sst_amount'] : 0;
                            
                            // Calculate new total transferred amounts for bill
                            // Subtract the original amounts from this transfer and add the new amounts
                            $newTransferredPfeeBill = $originalTransferredPfeeBill - $originalTransferAmountBill + $add_invoices[$i]['value'];
                            $newTransferredSstBill = $originalTransferredSstBill - $originalSstAmountBill + $add_invoices[$i]['sst'];
                            
                            $LoanCaseBillMain->transferred_pfee_amt = $newTransferredPfeeBill;
                            $LoanCaseBillMain->transferred_sst_amt = $newTransferredSstBill;
                            
                            // Use invoice data for consistency, but still update bill record for backward compatibility
                            $inv_pfee_bill = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                            
                            // Check if both pfee and SST are fully transferred
                            $remaining_pfee_bill = bcsub($inv_pfee_bill, $newTransferredPfeeBill, 2);
                            $remaining_sst_bill = bcsub($LoanCaseInvoiceMain->sst_inv, $newTransferredSstBill, 2);
                            
                            // Mark as fully transferred only if both pfee and SST are <= 0
                            if ($remaining_pfee_bill <= 0 && $remaining_sst_bill <= 0) {
                                $LoanCaseBillMain->transferred_to_office_bank = 1;
                            } else {
                                $LoanCaseBillMain->transferred_to_office_bank = 0;
                            }
                            
                            $LoanCaseBillMain->save();
                        }

                        // Create ledger entries for accounting
                        $this->addLedgerEntriesV3($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $add_invoices[$i]['sst'], $add_invoices[$i]['value']);
                    }

                    // Update total amount in main record
                    $TransferFeeMain->transfer_amount = $total_amount;
                    $TransferFeeMain->save();

                    // Update transfer fee main amount (for consistency with original system)
                    $this->updateTransferFeeMainAmt($TransferFeeMain->id);
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
                ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT', 'SST_IN', 'SST_OUT'])->delete();
            
            // Delete LedgerEntries (old system) - delete by key_id (TransferFeeDetails ID)
            $deletedLedger = LedgerEntries::where('key_id', '=', $detail->id)
                ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'SSTIN', 'SSTOUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON', 'SSTINRECON', 'SSTOUTRECON'])->delete();
            
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
                        $SumTransferFeeBill += $TransferFeeDetailsSumBill[$j]->transfer_amount;
                        $SumTransferSstBill += $TransferFeeDetailsSumBill[$j]->sst_amount ?? 0;
                    }
                }
                
                $LoanCaseBillMain->transferred_pfee_amt = $SumTransferFeeBill;
                $LoanCaseBillMain->transferred_sst_amt = $SumTransferSstBill;
                
                // Check if both pfee and SST are still fully transferred
                $inv_pfee_bill = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;
                $remaining_pfee_bill = bcsub($inv_pfee_bill, $SumTransferFeeBill, 2);
                $remaining_sst_bill = bcsub($LoanCaseBillMain->sst_inv, $SumTransferSstBill, 2);
                
                // Mark as not fully transferred if either pfee or SST has remaining balance
                if ($remaining_pfee_bill > 0 || $remaining_sst_bill > 0) {
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
                if (count($TransferFeeDetailsSumInvoice) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSumInvoice); $j++) {
                        $SumTransferFeeInvoice += $TransferFeeDetailsSumInvoice[$j]->transfer_amount;
                        $SumTransferSstInvoice += $TransferFeeDetailsSumInvoice[$j]->sst_amount ?? 0;
                    }
                }
                
                $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFeeInvoice;
                $LoanCaseInvoiceMain->transferred_sst_amt = $SumTransferSstInvoice;
                
                // Check if both pfee and SST are still fully transferred
                $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                $remaining_pfee = bcsub($inv_pfee, $SumTransferFeeInvoice, 2);
                $remaining_sst = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSstInvoice, 2);
                
                // Mark as not fully transferred if either pfee or SST has remaining balance
                if ($remaining_pfee > 0 || $remaining_sst > 0) {
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
                ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT', 'SST_IN', 'SST_OUT'])->delete();
            
            $deletedLedger = LedgerEntries::where('key_id', '=', $TransferFeeDetail->id)
                ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'SSTIN', 'SSTOUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON', 'SSTINRECON', 'SSTOUTRECON'])->delete();

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
                        $SumTransferFeeBill += $TransferFeeDetailsSumBill[$j]->transfer_amount;
                        $SumTransferSstBill += $TransferFeeDetailsSumBill[$j]->sst_amount ?? 0;
                    }
                }
                
                $LoanCaseBillMain->transferred_pfee_amt = $SumTransferFeeBill;
                $LoanCaseBillMain->transferred_sst_amt = $SumTransferSstBill;
                
                // Check if both pfee and SST are still fully transferred
                $inv_pfee_bill = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;
                $remaining_pfee_bill = bcsub($inv_pfee_bill, $SumTransferFeeBill, 2);
                $remaining_sst_bill = bcsub($LoanCaseBillMain->sst_inv, $SumTransferSstBill, 2);
                
                // Mark as not fully transferred if either pfee or SST has remaining balance
                if ($remaining_pfee_bill > 0 || $remaining_sst_bill > 0) {
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
                if (count($TransferFeeDetailsSumInvoice) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSumInvoice); $j++) {
                        $SumTransferFeeInvoice += $TransferFeeDetailsSumInvoice[$j]->transfer_amount;
                        $SumTransferSstInvoice += $TransferFeeDetailsSumInvoice[$j]->sst_amount ?? 0;
                    }
                }
                
                $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFeeInvoice;
                $LoanCaseInvoiceMain->transferred_sst_amt = $SumTransferSstInvoice;
                
                // Check if both pfee and SST are still fully transferred
                $inv_pfee_invoice = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                $remaining_pfee_invoice = bcsub($inv_pfee_invoice, $SumTransferFeeInvoice, 2);
                $remaining_sst_invoice = bcsub($LoanCaseInvoiceMain->sst_inv, $SumTransferSstInvoice, 2);
                
                // Mark as not fully transferred if either pfee or SST has remaining balance
                if ($remaining_pfee_invoice > 0 || $remaining_sst_invoice > 0) {
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
        }
        
        $AccountLog->status = '1';
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();
    }

    /**
     * Create ledger entries for V3
     */
    private function addLedgerEntriesV3($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $sst_amount, $transfer_amount)
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
            $invoices = $request->input('invoices', []);
            
            // Get transfer fee details
            $transferFee = TransferFeeMain::find($transferFeeId);
            if (!$transferFee) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Transfer fee not found'
                ], 404);
            }

            if (empty($invoices)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No invoices found for export'
                ], 404);
            }

            // Prepare data for export using the actual table data
            $exportData = [];
            foreach ($invoices as $index => $invoice) {
                $exportData[] = [
                    'No' => $index + 1,
                    'Case Ref No' => $invoice['case_ref'] ?? 'N/A',
                    'Invoice No' => $invoice['invoice_no'] ?? 'N/A',
                    'Invoice Date' => $invoice['invoice_date'] ?? 'N/A',
                    'Total Amount' => (float)($invoice['value'] ?? 0) + (float)($invoice['sst'] ?? 0),
                    'Collected Amount' => (float)($invoice['value'] ?? 0) + (float)($invoice['sst'] ?? 0),
                    'Professional Fee' => (float)($invoice['value'] ?? 0),
                    'SST' => (float)($invoice['sst'] ?? 0),
                    'Pfee to Transfer' => (float)($invoice['current_transfer_pfee'] ?? 0),
                    'SST to Transfer' => (float)($invoice['current_transfer_sst'] ?? 0),
                    'Transferred Balance' => (float)($invoice['current_transfer_pfee'] ?? 0),
                    'Transferred SST' => (float)($invoice['current_transfer_sst'] ?? 0),
                    'Payment Date' => $invoice['payment_date'] ?? 'N/A'
                ];
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
                'Pfee to Transfer' => array_sum(array_column($exportData, 'Pfee to Transfer')),
                'SST to Transfer' => array_sum(array_column($exportData, 'SST to Transfer')),
                'Transferred Balance' => array_sum(array_column($exportData, 'Transferred Balance')),
                'Transferred SST' => array_sum(array_column($exportData, 'Transferred SST')),
                'Payment Date' => ''
            ];
            $exportData[] = $totals;

            if ($format === 'excel') {
                return $this->exportToExcel($exportData, $transferFee);
            } else {
                return $this->exportToPDF($exportData, $transferFee);
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
    private function exportToExcel($data, $transferFee)
    {
        $filename = 'transfer_fee_invoices_' . $transferFee->transaction_id . '_' . date('Y-m-d') . '.xlsx';
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Transfer Fee Invoices Report');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set subtitle
        $sheet->setCellValue('A2', 'Transaction ID: ' . ($transferFee->transaction_id ?? 'N/A'));
        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A3', 'Transfer Date: ' . ($transferFee->transfer_date ?? 'N/A'));
        $sheet->mergeCells('A3:M3');
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A4', 'Purpose: ' . ($transferFee->purpose ?? 'N/A'));
        $sheet->mergeCells('A4:M4');
        $sheet->getStyle('A4')->getFont()->setSize(12);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
        
        // Add empty row
        $sheet->setCellValue('A5', '');
        
        // Write headers starting from row 6
        $headers = array_keys($data[0]);
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '6', $header);
            $sheet->getStyle($col . '6')->getFont()->setBold(true);
            $sheet->getStyle($col . '6')->getAlignment()->setHorizontal('center');
            $col++;
        }
        
        // Write data
        $row = 7;
        foreach ($data as $rowData) {
            $col = 'A';
            foreach ($rowData as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set number format for amount columns (E, F, G, H, I, J, K, L)
        $sheet->getStyle('E7:L' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Style the totals row
        if (!empty($data) && end($data)['No'] === 'TOTAL') {
            $lastRow = $row - 1;
            $sheet->getStyle('A' . $lastRow . ':M' . $lastRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $lastRow . ':M' . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
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
    private function exportToPDF($data, $transferFee)
    {
        $filename = 'transfer_fee_invoices_' . $transferFee->transaction_id . '_' . date('Y-m-d') . '.pdf';
        
        // Generate PDF using DomPDF
        $pdf = PDF::loadView('dashboard.transfer-fee-v3.export.pdf', [
            'data' => $data,
            'transferFee' => $transferFee
        ]);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        
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
}
