<?php

namespace App\Http\Controllers;

use App\Models\AccountItem;
use App\Models\AccountLog;
use App\Models\Banks;
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
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseAccountFiles;
use App\Models\LoanCaseBillDetails;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseFiles;
use App\Models\LoanCaseTrustMain;
use App\Models\Notification;
use App\Models\OfficeBankAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $notification = [];
        $notification_puchong = [];
        $notification_arkadia = [];
        $notification_rama = [];
        $requestor_list = [];

        $pendingCount = 0;
        $approveCount = 0;
        $rejectedCount = 0;

        // $voucher_list = DB::table('voucher_main as v')
        //     ->join('loan_case as l', 'l.id', '=', 'v.case_id')
        //     ->join('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
        //     ->where('p.parameter_type', '=', 'payment_type')
        //     ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name')
        //     ->get();



        if (in_array($userRoles, ['admin', 'management', 'account'])) {
            $requestor_list = Users::where('status', '=', '1')->orderBy('name', 'ASC')->get();
        } elseif (in_array($userRoles, ['maker'])) {
            $requestor_list = Users::where('status', '=', '1')->where('branch_id', '=', $current_user->branch_id)->orderBy('name', 'ASC')->get();
        }



        $branchInfo = BranchController::manageBranchAccess();

        $notifications = [];

        $role = 'lawyer';

        if (in_array($userRoles, ['account', 'maker'])) {
            $role = 'account';
        }

        for ($i = 0; $i < count($branchInfo['branch']); $i++) {
            $notification = DB::table('notification as v')
                ->join('loan_case as l', 'l.id', '=', 'v.parameter1')
                ->join('voucher_main as vm', 'vm.id', '=', 'v.parameter2')
                ->where('v.bln_read', '=', '0')
                ->where('l.branch_id', '=', $branchInfo['branch'][$i]->id)
                ->where('vm.status', '=', 1)
                ->where('vm.account_approval', '=', 0)
                ->where('v.role', 'like', '%' . $role . '%')
                ->where('v.desc', 'like', '%request%')
                ->select('v.*', 'l.case_ref_no', 'vm.payee', 'vm.transaction_id')
                ->orderBy('created_at', 'desc')
                ->get();

            array_push($notifications,  array('data' => $notification, 'name' => $branchInfo['branch'][$i]->name, 'branch_id' => $branchInfo['branch'][$i]->id));
        }

        return view('dashboard.voucher.index', [
            // 'voucher_list' => $voucher_list,
            'current_user' => $current_user,
            'pendingCount' => $pendingCount,
            'approveCount' => $approveCount,
            'notification' => $notification,
            'notifications' => $notifications,
            'requestor_list' => $requestor_list,
            'branchs' => $branchInfo['branch'],
            'notification_puchong' => $notification_puchong,
            'notification_arkadia' => $notification_arkadia,
            'notification_rama' => $notification_rama,
            'rejectedCount' => $rejectedCount
        ]);
    }

    public function voucherArchieve()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $requestor_list = [];

        // $voucher_list = DB::table('voucher_main as v')
        //     ->join('loan_case as l', 'l.id', '=', 'v.case_id')
        //     ->join('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
        //     ->where('p.parameter_type', '=', 'payment_type')
        //     ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name')
        //     ->get();

        if ($userRoles == "admin" || $userRoles == "management" || $userRoles == "account") {
            $requestor_list = Users::where('status', '=', '1')->orderBy('name', 'ASC')->get();
        }
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.voucher.voucher-achieve', [
            // 'voucher_list' => $voucher_list,
            'current_user' => $current_user,
            'branchs' => $branchInfo['branch'],
            'requestor_list' => $requestor_list,
        ]);
    }

    public function voucherInprogress()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $requestor_list = [];

        // $voucher_list = DB::table('voucher_main as v')
        //     ->join('loan_case as l', 'l.id', '=', 'v.case_id')
        //     ->join('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
        //     ->where('p.parameter_type', '=', 'payment_type')
        //     ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name')
        //     ->get();


        if ($userRoles == "admin" || $userRoles == "management" || $userRoles == "account") {
            $requestor_list = Users::where('status', '=', '1')->orderBy('name', 'ASC')->get();
        }

        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.voucher.voucher-inprogress', [
            // 'voucher_list' => $voucher_list,
            'current_user' => $current_user,
            'branchs' => $branchInfo['branch'],
            'requestor_list' => $requestor_list,
        ]);
    }

    public function paymentReceived()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $requestor_list = [];

        $voucher_list = DB::table('voucher_main as v')
            ->join('loan_case as l', 'l.id', '=', 'v.case_id')
            ->join('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
            ->where('p.parameter_type', '=', 'payment_type')
            ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name')
            ->get();


        if ($userRoles == "admin" || $userRoles == "management" || $userRoles == "account") {
            $requestor_list = Users::where('status', '=', '1')->orderBy('name', 'ASC')->get();
        }

        return view('dashboard.voucher.payment-received', [
            'voucher_list' => $voucher_list,
            'current_user' => $current_user,
            'requestor_list' => $requestor_list,
        ]);
    }

    public function bulkUpdateVoucherStatus(Request $request)
    {
        $status = 0;
        $desc = '';
        $error_count = 0;

        if ($request->input('voucher_list') != null) {
            $voucher_list = json_decode($request->input('voucher_list'), true);
        }

        if (count($voucher_list) > 0) {

            $current_user = auth()->user();
            if ($request->input('status') == 'APPROVE') {
                $status = 1;
                $desc =  $current_user->name . ' approved voucher ([voucher_no])';
            } else  if ($request->input('status') == 'INPROGRESS') {
                $status = 6;
                $desc =  $current_user->name . ' update voucher ([voucher_no]) to in progress ';
            }

            if (count($voucher_list) > 0) {
                $VoucherMain = VoucherMain::whereIn('id', $voucher_list)->get();

                //Only approve need to validate whether have transaction ID or office bank
                if ($status == 1) {
                    for ($i = 0; $i < count($VoucherMain); $i++) {
                        if ($VoucherMain[$i]->transaction_id == '' || $VoucherMain[$i]->transaction_id == null || $VoucherMain[$i]->office_account_id == 0) {
                            $error_count += 1;
                        }

                        if ($VoucherMain[$i]->payment_date == null) {
                            $error_count += 1;
                        }
                    }

                    if ($error_count > 0) {
                        return response()->json(['status' => 0, 'message' => 'Please make sure transaction ID, office account and payment date provided before approve']);
                    }
                }

                for ($i = 0; $i < count($VoucherMain); $i++) {
                    $VoucherMain[$i]->account_approval = $status;
                    $VoucherMain[$i]->approval_id = $current_user->id;
                    $VoucherMain[$i]->save();

                    $AccountLog = new AccountLog();
                    $AccountLog->user_id = $current_user->id;
                    $AccountLog->case_id = $VoucherMain[$i]->case_id;
                    $AccountLog->bill_id = $VoucherMain[$i]->case_bill_main_id;
                    $AccountLog->object_id = $voucher_list[$i]['voucher_id'];
                    $AccountLog->ori_amt = 0;
                    $AccountLog->new_amt = 0;
                    $AccountLog->action = 'Voucher';
                    $AccountLog->desc = str_replace("[voucher_no]", $VoucherMain[$i]->voucher_no, $desc);
                    $AccountLog->status = 1;
                    $AccountLog->created_at = date('Y-m-d H:i:s');
                    $AccountLog->save();

                    $Notification  = new Notification();
                    $Notification->name = $current_user->name;
                    $Notification->desc = str_replace("[voucher_no]", $VoucherMain[$i]->voucher_no, $desc);
                    $Notification->user_id = 0;
                    $Notification->role = 'lawyer|clerk|admin';
                    $Notification->parameter1 = $VoucherMain[$i]->case_id;
                    $Notification->parameter2 = $VoucherMain[$i]->id;
                    $Notification->module = 'voucher';
                    $Notification->bln_read = 0;
                    $Notification->status = 1;
                    $Notification->created_at = now();
                    $Notification->created_by = $current_user->id;
                    $Notification->save();

                    $this->readNotification($VoucherMain[$i]->case_id, $VoucherMain[$i]->id);

                    if ($status == 1) {
                        $voucher_type = '';
                        $voucher_type_v2 = '';

                        if ($VoucherMain[$i]->voucher_type == 1) {
                            // $this->rejectRevertFloatingValue($voucher_list[$i]['voucher_id']);
                            $voucher_type = 'BILLDISB';
                            $voucher_type_v2 = 'BILL_DISB';

                            // if ($VoucherMain->voucher_type == 2) {
                            //     $this->reverseTrustDisburse($id);
                            //     $voucher_type = 'TRUSTDISB';
                            // }
                        } else {
                            $this->reverseTrustDisburse($VoucherMain[$i]->id);
                            $voucher_type = 'TRUSTDISB';
                            $voucher_type_v2 = 'TRUST_DISB';
                        }

                        if (!in_array($current_user->menuroles, ['lawyer'])) {
                            $LedgerEntries = new LedgerEntries();

                            $voucher_item = '';

                            $VoucherDetails = DB::table('voucher_details as a')
                                ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
                                ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
                                ->select('a.*', 'c.name as account_name')
                                ->where('voucher_main_id', '=', $VoucherMain[$i]->id)->get();

                            if (count($VoucherDetails) > 0) {
                                for ($j = 0; $j < count($VoucherDetails); $j++) {
                                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$j]->account_name . '=' . number_format((float)$VoucherDetails[$j]->amount, 2, '.', ',') . '<br/>';
                                }
                            }

                            $transaction = '';

                            if ($VoucherMain[$i]->transaction_id != null) {
                                $transaction = $VoucherMain[$i]->transaction_id;
                            }

                            $LedgerEntries->transaction_id = $transaction;
                            $LedgerEntries->case_id = $VoucherMain[$i]->case_id;
                            $LedgerEntries->loan_case_main_bill_id = $VoucherMain[$i]->case_bill_main_id;
                            $LedgerEntries->user_id = $VoucherMain[$i]->created_by;
                            $LedgerEntries->key_id = $VoucherMain[$i]->id;
                            $LedgerEntries->transaction_type = 'C';
                            $LedgerEntries->amount = $VoucherMain[$i]->total_amount;
                            $LedgerEntries->bank_id = $VoucherMain[$i]->office_account_id;
                            $LedgerEntries->remark = $VoucherMain[$i]->remark;
                            $LedgerEntries->sys_desc = $voucher_item;
                            $LedgerEntries->status = 1;
                            $LedgerEntries->created_at = date('Y-m-d H:i:s');
                            $LedgerEntries->date = $VoucherMain[$i]->payment_date;
                            $LedgerEntries->type = $voucher_type;
                            $LedgerEntries->save();


                            $LedgerEntries = new LedgerEntriesV2();

                            $LedgerEntries->transaction_id = $transaction;
                            $LedgerEntries->case_id = $VoucherMain[$i]->case_id;
                            $LedgerEntries->loan_case_main_bill_id = $VoucherMain[$i]->case_bill_main_id;
                            $LedgerEntries->user_id = $VoucherMain[$i]->created_by;
                            $LedgerEntries->cheque_no = $VoucherMain[$i]->voucher_no;
                            $LedgerEntries->key_id = $VoucherMain[$i]->id;
                            $LedgerEntries->transaction_type = 'C';
                            $LedgerEntries->amount = $VoucherMain[$i]->total_amount;
                            $LedgerEntries->bank_id = $VoucherMain[$i]->office_account_id;
                            $LedgerEntries->payee = $VoucherMain[$i]->payee;
                            $LedgerEntries->remark = $VoucherMain[$i]->remark;
                            $LedgerEntries->desc_1 = $voucher_item;
                            $LedgerEntries->is_recon = 0;
                            $LedgerEntries->status = 1;
                            $LedgerEntries->created_at = date('Y-m-d H:i:s');
                            $LedgerEntries->date = $VoucherMain[$i]->payment_date;
                            $LedgerEntries->type = $voucher_type_v2;
                            $LedgerEntries->save();
                        }
                    }
                }
            }

            return response()->json(['status' => 1, 'data' => 'Status updated']);



            // return $VoucherMain;

            // for ($i = 0; $i < count($voucher_list); $i++) {


            // }

            // return count($voucher_list);

            for ($i = 0; $i < count($voucher_list); $i++) {

                $VoucherMain = VoucherMain::where('id', '=', $voucher_list[$i]['voucher_id'])->first();

                if ($VoucherMain->transaction_id == '' || $VoucherMain->transaction_id == null || $VoucherMain->office_account_id == 0) {
                    return response()->json(['status' => 0, 'message' => 'Please make sure transaction ID and office account fill before approve']);
                }

                if ($VoucherMain) {
                    $VoucherMain->account_approval = $status;
                    $VoucherMain->approval_id = $current_user->id;
                    $VoucherMain->save();

                    $AccountLog = new AccountLog();
                    $AccountLog->user_id = $current_user->id;
                    $AccountLog->case_id = $VoucherMain->case_id;
                    $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
                    $AccountLog->object_id = $voucher_list[$i]['voucher_id'];
                    $AccountLog->ori_amt = 0;
                    $AccountLog->new_amt = 0;
                    $AccountLog->action = 'Voucher';
                    $AccountLog->desc = str_replace("[voucher_no]", $VoucherMain->voucher_no, $desc);
                    $AccountLog->status = 1;
                    $AccountLog->created_at = date('Y-m-d H:i:s');
                    $AccountLog->save();

                    $Notification  = new Notification();
                    $Notification->name = $current_user->name;
                    $Notification->desc = str_replace("[voucher_no]", $VoucherMain->voucher_no, $desc);
                    $Notification->user_id = 0;
                    $Notification->role = 'lawyer|clerk|admin';
                    $Notification->parameter1 = $VoucherMain->case_id;
                    $Notification->parameter2 = $voucher_list[$i]['voucher_id'];
                    $Notification->module = 'voucher';
                    $Notification->bln_read = 0;
                    $Notification->status = 1;
                    $Notification->created_at = now();
                    $Notification->created_by = $current_user->id;
                    $Notification->save();

                    $this->readNotification($VoucherMain->case_id, $voucher_list[$i]['voucher_id']);

                    if ($status == 1) {



                        $voucher_type = '';

                        if ($VoucherMain->voucher_type == 1) {
                            // $this->rejectRevertFloatingValue($voucher_list[$i]['voucher_id']);
                            // $voucher_type = 'BILLDISB';

                            // if ($VoucherMain->voucher_type == 2) {
                            //     $this->reverseTrustDisburse($id);
                            //     $voucher_type = 'TRUSTDISB';
                            // }
                        } else {
                            $this->reverseTrustDisburse($voucher_list[$i]['voucher_id']);
                            $voucher_type = 'TRUSTDISB';
                        }

                        if (!in_array($current_user->menuroles, ['lawyer'])) {
                            $LedgerEntries = new LedgerEntries();

                            $voucher_item = '';

                            $VoucherDetails = DB::table('voucher_details as a')
                                ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
                                ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
                                ->select('a.*', 'c.name as account_name')
                                ->where('voucher_main_id', '=', $VoucherMain->case_bill_main_id)->get();

                            if (count($VoucherDetails) > 0) {
                                for ($j = 0; $j < count($VoucherDetails); $j++) {
                                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$j]->account_name . '=' . number_format((float)$VoucherDetails[$j]->amount, 2, '.', ',') . '<br/>';
                                }
                            }

                            $transaction = '';

                            if ($VoucherMain->transaction_id != null) {
                                $transaction = $VoucherMain->transaction_id;
                            }

                            $LedgerEntries->transaction_id = $transaction;
                            $LedgerEntries->case_id = $VoucherMain->case_id;
                            $LedgerEntries->loan_case_main_bill_id = $VoucherMain->case_bill_main_id;
                            $LedgerEntries->user_id = $VoucherMain->created_by;
                            $LedgerEntries->key_id = $VoucherMain->id;
                            $LedgerEntries->transaction_type = 'C';
                            $LedgerEntries->amount = $VoucherMain->total_amount;
                            $LedgerEntries->bank_id = $VoucherMain->office_account_id;
                            $LedgerEntries->remark = $VoucherMain->remark;
                            $LedgerEntries->sys_desc = $voucher_item;
                            $LedgerEntries->status = 1;
                            $LedgerEntries->created_at = date('Y-m-d H:i:s');
                            $LedgerEntries->date = $VoucherMain->payment_date;
                            $LedgerEntries->type = $voucher_type;
                            $LedgerEntries->save();
                        }
                    }
                }
            }

            return response()->json(['status' => 1, 'data' => 'Status updated']);
        }
    }


    public function getVoucherList(Request $request)
    {
        if ($request->ajax()) {

            $branch_id = 1;
            $account_approval_status = 0;
            $account_approval_operation = '=';

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            if (!empty($request->input('account_approval_status'))) {
                $account_approval_status = $request->input('account_approval_status');
                $account_approval_operation = '<>';
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            if (!empty($request->input('account_approval_status'))) {
                if ($userRoles == "account") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('v.status', '=', 1)
                        // ->where('v.account_approval', $account_approval_operation, 0)

                        ->whereNotIn('v.account_approval', [0, 5])
                        ->where('l.branch_id', '=', $branch_id)
                        // ->orWhere('v.account_approval', '=', 5)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereNotIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "admin" || $userRoles == "management") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        // ->leftJoin('parameter as p', function ($j) {
                        //     $j->on('p.parameter_value_3', '=', 'v.payment_type')->whereNotNull('v.payment_type');
                        // })
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('v.status', '=', 1)
                        ->where('l.branch_id', '=', $branch_id)
                        ->whereNotIn('v.account_approval', [0, 5])
                        ->where('v.voucher_type', '=', 1)
                        // ->orWhere('v.account_approval', '=', 5)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('v.id', 'desc')
                        ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereNotIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "lawyer" || $userRoles == "clerk") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.' . $userRoles . '_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)

                        ->whereNotIn('v.account_approval', [0, 5])
                        ->where('l.branch_id', '=', $branch_id)
                        // ->Where('v.account_approval', '=', 5)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "chambering") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.lawyer_id', '=', $current_user->id)
                        ->where('l.branch_id', '=', $branch_id)
                        // ->where('v.account_approval', $account_approval_operation, 0)

                        ->whereNotIn('v.account_approval', [0, 5])
                        ->orWhere('l.clerk_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                }
            } else {
                if ($userRoles == "account") {


                    // if ($voucherMain->voucher_type == 1) {

                    //     $voucherDetails = DB::table('voucher_details as vd')
                    //         ->join('loan_case_bill_details as bd', 'bd.id', '=', 'vd.account_details_id')
                    //         ->join('account_item as a', 'a.id', '=', 'bd.account_item_id')
                    //         ->select('vd.*', 'a.name as account_item_name')
                    //         ->where('vd.voucher_main_id', '=', $id)
                    //         ->where('vd.account_details_id', '<>', 0)
                    //         ->get();
                    // } else {
                    //     $voucherDetails = DB::table('voucher_details as vd')
                    //         ->join('loan_case_trust as bd', 'bd.id', '=', 'vd.account_details_id')
                    //         // ->join('account_item as a', 'a.id', '=', 'bd.account_item_id')
                    //         ->select('vd.*', 'bd.remark as account_item_name')
                    //         ->where('vd.voucher_main_id', '=', $id)
                    //         ->where('vd.account_details_id', '<>', 0)
                    //         ->get();
                    // }


                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "admin" || $userRoles == "management") {
                    // $voucher_list = DB::table('voucher_main as v')
                    //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                    //     ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                    //     ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                    //     ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                    //     ->join('client as c', 'c.id', '=', 'l.customer_id')
                    //     // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                    //     // ->leftJoin('parameter as p', function ($j) {
                    //     //     $j->on('p.parameter_value_3', '=', 'v.payment_type')->whereNotNull('v.payment_type');
                    //     // })
                    //     ->join('users as u1', 'u1.id', '=', 'v.created_by')
                    //     // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                    //     // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                    //     // ->where('p.parameter_type', '=', 'payment_type')
                    //     ->where('v.status', '=', 1)
                    //     ->where('l.branch_id', '=', $branch_id)
                    //     ->whereIn('v.account_approval', [0, 5])
                    //     ->where('v.voucher_type', '=', 1)
                    //     // ->where('v.account_approval', $account_approval_operation, 0)
                    //     // ->orWhere('v.account_approval', '=', 5)
                    //     ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                    //     ->orderBy('v.id', 'desc') 
                    //     ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        // ->leftJoin('parameter as p', function ($j) {
                        //     $j->on('p.parameter_value_3', '=', 'v.payment_type')->whereNotNull('v.payment_type');
                        // })
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('v.status', '=', 1)
                        // ->where('l.branch_id', '=', $branch_id)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        // ->where('v.account_approval', $account_approval_operation, 0)
                        // ->orWhere('v.account_approval', '=', 5)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('v.id', 'desc')
                        ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "lawyer" || $userRoles == "clerk") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        // ->where('l.' . $userRoles . '_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)
                        // ->where('v.account_approval', $account_approval_operation, 0)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        // ->Where('v.account_approval', '=', 5)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor',  DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc');

                    if ($current_user->branch_id == 3) {
                        $voucher_list = $voucher_list->where('l.branch_id', '=', $current_user->branch_id);
                    } else {
                        $voucher_list = $voucher_list->where('l.' . $userRoles . '_id', '=', $current_user->id);
                    }

                    $voucher_list = $voucher_list->get();
                } else if ($userRoles == "chambering") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.lawyer_id', '=', $current_user->id)
                        ->where('l.branch_id', '=', $branch_id)
                        // ->where('v.account_approval', $account_approval_operation, 0)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->orWhere('l.clerk_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor',  DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                }
            }



            return DataTables::of($voucher_list)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info btn-xs  " data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                   ';

                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <a  href="javascript:void(0)" class="btn btn-danger  btn-xs " onclick="deleteVoucher(' . $row->id . ')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>';
                    }

                    return $actionBtn;
                })
                ->addColumn('hrefcase', function ($data) {
                    return '<a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a>';
                })
                ->addColumn('status1', function ($data) {
                    if ($data->status == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('lawyer_approval', function ($data) {
                    if ($data->lawyer_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->lawyer_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('account_approval', function ($data) {
                    if ($data->account_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    elseif ($data->account_approval == '5')
                        return '<span class="label bg-info">Resubmit</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->editColumn('receipt_issued', function ($data) {
                    if ($data->receipt_issued == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->receipt_issued == '1')
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if ($data->voucher_type == '1')
                        return 'Bill';
                    elseif ($data->voucher_type == '2')
                        return 'Trust';
                })
                ->rawColumns(['status1', 'action', 'hrefcase', 'lawyer_approval', 'account_approval', 'voucher_type', 'receipt_issued'])
                ->editColumn('status', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->make(true);
        }
    }


    public function getVoucherListV2(Request $request)
    {
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;
            $accessInfo = AccessController::manageAccess();

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $voucher_list = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                ->join('client as c', 'c.id', '=', 'l.customer_id')
                ->join('users as u1', 'u1.id', '=', 'v.created_by')
                ->where('v.status', '=', 1);

            if (!in_array($userRoles, ['admin', 'management', 'account', 'maker'])) {
                $accessCaseList = CaseController::caseManagementEngine();

                $voucher_list = $voucher_list->whereIn('l.id', $accessCaseList);
            }

            if (!empty($request->input('search_box'))) {
                $voucher_list = $voucher_list->where('v.adjudication_no', '=', $request->input('search_box'));
            } else {
                if ($request->input('requestor')) {
                    if ($request->input('requestor') <> 0) {
                        $voucher_list = $voucher_list->where('v.user_id', '=', $request->input('requestor'));
                    }
                }

                if (!empty($request->input('type'))) {
                    if ($request->input('type') <> 99) {
                        $voucher_list = $voucher_list->where('v.voucher_type', '=', $request->input('type'));
                    }
                }

                if (!empty($request->input('account_approval_status'))) {
                    if ($request->input('account_approval_status') == 1) {

                        $Last7Days = Carbon::now()->subDays(90);
                        $voucher_list = $voucher_list->whereNotIn('v.account_approval', [0, 5, 6]);
                        // $voucher_list = $voucher_list->where('v.payment_date', '>=', $Last7Days);
                        // $voucher_list = $voucher_list->whereDate('v.payment_date', Carbon::today());
                    } else if ($request->input('account_approval_status') == 6) {
                        $voucher_list = $voucher_list->whereIn('v.account_approval', [6]);
                    }
                } else {
                    $voucher_list = $voucher_list->whereIn('v.account_approval', [0, 5]);
                }

                if ($request->input("year") <> 0) {
                    $voucher_list = $voucher_list->whereYear('v.created_at', $request->input("year"));
                }

                if ($request->input("month") <> 0) {
                    $voucher_list = $voucher_list->whereMonth('v.created_at', $request->input("month"));
                }

                if (!empty($request->input('status'))) {
                    if ($request->input('status') <> 99) {
                        $voucher_list = $voucher_list->where('v.account_approval', '=', $request->input('status'));
                    }
                }

                if (!empty($request->input('voucher_type'))) {
                    if ($request->input('voucher_type') ==  'in') {
                        $voucher_list = $voucher_list->whereIn('v.voucher_type', [3, 4]);
                    } else {
                        $voucher_list = $voucher_list->whereIn('v.voucher_type', [1, 2]);
                    }
                } else {
                    $voucher_list = $voucher_list->whereIn('v.voucher_type', [1, 2]);
                }
            }

            // $voucher_list = $voucher_list->where('l.branch_id', '=', $branch_id)
            // ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
            //         WHEN v.voucher_type = 1 THEN a.name 
            //         WHEN v.voucher_type = 2 THEN bd.remark
            //         END) AS account'), 'd.amount as details_amount')
            // ->orderBy('d.created_at', 'desc')
            // ->get();


            $voucher_list = $voucher_list->where('l.branch_id', '=', $branch_id)
                ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN v.remark
                        END) AS account'), 'd.amount as details_amount')
                ->orderBy('d.created_at', 'desc')
                ->get();


            return DataTables::of($voucher_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) use ($request, $userRoles, $current_user) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info btn-xs  " data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                   ';

                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <a  href="javascript:void(0)" class="btn btn-danger  btn-xs " onclick="deleteVoucher(' . $row->id . ')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>';
                    }

                    $actionStyle = '';
                    $checkboxStyle = '';

                    if ($request->input('mode') == 'checkbox') {
                        $actionStyle = 'style="display:none"';
                    } else {
                        $checkboxStyle = 'style="display:none"';
                    }

                    $actionBtn = '
                    <div class="checkbox  bulk-edit-mode" ' . $checkboxStyle . '>
                        <input type="checkbox" name="voucher" value="' . $row->id . '" id="chk_' . $row->id . '" >
                        <label for="chk_' . $row->id . '"></label>
                        </div> 
                    <div class="btn-group  normal-edit-mode" ' . $actionStyle . '>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank"   href="/voucher/' . $row->id . '/edit" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                     
                      ';
                    if (in_array($userRoles, ['management', 'account']) || in_array($current_user->id, [1, 51, 127])) {
                        if ($row->account_approval <> '2' && $row->account_approval <> '1') {
                            $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                            <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="updateInProgress(' . $row->id . ', \'APPROVE\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-check"></i>Approve</a>';
                        }
                        if ($row->account_approval == '0') {
                            $actionBtn = $actionBtn . '<div class="dropdown-divider" style="margin:0"></div>
                            <a class="dropdown-item btn-warning" href="javascript:void(0)" onclick="updateInProgress(' . $row->id . ', \'INPROGRESS\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-running"></i>In Progress</a>';
                        }

                        // $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div> c
                        // <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="setReceiptIssued(' . $row->id . ', \'APPROVE\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-featured-playlist"></i>Receipt Issued</a>';

                    }


                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteVoucher(' . $row->id . ')" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                        ';
                    }
                    $actionBtn = $actionBtn . '</div></div>';

                    return $actionBtn;
                })
                ->addColumn('hrefcase', function ($data) {
                    return '<a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a>';
                })
                ->addColumn('status1', function ($data) {
                    if ($data->status == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('lawyer_approval', function ($data) {
                    if ($data->lawyer_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->lawyer_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('account_approval', function ($data) {
                    if ($data->account_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    elseif ($data->account_approval == '5')
                        return '<span class="label bg-info">Resubmit</span>';
                    elseif ($data->account_approval == '6')
                        return '<span class="label bg-warning">In Progress</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if ($data->voucher_type == '1')
                        return 'Bill';
                    elseif ($data->voucher_type == '2')
                        return 'Trust';
                })
                ->editColumn('receipt_issued', function ($data) {
                    if ($data->receipt_issued == '0')
                        return '<span class="label bg-danger">No</span>';
                    elseif ($data->receipt_issued == '1')
                        return '<span class="label bg-success">Yes</span>';
                })
                ->rawColumns(['status1', 'action', 'hrefcase', 'lawyer_approval', 'account_approval', 'voucher_type', 'receipt_issued'])
                ->editColumn('status', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->make(true);
        }
    }

    public function getVoucherListV2Bak2(Request $request)
    {
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;
            $accessInfo = AccessController::manageAccess();

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $voucher_list = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                ->join('client as c', 'c.id', '=', 'l.customer_id')
                ->join('users as u1', 'u1.id', '=', 'v.created_by')
                ->where('v.status', '=', 1);

            $userList = $accessInfo['user_list'];

            // if (!in_array($userRoles, ['admin', 'management', 'account', 'maker'])) {
            //     $accessCaseList = CaseController::caseManagementEngine();

            //     $voucher_list = $voucher_list->whereIn('l.id', $accessCaseList);
            // }


            if (!in_array($userRoles, ['admin', 'management', 'account', 'maker'])) {


                if ($userList) {
                    $voucher_list = $voucher_list->where(function ($q) use ($userList, $accessInfo) {
                        $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
                            ->whereIn('l.lawyer_id', $userList)
                            ->orWhereIn('l.clerk_id', $userList)
                            ->orWhereIn('l.sales_user_id', $userList);
                    });
                } else {
                    $voucher_list = $voucher_list->whereIn('l.branch_id', $accessInfo['brancAccessList']);
                }
            } else {
                if (in_array($userRoles, ['maker'])) {
                    if (in_array($current_user->branch_id, [2])) {
                        $voucher_list = $voucher_list->where(function ($q) use ($userList, $accessInfo) {
                            $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
                                ->whereIn('l.lawyer_id', $userList)
                                ->orWhereIn('l.clerk_id', $userList);
                        });
                    } else {
                        $voucher_list = $voucher_list->where(function ($q) use ($userList, $accessInfo) {
                            $q->whereIn('l.branch_id', $accessInfo['brancAccessList']);
                        });
                    }
                }
            }


            if (!empty($request->input('search_box'))) {
                $voucher_list = $voucher_list->where('v.adjudication_no', '=', $request->input('search_box'));
            } else {
                if ($request->input('requestor')) {
                    if ($request->input('requestor') <> 0) {
                        $voucher_list = $voucher_list->where('v.user_id', '=', $request->input('requestor'));
                    }
                }

                if (!empty($request->input('type'))) {
                    if ($request->input('type') <> 99) {
                        $voucher_list = $voucher_list->where('v.voucher_type', '=', $request->input('type'));
                    }
                }

                if (!empty($request->input('account_approval_status'))) {
                    if ($request->input('account_approval_status') == 1) {

                        $Last7Days = Carbon::now()->subDays(90);
                        $voucher_list = $voucher_list->whereNotIn('v.account_approval', [0, 5, 6]);
                        // $voucher_list = $voucher_list->where('v.payment_date', '>=', $Last7Days);
                        // $voucher_list = $voucher_list->whereDate('v.payment_date', Carbon::today());
                    } else if ($request->input('account_approval_status') == 6) {
                        $voucher_list = $voucher_list->whereIn('v.account_approval', [6]);
                    }
                } else {
                    $voucher_list = $voucher_list->whereIn('v.account_approval', [0, 5]);
                }

                if ($request->input("year") <> 0) {
                    $voucher_list = $voucher_list->whereYear('v.created_at', $request->input("year"));
                }

                if ($request->input("month") <> 0) {
                    $voucher_list = $voucher_list->whereMonth('v.created_at', $request->input("month"));
                }

                if (!empty($request->input('status'))) {
                    if ($request->input('status') <> 99) {
                        $voucher_list = $voucher_list->where('v.account_approval', '=', $request->input('status'));
                    }
                }

                if (!empty($request->input('voucher_type'))) {
                    if ($request->input('voucher_type') ==  'in') {
                        $voucher_list = $voucher_list->whereIn('v.voucher_type', [3, 4]);
                    } else {
                        $voucher_list = $voucher_list->whereIn('v.voucher_type', [1, 2]);
                    }
                } else {
                    $voucher_list = $voucher_list->whereIn('v.voucher_type', [1, 2]);
                }
            }




            $voucher_list = $voucher_list->where('l.branch_id', '=', $branch_id)
                ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                ->orderBy('d.created_at', 'desc')
                ->get();


            return DataTables::of($voucher_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) use ($request, $userRoles, $current_user) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info btn-xs  " data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                   ';

                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <a  href="javascript:void(0)" class="btn btn-danger  btn-xs " onclick="deleteVoucher(' . $row->id . ')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>';
                    }

                    $actionStyle = '';
                    $checkboxStyle = '';

                    if ($request->input('mode') == 'checkbox') {
                        $actionStyle = 'style="display:none"';
                    } else {
                        $checkboxStyle = 'style="display:none"';
                    }

                    $actionBtn = '
                    <div class="checkbox  bulk-edit-mode" ' . $checkboxStyle . '>
                        <input type="checkbox" name="voucher" value="' . $row->id . '" id="chk_' . $row->id . '" >
                        <label for="chk_' . $row->id . '"></label>
                        </div> 
                    <div class="btn-group  normal-edit-mode" ' . $actionStyle . '>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank"   href="/voucher/' . $row->id . '/edit" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                     
                      ';
                    if (in_array($userRoles, ['management', 'account']) || in_array($current_user->id, [1, 51, 127])) {
                        if ($row->account_approval <> '2' && $row->account_approval <> '1') {
                            $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                            <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="updateInProgress(' . $row->id . ', \'APPROVE\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-check"></i>Approve</a>';
                        }
                        if ($row->account_approval == '0') {
                            $actionBtn = $actionBtn . '<div class="dropdown-divider" style="margin:0"></div>
                            <a class="dropdown-item btn-warning" href="javascript:void(0)" onclick="updateInProgress(' . $row->id . ', \'INPROGRESS\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-running"></i>In Progress</a>';
                        }

                        // $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div> c
                        // <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="setReceiptIssued(' . $row->id . ', \'APPROVE\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-featured-playlist"></i>Receipt Issued</a>';

                    }


                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteVoucher(' . $row->id . ')" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                        ';
                    }
                    $actionBtn = $actionBtn . '</div></div>';

                    return $actionBtn;
                })
                ->addColumn('hrefcase', function ($data) {
                    return '<a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a>';
                })
                ->addColumn('status1', function ($data) {
                    if ($data->status == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('lawyer_approval', function ($data) {
                    if ($data->lawyer_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->lawyer_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('account_approval', function ($data) {
                    if ($data->account_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    elseif ($data->account_approval == '5')
                        return '<span class="label bg-info">Resubmit</span>';
                    elseif ($data->account_approval == '6')
                        return '<span class="label bg-warning">In Progress</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if ($data->voucher_type == '1')
                        return 'Bill';
                    elseif ($data->voucher_type == '2')
                        return 'Trust';
                })
                ->editColumn('receipt_issued', function ($data) {
                    if ($data->receipt_issued == '0')
                        return '<span class="label bg-danger">No</span>';
                    elseif ($data->receipt_issued == '1')
                        return '<span class="label bg-success">Yes</span>';
                })
                ->rawColumns(['status1', 'action', 'hrefcase', 'lawyer_approval', 'account_approval', 'voucher_type', 'receipt_issued'])
                ->editColumn('status', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->make(true);
        }
    }

    public function getVoucherListV2Bak(Request $request)
    {
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $voucher_list = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                ->join('client as c', 'c.id', '=', 'l.customer_id')
                ->join('users as u1', 'u1.id', '=', 'v.created_by')
                ->where('v.status', '=', 1);

            if ($userRoles == "lawyer" || $userRoles == "clerk") {
                if ($current_user->id == 63) {

                    $voucher_list = $voucher_list->whereIn('l.' . $userRoles . '_id', [$current_user->id, 79]);
                } else {

                    $voucher_list = $voucher_list->where('l.' . $userRoles . '_id', '=', $current_user->id);
                }
            } else if ($userRoles == "chambering") {
                $voucher_list = $voucher_list->where('l.lawyer_id', '=', $current_user->id)
                    ->orWhere('l.clerk_id', '=', $current_user->id);
            } else if ($userRoles == "sales") {
                $voucher_list = $voucher_list->where('l.sales_user_id', '=', $current_user->id);
            }

            if ($request->input('requestor')) {
                if ($request->input('requestor') <> 0) {
                    $voucher_list = $voucher_list->where('v.user_id', '=', $request->input('requestor'));
                }
            }

            if (!empty($request->input('type'))) {
                if ($request->input('type') <> 99) {
                    $voucher_list = $voucher_list->where('v.voucher_type', '=', $request->input('type'));
                }
            }

            if (!empty($request->input('account_approval_status'))) {
                if ($request->input('account_approval_status') == 1) {
                    $voucher_list = $voucher_list->whereNotIn('v.account_approval', [0, 5, 6]);
                } else if ($request->input('account_approval_status') == 6) {
                    $voucher_list = $voucher_list->whereIn('v.account_approval', [6]);
                }
            } else {
                $voucher_list = $voucher_list->whereIn('v.account_approval', [0, 5]);
            }

            if (!empty($request->input('status'))) {
                if ($request->input('status') <> 99) {
                    $voucher_list = $voucher_list->where('v.account_approval', '=', $request->input('status'));
                }
            }

            if (!empty($request->input('voucher_type'))) {
                if ($request->input('voucher_type') ==  'in') {
                    $voucher_list = $voucher_list->whereIn('v.voucher_type', [3, 4]);
                } else {
                    $voucher_list = $voucher_list->whereIn('v.voucher_type', [1, 2]);
                }
            } else {
                $voucher_list = $voucher_list->whereIn('v.voucher_type', [1, 2]);
            }


            $voucher_list = $voucher_list->where('l.branch_id', '=', $branch_id)
                ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                ->orderBy('d.created_at', 'desc')
                ->get();


            return DataTables::of($voucher_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) use ($request, $userRoles) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info btn-xs  " data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                   ';

                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <a  href="javascript:void(0)" class="btn btn-danger  btn-xs " onclick="deleteVoucher(' . $row->id . ')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>';
                    }

                    $actionStyle = '';
                    $checkboxStyle = '';

                    if ($request->input('mode') == 'checkbox') {
                        $actionStyle = 'style="display:none"';
                    } else {
                        $checkboxStyle = 'style="display:none"';
                    }

                    $actionBtn = '
                    <div class="checkbox  bulk-edit-mode" ' . $checkboxStyle . '>
                        <input type="checkbox" name="voucher" value="' . $row->id . '" id="chk_' . $row->id . '" >
                        <label for="chk_' . $row->id . '"></label>
                        </div> 
                    <div class="btn-group  normal-edit-mode" ' . $actionStyle . '>
                    <button type="button" class="btn btn-info btn-flat">Action</button>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank"   href="/voucher/' . $row->id . '/edit" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                     
                      ';
                    if (in_array($userRoles, ['admin', 'management', 'account'])) {
                        if ($row->account_approval <> '2' && $row->account_approval <> '1') {
                            $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                            <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="updateInProgress(' . $row->id . ', \'APPROVE\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-check"></i>Approve</a>';
                        }
                        if ($row->account_approval == '0') {
                            $actionBtn = $actionBtn . '<div class="dropdown-divider" style="margin:0"></div>
                            <a class="dropdown-item btn-warning" href="javascript:void(0)" onclick="updateInProgress(' . $row->id . ', \'INPROGRESS\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-running"></i>In Progress</a>';
                        }

                        // $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                        // <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="setReceiptIssued(' . $row->id . ', \'APPROVE\');" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-featured-playlist"></i>Receipt Issued</a>';

                    }


                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteVoucher(' . $row->id . ')" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                        ';
                    }
                    $actionBtn = $actionBtn . '</div></div>';

                    return $actionBtn;
                })
                ->addColumn('hrefcase', function ($data) {
                    return '<a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a>';
                })
                ->addColumn('status1', function ($data) {
                    if ($data->status == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('lawyer_approval', function ($data) {
                    if ($data->lawyer_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->lawyer_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('account_approval', function ($data) {
                    if ($data->account_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    elseif ($data->account_approval == '5')
                        return '<span class="label bg-info">Resubmit</span>';
                    elseif ($data->account_approval == '6')
                        return '<span class="label bg-warning">In Progress</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if ($data->voucher_type == '1')
                        return 'Bill';
                    elseif ($data->voucher_type == '2')
                        return 'Trust';
                })
                ->editColumn('receipt_issued', function ($data) {
                    if ($data->receipt_issued == '0')
                        return '<span class="label bg-danger">No</span>';
                    elseif ($data->receipt_issued == '1')
                        return '<span class="label bg-success">Yes</span>';
                })
                ->rawColumns(['status1', 'action', 'hrefcase', 'lawyer_approval', 'account_approval', 'voucher_type', 'receipt_issued'])
                ->editColumn('status', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->make(true);
        }
    }


    public function getVoucherListBak(Request $request)
    {
        if ($request->ajax()) {

            $branch_id = 1;
            $account_approval_status = 0;
            $account_approval_operation = '=';

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            if (!empty($request->input('account_approval_status'))) {
                $account_approval_status = $request->input('account_approval_status');
                $account_approval_operation = '<>';
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            if (!empty($request->input('account_approval_status'))) {
                if ($userRoles == "account") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('v.status', '=', 1)
                        // ->where('v.account_approval', $account_approval_operation, 0)

                        ->whereNotIn('v.account_approval', [0, 5])
                        ->where('l.branch_id', '=', $branch_id)
                        // ->orWhere('v.account_approval', '=', 5)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereNotIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "admin" || $userRoles == "management") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        // ->leftJoin('parameter as p', function ($j) {
                        //     $j->on('p.parameter_value_3', '=', 'v.payment_type')->whereNotNull('v.payment_type');
                        // })
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('v.status', '=', 1)
                        ->where('l.branch_id', '=', $branch_id)
                        ->whereNotIn('v.account_approval', [0, 5])
                        ->where('v.voucher_type', '=', 1)
                        // ->orWhere('v.account_approval', '=', 5)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('v.id', 'desc')
                        ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereNotIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "lawyer" || $userRoles == "clerk") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.' . $userRoles . '_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)

                        ->whereNotIn('v.account_approval', [0, 5])
                        ->where('l.branch_id', '=', $branch_id)
                        // ->Where('v.account_approval', '=', 5)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "chambering") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.lawyer_id', '=', $current_user->id)
                        ->where('l.branch_id', '=', $branch_id)
                        // ->where('v.account_approval', $account_approval_operation, 0)

                        ->whereNotIn('v.account_approval', [0, 5])
                        ->orWhere('l.clerk_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                }
            } else {
                if ($userRoles == "account") {


                    // if ($voucherMain->voucher_type == 1) {

                    //     $voucherDetails = DB::table('voucher_details as vd')
                    //         ->join('loan_case_bill_details as bd', 'bd.id', '=', 'vd.account_details_id')
                    //         ->join('account_item as a', 'a.id', '=', 'bd.account_item_id')
                    //         ->select('vd.*', 'a.name as account_item_name')
                    //         ->where('vd.voucher_main_id', '=', $id)
                    //         ->where('vd.account_details_id', '<>', 0)
                    //         ->get();
                    // } else {
                    //     $voucherDetails = DB::table('voucher_details as vd')
                    //         ->join('loan_case_trust as bd', 'bd.id', '=', 'vd.account_details_id')
                    //         // ->join('account_item as a', 'a.id', '=', 'bd.account_item_id')
                    //         ->select('vd.*', 'bd.remark as account_item_name')
                    //         ->where('vd.voucher_main_id', '=', $id)
                    //         ->where('vd.account_details_id', '<>', 0)
                    //         ->get();
                    // }


                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "admin" || $userRoles == "management") {
                    // $voucher_list = DB::table('voucher_main as v')
                    //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                    //     ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                    //     ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                    //     ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                    //     ->join('client as c', 'c.id', '=', 'l.customer_id')
                    //     // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                    //     // ->leftJoin('parameter as p', function ($j) {
                    //     //     $j->on('p.parameter_value_3', '=', 'v.payment_type')->whereNotNull('v.payment_type');
                    //     // })
                    //     ->join('users as u1', 'u1.id', '=', 'v.created_by')
                    //     // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                    //     // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                    //     // ->where('p.parameter_type', '=', 'payment_type')
                    //     ->where('v.status', '=', 1)
                    //     ->where('l.branch_id', '=', $branch_id)
                    //     ->whereIn('v.account_approval', [0, 5])
                    //     ->where('v.voucher_type', '=', 1)
                    //     // ->where('v.account_approval', $account_approval_operation, 0)
                    //     // ->orWhere('v.account_approval', '=', 5)
                    //     ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', 'a.name as account', 'd.amount as details_amount')
                    //     ->orderBy('v.id', 'desc') 
                    //     ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        // ->leftJoin('parameter as p', function ($j) {
                        //     $j->on('p.parameter_value_3', '=', 'v.payment_type')->whereNotNull('v.payment_type');
                        // })
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('v.status', '=', 1)
                        // ->where('l.branch_id', '=', $branch_id)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        // ->where('v.account_approval', $account_approval_operation, 0)
                        // ->orWhere('v.account_approval', '=', 5)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('v.id', 'desc')
                        ->get();

                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        ->where('v.status', '=', 1)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor', DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "lawyer" || $userRoles == "clerk") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.' . $userRoles . '_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)
                        // ->where('v.account_approval', $account_approval_operation, 0)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->where('l.branch_id', '=', $branch_id)
                        // ->Where('v.account_approval', '=', 5)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor',  DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                } else if ($userRoles == "chambering") {
                    $voucher_list = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'v.id')
                        ->leftJoin('loan_case_bill_details as ld', 'ld.id', '=', 'd.account_details_id')
                        ->leftJoin('loan_case_trust as bd', 'bd.id', '=', 'd.account_details_id')
                        ->leftJoin('account_item as a', 'a.id', '=', 'ld.account_item_id')
                        ->join('loan_case as l', 'l.id', '=', 'v.case_id')
                        ->join('client as c', 'c.id', '=', 'l.customer_id')
                        // ->leftJoin('parameter as p', 'p.parameter_value_3', '=', 'v.payment_type')
                        ->join('users as u1', 'u1.id', '=', 'v.created_by')
                        // ->join('users  AS request', 'request.id', '=', 'voucher.user_id')
                        // ->leftJoin('users  AS approval', 'approval.id', '=', 'voucher.approval_id')
                        // ->where('p.parameter_type', '=', 'payment_type')
                        ->where('l.lawyer_id', '=', $current_user->id)
                        ->where('l.branch_id', '=', $branch_id)
                        // ->where('v.account_approval', $account_approval_operation, 0)
                        ->whereIn('v.account_approval', [0, 5])
                        ->whereIn('v.voucher_type', [1, 2])
                        ->orWhere('l.clerk_id', '=', $current_user->id)
                        ->where('v.status', '=', 1)
                        // ->select('v.*', 'l.case_ref_no', 'p.parameter_value_2 AS payment_type_name', 'c.name as client_name', 'u1.name as requestor')
                        ->select('v.*', 'l.case_ref_no', 'c.name as client_name', 'u1.name as requestor',  DB::raw('(CASE 
                        WHEN v.voucher_type = 1 THEN a.name 
                        WHEN v.voucher_type = 2 THEN bd.remark
                        END) AS account'), 'd.amount as details_amount')
                        ->orderBy('d.created_at', 'desc')
                        ->get();
                }
            }



            return DataTables::of($voucher_list)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info btn-xs  " data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                   ';

                    if ($row->account_approval <> '1') {
                        $actionBtn = $actionBtn . ' <a  href="javascript:void(0)" class="btn btn-danger  btn-xs " onclick="deleteVoucher(' . $row->id . ')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>';
                    }

                    return $actionBtn;
                })
                ->addColumn('hrefcase', function ($data) {
                    return '<a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a>';
                })
                ->addColumn('status1', function ($data) {
                    if ($data->status == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('lawyer_approval', function ($data) {
                    if ($data->lawyer_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->lawyer_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('account_approval', function ($data) {
                    if ($data->account_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval == '2')
                        return '<span class="label bg-danger">Rejected</span>';
                    elseif ($data->account_approval == '5')
                        return '<span class="label bg-info">Resubmit</span>';
                    else
                        return '<span class="label bg-success">Approved</span>';
                })
                ->addColumn('voucher_type', function ($data) {
                    if ($data->voucher_type == '1')
                        return 'Bill';
                    elseif ($data->voucher_type == '2')
                        return 'Trust';
                })
                ->rawColumns(['status1', 'action', 'hrefcase', 'lawyer_approval', 'account_approval', 'voucher_type'])
                ->editColumn('status', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->make(true);
        }
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where('status', '=', '1')->get();

        return view('dashboard.documentTemplate.create', [
            'templates' => CaseTemplate::all(),
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $to = '';
        $from = '';
        $cc = '';

        if (!empty($request->input('emailTo'))) {
            $to = implode(",", $request->input('emailTo'));
        }

        if (!empty($request->input('emailFrom'))) {
            $from = implode(",", $request->input('emailFrom'));
        }

        if (!empty($request->input('emailCC'))) {
            $cc = implode(",", $request->input('emailCC'));
        }

        $templateEmail  = new EmailTemplateMain();

        $templateEmail->name = $request->input('name');
        $templateEmail->desc = $request->input('desc');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->to = $to;
        $templateEmail->from = $from;
        $templateEmail->cc = $cc;
        $templateEmail->status =  $request->input('status');
        // $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();


        if ($templateEmail->id != null && $templateEmail->id != '') {
            $templateEmailDetails  = new EmailTemplateDetails();

            $templateEmailDetails->email_template_id = $templateEmail->id;
            $templateEmailDetails->version_name = 'Orinal';
            $templateEmailDetails->content = $request->input('summary-ckeditor');
            $templateEmailDetails->status = $request->input('status');

            $templateEmailDetails->save();
        }



        // $lastId = DB::getPdo()->lastInsertId();

        $request->session()->flash('message', 'Successfully created template');
        return redirect()->route('email-template.index');

        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        // $menuLang = new MenuLangList();
        // $menuLang->name         = $request->input('name');
        // $menuLang->short_name   = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLang->save();
        // $request->session()->flash('message', 'Successfully created language');
        // return redirect()->route('todolist.create');
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

        $LoanCaseAccountFiles = LoanCaseAccountFiles::where('main_id', '=', $id)->where('status', '=', 1)->get();

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
            'LoanCaseAccountFiles' => $LoanCaseAccountFiles,
            'caseMasterListCategory' => $caseMasterListCategory
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lawyerName = '-';
        $ApprovalName = '-';
        $requestName = '-';
        $AccountItem = [];

        $voucherMain = VoucherMain::where('id', '=', $id)->first();

        // $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $voucherMain->case_bill_main_id)->where('status', '=', 1)->first();


        // voucher_type = 1: bill voucher
        // voucher_type = 2: trust voucher
        if ($voucherMain->voucher_type == 1) {

            $voucherDetails = DB::table('voucher_details as vd')
                ->join('loan_case_bill_details as bd', 'bd.id', '=', 'vd.account_details_id')
                ->join('account_item as a', 'a.id', '=', 'bd.account_item_id')
                ->select('vd.*', 'a.name as account_item_name', 'a.account_cat_id as account_cat_id', 'a.id as acc_item_id', 'bd.amount as detail_bal', 'bd.id as bill_detail_id')
                ->where('vd.voucher_main_id', '=', $id)
                ->where('vd.account_details_id', '<>', 0)
                ->get();


            $AccountItem = DB::table('loan_case_bill_details as a')
                ->join('account_item as b', 'b.id', '=', 'a.account_item_id')
                ->select('a.*', 'b.name as account_item_name', 'b.account_cat_id as account_cat_id', 'b.id as acc_item_id', 'a.amount as bal')
                ->where('a.loan_case_main_bill_id', '=', $voucherMain->case_bill_main_id)
                ->where('b.account_cat_id', '<>', 1)
                ->get();
        } else {
            $voucherDetails = DB::table('voucher_details as vd')
            ->join('voucher_main as m', 'm.id', '=', 'vd.voucher_main_id')
                // ->join('loan_case_trust as bd', 'bd.id', '=', 'vd.account_details_id')
                // ->join('account_item as a', 'a.id', '=', 'bd.account_item_id')
                ->select('vd.*', 'm.remark as account_item_name')
                ->where('vd.voucher_main_id', '=', $id)
                // ->where('vd.account_details_id', '<>', 0)
                ->get();

                // return $voucherDetails;
        }


       


        // $voucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->get();
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        // $LoanCaseAccountFiles = LoanCaseAccountFiles::where('main_id', '=', $id)->where('status', '=', 1)->get();

        $LoanCaseAccountFiles = DB::table('loan_case_account_files as a')
            ->join('users as b', 'b.id', '=', 'a.created_by')
            ->select('a.*', 'b.name as upload_by')
            ->where('main_id', '=', $id)->where('a.status', '=', 1)->get();

        $lawyerID = $voucherMain->lawyer_id;
        $lawyerApprovalName = '';
        $requestID = $voucherMain->created_by;
        $AccountID = $voucherMain->approval_id;
        $lawyerApprovalID = $voucherMain->lawyer_approval_id;

        if ($lawyerID != 0) {
            $lawyer = User::where('id', '=', $lawyerID)->first();
            $lawyerName = $lawyer->name;
        }

        if ($requestID != 0) {
            $requestID = User::where('id', '=', $requestID)->first();
            $requestName = $requestID->name;
        }

        if ($AccountID != 0) {
            $Account = User::where('id', '=', $AccountID)->first();
            $ApprovalName = $Account->name;
        }

        if ($lawyerApprovalID != 0) {
            $lawyer = User::where('id', '=', $lawyerApprovalID)->first();
            $lawyerApprovalName = $lawyer->name;
        }

        // return $requestName;




        $case = LoanCase::where('id', '=', $voucherMain->case_id)->first();
        $customer = Customer::where('id', '=', $case->customer_id)->first();


        $case_ref_no = str_replace("/", "_", $case->case_ref_no);
        $template_path_old = 'documents/cases/' . $case_ref_no . '/voucher/';;
        $template_path = 'documents/cases/' . $case->id . '/voucher/';;

        $bank = Banks::where('id', '=', $voucherMain->bank_id)->first();

        $parameter_controller = new ParameterController;
        $parameters = $parameter_controller->getParameter('payment_type');


        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1);

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6]);
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id);
            }
        }

        $OfficeBankAccount = $OfficeBankAccount->get();


        $Branch = Branch::where('id', '=', $case->branch_id)->first();

        $bank_list = Banks::where('status', '=', 1)->orderBy('name')->get();

        return view('dashboard.voucher.edit', [
            'case' =>  $case,
            'customer' => $customer,
            'voucherMain' => $voucherMain,
            'voucherDetails' => $voucherDetails,
            'userRoles' => $userRoles,
            'Branch' => $Branch,
            'LoanCaseAccountFiles' => $LoanCaseAccountFiles,
            'template_path' => $template_path,
            'template_path_old' => $template_path_old,
            'OfficeBankAccount' => $OfficeBankAccount,
            'lawyerName' => $lawyerName,
            'ApprovalName' => $ApprovalName,
            'lawyerApprovalName' => $lawyerApprovalName,
            'current_user' => $current_user,
            'bank' => $bank,
            'bank_list' => $bank_list,
            'AccountItem' => $AccountItem,
            'parameters' => $parameters,
            'requestName' => $requestName
        ]);
    }


    public function updateVoucherAccountItem(Request $request)
    {

        $oriAccountItemName = '';
        $oriAccountItemAmt = 0;
        $newAccountItemName = '';

        $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $request->input('accountItem'))->first();

        if ($request->input('request_val') == 0) {
            return response()->json(['status' => 2, 'message' => 'No request value']);
        }

        if (!$LoanCaseBillDetails) {
            return response()->json(['status' => 2, 'message' => 'Account not exist in bill']);
        }

        if ($LoanCaseBillDetails->amount < $request->input('request_val')) {
            return response()->json(['status' => 2, 'message' => 'Selected account balance not enough']);
        }

        $VoucherDetails = VoucherDetails::where('id', '=', $request->input('current_id'))->first();

        $LoanCaseBillDetailsReturn = LoanCaseBillDetails::where('id', '=', $VoucherDetails->account_details_id)->first();

        //return value back to the original account item
        if ($LoanCaseBillDetailsReturn) {
            $oriAccountItemAmt = $VoucherDetails->amount;
            $LoanCaseBillDetailsReturn->amount += $VoucherDetails->amount;
            $LoanCaseBillDetailsReturn->save();

            $AccountItemROri = AccountItem::where('id', '=', $LoanCaseBillDetailsReturn->account_item_id)->first();
            $oriAccountItemName = $AccountItemROri->name;
        }


        //Take out the request amount from new account item
        $LoanCaseBillDetails->amount -= $request->input('request_val');
        $LoanCaseBillDetails->save();

        $AccountItemNew = AccountItem::where('id', '=', $LoanCaseBillDetails->account_item_id)->first();
        $newAccountItemName = $AccountItemNew->name;

        //Update voucher details
        $VoucherDetails->amount = $request->input('request_val');
        $VoucherDetails->account_details_id = $request->input('accountItem');
        $VoucherDetails->save();

        // update main voucher total amount
        $voucher_main_id = $VoucherDetails->voucher_main_id;

        $VoucherMain = VoucherMain::where('id', '=', $voucher_main_id)->first();
        $VoucherDetailsCheck = VoucherDetails::where('voucher_main_id', '=', $voucher_main_id)->get();

        if (count($VoucherDetailsCheck) > 0) {
            $total_sum = 0;
            for ($i = 0; $i < count($VoucherDetailsCheck); $i++) {
                $total_sum += $VoucherDetailsCheck[$i]->amount;
            }

            $VoucherMain->total_amount = $total_sum;
            $VoucherMain->save();
        }

        $this->updateMainBillAmount($VoucherMain->case_bill_main_id);


        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $VoucherMain->case_id;
        $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
        $AccountLog->object_id = $LoanCaseBillDetailsReturn->id;
        $AccountLog->object_id_2 = $LoanCaseBillDetails->id;
        $AccountLog->ori_amt = $oriAccountItemAmt;
        $AccountLog->new_amt = $request->input('request_val');
        $AccountLog->action = 'ChangeVoucherAccountItem';
        $AccountLog->desc = $current_user->name . ' Update voucher (' . $VoucherMain->voucher_no . ') ' . $oriAccountItemName . ' (RM ' . $oriAccountItemAmt . ') --> '  . $newAccountItemName . ') (RM ' . $request->input('request_val') . ')';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();


        return response()->json(['status' => 1, 'message' => 'Voucher updated']);
    }

    public function checkTrustBalance($VoucherMain, $voucher)
    {
        $total_change_val = 0;
        $case_id = $VoucherMain->case_id;

        $loan_case_trust_main_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $case_id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->sum('total_amount');

        $loan_case_trust_main_dis = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $case_id)
            ->where('v.voucher_type', '=', 2)
            ->where('v.account_approval', '=', 1)
            ->where('v.status', '<>', 99)
            ->sum('total_amount');

        if ($VoucherMain->voucher_type != 1) {
            if (count($voucher) > 0) {
                for ($i = 0; $i < count($voucher); $i++) {
                    $total_change_val += $voucher[$i]['update_value'];
                }
            }

            $remaining_amt = $loan_case_trust_main_receive - $loan_case_trust_main_dis;
            // $amt = (float)$request->input('amount');

            $result =  bcsub($remaining_amt, $total_change_val, 2);

            if ($result < 0) {
                return false;
            }
        }

        return true;
    }

    public function updateVoucherValue(Request $request, $id)
    {
        $errorCOunt = 0;

        if ($request->input('voucher') != null) {
            $voucher = json_decode($request->input('voucher'), true);
        }

        $VoucherMain = VoucherMain::where('id', '=', $id)->first();

        if ($this->checkTrustBalance($VoucherMain, $voucher) == false) {
            return response()->json(['status' => 2, 'message' => 'No enough trust fund']);
        }


        $voucher_main_id = 0;
        $desc = '';
        $dibsType = 'BILL_DISB';

        $current_user = auth()->user();

        if (count($voucher) > 0) {
            for ($i = 0; $i < count($voucher); $i++) {
                $orgianal_val = 0;
                $VoucherDetails = VoucherDetails::where('id', '=', $voucher[$i]['itemID'])->first();

                if ($voucher_main_id == 0) {
                    $voucher_main_id = $VoucherDetails->voucher_main_id;
                }

                $VoucherMain = VoucherMain::where('id', '=', $VoucherDetails->voucher_main_id)->first();

                if ($VoucherMain->voucher_type == 1) {

                    $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $VoucherDetails->account_details_id)->first();
                    $valueCheck = $VoucherDetails->amount + $LoanCaseBillDetails->quo_amount;

                    $orgianal_val = $VoucherDetails->amount;

                    if ($voucher[$i]['update_value'] <  $VoucherDetails->amount) {
                        $return_amount = $VoucherDetails->amount - $voucher[$i]['update_value'];

                        $VoucherDetails->amount = $voucher[$i]['update_value'];
                        $VoucherDetails->save();

                        if ($VoucherMain->account_approval <> 2) {
                            $LoanCaseBillDetails->amount += $return_amount;
                            $LoanCaseBillDetails->save();
                        }
                    } else {
                        $checkValue = $LoanCaseBillDetails->amount + $VoucherDetails->amount;

                        if ($voucher[$i]['update_value'] <= $checkValue) {
                            $return_amount = $VoucherDetails->amount - $voucher[$i]['update_value'];
                            $VoucherDetails->amount = $voucher[$i]['update_value'];
                            $VoucherDetails->save();

                            if ($VoucherMain->account_approval <> 2) {
                                $LoanCaseBillDetails->amount += $return_amount;
                                $LoanCaseBillDetails->save();
                            }
                        } else {
                            $errorCOunt += 1;
                        }
                    }

                    $AccountItem = AccountItem::where('id', '=', $LoanCaseBillDetails->account_item_id)->first();
                    $AccountItemName = $AccountItem->name;

                    $desc = $current_user->name . ' Update voucher (' . $VoucherMain->voucher_no . ') ' . $AccountItemName . ' (RM ' . $orgianal_val . ' --> RM ' . $VoucherDetails->amount . ')';
                } else {

                    $dibsType = 'TRUST_DISB';

                    $orgianal_val = $VoucherDetails->amount;

                    $return_amount = $VoucherDetails->amount - $voucher[$i]['update_value'];
                    $VoucherMain->total_amount = $voucher[$i]['update_value'];
                    $VoucherMain->save();

                    $VoucherDetails->amount = $voucher[$i]['update_value'];
                    $VoucherDetails->save();
                    $desc = $current_user->name . ' Update voucher (' . $VoucherMain->voucher_no . ') - Trust  (RM ' . $orgianal_val . ' --> RM ' . $VoucherDetails->amount . ')';
                }

                $AccountLog = new AccountLog();
                $AccountLog->user_id = $current_user->id;
                $AccountLog->case_id = $VoucherDetails->case_id;
                $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
                $AccountLog->object_id = $VoucherDetails->id;
                $AccountLog->object_id_2 = $VoucherMain->id;
                $AccountLog->ori_amt = $orgianal_val;
                $AccountLog->new_amt = $VoucherDetails->amount;
                $AccountLog->action = 'ChangeVoucherValue';
                $AccountLog->desc = $desc;
                $AccountLog->status = 1;
                $AccountLog->created_at = date('Y-m-d H:i:s');
                $AccountLog->save();

            }
        }


        if ($errorCOunt <= 0) {

            $sum = 0;

            $voucherItemSum = VoucherDetails::where('voucher_main_id', $id)->where('status', 1)->sum('amount');

            $VoucherMain->total_amount = $voucherItemSum;
            $VoucherMain->save();

            if ($VoucherMain->voucher_type == 1) 
            {
                // if ($voucher_main_id > 0) {
                //     for ($i = 0; $i < count($voucher); $i++) {
                //         $VoucherDetails = VoucherDetails::where('id', '=', $voucher[$i]['itemID'])->first();
    
                //         $sum += $VoucherDetails->amount;
                //     }
    
                    // $VoucherMain = VoucherMain::where('id', '=', $voucher_main_id)->first();
                    // $VoucherMain->total_amount = $sum;
                    // $VoucherMain->save();
    
                //     CaseController::updateBillCaseBillDisb($VoucherMain->case_id, $VoucherMain->case_bill_main_id);
                // }

                $this->updateBillCaseDetails($VoucherMain->id);
            }
            else if ($VoucherMain->voucher_type == 2) 
            {
                $this->reverseTrustDisburse($VoucherMain->id);
            }

            return response()->json(['status' => 1, 'message' => 'Voucher amount updated']);
        } else {
            return response()->json(['status' => 2, 'message' => 'There are some item exceed bill amount']);
        }
    }


    public function updateVoucherStatus(Request $request, $id)
    {
        $status = 1;
        $type = 0;
        $totalAmount = 0;
        $current_user = auth()->user();
        $message = '';

        $type = $request->input('type');

        if ($type == "1") {
            $VoucherMain = VoucherMain::where('id', '=', $id)->first();

            $VoucherMain->cheque_no = $request->input('cheque_no');
            $VoucherMain->payee = $request->input('payee');


            $VoucherMain->remark = $request->input('remark');
            $VoucherMain->adjudication_no = $request->input('adjudication_no');
            $VoucherMain->payment_type = $request->input('payment_type');
            $VoucherMain->bank_account = $request->input('bank_account');
            $VoucherMain->email = $request->input('email');
            $VoucherMain->credit_card_no = $request->input('credit_card_no');
            $VoucherMain->bank_id = $request->input('payee_bank');

            if ($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'maker' || in_array($current_user->id, [51, 127])) {
                if ($request->input('payment_date') <> 'undefined') {
                    $VoucherMain->payment_date = $request->input('payment_date');
                }
                $VoucherMain->transaction_id = $request->input('transaction_id');
                $VoucherMain->office_account_id = $request->input('OfficeBankAccount_id');
            }

            // if ($current_user->menuroles == 'lawyer') $VoucherMain->lawyer_id = $current_user->id;
            // if ($current_user->menuroles == 'account') $VoucherMain->approval_id = $current_user->id;
            // $VoucherMain->office_account_id = $request->input('OfficeBankAccount_id');
            $VoucherMain->save();

            $message = 'Updated voucher info';

            return response()->json(['status' => $status, 'message' => $message]);
        } else if ($type == "2") {
            $VoucherMain = VoucherMain::where('id', '=', $id)->first();

            $VoucherMain->lawyer_id = $current_user->id;
            $VoucherMain->lawyer_approval = 1;

            $VoucherMain->save();

            $message = 'Approved the voucher and send to account';

            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'approved voucher ' . $VoucherMain->voucher_no;
            $Notification->user_id = 0;
            $Notification->role = 'account|admin|management';
            $Notification->parameter1 = $VoucherMain->case_id;
            $Notification->parameter2 = $id;
            $Notification->module = 'voucher';
            $Notification->bln_read = 0;
            $Notification->status = 1;
            $Notification->created_at = now();
            $Notification->created_by = $current_user->id;
            $Notification->save();

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $VoucherMain->case_id;
            $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
            $AccountLog->object_id = $id;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'Voucher';
            $AccountLog->desc = $current_user->name . ' Approved voucher (' . $VoucherMain->voucher_no . ') and send to account ';
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => $status, 'message' => $message]);
        } else {
            $status = $request->input('status');

            $action = 'approved';

            if ($type == 4) {
                $status = 1;
            } else {
                $status = 2;
                $action = 'rejected';
            }

            $VoucherMain = VoucherMain::where('id', '=', $id)->first();

            if ($current_user->menuroles == "lawyer") {
                $VoucherMain->lawyer_approval = $status;
                $VoucherMain->lawyer_reject_reason = $request->input('lawyer_reject_reason');
                $VoucherMain->lawyer_id = $current_user->id;


                $Notification  = new Notification();
                $Notification->name = $current_user->name;
                $Notification->desc = $action . ' voucher ' . $VoucherMain->voucher_no;
                $Notification->user_id = 0;
                $Notification->role = 'clerk';
                $Notification->parameter1 = $VoucherMain->case_id;
                $Notification->parameter2 = $id;
                $Notification->module = 'voucher';
                $Notification->bln_read = 0;
                $Notification->status = 1;
                $Notification->created_at = now();
                $Notification->created_by = $current_user->id;
                $Notification->save();

                $current_user = auth()->user();
                $AccountLog = new AccountLog();
                $AccountLog->user_id = $current_user->id;
                $AccountLog->case_id = $VoucherMain->case_id;
                $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
                $AccountLog->object_id = $id;
                $AccountLog->ori_amt = 0;
                $AccountLog->new_amt = 0;
                $AccountLog->action = 'Voucher';
                $AccountLog->desc = $current_user->name . ' Rejected voucher (' . $VoucherMain->voucher_no . ')';
                $AccountLog->status = 1;
                $AccountLog->created_at = date('Y-m-d H:i:s');
                $AccountLog->save();
            } else if ($current_user->menuroles == "account" || $current_user->menuroles == "maker") {

                $VoucherMain->account_approval = $status;
                $VoucherMain->status = 1;
                $VoucherMain->approval_id = $current_user->id;
                if ($type == "3") {
                    $VoucherMain->account_reject_reason = $request->input('account_reject_reason');
                }

                $Notification  = new Notification();
                $Notification->name = $current_user->name;
                $Notification->desc = $action . ' voucher ' . $VoucherMain->voucher_no;
                $Notification->user_id = 0;
                $Notification->role = 'lawyer|clerk|admin';
                $Notification->parameter1 = $VoucherMain->case_id;
                $Notification->parameter2 = $id;
                $Notification->module = 'voucher';
                $Notification->bln_read = 0;
                $Notification->status = 1;
                $Notification->created_at = now();
                $Notification->created_by = $current_user->id;
                $Notification->save();

                $current_user = auth()->user();
                $AccountLog = new AccountLog();
                $AccountLog->user_id = $current_user->id;
                $AccountLog->case_id = $VoucherMain->case_id;
                $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
                $AccountLog->object_id = $id;
                $AccountLog->ori_amt = 0;
                $AccountLog->new_amt = 0;
                $AccountLog->action = 'Voucher';
                $AccountLog->desc = $current_user->name . ' ' . $action . ' voucher (' . $VoucherMain->voucher_no . ')';
                $AccountLog->status = 1;
                $AccountLog->created_at = date('Y-m-d H:i:s');
                $AccountLog->save();

                $this->readNotification($VoucherMain->case_id, $id);
            }

            // $prevNotification = Notification::where('parameter1', '=', $VoucherMain->case_id)
            // ->where('parameter2', '=', $id)
            // ->where('role', '=', 'account|admin|management')
            // ->where('bln_read', '=', 0)
            // ->first();

            // return  $prevNotification;

            // if ($prevNotification != null)
            // {
            //     $prevNotification->bln_read = 1;
            //     $prevNotification->save();
            // }




            // $VoucherMain->status = $status;
            $VoucherMain->cheque_no = $request->input('cheque_no');
            $VoucherMain->payee = $request->input('payee');
            $VoucherMain->remark = $request->input('remark');
            $VoucherMain->save();
        }


        if ($type == "3") {

            if ($VoucherMain->voucher_type == 1) {
                // $this->rejectRevertFloatingValue($id);
                $this->updateBillCaseDetails($id);
            } else {
                $this->reverseTrustDisburse($id);
            }

            $message = 'Rejected the voucher';
        } else {
            if ($VoucherMain->voucher_type == 2) {
                $this->reverseTrustDisburse($id);
            }
            $message = 'Approved the voucher';
        }

        return response()->json(['status' => $status, 'message' => $message]);
    }

    // new approve voucher to cater some new requirements
    public function approveVoucher(Request $request, $id)
    {
        $current_user = auth()->user();
        $role = '';
        $voucher_type = 'BILLDISB';
        $voucher_type_v2 = 'BILL_DISB';
        $message = '';

        $VoucherMain = VoucherMain::where('id', '=', $id)->first();

        if (in_array($current_user->menuroles, ['lawyer', 'management', 'admin'])) {
            $VoucherMain->lawyer_approval = 1;
            $VoucherMain->lawyer_id = $current_user->id;
            $VoucherMain->lawyer_approval_date = date('Y-m-d H:i:s');

            $role = 'account|admin|management';
            $message = 'Approved the voucher and send to account';
        } else if (in_array($current_user->menuroles, ['account', 'maker']) || in_array($current_user->id, [51, 127])) {
            if ($VoucherMain->transaction_id == '' || $VoucherMain->transaction_id == null || $VoucherMain->office_account_id == 0) {
                return response()->json(['status' => 0, 'message' => 'Please make sure transaction ID and office account fill before approve']);
            }

            if ($VoucherMain->payment_date == null) {
                return response()->json(['status' => 0, 'message' => 'Please make sure payment date updated before approve']);
            }

            $VoucherMain->account_approval = 1;
            $VoucherMain->approval_id = $current_user->id;

            $role = 'lawyer|clerk|admin';

            $this->readNotification($VoucherMain->case_id, $id);
            $message = 'Approved the voucher';
        }

        $VoucherMain->save();

        $desc = 'approved voucher ' . $VoucherMain->voucher_no;
        $param_notification = [
            'desc' => $desc,
            'module' => 'voucher',
            'role' => $role,
            'parameter1' => $VoucherMain->case_id,
            'parameter2' => $id,
        ];

        $NotificationController = new NotificationController();
        $NotificationController->createNotificationV2($param_notification);

        $bill_id = 0;

        if ($VoucherMain->voucher_type == 1) {
            $bill_id = $VoucherMain->case_bill_main_id;
        }


        $param_log = [
            'case_id' => $VoucherMain->case_id,
            'bill_id' => $bill_id,
            'object_id' => $id,
            'ori_amt' => 0,
            'new_amt' => 0,
            'action' => 'Voucher',
            'desc' => $current_user->name . ' approved voucher (' . $VoucherMain->voucher_no . ')',
        ];


        $LogsController = new LogsController();
        $LogsController->createAccountLog($param_log);

        if (in_array($current_user->menuroles, ['account', 'maker'])) {
            if ($VoucherMain->voucher_type == 2) {
                $this->reverseTrustDisburse($id);
                $voucher_type = 'TRUSTDISB';
                $voucher_type_v2 = 'TRUST_DISB';
            }
        }

        if (in_array($current_user->menuroles, ['account', 'maker'])) {
            $LedgerEntries = new LedgerEntries();

            $voucher_item = '';

            $VoucherDetails = DB::table('voucher_details as a')
                ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
                ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
                ->select('a.*', 'c.name as account_name')
                ->where('voucher_main_id', '=', $VoucherMain->id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
                }
            }

            $transaction = '';

            if ($VoucherMain->transaction_id != null) {
                $transaction = $VoucherMain->transaction_id;
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $VoucherMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $bill_id;
            $LedgerEntries->user_id = $VoucherMain->created_by;
            $LedgerEntries->key_id = $VoucherMain->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $VoucherMain->total_amount;
            $LedgerEntries->bank_id = $VoucherMain->office_account_id;
            $LedgerEntries->remark = $VoucherMain->remark;
            $LedgerEntries->sys_desc = $voucher_item;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $VoucherMain->payment_date;
            $LedgerEntries->type = $voucher_type;
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntriesV2();

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $VoucherMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $bill_id;
            $LedgerEntries->user_id = $VoucherMain->created_by;
            $LedgerEntries->cheque_no = $VoucherMain->voucher_no;
            $LedgerEntries->key_id = $VoucherMain->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $VoucherMain->total_amount;
            $LedgerEntries->bank_id = $VoucherMain->office_account_id;
            $LedgerEntries->payee = $VoucherMain->payee;
            $LedgerEntries->remark = $VoucherMain->remark;
            $LedgerEntries->desc_1 = $voucher_item;
            $LedgerEntries->is_recon = 0;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $VoucherMain->payment_date;
            $LedgerEntries->type = $voucher_type_v2;
            $LedgerEntries->save();

            $LoanCase = LoanCase::where('id', $VoucherMain->case_id)->first();
            CaseController::adminUpdateClientLedger($LoanCase);
        }


        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function updateVoucherStatusV2(Request $request, $id)
    {
        $current_user = auth()->user();
        $message = 'Status Updated';
        $message2 = 'Status Updated';

        $type = $request->input('type');

        $VoucherMain = VoucherMain::where('id', '=', $id)->first();

        $VoucherMain->cheque_no = $request->input('cheque_no');
        $VoucherMain->payee = $request->input('payee');
        $VoucherMain->payment_date = $request->input('payment_date');
        $VoucherMain->remark = $request->input('remark');
        $VoucherMain->adjudication_no = $request->input('adjudication_no');
        $VoucherMain->transaction_id = $request->input('transaction_id');
        $VoucherMain->office_account_id = $request->input('OfficeBankAccount_id');
        $VoucherMain->payment_type = $request->input('payment_type');
        $VoucherMain->email = $request->input('email');
        $VoucherMain->bank_account = $request->input('bank_account');
        $VoucherMain->credit_card_no = $request->input('credit_card_no');

        $VoucherMain->office_account_id = $request->input('OfficeBankAccount_id');


        if ($type == "INPROGRESS") {
            $VoucherMain->account_approval = 6;
            $message2 = 'in progress';
        } else  if ($type == "PENDING") {
            $VoucherMain->account_approval = 0;
            $message2 = 'pending';
        }

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $VoucherMain->case_id;
        $AccountLog->bill_id = $VoucherMain->case_bill_main_id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'Voucher';
        $AccountLog->desc = $current_user->name . ' update voucher (' . $VoucherMain->voucher_no . ') to ' .  $message2;
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        $VoucherMain->save();

        $this->readNotification($VoucherMain->case_id, $id);

        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function unapproveVoucher(Request $request, $id)
    {
        $current_user = auth()->user();
        $message = 'Status Updated';

        $type = $request->input('type');

        $VoucherMain = VoucherMain::where('id', '=', $id)->first();

        if ($VoucherMain) {
            if ($VoucherMain->is_recon == 1) {
                return response()->json(['status' => 0, 'message' => 'The voucher already recon, not allow to unapprove']);
            } else {
                $VoucherMain->account_approval = 0;
                $VoucherMain->updated_at = date('Y-m-d H:i:s');
                $VoucherMain->save();

                $current_user = auth()->user();

                $bill_id = 0;
                $type = '';
                $type_v2 = '';

                if ($VoucherMain->voucher_type == 1) {
                    $bill_id = $VoucherMain->case_bill_main_id;
                    $type = 'BILLDISB';
                    $type_v2 = 'BILL_DISB';
                } else if ($VoucherMain->voucher_type == 2) {
                    $type = 'TRUSTDISB';
                    $type_v2 = 'TRUST_DISB';
                }

                $AccountLog = new AccountLog();
                $AccountLog->user_id = $current_user->id;
                $AccountLog->case_id = $VoucherMain->case_id;
                $AccountLog->bill_id = $bill_id;
                $AccountLog->ori_amt = 0;
                $AccountLog->new_amt = 0;
                $AccountLog->action = 'Voucher|unapproved';
                $AccountLog->object_id = $id;
                $AccountLog->desc = $current_user->name . ' unapproved voucher (' . $VoucherMain->voucher_no . ')';
                $AccountLog->status = 1;
                $AccountLog->created_at = date('Y-m-d H:i:s');
                $AccountLog->save();

                if ($VoucherMain->transaction_id != null) {
                    $transaction = $VoucherMain->transaction_id;
                }


                $LedgerEntries = LedgerEntries::where('key_id', '=', $VoucherMain->id)->where('type', '=', $type)->first();
                $LedgerEntries->status = 99;
                $LedgerEntries->save();

                $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', $type_v2)->first();

                if ($LedgerEntries) {
                    $LedgerEntries->status = 99;
                    $LedgerEntries->save();
                }

                $LoanCase = LoanCase::where('id', $VoucherMain->case_id)->first();
                CaseController::adminUpdateClientLedger($LoanCase);


                return response()->json(['status' => 1, 'message' => 'Unapproved the voucher']);
            }
        }

        return response()->json(['status' => 0, 'message' => 'Error occur, please try again later']);
    }

    public function readNotification($case_id, $voucher_id)
    {
        $Notification = Notification::where('bln_read', '=', '0')->where('role', '=', 'account|admin|management')->where('parameter1', '=', $case_id)->where('parameter2', '=', $voucher_id)->first();

        if ($Notification) {
            $Notification->bln_read = 1;
            $Notification->save();
        }
    }

    public function rejectRevertFloatingValue($id)
    {
        $main_bill_id = 0;
        $total_revert_value = 0;
        $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->get();

        if (count($VoucherDetails) > 0) {
            for ($i = 0; $i < count($VoucherDetails); $i++) {

                $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $VoucherDetails[$i]->account_details_id)->first();

                if ($LoanCaseBillDetails) {
                    $main_bill_id = $LoanCaseBillDetails->loan_case_main_bill_id;
                    $total_revert_value +=  $VoucherDetails[$i]->amount;
                    $LoanCaseBillDetails->amount += $VoucherDetails[$i]->amount;
                    $LoanCaseBillDetails->save();
                }
            }
        }

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $main_bill_id)->first();
        $LoanCaseBillMain->used_amt -= $total_revert_value;
        $LoanCaseBillMain->save();

        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();
        $LoanCase->total_bill -= $total_revert_value;
        $LoanCase->save();
    }

    public function resubmitFloatingValue($id, $VoucherMain)
    {
        $main_bill_id = 0;
        $total_revert_value = 0;

        if ($VoucherMain->voucher_type == 1) {
            $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {

                    $total_sum = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                        ->select('v.*')
                        ->where('v.case_bill_main_id', '=', $VoucherMain->case_bill_main_id)
                        // ->where('v.voucher_type', '=', 2)
                        ->where('vd.account_details_id', $VoucherDetails[$i]->account_details_id)
                        ->where('v.status', '<>', 99)
                        ->whereIn('v.account_approval', [0,1,5])
                        ->sum('vd.amount');

                        // $total_sum = VoucherDetails::where('voucher_main_id', $id)
                        //                             ->where('account_details_id', $VoucherDetails[$i]->account_details_id)
                        //                             ->whereNotIn('account_approval', [0,99])->sum('amount');

                        // return $total_sum;
                    $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $VoucherDetails[$i]->account_details_id)->first();

                    if ($LoanCaseBillDetails) {
                        $main_bill_id = $LoanCaseBillDetails->loan_case_main_bill_id;
                        // $total_revert_value +=  $VoucherDetails[$i]->amount;
                        $LoanCaseBillDetails->amount = $LoanCaseBillDetails->quo_amount - $total_sum;
                        $LoanCaseBillDetails->save();
                    }
                }
            }

            // CaseController::updateBillCaseBillDisb($VoucherMain->case_id, $VoucherMain->case_bill_main_id);

            // $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $main_bill_id)->first();
            // $LoanCaseBillMain->used_amt += $total_revert_value;
            // $LoanCaseBillMain->save();

            // $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();
            // $LoanCase->total_bill += $total_revert_value;
            // $LoanCase->save();
        } else {
            $total_sum = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*')
                ->where('v.case_id', '=', $VoucherMain->case_id)
                ->where('v.voucher_type', '=', 2)
                ->where('v.status', '<>', 99)
                ->sum('vd.amount');

            LoanCaseTrustMain::where('case_id', $VoucherMain->case_id)->update(['total_used' => $total_sum]);
        }
    }

    public function resubmitVoucher($id)
    {

        $current_user = auth()->user();
        $VoucherMain = VoucherMain::where('id', '=', $id)->first();

        if ($this->checkBillAvailableAmount($id, $VoucherMain) == false) {
            return response()->json(['status' => 0, 'message' => 'Please edit resubmit amount, the requested item not enough balance']);
        } 

        $VoucherMain->account_approval = 5;
        $VoucherMain->save();

        if ($VoucherMain->voucher_type == 1)
        {
            // $this->resubmitFloatingValue($id, $VoucherMain);
            $this->updateBillCaseDetails($VoucherMain->id);
        }
        else if ($VoucherMain->voucher_type == 2)
        {
            $this->reverseTrustDisburse($id);
        }
        
        $Notification  = new Notification();
        $Notification->name = $current_user->name;
        $Notification->desc = 'resubmit voucher ' . $VoucherMain->voucher_no;
        $Notification->user_id = 0;
        $Notification->role = 'account|admin|management';
        $Notification->parameter1 = $VoucherMain->case_id;
        $Notification->parameter2 = $id;
        $Notification->module = 'voucher';
        $Notification->bln_read = 0;
        $Notification->status = 1;
        $Notification->created_at = now();
        $Notification->created_by = $current_user->id;
        $Notification->save();

        return response()->json(['status' => 1, 'message' => 'Voucher resubmit']);
    }

    public function deleteVoucher($id)
    {
        $VoucherMain = VoucherMain::where('id', '=', $id)->first();
        $current_user = auth()->user();
        $bill_id = 0;

        if (!$VoucherMain) {
            return response()->json(['status' => 0, 'message' => 'No voucher found']);
        }

        if ($current_user->menuroles <> 'admin') {
            if ($VoucherMain->account_approval == 1) {
                return response()->json(['status' => 0, 'message' => 'Voucher already approved by account, please look for account']);
            }
        }

        if ($VoucherMain->transaction_id != null || $VoucherMain->transaction_id != '') {
            return response()->json(['status' => 2, 'message' => 'Transaction ID record created, not allow to delete']);
        }

        if ($VoucherMain->is_recon == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already recon, not allow to delete']);
        }

        $type = '';

        if ($VoucherMain->voucher_type == 1) {
            $type = 'bill';
            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $VoucherMain->case_bill_main_id)->first();

            if ($LoanCaseBillMain) {
                $bill_id = $LoanCaseBillMain->id;
            }
        } else {
            $type = 'trust';
        }

        $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->get();

        for ($i = 0; $i < count($VoucherDetails); $i++) {
            $VoucherDetails[$i]->status = 99;
            $VoucherDetails[$i]->save();

            $LedgerEntries = LedgerEntries::where('key_id', '=', $id)->whereIn('type', ['BILLDISB'])->first();

            if ($LedgerEntries) {
                $LedgerEntries->delete();
            }

            $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', 'TRUST_DISB')->first();

            if ($LedgerEntries) {
                $LedgerEntries->status = 99;
                $LedgerEntries->save();
            }
        }

        $VoucherMain->status = 99;
        $VoucherMain->save();

        if ($VoucherMain->account_approval <> 2) {
            if ($VoucherMain->voucher_type == 1) {
                // $this->rejectRevertFloatingValue($id);
                $this->updateBillCaseDetails($id);
            } else {
                $this->reverseTrustDisburse($id);
            }
        }

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $VoucherMain->case_id;
        $AccountLog->bill_id = $bill_id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->object_id = $id;
        $AccountLog->action = 'Delete';
        $AccountLog->desc = $current_user->name . ' deleted ' . $type . ' voucher (' . $VoucherMain->voucher_no . ') ';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        $this->readNotification($VoucherMain->case_id, $id);

        // if ($VoucherMain) {
        //     if ($VoucherMain->account_approval <> 1) {

        //     } else {
        //         return response()->json(['status' => 0, 'message' => 'Voucher already approved by account, please look for account']);
        //     }
        // } else {
        //     return response()->json(['status' => 0, 'message' => 'No voucher found']);
        // }


        return response()->json(['status' => 1, 'message' => 'Voucher deleted']);
    }

    // public function updateTrustDisburse($id)
    // {
    //     $sumTrust = 0;
    //     $voucherMain = VoucherMain::where('id', '=', $id)->first();

    //     if ($voucherMain->voucher_type == 2) {
    //         $voucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->where('status', '=', 3)->get();

    //         $loan_case_trust_main_dis = DB::table('voucher_main as v')
    //             ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
    //             ->select('v.*', 'vd.amount')
    //             ->where('v.case_id', '=', $voucherMain->case_id)
    //             ->where('v.voucher_type', '=', 2)
    //             ->where('v.account_approval', '<>', 2)
    //             ->where('v.status', '<>', 99)
    //             ->get();

    //         for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {
    //             $sumTrust += $loan_case_trust_main_dis[$i]->amount;
    //         }

    //         $loanCase = LoanCaseTrustMain::where('case_id', '=', $voucherMain->case_id)->first();

    //         $loanCase->total_used = $sumTrust;
    //         $loanCase->updated_at = date('Y-m-d H:i:s');
    //         $loanCase->save();
    //     } else if ($voucherMain->voucher_type == 3) {
    //         // $voucherDetails = VoucherDetails::where('case_id', '=', $case_id)->where('status', '=', 3)->get();

    //         $loan_case_trust_main_dis = DB::table('voucher_main as v')
    //             ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
    //             ->select('v.*', 'vd.amount')
    //             ->where('v.case_id', '=', $voucherMain->case_id)
    //             ->where('v.voucher_type', '=', 3)
    //             ->where('v.account_approval', '<>', 2)
    //             ->where('v.status', '<>', 99)
    //             ->get();

    //         for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {
    //             $sumTrust += $loan_case_trust_main_dis[$i]->amount;
    //         }
    //         $loanCase = LoanCaseTrustMain::where('case_id', '=', $voucherMain->case_id)->first();

    //         // $total_trust = (float)($loanCase->total_trust) + (float)($request->input('amount'));

    //         // $loanCase->collected_trust = $collected_trust;

    //         $loanCase->total_received = $sumTrust;
    //         $loanCase->updated_at = date('Y-m-d H:i:s');
    //         $loanCase->save();
    //     }
    // }



    public static function reverseTrustDisburse($id)
    {
        $voucherMain = VoucherMain::where('id', '=', $id)->first();

        if ($voucherMain->voucher_type == 2) {
            $total_sum = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*', 'vd.amount')
            ->where('v.case_id', '=', $voucherMain->case_id)
            ->where('v.voucher_type', '=', 2)
            ->whereNotIn('v.account_approval', [2])
            ->where('v.status', '<>', 99)
            ->sum('vd.amount');

            LoanCaseTrustMain::where('case_id', $voucherMain->case_id)->update(['total_used' => $total_sum]);
            LoanCase::where('id', $voucherMain->case_id)->update(['total_trust' => $total_sum]);

        } else if ($voucherMain->voucher_type == 3) {
            $total_sum = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $voucherMain->case_id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->sum('vd.amount');

            LoanCaseTrustMain::where('case_id', $voucherMain->case_id)->update(['total_received' => $total_sum]);
            LoanCase::where('id', $voucherMain->case_id)->update(['collected_trust' => $total_sum]);

        }
    }

    public static function checkBillAvailableAmount($id, VoucherMain $VoucherMain)
    {
        if ($VoucherMain->voucher_type == 1)
        {
            $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
    
                    if ($VoucherMain->voucher_type == 1) {
                        $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $VoucherDetails[$i]->account_details_id)->first();
    
                        if ($LoanCaseBillDetails) {
                            if ($LoanCaseBillDetails->amount < $VoucherDetails[$i]->amount) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
        else if ($VoucherMain->voucher_type == 2)
        {
            $total_trust_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', $VoucherMain->case_id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->sum('total_amount');

            $total_trust_disburse = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*')
                ->where('v.case_id', $VoucherMain->case_id)
                ->where('v.voucher_type', '=', 2)
                ->whereNotIn('v.account_approval', [2])
                ->where('v.status', '<>', 99)
                ->sum('total_amount');

            $resubmitAmount = VoucherDetails::where('voucher_main_id', '=', $id)->sum('amount');

            $remaining_amt = (float)$total_trust_receive - (float)$total_trust_disburse;

            if (floatval(($remaining_amt)) <  floatval(($resubmitAmount))) {
                return false;
            }
        }

        return true;
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

    public function uploadAccountFile(Request $request, $id)
    {
        $status = 1;
        $data = '';
        $file = $request->file('inp_file');

        $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());
        $filename = time() . '_' . $res;

        $isImage =  ImageController::verifyImage($file);


        $current_user = auth()->user();

        // File extension
        $extension = $file->getClientOriginalExtension();
        $case_ref_no =  $request->input('case_ref_no');
        $case_id =  $request->input('case_id');
        $remarks =  $request->input('remarks');

        // File upload location
        $case_ref_no = str_replace("/", "_", $case_ref_no);
        // $location = 'documents/cases/' . $case_id . '/voucher/';
        $location = 'cases/' . $request->input('case_id') . '/voucher';

        $disk = Storage::disk('Wasabi');
        $s3_file_name = '';

        if ($isImage == true) {
            $s3_file_name = ImageController::resizeImg($file, $location, $filename);
        } else {
            // $file->move($location, $filename);
            // $filepath = url($location . $filename);

            $s3_file_name =  $disk->put($location, $file);
        }

        //   // Upload file
        //   $file->move($location, $filename); 

        // // File path
        // $filepath = url($location . $filename);

        $LoanCaseAccountFiles = new LoanCaseAccountFiles();

        $LoanCaseAccountFiles->main_id =  $id;
        $LoanCaseAccountFiles->case_id =  $case_id;
        $LoanCaseAccountFiles->file_name = $s3_file_name;
        $LoanCaseAccountFiles->ori_name = $file->getClientOriginalName();
        $LoanCaseAccountFiles->s3_file_name = $s3_file_name;
        $LoanCaseAccountFiles->type = $extension;
        $LoanCaseAccountFiles->remarks = $remarks;
        $LoanCaseAccountFiles->status = 1;
        $LoanCaseAccountFiles->created_by = $current_user->id;
        $LoanCaseAccountFiles->created_at = date('Y-m-d H:i:s');
        $LoanCaseAccountFiles->save();

        // $activityLog = [];

        // $activityLog['action'] = 'Upload';
        // $activityLog['case_id'] = $request->input('case_id');
        // $activityLog['checklist_id'] = $request->input('selected_id');
        // $activityLog['desc'] = 'Upload file';

        // $activity_controller = new ActivityLogController;
        // $activity_controller->storeActivityLog($activityLog);

        return response()->json(['status' => $status, 'data' => $data]);
    }


    public function uploadAccountFile2(Request $request, $id)
    {
        $status = 1;
        $data = '';
        $file = $request->file('inp_file');

        $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());
        $filename = time() . '_' . $res;

        $isImage =  ImageController::verifyImage($file);


        $current_user = auth()->user();

        // File extension
        $extension = $file->getClientOriginalExtension();
        $case_ref_no =  $request->input('case_ref_no');
        $case_id =  $request->input('case_id');
        $remarks =  $request->input('remarks');

        // File upload location
        $case_ref_no = str_replace("/", "_", $case_ref_no);
        // $location = 'documents/cases/' . $case_id . '/voucher/';
        $location = 'cases/' . $request->input('case_id') . '/voucher';

        $disk = Storage::disk('Wasabi');
        $s3_file_name = '';

        if ($isImage == true) {
            $s3_file_name = ImageController::resizeImg($file, $location, $filename);
        } else {
            // $file->move($location, $filename);
            // $filepath = url($location . $filename);

            $s3_file_name =  $disk->put($location, $file);
        }

        //   // Upload file
        //   $file->move($location, $filename); 

        // // File path
        // $filepath = url($location . $filename);

        $LoanCaseAccountFiles = new LoanCaseAccountFiles();

        $LoanCaseAccountFiles->main_id =  $id;
        $LoanCaseAccountFiles->case_id =  $case_id;
        $LoanCaseAccountFiles->file_name = $s3_file_name;
        $LoanCaseAccountFiles->ori_name = $file->getClientOriginalName();
        $LoanCaseAccountFiles->s3_file_name = $s3_file_name;
        $LoanCaseAccountFiles->type = $extension;
        $LoanCaseAccountFiles->remarks = $remarks;
        $LoanCaseAccountFiles->status = 1;
        $LoanCaseAccountFiles->created_by = $current_user->id;
        $LoanCaseAccountFiles->created_at = date('Y-m-d H:i:s');
        $LoanCaseAccountFiles->save();

        // $activityLog = [];

        // $activityLog['action'] = 'Upload';
        // $activityLog['case_id'] = $request->input('case_id');
        // $activityLog['checklist_id'] = $request->input('selected_id');
        // $activityLog['desc'] = 'Upload file';

        // $activity_controller = new ActivityLogController;
        // $activity_controller->storeActivityLog($activityLog);

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function deleteVoucherAttachment($id)
    {
        $LoanCaseAccountFiles = LoanCaseAccountFiles::where('id', '=', $id)->first();

        if ($LoanCaseAccountFiles) {
            $VoucherMain = VoucherMain::where('id', '=', $LoanCaseAccountFiles->main_id)->first();

            if ($LoanCaseAccountFiles->s3_file_name) {
                if (Storage::disk('Wasabi')->exists($LoanCaseAccountFiles->s3_file_name)) {
                    Storage::disk('Wasabi')->delete($LoanCaseAccountFiles->s3_file_name);
                }
            } else {
                // if (File::exists(public_path($case_path . 'file_case_' . $case_id . '/' . $file_name))) {
                //     File::delete(public_path($case_path . 'file_case_' . $case_id . '/' . $file_name));
                // }
            }

            $LoanCaseAccountFiles->status = 99;
            $LoanCaseAccountFiles->save();



            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $LoanCaseAccountFiles->case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'DeleteVoucherFile';
            $AccountLog->desc = $current_user->name . ' deleted file (' . $LoanCaseAccountFiles->ori_name . ') for voucher no (' . $VoucherMain->voucher_no . ')';
            $AccountLog->object_id = $LoanCaseAccountFiles->main_id;
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        }

        return response()->json(['status' => 1, 'message' => 'Deleted the file']);
    }

    public function setVoucherReceiptIssue($id)
    {
        $VoucherMain = VoucherMain::where('id', '=', $id)->first();

        if ($VoucherMain) {
            $VoucherMain->receipt_issued = 1;
            $VoucherMain->updated_at = date('Y-m-d H:i:s');
            $VoucherMain->save();
        }

        return response()->json(['status' => 1, 'message' => 'Updated status']);
    }

    public function updateBillCaseDetails($id)
    {
        $VoucherMain = VoucherMain::where('id', $id)->first();

        if ($VoucherMain->voucher_type == 1) {
            $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {

                    $total_sum = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                        ->select('v.*')
                        ->where('v.case_bill_main_id', '=', $VoucherMain->case_bill_main_id)
                        ->where('vd.account_details_id', $VoucherDetails[$i]->account_details_id)
                        ->where('v.status', '<>', 99)
                        ->whereIn('v.account_approval', [0,1,5,6])
                        ->sum('vd.amount');

                    $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $VoucherDetails[$i]->account_details_id)->first();

                    if ($LoanCaseBillDetails) {
                        $LoanCaseBillDetails->amount = $LoanCaseBillDetails->quo_amount - $total_sum;
                        $LoanCaseBillDetails->save();
                    }
                }
            }
        } 

        $this->updateMainBillAmount($VoucherMain->case_bill_main_id);
    }
    
    public static function updateMainBillAmount($bill_id)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', $bill_id)->first();

        if ($LoanCaseBillMain) {
            $voucher_sum = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount as vd_amt')
                ->where('v.case_bill_main_id', $bill_id)
                ->where('v.voucher_type', 1)
                ->where('v.account_approval', '<>', 2)
                ->where('v.status', '<>', 99)
                ->sum('vd.amount');

            $LoanCaseBillMain->used_amt = $voucher_sum;
            $LoanCaseBillMain->save();

            LoanCase::where('id', $LoanCaseBillMain->case_id)->update(['total_bill' => $voucher_sum]);
        }

        return;
    }


}
