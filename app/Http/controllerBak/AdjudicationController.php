<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
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
use App\Models\CHKT;
use App\Models\Dispatch;
use App\Models\LandOffice;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\LoanCaseKivNotes;
use App\Models\PrepareDocs;
use App\Models\ReturnCall;
use App\Models\SafeKeeping;
use App\Models\User;
use App\Models\Voucher;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdjudicationController extends Controller
{

    public function getOperationCode()
    {
        return config('global.operation.adjudication');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();
        $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1);
        $exemptedCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 2);
        $pendingCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 0);
        
        if ($current_user->branch_id == 3) {
            $paidCount = $paidCount->where('branch', '=', 3);
            $exemptedCount = $exemptedCount->where('branch', '=', 3);
            $pendingCount = $pendingCount->where('branch', '=', 3);
        }

        $paidCount = $paidCount->count();
        $exemptedCount = $exemptedCount->count();
        $pendingCount = $pendingCount->count();

        $branch = DB::table('branch')->where('status', '=', 1)->get();

        $branchInfo = BranchController::manageBranchAccess();


        return view('dashboard.adjudication.index', [
            'paidCount' => $paidCount,
            'exemptedCount' => $exemptedCount,
            'pendingCount' => $pendingCount,
            'branches' => $branchInfo['branch'],
            'current_user' => $current_user
        ]);
    }

    public function getAdjudicationList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();

            $Adjudication = DB::table('adjudication as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'a.client_id')
                ->join('users as u', 'u.id', '=', 'a.created_by')
                ->select('a.*', 'u.name as assign_by', 'l.case_ref_no')
                ->where('a.status', '<>', 99);

            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                // $Adjudication = $Adjudication->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);

                $Adjudication = $Adjudication->whereDate('a.created_at', '>=', $request->input("date_from"))
                ->whereDate('a.created_at', '<=', $request->input("date_to"));
            } else {
                if ($request->input("date_from") <> null) {
                    $Adjudication = $Adjudication->where('a.created_at', '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $Adjudication = $Adjudication->where('a.created_at', '<=', $request->input("date_to"));
                }
            }

            if (in_array($current_user->menuroles, ['clerk', 'lawyer', 'chambering']))
            {
                $Adjudication = $Adjudication->where(function ($q) use($current_user) {
                    $q->where('l.clerk_id', '=', $current_user->id)
                    ->orWhere('l.lawyer_id', '=', $current_user->id)
                    ->orWhere('a.created_by', '=', $current_user->id)
                    ->orWhere('a.branch', '=', $current_user->branch_id);
                });
            }

            // if ($current_user->menuroles <> "admin" && $current_user->menuroles <> "management" && $current_user->menuroles <> "account" && $current_user->menuroles <> "receptionist") {
               
            //     $Adjudication = $Adjudication->where(function ($q) use($current_user) {
            //         $q->where('l.lawyer_id', '=', $current_user->id)
            //         ->orWhere('l.sales_user_id', '=', $current_user->id)
            //             ->orWhere('l.clerk_id', '=', $current_user->id);
            //     });
            // }

            // if ($current_user->menuroles == 'sales') {
            //     // $safe_keeping = $safe_keeping->where('l.sales_user_id', '=', $current_user->id);
            //     $Adjudication = $Adjudication->where(function ($q) use($current_user)  {
            //         $q->where('l.sales_user_id', '=', $current_user->id)
            //         ->orWhere('a.created_by', '=', $current_user->id);
            //     });
            // } elseif ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer') {
            //     $Adjudication = $Adjudication->where(function ($q) use($current_user)  {
            //         $q->where('l.' . $current_user->menuroles . '_id', '=', $current_user->id)
            //         ->orWhere('a.created_by', '=', $current_user->id);
            //     });
            //     // $safe_keeping = $safe_keeping->where('l.' . $current_user->menuroles . '_id', '=', $current_user->id);
            // }

            if ($request->input("status") <> 99) {
                $Adjudication->where('a.status', '=', $request->input("status"));
            }

            if ($request->input("branch") <> 0) {
                $Adjudication = $Adjudication->where(function ($q) use($request) {
                    $q->where('l.branch_id', '=', $request->input("branch"))
                    ->orWhere('a.branch', '=', $request->input("branch"));
                });
            }

            if (in_array($current_user->menuroles, ['receptionist','account','sales','maker','jr_account']))
            {
                // $Adjudication = $Adjudication->Where(function ($q) use($branchInfo) {
                //     $q->whereIn('l.branch_id', $branchInfo['brancAccessList']);
                // });

                $Adjudication = $Adjudication->Where(function ($q) use($branchInfo) {
                    $q->whereIn('a.branch', $branchInfo['brancAccessList'])->where('a.status', '<>','99');
                });
            }
            

            $Adjudication = $Adjudication->orderBy('a.created_at', 'DESC')->get();

            return DataTables::of($Adjudication)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use($current_user) {
                    $actionBtn = ' <a target="_blank" href="/adjudication/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    
                    $actionBtn = '
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank" href="/adjudication/' . $row->id . '/edit"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                      ';

                     if ($row->created_by == $current_user->id)
                     {
                        $actionBtn .= '<div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteOperation(' . $row->id . ', \'ADJU\')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                       ';
                     }
                    
                    return $actionBtn;
                })
                ->editColumn('stamp_duty_paid', function ($data) {
                    if ($data->status === '0')
                        return '-';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Paid</span>';
                    else
                        return '<span class="label bg-warning">Exempted</span>';
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }
                    // $actionBtn = ' <a href="/case/'. $row->case_id . '" class="  " >'. $row->case_ref_no . ' </a>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'stamp_duty_paid', 'case_ref_no'])
                ->make(true);
        }
    }

    public function deleteOperation(Request $request)
    {
        $obj = null;
        $current_user = auth()->user();

        if ($request->input('type') == 'ADJU')
        {
            $obj = Adjudication::where('id', $request->input('id'))->first();

        }
        else if ($request->input('type') == 'SAFE')
        {
            $obj = SafeKeeping::where('id', $request->input('id'))->first();

        } 
        else if ($request->input('type') == 'DOCS')
        {
            $obj = PrepareDocs::where('id', $request->input('id'))->first();

        }
        else if ($request->input('type') == 'CALL')
        {
            $obj = ReturnCall::where('id', $request->input('id'))->first();

        }
        else if ($request->input('type') == 'LAND')
        {
            $obj = LandOffice::where('id', $request->input('id'))->first();

        }
        else if ($request->input('type') == 'CHKT')
        {
            $obj = CHKT::where('id', $request->input('id'))->first();

        }
        else if ($request->input('type') == 'DISPATCH')
        {
            $obj = Dispatch::where('id', $request->input('id'))->first();

        }

        if($obj)
        {
            $date = new DateTime($obj->created_at);
            $diff = (new DateTime)->diff($date)->days;

    
            if ($diff > 3) {
                return response()->json(['status' => 0, 'message' => 'Not allow to delete the record that created more than 3 days']);
            }

            $obj->status = 99;
            $obj->deleted_by = $current_user->id;
            $obj->deleted_at = date('Y-m-d H:i:s');
            $obj->save();

            if ($obj->s3_file_name)
            {
                if(Storage::disk('Wasabi')->exists($obj->s3_file_name)) {
                    Storage::disk('Wasabi')->delete($obj->s3_file_name);
                }
            }

        }

        return response()->json(['status' => 1, 'message' => 'Record deleted']);
        
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

    public function create()
    {
        $branchInfo = BranchController::manageBranchAccess();
        $loan_case = CaseController::getCaseListHub();

        return view('dashboard.adjudication.create', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
        ]);
    }

    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'case_id'             => 'required',
        //     'client_id'        => 'required',
        // ]);

        $case_id = 0;

        $current_user = auth()->user();

        $Adjudication  = new Adjudication();

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%'.$running_no.'%')->count() > 0) {
            $case_id = $request->input('case_id');
        }

        $Adjudication->case_id = $case_id;
        $Adjudication->case_ref = $request->input('case_ref_no');
        $Adjudication->client_id = 0;
        $Adjudication->client_name = $request->input('client');
        $Adjudication->first_house = $request->input('first_house');
        $Adjudication->adju_doc = $request->input('adju_doc');
        $Adjudication->adju_date = $request->input('adju_date');
        $Adjudication->adju_no = $request->input('adju_no');
        $Adjudication->notis_date = $request->input('notis_date');
        $Adjudication->stamp_duty_paid = $request->input('stamp_duty_paid');
        $Adjudication->created_by = $current_user->id;
        $Adjudication->branch = $request->input('branch');
        $Adjudication->remark = $request->input('remark');
        $Adjudication->status = $request->input('stamp_duty_paid');
        $Adjudication->created_at = date('Y-m-d H:i:s');
        $Adjudication->save();

        $status_span = '';

        if ($Adjudication->stamp_duty_paid == '1') {
            $status_span = '<span class="label bg-success">Paid</span>';
        } else {
            $status_span = '<span class="label bg-warning">Exempted</span>';
        }

        $message = '
        <a href="/adjudication/' . $Adjudication->id . '/edit" target="_blank">[Created&nbsp;<b>Adjudication</b> record]</a><br />
        <strong>First House</strong>:&nbsp;' . $request->input('first_house') . '<br />
        <strong>Adju No</strong>:&nbsp;' . $request->input('adju_no') . '<br />
        <strong>Adju Doc</strong>:&nbsp;' . $request->input('adju_doc') . '<br />
        <strong>Adju Date</strong>:&nbsp;' . $request->input('adju_date') . '<br />
        <strong>Date of Notis</strong>:&nbsp;' . $request->input('notis_date') . '<br />
        <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
        <strong>Stamp Duty Paid</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|adju';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $Adjudication->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new adjudication');
        return redirect()->route('adjudication.index');
    }

    public function edit($id)
    {
        $Adjudication = DB::table('adjudication as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            // ->join('client as c', 'c.id', '=', 'a.client_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $branchInfo = BranchController::manageBranchAccess();
        $loan_case = CaseController::getCaseListHub();

        return view('dashboard.adjudication.edit', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
            'adjudication' => $Adjudication
        ]);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $validatedData = $request->validate([
        //     'case_id'             => 'required',
        //     'client_id'        => 'required',
        // ]);

        $current_user = auth()->user();
        $Adjudication = Adjudication::where('id', '=', $id)->first();

        $case_id = 0;

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }


        if ($Adjudication) {
            $Adjudication->case_id = $case_id;
            $Adjudication->case_ref = $request->input('case_ref_no');
            $Adjudication->client_id = 0;
            $Adjudication->client_name = $request->input('client');
            $Adjudication->first_house = $request->input('first_house');
            $Adjudication->adju_doc = $request->input('adju_doc');
            $Adjudication->adju_date = $request->input('adju_date');
            $Adjudication->adju_no = $request->input('adju_no');
            $Adjudication->notis_date = $request->input('notis_date');
            $Adjudication->stamp_duty_paid = $request->input('stamp_duty_paid');
            $Adjudication->remark = $request->input('remark');
            $Adjudication->status = $request->input('stamp_duty_paid');
            $Adjudication->updated_at = date('Y-m-d H:i:s');
            $Adjudication->save();

            $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|adju')->first();

            if ($LoanCaseKivNotes) {
                $status_span = '';

                if ($Adjudication->stamp_duty_paid == '1') {
                    $status_span = '<span class="label bg-success">Paid</span>';
                } else {
                    $status_span = '<span class="label bg-warning">Exempted</span>';
                }

                $message = '
                <a href="/adjudication/' . $Adjudication->id . '/edit" target="_blank">[Created&nbsp;<b>Adjudication</b> record]</a><br />
                <strong>First House</strong>:&nbsp;' . $request->input('first_house') . '<br />
                <strong>Adju No</strong>:&nbsp;' . $request->input('adju_no') . '<br />
                <strong>Adju Doc</strong>:&nbsp;' . $request->input('adju_doc') . '<br />
                <strong>Adju Date</strong>:&nbsp;' . $request->input('adju_date') . '<br />
                <strong>Date of Notis</strong>:&nbsp;' . $request->input('notis_date') . '<br />
                <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
                <strong>Stamp Duty Paid</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
                $LoanCaseKivNotes->updated_by = $current_user->id;
                $LoanCaseKivNotes->save();
            }
        }

        $request->session()->flash('message', 'Successfully created new adjudication');
        return redirect()->route('adjudication.index');
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
