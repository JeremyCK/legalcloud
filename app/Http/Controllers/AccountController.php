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
use App\Models\JournalEntryDetails;
use App\Models\JournalEntryMain;
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseBillMain;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\PermissionController;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('admin');

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $accounts = DB::table('account AS a')
            ->leftJoin('account_category AS ac', 'ac.id', '=', 'a.account_category_id')
            ->select('a.*', 'ac.category')
            ->orderBy('id', 'ASC')
            ->paginate(10);

        return view('dashboard.account.index', ['accounts' => $accounts]);
    }

    public function transferFeeList()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        $TransferFeeMain = TransferFeeMain::where('status', '=', 1);

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }
        
        if (!in_array($current_user->menuroles, ['admin', 'account'])) {
                

            if (in_array($current_user->branch_id, [5,6])) {
                $TransferFeeMain = $TransferFeeMain->whereIn('branch_id', [5,6]);
            }
            else
            {
                $TransferFeeMain = $TransferFeeMain->where('branch_id',$current_user->branch_id);
            }
        }

        $TransferFeeMain = $TransferFeeMain->get();
        
        return view('dashboard.transfer-fee.index', [
            'TransferFeeMain' => $TransferFeeMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function transferFeeView($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

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
        return view('dashboard.transfer-fee.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'TransferFeeMain' => $TransferFeeMain,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function transferFeeEdit($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
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
        
        return view('dashboard.transfer-fee.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'TransferFeeMain' => $TransferFeeMain,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function transferFeeCreate()
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
        }else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        return view('dashboard.transfer-fee.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function getTransferList()
    {
        $current_user = auth()->user();

        $rows = DB::table('loan_case_bill_main as b')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
            ->where('b.transferred_to_office_bank', '=',  0)
            ->where('b.status', '<>',  99);

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $rows = $rows->where('l.branch_id', '=',  3);
            }
        }

        $rows = $rows->orderBy('b.id', 'ASC')->get();


        $billList = view('dashboard.transfer-fee.table.tbl-transfer-list', compact('rows', 'current_user'))->render();

        return [
            'status' => 1,
            'billList' => $billList,
        ];
    }

    public function getTransferMainList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();
            // $TransferFeeMain = TransferFeeMain::where('status', '=', 1)->get();


            $TransferFeeMain = DB::table('transfer_fee_main as m')
                ->leftJoin('office_bank_account as b1', 'b1.id', '=', 'm.transfer_from')
                ->leftJoin('office_bank_account as b2', 'b2.id', '=', 'm.transfer_to')
                ->leftJoin('users as u', 'u.id', '=', 'm.transfer_by')
                ->select('m.*', 'b1.name as transfer_from_bank', 'b2.name as transfer_to_bank', 'b1.account_no as transfer_from_bank_acc_no', 'b2.account_no as transfer_to_bank_acc_no',)
                ->where('m.status', '<>',  99);

            if ($request->input("transfer_date_from") <> null && $request->input("transfer_date_to") <> null) {
                $TransferFeeMain = $TransferFeeMain->whereBetween('m.transfer_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            } else {
                if ($request->input("transfer_date_from") <> null) {
                    $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
                }

                if ($request->input("transfer_date_to") <> null) {
                    $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
                }
            }

            if ($request->input("branch_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  $request->input("branch_id"));
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5,6])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [5,6]);
                }
                else
                {
                    $TransferFeeMain = $TransferFeeMain->where('b2.branch_id', '=',  $current_user->branch_id);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [51, 32])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [5, 6]);
                }
            }else {
                if (in_array($current_user->id, [13])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [$current_user->branch_id]);
                }
            }

            // if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker', 'lawyer'])) {

            //     $TransferFeeMain = $TransferFeeMain->where(function ($q) use ($current_user, $branchInfo) {
            //         $q->whereIn('m.branch_id', $branchInfo['brancAccessList']);
            //     });
            // }
            

            $TransferFeeMain = $TransferFeeMain->orderBy('m.transfer_date', 'DESC')->get();

            return DataTables::of($TransferFeeMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    <a href="/transfer-fee/' . $data->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('transfer_from_bank', function ($data) {
                    $actionBtn = $data->transfer_from_bank . '<br/>(' . $data->transfer_from_bank_acc_no . ')';
                    return $actionBtn;
                })
                ->addColumn('transfer_to_bank', function ($data) {
                    $actionBtn = $data->transfer_to_bank . '<br/>(' . $data->transfer_to_bank_acc_no . ')';
                    return $actionBtn;
                })
                ->editColumn('is_recon', function ($data) {

                    if ($data->is_recon == '1' || $data->is_recon == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->rawColumns(['action', 'bal_to_transfer', 'transfer_from_bank', 'transfer_to_bank', 'transferred_to_office_bank', 'case_ref_no', 'is_recon'])
                ->make(true);
        }
    }

    public function saveTransferFee(Request $request)
    {
        return 'test';
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('recon_date', '=', $request->input("recon_date"))->first();

        if (!$TransferFeeMain) {
            $TransferFeeMain  = new TransferFeeMain();

            $TransferFeeMain->transfer_amount = $request->input("transfer_amount");
            $TransferFeeMain->transfer_date = $request->input("transfer_date");
            $TransferFeeMain->transfer_by = $current_user->id;
            $TransferFeeMain->transaction_id = $request->input("transaction_id");
            $TransferFeeMain->receipt_no = $request->input("receipt_no");
            $TransferFeeMain->transfer_from = $request->input("transfer_from");
            $TransferFeeMain->transfer_to = $request->input("transfer_to");
            $TransferFeeMain->purpose = $request->input("purpose");
            $TransferFeeMain->status = 1;
            $TransferFeeMain->created_at = date('Y-m-d H:i:s');
        } else {

            $TransferFeeMain->transfer_amount = $request->input("transfer_amount");
            $TransferFeeMain->transfer_date = $request->input("transfer_date");
            $TransferFeeMain->transfer_by = $current_user->id;
            $TransferFeeMain->transaction_id = $request->input("transaction_id");
            $TransferFeeMain->receipt_no = $request->input("receipt_no");
            $TransferFeeMain->transfer_from = $request->input("transfer_from");
            $TransferFeeMain->transfer_to = $request->input("transfer_to");
            $TransferFeeMain->purpose = $request->input("purpose");
            $TransferFeeMain->status = 1;
            $TransferFeeMain->created_at = date('Y-m-d H:i:s');
        }

        $TransferFeeMain->save();

        LedgerEntriesV2::where('key_id', $TransferFeeMain->id)->whereIn('type', ['TRANSFER_IN', 'SST_IN'])->update(['bank_id' => $request->input("transfer_to")]);
        LedgerEntriesV2::where('key_id', $TransferFeeMain->id)->whereIn('type', ['TRANSFER_OUT', 'SST_OUT'])->update(['bank_id' => $request->input("transfer_from")]);

        $TransferFeeMain = TransferFeeMain::where('transfer_fee_main_id', '=', $TransferFeeMain->id)->update(['stauts' => 0]);

        DB::table('post')
            ->where('id', 3)
            ->update(['title' => "Updated Title"]);
    }

    public function createNewTranferFee(Request $request)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $TransferFeeMain  = new TransferFeeMain();

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

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('id', $request->input("transfer_to"))->first();

        if ($OfficeBankAccount) {
            $TransferFeeMain->branch_id = $OfficeBankAccount->branch_id;
        }

        $TransferFeeMain->save();

        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
        }

        if (count($add_bill) > 0) {
            for ($i = 0; $i < count($add_bill); $i++) {
                $TransferFeeDetails  = new TransferFeeDetails();

                $total_amount += $add_bill[$i]['value'];

                $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                $TransferFeeDetails->loan_case_main_bill_id = $add_bill[$i]['id'];
                // $TransferFeeDetails->loan_case_invoice_main_id = $add_bill[$i]['invoice_id']; // Column doesn't exist in table
                $TransferFeeDetails->created_by = $current_user->id;
                $TransferFeeDetails->transfer_amount = $add_bill[$i]['value'];

                if ($add_bill[$i]['sst'] > 0) {
                    $TransferFeeDetails->sst_amount = $add_bill[$i]['sst'];
                }


                $TransferFeeDetails->status = 1;
                $TransferFeeDetails->created_at = date('Y-m-d H:i:s');

                $TransferFeeDetails->save();

                // Update both bill and invoice records
                $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->first();
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['invoice_id'])->first();

                // Update bill record
                $LoanCaseBillMain->transferred_pfee_amt += $add_bill[$i]['value'];

                $TransferFeeDetailsSum  = TransferFeeDetails::where('loan_case_main_bill_id', '=', $add_bill[$i]['id'])->get();

                $SumTransgerFee = 0;

                if (count($TransferFeeDetailsSum) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                        $SumTransgerFee += $TransferFeeDetailsSum[$j]->transfer_amount;
                    }
                }

                $LoanCaseBillMain->transferred_pfee_amt = $SumTransgerFee;
                $inv_pfee = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;

                $sum = bcsub($inv_pfee,$SumTransgerFee,2);

                if (($sum) <= 0) {
                    $LoanCaseBillMain->transferred_pfee_amt = $SumTransgerFee;
                    $LoanCaseBillMain->transferred_to_office_bank = 1;
                }

                if ($add_bill[$i]['sst'] > 0) {
                    $LoanCaseBillMain->transferred_sst_amt = $add_bill[$i]['sst'];
                }

                $LoanCaseBillMain->save();

                // Update invoice record - find by bill ID since we don't store invoice ID in transfer details
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', '=', $add_bill[$i]['id'])->first();
                if ($LoanCaseInvoiceMain) {
                    $LoanCaseInvoiceMain->transferred_pfee_amt += $add_bill[$i]['value'];
                    
                    // Calculate total transferred amount for this invoice
                    $TransferFeeDetailsSumInvoice = TransferFeeDetails::where('loan_case_main_bill_id', '=', $add_bill[$i]['id'])->get();
                    
                    $SumTransgerFeeInvoice = 0;
                    
                    if (count($TransferFeeDetailsSumInvoice) > 0) {
                        for ($j = 0; $j < count($TransferFeeDetailsSumInvoice); $j++) {
                            $SumTransgerFeeInvoice += $TransferFeeDetailsSumInvoice[$j]->transfer_amount;
                        }
                    }
                    
                    $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransgerFeeInvoice;
                    $inv_pfee_invoice = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                    
                    $sum_invoice = bcsub($inv_pfee_invoice, $SumTransgerFeeInvoice, 2);
                    
                    if (($sum_invoice) <= 0) {
                        $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransgerFeeInvoice;
                        $LoanCaseInvoiceMain->transferred_to_office_bank = 1;
                    }
                    
                    if ($add_bill[$i]['sst'] > 0) {
                        $LoanCaseInvoiceMain->transferred_sst_amt = $add_bill[$i]['sst'];
                    }
                    
                    $LoanCaseInvoiceMain->save();
                }

                $this->addLedgerEntries($TransferFeeMain, $TransferFeeDetails, $LoanCaseBillMain, $add_bill[$i]['sst'], $add_bill[$i]['value']);
 
                

                // // Create ledger entries
                // // ============================================================

                // $LedgerEntries = new LedgerEntries();

                // $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
                // $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
                // $LedgerEntries->loan_case_main_bill_id =  $TransferFeeDetails->loan_case_main_bill_id;
                // $LedgerEntries->user_id = $current_user->id;
                // $LedgerEntries->key_id = $TransferFeeDetails->id;
                // $LedgerEntries->transaction_type = 'C';
                // $LedgerEntries->amount = $LoanCaseBillMain->sst_inv;
                // $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
                // $LedgerEntries->remark = $TransferFeeMain->purpose;
                // // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
                // $LedgerEntries->status = 1;
                // $LedgerEntries->created_at = date('Y-m-d H:i:s');
                // $LedgerEntries->date = $TransferFeeMain->transfer_date;
                // $LedgerEntries->type = 'TRANSFEROUT';
                // $LedgerEntries->save();

                // if ($add_bill[$i]['sst'] > 0)
                // {
                //     $LedgerEntries = new LedgerEntries();

                //     $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
                //     $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
                //     $LedgerEntries->loan_case_main_bill_id =  $TransferFeeDetails->loan_case_main_bill_id;
                //     $LedgerEntries->user_id = $current_user->id;
                //     $LedgerEntries->key_id = $TransferFeeDetails->id;
                //     $LedgerEntries->transaction_type = 'C';
                //     $LedgerEntries->amount = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;
                //     $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
                //     $LedgerEntries->remark = $TransferFeeMain->purpose;
                //     // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
                //     $LedgerEntries->status = 1;
                //     $LedgerEntries->created_at = date('Y-m-d H:i:s');
                //     $LedgerEntries->date = $TransferFeeMain->transfer_date;
                //     $LedgerEntries->type = 'SSTOUT';
                //     $LedgerEntries->save();
                // }




                // // transfer in

                // $LedgerEntries = new LedgerEntries();

                // $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
                // $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
                // $LedgerEntries->loan_case_main_bill_id =  $TransferFeeDetails->loan_case_main_bill_id;
                // $LedgerEntries->user_id = $current_user->id;
                // $LedgerEntries->key_id = $TransferFeeDetails->id;
                // $LedgerEntries->transaction_type = 'D';
                // $LedgerEntries->amount = $LoanCaseBillMain->sst_inv;
                // $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
                // $LedgerEntries->remark = $TransferFeeMain->purpose;
                // // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
                // $LedgerEntries->status = 1;
                // $LedgerEntries->created_at = date('Y-m-d H:i:s');
                // $LedgerEntries->date = $TransferFeeMain->transfer_date;
                // $LedgerEntries->type = 'TRANSFERIN';
                // $LedgerEntries->save();


                // if ($add_bill[$i]['sst'] > 0)
                // {
                //     $LedgerEntries = new LedgerEntries();

                //     $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
                //     $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
                //     $LedgerEntries->loan_case_main_bill_id =  $TransferFeeDetails->loan_case_main_bill_id;
                //     $LedgerEntries->user_id = $current_user->id;
                //     $LedgerEntries->key_id = $TransferFeeDetails->id;
                //     $LedgerEntries->transaction_type = 'C';
                //     $LedgerEntries->amount = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;
                //     $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
                //     $LedgerEntries->remark = $TransferFeeMain->purpose;
                //     // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
                //     $LedgerEntries->status = 1;
                //     $LedgerEntries->created_at = date('Y-m-d H:i:s');
                //     $LedgerEntries->date = $TransferFeeMain->transfer_date;
                //     $LedgerEntries->type = 'SSTIN';
                //     $LedgerEntries->save();
                // }


            }

            $this->updateTransferFeeMainAmt($TransferFeeMain->id);

            // $TransferFeeMain->transfer_amount = $total_amount;
            // $TransferFeeMain->save();
        }

        return response()->json(['status' => 1, 'data' => $total_amount]);
    }

    public function updateTranferFee(Request $request, $id)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $TransferFeeMain  = TransferFeeMain::where('id', '=', $id)->first();

        $TransferFeeMain->transfer_date = $request->input("transfer_date");
        // $TransferFeeMain->transfer_by = $current_user->id;
        $TransferFeeMain->transaction_id = $request->input("trx_id");
        $TransferFeeMain->receipt_no = '';
        $TransferFeeMain->voucher_no = '';
        $TransferFeeMain->transfer_from = $request->input("transfer_from");
        $TransferFeeMain->transfer_to = $request->input("transfer_to");
        $TransferFeeMain->purpose = $request->input("purpose");
        $TransferFeeMain->status = 1;
        $TransferFeeMain->created_at = date('Y-m-d H:i:s');


        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('id', $request->input("transfer_to"))->first();

        if ($OfficeBankAccount) {
            $TransferFeeMain->branch_id = $OfficeBankAccount->branch_id;
        }


        $TransferFeeMain->save();

        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
        }

        $TransferFeeDetails  = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)->get();

        // if (count($TransferFeeDetails) > 0) {
        //     for ($i = 0; $i < count($TransferFeeDetails); $i++) {

        //         $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $TransferFeeDetails[$i]['loan_case_main_bill_id'])->first();

        //         $transfer_amount = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv + $LoanCaseBillMain->sst_inv;

        //         $total_amount += $transfer_amount;
        //         $TransferFeeDetails[$i]['transfer_amount'] = $transfer_amount;
        //         $TransferFeeDetails[$i]->save();
        //         // $total_amount += $TransferFeeDetails[$i]['transfer_amount'];
        //     }
        // }

        if (count($add_bill) > 0) {

            for ($i = 0; $i < count($add_bill); $i++) {
                $TransferFeeDetails  = new TransferFeeDetails();

                $total_amount += $add_bill[$i]['value'];

                $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                $TransferFeeDetails->loan_case_main_bill_id = $add_bill[$i]['id'];
                $TransferFeeDetails->created_by = $current_user->id;
                $TransferFeeDetails->transfer_amount = $add_bill[$i]['value'];
                $TransferFeeDetails->status = 1;
                $TransferFeeDetails->created_at = date('Y-m-d H:i:s');

                if ($add_bill[$i]['sst'] > 0) {
                    $TransferFeeDetails->sst_amount = $add_bill[$i]['sst'];
                }

                $TransferFeeDetails->save();

                // LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['transferred_to_office_bank' => 1]);
                $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->first();

                $TransferFeeDetailsSum  = TransferFeeDetails::where('loan_case_main_bill_id', '=', $add_bill[$i]['id'])->get();

                // return $TransferFeeDetailsSum;

                $SumTransgerFee = 0;

                if (count($TransferFeeDetailsSum) > 0) {
                    for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                        $SumTransgerFee += $TransferFeeDetailsSum[$j]->transfer_amount;
                    }
                }


                $LoanCaseBillMain->transferred_pfee_amt = $SumTransgerFee;
                $inv_pfee = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv;

                $sum = bcsub($inv_pfee,$SumTransgerFee,2);

                if (($sum) <= 0) {
                    $LoanCaseBillMain->transferred_pfee_amt = $SumTransgerFee;
                    $LoanCaseBillMain->transferred_to_office_bank = 1;
                }

                // if (($inv_pfee - $SumTransgerFee) <= 0) {
                //     $LoanCaseBillMain->transferred_pfee_amt = $SumTransgerFee;
                //     $LoanCaseBillMain->transferred_to_office_bank = 1;
                // }

                if ($add_bill[$i]['sst'] > 0) {
                    $LoanCaseBillMain->transferred_sst_amt = $add_bill[$i]['sst'];
                }

                $LoanCaseBillMain->save();



                $this->addLedgerEntries($TransferFeeMain, $TransferFeeDetails, $LoanCaseBillMain, $add_bill[$i]['sst'], $add_bill[$i]['value']);
            }
        }
        else
        {
            LedgerEntriesV2::where('key_id', '=', $id)
                ->whereIn('type', ['TRANSFER_IN','SST_IN'])->update([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'remark' => $TransferFeeMain->purpose,
                    'date' => $TransferFeeMain->transfer_date,
                    'bank_id' => $TransferFeeMain->transfer_to,
            ]);

            LedgerEntriesV2::where('key_id', '=', $id)
                ->whereIn('type', ['TRANSFER_OUT','SST_OUT'])->update([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'remark' => $TransferFeeMain->purpose,
                    'date' => $TransferFeeMain->transfer_date,
                    'bank_id' => $TransferFeeMain->transfer_from,
            ]);

            // $current_user = auth()->user();
            // $AccountLog = new AccountLog();
            // $AccountLog->user_id = $current_user->id;
            // $AccountLog->case_id = $TransferFeeMain->case_id;
            // $AccountLog->bill_id = 0;
            // $AccountLog->ori_amt = 0;
            // $AccountLog->new_amt = 0;
            // $AccountLog->action = 'create_journal';
            // $AccountLog->desc = $current_user->name . ' created journal(' . $JournalEntryMain->journal_no . ')' . $logNote;
            // $AccountLog->status = 1;
            // $AccountLog->object_id = $JournalEntryMain->id;
            // $AccountLog->object_id_2 = $JournalEntryDetails->id;
            // $AccountLog->created_at = date('Y-m-d H:i:s');
            // $AccountLog->save();
        }

        $this->updateTransferFeeMainAmt($id);

        // $TransferFeeMain->transfer_amount = $total_amount;
        //     $TransferFeeMain->save();

        return response()->json(['status' => 1, 'data' => 'success', 'total_amount' => $total_amount]);
    }

    public function addLedgerEntries($TransferFeeMain, $TransferFeeDetails, $LoanCaseBillMain, $sst_amount, $tranfer_amt)
    {
        // Create ledger entries
        // // ============================================================


        $current_user = auth()->user();

        $LedgerEntries = new LedgerEntries();

        // $TransferFeeDetails  = TransferFeeDetails::where('transfer_fee_main_id', '=', $TransferFeeMain->id)->first();

        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'C';
        $LedgerEntries->amount =  $tranfer_amt;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFEROUT';
        $LedgerEntries->save();


        $LedgerEntries = new LedgerEntriesV2();

        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $TransferFeeMain->id;
        $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'C';
        $LedgerEntries->amount =  $tranfer_amt;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->payee = $TransferFeeMain->payee;
        $LedgerEntries->status = 1;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFER_OUT';
        $LedgerEntries->save();

        


        if ($sst_amount > 0) {
            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SSTOUT';
            $LedgerEntries->save();


            $LedgerEntries = new LedgerEntriesV2();

            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount =  $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->payee = $TransferFeeMain->payee;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SST_OUT';
            $LedgerEntries->save();
        }




        // transfer in
        $LedgerEntries = new LedgerEntries();

        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount =  $tranfer_amt;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFERIN';
        $LedgerEntries->save();


        $LedgerEntries = new LedgerEntriesV2();

        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $TransferFeeMain->id;
        $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount =  $tranfer_amt;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->payee = $TransferFeeMain->payee;
        $LedgerEntries->status = 1;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFER_IN';
        $LedgerEntries->save();


        if ($sst_amount > 0) {
            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SSTIN';
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntriesV2();

            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id =  $LoanCaseBillMain->id;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = $TransferFeeMain->id;
            $LedgerEntries->key_id_2 = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount =  $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose;
            $LedgerEntries->payee = $TransferFeeMain->payee;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SST_IN';
            $LedgerEntries->save();
        }

        $LoanCase = LoanCase::where('id', $LoanCaseBillMain->case_id)->first();
        CaseController::adminUpdateClientLedger($LoanCase);
    }

    public function updateTransferFeeMainAmt($id)
    {
        $SumTransgerFee = 0;

        $TransferFeeMain  = TransferFeeMain::where('id', '=', $id)->first();
        $TransferFeeDetailsSum  = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();

        if (count($TransferFeeDetailsSum) > 0) {
            for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                // Include all components: transfer_amount + sst_amount + reimbursement_amount + reimbursement_sst_amount
                $SumTransgerFee += ($TransferFeeDetailsSum[$j]->transfer_amount ?? 0);
                $SumTransgerFee += ($TransferFeeDetailsSum[$j]->sst_amount ?? 0);
                $SumTransgerFee += ($TransferFeeDetailsSum[$j]->reimbursement_amount ?? 0);
                $SumTransgerFee += ($TransferFeeDetailsSum[$j]->reimbursement_sst_amount ?? 0);
            }
        }

        $TransferFeeMain->transfer_amount = round($SumTransgerFee, 2);
        $TransferFeeMain->save();
    }

    /**
     * Verify transfer fee totals from both sources
     */
    public function verifyTransferFeeTotals($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get totals from transfer_fee_details
        $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();
        
        $detailsTotal = 0;
        $detailsBreakdown = [
            'transfer_amount' => 0,
            'sst_amount' => 0,
            'reimbursement_amount' => 0,
            'reimbursement_sst_amount' => 0
        ];
        
        foreach ($transferFeeDetails as $detail) {
            $detailsBreakdown['transfer_amount'] += ($detail->transfer_amount ?? 0);
            $detailsBreakdown['sst_amount'] += ($detail->sst_amount ?? 0);
            $detailsBreakdown['reimbursement_amount'] += ($detail->reimbursement_amount ?? 0);
            $detailsBreakdown['reimbursement_sst_amount'] += ($detail->reimbursement_sst_amount ?? 0);
            $detailsTotal += ($detail->transfer_amount ?? 0) + ($detail->sst_amount ?? 0) + ($detail->reimbursement_amount ?? 0) + ($detail->reimbursement_sst_amount ?? 0);
        }
        
        // Get totals from ledger entries
        $ledgerEntries = DB::table('ledger_entries_v2')
            ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
            ->where('status', '<>', 99)
            ->whereIn('type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
            ->select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();
        
        $ledgerTotal = 0;
        $ledgerBreakdown = [
            'TRANSFER_IN' => 0,
            'SST_IN' => 0,
            'REIMB_IN' => 0,
            'REIMB_SST_IN' => 0
        ];
        
        foreach ($ledgerEntries as $entry) {
            $ledgerTotal += $entry->total;
            $ledgerBreakdown[$entry->type] = [
                'total' => round($entry->total, 2),
                'count' => $entry->count
            ];
        }
        
        // Calculate what "Transferred Bal" and "Transferred SST" should be
        $transferredBal = $detailsBreakdown['transfer_amount'] + $detailsBreakdown['reimbursement_amount'];
        $transferredSst = $detailsBreakdown['sst_amount'] + $detailsBreakdown['reimbursement_sst_amount'];
        
        return response()->json([
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'transfer_fee_main_transfer_amount' => round($TransferFeeMain->transfer_amount ?? 0, 2),
            'details_total' => round($detailsTotal, 2),
            'ledger_total' => round($ledgerTotal, 2),
            'difference' => round($ledgerTotal - $detailsTotal, 2),
            'details_breakdown' => [
                'transfer_amount' => round($detailsBreakdown['transfer_amount'], 2),
                'sst_amount' => round($detailsBreakdown['sst_amount'], 2),
                'reimbursement_amount' => round($detailsBreakdown['reimbursement_amount'], 2),
                'reimbursement_sst_amount' => round($detailsBreakdown['reimbursement_sst_amount'], 2),
                'transferred_bal' => round($transferredBal, 2),
                'transferred_sst' => round($transferredSst, 2),
                'transferred_bal_plus_sst' => round($transferredBal + $transferredSst, 2)
            ],
            'ledger_breakdown' => $ledgerBreakdown,
            'details_count' => $transferFeeDetails->count(),
            'ledger_entry_count' => $ledgerEntries->sum('count')
        ]);
    }

    /**
     * Find discrepancies between transfer_fee_details and ledger entries
     * Handles split invoices correctly by checking if amounts should be split
     */
    public function findTransferFeeDiscrepancies($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get all transfer fee details
        $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();
        
        $discrepancies = [];
        
        foreach ($transferFeeDetails as $detail) {
            // Get invoice info to check if it's a split invoice
            $invoice = DB::table('loan_case_invoice_main')
                ->where('id', '=', $detail->loan_case_invoice_main_id)
                ->first();
            
            if (!$invoice) {
                continue;
            }
            
            // Check if this is a split invoice (multiple invoices for same bill)
            $billId = $invoice->loan_case_main_bill_id ?? null;
            $splitInvoiceCount = 0;
            $splitInvoiceIds = [];
            
            if ($billId) {
                $splitInvoices = DB::table('loan_case_invoice_main')
                    ->where('loan_case_main_bill_id', '=', $billId)
                    ->where('status', '<>', 99)
                    ->pluck('id')
                    ->toArray();
                $splitInvoiceCount = count($splitInvoices);
                $splitInvoiceIds = $splitInvoices;
            }
            
            // Get ledger entries for this detail record
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->where('key_id_2', '=', $detail->id)
                ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
                ->where('status', '<>', 99)
                ->get();
            
            $ledgerPfee = 0;
            $ledgerSst = 0;
            $ledgerReimb = 0;
            $ledgerReimbSst = 0;
            
            // Track entries to detect duplicates
            $entryGroups = [
                'TRANSFER_IN' => [],
                'SST_IN' => [],
                'REIMB_IN' => [],
                'REIMB_SST_IN' => []
            ];
            
            foreach ($ledgerEntries as $entry) {
                if ($entry->type == 'TRANSFER_IN') {
                    $ledgerPfee += $entry->amount;
                    $entryGroups['TRANSFER_IN'][] = ['id' => $entry->id, 'amount' => $entry->amount];
                } elseif ($entry->type == 'SST_IN') {
                    $ledgerSst += $entry->amount;
                    $entryGroups['SST_IN'][] = ['id' => $entry->id, 'amount' => $entry->amount];
                } elseif ($entry->type == 'REIMB_IN') {
                    $ledgerReimb += $entry->amount;
                    $entryGroups['REIMB_IN'][] = ['id' => $entry->id, 'amount' => $entry->amount];
                } elseif ($entry->type == 'REIMB_SST_IN') {
                    $ledgerReimbSst += $entry->amount;
                    $entryGroups['REIMB_SST_IN'][] = ['id' => $entry->id, 'amount' => $entry->amount];
                }
            }
            
            // Check for duplicates
            $hasDuplicates = false;
            $duplicateInfo = [];
            foreach ($entryGroups as $type => $entries) {
                if (count($entries) > 1) {
                    // Check if amounts are the same (duplicates)
                    $amounts = array_column($entries, 'amount');
                    $uniqueAmounts = array_unique($amounts);
                    if (count($uniqueAmounts) == 1 && count($amounts) > 1) {
                        $hasDuplicates = true;
                        $duplicateInfo[] = "{$type}: {$amounts[0]} appears " . count($amounts) . " times (entry IDs: " . implode(', ', array_column($entries, 'id')) . ")";
                    }
                }
            }
            
            $detailPfee = $detail->transfer_amount ?? 0;
            $detailSst = $detail->sst_amount ?? 0;
            $detailReimb = $detail->reimbursement_amount ?? 0;
            $detailReimbSst = $detail->reimbursement_sst_amount ?? 0;
            
            // For split invoices, check if ledger has full amount but detail has half
            $isSplitInvoice = $splitInvoiceCount > 1;
            $splitNote = '';
            
            if ($isSplitInvoice && ($ledgerReimb > $detailReimb || $ledgerReimbSst > $detailReimbSst)) {
                // Check if ledger has full amount (sum of all split invoices)
                $totalDetailReimb = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
                    ->whereIn('loan_case_invoice_main_id', $splitInvoiceIds)
                    ->where('status', '<>', 99)
                    ->sum('reimbursement_amount');
                
                $totalDetailReimbSst = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
                    ->whereIn('loan_case_invoice_main_id', $splitInvoiceIds)
                    ->where('status', '<>', 99)
                    ->sum('reimbursement_sst_amount');
                
                // If ledger has full amount but detail has half, this is a split invoice issue
                // The ledger entries are wrong - they should be split between invoices
                if (abs($ledgerReimb - $totalDetailReimb) < 0.01 && abs($ledgerReimb - $detailReimb) > 0.01) {
                    $splitNote = " ⚠️ Split invoice: Ledger has FULL amount ({$ledgerReimb}) but this invoice should have HALF ({$detailReimb}). The transfer_fee_details is CORRECT - ledger entries need to be split between split invoices.";
                    // Still flag as discrepancy but note that transfer_fee_details is correct
                }
                if (abs($ledgerReimbSst - $totalDetailReimbSst) < 0.01 && abs($ledgerReimbSst - $detailReimbSst) > 0.01) {
                    $splitNote .= " Same for SST: Ledger has {$ledgerReimbSst} but should be {$detailReimbSst}.";
                }
            }
            
            $hasDiscrepancy = false;
            $discrepancy = [
                'transfer_fee_detail_id' => $detail->id,
                'invoice_id' => $detail->loan_case_invoice_main_id,
                'invoice_no' => $invoice->invoice_no ?? 'N/A',
                'is_split_invoice' => $isSplitInvoice,
                'split_invoice_count' => $splitInvoiceCount,
                'has_duplicate_entries' => $hasDuplicates,
                'duplicate_info' => $duplicateInfo,
                'pfee_match' => abs($ledgerPfee - $detailPfee) < 0.01,
                'sst_match' => abs($ledgerSst - $detailSst) < 0.01,
                'reimb_match' => abs($ledgerReimb - $detailReimb) < 0.01,
                'reimb_sst_match' => abs($ledgerReimbSst - $detailReimbSst) < 0.01,
                'pfee_detail' => round($detailPfee, 2),
                'pfee_ledger' => round($ledgerPfee, 2),
                'pfee_diff' => round($ledgerPfee - $detailPfee, 2),
                'sst_detail' => round($detailSst, 2),
                'sst_ledger' => round($ledgerSst, 2),
                'sst_diff' => round($ledgerSst - $detailSst, 2),
                'reimb_detail' => round($detailReimb, 2),
                'reimb_ledger' => round($ledgerReimb, 2),
                'reimb_diff' => round($ledgerReimb - $detailReimb, 2),
                'reimb_sst_detail' => round($detailReimbSst, 2),
                'reimb_sst_ledger' => round($ledgerReimbSst, 2),
                'reimb_sst_diff' => round($ledgerReimbSst - $detailReimbSst, 2),
                'split_note' => $splitNote,
            ];
            
            // Only flag as discrepancy if there's an actual amount difference OR duplicate entries
            // Don't flag if all amounts match but there's just a split note
            if (!$discrepancy['pfee_match'] || !$discrepancy['sst_match'] || !$discrepancy['reimb_match'] || !$discrepancy['reimb_sst_match'] || $hasDuplicates) {
                $hasDiscrepancy = true;
            }
            
            // Only add to discrepancies if there's a real issue (amount mismatch or duplicates)
            // Skip if only split note exists but amounts match
            if ($hasDiscrepancy) {
                // Check if there's any actual difference
                $hasActualDifference = abs($discrepancy['pfee_diff']) > 0.01 || 
                                       abs($discrepancy['sst_diff']) > 0.01 || 
                                       abs($discrepancy['reimb_diff']) > 0.01 || 
                                       abs($discrepancy['reimb_sst_diff']) > 0.01 ||
                                       $hasDuplicates;
                
                if ($hasActualDifference) {
                    $discrepancies[] = $discrepancy;
                }
            }
        }
        
        return response()->json([
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'total_discrepancies' => count($discrepancies),
            'discrepancies' => $discrepancies
        ]);
    }

    /**
     * Remove duplicate ledger entries for a transfer fee
     * This fixes discrepancies caused by duplicate ledger entries
     */
    public function removeDuplicateLedgerEntries($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get all transfer fee details
        $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();
        
        $removedCount = 0;
        $removedEntries = [];
        
        foreach ($transferFeeDetails as $detail) {
            // Get all ledger entries for this detail record
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->where('key_id_2', '=', $detail->id)
                ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
                ->where('status', '<>', 99)
                ->orderBy('id', 'asc')
                ->get();
            
            // Group by type and amount to find duplicates
            $entryGroups = [];
            foreach ($ledgerEntries as $entry) {
                $key = $entry->type . '_' . $entry->amount;
                if (!isset($entryGroups[$key])) {
                    $entryGroups[$key] = [];
                }
                $entryGroups[$key][] = $entry;
            }
            
            // For each group, keep the first entry and delete duplicates
            foreach ($entryGroups as $key => $entries) {
                if (count($entries) > 1) {
                    // Keep the first entry (oldest ID), delete the rest
                    $firstEntry = array_shift($entries);
                    foreach ($entries as $duplicate) {
                        DB::table('ledger_entries_v2')
                            ->where('id', '=', $duplicate->id)
                            ->delete();
                        $removedCount++;
                        $removedEntries[] = [
                            'entry_id' => $duplicate->id,
                            'type' => $duplicate->type,
                            'amount' => $duplicate->amount,
                            'transfer_fee_detail_id' => $detail->id
                        ];
                    }
                }
            }
        }
        
        // Update transfer_fee_main amount after removing duplicates
        $this->updateTransferFeeMainAmt($id);
        
        // Get updated total
        $TransferFeeMain->refresh();
        
        return response()->json([
            'success' => true,
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'removed_duplicates_count' => $removedCount,
            'updated_transfer_amount' => round($TransferFeeMain->transfer_amount, 2),
            'removed_entries' => $removedEntries
        ]);
    }

    /**
     * Create missing ledger entries for transfer fee details
     * This creates missing SST_IN, TRANSFER_IN, REIMB_IN, or REIMB_SST_IN entries
     */
    public function createMissingLedgerEntries($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get all transfer fee details
        $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();
        
        $createdCount = 0;
        $createdEntries = [];
        
        foreach ($transferFeeDetails as $detail) {
            // Get invoice and bill info
            $invoice = DB::table('loan_case_invoice_main')
                ->where('id', '=', $detail->loan_case_invoice_main_id)
                ->first();
            
            if (!$invoice) {
                continue;
            }
            
            $bill = DB::table('loan_case_bill_main')
                ->where('id', '=', $invoice->loan_case_main_bill_id ?? $detail->loan_case_main_bill_id)
                ->first();
            
            if (!$bill) {
                continue;
            }
            
            // Get existing ledger entries for this detail
            $existingEntries = DB::table('ledger_entries_v2')
                ->where('key_id_2', '=', $detail->id)
                ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
                ->where('status', '<>', 99)
                ->get();
            
            $existingTypes = [];
            foreach ($existingEntries as $entry) {
                $existingTypes[$entry->type] = ($existingTypes[$entry->type] ?? 0) + $entry->amount;
            }
            
            // Check what's missing and create entries
            $detailPfee = $detail->transfer_amount ?? 0;
            $detailSst = $detail->sst_amount ?? 0;
            $detailReimb = $detail->reimbursement_amount ?? 0;
            $detailReimbSst = $detail->reimbursement_sst_amount ?? 0;
            
            // Create TRANSFER_IN if missing
            if ($detailPfee > 0.01 && ($existingTypes['TRANSFER_IN'] ?? 0) < $detailPfee - 0.01) {
                $missingAmount = $detailPfee - ($existingTypes['TRANSFER_IN'] ?? 0);
                
                // TRANSFER_OUT
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'TRANSFER_OUT'
                ]);
                
                // TRANSFER_IN
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'TRANSFER_IN'
                ]);
                
                $createdCount += 2;
                $createdEntries[] = ['type' => 'TRANSFER_IN', 'amount' => $missingAmount, 'invoice_no' => $invoice->invoice_no ?? 'N/A'];
            }
            
            // Create SST_IN if missing
            if ($detailSst > 0.01 && ($existingTypes['SST_IN'] ?? 0) < $detailSst - 0.01) {
                $missingAmount = $detailSst - ($existingTypes['SST_IN'] ?? 0);
                
                // SST_OUT
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'SST_OUT'
                ]);
                
                // SST_IN
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'SST_IN'
                ]);
                
                $createdCount += 2;
                $createdEntries[] = ['type' => 'SST_IN', 'amount' => $missingAmount, 'invoice_no' => $invoice->invoice_no ?? 'N/A'];
            }
            
            // Create REIMB_IN if missing
            if ($detailReimb > 0.01 && ($existingTypes['REIMB_IN'] ?? 0) < $detailReimb - 0.01) {
                $missingAmount = $detailReimb - ($existingTypes['REIMB_IN'] ?? 0);
                
                // REIMB_OUT
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_OUT'
                ]);
                
                // REIMB_IN
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_IN'
                ]);
                
                $createdCount += 2;
                $createdEntries[] = ['type' => 'REIMB_IN', 'amount' => $missingAmount, 'invoice_no' => $invoice->invoice_no ?? 'N/A'];
            }
            
            // Create REIMB_SST_IN if missing
            if ($detailReimbSst > 0.01 && ($existingTypes['REIMB_SST_IN'] ?? 0) < $detailReimbSst - 0.01) {
                $missingAmount = $detailReimbSst - ($existingTypes['REIMB_SST_IN'] ?? 0);
                
                // REIMB_SST_OUT
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_SST_OUT'
                ]);
                
                // REIMB_SST_IN
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_SST_IN'
                ]);
                
                $createdCount += 2;
                $createdEntries[] = ['type' => 'REIMB_SST_IN', 'amount' => $missingAmount, 'invoice_no' => $invoice->invoice_no ?? 'N/A'];
            }
        }
        
        // Update transfer_fee_main amount after creating entries
        $this->updateTransferFeeMainAmt($id);
        
        // Get updated total
        $TransferFeeMain->refresh();
        
        return response()->json([
            'success' => true,
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'created_entries_count' => $createdCount,
            'updated_transfer_amount' => round($TransferFeeMain->transfer_amount, 2),
            'created_entries' => $createdEntries
        ]);
    }

    /**
     * Intelligently fix all transfer fee discrepancies
     * This method automatically detects and fixes:
     * 1. Missing ledger entries → creates them
     * 2. Duplicate ledger entries → removes duplicates
     * 3. Amount mismatches → fixes transfer_fee_details to match ledger
     */
    public function fixAllTransferFeeDiscrepancies($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get all transfer fee details
        $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();
        
        $actions = [
            'duplicates_removed' => 0,
            'missing_entries_created' => 0,
            'amounts_fixed' => 0,
            'details' => []
        ];
        
        foreach ($transferFeeDetails as $detail) {
            // Get invoice and bill info
            $invoice = DB::table('loan_case_invoice_main')
                ->where('id', '=', $detail->loan_case_invoice_main_id)
                ->first();
            
            if (!$invoice) {
                continue;
            }
            
            $bill = DB::table('loan_case_bill_main')
                ->where('id', '=', $invoice->loan_case_main_bill_id ?? $detail->loan_case_main_bill_id)
                ->first();
            
            if (!$bill) {
                continue;
            }
            
            // Get existing ledger entries
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->where('key_id_2', '=', $detail->id)
                ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
                ->where('status', '<>', 99)
                ->orderBy('id', 'asc')
                ->get();
            
            // Step 1: Remove duplicates (keep first entry, delete rest)
            $entryGroups = [];
            foreach ($ledgerEntries as $entry) {
                $key = $entry->type . '_' . $entry->amount;
                if (!isset($entryGroups[$key])) {
                    $entryGroups[$key] = [];
                }
                $entryGroups[$key][] = $entry;
            }
            
            foreach ($entryGroups as $key => $entries) {
                if (count($entries) > 1) {
                    $firstEntry = array_shift($entries);
                    foreach ($entries as $duplicate) {
                        DB::table('ledger_entries_v2')->where('id', '=', $duplicate->id)->delete();
                        $actions['duplicates_removed']++;
                    }
                }
            }
            
            // Re-fetch ledger entries after removing duplicates
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->where('key_id_2', '=', $detail->id)
                ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
                ->where('status', '<>', 99)
                ->get();
            
            // Calculate ledger totals
            $ledgerPfee = 0;
            $ledgerSst = 0;
            $ledgerReimb = 0;
            $ledgerReimbSst = 0;
            $existingTypes = [];
            
            foreach ($ledgerEntries as $entry) {
                if ($entry->type == 'TRANSFER_IN') {
                    $ledgerPfee += $entry->amount;
                } elseif ($entry->type == 'SST_IN') {
                    $ledgerSst += $entry->amount;
                } elseif ($entry->type == 'REIMB_IN') {
                    $ledgerReimb += $entry->amount;
                } elseif ($entry->type == 'REIMB_SST_IN') {
                    $ledgerReimbSst += $entry->amount;
                }
                $existingTypes[$entry->type] = ($existingTypes[$entry->type] ?? 0) + $entry->amount;
            }
            
            // Get detail amounts
            $detailPfee = $detail->transfer_amount ?? 0;
            $detailSst = $detail->sst_amount ?? 0;
            $detailReimb = $detail->reimbursement_amount ?? 0;
            $detailReimbSst = $detail->reimbursement_sst_amount ?? 0;
            
            $invoiceNo = $invoice->invoice_no ?? 'N/A';
            $detailActions = [];
            
            // Step 2: Create missing ledger entries (if detail has amount but ledger is 0 or missing)
            // Only create if ledger is essentially 0, not if it's just a different amount (that's a mismatch handled in Step 3)
            if ($detailPfee > 0.01 && ($existingTypes['TRANSFER_IN'] ?? 0) < 0.01) {
                $missingAmount = $detailPfee;
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'TRANSFER_OUT'
                ]);
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'TRANSFER_IN'
                ]);
                
                $actions['missing_entries_created'] += 2;
                $detailActions[] = "Created TRANSFER_IN for {$missingAmount}";
                $ledgerPfee = $detailPfee; // Update ledger total
            }
            
            if ($detailSst > 0.01 && ($existingTypes['SST_IN'] ?? 0) < 0.01) {
                $missingAmount = $detailSst;
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'SST_OUT'
                ]);
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'SST_IN'
                ]);
                
                $actions['missing_entries_created'] += 2;
                $detailActions[] = "Created SST_IN for {$missingAmount}";
                $ledgerSst = $detailSst; // Update ledger total
            }
            
            if ($detailReimb > 0.01 && ($existingTypes['REIMB_IN'] ?? 0) < 0.01) {
                $missingAmount = $detailReimb;
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_OUT'
                ]);
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_IN'
                ]);
                
                $actions['missing_entries_created'] += 2;
                $detailActions[] = "Created REIMB_IN for {$missingAmount}";
                $ledgerReimb = $detailReimb; // Update ledger total
            }
            
            if ($detailReimbSst > 0.01 && ($existingTypes['REIMB_SST_IN'] ?? 0) < 0.01) {
                $missingAmount = $detailReimbSst;
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'C',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_from,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_SST_OUT'
                ]);
                
                DB::table('ledger_entries_v2')->insert([
                    'transaction_id' => $TransferFeeMain->transaction_id,
                    'case_id' => $bill->case_id,
                    'loan_case_main_bill_id' => $bill->id,
                    'loan_case_invoice_main_id' => $invoice->id,
                    'user_id' => $TransferFeeMain->transfer_by ?? auth()->id(),
                    'key_id' => $TransferFeeMain->id,
                    'key_id_2' => $detail->id,
                    'transaction_type' => 'D',
                    'amount' => $missingAmount,
                    'bank_id' => $TransferFeeMain->transfer_to,
                    'remark' => $TransferFeeMain->purpose ?? '',
                    'status' => 1,
                    'is_recon' => 0,
                    'created_at' => $TransferFeeMain->transfer_date ?? now(),
                    'date' => $TransferFeeMain->transfer_date ?? now(),
                    'type' => 'REIMB_SST_IN'
                ]);
                
                $actions['missing_entries_created'] += 2;
                $detailActions[] = "Created REIMB_SST_IN for {$missingAmount}";
                $ledgerReimbSst = $detailReimbSst; // Update ledger total
            }
            
            // Step 3: Fix amount mismatches (if ledger has amount but detail doesn't match)
            $needsUpdate = false;
            if (abs($ledgerPfee - $detailPfee) > 0.01 && $ledgerPfee > 0.01) {
                $detail->transfer_amount = round($ledgerPfee, 2);
                $needsUpdate = true;
                $detailActions[] = "Fixed Pfee: {$detailPfee} → {$ledgerPfee}";
            }
            
            if (abs($ledgerSst - $detailSst) > 0.01 && $ledgerSst > 0.01) {
                $detail->sst_amount = round($ledgerSst, 2);
                $needsUpdate = true;
                $detailActions[] = "Fixed SST: {$detailSst} → {$ledgerSst}";
            }
            
            if (abs($ledgerReimb - $detailReimb) > 0.01 && $ledgerReimb > 0.01) {
                $detail->reimbursement_amount = round($ledgerReimb, 2);
                $needsUpdate = true;
                $detailActions[] = "Fixed Reimb: {$detailReimb} → {$ledgerReimb}";
            }
            
            if (abs($ledgerReimbSst - $detailReimbSst) > 0.01 && $ledgerReimbSst > 0.01) {
                $detail->reimbursement_sst_amount = round($ledgerReimbSst, 2);
                $needsUpdate = true;
                $detailActions[] = "Fixed Reimb SST: {$detailReimbSst} → {$ledgerReimbSst}";
            }
            
            if ($needsUpdate) {
                $detail->save();
                $actions['amounts_fixed']++;
            }
            
            if (count($detailActions) > 0) {
                $actions['details'][] = [
                    'invoice_no' => $invoiceNo,
                    'actions' => $detailActions
                ];
            }
        }
        
        // Update transfer_fee_main amount
        $this->updateTransferFeeMainAmt($id);
        $TransferFeeMain->refresh();
        
        return response()->json([
            'success' => true,
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'actions' => $actions,
            'updated_transfer_amount' => round($TransferFeeMain->transfer_amount, 2)
        ]);
    }

    /**
     * Fix transfer fee discrepancies by updating transfer_fee_details to match ledger entries
     * NOTE: This should NOT be used if discrepancies are caused by duplicate ledger entries or missing entries
     * Use removeDuplicateLedgerEntries() for duplicates or createMissingLedgerEntries() for missing entries
     * @deprecated Use fixAllTransferFeeDiscrepancies() instead
     */
    public function fixTransferFeeDiscrepancies($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get all transfer fee details
        $transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->get();
        
        $fixedCount = 0;
        $fixedDetails = [];
        $totalFixedReimb = 0;
        $totalFixedReimbSst = 0;
        
        foreach ($transferFeeDetails as $detail) {
            // Get ledger entries for this detail record
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->where('key_id_2', '=', $detail->id)
                ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
                ->where('status', '<>', 99)
                ->get();
            
            $ledgerPfee = 0;
            $ledgerSst = 0;
            $ledgerReimb = 0;
            $ledgerReimbSst = 0;
            
            foreach ($ledgerEntries as $entry) {
                if ($entry->type == 'TRANSFER_IN') {
                    $ledgerPfee += $entry->amount;
                } elseif ($entry->type == 'SST_IN') {
                    $ledgerSst += $entry->amount;
                } elseif ($entry->type == 'REIMB_IN') {
                    $ledgerReimb += $entry->amount;
                } elseif ($entry->type == 'REIMB_SST_IN') {
                    $ledgerReimbSst += $entry->amount;
                }
            }
            
            $detailPfee = $detail->transfer_amount ?? 0;
            $detailSst = $detail->sst_amount ?? 0;
            $detailReimb = $detail->reimbursement_amount ?? 0;
            $detailReimbSst = $detail->reimbursement_sst_amount ?? 0;
            
            $needsUpdate = false;
            $updateInfo = [
                'transfer_fee_detail_id' => $detail->id,
                'invoice_id' => $detail->loan_case_invoice_main_id,
            ];
            
            // Check and fix pfee amounts
            if (abs($ledgerPfee - $detailPfee) > 0.01) {
                $oldPfee = $detailPfee;
                $detail->transfer_amount = round($ledgerPfee, 2);
                $needsUpdate = true;
                $updateInfo['pfee'] = [
                    'old' => round($oldPfee, 2),
                    'new' => round($ledgerPfee, 2),
                    'diff' => round($ledgerPfee - $oldPfee, 2)
                ];
            }
            
            // Check and fix SST amounts
            if (abs($ledgerSst - $detailSst) > 0.01) {
                $oldSst = $detailSst;
                $detail->sst_amount = round($ledgerSst, 2);
                $needsUpdate = true;
                $updateInfo['sst'] = [
                    'old' => round($oldSst, 2),
                    'new' => round($ledgerSst, 2),
                    'diff' => round($ledgerSst - $oldSst, 2)
                ];
            }
            
            // Check and fix reimbursement amounts
            if (abs($ledgerReimb - $detailReimb) > 0.01) {
                $oldReimb = $detailReimb;
                $detail->reimbursement_amount = round($ledgerReimb, 2);
                $needsUpdate = true;
                $updateInfo['reimbursement'] = [
                    'old' => round($oldReimb, 2),
                    'new' => round($ledgerReimb, 2),
                    'diff' => round($ledgerReimb - $oldReimb, 2)
                ];
                $totalFixedReimb += ($ledgerReimb - $oldReimb);
            }
            
            if (abs($ledgerReimbSst - $detailReimbSst) > 0.01) {
                $oldReimbSst = $detailReimbSst;
                $detail->reimbursement_sst_amount = round($ledgerReimbSst, 2);
                $needsUpdate = true;
                $updateInfo['reimbursement_sst'] = [
                    'old' => round($oldReimbSst, 2),
                    'new' => round($ledgerReimbSst, 2),
                    'diff' => round($ledgerReimbSst - $oldReimbSst, 2)
                ];
                $totalFixedReimbSst += ($ledgerReimbSst - $oldReimbSst);
            }
            
            if ($needsUpdate) {
                $detail->save();
                $fixedCount++;
                
                // Get invoice number
                $invoice = DB::table('loan_case_invoice_main')
                    ->where('id', '=', $detail->loan_case_invoice_main_id)
                    ->first();
                $updateInfo['invoice_no'] = $invoice->invoice_no ?? 'N/A';
                
                $fixedDetails[] = $updateInfo;
            }
        }
        
        // Update transfer_fee_main amount after fixing details
        $this->updateTransferFeeMainAmt($id);
        
        // Get updated total
        $TransferFeeMain->refresh();
        
        return response()->json([
            'success' => true,
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'fixed_details_count' => $fixedCount,
            'total_fixed_reimbursement' => round($totalFixedReimb, 2),
            'total_fixed_reimbursement_sst' => round($totalFixedReimbSst, 2),
            'updated_transfer_amount' => round($TransferFeeMain->transfer_amount, 2),
            'fixed_details' => $fixedDetails
        ]);
    }

    /**
     * Recalculate and update transfer_fee_main amount from transfer_fee_details
     * This recalculates the total from the details table (source of truth)
     */
    public function recalculateTransferFeeTotal($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Recalculate from transfer_fee_details (source of truth)
        $total = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
            ->where('status', '<>', 99)
            ->selectRaw('SUM(COALESCE(transfer_amount, 0) + COALESCE(sst_amount, 0) + COALESCE(reimbursement_amount, 0) + COALESCE(reimbursement_sst_amount, 0)) as total')
            ->value('total');
        
        $oldAmount = $TransferFeeMain->transfer_amount;
        $newAmount = round($total ?? 0, 2);
        
        $TransferFeeMain->transfer_amount = $newAmount;
        $TransferFeeMain->save();
        
        return response()->json([
            'success' => true,
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'old_amount' => round($oldAmount, 2),
            'new_amount' => $newAmount,
            'difference' => round($newAmount - $oldAmount, 2),
            'calculated_from' => 'transfer_fee_details'
        ]);
    }

    /**
     * Update transfer_fee_main amount from ledger entries (for fixing discrepancies)
     * This ensures the total matches what's in the ledger_entries_v2 table
     */
    public function updateTransferFeeMainFromLedger($id)
    {
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();
        
        if (!$TransferFeeMain) {
            return response()->json(['error' => 'Transfer fee not found'], 404);
        }
        
        // Get total from ledger entries
        $ledgerTotal = DB::table('ledger_entries_v2')
            ->where('transaction_id', '=', $TransferFeeMain->transaction_id)
            ->where('status', '<>', 99)
            ->whereIn('type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
            ->sum('amount');
        
        $oldAmount = $TransferFeeMain->transfer_amount;
        $TransferFeeMain->transfer_amount = round($ledgerTotal, 2);
        $TransferFeeMain->save();
        
        return response()->json([
            'success' => true,
            'transfer_fee_main_id' => $id,
            'transaction_id' => $TransferFeeMain->transaction_id,
            'old_amount' => round($oldAmount, 2),
            'new_amount' => round($ledgerTotal, 2),
            'difference' => round($ledgerTotal - $oldAmount, 2)
        ]);
    }

    public function reconTransferFee($id)
    {
        $TransferFeeDetails  = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)->get();

        $end = new Carbon('last day of last month');


        if (count($TransferFeeDetails) > 0) {
            for ($j = 0; $j < count($TransferFeeDetails); $j++) {
                LedgerEntries::where('key_id', $TransferFeeDetails[$j]->id)->whereIn('type', ['TRANSFERIN'])->update(['type' => 'TRANSFERINRECON']);
                LedgerEntries::where('key_id', $TransferFeeDetails[$j]->id)->whereIn('type', ['TRANSFEROUT'])->update(['type' => 'TRANSFEROUTRECON']);
                LedgerEntries::where('key_id', $TransferFeeDetails[$j]->id)->whereIn('type', ['SSTIN'])->update(['type' => 'SSTINRECON']);
                LedgerEntries::where('key_id', $TransferFeeDetails[$j]->id)->whereIn('type', ['SSTOUT'])->update(['type' => 'SSTOUTRECON']);

                LedgerEntriesV2::where('key_id_2', $TransferFeeDetails[$j]->id)->whereIn('type', ['TRANSFER_IN'])->update(['is_recon' => 1, 'recon_date' => $end]);
                LedgerEntriesV2::where('key_id_2', $TransferFeeDetails[$j]->id)->whereIn('type', ['TRANSFER_OUT'])->update(['is_recon' => 1, 'recon_date' => $end]);
                LedgerEntriesV2::where('key_id_2', $TransferFeeDetails[$j]->id)->whereIn('type', ['SST_IN'])->update(['is_recon' => 1, 'recon_date' => $end]);
                LedgerEntriesV2::where('key_id_2', $TransferFeeDetails[$j]->id)->whereIn('type', ['SST_OUT'])->update(['is_recon' => 1, 'recon_date' => $end]);
            }
        }

        

        TransferFeeMain::where('id', $id)->update(['is_recon' => 1]);
        TransferFeeDetails::where('transfer_fee_main_id', $id)->update(['is_recon' => 1]);

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    public function deleteTransferFee(Request $request, $id)
    {
        $total_amount = 0;
        $current_user = auth()->user();

        if ($request->input('delete_bill') != null) {
            $delete_bill = json_decode($request->input('delete_bill'), true);
        }




        if (count($delete_bill) > 0) {
            for ($i = 0; $i < count($delete_bill); $i++) {

                $TransferFeeDetails  = TransferFeeDetails::where('id', '=', $delete_bill[$i]['id'])->first();


                if ($TransferFeeDetails) {
                    $TransferFeeDetailsDelete  = new TransferFeeDetailsDelete();

                    $TransferFeeDetailsDelete->transfer_fee_main_id = $TransferFeeDetails->transfer_fee_main_id;
                    $TransferFeeDetailsDelete->loan_case_main_bill_id = $TransferFeeDetails->loan_case_main_bill_id;
                    $TransferFeeDetailsDelete->created_by = $current_user->id;
                    $TransferFeeDetailsDelete->transfer_amount = $TransferFeeDetails->transfer_amount;
                    $TransferFeeDetailsDelete->status = 1;
                    $TransferFeeDetailsDelete->created_at = date('Y-m-d H:i:s');

                    $TransferFeeDetailsDelete->save();

                    $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $TransferFeeDetails->loan_case_main_bill_id)->first();

                    if ($LoanCaseBillMain) {
                        if ($LoanCaseBillMain->transferred_pfee_amt > 0) {
                            $LoanCaseBillMain->transferred_pfee_amt -= $TransferFeeDetails->transfer_amount;
                            $LoanCaseBillMain->transferred_sst_amt -= $TransferFeeDetails->sst_amount;
                        }

                        //delete transfer in/out from ledger
                        $LedgerEntries  = LedgerEntries::where('key_id', '=', $TransferFeeDetails->id)
                            ->whereIn('type', ['TRANSFERIN', 'TRANSFEROUT', 'TRANSFERINRECON', 'TRANSFEROUTRECON'])->get();

                        LedgerEntriesV2::where('key_id', '=', $TransferFeeDetails->transfer_fee_main_id)->where('key_id_2', '=', $TransferFeeDetails->id)
                            ->whereIn('type', ['TRANSFER_IN', 'TRANSFER_OUT'])->delete();

                        if (count($LedgerEntries) > 0) {
                            for ($j = 0; $j < count($LedgerEntries); $j++) {
                                $LedgerEntries[$j]->delete();
                            }
                        }

                        // delete sst from ledger if transfer under this record
                        if ($TransferFeeDetails->sst_amount > 0) {
                            $LedgerEntries  = LedgerEntries::where('key_id', '=', $TransferFeeDetails->id)
                                ->whereIn('type', ['SSTIN', 'SSTOUT', 'SSTINRECON', 'SSTOUTRECON'])->get();

                            if (count($LedgerEntries) > 0) {
                                for ($j = 0; $j < count($LedgerEntries); $j++) {
                                    $LedgerEntries[$j]->delete();
                                }
                            }

                            LedgerEntriesV2::where('key_id', '=', $TransferFeeDetails->transfer_fee_main_id)->where('key_id_2', '=', $TransferFeeDetails->id)
                                ->whereIn('type', ['SST_IN', 'SST_OUT'])->delete();
                        }

                        $pf1 = number_format((float)$LoanCaseBillMain->pfee1_inv, 2, '.', '');
                        $pf2 = number_format((float)$LoanCaseBillMain->pfee2_inv, 2, '.', '');
                        $pftf = number_format((float)$LoanCaseBillMain->transferred_pfee_amt, 2, '.', '');
            
                        // $pf1 = round((float)$LoanCaseBillMain[$j]->pfee1_inv, 2);
                        // $pf2 = round((float)$LoanCaseBillMain[$j]->pfee2_inv, 2);
                        // $pftf = round((float)$LoanCaseBillMain[$j]->transferred_pfee_amt, 2);
            
                        // $bal_to_transfer =  (float)($pf1) + (float)($pf2)  - (float)($pftf);
            
                        
                        $bal_to_transfer=  bcsub(bcadd($pf1,$pf2,2),$pftf,2);

                        if ($bal_to_transfer != 0) 
                        {
                            $LoanCaseBillMain->transferred_to_office_bank = 0;
                            $LoanCaseBillMain->save();
                        }

                        // if ($LoanCaseBillMain->transferred_pfee_amt <= 0) {
                        //     $LoanCaseBillMain->transferred_to_office_bank = 0;
                        // }

                        $LoanCaseBillMain->save();

                        $LoanCase = LoanCase::where('id', $LoanCaseBillMain->case_id)->first();
                        CaseController::adminUpdateClientLedger($LoanCase);
                    }

                    $TransferFeeDetails->delete();
                }
            }

            $this->updateTransferFeeMainAmt($id);
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    public function adminUpdateTransferAmount()
    {
        $TransferFeeDetails  = TransferFeeDetails::where('transfer_fee_main_id', '=', 2)->get();

        if (count($TransferFeeDetails) > 0) {
            for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $TransferFeeDetails[$i]['loan_case_main_bill_id'])->first();
                $TransferFeeDetails[$i]['transfer_amount'] = $LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv + $LoanCaseBillMain->sst_inv;

                $TransferFeeDetails[$i]->save();
            }
        }
    }

    /**
     * Enhanced Transfer Fee List V2
     * Enhanced version with improved UI/UX and advanced features
     */
    public function transferFeeListV2()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        $TransferFeeMain = TransferFeeMain::where('status', '=', 1);

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }
        
        if (!in_array($current_user->menuroles, ['admin', 'account'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $TransferFeeMain = $TransferFeeMain->whereIn('branch_id', [5,6]);
            } else {
                $TransferFeeMain = $TransferFeeMain->where('branch_id',$current_user->branch_id);
            }
        }

        $TransferFeeMain = $TransferFeeMain->get();
        
        return view('dashboard.transfer-fee.index-v2', [
            'TransferFeeMain' => $TransferFeeMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    /**
     * Enhanced Transfer Fee Main List V2
     * Enhanced data handling with additional filters and search capabilities
     */
    public function getTransferMainListV2(Request $request)
    {
        if ($request->ajax()) {
            $current_user = auth()->user();

            $TransferFeeMain = DB::table('transfer_fee_main as m')
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
                ->where('m.status', '<>',  99);

            // Enhanced date filtering
            if ($request->input("transfer_date_from") && $request->input("transfer_date_to")) {
                $TransferFeeMain = $TransferFeeMain->whereBetween('m.transfer_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            } else {
                if ($request->input("transfer_date_from")) {
                    $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
                }
                if ($request->input("transfer_date_to")) {
                    $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
                }
            }

            // Branch filtering
            if ($request->input("branch_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  $request->input("branch_id"));
            }

            // Reconciliation status filtering
            if ($request->input("recon_status") !== null && $request->input("recon_status") !== '') {
                $TransferFeeMain = $TransferFeeMain->where('m.is_recon', '=', $request->input("recon_status"));
            }

            // Amount range filtering
            if ($request->input("amount_from")) {
                $TransferFeeMain = $TransferFeeMain->where('m.transfer_amount', '>=', $request->input("amount_from"));
            }
            if ($request->input("amount_to")) {
                $TransferFeeMain = $TransferFeeMain->where('m.transfer_amount', '<=', $request->input("amount_to"));
            }

            // Global search
            if ($request->input("global_search")) {
                $searchTerm = $request->input("global_search");
                $TransferFeeMain = $TransferFeeMain->where(function($query) use ($searchTerm) {
                    $query->where('m.transaction_id', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('m.purpose', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('b1.name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('b2.name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('u.name', 'LIKE', "%{$searchTerm}%");
                });
            }

            // User access control
            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5,6])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [5,6]);
                } else {
                    $TransferFeeMain = $TransferFeeMain->where('b2.branch_id', '=',  $current_user->branch_id);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [51, 32])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [5, 6]);
                }
            } else {
                if (in_array($current_user->id, [13])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [$current_user->branch_id]);
                }
            }

            $TransferFeeMain = $TransferFeeMain->orderBy('m.transfer_date', 'DESC')->get();

            return DataTables::of($TransferFeeMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                        <div class="btn-group" role="group">
                            <a href="/transfer-fee/' . $data->id . '" class="btn btn-info btn-sm" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/transfer-fee/' . $data->id . '/edit" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/transfer-fee/' . $data->id . '/download" class="btn btn-success btn-sm" title="Download" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    ';
                    return $actionBtn;
                })
                ->addColumn('transfer_from_bank', function ($data) {
                    $actionBtn = '<strong>' . $data->transfer_from_bank . '</strong><br/><small class="text-muted">(' . $data->transfer_from_bank_acc_no . ')</small>';
                    return $actionBtn;
                })
                ->addColumn('transfer_to_bank', function ($data) {
                    $actionBtn = '<strong>' . $data->transfer_to_bank . '</strong><br/><small class="text-muted">(' . $data->transfer_to_bank_acc_no . ')</small>';
                    return $actionBtn;
                })
                ->editColumn('is_recon', function ($data) {
                    if ($data->is_recon == '1' || $data->is_recon == 1) {
                        return '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Reconciled</span>';
                    } else {
                        return '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Pending</span>';
                    }
                })
                ->editColumn('transfer_amount', function ($data) {
                    return '<strong class="text-success">RM ' . number_format($data->transfer_amount, 2) . '</strong>';
                })
                ->editColumn('transfer_date', function ($data) {
                    return '<small class="text-muted">' . date('d M Y', strtotime($data->transfer_date)) . '</small>';
                })
                ->editColumn('transaction_id', function ($data) {
                    return '<strong class="text-primary">' . $data->transaction_id . '</strong>';
                })
                ->rawColumns(['action', 'transfer_from_bank', 'transfer_to_bank', 'is_recon', 'transfer_amount', 'transfer_date', 'transaction_id'])
                ->make(true);
        }
    }

    /**
     * Get Transfer Fee Statistics for Dashboard
     */
    public function getTransferFeeStatistics(Request $request)
    {
        $current_user = auth()->user();
        
        $query = DB::table('transfer_fee_main as m')
            ->leftJoin('office_bank_account as b2', 'b2.id', '=', 'm.transfer_to')
            ->where('m.status', '<>', 99);

        // Apply date filters if provided
        if ($request->input("transfer_date_from") && $request->input("transfer_date_to")) {
            $query = $query->whereBetween('m.transfer_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
        } else {
            if ($request->input("transfer_date_from")) {
                $query = $query->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
            }
            if ($request->input("transfer_date_to")) {
                $query = $query->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
            }
        }

        // Apply branch filter
        if ($request->input("branch_id")) {
            $query = $query->where('m.branch_id', '=', $request->input("branch_id"));
        }

        // Apply user access control
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

        // Get statistics
        $stats = $query->selectRaw('
            COUNT(*) as total_transfers,
            SUM(m.transfer_amount) as total_amount,
            SUM(CASE WHEN m.is_recon = 0 THEN 1 ELSE 0 END) as pending_recon,
            SUM(CASE WHEN DATE_FORMAT(m.transfer_date, "%Y-%m") = DATE_FORMAT(NOW(), "%Y-%m") THEN 1 ELSE 0 END) as this_month
        ')->first();

        return response()->json([
            'total_transfers' => $stats->total_transfers ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'pending_recon' => $stats->pending_recon ?? 0,
            'this_month' => $stats->this_month ?? 0
        ]);
    }

    public function getTransferFeeBillList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();

            $rows = DB::table('loan_case_invoice_main as i')
                ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                // ->leftJoin('transfer_fee_details as t', function($join) use( $request)
                // {
                //     $join->on('t.loan_case_main_bill_id', '=', 'b.id');
                //     $join->on('t.transfer_fee_main_id', '=', $request->input('transaction_id'));

                // })
                ->leftJoin('transfer_fee_details as t', 't.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'i.invoice_no as invoice_no_v2', 'i.id as invoice_id', 'l.case_ref_no', 'c.name as client_name', 't.transfer_amount', 't.sst_amount', 't.is_recon', 't.id as transfer_id')
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('type') == 'transferred') {

                // $rows = $rows->where('b.transferred_to_office_bank', '=',  $request->input('transfer_list')); 


                $transferred_list = [];

                $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();

                for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                    array_push($transferred_list, $TransferFeeDetails[$i]->loan_case_main_bill_id);
                }


                if ($request->input('transaction_id')) {
                    $rows = $rows->whereIn('b.id', $transferred_list)->where('t.transfer_fee_main_id', '=',  $request->input('transaction_id'));
                }
            } else {

                if ($request->input('type') == 'add') {

                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);

                        $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();

                        for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                            array_push($transfer_list, $TransferFeeDetails[$i]->loan_case_main_bill_id);
                        }

                        $rows = $rows->whereIn('b.id', $transfer_list);
                    }
                } else {
                    if ($request->input('transfer_list')) {

                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        // $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();
                        $TransferFeeDetails = TransferFeeDetails::where('status', 1)->get();


                        for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                            array_push($transfer_list, $TransferFeeDetails[$i]->loan_case_main_bill_id);
                        }
                        // $rows = $rows->whereNotIn('b.id', $transfer_list);
                        $rows = $rows->where('b.transferred_to_office_bank', '=',  0);
                    }
                }
            }

            if ($request->input("recv_start_date") <> null && $request->input("recv_end_date") <> null) {
                $rows = $rows->whereBetween('b.payment_receipt_date', [$request->input("recv_start_date"), $request->input("recv_end_date")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '>=', $request->input("recv_start_date"));
                }

                if ($request->input("date_to") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '<=', $request->input("recv_end_date"));
                }
            }

            if ($request->input('branch')) {
                // $rows = $rows->where('l.branch_id', '=', $request->input("branch")); 
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                // if ($current_user->branch_id == 3) {
                //     $rows = $rows->where('b.invoice_branch_id', '=',  3);
                // } else if ($current_user->branch_id == 5) {
                //     $rows = $rows->whereIn('b.invoice_branch_id', $accessInfo['brancAccessList']);
                // }
                if (in_array($current_user->branch_id, [5,6]))
                {
                    $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
                }
                else if (in_array($current_user->branch_id, [2]))
                {
                    $rows = $rows->whereIn('l.sales_user_id', [13]);
                }
                else{
                    $rows = $rows->whereIn('b.invoice_branch_id', $accessInfo['brancAccessList']);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [32, 51])) {
                    $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
                }
            }else if (in_array($current_user->menuroles, ['lawyer'])) {
                if (in_array($current_user->id, [13])) {
                    $rows = $rows->whereIn('b.invoice_branch_id', [2]);
                }
            }

            $rows = $rows->orderBy('b.invoice_no', 'ASC')->get();

            // return $rows;

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request) {
                    if ($request->input('type') == 'transferred') {

                        $is_disabled = '';

                        if ($data->is_recon == 1) {
                            $is_disabled = 'disabled';
                        }

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="trans_bill" value="' . $data->transfer_id . '" id="trans_chk_' . $data->transfer_id . '" ' . $is_disabled . ' >
                        <label for="trans_chk_' . $data->transfer_id . '"></label>
                        </div> ';
                    } else {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="hidden" name="invoice" value="' . $data->invoice_id . '" id="inp_inv_' . $data->id . '" >
                        <input type="checkbox" name="bill" value="' . $data->id . '" id="chk_' . $data->id . '" >
                        <label for="chk_' . $data->id . '"></label>
                        </div> ';
                    }

                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>';  


                    // return '<a target="_blank" href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';

                    return $data->client_name . '<br/><b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->addColumn('sst_to_transfer', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    return $sst_to_transfer;
                })

                ->addColumn('bal_to_transfer_v2', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transferred_pfee_amt, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)  - (float)($data->transferred_pfee_amt);

                    $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }
                    // $bal_to_transfer =(float)($data->pfee2_inv);
                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    $actionBtn = '' . $bal_to_transfer . '<input class="bal_to_transfer" onchange="balUpdate()" type="hidden" id="ban_to_transfer' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                   
                    <input type="hidden" id="ban_to_transfer_limt_' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <input type="hidden" id="sst_to_transfer_' . $data->id  . '" value = "' . $sst_to_transfer . '" />
                    ';
                    return $actionBtn;
                })

                ->addColumn('bal_to_transfer_v3', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt);

                    $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }

                    return $bal_to_transfer;
                })

                ->addColumn('cal_pfee_bal', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $sst_inv = number_format((float)$data->sst_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt);

                    if ($data->sst_amount == 0) {
                        $bal_to_transfer = (float)($pftf) - (float)($sst_inv);
                    } else {
                        $bal_to_transfer = (float)($pftf);
                    }



                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }

                    return $bal_to_transfer;
                })
                ->addColumn('cal_sst_bal', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $sst_inv = number_format((float)$data->sst_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');

                    if ($data->sst_amount == 0) {
                        return $data->sst_inv;
                    } else {
                        return $data->sst_amount;
                    }
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                ->editColumn('invoice_date', function ($data) {
                    if($data->invoice_date)
                    {
                        $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                        return $formatedDate;
                    }
                    else
                    {
                        return $data->invoice_date;
                    }
                   
                })
                ->addColumn('transferred_to_office_bank', function ($data) {


                    if ($data->transferred_to_office_bank == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'cal_sst_bal', 
                'cal_pfee_bal', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no', 'bal_to_transfer_v2',
                 'bal_to_transfer_v3', 'pfee_sum', 'sst_to_transfer','invoice_date'])
                ->make(true);
        }
    }

    public function getTransferFeeBillListV2(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();

            // Updated to use loan_case_invoice_main as primary table
            $rows = DB::table('loan_case_invoice_main as i')
                ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('transfer_fee_details as t', 't.loan_case_invoice_main_id', '=', 'i.id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select(
                    'i.*', 
                    'b.case_id', 
                    'b.invoice_branch_id',
                    'b.payment_receipt_date',
                    'l.case_ref_no', 
                    'c.name as client_name', 
                    't.transfer_amount', 
                    't.sst_amount', 
                    't.is_recon', 
                    't.id as transfer_id'
                )
                ->where('i.status', '<>', 99)
                ->where('i.bln_invoice', '=', 1);

            if ($request->input('type') == 'transferred') {

                $transferred_list = [];

                $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();

                for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                    array_push($transferred_list, $TransferFeeDetails[$i]->loan_case_invoice_main_id);
                }

                if ($request->input('transaction_id')) {
                    $rows = $rows->whereIn('i.id', $transferred_list)->where('t.transfer_fee_main_id', '=',  $request->input('transaction_id'));
                }
            } else {

                if ($request->input('type') == 'add') {

                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);

                        $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();

                        for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                            array_push($transfer_list, $TransferFeeDetails[$i]->loan_case_invoice_main_id);
                        }

                        $rows = $rows->whereIn('i.id', $transfer_list);
                    }
                } else {
                    if ($request->input('transfer_list')) {

                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        $TransferFeeDetails = TransferFeeDetails::where('status', 1)->get();

                        for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                            array_push($transfer_list, $TransferFeeDetails[$i]->loan_case_invoice_main_id);
                        }
                        $rows = $rows->whereNotIn('i.id', $transfer_list);
                        $rows = $rows->where('i.transferred_to_office_bank', '=',  0);
                    }
                }
            }

            if ($request->input("recv_start_date") <> null && $request->input("recv_end_date") <> null) {
                $rows = $rows->whereBetween('b.payment_receipt_date', [$request->input("recv_start_date"), $request->input("recv_end_date")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '>=', $request->input("recv_start_date"));
                }

                if ($request->input("date_to") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '<=', $request->input("recv_end_date"));
                }
            }

            if ($request->input('branch')) {
                // $rows = $rows->where('l.branch_id', '=', $request->input("branch")); 
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                // if ($current_user->branch_id == 3) {
                //     $rows = $rows->where('b.invoice_branch_id', '=',  3);
                // } else if ($current_user->branch_id == 5) {
                //     $rows = $rows->whereIn('b.invoice_branch_id', $accessInfo['brancAccessList']);
                // }
                if (in_array($current_user->branch_id, [5,6]))
                {
                    $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
                }
                else if (in_array($current_user->branch_id, [2]))
                {
                    $rows = $rows->whereIn('l.sales_user_id', [13]);
                }
                else{
                    $rows = $rows->whereIn('b.invoice_branch_id', $accessInfo['brancAccessList']);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [32, 51])) {
                    $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
                }
            }else if (in_array($current_user->menuroles, ['lawyer'])) {
                if (in_array($current_user->id, [13])) {
                    $rows = $rows->whereIn('b.invoice_branch_id', [2]);
                }
            }

            $rows = $rows->orderBy('i.invoice_no', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request) {
                    if ($request->input('type') == 'transferred') {

                        $is_disabled = '';

                        if ($data->is_recon == 1) {
                            $is_disabled = 'disabled';
                        }

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="trans_bill" value="' . $data->id . '" id="trans_chk_' . $data->id . '" ' . $is_disabled . ' >
                        <label for="trans_chk_' . $data->id . '"></label>
                        </div> ';
                    } else {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="bill" value="' . $data->id . '" id="chk_' . $data->id . '" >
                        <label for="chk_' . $data->id . '"></label>
                        </div> ';
                    }

                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>';  


                    // return '<a target="_blank" href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';

                    return $data->client_name . '<br/><b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->addColumn('sst_to_transfer', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    return $sst_to_transfer;
                })

                ->addColumn('bal_to_transfer_v2', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transferred_pfee_amt, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)  - (float)($data->transferred_pfee_amt);

                    $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }
                    // $bal_to_transfer =(float)($data->pfee2_inv);
                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    $actionBtn = '' . $bal_to_transfer . '<input class="bal_to_transfer" onchange="balUpdate()" type="hidden" id="ban_to_transfer' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                   
                    <input type="hidden" id="ban_to_transfer_limt_' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <input type="hidden" id="sst_to_transfer_' . $data->id  . '" value = "' . $sst_to_transfer . '" />
                    ';
                    return $actionBtn;
                })

                ->addColumn('bal_to_transfer_v3', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt);

                    $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }

                    return $bal_to_transfer;
                })

                ->addColumn('cal_pfee_bal', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $sst_inv = number_format((float)$data->sst_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt);

                    if ($data->sst_amount == 0) {
                        $bal_to_transfer = (float)($pftf) - (float)($sst_inv);
                    } else {
                        $bal_to_transfer = (float)($pftf);
                    }



                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }

                    return $bal_to_transfer;
                })
                ->addColumn('cal_sst_bal', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $sst_inv = number_format((float)$data->sst_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');

                    if ($data->sst_amount == 0) {
                        return $data->sst_inv;
                    } else {
                        return $data->sst_amount;
                    }
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                // ->addColumn('bal_to_transfer_v2', function ($data) {
                //     $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transferred_pfee_amt; 
                //     return $bal_to_transfer;
                // })
                ->addColumn('transferred_to_office_bank', function ($data) {


                    if ($data->transferred_to_office_bank == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'cal_sst_bal', 'cal_pfee_bal', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no', 'bal_to_transfer_v2', 'bal_to_transfer_v3', 'pfee_sum', 'sst_to_transfer'])
                ->make(true);
        }
    }

    public function getTransferFeeAddBillList(Request $request)
    {
        if ($request->ajax()) {
            
            // Debug: Log the request data
            error_log("getTransferFeeAddBillList - Request data: " . json_encode($request->all()));

            $current_user = auth()->user();

            // Updated to use loan_case_invoice_main as primary table
            $rows = DB::table('loan_case_invoice_main as i')
                ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select(
                    'i.*', 
                    'b.case_id', 
                    'b.invoice_branch_id',
                    'b.payment_receipt_date',
                    'l.case_ref_no', 
                    'c.name as client_name'
                )
                ->where('i.transferred_to_office_bank', '=',  0)
                ->where('i.status', '<>',  99)
                ->where('i.bln_invoice', '=',  1);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                
                // Debug: Log the transfer list
                error_log("getTransferFeeAddBillList - transfer_list received: " . json_encode($transfer_list));
                
                // Extract just the IDs from the array of objects
                $invoice_ids = array_column($transfer_list, 'id');
                
                // Debug: Log the extracted IDs
                error_log("getTransferFeeAddBillList - invoice_ids extracted: " . json_encode($invoice_ids));
                
                $rows = $rows->whereIn('i.id', $invoice_ids);
            } else {
                error_log("getTransferFeeAddBillList - No transfer_list provided");
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if ($current_user->branch_id == 3) {
                    $rows = $rows->where('b.invoice_branch_id', '=',  3);
                }
            }

            $rows = $rows->orderBy('i.id', 'ASC')->get();

            // Debug: Log the final results
            error_log("getTransferFeeAddBillList - Final results count: " . $rows->count());
            error_log("getTransferFeeAddBillList - Final results: " . json_encode($rows->toArray()));

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="add_bill" value="' . $data->id . '" id="add_chk_' . $data->id . '" >
                    <label for="add_chk_' . $data->id . '" ></label>
                    </div> 
                    <input id="selected_amt_' . $data->id . '" type="hidden" value="' . $bal_to_transfer . '" />
                    <input id="inp_inv_' . $data->id . '" type="hidden" value="' . $data->id . '" />';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>'; 


                    return $data->client_name . '<br/><b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                ->addColumn('bal_to_transfer_v2', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv  - $data->transferred_pfee_amt;
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv  - $data->transferred_pfee_amt;
                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    $actionBtn = '<input class="bal_to_transfer" onchange="balUpdate()" type="number" id="ban_to_transfer' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <a href="javascript:void(0)" onclick="maxValue(' . $data->id  . ', ' . $bal_to_transfer . ')" class="btn btn-info btn-xs">Max</a>
                    <input type="hidden" id="ban_to_transfer_limt_' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <input type="hidden" id="sst_to_transfer_' . $data->id  . '" value = "' . $sst_to_transfer . '" />
                    ';
                    return $actionBtn;
                })
                ->addColumn('sst_to_transfer', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    return $sst_to_transfer;
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                
                ->editColumn('invoice_date', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                    return $formatedDate;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'is_recon', 'case_ref_no', 'bal_to_transfer_v2', 'pfee_sum', 'sst_to_transfer'])
                ->make(true);
        }
    }

    public function sstMainList()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 5) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            }
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }


        $SSTMain = SSTMain::where('status', '=', 1)->get();
        return view('dashboard.sst.index', [
            'SSTMain' => $SSTMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
        ]);
    }

    public function getSSTMainList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();
            // $TransferFeeMain = TransferFeeMain::where('status', '=', 1)->get();


            $sstMain = DB::table('sst_main as m')
                ->leftJoin('users as u', 'u.id', '=', 'm.paid_by')
                ->leftJoin('branch as b', 'b.id', '=', 'm.branch_id')
                ->select('m.*', 'u.name as paid_user', 'b.name as branch_name')
                ->where('m.status', '<>',  99);

            if ($request->input("transfer_date_from") <> null && $request->input("transfer_date_to") <> null) {
                $sstMain = $sstMain->whereBetween('m.payment_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            } else {
                if ($request->input("transfer_date_from") <> null) {
                    $sstMain = $sstMain->where('m.payment_date', '>=', $request->input("transfer_date_from"));
                }

                if ($request->input("transfer_date_to") <> null) {
                    $sstMain = $sstMain->where('m.payment_date', '<=', $request->input("transfer_date_to"));
                }
            }

            if ($request->input("branch_id")) {
                $sstMain = $sstMain->where('m.branch_id', '=',  $request->input("branch_id"));
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                $sstMain = $sstMain->where('m.branch_id', '=',  $current_user->branch_id);
            } else if (in_array($current_user->id, [51, 32])) {
                $sstMain = $sstMain->whereIn('m.branch_id', [5, 6]);
            } if (in_array($current_user->menuroles, ['lawyer'])) {
                $sstMain = $sstMain->where('m.branch_id', '=',  $current_user->branch_id);
            }


            $sstMain = $sstMain->OrderBy('payment_date', 'desc')->get();


            return DataTables::of($sstMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    <a href="/sst/' . $data->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="edit"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                // ->addColumn('transfer_from_bank', function ($data) {
                //     $actionBtn = $data->transfer_from_bank . '<br/>(' . $data->transfer_from_bank_acc_no . ')';
                //     return $actionBtn;
                // })
                // ->addColumn('transfer_to_bank', function ($data) {
                //     $actionBtn = $data->transfer_to_bank . '<br/>(' . $data->transfer_to_bank_acc_no . ')';
                //     return $actionBtn;
                // })
                ->rawColumns(['action', 'bal_to_transfer', 'transfer_from_bank', 'transfer_to_bank', 'transferred_to_office_bank', 'case_ref_no'])
                ->make(true);
        }
    }

    public function getInvoiceList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('type') == 'transferred') {

                // $rows = $rows->where('b.transferred_to_office_bank', '=',  $request->input('transfer_list'));


                $transferred_list = [];

                $SSTDetails = SSTDetails::where('sst_main_id', '=', $request->input('transaction_id'))->get();

                for ($i = 0; $i < count($SSTDetails); $i++) {
                    array_push($transferred_list, $SSTDetails[$i]->loan_case_main_bill_id);
                }


                if ($request->input('transaction_id')) {
                    $rows = $rows->whereIn('b.id', $transferred_list);
                    // $rows = $rows->whereIn('b.id',[15,17]);
                }
            } else {

                if ($request->input('type') == 'add') {
                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        $rows = $rows->whereIn('b.id', $transfer_list);
                    }
                } else {
                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        $rows = $rows->whereNotIn('b.id', $transfer_list);
                        $rows = $rows->where('b.bln_sst', '=',  0);
                    }
                }
            }

            if ($request->input("recv_start_date") <> null && $request->input("recv_end_date") <> null) {
                $rows = $rows->whereBetween('b.payment_receipt_date', [$request->input("recv_start_date"), $request->input("recv_end_date")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '>=', $request->input("recv_start_date"));
                }

                if ($request->input("date_to") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '<=', $request->input("recv_end_date"));
                }
            }

            if ($request->input('branch')) {
                // $rows = $rows->where('l.branch_id', '=', $request->input("branch"));
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if ($current_user->branch_id == 3) {
            //         $rows = $rows->where('l.branch_id', '=',  3);
            //     } else if ($current_user->branch_id == 5) {
            //         $rows = $rows->where('l.branch_id', '=',  5);
            //     }
            // }

            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id,[5,6])) {
                    $rows = $rows->whereIn('l.branch_id', [5,6]);
                }
                else if (in_array($current_user->branch_id,[2])) {
                    $rows = $rows->whereIn('l.sales_user_id', [13]);
                }
                else
                {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
            } else  if (in_array($current_user->menuroles, ['lawyer'])) {
                if (in_array($current_user->id, [13]))
                {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id])->where('l.id', '>=', 2342);
                }
                else
                {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
                
            }

            $rows = $rows->orderBy('b.id', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request) {
                    if ($request->input('type') == 'transferred') {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="trans_bill" value="' . $data->id . '" id="trans_chk_' . $data->id . '" >
                        <label for="trans_chk_' . $data->id . '"></label>
                        </div> ';
                    } else {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="bill" value="' . $data->id . '" id="chk_' . $data->id . '" >
                        <label for="chk_' . $data->id . '"></label>
                        </div> ';
                    }

                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>'; 


                    return '<a target="_blank" href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->addColumn('transferred_to_office_bank', function ($data) {


                    if ($data->transferred_to_office_bank == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no'])
                ->make(true);
        }
    }

    public function getInvoiceAddList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
                // ->where('b.transferred_to_office_bank', '=',  0)
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                $rows = $rows->whereIn('b.id', $transfer_list);
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id,[5,6])) {
                    $rows = $rows->whereIn('l.branch_id', [5,6]);
                }
                else
                {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
            } else  if (in_array($current_user->menuroles, ['lawyer'])) {
                $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
            }



            $rows = $rows->orderBy('b.id', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="add_bill" value="' . $data->id . '" id="add_chk_' . $data->id . '" >
                    <label for="add_chk_' . $data->id . '" ></label>
                    </div> 
                    <input id="selected_amt_' . $data->id . '" type="hidden" value="' . $data->sst_inv . '" />';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>';


                    return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'is_recon', 'case_ref_no'])
                ->make(true);
        }
    }

    // public function getTransferFeeBillList(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $current_user = auth()->user();

    //         $rows = DB::table('loan_case_bill_main as b')
    //             ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    //             ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
    //             ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
    //             ->where('b.status', '<>',  99)
    //             ->where('b.bln_invoice', '=',  1);



    //         if ($request->input('type') == 'transferred') {

    //             // $rows = $rows->where('b.transferred_to_office_bank', '=',  $request->input('transfer_list'));


    //             $transferred_list = [];

    //             $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();

    //             for ($i = 0; $i < count($TransferFeeDetails); $i++) {
    //                 array_push($transferred_list, $TransferFeeDetails[$i]->loan_case_main_bill_id);
    //             }


    //             if ($request->input('transaction_id')) {
    //                 $rows = $rows->whereIn('b.id', $transferred_list);
    //                 // $rows = $rows->whereIn('b.id',[15,17]);
    //             }
    //         } else {

    //             if ($request->input('type') == 'add') {
    //                 if ($request->input('transfer_list')) {
    //                     $transfer_list = json_decode($request->input('transfer_list'), true);
    //                     $rows = $rows->whereIn('b.id', $transfer_list);
    //                 }
    //             } else {
    //                 if ($request->input('transfer_list')) {
    //                     $transfer_list = json_decode($request->input('transfer_list'), true);
    //                     $rows = $rows->whereNotIn('b.id', $transfer_list);
    //                     $rows = $rows->where('b.transferred_to_office_bank', '=',  0);
    //                 }
    //             }
    //         }

    //         if ($request->input("recv_start_date") <> null && $request->input("recv_end_date") <> null) {
    //             $rows = $rows->whereBetween('b.payment_receipt_date', [$request->input("recv_start_date"), $request->input("recv_end_date")]);
    //         } else {
    //             if ($request->input("date_from") <> null) {
    //                 $rows = $rows->where('b.payment_receipt_date', '>=', $request->input("recv_start_date"));
    //             }

    //             if ($request->input("date_to") <> null) {
    //                 $rows = $rows->where('b.payment_receipt_date', '<=', $request->input("recv_end_date"));
    //             }
    //         }

    //         if ($request->input('branch')) {
    //             // $rows = $rows->where('l.branch_id', '=', $request->input("branch"));
    //             $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
    //         }

    //         if (in_array($current_user->menuroles, ['maker'])) {
    //             if ($current_user->branch_id == 3) {
    //                 $rows = $rows->where('l.branch_id', '=',  3);
    //             }
    //         }

    //         $rows = $rows->orderBy('b.id', 'ASC')->get();

    //         return DataTables::of($rows)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($data) use ($request) {
    //                 if ($request->input('type') == 'transferred') {
    //                     $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
    //                     <input type="checkbox" name="trans_bill" value="' . $data->id . '" id="trans_chk_' . $data->id . '" >
    //                     <label for="trans_chk_' . $data->id . '"></label>
    //                     </div> ';
    //                 } else {
    //                     $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
    //                     <input type="checkbox" name="bill" value="' . $data->id . '" id="chk_' . $data->id . '" >
    //                     <label for="chk_' . $data->id . '"></label>
    //                     </div> ';
    //                 }

    //                 return $actionBtn;
    //             })
    //             ->editColumn('case_ref_no', function ($data) {

    //                 // $status_lbl = '';
    //                 // if ($data->status === '2')
    //                 //     $status_lbl = '<span class="label bg-info">Open</span>';
    //                 // elseif ($data->status === '0')
    //                 //     $status_lbl = '<span class="label bg-success">Closed</span>';
    //                 // elseif ($data->status === '1')
    //                 //     $status_lbl = '<span class="label bg-purple">Running</span>';
    //                 // elseif ($data->status === '3')
    //                 //     $status_lbl = '<span class="label bg-warning">KIV</span>';
    //                 // elseif ($data->status === '99')
    //                 //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
    //                 // else
    //                 //     $status_lbl = '<span class="label bg-danger">Overdue</span>'; 


    //                 return '<a target="_blank" href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
    //             })
    //             ->addColumn('bal_to_transfer', function ($data) {
    //                 $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
    //                 return $bal_to_transfer;
    //             })
    //             ->addColumn('transferred_to_office_bank', function ($data) {


    //                 if ($data->transferred_to_office_bank == 1)
    //                     return '<span class="label bg-success">Yes</span>';
    //                 else
    //                     return '<span class="label bg-warning">No</span>';
    //             })
    //             ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no'])
    //             ->make(true);
    //     }
    // }




    public function SSTRecordCreate()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        // if (!in_array($current_user->menuroles, ['admin', 'account', 'maker'])) {
        //     return redirect()->route('dashboard.index');
        // }

        // if (AccessController::UserAccessController($this->getSSTAccessCode()) == false) {
        //     return redirect()->route('dashboard.index');
        // }

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();


        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 5) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            }else if ($current_user->branch_id == 2) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 2)->get();
            }
        }else if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        return view('dashboard.sst.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch'],
        ]);
    }

    public function createNewSSTRecord(Request $request)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $SSTMain  = new SSTMain();

        // $TransferFeeMain->transfer_amount = $request->input("transfer_amount");
        $SSTMain->payment_date = $request->input("payment_date");
        $SSTMain->paid_by = $current_user->id;
        $SSTMain->transaction_id = $request->input("trx_id");
        $SSTMain->receipt_no = '';
        $SSTMain->voucher_no = '';
        $SSTMain->branch_id = $request->input("branch");
        $SSTMain->remark = $request->input("remark");
        $SSTMain->status = 1;
        $SSTMain->created_at = date('Y-m-d H:i:s');

        $SSTMain->save();

        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
        }

        if (count($add_bill) > 0) {
            for ($i = 0; $i < count($add_bill); $i++) {
                $SSTDetails  = new SSTDetails();

                $total_amount += $add_bill[$i]['value'];

                $SSTDetails->sst_main_id = $SSTMain->id;
                $SSTDetails->loan_case_main_bill_id = $add_bill[$i]['id'];
                $SSTDetails->created_by = $current_user->id;
                $SSTDetails->amount = $add_bill[$i]['value'];
                $SSTDetails->status = 1;
                $SSTDetails->created_at = date('Y-m-d H:i:s');

                $SSTDetails->save();

                LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
                
                // Sync bln_sst to invoice records
                LoanCaseInvoiceMain::where('loan_case_main_bill_id', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
            }

            $SSTMain->amount = $total_amount;
            $SSTMain->save();
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    public function sstView($id)
    {
        $current_user = auth()->user();
        $SSTMain = SSTMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 5) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            }
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        return view('dashboard.sst.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'SSTMain' => $SSTMain,
            'Branchs' => $branchInfo['branch'],
        ]);
    }


    public function updateSST(Request $request, $id)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $SSTMain  = SSTMain::where('id', '=', $id)->first();

        $SSTMain->payment_date = $request->input("payment_date");
        $SSTMain->updated_by = $current_user->id;
        $SSTMain->transaction_id = $request->input("trx_id");
        $SSTMain->remark = $request->input("remark");
        $SSTMain->status = 1;
        $SSTMain->updated_at = date('Y-m-d H:i:s');

        $SSTMain->save();

        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
        }

        $SSTDetails  = SSTDetails::where('sst_main_id', '=', $id)->get();

        if (count($SSTDetails) > 0) {
            for ($i = 0; $i < count($SSTDetails); $i++) {

                $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $SSTDetails[$i]['loan_case_main_bill_id'])->first();

                $transfer_amount = $LoanCaseBillMain->sst_inv;

                $total_amount += $transfer_amount;
                $SSTDetails[$i]['amount'] = $transfer_amount;
                $SSTDetails[$i]->save();
                // $total_amount += $TransferFeeDetails[$i]['transfer_amount'];
            }
        }

        if (count($add_bill) > 0) {

            for ($i = 0; $i < count($add_bill); $i++) {
                $SSTDetails  = new SSTDetails();

                $total_amount += $add_bill[$i]['value'];

                $SSTDetails->sst_main_id = $SSTMain->id;
                $SSTDetails->loan_case_main_bill_id = $add_bill[$i]['id'];
                $SSTDetails->case_id = $add_bill[$i]['id'];
                $SSTDetails->created_by = $current_user->id;
                $SSTDetails->amount = $add_bill[$i]['value'];
                $SSTDetails->status = 1;
                $SSTDetails->created_at = date('Y-m-d H:i:s');

                $SSTDetails->save();

                LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
                
                // Sync bln_sst to invoice records
                LoanCaseInvoiceMain::where('loan_case_main_bill_id', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
            }
        }

        $SSTMain->amount = $total_amount;
        $SSTMain->save();

        return response()->json(['status' => 1, 'data' => 'success', 'total_amount' => $total_amount]);
    }

    public function deleteSST(Request $request, $id)
    {
        $total_amount = 0;
        $current_user = auth()->user();

        if ($request->input('delete_bill') != null) {
            $delete_bill = json_decode($request->input('delete_bill'), true);
        }

        if (count($delete_bill) > 0) {
            for ($i = 0; $i < count($delete_bill); $i++) {

                $SSTDetails  = SSTDetails::where('loan_case_main_bill_id', '=', $delete_bill[$i]['id'])->first();

                if ($SSTDetails) {
                    $SSTDetailsDelete  = new SSTDetailsDelete();

                    $SSTDetailsDelete->sst_main_id = $SSTDetails->sst_main_id;
                    $SSTDetailsDelete->loan_case_main_bill_id = $SSTDetails->loan_case_main_bill_id;
                    $SSTDetailsDelete->created_by = $current_user->id;
                    $SSTDetailsDelete->amount = $SSTDetails->amount;
                    $SSTDetailsDelete->status = 1;
                    $SSTDetailsDelete->created_at = date('Y-m-d H:i:s');

                    $SSTDetailsDelete->save();

                    $SSTDetails->delete();

                    LoanCaseBillMain::where('id', '=', $SSTDetails->loan_case_main_bill_id)->update(['bln_sst' => 0]);
                    
                    // Sync bln_sst to invoice records
                    LoanCaseInvoiceMain::where('loan_case_main_bill_id', $SSTDetails->loan_case_main_bill_id)->update(['bln_sst' => 0]);
                }
            }


            $SSTMain  = SSTMain::where('id', '=', $id)->first();
            $SSTDetails  = SSTDetails::where('sst_main_id', '=', $id)->get();

            if (count($SSTDetails) > 0) {
                for ($i = 0; $i < count($SSTDetails); $i++) {
                    $total_amount += $SSTDetails[$i]['amount'];
                }

                $SSTMain->amount = $total_amount;
                $SSTMain->save();
            }
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    public function bankRecon()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::BankReconPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // if (in_array($current_user->menuroles, ['sales'])) {
        //     if (!in_array($current_user->id, [51, 32])) {
        //         return redirect()->route('case.index');
        //     }
        // }

        // $voucher_list = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
        //     ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
        //     ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
        //     ->join('loan_case as l', 'l.id', '=', 'v.case_id')
        //     ->join('client as c', 'c.id', '=', 'l.customer_id')
        //     ->join('users as u1', 'u1.id', '=', 'v.created_by')
        //     ->where('v.status', '=', 1)
        //     ->whereNotIn('v.account_approval', [0, 5])
        //     ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
        //     ->orderBy('d.created_at', 'desc')
        //     ->get();

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1);
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [5,6]);
            }
            else
            {
                $OfficeBankAccount = $OfficeBankAccount->where('branch_id', $current_user->branch_id);
            }
        } else if (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id, [32, 51])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [5, 6]);
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
            if (in_array($current_user->id, [13])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [$current_user->branch_id]);
            }
            else
            {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [99]);
            }
        }

        if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker', 'lawyer'])) {

                $OfficeBankAccount = $OfficeBankAccount->where(function ($q) use ($current_user, $branchInfo) {
                    $q->whereIn('m.branch_id', $branchInfo['brancAccessList']);
                });
        }

        $OfficeBankAccount = $this->getOfficeBankAccount();

        $transaction = DB::table('transaction as t')
            ->join('account as a', 'a.id', '=', 't.account_details_id')
            ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            ->select('t.*', 'a.name', 'b.name as bank_name')
            ->get();

        return view('dashboard.bank-recon.index', [
            'transactions' => $transaction,
            // 'voucher_list' => $voucher_list,
            'OfficeBankAccount' => $OfficeBankAccount
        ]);
    }

    public static function getOfficeBankAccount()
    {
        $current_user = auth()->user();
        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1);

        // Get user's accessible branches using the same logic as other methods
        $branchInfo = BranchController::manageBranchAccess();
        $accessibleBranchIds = $branchInfo['brancAccessList'];

        // Apply branch filtering based on accessible branches
        if (count($accessibleBranchIds) > 0) {
            $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', $accessibleBranchIds);
        } else {
            // If user has no accessible branches, return empty result
            $OfficeBankAccount = $OfficeBankAccount->where('branch_id', '=', -1);
        }

        $OfficeBankAccount = $OfficeBankAccount->get();

        return $OfficeBankAccount;
    }

    public function getBankReconList(Request $request)
    {
        if ($request->ajax()) {

            $accessInfo = AccessController::manageAccess();

            $current_user = auth()->user();

            // // $safe_keeping = DB::table('voucher_main as m')
            // //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            // //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            // //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            // //     ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date', 'l.case_ref_no as case_ref_no')
            // //     ->where('m.status', '<>', 99)
            // //     ->where('m.account_approval', '=', 1);

            $safe_keeping = DB::table('voucher_main as m')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
                ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
                ->where('m.status', '<>', 99)
                ->where('m.account_approval', '=', 1);

            // $safe_keeping = DB::table('ledger_entried as m')
            //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            //     ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
            //     ->where('m.status', '<>', 99)
            //     ->where('m.type', '=', 'RECON');

            if ($request->input("no_date_range_filter") == 0) {
                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                    $safe_keeping = $safe_keeping->whereBetween('m.payment_date', [$request->input("date_from"), $request->input("date_to")]);
                } else {
                    if ($request->input("date_from") <> null) {
                        $safe_keeping = $safe_keeping->where('m.payment_date', '>=', $request->input("date_from"));
                    }

                    if ($request->input("date_to") <> null) {
                        $safe_keeping = $safe_keeping->where('m.payment_date', '<=', $request->input("date_to"));
                    }
                }
            }

            // if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            //     $safe_keeping = $safe_keeping->whereBetween('m.payment_date', [$request->input("date_from"), $request->input("date_to")]);
            // } else {
            //     if ($request->input("date_from") <> null) {
            //         $safe_keeping = $safe_keeping->where('m.payment_date', '>=', $request->input("date_from"));
            //     }

            //     if ($request->input("date_to") <> null) {
            //         $safe_keeping = $safe_keeping->where('m.payment_date', '<=', $request->input("date_to"));
            //     }
            // }

            if ($request->input("trx_id")) {
                $safe_keeping->where('m.transaction_id', 'like', '%' . $request->input("trx_id") . '%');
            }

            if ($request->input("trx_amt")) {
                $safe_keeping->where('m.total_amount', '=', $request->input("trx_amt"));
            }

            if (in_array($current_user->menuroles, ['account', 'admin', 'admin', 'sales'])) {
                if ($request->input("bank_id") <> 99) {
                    $safe_keeping->where('m.office_account_id', '=', $request->input("bank_id"));
                }
            } else {
                $safe_keeping->where('b.branch_id', '=', $current_user->branch_id);
            }

            if ($request->input("is_recon") <> 99) {
                $safe_keeping->where('is_recon', '=', $request->input("is_recon"));
            }

            if ($request->input("transaction_type") <> 0) {
                if ($request->input("transaction_type") == 1) {
                    $safe_keeping->whereIn('m.voucher_type', [4, 3]);
                } else if ($request->input("transaction_type") == 2) {
                    $safe_keeping->whereIn('m.voucher_type', [1, 2]);
                }
            }

            if ($request->input("voucher_type") <> 0) {
                if ($request->input("voucher_type") == 1) {
                    $safe_keeping->whereIn('m.voucher_type', [1, 4]);
                } else if ($request->input("voucher_type") == 2) {
                    $safe_keeping->whereIn('m.voucher_type', [3, 2]);
                }
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                // $safe_keeping = $safe_keeping->where('l.branch_id', '=', $current_user->branch_id);
                $safe_keeping = $safe_keeping->whereIn('l.branch_id', '=', $accessInfo['brancAccessList']);
            }

            $safe_keeping = $safe_keeping->orderBy('m.payment_date', 'ASC')->get();

            return DataTables::of($safe_keeping)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="voucher" value="' . $data->id . '" id="chk_' . $data->id . '" >
                    <label for="chk_' . $data->id . '"></label>
                    </div> ';
                    return $actionBtn;
                })
                ->editColumn('voucher_no', function ($data) {
                    if ($data->voucher_type == '1' || $data->voucher_type == '2') {
                        return '<a target="_blank" href="/voucher/' . $data->id . '/edit">' . $data->voucher_no . '>></a>';
                    } else {
                        return $data->voucher_no;
                    }
                })
                ->editColumn('case_ref_no', function ($data) {

                    return '<a target="_blank"  href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('is_recon', function ($data) {
                    if ($data->is_recon == '1' || $data->is_recon == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if ($data->voucher_type == '1' || $data->voucher_type == '4')
                        return 'Bill';
                    elseif ($data->voucher_type == '2' || $data->voucher_type == '3')
                        return 'Trust';
                })
                ->addColumn('transaction_type', function ($data) {
                    if ($data->voucher_type == '1' || $data->voucher_type == '2')
                        return 'Out';
                    elseif ($data->voucher_type == '4' || $data->voucher_type == '3')
                        return 'In';
                })
                ->rawColumns(['action', 'voucher_no', 'voucher_type', 'transaction_type', 'is_recon', 'case_ref_no'])
                ->make(true);
        }
    }

    public function getBankReconListV2(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();

            $safe_keeping = DB::table('ledger_entries_v2 as m')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.bank_id')
                ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
                // ->whereNotIn('m.type', ['TRANSFER_IN', 'SST_IN'])
                ->where('m.status', '<>', 99);


            // if ($request->input("no_date_range_filter") == 0) {
            //     if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            //         $safe_keeping = $safe_keeping->whereBetween('m.payment_date', [$request->input("date_from"), $request->input("date_to")]);
            //     } else {
            //         if ($request->input("date_from") <> null) {
            //             $safe_keeping = $safe_keeping->where('m.payment_date', '>=', $request->input("date_from"));
            //         }

            //         if ($request->input("date_to") <> null) {
            //             $safe_keeping = $safe_keeping->where('m.payment_date', '<=', $request->input("date_to"));
            //         }
            //     }
            // }

            // If transaction_id is provided, ignore date range filter to include all entries for that transaction
            // This is because ledger entries use invoice payment dates, which may differ from transfer date
            $hasTransactionIdFilter = $request->input("trx_id") && trim($request->input("trx_id")) != '';
            
            if ($hasTransactionIdFilter) {
                // Use exact match instead of LIKE to match the SQL query and avoid unintended matches
                $safe_keeping->where('m.transaction_id', '=', $request->input("trx_id"));
                // Don't apply date range filter when filtering by transaction_id
                // This ensures all entries for the same transaction batch are included
            } else if ($request->input("no_date_range_filter") == 0) {
                // Apply date range filter only when NOT filtering by transaction_id
                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                    $safe_keeping = $safe_keeping->whereBetween('m.date', [$request->input("date_from"), $request->input("date_to")]);
                } else {
                    if ($request->input("date_from") <> null) {
                        $safe_keeping = $safe_keeping->where('m.date', '>=', $request->input("date_from"));
                    }

                    if ($request->input("date_to") <> null) {
                        $safe_keeping = $safe_keeping->where('m.date', '<=', $request->input("date_to"));
                    }
                }
            }

            if ($request->input("trx_amt")) {
                $safe_keeping->where('m.amount', '=', $request->input("trx_amt"));
            }


            if (in_array($current_user->menuroles, ['account', 'admin', 'admin','maker'])) {
                if ($request->input("bank_id") <> 99) {
                    // $safe_keeping->where('m.office_account_id', '=', $request->input("bank_id"));
                    $safe_keeping->where('m.bank_id', '=', $request->input("bank_id"));
                }
            } else {
                $safe_keeping->where('b.branch_id', '=', $current_user->branch_id);
            }

            if ($request->input("is_recon") <> 99) {
                $safe_keeping->where('is_recon', '=', $request->input("is_recon"));
            }

            if ($request->input("transaction_type") <> 0) {
                if ($request->input("transaction_type") == 2) {
                    $safe_keeping->whereIn('m.type', ['JOURNAL_OUT', 'TRANSFER_OUT', 'BILL_DISB', 'TRUST_DISB', 'SST_OUT', 'REIMB_OUT', 'REIMB_SST_OUT', 'CLOSEFILE_OUT', 'ABORTFILE_OUT']);
                } else if ($request->input("transaction_type") == 1) {
                    $safe_keeping->whereIn('m.type', ['JOURNAL_IN', 'TRANSFER_IN', 'BILL_RECV', 'TRUST_RECV', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN']);
                }
            }

            if ($request->input("voucher_type") <> 0) {
                $safe_keeping->where('m.type', 'like', '%' . $request->input("voucher_type") . '%');
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                // $safe_keeping = $safe_keeping->where('l.branch_id', '=', $current_user->branch_id);
                // $safe_keeping = $safe_keeping->whereIn('l.branch_id',  $accessInfo['brancAccessList']);

                $safe_keeping = $safe_keeping->where(function ($q) use ($accessInfo) {
                    $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                        ->orWhereIn('l.sales_user_id', $accessInfo['user_list'])
                        ->orWhereIn('l.clerk_id', $accessInfo['user_list'])
                        ->orWhereIn('l.id', $accessInfo['case_list'])
                        ->orWhereIn('l.lawyer_id', $accessInfo['user_list'])
                        // Include entries where case_id is null (system entries like transfer fees)
                        ->orWhereNull('m.case_id');
                });
            }

            // $safe_keeping = $safe_keeping->orderBy('m.payment_date', 'ASC')->get(); 
            $safe_keeping = $safe_keeping->orderBy('m.date', 'ASC')->get();

            return DataTables::of($safe_keeping)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="voucher" value="' . $data->id . '" id="chk_' . $data->id . '" >
                    <label for="chk_' . $data->id . '"></label>
                    </div> ';
                    return $actionBtn;
                })
                ->editColumn('cheque_no', function ($data) {
                    if (($data->type == 'TRUST_DISB' || $data->type == 'BILL_DISB') && $data->is_recon == 0)
                    {
                        return '<a target="_blank" href="/voucher/' . $data->key_id . '/edit">' . $data->cheque_no . '>></a>';
                    }
                    else
                    {
                        return $data->cheque_no;
                    }

                })
                ->editColumn('case_ref_no', function ($data) {

                    return '<a target="_blank"  href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('is_recon', function ($data) {
                    if ($data->is_recon == '1' || $data->is_recon == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if (in_array($data->type, ['BILL_DISB', 'BILL_RECV'])) {
                        return 'Bill';
                    } elseif (in_array($data->type, ['TRUST_DISB', 'TRUST_RECV'])) {
                        return 'Trust';
                    } elseif (in_array($data->type, ['JOURNAL_IN', 'JOURNAL_OUT'])) {
                        return 'Journal';
                    } elseif (in_array($data->type, ['TRANSFER_IN', 'TRANSFER_OUT'])) {
                        return 'Transfer Fee';
                    } elseif (in_array($data->type, ['SST_IN', 'SST_OUT'])) {
                        return 'SST';
                    } elseif (in_array($data->type, ['REIMB_IN', 'REIMB_OUT'])) {
                        return 'Reimbursement';
                    } elseif (in_array($data->type, ['REIMB_SST_IN', 'REIMB_SST_OUT'])) {
                        return 'Reimbursement SST';
                    } elseif (in_array($data->type, ['CLOSEFILE_IN', 'CLOSEFILE_OUT'])) {
                        return 'Close File';
                    } elseif (in_array($data->type, ['ABORTFILE_IN', 'ABORTFILE_OUT'])) {
                        return 'Abort File';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('transaction_type', function ($data) {
                    // if ($data->voucher_type == '1' || $data->voucher_type == '2') 
                    //     return 'Out';
                    // elseif ($data->voucher_type == '4' || $data->voucher_type == '3')
                    //     return 'In';

                    if (in_array($data->type, ['JOURNAL_OUT', 'TRANSFER_OUT', 'BILL_DISB', 'TRUST_DISB', 'SST_OUT', 'REIMB_OUT', 'REIMB_SST_OUT', 'CLOSEFILE_OUT', 'ABORTFILE_OUT'])) {
                        return 'Out';
                    } else {
                        return 'In';
                    }

                    // if ($data->type == 'JOURNAL_IN' || $data->type == 'JOURNAL_OUT')
                    //     return 'Journal';
                    // elseif ($data->type == 'TRANSFER_IN' || $data->type == 'TRANSFER_OUT')
                    //     return 'Transfer Fee';
                    // elseif ($data->type == 'BILL_RECEIVE' || $data->type == 'BILL_DISB')
                    //     return 'Bill';
                    // elseif ($data->type == 'TRUST_RECV' || $data->type == 'TRUST_DISB')
                    //     return 'Trust';
                    // elseif ($data->type == 'CLOSEFILE_IN' || $data->type == 'CLOSEFILE_OUT')
                    //     return 'Close File';
                    // elseif ($data->type == 'SST_IN' || $data->type == 'SST_OUT')
                    //     return 'SST';
                })
                ->addColumn('type', function ($data) {
                    if ($data->type == 'JOURNAL_IN' || $data->type == 'JOURNAL_OUT')
                        return 'Journal';
                    elseif ($data->type == 'TRANSFER_IN' || $data->type == 'TRANSFER_OUT')
                        return 'Transfer Fee';
                    elseif ($data->type == 'BILL_RECEIVE' || $data->type == 'BILL_DISB')
                        return 'Bill';
                    elseif ($data->type == 'TRUST_RECV' || $data->type == 'TRUST_DISB')
                        return 'Trust';
                    elseif ($data->type == 'CLOSEFILE_IN' || $data->type == 'CLOSEFILE_OUT')
                        return 'Close File';
                    elseif ($data->type == 'SST_IN' || $data->type == 'SST_OUT')
                        return 'SST';
                })
                ->rawColumns(['action', 'cheque_no', 'voucher_type', 'transaction_type', 'is_recon', 'case_ref_no'])
                ->make(true);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function getBankReconTotal(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();

            $safe_keeping = DB::table('ledger_entries_v2 as m')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.bank_id')
                ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
                // ->whereNotIn('m.type', ['TRANSFER_IN', 'SST_IN'])
                ->where('m.status', '<>', 99);


            // if ($request->input("no_date_range_filter") == 0) {
            //     if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            //         $safe_keeping = $safe_keeping->whereBetween('m.payment_date', [$request->input("date_from"), $request->input("date_to")]);
            //     } else {
            //         if ($request->input("date_from") <> null) {
            //             $safe_keeping = $safe_keeping->where('m.payment_date', '>=', $request->input("date_from"));
            //         }

            //         if ($request->input("date_to") <> null) {
            //             $safe_keeping = $safe_keeping->where('m.payment_date', '<=', $request->input("date_to"));
            //         }
            //     }
            // }

            // If transaction_id is provided, ignore date range filter to include all entries for that transaction
            // This is because ledger entries use invoice payment dates, which may differ from transfer date
            $hasTransactionIdFilter = $request->input("trx_id") && trim($request->input("trx_id")) != '';
            
            if ($hasTransactionIdFilter) {
                // Use exact match instead of LIKE to match the SQL query and avoid unintended matches
                $safe_keeping->where('m.transaction_id', '=', $request->input("trx_id"));
                // Don't apply date range filter when filtering by transaction_id
                // This ensures all entries for the same transaction batch are included
            } else if ($request->input("no_date_range_filter") == 0) {
                // Apply date range filter only when NOT filtering by transaction_id
                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                    $safe_keeping = $safe_keeping->whereBetween('m.date', [$request->input("date_from"), $request->input("date_to")]);
                } else {
                    if ($request->input("date_from") <> null) {
                        $safe_keeping = $safe_keeping->where('m.date', '>=', $request->input("date_from"));
                    }

                    if ($request->input("date_to") <> null) {
                        $safe_keeping = $safe_keeping->where('m.date', '<=', $request->input("date_to"));
                    }
                }
            }

            if ($request->input("trx_amt")) {
                $safe_keeping->where('m.amount', '=', $request->input("trx_amt"));
            }


            if (in_array($current_user->menuroles, ['account', 'admin', 'admin','maker'])) {
                if ($request->input("bank_id") <> 99) {
                    // $safe_keeping->where('m.office_account_id', '=', $request->input("bank_id"));
                    $safe_keeping->where('m.bank_id', '=', $request->input("bank_id"));
                }
            } else {
                $safe_keeping->where('b.branch_id', '=', $current_user->branch_id);
            }

            if ($request->input("is_recon") <> 99) {
                $safe_keeping->where('is_recon', '=', $request->input("is_recon"));
            }

            if ($request->input("transaction_type") <> 0) {
                if ($request->input("transaction_type") == 2) {
                    $safe_keeping->whereIn('m.type', ['JOURNAL_OUT', 'TRANSFER_OUT', 'BILL_DISB', 'TRUST_DISB', 'SST_OUT', 'REIMB_OUT', 'REIMB_SST_OUT', 'CLOSEFILE_OUT', 'ABORTFILE_OUT']);
                } else if ($request->input("transaction_type") == 1) {
                    $safe_keeping->whereIn('m.type', ['JOURNAL_IN', 'TRANSFER_IN', 'BILL_RECV', 'TRUST_RECV', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN']);
                }
            }

            if ($request->input("voucher_type") <> 0) {
                $safe_keeping->where('m.type', 'like', '%' . $request->input("voucher_type") . '%');
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                // $safe_keeping = $safe_keeping->where('l.branch_id', '=', $current_user->branch_id);
                // $safe_keeping = $safe_keeping->whereIn('l.branch_id',  $accessInfo['brancAccessList']);

                $safe_keeping = $safe_keeping->where(function ($q) use ($accessInfo) {
                    $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                        ->orWhereIn('l.sales_user_id', $accessInfo['user_list'])
                        ->orWhereIn('l.clerk_id', $accessInfo['user_list'])
                        ->orWhereIn('l.id', $accessInfo['case_list'])
                        ->orWhereIn('l.lawyer_id', $accessInfo['user_list'])
                        // Include entries where case_id is null (system entries like transfer fees)
                        ->orWhereNull('m.case_id');
                });
            }

            $safe_keeping = $safe_keeping->orderBy('m.date', 'ASC')->sum('amount');

            return $safe_keeping;
        }
        
        return 0;
    }

    public function diagnoseBankReconDiscrepancy(Request $request)
    {
        if ($request->ajax()) {
            $transactionId = $request->input("trx_id");
            $bankId = $request->input("bank_id", 15);
            
            if (!$transactionId) {
                return response()->json(['error' => 'Transaction ID required'], 400);
            }
            
            // Get totals from ledger_entries_v2 (what bank reconciliation shows)
            $ledgerTotals = DB::table('ledger_entries_v2 as m')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.bank_id')
                ->where('m.transaction_id', '=', $transactionId)
                ->where('m.status', '<>', 99)
                ->where('m.bank_id', '=', $bankId)
                ->whereIn('m.type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
                ->selectRaw('m.type, SUM(m.amount) as total, COUNT(*) as count')
                ->groupBy('m.type')
                ->get();
            
            // Get totals from transfer_fee_details (what transfer fee page shows)
            $transferFeeMain = DB::table('transfer_fee_main')
                ->where('transaction_id', '=', $transactionId)
                ->where('status', '<>', 99)
                ->first();
            
            $transferFeeDetails = null;
            if ($transferFeeMain) {
                $transferFeeDetails = DB::table('transfer_fee_details')
                    ->where('transfer_fee_main_id', '=', $transferFeeMain->id)
                    ->where('status', '<>', 99)
                    ->selectRaw('
                        SUM(transfer_amount) as total_pfee,
                        SUM(sst_amount) as total_sst,
                        SUM(reimbursement_amount) as total_reimb,
                        SUM(reimbursement_sst_amount) as total_reimb_sst,
                        COUNT(*) as count
                    ')
                    ->first();
            }
            
            // Calculate totals
            $ledgerTotal = 0;
            $ledgerBreakdown = [];
            foreach ($ledgerTotals as $row) {
                $ledgerTotal += $row->total;
                $ledgerBreakdown[$row->type] = [
                    'total' => $row->total,
                    'count' => $row->count
                ];
            }
            
            $transferFeeTotal = 0;
            if ($transferFeeDetails) {
                $transferFeeTotal = ($transferFeeDetails->total_pfee ?? 0) + 
                                  ($transferFeeDetails->total_sst ?? 0) + 
                                  ($transferFeeDetails->total_reimb ?? 0) + 
                                  ($transferFeeDetails->total_reimb_sst ?? 0);
            }
            
            // Check for extra entries in ledger that don't match transfer_fee_details
            $extraEntries = DB::table('ledger_entries_v2 as m')
                ->leftJoin('transfer_fee_details as tfd', function($join) {
                    $join->on('m.key_id_2', '=', 'tfd.id')
                         ->orOn('m.key_id', '=', DB::raw('(SELECT transfer_fee_main_id FROM transfer_fee_details WHERE id = m.key_id_2)'));
                })
                ->where('m.transaction_id', '=', $transactionId)
                ->where('m.status', '<>', 99)
                ->where('m.bank_id', '=', $bankId)
                ->whereIn('m.type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
                ->whereNull('tfd.id')
                ->select('m.*')
                ->get();
            
            return response()->json([
                'transaction_id' => $transactionId,
                'bank_id' => $bankId,
                'ledger_total' => round($ledgerTotal, 2),
                'transfer_fee_total' => round($transferFeeTotal, 2),
                'difference' => round($ledgerTotal - $transferFeeTotal, 2),
                'ledger_breakdown' => $ledgerBreakdown,
                'transfer_fee_breakdown' => $transferFeeDetails ? [
                    'TRANSFER_IN' => round($transferFeeDetails->total_pfee ?? 0, 2),
                    'SST_IN' => round($transferFeeDetails->total_sst ?? 0, 2),
                    'REIMB_IN' => round($transferFeeDetails->total_reimb ?? 0, 2),
                    'REIMB_SST_IN' => round($transferFeeDetails->total_reimb_sst ?? 0, 2),
                ] : null,
                'extra_entries_count' => $extraEntries->count(),
                'extra_entries' => $extraEntries->map(function($entry) {
                    return [
                        'id' => $entry->id,
                        'type' => $entry->type,
                        'amount' => $entry->amount,
                        'date' => $entry->date,
                        'key_id' => $entry->key_id,
                        'key_id_2' => $entry->key_id_2,
                    ];
                })
            ]);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function getBankReconTotalBak2(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();

            // $safe_keeping = DB::table('voucher_main as m')
            //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            //     ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date', 'l.case_ref_no as case_ref_no')
            //     ->where('m.status', '<>', 99)
            //     ->where('m.account_approval', '=', 1);

            // $safe_keeping = DB::table('voucher_main as m')
            //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            //     ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
            //     ->where('m.status', '<>', 99)
            //     ->where('m.account_approval', '=', 1);

            $safe_keeping = DB::table('ledger_entries_v2 as m')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.bank_id')
                ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
                ->whereNotIn('m.type', ['TRANSFER_IN', 'SST_IN'])
                ->where('m.status', '<>', 99);

                if ($request->input("no_date_range_filter") == 0) {
                    if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                        $safe_keeping = $safe_keeping->whereBetween('m.date', [$request->input("date_from"), $request->input("date_to")]);
                    } else {
                        if ($request->input("date_from") <> null) {
                            $safe_keeping = $safe_keeping->where('m.date', '>=', $request->input("date_from"));
                        }
    
                        if ($request->input("date_to") <> null) {
                            $safe_keeping = $safe_keeping->where('m.date', '<=', $request->input("date_to"));
                        }
                    }
                }
    
    
    
                if ($request->input("trx_id")) {
                    $safe_keeping->where('m.transaction_id', 'like', '%' . $request->input("trx_id") . '%');
                }
    
                if ($request->input("trx_amt")) {
                    $safe_keeping->where('m.amount', '=', $request->input("trx_amt"));
                }
    
    
                if (in_array($current_user->menuroles, ['account', 'admin', 'admin','maker'])) {
                    if ($request->input("bank_id") <> 99) {
                        // $safe_keeping->where('m.office_account_id', '=', $request->input("bank_id"));
                        $safe_keeping->where('m.bank_id', '=', $request->input("bank_id"));
                    }
                } else {
                    $safe_keeping->where('b.branch_id', '=', $current_user->branch_id);
                }
    
                if ($request->input("is_recon") <> 99) {
                    $safe_keeping->where('is_recon', '=', $request->input("is_recon"));
                }
    
                if ($request->input("transaction_type") <> 0) {
                    if ($request->input("transaction_type") == 2) {
                        $safe_keeping->whereIn('m.type', ['JOURNAL_OUT', 'TRANSFER_OUT', 'BILL_DISB', 'TRUST_DISB', 'SST_OUT', 'CLOSEFILE_OUT']);
                    } else if ($request->input("transaction_type") == 1) {
                        $safe_keeping->whereIn('m.type', ['JOURNAL_IN', 'TRANSFER_IN', 'BILL_RECV', 'TRUST_RECV', 'SST_IN', 'CLOSEFILE_IN']);
                    }
                }
    
                if ($request->input("voucher_type") <> 0) {
                    $safe_keeping->where('m.type', 'like', '%' . $request->input("voucher_type") . '%');
                }
    
                if (in_array($current_user->menuroles, ['maker'])) {
                    // $safe_keeping = $safe_keeping->where('l.branch_id', '=', $current_user->branch_id);
                    // $safe_keeping = $safe_keeping->whereIn('l.branch_id',  $accessInfo['brancAccessList']);

                    $safe_keeping = $safe_keeping->where(function ($q) use ($accessInfo) {
                        $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                            ->orWhereIn('sales_user_id', $accessInfo['user_list'])
                            ->orWhereIn('clerk_id', $accessInfo['user_list'])
                            ->orWhereIn('lawyer_id', $accessInfo['user_list']);
                    });
                }


            $safe_keeping = $safe_keeping->orderBy('m.date', 'ASC')->sum('amount');

            return $safe_keeping;
        }
    }

    public function getBankReconTotalBak(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            // $safe_keeping = DB::table('voucher_main as m')
            //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            //     ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date', 'l.case_ref_no as case_ref_no')
            //     ->where('m.status', '<>', 99)
            //     ->where('m.account_approval', '=', 1);

            $safe_keeping = DB::table('voucher_main as m')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
                ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
                ->where('m.status', '<>', 99)
                ->where('m.account_approval', '=', 1);

            if ($request->input("no_date_range_filter") == 0) {
                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                    $safe_keeping = $safe_keeping->whereBetween('m.payment_date', [$request->input("date_from"), $request->input("date_to")]);
                } else {
                    if ($request->input("date_from") <> null) {
                        $safe_keeping = $safe_keeping->where('m.payment_date', '>=', $request->input("date_from"));
                    }

                    if ($request->input("date_to") <> null) {
                        $safe_keeping = $safe_keeping->where('m.payment_date', '<=', $request->input("date_to"));
                    }
                }
            }

            // if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            //     $safe_keeping = $safe_keeping->whereBetween('m.payment_date', [$request->input("date_from"), $request->input("date_to")]);
            // } else {
            //     if ($request->input("date_from") <> null) {
            //         $safe_keeping = $safe_keeping->where('m.payment_date', '>=', $request->input("date_from"));
            //     }

            //     if ($request->input("date_to") <> null) {
            //         $safe_keeping = $safe_keeping->where('m.payment_date', '<=', $request->input("date_to"));
            //     }
            // }


            if ($request->input("trx_id")) {
                $safe_keeping->where('m.transaction_id', 'like', '%' . $request->input("trx_id") . '%');
            }

            if ($request->input("trx_amt")) {
                $safe_keeping->where('m.total_amount', '=', $request->input("trx_amt"));
            }



            if (in_array($current_user->menuroles, ['account', 'admin', 'admin'])) {
                if ($request->input("bank_id") <> 99) {
                    $safe_keeping->where('m.office_account_id', '=', $request->input("bank_id"));
                }
            } else {
                $safe_keeping->where('b.branch_id', '=', $current_user->branch_id);
            }

            if ($request->input("is_recon") <> 99) {
                $safe_keeping->where('is_recon', '=', $request->input("is_recon"));
            }

            if ($request->input("transaction_type") <> 0) {
                if ($request->input("transaction_type") == 1) {
                    $safe_keeping->whereIn('m.voucher_type', [4, 3]);
                } else if ($request->input("transaction_type") == 2) {
                    $safe_keeping->whereIn('m.voucher_type', [1, 2]);
                }
            }

            if ($request->input("voucher_type") <> 0) {
                if ($request->input("voucher_type") == 1) {
                    $safe_keeping->whereIn('m.voucher_type', [1, 4]);
                } else if ($request->input("voucher_type") == 2) {
                    $safe_keeping->whereIn('m.voucher_type', [3, 2]);
                }
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                $safe_keeping = $safe_keeping->where('l.branch_id', '=', $current_user->branch_id);
            }


            $safe_keeping = $safe_keeping->orderBy('m.payment_date', 'ASC')->sum('total_amount');

            return $safe_keeping;
        }
    }

    public function updateRecon(Request $request)
    {
        $status = 0;
        $date = null;
        $voucherList = [];
        $data = [];

        $ledgerStatus = 99;


        if ($request->input('type')) {
            if ($request->input('type') == 'UPDATE') {
                $status = 1;
                $date = $request->input('recon_date');
                $ledgerStatus = 1;
            } elseif ($request->input('type') == 'REVERT') {
                $status = 0;
                $ledgerStatus = 99;
            }


            if ($request->input('voucher_list') != null) {
                $voucherList = json_decode($request->input('voucher_list'), true);
            }



            if (Count($voucherList) > 0) {
                for ($i = 0; $i < count($voucherList); $i++) {
                    $VoucherMain = VoucherMain::where('id', '=', $voucherList[$i]['id'])->first();


                    if ($VoucherMain) {
                        $VoucherMain->is_recon = $status;
                        $VoucherMain->recon_date = $date;
                        $VoucherMain->save();

                        $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $VoucherMain->id)->get();


                        $VoucherDetails = DB::table('voucher_details as d')
                            ->leftJoin('loan_case_bill_details as bd', 'bd.id', '=', 'd.account_details_id')
                            ->leftJoin('account_item as a', 'a.id', '=', 'bd.account_item_id')
                            ->select('d.*', 'a.name as account_item')
                            ->where('d.voucher_main_id', '=', $VoucherMain->id)->get();



                        if (count($VoucherDetails) > 0) {
                            for ($j = 0; $j < count($VoucherDetails); $j++) {

                                $LedgerEntries = LedgerEntries::where('key_id_2', '=', $VoucherDetails[$j]->id)
                                    ->where('type', 'like', '%RECON%')->first();

                                $trans_type = '';

                                if ($VoucherMain->voucher_type == 3 || $VoucherMain->voucher_type == 4) {
                                    $trans_type = 'RECONADD';
                                } else {
                                    $trans_type = 'RECONLESS';
                                }

                                if ($LedgerEntries) {
                                    $LedgerEntries->transaction_id = $VoucherMain->transaction_id;
                                    $LedgerEntries->case_id = $VoucherMain->case_id;
                                    $LedgerEntries->loan_case_main_bill_id = $VoucherMain->case_bill_main_id;
                                    $LedgerEntries->user_id = $VoucherMain->created_by;
                                    $LedgerEntries->key_id = $VoucherMain->id;
                                    $LedgerEntries->key_id_2 = $VoucherDetails[$j]->id;
                                    $LedgerEntries->cheque_no = $VoucherMain->voucher_no;
                                    $LedgerEntries->transaction_type = $VoucherMain->voucher_type;
                                    $LedgerEntries->amount = $VoucherDetails[$j]->amount;
                                    $LedgerEntries->bank_id = $VoucherMain->office_account_id;
                                    $LedgerEntries->remark = $VoucherMain->remark;
                                    $LedgerEntries->payee = $VoucherMain->payee;
                                    // $LedgerEntries->sys_desc = $transfer_fee[$j]->account_item;
                                    $LedgerEntries->status = $ledgerStatus;
                                    $LedgerEntries->created_at = $VoucherMain->recon_date;
                                    $LedgerEntries->date = $VoucherMain->recon_date;
                                    $LedgerEntries->type = $trans_type;
                                    $LedgerEntries->save();
                                } else {
                                    $LedgerEntries = new LedgerEntries();


                                    $LedgerEntries->transaction_id = $VoucherMain->transaction_id;
                                    $LedgerEntries->case_id = $VoucherMain->case_id;
                                    $LedgerEntries->loan_case_main_bill_id = $VoucherMain->case_bill_main_id;
                                    $LedgerEntries->user_id = $VoucherMain->created_by;
                                    $LedgerEntries->key_id = $VoucherMain->id;
                                    $LedgerEntries->key_id_2 = $VoucherDetails[$j]->id;
                                    $LedgerEntries->cheque_no = $VoucherMain->voucher_no;
                                    $LedgerEntries->transaction_type = $VoucherMain->voucher_type;
                                    $LedgerEntries->amount = $VoucherDetails[$j]->amount;
                                    $LedgerEntries->bank_id = $VoucherMain->office_account_id;
                                    $LedgerEntries->remark = $VoucherMain->remark;
                                    $LedgerEntries->payee = $VoucherMain->payee;
                                    $LedgerEntries->sys_desc = $VoucherDetails[$j]->account_item;
                                    $LedgerEntries->status = $ledgerStatus;
                                    $LedgerEntries->created_at = $VoucherMain->recon_date;
                                    $LedgerEntries->date = $VoucherMain->recon_date;
                                    $LedgerEntries->type = $trans_type;
                                    $LedgerEntries->save();
                                }
                            }
                        } else {
                        }
                    }
                }
            }

            $data = $this->getMonthRecon($request);
        }


        return response()->json(['status' => 1, 'message' => 'Successfully created new case', 'data' => $data]);
    }


    public function updateReconV2(Request $request)
    {
        $status = 0;
        $date = null;
        $voucherList = [];
        $data = [];

        $ledgerStatus = 99;


        if ($request->input('type')) {
            if ($request->input('type') == 'UPDATE') {
                $status = 1;
                $date = $request->input('recon_date');
                $ledgerStatus = 1;
            } elseif ($request->input('type') == 'REVERT') {
                $status = 0;
                $ledgerStatus = 99;
            }


            if ($request->input('voucher_list') != null) {
                $voucherList = json_decode($request->input('voucher_list'), true);
            }


            if (Count($voucherList) > 0) {
                for ($i = 0; $i < count($voucherList); $i++) {

                    $LedgerEntriesV2 = LedgerEntriesV2::where('id', '=', $voucherList[$i]['id'])->first();


                    if ($LedgerEntriesV2) {
                        $LedgerEntriesV2->is_recon = $status;
                        $LedgerEntriesV2->recon_date = $date;
                        $LedgerEntriesV2->save();

                        if (in_array($LedgerEntriesV2->type, ['BILL_RECV', 'BILL_DISB','TRUST_RECV', 'TRUST_DISB']))
                        {
                            $VoucherMain = VoucherMain::where('id', '=', $LedgerEntriesV2->key_id)->first();

                            if ($VoucherMain) {
                                $VoucherMain->is_recon = $status;
                                $VoucherMain->recon_date = $date;
                                $VoucherMain->save();
                            }
                        }
                        else if (in_array($LedgerEntriesV2->type, ['JOURNAL_IN', 'JOURNAL_OUT']))
                        {
                            $JournalEntryMain = JournalEntryMain::where('id', '=', $LedgerEntriesV2->key_id)->first();

                            if ($JournalEntryMain) {
                                $JournalEntryMain->is_recon = $status;
                                // $JournalEntryMain->recon_date = $date;
                                $JournalEntryMain->save();
                            }

                            $JournalEntryDetails = JournalEntryDetails::where('id', '=', $LedgerEntriesV2->key_id_2)->first();

                            if ($JournalEntryDetails) {
                                $JournalEntryDetails->is_recon = $status;
                                $JournalEntryDetails->save();
                            }
                        }
                        else if (in_array($LedgerEntriesV2->type, ['TRANSFER_IN', 'TRANSFER_OUT','SST_IN', 'SST_OUT']))
                        {
                            $TransferFeeMain = TransferFeeMain::where('id', '=', $LedgerEntriesV2->key_id)->first();

                            if ($TransferFeeMain) {
                                $TransferFeeMain->is_recon = $status;
                                // $TransferFeeMain->recon_date = $date;
                                $TransferFeeMain->save();
                            }

                            $TransferFeeDetails = TransferFeeDetails::where('id', '=', $LedgerEntriesV2->key_id_2)->first();

                            if ($TransferFeeDetails) {
                                $TransferFeeDetails->is_recon = $status;
                                $TransferFeeDetails->save();
                            }
                        }
                        else
                        {

                        }

                        
                    }
                }
            }

            $data = $this->getMonthRecon($request);
        }


        return response()->json(['status' => 1, 'message' => 'Successfully created new case', 'data' => $data]);
    }

    public function getMonthRecon(Request $request)
    {
        $current_user = auth()->user();
        $totalAddCLRDeposit = 0;
        $totalLessCLRDeposit = 0;

        $d = date_parse_from_format("Y-m-d", $request->input("recon_date"));

        // $totalAddCLRDeposit = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     ->whereIn('m.type', ['RECONADD', 'JOURNALIN', 'TRANSFERIN', 'SSTIN'])
        //     ->where('m.bank_id', '=', $request->input("bank_id"))->sum('amount');

        $totalAddCLRDeposit = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.date', $d["month"])
            ->whereYear('m.date', $d["year"])
            ->whereIn('m.type', ['BILL_RECV', 'TRUST_RECV', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.is_recon', 1)
            ->where('m.bank_id', '=', $request->input("bank_id"))->sum('amount');

        // $totalLessCLRDeposit = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     ->whereIn('m.type',  ['RECONLESS', 'TRANSFEROUT', 'SSTOUT', 'JOURNALOUT'])
        //     ->where('m.bank_id', '=', $request->input("bank_id"))->sum('amount');


        $totalLessCLRDeposit = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.date', $d["month"])
            ->whereYear('m.date', $d["year"])
            ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB'])
            ->where('m.is_recon', 1)
            ->where('m.bank_id', '=', $request->input("bank_id"))->sum('amount');


        // $totalLessCLRDeposit = DB::table('ledger_entries_v2 as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB'])
        //     ->where('m.is_recon', 1)
        //     ->where('m.bank_id', '=', $request->input("bank_id"))->get();

            // return $totalLessCLRDeposit;

        $totalAddCLRDeposit = number_format((float)$totalAddCLRDeposit, 2, '.', '');
        $totalLessCLRDeposit = number_format((float)$totalLessCLRDeposit, 2, '.', '');

        $this->updateBankReconRecord($request->input("recon_date"),  $request->input("bank_id"));

        return [
            'status' => 1,
            'totalAddCLRDeposit' => $totalAddCLRDeposit,
            'totalLessCLRDeposit' => $totalLessCLRDeposit
        ];
    }

    public function getMonthReconBak(Request $request)
    {
        $current_user = auth()->user();


        $voucher = DB::table('voucher_main as m')
            ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date')
            ->where('m.status', '<>', 99)
            ->where('m.account_approval', '=', 1)
            // ->where('d.is_recon', '=', 1)
            ->where('m.is_recon', '=', 1)
            ->whereMonth('m.recon_date', $request->input("month"))
            ->whereYear('m.recon_date', $request->input("year"))
            // ->whereYear('m.recon_date', $request->input("recon_date"))
            ->where('m.office_account_id', '=', $request->input("bank_id"))->get();



        // return $voucher;


        $totalAddCLRDeposit = 0;
        $totalLessCLRDeposit = 0;

        if (Count($voucher) > 0) {
            for ($i = 0; $i < count($voucher); $i++) {

                if (in_array($voucher[$i]->voucher_type, array(1, 2))) {
                    $totalLessCLRDeposit += $voucher[$i]->detail_amt;
                } else if (in_array($voucher[$i]->voucher_type, array(3, 4))) {
                    $totalAddCLRDeposit += $voucher[$i]->detail_amt;
                }
            }

            // return $totalAddCLRDeposit;


            $totalAddCLRDeposit = number_format((float)$totalAddCLRDeposit, 2, '.', '');
            $totalLessCLRDeposit = number_format((float)$totalLessCLRDeposit, 2, '.', '');



            $this->updateBankReconRecord($request,  $totalAddCLRDeposit, $totalLessCLRDeposit);
        }

        return [
            'status' => 1,
            'totalAddCLRDeposit' => $totalAddCLRDeposit,
            'totalLessCLRDeposit' => $totalLessCLRDeposit
        ];
    }

    public static function updateBankReconRecord($recon_date,  $bank_id)
    {

        $d = date_parse_from_format("Y-m-d", $recon_date);

        // $totalAddCLRDeposit = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     ->whereIn('m.type', ['RECONADD', 'JOURNALIN', 'TRANSFERINRECON', 'SSTINRECON', 'CLOSEFILEIN'])
        //     ->where('m.bank_id', '=',  $bank_id)->sum('amount');

            $totalAddCLRDeposit = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.recon_date', $d["month"])
            ->whereYear('m.recon_date', $d["year"])
            ->whereIn('m.type', ['BILL_RECV', 'TRUST_RECV', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.is_recon', 1)
            ->where('m.bank_id', '=',  $bank_id)->sum('amount');


        // $totalLessCLRDeposit = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     ->whereIn('m.type',  ['RECONLESS', 'TRANSFEROUTRECON', 'SSTOUTRECON', 'JOURNALOUTRECON', 'CLOSEFILEOUT'])
        //     ->where('m.bank_id', '=',  $bank_id)->sum('amount');

        $totalLessCLRDeposit = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.recon_date', $d["month"])
            ->whereYear('m.recon_date', $d["year"])
            ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB', 'REIMB_OUT', 'REIMB_SST_OUT'])
            ->where('m.is_recon', 1)
            ->where('m.bank_id', '=',  $bank_id)->sum('amount');

            // return  $bank_id;

        $BankReconRecord = BankReconRecord::where('bank_account_id', $bank_id)
            ->whereMonth('recon_date', $d["month"])
            ->whereYear('recon_date', $d["year"])
            ->first();

        if (!$BankReconRecord) {
            $BankReconRecord  = new BankReconRecord();

            $BankReconRecord->bank_account_id = $bank_id;
            $BankReconRecord->recon_date = $recon_date;
            $BankReconRecord->status = 1;
            $BankReconRecord->created_at = date('Y-m-d H:i:s');
        } else {
            $BankReconRecord->bank_account_id = $bank_id;
            $BankReconRecord->updated_at = date('Y-m-d H:i:s');
        }

        // return $totalLessCLRDeposit;

        $BankReconRecord->in_amt = $totalAddCLRDeposit;
        $BankReconRecord->out_amt = $totalLessCLRDeposit;

        $BankReconRecord->save();
    }

    public function updateBankReconRecordBak(Request $request,  $totalAddCLRDeposit, $totalLessCLRDeposit)
    {
        // $BankReconRecord = BankReconRecord::where('recon_date', '=', $request->input("recon_date"))->first();

        $d = getdate();
        $BankReconRecord = BankReconRecord::whereMonth('recon_date', $d["month"])
            ->whereYear('recon_date', $d["year"])
            ->first();

        // return $BankReconRecord;

        if (!$BankReconRecord) {
            $BankReconRecord  = new BankReconRecord();

            $BankReconRecord->bank_account_id = $request->input("bank_id");
            $BankReconRecord->recon_date = $request->input("recon_date");
            $BankReconRecord->status = 1;
            $BankReconRecord->created_at = date('Y-m-d H:i:s');
        } else {
            $BankReconRecord->bank_account_id = $request->input("bank_id");
            $BankReconRecord->updated_at = date('Y-m-d H:i:s');
        }


        $BankReconRecord->in_amt = $totalAddCLRDeposit;
        $BankReconRecord->out_amt = $totalLessCLRDeposit;

        $BankReconRecord->save();
    }


    // Journal Entry

    public function journalEntryList()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::JournalEntryPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // if (!in_array($current_user->menuroles, ['admin', 'account', 'maker', 'sales'])) {
        //     if (!in_array($current_user->id, [32, 51])) {
        //         return redirect()->route('dashboard.index');
        //     }
        // }

      
        if (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id, [32, 51])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6])->get();
            }
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 2) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 2)->get();
            }else if (in_array($current_user->branch_id, [5,6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5,6])->get();
            }
        }

        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.journal-entry.index', ['OfficeBankAccount' => $OfficeBankAccount, 'Branchs' => $branchInfo['branch']]);
    }

    public function getJournalEntryList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            // $TransferFeeMain = TransferFeeMain::where('status', '=', 1)->get();


            $TransferFeeMain = DB::table('journal_entry_main as m')
                ->leftJoin('users as u', 'u.id', '=', 'm.created_by')
                ->leftJoin('office_bank_account as o', 'o.id', '=', 'm.bank_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
                ->select('m.*', 'l.case_ref_no', 'u.name as user_name', 'o.short_code', 'o.account_no')
                ->where('m.status', '<>',  99);

            if ($request->input("trx_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.transaction_id', $request->input("trx_id"));
            } else {
                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                    $TransferFeeMain = $TransferFeeMain->whereBetween('m.date', [$request->input("date_from"), $request->input("date_to")]);
                } else {
                    if ($request->input("date_from") <> null) {
                        $TransferFeeMain = $TransferFeeMain->where('m.date', '>=', $request->input("date_from"));
                    }

                    if ($request->input("date_to") <> null) {
                        $TransferFeeMain = $TransferFeeMain->where('m.date', '<=', $request->input("date_to"));
                    }
                }
            }

            if ($request->input("bank_account")) {
                $TransferFeeMain = $TransferFeeMain->where('m.bank_id', $request->input("bank_account"));
            }

            if ($request->input("branch_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.branch_id', $request->input("branch_id"));
            }


            if (in_array($current_user->menuroles, ['maker'])) {
                if ($current_user->branch_id == 3) {
                    $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  3);
                } else if ($current_user->branch_id == 2) {
                    $TransferFeeMain =  $TransferFeeMain->where('m.branch_id', '=',  2);
                }
                else if (in_array($current_user->branch_id,[5,6])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [5,6]);
                }
                else if (in_array($current_user->branch_id,[7])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [7]);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [32, 51])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [5, 6]);
                }
            }else if (in_array($current_user->menuroles, ['lawyer'])) {
                $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [$current_user->branch_id]);
            }


            $TransferFeeMain = $TransferFeeMain->OrderBy('created_at', 'desc')->get();


            return DataTables::of($TransferFeeMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    <a href="/journal-entry/' . $data->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-pencil"></i></a>
                    ';

                    $actionBtn = '<div class="btn-group">
                    <button type="button" class="btn btn-info btn-flat">Action</button>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                    <a class="dropdown-item"  href="/journal-entry/' . $data->id . '"  ><i style="margin-right: 10px;" class="cil-pencil"></i>Edit</a>
                     </div>
                  </div>';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    return '<a target="_blank"  href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bank_account', function ($data) {

                    return '<div>' . $data->short_code . '<br/>' . $data->account_no . '</div>';
                })
                ->editColumn('is_recon', function ($data) {
                    if ($data->is_recon == '1' || $data->is_recon == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->rawColumns(['action', 'case_ref_no', 'bank_account', 'is_recon'])
                ->make(true);
        }
    }

    public function journalEntryView($id)
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::JournalEntryPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $JournalEntryMain = DB::table('journal_entry_main as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->select('m.*', 'l.case_ref_no')
            ->where('m.id', '=', $id)->first();

        $JournalEntryDetails = DB::table('journal_entry_details as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->select('m.*', 'l.case_ref_no')
            ->where('journal_entry_main_id', '=', $id)->get();

        // $JournalEntryDetails = JournalEntryDetails::where('journal_entry_main_id', '=', $id)->get();
        $LoanCase = LoanCase::get();

        $AccountCode = AccountCode::where('status', '=', 1)->get();

        // if (in_array($current_user->menuroles, ['maker'])) {
        //     if ($current_user->branch_id == 3) {
        //         $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
        //     } else if ($current_user->branch_id == 2) {
        //         $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 2)->get();
        //     } else if (in_array($current_user->branch_id, [5,6])) {
        //         $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5,6])->get();
        //     }
        // } else {
        //     $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        // }

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5,6])->get();
            } else{
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        // $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.journal-entry.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'JournalEntryMain' => $JournalEntryMain,
            'JournalEntryDetails' => $JournalEntryDetails,
            'Branchs' => $branchInfo['branch'],
            'loan_case' => $LoanCase,
            'AccountCode' => $AccountCode
        ]);
    }

    public function journalEntryCreate()
    {
        $current_user = auth()->user();

        // if (!in_array($current_user->menuroles, ['admin', 'account', 'maker', 'sales'])) {
        //     if (!in_array($current_user->id, [32, 51])) {
        //         return redirect()->route('dashboard.index');
        //     }
        // }

        if (AccessController::UserAccessPermissionController(PermissionController::JournalEntryPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();

        $branchInfo = BranchController::manageBranchAccess();

        $AccountCode = AccountCode::where('status', '=', 1)->get();


        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5,6])->get();
            } else{
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $LoanCase = LoanCase::get();

        return view('dashboard.journal-entry.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch'],
            'loan_case' => $LoanCase,
            'AccountCode' => $AccountCode
        ]);
    }

    public function saveJournalEntry(Request $request)
    {
        $entriesList = [];
        $current_user = auth()->user();
        $logNote = '';
        $total_debit = 0;
        $total_credit = 0;

        if ($request->input('entries_list') != null) {
            $entriesList = json_decode($request->input('entries_list'), true);
        }

        if (count($entriesList) <= 0) {
            return response()->json(['status' => 2, 'message' => 'No Entries']);
        }

        // return $entriesList;

        $Parameter = Parameter::where('parameter_type', '=', 'jv_running_no')->first();
        $jv_running_no = $Parameter->parameter_value_2 . $Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();

        $JournalEntryMain = new JournalEntryMain();

        $JournalEntryMain->name = $request->input('name');
        $JournalEntryMain->remarks = $request->input('desc');
        $JournalEntryMain->transaction_id = $request->input('trx_id');
        $JournalEntryMain->case_id = $request->input('case_id');
        $JournalEntryMain->bank_id = $request->input('bank_account');
        $JournalEntryMain->branch_id = $request->input('branch_id');
        $JournalEntryMain->journal_no = $jv_running_no;
        $JournalEntryMain->date = $request->input('date');
        $JournalEntryMain->created_by = $current_user->id;

        $JournalEntryMain->save();

        if ($JournalEntryMain) {

            for ($i = 0; $i < count($entriesList); $i++) {
                $JournalEntryDetails = new JournalEntryDetails();

                $transaction_type = '';
                $type = '';
                $type_v2 = '';
                $amount = 0;

                if ($entriesList[$i]['debit'] > 0) {
                    $transaction_type = 'D';
                    $amount = $entriesList[$i]['debit'];
                    $type = 'JOURNALIN';
                    $type_v2 = 'JOURNAL_IN';
                    $total_debit += $entriesList[$i]['debit'] + $entriesList[$i]['sst_amount'];
                } else if ($entriesList[$i]['credit'] > 0) {
                    $transaction_type = 'C';
                    $amount = $entriesList[$i]['credit'];
                    $type = 'JOURNALOUT';
                    $type_v2 = 'JOURNAL_OUT';
                    $total_credit += $entriesList[$i]['credit'] + $entriesList[$i]['sst_amount'];
                }

                $JournalEntryDetails->journal_entry_main_id = $JournalEntryMain->id;
                $JournalEntryDetails->remarks = $entriesList[$i]['desc'];
                $JournalEntryDetails->account_code_id = $entriesList[$i]['account_code_id'];
                $JournalEntryDetails->case_id = $entriesList[$i]['case_id'];
                $JournalEntryDetails->amount = $amount;
                $JournalEntryDetails->sst_amount = $entriesList[$i]['sst_amount'];
                $JournalEntryDetails->transaction_type = $transaction_type;

                $JournalEntryDetails->save();

                $logNote .= '<br/>' . $JournalEntryDetails->remarks . ': ' . number_format((float)($JournalEntryDetails->amount + $JournalEntryDetails->sst_amount), 2, '.', ',');

                // $LedgerEntries = new LedgerEntries();

                // $LedgerEntries->transaction_id = $JournalEntryMain->transaction_id;
                // $LedgerEntries->case_id = $JournalEntryMain->case_id;
                // $LedgerEntries->loan_case_main_bill_id = 0;
                // $LedgerEntries->user_id = $JournalEntryMain->created_by;
                // $LedgerEntries->key_id = $JournalEntryMain->id;
                // $LedgerEntries->key_id_2 = $JournalEntryDetails->id;
                // $LedgerEntries->key_id_3 = $entriesList[$i]['account_code_id'];
                // $LedgerEntries->cheque_no = $JournalEntryMain->journal_no;
                // $LedgerEntries->transaction_type = $JournalEntryDetails->transaction_type;
                // $LedgerEntries->amount = $JournalEntryDetails->amount + $JournalEntryDetails->sst_amount;
                // $LedgerEntries->bank_id = $request->input('bank_account');
                // $LedgerEntries->remark = $JournalEntryMain->remarks;
                // $LedgerEntries->payee = $JournalEntryMain->name;
                // $LedgerEntries->sys_desc = $JournalEntryDetails->remarks;
                // $LedgerEntries->status = 1;
                // $LedgerEntries->created_at = date('Y-m-d H:i:s');
                // $LedgerEntries->date = $JournalEntryMain->date;
                // $LedgerEntries->type = $type;
                // $LedgerEntries->save();

                $OfficeBankAccount = OfficeBankAccount::where('account_code', $entriesList[$i]['account_code_id'])->first();

                if ($OfficeBankAccount)
                {
                    $d = date_parse_from_format("Y-m-d", $request->input('date'));
                    $recon_date =  $this->getLastDayForBankRecon($d["month"], $d["year"], $OfficeBankAccount->id);
                    $this->updateBankReconRecord($recon_date, $OfficeBankAccount->id);
                }


                $LedgerEntries = new LedgerEntriesV2();

                $LedgerEntries->transaction_id = $JournalEntryMain->transaction_id;
                $LedgerEntries->case_id = $entriesList[$i]['case_id'];
                $LedgerEntries->loan_case_main_bill_id = 0;
                $LedgerEntries->user_id = $JournalEntryMain->created_by;
                $LedgerEntries->key_id = $JournalEntryMain->id;
                $LedgerEntries->key_id_2 = $JournalEntryDetails->id;
                $LedgerEntries->key_id_3 = $entriesList[$i]['account_code_id'];
                $LedgerEntries->cheque_no = $JournalEntryMain->journal_no;
                $LedgerEntries->transaction_type = $JournalEntryDetails->transaction_type;
                $LedgerEntries->amount = $JournalEntryDetails->amount + $JournalEntryDetails->sst_amount;

                if ($OfficeBankAccount)
                {
                    $LedgerEntries->bank_id = $OfficeBankAccount->id;
                }

                
                $LedgerEntries->remark = $JournalEntryMain->remarks;
                $LedgerEntries->payee = $JournalEntryMain->name;
                $LedgerEntries->desc_1 = $JournalEntryDetails->remarks;
                $LedgerEntries->status = 1;
                $LedgerEntries->is_recon = 0;
                $LedgerEntries->created_at = date('Y-m-d H:i:s');
                $LedgerEntries->date = $JournalEntryMain->date;
                $LedgerEntries->type = $type_v2;
                $LedgerEntries->save();

                $LoanCase = LoanCase::where('id', $entriesList[$i]['case_id'])->first();
                CaseController::adminUpdateClientLedger($LoanCase);

                

            }

            $JournalEntryMain->total_debit = $total_debit;
            $JournalEntryMain->total_credit = $total_credit;

            $JournalEntryMain->save();

            // $d = date_parse_from_format("Y-m-d", $request->input('date'));
            // $recon_date =  $this->getLastDayForBankRecon($d["month"], $d["year"], $request->input('bank_account'));
            // $this->updateBankReconRecord($recon_date,  $request->input("bank_account"));

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $JournalEntryMain->case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'create_journal';
            $AccountLog->desc = $current_user->name . ' created journal(' . $JournalEntryMain->journal_no . ')' . $logNote;
            $AccountLog->status = 1;
            $AccountLog->object_id = $JournalEntryMain->id;
            $AccountLog->object_id_2 = $JournalEntryDetails->id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        }

        return response()->json(['status' => 1, 'message' => 'Journal entriy created']);
    }

    public function updateJournalEntry(Request $request, $id)
    {
        // Add cache lock to prevent concurrent updates
        $lockKey = "journal_entry_update_{$id}";
        $lock = Cache::lock($lockKey, 30); // 30 second lock
        
        if (!$lock->get()) {
            return response()->json(['status' => 2, 'message' => 'Another update is in progress. Please wait a moment and try again.']);
        }
        
        try {
            DB::beginTransaction();
            
            $entriesList = [];
            $current_user = auth()->user();
            $logNote = '';
            $prevDate = '';
            $prevBankID = 0;
            $total_debit = 0;
            $total_credit = 0;

            $d = date_parse_from_format("Y-m-d", $request->input('date'));

            // return $request->input('date');



            if ($request->input('entries_list') != null) {
                $entriesList = json_decode($request->input('entries_list'), true);
            }

            if (count($entriesList) <= 0) {
                DB::rollBack();
                $lock->release();
                return response()->json(['status' => 2, 'message' => 'No Entries']);
            }

            // Remove duplicates from entries_list before processing
            $uniqueEntries = [];
            $seenEntries = [];
            foreach ($entriesList as $entry) {
                $entryKey = md5(json_encode([
                    'account_code_id' => $entry['account_code_id'] ?? '',
                    'desc' => $entry['desc'] ?? '',
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'case_id' => $entry['case_id'] ?? '',
                    'sst_amount' => $entry['sst_amount'] ?? 0,
                ]));
                
                if (!isset($seenEntries[$entryKey])) {
                    $seenEntries[$entryKey] = true;
                    $uniqueEntries[] = $entry;
                }
            }
            $entriesList = $uniqueEntries;
            
            if (count($entriesList) <= 0) {
                DB::rollBack();
                $lock->release();
                return response()->json(['status' => 2, 'message' => 'No valid entries after duplicate removal']);
            }

            $JournalEntryMain = JournalEntryMain::where('id', '=', $id)->first();

            if (!$JournalEntryMain) {
                DB::rollBack();
                $lock->release();
                return response()->json(['status' => 2, 'message' => 'Record not exits']);
            }

            if ($JournalEntryMain->bank_id !=  $request->input('bank_account') || $JournalEntryMain->date != $request->input('date')) {
                $prevDate = $JournalEntryMain->date;
                $prevBankID = $JournalEntryMain->bank_id;
            }

            // if ($prevDate != '' || $prevBankID != 0)
            // {
            //     $this->updateBankReconRecord($prevDate,  $prevBankID);
            //     return 1;
            // }

            // return    $recon_date =  $this->getLastDayForBankRecon($d["month"], $d["year"], $request->input('bank_account'));

            $JournalEntryMain->name = $request->input('name');
            $JournalEntryMain->remarks = $request->input('desc');
            $JournalEntryMain->transaction_id = $request->input('trx_id');
            // $JournalEntryMain->case_id = $request->input('case_id');
            $JournalEntryMain->bank_id = $request->input('bank_account');
            $JournalEntryMain->date = $request->input('date');
            $JournalEntryMain->branch_id = $request->input('branch_id');
            $JournalEntryMain->updated_by = $current_user->id;

            $JournalEntryMain->save();

            if ($JournalEntryMain) {

            // Delete existing entries
            $deletedDetails = JournalEntryDetails::where('journal_entry_main_id', '=', $id)->delete();
            $deletedLedger = LedgerEntries::where('cheque_no', '=', $JournalEntryMain->journal_no)->delete();
            $deletedLedgerV2 = LedgerEntriesV2::where('key_id', $id)->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])->delete();
            
            // Log deletions for debugging
            Log::info("Journal Entry Update #{$id} - Deleted: Details={$deletedDetails}, Ledger={$deletedLedger}, LedgerV2={$deletedLedgerV2}");
            
            // Verify deletions were successful
            $remainingDetails = JournalEntryDetails::where('journal_entry_main_id', '=', $id)->count();
            $remainingLedgerV2 = LedgerEntriesV2::where('key_id', $id)
                ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
                ->count();
            
            if ($remainingDetails > 0 || $remainingLedgerV2 > 0) {
                Log::warning("Journal Entry Update #{$id} - Found remaining entries: Details={$remainingDetails}, LedgerV2={$remainingLedgerV2}. Attempting force delete.");
                // Force delete any remaining entries
                JournalEntryDetails::where('journal_entry_main_id', '=', $id)->forceDelete();
                LedgerEntriesV2::where('key_id', $id)
                    ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
                    ->forceDelete();
            }


            for ($i = 0; $i < count($entriesList); $i++) {
                $JournalEntryDetails = new JournalEntryDetails();

                $transaction_type = '';
                $amount = 0;

                if ($entriesList[$i]['debit'] > 0) {
                    $transaction_type = 'D';
                    $amount = $entriesList[$i]['debit'];
                    $type = 'JOURNALIN';
                    $type_v2 = 'JOURNAL_IN';
                    $total_debit += $entriesList[$i]['debit'] + $entriesList[$i]['sst_amount'];
                } else if ($entriesList[$i]['credit'] > 0) {
                    $transaction_type = 'C';
                    $amount = $entriesList[$i]['credit'];
                    $type = 'JOURNALOUT';
                    $type_v2 = 'JOURNAL_OUT';
                    $total_credit += $entriesList[$i]['credit'] + $entriesList[$i]['sst_amount'];
                }

                $JournalEntryDetails->journal_entry_main_id = $JournalEntryMain->id;
                $JournalEntryDetails->remarks = $entriesList[$i]['desc'];
                $JournalEntryDetails->account_code_id = $entriesList[$i]['account_code_id'];
                $JournalEntryDetails->amount = $amount;
                $JournalEntryDetails->case_id = $entriesList[$i]['case_id'];
                $JournalEntryDetails->sst_amount = $entriesList[$i]['sst_amount'];
                $JournalEntryDetails->transaction_type = $transaction_type;

                $JournalEntryDetails->save();

                $logNote .= '<br/>' . $JournalEntryDetails->remarks . ': ' . number_format((float)($JournalEntryDetails->amount + $JournalEntryDetails->sst_amount), 2, '.', ',');

                $OfficeBankAccount = OfficeBankAccount::where('account_code', $entriesList[$i]['account_code_id'])->first();

                if ($OfficeBankAccount)
                {
                    $d = date_parse_from_format("Y-m-d", $request->input('date'));
                    $recon_date =  $this->getLastDayForBankRecon($d["month"], $d["year"], $OfficeBankAccount->id);
                    $this->updateBankReconRecord($recon_date, $OfficeBankAccount->id);
                }


                $LedgerEntries = new LedgerEntries();

                $LedgerEntries->transaction_id = $JournalEntryMain->transaction_id;
                $LedgerEntries->case_id = $entriesList[$i]['case_id'];
                $LedgerEntries->loan_case_main_bill_id = 0;
                $LedgerEntries->user_id = $JournalEntryMain->created_by;
                $LedgerEntries->key_id = $JournalEntryMain->id;
                $LedgerEntries->key_id_2 = $JournalEntryDetails->id;
                $LedgerEntries->key_id_3 = $entriesList[$i]['account_code_id'];
                $LedgerEntries->cheque_no = $JournalEntryMain->journal_no;
                $LedgerEntries->transaction_type = $JournalEntryDetails->transaction_type;
                $LedgerEntries->amount = $JournalEntryDetails->amount + $JournalEntryDetails->sst_amount;
                $LedgerEntries->bank_id = $request->input('bank_account');
                $LedgerEntries->remark = $JournalEntryMain->remarks;
                $LedgerEntries->payee = $JournalEntryMain->name;
                $LedgerEntries->sys_desc = $JournalEntryDetails->remarks;
                $LedgerEntries->status = 1;
                $LedgerEntries->created_at = date('Y-m-d H:i:s');
                $LedgerEntries->date = $JournalEntryMain->date;
                $LedgerEntries->type = $type;
                $LedgerEntries->save();

                // Check if LedgerEntriesV2 already exists before creating (prevent duplicates)
                $existingLedgerV2 = LedgerEntriesV2::where('key_id', $JournalEntryMain->id)
                    ->where('key_id_2', $JournalEntryDetails->id)
                    ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
                    ->first();
                
                if (!$existingLedgerV2) {
                    $LedgerEntries = new LedgerEntriesV2();

                    $LedgerEntries->transaction_id = $JournalEntryMain->transaction_id;
                    $LedgerEntries->case_id = $entriesList[$i]['case_id'];
                    $LedgerEntries->loan_case_main_bill_id = 0;
                    $LedgerEntries->cheque_no = $JournalEntryMain->journal_no;
                    $LedgerEntries->user_id = $JournalEntryMain->created_by;
                    $LedgerEntries->key_id =  $JournalEntryMain->id;
                    $LedgerEntries->key_id_2 = $JournalEntryDetails->id;
                    $LedgerEntries->key_id_3 = $entriesList[$i]['account_code_id'];
                    $LedgerEntries->transaction_type = $JournalEntryDetails->transaction_type;
                    $LedgerEntries->amount = $JournalEntryDetails->amount + $JournalEntryDetails->sst_amount;

                    if ($OfficeBankAccount)
                    {
                        $LedgerEntries->bank_id = $OfficeBankAccount->id;
                    }
                    // $LedgerEntries->bank_id = $request->input('bank_account');
                    // $LedgerEntries->remark = $ledgers[$j]->remark;
                    $LedgerEntries->payee = $JournalEntryMain->name;
                    $LedgerEntries->remark = $JournalEntryMain->remarks;
                    $LedgerEntries->desc_1 = $JournalEntryDetails->remarks;
                    // $LedgerEntries->desc_2 = $ledgers[$j]->remark;
                    $LedgerEntries->status = 1;
                    $LedgerEntries->is_recon = 0;
                    $LedgerEntries->created_at = date('Y-m-d H:i:s');
                    $LedgerEntries->date = $JournalEntryMain->date;
                    $LedgerEntries->type =  $type_v2;
                    $LedgerEntries->save();
                } else {
                    Log::warning("Journal Entry Update #{$id} - Skipped duplicate LedgerEntriesV2 for detail_id={$JournalEntryDetails->id}, ledger_id={$existingLedgerV2->id}");
                }


                $LoanCase = LoanCase::where('id', $entriesList[$i]['case_id'])->first();
                CaseController::adminUpdateClientLedger($LoanCase);
            }

            $JournalEntryMain->total_debit = $total_debit;
            $JournalEntryMain->total_credit = $total_credit;

            $JournalEntryMain->save();

            if ($prevDate != '' || $prevBankID != 0) {
                $this->updateBankReconRecord($prevDate,  $prevBankID);
            }

            $d = date_parse_from_format("Y-m-d", $request->input('date'));
            $recon_date =  $this->getLastDayForBankRecon($d["month"], $d["year"], $request->input('bank_account'));

            $this->updateBankReconRecord($recon_date,  $request->input("bank_account"));

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $JournalEntryMain->case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'update_journal';
            $AccountLog->desc = $current_user->name . ' updated journal(' . $JournalEntryMain->journal_no . ')' . $logNote;
            $AccountLog->status = 1;
            $AccountLog->object_id = $JournalEntryMain->id;
            $AccountLog->object_id_2 = $JournalEntryDetails->id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
            }
            
            DB::commit();
            $lock->release();
            
            return response()->json(['status' => 1, 'message' => 'Journal entry updated successfully']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $lock->release();
            Log::error("Journal Entry Update Error #{$id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['status' => 2, 'message' => 'Error updating journal entry: ' . $e->getMessage()]);
        }
    }

    public function lockJournal(Request $request, $id)
    {

        $JournalEntryMain = JournalEntryMain::where('id', '=', $id)->first();

        JournalEntryDetails::where('journal_entry_main_id', $id)->update(['is_recon' => 1]);
        LedgerEntries::where('key_id', $id)->whereIn('type', ['JOURNALIN'])->update(['type' => 'JOURNALINRECON']);
        LedgerEntries::where('key_id', $id)->whereIn('type', ['JOURNALOUT'])->update(['type' => 'JOURNALOUTRECON']);

        if ($JournalEntryMain) {
            $JournalEntryMain->is_recon = 1;
            $JournalEntryMain->save();

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $JournalEntryMain->case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'recon_journal';
            $AccountLog->desc = $current_user->name . ' reconciled journal(' . $JournalEntryMain->journal_no . ')';
            $AccountLog->status = 1;
            $AccountLog->object_id = $JournalEntryMain->id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => 1, 'message' => 'Journal reconciled']);
        }
    }

    public function unlockJournal(Request $request, $id)
    {
        $JournalEntryMain = JournalEntryMain::where('id', '=', $id)->first();

        if ($JournalEntryMain) {
            $JournalEntryMain->is_recon = 0;
            $JournalEntryMain->save();

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $JournalEntryMain->case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'unlock_journal';
            $AccountLog->desc = $current_user->name . ' unlocked journal(' . $JournalEntryMain->journal_no . ')';
            $AccountLog->status = 1;
            $AccountLog->object_id = $JournalEntryMain->id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => 1, 'message' => 'Journal unlocked']);
        }
    }

    public function deleteJournal(Request $request, $id)
    {
        $JournalEntryMain = JournalEntryMain::where('id', '=', $id)->first();

        if ($JournalEntryMain) {
            JournalEntryDetails::where('journal_entry_main_id', '=', $id)->delete();
            LedgerEntries::where('cheque_no', '=', $JournalEntryMain->journal_no)->delete();
            LedgerEntriesV2::where('key_id', $id)->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])->delete();

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $JournalEntryMain->case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'delete_journal';
            $AccountLog->desc = $current_user->name . ' deleted journal(' . $JournalEntryMain->journal_no . ')';
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            $this->updateBankReconRecord($JournalEntryMain->date,  $JournalEntryMain->bank_id);

            $JournalEntryMain->delete();

            return response()->json(['status' => 1, 'message' => 'Journal deleted']);
        }
    }


    public function clientLedger()
    {
        $current_user = auth()->user();
        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $rows = [];
        $case_receive = [];

        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::ClientAccountBalancePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $OfficeBankAccount = $this->getOfficeBankAccount();

        return view('dashboard.account.client-ledger', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'rows' => $rows,
            'case_receive' => $case_receive,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function getClientLedgerBaak3(Request $request)
    {
        $status = $request->input("status");

        $rows = DB::table('loan_case as a');

        if ($request->input("status") != '')
        {
            $rows = $rows->where('a.status', $request->input("status"));
        }
        else
        {
            $rows = $rows->where('a.status', '<>',99);
        }

        $end = new Carbon('last day of last month');

        $last_day = Carbon::create($request->input("year"), $request->input("mon"))->lastOfMonth()->format('Y-m-d');


        if ($request->input("branch_id")) {
            $rows = $rows->where('a.branch_id', '=',  $request->input("branch_id"));
            
        }

        // $rows = $rows->where('a.id', 13);    
        $rows = $rows->orderBy('a.created_at', 'asc')->pluck('id')->toArray();

        $total_credit = 0;
        $total_debit = 0;

        $credit = DB::table('ledger_entries_v2 as a')
        ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
        ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
        ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
        ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
        ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
        ->whereIn('e.id', [$rows])
        ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', '',''])
        // ->whereIn('a.type', ['BILL_RECV', 'TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', '', 'JOURNAL_OUT','JOURNAL_IN', 'SST_IN'])
        // ->whereIn('a.type', ['TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', 'TRUST_DISB', 'JOURNAL_OUT'])
        ->where('a.transaction_type', 'C')
        ->where('a.status', '<>',  99)
        ->where('a.date','<=',$last_day)
        ->orderBy('a.date', 'ASC')->get();

        return $credit;



        for ($i = 0; $i < count($rows); $i++)
        {
            $credit = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $rows[$i]->id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            // ->whereIn('a.type', ['BILL_RECV', 'TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', '', 'JOURNAL_OUT','JOURNAL_IN', 'SST_IN'])
            // ->whereIn('a.type', ['TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', 'TRUST_DISB', 'JOURNAL_OUT'])
            ->where('a.transaction_type', 'C')
            ->where('a.status', '<>',  99)
            ->where('a.date','<=',$last_day)
            ->orderBy('a.date', 'ASC');

            if ($request->input("bank_id")) {
                $credit = $credit->where('a.bank_id', '=',  $request->input("bank_id"));
            }

            $credit = $credit->sum('amount');
            // $credit = $credit->get();
            $total_credit += $credit;

            // return $credit;


        $debit = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $rows[$i]->id)
            
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            // ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN','CLOSEFILE_OUT'])
            // ->whereIn('a.type', ['BILL_RECV', 'TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', '', 'JOURNAL_OUT','JOURNAL_IN', ''])
            ->where('a.transaction_type', 'D')
            ->where('a.status', '<>',  99)
            ->where('a.date','<=',$last_day)
            ->orderBy('a.date', 'ASC');

            if ($request->input("bank_id")) {
                $debit = $debit->where('a.bank_id', '=',  $request->input("bank_id"));
            }


            
            $debit = $debit->sum('amount');

            $total_debit += $debit;
            $rows[$i]->amount_ledger =  $credit - $debit;
        }

        return $total_credit;

        $rows = $rows->filter(function($result) use($request){

            if($result->amount_ledger != 0)
            {
                return true;
            }
            else{
                return false;
            }

            
         });


        return response()->json([
            'view' => view('dashboard.account.table.tab-client-ledger', compact('rows'))->render(),
        ]);
    }

    public function getClientLedger(Request $request)
    {
        $status = $request->input("status");

        $rows = DB::table('loan_case as a');

        if ($request->input("status") != '')
        {
            $rows = $rows->where('a.status', $request->input("status"));
        }
        else
        {
            // $rows = $rows->where('a.status', '<>',99);
        }

        $end = new Carbon('last day of last month');

        $last_day = Carbon::create($request->input("year"), $request->input("mon"))->lastOfMonth()->format('Y-m-d');


        if ($request->input("branch_id")) {
            $rows = $rows->where('a.branch_id', '=',  $request->input("branch_id"));
            
        }

        // $rows = $rows->where('a.id', 1146);    
        $rows = $rows->orderBy('a.created_at', 'asc')->get();

        // Get CA (Client Account) bank account IDs, exclude OA (Office Account) - match case details ledger
        $clientBankAccountIds = DB::table('office_bank_account')
            ->where('status', '=', 1)
            ->where(function($query) {
                $query->where('account_type', '=', 'CA')
                      ->orWhereNull('account_type'); // Include entries with no account type or null
            })
            ->pluck('id')
            ->toArray();

        $total_credit = 0;
        $total_debit = 0;

        for ($i = 0; $i < count($rows); $i++)
        {
            // DEBUG: Log query for case 41201
            $is_debug_case = (isset($rows[$i]->case_ref_no) && $rows[$i]->case_ref_no == 'DP/JAN/NRN/OCBCi/41201/MAHBA/MUS') || (isset($rows[$i]->id) && $rows[$i]->id == 7298);
            
            $credit = DB::table('ledger_entries_v2 as a')
            ->where('a.case_id', '=',  $rows[$i]->id)
            // ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', '',''])
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            // ->whereIn('a.type', ['BILL_RECV', 'TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', '', 'JOURNAL_OUT','JOURNAL_IN', 'SST_IN'])
            // ->whereIn('a.type', ['TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', 'TRUST_DISB', 'JOURNAL_OUT'])
            ->where(function($query) use ($clientBankAccountIds) {
                // Exclude OA accounts - only include CA accounts or entries with no bank_id (match case details ledger)
                if (!empty($clientBankAccountIds)) {
                    $query->whereIn('a.bank_id', $clientBankAccountIds)
                          ->orWhereNull('a.bank_id');
                } else {
                    // If no CA accounts exist, exclude entries linked to OA accounts
                    $query->whereNull('a.bank_id')
                          ->orWhere(function($q) {
                              $q->whereNotNull('a.bank_id')
                                ->whereNotExists(function($subQuery) {
                                    $subQuery->select(DB::raw(1))
                                             ->from('office_bank_account as oba')
                                             ->whereColumn('oba.id', 'a.bank_id')
                                             ->where('oba.account_type', '=', 'OA');
                                });
                          });
                }
            })
            ->where('a.transaction_type', 'C')
            ->where('a.status', '<>',  99)
            // Date filter removed to match case details ledger - both should show all transactions for comparison
            // ->where('a.date','<=',$last_day)
            ->orderBy('a.date', 'ASC');

            // REMOVED: Bank filter should not affect balance calculation - balance should always show ALL CA banks to match case details ledger
            // if ($request->input("bank_id")) {
            //     $credit = $credit->where('a.bank_id', '=',  $request->input("bank_id"));
            // }

            if ($is_debug_case) {
                $credit_query = clone $credit;
                $credit_debug = $credit_query->get();
                Log::info('DEBUG Client Ledger Credit Query', [
                    'case_id' => $rows[$i]->id,
                    'case_ref_no' => $rows[$i]->case_ref_no,
                    'query_count' => count($credit_debug),
                    'sample_ids' => $credit_debug->take(5)->pluck('id')->toArray()
                ]);
            }
            
            $credit = $credit->sum('amount');
            // $credit = $credit->get();
            // $total_credit += $credit;

            // return $credit;


        $debit = DB::table('ledger_entries_v2 as a')
            ->where('a.case_id', '=',  $rows[$i]->id)
            // ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN','CLOSEFILE_OUT'])
            // ->whereIn('a.type', ['BILL_RECV', 'TRANSFER_OUT', 'SST_OUT', 'BILL_DISB', 'TRUST_DISB', '', 'JOURNAL_OUT','JOURNAL_IN', ''])
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            ->where(function($query) use ($clientBankAccountIds) {
                // Exclude OA accounts - only include CA accounts or entries with no bank_id (match case details ledger)
                if (!empty($clientBankAccountIds)) {
                    $query->whereIn('a.bank_id', $clientBankAccountIds)
                          ->orWhereNull('a.bank_id');
                } else {
                    // If no CA accounts exist, exclude entries linked to OA accounts
                    $query->whereNull('a.bank_id')
                          ->orWhere(function($q) {
                              $q->whereNotNull('a.bank_id')
                                ->whereNotExists(function($subQuery) {
                                    $subQuery->select(DB::raw(1))
                                             ->from('office_bank_account as oba')
                                             ->whereColumn('oba.id', 'a.bank_id')
                                             ->where('oba.account_type', '=', 'OA');
                                });
                          });
                }
            })
            ->where('a.transaction_type', 'D')
            ->where('a.status', '<>',  99)
            // Date filter removed to match case details ledger - both should show all transactions for comparison
            // ->where('a.date','<=',$last_day)
            ->orderBy('a.date', 'ASC');

            // REMOVED: Bank filter should not affect balance calculation - balance should always show ALL CA banks to match case details ledger
            // if ($request->input("bank_id")) {
            //     $debit = $debit->where('a.bank_id', '=',  $request->input("bank_id"));
            // }

            if ($is_debug_case) {
                $debit_query = clone $debit;
                $debit_debug = $debit_query->get();
                Log::info('DEBUG Client Ledger Debit Query', [
                    'case_id' => $rows[$i]->id,
                    'case_ref_no' => $rows[$i]->case_ref_no,
                    'query_count' => count($debit_debug),
                    'sample_ids' => $debit_debug->take(5)->pluck('id')->toArray()
                ]);
            }
            
            $debit = $debit->sum('amount');

            // $total_debit += $debit;
            $rows[$i]->amount_ledger =  $credit - $debit;
            
            // DEBUG: Log calculation for case 41201 or case ID 7298
            if ($is_debug_case) {
                Log::info('DEBUG Client Ledger Final Calculation', [
                    'case_id' => $rows[$i]->id,
                    'case_ref_no' => $rows[$i]->case_ref_no,
                    'credit' => $credit,
                    'debit' => $debit,
                    'amount_ledger' => $rows[$i]->amount_ledger,
                    'calculation' => "$credit - $debit = " . ($credit - $debit)
                ]);
            }
        }

        // return $total_credit;

        $rows = $rows->filter(function($result){

            if($result->amount_ledger != 0)
            {
                return true;
            }
            else{
                return false;
            }

         });


        return response()->json([
            'view' => view('dashboard.account.table.tab-client-ledger', compact('rows'))->render(),
        ]);
    }

    public function getClientLedgerBak(Request $request)
    {
        $status = $request->input("status");

        $rows = DB::table('loan_case as a');

        if ($request->input("status") != '')
        {
            $rows = $rows->where('a.status', $request->input("status"));
        }
        else
        {
            $rows = $rows->where('a.status', '<>',99);
        }

        $end = new Carbon('last day of last month');

        $last_day = Carbon::create($request->input("year"), $request->input("mon"))->lastOfMonth()->format('Y-m-d');

        // if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
        //     $rows = $rows->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);
        // } else {
        //     if ($request->input("date_from") <> null) {
        //         $rows = $rows->where('a.created_at', '>=', $request->input("date_from"));
        //     }

        //     if ($request->input("date_to") <> null) {
        //         $rows = $rows->where('a.created_at', '<=', $request->input("date_to"));
        //     }
        // }

        if ($request->input("branch_id")) {
            $rows = $rows->where('a.branch_id', '=',  $request->input("branch_id"));
            
        }

        // if ($request->input("ref_no") != '')
        // {
        //     $rows = $rows->where('a.case_ref_no', 'like', '%'.$request->input("ref_no")).'%';
        // }

        $rows = $rows->orderBy('a.created_at', 'asc')->get();

        for ($i = 0; $i < count($rows); $i++)
        {
            $credit = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $rows[$i]->id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            ->where('a.transaction_type', 'C')
            ->where('a.status', '<>',  99)
            // ->whereMonth('a.date','<=','2')
            // ->whereYear('a.date','<=','2023')
            ->where('a.date','<=',$last_day)
            ->orderBy('a.date', 'ASC')
            ->sum('amount');


        $debit = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $rows[$i]->id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            ->where('a.transaction_type', 'D')
            ->where('a.status', '<>',  99)
            // ->whereMonth('a.date','<=','2')
            // ->whereYear('a.date','<=','2023')
            ->where('a.date','<=',$last_day)
            ->orderBy('a.date', 'ASC')
            ->sum('amount');

            
            $rows[$i]->amount_ledger =  $credit - $debit;
            // $rows2 = $rows[$i];
            // $rows2->push($rows[$i]);
            
           
            // $loancase->client_ledger_amount = $credit - $debit;
        }

        $rows = $rows->filter(function($result){

            if($result->amount_ledger != 0)
            {
                return true;
            }
            else{
                return false;
            }
         });


        return response()->json([
            'view' => view('dashboard.account.table.tab-client-ledger', compact('rows'))->render(),
        ]);
    }

    public function getLastDayForBankRecon($month, $year, $bank_id)
    {

        $timestamp = mktime(0, 0, 0, $month, 1, $year);
        $lastDay = date("t", $timestamp);

        // if ($bank_id == 7) {
        //     $lastDay = '28';
        // }

        return $year . '-' . $month . '-' . $lastDay;
    }


    function view($id)
    {
        $account_template_cat = DB::table('voucher')
            ->join('loan_case', 'loan_case.id', '=', 'voucher.case_id')
            ->join('loan_case_account', 'loan_case_account.id', '=', 'voucher.account_details_id')
            ->join('users', 'users.id', '=', 'voucher.user_id')
            ->select('voucher.*', 'loan_case.case_ref_no', 'loan_case_account.item_name', 'users.name')
            ->where('voucher.id', '=', $id)
            ->first();

        return response()->json(['status' => 1, 'data' => $account_template_cat]);
    }

    public function create()
    {
        $account_category = AccountCategory::all();

        return view('dashboard.account.create', [
            'account_category' => $account_category
        ]);
    }

    public function store(Request $request)
    {

        $account  = new Account();

        $account->code = $request->input('code');
        $account->name = $request->input('name');
        $account->account_category_id = $request->input('account_category_id');
        $account->approval = $request->input('approval');
        $account->remark = $request->input('remark');
        $account->status =  $request->input('status');
        $account->created_at = date('Y-m-d H:i:s');

        $account->save();

        $request->session()->flash('message', 'Successfully created new account');
        return redirect()->route('account.index');
    }

    public function edit($id)
    {
        $account = Account::where('id', '=', $id)->first();
        $account_category = AccountCategory::where('status', '=', 1)->get();

        return view('dashboard.account.edit', [
            'account_category' => $account_category,
            'account' => $account
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $docTemplateDetailSelected = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->where('status', '=', 1)->get();
        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->get();

        $current_user = auth()->user();

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.documentTemplate.show', [
            'template' => DocumentTemplateMain::where('id', '=', $id)->first(),
            'docTemplatePages' => $docTemplatePages,
            'docTemplateDetail' => $docTemplateDetail,
            'docTemplateMain' => $docTemplateMain,
            'caseMasterListField' => $caseMasterListField,
            'caseMasterListCategory' => $caseMasterListCategory
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        $status = 1;
        $message = '';
        $current_user = auth()->user();

        try {
            $voucher = Voucher::where('id', '=',  $request->input('voucher_id'))->first();

            $voucher->remark = $request->input('remarks');
            $voucher->status = $request->input('status');
            $voucher->approval_id = $current_user->id;
            $voucher->updated_at = date('Y-m-d H:i:s');
            $voucher->save();
            $message = 'Voucher approved';

            // if voucher rejected
            if ($request->input('status') == 2) {
                $loanCaseAccount = LoanCaseAccount::where('id', '=', $voucher->account_details_id)->first();
                $loanCaseAccount->amount = $loanCaseAccount->amount + $voucher->amount;
                $loanCaseAccount->updated_at = date('Y-m-d H:i:s');
                $loanCaseAccount->save();
                $message = 'Voucher rejected';

                $caseAccountTransaction = new CaseAccountTransaction();

                $caseAccountTransaction->case_id = $voucher->case_id;
                $caseAccountTransaction->account_details_id = $voucher->account_details_id;
                $caseAccountTransaction->debit = $voucher->amount;
                $caseAccountTransaction->credit = 0;
                $caseAccountTransaction->remark = 'rejected_knockoff';
                $caseAccountTransaction->status = 1;
                $caseAccountTransaction->created_at = date('Y-m-d H:i:s');
                $caseAccountTransaction->save();
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
    }

    public function testTransferList(Request $request)
    {
        // Simple test method to debug transfer_list data
        $data = [
            'request_all' => $request->all(),
            'transfer_list_raw' => $request->input('transfer_list'),
            'transfer_list_decoded' => json_decode($request->input('transfer_list'), true),
            'type' => $request->input('type')
        ];
        
        return response()->json($data);
    }

    public function getTransferFeeInvoiceData(Request $request)
    {
        if ($request->ajax()) {
            
            // Debug: Log the request data
            error_log("getTransferFeeInvoiceData - Request data: " . json_encode($request->all()));

            $current_user = auth()->user();

            // Updated to use loan_case_invoice_main as primary table
            $rows = DB::table('loan_case_invoice_main as i')
                ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select(
                    'i.id',
                    'i.invoice_no',
                    'i.invoice_date',
                    'i.pfee1_inv',
                    'i.pfee2_inv', 
                    'i.sst_inv',
                    'i.transferred_pfee_amt',
                    'i.transferred_sst_amt',
                    'i.payment_date',
                    'b.case_id', 
                    'b.invoice_branch_id',
                    'b.payment_receipt_date',
                    'l.case_ref_no', 
                    'c.name as client_name'
                )
                ->where('i.transferred_to_office_bank', '=',  0)
                ->where('i.status', '<>',  99)
                ->where('i.bln_invoice', '=',  1);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                
                // Debug: Log the transfer list
                error_log("getTransferFeeInvoiceData - transfer_list received: " . json_encode($transfer_list));
                
                // Extract just the IDs from the array of objects
                $invoice_ids = array_column($transfer_list, 'id');
                
                // Debug: Log the extracted IDs
                error_log("getTransferFeeInvoiceData - invoice_ids extracted: " . json_encode($invoice_ids));
                
                $rows = $rows->whereIn('i.id', $invoice_ids);
            } else {
                error_log("getTransferFeeInvoiceData - No transfer_list provided");
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if ($current_user->branch_id == 3) {
                    $rows = $rows->where('b.invoice_branch_id', '=',  3);
                }
            }

            $rows = $rows->orderBy('i.id', 'ASC')->get();

            // Transform the data to match what the frontend expects
            $transformed_data = $rows->map(function($row) {
                return [
                    'id' => $row->id,
                    'ref_no' => $row->client_name . ' - ' . $row->case_ref_no,
                    'invoice_no' => $row->invoice_no,
                    'invoice_date' => $row->invoice_date ? date('d/m/Y', strtotime($row->invoice_date)) : '',
                    'total_amount' => number_format($row->pfee1_inv + $row->pfee2_inv + $row->sst_inv, 2),
                    'collected_amount' => '0.00', // This might need to be calculated from another table
                    'professional_fee' => number_format($row->pfee1_inv + $row->pfee2_inv - $row->transferred_pfee_amt, 2),
                    'sst_amount' => number_format($row->sst_inv - $row->transferred_sst_amt, 2),
                    'payment_date' => $row->payment_date ? date('d/m/Y', strtotime($row->payment_date)) : date('d/m/Y'),
                    'case_ref_no' => $row->case_ref_no,
                    'client_name' => $row->client_name
                ];
            });

            // Debug: Log the final results
            error_log("getTransferFeeInvoiceData - Final results count: " . $transformed_data->count());
            error_log("getTransferFeeInvoiceData - Final results: " . json_encode($transformed_data->toArray()));

            return response()->json([
                'status' => 'success',
                'data' => $transformed_data
            ]);
        }
    }

    /**
     * API endpoint for invoice search (for transfer fee inline add)
     */
    public function apiInvoiceSearch(Request $request)
    {
        $q = $request->input('q');
        $current_user = auth()->user();

        $rows = DB::table('loan_case_invoice_main as i')
            ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->select(
                'i.id',
                'i.invoice_no',
                'i.invoice_date',
                'l.case_ref_no',
                'c.name as client_name'
            )
            ->where('i.transferred_to_office_bank', '=', 0)
            ->where('i.status', '<>', 99)
            ->where('i.bln_invoice', '=', 1)
            ->where(function($query) use ($q) {
                $query->where('i.invoice_no', 'like', "%$q%")
                      ->orWhere('c.name', 'like', "%$q%")
                      ->orWhere('l.case_ref_no', 'like', "%$q%")
                      ->orWhereRaw('DATE_FORMAT(i.invoice_date, "%d-%m-%Y") like ?', ["%$q%"]);
            })
            ->orderBy('i.invoice_date', 'desc')
            ->limit(20)
            ->get();

        // Format date for display
        $results = $rows->map(function($row) {
            $row->invoice_date = $row->invoice_date ? date('d-m-Y', strtotime($row->invoice_date)) : '';
            return $row;
        });

        return response()->json($results);
    }

    /**
     * Export Client Ledger to Excel
     */
    public function exportClientLedger(Request $request)
    {
        try {
            $current_user = auth()->user();
            
            // Check permissions
            if (AccessController::UserAccessPermissionController(PermissionController::ClientAccountBalancePermission()) == false) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied'
                ], 403);
            }

            $status = $request->input("status");
            $year = $request->input("year");
            $month = $request->input("mon");
            $branch_id = $request->input("branch_id");
            $bank_id = $request->input("bank_id");

            // Get the same data as getClientLedger method
            $rows = DB::table('loan_case as a');

            if ($status != '') {
                $rows = $rows->where('a.status', $status);
            }

            $last_day = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d');

            if ($branch_id) {
                $rows = $rows->where('a.branch_id', '=', $branch_id);
            }

            $rows = $rows->orderBy('a.created_at', 'asc')->get();

            $total_credit = 0;
            $total_debit = 0;

            for ($i = 0; $i < count($rows); $i++) {
                $credit = DB::table('ledger_entries_v2 as a')
                    ->where('a.case_id', '=', $rows[$i]->id)
                    ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
                    ->where('a.transaction_type', 'C')
                    ->where('a.status', '<>', 99)
                    ->where('a.date', '<=', $last_day)
                    ->orderBy('a.date', 'ASC');

                if ($bank_id) {
                    $credit = $credit->where('a.bank_id', '=', $bank_id);
                }

                $credit = $credit->sum('amount');

                $debit = DB::table('ledger_entries_v2 as a')
                    ->where('a.case_id', '=', $rows[$i]->id)
                    ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
                    ->where('a.transaction_type', 'D')
                    ->where('a.status', '<>', 99)
                    ->where('a.date', '<=', $last_day)
                    ->orderBy('a.date', 'ASC');

                if ($bank_id) {
                    $debit = $debit->where('a.bank_id', '=', $bank_id);
                }

                $debit = $debit->sum('amount');

                $rows[$i]->amount_ledger = $credit - $debit;
            }

            // Filter out zero amounts
            $rows = $rows->filter(function($result) {
                if ($result->amount_ledger != 0) {
                    return true;
                } else {
                    return false;
                }
            });

            // Prepare data for export
            $exportData = [];
            $rowNumber = 1;
            $grandTotal = 0;

            foreach ($rows as $row) {
                $grandTotal += $row->amount_ledger;
                
                // Get status text
                $statusText = '';
                switch ($row->status) {
                    case 0:
                        $statusText = 'Closed';
                        break;
                    case 1:
                        $statusText = 'In Progress';
                        break;
                    case 2:
                        $statusText = 'Open';
                        break;
                    case 3:
                        $statusText = 'KIV';
                        break;
                    case 4:
                        $statusText = 'Pending Close';
                        break;
                    case 7:
                        $statusText = 'Reviewing';
                        break;
                    case 99:
                        $statusText = 'Aborted';
                        break;
                    default:
                        $statusText = 'Unknown';
                }

                $exportData[] = [
                    'No' => $rowNumber,
                    'Client Account Group' => '004-1-' . $row->client_ledger_account_code . ': ' . $row->case_ref_no,
                    'Ref No' => $row->case_ref_no,
                    'Status' => $statusText,
                    'Amount' => $row->amount_ledger
                ];
                
                $rowNumber++;
            }

            // Add totals row
            $exportData[] = [
                'No' => 'TOTAL',
                'Client Account Group' => '',
                'Ref No' => '',
                'Status' => '',
                'Amount' => $grandTotal
            ];

            return $this->exportClientLedgerToExcel($exportData, $year, $month, $branch_id, $bank_id);

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
    private function exportClientLedgerToExcel($data, $year, $month, $branch_id, $bank_id)
    {
        $filename = 'client_ledger_' . $year . '_' . $month . '_' . date('Y-m-d') . '.xlsx';
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Client Account Balance Report');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set subtitle
        $sheet->setCellValue('A2', 'Year: ' . $year . ' | Month: ' . $month);
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        // Set headers
        $headers = ['No', 'Client Account Group', 'Ref No', 'Status', 'Amount'];
        $col = 'A';
        $row = 4;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $headerRange = 'A4:E4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D8DBE0');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
        
        // Add data
        $row = 5;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($item as $value) {
                if ($col == 'E' && is_numeric($value)) { // Amount column
                    $sheet->setCellValue($col . $row, $value);
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                $col++;
            }
            $row++;
        }
        
        // Style data rows
        $dataRange = 'A5:E' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set response headers
        $response = response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }

    public function officeAccountLedger()
    {
        $current_user = auth()->user();
        $rows = [];

        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $OfficeBankAccount = $this->getOfficeBankAccount();

        return view('dashboard.account.office-account-ledger', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'rows' => $rows,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function getOfficeAccountLedger(Request $request)
    {
        $current_user = auth()->user();
        $status = $request->input("status");

        // Get user's accessible branches
        $branchInfo = BranchController::manageBranchAccess();
        $accessibleBranchIds = $branchInfo['brancAccessList'];

        $rows = DB::table('office_bank_account as a')
            ->leftJoin('account_code as b', 'a.account_code', '=', 'b.id')
            ->select('a.*', 'b.name as account_code_name', 'b.code as account_code_code');

        if ($request->input("status") != '') {
            $rows = $rows->where('a.status', $request->input("status"));
        } else {
            $rows = $rows->where('a.status', '=', 1);
        }

        $last_day = Carbon::create($request->input("year"), $request->input("mon"))->lastOfMonth()->format('Y-m-d');

        // Validate and filter by branch_id if provided
        if ($request->input("branch_id")) {
            $requestedBranchId = (int)$request->input("branch_id");
            // Validate that user has access to the requested branch
            if (!in_array($requestedBranchId, $accessibleBranchIds)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied to this branch'
                ], 403);
            }
            $rows = $rows->where('a.branch_id', '=', $requestedBranchId);
        } else {
            // If no branch_id specified, filter by user's accessible branches
            if (count($accessibleBranchIds) > 0) {
                $rows = $rows->whereIn('a.branch_id', $accessibleBranchIds);
            } else {
                // If user has no accessible branches, return empty result
                $rows = $rows->where('a.branch_id', '=', -1);
            }
        }

        $rows = $rows->orderBy('a.created_at', 'asc')->get();

        for ($i = 0; $i < count($rows); $i++) {
            $credit = DB::table('ledger_entries_v2 as a')
                ->where('a.bank_id', '=', $rows[$i]->id)
                ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
                ->where('a.transaction_type', 'C')
                ->where('a.status', '<>', 99)
                ->where('a.date', '<=', $last_day)
                ->orderBy('a.date', 'ASC')
                ->sum('amount');

            $debit = DB::table('ledger_entries_v2 as a')
                ->where('a.bank_id', '=', $rows[$i]->id)
                ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
                ->where('a.transaction_type', 'D')
                ->where('a.status', '<>', 99)
                ->where('a.date', '<=', $last_day)
                ->orderBy('a.date', 'ASC')
                ->sum('amount');

            $rows[$i]->amount_ledger = $credit - $debit;
        }

        // Filter out zero balances (optional - can be removed if you want to show all)
        $rows = $rows->filter(function($result) {
            if ($result->amount_ledger != 0) {
                return true;
            } else {
                return false;
            }
        });

        return response()->json([
            'view' => view('dashboard.account.table.tab-office-account-ledger', compact('rows'))->render(),
        ]);
    }

    public function officeAccountLedgerDetails($bank_id)
    {
        $current_user = auth()->user();

        // Check permissions
        if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // Get user's accessible branches
        $branchInfo = BranchController::manageBranchAccess();
        $accessibleBranchIds = $branchInfo['brancAccessList'];

        // Get bank account info and validate branch access
        $bankAccount = DB::table('office_bank_account as a')
            ->leftJoin('account_code as b', 'a.account_code', '=', 'b.id')
            ->select('a.*', 'b.name as account_code_name', 'b.code as account_code_code')
            ->where('a.id', '=', $bank_id)
            ->where('a.status', '=', 1)
            ->first();

        if (!$bankAccount) {
            return redirect()->to('office-account-ledger')->with('error', 'Bank account not found');
        }

        // Validate that user has access to this bank account's branch
        if (!in_array($bankAccount->branch_id, $accessibleBranchIds)) {
            return redirect()->to('office-account-ledger')->with('error', 'Access denied to this bank account');
        }

        return view('dashboard.account.office-account-ledger-details', [
            'bankAccount' => $bankAccount,
            'bank_id' => $bank_id
        ]);
    }

    public function getOfficeAccountLedgerDetails(Request $request)
    {
        $current_user = auth()->user();
        $bank_id = $request->input('bank_id');

        // Check permissions
        if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) == false) {
            return response()->json([
                'status' => 0,
                'message' => 'Access denied'
            ], 403);
        }

        // Get user's accessible branches
        $branchInfo = BranchController::manageBranchAccess();
        $accessibleBranchIds = $branchInfo['brancAccessList'];

        // Validate bank account exists and user has access
        $bankAccount = DB::table('office_bank_account')
            ->where('id', '=', $bank_id)
            ->where('status', '=', 1)
            ->first();

        if (!$bankAccount) {
            return response()->json([
                'status' => 0,
                'message' => 'Bank account not found'
            ], 404);
        }

        // Validate branch access
        if (!in_array($bankAccount->branch_id, $accessibleBranchIds)) {
            return response()->json([
                'status' => 0,
                'message' => 'Access denied to this bank account'
            ], 403);
        }

        // Get ledger entries for this bank account
        $rows = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('a.bank_id', '=', $bank_id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
            ->where('a.status', '<>', 99);

        // Apply date filters if provided
        if ($request->input("date_from") != null && $request->input("date_to") != null) {
            $rows = $rows->whereBetween('a.date', [$request->input("date_from"), $request->input("date_to")]);
        } else {
            if ($request->input("date_from") != null) {
                $rows = $rows->where('a.date', '>=', $request->input("date_from"));
            }

            if ($request->input("date_to") != null) {
                $rows = $rows->where('a.date', '<=', $request->input("date_to"));
            }
        }

        $rows = $rows->orderBy('a.date', 'asc')
            ->orderBy('a.last_row_entry', 'asc')
            ->get();

        $bank_name = $bankAccount->name . ' (' . $bankAccount->account_no . ')';

        return response()->json([
            'view' => view('dashboard.account.table.tab-office-account-ledger-details', compact('rows', 'bank_name'))->render(),
        ]);
    }

    public function exportOfficeAccountLedger(Request $request)
    {
        try {
            $current_user = auth()->user();
            
            // Check permissions
            if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) == false) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied'
                ], 403);
            }

            // Get user's accessible branches
            $branchInfo = BranchController::manageBranchAccess();
            $accessibleBranchIds = $branchInfo['brancAccessList'];

            $status = $request->input("status");
            $year = $request->input("year");
            $month = $request->input("mon");
            $branch_id = $request->input("branch_id");

            // Get the same data as getOfficeAccountLedger method
            $rows = DB::table('office_bank_account as a')
                ->leftJoin('account_code as b', 'a.account_code', '=', 'b.id')
                ->select('a.*', 'b.name as account_code_name', 'b.code as account_code_code');

            if ($status != '') {
                $rows = $rows->where('a.status', $status);
            } else {
                $rows = $rows->where('a.status', '=', 1);
            }

            $last_day = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d');

            // Validate and filter by branch_id if provided
            if ($branch_id) {
                $requestedBranchId = (int)$branch_id;
                // Validate that user has access to the requested branch
                if (!in_array($requestedBranchId, $accessibleBranchIds)) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Access denied to this branch'
                    ], 403);
                }
                $rows = $rows->where('a.branch_id', '=', $requestedBranchId);
            } else {
                // If no branch_id specified, filter by user's accessible branches
                if (count($accessibleBranchIds) > 0) {
                    $rows = $rows->whereIn('a.branch_id', $accessibleBranchIds);
                } else {
                    // If user has no accessible branches, return empty result
                    $rows = $rows->where('a.branch_id', '=', -1);
                }
            }

            $rows = $rows->orderBy('a.created_at', 'asc')->get();

            for ($i = 0; $i < count($rows); $i++) {
                $credit = DB::table('ledger_entries_v2 as a')
                    ->where('a.bank_id', '=', $rows[$i]->id)
                    ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
                    ->where('a.transaction_type', 'C')
                    ->where('a.status', '<>', 99)
                    ->where('a.date', '<=', $last_day)
                    ->orderBy('a.date', 'ASC')
                    ->sum('amount');

                $debit = DB::table('ledger_entries_v2 as a')
                    ->where('a.bank_id', '=', $rows[$i]->id)
                    ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
                    ->where('a.transaction_type', 'D')
                    ->where('a.status', '<>', 99)
                    ->where('a.date', '<=', $last_day)
                    ->orderBy('a.date', 'ASC')
                    ->sum('amount');

                $rows[$i]->amount_ledger = $credit - $debit;
            }

            // Filter out zero balances
            $rows = $rows->filter(function($result) {
                if ($result->amount_ledger != 0) {
                    return true;
                } else {
                    return false;
                }
            });

            // Prepare data for export
            $exportData = [];
            $rowNumber = 1;
            $grandTotal = 0;

            foreach ($rows as $row) {
                $grandTotal += $row->amount_ledger;
                
                // Get status text
                $statusText = ($row->status == 1) ? 'Active' : 'Inactive';

                // Format account group
                $accountGroup = ($row->account_code_name) ? $row->account_code_name . ': ' . $row->name : $row->name;

                $exportData[] = [
                    'No' => $rowNumber,
                    'Office Account Group' => $accountGroup,
                    'Account Name' => $row->name,
                    'Account No' => $row->account_no,
                    'Status' => $statusText,
                    'Balance' => $row->amount_ledger
                ];
                
                $rowNumber++;
            }

            // Add totals row
            $exportData[] = [
                'No' => 'TOTAL',
                'Office Account Group' => '',
                'Account Name' => '',
                'Account No' => '',
                'Status' => '',
                'Balance' => $grandTotal
            ];

            return $this->exportOfficeAccountLedgerToExcel($exportData, $year, $month, $branch_id);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Office Account Ledger data to Excel
     */
    private function exportOfficeAccountLedgerToExcel($data, $year, $month, $branch_id)
    {
        $filename = 'office_account_ledger_' . $year . '_' . $month . '_' . date('Y-m-d') . '.xlsx';
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Office Account Balance Report');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set subtitle
        $sheet->setCellValue('A2', 'Year: ' . $year . ' | Month: ' . $month);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        // Set headers
        $headers = ['No', 'Office Account Group', 'Account Name', 'Account No', 'Status', 'Balance'];
        $col = 'A';
        $row = 4;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $headerRange = 'A4:F4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D8DBE0');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
        
        // Add data
        $row = 5;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($item as $value) {
                if ($col == 'F' && is_numeric($value)) { // Balance column
                    $sheet->setCellValue($col . $row, $value);
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                $col++;
            }
            $row++;
        }
        
        // Style data rows
        $dataRange = 'A5:F' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set response headers
        $response = response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}
