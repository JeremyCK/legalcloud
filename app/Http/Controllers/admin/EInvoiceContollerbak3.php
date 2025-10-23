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
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EInvoiceContoller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['clientEinvoiceData', 'updateClientEinvoiceData']);
        // $this->middleware('admin');

    }

    public static function getEinvoiceCustomerMandatoryField()
    {
        return [
            'customer_code',
            'customer_name',
            // 'brn',
            // 'brn2',
            // 'sales_tax_no',
            // 'service_tax_no',
            'customer_category',
            'id_no',
            'id_type',
            'tin',
            'address_1',
            // 'address_2',
            // 'address_3',
            // 'address_4',
            'postcode',
            'city',
            'state',
            'country',
            'phone1'
        ];
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

    public function EInvoiceList()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        $EInvoiceMain = EInvoiceMain::where('status', '=', 1);

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (!in_array($current_user->menuroles, ['admin', 'account'])) {


            if (in_array($current_user->branch_id, [5, 6])) {
                $EInvoiceMain = $EInvoiceMain->whereIn('branch_id', [5, 6]);
            } else {
                $EInvoiceMain = $EInvoiceMain->where('branch_id', $current_user->branch_id);
            }
        }

        $EInvoiceMain = $EInvoiceMain->get();


        return view('dashboard.e-invoice.index', [
            'TransferFeeMain' => $EInvoiceMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch']
        ]);
    }



    public function einvoiceView($id)
    {
        $current_user = auth()->user();
        $EInvoiceMain = EInvoiceMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [2, 5])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [$current_user->branch_id, 6])->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();
        return view('dashboard.e-invoice.editv2', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'EInvoiceMain' => $EInvoiceMain,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function einvoiceCreate()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6])->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }


        return view('dashboard.e-invoice.create', [
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

    public function getEInvoiceMainList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            // $TransferFeeMain = TransferFeeMain::where('status', '=', 1)->get();
            $branchInfo = BranchController::manageBranchAccess();


            $TransferFeeMain = DB::table('einvoice_main as m')
                ->leftJoin('users as u', 'u.id', '=', 'm.created_by')
                ->select('m.*')
                ->where('m.status', '<>',  99);

            // if ($request->input("transfer_date_from") <> null && $request->input("transfer_date_to") <> null) {
            //     $TransferFeeMain = $TransferFeeMain->whereBetween('m.transfer_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            // } else {
            //     if ($request->input("transfer_date_from") <> null) {
            //         $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
            //     }

            //     if ($request->input("transfer_date_to") <> null) {
            //         $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
            //     }
            // }

            if ($request->input("branch_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  $request->input("branch_id"));
            }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if (in_array($current_user->branch_id, [5, 6])) {
            //         $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [5, 6]);
            //     } else {
            //         $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  $current_user->branch_id);
            //     }
            // } else if (in_array($current_user->menuroles, ['sales'])) {
            //     if (in_array($current_user->id, [51, 32])) {
            //         $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [5, 6]);
            //     }
            // } else {
            //     if (in_array($current_user->id, [13])) {
            //         $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [$current_user->branch_id]);
            //     }
            // }

            
            if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker', 'lawyer'])) {

                $TransferFeeMain = $TransferFeeMain->where(function ($q) use ($current_user, $branchInfo) {
                    $q->whereIn('m.branch_id', $branchInfo['brancAccessList']);
                });
            }

            $TransferFeeMain = $TransferFeeMain->orderBy('m.created_at', 'DESC')->get();

            return DataTables::of($TransferFeeMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '


<div class="btn-group  normal-edit-mode">
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank" href="/einvoice/' . $data->id . '" style="color:white;margin:0"><i style="margin-right: 10px;" class="cil-pencil"></i>Edit</a>
                     
                       <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteEinvoiceMainRecords(' . $data->id . ')" style="color:white;margin:0"><i style="margin-right: 10px;" class="cil-x"></i>Delete</a>
                        </div></div>

                    ';
                    return $actionBtn;
                })
                ->editColumn('is_recon', function ($data) {

                    if ($data->is_recon == '1')
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })

                ->editColumn('client_profile_completed', function ($row) {
                    return $row->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                })
                ->rawColumns(['action', 'case_ref_no', 'client_profile_completed'])
                ->make(true);
        }
    }

    public function getEInvoiceSentList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();
            $branchInfo = BranchController::manageBranchAccess();
            $transfer_list = null;

            $rows = DB::table('loan_case_invoice_main as i')
                ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                // ->leftJoin('einvoice_details as t', 't.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('einvoice_details as t', 't.loan_case_invoice_id', '=', 'i.id')
                ->leftJoin('invoice_billing_party as p', 'i.bill_party_id', '=', 'p.id')
                // ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                // ->select('b.*', 'l.case_ref_no', 'c.name as client_name', 't.transfer_amount', 't.sst_amount', 't.is_recon', 't.id as transfer_id')
                ->select('b.*','i.invoice_no as invoice_no_v2','i.id as invoice_id', 'i.bill_party_id','i.amount as invoice_amout', 'l.case_ref_no', 't.einvoice_status', 'p.completed as client_profile_completed', 'p.id as party_id')
                ->where('i.status', '<>',  99);


            if ($request->input('type') == 'add') {
                $transfer_list = json_decode($request->input('transfer_list'));
                $rows = $rows->whereIn('i.id', $transfer_list);
            } else if ($request->input('type') == 'transferred') {
                if ($request->input('transaction_id')) {
                    $rows = $rows->whereIn('b.id', EInvoiceDetails::where('status', '<>', 99)->where('einvoice_main_id', $request->input('transaction_id'))->pluck('loan_case_main_bill_id')->toArray())
                        ->where('t.einvoice_main_id', '=',  $request->input('transaction_id'));
                }
            } else if ($request->input('type') == 'sent') {
                if ($request->input('id')) {
                    $rows = $rows->whereIn('i.id', EInvoiceDetails::where('status', '<>', 99)->where('einvoice_main_id', $request->input('id'))->pluck('loan_case_invoice_id')->toArray());
                }
            } else {
                // $rows = $rows->whereNotIn('b.id', EInvoiceDetails::where('status', '<>', 99)->pluck('loan_case_main_bill_id')->toArray());
                // $rows = $rows->whereNotIn('i.id', EInvoiceDetails::where('status', '<>', 99)->pluck('einvoice_main_id')->toArray());
                    $rows = $rows->whereNotIn('i.id', EInvoiceDetails::where('status', '<>', 99)->pluck('loan_case_invoice_id')->toArray());
            }

            if ($request->input("start_date") <> null && $request->input("end_date") <> null) {
                $rows = $rows->whereBetween('b.invoice_date', [$request->input("start_date"), $request->input("end_date")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $rows = $rows->where('b.invoice_date', '>=', $request->input("start_date"));
                }

                if ($request->input("date_to") <> null) {
                    $rows = $rows->where('b.invoice_date', '<=', $request->input("end_date"));
                }
            }

            if ($request->input('branch')) {
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if (in_array($current_user->branch_id, [5, 6])) {
            //         $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
            //     } else if (in_array($current_user->branch_id, [2])) {
            //         $rows = $rows->whereIn('l.sales_user_id', [13]);
            //     } else {
            //         $rows = $rows->whereIn('b.invoice_branch_id', $accessInfo['brancAccessList']);
            //     }
            // } else if (in_array($current_user->menuroles, ['sales'])) {
            //     if (in_array($current_user->id, [32, 51])) {
            //         $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
            //     }
            // } else if (in_array($current_user->menuroles, ['lawyer'])) {
            //     if (in_array($current_user->id, [13])) {
            //         $rows = $rows->whereIn('b.invoice_branch_id', [2]);
            //     }
            // }

            if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker', 'lawyer'])) {

                $rows = $rows->where(function ($q) use ($current_user, $branchInfo) {
                    $q->whereIn('b.invoice_branch_id', $branchInfo['brancAccessList']);
                });
            }

            $rows = $rows->orderBy('b.invoice_no', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request, $transfer_list) {
                    if ($request->input('type') == 'transferred') {

                        $is_disabled = '';

                        // if ($data->is_recon == 1) {
                        //     $is_disabled = 'disabled';
                        // }
                        // $is_disabled = 'disabled';

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="trans_bill" value="' . $data->invoice_id . '" id="trans_chk_' . $data->invoice_id . '" ' . $is_disabled . ' >
                        <label for="trans_chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    } elseif ($request->input('type') == 'add') {

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="add_bill" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '"  >
                        <label for="chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    } elseif ($request->input('type') == 'sent') {

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="add_bill" class="invoice_all" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '" data-status="' . $data->einvoice_status . '" >
                        <label for="chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    } elseif ($request->input('type') == 'not_transfer') {

                        $is_checked = '';

                        echo ($transfer_list);

                        if ($transfer_list != null) {

                            if (in_array($data->invoice_id, $transfer_list)) {
                                $is_checked = 'checked';
                            }
                        }

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="hidden"  value="' . $data->id . '" id="bill_id_' . $data->invoice_id . '"  >
                        <input type="hidden"  value="' . $data->case_id . '" id="case_id_' . $data->invoice_id . '"  >
                        <input type="checkbox" name="bill" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '"  ' . $is_checked . '>
                        <label for="chk_' . $data->invoice_id . '">' . $transfer_list . '</label>
                        </div> ';
                    } else {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="bill" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '" >
                        <label for="chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    }

                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {
                    return '<b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('einvoice_status', function ($data) {
                    if ($data->einvoice_status == 'SQL') {
                        return 'Sent to SQL Server';
                    } else if ($data->einvoice_status == 'EXCEL') {
                        return 'Excel template generated';
                    } else if ($data->einvoice_status == 'LHDN') {
                        return 'Submmited to LHDN';
                    } else {
                        return 'Pending';
                    }
                })

                ->editColumn('client_profile_completed', function ($data) {

                    if ($data->client_profile_completed == 1)
                    {
                        return '<span class="label label-success">Completed</span>' ;
                    }
                    else
                    {
                        if($data->bill_party_id != null)
                        {
                            return '<span class="label label-warning">
                                    <a  href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                data-keyboard="false" data-toggle="modal" data-target="#modalAddBilltoInfo"
                                onclick="loadInvBilltoDetails(' . $data->bill_party_id . ');" style="color:white;margin:0"><i
                                    style="margin-right: 10px;" class="cil-calendar"></i>Pending update</a>
                                    </span>';
                        }
                        else{
                                //   return '<span class="label label-warning">
                                //     <a  href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                // data-keyboard="false" data-toggle="modal" data-target="#modalAddBillto"
                                // onclick="loadInvBilltoDetails(' . $data->id . ');" style="color:white;margin:0"><i
                                //     style="margin-right: 10px;" class="cil-calendar"></i>Add</a>
                                //     </span>';

                                
                        return '<span class="label label-warning">No Recipient</span>' ;
                        }
                        
                    }

  


                //     return $data->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : 
                //     '<span class="label label-warning">
                //     <a  href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                //  data-keyboard="false" data-toggle="modal" data-target="#modalAddBilltoInfo"
                //  onclick="loadInvBilltoDetails(' . $data->party_id . ');" style="color:white;margin:0"><i
                //      style="margin-right: 10px;" class="cil-calendar"></i>Pending Update</a>
                //     </span>';
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                ->editColumn('invoice_date', function ($data) {
                    if ($data->invoice_date) {
                        $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                        return $formatedDate;
                    } else {
                        return $data->invoice_date;
                    }
                })
                ->rawColumns(['action',  'case_ref_no', 'pfee_sum', 'sst_to_transfer', 'invoice_date', 'client_profile_completed'])
                ->make(true);
        }
    }



    public function AddInvoiceIntoEInvoice(Request $request, $id)
    {
        $current_user = auth()->user();

        if ($request->input('add_invoice') != null) {
            $add_bill = json_decode($request->input('add_invoice'), true);
        }

        if (count($add_bill) > 0) {
            for ($i = 0; $i < count($add_bill); $i++) {
                $EInvoiceDetails  = new EInvoiceDetails();

                $EInvoiceDetails->einvoice_main_id = $id;

                $EInvoiceDetails->loan_case_main_bill_id = $add_bill[$i]['bill_id']; // Use 'id' from the bill item
                $EInvoiceDetails->loan_case_invoice_id = $add_bill[$i]['id']; // Use 'id' from the bill item
                $EInvoiceDetails->case_id = $add_bill[$i]['case_id']; // Use 'id' from the bill item

                $EInvoiceDetails->created_by = $current_user->id;
                $EInvoiceDetails->amt = 0;
                $EInvoiceDetails->einvoice_status = 'SENT';

                $EInvoiceDetails->status = 1;
                $EInvoiceDetails->created_at = date('Y-m-d H:i:s');

                $EInvoiceDetails->save();

                LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['sql_submit' => 1]);
            }
        }

        return response()->json(['status' => 1]);
    }

    public function DeleteInvoiceFromEInvoice(Request $request, $id)
    {
        $current_user = auth()->user();

        if ($request->input('delete_invoice') != null) {
            $delete_invoice = json_decode($request->input('delete_invoice'), true);
        }

        if (count($delete_invoice) > 0) {
            for ($i = 0; $i < count($delete_invoice); $i++) {
                EInvoiceDetails::where('loan_case_invoice_id', $delete_invoice[$i]['id'])->where('einvoice_main_id', $id)->delete();
                LoanCaseBillMain::where('id', '=', $delete_invoice[$i]['id'])->update(['sql_submit' => 0]);
            }

            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = 0;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'delete_einvoice';
            $AccountLog->desc = $current_user->name . ' remove invoice id (' . $request->input('delete_invoice') . ') from E-Invoice Records';
            $AccountLog->status = 1;
            $AccountLog->object_id = $id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        }

        return response()->json(['status' => 1]);
    }

    public function deleteEinvoiceMainRecords(Request $request, $id)
    {
        $einvoiceMain = EInvoiceMain::Where('id', $id)->first();
        $transaction_id = $einvoiceMain->transaction_id;

        if (!$einvoiceMain) {
            return response()->json(['status' => 0, 'message' => 'E-Invoice record not found.']);
        }

        // Check if any associated invoices have been sent
        $detailsSent = EInvoiceDetails::where('einvoice_main_id', $id)
            ->whereIn('einvoice_status', ['LHDN', 'SQL'])
            ->exists();

        if ($detailsSent) {
            return response()->json(['status' => 0, 'message' => 'Cannot delete this record as it contains invoices already sent to LHDN or SQL.']);
        }

        // Get related bill IDs to update their status after deletion
        $billIds = EInvoiceDetails::where('einvoice_main_id', $id)->pluck('loan_case_main_bill_id');

        // Delete associated details
        EInvoiceDetails::where('einvoice_main_id', $id)->delete();

        // Delete the main record
        $einvoiceMain->delete();

        // Reset the submission flag on the related bills
        if ($billIds->isNotEmpty()) {
            LoanCaseBillMain::whereIn('id', $billIds)->update(['sql_submit' => 0]);
        }

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = 0;
        $AccountLog->bill_id = 0;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'DeleteEInvoiceMainRecord';
        $AccountLog->desc = $current_user->name . ' Deleted Einvoice Main record ('. $transaction_id. ')';
        $AccountLog->object_id = $id;
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'message' => 'E-Invoice record deleted successfully.']);
    }

    public function AddBilltoInvoice(Request $request, $bill_id)
    {
        $current_user = auth()->user();
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_id)->first();

        // Check if a specific billing_party_id is provided (from existing party selection)
        if ($request->has('billing_party_id') && $request->input('billing_party_id')) {
            $InvoiceBillingParty = InvoiceBillingParty::find($request->input('billing_party_id'));
            
            if (!$InvoiceBillingParty) {
                return response()->json(['status' => 0, 'message' => 'Billing party not found']);
            }
            
            // Check if this billing party is already associated with this bill
            // For split invoices, allow the same billing party to be associated with multiple invoices
            if ($InvoiceBillingParty->loan_case_main_bill_id == $bill_id) {
                // Only prevent if trying to add to the same specific invoice
                if($request->input('invoice_id') != "undefined") {
                    $existingInvoice = LoanCaseInvoiceMain::where('id', $request->input('invoice_id'))
                        ->where('bill_party_id', $InvoiceBillingParty->id)
                        ->first();
                    
                    if ($existingInvoice) {
                        return response()->json(['status' => 2, 'message' => 'This party is already associated with this specific invoice']);
                    }
                }
                // If not adding to a specific invoice, or if it's a different invoice, allow it
            }
            
            // Associate the existing billing party with this bill
            $InvoiceBillingParty->loan_case_main_bill_id = $bill_id;
            $InvoiceBillingParty->save();
        } else {
            // Legacy logic for creating new billing parties (when no specific ID provided)
            // First check if a billing party is already associated with this specific bill_id
            $existingBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_id)
                ->where('customer_name', $request->input('bill_to'))
                ->first();

            if ($existingBillingParty) {
                // Billing party already exists for this bill, just use it
                $InvoiceBillingParty = $existingBillingParty;
            } else {
                // Check if there's an existing billing party with the same customer name that can be reused
                $InvoiceBillingParty = InvoiceBillingParty::where('case_id', $request->input('case_id'))
                    ->where('customer_name', $request->input('bill_to'))
                    ->whereNull('loan_case_main_bill_id') // Not already associated with another bill
                    ->first();

                if (!$InvoiceBillingParty) {
                    // No existing party found, create a new one
                    $Parameter = Parameter::where('parameter_type', '=', 'sql_client_code_running_no')->first();
                    $SQL_prefix = Parameter::where('parameter_type', '=', 'sql_client_code_prefix')->value('parameter_value_1');
                    $nextRunningNo = str_pad((int)$Parameter->parameter_value_1, 5, '0', STR_PAD_LEFT);
                    $customer_code = $SQL_prefix . '-' . $nextRunningNo;

                    $InvoiceBillingParty = new InvoiceBillingParty();
                    $InvoiceBillingParty->customer_code = $customer_code;
                    $InvoiceBillingParty->case_id = $request->input('case_id');
                    $InvoiceBillingParty->customer_name = $request->input('bill_to');
                    $InvoiceBillingParty->status = 1;
                    $InvoiceBillingParty->created_at = date('Y-m-d H:i:s');
                    $InvoiceBillingParty->created_by = $current_user->id;
                    $InvoiceBillingParty->save();

                    $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
                    $Parameter->save();
                }

                // Associate the billing party with this bill
                $InvoiceBillingParty->loan_case_main_bill_id = $bill_id;
                $InvoiceBillingParty->save();
            }
        }

        // Check if this specific billing party is already associated with this specific invoice
        if($request->input('invoice_id') != "undefined") {
            $existingInvoice = LoanCaseInvoiceMain::where('id', $request->input('invoice_id'))
                ->where('bill_party_id', $InvoiceBillingParty->id)
                ->first();
            
            if ($existingInvoice) {
                return response()->json(['status' => 2, 'message' => 'This party is already associated with this specific invoice']);
            }
        }

        if($request->input('invoice_id') != "undefined")
        {
            $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', $request->input('invoice_id'))->first();
                
            $LoanCaseInvoiceMain->bill_party_id = $InvoiceBillingParty->id;
            $LoanCaseInvoiceMain->save();

            $InvoiceBillingParty->invoice_main_id = $request->input('invoice_id');
            $InvoiceBillingParty->save();

            $this->updateInvoiceDetailsAmt($LoanCaseInvoiceMain->id, $LoanCaseBillMain->id, $LoanCaseBillMain->case_id);

        }
        else
        {
            $InvoiceBillingParty->invoice_main_id = 0;
            $InvoiceBillingParty->save();
        }

        
        // $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_id)->get();

        $InvoiceBillingParty = DB::table('invoice_billing_party as bp')
            ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'bp.invoice_main_id')
            ->select('bp.*', 'im.invoice_no', 'im.bill_party_id', 'im.id as invoice_id')
            ->where('bp.loan_case_main_bill_id', $bill_id)
            ->get();

        // $billList = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();


        return response()->json(['status' => 1, 'message' => 'Added party into list']);
    }


    public function loadBillToInv($id)
    {
        \Log::info('loadBillToInv called with ID: ' . $id);
        
        $InvoiceBillingParty = InvoiceBillingParty::where('id', $id)->first();
        
        if (!$InvoiceBillingParty) {
            \Log::error('InvoiceBillingParty not found for ID: ' . $id);
            return response()->json([
                'status' => 0,
                'message' => 'Billing party not found',
                'data' => null
            ]);
        }
        
        \Log::info('InvoiceBillingParty found:', $InvoiceBillingParty->toArray());

        $invoice_no = LoanCaseInvoiceMain::where('id', $InvoiceBillingParty->invoice_main_id)->pluck('invoice_no');

        $response = [
            'status' => 1,
            'data' => $InvoiceBillingParty,
            'inv_no' => $invoice_no,
            'view' => view('dashboard.case.section.d-party-infov2', compact('InvoiceBillingParty'))->render(),
        ];
        
        \Log::info('loadBillToInv response:', $response);

        return response()->json($response);
    }

    public function loadBillToInvWIthInvoice($id)
    {
        // $InvoiceBillingParty = InvoiceBillingParty::where('id', $id)->first();
        $invoiceMain = LoanCaseInvoiceMain::where('id', $id)->first();
        $invoice_no = $invoiceMain->invoice_no;
        $InvoiceBillingParty = InvoiceBillingParty::where('id', $invoiceMain->bill_party_id)->first();

        return response()->json([
            'status' => 1,
            'data' => $InvoiceBillingParty,
            'inv_no' => $invoice_no,
            'view' => view('dashboard.case.section.d-party-infov2', compact('InvoiceBillingParty'))->render(),
        ]);
    }

    public function UpdateBillToInfo(Request $request, $id)
    {
        try {
            $billingParty = InvoiceBillingParty::find($id);

            if (!$billingParty) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Billing party not found'
                ], 404);
            }

            // Update all fields
            $billingParty->customer_name = $request->input('customer_name');
            $billingParty->customer_code = $request->input('customer_code');
            $billingParty->customer_category = $request->input('customer_category');
            $billingParty->tin = $request->input('tin');
            $billingParty->brn = $request->input('brn');
            $billingParty->brn2 = $request->input('brn2');
            $billingParty->sales_tax_no = $request->input('sales_tax_no');
            $billingParty->service_tax_no = $request->input('service_tax_no');
            $billingParty->id_type = $request->input('id_type');
            $billingParty->id_no = $request->input('id_no');
            $billingParty->address_1 = $request->input('address_1');
            $billingParty->address_2 = $request->input('address_2');
            $billingParty->address_3 = $request->input('address_3');
            $billingParty->address_4 = $request->input('address_4');
            $billingParty->postcode = $request->input('postcode');
            $billingParty->city = $request->input('city');
            $billingParty->state = $request->input('state');
            $billingParty->country = $request->input('country');
            $billingParty->phone1 = $request->input('phone1');
            $billingParty->mobile = $request->input('mobile');
            $billingParty->fax1 = $request->input('fax1');
            $billingParty->fax2 = $request->input('fax2');
            $billingParty->email = $request->input('email_einvoice');

            // Check mandatory fields
            $mandatoryFields = self::getEinvoiceCustomerMandatoryField();
            $allMandatoryFieldsFilled = true;

            foreach ($mandatoryFields as $field) {
                if (empty($billingParty->$field)) {
                    $allMandatoryFieldsFilled = false;
                    break;
                }
            }

            // Update completed status of the current billing party
            $billingParty->completed = $allMandatoryFieldsFilled ? 1 : 0;
            $billingParty->save();

            // Now check all billing parties related to the same loan_case_main_bill_id
            $relatedBillingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->get();

            $allRelatedCompleted = true;
            foreach ($relatedBillingParties as $relatedParty) {
                if ($relatedParty->completed == 0) {
                    $allRelatedCompleted = false;
                    break;
                }
            }

            // Find the associated LoanCaseBillMain record
            $loanCaseBillMain = LoanCaseBillMain::find($billingParty->loan_case_main_bill_id);

            // If LoanCaseBillMain and associated EInvoiceMain exist, update client_profile_completed
            if ($loanCaseBillMain && $loanCaseBillMain->id) {
                $EInvoiceDetails = EInvoiceDetails::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->first();
                if ($EInvoiceDetails) {
                    $EInvoiceDetails->client_profile_completed = $allRelatedCompleted ? 1 : 0;
                    $EInvoiceDetails->save();


                    $EInvoiceDetailsAll = EInvoiceDetails::where('einvoice_main_id', $EInvoiceDetails->einvoice_main_id)->get();

                    $allRelatedCompleted = true;
                    foreach ($EInvoiceDetailsAll as $relatedParty) {
                        if ($relatedParty->client_profile_completed == 0) {
                            $allRelatedCompleted = false;
                            break;
                        }
                    }

                    EInvoiceMain::where('id', $EInvoiceDetails->einvoice_main_id)->update(['client_profile_completed' => $allRelatedCompleted]);
                }
            }





            $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->get();
            $billList = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();



            return response()->json([
                'status' => 1,
                'message' => 'Billing party information updated successfully',
                'view' => $billList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error updating billing party information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeBillto($id)
    {
        
        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $id)->first();
        $billingParty = InvoiceBillingParty::find($LoanCaseInvoiceMain->bill_party_id);


        if (!$billingParty) {
            return response()->json(['status' => 0, 'message' => 'Billing party not found'], 404);
        };

        // Check if completed or sent_to_sql status is 1
        if ($billingParty->completed == 1 || $billingParty->sent_to_sql == 1) {
            return response()->json(['status' => 0, 'message' => 'Cannot remove a completed or sent record.']);
        }

        $invoice_main_id = $billingParty->invoice_main_id;
        $bill_main_id = $billingParty->loan_case_main_bill_id;
        $case_id = $billingParty->case_id;

        

        // return $invoice_main_id . '-' . $bill_main_id . '-' . $case_id;

        // If not completed or sent, delete the record
        // $billingParty->delete();
        

         InvoiceBillingParty::where('invoice_main_id', $invoice_main_id)->update(['loan_case_main_bill_id' => 0, 'invoice_main_id' => 0]); 


        $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)->get();


        $current_user = auth()->user();
        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        // $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $case_id)
        //     ->count();

          $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_main_id)->first();
        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $invoice_main_id)->first();

        
        // $this->updateInvoiceDetailsAmt($LoanCaseInvoiceMain->id, $LoanCaseBillMain->id, $LoanCaseBillMain->case_id);

        // if ($LoanCaseBillMain->invoice_no != $LoanCaseInvoiceMain->invoice_no)
        // {
        //     LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->delete();
        //     LoanCaseInvoiceMain::where('id', $invoice_main_id)->delete();
        // }

        if($party_count == 0)
        {
            $party_count = 1;
        }


        
        // LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)->update(['amount' => DB::raw('ori_invoice_amt /' . $party_count)]);

        // $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("amount");
        // $total_sst = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("sst");

        // $total_amt = ($total_amt + $total_sst)/$party_count;

        // LoanCaseInvoiceMain::where("loan_case_main_bill_id", $bill_main_id)->update(["amount" => $total_amt]);
        LoanCaseInvoiceMain::where("id", $id)->update(["bill_party_id" => 0]);

        

        $billList = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();

        return response()->json(['status' => 1, 'message' => 'Billing party removed successfully', 'view' => $billList]);
    }

    public function removeInvoice($id)
    {
        $delete_invoice_id = '';
        $loan_case_main_bill_id = '';
        $case_id = '';

        $current_user = auth()->user();
        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $id)->first();

        if (!$LoanCaseInvoiceMain) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found'], 404);
        };

        
        $delete_invoice_id = $LoanCaseInvoiceMain->id;
        $loan_case_main_bill_id = $LoanCaseInvoiceMain->loan_case_main_bill_id;
        $case_id = LoanCaseBillMain::where('id', $loan_case_main_bill_id)->pluck('case_id');

        // Check if this invoice is already in transfer fee records
        $transferFeeDetails = \App\Models\TransferFeeDetails::where('loan_case_invoice_main_id', $id)->get();
        
        if ($transferFeeDetails->isNotEmpty()) {
            // Get transfer fee main information for better error message
            $transferFeeMainIds = $transferFeeDetails->pluck('transfer_fee_main_id')->unique();
            $transferFeeMains = \App\Models\TransferFeeMain::whereIn('id', $transferFeeMainIds)->get();
            
            $transferIds = $transferFeeMains->pluck('transaction_id')->implode(', ');
            
            return response()->json([
                'status' => 0, 
                'message' => 'Cannot remove invoice. This invoice is already included in transfer fee record(s): ' . $transferIds . '. Please remove it from transfer fee first.'
            ], 400);
        }

        InvoiceBillingParty::where('invoice_main_id', $id)->update(['invoice_main_id' => 0]); 
        $LoanCaseInvoiceMain->delete();

        LoanCaseInvoiceDetails::where('invoice_main_id', '=', $id)->delete();
        
        $this->updateInvoiceDetailsAmt($delete_invoice_id, $loan_case_main_bill_id, $case_id);
        
        $this->generateNewInvNo($loan_case_main_bill_id, $delete_invoice_id, true);

        return response()->json(['status' => 1, 'message' => 'Billing party removed successfully']);
    }

    public function splitInvoice(Request $request, $bill_main_id)
    {
        $current_user = auth()->user();
        
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_main_id)->first();
        
        if (!$LoanCaseBillMain) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }

        $case = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();
        
        if (!$case) {
            return response()->json(['status' => 0, 'message' => 'Case not found.']);
        }

        // Get existing invoice for this bill
        $existingInvoice = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->first();
        
        if (!$existingInvoice) {
            return response()->json(['status' => 0, 'message' => 'No invoice found for this bill.']);
        }

        // Skip party creation - will set bill_party_id to 0

        $loanCaseInvoiceMain = new LoanCaseInvoiceMain();

        $loanCaseInvoiceMain->loan_case_main_bill_id = $bill_main_id;
        $loanCaseInvoiceMain->invoice_no = '';
        $loanCaseInvoiceMain->Invoice_date = $LoanCaseBillMain->Invoice_date;
        $loanCaseInvoiceMain->amount = $LoanCaseBillMain->total_amt_inv;
        $loanCaseInvoiceMain->pfee1_inv = $LoanCaseBillMain->pfee1_inv;
        $loanCaseInvoiceMain->pfee2_inv = $LoanCaseBillMain->pfee2_inv;
        $loanCaseInvoiceMain->sst_inv = $LoanCaseBillMain->sst;
        $loanCaseInvoiceMain->reimbursement_amount = $LoanCaseBillMain->reimbursement_amount ?? 0;
        $loanCaseInvoiceMain->reimbursement_sst = $LoanCaseBillMain->reimbursement_sst ?? 0;
        $loanCaseInvoiceMain->bill_party_id = 0; // Set to 0 since we're not creating a party
        $loanCaseInvoiceMain->remark = "";
        $loanCaseInvoiceMain->created_by = $current_user->id;
        $loanCaseInvoiceMain->status = 1;
        $loanCaseInvoiceMain->created_at = date('Y-m-d H:i:s');

        $loanCaseInvoiceMain->save();

        $invoice_main_id_new = $loanCaseInvoiceMain->id;

        $this->generateNewInvNo($bill_main_id, $invoice_main_id_new, false);

        $invoice_main_id = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->pluck('id')->first();


        



        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        // $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $case_id)
        //     ->where('invoice_main_id','!=', 0)
        //     ->count();

        $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        $sum = 0;

        if (count($loanCaseInvoiceDetails) > 0) {
            for ($i = 0; $i < count($loanCaseInvoiceDetails); $i++) {

                if ($invoice_main_id_new != "") {
                    $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();

                    $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
                    $LoanCaseInvoiceDetailsNew->account_item_id = $loanCaseInvoiceDetails[$i]->account_item_id;
                    $LoanCaseInvoiceDetailsNew->quotation_item_id = $loanCaseInvoiceDetails[$i]->id;
                    $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
                    $LoanCaseInvoiceDetailsNew->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt / $party_count;
                    $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $loanCaseInvoiceDetails[$i]->ori_invoice_amt;
                    $LoanCaseInvoiceDetailsNew->quo_amount = $loanCaseInvoiceDetails[$i]->quo_amount;
                    $LoanCaseInvoiceDetailsNew->remark = $loanCaseInvoiceDetails[$i]->remark;
                    $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
                    $LoanCaseInvoiceDetailsNew->status = 1;
                    $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');

                    $LoanCaseInvoiceDetailsNew->save();

                }

                $loanCaseInvoiceDetails[$i]->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt / $party_count;
                $loanCaseInvoiceDetails[$i]->save();
            }

            $this->updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $LoanCaseBillMain->case_id, $invoice_main_id_new);

        }

        
        return response()->json(['status' => 1, 'message' => 'Invoice split successfully']);

        // $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        //  $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $LoanCaseBillMain->case_id)
        //     ->count();


        // if (count($loanCaseInvoiceDetails) > 0) {
        //     for ($i = 0; $i < count($loanCaseInvoiceDetails); $i++) {

        //         $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();

        //         $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
        //         $LoanCaseInvoiceDetailsNew->account_item_id = $loanCaseInvoiceDetails[$i]->account_item_id;
        //         $LoanCaseInvoiceDetailsNew->quotation_item_id = $loanCaseInvoiceDetails[$i]->id;
        //         $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
        //         $LoanCaseInvoiceDetailsNew->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt/$party_count;
        //         $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $loanCaseInvoiceDetails[$i]->ori_invoice_amt;
        //         $LoanCaseInvoiceDetailsNew->quo_amount = $loanCaseInvoiceDetails[$i]->quo_amount;
        //         $LoanCaseInvoiceDetailsNew->remark = $loanCaseInvoiceDetails[$i]->remark;
        //         $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
        //         $LoanCaseInvoiceDetailsNew->status = 1;
        //         $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');

        //         $LoanCaseInvoiceDetailsNew->save();

        //         $loanCaseInvoiceDetails[$i]->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt/$party_count;
        //         $loanCaseInvoiceDetails[$i]->save();
        //     }
        // }
    }

    public static function updateInvoiceDetailsAmtBak($invoice_main_id, $bill_main_id, $case_id, $invoice_main_id_new = "")
    {

        $current_user = auth()->user();
        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        // $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $case_id)
        //     ->where('invoice_main_id','!=', 0)
        //     ->count();

        $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        $sum = 0;

        if (count($loanCaseInvoiceDetails) > 0) {
            for ($i = 0; $i < count($loanCaseInvoiceDetails); $i++) {

                if ($invoice_main_id_new != "") {
                    $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();

                    $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
                    $LoanCaseInvoiceDetailsNew->account_item_id = $loanCaseInvoiceDetails[$i]->account_item_id;
                    $LoanCaseInvoiceDetailsNew->quotation_item_id = $loanCaseInvoiceDetails[$i]->id;
                    $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
                    $LoanCaseInvoiceDetailsNew->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt / $party_count;
                    $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $loanCaseInvoiceDetails[$i]->ori_invoice_amt;
                    $LoanCaseInvoiceDetailsNew->quo_amount = $loanCaseInvoiceDetails[$i]->quo_amount;
                    $LoanCaseInvoiceDetailsNew->remark = $loanCaseInvoiceDetails[$i]->remark;
                    $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
                    $LoanCaseInvoiceDetailsNew->status = 1;
                    $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');

                    $LoanCaseInvoiceDetailsNew->save();

                }

                $loanCaseInvoiceDetails[$i]->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt / $party_count;
                $loanCaseInvoiceDetails[$i]->save();
            }

            $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("amount");
            $total_sst = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("sst");
            // $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("ori_invoice_amt");

            $total_amt = ($total_amt + $total_sst)/$party_count;

            LoanCaseInvoiceMain::where("loan_case_main_bill_id", $bill_main_id)->update(["amount" => $total_amt]);

        }
    }

    // public static function updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $case_id, $invoice_main_id_new = "")
    // {

    //     $current_user = auth()->user();

    //     $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->get();

    //     $party_count = EInvoiceContoller::getPartyCount($bill_main_id);

    //     LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)
    //         ->update([
    //             'amount' => DB::raw("ori_invoice_amt / {$party_count}"),
    //         ]);

    //     $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("amount");
    //     $total_sst = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("sst");

    //     $total_amt = ($total_amt + $total_sst) / $party_count;
    //     // $total_amt = ($total_amt) / $party_count;

    //     LoanCaseInvoiceMain::where("id", $invoice_main_id)->update(["amount" => $total_amt]);

    // }

    public static function updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $case_id, $invoice_main_id_new = "")
    {

        $party_count = EInvoiceContoller::getPartyCount($bill_main_id);

        LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)
            ->update([
                'amount' => DB::raw("ori_invoice_amt / {$party_count}"),
            ]);

        $total_amt_inv = LoanCaseBillMain::where("id", $bill_main_id)->sum("total_amt_inv");
        //$total_amt_inv = LoanCaseinvoiceMain::where("id", $bill_main_id)->sum("total_amt_inv");
        $pfee1_inv = LoanCaseBillMain::where("id", $bill_main_id)->sum("pfee1_inv");
        $pfee2_inv = LoanCaseBillMain::where("id", $bill_main_id)->sum("pfee2_inv");
        $sst_inv = LoanCaseBillMain::where("id", $bill_main_id)->sum("sst_inv");
        $reimbursement_amount = LoanCaseBillMain::where("id", $bill_main_id)->sum("reimbursement_amount");
        $reimbursement_sst = LoanCaseBillMain::where("id", $bill_main_id)->sum("reimbursement_sst");

        $total_amt = ($total_amt_inv) / $party_count;
        $total_pfee1 = ($pfee1_inv) / $party_count;
        $total_pfee2 = ($pfee2_inv) / $party_count;
        $total_sst = ($sst_inv) / $party_count;
        $total_reimbursement_amount = ($reimbursement_amount) / $party_count;
        $total_reimbursement_sst = ($reimbursement_sst) / $party_count;
        LoanCaseInvoiceMain::where("loan_case_main_bill_id", $bill_main_id)->update([
            "amount" => $total_amt,
            "pfee1_inv" => $total_pfee1,
            "pfee2_inv" => $total_pfee2,
            "sst_inv" => $total_sst,
            "reimbursement_amount" => $total_reimbursement_amount,
            "reimbursement_sst" => $total_reimbursement_sst
        ]);

    }

    public static function getPartyCount($id)
    {
        $party_count= 1;
        $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
            ->count();

        if ($party_count <= 0)
        {
            $party_count= 1;
        }

        return $party_count;
    }

    public function generateNewInvNo($id, $invoice_main_id, $is_revert)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        if ($LoanCase->branch_id == 2) {
            $running_no = $LoanCase->case_running_no;
            $newPuchong =  substr($running_no, 0, 1) === "7";

            if ($newPuchong == 1) {
                $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                    ->where('parameter_value_2', 'A')->first();

                if ($LoanCaseBillMain->sst_rate == 6) {
                    $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                        ->where('parameter_value_2', 'A')->first();
                } else if ($LoanCaseBillMain->sst_rate == 8) {
                    $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                        ->where('parameter_value_2', 'A')->first();
                }
            } else {
                if ($LoanCaseBillMain->sst_rate == 6) {
                    $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
                } else if ($LoanCaseBillMain->sst_rate == 8) {
                    $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
                }
            }
        } else {
            // $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();

            if ($LoanCaseBillMain->sst_rate == 6) {
                $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
            } else if ($LoanCaseBillMain->sst_rate == 8) {
                $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
            }
        }

        $blnFound = 0;
        $breakCount = 0;

        $running_no = (int)$parameter->parameter_value_1;

        if ($is_revert == true) {
            $running_no = (int)$parameter->parameter_value_1;
            $running_no = $running_no - 1;
        } else {
            while ($blnFound == 0 && $breakCount < 20) {
                $running_no += 1;

                $LoanCaseInvoiceMainCheck = LoanCaseInvoiceMain::where('invoice_no', $parameter->parameter_value_2 . $running_no)->count();

                if ($LoanCaseInvoiceMainCheck == 0) {
                    $blnFound = 1;
                }

                $breakCount += 1;
            }

            $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', $invoice_main_id)->first();

            $LoanCaseInvoiceMain->invoice_no = $parameter->parameter_value_2 . $running_no;
            $LoanCaseInvoiceMain->save();
        }

        $parameter->parameter_value_1 = $running_no;
        $parameter->save();
    }


    public function getTransferFeeAddBillList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
                ->where('b.transferred_to_office_bank', '=',  0)
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                $rows = $rows->whereIn('b.id', $transfer_list);
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if ($current_user->branch_id == 3) {
                    $rows = $rows->where('l.branch_id', '=',  3);
                }
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
                    <input id="selected_amt_' . $data->id . '" type="hidden" value="' . $bal_to_transfer . '" />';
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
                ->editColumn('client_profile_completed', function ($row) {
                    return $row->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })

                ->editColumn('invoice_date', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                    return $formatedDate;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'client_profile_completed', 'case_ref_no', 'bal_to_transfer_v2', 'pfee_sum', 'sst_to_transfer'])
                ->make(true);
        }
    }

    public function einvoice_billto()
    {
        return view('dashboard.einvoice-client.index');
    }

    public function getEInvoiceClientList()
    {
        $current_user = auth()->user();
        $customer_id_list = [];
        $BillingParties = DB::table('invoice_billing_party as ibp')
            ->leftJoin('loan_case_bill_main as lcbm', 'lcbm.id', '=', 'ibp.loan_case_main_bill_id')
            ->leftJoin('loan_case as lc', 'lc.id', '=', 'ibp.case_id')
            ->select(
                'ibp.id',
                'ibp.customer_code',
                'ibp.loan_case_main_bill_id',
                'ibp.case_id',
                'ibp.customer_name',
                'ibp.brn',
                'ibp.brn2',
                'ibp.sales_tax_no',
                'ibp.service_tax_no',
                'ibp.customer_category',
                'ibp.id_no',
                'ibp.tin',
                'ibp.address_1',
                'ibp.address_2',
                'ibp.address_3',
                'ibp.address_4',
                'ibp.postcode',
                'ibp.city',
                'ibp.state',
                'ibp.country',
                'ibp.phone1',
                'ibp.mobile',
                'ibp.fax1',
                'ibp.fax2',
                'ibp.email',
                'ibp.completed',
                'ibp.sent_to_sql',
                'ibp.created_at',
                'lc.case_ref_no' // Include case reference number if needed
            );

        if (!in_array($current_user->menuroles,  ['admin', 'management', 'account'])) {

            $ids = CaseController::getCaseListHub('array', 'customer_id');
            $customer_id_list = array_merge($customer_id_list, $ids);

            $BillingParties = $BillingParties->whereIn('ibp.id', $customer_id_list);
        }


        // else if (in_array($current_user->menuroles,  ['lawyer'])) {

        // return $customer_id_list;


        //     if (in_array($current_user->id,  [118]))
        //     {
        //         $Customer = $Customer->whereIn('l.sales_user_id', [$current_user->id, 32]);
        //     }
        //     else
        //     {
        //         $Customer = $Customer->where('l.sales_user_id', '=', $current_user->id);
        //     }
        // }


        $BillingParties = $BillingParties->get();

        if (count($BillingParties) > 0) {
            for ($i = 0; $i < count($BillingParties); $i++) {
                $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('customer_id', '=', $BillingParties[$i]->id)->count();
                $BillingParties[$i]->case_count = $LoanCaseCount;
            }
        }

        return DataTables::of($BillingParties)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = ' <a  href="/einvoice-client-edit/' . $row->id . '"  class="btn btn-info shadow sharp mr-1" title="Edit"><i class="cil-pencil"></i></a>';
                return $actionBtn;
            })
            ->editColumn('case_id', function ($row) {
                return '<a href="/case/' . $row->case_id . '">' . $row->case_ref_no . '</a>';
            })
            ->editColumn('completed', function ($row) {
                return $row->completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
            })
            ->editColumn('sent_to_sql', function ($row) {
                return $row->sent_to_sql == 1 ? '<span class="label label-success">Sent</span>' : '<span class="label label-warning">Pending</span>';
            })
            ->editColumn('customer_category', function ($row) {
                if ($row->customer_category == 1) return 'Individual';
                if ($row->customer_category == 2) return 'Company';
                return '';
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
            })
            ->rawColumns(['action', 'case_id', 'completed', 'sent_to_sql'])
            ->make(true);
    }

    public function einvoiceBilltoEdit($id)
    {
        $current_user = auth()->user();

        // Retrieve billing party record from invoice_billing_party table
        $billingParty = InvoiceBillingParty::where('id', $id)->first();

        if (!$billingParty) {
            abort(404, 'Billing party not found');
        }

        // Retrieve LoanCase records associated with the case_id from the billing party
        $LoanCase = DB::table('loan_case as l')
            ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
            ->select('l.*', 'u1.name as lawyer', 'u2.name as clerk')
            ->where('l.id', '=', $billingParty->case_id) // Filter by case_id from billing party
            ->get();


        return view('dashboard.einvoice-client.edit', ['billingParty' => $billingParty, 'LoanCase' => $LoanCase]);
    }

    public function generateClientLink(Request $request, $id)
    {
        try {
            $billingParty = InvoiceBillingParty::find($id);

            if (!$billingParty) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Billing party not found'
                ], 404);
            }

            // Generate token if it doesn't exist
            if (empty($billingParty->token)) {
                $billingParty->token = $this->generateUniqueToken();
                $billingParty->save();
            }

            // Generate the client link
            $clientLink = url('/client-einvoice-data/' . $billingParty->token);

            return response()->json([
                'status' => 1,
                'message' => 'Client link generated successfully',
                'client_link' => $clientLink,
                'token' => $billingParty->token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error generating client link: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateUniqueToken()
    {
        do {
            $token = bin2hex(random_bytes(32)); // Generate 64 character hex string
        } while (InvoiceBillingParty::where('token', $token)->exists());

        return $token;
    }

    public function clientEinvoiceData($token)
    {
        try {
            // Find the billing party by token
            $billingParty = InvoiceBillingParty::where('token', $token)->first();

            if (!$billingParty) {
                abort(404, 'Invalid or expired link');
            }

            // Get related case information
            $case = DB::table('loan_case as l')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('l.*', 'c.name as client_name')
                ->where('l.id', '=', $billingParty->case_id)
                ->first();

            // Get invoice information
            $invoice = null;
            if ($billingParty->invoice_main_id) {
                $invoice = DB::table('loan_case_invoice_main as im')
                    ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
                    ->select('im.*', 'bm.invoice_date', 'bm.total_amt_inv')
                    ->where('im.id', '=', $billingParty->invoice_main_id)
                    ->first();
            }

            return view('dashboard.einvoice-client.client-view', [
                'billingParty' => $billingParty,
                'case' => $case,
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            abort(500, 'Error accessing e-invoice data');
        }
    }

    public function updateClientEinvoiceData(Request $request, $token)
    {
        try {
            // Find the billing party by token
            $billingParty = InvoiceBillingParty::where('token', $token)->first();

            if (!$billingParty) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid or expired link'
                ], 404);
            }

            // Update all fields
            $billingParty->customer_category = $request->input('customer_category');
            $billingParty->tin = $request->input('tin');
            $billingParty->brn = $request->input('brn');
            $billingParty->brn2 = $request->input('brn2');
            $billingParty->sales_tax_no = $request->input('sales_tax_no');
            $billingParty->service_tax_no = $request->input('service_tax_no');
            $billingParty->id_no = $request->input('id_no');
            $billingParty->address_1 = $request->input('address_1');
            $billingParty->address_2 = $request->input('address_2');
            $billingParty->address_3 = $request->input('address_3');
            $billingParty->address_4 = $request->input('address_4');
            $billingParty->postcode = $request->input('postcode');
            $billingParty->city = $request->input('city');
            $billingParty->state = $request->input('state');
            $billingParty->country = $request->input('country');
            $billingParty->phone1 = $request->input('phone1');
            $billingParty->mobile = $request->input('mobile');
            $billingParty->fax1 = $request->input('fax1');
            $billingParty->email = $request->input('email');

            // Check mandatory fields
            $mandatoryFields = self::getEinvoiceCustomerMandatoryField();
            $allMandatoryFieldsFilled = true;

            foreach ($mandatoryFields as $field) {
                if (empty($billingParty->$field)) {
                    $allMandatoryFieldsFilled = false;
                    break;
                }
            }

            // Update completed status
            $billingParty->completed = $allMandatoryFieldsFilled ? 1 : 0;
            $billingParty->save();

            // Update related EInvoiceDetails and EInvoiceMain if needed
            if ($billingParty->loan_case_main_bill_id) {
                $EInvoiceDetails = EInvoiceDetails::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->first();
                if ($EInvoiceDetails) {
                    $EInvoiceDetails->client_profile_completed = $allMandatoryFieldsFilled ? 1 : 0;
                    $EInvoiceDetails->save();

                    // Check all related EInvoiceDetails for this einvoice_main
                    $EInvoiceDetailsAll = EInvoiceDetails::where('einvoice_main_id', $EInvoiceDetails->einvoice_main_id)->get();
                    $allRelatedCompleted = true;
                    foreach ($EInvoiceDetailsAll as $relatedDetail) {
                        if ($relatedDetail->client_profile_completed == 0) {
                            $allRelatedCompleted = false;
                            break;
                        }
                    }

                    EInvoiceMain::where('id', $EInvoiceDetails->einvoice_main_id)->update(['client_profile_completed' => $allRelatedCompleted]);
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Information updated successfully',
                'completed' => $allMandatoryFieldsFilled
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error updating information: ' . $e->getMessage()
            ], 500);
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
            }
            if (in_array($current_user->menuroles, ['lawyer'])) {
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
                if (in_array($current_user->branch_id, [5, 6])) {
                    $rows = $rows->whereIn('l.branch_id', [5, 6]);
                } else if (in_array($current_user->branch_id, [2])) {
                    $rows = $rows->whereIn('l.sales_user_id', [13]);
                } else {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
            } else  if (in_array($current_user->menuroles, ['lawyer'])) {
                if (in_array($current_user->id, [13])) {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id])->where('l.id', '>=', 2342);
                } else {
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
                if (in_array($current_user->branch_id, [5, 6])) {
                    $rows = $rows->whereIn('l.branch_id', [5, 6]);
                } else {
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

                ->editColumn('client_profile_completed', function ($row) {
                    return $row->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'client_profile_completed', 'case_ref_no'])
                ->make(true);
        }
    }

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
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 5)->get();
            } else if ($current_user->branch_id == 2) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 2)->get();
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
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
    public function destroy($id, Request $request) {}

    public function sendInvoicesToSQL(Request $request)
    {
        try {
            $invoiceIds = json_decode($request->input('invoice_ids'), true);
            $einvoiceMainId = $request->input('einvoice_main_id');

            if (empty($invoiceIds)) {
                return response()->json(['status' => 0, 'message' => 'No invoice IDs provided.'], 400);
            }

            // Update einvoice_status for the selected invoices in einvoice_details table
            EInvoiceDetails::whereIn('loan_case_invoice_id', $invoiceIds)
                ->where('einvoice_main_id', $einvoiceMainId)
                ->update(['einvoice_status' => 'SQL']);

            // Update sent_to_sql in invoice_billing_party
            InvoiceBillingParty::whereIn('loan_case_main_bill_id', $invoiceIds)
                ->update(['sent_to_sql' => 1]);

            // Optionally, check if all associated einvoice_details for this einvoice_main are 'SQL' and update einvoice_main batch_status
            $allDetailsSent = EInvoiceDetails::where('einvoice_main_id', $einvoiceMainId)->where('einvoice_status', '<>', 'SQL')->doesntExist();

            if ($allDetailsSent) {
                EInvoiceMain::where('id', $einvoiceMainId)->update(['batch_status' => 'SQL']);
            }

            return response()->json(['status' => 1, 'message' => 'Invoices status updated successfully to SQL.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating invoice status: ' . $e->getMessage()], 500);
        }
    }

    public function sendInvoicesToLHDN(Request $request)
    {
        try {
            $invoiceIds = json_decode($request->input('invoice_ids'), true);
            $einvoiceMainId = $request->input('einvoice_main_id');

            if (empty($invoiceIds)) {
                return response()->json(['status' => 0, 'message' => 'No invoice IDs provided.'], 400);
            }

            // Update einvoice_status for the selected invoices in einvoice_details table
            EInvoiceDetails::whereIn('loan_case_invoice_id', $invoiceIds)
                ->where('einvoice_main_id', $einvoiceMainId)
                ->update(['einvoice_status' => 'LHDN']);

            // Optionally, check if all associated einvoice_details for this einvoice_main are 'LHDN' and update einvoice_main batch_status
            $allDetailsSent = EInvoiceDetails::where('einvoice_main_id', $einvoiceMainId)->where('einvoice_status', '<>', 'LHDN')->doesntExist();

            if ($allDetailsSent) {
                EInvoiceMain::where('id', $einvoiceMainId)->update(['batch_status' => 'LHDN']);
            }

            return response()->json(['status' => 1, 'message' => 'Invoices status updated successfully to LHDN.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating invoice status: ' . $e->getMessage()], 500);
        }
    }

    public function updateEinvoice(Request $request, $id)
    {
        try {
            $eInvoiceMain = EInvoiceMain::find($id);

            if (!$eInvoiceMain) {
                return response()->json(['status' => 0, 'message' => 'E-Invoice record not found.'], 404);
            }

            // Update fields from the request
            $eInvoiceMain->einvoice_date = $request->input('einvoice_date');
            // $eInvoiceMain->ref_no = $request->input('ref_no');
            // total_amount is read-only in the form, so we don't update it here
            $eInvoiceMain->transaction_id = $request->input('transaction_id');
            $eInvoiceMain->branch_id = $request->input('branch_id');
            $eInvoiceMain->batch_status = $request->input('batch_status');
            $eInvoiceMain->description = $request->input('description');
            $eInvoiceMain->pfee_only = $request->input('pfee_only', 0);

            $eInvoiceMain->save();

            return response()->json(['status' => 1, 'message' => 'E-Invoice record updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating E-Invoice record: ' . $e->getMessage()], 500);
        }
    }

    public function saveEinvoice(Request $request)
    {
        try {
            // Start database transaction
            DB::beginTransaction();

            // Get the authenticated user
            $user = auth()->user();

            // Get parameters for generating ref_no
            $prefixParameter = Parameter::where('parameter_type', '=', 'sql_einvoice_ref_prefix')->first();
            $runningNoParameter = Parameter::where('parameter_type', '=', 'sql_einvoice_ref_running_no')->first();

            if (!$prefixParameter || !$runningNoParameter) {
                return response()->json([
                    'status' => 0,
                    'message' => 'E-invoice reference number parameters not found.'
                ]);
            }

            $prefix = $prefixParameter->parameter_value_1;
            $currentRunningNo = (int)$runningNoParameter->parameter_value_1;
            $newRunningNo = $currentRunningNo + 1;

            // Generate the new ref_no
            $generatedRefNo = $prefix . str_pad($newRunningNo, 5, '0', STR_PAD_LEFT);

            // Update the running number in the parameter table
            $runningNoParameter->parameter_value_1 = $newRunningNo;
            $runningNoParameter->save();

            // Validate required fields (excluding ref_no as it's generated)
            if (!$request->has('transaction_id')) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Required fields are missing' // transaction_id is mandatory now
                ]);
            }

            // Create main e-invoice record
            $einvoiceMain = new EInvoiceMain();
            $einvoiceMain->ref_no = $generatedRefNo; // Use the generated ref_no
            $einvoiceMain->einvoice_date = $request->input('einvoice_date');
            // $einvoiceMain->total_amount = $request->input('total_amount') ?: 0; // Use input() to get value
            $einvoiceMain->transaction_id = $request->input('transaction_id');
            $einvoiceMain->branch_id = $request->input('branch_id');
            $einvoiceMain->description = $request->input('description');
            $einvoiceMain->batch_status = $request->input('batch_status') ?: 'NOTSENT';
            $einvoiceMain->pfee_only = $request->input('pfee_only', 0);
            $einvoiceMain->created_by = 1;
            $einvoiceMain->status = 1; // Assuming status 1 means active
            $einvoiceMain->created_at = now();
            $einvoiceMain->updated_at = now();
            $einvoiceMain->save();

            $einvoiceId = $einvoiceMain->id;

            // Process invoice items from 'add_invoice'
            if ($request->has('add_invoice')) {
                $add_bills = json_decode($request->input('add_invoice'), true);

                foreach ($add_bills as $bill) {
                    // Assuming $bill has 'id', 'value', and 'sst'
                    $einvoiceDetails = new EInvoiceDetails();
                    $einvoiceDetails->einvoice_main_id = $einvoiceId;
                    $einvoiceDetails->loan_case_main_bill_id = $bill['bill_id']; // Use 'id' from the bill item
                    $einvoiceDetails->loan_case_invoice_id = $bill['id']; // Use 'id' from the bill item
                    $einvoiceDetails->case_id = $bill['case_id']; // Use 'id' from the bill item
                    $einvoiceDetails->created_by = 1;
                    // $einvoiceDetails->amt = $bill['value']; // Use 'value' from the bill item for amount
                    $einvoiceDetails->einvoice_status = 'SENT'; // Default status for new details
                    $einvoiceDetails->status = 1;
                    $einvoiceDetails->created_at = now();
                    $einvoiceDetails->updated_at = now();
                    $einvoiceDetails->save();

                    // Optionally update related LoanCaseBillMain if needed, similar to AddInvoiceIntoEInvoice
                    LoanCaseBillMain::where('id', '=', $bill['id'])->update(['sql_submit' => 1]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'E-invoice record created successfully ',
                'einvoice_id' => $einvoiceId
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log the error
            Log::error('Error creating e-invoice: ' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while saving the e-invoice: ' . $e->getMessage()
            ], 500); // Use 500 for internal server error
        }
    }

    /**
     * Search billing parties API
     */
    public function searchBillingParties(Request $request)
    {
        try {
            $current_user = auth()->user();
            $perPage = 10;
            $page = $request->input('page', 1);
            
            $query = DB::table('invoice_billing_party as ibp')
                ->select(
                    'ibp.id',
                    'ibp.customer_name',
                    'ibp.customer_code',
                    'ibp.id_no',
                    'ibp.tin',
                    'ibp.email',
                    'ibp.phone1 as phone',
                    'ibp.brn',
                    'ibp.brn2',
                    'ibp.sales_tax_no',
                    'ibp.service_tax_no',
                    'ibp.customer_category',
                    'ibp.address_1',
                    'ibp.address_2',
                    'ibp.address_3',
                    'ibp.address_4',
                    'ibp.postcode',
                    'ibp.city',
                    'ibp.state',
                    'ibp.country',
                    'ibp.created_at'
                )
                ->where('ibp.status', 1);

            // Apply search filters
            if ($request->input('name')) {
                $query->where('ibp.customer_name', 'LIKE', '%' . $request->input('name') . '%');
            }
            
            if ($request->input('id_no')) {
                $query->where('ibp.id_no', 'LIKE', '%' . $request->input('id_no') . '%');
            }
            
            if ($request->input('tin')) {
                $query->where('ibp.tin', 'LIKE', '%' . $request->input('tin') . '%');
            }

            // Apply user access control - admin, management, account, maker, and lawyer users should see all billing parties
            if (!in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker', 'lawyer'])) {
                $ids = CaseController::getCaseListHub('array', 'customer_id');
                $query->whereIn('ibp.case_id', $ids);
            }

            // Get total count for pagination
            $totalCount = $query->count();
            
            // Get paginated results
            $parties = $query->orderBy('ibp.customer_name', 'ASC')
                           ->offset(($page - 1) * $perPage)
                           ->limit($perPage)
                           ->get();

            $pagination = [
                'current_page' => $page,
                'total_pages' => ceil($totalCount / $perPage),
                'total_count' => $totalCount,
                'per_page' => $perPage
            ];

            return response()->json([
                'status' => 1,
                'data' => $parties,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching billing parties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific billing party details API
     */
    public function getBillingParty($id)
    {
        try {
            $current_user = auth()->user();
            
            $party = DB::table('invoice_billing_party as ibp')
                ->select(
                    'ibp.id',
                    'ibp.customer_name',
                    'ibp.customer_code',
                    'ibp.id_no',
                    'ibp.tin',
                    'ibp.email',
                    'ibp.phone1 as phone',
                    'ibp.brn',
                    'ibp.brn2',
                    'ibp.sales_tax_no',
                    'ibp.service_tax_no',
                    'ibp.customer_category',
                    'ibp.address_1',
                    'ibp.address_2',
                    'ibp.address_3',
                    'ibp.address_4',
                    'ibp.postcode',
                    'ibp.city',
                    'ibp.state',
                    'ibp.country',
                    'ibp.created_at'
                )
                ->where('ibp.id', $id)
                ->where('ibp.status', 1)
                ->first();

            if (!$party) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Billing party not found'
                ], 404);
            }

            // Apply user access control
            if (!in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker', 'lawyer'])) {
                $ids = CaseController::getCaseListHub('array', 'customer_id');
                $partyExists = DB::table('invoice_billing_party')
                    ->where('id', $id)
                    ->whereIn('case_id', $ids)
                    ->exists();
                
                if (!$partyExists) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Access denied to this billing party'
                    ], 403);
                }
            }

            return response()->json([
                'status' => 1,
                'data' => $party
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting billing party details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new billing party API
     */
    public function createBillingParty(Request $request)
    {
        try {
            $current_user = auth()->user();

            // Debug logging
            \Illuminate\Support\Facades\Log::info('Create billing party request data:', $request->all());

            // Validate required fields
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'customer_category' => 'required|in:1,2',
                'id_type' => 'required|in:1,2,3,4',
                'tin' => 'nullable|string|max:255',
                'id_no' => 'nullable|string|max:255',
                'address_1' => 'nullable|string|max:255',
                'phone1' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 0,
                    'message' => 'Validation failed: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate customer code using existing Parameter table logic
            $Parameter = Parameter::where('parameter_type', '=', 'sql_client_code_running_no')->first();
            $SQL_prefix = Parameter::where('parameter_type', '=', 'sql_client_code_prefix')->value('parameter_value_1');

            $nextRunningNo = str_pad((int)$Parameter->parameter_value_1, 5, '0', STR_PAD_LEFT);
            $customerCode = $SQL_prefix . '-' . $nextRunningNo;

            // Create new billing party
            $billingParty = new InvoiceBillingParty();
            $billingParty->customer_name = $request->input('customer_name');
            $billingParty->customer_code = $customerCode;
            $billingParty->customer_category = $request->input('customer_category');
            $billingParty->id_type = $request->input('id_type');
            $billingParty->id_no = $request->input('id_no');
            $billingParty->tin = $request->input('tin');
            $billingParty->brn = $request->input('brn');
            $billingParty->brn2 = $request->input('brn2');
            $billingParty->sales_tax_no = $request->input('sales_tax_no');
            $billingParty->service_tax_no = $request->input('service_tax_no');
            $billingParty->email = $request->input('email');
            $billingParty->phone1 = $request->input('phone1');
            $billingParty->mobile = $request->input('mobile');
            $billingParty->fax1 = $request->input('fax1');
            $billingParty->fax2 = $request->input('fax2');
            $billingParty->address_1 = $request->input('address_1');
            $billingParty->address_2 = $request->input('address_2');
            $billingParty->address_3 = $request->input('address_3');
            $billingParty->address_4 = $request->input('address_4');
            $billingParty->postcode = $request->input('postcode');
            $billingParty->city = $request->input('city');
            $billingParty->state = $request->input('state');
            $billingParty->country = $request->input('country', 'Malaysia');
            
            // Set case and bill information if provided
            $billingParty->case_id = $request->input('case_id');
            $billingParty->loan_case_main_bill_id = $request->input('loan_case_main_bill_id');
            
            // Set default values
            $billingParty->completed = 0;
            $billingParty->sent_to_sql = 0;
            $billingParty->status = 1;
            $billingParty->created_at = date('Y-m-d H:i:s');
            $billingParty->created_by = $current_user->id;

            $billingParty->save();

            // Increment the running number in Parameter table
            $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
            $Parameter->save();

            return response()->json([
                'status' => 1,
                'message' => 'Billing party created successfully',
                'data' => [
                    'id' => $billingParty->id,
                    'customer_name' => $billingParty->customer_name,
                    'customer_code' => $billingParty->customer_code
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error creating billing party: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Search clients API
     */
    public function searchClients(Request $request)
    {
        try {
            $current_user = auth()->user();
            $perPage = 10;
            $page = $request->input('page', 1);
            
            $query = DB::table('client as c')
                ->select(
                    'c.id',
                    'c.name',
                    'c.ic_no',
                    'c.account_no',
                    'c.phone_no',
                    'c.email',
                    'c.client_type',
                    'c.address',
                    'c.mailing_state',
                    'c.mailing_postcode',
                    'c.mailing_country',
                    'c.billing_state',
                    'c.billing_postcode',
                    'c.billing_country'
                )
                ->where('c.status', 1);

            // Apply search filters
            if ($request->input('name')) {
                $query->where('c.name', 'LIKE', '%' . $request->input('name') . '%');
            }
            
            if ($request->input('ic_no')) {
                $query->where('c.ic_no', 'LIKE', '%' . $request->input('ic_no') . '%');
            }
            
            if ($request->input('account_no')) {
                $query->where('c.account_no', 'LIKE', '%' . $request->input('account_no') . '%');
            }

            // Apply user access control if needed
            if (!in_array($current_user->menuroles, ['admin', 'management'])) {
                // Add any client access restrictions here if needed
            }

            // Get total count for pagination
            $totalCount = $query->count();
            
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Client search - Total count: ' . $totalCount);
            \Illuminate\Support\Facades\Log::info('Client search - Query: ' . $query->toSql());
            \Illuminate\Support\Facades\Log::info('Client search - Bindings: ' . json_encode($query->getBindings()));
            
            // Get paginated results
            $clients = $query->orderBy('c.name', 'ASC')
                           ->offset(($page - 1) * $perPage)
                           ->limit($perPage)
                           ->get();
            
            \Illuminate\Support\Facades\Log::info('Client search - Results count: ' . $clients->count());

            $pagination = [
                'current_page' => $page,
                'total_pages' => ceil($totalCount / $perPage),
                'total_count' => $totalCount,
                'per_page' => $perPage
            ];

            return response()->json([
                'status' => 1,
                'data' => $clients,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching clients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Combined search for clients and masterlist parties
     */
    public function searchParties(Request $request)
    {
        try {
            $current_user = auth()->user();
            $perPage = 10;
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $sourceFilter = $request->input('source_filter', 'both'); // 'client', 'masterlist', 'both'
            
            $allResults = collect();
            
            // Search clients if source filter allows
            if (in_array($sourceFilter, ['client', 'both'])) {
                $clients = $this->searchClientParties($search);
                \Illuminate\Support\Facades\Log::info('Client search results count: ' . $clients->count());
                $allResults = $allResults->merge($clients);
            }
            
            // Search masterlist if source filter allows
            if (in_array($sourceFilter, ['masterlist', 'both'])) {
                $masterlistParties = $this->searchMasterlistParties($search);
                \Illuminate\Support\Facades\Log::info('Masterlist search results count: ' . $masterlistParties->count());
                $allResults = $allResults->merge($masterlistParties);
            }
            
            // Sort combined results by name
            $allResults = $allResults->sortBy('name')->values();
            
            // Use Laravel's LengthAwarePaginator for proper pagination
            $totalCount = $allResults->count();
            $currentPageItems = $allResults->forPage($page, $perPage);
            
            // Create pagination data
            $pagination = [
                'current_page' => (int)$page,
                'total_pages' => (int)ceil($totalCount / $perPage),
                'total_count' => (int)$totalCount,
                'per_page' => (int)$perPage,
                'has_next_page' => $page < ceil($totalCount / $perPage),
                'has_prev_page' => $page > 1
            ];
            
            \Illuminate\Support\Facades\Log::info('Combined party search - Total results: ' . $totalCount);
            \Illuminate\Support\Facades\Log::info('Combined party search - Source filter: ' . $sourceFilter);
            \Illuminate\Support\Facades\Log::info('Combined party search - Page: ' . $page . ', Items on this page: ' . $currentPageItems->count());
            \Illuminate\Support\Facades\Log::info('Combined party search - Current page items: ' . json_encode($currentPageItems->take(3)->toArray()));
            
            $dataArray = $currentPageItems->toArray();
            
            return response()->json([
                'status' => 1,
                'data' => $dataArray, // Ensure data is always an array
                'pagination' => $pagination
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching parties: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search client parties (no pagination - pagination applied to combined results)
     */
    private function searchClientParties($search)
    {
        $query = DB::table('client as c')
            ->select(
                DB::raw("'client' as source"),
                'c.id',
                'c.name',
                'c.ic_no as id_no',
                'c.account_no',
                'c.phone_no as phone1',
                'c.email',
                'c.client_type',
                'c.income_tax_no as tin',
                'c.address',
                'c.mailing_state as state',
                'c.mailing_postcode as postcode',
                'c.mailing_country as country',
                DB::raw("NULL as case_id"),
                DB::raw("NULL as case_ref_no"),
                DB::raw("NULL as party_type"),
                DB::raw("NULL as party_category")
            )
            ->where('c.status', 1)
            ->where(function($q) use ($search) {
                $q->where('c.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.ic_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.account_no', 'LIKE', '%' . $search . '%');
            });
            
        return $query->orderBy('c.name', 'ASC')->get();
    }
    
    /**
     * Search masterlist parties (no pagination - pagination applied to combined results)
     */
    private function searchMasterlistParties($search)
    {
        // Get individual parties (Purchaser 1-6, Vendor 1-6, Borrower 1-6, Guarantor 1-6)
        $individualParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'individual' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name LIKE 'Purchaser%' THEN 'Purchaser'
                    WHEN cmfc.name LIKE 'Vendor%' THEN 'Vendor'
                    WHEN cmfc.name LIKE 'Borrower%' THEN 'Borrower'
                    WHEN cmfc.name LIKE 'Guarantor%' THEN 'Guarantor'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->where(function($query) {
                $query->where('cmfc.name', 'LIKE', 'Purchaser%')
                      ->orWhere('cmfc.name', 'LIKE', 'Vendor%')
                      ->orWhere('cmfc.name', 'LIKE', 'Borrower%')
                      ->orWhere('cmfc.name', 'LIKE', 'Guarantor%');
            })
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->where('lc.status', '!=', 'deleted');
            
        // Get company parties (Purchaser Company, Vendor Company, Borrower Company, Guarantor Company)
        $companyParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'company' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name = 'Purchaser Company' THEN 'Purchaser Company'
                    WHEN cmfc.name = 'Vendor Company' THEN 'Vendor Company'
                    WHEN cmfc.name = 'Borrower Company' THEN 'Borrower Company'
                    WHEN cmfc.name = 'Guarantor Company' THEN 'Guarantor Company'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->whereIn('cmfc.name', ['Purchaser Company', 'Vendor Company', 'Borrower Company', 'Guarantor Company'])
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->where('lc.status', '!=', 'deleted');
            
        // Combine and return
        return $individualParties->union($companyParties)
                                ->orderBy('name', 'ASC')
                                ->get();
    }
    
    /**
     * Get masterlist party detailed data for billing party creation
     */
    public function getMasterlistPartyData($caseId, $partyType, $partyCategory)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Getting masterlist party data:', [
                'caseId' => $caseId,
                'partyType' => $partyType,
                'partyCategory' => $partyCategory
            ]);
            
            // Get the masterlist field IDs for the party category
            $fieldIds = DB::table('case_masterlist_field as cmf')
                ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
                ->where('cmfc.name', $partyCategory)
                ->pluck('cmf.id');
                
            \Illuminate\Support\Facades\Log::info('Field IDs found:', $fieldIds->toArray());
                
            // Get all masterlist data for this case and party
            $masterlistData = DB::table('loan_case_masterlist as lcm')
                ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
                ->where('lcm.case_id', $caseId)
                ->whereIn('lcm.masterlist_field_id', $fieldIds)
                ->select('cmf.name as field_name', 'lcm.value')
                ->get();
                
            \Illuminate\Support\Facades\Log::info('Masterlist data found:', $masterlistData->toArray());
            
            $masterlistData = $masterlistData->keyBy('field_name');
                
            // Map masterlist fields to billing party fields
            $billingPartyData = [
                'customer_name' => $masterlistData->get('Name') ? $masterlistData->get('Name')->value : '',
                'id_no' => $masterlistData->get('NRIC') ? $masterlistData->get('NRIC')->value : '',
                'tin' => $masterlistData->get('Income Tax No') ? $masterlistData->get('Income Tax No')->value : '',
                'brn' => $masterlistData->get('Company No') ? $masterlistData->get('Company No')->value : '',
                'email' => $masterlistData->get('Email') ? $masterlistData->get('Email')->value : '',
                'phone1' => $masterlistData->get('Tel') ? $masterlistData->get('Tel')->value : '',
                'mobile' => $masterlistData->get('HP') ? $masterlistData->get('HP')->value : '',
                'fax1' => $masterlistData->get('Fax') ? $masterlistData->get('Fax')->value : '',
            ];
            
            // Handle address mapping
            $address = $masterlistData->get('Address') ? $masterlistData->get('Address')->value : '';
            if ($address) {
                $addressParts = $this->splitAddressComplete($address);
                $billingPartyData['address_1'] = $addressParts['address_1'] ?? '';
                $billingPartyData['address_2'] = $addressParts['address_2'] ?? '';
                $billingPartyData['address_3'] = $addressParts['address_3'] ?? '';
                $billingPartyData['address_4'] = $addressParts['address_4'] ?? '';
                $billingPartyData['postcode'] = $addressParts['postcode'] ?? '';
                $billingPartyData['city'] = $addressParts['city'] ?? '';
                $billingPartyData['state'] = $addressParts['state'] ?? '';
            }
            
            // Map country
            $country = $masterlistData->get('Country') ? $masterlistData->get('Country')->value : 'Malaysia';
            $billingPartyData['country'] = $this->mapCountryNameToCode($country);
            
            \Illuminate\Support\Facades\Log::info('Billing party data mapped:', $billingPartyData);
            
            return response()->json([
                'status' => 1,
                'data' => $billingPartyData
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getMasterlistPartyData:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 0,
                'message' => 'Error getting masterlist party data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client data mapped for billing party creation
     */
    public function getClientBillingPartyData($clientId)
    {
        try {
            $current_user = auth()->user();
            
            $client = DB::table('client as c')
                ->select(
                    'c.id',
                    'c.name',
                    'c.ic_no',
                    'c.account_no',
                    'c.phone_no',
                    'c.email',
                    'c.client_type',
                    'c.income_tax_no',
                    'c.company_ref_no',
                    'c.address',
                    'c.mailing_state',
                    'c.mailing_postcode',
                    'c.mailing_country',
                    'c.billing_state',
                    'c.billing_postcode',
                    'c.billing_country'
                )
                ->where('c.id', $clientId)
                ->where('c.status', 1)
                ->first();

            if (!$client) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Client not found'
                ], 404);
            }

            // Extract location information from address
            $locationInfo = $this->extractLocationInfo($client->address);
            
            // Debug logging for address processing
            \Illuminate\Support\Facades\Log::info('Client address processing:', [
                'client_id' => $client->id,
                'original_address' => $client->address,
                'address_1' => $this->splitAddress($client->address, 0),
                'address_2' => $this->splitAddress($client->address, 1),
                'address_3' => $this->splitAddress($client->address, 2),
                'address_4' => $this->splitAddress($client->address, 3),
                'extracted_postcode' => $locationInfo['postcode'],
                'extracted_state' => $locationInfo['state']
            ]);
            
            // Map client data to billing party format
            $billingPartyData = [
                'customer_name' => $client->name,
                'customer_code' => $client->account_no,
                'customer_category' => $client->client_type,
                'id_no' => $client->ic_no,
                'tin' => $client->income_tax_no,
                'brn' => $client->company_ref_no,
                'email' => $client->email,
                'phone1' => $client->phone_no,
                'address_1' => $this->splitAddress($client->address, 0),
                'address_2' => $this->splitAddress($client->address, 1),
                'address_3' => $this->splitAddress($client->address, 2),
                'address_4' => $this->splitAddress($client->address, 3),
                'postcode' => $client->mailing_postcode ?: $client->billing_postcode ?: $locationInfo['postcode'],
                'state' => $client->mailing_state ?: $client->billing_state ?: $locationInfo['state'],
                'country' => $this->mapCountryNameToCode($client->mailing_country ?: $client->billing_country ?: 'Malaysia'),
                'city' => '', // Client table doesn't have city field
            ];

            return response()->json([
                'status' => 1,
                'data' => $billingPartyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting client data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Split address into multiple parts with smart character limit handling
     */
    private function splitAddress($address, $partIndex)
    {
        if (empty($address)) {
            return '';
        }

        $address = trim($address);
        $maxLength = 60; // Maximum characters per address line
        
        // If address is shorter than max length, return as is for first part
        if (strlen($address) <= $maxLength) {
            return $partIndex === 0 ? $address : '';
        }

        // Split address into parts based on character limit and natural breaks
        $parts = [];
        $remaining = $address;
        
        while (strlen($remaining) > 0 && count($parts) < 4) {
            if (strlen($remaining) <= $maxLength) {
                $parts[] = trim($remaining);
                break;
            }
            
            // Find the best break point within the character limit
            $breakPoint = $this->findBestBreakPoint($remaining, $maxLength);
            
            if ($breakPoint === false) {
                // If no good break point found, force break at max length
                $parts[] = trim(substr($remaining, 0, $maxLength));
                $remaining = trim(substr($remaining, $maxLength));
            } else {
                $parts[] = trim(substr($remaining, 0, $breakPoint));
                $remaining = trim(substr($remaining, $breakPoint));
            }
        }
        
        return $parts[$partIndex] ?? '';
    }

    /**
     * Find the best break point in an address string
     */
    private function findBestBreakPoint($address, $maxLength)
    {
        // Look for natural break points in order of preference
        $breakPoints = [
            ', ', // Comma followed by space
            ' ',  // Space
            ',',  // Comma
            ';',  // Semicolon
        ];
        
        foreach ($breakPoints as $breakPoint) {
            $pos = strrpos(substr($address, 0, $maxLength), $breakPoint);
            if ($pos !== false) {
                return $pos + strlen($breakPoint);
            }
        }
        
        return false;
    }

    /**
     * Extract postcode and state from address
     */
    private function extractLocationInfo($address)
    {
        $postcode = '';
        $state = '';
        
        if (empty($address)) {
            return ['postcode' => '', 'state' => ''];
        }

        // Malaysian postcode pattern: 5 digits
        if (preg_match('/\b(\d{5})\b/', $address, $matches)) {
            $postcode = $matches[1];
        }

        // Malaysian states list
        $malaysianStates = [
            'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 
            'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 
            'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 
            'Labuan', 'Putrajaya'
        ];

        foreach ($malaysianStates as $stateName) {
            if (stripos($address, $stateName) !== false) {
                $state = $stateName;
                break;
            }
        }

        return ['postcode' => $postcode, 'state' => $state];
    }

    /**
     * Split address completely and return all parts
     */
    private function splitAddressComplete($address)
    {
        if (empty($address)) {
            return [
                'address_1' => '',
                'address_2' => '',
                'address_3' => '',
                'address_4' => '',
                'postcode' => '',
                'city' => '',
                'state' => ''
            ];
        }

        // Extract location info first
        $locationInfo = $this->extractLocationInfo($address);
        
        // Split address into parts
        $addressParts = [
            'address_1' => $this->splitAddress($address, 0),
            'address_2' => $this->splitAddress($address, 1),
            'address_3' => $this->splitAddress($address, 2),
            'address_4' => $this->splitAddress($address, 3),
            'postcode' => $locationInfo['postcode'],
            'state' => $locationInfo['state'],
            'city' => '' // We'll try to extract city from the address
        ];
        
        // Try to extract city from the address
        $city = $this->extractCityFromAddress($address);
        $addressParts['city'] = $city;
        
        return $addressParts;
    }

    /**
     * Extract city from address
     */
    private function extractCityFromAddress($address)
    {
        if (empty($address)) {
            return '';
        }

        // Common Malaysian cities
        $malaysianCities = [
            'Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Subang Jaya', 'Klang',
            'Johor Bahru', 'Ipoh', 'Penang', 'George Town', 'Malacca', 'Melaka',
            'Kuantan', 'Kuching', 'Kota Kinabalu', 'Alor Setar', 'Kangar',
            'Kota Bharu', 'Kuala Terengganu', 'Seremban', 'Miri', 'Sandakan',
            'Tawau', 'Lahad Datu', 'Sibu', 'Bintulu', 'Muar', 'Batu Pahat',
            'Segamat', 'Kluang', 'Kulai', 'Pontian', 'Tangkak', 'Yong Peng'
        ];

        foreach ($malaysianCities as $city) {
            if (stripos($address, $city) !== false) {
                return $city;
            }
        }

        return '';
    }

    /**
     * Check for duplicate billing party
     */
    public function checkDuplicateBillingParty(Request $request)
    {
        try {
            $name = $request->input('customer_name');
            $idNo = $request->input('id_no');
            $tin = $request->input('tin');
            
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Duplicate check input values:', [
                'customer_name' => $name,
                'id_no' => $idNo,
                'tin' => $tin,
                'tin_empty_check' => empty($tin),
                'tin_trim_check' => trim($tin) !== '',
                'id_no_empty_check' => empty($idNo),
                'id_no_trim_check' => trim($idNo) !== ''
            ]);
            
            $duplicates = [];
            
            // Check by exact ID number match
            if (!empty($idNo) && trim($idNo) !== '') {
                $idMatch = \App\Models\InvoiceBillingParty::where('id_no', $idNo)
                    ->where('status', 1)
                    ->first();
                if ($idMatch) {
                    $duplicates[] = [
                        'type' => 'ID Number',
                        'value' => $idNo,
                        'record' => $idMatch
                    ];
                }
            }
            
            // Check by exact TIN match
            if (!empty($tin) && trim($tin) !== '') {
                $tinMatch = \App\Models\InvoiceBillingParty::where('tin', $tin)
                    ->where('status', 1)
                    ->first();
                if ($tinMatch) {
                    $duplicates[] = [
                        'type' => 'TIN',
                        'value' => $tin,
                        'record' => $tinMatch
                    ];
                }
            }
            
            // Check by similar name (fuzzy match)
            if (!empty($name) && trim($name) !== '') {
                $similarNames = \App\Models\InvoiceBillingParty::where('status', 1)
                    ->where(function($query) use ($name) {
                        // Only exact substring match - more conservative approach
                        $query->where('customer_name', 'LIKE', '%' . $name . '%');
                    })
                    ->limit(5)
                    ->get();
                
                if ($similarNames->count() > 0) {
                    foreach ($similarNames as $similar) {
                        // Additional filtering: only consider it a duplicate if the similarity is significant
                        $inputName = strtolower(trim($name));
                        $existingName = strtolower(trim($similar->customer_name));
                        
                        // Calculate similarity percentage
                        $similarity = 0;
                        similar_text($inputName, $existingName, $similarity);
                        
                        // Only flag as duplicate if similarity is above 60% or if it's an exact substring match
                        if ($similarity > 60 || strpos($existingName, $inputName) !== false) {
                            $duplicates[] = [
                                'type' => 'Similar Name',
                                'value' => $name,
                                'record' => $similar,
                                'similarity' => round($similarity, 2)
                            ];
                        }
                    }
                }
            }
            
            return response()->json([
                'status' => 1,
                'has_duplicates' => count($duplicates) > 0,
                'duplicates' => $duplicates
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error checking duplicates: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error checking for duplicates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map country name to country code
     */
    private function mapCountryNameToCode($countryName)
    {
        if (empty($countryName)) {
            return 'MY';
        }

        $countryMap = [
            'malaysia' => 'MY',
            'singapore' => 'SG',
            'indonesia' => 'ID',
            'thailand' => 'TH',
            'philippines' => 'PH',
            'vietnam' => 'VN',
            'brunei' => 'BN',
            'myanmar' => 'MM',
            'laos' => 'LA',
            'cambodia' => 'KH',
            'united states' => 'US',
            'united kingdom' => 'GB',
            'australia' => 'AU',
            'china' => 'CN',
            'japan' => 'JP',
            'south korea' => 'KR',
            'india' => 'IN'
        ];

        return $countryMap[strtolower($countryName)] ?? 'MY';
    }

    /**
     * Search parties with access control (NEW - Access Controlled Version)
     */
    public function searchPartiesWithAccess(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 10);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $sourceFilter = $request->input('source_filter', 'both'); // 'client', 'masterlist', 'both'
            
            $allResults = collect();
            
            // Search clients if source filter allows
            if (in_array($sourceFilter, ['client', 'both'])) {
                $clients = $this->searchClientPartiesWithAccess($search);
                $allResults = $allResults->merge($clients);
            }
            
            // Search masterlist if source filter allows
            if (in_array($sourceFilter, ['masterlist', 'both'])) {
                $masterlistParties = $this->searchMasterlistPartiesWithAccess($search);
                $allResults = $allResults->merge($masterlistParties);
            }
            
            // Sort combined results by name
            $allResults = $allResults->sortBy('name')->values();
            
            // Use Laravel's forPage method for proper pagination
            $totalCount = $allResults->count();
            $currentPageItems = $allResults->forPage($page, $perPage);
            
            $pagination = [
                'current_page' => (int)$page,
                'total_pages' => (int)ceil($totalCount / $perPage),
                'total_count' => (int)$totalCount,
                'per_page' => (int)$perPage,
                'has_next_page' => $page < ceil($totalCount / $perPage),
                'has_prev_page' => $page > 1
            ];
            
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Total results: ' . $totalCount);
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Source filter: ' . $sourceFilter);
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Page: ' . $page . ', Items on this page: ' . $currentPageItems->count());
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Current page items: ' . json_encode($currentPageItems->take(3)->toArray()));
            
            return response()->json([
                'status' => 1,
                'data' => $currentPageItems->toArray(),
                'pagination' => $pagination
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching parties: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search client parties with access control (NEW)
     */
    private function searchClientPartiesWithAccess($search)
    {
        // Get user's accessible cases using existing caseManagementEngine
        $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
        
        $query = DB::table('client as c')
            ->join('loan_case as lc', 'c.id', '=', 'lc.customer_id')
            ->select(
                DB::raw("'client' as source"),
                'c.id',
                'c.name',
                'c.ic_no as id_no',
                'c.account_no',
                'c.phone_no as phone1',
                'c.email',
                'c.client_type',
                'c.income_tax_no as tin',
                'c.address',
                'c.mailing_state as state',
                'c.mailing_postcode as postcode',
                'c.mailing_country as country',
                'lc.id as case_id',
                'lc.case_ref_no',
                DB::raw("NULL as party_type"),
                DB::raw("NULL as party_category")
            )
            ->where('c.status', 1)
            ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
            ->where(function($q) use ($search) {
                $q->where('c.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.ic_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.account_no', 'LIKE', '%' . $search . '%');
            });
            
        return $query->orderBy('c.name', 'ASC')->get();
    }
    
    /**
     * Search masterlist parties with access control (NEW)
     */
    private function searchMasterlistPartiesWithAccess($search)
    {
        // Get user's accessible cases using existing caseManagementEngine
        $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
        
        // Get individual parties (Purchaser 1-6, Vendor 1-6, Borrower 1-6, Guarantor 1-6)
        $individualParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'individual' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name LIKE 'Purchaser%' THEN 'Purchaser'
                    WHEN cmfc.name LIKE 'Vendor%' THEN 'Vendor'
                    WHEN cmfc.name LIKE 'Borrower%' THEN 'Borrower'
                    WHEN cmfc.name LIKE 'Guarantor%' THEN 'Guarantor'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->where(function($query) {
                $query->where('cmfc.name', 'LIKE', 'Purchaser%')
                      ->orWhere('cmfc.name', 'LIKE', 'Vendor%')
                      ->orWhere('cmfc.name', 'LIKE', 'Borrower%')
                      ->orWhere('cmfc.name', 'LIKE', 'Guarantor%');
            })
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
            ->where('lc.status', '!=', 'deleted');
            
        // Get company parties (Purchaser Company, Vendor Company, Borrower Company, Guarantor Company)
        $companyParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'company' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name = 'Purchaser Company' THEN 'Purchaser Company'
                    WHEN cmfc.name = 'Vendor Company' THEN 'Vendor Company'
                    WHEN cmfc.name = 'Borrower Company' THEN 'Borrower Company'
                    WHEN cmfc.name = 'Guarantor Company' THEN 'Guarantor Company'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->where(function($query) {
                $query->where('cmfc.name', '=', 'Purchaser Company')
                      ->orWhere('cmfc.name', '=', 'Vendor Company')
                      ->orWhere('cmfc.name', '=', 'Borrower Company')
                      ->orWhere('cmfc.name', '=', 'Guarantor Company');
            })
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
            ->where('lc.status', '!=', 'deleted');
            
        // Union individual and company parties
        $combinedQuery = $individualParties->union($companyParties);
        
        return $combinedQuery->orderBy('name', 'ASC')->get();
    }
    
    /**
     * Get client data with access control (NEW)
     */
    public function getClientBillingPartyDataWithAccess($clientId)
    {
        try {
            $current_user = auth()->user();
            
            // Get user's accessible cases
            $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
            
            $client = DB::table('client as c')
                ->join('loan_case as lc', 'c.id', '=', 'lc.customer_id')
                ->select(
                    'c.id',
                    'c.name',
                    'c.ic_no',
                    'c.account_no',
                    'c.phone_no',
                    'c.email',
                    'c.client_type',
                    'c.income_tax_no',
                    'c.company_ref_no',
                    'c.address',
                    'c.mailing_state',
                    'c.mailing_postcode',
                    'c.mailing_country',
                    'c.billing_state',
                    'c.billing_postcode',
                    'c.billing_country'
                )
                ->where('c.id', $clientId)
                ->where('c.status', 1)
                ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
                ->first();

            if (!$client) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Client not found or access denied'
                ], 404);
            }

            // Extract location information from address
            $locationInfo = $this->extractLocationInfo($client->address);
            
            // Map client data to billing party format
            $billingPartyData = [
                'customer_name' => $client->name,
                'customer_code' => $client->account_no,
                'customer_category' => $client->client_type,
                'id_no' => $client->ic_no,
                'tin' => $client->income_tax_no,
                'brn' => $client->company_ref_no,
                'email' => $client->email,
                'phone1' => $client->phone_no,
                'address_1' => $this->splitAddress($client->address, 0),
                'address_2' => $this->splitAddress($client->address, 1),
                'address_3' => $this->splitAddress($client->address, 2),
                'address_4' => $this->splitAddress($client->address, 3),
                'postcode' => $client->mailing_postcode ?: $client->billing_postcode ?: $locationInfo['postcode'],
                'state' => $client->mailing_state ?: $client->billing_state ?: $locationInfo['state'],
                'country' => $this->mapCountryNameToCode($client->mailing_country ?: $client->billing_country ?: 'Malaysia'),
                'city' => '', // Client table doesn't have city field
            ];

            return response()->json([
                'status' => 1,
                'data' => $billingPartyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting client data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get masterlist party data with access control (NEW)
     */
    public function getMasterlistPartyDataWithAccess($caseId, $partyType, $partyCategory)
    {
        try {
            // Get user's accessible cases
            $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
            
            // Check if user has access to this specific case
            if (!in_array($caseId, $accessCaseList)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied to this case'
                ], 403);
            }
            
            // Use existing logic but with access control
            return $this->getMasterlistPartyData($caseId, $partyType, $partyCategory);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting masterlist party data: ' . $e->getMessage()
            ], 500);
        }
    }
}
