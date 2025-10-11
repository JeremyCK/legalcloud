<?php

namespace App\Http\Controllers;

use App\Http\Helper\Helper;
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
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClientsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.clients.index');
    }

    public function getClientList()
    {
        $current_user = auth()->user();
        $customer_id_list = [];
        $Customer = DB::table('client as c')
            // ->leftJoin('loan_case as l', 'l.customer_id', '=', 'c.id')
            ->select('c.*');

        // $voucher_list = $voucher_list->where(function ($q) use ($userList, $accessInfo) {
        //     $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
        //     ->whereIn('l.lawyer_id', $userList)
        //         ->orWhereIn('l.clerk_id', $userList);
                
        // });

        if (!in_array($current_user->menuroles,  ['admin', 'management']))
        {

            $ids = CaseController::getCaseListHub('array','customer_id');
            $customer_id_list = array_merge($customer_id_list,$ids);

            $Customer = $Customer->whereIn('id', $customer_id_list);  
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
        

        $Customer = $Customer->get();

        if (count($Customer) > 0) {
            for ($i = 0; $i < count($Customer); $i++) {
                $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('customer_id', '=', $Customer[$i]->id)->count();
                $Customer[$i]->case_count = $LoanCaseCount;
            }
        }
        
        return DataTables::of($Customer)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = ' <a  href="/clients/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-chevron-double-right"></i></a>
                   ';
                return $actionBtn;
            })
            ->addColumn('action_change_client', function ($row) {
                $actionBtn = ' <a  href="javascript:void(0)" onclick="changeClient(' . $row->id . ', \'' . $row->name . '\')" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="change" >
                <i class="fa fa-refresh"></i></a>
                   ';
                return $actionBtn;
            })
            ->rawColumns(['action','action_change_client'])
            ->make(true);
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

    public static function getCustomerDetails($id)
    {
        $current_user = auth()->user();
        $customer = Customer::where('id', '=', $id)->first();

        $client_id = '';

        if ($customer->ic_no)
        {
            $client_id = $customer->ic_no;
        }
        else
        {
            $client_id = $customer->company_ref_no;
        }

        // get same case client
        $clientList = explode('&',$client_id);
        $Customer_ic = [];
        $clientList = $clientList[0];
        $clientList = explode(',',$clientList);

        if (count($clientList) > 0)
        {
            for ($i = 0; $i < count($clientList); $i++)
            {
                if($clientList[$i] != '')
                {
                    $client_ic = trim(str_replace("-", "", $clientList[$i]));

                    if ($client_ic != '')
                    {
                        $Customer = Customer::whereRaw("TRIM(REPLACE(ic_no,'-','')) like ?",'%'.$client_ic.'%')->get();
                        if (count($Customer) > 0)
                        {
                            for ($j = 0; $j < count($Customer); $j++)
                            {
                                array_push($Customer_ic, $Customer[$j]->id);
                            }
                        }
                        
        
                        $Customer = Customer::whereRaw("TRIM(REPLACE(company_ref_no,'-','')) like ?",'%'.$client_ic.'%')->get();
                        if (count($Customer) > 0)
                        {
                            for ($j = 0; $j < count($Customer); $j++)
                            {
                                array_push($Customer_ic, $Customer[$j]->id);
                            }
                            
                        }
                    }
                }
                
                
            }
        }

        if (in_array($current_user->menuroles, ['admin', 'management', 'account']))
        {
            // $ClientOtherLoanCase = [];
            $ClientOtherLoanCase = DB::table('loan_case as l')
            ->whereIn('l.customer_id',  $Customer_ic)
            ->where('l.id', '<>', $id)
            ->get();
        }
        else
        {
            $ClientOtherLoanCase = DB::table('loan_case as l')
            ->whereIn('l.customer_id',  $Customer_ic)
            ->where('l.id', '<>', $id)
            ->where('l.sales_user_id', '<>', 1)
            ->get();
        }

        return ([
            'customer' =>  $customer,
            'ClientOtherLoanCase' =>  $ClientOtherLoanCase,
        ]);
    }

    public function updateNewRefNo($LoanCase)
    {
        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';
        $lawyer_id = 0;
        $clerk_id = 0;
        $client_id = 0;


        $sales_id = $LoanCase->sales_user_id;
        $lawyer_id = $LoanCase->lawyer_id;
        $clerk_id = $LoanCase->clerk_id;

        $lawyer = User::where('id', '=', $lawyer_id)->first();
        $clerk = User::where('id', '=', $clerk_id)->first();
        $sales = User::where('id', '=', $sales_id)->first();


        $client = Customer::where('id', '=', $LoanCase->customer_id)->first();
        $client_short_code = Helper::generateNickName($client->name);
        $client_short_code = strtoupper($client_short_code);


        $bank = Portfolio::where('id', '=', $LoanCase->bank_id)->first();

        // return $running_no[0];
        $extractRefNo = str_replace('-','', $LoanCase->case_ref_no);
        $extractRefNo = str_replace('N1','', $LoanCase->case_ref_no);
        $running_no = (int)filter_var($extractRefNo, FILTER_SANITIZE_NUMBER_INT);

        if($running_no < 0)
        {
            $running_no = $running_no* -1;
        }


        $case_ref_no = str_replace("[sales]", $sales->nick_name, $case_ref_no);

        if($bank != null)
        {
            $case_ref_no = str_replace("[bank]", $bank->short_code, $case_ref_no);
        }
        

        if ($LoanCase->branch_id <> 1) {
            $Branch = Branch::where('id', '=', $LoanCase->branch_id)->first();
            $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        }

        $case_ref_no = str_replace("[running_no]", $running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);

        $case_ref_no = str_replace("[lawyer]", $lawyer->nick_name, $case_ref_no);

        if ($clerk_id != 0) {
            $clerk_user = User::where('id', '=', $clerk_id)->first();
            $case_ref_no = str_replace("[clerk]", $clerk->nick_name, $case_ref_no);
        } else {
            $case_ref_no = str_replace("/[clerk]", '', $case_ref_no);
        }

        return $case_ref_no;
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
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Customer::where('id', '=', $id)->first();
        $LoanCase = LoanCase::where('customer_id', '=', $id)->get();

        $LoanCase = DB::table('loan_case as l')
        ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
        ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
        ->select('l.*', 'u1.name as lawyer', 'u2.name as clerk')
        ->where('customer_id', '=', $id)->get();


        return view('dashboard.clients.edit', ['client' => $client, 'LoanCase' => $LoanCase]);
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
        $status = 1;
        $message = '';
        $current_user = auth()->user();


        try {
            $Customer = Customer::where('id', '=',  $id)->first();

            if ($Customer) {
                $Customer->name = $request->input('name');
                $Customer->ic_no = $request->input('ic_no');
                $Customer->company_ref_no = $request->input('company_ref_no');
                $Customer->phone_no = $request->input('phone_no');
                $Customer->email = $request->input('email');
                $Customer->address = $request->input('address');
                $Customer->updated_at = date('Y-m-d H:i:s');
                $Customer->save();

                $LoanCase = LoanCase::where('customer_id', '=',  $id)->get();

                if (count($LoanCase) > 0)
                {
                    for ($i = 0; $i < count($LoanCase); $i++) {

                        if ($LoanCase[$i]->bank_id != 0)
                        {
                            $new_ref_no = $this->updateNewRefNo($LoanCase[$i]);
                            $LoanCase[$i]->case_ref_no = $new_ref_no;
                            $LoanCase[$i]->save();
                        }
                        
                    }
                }

                
            }

           
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;

            return $e;
        }


        $request->session()->flash('message', 'Successfully updated client');
        return redirect()->route('clients.index');
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
