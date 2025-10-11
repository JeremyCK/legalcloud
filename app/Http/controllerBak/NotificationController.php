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
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Banks::all();

        return view('dashboard.banks.index', ['banks' => $banks]);
    }

    public function retriveNotification()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $notification = [];
        $notification_receipt = [];

        $date = Carbon::now()->subDays(1);

        $notification = DB::table('notification as v')
            ->leftJoin('voucher_main AS m', 'm.id', '=', 'v.parameter2')
            ->leftJoin('loan_case AS l', 'l.id', '=', 'v.parameter1')
                ->where('v.bln_read', '=', '0')
                ->where('v.module', '=', 'voucher')
                ->where('m.status', '<>', 99)
                ->select('v.*','m.id as voucher_id')
                ->where('v.role', 'like', '%' . $userRoles . '%')
                ->orderBy('v.created_at', 'desc');

                if (in_array($userRoles, ['lawyer', 'clerk', 'chambering', 'sales']))
                {
                    // $notification = $notification->where('v.user_id', '=',  $current_user->id);
                        $notification = $notification->where(function ($q) use ($current_user) {
                                $q->where('l.lawyer_id', '=',  $current_user->id)
                                    ->orWhere('l.clerk_id', '=',  $current_user->id);
                            });
                }

                
                if (in_array($userRoles, ['receptionist']))
                {
                    $notification = $notification->where('v.user_id', '=',  $current_user->id);
                        //     });
                }

                if ($userRoles <> 'account')
                {
                    $notification = $notification->where('v.created_at', '>=',  $date);
                }

       $notification = $notification->get();

       $notificationVoucherAccount = DB::table('notification as v')
       ->join('loan_case as l', 'l.id', '=', 'v.parameter1')
       ->join('voucher_main as vm', 'vm.id', '=', 'v.parameter2')
       ->where('v.bln_read', '=', '0')
    //    ->where('l.branch_id', '=', $branchInfo['branch'][$i]->id)
       ->where('vm.status', '=', 1)
       ->where('vm.account_approval', '=', 0)
       ->where('v.role', 'like', '%' . $userRoles . '%')
       ->where('v.desc', 'like', '%request%')
       ->select('v.*', 'l.case_ref_no', 'vm.payee', 'vm.transaction_id')
       ->orderBy('created_at', 'desc');

       if (in_array($userRoles, ['lawyer', 'clerk', 'chambering', 'sales']))
       {
           // $notification = $notification->where('v.user_id', '=',  $current_user->id);
               $notificationVoucherAccount = $notificationVoucherAccount->where(function ($q) use ($current_user) {
                       $q->where('l.lawyer_id', '=',  $current_user->id)
                           ->orWhere('l.clerk_id', '=',  $current_user->id);
                   });
       }

       
       if (in_array($userRoles, ['receptionist']))
       {
           $notificationVoucherAccount = $notificationVoucherAccount->where('v.user_id', '=',  $current_user->id);
               //     });
       }

       if ($userRoles <> 'account')
       {
           $notificationVoucherAccount = $notificationVoucherAccount->where('v.created_at', '>=',  $date);
       }

