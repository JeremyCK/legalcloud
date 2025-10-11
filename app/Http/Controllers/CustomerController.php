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
use Facade\FlareClient\Http\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Customer::all();

        return view('dashboard.clients.index', ['clients' => $clients]);
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

        $customer = Customer::where('id', '=', $id)->first();
        $loanCase = LoanCase::where('customer_id', '=', $id)->get();


        return view('dashboard.clients.show', ['customer' => $customer, 'loanCase' => $loanCase]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Customer::where('id', '=', $id)->first();

        return view('dashboard.clients.edit', ['client' => $client]);
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
