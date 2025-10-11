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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CaseArchieveController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $current_user = auth()->user();

        $old_pic = DB::table('cases_outside_system as c')
            ->join('users as u', 'u.id', '=', 'c.old_pic_id')
            ->select('old_pic_id', 'u.name')
            ->groupBy('old_pic_id')
            ->orderBy('u.name','asc')
            ->get();

        $new_pic = DB::table('cases_outside_system as c')
            ->join('users as u', 'u.id', '=', 'c.new_pic_id')
            ->select('new_pic_id', 'u.name')
            ->groupBy('new_pic_id')
            ->orderBy('u.name','asc')
            ->get();

        if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'account') {
            $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
            $totalAcount = DB::table('cases_outside_system')->where('status', '=', 1)->count();
            $totalAssigned = DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->count();
            $totalUpdated = DB::table('cases_outside_system')->where('remarks', '<>', '')->count();
        } else {
            $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
            $totalAcount = DB::table('cases_outside_system')->where('status', '=', 1)->where('old_pic_id', '=', $current_user->id)->count();
            $totalAssigned = DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->where('old_pic_id', '=', $current_user->id)->count();
            $totalUpdated = DB::table('cases_outside_system')->where('old_pic_id', '=', $current_user->id)->where('remarks', '<>', '')->count();
            $pendingCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 0)->count();
        }


        // $users = User::whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderby('menuroles', 'ASC')->orderby('name', 'DESC')->get();
        
        $users = User::whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->where('status','<>','99')->orderby('name', 'ASC')->get();
        $sales = User::where('status', '=', 1)->whereIn('menuroles', ['sales'])->orderby('menuroles', 'ASC')->orderby('name', 'DESC')->get();
        $lawyers = User::where('status', '=', 1)->whereIn('menuroles', ['lawyer'])->orderby('menuroles', 'ASC')->orderby('name', 'DESC')->get();


        return view('dashboard.case-archieve.index', [
            'totalAssigned' => $totalAssigned,
            'totalAcount' => $totalAcount,
            'users' => $users,
            'sales' => $sales,
            'lawyers' => $lawyers,
            'old_pic' => $old_pic,
            'new_pic' => $new_pic,
            'current_user' => $current_user,
            'totalUpdated' => $totalUpdated
        ]);
    }

    public function closedList()
    {

        $current_user = auth()->user();

        $old_pic = DB::table('cases_outside_system as c')
            ->join('users as u', 'u.id', '=', 'c.old_pic_id')
            ->select('old_pic_id', 'u.name')
            ->groupBy('old_pic_id')
            ->orderBy('u.name','asc')
            ->get();


        $new_pic = DB::table('cases_outside_system as c')
            ->join('users as u', 'u.id', '=', 'c.new_pic_id')
            ->select('new_pic_id', 'u.name')
            ->groupBy('new_pic_id')
            ->orderBy('u.name','asc')
            ->get();

        if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'account') {
            $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
            $totalAcount = DB::table('cases_outside_system')->where('status', '=', 1)->count();
            $totalAssigned = DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->count();
            $totalUpdated = DB::table('cases_outside_system')->where('remarks', '<>', '')->count();
        } else {
            $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
            $totalAcount = DB::table('cases_outside_system')->where('status', '=', 1)->where('old_pic_id', '=', $current_user->id)->count();
            $totalAssigned = DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->where('old_pic_id', '=', $current_user->id)->count();
            $totalUpdated = DB::table('cases_outside_system')->where('old_pic_id', '=', $current_user->id)->where('remarks', '<>', '')->count();
            $pendingCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 0)->count();
        }


        $users = User::where('status', '=', 1)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderby('menuroles', 'ASC')->orderby('name', 'DESC')->get();


        return view('dashboard.case-archieve.index-close', [
            'totalAssigned' => $totalAssigned,
            'totalAcount' => $totalAcount,
            'users' => $users,
            'old_pic' => $old_pic,
            'new_pic' => $new_pic,
            'current_user' => $current_user,
            'totalUpdated' => $totalUpdated
        ]);
    }

    public function pendingClosedList()
    {

        $current_user = auth()->user();

        $old_pic = DB::table('cases_outside_system as c')
            ->join('users as u', 'u.id', '=', 'c.old_pic_id')
            ->select('old_pic_id', 'u.name')
            ->groupBy('old_pic_id')
            ->orderBy('u.name','asc')
            ->get();


        $new_pic = DB::table('cases_outside_system as c')
            ->join('users as u', 'u.id', '=', 'c.new_pic_id')
            ->select('new_pic_id', 'u.name')
            ->groupBy('new_pic_id')
            ->orderBy('u.name','asc')
            ->get();

        if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'account') {
            $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
            $totalAcount = DB::table('cases_outside_system')->where('status', '=', 1)->count();
            $totalAssigned = DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->count();
            $totalUpdated = DB::table('cases_outside_system')->where('remarks', '<>', '')->count();
        } else {
            $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
            $totalAcount = DB::table('cases_outside_system')->where('status', '=', 1)->where('old_pic_id', '=', $current_user->id)->count();
            $totalAssigned = DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->where('old_pic_id', '=', $current_user->id)->count();
            $totalUpdated = DB::table('cases_outside_system')->where('old_pic_id', '=', $current_user->id)->where('remarks', '<>', '')->count();
            $pendingCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 0)->count();
        }


        $users = User::where('status', '=', 1)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderby('menuroles', 'ASC')->orderby('name', 'DESC')->get();


        return view('dashboard.case-archieve.index-pending', [
            'totalAssigned' => $totalAssigned,
            'totalAcount' => $totalAcount,
            'users' => $users,
            'old_pic' => $old_pic,
            'new_pic' => $new_pic,
            'current_user' => $current_user,
            'totalUpdated' => $totalUpdated
        ]);
    }

    public function getCaseList(Request $request, $status)
    {
        if ($request->ajax()) {

            $bln_status = 1;
            $user_id = 0;




            $bln_status = $status;

            return $bln_status;

            $current_user = auth()->user();

            // $Adjudication = DB::table('adjudication as a')
            // ->join('loan_case as l', 'l.id', '=', 'a.case_id')
            // ->join('client as c', 'c.id', '=', 'a.client_id')
            // ->join('users as u', 'u.id', '=', 'a.created_by')
            // ->select('a.*', 'c.name as client_name', 'l.case_ref_no', 'u.name as assign_by')
            // ->where('a.status', '<>', 99)
            // ->get();

            if (in_array($current_user->menuroles,['manager','admin', 'account','sales'])){

                if ($user_id == 0) {
                    $Adjudication = DB::table('cases_outside_system as c')
                        ->join('users as u', 'u.id', '=', 'c.old_pic_id')
                        ->leftJoin('users as u1', 'u1.id', '=', 'c.new_pic_id')
                        ->select('c.*', 'u.name as old_pic', 'u1.name as new_pic')
                        ->where('c.status', '=', $bln_status)
                        ->get();
                } else {
                    $Adjudication = DB::table('cases_outside_system as c')
                        ->join('users as u', 'u.id', '=', 'c.old_pic_id')
                        ->leftJoin('users as u1', 'u1.id', '=', 'c.new_pic_id')
                        ->select('c.*', 'u.name as old_pic', 'u1.name as new_pic')
                        ->where('c.status', '=', $bln_status)
                        ->where('c.old_pic_id', '=', $user_id)
                        ->get();
                }
            } else {
                $Adjudication = DB::table('cases_outside_system as c')
                    ->join('users as u', 'u.id', '=', 'c.old_pic_id')
                    ->leftJoin('users as u1', 'u1.id', '=', 'c.new_pic_id')
                    ->select('c.*', 'u.name as old_pic', 'u1.name as new_pic')
                    // ->where('c.status', '=', $bln_status)
                    // ->where('c.old_pic_id', '=', $current_user->id)
                    // ->orWhere('c.new_pic_id', '=', $current_user->id)
                    ->where('c.sales_id', '=', 29);

                    // if ($current_user->id == 29)
                    // {
                    //     $Adjudication= $Adjudication->orWhere('c.new_pic_id', '=', $current_user->id);
                    // }
                    $Adjudication= $Adjudication->get();
            }



            return DataTables::of($Adjudication, $bln_status)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($bln_status) {


                    $current_user = auth()->user();
                    $transfer_button = '';
                    $avai_status_button = '';

                    if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin') {
                        $transfer_button = ' <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ')" data-toggle="modal" data-target="#modalTransfer"><i class="cil-transfer"></i>  Transfer case</a>
                        <div class="dropdown-divider"></div>';
                    }

                    if ($bln_status == 1) {
                        $avai_status_button = '<a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="getValue(' . $row->id . ')" data-toggle="modal" data-target="#modalStatus"><i class="cil-pencil"></i>  Update status</a>
                        <div class="dropdown-divider"></div>
                        ' . $transfer_button . '
                      <a class="dropdown-item" href="javascript:void(0)" onclick="closeCase(' . $row->id . ')"><i class="cil-badge"></i> Close case</a>
                        ';
                    }

                    $actionBtn = '<div class="btn-group">
                    <button type="button" class="btn btn-warning btn-flat">Action</button>
                    <button type="button" class="btn btn-warning btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                      
                      <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="completionDateModal(' . $row->id . ')" data-toggle="modal" data-target="#modalCompletionDate"><i class="cil-calendar-check"></i>  Update completion date</a>
                      <div class="dropdown-divider"></div>
                      ' . $avai_status_button . '
                      
                    </div>
                  </div>
                    ';


                    return $actionBtn;
                })
                ->editColumn('client', function ($row) {
                    $actionBtn = '<b>Client (P): </b>' . $row->client_name_p.'<br/><b>Client (C): </b>' . $row->client_name_c.'<br/>' ;

                    return $actionBtn;
                })
                ->editColumn('remarks', function ($row) {
                    $actionBtn = ' <div id="remark_' . $row->id . '">' . $row->remarks . '</div>';

                    return $actionBtn;
                })
                ->editColumn('new_pic', function ($row) {
                    $actionBtn = ' <div id="new_pic_id_' . $row->id . '">' . $row->new_pic . '</div>';

                    return $actionBtn;
                })
                ->editColumn('completion_date', function ($row) {
                    $actionBtn = ' <div id="completion_date_' . $row->id . '">' . $row->completion_date . '</div>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'remarks', 'new_pic', 'completion_date','client'])
                ->make(true);
        }
    }

    public function getOpenCaseList(Request $request, $user_id, $new_pic, $lawyer)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $Adjudication = DB::table('cases_outside_system as c')
                ->leftJoin('users as u', 'u.id', '=', 'c.old_pic_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'c.new_pic_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'c.sales_id')
                ->leftJoin('users as u3', 'u3.id', '=', 'c.lawyer_id')
                ->select('c.*', 'u.name as old_pic', 'u1.name as new_pic', 'u2.name as sales_user', 'u3.name as lawyer_name',
                'u.branch_id as old_pic_branch', 'u1.branch_id as new_pic_branch', 'u2.branch_id as sales_user_branch', 'u3.branch_id as lawyer_branch')
                ->where('c.status', '=', 1);

                
            if (in_array($current_user->menuroles,['manager','admin', 'sales'])){
                if ($user_id <> 0 || $new_pic <> 0 || $lawyer <> 0) 
                {
                    if ($user_id <> 0) {
                        $Adjudication->where('c.old_pic_id', '=', $user_id);
                    }
    
                    if ($new_pic <> 0) {
                        $Adjudication->where('c.new_pic_id', '=', $new_pic);
                       
                    }

                    
                    if ($lawyer <> 0) {
                        $Adjudication->where('c.lawyer_id', '=', $lawyer);
                       
                    }

                }
                else
                {
                    if ($current_user->menuroles == 'sales')
                    {
                        if ($current_user->id == 51) {
                            $Adjudication->where(function ($q) use ($current_user) {
                                $q->where('c.old_pic_id', '=', $current_user->id)
                                    ->orWhere('c.new_pic_id', '=', $current_user->id)
                                    ->orWhere('c.lawyer_id', '=', $current_user->id)
                                    ->orWhere('c.sales_id', '=', 32);
                            });
                        }
                        else if ($current_user->id == 29) {
                            $Adjudication->where(function ($q) use ($current_user) {
                                $q->where('c.old_pic_id', '=', $current_user->id)
                                    ->orWhere('c.new_pic_id', '=', $current_user->id)
                                    ->orWhere('c.lawyer_id', '=', $current_user->id)
                                    ->orWhere('c.sales_id', '=', 3)
                                    ->orWhere('c.sales_id', '=', 29)
                                    ->orWhere('c.id', '=', 502);
                            });
                        }
                        else
                        {
                            $Adjudication->where(function ($q) use ($current_user) {
                                $q->Where('c.old_pic_id', '=', $current_user->id)
                                    ->orWhere('c.new_pic_id', '=', $current_user->id)
                                    ->orWhere('c.lawyer_id', '=', $current_user->id)
                                    ->orWhere('c.sales_id', '=', $current_user->id);
                            });
    
                            // $Adjudication->where('c.new_pic_id', '=', 47);
                        }
                    }
                }

                
            } else {
                
                if ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer' || $current_user->menuroles == 'chambering'  || $current_user->menuroles == 'sales') {

                  

                    if ($current_user->id == 51) {
                        $Adjudication->where(function ($q) use ($current_user) {
                            $q->where('c.old_pic_id', '=', $current_user->id)
                                ->orWhere('c.new_pic_id', '=', $current_user->id)
                                ->orWhere('c.lawyer_id', '=', $current_user->id)
                                ->orWhere('c.sales_id', '=', 32);
                        });
                    }else if ($current_user->id == 14) {
                        // $case_list = $case_list->whereIn('l.lawyer_id', [63, 79]);
                    }
                    else if ($current_user->id == 29) {
                        $Adjudication->where(function ($q) use ($current_user) {
                            $q->where('c.old_pic_id', '=', $current_user->id)
                                ->orWhere('c.new_pic_id', '=', $current_user->id)
                                ->orWhere('c.lawyer_id', '=', $current_user->id)
                                ->orWhere('c.sales_id', '=', 3)
                                ->orWhere('c.sales_id', '=', 29);
                        });
                    }
                    else
                    {
                        $Adjudication->where(function ($q) use ($current_user) {
                            $q->Where('c.old_pic_id', '=', $current_user->id)
                                ->orWhere('c.new_pic_id', '=', $current_user->id)
                                ->orWhere('c.lawyer_id', '=', $current_user->id)
                                ->orWhere('c.sales_id', '=', $current_user->id);
                        });

                        // $Adjudication->where('c.new_pic_id', '=', 47);
                    }
                }
                else if (in_array($current_user->menuroles,['receptionist'])){
                    $Adjudication->where(function ($q) use ($current_user) {
                        $q->Where('u.branch_id', '=', $current_user->branch_id)
                            ->orWhere('u1.branch_id', '=', $current_user->branch_id)
                            ->orWhere('u2.branch_id', '=', $current_user->branch_id)
                            ->orWhere('u3.branch_id', '=', $current_user->branch_id);
                    });
                }
                else if (in_array($current_user->menuroles,['maker'])){
                    $Adjudication->where(function ($q) use ($current_user) {
                        $q->Where('u.branch_id', '=', $current_user->branch_id)
                            ->orWhere('u1.branch_id', '=', $current_user->branch_id)
                            ->orWhere('u2.branch_id', '=', $current_user->branch_id)
                            ->orWhere('u3.branch_id', '=', $current_user->branch_id);
                    });
                }


                // if ($current_user->menuroles == 'clerk') 
                // {
                //     $Adjudication->where('c.new_pic_id', '=', $current_user->id);
                // }
            }


            $Adjudication = $Adjudication->get();



            return DataTables::of($Adjudication)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {


                    $current_user = auth()->user();
                    $transfer_button = '';
                    $avai_status_button = '';
                    
                    if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin'|| $current_user->id == 14) {
                        $transfer_button = ' <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ')" data-toggle="modal" data-target="#modalTransfer"><i class="cil-transfer"></i>  Transfer case</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)"  onclick="moveCaseToPerfectionCase(' . $row->id . ')" ><i class="cil-transfer"></i>Move to Perfection</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="AssignSalesModal(' . $row->id . ')" data-toggle="modal" data-target="#AssignSalesModal"><i class="cil-transfer"></i>Asign Sales</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="AssignLawyerModal(' . $row->id . ')" data-toggle="modal" data-target="#AssignLawyerModal"><i class="cil-transfer"></i>Asign Lawyer</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="pendingCloseCase(' . $row->id . ')"><i class="cil-badge"></i> Pending Close</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="closeCase(' . $row->id . ')"><i class="cil-badge"></i> Close case</a>
                        ';
                    }

                    $avai_status_button = '<a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="getValue(' . $row->id . ')" data-toggle="modal" data-target="#modalStatus"><i class="cil-pencil"></i>  Update status</a>
                        <div class="dropdown-divider"></div>
                        ' . $transfer_button . '
                     
                        ';

                    $actionBtn = '<div class="btn-group">
                    <button type="button" class="btn btn-warning btn-flat">Action</button>
                    <button type="button" class="btn btn-warning btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                      
                      <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="completionDateModal(' . $row->id . ')" data-toggle="modal" data-target="#modalCompletionDate"><i class="cil-calendar-check"></i>  Update completion date</a>
                      <div class="dropdown-divider"></div>
                      ' . $avai_status_button . '
                      
                    </div>
                  </div>
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
                ->editColumn('new_pic', function ($row) {
                    $actionBtn = ' <div id="new_pic_id_' . $row->id . '">' . $row->new_pic . '</div>';

                    return $actionBtn;
                })
                ->editColumn('sales_id', function ($row) {
                    $actionBtn = ' <div id="sales_id_' . $row->id . '">' . $row->sales_user . '</div>';

                    return $actionBtn;
                })
                ->editColumn('lawyer_id', function ($row) {
                    $actionBtn = ' <div id="lawyer_id_' . $row->id . '">' . $row->lawyer_name . '</div>';

                    return $actionBtn;
                })
                ->editColumn('completion_date', function ($row) {
                    $actionBtn = ' <div id="completion_date_' . $row->id . '">' . $row->completion_date . '</div>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'remarks', 'new_pic', 'sales_id', 'lawyer_id', 'completion_date','client'])
                ->make(true);
        }
    }

    public function getClosedCaseList(Request $request, $user_id, $new_pic)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $Adjudication = DB::table('cases_outside_system as c')
                ->leftJoin('users as u', 'u.id', '=', 'c.old_pic_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'c.new_pic_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'c.sales_id')
                ->select('c.*', 'u.name as old_pic', 'u1.name as new_pic', 'u2.name as sales_user')
                ->where('c.status', '=', 2);

            if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'management') {
                if ($user_id <> 0) {
                    $Adjudication->where('c.old_pic_id', '=', $user_id);
                }

                if ($new_pic <> 0) {
                    $Adjudication->where('c.new_pic_id', '=', $new_pic);
                }
            } else {
                if ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer' || $current_user->menuroles == 'chambering') {
                    

                        if ($current_user->id == 14) {
                            // $case_list = $case_list->whereIn('l.lawyer_id', [63, 79]);
                        }
                        else
                        {
                            $Adjudication->where('c.old_pic_id', '=', $current_user->id)
                            ->orWhere('c.new_pic_id', '=', $current_user->id);
                        }
                }
            }

            $Adjudication->where('c.status', '=', 2);

            if ($current_user->menuroles == 'sales') {
                $Adjudication->where('c.sales_id', '=', $current_user->id);
            }

            $Adjudication = $Adjudication->get();

            return DataTables::of($Adjudication)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {


                    $current_user = auth()->user();
                    $transfer_button = '';

                    if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin') {
                        $transfer_button = ' <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ')" data-toggle="modal" data-target="#modalTransfer"><i class="cil-transfer"></i>  Transfer case</a>
                        <div class="dropdown-divider"></div>';
                    }

                    $avai_status_button = '<a class="dropdown-item" href="javascript:void(0)" onclick="closeCase(' . $row->id . ')"><i class="cil-badge"></i> Close case</a>
                    ';

                    $actionBtn = '<div class="btn-group">
                    <button type="button" class="btn btn-warning btn-flat">Action</button>
                    <button type="button" class="btn btn-warning btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu"><a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="getValue(' . $row->id . ')" data-toggle="modal" data-target="#modalStatus"><i class="cil-pencil"></i>  Update status</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="completionDateModal(' . $row->id . ')" data-toggle="modal" data-target="#modalCompletionDate"><i class="cil-calendar-check"></i>  Update completion date</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="closeCase(' . $row->id . ')"><i class="cil-badge"></i> Reopen case</a>
                    </div>
                  </div>
                    ';


                    return $actionBtn;
                })
                ->editColumn('remarks', function ($row) {
                    $actionBtn = ' <div id="remark_' . $row->id . '">' . $row->remarks . '</div>';

                    return $actionBtn;
                })
                ->editColumn('new_pic', function ($row) {
                    $actionBtn = ' <div id="new_pic_id_' . $row->id . '">' . $row->new_pic . '</div>';

                    return $actionBtn;
                })
                ->editColumn('sales_id', function ($row) {
                    $actionBtn = ' <div id="sales_id_' . $row->id . '">' . $row->sales_user . '</div>';

                    return $actionBtn;
                })
                ->editColumn('completion_date', function ($row) {
                    $actionBtn = ' <div id="completion_date_' . $row->id . '">' . $row->completion_date . '</div>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'remarks', 'new_pic', 'sales_id', 'completion_date'])
                ->make(true);
        }
    }

    public function getPendingCloseCaseList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $Adjudication = DB::table('cases_outside_system as c')
                ->leftJoin('users as u', 'u.id', '=', 'c.old_pic_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'c.new_pic_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'c.sales_id')
                ->select('c.*', 'u.name as old_pic', 'u1.name as new_pic', 'u2.name as sales_user')
                ->where('c.status', '=', $request->input('status'));



            if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'management') {
                if ($request->input('userID') <> 0) {
                    $Adjudication->where('c.old_pic_id', '=', $request->input('userID'));
                }

                if ($request->input('newPIC')  <> 0) {
                    $Adjudication->where('c.new_pic_id', '=', $request->input('newPIC'));
                }
            } else {
                if ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer' || $current_user->menuroles == 'chambering') {
                   

                        if ($current_user->id == 14) {
                            // $case_list = $case_list->whereIn('l.lawyer_id', [63, 79]);
                        }
                        else
                        {
                            $Adjudication->where('c.old_pic_id', '=', $current_user->id)
                        ->orWhere('c.new_pic_id', '=', $current_user->id);
                        }
                }
            }

            $Adjudication->where('c.status', '=', 3);

            if ($current_user->menuroles == 'sales') {
                $Adjudication->where('c.sales_id', '=', $current_user->id);
            }

            $Adjudication = $Adjudication->get();

            return DataTables::of($Adjudication)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {


                    $current_user = auth()->user();
                    $transfer_button = '';

                    if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin') {
                        $transfer_button = ' <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ')" data-toggle="modal" data-target="#modalTransfer"><i class="cil-transfer"></i>  Transfer case</a>
                        <div class="dropdown-divider"></div>';
                    }

                    $avai_status_button = '<a class="dropdown-item" href="javascript:void(0)" onclick="closeCase(' . $row->id . ')"><i class="cil-badge"></i> Close case</a>
                    ';

                    $actionBtn = '<div class="btn-group">
                    <button type="button" class="btn btn-warning btn-flat">Action</button>
                    <button type="button" class="btn btn-warning btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="completionDateModal(' . $row->id . ')" data-toggle="modal" data-target="#modalCompletionDate"><i class="cil-calendar-check"></i>  Update completion date</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="reopenCase(' . $row->id . ')"><i class="cil-badge"></i> Reopen case</a>          
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)"  onclick="moveCaseToPerfectionCase(' . $row->id . ')" ><i class="cil-transfer"></i>Move to Perfection</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="closeCase(' . $row->id . ')"><i class="cil-badge"></i> Close case</a>
                    </div>
                  </div>
                    ';


                    return $actionBtn;
                })
                ->editColumn('remarks', function ($row) {
                    $actionBtn = ' <div id="remark_' . $row->id . '">' . $row->remarks . '</div>';

                    return $actionBtn;
                })
                ->editColumn('new_pic', function ($row) {
                    $actionBtn = ' <div id="new_pic_id_' . $row->id . '">' . $row->new_pic . '</div>';

                    return $actionBtn;
                })
                ->editColumn('sales_id', function ($row) {
                    $actionBtn = ' <div id="sales_id_' . $row->id . '">' . $row->sales_user . '</div>';

                    return $actionBtn;
                })
                ->editColumn('completion_date', function ($row) {
                    $actionBtn = ' <div id="completion_date_' . $row->id . '">' . $row->completion_date . '</div>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'remarks', 'new_pic', 'sales_id', 'completion_date'])
                ->make(true);
        }
    }

    public function pendingCloseArchieveCase(Request $request, $id)
    {


        $current_user = auth()->user();


        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->status =  3;
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->updated_by = $current_user->id;
        $CaseArchive->save();

        return response()->json(['status' => 1, 'data' => 'Case pending closed']);
    }

    public function closeArchieveCase(Request $request, $id)
    {


        $current_user = auth()->user();


        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->status =  2;
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->updated_by = $current_user->id;
        $CaseArchive->save();

        return response()->json(['status' => 1, 'data' => 'Case closed']);
    }

    public function reopenArchieveCase(Request $request, $id)
    {


        $current_user = auth()->user();


        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->status =  1;
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->updated_by = $current_user->id;
        $CaseArchive->save();

        return response()->json(['status' => 1, 'data' => 'Case closed']);
    }

    public function updateArchieveCaseCompletionDate(Request $request, $id)
    {


        $current_user = auth()->user();


        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->completion_date =  $request->input('completion_date');
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->updated_by = $current_user->id;
        $CaseArchive->save();

        return response()->json(['status' => 1, 'data' => 'Case closed']);
    }

    public function updateArchieveCaseRemark(Request $request, $id)
    {

        $original_text = '';
        $current_user = auth()->user();


        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $original_text = $CaseArchive->remarks;

        $CaseArchive->remarks =  $request->input('status');
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->updated_by = $current_user->id;
        $CaseArchive->save();


        $CaseActivityLog  = new CaseActivityLog();

        $CaseActivityLog->user_id = $current_user->id;
        $CaseActivityLog->case_id = $id;
        $CaseActivityLog->ori_text = $original_text;
        $CaseActivityLog->edit_text = $request->input('status');
        $CaseActivityLog->action = 'UPDATE';
        $CaseActivityLog->desc = $current_user->name . ' updated case status';
        $CaseActivityLog->status = 1;
        $CaseActivityLog->created_at = date('Y-m-d H:i:s');
        $CaseActivityLog->save();



        return response()->json(['status' => 1, 'data' => 'Notes updated']);
    }

    public function TransferCase(Request $request, $id)
    {


        $current_user = auth()->user();

        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->new_pic_id =  $request->input('new_pic_id');
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->assigned_by = $current_user->id;
        $CaseArchive->save();



        return response()->json(['status' => 1, 'data' => 'Notes updated']);
    }

    public function moveCaseToPerfectionCase(Request $request, $id)
    {

        $current_user = auth()->user();

        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $Cases  = new Cases();

        $Cases->ref_no = $CaseArchive->ref_no;
        $Cases->client_name_p = $CaseArchive->client_name_p;
        $Cases->client_name_v = $CaseArchive->client_name_v;
        $Cases->sales_id = $CaseArchive->sales_id;
        $Cases->branch_id = $CaseArchive->branch_id;
        $Cases->case_date = $CaseArchive->case_date;
        $Cases->status = $CaseArchive->status;
        $Cases->case_type = 1;
        $Cases->completion_date = $CaseArchive->completion_date;
        $Cases->created_at = $CaseArchive->created_at;
        $Cases->created_by = $CaseArchive->created_by;
        $Cases->save();

        if ($Cases)
        {
            if($CaseArchive->remarks)
            {
                $CasesNotes  = new CasesNotes();

                $CasesNotes->case_id =  $Cases->id;
                $CasesNotes->notes =  $CaseArchive->remarks;
                $CasesNotes->label =  '';
                $CasesNotes->created_at = $CaseArchive->updated_at;
                $CasesNotes->created_by = $CaseArchive->updated_by;
                $CasesNotes->save();
            }

            if($CaseArchive->lawyer_id != null && $CaseArchive->lawyer_id != 0)
            {
                $CasesPIC = new CasesPIC();

                $CasesPIC->case_id =  $Cases->id;
                $CasesPIC->pic_id =  $CaseArchive->lawyer_id;
                $CasesPIC->created_at = date('Y-m-d H:i:s');
                $CasesPIC->assigned_by = $current_user->id;
                $CasesPIC->save();
                
            }

            if($CaseArchive->old_pic_id != null && $CaseArchive->old_pic_id != 0)
            {
                $CasesPIC = CasesPIC::where('pic_id', $CaseArchive->old_pic_id)->where('case_id', $Cases->id)->where('status', 1)->first();

                if (!$CasesPIC)
                {
                    $CasesPIC = new CasesPIC();

                    $CasesPIC->case_id =  $Cases->id;
                    $CasesPIC->pic_id =  $CaseArchive->old_pic_id;
                    $CasesPIC->created_at = date('Y-m-d H:i:s');
                    $CasesPIC->assigned_by = $current_user->id;
                    $CasesPIC->save();
                }

                
            }

            if($CaseArchive->new_pic_id != null && $CaseArchive->new_pic_id != 0)
            {
                $CasesPIC = CasesPIC::where('pic_id', $CaseArchive->new_pic_id)->where('case_id', $Cases->id)->where('status', 1)->first();

                if (!$CasesPIC)
                {
                    $CasesPIC = new CasesPIC();

                    $CasesPIC->case_id =  $Cases->id;
                    $CasesPIC->pic_id =  $CaseArchive->new_pic_id;
                    $CasesPIC->created_at = date('Y-m-d H:i:s');
                    $CasesPIC->assigned_by = $current_user->id;
                    $CasesPIC->save();
                }
            }

            $CaseArchive->status =  99;
            $CaseArchive->updated_at = date('Y-m-d H:i:s');
            $CaseArchive->assigned_by = $current_user->id;
            $CaseArchive->save();
        }

        $CaseActivityLog  = new CaseActivityLog();

        $CaseActivityLog->user_id = $current_user->id;
        $CaseActivityLog->case_id = $id;
        $CaseActivityLog->ori_text = '';
        $CaseActivityLog->edit_text = '';
        $CaseActivityLog->action = 'UPDATE';
        $CaseActivityLog->desc = $current_user->name . ' Moved file ('.$CaseArchive->ref_no.') to perfection';
        $CaseActivityLog->status = 1;
        $CaseActivityLog->created_at = date('Y-m-d H:i:s');
        $CaseActivityLog->object_id = $CaseArchive->id;
        $CaseActivityLog->save();


        return response()->json(['status' => 1, 'data' => 'Notes updated']);
    }

    public function AssignSales(Request $request, $id)
    {


        $current_user = auth()->user();

        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->sales_id =  $request->input('sales_id');
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->save();

        return response()->json(['status' => 1, 'data' => 'Notes updated']);
    }

    public function AssignLawyer(Request $request, $id)
    {


        $current_user = auth()->user();

        $CaseArchive = CaseArchive::where('id', '=', $id)->first();

        $CaseArchive->lawyer_id =  $request->input('lawyer_id');
        $CaseArchive->updated_at = date('Y-m-d H:i:s');
        $CaseArchive->save();

        return response()->json(['status' => 1, 'data' => 'Notes updated']);
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

        return view('dashboard.adjudication.create', [
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

        $Adjudication  = new Adjudication();

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
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

        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '=', 1)
            ->get();


        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();

        return view('dashboard.adjudication.edit', [
            'loan_case' => $loan_case,
            'branch' => $branch,
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
