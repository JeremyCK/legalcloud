<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\CaseTemplate;
use App\Models\CaseTemplateDetails;
use App\Models\CaseTemplateItems;
use App\Models\CaseTemplateMain;
use App\Models\CaseTemplateSteps;
use App\Models\EmailTemplateMain;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Roles;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ChecklistItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.checklistItem.index', ['templates' => CaseTemplateSteps::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.checklistItem.create');
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

    // function deleteChecklist($id)
    // {
    //     $caseTemplateItems = CaseTemplateItems::where('id', '=', $id)->first();
    // }

    public function deleteChecklist($id)
    {
        $caseTemplateItems = CaseTemplateItems::where('id', '=', $id)->first();
        $caseTemplateItems->delete();

        return response()->json(['status' => 1, 'message' => 'Checklist deleted']);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $caseTemplateDetail = CaseTemplateItems::where('step_id', '=', $id)->get()->sortBy('order');

        // return $caseTemplateDetail;

        $caseTemplateDetail = DB::table('checklist_template_item as v')
            ->leftJoin('checklist_template_item as s', 's.id', '=', 'v.start')
            ->where('v.step_id', '=', $id)
            ->select('v.*', 's.name as checklist_name')
            ->get()->sortBy('order');

        $CaseTemplateSteps = CaseTemplateSteps::where('status', '=', 1)->get();


        $CaseTemplateItems = CaseTemplateItems::where('status', '=', 1)->get();

        $email = EmailTemplateMain::all();

        $caseTemplateMain = CaseTemplateSteps::where('id', '=', $id)->first();
        $roles = Roles::whereNotIn('id', [1, 2, 3])->get();
        return view('dashboard.checklistItem.show', [
            'email' => $email,
            'templates' => $caseTemplateDetail,
            'templates_main' => $caseTemplateMain,
            'CaseTemplateSteps' => $CaseTemplateSteps,
            'roles' => $roles,
            'CaseTemplateItems' => $CaseTemplateItems
        ]);
    }

    public function getChecklist(Request $request)
    {
        if ($request->ajax()) {

            // $bills = DB::table('loan_case_bill_main')
            //     ->join('loan_case', 'loan_case.id', '=', 'loan_case_bill_main.case_id')
            //     ->join('client', 'client.id', '=', 'loan_case.customer_id')
            //     ->select('loan_case.case_ref_no', 'loan_case_bill_main.*', 'client.name as client_name')
            //     ->get();

            $checklist = DB::table('checklist_template_item as v')
                ->join('checklist_template_steps as s', 's.id', '=', 'v.step_id')
                ->where('v.status', '=', 1)
                ->select('v.*', 's.name as step_name')
                ->get();


            return DataTables::of($checklist)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/voucher/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="javascript:void(0)" onclick="selectThisChecklist(' . $row->id . ', \'' . $row->name . '\')" class="btn btn-success shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-check"></i></a>
                    ';
                    return $actionBtn;
                })
                ->make(true);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $caseTemplate = CaseTemplateSteps::where('id', '=', $id)->first();
        return view('dashboard.checklistItem.edit', ['caseTemplate' => $caseTemplate]);
    }

    public function AddNewChecklistStep(Request $request)
    {
        $status = 1;
        $data = [];

        $caseTemplateSteps = new CaseTemplateSteps();

        $caseTemplateSteps->name = $request->input('name');
        $caseTemplateSteps->remarks = $request->input('remarks');
        $caseTemplateSteps->Status = $request->input('status');
        $caseTemplateSteps->created_at = date('Y-m-d H:i:s');

        $caseTemplateSteps->save();

        $data['message'] = 'New Step Created';
        $data['id'] = $caseTemplateSteps->id;

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function updateChecklistSteps(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $caseTemplateSteps = CaseTemplateSteps::where('id', '=', $id)->first();

        $caseTemplateSteps->name = $request->input('name');
        $caseTemplateSteps->remarks = $request->input('remarks');
        $caseTemplateSteps->Status = $request->input('status');
        $caseTemplateSteps->updated_at = date('Y-m-d H:i:s');

        $caseTemplateSteps->save();

        return response()->json(['status' => $status, 'data' => 'Step updated']);
    }

    public function AddCheckListItem(Request $request, $id)
    {
        $status = 1;
        $data = '';
        $need_attachment = 0;
        $auto_dispatch = 0;
        $auto_receipt = 0;

        $caseTemplateItems = CaseTemplateItems::where('step_id', '=', $id)->orderByDesc('order')->get();

        if (count($caseTemplateItems) > 0) {
            $lastOrderNumber = $caseTemplateItems[0]->order;
        } else {
            $lastOrderNumber = 0;
        }


        if ($request->input('need_attachment') == "on") {
            $need_attachment = 1;
        }

        if ($request->input('auto_dispatch') == "on") {
            $auto_dispatch = 1;
        }

        if ($request->input('auto_receipt') == "on") {
            $auto_receipt = 1;
        }


        $caseTemplateItems = new CaseTemplateItems();

        $caseTemplateItems->name = $request->input('checklist_name');
        $caseTemplateItems->kpi = $request->input('kpi');
        $caseTemplateItems->step_id =  $id;
        $caseTemplateItems->roles = $request->input('role_id');
        $caseTemplateItems->days = $request->input('days');
        $caseTemplateItems->start = $request->input('start');
        $caseTemplateItems->duration = 0;
        $caseTemplateItems->need_attachment = $need_attachment;
        $caseTemplateItems->auto_dispatch = $auto_dispatch;
        $caseTemplateItems->auto_receipt = $auto_receipt;
        $caseTemplateItems->order = $lastOrderNumber + 1;
        $caseTemplateItems->remark = $request->input('remarks');
        $caseTemplateItems->status = $request->input('check_list_status');
        $caseTemplateItems->created_at = date('Y-m-d H:i:s');

        $caseTemplateItems->save();

        return response()->json(['status' => $status, 'data' => 'Added new checklist']);
    }

    public function updateCheckListItem(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $need_attachment = 0;
        $auto_dispatch = 0;
        $auto_receipt = 0;

        if ($request->input('need_attachment') == "on") {
            $need_attachment = 1;
        }

        if ($request->input('auto_dispatch') == "on") {
            $auto_dispatch = 1;
        }

        if ($request->input('auto_receipt') == "on") {
            $auto_receipt = 1;
        }

        $caseTemplateItems = CaseTemplateItems::where('id', '=', $id)->first();

        $caseTemplateItems->name = $request->input('checklist_name');
        $caseTemplateItems->kpi = $request->input('kpi');
        $caseTemplateItems->roles = $request->input('role_id');
        $caseTemplateItems->days = $request->input('days');
        $caseTemplateItems->start = $request->input('start');
        // $caseTemplateItems->duration = $request->input('duration');
        $caseTemplateItems->need_attachment = $need_attachment;
        $caseTemplateItems->auto_dispatch = $auto_dispatch;
        $caseTemplateItems->auto_receipt = $auto_receipt;
        // $caseTemplateItems->check_point = $request->input('check_point');
        $caseTemplateItems->remark = $request->input('remarks');
        $caseTemplateItems->status = $request->input('check_list_status');

        $caseTemplateItems->save();

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

    function reorderSequenceChecklistTemplate(Request $request)
    {
        if ($request->input('check_list') != null) {
            $checkList = json_decode($request->input('check_list'), true);
        }

        if (count($checkList) > 0) {
            for ($i = 0; $i < count($checkList); $i++) {

                $caseTemplateItems = CaseTemplateItems::where('id', '=', $checkList[$i]['id'])->first();

                $caseTemplateItems->order = $checkList[$i]['seq'];
                $caseTemplateItems->save();
            }
        }

        return response()->json(['status' => 1, 'data' => 'Update success']);
    }
}
