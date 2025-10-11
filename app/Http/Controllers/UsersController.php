<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\User;
use App\Models\Status;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;
use App\Http\Helper\Helper;
use App\Models\Branch;
use App\Models\LoanCase;
use App\Models\LoanCaseChecklistDetails;
use App\Models\Menurole;
use App\Models\Portfolio;
use App\Models\TeamPortfolios;
use App\Models\UserAccessControl;
use App\Models\UserKpiHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Contracts\Role;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('admin');

        
        
    }

    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (AccessController::UserAccessPermissionController(PermissionController::ManageUserPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $you = auth()->user();
        $current_user = auth()->user();

        if ($you->menuroles == 'admin') {
            $users = DB::table('users')->paginate(10);
            $roles = DB::table('roles')->whereNotIn('id', ['2', '3'])->get();
        }
        else  if ($you->menuroles == 'management') {
            $users = DB::table('users')->whereNotIn('menuroles', ['admin'])->paginate(10);
            $roles = DB::table('roles')->whereNotIn('id', ['1', '2', '3'])->get();
        }else {
            $users = DB::table('users')->whereNotIn('menuroles', ['admin'])->paginate(10);
            $roles = DB::table('roles')->whereNotIn('id', ['1', '2', '3'])->get();
        }

        $branchInfo = BranchController::manageBranchAccess();
        $branchList = $branchInfo['branch'];

        return view('dashboard.admin.usersList', compact('users', 'you', 'roles', 'branchList', 'current_user'));
    }

    public function getStaffList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $users = DB::table('users');

            if ($request->input('role')) {
                if ($request->input('role') <> '') {

                    $users->where('menuroles',  $request->input('role'));
                }
            }

            

            if ( $request->input('status') !== null) {
                
                if ($request->input('status') != 1) {
                    $users->where('status', '<>', 1);
                }
                else
                {
                    $users->where('status',  $request->input('status'));
                }
            }

            if ($request->input('branch')) {
                if ($request->input('branch') <> '') {

                    $users->where('branch_id',  $request->input('branch'));
                }
            }

            if (!in_array($current_user->id,[1]))
            {
                if (!in_array($current_user->id,[2,3]))
                {
                    $users->where('branch_id',  [$current_user->branch_id])->whereNotIn('id',  [1,2,3]);
                }
                else
                {
                    $users->whereNotIn('id',  [1]);
                }
            }
            

            $users = $users->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <div class="btn-group  normal-edit-mode" >
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info"   href="/reset-password/' . $row->id . '"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-airplay"></i>Reset Password</a>
                      <a class="dropdown-item btn-success"   href="/users/' . $row->id . '/edit"" style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                     
                      ';

                    //   <a class="dropdown-item btn-info" target="_blank"   href="/users/' . $row->id . '"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-airplay"></i>Details</a>
                      
                    return $actionBtn;
                })
                ->editColumn('status', function ($data) {
                    if (in_array($data->status,[0,99]))
                        return '<span class="label bg-danger">Inactive</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Active</span>';
                    else
                        return '<span class="label bg-danger">Inactive</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function kpiList(Request $request, $id)
    {
        $users = User::where('id', '=', $id)->first();

        return view('dashboard.admin.usersKpiList', compact('users'));
    }

    public function getUserKPIList(Request $request, $id)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            // $UserKpiHistory = UserKpiHistory::where('user_id', '=', $id)->where('point', '=', -1)->get();
            $LoanCaseChecklistDetails = LoanCaseChecklistDetails::where('pic_id', '=', $id)->where('status', '=', 0)->get();


            $LoanCaseChecklistDetails = DB::table('loan_case_checklist_details as d')
                ->join('loan_case as l', 'l.id', '=', 'd.case_id')
                ->select('d.*', 'l.case_ref_no')
                ->where('d.pic_id', '=',  $id)
                ->where('d.status', '=',  0)
                ->where('kpi', '=',  1)
                ->whereDate('d.target_close_date', '<', Carbon::now())
                ->orderBy('d.target_close_date', 'ASC')
                ->get();

            $LoanCaseChecklistDetails = DB::table('loan_case_checklist_details as d')
                ->join('loan_case as l', 'l.id', '=', 'd.case_id')
                ->select('d.*', 'l.case_ref_no')
                ->where('d.pic_id', '=',  $id)
                ->where('d.status', '=',  1)
                ->where('kpi', '=',  1)
                ->whereDate('d.target_close_date', '<', Carbon::now())
                ->orderBy('d.target_close_date', 'ASC')
                ->get();



            return DataTables::of($LoanCaseChecklistDetails)
                ->addIndexColumn()
                ->addColumn('case_link', function ($row) {
                    // $actionBtn = '<a href="/referral/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>';
                    // return $actionBtn;

                    return "<a href='/users/" . $row->id . "'>" . $row->id . "</a>";
                })
                ->editColumn('status', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-danger">Overdue</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-chevron-double-right"></i></a>
                   ';
                    return $actionBtn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function filter(Request $request)
    {
        $you = auth()->user();

        if ($request->input('role') == "0") {
            if ($you->menuroles == 'admin') {
                $users = DB::table('users')->paginate(10);
            } else {
                $users = DB::table('users')->whereNotIn('menuroles', ['admin'])->paginate(10);
            }
        } else {
            $users = DB::table('users')->where('menuroles', $request->input('role'))->paginate(10);
        }

        return response()->json([
            'view' => view('dashboard.admin.table.tbl-list', compact('users'))->render()
        ]);

        // return  $users;
    }

    public function create()
    {
        $you = auth()->user();
        $current_user = auth()->user();
        $LinkCaseUser = [];
        $SpecialCaseAccess = [];

        if (AccessController::UserAccessPermissionController(PermissionController::ManageUserPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $myrole = Roles::where('name', $you->menuroles)->first();

        if($current_user->id == 1)
        {
            $roles = DB::table('roles')->where('status', 1)->get();
        }
        else if(in_array($current_user->id,[2,3]))
        {
            $roles = DB::table('roles')->where('hierarchy', '>', 2)->get();
        }
        else
        {
            $roles = DB::table('roles')->where('hierarchy', '>=', $myrole->hierarchy)->get();
        }
        

        $branchInfo = BranchController::manageBranchAccess();
        $branchList = $branchInfo['branch'];

        $UserAccessControl = UserAccessControl::where('show_in_menu', 1)->get();
        $Portfolio = Portfolio::where('status', 1)->orderBy('name','ASC')->get();

        if (!in_array($current_user->id,[1]))
        {
            if (!in_array($current_user->id,[2,3]))
            {
                // $users->where('branch_id',  [$current_user->branch_id]);
                $linkUser = User::where('branch_id',  $current_user->branch_id)->get();
            }
            else
            {
                // $users->whereNotIn('id',  [1]);
                $linkUser = User::whereNotIn('id',  [1])->get();
            }
        }
        else
        {
            $linkUser = User::where('status', 1)->get();
        }

        // $userRoleId = Roles::where('name',$user->menuroles)->first();
        $UserAccessControl = UserAccessControl::where('show_in_menu', 1)->where('hierarchy', 0)->orWhere('hierarchy', $myrole->hierarchy)->get();
        $UserAccessControlType = UserAccessControl::where('show_in_menu', 1)->where('type_name', '!=',null )->where('hierarchy', 0)->orWhere('hierarchy', $myrole->hierarchy)->distinct()
        ->select('type_name')->get();

        return view('dashboard.admin.create', ['roles' => $roles, 'branchList' => $branchList, 'UserAccessControl' => $UserAccessControl, 'LinkCaseUser' => $LinkCaseUser, 
        'Portfolio' => $Portfolio, 'Portfolio' => $Portfolio, 'linkUser' => $linkUser, 'UserAccessControlType' => $UserAccessControlType, 'current_user' => $current_user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $userRoles = Auth::user()->getRoleNames();

        $roleName = 'lawyer_id';

        if ($userRoles == "lawyer") {
            $roleName = 'lawyer_id';
        } else if ($userRoles == "clerk") {
            $roleName = 'clerk_id';
        } else if ($userRoles == "sales") {
            $roleName = 'sales_user_id';
        }

        // get case count
        $InProgressCaseCount = DB::table('loan_case')->whereIn('status', [1,2,3])->where($roleName, '=', $id)->count();
        $openCaseCount = DB::table('loan_case')->where('status', '=', 2)->where($roleName, '=', $id)->count();
        $closedCaseCount = DB::table('loan_case')->where('status', '=', 0)->where($roleName, '=', $id)->count();
        $OverdueCaseCount = DB::table('loan_case')->where('status', 4)->where($roleName, '=', $id)->count();


        $portfolios = Portfolio::where('status', 1)->get();
        $TeamPortfolios = TeamPortfolios::where('user_id', $id)->where('status', 1)->pluck('portfolio_id')->toArray();

        

        return view('dashboard.admin.userShow',   [
            'user' => $user,
            'InProgressCaseCount' => $InProgressCaseCount,
            'openCaseCount' => $openCaseCount,
            'closedCaseCount' => $closedCaseCount,
            'OverdueCaseCount' => $OverdueCaseCount,
            'portfolios' => $portfolios,
            'TeamPortfolios' => $TeamPortfolios,
        ]);
    }

    public function store(Request $request)
    {
        // return  $request;
        $branch_case = [];

        

        // TeamPortfolios::where('user_id', $user_id)->delete();

        $validatedData = $request->validate([
            'name'       => 'required|min:1|max:256',
            'email'      => 'required|email|max:256'
        ]);

        if($request->input('branch_case') != null)
        {
            $branch_case = json_encode($request->input('branch_case'));
        }
        else
        {
            $branch_case = json_encode($branch_case);
        }
        

        $user = auth()->user();
        $users = new User();
        $users->email = $request->input('email');
        $users->name = $request->input('name');
        $users->ic_name = $request->input('ic_name');
        $users->phone_no = $request->input('phone_no');
        $users->office_no = $request->input('office_no');
        $users->max_files = $request->input('max_file');
        $users->min_files = $request->input('min_file');
        $users->menuroles = $request->input('role');
        $users->nick_name = $request->input('nick_name');
        $users->race = $request->input('race');
        $users->bc_no = $request->input('bc_no');
        $users->branch_id = $request->input('branch_id');
        $user->handle_branch = $request->input('branch_id');
        $users->branch_case = $branch_case;
        $users->link_user_case = $request->input('link_case_user');
        $user->is_sales = $request->input('is_sales');
        $users->status = 1;
        // $users->password = Hash::make($request->input('password'));
        $users->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        // $user->assignRole($request->input('role'));

        $user_email = User::where('email', '=', $request->input('email'))->first();

        if ($user_email) {
            $request->session()->flash('error', 'Email exist in the system');
            return redirect()->route('users.create');
        }

        // $user_name = $request->input('name');
        // $nick_name = '';

        // $nickName  = $this->generateNickName($user_name, $nick_name);

        // if (User::where('nick_name', '=', $nickName)) {
        //     $int = (int) filter_var($nickName, FILTER_SANITIZE_NUMBER_INT);
        //     $nickName = str_replace($int, '', $nickName);
        //     $nickName = $nickName . ($int + 1);
        // }

        // $users->nick_name = $nickName;

        if($request->input('role') == 'lawyer')
        {
            $users->is_lawyer = 1;
        }
       
        $users->save();

        $users->assignRole($request->input('role'));

        $this->updateUserPortfolio($request, $users->id);
        $this->updateUserAccessPermission($request, $users->id);
        

        Helper::logAction(config('global.user'), config('global.action_create'));

        $request->session()->flash('message', 'Successfully created user');
        return redirect()->route('users.index');
    }

    public function updateUserAccessPermission(Request $request, $user_id)
    {
        $Roles = Roles::where('name', $request->input('role'))->first();

        if($request->input('permissions') == null)
        {
            return ;
        }

        $UserAccessControl = UserAccessControl::whereIn('code', $request->input('permissions'))->get();

        // return $UserAccessControl;

        if(count($UserAccessControl) > 0)
        {
            for ($i = 0; $i < count($UserAccessControl); $i++) {

                if ($UserAccessControl[$i]->role_id_list != '')
                {
                    $role_id_list = json_decode($UserAccessControl[$i]->role_id_list);
                    // $role_id_list = explode(',', $UserAccessControl[$i]->role_id_list);

                    if (!in_array($Roles->id, $role_id_list)) {

                        $user_id_list = json_decode($UserAccessControl[$i]->user_id_list);

                        if(!is_array($user_id_list))
                        {
                            $user_id_list = [];
                        }

                        if (!in_array($user_id, $user_id_list))
                        {
                            array_push($user_id_list, $user_id);

                            $UserAccessControl[$i]->user_id_list = json_encode($user_id_list);
                            $UserAccessControl[$i]->save();
                        }

                    }
                }

                // if ($UserAccessControl[$i]->code == 'CloseCasePermission')
                // {
                //     // $userListArr = [13,14];
                //     // $UserAccessControl[$i]->user_id_list = json_encode($userListArr);
                //     // $UserAccessControl[$i]->save();

                //     if ($UserAccessControl[$i]->role_id_list != '')
                //     {
                //         $role_id_list = json_decode($UserAccessControl[$i]->role_id_list);
                //         // $role_id_list = explode(',', $UserAccessControl[$i]->role_id_list);
    
                //         if (!in_array($Roles->id, $role_id_list)) {
    
                //             $user_id_list = json_decode($UserAccessControl[$i]->user_id_list);
    
                //             if (!in_array($user_id, $user_id_list))
                //             {
                //                 array_push($user_id_list, $user_id);

                //                 $UserAccessControl[$i]->user_id_list = json_encode($user_id_list);
                //                 $UserAccessControl[$i]->save();
                //             }
    
                //         }
                //     }
                // }
                // else
                // {
                //     if ($UserAccessControl[$i]->role_id_list != '')
                //     {
                //         $role_id_list = explode(',', $UserAccessControl[$i]->role_id_list);
    
                //         if (!in_array($Roles->id, $role_id_list)) {
    
                //             $user_id_list = explode(',', $UserAccessControl[$i]->user_id_list);
    
                //             if (!in_array($user_id, $user_id_list))
                //             {
                //                 $UserAccessControl[$i]->user_id_list = $UserAccessControl[$i]->user_id_list.$user_id.',';
                //                 $UserAccessControl[$i]->save();
                //             }
    
                //         }
                //     }
                // }
                
                
                
            }
        }

        // uncheck 
        $UserAccessControl = UserAccessControl::whereNotIn('code', $request->input('permissions'))->get();

        if(count($UserAccessControl) > 0)
        {
            
            for ($i = 0; $i < count($UserAccessControl); $i++) {

                if ($UserAccessControl[$i]->role_id_list != '')
                {
                    $role_id_list = json_decode($UserAccessControl[$i]->role_id_list);
                    // $role_id_list = explode(',', $UserAccessControl[$i]->role_id_list);

                    if (!in_array($Roles->id, $role_id_list)) {

                        $user_id_list = json_decode($UserAccessControl[$i]->user_id_list);

                        if(is_array($user_id_list))
                        {
                            if (in_array($user_id, $user_id_list))
                            {
                                // array_push($user_id_list, $user_id);
    
                                if (($key = array_search($user_id, $user_id_list)) !== false) {
                                    unset($user_id_list[$key]);
                                    $user_id_list = array_values($user_id_list);
                                }
    
                                $UserAccessControl[$i]->user_id_list = json_encode($user_id_list);
                                $UserAccessControl[$i]->save();
                            }
                        } 

                    }
                }
            }
        }

    }

    public function updateUserPortfolio(Request $request, $user_id)
    {

        

        TeamPortfolios::where('user_id', $user_id)->delete();

        if($request->input('portfolios') == null)
        {
            return;
        }

        $portfolios = $request->input('portfolios');

        if(count($portfolios) > 0)
        {
            for ($i = 0; $i < count($portfolios); $i++) {

                if ($portfolios[$i] != '')
                {
                    TeamPortfolios::insert([
                        'user_id' => $user_id,
                        'portfolio_id' => $portfolios[$i],
                        'created_at' => now(),
                    ]);
                }

                
            }
        }


    }

    public function generateNickName($user_name, $nick_name)
    {
        if ($nick_name == '') {
            if ($user_name != '') {
                $nameParts = explode(' ', trim($user_name));
                $firstName = array_shift($nameParts);
                $lastName = array_pop($nameParts);
                $nick_name = mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1);
            }
        } else {
            $int = (int) filter_var($nick_name, FILTER_SANITIZE_NUMBER_INT);
            $nick_name = str_replace($int, '', $nick_name);
            $nick_name = $nick_name . ($int + 1);
        }

        return $nick_name;
    }

    public function resetUserPasswordView($id)
    {
        $current_user = auth()->user();
        
        $user = User::find($id);
        return view('dashboard.admin.userResetPassword', compact('user'));
    }


    public function resetUserPassword(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $user = User::find($id);

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        $data = 'Password reset';
        
        return response()->json(['status' => $status, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::ManageUserPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $LinkCaseUser = [];
        $SpecialCaseAccess = [];
        $you = auth()->user();

        $user = User::find($id);
        $myrole = Roles::where('name', $you->menuroles)->first();

        // if ($this->validateUser($current_user ,$user) == false)
        // {
        //     return redirect()->route('dashboard.index');
        // }
        
        if($current_user->id == 1)
        {
            $roles = DB::table('roles')->where('status', 1)->get();
        }
        else if(in_array($current_user->id,[2,3]))
        {
            $roles = DB::table('roles')->where('hierarchy', '>', 2)->get();
        }
        else
        {
            $roles = DB::table('roles')->where('hierarchy', '>=', $myrole->hierarchy)->get();
        }

        $branchInfo = BranchController::manageBranchAccess();
        $branchList = $branchInfo['branch'];

        if($myrole->hierarchy < 3)
        {
            $branchList = Branch::where('status',1)->orderBy('id','ASC')->get();
        }
        else
        {
            $branchList = Branch::where('id',$you->handle_branch)->orderBy('id','ASC')->get();
        }
        

        // $UserAccessControl = UserAccessControl::where('show_in_menu', 1)->where('hierarchy', 1)->orWhere('hierarchy', $myrole->hierarchy)->get();
        $UserAccessControl = UserAccessControl::where('show_in_menu', 1)->where('hierarchy', 0)->orWhere('hierarchy', $myrole->hierarchy)->get();
        $Portfolio = Portfolio::where('status', 1)->orderBy('name','ASC')->get();
        $TeamPortfolios = TeamPortfolios::where('status', 1)->where('user_id', $id)->pluck('portfolio_id')->toArray();

        
        $UserAccessControlType = UserAccessControl::where('show_in_menu', 1)->where('hierarchy', 0)->orWhere('hierarchy', $myrole->hierarchy)->where('type_name', '!=',null )->distinct()
        ->select('type_name')->get();

        if($user->link_user_case != null)
        {
            $LinkCaseUser = User::whereIn('id', json_decode($user->link_user_case))->get();
        }

        if($user->special_access_case != null)
        {
            $SpecialCaseAccess = LoanCase::whereIn('id', json_decode($user->special_access_case))->get();
        }

        if (!in_array($current_user->id,[1]))
        {
            if (!in_array($current_user->id,[2,3]))
            {
                // $users->where('branch_id',  [$current_user->branch_id]);
                $linkUser = User::where('branch_id',  $current_user->branch_id)->get();
            }
            else
            {
                // $users->whereNotIn('id',  [1]);
                $linkUser = User::whereNotIn('id',  [1])->get();
            }
        }
        else
        {
            $linkUser = User::where('status', 1)->get();
        }

        $userRoleId = Roles::where('name',$user->menuroles)->first();
        return view('dashboard.admin.userEditForm', ['user' => $user, 'roles' => $roles, 'branchList' => $branchList, 'LinkCaseUser' => $LinkCaseUser, 'SpecialCaseAccess' => $SpecialCaseAccess, 
        'UserAccessControl' => $UserAccessControl, 'userRoleId' => $userRoleId, 'Portfolio' => $Portfolio, 'TeamPortfolios' => $TeamPortfolios, 'linkUser' => $linkUser,
        'UserAccessControlType' => $UserAccessControlType,'current_user' => $current_user ]);
    }

    public function validateUser($current_user, $edit_user)
    {
        $current_user_role = Roles::where('name',  $current_user->menuroles)->first();
        $edit_user_role = Roles::where('name',  $edit_user->menuroles)->first();

        // if($current_user->id != 1)
        // {
        //     if($edit_user_role->id == 1)
        //     {
        //         return false;
        //     }
        // }

        if($current_user_role->hierarchy > $edit_user_role->hierarchy)
        {
            return false;
        }

        //For role lower than account
        if($current_user_role->hierarchy > 4)
        {
            if($current_user->branch_id != $edit_user->branch_id)
            {
                return false;
            }
        }

        return true;

        // if (!in_array($current_user->id,[1]))
        // {
        //     if (!in_array($current_user->id,[2,3]))
        //     {
        //         // $users->where('branch_id',  [$current_user->branch_id]);
        //         $linkUser = User::where('branch_id',  $current_user->branch_id)->get();
        //     }
        //     else
        //     {
        //         // $users->whereNotIn('id',  [1]);
        //         $linkUser = User::whereNotIn('id',  [1])->get();
        //     }
        // }
        // else
        // {
        //     $linkUser = User::where('status', 1)->get();
        // }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'name'       => 'required|min:1|max:256'
        ]);
        $user = User::find($id);
        $user->name       = $request->input('name');
        $user->ic_name = $request->input('ic_name');

        // $user->email      = $request->input('email');

        $user->phone_no = $request->input('phone_no');
        $user->office_no = $request->input('office_no');
        $user->menuroles = $request->input('role');
        $user->commission = $request->input('commission');
        $user->max_files = $request->input('max_file');
        $user->min_files = $request->input('min_file');
        $user->nick_name = $request->input('nick_name');
        $user->race = $request->input('race');
        $user->status = $request->input('status');
        $user->bc_no = $request->input('bc_no');
        $user->branch_id = $request->input('branch_id');
        $user->handle_branch = $request->input('branch_id');
        $user->branch_case = $request->input('branch_case');
        $user->link_user_case = $request->input('link_case_user');
        $user->is_sales = $request->input('is_sales');

        if ($user->status != 1) {
            $user->password = '';
        }
        else{
            if(in_array($user->password, ['NA','']))
            {
                $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';;
            }
        }

        if (count($user->roles) != 0) {
            $user->removeRole($user->roles[0]->name);
        }

        
        if($request->input('role') == 'lawyer')
        {
            $user->is_lawyer = 1;
        }

        $user->assignRole($request->input('role'));

        $this->updateUserAccessPermission($request, $user->id);
        $this->updateUserPortfolio($request, $user->id);

        $user->save();
        $request->session()->flash('message', 'Successfully updated user');
        Helper::logAction(config('global.user'), config('global.action_update'));
        // return $this->edit($id);
        // return redirect()->route('users.index');
        return Redirect::back()->with('message','Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return redirect()->route('users.index');
    }
}
