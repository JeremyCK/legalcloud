<?php

namespace App\Http\Controllers;

use App\Models\AccountLog;
use App\Models\LedgerEntriesV2;
use Illuminate\Http\Request;
use App\Models\LoanCase;
use App\Models\LoanCaseAccountFiles;
use App\Models\LoanCaseBillDetails;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseTrustMain;
use App\Models\Parameter;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class VoucherControllerV2 extends Controller
{
    public static function getVoucherType($voucher_type)
    {
        switch ($voucher_type) {
            case "BILL_DISB":
                return 1;
                break;
            case "TRUST_DISB":
                return 2;
                break;
            case "BILL_RECV":
                return 4;
                break;
            case "TRUST_RECV":
                return 3;
                break;
            default:
                return "";
        }
    }

    public static function getVoucherTypeByID($voucher_type_id)
    {
        switch ($voucher_type_id) {
            case 1:
                return "BILL_DISB";
                break;
            case 2:
                return "TRUST_DISB";
                break;
            case 4:
                return "BILL_RECV";
                break;
            case 3:
                return "TRUST_RECV";
                break;
            default:
                return "";
        }
    }

    public static function getTransactionType($voucher_type)
    {
        switch ($voucher_type) {
            case "BILL_DISB":
                return 'C';
                break;
            case "TRUST_DISB":
                return 'C';
                break;
            case "BILL_RECV":
                return 'D';
                break;
            case "TRUST_RECV":
                return 'D';
                break;
            default:
                return "";
        }
    }

    public function requestBillDisb(Request $request, $id)
    {
        $voucherList = [];

        if ($request->input('voucher_list') != null) {
            $voucherList = json_decode($request->input('voucher_list'), true);
        }

        if (count($voucherList) > 0) {
            $this->voucherProcessEngine($request, $id, 'BILL_DISB', $voucherList);
        }

        return response()->json(['status' => 1, 'message' => 'Voucher requested']);
    }

    public function receiveBillPayment(Request $request, $id)
    {
        $current_user = auth()->user();
        $this->voucherProcessEngine($request, $id, 'BILL_RECV');

        $bill_receive = VoucherControllerV2::getBillReceive($request->input('bill_main_id'));

        return response()->json([
            'status' => 1,
            'data' => 'Bill received',
            'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive', 'current_user'))->render(),
        ]);
    }

    public function requestTrustDisb(Request $request, $case_id)
    {
        $total_trust_receive = 0;
        $total_trust_disburse = 0;

        if(VoucherControllerV2::checkBalance($case_id, $request->input('amount')) == false)
        {
            return response()->json(['status' => 2, 'message' => 'No enough trust fund']);
        }

        $this->voucherProcessEngine($request, $case_id, 'TRUST_DISB');

        $loan_case_trust_main_dis = CaseController::getTrustRequestList($case_id);
        $case = LoanCase::where('id', $case_id)->first();
        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', $case_id)->first();
        // return response()->json(['status' => 1, 'data' => 'Trust request submitted']);


        return response()->json([
            'status' => 1,
            'message' => 'Trust request submitted',
            'view' => view('dashboard.case.table.tbl-trust-disb-list', compact('loan_case_trust_main_dis', 'case'))->render(),
            'summary' => view('dashboard.case.section.d-trust-summary', compact('LoanCaseTrustMain', 'case'))->render(),
        ]);
    }

    public function receiveTrustFund(Request $request, $case_id)
    {
        $this->voucherProcessEngine($request, $case_id, 'TRUST_RECV');

        $loan_case_trust_main_receive = CaseController::getTrustReceiveList($case_id);
        $case = LoanCase::where('id', $case_id)->first();
        $loan_case_trust_main_dis = CaseController::getTrustRequestList($case_id);
        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', $case_id)->first();

        return response()->json([
            'status' => 1,
            'message' => 'Trust fund received',
            'view' => view('dashboard.case.table.tbl-trust-recv-list', compact('loan_case_trust_main_receive'))->render(),
            'view2' => view('dashboard.case.table.tbl-trust-disb-list', compact('loan_case_trust_main_dis', 'case'))->render(),
            'summary' => view('dashboard.case.section.d-trust-summary', compact('LoanCaseTrustMain', 'case'))->render(),
        ]);
    }

    public static function checkBalance($case_id, $request_amt)
    {
        $total_trust_receive = 0;
        $total_trust_disburse = 0;

        $total_trust_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $case_id)
            ->where('v.voucher_type', 3)
            ->where('v.status', '<>', 99)
            ->sum('total_amount');

        $total_trust_disburse = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $case_id)
            ->where('v.voucher_type', 2)
            ->whereNotIn('v.account_approval', [2])
            ->where('v.status', '<>', 99)
            ->sum('total_amount');


        $remaining_amt = $total_trust_receive - $total_trust_disburse;
        $amt = (float)$request_amt;

        $result =  bcsub($remaining_amt, $amt, 2);

        if ($result < 0) {
            
            return false;
        }

        return true;
    }

    public function checkTrustBalanceEdit($VoucherMain, $voucher)
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

    public function updateVoucher(Request $request, $id)
    {
        $current_user = auth()->user();
        $original_val = 0;

        DB::beginTransaction();

        $voucherMain = voucherMain::where('id', $id)->first();
        $original_val = $voucherMain->total_amount;

        if ($voucherMain->is_recon == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already recon, not allow to update']);
        }

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('TRUST_DISB')]))
        {
            // if(VoucherControllerV2::checkBalance($voucherMain->case_id, $request->input('amount')) == false)
            // {
            //     return response()->json(['status' => 2, 'message' => 'No enough trust fund']);
            // }
        }

        $voucherMain->payment_type = $request->input('payment_type');
        $voucherMain->cheque_no = $request->input('cheque_no');
        $voucherMain->credit_card_no = $request->input('credit_card_no');
        $voucherMain->bank_id = $request->input('bank_id');
        $voucherMain->payee = $request->input('payee');
        $voucherMain->transaction_id = $request->input('transaction_id');
        $voucherMain->office_account_id = $request->input('office_account_id');
        $voucherMain->remark = $request->input('remark');
        $voucherMain->email = $request->input('email');
        $voucherMain->created_by = $current_user->id;
        $voucherMain->bank_account = $request->input('bank_account');
        $voucherMain->adjudication_no = $request->input('adjudication_no');
        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->total_amount = $request->input('amount');
        $voucherMain->save();

        $voucherDetails = VoucherDetails::where('voucher_main_id', $id)->first();

        $voucherDetails->amount = $request->input('amount');
        $voucherDetails->payment_type = $request->input('payment_type');
        $voucherDetails->cheque_no = $request->input('cheque_no');
        $voucherDetails->credit_card_no = $request->input('credit_card_no');
        $voucherDetails->bank_id = $request->input('bank_id');
        $voucherDetails->bank_account = $request->input('bank_account');
        $voucherDetails->updated_at = date('Y-m-d H:i:s');
        $voucherDetails->save();



        $action = '';
        $voucherMain->ori_amt = $original_val;
        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_DISB'), VoucherControllerV2::getVoucherType('TRUST_DISB')])) {
        
        // if (in_array($voucherMain->voucher_type, ['BILL_DISB', 'TRUST_DISB'])) {
            if($voucherMain->account_approval == 1)
            {
                VoucherControllerV2::createLedgerRecord($voucherMain, VoucherControllerV2::getVoucherTypeByID($voucherMain->voucher_type));
            }
           
        }
        else
        {
            VoucherControllerV2::createLedgerRecord($voucherMain, VoucherControllerV2::getVoucherTypeByID($voucherMain->voucher_type));
        }


        
        VoucherControllerV2::updateTotalFigureBillTrust($voucherMain->id);
        // VoucherControllerV2::createAccountLog($voucherMain, $action);
        VoucherControllerV2::createAccountLog($voucherMain, 'UpdateVoucher');

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_DISB')]))
        {
            VoucherControllerV2::revertBillCaseDetails($voucherMain->id);
        }

        DB::commit();

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_RECV'), VoucherControllerV2::getVoucherType('BILL_DISB')])) {
            // $bill_receive = VoucherControllerV2::getBillReceive($voucherMain->case_bill_main_id);
            // $bill_disburse = VoucherControllerV2::getBillDisb($voucherMain->case_bill_main_id);
            // $LoanCaseBillMain = LoanCaseBillMain::where('id', $voucherMain->case_bill_main_id)->first();

            return response()->json([
                'status' => 1,
                'type' => 'bill',
                'message' => 'Record updated',
                // 'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive', 'current_user'))->render(),
                // 'disburse' => view('dashboard.case.table.tbl-bill-disburse-list', compact('bill_disburse', 'current_user',))->render(),
                // 'billSummary' => view('dashboard.case.section.d-bill-summary-details', compact('LoanCaseBillMain'))->render(),
            ]);
        } else {
            $loan_case_trust_main_receive = CaseController::getTrustReceiveList($voucherMain->case_id);
            $case = LoanCase::where('id', $voucherMain->case_id)->first();
            $loan_case_trust_main_dis = CaseController::getTrustRequestList($voucherMain->case_id);
            
            $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', $voucherMain->case_id)->first();

            return response()->json([
                'status' => 1,
                'type' => 'trust',
                'message' => 'Record updated',
                'view' => view('dashboard.case.table.tbl-trust-recv-list', compact('loan_case_trust_main_receive'))->render(),
                'view2' => view('dashboard.case.table.tbl-trust-disb-list', compact('loan_case_trust_main_dis', 'case'))->render(),
                'summary' => view('dashboard.case.section.d-trust-summary', compact('LoanCaseTrustMain', 'case'))->render(),
            ]);
        }

    }

    public function updateVoucherBillDisbAmt(Request $request, $id)
    {
        $current_user = auth()->user();
        $original_val = 0;
        $total_amount = 0;

        DB::beginTransaction();

        $voucherMain = voucherMain::where('id', $id)->first();

        if ($voucherMain->is_recon == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already recon, not allow to update']);
        }

        if ($voucherMain->account_approval == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already approved, not allow to update']);
        }

        if ($request->input('voucher') != null) {
            $voucher = json_decode($request->input('voucher'), true);
        }

        if (count($voucher) <= 0) {
            return response()->json(['status' => 2, 'message' => 'No enough trust fund']);
        }

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_DISB')]))
        {
            for ($i = 0; $i < count($voucher); $i++) {
                $VoucherDetails = VoucherDetails::where('id', $voucher[$i]['itemID'])->first();
                $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $VoucherDetails->account_details_id)->first();
    
                $return_amount = $VoucherDetails->amount - $voucher[$i]['update_value'];
    
                // return $return_amount;
                $original_val = $VoucherDetails->amount;
    
                if($LoanCaseBillDetails->amount + $return_amount < 0)
                {
                    return response()->json(['status' => 2, 'message' => 'There are some item exceed bill amount']);
                }
                else
                {
                    $VoucherDetails->amount = $voucher[$i]['update_value'];
                    $VoucherDetails->save();
                }
    
                $total_amount += $VoucherDetails->amount;
                $desc = $current_user->name . ' Update voucher (' . $voucherMain->voucher_no . ') - Trust  (RM ' . $original_val . ' --> RM ' . $VoucherDetails->amount . ')';
    
                if($original_val != $VoucherDetails->amount)
                {
                    $AccountLog = new AccountLog();
                    $AccountLog->user_id = $current_user->id;
                    $AccountLog->case_id = $VoucherDetails->case_id;
                    $AccountLog->bill_id = $voucherMain->case_bill_main_id;
                    $AccountLog->object_id = $VoucherDetails->id;
                    $AccountLog->object_id_2 = $voucherMain->id;
                    $AccountLog->ori_amt = $original_val;
                    $AccountLog->new_amt = $VoucherDetails->amount;
                    $AccountLog->action = 'ChangeVoucherValue';
                    $AccountLog->desc = $desc;
                    $AccountLog->status = 1;
                    $AccountLog->created_at = date('Y-m-d H:i:s');
                    $AccountLog->save();
                }
            }

        }
        else
        {
            
            for ($i = 0; $i < count($voucher); $i++) {
                $VoucherDetails = VoucherDetails::where('id', $voucher[$i]['itemID'])->first();
                $original_val = $VoucherDetails->amount;

                if ($this->checkTrustBalanceEdit($voucherMain, $voucher) == false) {
                    return response()->json(['status' => 2, 'message' => 'No enough trust fund']);
                }
    
                $return_amount = $VoucherDetails->amount - $voucher[$i]['update_value'];
                // $voucherMain->total_amount = $voucher[$i]['update_value'];
                // $voucherMain->save();
    
                $VoucherDetails->amount = $voucher[$i]['update_value'];
                $VoucherDetails->save();
                $desc = $current_user->name . ' Update voucher (' . $voucherMain->voucher_no . ') - Trust  (RM ' . $original_val . ' --> RM ' . $VoucherDetails->amount . ')';

                $total_amount += $VoucherDetails->amount;
                if($original_val != $VoucherDetails->amount)
                {
                    $AccountLog = new AccountLog();
                    $AccountLog->user_id = $current_user->id;
                    $AccountLog->case_id = $VoucherDetails->case_id;
                    $AccountLog->bill_id = $voucherMain->case_bill_main_id;
                    $AccountLog->object_id = $VoucherDetails->id;
                    $AccountLog->object_id_2 = $voucherMain->id;
                    $AccountLog->ori_amt = $original_val;
                    $AccountLog->new_amt = $VoucherDetails->amount;
                    $AccountLog->action = 'ChangeVoucherValue';
                    $AccountLog->desc = $desc;
                    $AccountLog->status = 1;
                    $AccountLog->created_at = date('Y-m-d H:i:s');
                    $AccountLog->save();
                }
            }
            

        }

        $voucherMain->total_amount = $total_amount;
        $voucherMain->save();

        VoucherControllerV2::updateTotalFigureBillTrust($voucherMain->id);

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_DISB')]))
        {
            VoucherControllerV2::revertBillCaseDetails($voucherMain->id);
        }

        DB::commit();

        return response()->json(['status' => 1, 'message' => 'Voucher amount updated']);

    }

    public function deleteVoucher(Request $request, $id)
    {
        $current_user = auth()->user();

        $voucherMain = voucherMain::where('id', '=', $id)->first();

        if ($voucherMain->transaction_id != null || $voucherMain->transaction_id != '') {
            return response()->json(['status' => 2, 'message' => 'Transaction ID record created, not allow to delete']);
        }

        if ($voucherMain->is_recon == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already recon, not allow to delete']);
        }

        $voucherMain->status = 99;
        $voucherMain->save();

        VoucherDetails::where('voucher_main_id', $id)->update(['status' => 99]);

        VoucherControllerV2::updateTotalFigureBillTrust($voucherMain->id);

        LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', VoucherControllerV2::getVoucherTypeByID($voucherMain->voucher_type))
            ->update(['status' => 99]);

        VoucherControllerV2::createAccountLog($voucherMain, 'DeleteVoucher');

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_DISB')]))
        {
            VoucherControllerV2::revertBillCaseDetails($voucherMain->id);
        }

        if (in_array($voucherMain->voucher_type, [VoucherControllerV2::getVoucherType('BILL_RECV'), VoucherControllerV2::getVoucherType('BILL_DISB')])) {
            // $bill_receive = VoucherControllerV2::getBillReceive($voucherMain->case_bill_main_id);
            // $bill_disburse = VoucherControllerV2::getBillDisb($voucherMain->case_bill_main_id);
            // $LoanCaseBillMain = LoanCaseBillMain::where('id', $voucherMain->case_bill_main_id)->first();

            return response()->json([
                'status' => 1,
                'type' => 'bill',
                'message' => 'Record deleted',
                // 'bill' => view('dashboard.case.table.tbl-case-bill-list', compact('quotation', 'current_user', 'LoanCaseBillMain', 'blnCommPaid'))->render(),
                // 'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive', 'current_user'))->render(),
                // 'disburse' => view('dashboard.case.table.tbl-bill-disburse-list', compact('bill_disburse', 'current_user',))->render(),
                // 'billSummary' => view('dashboard.case.section.d-bill-summary-details', compact('LoanCaseBillMain'))->render(),
            ]);
        } else {
            $loan_case_trust_main_receive = CaseController::getTrustReceiveList($voucherMain->case_id);
            $case = LoanCase::where('id', $voucherMain->case_id)->first();
            $loan_case_trust_main_dis = CaseController::getTrustRequestList($voucherMain->case_id);

            return response()->json([
                'status' => 1,
                'type' => 'trust',
                'message' => 'Record deleted',
                'view' => view('dashboard.case.table.tbl-trust-recv-list', compact('loan_case_trust_main_receive'))->render(),
                'view2' => view('dashboard.case.table.tbl-trust-disb-list', compact('loan_case_trust_main_dis', 'case'))->render(),
                'summary' => view('dashboard.case.section.d-trust-summary', compact('case'))->render(),
            ]);
        }
    }

    public static function voucherProcessEngine(Request $request, $case_id, $voucher_type, $voucherList = null)
    {
        $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();

        $voucherMain = new VoucherMain();

        $current_user = auth()->user();
        $loanCase = LoanCase::where('id', '=', $case_id)->first();

        $voucherMain->user_id = $current_user->id;
        $voucherMain->case_id = $case_id;
        $voucherMain->payment_type = $request->input('payment_type');
        $voucherMain->voucher_no = $voucher_running_no;
        $voucherMain->cheque_no = $request->input('cheque_no');
        $voucherMain->credit_card_no = $request->input('credit_card_no');
        $voucherMain->bank_id = $request->input('bank_id');
        $voucherMain->case_bill_main_id = $request->input('bill_main_id');
        $voucherMain->payee = $request->input('payee');
        $voucherMain->transaction_id = $request->input('transaction_id');
        $voucherMain->office_account_id = $request->input('office_account_id');
        $voucherMain->remark = $request->input('remark');
        $voucherMain->email = $request->input('email');
        $voucherMain->created_by = $current_user->id;
        $voucherMain->bank_account = $request->input('bank_account');
        $voucherMain->adjudication_no = $request->input('adjudication_no');
        $voucherMain->lawyer_approval = 0;
        $voucherMain->voucher_type = VoucherControllerV2::getVoucherType($voucher_type);
        $voucherMain->lawyer_approval_date = date('Y-m-d H:i:s');
        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->total_amount = 0;
        $voucherMain->status = 1;
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();

        if ($voucherMain) {
            VoucherControllerV2::uploadVoucherFile($request, $voucherMain, $loanCase);
        }

        if ($voucher_type == 'BILL_DISB') {
            
            $voucherMain->lawyer_approval = 1;
            $voucherMain->save();
            if (count($voucherList) > 0) {

                $totalAmount = 0;

                for ($i = 0; $i < count($voucherList); $i++) {
                    $voucherDetails = new VoucherDetails();

                    $voucherDetails->voucher_main_id = $voucherMain->id;
                    $voucherDetails->user_id = $current_user->id;
                    $voucherDetails->case_id = $case_id;
                    $voucherDetails->account_details_id = $voucherList[$i]['account_details_id'];
                    $voucherDetails->amount = $voucherList[$i]['amount'];
                    $voucherDetails->payment_type = $request->input('payment_type');
                    $voucherDetails->voucher_no = $voucher_running_no;
                    $voucherDetails->cheque_no = $request->input('cheque_no');
                    $voucherDetails->credit_card_no = $request->input('credit_card_no');
                    $voucherDetails->bank_id = $request->input('bank_id');
                    $voucherDetails->bank_account = $request->input('bank_account');
                    $voucherDetails->status = 1;
                    $voucherDetails->created_at = date('Y-m-d H:i:s');
                    $voucherDetails->save();

                    $totalAmount += $voucherList[$i]['amount'];

                    $loanCaseAccount = LoanCaseBillDetails::where('id', '=', $voucherList[$i]['account_details_id'])->first();

                    $loanCaseAccount->amount = $loanCaseAccount->amount - $voucherList[$i]['amount'];
                    $loanCaseAccount->save();
                }

                $voucherMain->total_amount = $totalAmount;
                $voucherMain->save();

                $voucherMain->module = 'voucher';
            }
        } else if (in_array($voucher_type, ['BILL_RECV', 'TRUST_DISB', 'TRUST_RECV'])) {
            if ($voucher_type == 'TRUST_DISB') {
                if (in_array($current_user->menuroles, ['lawyer', 'account'])) {
                    $voucherMain->lawyer_approval = 1;
                    $voucherMain->lawyer_id = $current_user->id;
                    $voucherMain->lawyer_approval_date = date('Y-m-d H:i:s');
                }
            } else if (in_array($voucher_type, ['BILL_RECV', 'TRUST_RECV'])) {
                $voucherMain->lawyer_approval = 1;
                $voucherMain->lawyer_approval_date = date('Y-m-d H:i:s');
                $voucherMain->account_approval = 1;
            }

            if (in_array($voucher_type, ['TRUST_DISB', 'TRUST_RECV'])) {
                $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

                if ($LoanCaseTrustMain == null) {
                    $LoanCaseTrustMain = new LoanCaseTrustMain();
                    $LoanCaseTrustMain->case_id =  $case_id;
                    $LoanCaseTrustMain->transaction_id =  '';
                    $LoanCaseTrustMain->office_account_id =  0;
                    $LoanCaseTrustMain->status =  1;
                    $LoanCaseTrustMain->updated_by = $current_user->id;
                    $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
                    $LoanCaseTrustMain->save();
                }
            }

        

            $voucherMain->total_amount = $request->input('amount');
            $voucherMain->save();

            $voucherDetails = new VoucherDetails();

            $voucherDetails->voucher_main_id = $voucherMain->id;
            $voucherDetails->user_id = $current_user->id;
            $voucherDetails->case_id = $case_id;
            $voucherDetails->account_details_id = 0;
            $voucherDetails->amount = $request->input('amount');
            $voucherDetails->payment_type = $request->input('payment_type');
            $voucherDetails->voucher_no = $voucher_running_no;
            $voucherDetails->cheque_no = $request->input('cheque_no');
            $voucherDetails->credit_card_no = $request->input('credit_card_no');
            $voucherDetails->bank_id = $request->input('bank_id');
            $voucherDetails->bank_account = $request->input('bank_account');
            $voucherDetails->status = 1;
            $voucherDetails->created_at = date('Y-m-d H:i:s');
            $voucherDetails->save();

            $voucherMain->module = 'voucher|trust';
        }

        $desc = $current_user->name. ' request voucher ' . $voucherMain->voucher_no;

        $voucherMain->role = 'account|admin|management';
        $voucherMain->notification_desc = $desc;

        if (in_array($voucher_type, ['BILL_DISB', 'TRUST_DISB'])) {
            NotificationController::createVoucherNotification($voucherMain);
            VoucherControllerV2::createAccountLog($voucherMain, $voucher_type);
        }

        if (in_array($voucher_type, ['BILL_RECV', 'TRUST_RECV'])) {
            VoucherControllerV2::createLedgerRecord($voucherMain, $voucher_type);
            VoucherControllerV2::createAccountLog($voucherMain, $voucher_type);

            if (in_array($voucher_type, ['BILL_RECV']))
            {
                if ($request->input('payment_date')) {
                    LoanCaseBillMain::where('id', $voucherMain->case_bill_main_id)->update(['payment_receipt_date' => $request->input('payment_date')]);
                }
            }

            CaseController::adminUpdateClientLedger(LoanCase::where('id', $voucherMain->case_id)->first());
        }

        VoucherControllerV2::updateTotalFigureBillTrust($voucherMain->id);
    }

    public static function voucherStatusManagement($voucher_id)
    {
        $current_user = auth()->user();

        $voucherMain = VoucherMain::where('id', $voucher_id)->first();

        if (in_array($current_user->menuroles, ['lawyer', 'management', 'admin'])) {
            VoucherMain::where('id', 'id', $voucher_id)->update([
                'lawyer_approval' => 1,
                'lawyer_id' => $current_user->id,
                'lawyer_approval_date' => date('Y-m-d H:i:s'),
            ]);

        } else if (in_array($current_user->menuroles, ['account', 'maker']) || in_array($current_user->id, [127])) {

            if ($voucherMain->transaction_id == '' || $voucherMain->transaction_id == null || $voucherMain->office_account_id == 0) {
                return response()->json(['status' => 0, 'message' => 'Please make sure transaction ID and office account fill before approve']);
            }

            if ($voucherMain->payment_date == null) {
                return response()->json(['status' => 0, 'message' => 'Please make sure payment date updated before approve']);
            }

            VoucherMain::where('id', 'id', $voucher_id)->update([
                'account_approval' => 1,
                'approval_id' => $current_user->id,
            ]);

            NotificationController::readNotification($voucher_id);
        }

        $voucherMain->role = 'account|admin|management';
        $voucherMain->notification_desc = $current_user->name. ' approved voucher ' . $voucherMain->voucher_no;

        NotificationController::createVoucherNotification($voucherMain);
        VoucherControllerV2::createAccountLog($voucherMain, 'ApproveVoucher');

        if (in_array($current_user->menuroles, ['account', 'maker'])) {

            if ($voucherMain->voucher_type == VoucherControllerV2::getVoucherType('TRUST_DISB')) {
                VoucherControllerV2::updateTotalFigureBillTrust($voucherMain->id);
            }

            VoucherControllerV2::createLedgerRecord($voucherMain, $voucherMain->voucher_type);
            CaseController::adminUpdateClientLedger(LoanCase::where('id', $voucherMain->case_id)->first());
        }

        return response()->json(['status' => 1, 'message' => 'Voucher approved']);
    }

    public static function revertBillCaseDetails($id)
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

    }

    public static function updateTotalFigureBillTrust($id)
    {
        $voucherMain = VoucherMain::where('id', '=', $id)->first();

        if (in_array($voucherMain->voucher_type, [1, 4])) {
            $total_bill_sum = 0;
            $total_case_sum = 0;

            $total_bill_sum = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount')
                ->where('v.voucher_type', $voucherMain->voucher_type)
                ->whereNotIn('v.account_approval', [2])
                ->where('v.case_bill_main_id', '=', $voucherMain->case_bill_main_id)
                ->where('v.status', '<>', 99)
                ->where('vd.status', '<>', 99)
                ->sum('amount');

            $total_case_sum = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount')
                ->where('v.case_id', $voucherMain->case_id)
                ->where('v.voucher_type', $voucherMain->voucher_type)
                ->whereNotIn('v.account_approval', [2])
                ->where('vd.status', '<>',  99)
                ->where('v.status', '<>', 99)
                ->sum('amount');

            if ($voucherMain->voucher_type == 1) {
                LoanCaseBillMain::where('id', $voucherMain->case_bill_main_id)->update(['used_amt' => $total_bill_sum]);
                LoanCase::where('id', $voucherMain->case_id)->update(['total_bill' => $total_case_sum]);
            } else if ($voucherMain->voucher_type == 4) {
                LoanCaseBillMain::where('id', $voucherMain->case_bill_main_id)->update(['collected_amt' => $total_bill_sum]);
                LoanCase::where('id', $voucherMain->case_id)->update(['collected_bill' => $total_case_sum]);
            }
        } else {
            $total_sum = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount')
                ->where('v.case_id', '=', $voucherMain->case_id)
                ->where('v.voucher_type', '=', $voucherMain->voucher_type)
                ->whereNotIn('v.account_approval', [2])
                ->where('v.status', '<>', 99)
                ->sum('vd.amount');


            if ($voucherMain->voucher_type == 2) {
                LoanCaseTrustMain::where('case_id', $voucherMain->case_id)->update(['total_used' => $total_sum]);
                LoanCase::where('id', $voucherMain->case_id)->update(['total_trust' => $total_sum]);
            } else if ($voucherMain->voucher_type == 3) {
                LoanCaseTrustMain::where('case_id', $voucherMain->case_id)->update(['total_received' => $total_sum]);
                LoanCase::where('id', $voucherMain->case_id)->update(['collected_trust' => $total_sum]);
            }
        }
    }

    public static function checkBillAvailableAmount($id, VoucherMain $VoucherMain)
    {
        if ($VoucherMain->voucher_type == 1) {
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
        } else if ($VoucherMain->voucher_type == 2) {
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

    public static function uploadVoucherFile(Request $request, $voucherMain, $loanCase)
    {
        $status = 1;
        $data = '';

        $file = $request->file('attachment_file');

        if ($file != null) {
            $filename = time() . '_' . $file->getClientOriginalName();

            $current_user = auth()->user();

            // File extension
            $extension = $file->getClientOriginalExtension();
            $case_id =  $loanCase->id;
            $remarks =  $request->input('remark');

            // File upload location
            $location = 'cases/' . $loanCase->id . '/voucher';

            $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

            $filename = time() . '_' . $res;
            $current_user = auth()->user();

            $isImage =  ImageController::verifyImage($file);

            $disk = Storage::disk('Wasabi');
            $s3_file_name = '';


            if ($isImage == true) {
                $s3_file_name = ImageController::resizeImg($file, $location, $filename);
            } else {
                $s3_file_name =  $disk->put($location, $file);
            }

            $LoanCaseAccountFiles = new LoanCaseAccountFiles();

            $LoanCaseAccountFiles->main_id =  $voucherMain->id;
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
        }
    }

    public static function createLedgerRecord($obj, $voucher_type)
    {
        $current_user = auth()->user();
        $LedgerEntries = LedgerEntriesV2::where('key_id', $obj->id)->where('status', 1)->where('type', '=', $voucher_type)->first();

        if ($LedgerEntries == null) {
            $LedgerEntries = new LedgerEntriesV2();
        }

        $voucher_item = '';

        if(VoucherControllerV2::getVoucherType('BILL_DISB') == $voucher_type)
        {
            $VoucherDetails = DB::table('voucher_details as a')
            ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
            ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
            ->select('a.*', 'c.name as account_name')
            ->where('voucher_main_id', '=', $obj->id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
                }
            }
        }

        $LedgerEntries->transaction_id = $obj->transaction_id;
        $LedgerEntries->case_id = $obj->case_id;
        $LedgerEntries->loan_case_main_bill_id = $obj->case_bill_main_id;
        $LedgerEntries->cheque_no = $obj->voucher_no;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $obj->id;
        $LedgerEntries->transaction_type = VoucherControllerV2::getTransactionType($voucher_type);
        $LedgerEntries->amount = $obj->total_amount;
        $LedgerEntries->bank_id = $obj->office_account_id;
        $LedgerEntries->remark = $obj->remark;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->payee  = $obj->payee;
        $LedgerEntries->status = 1;
        $LedgerEntries->desc_1 = $voucher_item;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $obj->payment_date;
        $LedgerEntries->type = $voucher_type;
        $LedgerEntries->save();
    }

    public static function createAccountLog($obj, $voucher_type)
    {
        $action = '';
        $log_desc = '';
        $ori_amount = 0;
        $new_amt = 0;

        $current_user = auth()->user();

        if (in_array($voucher_type, ['BILL_DISB'])) {
            $action = 'RequestBill';
            $log_desc = $current_user->name . ' requested bill (' . $obj->voucher_no . ') value:' . $obj->total_amount . ' ';
            $ori_amount = $obj->total_amount;
        }else if (in_array($voucher_type, ['BILL_RECV'])) {
            $action = 'ReceiveBill';
            $log_desc = $current_user->name . ' received bill (' . $obj->voucher_no . ') value:' . $obj->total_amount . ' ';
            $ori_amount = $obj->total_amount;
        } else if (in_array($voucher_type, ['TRUST_DISB'])) {
            $action = 'RequestTrust';
            $log_desc = $current_user->name . ' requested trust (' . $obj->voucher_no . ') value:' . $obj->total_amount . ' ';
            $ori_amount = $obj->total_amount;
        }else if (in_array($voucher_type, ['TRUST_RECV'])) {
            $action = 'ReceieveTrust';
            $log_desc = $current_user->name . ' received trust (' . $obj->voucher_no . ') value:' . $obj->total_amount . ' ';
            $ori_amount = $obj->total_amount;
        } else if (in_array($voucher_type, ['DeleteVoucher'])) {
            $action = $voucher_type;
            $log_desc = $current_user->name . ' deleted ' . VoucherControllerV2::getVoucherTypeByID($obj->voucher_type) . ' (' . $obj->voucher_no . ')';
        }else if (in_array($voucher_type, ['UpdateVoucher'])) {
            $action = $voucher_type;
            $ori_amount = $obj->ori_amt;
            $new_amt = $obj->total_amount;
            $log_desc = $current_user->name . ' updated ' . VoucherControllerV2::getVoucherTypeByID($obj->voucher_type) . ' (' . $obj->voucher_no . ')';
        }else if (in_array($voucher_type, ['ApproveVoucher'])) {
            $action = $voucher_type;
            $log_desc =  $current_user->name . ' approved voucher (' . $obj->voucher_no . ')';
        }

        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $obj->case_id;
        $AccountLog->bill_id = $obj->case_bill_main_id;
        $AccountLog->ori_amt = $ori_amount;
        $AccountLog->new_amt = $new_amt;
        $AccountLog->object_id = $obj->id;
        $AccountLog->action = $action;
        $AccountLog->desc = $log_desc;
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();
    }

    public static function getBillReceive($billMainId)
    {
        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->select('vd.*', 'a.name as account_name', 'u.name as requestor', 'vm.id as voucher_id', 'vm.transaction_id as trx_id', 'vm.voucher_no', 'b.short_code as bank_short_code', 'vm.payee', 'vm.remark as remark', 'b.name as client_bank_name', 'vm.payment_date as payment_date')
            ->where('vm.case_bill_main_id', $billMainId)
            ->where('vm.voucher_type', VoucherControllerV2::getVoucherType('BILL_RECV'))
            ->where('vm.status', '<>',  99)
            ->get();

        return $bill_receive;
    }

    public static function getBillDisb($billMainId)
    {
        // $bill_receive = DB::table('voucher_details AS vd')
        //     ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
        //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
        //     ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
        //     ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
        //     ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
        //     ->select('vd.*', 'a.name as account_name', 'u.name as requestor', 'vm.id as voucher_id', 'vm.transaction_id as trx_id', 'vm.voucher_no', 'b.short_code as bank_short_code', 'vm.payee', 'vm.remark as remark', 'b.name as client_bank_name', 'vm.payment_date as payment_date')
        //     ->where('vm.case_bill_main_id', $billMainId)
        //     ->where('vm.voucher_type', VoucherControllerV2::getVoucherType('BILL_DISB'))
        //     ->where('vm.status', '<>',  99)
        //     ->get();

            $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select(
                'vd.*',
                'a.name as account_name',
                'vm.id as voucher_id',
                'vm.voucher_no',
                'b.name as client_bank_name',
                'b.short_code as bank_short_code',
                'vm.lawyer_approval as lawyer_approval',
                'vm.account_approval as account_approval',
                'vm.remark as remark',
                'vm.payment_date',
                'vm.transaction_id as transaction_id',
                'u.name as requestor'
            )
            ->where('bd.loan_case_main_bill_id', '=',  $billMainId)
            ->where('vm.voucher_type',  VoucherControllerV2::getVoucherType('BILL_DISB'))
            ->where('vd.status', '<>',  99)
            ->where('vm.status', '<>',  99)
            ->orderBy('vm.created_at', 'desc')
            ->get();

        return $bill_disburse;
    }
}
