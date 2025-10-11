<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\BanksUsersRel;
use App\Models\Teams;
use App\Models\TeamMember;
use App\Models\CaseType;
use App\Models\Customer;
use App\Models\GroupPortfolio;
use App\Models\caseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\Portfolio;
use App\Models\TeamPortfolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = DB::table('team_main AS t')
            ->leftJoin('team_member as m', 'm.team_main_id', '=', 't.id')
            ->select(array('t.*', DB::raw('COUNT(m.user_id) as member_count')))
            ->groupBy('t.id')
            ->orderBy('t.id')
            ->get();

        return view('dashboard.teams.index', ['teams' => $teams]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account = Users::where('menuroles', '=', 'account')->get();
        $lawyer = Users::where('menuroles', '=', 'lawyer')->get();
        $sales = Users::where('menuroles', '=', 'sales')->get();
        $clerk = Users::where('menuroles', '=', 'clerk')->get();
        $banks = Banks::where('status', '=', 1)->get();
        $case_type = CaseType::where('status', '=', 1)->get();

        return view('dashboard.teams.create', [
            'banks' => $banks,
            'lawyers' => $lawyer,
            'sales' => $sales,
            'accounts' => $account,
            'clerks' => $clerk,
            'case_type' => $case_type
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'desc'             => 'required|min:1|max:64'
        // ]);

        $teams = new Teams();


        $teams->name = $request->input('name');
        $teams->desc = $request->input('desc');
        $teams->status = $request->input('status');
        $teams->created_at = now();

        $teams->save();

        if ($teams) {
            if (!empty($request->input('assignTo'))) {
                $staffList = $request->input('assignTo');

                for ($i = 0; $i < count($staffList); $i++) {

                    $members = new TeamMember();

                    $members->team_main_id = $teams->id;
                    $members->user_id = $staffList[$i];
                    $members->status = 1;
                    $members->created_at = now();

                    $members->save();
                }
            }

            Helper::logAction(config('global.team'), config('global.action_create'));
        }

        $request->session()->flash('message', 'Successfully created new Team');

        $teams = Teams::all();

        return view('dashboard.teams.index', ['teams' => $teams]);
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
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $account = Users::where('menuroles', '=', 'account')->get();
        $lawyer = Users::where('menuroles', '=', 'lawyer')->get();
        $sales = Users::where('menuroles', '=', 'sales')->get();
        $clerk = Users::where('menuroles', '=', 'clerk')->get();
        $teams = Teams::where('id', '=', $id)->get();
        $portfolio = Portfolio::where('status', '=', 1)->get();
        $banks = Banks::where('status', '=', 1)->get();
        $case_type = CaseType::where('status', '=', 1)->get();
        $banksUsersRelAraa = TeamMember::where('team_main_id', '=', $id)->get();
        $teamPortfolio = TeamPortfolio::where('team_main_id', '=', $id)->get();
        $team_case = GroupPortfolio::where('group_id', '=', $id)->where('type', '=', 'CASE')->get();
        $team_bank = GroupPortfolio::where('group_id', '=', $id)->where('type', '=', 'BANK')->get();


        // $teamMembers = TeamMember::where('team_main_id', '=', $id)->get();

        $teamMembers = DB::table('team_member AS m')
            ->leftJoin('users AS u', 'u.id', '=', 'm.user_id')
            ->select('m.*', 'u.name as member_name',  'u.menuroles')
            ->where('m.team_main_id', '=',  $id)
            ->get();


        $teamPortfolio = DB::table('team_portfolio AS m')
            ->join('portfolio AS u', 'u.id', '=', 'm.portfolio_id')
            ->select('m.*', 'u.name as portfolio_name')
            ->where('m.team_main_id', '=',  $id)
            ->get();


        $banksUsersRel = [];
        $teamPort = [];
        $teamCaseList = [];
        $teamBankList = [];

        for ($i = 0; $i < count($teamPortfolio); $i++) {
            array_push($teamPort, $teamPortfolio[$i]->id);
        }

        for ($i = 0; $i < count($banksUsersRelAraa); $i++) {
            array_push($banksUsersRel, $banksUsersRelAraa[$i]->user_id);
        }

        for ($i = 0; $i < count($team_case); $i++) {
            array_push($teamCaseList, $team_case[$i]->portfolio_id);
        }

        for ($i = 0; $i < count($team_bank); $i++) {
            array_push($teamBankList, $team_bank[$i]->portfolio_id);
        }


        return view('dashboard.teams.edit', [
            'teams' => $teams[0],
            'lawyers' => $lawyer,
            'sales' => $sales,
            'accounts' => $account,
            'banksUsersRel' => $banksUsersRel,
            'teamMembers' => $teamMembers,
            'banks' => $banks,
            'case_type' => $case_type,
            'portfolio' => $portfolio,
            'teamCaseList' => $teamCaseList,
            'teamBankList' => $teamBankList,
            'teamPortfolio' => $teamPortfolio,
            'teamPort' => $teamPort,
            'clerks' => $clerk
        ]);
    }

    function setTeamMember(Request $request, $id)
    {

        $status = 1;
        $data = '';




        // if($request->input('id'))

        // $array = (array) $request->input('memberList');

        // $array = (array) $fileTemplateId;

        $array = explode(",", $request->input('memberList'));

        if (count($array) > 0) {
            // $teamMember = TeamMember::where('team_main_id', '=', $id)->get();
            // $teamMember->delete();

            DB::table('team_member')->where('team_main_id', $id)->delete();

            for ($i = 0; $i < count($array); $i++) {
                $teamMember = new TeamMember();
                $teamMember->team_main_id = $id;
                $teamMember->user_id = $array[$i];
                $teamMember->status = 1;
                $teamMember->created_at = date('Y-m-d H:i:s');

                $teamMember->save();
            }

            Helper::logAction(config('global.team_member'), config('global.action_update'));
        }


        return response()->json(['status' => $status, 'data' => $data]);
    }

    function setTeamPortfolio(Request $request, $id)
    {

        $status = 1;
        $data = '';




        // if($request->input('id'))

        // $array = (array) $request->input('memberList');

        // $array = (array) $fileTemplateId;

        $array = explode(",", $request->input('portfolioList'));

        if (count($array) > 0) {
            // $teamMember = TeamMember::where('team_main_id', '=', $id)->get();
            // $teamMember->delete();

            DB::table('team_portfolio')->where('team_main_id', $id)->delete();

            for ($i = 0; $i < count($array); $i++) {
                $teamPortfolio = new TeamPortfolio();
                $teamPortfolio->team_main_id = $id;
                $teamPortfolio->portfolio_id = $array[$i];
                $teamPortfolio->status = 1;
                $teamPortfolio->created_at = date('Y-m-d H:i:s');

                $teamPortfolio->save();
            }

            Helper::logAction(config('global.team_portfolio'), config('global.action_update'));
        }


        return response()->json(['status' => $status, 'data' => $data]);
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
        //     'desc'             => 'required|min:1|max:64'
        // ]);


        // return redirect()->back()->withInput();

        // if ($validatedData->fails()) {
        //     return redirect()->back()->withInput();
        // }
        // $menuLangList = MenuLangList::where('id', '=', $request->input('id'))->first();
        // $menuLangList->name = $request->input('name');
        // $menuLangList->short_name = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLangList->save();
        // $request->session()->flash('message', 'Successfully updated language');
        // return redirect()->route('todolist.edit', [$request->input('id')]); 


        $teams = new Teams();
        $teams = Teams::where('id', '=', $id)->first();

        $teams->name = $request->input('name');
        $teams->desc = $request->input('desc');
        $teams->status = $request->input('status');
        $teams->created_at = now();

        $teams->save();

        if ($teams) {
            $banksUsersRel = TeamMember::where('team_main_id', '=', $id);
            $banksUsersRel->delete();

            if (!empty($request->input('assignTo'))) {
                $staffList = $request->input('assignTo');

                for ($i = 0; $i < count($staffList); $i++) {

                    $members = new TeamMember();

                    $members->team_main_id = $teams->id;
                    $members->user_id = $staffList[$i];
                    $members->status = 1;
                    $members->created_at = now();

                    $members->save();
                }
            }

            if (!empty($request->input('selectType')) || !empty($request->input('selectBank'))) {

                $group_portfolio = GroupPortfolio::where('group_id', '=', $teams->id);
                $group_portfolio->delete();

                if (!empty($request->input('selectType'))) {
                    $typeList = $request->input('selectType');

                    for ($i = 0; $i < count($typeList); $i++) {

                        $group_portfolio = new GroupPortfolio();

                        $group_portfolio->group_id = $teams->id;
                        $group_portfolio->portfolio_id = $typeList[$i];
                        $group_portfolio->type = 'CASE';
                        $group_portfolio->status = 1;
                        $group_portfolio->created_at = now();

                        $group_portfolio->save();
                    }
                }

                if (!empty($request->input('selectBank'))) {
                    $bankList = $request->input('selectBank');

                    for ($i = 0; $i < count($bankList); $i++) {

                        $group_portfolio = new GroupPortfolio();

                        $group_portfolio->group_id = $teams->id;
                        $group_portfolio->portfolio_id = $bankList[$i];
                        $group_portfolio->type = 'BANK';
                        $group_portfolio->status = 1;
                        $group_portfolio->created_at = now();

                        $group_portfolio->save();
                    }
                }
            }
        }

        $request->session()->flash('message', 'Successfully update Team');

        $teams = Teams::all();

        return view('dashboard.teams.index', ['teams' => $teams]);
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
