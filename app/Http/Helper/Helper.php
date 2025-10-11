<?php
/*
*   07.11.2019
*   MenusMenu.php
*/
namespace App\Http\Helper;

use App\Models\AuditLog;
use App\Models\EmailTemplateDetails;
use App\Models\EmailTemplateMain;
use App\Models\LoanCase;
use App\Models\User;
use App\Models\Users;
use Illuminate\Support\Facades\DB;

class Helper
{
    public static function generateNickName($user_name)
    {
        $nick_name = '';

        if ($user_name != '')
        {
            $user_name = str_replace("&", "", $user_name);
           
            $nameParts = explode(' ', trim($user_name));
            $nameCount = count($nameParts);

            if (count($nameParts) > 5)
            {
                $nameCount = 5;
            }

            for($i = 0; $i < $nameCount; $i++){
                $nick_name =  $nick_name.mb_substr($nameParts[$i],0,1);
            }
        }

        $user = User::where('nick_name', '=', $nick_name)->first();
        if ($user != null) {
            $int = (int) filter_var($nick_name, FILTER_SANITIZE_NUMBER_INT); 
            $nick_name = str_replace($int,'',$nick_name);
            $nick_name = $nick_name.($int+1);
        }
        
        return $nick_name;
        
    }

    public static function getRolePermission($userRoles, $role)
    {
        $result = "false";

        for($i = 0; $i < count($userRoles); $i++){

            if (in_array($userRoles[$i], $role))
            {
                $result = "true";
                break;
            }

            // if ($userRoles[$i] == $role)
            // {
            //     $result = "true";
            //     break;
            // }
        }

        return $result;
    }

    public static function logAction($model, $action)
    {
        $AuditLog  = new AuditLog();

        $current_user = auth()->user();

        $user_id = $current_user->id;
        $user_name = $current_user->name;

        $AuditLog->user_id = $user_id;
        $AuditLog->model = $model;
        $AuditLog->desc = $user_name.' '.strtolower($action).' '.strtolower($model);
        $AuditLog->created_at = date('Y-m-d H:i:s');
        $AuditLog->status =1;

        $AuditLog->save();
    }

    public static function generateEmail($case_id, $email_template_id)
    {
        // get email main and details
        $templateEmail = EmailTemplateMain::where('id', '=', $email_template_id)->first();
        

        if ($templateEmail == null)
        {
            return response()->json(['status' => 0, 'templateEmail' => null, 'content' => null ]);
        }

        $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $email_template_id)->where('status', '=', '1')->first();

        // get loan case and the pic
        $case = LoanCase::where('id', '=', $case_id)->first();

        $lawyer = Users::where('id', '=', $case->lawyer_id)->first();
        $clerk = Users::where('id', '=', $case->clerk_id)->first();
        $sales = Users::where('id', '=', $case->sales_user_id)->first();

        // get value from master list
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

}