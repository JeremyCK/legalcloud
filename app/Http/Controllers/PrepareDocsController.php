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
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\LoanCaseKivNotes;
use App\Models\PrepareDocs;
use App\Models\ReturnCall;
use App\Models\SafeKeeping;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PrepareDocsController extends Controller
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


        return view('dashboard.prepare-docs.index', [
            'paidCount' => $paidCount,
            'exemptedCount' => $exemptedCount,
            'pendingCount' => $pendingCount,
            'branches' => $branch,
            'current_user' => $current_user
        ]);
    }

    public function getPrepareDocsList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();

            $safe_keeping = DB::table('prepare_docs as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
                ->select('a.*', 'u.name as assign_by', 'l.case_ref_no')
                ->where('a.status', '<>', 99);

                // if ($current_user->menuroles == 'sales') {
                //     // $safe_keeping = $safe_keeping->where('l.sales_user_id', '=', $current_user->id);
                //     $safe_keeping = $safe_keeping->where(function ($q) use($current_user)  {
                //         $q->where('l.sales_user_id', '=', $current_user->id)
                //         ->orWhere('a.created_by', '=', $current_user->id);
                //     });
                // } elseif ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer') {
                //     $safe_keeping = $safe_keeping->where(function ($q) use($current_user)  {
                //         $q->where('l.' . $current_user->menuroles . '_id', '=', $current_user->id)
                //         ->orWhere('a.created_by', '=', $current_user->id);
                //     });
                //     // $safe_keeping = $safe_keeping->where('l.' . $current_user->menuroles . '_id', '=', $current_user->id);
                // }

                if (in_array($current_user->menuroles, ['clerk', 'lawyer', 'chambering']))
                {
                    $safe_keeping = $safe_keeping->where(function ($q) use($current_user) {
                        $q->where('l.clerk_id', '=', $current_user->id)
                        ->orWhere('l.lawyer_id', '=', $current_user->id)
                        ->orWhere('a.created_by', '=', $current_user->id)
                        ->orWhere('a.branch', '=', $current_user->branch_id);
                    });
                }

            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                $safe_keeping = $safe_keeping->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $safe_keeping = $safe_keeping->where('a.created_at', '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $safe_keeping = $safe_keeping->where('a.created_at', '<=', $request->input("date_to"));
                }
            }

            if ($request->input("status") <> 99) {
                $safe_keeping->where('a.done', '=', $request->input("status"));
            }

            if ($request->input("branch") <> 0) {
                $safe_keeping->where('a.branch', '=', $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['receptionist','account','sales','maker']))
            {
                // if ($current_user->branch_id == 3)
                // {
                //     $safe_keeping = $safe_keeping->where('l.branch_id', '=',$current_user->branch_id);
                // }

                // $safe_keeping = $safe_keeping->Where(function ($q) use($branchInfo) {
                //     $q->whereIn('l.branch_id', $branchInfo['brancAccessList']);
                // });

                $safe_keeping = $safe_keeping->Where(function ($q) use($branchInfo) {
                    $q->whereIn('a.branch', $branchInfo['brancAccessList'])->where('a.status', '<>','99');
                });
            }

            $safe_keeping = $safe_keeping->orderBy('a.created_at', 'DESC')->get();

            return DataTables::of($safe_keeping)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use($current_user) {
                    $actionBtn = ' <a target="_blank" href="/prepare-docs/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    
                    $actionBtn = '
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank" href="/prepare-docs/' . $row->id . '/edit"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                      ';

                     if ($row->created_by == $current_user->id && $row->done <> 1)
                     {
                        $actionBtn .= '<div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteOperation(' . $row->id . ', \'DOCS\')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                       ';
                     }

                    return $actionBtn;
                })
                ->editColumn('done', function ($data) {
                    if ($data->done == '1')
                        return '<span class="label bg-success">Done</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->rawColumns(['action', 'done', 'case_ref_no'])
                ->make(true);
        }
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

        return view('dashboard.prepare-docs.create', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
        ]);
    }

    public function store(Request $request)
    {

        $case_id = 0;

        $current_user = auth()->user();

        $PrepareDocs  = new PrepareDocs();

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%'.$running_no.'%')->count() > 0) {
            $case_id = $request->input('case_id');
        }

        $PrepareDocs->case_id = $case_id;
        $PrepareDocs->case_ref = $request->input('case_ref_no');
        $PrepareDocs->client_id = 0;
        $PrepareDocs->client_name = $request->input('client');
        $PrepareDocs->signing_date = $request->input('signing_date');
        $PrepareDocs->docs_prepared = $request->input('docs_prepared');
        $PrepareDocs->done = $request->input('done');

        if ($request->input('done') == 1) {
            $PrepareDocs->done_date = date('Y-m-d H:i:s');
        }

        $PrepareDocs->created_by = $current_user->id;
        $PrepareDocs->branch = $request->input('branch');
        $PrepareDocs->remark = $request->input('remark');
        $PrepareDocs->status = 1;
        $PrepareDocs->created_at = date('Y-m-d H:i:s');
        $PrepareDocs->save();

        $status_span = '';

        if ($PrepareDocs->done == '1') {
            $status_span = '<span class="label bg-success">Yes</span>';
        } else {
            $status_span = '<span class="label bg-warning">No</span>';
        }

        $message = '
        <a href="/prepare-docs/' . $PrepareDocs->id . '/edit" target="_blank">[Created&nbsp;<b>Prepare Docs</b> record]</a><br />
        <strong>Signing Date</strong>:&nbsp;' . $request->input('signing_date') . '<br />
        <strong>Docs Prepared</strong>:&nbsp;' . $request->input('docs_prepared') . '<br />
        <strong>Done</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|preparedoc';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $PrepareDocs->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new record');
        return redirect()->route('prepare-docs.index');
    }

    public function edit($id)
    {
        $Adjudication = DB::table('prepare_docs as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $branchInfo = BranchController::manageBranchAccess();
        $loan_case = CaseController::getCaseListHub();

        return view('dashboard.prepare-docs.edit', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
            'main_obj' => $Adjudication
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
        $PrepareDocs = PrepareDocs::where('id', '=', $id)->first();

        $case_id = 0;

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }

        if ($PrepareDocs) {

            $PrepareDocs->case_id = $case_id;
            $PrepareDocs->case_ref = $request->input('case_ref_no');
            $PrepareDocs->client_id = 0;
            $PrepareDocs->client_name = $request->input('client');
            $PrepareDocs->signing_date = $request->input('signing_date');
            $PrepareDocs->docs_prepared = $request->input('docs_prepared');
            $PrepareDocs->done = $request->input('done');

            if ($request->input('done') == 1) {
                $PrepareDocs->done_date = date('Y-m-d H:i:s');
            }

            $PrepareDocs->created_by = $current_user->id;
            $PrepareDocs->branch = $request->input('branch');
            $PrepareDocs->remark = $request->input('remark');
            $PrepareDocs->updated_at = date('Y-m-d H:i:s');
            $PrepareDocs->save();

            $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|preparedoc')->first();

            if ($LoanCaseKivNotes) {
                $status_span = '';

                if ($PrepareDocs->done == '1') {
                    $status_span = '<span class="label bg-success">Yes</span>';
                } else {
                    $status_span = '<span class="label bg-warning">No</span>';
                }

                $message = '
                <a href="/prepare-docs/' . $PrepareDocs->id . '/edit" target="_blank">[Created&nbsp;<b>Prepare Docs</b> record]</a><br />
                <strong>Signing Date</strong>:&nbsp;' . $request->input('signing_date') . '<br />
                <strong>Docs Prepared</strong>:&nbsp;' . $request->input('docs_prepared') . '<br />
                <strong>Done</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
                $LoanCaseKivNotes->updated_by = $current_user->id;
                $LoanCaseKivNotes->save();
            }
        }

        $request->session()->flash('message', 'Successfully updated record');
        return redirect()->route('prepare-docs.index');
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
