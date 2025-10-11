<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Models\CaseEmailSchedule;
use App\Models\LoanCase;
use App\Models\EmailTemplateMain;
use App\Models\EmailTemplateDetails;

use Illuminate\Support\Facades\Mail;

class EmailService{

    public function __construct(){

    }

    public function sendEmail($toUsers,$mailContent,$ccUsers=[],$bccUsers=[]){
        Mail::to($toUsers)
            ->cc($ccUsers)
            ->bcc($bccUsers)
            ->queue($mailContent);
    }

    //freely create custom email here
}