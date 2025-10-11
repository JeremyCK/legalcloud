<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\caseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\LoanCaseNotes;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allowCreateCase = "false";
        $current_user = auth()->user();

        $userRoles = Auth::user()->getRoleNames();

        // return $current_user;

        $role = array('sales', 'admin');

        
        $allowCreateCase = Helper::getRolePermission($userRoles, $role);

        // return $current_user->menuroles;

        if($current_user->menuroles == 'sales')
        {
            $case = TodoList::where('sales_user_id', '=', $current_user->id)->get();
        }
        if($current_user->menuroles == 'lawyer')
        {
            $case = TodoList::where('lawyer_id', '=', $current_user->id)->get();
        }

        if($current_user->menuroles == 'admin')
        {
            // $case = TodoList::all();

            $case = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->select(array('loan_case.*', 'case_type.name AS type_name'))
                ->get();
        }

        // $case = TodoList::where('lawyer_id', '=', 3)->get();


        // if (in_array("admin", $userRoles)) {
        //     $allowCreateCase = "true";
        // }

        // if (property_exists($userRoles,"admin"))
        // {
        //     $allowCreateCase = "true";
        // }

        // return $allowCreateCase;


        // $user = DB::table('users')
        // ->leftJoin('loan_case', 'users.id', '=', 'loan_case.sales_id')
        // ->select(array('users.*', DB::raw('COUNT(loan_case.'.$role.'_id) as task_count')))
        // ->where('menuroles', 'like', '%'.$role.'%')
        // ->groupBy('users.id')
        // ->orderBy('task_count')
        // ->get();

        return view('dashboard.todolist.index', ['cases' => $case, 'allowCreateCase' => $allowCreateCase]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lawyer = Users::where('id', '=', 7)->get();
        $sales = Users::where('id', '=', 6)->get();
        $banks = Banks::where('status', '=', 1)->get();

        return view('dashboard.todolist.create', ['templates' => CaseTemplate::all(), 'lawyers' => $lawyer, 'sales' => $sales, 'banks' => $banks]);
    }

    public function assignTask($role)
    {
        $result = [];

        //in future maybe have to take staff leave status as consideration, currently based on least task and by sorting
        $user = DB::table('users')
        ->leftJoin('loan_case', 'users.id', '=', 'loan_case.'.$role.'_id')
        ->select(array('users.*', DB::raw('COUNT(loan_case.'.$role.'_id) as task_count')))
        ->where('menuroles', 'like', '%'.$role.'%')
        ->groupBy('users.id')
        ->orderBy('task_count')
        ->get();

        if (count($user))
        {
            $result[0] = $user[0];
        }

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function masterlist()
    {
        $lawyer = Users::where('id', '=', 7)->get();
        $sales = Users::where('id', '=', 6)->get();
        $banks = Banks::where('status', '=', 1)->get();

        // $query = DB::table('users')
        // ->leftJoin('loan_case', 'users.id', '=', 'loan_case.lawyer_id')
        // ->select(array('users.*', DB::raw('COUNT(loan_case.lawyer_id) as followers')))
        // ->groupBy('users.id')
        // ->orderByDesc('followers')
        // ->get();



        return view('dashboard.todolist.masterlist', ['templates' => CaseTemplate::all(), 'lawyers' => $lawyer, 'sales' => $sales, 'banks' => $banks]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_name =  $request->input('client_name');
        $bank_id =  $request->input('bank');
        $customer = new Customer();

        $current_user = auth()->user();

        $client_short_code = Helper::generateNickName($client_name);


        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';
        $lawyer = $this->assignTask('lawyer');
        $clerk = $this->assignTask('clerk');

        $bank = Banks::where('id', '=', $bank_id)->get();
        $running_no = Parameter::where('parameter_type', '=', 'case_running_no')->get();

        $current_user = auth()->user();

        $case_ref_no = str_replace("[sales]",$current_user->nick_name,$case_ref_no);
        $case_ref_no = str_replace("[bank]",$bank[0]->short_code,$case_ref_no);
        $case_ref_no = str_replace("[running_no]",$running_no[0]->parameter_value_1,$case_ref_no);
        $case_ref_no = str_replace("[client]",$client_short_code,$case_ref_no);

        if (count($lawyer))
        {
            $case_ref_no = str_replace("[lawyer]",$lawyer[0]->nick_name,$case_ref_no);
        }

        if (count($clerk))
        {
            $case_ref_no = str_replace("[clerk]",$clerk[0]->nick_name,$case_ref_no);
        }

        $loanCase = new TodoList();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = $request->input('property_address');
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->remark = $request->input('remark');
        $loanCase->sales_user_id = $current_user->id;
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer[0]->id;
        $loanCase->clerk_id = $clerk[0]->id;
        $loanCase->status = "2";
        $loanCase->created_at = now();


        $loanCase->save();

        if($loanCase)
        {
            $customer = $this->createCustomer($request, $case_ref_no);
        }

        if($customer)
        {
            $customer = $this->createCustomer($request, $case_ref_no);
        }
        else
        {

        }

        $request->session()->flash('message', 'Successfully created new case');

        return redirect()->route('todolist.index', ['cases' => TodoList::all()]);

                // return view('dashboard.form.create');

        // return $current_user->nick_name;
        // $nickName  = Helper::generateNickName($name);

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
        // return redirect()->route('todolist.index', ['cases' => TodoList::all()]);
    }

    public function createCustomer($request, $case_ref_no)
    {
        $customer = new Customer();

        $customer->case_ref_no = $case_ref_no;
        $customer->name = $request->input('client_name');
        $customer->phone_no = $request->input('client_phone_no');
        $customer->status = "1";
        $customer->created_at = now();
        // $customer->name = $request->input('name');
        // $customer->name = $request->input('name');
        // $customer->name = $request->input('name');

        $customer->save();

        return $customer;

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
        $loanCaseNotes = LoanCaseNotes::where('case_id', '=', $id)->get();
        

        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $now = time(); // or your date as well
        $your_date = strtotime($loanCase[0]->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

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
                                                'cases_notes' => $loanCaseNotes, 
                                                'caseTemplate'=> $caseTemplate, 
                                                'current_user'=> $current_user,
                                                'caseMasterListCategory'=> $caseMasterListCategory,
                                                'caseMasterListField'=> $caseMasterListField,
                                                'datediff'=> $datediff,
                                                'loanCaseDetailsCount' => $loanCaseDetailsCount]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('dashboard.todolist.edit', [
            'lang' => MenuLangList::where('id', '=', $id)->first()
        ]);
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accept($id)
    {


        $loanCase = LoanCase::where('id', '=', $id)->get();
        $loanCaseDetails = LoanCaseDetails::where('case_id', '=', $id)->get();
        

        $lawyer = Users::where('id', '=', $loanCase[0]->lawyer_id)->get();
        $clerk = Users::where('id', '=', $loanCase[0]->clerk_id)->get();
        $caseTemplate = caseTemplate::all();

        $loanCase[0]->lawyer = $lawyer[0]->name;
        $loanCase[0]->clerk = $clerk[0]->name;

        if (count($loanCase))
        {

        }
        // return $loanCaseDetails;


        return view('dashboard.todolist.show', ['cases' => $loanCase, 'cases_details' => $loanCaseDetails, 'caseTemplate'=> $caseTemplate]);
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
