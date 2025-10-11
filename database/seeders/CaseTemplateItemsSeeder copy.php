<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTemplateItemsSeedercopy extends Seeder
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
            'name' => 'Call all parties & request documents',
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

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Reminder 1',
            'step_id' => 1,
            'order' => 3,
            'roles' => '7',
            'kpi' => 0,
            'days' => 2,
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
            'name' => 'Reminder 2',
            'step_id' => 1,
            'order' => 4,
            'roles' => '7',
            'kpi' => 0,
            'days' => 3,
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
            'name' => 'Reminder 3',
            'step_id' => 1,
            'order' => 5,
            'roles' => '7',
            'kpi' => 0,
            'days' => 4,
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
            'name' => 'Received All Documents to draft S&P',
            'step_id' => 1,
            'order' => 6,
            'roles' => '7',
            'kpi' => 0,
            'days' => 5,
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
            'name' => 'Write In to Purchaser',
            'step_id' => 1,
            'order' => 7,
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
            'remark' => 'cc sales & client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Write In to Psol',
            'step_id' => 1,
            'order' => 8,
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
            'remark' => 'cc sales & client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Write In to Vsol',
            'step_id' => 1,
            'order' => 9,
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
            'remark' => 'cc sales & client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Write in to Vendor',
            'step_id' => 1,
            'order' => 10,
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
            'remark' => 'cc sales & client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Write in to Vendor\'s Financier',
            'step_id' => 1,
            'order' => 11,
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
            'remark' => 'cc sales & client',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //S&P
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Conduct Land Search',
            'step_id' => 2,
            'order' => 1,
            'roles' => '7',
            'kpi' => 0,
            'days' => 5,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 1,
            'auto_dispatch' => 0,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Pre-Fix Appointment for Signing',
            'step_id' => 2,
            'order' => 2,
            'roles' => '7',
            'kpi' => 0,
            'days' => 5,
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
            'name' => 'Advise Payment for Legal Fees',
            'step_id' => 2,
            'order' => 3,
            'roles' => '5',
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
            'name' => 'Advise Payment for Balance Deposit',
            'step_id' => 2,
            'order' => 4,
            'roles' => '5',
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
            'name' => 'Advise Payment for Differential Sum',
            'step_id' => 2,
            'order' => 5,
            'roles' => '7',
            'kpi' => 0,
            'days' => 5,
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
            'name' => 'Conduct Bankruptcy Search',
            'step_id' => 2,
            'order' => 6,
            'roles' => '7',
            'kpi' => 0,
            'days' => 5,
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
            'name' => 'Prepare documents for signing',
            'step_id' => 2,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 6,
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
            'name' => 'Finalised S&P',
            'step_id' => 2,
            'order' => 7,
            'roles' => '7',
            'kpi' => 0,
            'days' => 6,
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
            'name' => 'Received Land Search',
            'step_id' => 2,
            'order' => 8,
            'roles' => '7',
            'kpi' => 0,
            'days' => 6,
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
            'name' => 'Received Bankruptcy Search',
            'step_id' => 2,
            'order' => 9,
            'roles' => '7',
            'kpi' => 0,
            'days' => 6,
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
            'name' => 'Land Search is clean',
            'step_id' => 2,
            'order' => 10,
            'roles' => '7',
            'kpi' => 0,
            'days' => 6,
            'start' => 0,
            'duration' => 0,
            'need_attachment' => 0,
            'auto_dispatch' => 0,
            'auto_receipt' => 1,
            'close_case' => 0,
            'email_template_id' => 0,
            'email_recurring' => 0,
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'No Bankrupt',
            'step_id' => 2,
            'order' => 11,
            'roles' => '7',
            'kpi' => 0,
            'days' => 6,
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
            'name' => 'Purchaser sign',
            'step_id' => 2,
            'order' => 12,
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
            'name' => 'Ask for PFS details & write in to us',
            'step_id' => 2,
            'order' => 13,
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
            'name' => 'Balance Deposit paid',
            'step_id' => 2,
            'order' => 14,
            'roles' => '5',
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
            'name' => 'Differential Sum Paid',
            'step_id' => 2,
            'order' => 15,
            'roles' => '5',
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
            'name' => 'Legal Fees paid',
            'step_id' => 2,
            'order' => 16,
            'roles' => '5',
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
            'name' => 'Payment Checklist',
            'step_id' => 2,
            'order' => 17,
            'roles' => '8',
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
            'name' => 'Vendor sign',
            'step_id' => 2,
            'order' => 18,
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
            'order' => 19,
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
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Vendor legal paid',
            'step_id' => 2,
            'order' => 20,
            'roles' => '5',
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
            'name' => 'Stamp SPA',
            'step_id' => 2,
            'order' => 21,
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
            'order' => 22,
            'roles' => '8',
            'kpi' => 0,
            'days' => 11,
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
            'name' => 'Recv PFS letter',
            'step_id' => 2,
            'order' => 22,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
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
            'name' => 'Send stamp SPA to Purchaser',
            'step_id' => 2,
            'order' => 23,
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
            'order' => 24,
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
            'name' => 'Send stamp SPA to Agent',
            'step_id' => 2,
            'order' => 25,
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
            'name' => 'Send stamp SPA to Vendor',
            'step_id' => 2,
            'order' => 26,
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
            'name' => 'Send stamp SPA to Valuer',
            'step_id' => 2,
            'order' => 27,
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
            'name' => 'send stamp SPA to Bank Lawyer',
            'step_id' => 2,
            'order' => 28,
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


        // POT
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Write in to Dev for DT / POT',
            'step_id' => 3,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 1,
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
            'name' => '1st reminder to Dev for DT / POT',
            'step_id' => 3,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 2,
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
            'name' => '2nd reminder to Dev for DT / POT',
            'step_id' => 3,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 3,
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
            'name' => '3rd reminder to Dev for DT / POT',
            'step_id' => 3,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 4,
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
            'name' => 'Received Dev reply for DT/POT',
            'step_id' => 3,
            'order' => 5,
            'roles' => '8',
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
            'name' => 'Send POT to Dev execution',
            'step_id' => 3,
            'order' => 6,
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
            'name' => '1st reminder to Dev to return POT',
            'step_id' => 3,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 18,
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
            'name' => '2nd reminder to Dev to return POT',
            'step_id' => 3,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 25,
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
            'name' => '3rd reminder to Dev to return POT',
            'step_id' => 3,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 32,
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
            'name' => '4th reminder to Dev to return POT',
            'step_id' => 3,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 39,
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
            'name' => '5th reminder to Dev to return POT',
            'step_id' => 3,
            'order' => 11,
            'roles' => '8',
            'kpi' => 0,
            'days' => 46,
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
            'name' => '6th reminder to Dev to return POT',
            'step_id' => 3,
            'order' => 12,
            'roles' => '8',
            'kpi' => 0,
            'days' => 53,
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
            'name' => 'Received POT signed by Dev',
            'step_id' => 3,
            'order' => 13,
            'roles' => '8',
            'kpi' => 0,
            'days' => 60,
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
            'name' => 'Adju POT',
            'step_id' => 3,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 60,
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
            'name' => '1st email to LHDN to expedite stamping',
            'step_id' => 3,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 65,
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
            'name' => '2nd email to LHDN to expedite stamping',
            'step_id' => 3,
            'order' => 15,
            'roles' => '8',
            'kpi' => 0,
            'days' => 70,
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
            'name' => '3rd email to LHDN to expedite stamping',
            'step_id' => 3,
            'order' => 16,
            'roles' => '8',
            'kpi' => 0,
            'days' => 75,
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
            'name' => 'Stamp POT',
            'step_id' => 3,
            'order' => 17,
            'roles' => '8',
            'kpi' => 0,
            'days' => 80,
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
            'name' => 'Stamp POT',
            'step_id' => 3,
            'order' => 18,
            'roles' => '8',
            'kpi' => 0,
            'days' => 80,
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
            'name' => 'Send stamped POT to Purchaser lawyer / land office to present',
            'step_id' => 3,
            'order' => 19,
            'roles' => '8',
            'kpi' => 0,
            'days' => 81,
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
            'name' => 'Collect original title from land office',
            'step_id' => 3,
            'order' => 20,
            'roles' => '8',
            'kpi' => 0,
            'days' => 75,
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
            'name' => 'Send original title to Vendor or its Financier',
            'step_id' => 3,
            'order' => 21,
            'roles' => '8',
            'kpi' => 0,
            'days' => 80,
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

        //DT
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Write in to Dev for DT / POT',
            'step_id' => 4,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 1,
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
            'name' => '1st reminder to Dev for DT / POT',
            'step_id' => 4,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 2,
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
            'name' => '2nd reminder to Dev for DT / POT',
            'step_id' => 4,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 3,
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
            'name' => '3rd reminder to Dev for DT / POT',
            'step_id' => 4,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 4,
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
            'name' => 'Received Dev reply for DT/POT',
            'step_id' => 4,
            'order' => 5,
            'roles' => '8',
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
            'name' => 'Send DT to Dev execution',
            'step_id' => 4,
            'order' => 6,
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
            'name' => '1st reminder to Dev to return DT',
            'step_id' => 4,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 18,
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
            'name' => '2nd reminder to Dev to return DT',
            'step_id' => 4,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 25,
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
            'name' => '3rd reminder to Dev to return DT',
            'step_id' => 4,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 32,
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
            'name' => '4th reminder to Dev to return DT',
            'step_id' => 4,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 39,
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
            'name' => '5th reminder to Dev to return DT',
            'step_id' => 4,
            'order' => 11,
            'roles' => '8',
            'kpi' => 0,
            'days' => 46,
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
            'name' => '6th reminder to Dev to return DT',
            'step_id' => 4,
            'order' => 12,
            'roles' => '8',
            'kpi' => 0,
            'days' => 53,
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
            'name' => 'Received DT signed by Dev',
            'step_id' => 4,
            'order' => 13,
            'roles' => '8',
            'kpi' => 0,
            'days' => 60,
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
            'name' => 'Adju DT',
            'step_id' => 4,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 60,
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
            'name' => '1st email to LHDN to expedite stamping',
            'step_id' => 4,
            'order' => 15,
            'roles' => '8',
            'kpi' => 0,
            'days' => 65,
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
            'name' => '2nd email to LHDN to expedite stamping',
            'step_id' => 4,
            'order' => 16,
            'roles' => '8',
            'kpi' => 0,
            'days' => 70,
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
            'name' => '3rd email to LHDN to expedite stamping',
            'step_id' => 4,
            'order' => 17,
            'roles' => '8',
            'kpi' => 0,
            'days' => 75,
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
            'name' => 'Stamp DT',
            'step_id' => 4,
            'order' => 18,
            'roles' => '8',
            'kpi' => 0,
            'days' => 80,
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
            'name' => 'Send stamped DT to Purchaser lawyer / land office to present',
            'step_id' => 4,
            'order' => 19,
            'roles' => '8',
            'kpi' => 0,
            'days' => 81,
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
            'name' => 'Collect original title from land office',
            'step_id' => 4,
            'order' => 20,
            'roles' => '8',
            'kpi' => 0,
            'days' => 75,
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
            'name' => 'Send original title to Vendor or its Financier',
            'step_id' => 4,
            'order' => 21,
            'roles' => '8',
            'kpi' => 0,
            'days' => 80,
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


        // MOT
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Adju MOT',
            'step_id' => 5,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 26,
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
            'name' => '1st email to LHDN to expedite stamping',
            'step_id' => 5,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 36,
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
            'name' => '2nd email to LHDN to expedite stamping',
            'step_id' => 5,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 43,
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
            'name' => '3rd email to LHDN to expedite stamping',
            'step_id' => 5,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 50,
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
            'name' => 'Stamp MOT',
            'step_id' => 5,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 56,
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
            'name' => 'Send stamped MOT to PFS',
            'step_id' => 5,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 60,
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


        // DCL
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Call Dev',
            'step_id' => 6,
            'order' => 1,
            'roles' => '7',
            'kpi' => 0,
            'days' => 1,
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
            'name' => 'Write in to Dev',
            'step_id' => 6,
            'order' => 2,
            'roles' => '7',
            'kpi' => 0,
            'days' => 1,
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
            'name' => 'Write in to Proprietor',
            'step_id' => 6,
            'order' => 3,
            'roles' => '7',
            'kpi' => 0,
            'days' => 1,
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
            'name' => 'Write in to Master Chargee',
            'step_id' => 6,
            'order' => 4,
            'roles' => '7',
            'kpi' => 0,
            'days' => 1,
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
            'name' => '1st reminder to Dev',
            'step_id' => 6,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 3,
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
            'name' => '1st reminder to Proprietor',
            'step_id' => 6,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 3,
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
            'name' => '1st reminder to Master Chargee',
            'step_id' => 6,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 3,
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
            'name' => '2nd reminder to Dev',
            'step_id' => 6,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 6,
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
            'name' => '2nd reminder to Proprietor',
            'step_id' => 6,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 6,
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
            'name' => '2nd reminder to Master Chargee',
            'step_id' => 6,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 6,
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
            'name' => '3rd reminder to Dev',
            'step_id' => 6,
            'order' => 11,
            'roles' => '8',
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
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => '3rd reminder to Proprietor',
            'step_id' => 6,
            'order' => 12,
            'roles' => '8',
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
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => '3rd reminder to Master Chargee',
            'step_id' => 6,
            'order' => 13,
            'roles' => '8',
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
            'remark' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_item')->insert([ 
            'name' => 'Receive DCL',
            'step_id' => 6,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
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
            'name' => 'Receive proprietor confirmation',
            'step_id' => 6,
            'order' => 15,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
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
            'name' => 'Receive disclaimer from Master Chargee',
            'step_id' => 6,
            'order' => 16,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
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

        // NOA
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Send stamp SPA to Developer',
            'step_id' => 7,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
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
            'name' => 'Send stamp SPA to Proprietor',
            'step_id' => 7,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 12,
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
            'name' => 'Serve stamped DOA to Developer',
            'step_id' => 7,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 71,
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
            'name' => 'Serve stamped DRR to Developer',
            'step_id' => 7,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 63,
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
            'name' => 'Serve stamped DOA to Proprietor',
            'step_id' => 7,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 71,
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
            'name' => 'Serve stamped DRR to Proprietor',
            'step_id' => 7,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 63,
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


        // POT Consent
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Request Blanket Consent from Developer',
            'step_id' => 8,
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
            'name' => '1st reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 18,
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
            'name' => '2nd reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 23,
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
            'name' => '3rd reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 28,
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
            'name' => '4th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 33,
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
            'name' => '5th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 38,
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
            'name' => '6th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 43,
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
            'name' => '7th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 48,
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
            'name' => '8th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 53,
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
            'name' => '9th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 58,
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
            'name' => '10th reminder to Dev for Blanket Consent',
            'step_id' => 8,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 63,
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
            'name' => 'Receive Blanket Consent from Developer',
            'step_id' => 8,
            'order' => 11,
            'roles' => '8',
            'kpi' => 0,
            'days' => 68,
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
            'name' => 'Apply CTC Blanket Consent from Land Office',
            'step_id' => 8,
            'order' => 12,
            'roles' => '8',
            'kpi' => 0,
            'days' => 68,
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
            'name' => '1st reminder to Land office for CTC Blanket Consent',
            'step_id' => 8,
            'order' => 13,
            'roles' => '8',
            'kpi' => 0,
            'days' => 73,
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
            'name' => '2nd reminder to Land office for CTC Blanket Consent',
            'step_id' => 8,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 78,
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
            'name' => '3rd reminder to Land office for CTC Blanket Consent',
            'step_id' => 8,
            'order' => 15,
            'roles' => '8',
            'kpi' => 0,
            'days' => 82,
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
            'name' => 'Receive CTC Blanket Consent from land office',
            'step_id' => 8,
            'order' => 16,
            'roles' => '8',
            'kpi' => 0,
            'days' => 87,
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
            'name' => 'Send Blanket Consent to Land Office to present',
            'step_id' => 8,
            'order' => 17,
            'roles' => '8',
            'kpi' => 0,
            'days' => 88,
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


        // DT Consent
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Request DT Consent from Developer',
            'step_id' => 9,
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
            'name' => '1st reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 2,
            'roles' => '8',
            'kpi' => 0,
            'days' => 18,
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
            'name' => '2nd reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 3,
            'roles' => '8',
            'kpi' => 0,
            'days' => 23,
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
            'name' => '3rd reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 4,
            'roles' => '8',
            'kpi' => 0,
            'days' => 28,
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
            'name' => '4th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 5,
            'roles' => '8',
            'kpi' => 0,
            'days' => 33,
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
            'name' => '5th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 6,
            'roles' => '8',
            'kpi' => 0,
            'days' => 38,
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
            'name' => '6th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 7,
            'roles' => '8',
            'kpi' => 0,
            'days' => 43,
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
            'name' => '7th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 8,
            'roles' => '8',
            'kpi' => 0,
            'days' => 48,
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
            'name' => '8th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 9,
            'roles' => '8',
            'kpi' => 0,
            'days' => 53,
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
            'name' => '9th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 10,
            'roles' => '8',
            'kpi' => 0,
            'days' => 58,
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
            'name' => '10th reminder to Dev for DT Consent',
            'step_id' => 9,
            'order' => 11,
            'roles' => '8',
            'kpi' => 0,
            'days' => 63,
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
            'name' => 'Receive DT Consent from Developer',
            'step_id' => 9,
            'order' => 12,
            'roles' => '8',
            'kpi' => 0,
            'days' => 68,
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
            'name' => 'Apply CTC DT Consent from Land Office',
            'step_id' => 9,
            'order' => 13,
            'roles' => '8',
            'kpi' => 0,
            'days' => 68,
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
            'name' => '1st reminder to Land office for CTC DT Consent',
            'step_id' => 9,
            'order' => 14,
            'roles' => '8',
            'kpi' => 0,
            'days' => 73,
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
            'name' => '2nd reminder to Land office for CTC DTConsent',
            'step_id' => 9,
            'order' => 15,
            'roles' => '8',
            'kpi' => 0,
            'days' => 78,
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
            'name' => '3rd reminder to Land office for CTC DT Consent',
            'step_id' => 9,
            'order' => 16,
            'roles' => '8',
            'kpi' => 0,
            'days' => 82,
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
            'name' => 'Receive CTC DT Consent from land office',
            'step_id' => 9,
            'order' => 17,
            'roles' => '8',
            'kpi' => 0,
            'days' => 87,
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
            'name' => 'Send DT Consent to PFS / Land Office to present',
            'step_id' => 9,
            'order' => 18,
            'roles' => '8',
            'kpi' => 0,
            'days' => 88,
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


        // Consent1
        DB::table('checklist_template_item')->insert([ 
            'name' => 'Request letter of no objection from Vendor Financier',
            'step_id' => 10,
            'order' => 1,
            'roles' => '8',
            'kpi' => 0,
            'days' => 1,
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
            'name' => '1st reminder to VF for letter of no objection',
            'step_id' => 10,
            'order' => 2,
            'roles' => '1',
            'kpi' => 0,
            'days' => 5,
            'start' => 0,
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
