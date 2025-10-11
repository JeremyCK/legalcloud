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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use stdClass;

class ParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $banks = Banks::all();

        // return view('dashboard.banks.index', ['banks' => $banks]);
    }

    public function getParameter($parameterName = "")
    {

        if ($parameterName != "")
        {
            $parameter = Parameter::where('parameter_type', '=', $parameterName)->orderBy('parameter_value_1', 'asc')->get();
        }
        else
        {
            $parameter = Parameter::all();
        }

        return $parameter;

    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {
       
    }
   
    public function update(Request $request, $id)
    {
        
    }

}
