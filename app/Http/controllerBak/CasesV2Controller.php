<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountItem;
use App\Models\Adjudication;
use App\Models\Branch;
use App\Models\CaseAccountTransaction;
use App\Models\CaseActivityLog;
use App\Models\CaseArchive;
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
use App\Models\Cases;
use App\Models\CasesNotes;
use App\Models\CasesPIC;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\User;
use App\Models\Voucher;
use DateTime;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CasesV2Controller extends Controller
{
    public function perfectionCase()
    {
        $current_user = auth()->user();

        $users = User::where('status', '=', 1)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderby('name', 'ASC')->get();
        $sales = User::where('status', '=', 1)->whereIn('menuroles', ['sales'])->orWhereIn('id', [2,3])->orderby('name', 'ASC')->get();

        return view('dashboard.cases.perfection-case', [
            'users' => $users,
            'current_user' => $current_user,
            'sales' => $sales,
        ]);
    }

    public function addCase()
    {
        $sales = User::where('status', '=', 1)->whereIn('menuroles', ['sales'])->orWhereIn('id', [2,3])->orderby('name', 'ASC')->get();
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.cases.add-case', [
            'sales' => $sales,
            'branchs' => $branchInfo['branch'],
        ]);
    }

    public function createCaseOther(Request $request)
    {
        $Cases = new Cases();
        $current_user = auth()->user();

        $Cases->ref_no =  $request->input('ref_no');
        $Cases->client_name_p =  $request->input('client_name_p');
        $Cases->client_name_v =  $request->input('client_name_v');
        $Cases->case_date =  $request->input('case_date');
        $Cases->completion_date =  $request->input('completion_date');
        $Cases->case_type = $request->input('case_type');
        $Cases->sales_id = $request->input('sales_id');
        $Cases->created_at = date('Y-m-d H:i:s');
        $Cases->created_by = $current_user->id;
        $Cases->save();

        return response()->json(['status' => 1, 'data' => 'Case added', 'return_id' => $Cases->id]);
    }

    public function getCasesList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $cases = DB::table('cases as a')
                ->leftJoin('users as u1', 'u1.id', '=', 'a.lawyer_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'a.clerk_id')
                ->leftJoin('users as u3', 'u3.id', '=', 'a.sales_id')
                ->leftjoin('cases_note as n', function ($join) {
                    $join->on('n.case_id', '=', 'a.id')
                        ->where('n.status', '<>', 99)
                        ->where('n.id', '=', DB::raw("(SELECT max(id) from cases_note WHERE case_id = a.id and status <> 99)"));
                })
                ->leftJoin('users as u', 'u.id', '=', 'n.created_by')
                ->select('a.*', 'u1.name as lawyer_name', 'u2.name as clerk_name', 'u3.name as sales_name','n.notes', 'n.created_at as meg_time', 'u.menuroles as menuroles', 'u.name as msg_user', )
                ->addSelect(DB::raw('(SELECT GROUP_CONCAT(ua.name SEPARATOR "<br/>") FROM cases_pic as p left join users ua on ua.id =p.pic_id WHERE case_id = a.id and p.status = 1) as pic'))
                ->where('a.status', '<>', 99);

            if (in_array($current_user->menuroles,['manager','admin', 'account']) || in_array($current_user->id,[14])){

                if ($request->input('pic')) {
                    if ($request->input('pic') <> 0) {

                        $cases = $cases->where(function ($q) use ($request) {
                            $q->where('a.lawyer_id', $request->input('pic'))
                                ->orWhere('a.clerk_id', $request->input('pic'));
                        });
                    }
                }

                if ($request->input('sales')) {
                    if ($request->input('sales') <> 0) {

                        $cases->where('a.sales_id',  $request->input('sales'));
                    }
                }
               
            } 
            elseif (in_array($current_user->menuroles,['lawyer','clerk', 'chambering']))
            {
                $CasesPIC = CasesPIC::where('status', '=', 1)->where('pic_id', $current_user->id)->pluck('case_id')->toArray();
                $cases->whereIn('a.id', $CasesPIC);
            }
            elseif (in_array($current_user->menuroles,['sales']))
            {
                $cases->where('a.sales_id',  $current_user->id);
            }
            else {
                $cases->where('b.branch_id',  $current_user->branch_id);
            }

            $cases = $cases->get();
            
            return DataTables::of($cases)
                ->addIndexColumn()
                ->addColumn('action', function ($row)  {
                    $actionBtn = '
                    <a  href="/case-details/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->editColumn('remarks', function ($row) {
                    $actionBtn = ' <div id="remark_' . $row->id . '">' . $row->remarks . '</div>';

                    return $actionBtn;
                })
                ->editColumn('client', function ($row) {
                    $actionBtn = '<b>Client (P): </b>' . $row->client_name_p.'<br/><b>Client (V): </b>' . $row->client_name_v.'<br/>' ;

                    return $actionBtn;
                })
                ->editColumn('completion_date', function ($row) {
                    $actionBtn = ' <div id="completion_date_' . $row->id . '">' . $row->completion_date . '</div>';

                    return $actionBtn;
                })
                ->editColumn('pic', function ($row) {
                    $actionBtn = ' <div >' . $row->pic . '</div>';

                    return $actionBtn;
                })
                ->editColumn('notes', function ($data) use ($current_user) {
                    $color = 'info';

                    if ($data->menuroles == 'account') {
                        $color = 'warning';
                    } else if ($data->menuroles == 'admin') {
                        $color = 'danger';
                    } else if ($data->menuroles == 'sales') {
                        $color = 'success';
                    } else if ($data->menuroles == 'clerk') {
                        $color = 'primary';
                    } else if ($data->menuroles == 'lawyer') {
                        $color = 'info';
                    }
                    else{
                        $color = 'question';
                    }

                    if ($data->notes) {
                        return  '<div><span class="text-' . $color . '"><b>' . $data->msg_user . '</b></span><br/><span style="color:#636f83">' . date('Y-m-d h:i A', strtotime($data->meg_time))   . '</span><br/><br/>' . $data->notes . '</div>';
                    } else {
                        return 'No notes yet';
                    }
                })
                ->rawColumns(['action', 'remarks', 'new_pic',  'notes', 'sales_id', 'lawyer_id', 'completion_date','client','pic'])
                ->make(true);
        }
    }

    public function caseDetails($id)
    {
        $current_user = auth()->user();

        if (in_array($current_user->menuroles,['manager','admin', 'account'])|| in_array($current_user->id,[14])){
               
        } 
        else if (in_array($current_user->menuroles,['lawyer','chambering', 'clerk'])){
            $CasesPICCount = CasesPIC::where('status', '=', 1)->where('case_id', $id)->where('pic_id', $current_user->id)->count();

            if ($CasesPICCount == 0)
            {
                return redirect()->route('perfectionCase');
            }
        } 
        else if (in_array($current_user->menuroles,['sales'])){
            $Cases = Cases::where('status', '=', 1)->where('id', $id)->where('sales_id', $current_user->id)->count();

            if ($Cases == 0)
            {
                return redirect()->route('perfectionCase');
            }
        }

        $case = DB::table('cases as a')
        ->leftJoin('users as u1', 'u1.id', '=', 'a.lawyer_id')
        ->leftJoin('users as u2', 'u2.id', '=', 'a.clerk_id')
        ->leftJoin('users as u3', 'u3.id', '=', 'a.sales_id')
        ->select('a.*', 'u1.name as lawyer_name', 'u2.name as clerk_name', 'u3.name as sales_name')
        ->where('a.id', $id)->first();

        $Staffs = Users::whereIn('menuroles', ['lawyer', 'clerk'])->where('status','<>' ,99)->orderBy('name', 'asc')->get();
        $Sales = Users::whereIn('menuroles', ['sales'])->orwhereIn('id',[2,3])->where('status','<>' ,99)->orderBy('name', 'asc')->get();

        $CasesPIC = DB::table('cases_pic as a')
        ->leftJoin('users as u', 'u.id', '=', 'a.pic_id')
        ->select('a.*', 'u.name as name', 'u.menuroles as menuroles')
        ->where('a.case_id', $id)->where('a.status', 1)->orderBy('menuroles', 'desc')->get();

        $CasesNotes = $this->loadCaseNote($id);

        return view('dashboard.cases.case-details', [
            'case' => $case,
            'CasesPIC' => $CasesPIC,
            'CasesNotes' => $CasesNotes,
            'current_user' => $current_user,
            'Staffs' => $Staffs,
            'Sales' => $Sales,
        ]);
    }

    public function assignPIC(Request $request, $id)
    {
        $current_user = auth()->user();
        $CasesPIC = CasesPIC::where('case_id', $id)->where('pic_id', $request->input('pic_id'))->where('status', 1)->first();

        if($CasesPIC)
        {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'This PIC already in this case']);
        }

        $request->input('role');

        $CasesPIC = new CasesPIC();

        $CasesPIC->case_id =  $id;
        $CasesPIC->pic_id =  $request->input('pic_id');
        $CasesPIC->created_at = date('Y-m-d H:i:s');
        $CasesPIC->assigned_by = $current_user->id;
        $CasesPIC->save();

        $CasesPIC = DB::table('cases_pic as a')
        ->leftJoin('users as u', 'u.id', '=', 'a.pic_id')
        ->select('a.*', 'u.name as name', 'u.menuroles as menuroles')
        ->where('a.case_id', $id)->where('a.status', 1)->orderBy('menuroles', 'desc')->get();

        $view = view('dashboard.cases.pic-list', compact('CasesPIC', 'current_user'))->render();
    
        return response()->json(['status' => 1, 'data' => 'PIC updated', 'view' =>  $view]);
    }

    public function removePIC(Request $request, $id)
    {
        $case_id = 0;
        $current_user = auth()->user();
        $CasesPIC = CasesPIC::where('id', $id)->where('status', 1)->first();


        if($CasesPIC)
        {
            $case_id = $CasesPIC->case_id;
            $CasesPIC->status = 99;
            $CasesPIC->remove_by = $current_user->id;
            $CasesPIC->remove_at = date('Y-m-d H:i:s');
            $CasesPIC->save();
        }


        $CasesPIC = DB::table('cases_pic as a')
        ->leftJoin('users as u', 'u.id', '=', 'a.pic_id')
        ->select('a.*', 'u.name as name', 'u.menuroles as menuroles')
        ->where('a.case_id', $case_id)->where('a.status', 1)->orderBy('menuroles', 'desc')->get();

        $view = view('dashboard.cases.pic-list', compact('CasesPIC', 'current_user'))->render();
    
        return response()->json(['status' => 1, 'data' => 'PIC removed', 'view' =>  $view]);
    }

    public function updateCaseDetails(Request $request, $id)
    {
        $current_user = auth()->user();
        $case = Cases::where('id', $id)->first();

        if($case)
        {
            $case->ref_no =  $request->input('ref_no');
            $case->client_name_p =  $request->input('client_name_p');
            $case->client_name_v =  $request->input('client_name_v');
            $case->case_date =  $request->input('case_date');
            $case->sales_id =  $request->input('sales_id');
            $case->completion_date =  $request->input('completion_date');
            $case->updated_at = date('Y-m-d H:i:s');
            $case->updated_by = $current_user->id;
            $case->save();

            $case = DB::table('cases as a')
            ->leftJoin('users as u1', 'u1.id', '=', 'a.lawyer_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'a.clerk_id')
            ->leftJoin('users as u3', 'u3.id', '=', 'a.sales_id')
            ->select('a.*', 'u1.name as lawyer_name', 'u2.name as clerk_name', 'u3.name as sales_name')
            ->where('a.id', $id)->first();

            $view = view('dashboard.cases.div-case-details', compact('case', 'current_user'))->render();
    
            return response()->json(['status' => 1, 'data' => 'Notes updated', 'view' =>  $view]);
        }
    }

    public function addCaseNotes(Request $request, $id)
    {
        $return_id = 0;
        $current_user = auth()->user();
        $view = null;

        $CasesNotes = new CasesNotes();

        $CasesNotes->case_id =  $id;
        $CasesNotes->notes =  $request->input('notes_msg');
        $CasesNotes->label =  '';
        $CasesNotes->created_at = date('Y-m-d H:i:s');
        $CasesNotes->created_by = $current_user->id;
        $CasesNotes->save();

        $return_id = $CasesNotes->id;

        $CasesNotes = $this->loadCaseNote($id);

        $view = view('dashboard.cases.notes-list', compact('CasesNotes', 'current_user'))->render();

        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $return_id, 'view' =>  $view]);
    }

    public function editCaseNotes(Request $request, $id)
    {
        $return_id = 0;
        $current_user = auth()->user();
        $view = null;

        $CasesNotes = CasesNotes::where('id', $id)->first();

        if($CasesNotes)
        {
            $date = new DateTime($CasesNotes->created_at);
            $diff = (new DateTime)->diff($date)->days;
    
            if ($diff > 7) {
                return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to edit the note that created more than 7 days']);
            }

            $case_id = $CasesNotes->case_id;
            $CasesNotes->notes =  $request->input('notes_msg');
            $CasesNotes->updated_at = date('Y-m-d H:i:s');
            $CasesNotes->updated_by = $current_user->id;
            $CasesNotes->save();
        }

        $CasesNotes = $this->loadCaseNote($case_id);
        $view = view('dashboard.cases.notes-list', compact('CasesNotes', 'current_user'))->render();

        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $return_id, 'view' =>  $view]);
    }

    public function deleteCaseNote(Request $request, $id)
    {
        $return_id = 0;
        $case_id = 0;
        $current_user = auth()->user();
        $view = null;

        $CasesNotes = CasesNotes::where('id', $id)->first();
        
        if($CasesNotes)
        {
            $date = new DateTime($CasesNotes->created_at);
            $diff = (new DateTime)->diff($date)->days;
    
            if ($diff > 7) {
                return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to delete the note that created more than 7 days']);
            }

            $case_id = $CasesNotes->case_id;
            $CasesNotes->status = 99;
            $CasesNotes->save();
        }


        $CasesNotes = $this->loadCaseNote($case_id);
        $view = view('dashboard.cases.notes-list', compact('CasesNotes', 'current_user'))->render();

        return response()->json(['status' => 1, 'data' => 'Notes deleted', 'return_id' => $return_id, 'view' =>  $view]);
    }

    public function loadCaseNote($id)
    {
        $CasesNotes = DB::table('cases_note as a')
        ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
        ->select('a.*', 'u.name as user_name', 'u.menuroles as menuroles')
        ->where('a.case_id', $id)->where('a.status', 1)
        ->orderBy('a.created_at', 'desc')->get();

        return $CasesNotes;
    }

}