$notificationVoucherAccount = $notificationVoucherAccount->get();


        // if ($userRoles == "admin") {
        //     $notification = DB::table('notification as v')
        //     ->leftJoin('voucher_main AS m', 'm.id', '=', 'v.parameter2')
        //         ->where('v.bln_read', '=', '0')
        //         ->where('v.module', '=', 'voucher')
        //         ->where('m.status', '<>', 99)
        //         ->where('v.role', 'like', '%admin%')
        //         ->orderBy('v.created_at', 'desc')
        //         ->get();

        //     // $notification_receipt = DB::table('notification as v')
        //     //     ->where('v.bln_read', '=', '0')
        //     //     ->where('v.module', '=', 'receipt')
        //     //     ->where('v.role', 'like', '%admin%')
        //     //     ->orderBy('created_at', 'desc')
        //     //     ->get();
        // } else {
        //     // $notification = DB::table('notification as v')
        //     //     ->leftJoin('voucher_main AS m', 'm.id', '=', 'v.parameter2')
        //     //     ->where('v.bln_read', '=', '0')
        //     //     ->where('v.module', '=', 'voucher')
        //     //     ->where('m.status', '<>', 99)
        //     //     ->where('v.role', 'like', '%' . $userRoles . '%')
        //     //     ->orWhere('v.user_id', '=',  $current_user->id)
        //     //     ->orderBy('v.created_at', 'desc')
        //     //     ->get();

        //         $notification = DB::table('notification as v')
        //         ->leftJoin('voucher_main AS m', 'm.id', '=', 'v.parameter2')
        //         ->where('v.bln_read', '=', '0')
        //         ->where('v.module', '=', 'voucher')
        //         ->where('m.status', '<>', 99)
        //         ->where('v.role', 'like', '%' . $userRoles . '%')
        //         ->orWhere(function ($q) use ($current_user) {
        //             $q->where('v.user_id', '=',  $current_user->id)
        //                 ->orWhere('v.created_by', '=',  $current_user->id);
        //         })
        //         ->orderBy('v.created_at', 'desc')
        //         ->get();
                

        //     // $notification_receipt = DB::table('notification as v')
        //     //     ->where('v.bln_read', '=', '0')
        //     //     ->where('v.module', '=', 'receipt')
        //     //     ->where('v.role', 'like', '%' . $userRoles . '%')
        //     //     ->orWhere('v.user_id', '=',  $current_user->id)
        //     //     ->orderBy('created_at', 'desc')
        //     //     ->get();
        // }

        // $notification = DB::table('notification as v')
        //     ->where('v.bln_read', '=', '0')
        //     ->where('v.role', 'like', '%' . $userRoles . '%')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        return response()->json([
            'status' => 1, 'data' => $notification,
            'notificationVoucherAccount' => $notificationVoucherAccount,
            'notification_receipt' => $notification_receipt
        ]);
    }

    public function createNotification($desc)
    {
        $current_user = auth()->user();

        $Notification  = new Notification();
        $Notification->name = $current_user->name;
        $Notification->desc = $desc;
        $Notification->user_id = 0;
        $Notification->role = 'account|admin|management';
        // $Notification->parameter1 = $id;
        // $Notification->parameter2 = $voucherMain->id;
        $Notification->module = 'trust';
        $Notification->bln_read = 0;
        $Notification->status = 1;
        $Notification->created_at = now();
        $Notification->created_by = $current_user->id;
        $Notification->save();
    }

    public function createNotificationV2($param_notification)
    {
        $current_user = auth()->user();

        $Notification  = new Notification();
        $Notification->name = $current_user->name;
        $Notification->desc = $param_notification['desc'];
        $Notification->user_id = $current_user->id;
        $Notification->role = $param_notification['role'];
        $Notification->parameter1 = $param_notification['parameter1'];
        $Notification->parameter2 = $param_notification['parameter2'];
        $Notification->module = $param_notification['module'];
        $Notification->bln_read = 0;
        $Notification->status = 1;
        $Notification->created_at = now();
        $Notification->created_by = $current_user->id;
        $Notification->save();
    }

    public static function createVoucherNotification($obj)
    {
        $current_user = auth()->user();

        $Notification  = new Notification();
        $Notification->name = $current_user->name;
        $Notification->desc = $obj->notification_desc;
        $Notification->user_id = $current_user->id;
        $Notification->role = $obj->role;
        $Notification->parameter1 = $obj->case_id;
        $Notification->parameter2 = $obj->id;
        $Notification->module = $obj->module;
        $Notification->bln_read = 0;
        $Notification->status = 1;
        $Notification->created_at = now();
        $Notification->created_by = $current_user->id;
        $Notification->save();
    }

    public static function readNotification($voucher_id)
    {
        $Notification = Notification::where('bln_read', '=', '0')->where('role', '=', 'account|admin|management')->where('parameter2', '=', $voucher_id)->first();

        if ($Notification) {
            $Notification->bln_read = 1;
            $Notification->save();
        }
    }

    public function openNotification($id)
    {
        $Notification = Notification::where('id', '=', $id)->first();
        // $Notification->bln_read = 1;
        // $Notification->save();

        


        return response()->json(['status' => 1, 'data' => $Notification->parameter2]);
    }
}
