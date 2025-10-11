<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\Courier;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $couriers = Courier::all();

        return view('dashboard.couriers.index', ['couriers' => $couriers]);
    }

    public function create()
    {
        return view('dashboard.couriers.create');
    }

    public function store(Request $request)
    {
        $courier = new Courier();

        $courier->name = $request->input('name');
        $courier->short_code = $request->input('short_code');
        $courier->desc = $request->input('desc');
        $courier->tel_no = $request->input('tel_no');
        $courier->fax = $request->input('fax');
        $courier->address = $request->input('address');
        $courier->status = 1;
        $courier->created_at = now();

        $courier->save();

        $request->session()->flash('message', 'Successfully created new courier');

        $couriers = Courier::all();
        return view('dashboard.couriers.index', ['couriers' => $couriers]);
    }

    public function edit($id)
    {
        $courier = Courier::where('id', '=', $id)->first();

        return view('dashboard.couriers.edit',  ['courier' => $courier]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name'             => 'required|min:1|max:64',
        ]);
        
        $courier = Courier::where('id', '=', $id)->first();

        $courier->name = $request->input('name');
        $courier->short_code = $request->input('short_code');
        $courier->desc = $request->input('desc');
        $courier->tel_no = $request->input('tel_no');
        $courier->fax = $request->input('fax');
        $courier->address = $request->input('address');
        $courier->status = $request->input('status');
        $courier->created_at = now();

        $courier->save();


        $request->session()->flash('message', 'Successfully updated courier info');

        $couriers = Courier::all();

        return view('dashboard.couriers.index', ['couriers' => $couriers]);
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
