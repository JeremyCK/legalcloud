<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\CaseTemplate;
use App\Models\CaseTemplateCategories;
use App\Models\CaseTemplateDetails;
use App\Models\CaseTemplateMain;
use App\Models\CaseTemplateMainStepsRel;
use App\Models\EmailTemplateMain;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Roles;
use Illuminate\Support\Facades\DB;

class ChecklistTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $template = DB::table('checklist_template_main as m')
            ->leftJoin('checklist_template_categories as c', 'c.id', '=', 'm.checklist_category_id')
            ->select('m.*', 'c.name as cat_name')
            //  ->where('t.case_id', '=', $id)
            ->get();

        return view('dashboard.checklistTemplate.index', ['templates' => $template]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.caseTemplate.create');
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
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        // $menuLang = new MenuLangList();
        // $menuLang->name         = $request->input('name');
        // $menuLang->short_name   = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLang->save();
        // $request->session()->flash('message', 'Successfully created language');
        // return redirect()->route('todolist.create');

        $status = 1;

        $caseTemplateMain = new CaseTemplate();

        $caseTemplateMain->display_name = $request->input('display_name');
        $caseTemplateMain->type = $request->input('type');
        $caseTemplateMain->target_close_day = $request->input('target_close_day');
        $caseTemplateMain->Status = 0;
        $caseTemplateMain->created_at = date('Y-m-d H:i:s');

        $caseTemplateMain->save();

        return response()->json(['status' => $status, 'data' => $caseTemplateMain->id]);
    }

    public function createChecklistTemplate(Request $request)
    {

        $status = 1;
        $data = '';

        $caseTemplateMain = new CaseTemplate();

        $caseTemplateMain->display_name = $request->input('display_name');
        $caseTemplateMain->type = $request->input('type');
        $caseTemplateMain->target_close_day = $request->input('target_close_day');
        $caseTemplateMain->Status = 0;
        $caseTemplateMain->created_at = date('Y-m-d H:i:s');

        $caseTemplateMain->save();

        return response()->json(['status' => $status, 'data' => $caseTemplateMain->id]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id){

        // $caseTemplateDetail = CaseTemplateDetails::all();

        // $template = DB::table('checklist_template_main as m')
        //     ->join('checklist_template_main_step_rel as r', 'r.id', '=', 'm.template_main_id')
        //     ->join('checklist_template_steps as s', 's.id', '=', 'r.checklist_step_id')
        //     ->select('m.*', 's.name as step_name')
        //      ->where('m.case_id', '=', $id)
        //     ->get();

        $roles = DB::table('roles')->whereNotIn('id', ['1', '2', '3'])->get();

            $template = DB::table('checklist_template_main_step_rel as r')
            ->join('checklist_template_steps as s', 's.id', '=', 'r.checklist_step_id')
            ->select('r.*', 's.name as step_name')
             ->where('r.template_main_id', '=', $id)
            ->get();


            $Step = DB::table('checklist_template_steps')->get();

            $caseTemplateMain = caseTemplateMain::where('id', '=', $id)->first();

        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', $id)->get()->sortBy('process_number');
        $email = EmailTemplateMain::all();

        // $caseTemplateMain = caseTemplate::where('id', '=', $id)->get();
        $roles = Roles::whereNotIn('id', [1,2,3])->get();
        return view('dashboard.checklistTemplate.show', [
            'email' => $email,
            'templates' => $caseTemplateDetail,
            'templates_main' => $caseTemplateMain,
            'template_steps' => $template,
            'steps' => $Step,
            'roles' => $roles
        ]);
    }

    public function AddCheckListTemplateStep(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $caseTemplateMainStepsRel = new CaseTemplateMainStepsRel();

        $caseTemplateMainStepsRel->template_main_id = $id;
        $caseTemplateMainStepsRel->checklist_step_id = $request->input('steps');
        $caseTemplateMainStepsRel->status = 1;
        $caseTemplateMainStepsRel->created_at = date('Y-m-d H:i:s');

        $caseTemplateMainStepsRel->save();

        return response()->json(['status' => $status, 'data' => 'Added step']);
    }

    public function UpdateCheckListTemplateStep(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $caseTemplateMainStepsRel = CaseTemplateMainStepsRel::where('id', '=', $id)->first();

        $caseTemplateMainStepsRel->checklist_step_id = $request->input('steps');

        $caseTemplateMainStepsRel->save();

        return response()->json(['status' => $status, 'data' => 'Update success']);
    }

    public function DeleteCheckListTemplateStep($id)
    {
        $status = 1;
        $data = '';

        $caseTemplateMainStepsRel = CaseTemplateMainStepsRel::where('id', '=', $id)->first();

        $caseTemplateMainStepsRel->delete();

        return response()->json(['status' => $status, 'data' => 'Deleted step']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $caseTemplate = CaseTemplateMain::where('id', '=', $id)->first();
        $caseTemplateCategories = CaseTemplateCategories::where('status', '=', '1')->get();
        return view('dashboard.checklistTemplate.edit',['caseTemplate' => $caseTemplate, 'categories' => $caseTemplateCategories]);
    }

    public function updateTemplateDetails(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $caseTemplate = CaseTemplate::where('id', '=', $id)->first();

        $caseTemplate->display_name = $request->input('display_name');
        $caseTemplate->type = $request->input('type');
        $caseTemplate->target_close_day = $request->input('target_close_day');
        $caseTemplate->Status = $request->input('status');
        $caseTemplate->updated_at = date('Y-m-d H:i:s');

        $caseTemplate->save();

        return response()->json(['status' => $status, 'data' => 'Case template updated']);
    }

    public function addNewCheckListTemplate(Request $request, $id)
    {
        $status = 1;
        $data = '';
        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', $id)->orderByDesc('process_number')->get();


        if ($caseTemplateDetail != null)
        {
            $lastNumber = $caseTemplateDetail[0]->process_number + 1;
        }

        $caseTemplateDetails = new CaseTemplateDetails();

        $caseTemplateDetails->checklist_name = $request->input('checklist_name');
        $caseTemplateDetails->role_id = 7;
        $caseTemplateDetails->template_main_id = $id;
        $caseTemplateDetails->process_number = $lastNumber;
        $caseTemplateDetails->kpi = $request->input('kpi');
        $caseTemplateDetails->duration_base_item = 1;
        $caseTemplateDetails->duration = $request->input('duration');
        $caseTemplateDetails->remark = $request->input('remark');
        $caseTemplateDetails->system_code = "";
        $caseTemplateDetails->email_template_id = 0;
        $caseTemplateDetails->check_point = $request->input('check_point');
        $caseTemplateDetails->Status = 1;
        $caseTemplateDetails->created_at = date('Y-m-d H:i:s');

        $caseTemplateDetails->save();

        return response()->json(['status' => $status, 'data' => 'Added new checklist']);
    }

    public function updateCheckListTemplate(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $caseTemplateDetail = CaseTemplateDetails::where('id', '=', $id)->first();

        $caseTemplateDetail->checklist_name = $request->input('checklist_name');
        $caseTemplateDetail->kpi = $request->input('kpi');
        $caseTemplateDetail->check_point = $request->input('check_point');
        $caseTemplateDetail->role_id = $request->input('role_id');
        $caseTemplateDetail->duration = $request->input('duration');
        // $caseTemplateDetail->remark = $request->input('remark');
        $caseTemplateDetail->status = $request->input('check_list_status');

        $caseTemplateDetail->save();

        return response()->json(['status' => $status, 'data' => 'Update success']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
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
        if(!empty($menusLang)){
            $request->session()->flash('message', "Can't delete. Language has one or more assigned tranlsation of menu element");
            $request->session()->flash('back', 'todolist.index');
            return view('dashboard.shared.universal-info');
        }else{
            $menus = MenuLangList::all();
            if(count($menus) <= 1){
                $request->session()->flash('message', "Can't delete. This is last language on the list");
                $request->session()->flash('back', 'todolist.index');
                return view('dashboard.shared.universal-info');
            }else{
                if($menu->is_default == true){
                    $request->session()->flash('message', "Can't delete. This is default language");
                    $request->session()->flash('back', 'todolist.index');
                    return view('dashboard.shared.universal-info');
                }else{
                    $menu->delete();
                    $request->session()->flash('message', 'Successfully deleted language');
                    $request->session()->flash('back', 'todolist.index');
                    return view('dashboard.shared.universal-info');
                }
            }
        }
    }
}
