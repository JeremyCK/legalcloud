<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\BanksUsersRel;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\caseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\AccountCode;
use App\Models\Branch;
use App\Models\OfficeBankAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class OfficeBankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();

        if($current_user->branch_id == 3)
        {
            $banks = DB::table('office_bank_account AS a')
            ->leftJoin('account_code AS b', 'a.account_code', '=', 'b.id')
            ->select('a.*', 'b.name as account_code_label')
            ->where('a.status', '=',  1)
            ->where('a.branch_id', '=',  3)
            ->get();
        }
        else if($current_user->branch_id == 5)
        {
            $banks = DB::table('office_bank_account AS a')
            ->leftJoin('account_code AS b', 'a.account_code', '=', 'b.id')
            ->select('a.*', 'b.name as account_code_label')
            ->where('a.status', '=',  1)
            ->where('a.branch_id', '=',  5)
            ->get();
        }
        else
        {
            $banks = DB::table('office_bank_account AS a')
            ->leftJoin('account_code AS b', 'a.account_code', '=', 'b.id')
            ->select('a.*', 'b.name as account_code_label')
            // ->where('a.status', '=',  1)
            ->get();
        }

        

        return view('dashboard.officeBankAccount.index', ['banks' => $banks]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $current_user = auth()->user();
        $banks = Banks::where('status', '=', 1)->get();
        $branch = Branch::where('status', '=', 1)->get();
        $AccountCode = AccountCode::where('status', '=', 1)->get();

        return view('dashboard.officeBankAccount.create', ['banks' => $banks,'AccountCode' => $AccountCode,'branchs' => $branch,'current_user' => $current_user]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $current_user = auth()->user();
        $branch_id = 0;

        if($current_user->branch_id == 3)
        {
            $branch_id = 3;
        }

        $OfficeBankAccount = new OfficeBankAccount();

        $OfficeBankAccount->name = $request->input('name');
        $OfficeBankAccount->short_code = $request->input('short_code');
        $OfficeBankAccount->tel_no = $request->input('tel_no');
        $OfficeBankAccount->account_no = $request->input('account_no');
        $OfficeBankAccount->opening_balance = $request->input('opening_balance');
        $OfficeBankAccount->opening_bal_date = $request->input('opening_bal_date');
        $OfficeBankAccount->account_code = $request->input('account_code');
        $OfficeBankAccount->branch_id = $branch_id;
        $OfficeBankAccount->remark = $request->input('remark');
        $OfficeBankAccount->status = $request->input('status');
        $OfficeBankAccount->created_at = now();

        $OfficeBankAccount->save();


        $request->session()->flash('message', 'Successfully created new Bank');

        return redirect('office-bank-account');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $current_user = auth()->user();


        $loanCase = LoanCase::where('id', '=', $id)->get();
        $loanCaseDetails = LoanCaseDetails::where('case_id', '=', $id)->get();
        $loanCaseDetailsCount = LoanCaseDetails::where('case_id', '=', $id)->where('check_point', '>', 0)->get();

        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();
        

        $lawyer = Users::where('id', '=', $loanCase[0]->lawyer_id)->get();
        $clerk = Users::where('id', '=', $loanCase[0]->clerk_id)->get();
        $sales = Users::where('id', '=', $loanCase[0]->sales_user_id)->get();
        $caseTemplate = caseTemplate::all();

        $loanCase[0]->lawyer = $lawyer[0]->name;
        $loanCase[0]->clerk = $clerk[0]->name;
        $loanCase[0]->sales = $sales[0]->name;

        if (count($loanCase))
        {

        }
        // return $loanCaseDetails;


        return view('dashboard.todolist.show', ['cases' => $loanCase, 
                                                'cases_details' => $loanCaseDetails, 
                                                'caseTemplate'=> $caseTemplate, 
                                                'current_user'=> $current_user,
                                                'caseMasterListCategory'=> $caseMasterListCategory,
                                                'caseMasterListField'=> $caseMasterListField,
                                                'loanCaseDetailsCount' => $loanCaseDetailsCount]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $current_user = auth()->user();
        $banks = OfficeBankAccount::where('id', '=', $id)->first();
        $AccountCode = AccountCode::where('status', '=', 1)->get();
        $branch = Branch::where('status', '=', 1)->get();


        return view('dashboard.officeBankAccount.edit', ['banks' => $banks,'AccountCode' => $AccountCode,'branchs' => $branch,'current_user' => $current_user]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        // $menuLangList = MenuLangList::where('id', '=', $request->input('id'))->first();
        // $menuLangList->name = $request->input('name');
        // $menuLangList->short_name = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLangList->save();
        // $request->session()->flash('message', 'Successfully updated language');
        // return redirect()->route('todolist.edit', [$request->input('id')]); 

        $banks = OfficeBankAccount::where('id', '=', $id)->first();

        $banks->name = $request->input('name');
        $banks->short_code = $request->input('short_code');
        $banks->account_code = $request->input('account_code');
        $banks->opening_balance = $request->input('opening_balance');
        $banks->opening_bal_date = $request->input('opening_bal_date');
        $banks->tel_no = $request->input('tel_no');
        $banks->account_no = $request->input('account_no');
        $banks->remark = $request->input('remark');
        $banks->status = $request->input('status');
        $banks->created_at = now();

        $banks->save();



        $request->session()->flash('message', 'Successfully updated bank info');

        return redirect('office-bank-account');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $menu = MenuLangList::where('id', '=', $id)->first();
        $menusLang = MenusLang::where('lang', '=', $menu->short_name)->first();
        if (!empty($menusLang)) {
            $request->session()->flash('message', "Can't delete. Language has one or more assigned tranlsation of menu element");
            $request->session()->flash('back', 'todolist.index');
            return view('dashboard.shared.universal-info');
        } else {
            $menus = MenuLangList::all();
            if (count($menus) <= 1) {
                $request->session()->flash('message', "Can't delete. This is last language on the list");
                $request->session()->flash('back', 'todolist.index');
                return view('dashboard.shared.universal-info');
            } else {
                if ($menu->is_default == true) {
                    $request->session()->flash('message', "Can't delete. This is default language");
                    $request->session()->flash('back', 'todolist.index');
                    return view('dashboard.shared.universal-info');
                } else {
                    $menu->delete();
                    $request->session()->flash('message', 'Successfully deleted language');
                    $request->session()->flash('back', 'todolist.index');
                    return view('dashboard.shared.universal-info');
                }
            }
        }
    }
}
