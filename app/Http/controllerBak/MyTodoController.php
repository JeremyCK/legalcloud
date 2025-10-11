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
use App\Models\CaseTodo;
use App\Models\VoucherDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MyTodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mytodo = DB::table('case_todo AS c')
        ->leftJoin('voucher_main as v', 'v.id', '=', 'c.ref_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'c.case_id')
        ->leftJoin('users as u', 'u.id', '=', 'c.request_user_id')
        ->select(DB::raw('c.*, "Voucher" AS type_name, l.case_ref_no, u.name as user '))
        ->where('type', '=', 1)
        ->get()->toArray();

        $mytodo2 = DB::table('case_todo AS c')
        ->leftJoin('voucher_main as v', 'v.id', '=', 'c.ref_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'c.case_id')
        ->leftJoin('users as u', 'u.id', '=', 'c.request_user_id')
        ->select(DB::raw('c.*, "test" AS type_name, l.case_ref_no, u.name as user '))
        ->where('type', '=', 2)
        ->get()->toArray();

        $data = array_merge($mytodo, $mytodo2);

        $pendingCount = DB::table('case_todo')->where('status', '=', 0)->count();
        $approveCount = DB::table('case_todo')->where('status', '=', 1)->count();
        $rejectedCount = DB::table('case_todo')->where('status', '=', 2)->count();

        $mytodo = DB::table('voucher_details AS d')
        ->leftJoin('voucher_main as v', 'v.id', '=', 'd.voucher_main_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'v.case_id')
        ->leftJoin('account as a', 'a.id', '=', 'd.account_details_id')
        ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
        ->leftJoin('banks as b', 'b.id', '=', 'd.bank_id')
        ->join('parameter as p', 'p.parameter_value_3', '=', 'd.payment_type')
        ->select(DB::raw('d.*, "Voucher" AS type_name, l.case_ref_no, u.name as user , a.name as account_name,p.parameter_value_2 AS payment_type_name,b.name AS bank_name'))
        // ->where('type', '=', 1)
        ->get()->toArray();

        // return $mytodo;

        $parameter_controller = new ParameterController;
        $parameters = $parameter_controller->getParameter('payment_type');


        return view('dashboard.mytodo.index', ['mytodo' => $mytodo,
        'pendingCount' => $pendingCount,
        'approveCount' => $approveCount,
        'parameters' => $parameters,
        'rejectedCount' => $rejectedCount]);
    }

    public function updateBillTransction(Request $request)
    {
        $status = 1;
        $message = 'Bill updated';
        $billList = [];

        try {
           

        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }


        if (count($billList) > 0) {
            for ($i = 0; $i < count($billList); $i++) {
                $voucherDetails = VoucherDetails::where('id', '=', $billList[$i]['id'])->first();

                $voucherDetails->transaction_id = $billList[$i]['transaction_id'];
                $voucherDetails->save();
            }
        }

        return response()->json(['status' => $status, 'data' => $message]);
   
    }

    public function filterTask($id)
    {


        $mytodo = DB::table('voucher_details AS d')
        ->leftJoin('voucher_main as v', 'v.id', '=', 'd.voucher_main_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'v.case_id')
        ->leftJoin('account as a', 'a.id', '=', 'd.account_details_id')
        ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
        ->leftJoin('banks as b', 'b.id', '=', 'd.bank_id')
        ->join('parameter as p', 'p.parameter_value_3', '=', 'd.payment_type')
        ->select(DB::raw('d.*, "Voucher" AS type_name, l.case_ref_no, u.name as user , a.name as account_name,p.parameter_value_2 AS payment_type_name,b.name AS bank_name'))
        ->where('d.id', '=', $id)
        ->get()->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account = Users::where('menuroles', '=', 'account')->get();
        $lawyer = Users::where('menuroles', '=', 'lawyer')->get();
        $sales = Users::where('menuroles', '=', 'sales')->get();
        $clerk = Users::where('menuroles', '=', 'clerk')->get();
        $banks = Banks::where('status', '=', 1)->get();

        return view('dashboard.banks.create', ['banks' => $banks,
                                                'lawyers' => $lawyer, 
                                                'sales' => $sales, 
                                                'accounts' => $account, 
                                                'clerks' => $clerk]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $banks = new Banks();

        $banks->name = $request->input('name');
        $banks->short_code = $request->input('short_code');
        $banks->tel_no = $request->input('tel_no');
        $banks->fax = $request->input('fax');
        $banks->address = $request->input('address');
        $banks->status = $request->input('status');
        $banks->created_at = now();

        $banks->save();

        if($banks)
        {
            if (!empty($request->input('assignTo')))
            {
                $staffList =$request->input('assignTo');
    
                for($i = 0; $i < count($staffList); $i++){
                    
                    $banksUsersRel = new BanksUsersRel();

                    $banksUsersRel->bank_id = $banks->id;
                    $banksUsersRel->user_id = $staffList[$i];
                    $banksUsersRel->status = 1;
                    $banksUsersRel->created_at = now();

                    $banksUsersRel->save();

                }
            }
        }

        $request->session()->flash('message', 'Successfully created new Bank');

        return view('dashboard.banks.index', ['banks' => $banks]);
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

        $mytodo = CaseTodo::where('id', '=', $id)->get();

        $mytodo = DB::table('case_todo as td')
        ->leftJoin('loan_case as l', 'l.id', '=', 'td.case_id')
        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
        ->select('td.*', 'l.case_ref_no', 'c.*')
        ->where('td.id', '=', $id)
        ->get();

        // $voucher_main = DB::table('voucher_main as vm')
        // ->join('loan_case_account as lc', 'lc.id', '=', 'vd.account_details_id')
        // ->select('vm.*', 'lc.item_name')
        // // ->where('vm.voucher_main_id', '=', $mytodo[0]->ref_id)
        // ->get();

        $voucher_details = DB::table('voucher_details as vd')
        ->join('loan_case_account as lc', 'lc.id', '=', 'vd.account_details_id')
        ->select('vd.*', 'lc.item_name')
        ->where('vd.voucher_main_id', '=', $mytodo[0]->ref_id)
        ->get();


        return view('dashboard.mytodo.edit', ['mytodo' => $mytodo[0], 'voucher_details' => $voucher_details]);
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

        $banks = Banks::where('id', '=', $id)->first();

        $banks->name = $request->input('name');
        $banks->short_code = $request->input('short_code');
        $banks->tel_no = $request->input('tel_no');
        $banks->fax = $request->input('fax');
        $banks->address = $request->input('address');
        $banks->status = $request->input('status');
        $banks->created_at = now();

        $banks->save();


        if($banks)
        {
            $banksUsersRel = BanksUsersRel::where('bank_id', '=',$id);
            $banksUsersRel->delete();

            if (!empty($request->input('assignTo')))
            {
                $staffList =$request->input('assignTo');
    
                for($i = 0; $i < count($staffList); $i++){
                    
                    $banksUsersRel = new BanksUsersRel();

                    $banksUsersRel->bank_id = $banks->id;
                    $banksUsersRel->user_id = $staffList[$i];
                    $banksUsersRel->status = 1;
                    $banksUsersRel->created_at = now();

                    $banksUsersRel->save();

                }
            }
        }

        $request->session()->flash('message', 'Successfully updated bank info');

        $banks = Banks::all();

        return view('dashboard.banks.index', ['banks' => $banks]);
    }

    public function updateMyTodoStatus(Request $request, $id)
    {
        $status = 1;
        $caseTodo = CaseTodo::where('id', '=', $id)->first();

        $caseTodo->status = $request->input('status');

        return response()->json(['status' => $status]);
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
