<?php

namespace App\Console\Commands;

use App\Models\LoanCaseKivNotes;
use Illuminate\Console\Command;

class SendCustomerReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email customer to remind submit document';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*
            ** create schedule command : php artisan make:command {name}

            1) filter customer needed to send email
            2) get email template
            3) send email
        */

        $LoanCaseKivNotes = new LoanCaseKivNotes();
        
        $LoanCaseKivNotes->case_id =  0;
        $LoanCaseKivNotes->notes =  'test';
        $LoanCaseKivNotes->label =  'job-tesy';
        $LoanCaseKivNotes->role = '';
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->created_by = 1;
        $LoanCaseKivNotes->save();

        // return 0;
    }
}
