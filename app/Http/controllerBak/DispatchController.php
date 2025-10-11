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
use App\Models\Branch;
use App\Models\Dispatch;
use App\Models\LoanCaseDispatch;
use App\Models\LoanCaseKivNotes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DispatchController extends Controller
{

    public function getOperationCode()
    {
        return config('global.operation.dispatch');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preparingCount = 0;
        $deliveredCount = 0;
        $sendingCount = 0;
        $current_user = auth()->user();

        $blnAllowEdit = true;

        if ($current_user->branch_id == 1) {
            if (!in_array($current_user->menuroles, ['receptionist', 'admin', 'management','sales'])) {
                $blnAllowEdit = false;
            }
        }

        $loanCaseDispatch = DB::table('dispatch as d')
            ->leftJoin('courier as c', 'd.courier_id', '=', 'c.id')
            ->leftJoin('loan_case as l', 'd.case_id', '=', 'l.id')
            ->select('d.*', 'c.name AS courier_name', 'l.case_ref_no')
            ->get();

        $courier = Courier::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $branch = DB::table('branch')->where('status', '=', 1)->get();
        $preparingCount = DB::table('dispatch')->where('status', '=', 0)->where('dispatch_type', '=', 1);
        $deliveredCount = DB::table('dispatch')->where('status', '=', 1)->where('dispatch_type', '=', 1);
        $sendingCount = DB::table('dispatch')->where('status', '=', 2)->where('dispatch_type', '=', 1);

        if ($current_user->branch_id == 3) {
            $preparingCount = $preparingCount->where('branch', '=', 3);
            $deliveredCount = $deliveredCount->where('branch', '=', 3);
            $sendingCount = $sendingCount->where('branch', '=', 3);
        }

        

        $preparingCount = $preparingCount->count();
        $deliveredCount = $deliveredCount->count();
        $sendingCount = $sendingCount->count();

        return view('dashboard.dispatch.index', [
            'loanCaseDispatch' => $loanCaseDispatch,
            'preparingCount' => $preparingCount,
            'deliveredCount' => $deliveredCount,
            'couriers' => $courier,
            'sendingCount' => $sendingCount,
            'branches' => $branch,
            'current_user' => $current_user,
            'blnAllowEdit' => $blnAllowEdit
        ]);
    }

    public function dispatchOutgoing()
    {
        $preparingCount = 0;
        $deliveredCount = 0;
        $sendingCount = 0;
        $current_user = auth()->user();

        $blnAllowEdit = true;

        if ($current_user->branch_id == 1) {
            if (!in_array($current_user->menuroles, ['receptionist', 'admin', 'management','sales'])) {
                $blnAllowEdit = false;
            }
        }

        $loanCaseDispatch = DB::table('dispatch as d')
            ->leftJoin('courier as c', 'd.courier_id', '=', 'c.id')
            ->leftJoin('loan_case as l', 'd.case_id', '=', 'l.id')
            ->select('d.*', 'c.name AS courier_name', 'l.case_ref_no')
            ->get();

        $courier = Courier::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $branch = DB::table('branch')->where('status', '=', 1)->get();
        $preparingCount = DB::table('dispatch')->where('status', '=', 0)->where('dispatch_type', '=', 1);
        $deliveredCount = DB::table('dispatch')->where('status', '=', 1)->where('dispatch_type', '=', 1);
        $sendingCount = DB::table('dispatch')->where('status', '=', 2)->where('dispatch_type', '=', 1);

        if ($current_user->branch_id == 3) {
            $preparingCount = $preparingCount->where('branch', '=', 3);
            $deliveredCount = $deliveredCount->where('branch', '=', 3);
            $sendingCount = $sendingCount->where('branch', '=', 3);
        }

        

        $preparingCount = $preparingCount->count();
        $deliveredCount = $deliveredCount->count();
        $sendingCount = $sendingCount->count();

        return view('dashboard.dispatch.index', [
            'loanCaseDispatch' => $loanCaseDispatch,
            'preparingCount' => $preparingCount,
            'deliveredCount' => $deliveredCount,
            'couriers' => $courier,
            'sendingCount' => $sendingCount,
            'branches' => $branch,
            'current_user' => $current_user,
            'blnAllowEdit' => $blnAllowEdit
        ]);
    }

    public function dispatchIncoming()
    {
        $preparingCount = 0;
        $deliveredCount = 0;
        $sendingCount = 0;

        $current_user = auth()->user();

        

        $loanCaseDispatch = DB::table('dispatch as d')
            ->leftJoin('courier as c', 'd.courier_id', '=', 'c.id')
            ->leftJoin('loan_case as l', 'd.case_id', '=', 'l.id')
            ->select('d.*', 'c.name AS courier_name', 'l.case_ref_no')
            ->get();

        $courier = Courier::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $branch = DB::table('branch')->where('status', '=', 1)->get();

        $preparingCount = DB::table('dispatch')->where('status', '=', 0)->where('dispatch_type', '=', 2)->count();
        $deliveredCount = DB::table('dispatch')->where('status', '=', 1)->where('dispatch_type', '=', 2)->count();
        $sendingCount = DB::table('dispatch')->where('status', '=', 2)->where('dispatch_type', '=', 2)->count();

        $branch = DB::table('branch')->where('status', '=', 1)->get();
        $preparingCount = DB::table('dispatch')->where('status', '=', 0)->where('dispatch_type', '=', 2);
        $deliveredCount = DB::table('dispatch')->where('status', '=', 1)->where('dispatch_type', '=', 2);
        $sendingCount = DB::table('dispatch')->where('status', '=', 2)->where('dispatch_type', '=', 2);

        if ($current_user->branch_id == 3) {
            $preparingCount = $preparingCount->where('branch', '=', 3);
            $deliveredCount = $deliveredCount->where('branch', '=', 3);
            $sendingCount = $sendingCount->where('branch', '=', 3);
        }
        
        $preparingCount = $preparingCount->count();
        $deliveredCount = $deliveredCount->count();
        $sendingCount = $sendingCount->count();
        
        $blnAllowEdit = true;

        if ($current_user->branch_id == 1) {
            if (!in_array($current_user->menuroles, ['receptionist', 'admin', 'management','sales'])) {
                $blnAllowEdit = false;
            }
        }

        return view('dashboard.dispatch.index-incoming', [
            'loanCaseDispatch' => $loanCaseDispatch,
            'preparingCount' => $preparingCount,
            'deliveredCount' => $deliveredCount,
            'couriers' => $courier,
            'sendingCount' => $sendingCount,
            'branches' => $branch,
            'blnAllowEdit' => $blnAllowEdit,
            'current_user' => $current_user
        ]);
    }

    public function getDispatchList(Request $request, $dispatch_id, $Status, $dispatch_type)
    {
        $date = Carbon::now()->subDays(3);

        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();
            $roles = array("admin", "management", "account");

            $loanCaseDispatch = DB::table('dispatch as d')
                ->leftJoin('courier as c', 'd.courier_id', '=', 'c.id')
                ->leftJoin('loan_case as l', 'd.case_id', '=', 'l.id')
                ->leftJoin('branch as b', 'd.branch', '=', 'b.id')
                ->join('users as u', 'u.id', '=', 'd.created_by')
                ->where('d.status', '<>', 99)
                ->where('d.created_at', '>=', '2023-01-01')
                ->where('d.dispatch_type', '=', $dispatch_type)
                ->select('d.*', 'c.name AS courier_name', 'l.case_ref_no', 'u.name as assign_by', 'b.name as branch_name',);


            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                $loanCaseDispatch = $loanCaseDispatch->whereDate('d.created_at', '>=', $request->input("date_from"))
                ->whereDate('d.created_at', '<=', $request->input("date_to"));
            } else {
                if ($request->input("date_from") <> null) {
                    $loanCaseDispatch = $loanCaseDispatch->where('d.created_at', '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $loanCaseDispatch = $loanCaseDispatch->where('d.created_at', '<=', $request->input("date_to"));
                }
            }

            if (in_array($current_user->menuroles, ['clerk', 'lawyer', 'chambering'])) {
                $loanCaseDispatch = $loanCaseDispatch->where(function ($q) use ($current_user) {
                    $q->where('l.clerk_id', '=', $current_user->id)
                        ->orWhere('l.lawyer_id', '=', $current_user->id)
                        ->orWhere('d.created_by', '=', $current_user->id)
                        ->orWhere('d.branch', '=', $current_user->branch_id);
                });
            }


            if ($dispatch_id <> 0) {
                $loanCaseDispatch->where('d.courier_id', '=', $dispatch_id);
            }

            if ($Status <> 99) {
                $loanCaseDispatch->where('d.status', '=', $Status);
            }

            if ($request->input("branch") <> 0) {
                $loanCaseDispatch->where('u.branch_id', '=', $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker'])) {

                $loanCaseDispatch = $loanCaseDispatch->where(function ($q) use ($current_user, $branchInfo) {
                    $q->whereIn('d.branch', $branchInfo['brancAccessList'])
                        ->orWhere('d.created_by', '=', $current_user->id);
                });
            }

            $loanCaseDispatch = $loanCaseDispatch->orderBy('d.created_at', 'DESC')->get();

            return DataTables::of($loanCaseDispatch)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use($current_user) {
                    $actionBtn = ' <a href="/dispatch/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';

                //     $actionBtn = '<div class="btn-group">
                //     <button type="button" class="btn btn-info btn-flat">Action</button>
                //     <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                //       <span class="caret"></span>
                //       <span class="sr-only">Toggle Dropdown</span>
                //     </button>
                //     <div class="dropdown-menu">
                //       <a class="dropdown-item" href="/dispatch/' . $row->id . '/edit" ><i class="cil-pencil"></i>Edit</a>
                //       <div class="dropdown-divider"></div>
                //       <a class="dropdown-item" href="javascript:void(0)" onclick="deleteDispatch(' . $row->id . ')" ><i class="cil-x"></i>Delete</a>
                //     </div>
                //   </div>
                //     ';

                    $actionBtn = '
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank" href="/dispatch/' . $row->id . '/edit"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                      ';

                     if ($row->created_by == $current_user->id && $row->status <> 1)
                     {
                    //     $actionBtn .= '<div class="dropdown-divider" style="margin:0"></div>
                    //     <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteOperation(' . $row->id . ', \'DISPATCH\')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                    //    ';

                    $op_code = $this->getOperationCode();

                       $actionBtn .= '<div class="dropdown-divider" style="margin:0"></div>
                       <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteOperationRecord(' . $row->id . ', \''.$op_code['code'].'\')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                      ';
                     }
                    

                    return $actionBtn;
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == '0')
                        return '<span class="label bg-warning">Sending</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Completed</span>';
                    else
                        return '<span class="label bg-info">In Progress</span>';
                })
                ->editColumn('file', function ($data) {
                    if ($data->file_new_name <> '') {
                        
                        if ($data->s3_file_name)
                        {
                            $actionBtn =  '<a  href="javascript:void(0)" class="btn btn-info shadow sharp mr-1" onclick="openFileFromS3(\'' . $data->s3_file_name . '\')"><i class="cil-paperclip"></i> </a>';
                        }
                        else
                        {
                            $actionBtn = ' <a target="_blank" href="app/documents/dispatch/' . $data->file_new_name . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-paperclip"></i></a>';
                        }
                   
                    } else {
                        $actionBtn = '';
                    }
                    return $actionBtn;
                })
                ->editColumn('dispatch_type', function ($data) {
                    if ($data->dispatch_type === '1')
                        return '<span class="label bg-warning">Outgoing</span>';
                    elseif ($data->dispatch_type === '2')
                        return '<span class="label bg-success">Incoming</span>';
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
                ->rawColumns(['action', 'status', 'case_ref_no', 'file'])
                ->make(true);
        }
    }

    public function create()
    {
        $current_user = auth()->user();
        $accessInfo = AccessController::manageAccess();

        $blnAllowEdit = true;
        

        if ($current_user->branch_id == 1) {
            if (!in_array($current_user->menuroles, ['receptionist', 'admin', 'management','sales'])) {
                $blnAllowEdit = false;
            }
        }
        
        $loan_case = CaseController::getCaseListHub();

        $courier = Courier::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $users = User::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC');

        if ($current_user->branch_id == 3) {
            $branch = $branch->where('id', '=', 3);
        }

        $branch = $branch->get();

        
        $branchInfo = BranchController::manageBranchAccess();
        $operation = $this->getOperationCode();

        return view('dashboard.dispatch.create', [
            'loan_case' => $loan_case,
            'courier' => $courier,
            // 'branch' => $branch,
            'users' => $users,
            'branch' => $branchInfo['branch'],
            'operation' => $operation,
            'blnAllowEdit' => $blnAllowEdit
        ]);
    }

    public function createIncoming()
    {
        $current_user = auth()->user();
        $accessInfo = AccessController::manageAccess();

        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '<>', 99);

        


        $courier = Courier::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $users = User::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();
 
        return view('dashboard.dispatch.create', [
            'loan_case' => $loan_case,
            'courier' => $courier,
            'branch' => $branch,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'case_id'             => 'required',
        //     'client_id'        => 'required',
        // ]);

        $case_id = 0;
        $dispatch_no = '';
        $dispatch_name = '';
        $filename = '';
        $oriFilename = '';
        $dispatch_type = '';

        $current_user = auth()->user();

        $Dispatch  = new Dispatch();

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }

        if ($request->input('courier_id') != '') {
            $current_timestamp = Carbon::now()->timestamp;
            $courier = Courier::where('id', '=', $request->input('courier_id'))->first();

            $dispatch_no = $courier->short_code . $case_id . $current_timestamp;
            $dispatch_name = $courier->name;
        }

        if ($request->input('dispatch_type') != '') {
            if ($request->input('dispatch_type') == 1) {
                $dispatch_type = 'Outgoing';
            } else if ($request->input('dispatch_type') == 2) {
                $dispatch_type = 'Incoming';
            }
        }

        $Dispatch->case_id = $case_id;
        $Dispatch->dispatch_no = $dispatch_no;
        $Dispatch->case_ref = $request->input('case_ref_no');
        $Dispatch->client_id = 0;
        $Dispatch->client_name = $request->input('client');
        $Dispatch->contact_name = $request->input('contact_name');
        $Dispatch->contact_no = $request->input('contact_no');
        $Dispatch->return_to_office_datetime = $request->input('return_to_office_datetime');
        // $Dispatch->received_by = $request->input('received_by');
        $Dispatch->courier_id = $request->input('courier_id');
        $Dispatch->send_to = $request->input('send_to');
        $Dispatch->status = $request->input('status');
        $Dispatch->branch = $request->input('branch');
        $Dispatch->remark = $request->input('remark');
        $Dispatch->dispatch_type = $request->input('dispatch_type');
        $Dispatch->created_by = $current_user->id;
        $Dispatch->job_desc = $request->input('job_desc');
        $Dispatch->created_at = date('Y-m-d H:i:s');
        $Dispatch->save();

        $file = $request->file('attachment_file');

        

        if ($file) {
            $oriFilename = $file->getClientOriginalName();
            // $filename = time() . '_' . $file->getClientOriginalName();

            $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' '), '_', $file->getClientOriginalName());

            $filename = time() . '_' . $res;

            $location = 'dispatch';

            $isImage =  ImageController::verifyImage($file);

            $disk = Storage::disk('Wasabi');
            $s3_file_name = '';
            
            if ($isImage == true) {
                $s3_file_name =  ImageController::resizeImg($file, $location, $filename);
            } else {
                // $file->move($location, $filename);
                // $filepath = url($location . $filename);

                $s3_file_name =  $disk->put($location, $file);
            }

            // Upload file
            // $file->move($location, $filename);


            $Dispatch->file_ori_name = $oriFilename;
            $Dispatch->file_new_name = $s3_file_name;
            $Dispatch->s3_file_name = $s3_file_name;
            $Dispatch->save();
        }

        $status_span = '';

        if ($Dispatch->status == '1') {
            $status_span = '<span class="label bg-success">Completed</span>';
        } else if ($Dispatch->status == '0') {
            $status_span = '<span class="label bg-warning">Sending</span>';
        } else {
            $status_span = '<span class="label bg-info">In Progress</span>';
        }

        $message = '
        <a href="/dispatch/' . $Dispatch->id . '/edit" target="_blank">[Created&nbsp;<b>Dispatch - ' . $dispatch_type . '</b> record]</a><br />
        <strong>Send To / Receive From</strong>:&nbsp;' . $request->input('send_to') . '<br />
        <strong>Dispatch Name</strong>:&nbsp;' . $dispatch_name . '<br />
        <strong>Returned To Office</strong>:&nbsp;' . $request->input('return_to_office_datetime') . '<br />
        <strong>Job Description</strong>:&nbsp;' . $request->input('job_desc') . '<br />
        <strong>Attachment</strong>:&nbsp;<a href="javascript:void(0)" onclick="openFileFromS3(\'' . $Dispatch->s3_file_name . '\')" class="mailbox-attachment-name "><i class="fa fa-paperclip"></i>' . $Dispatch->file_ori_name . '</a><br />
        <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
        <strong>Status</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|dispatch';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $Dispatch->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new dispatch');
        return redirect()->route('dispatch.index');
    }

    public function edit($id)
    {
        $current_user = auth()->user();
        $blnAllowEdit = true;

        if ($current_user->branch_id == 1) {
            if (!in_array($current_user->menuroles, ['receptionist', 'admin', 'management','sales'])) {
                $blnAllowEdit = false;
            }
        }

        $dispatch = DB::table('dispatch as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            // ->join('client as c', 'c.id', '=', 'a.client_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $loan_case = CaseController::getCaseListHub();

        $Attachment = DB::table('operation_attachment as l')
            ->join('users as c', 'c.id', '=', 'l.created_by')
            ->select('l.file_ori_name','l.file_new_name', 's3_file_name','l.created_at', 'c.name as upload_by','l.id')
            ->where('l.status', '<>', 99)
            ->where('l.key_id', $id)
            ->get();

        $Attachment2 = DB::table('dispatch as l')
            ->join('users as c', 'c.id', '=', 'l.created_by')
            ->select('l.file_ori_name','l.file_new_name', 's3_file_name','l.created_at', 'c.name as upload_by','l.id')
            ->where('l.status', '<>', 99)
            ->where('l.id', $id)
            ->get();

        if($Attachment2[0]->file_ori_name)
        {
            $Attachment = $Attachment->merge($Attachment2);
        }

        $courier = Courier::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $users = User::where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();
        $operation = $this->getOperationCode();
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.dispatch.edit',  ['courier' => $courier, 'users' => $users,
         'dispatch' => $dispatch, 
         'loan_case' => $loan_case, 
         'branch' => $branchInfo['branch'],
         'operation' => $operation,
         'Attachment' => $Attachment, 'blnAllowEdit' => $blnAllowEdit]);
    }

    public function deleteDispatch($id)
    {
        $Dispatch = Dispatch::where('id', '=', $id)->first();

        if(Storage::disk('Wasabi')->exists($Dispatch->s3_file_name)) {
            Storage::disk('Wasabi')->delete($Dispatch->s3_file_name);
        }

        $Dispatch->status = 99;
        $Dispatch->save();

        return response()->json(['status' => 1, 'data' => 'Dispatch deleted']);
    }

    public function update(Request $request, $id)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        // ]);

        $case_id = 0;
        $current_user = auth()->user();
        $dispatch_name = '';
        $dispatch_type = '';

        $Dispatch = Dispatch::where('id', '=', $id)->first();
        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }


        // if ($request->input('courier_id') != '' || $request->input('courier_id') != '0') {
        //     $current_timestamp = Carbon::now()->timestamp;
        //     $courier = Courier::where('id', '=', $request->input('courier_id'))->first();
        //     $dispatch_name = $courier->name;
        // }

        if ($request->input('courier_id') != '0') {
            $current_timestamp = Carbon::now()->timestamp;
            $courier = Courier::where('id', '=', $request->input('courier_id'))->first();
            $dispatch_name = $courier->name;
        }

        if ($request->input('dispatch_type') != '') {
            if ($request->input('dispatch_type') == 1) {
                $dispatch_type = 'Outgoing';
            } else if ($request->input('dispatch_type') == 2) {
                $dispatch_type = 'Incoming';
            }
        }


        $file = $request->file('attachment_file');

        if ($file) {

            $delete_path = 'app/documents/dispatch/' . $Dispatch->file_new_name;

            if ($Dispatch->s3_file_name)
            {
                if(Storage::disk('Wasabi')->exists($Dispatch->s3_file_name)) {
                    Storage::disk('Wasabi')->delete($Dispatch->s3_file_name);
                }
            }
            else
            {
                if (File::exists(public_path($delete_path))) {
                    File::delete(public_path($delete_path));
                }
            }


            $oriFilename = $file->getClientOriginalName();
            // $filename = time() . '_' . $file->getClientOriginalName();

            $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' '), '_', $file->getClientOriginalName());
            $filename = time() . '_' . $res;

            $isImage =  ImageController::verifyImage($file);

            // $location = 'app/documents/dispatch/';
            $location = 'dispatch';

            $disk = Storage::disk('Wasabi');
            $s3_file_name = '';


            if ($isImage == true) {
                $s3_file_name = ImageController::resizeImg($file, $location, $filename);
            } else {
                $s3_file_name =  $disk->put($location, $file);
            }

           



            $Dispatch->file_ori_name = $oriFilename;
            $Dispatch->file_new_name = $s3_file_name;
            $Dispatch->s3_file_name = $s3_file_name;
            $Dispatch->save();
        }

        $Dispatch->case_id = $case_id;
        $Dispatch->case_ref = $request->input('case_ref_no');
        $Dispatch->client_id = 0;
        // $Dispatch->file_ori_name = $oriFilename;
        // $Dispatch->file_new_name = $filename;
        $Dispatch->client_name = $request->input('client');
        $Dispatch->contact_name = $request->input('contact_name');
        $Dispatch->contact_no = $request->input('contact_no');
        $Dispatch->return_to_office_datetime = $request->input('return_to_office_datetime');
        $Dispatch->received_by = $request->input('received_by');
        $Dispatch->send_to = $request->input('send_to');
        $Dispatch->courier_id = $request->input('courier_id');
        $Dispatch->status = $request->input('status');
        $Dispatch->dispatch_type = $request->input('dispatch_type');
        $Dispatch->branch = $request->input('branch');
        $Dispatch->remark = $request->input('remark');
        // $Dispatch->created_by = $current_user->id;
        $Dispatch->job_desc = $request->input('job_desc');
        $Dispatch->updated_at = date('Y-m-d H:i:s');
        $Dispatch->save();

        $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|dispatch')->first();
        if ($LoanCaseKivNotes) {

            $status_span = '';

            if ($Dispatch->status == '1') {
                $status_span = '<span class="label bg-success">Completed</span>';
            } else if ($Dispatch->status == '0') {
                $status_span = '<span class="label bg-warning">Sending</span>';
            } else {
                $status_span = '<span class="label bg-info">In Progress</span>';
            }

            $message = '
            <a href="/dispatch/' . $Dispatch->id . '/edit" target="_blank">[Created&nbsp;<b>Dispatch - ' . $dispatch_type . '</b> record]</a><br />
            <strong>Send To / Receive From</strong>:&nbsp;' . $request->input('send_to') . '<br />
            <strong>Dispatch Name</strong>:&nbsp;' . $dispatch_name . '<br />
            <strong>Returned To Office</strong>:&nbsp;' . $request->input('return_to_office_datetime') . '<br />
            <strong>Job Description</strong>:&nbsp;' . $request->input('job_desc') . '<br />
            <strong>Attachment</strong>:&nbsp;<a  href="javascript:void(0)" onclick="openFileFromS3(\'' . $Dispatch->s3_file_name . '\')" class="mailbox-attachment-name "><i class="fa fa-paperclip"></i>' . $Dispatch->file_ori_name . '</a><br />
            <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
            <strong>Status</strong>:&nbsp;' . $status_span;

            $LoanCaseKivNotes->case_id =  $case_id;
            $LoanCaseKivNotes->notes =  $message;
            $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->updated_by = $current_user->id;
            $LoanCaseKivNotes->save();
        }


        $request->session()->flash('message', 'Successfully updated Dispatch info');

        return redirect()->route('dispatch.index');
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

        if (count($loanCase)) {
        }
        // return $loanCaseDetails;


        return view('dashboard.todolist.show', [
            'cases' => $loanCase,
            'cases_details' => $loanCaseDetails,
            'caseTemplate' => $caseTemplate,
            'current_user' => $current_user,
            'caseMasterListCategory' => $caseMasterListCategory,
            'caseMasterListField' => $caseMasterListField,
            'loanCaseDetailsCount' => $loanCaseDetailsCount
        ]);
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
