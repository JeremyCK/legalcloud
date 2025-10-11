<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class QueryController extends Controller
{
    public static function getCaseAttachment($id, $fileType)
    {
        $LoanAttachmentFrame = null;

        $LoanAttachment = DB::table('loan_attachment AS a')
            ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
            ->select(
                'a.id',
                'a.s3_file_name',
                'a.filename',
                'a.type',
                'a.receipt_done',
                'a.display_name',
                'a.attachment_type',
                'a.case_id',
                'a.remark',
                'a.created_at',
                'u.name as user_name',
                'a.checklist_id'
            )
            ->where('a.case_id', '=',  $id)
            ->where('a.status', '<>',  99)
            ->where('a.file_type', '=', $fileType)
            ->orderBy('created_at', 'desc')
            ->get();
        
            $LoanAttachmentFrame = $LoanAttachment;

        if ($fileType == 1) {
            $LoanAttachment2 = DB::table('loan_case_account_files AS a')
            ->leftJoin('users AS u', 'u.id', '=', 'a.created_by')
            // ->select('a.*', 'u.name as user_name')
            ->select(
                'a.id',
                'a.s3_file_name',
                'a.ori_name as display_name',
                'a.ori_name as filename',
                'a.type',
                'a.receipt_done',
                'a.ori_name',
                'a.type as attachment_type',
                'a.case_id',
                'a.remarks as remark',
                'a.created_at',
                'u.name as user_name'
            )
            ->where('a.case_id', '=',  $id)
            ->where('a.status', '<>',  99)
            ->orderBy('created_at','desc')
            ->get();

            $LoanAttachmentFrame = $LoanAttachment->merge($LoanAttachment2)->sortByDesc('created_at');;
        }

        // $LoanAttachment2 = DB::table('loan_attachment AS a')
        // ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
        // ->select('a.*', 'u.name as user_name')
        // ->where('a.case_id', '=',  $id)
        // ->where('a.status', '<>',  99)
        // ->where('a.file_type', '=', $fileType)
        // ->orderBy('created_at','desc')
        // ->get();

        return $LoanAttachmentFrame;
    }
}
