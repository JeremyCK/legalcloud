<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountItem;
use App\Models\AccountLog;
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
use App\Models\LegalCloudCaseActivityLog;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\LoanCaseKivNotes;
use App\Models\SafeKeeping;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class LogsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();

        $branch = DB::table('branch')->where('status', '=', 1)->get();

        return view('dashboard.chkt.index', [
            'branches' => $branch,
            'current_user' => $current_user
        ]);
    }

    public function accountLog()
    {
        $current_user = auth()->user();

        $branch = DB::table('branch')->where('status', '=', 1)->get();
        $user = User::whereIn('menuroles', ['clerk','account','clerk','chambering','lawyer'])->orderBy('name', 'ASC')->get();

        return view('dashboard.logs.account_log.index', [
            'branches' => $branch,
            'users' => $user,
            'current_user' => $current_user
        ]);
    }

    public function getAccountLog(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            // $d = date_parse_from_format("Y-m-d", $request->input("recon_date"));

            $safe_keeping = DB::table('account_log as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'a.bill_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->select('a.*', 'u.name as perform_by', 'l.case_ref_no', 'b.bill_no')
                // ->whereBetween('a.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->whereMonth('a.created_at', 8)
                ->where('a.status', '<>', 99);



            // if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            //     $safe_keeping = $safe_keeping->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);
            // } else {
            //     if ($request->input("date_from") <> null) {
            //         $safe_keeping = $safe_keeping->where('a.created_at', '>=', $request->input("date_from"));
            //     }

            //     if ($request->input("date_to") <> null) {
            //         $safe_keeping = $safe_keeping->where('a.created_at', '<=', $request->input("date_to"));
            //     }
            // }

            // if ($request->input("status") <> 99) {
            //     $safe_keeping->where('a.file_ori_name', '=', $request->input("status") );
            // }

            if ($request->input("ref_no") != "") {
                $safe_keeping->where('l.case_ref_no', 'like', '%'.$request->input("ref_no").'%');
            }


            if ($request->input("user") <> 0) {
                $safe_keeping->where('a.user_id', '=', $request->input("user"));
            }

            $safe_keeping = $safe_keeping->orderBy('a.created_at', 'DESC')->get();


            return DataTables::of($safe_keeping)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/chkt/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('desc', function ($row) {
                    $actionBtn = '<div>' . $row->desc . '</div>';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->rawColumns(['action', 'case_ref_no', 'desc'])
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
        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '=', 1)
            ->get();

        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();

        return view('dashboard.chkt.create', [
            'loan_case' => $loan_case,
            'branch' => $branch,
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

        $chkt  = new CHKT();

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%'.$running_no.'%')->count() > 0) {
            $case_id = $request->input('case_id');
        }


        $chkt->case_id = $case_id;
        $chkt->case_ref = $request->input('case_ref_no');
        $chkt->client_id = 0;
        $chkt->client_name = $request->input('client');
        $chkt->last_spa_date = $request->input('last_spa_date');
        $chkt->current_spa_date = $request->input('current_spa_date');
        $chkt->chkt_filled_on = $request->input('chkt_filled_on');
        $chkt->per3_rpgt_paid = $request->input('per3_rpgt_paid');
        $chkt->created_by = $current_user->id;
        $chkt->branch = $request->input('branch');
        $chkt->remark = $request->input('remark');
        $chkt->status = 1;
        $chkt->created_at = date('Y-m-d H:i:s');
        $chkt->save();

        $file = $request->file('attachment_file');

        if ($file) {
            $oriFilename = $file->getClientOriginalName();
            $filename = time() . '_chkt_' . $file->getClientOriginalName();

            $location = 'app/documents/chkt/';

            // Upload file
            $file->move($location, $filename);


            $chkt->file_ori_name = $oriFilename;
            $chkt->file_new_name = $filename;
            $chkt->save();
        }

        $status_span = '';

        if ($chkt->per3_rpgt_paid == '1') {
            $status_span = '<span class="label bg-success">Yes</span>';
        } else {
            $status_span = '<span class="label bg-warning">No</span>';
        }

        $message = '
        <a href="/chkt/' . $chkt->id . '/edit" target="_blank">[Created&nbsp;<b>CHKT</b> record]</a><br />
        <strong>Last SPA Date</strong>:&nbsp;' . $request->input('last_spa_date') . '<br />
        <strong>Current SPA Date</strong>:&nbsp;' . $request->input('current_spa_date') . '<br />
        <strong>CHKT Filed On</strong>:&nbsp;' . $request->input('chkt_filled_on') . '<br />
        <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
        <strong>Received Notis Taksiran</strong>:&nbsp;<a target="_blank" href="/app/documents/chkt/' . $chkt->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $chkt->file_ori_name . '</a><br />
        <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|chkt';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $chkt->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new record');
        return redirect()->route('chkt.index');
    }

    public function edit($id)
    {
        $Adjudication = DB::table('chkt as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '=', 1)
            ->get();


        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();

        return view('dashboard.chkt.edit', [
            'loan_case' => $loan_case,
            'branch' => $branch,
            'adjudication' => $Adjudication
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
        $chkt = CHKT::where('id', '=', $id)->first();

        $case_id = 0;

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }


        if ($chkt) {

            $chkt->case_id = $case_id;
            $chkt->case_ref = $request->input('case_ref_no');
            $chkt->client_id = 0;
            $chkt->client_name = $request->input('client');
            $chkt->last_spa_date = $request->input('last_spa_date');
            $chkt->current_spa_date = $request->input('current_spa_date');
            $chkt->per3_rpgt_paid = $request->input('per3_rpgt_paid');
            $chkt->created_by = $current_user->id;
            $chkt->branch = $request->input('branch');
            $chkt->remark = $request->input('remark');
            $chkt->status = 1;
            $chkt->created_at = date('Y-m-d H:i:s');
            $chkt->save();

            $file = $request->file('attachment_file');

            if ($file) {

                $delete_path = 'app/documents/chkt/' . $chkt->file_new_name;
                if (File::exists(public_path($delete_path))) {
                    File::delete(public_path($delete_path));
                }


                $oriFilename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = 'CHKT_' . $case_id . '_' . time() . '.' . $extension;

                $location = 'app/documents/chkt/';

                // Upload file
                $file->move($location, $filename);


                $chkt->file_ori_name = $oriFilename;
                $chkt->file_new_name = $filename;
                $chkt->save();
            }

            $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|chkt')->first();

            if ($LoanCaseKivNotes) {
                $status_span = '';

                if ($chkt->per3_rpgt_paid == '1') {
                    $status_span = '<span class="label bg-success">Yes</span>';
                } else {
                    $status_span = '<span class="label bg-warning">No</span>';
                }

                $message = '
                <a href="/chkt/' . $chkt->id . '/edit" target="_blank">[Created&nbsp;<b>CHKT</b> record]</a><br />
                <strong>Last SPA Date</strong>:&nbsp;' . $request->input('last_spa_date') . '<br />
                <strong>Current SPA Date</strong>:&nbsp;' . $request->input('current_spa_date') . '<br />
                <strong>CHKT Filed On</strong>:&nbsp;' . $request->input('chkt_filled_on') . '<br />
                <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
                <strong>Received Notis Taksiran</strong>:&nbsp;<a target="_blank" href="/app/documents/chkt/' . $chkt->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $chkt->file_ori_name . '</a><br />
                <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
                $LoanCaseKivNotes->updated_by = $current_user->id;
                $LoanCaseKivNotes->save();
            }
        }

        $request->session()->flash('message', 'Successfully updated record');
        return redirect()->route('chkt.index');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    public static function createAccountLog($param_log)
    {
        $current_user = auth()->user();
        $AccountLog = new AccountLog();

        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $param_log['case_id'];
        $AccountLog->bill_id = $param_log['bill_id'];
        $AccountLog->object_id = $param_log['object_id'];
        $AccountLog->ori_amt = $param_log['ori_amt'];
        $AccountLog->new_amt = $param_log['new_amt'];
        $AccountLog->action = $param_log['action'];
        $AccountLog->desc = $param_log['desc'];
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();
    }

    public static function generateLog($param_log)
    {
        $current_user = auth()->user();

        $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();
        $LegalCloudCaseActivityLog->user_id = $current_user->id;
        $LegalCloudCaseActivityLog->case_id = $param_log['case_id'];
        $LegalCloudCaseActivityLog->action = $param_log['action'];
        $LegalCloudCaseActivityLog->desc = $current_user->name.$param_log['desc'];
        $LegalCloudCaseActivityLog->status = 1;
        $LegalCloudCaseActivityLog->object_id = $param_log['object_id'];

        if(isset($param_log['object_id_2']))
        {
            $LegalCloudCaseActivityLog->object_id_2 = $param_log['object_id_2'];
        }
        
        // $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');    
        $LegalCloudCaseActivityLog->save();
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
