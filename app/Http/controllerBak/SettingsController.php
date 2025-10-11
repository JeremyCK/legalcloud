<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountCode;
use App\Models\AccountItem;
use App\Models\Adjudication;
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
use App\Models\FiscalYears;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\Parameter;
use App\Models\Referral;
use App\Models\SafeKeeping;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class SettingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();
        $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
        $exemptedCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 2)->count();
        $pendingCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 0)->count();

        $branch = DB::table('branch')->where('status', '=', 1)->get();


        return view('dashboard.safe-keeping.index', [
            'paidCount' => $paidCount,
            'exemptedCount' => $exemptedCount,
            'pendingCount' => $pendingCount,
            'branches' => $branch,
            'current_user' => $current_user
        ]);
    }

    public function referralCommList()
    {
        $current_user = auth()->user();

        if ($current_user->menurole <> 'admin' && $current_user->menurole <> 'management') {
            return redirect()->route('dashboard.index');
        }

        $ReferralFormula = DB::table('referral_formula')->where('status', '=', 1)->get();

        $branch = DB::table('branch')->where('status', '=', 1)->get();


        return view('dashboard.settings.referral_comm.index', [
            'ReferralFormula' => $ReferralFormula,
            'branches' => $branch,
            'current_user' => $current_user
        ]);
    }

    public function referralCommEdit($id)
    {
        $current_user = auth()->user();
        $ReferralFormula = DB::table('referral_formula')->where('id', '=', $id)->first();

        $branch = DB::table('branch')->where('status', '=', 1)->get();


        $referral = DB::table('referral')->where('referral_formula_id', '=', $id)->get();
        $referral_list = DB::table('referral')->where('status', '=', $id)->get();

        return view('dashboard.settings.referral_comm.edit', [
            'referral' => $referral,
            'branches' => $branch,
            'ReferralFormula' => $ReferralFormula,
            'referral_list' => $referral_list,
            'current_user' => $current_user
        ]);
    }

    public function getFormulaReferralList(Request $request, $id)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $referral_list = DB::table('referral')->where('referral_formula_id', '=', $id)->orderBy('name', 'ASC')->get();

            return DataTables::of($referral_list)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="javascript:void(0)" onclick="removeReferralFromCommGroup('.$row->id.');" class="btn btn-danger shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="remove"><i class="cil-x"></i>Remove from this group</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getReferralList(Request $request, $id)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $referral_list = DB::table('referral')->where('referral_formula_id', '<>', $id)->get();

            $referral_list = DB::table('referral as r')
            ->leftJoin('referral_formula as f', 'f.id', '=', 'r.referral_formula_id')
            ->select('r.*', 'f.formula')
            ->where('referral_formula_id', '<>', $id)->orderBy('name', 'ASC')->get();

            // $referral_list = $referral_list->orderBy('created_at', 'DESC')->get();

            return DataTables::of($referral_list)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="referral" value="' . $row->id . '" id="chk_' . $row->id . '" >
                    <label for="chk_' . $row->id . '"></label>
                    </div> ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function saveReferralIntoCommGroup(Request $request, $id)
    {
        $referral_id_list = [];

        $current_user = auth()->user();

        if ($request->input('referral_id_list') != null) {
            $referral_id_list = json_decode($request->input('referral_id_list'), true);
        }

        if (count($referral_id_list) > 0) {
            $referral = Referral::whereIn('id', $referral_id_list)->get();

            if (count($referral) > 0) {
                for ($i = 0; $i < count($referral); $i++) {
                    $referral[$i]->referral_formula_id = $id;
                    $referral[$i]->save();
                }
            }
        }


        return response()->json(['status' => 1, 'message' => 'saved']);
    }

    public function removeReferralFromCommGroup(Request $request)
    {
        $referral_id_list = [];

        $current_user = auth()->user();

        if ($request->input('referral_id_list') != null) {
            $referral_id_list = json_decode($request->input('referral_id_list'), true);
        }

        if (count($referral_id_list) > 0) {
            $referral = Referral::whereIn('id', $referral_id_list)->get();

            if (count($referral) > 0) {
                for ($i = 0; $i < count($referral); $i++) {
                    $referral[$i]->referral_formula_id = 0;
                    $referral[$i]->save();
                }
            }
        }


        return response()->json(['status' => 1, 'message' => 'deleted']);
    }

    public function letterHeadList()
    { 
        $current_user = auth()->user();
        
        $letterHeadLawyer = $this->loadLetterHead();

        $lawyer = User::where('is_lawyer', 1)->orderBy('name', 'asc')->get();

        return view('dashboard.settings.letter-head.index', [
            'letterHeadLawyer' => $letterHeadLawyer,
            'lawyers' => $lawyer,
        ]);
    }

    public function letterHeadLawyerList()
    { 
        $current_user = auth()->user();
        
        $letterHeadLawyer = $this->loadLetterHead();

        $lawyer = User::where('is_lawyer', 1)->where('status', 1)->orderBy('name', 'asc')->get();

        $branchInfo = BranchController::manageBranchAccess();
        $branchList = $branchInfo['branch'];

        $lawyer_ic_short_code = DB::table('parameter')->where('parameter_type', 'lawyer_ic_short_code')->first();

        return view('dashboard.settings.letter-head-lawyer.index', [
            'letterHeadLawyer' => $letterHeadLawyer,
            'lawyers' => $lawyer,
            'branchList' => $branchList,
            'current_user' => $current_user,
            'lawyer_ic_short_code' => $lawyer_ic_short_code,
        ]);
    }

    public function loadLetterHead()
    {
        $letterHeadLawyer = DB::table('parameter')->where('parameter_type', 'letter_head_lawyer')->get();

        return $letterHeadLawyer;
    }

    public function SaveLetterHead(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'ic_no'             => 'required'
        ]);

        $current_user = auth()->user();

        $Parameter = new Parameter();

        $Parameter->parameter_type = 'letter_head_lawyer';
        $Parameter->parameter_value_1 = $request->input('name');
        $Parameter->parameter_value_2 = $request->input('ic_no');
        $Parameter->created_by = $current_user->id;

        $Parameter->save();

        $letterHeadLawyer = $this->loadLetterHead();
        
        return response()->json(['status' => 1, 
        'message' => 'saved',
        'table' => view('dashboard.settings.letter-head.table.tbl-letterhead', compact('letterHeadLawyer'))->render(),
        'data' => $letterHeadLawyer]);
    }

    public function updateLetterHead(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'ic_no'             => 'required'
        ]);

        $current_user = auth()->user();

        $Parameter = Parameter::where('parameter_type','letter_head_lawyer')
        ->where('id',$request->input('id'))
        ->update(['parameter_value_1'=>$request->input('name'), 'parameter_value_2'=>$request->input('ic_no')]);

        // $Parameter->parameter_type = 'letter_head_lawyer';
        // $Parameter->parameter_value_1 = $request->input('name');
        // $Parameter->parameter_value_2 = $request->input('ic_no');
        // $Parameter->created_by = $current_user->id;

        // $Parameter->save();
        
        return response()->json(['status' => 1, 'message' => 'saved']);
    }

    public function deleteLetterHead($id)
    {
        $current_user = auth()->user();

        Parameter::where('parameter_type','letter_head_lawyer')->where('id',$id)->delete();

        // $Parameter->parameter_type = 'letter_head_lawyer';
        // $Parameter->parameter_value_1 = $request->input('name');
        // $Parameter->parameter_value_2 = $request->input('ic_no');
        // $Parameter->created_by = $current_user->id;

        // $Parameter->save();
        
        return response()->json(['status' => 1, 'message' => 'Deleted']);
    }

    public function AccountCodeSetting()
    {
        
        $AccountCode = AccountCode::where('status', 1)->get();

        return view('dashboard.settings.account-code.index', [
            'AccountCode' => $AccountCode
        ]);
    }

    public static function getFiscalYear()
    {
        $FiscalYears = FiscalYears::where('status', 1)->get();

        return $FiscalYears;
    }


}
