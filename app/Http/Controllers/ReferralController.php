<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use App\Models\LoanCase;
use Illuminate\Http\Request;
use App\Models\Referral;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();

        if ($current_user->menuroles == 'admin') {
            $referral = Referral::where('status', '!=', 99)->paginate(10);
        } else {
            $referral = Referral::where('status', '!=', 99)->where('status', '!=', 99)->paginate(10);
        }
        

        return view('dashboard.referral.index', ['referrals' => $referral]);
    }

    public function getReferralCaseList(Request $request)
    { 
        $LoanCase = DB::table('loan_case as l')
        ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
        ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
        ->select('l.*', 'u1.name as lawyer', 'u2.name as clerk')
        ->where('referral_id', '=', $request->input('id'))->get();


        // return $LoanCase;

        return DataTables::of($LoanCase)
            ->addIndexColumn()
            ->addColumn('action', function ($row)  {
                $actionBtn = ' <a  href="/referral/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                ';
                return $actionBtn;
            })
            ->editColumn('case_ref_no', function ($data) {


                return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a> ';
            })
            ->editColumn('status', function ($data) {
                if ($data->status === '2')
                    return '<span class="label bg-info">Open</span>';
                elseif ($data->status === '0')
                    return '<span class="label bg-success">Closed</span>';
                elseif ($data->status === '1')
                    return '<span class="label bg-purple">Running</span>';
                elseif ($data->status === '3')
                    return '<span class="label bg-warning">KIV</span>';
                elseif ($data->status == 4)
                    return '<span class="label bg-warning">Pending Close</span>';
                elseif ($data->status === '99')
                    return '<span class="label bg-danger">Aborted</span>';
                else
                    return '<span class="label bg-danger">Overdue</span>';
            })
            ->rawColumns(['status', 'action', 'case_ref_no'])
            ->make(true);
    }

    
    public function getReferralList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $referral = Referral::where('status', '=', 1);

            if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'account') {
                $referral = $referral->where('status', '=', 1)->get();
            }
            elseif ($current_user->menuroles == 'maker')
            {
                if ($current_user->branch_id == 3) {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [80])->get();
                }
                else if ($current_user->branch_id == 5) {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [32,118,143])->get();
                }
                else if ($current_user->branch_id == 2) {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [13])->get();
                }

                LoanCase::where('status', '<>', 99)->where('branch_id', '=', $current_user->branch_id)->get();
            } 
            else {
                if (in_array($current_user->id, [118,127, 179, 182]))
                {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [$current_user->id,32,141,118,127,143])->get();
                }
                else if (in_array($current_user->id, [14]))
                {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [2,32])->get();
                }
                else if (in_array($current_user->id, [29]))
                {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [$current_user->id,144])->get();
                }
                else if (in_array($current_user->id, [144]))
                {
                    $referral = $referral->where('status', '=', 1)->whereIn('created_by', [$current_user->id,29])->get();
                }
                else
                {

                    $referral = $referral->where('status', '=', 1)->where('created_by', '=', $current_user->id)->get();
                }
            }

            if (count($referral) > 0) {
                for ($i = 0; $i < count($referral); $i++) {
                    $sales_count = [];
    
                    // $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('referral_id', '=', $referral[$i]->id)->count();
                    $LoanCaseCount = LoanCase::where('referral_id', '=', $referral[$i]->id)->count();
    
                    $referral[$i]->case_count = $LoanCaseCount;
                }
            }

            return DataTables::of($referral)
                ->addIndexColumn()
                ->addColumn('action', function ($row)  {
                    $actionBtn = ' <a  href="/referral/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action_change_referral', function ($row) use($request) {

                    if($request->input('type') == 'case')
                    {
                        $actionBtn = ' <a  href="javascript:void(0)" onclick="changeReferral(' . $row->id . ', \'' . $row->name . '\')" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="change" >
                        <i class="fa fa-refresh"></i></a>
                        ';
                    }
                    else
                    {
                        $actionBtn = ' <a  href="javascript:void(0)" onclick="selectSummaryReportReferral(' . $row->id . ', \'' . $row->name . '\')" class="btn btn-success shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="change" >
                        <i class="fa fa-check"></i></a>
                        ';
                    }
                    
                    return $actionBtn;
                })
                ->addColumn('action_select_referral', function ($row) {
                    $actionBtn = ' <a  href="javascript:void(0)" onclick="selectedReferral(' . $row->id . ')" class="editData btn btn-success shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="change" >
                    <i class="fa fa-check"></i></a>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action','action_change_referral', 'action_select_referral'])
                ->make(true);
                
        }
    }

    public function getReferral()
    {
        // $current_user = auth()->user();
        // $userRoles = $current_user->menuroles;

        $referral = Referral::where('status', '=', 1)->get();
        
        return DataTables::of($referral)
            ->addIndexColumn()
            ->make(true);
    }

    public function show($id)
    {
    }

    public function createReferral(Request $request)
    {
        $validatedData = $request->validate([
            'name'             => 'required|min:1|max:500'
        ]);

        $current_user = auth()->user();

        if (!$validatedData) {
            return redirect()->back()->withInput();
        }

        $referral  = new Referral();

        $referral->name = $request->input('name');
        $referral->email = $request->input('email');
        $referral->ic_no = $request->input('ic_no');
        $referral->bank_account = $request->input('bank_account');
        $referral->bank_id = $request->input('bank_id');
        $referral->phone_no = $request->input('phone_no');
        $referral->company = $request->input('company');
        $referral->created_by = $current_user->id;
        $referral->status =  1;

        $referral->save();

        return response()->json(['status' => 1, 'message' => $referral->id]);
    }


    public function edit($id)
    {
        $referral = Referral::where('id', '=', $id)->first();
        $banks = Banks::where('status', '=', 1)->get();

        $case_count = DB::table('loan_case as l')
        ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
        ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
        ->select('l.*', 'u1.name as lawyer', 'u2.name as clerk')
        ->where('referral_id', '=', $id)->count();

        return view('dashboard.referral.edit', [
            'referral' => $referral,
            'banks' => $banks,
            'case_count' => $case_count
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name'             => 'required|min:1|max:64'
        ]);

        if (!$validatedData) {
            return redirect()->back()->withInput();
        }

        $referral = Referral::where('id', '=',  $id)->first();


        $referral->name = $request->input('name');
        $referral->email = $request->input('email');
        $referral->phone_no = $request->input('phone_no');
        $referral->remark = $request->input('remark');
        $referral->bank_account = $request->input('bank_account');
        $referral->ic_no = $request->input('ic_no');
        $referral->bank_id = $request->input('bank_id');
        $referral->company = $request->input('company');
        $referral->status =  $request->input('status');

        $referral->save();

        $request->session()->flash('message', 'Successfully updated Referral');
        return redirect()->route('referral.index');
    }

    public function autocomplete(Request $request)
    {
        $data = Referral::select("name")
            ->where("name", "LIKE", "%{$request->query}%")
            ->get();

        return response()->json($data);
    }
}
