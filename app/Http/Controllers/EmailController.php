<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\EmailTemplate;
use App\Models\CaseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use App\Models\LoanCase;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use Illuminate\Support\Facades\DB;


class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.emailTemplate.index', ['templates' => EmailTemplateMain::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where('status', '=', '1')->get();

        return view('dashboard.emailTemplate.create', [
            'templates' => CaseTemplate::all(),
            'roles' => $roles
        ]);
    }

    public function generateEmail($case_id, $email_template_id)
    {
        // get email main and details
        $templateEmail = EmailTemplateMain::where('id', '=', $email_template_id)->first();
        $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $email_template_id)->where('status', '=', '1')->first();

        $case = LoanCase::where('id', '=', $case_id)->first();

        $lawyer = Users::where('id', '=', $case->lawyer_id)->get();
        $clerk = Users::where('id', '=', $case->clerk_id)->get();
        $sales = Users::where('id', '=', $case->sales_user_id)->get();

        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
            })
            ->where('case_id', '=', $case_id)
            ->get();

        for ($j = 0; $j < count($caseMasterListField); $j++) {
            $templateEmail->subject =   str_replace("[" . $caseMasterListField[$j]->code . "]", $caseMasterListField[$j]->value, $templateEmail->subject);
            $templateEmailDetails->content =   str_replace("[" . $caseMasterListField[$j]->code . "]", $caseMasterListField[$j]->value, $templateEmailDetails->content);
        }
        
        $templateEmail->subject =   str_replace("[case_ref_no]", $case->case_ref_no, $templateEmail->subject);
        $templateEmailDetails->content =   str_replace("[case_ref_no]", $case->case_ref_no, $templateEmailDetails->content);

        $templateEmail->subject =   str_replace("[lawyer_name]", $lawyer->name, $templateEmail->subject);
        $templateEmailDetails->content =   str_replace("[lawyer_name]", $lawyer->name, $templateEmailDetails->content);

        $templateEmail->subject =   str_replace("[clerk_name]", $clerk->name, $templateEmail->subject);
        $templateEmailDetails->content =   str_replace("[clerk_name]", $clerk->name, $templateEmailDetails->content);

        $templateEmail->subject =   str_replace("[sales_name]", $sales->name, $templateEmail->subject);
        $templateEmailDetails->content =   str_replace("[lsales_name]", $sales->name, $templateEmailDetails->content);

        return response()->json(['status' => 1, 
        'templateEmail' => $templateEmail,
        'content' => $templateEmailDetails ]);
    }

    public function store(Request $request)
    {

        $request->session()->flash('message', 'Successfully created template');
        return redirect()->route('email-template.index');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', $id)->get();
        $caseTemplateMain = caseTemplate::where('id', '=', $id)->get();
        return view('dashboard.caseTemplate.show', [
            'lang' => MenuLangList::where('id', '=', $id)->first(), 'templates' => $caseTemplateDetail, 'templates_main' => $caseTemplateMain
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Roles::where('status', '=', '1')->get();
        $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $id)->first();
        return view('dashboard.emailTemplate.edit', [
            'template' => EmailTemplateMain::where('id', '=', $id)->first(),
            'templateEmailDetails' => $templateEmailDetails,
            'roles' => $roles
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
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);

        $templateEmail = EmailTemplateMain::where('id', '=', $id)->first();

        $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $id)->where('status', '=', '1')->first();

        $templateEmail->name = $request->input('name');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->to = '';
        $templateEmail->from = '';
        $templateEmail->cc = '';
        $templateEmail->status = '1';
        // $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();

        if ($templateEmail->id != null && $templateEmail->id != '') {
            $templateEmailDetails  = new EmailTemplateDetails();
            $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $id)->where('status', '=', '1')->first();

            $templateEmailDetails->email_template_id = $templateEmail->id;
            $templateEmailDetails->version_name = 'Orinal';
            $templateEmailDetails->content = $request->input('summary-ckeditor');
            $templateEmailDetails->status = 1;
            $templateEmailDetails->updated_at = date('Y-m-d H:i:s');

            $templateEmailDetails->save();
        }


        // $menuLangList->name = $request->input('name');
        // $menuLangList->short_name = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLangList->save();
        $request->session()->flash('message', 'Successfully updated language');
        return redirect()->route('email-template.index');
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
