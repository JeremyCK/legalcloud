<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTemplateItemsbakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //open
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Open file',
            'step_id' => 1,
            'order' => 1,
            'roles' => '6',
            'kpi' => 0,
            'days' => 1,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'open_case' => 1,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Call client',
            'step_id' => 1,
            'order' => 2,
            'roles' => '7',
            'kpi' => 0,
            'days' => 1,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //S&P
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Ask for PFS details & write in to us',
            'step_id' => 2,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Recv PFS letter',
            'step_id' => 2,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Balance Deposit paid',
            'step_id' => 2,
            'order' => 3,
            'roles' => '5',
            'kpi' => 0,
            'days' => 7,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 1,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '(auto generate receipt) to client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Differential Sum Paid',
            'step_id' => 2,
            'order' => 4,
            'roles' => '5',
            'kpi' => 0,
            'days' => 7,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 1,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '(auto generate receipt) to client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Legal Fees paid',
            'step_id' => 2,
            'order' => 4,
            'roles' => '5',
            'kpi' => 0,
            'days' => 7,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 1,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '(auto generate receipt) to client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Do bankruptcy search',
            'step_id' => 2,
            'order' => 5,
            'roles' => '7',
            'kpi' => 0,
            'days' => 7,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '(auto generate receipt) to client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Do bankruptcy search',
            'step_id' => 2,
            'order' => 6,
            'roles' => '7',
            'kpi' => 0,
            'days' => 7,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive bankruptcy search',
            'step_id' => 2,
            'order' => 7,
            'roles' => '7',
            'kpi' => 0,
            'days' => 7,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Vendor sign',
            'step_id' => 2,
            'order' => 8,
            'roles' => '7',
            'kpi' => 0,
            'days' => 9,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Balance Deposit released to Vendor',
            'step_id' => 2,
            'order' => 9,
            'roles' => '7',
            'kpi' => 0,
            'days' => 9,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => 'double ensure all the balance settlement completed before move to next (checkbox list --> LHYEO will provide)',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Vendor legal paid',
            'step_id' => 2,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 9,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 1,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '(generate receipt) to vendor',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Stamp SPA',
            'step_id' => 2,
            'order' => 11,
            'roles' => '8',
            'kpi' => 0,
            'days' => 10,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive stamp SPA',
            'step_id' => 2,
            'order' => 12,
            'roles' => '8',
            'kpi' => 0,
            'days' => 11,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Send stamp SPA to agent, Purchaser, Vendor, Valuer and Bank Lawyer',
            'step_id' => 2,
            'order' => 13,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Agent Fees released to Agent',
            'step_id' => 2,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // Consent1

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Request letter of no objection from Vendor Financier',
            'step_id' => 3,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 13,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Auto reminder to VF for letter of no objection',
            'step_id' => 3,
            'order' => 2,
            'roles' => '1',
            'kpi' => 0,
            'days' => 13,
            'start' => 14,
            'duration' => 7,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 1,
            'email_recurring' => 1,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Received letter of no objection',
            'step_id' => 3,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 30,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Final reminder to PFS to apply consent together',
            'step_id' => 3,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 31,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Apply carian rasmi',
            'step_id' => 3,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 31,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive carian rasmi',
            'step_id' => 3,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 38,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Submitted consent to land office, no waiting PFS',
            'step_id' => 3,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 40,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Auto reminder to land office for consent',
            'step_id' => 3,
            'order' => 7,
            'roles' => '1',
            'kpi' => 0,
            'days' => 20,
            'start' => 0,
            'duration' => 14,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 2,
            'email_recurring' => 1,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Consent approved & Collected',
            'step_id' => 3,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 100,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //MOT2
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Adjust MOT',
            'step_id' => 7,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 26,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Stamp MOT',
            'step_id' => 7,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 56,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //CKHT
        DB::table('checklist_template_item')->insert([ 
            'name' => 'File CKHT (request and receive)',
            'step_id' => 9,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 16,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


         //Redemption
         DB::table('checklist_template_item')->insert([ 
            'name' => 'Request redemption statement',
            'step_id' => 11,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Auto reminder to Vendor Financier for redemption statement',
            'step_id' => 11,
            'order' => 2,
            'roles' => '1',
            'kpi' => 0,
            'days' => 0,
            'start' => 0,
            'duration' => 3,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 3,
            'email_recurring' => 1,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive redemption statement',
            'step_id' => 11,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 26,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Reply bank lawyer to advise redemption',
            'step_id' => 11,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 26,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Auto reminder to bank lawyer for redemption sum',
            'step_id' => 11,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 0,
            'start' => 0,
            'duration' => 5,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 4,
            'email_recurring' => 1,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive redemption sum',
            'step_id' => 11,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 40,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive bank\'s undertaking in favour of Vendor',
            'step_id' => 11,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 26,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Forward Form 16N/DRR/Change of Name/F19G to Vendor Bank',
            'step_id' => 11,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 41,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Auto reminder to VF for return executed redeemed docs',
            'step_id' => 11,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 0,
            'start' => 0,
            'duration' => 3,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 5,
            'email_recurring' => 1,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Recv redeemed docs',
            'step_id' => 11,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 55,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Stamp F16N/DRR & revoke DRR',
            'step_id' => 11,
            'order' => 11,
            'roles' => '8',
            'kpi' => 0,
            'days' => 57,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Send all original to bank lawyer',
            'step_id' => 11,
            'order' => 12,
            'roles' => '8',
            'kpi' => 0,
            'days' => 60,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //Handover
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Auto reminder to bank lawyer for balance loan sum',
            'step_id' => 12,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 0,
            'start' => 0,
            'duration' => 5,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 6,
            'email_recurring' => 1,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive balance loan sum',
            'step_id' => 12,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 80,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Inform P that we are arranging keys',
            'step_id' => 12,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 84,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Inform V to deliver keys',
            'step_id' => 12,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 84,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Pay all outstanding bills',
            'step_id' => 12,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 85,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Apportionment of outgoings done, when P will collect keys',
            'step_id' => 12,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 85,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Purchaser paid apportionment and collect keys',
            'step_id' => 12,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 86,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Release BPP to V',
            'step_id' => 12,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 86,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Release agent fees',
            'step_id' => 12,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 86,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Release agent fees',
            'step_id' => 12,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 86,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //NOA
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Serve NOA',
            'step_id' => 13,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 51,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         //Close
         DB::table('checklist_template_item')->insert([ 
            'name' => 'Close File',
            'step_id' => 14,
            'order' => 1,
            'roles' => '7',
            'kpi' => 0,
            'days' => 90,
            'start' => 0,
            'duration' => 0,// unit in day
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 0,
            'close_case' => 1,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

    }
}
