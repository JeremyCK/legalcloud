<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTemplateItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('checklist_template_item')->insert([ 
            "name" => "open file",
            "step_id" => "1",
            "order" => "1",
            "roles" => "5",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Call all parties & request documents",
            "step_id" => "1",
            "order" => "2",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Create Group Chat ",
            "step_id" => "1",
            "order" => "3",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Reminder 1",
            "step_id" => "1",
            "order" => "4",
            "roles" => "7",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Reminder 2",
            "step_id" => "1",
            "order" => "5",
            "roles" => "7",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Reminder 3",
            "step_id" => "1",
            "order" => "6",
            "roles" => "7",
            "days" => "4",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Received All Documents to draft S&P",
            "step_id" => "1",
            "order" => "7",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write In to Purchaser",
            "step_id" => "1",
            "order" => "8",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write In to Psol",
            "step_id" => "1",
            "order" => "9",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write In to Vsol",
            "step_id" => "1",
            "order" => "10",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write in to Vendor",
            "step_id" => "1",
            "order" => "11",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write in to Vendor's Financier",
            "step_id" => "1",
            "order" => "12",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Conduct Land Search",
            "step_id" => "1",
            "order" => "1",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Pre-Fix Appointment for Signing",
            "step_id" => "1",
            "order" => "2",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Advise Payment for Legal Fees",
            "step_id" => "1",
            "order" => "3",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Advise Payment for Balance Deposit",
            "step_id" => "1",
            "order" => "4",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Advise Payment for Differential Sum",
            "step_id" => "1",
            "order" => "5",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Conduct Bankruptcy Search",
            "step_id" => "1",
            "order" => "6",
            "roles" => "7",
            "days" => "5",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "prepare documents for signing",
            "step_id" => "1",
            "order" => "7",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Finalised S&P",
            "step_id" => "1",
            "order" => "8",
            "roles" => "7",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Received Land Search",
            "step_id" => "1",
            "order" => "9",
            "roles" => "7",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Received Bankruptcy Search",
            "step_id" => "1",
            "order" => "10",
            "roles" => "7",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Land Search is clean",
            "step_id" => "1",
            "order" => "11",
            "roles" => "7",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "No Bankrupt",
            "step_id" => "1",
            "order" => "12",
            "roles" => "7",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Purchaser sign",
            "step_id" => "1",
            "order" => "13",
            "roles" => "7",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "ask for PFS details & write in to us",
            "step_id" => "1",
            "order" => "14",
            "roles" => "7",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Balance Deposit paid ",
            "step_id" => "1",
            "order" => "15",
            "roles" => "5",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Differential Sum Paid ",
            "step_id" => "1",
            "order" => "16",
            "roles" => "5",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Legal Fees paid ",
            "step_id" => "1",
            "order" => "17",
            "roles" => "5",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Payment Checklist",
            "step_id" => "1",
            "order" => "18",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Vendor sign",
            "step_id" => "1",
            "order" => "19",
            "roles" => "7",
            "days" => "9",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Balance Deposit released to Vendor ",
            "step_id" => "1",
            "order" => "20",
            "roles" => "7",
            "days" => "9",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Vendor legal paid ",
            "step_id" => "1",
            "order" => "21",
            "roles" => "5",
            "days" => "9",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp SPA",
            "step_id" => "1",
            "order" => "22",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive stamp SPA",
            "step_id" => "1",
            "order" => "23",
            "roles" => "8",
            "days" => "11",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "recv PFS letter",
            "step_id" => "1",
            "order" => "24",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Purchaser",
            "step_id" => "1",
            "order" => "25",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Agent Fees released to Agent",
            "step_id" => "1",
            "order" => "26",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Agent",
            "step_id" => "1",
            "order" => "27",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Vendor",
            "step_id" => "1",
            "order" => "28",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Valuer",
            "step_id" => "1",
            "order" => "29",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Bank Lawyer",
            "step_id" => "1",
            "order" => "30",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Dev for DT / POT",
            "step_id" => "3",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev for DT / POT",
            "step_id" => "3",
            "order" => "2",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev for DT / POT",
            "step_id" => "3",
            "order" => "3",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev for DT / POT",
            "step_id" => "3",
            "order" => "4",
            "roles" => "8",
            "days" => "4",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Dev reply for DT/POT",
            "step_id" => "3",
            "order" => "5",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send POT to Dev execution",
            "step_id" => "3",
            "order" => "6",
            "roles" => "8",
            "days" => "13",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev to return POT",
            "step_id" => "3",
            "order" => "7",
            "roles" => "8",
            "days" => "18",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev to return POT",
            "step_id" => "3",
            "order" => "8",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev to return POT",
            "step_id" => "3",
            "order" => "9",
            "roles" => "8",
            "days" => "32",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to Dev to return POT",
            "step_id" => "3",
            "order" => "10",
            "roles" => "8",
            "days" => "39",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to Dev to return POT",
            "step_id" => "3",
            "order" => "11",
            "roles" => "8",
            "days" => "46",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to Dev to return POT",
            "step_id" => "3",
            "order" => "12",
            "roles" => "8",
            "days" => "53",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received POT signed by Dev",
            "step_id" => "3",
            "order" => "13",
            "roles" => "8",
            "days" => "60",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "adju POT",
            "step_id" => "3",
            "order" => "14",
            "roles" => "8",
            "days" => "60",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st email to LHDN to expedite stamping",
            "step_id" => "3",
            "order" => "15",
            "roles" => "8",
            "days" => "65",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd email to LHDN to expedite stamping",
            "step_id" => "3",
            "order" => "16",
            "roles" => "8",
            "days" => "70",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd email to LHDN to expedite stamping",
            "step_id" => "3",
            "order" => "17",
            "roles" => "8",
            "days" => "75",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp POT",
            "step_id" => "3",
            "order" => "18",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamped POT to Purchaser lawyer / land office to present",
            "step_id" => "3",
            "order" => "19",
            "roles" => "8",
            "days" => "81",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Collect original title from land office",
            "step_id" => "3",
            "order" => "20",
            "roles" => "8",
            "days" => "75",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send original title to Vendor or its Financier",
            "step_id" => "3",
            "order" => "21",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Dev for DT / POT",
            "step_id" => "4",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev for DT / POT",
            "step_id" => "4",
            "order" => "2",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev for DT / POT",
            "step_id" => "4",
            "order" => "3",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev for DT / POT",
            "step_id" => "4",
            "order" => "4",
            "roles" => "8",
            "days" => "4",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Dev reply for DT/POT",
            "step_id" => "4",
            "order" => "5",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send DT to Dev execution",
            "step_id" => "4",
            "order" => "6",
            "roles" => "8",
            "days" => "13",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev to return DT",
            "step_id" => "4",
            "order" => "7",
            "roles" => "8",
            "days" => "18",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev to return DT",
            "step_id" => "4",
            "order" => "8",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev to return DT",
            "step_id" => "4",
            "order" => "9",
            "roles" => "8",
            "days" => "32",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to Dev to return DT",
            "step_id" => "4",
            "order" => "10",
            "roles" => "8",
            "days" => "39",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to Dev to return DT",
            "step_id" => "4",
            "order" => "11",
            "roles" => "8",
            "days" => "46",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to Dev to return DT",
            "step_id" => "4",
            "order" => "12",
            "roles" => "8",
            "days" => "53",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received DT signed by Dev",
            "step_id" => "4",
            "order" => "13",
            "roles" => "8",
            "days" => "60",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "adju DT",
            "step_id" => "4",
            "order" => "14",
            "roles" => "8",
            "days" => "60",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st email to LHDN to expedite stamping",
            "step_id" => "4",
            "order" => "15",
            "roles" => "8",
            "days" => "65",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd email to LHDN to expedite stamping",
            "step_id" => "4",
            "order" => "16",
            "roles" => "8",
            "days" => "70",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd email to LHDN to expedite stamping",
            "step_id" => "4",
            "order" => "17",
            "roles" => "8",
            "days" => "75",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp DT",
            "step_id" => "4",
            "order" => "18",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamped DT to Purchaser lawyer / land office to present",
            "step_id" => "4",
            "order" => "19",
            "roles" => "8",
            "days" => "81",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Collect original title from land office",
            "step_id" => "4",
            "order" => "20",
            "roles" => "8",
            "days" => "75",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send original title to Vendor or its Financier",
            "step_id" => "4",
            "order" => "21",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "adju MOT",
            "step_id" => "5",
            "order" => "1",
            "roles" => "8",
            "days" => "26",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st email to LHDN to expedite stamping",
            "step_id" => "5",
            "order" => "2",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd email to LHDN to expedite stamping",
            "step_id" => "5",
            "order" => "3",
            "roles" => "8",
            "days" => "43",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd email to LHDN to expedite stamping",
            "step_id" => "5",
            "order" => "4",
            "roles" => "8",
            "days" => "50",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp MOT",
            "step_id" => "5",
            "order" => "5",
            "roles" => "8",
            "days" => "56",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamped MOT to PFS",
            "step_id" => "5",
            "order" => "6",
            "roles" => "8",
            "days" => "60",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "call Dev",
            "step_id" => "6",
            "order" => "1",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write in to Dev",
            "step_id" => "6",
            "order" => "2",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write in to Proprietor",
            "step_id" => "6",
            "order" => "3",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Write in to Master Chargee",
            "step_id" => "6",
            "order" => "4",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev",
            "step_id" => "6",
            "order" => "5",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Proprietor",
            "step_id" => "6",
            "order" => "6",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Master Chargee",
            "step_id" => "6",
            "order" => "7",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev",
            "step_id" => "6",
            "order" => "8",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Proprietor",
            "step_id" => "6",
            "order" => "9",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Master Chargee",
            "step_id" => "6",
            "order" => "10",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev",
            "step_id" => "6",
            "order" => "11",
            "roles" => "8",
            "days" => "9",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Proprietor",
            "step_id" => "6",
            "order" => "12",
            "roles" => "8",
            "days" => "9",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Master Chargee",
            "step_id" => "6",
            "order" => "13",
            "roles" => "8",
            "days" => "9",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive DCL",
            "step_id" => "6",
            "order" => "14",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive proprietor confirmation",
            "step_id" => "6",
            "order" => "15",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive disclaimer from Master Chargee",
            "step_id" => "6",
            "order" => "16",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Developer",
            "step_id" => "7",
            "order" => "1",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamp SPA to Proprietor",
            "step_id" => "7",
            "order" => "2",
            "roles" => "8",
            "days" => "12",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve stamped DOA to Developer",
            "step_id" => "7",
            "order" => "3",
            "roles" => "8",
            "days" => "71",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve stamped DRR to Developer",
            "step_id" => "7",
            "order" => "4",
            "roles" => "8",
            "days" => "63",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve stamped DOA to Proprietor",
            "step_id" => "7",
            "order" => "5",
            "roles" => "8",
            "days" => "71",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve stamped DRR to Proprietor",
            "step_id" => "7",
            "order" => "6",
            "roles" => "8",
            "days" => "63",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Request Blanket Consent from Developer",
            "step_id" => "8",
            "order" => "1",
            "roles" => "8",
            "days" => "13",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "2",
            "roles" => "8",
            "days" => "18",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "3",
            "roles" => "8",
            "days" => "23",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "4",
            "roles" => "8",
            "days" => "28",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "5",
            "roles" => "8",
            "days" => "33",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "6",
            "roles" => "8",
            "days" => "38",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "7",
            "roles" => "8",
            "days" => "43",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "7th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "8",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "8th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "9",
            "roles" => "8",
            "days" => "53",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "9th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "10",
            "roles" => "8",
            "days" => "58",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "10th reminder to Dev for Blanket Consent",
            "step_id" => "8",
            "order" => "11",
            "roles" => "8",
            "days" => "63",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Receive Blanket Consent from Developer",
            "step_id" => "8",
            "order" => "12",
            "roles" => "8",
            "days" => "68",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Apply CTC Blanket Consent from Land Office",
            "step_id" => "8",
            "order" => "13",
            "roles" => "8",
            "days" => "68",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Land office for CTC Blanket Consent",
            "step_id" => "8",
            "order" => "14",
            "roles" => "8",
            "days" => "73",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Land office for CTC Blanket Consent",
            "step_id" => "8",
            "order" => "15",
            "roles" => "8",
            "days" => "78",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Land office for CTC Blanket Consent",
            "step_id" => "8",
            "order" => "16",
            "roles" => "8",
            "days" => "82",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive CTC Blanket Consent from land office",
            "step_id" => "8",
            "order" => "17",
            "roles" => "8",
            "days" => "87",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send Blanket Consent to Land Office to present",
            "step_id" => "8",
            "order" => "18",
            "roles" => "8",
            "days" => "88",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Request DT Consent from Developer",
            "step_id" => "9",
            "order" => "1",
            "roles" => "8",
            "days" => "13",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "2",
            "roles" => "8",
            "days" => "18",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "3",
            "roles" => "8",
            "days" => "23",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "4",
            "roles" => "8",
            "days" => "28",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "5",
            "roles" => "8",
            "days" => "33",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "6",
            "roles" => "8",
            "days" => "38",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "7",
            "roles" => "8",
            "days" => "43",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "7th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "8",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "8th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "9",
            "roles" => "8",
            "days" => "53",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "9th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "10",
            "roles" => "8",
            "days" => "58",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "10th reminder to Dev for DT Consent",
            "step_id" => "9",
            "order" => "11",
            "roles" => "8",
            "days" => "63",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Receive DT Consent from Developer",
            "step_id" => "9",
            "order" => "12",
            "roles" => "8",
            "days" => "68",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Apply CTC DT Consent from Land Office",
            "step_id" => "9",
            "order" => "13",
            "roles" => "8",
            "days" => "68",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Land office for CTC DT Consent",
            "step_id" => "9",
            "order" => "14",
            "roles" => "8",
            "days" => "73",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Land office for CTC DTConsent",
            "step_id" => "9",
            "order" => "15",
            "roles" => "8",
            "days" => "78",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Land office for CTC DT Consent",
            "step_id" => "9",
            "order" => "16",
            "roles" => "8",
            "days" => "82",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive CTC DT Consent from land office",
            "step_id" => "9",
            "order" => "17",
            "roles" => "8",
            "days" => "87",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send DT Consent to PFS / Land Office to present",
            "step_id" => "9",
            "order" => "18",
            "roles" => "8",
            "days" => "88",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request letter of no objection from Vendor Financier",
            "step_id" => "10",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to VF for letter of no objection",
            "step_id" => "10",
            "order" => "2",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "10",
            "order" => "3",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to VF for letter of no objection",
            "step_id" => "10",
            "order" => "4",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "10",
            "order" => "5",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to VF for letter of no objection",
            "step_id" => "10",
            "order" => "6",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "10",
            "order" => "7",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received letter of no objection",
            "step_id" => "10",
            "order" => "8",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "final reminder to PFS to apply consent together",
            "step_id" => "10",
            "order" => "9",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "apply carian rasmi",
            "step_id" => "10",
            "order" => "10",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive carian rasmi",
            "step_id" => "10",
            "order" => "11",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "submitted consent to land office, no more waiting PFS",
            "step_id" => "10",
            "order" => "12",
            "roles" => "8",
            "days" => "27",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to land office for consent",
            "step_id" => "10",
            "order" => "13",
            "roles" => "8",
            "days" => "34",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to land office for consent",
            "step_id" => "10",
            "order" => "14",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to land office for consent",
            "step_id" => "10",
            "order" => "15",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to land office for consent",
            "step_id" => "10",
            "order" => "16",
            "roles" => "8",
            "days" => "55",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to land office for consent",
            "step_id" => "10",
            "order" => "17",
            "roles" => "8",
            "days" => "62",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to land office for consent",
            "step_id" => "10",
            "order" => "18",
            "roles" => "8",
            "days" => "69",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "7th reminder to land office for consent",
            "step_id" => "10",
            "order" => "19",
            "roles" => "8",
            "days" => "76",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "8th reminder to land office for consent",
            "step_id" => "10",
            "order" => "20",
            "roles" => "8",
            "days" => "83",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "consent approved & Collected",
            "step_id" => "10",
            "order" => "21",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request letter of no objection from Vendor Financier",
            "step_id" => "11",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to VF for letter of no objection",
            "step_id" => "11",
            "order" => "2",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "11",
            "order" => "3",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to VF for letter of no objection",
            "step_id" => "11",
            "order" => "4",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "11",
            "order" => "5",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to VF for letter of no objection",
            "step_id" => "11",
            "order" => "6",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "11",
            "order" => "7",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received letter of no objection",
            "step_id" => "11",
            "order" => "8",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "final reminder to PFS to apply consent together",
            "step_id" => "11",
            "order" => "9",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "apply carian rasmi",
            "step_id" => "11",
            "order" => "10",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive carian rasmi",
            "step_id" => "11",
            "order" => "11",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "submitted consent to land office, no more waiting PFS",
            "step_id" => "11",
            "order" => "12",
            "roles" => "8",
            "days" => "27",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to land office for consent",
            "step_id" => "11",
            "order" => "13",
            "roles" => "8",
            "days" => "34",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to land office for consent",
            "step_id" => "11",
            "order" => "14",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to land office for consent",
            "step_id" => "11",
            "order" => "15",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to land office for consent",
            "step_id" => "11",
            "order" => "16",
            "roles" => "8",
            "days" => "55",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to land office for consent",
            "step_id" => "11",
            "order" => "17",
            "roles" => "8",
            "days" => "62",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to land office for consent",
            "step_id" => "11",
            "order" => "18",
            "roles" => "8",
            "days" => "69",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "7th reminder to land office for consent",
            "step_id" => "11",
            "order" => "19",
            "roles" => "8",
            "days" => "76",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "8th reminder to land office for consent",
            "step_id" => "11",
            "order" => "20",
            "roles" => "8",
            "days" => "83",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "consent approved & Collected",
            "step_id" => "11",
            "order" => "21",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request letter of no objection from Vendor Financier",
            "step_id" => "12",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to VF for letter of no objection",
            "step_id" => "12",
            "order" => "2",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "12",
            "order" => "3",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to VF for letter of no objection",
            "step_id" => "12",
            "order" => "4",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "12",
            "order" => "5",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to VF for letter of no objection",
            "step_id" => "12",
            "order" => "6",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "12",
            "order" => "7",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received letter of no objection",
            "step_id" => "12",
            "order" => "8",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "final reminder to PFS to apply consent together",
            "step_id" => "12",
            "order" => "9",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "apply carian rasmi",
            "step_id" => "12",
            "order" => "10",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive carian rasmi",
            "step_id" => "12",
            "order" => "11",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "submitted consent to land office, no more waiting PFS",
            "step_id" => "12",
            "order" => "12",
            "roles" => "8",
            "days" => "27",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to land office for consent",
            "step_id" => "12",
            "order" => "13",
            "roles" => "8",
            "days" => "34",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to land office for consent",
            "step_id" => "12",
            "order" => "14",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to land office for consent",
            "step_id" => "12",
            "order" => "15",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to land office for consent",
            "step_id" => "12",
            "order" => "16",
            "roles" => "8",
            "days" => "55",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to land office for consent",
            "step_id" => "12",
            "order" => "17",
            "roles" => "8",
            "days" => "62",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to land office for consent",
            "step_id" => "12",
            "order" => "18",
            "roles" => "8",
            "days" => "69",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "7th reminder to land office for consent",
            "step_id" => "12",
            "order" => "19",
            "roles" => "8",
            "days" => "76",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "8th reminder to land office for consent",
            "step_id" => "12",
            "order" => "20",
            "roles" => "8",
            "days" => "83",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "consent approved & Collected",
            "step_id" => "12",
            "order" => "21",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request letter of no objection from Vendor Financier",
            "step_id" => "13",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to VF for letter of no objection",
            "step_id" => "13",
            "order" => "2",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "13",
            "order" => "3",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to VF for letter of no objection",
            "step_id" => "13",
            "order" => "4",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "13",
            "order" => "5",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to VF for letter of no objection",
            "step_id" => "13",
            "order" => "6",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to PFS to give us CTC LO and RM156 fees",
            "step_id" => "13",
            "order" => "7",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received letter of no objection",
            "step_id" => "13",
            "order" => "8",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "final reminder to PFS to apply consent together",
            "step_id" => "13",
            "order" => "9",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "apply carian rasmi",
            "step_id" => "13",
            "order" => "10",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive carian rasmi",
            "step_id" => "13",
            "order" => "11",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "submitted consent to land office, no more waiting PFS",
            "step_id" => "13",
            "order" => "12",
            "roles" => "8",
            "days" => "27",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to land office for consent",
            "step_id" => "13",
            "order" => "13",
            "roles" => "8",
            "days" => "34",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to land office for consent",
            "step_id" => "13",
            "order" => "14",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to land office for consent",
            "step_id" => "13",
            "order" => "15",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "4th reminder to land office for consent",
            "step_id" => "13",
            "order" => "16",
            "roles" => "8",
            "days" => "55",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "5th reminder to land office for consent",
            "step_id" => "13",
            "order" => "17",
            "roles" => "8",
            "days" => "62",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "6th reminder to land office for consent",
            "step_id" => "13",
            "order" => "18",
            "roles" => "8",
            "days" => "69",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "7th reminder to land office for consent",
            "step_id" => "13",
            "order" => "19",
            "roles" => "8",
            "days" => "76",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "8th reminder to land office for consent",
            "step_id" => "13",
            "order" => "20",
            "roles" => "8",
            "days" => "83",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "consent approved & Collected",
            "step_id" => "13",
            "order" => "21",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "register Purchaser income tax",
            "step_id" => "14",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "register Vendor income tax",
            "step_id" => "14",
            "order" => "2",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "remind Psol for Purchaser income tax",
            "step_id" => "14",
            "order" => "3",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "remind Vsol for Vendor income tax",
            "step_id" => "14",
            "order" => "4",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder Psol for Purchaser income tax",
            "step_id" => "14",
            "order" => "5",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder Vsol for Vendor income tax",
            "step_id" => "14",
            "order" => "6",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder Psol for Purchaser income tax",
            "step_id" => "14",
            "order" => "7",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder Vsol for Vendor income tax",
            "step_id" => "14",
            "order" => "8",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder Psol for Purchaser income tax",
            "step_id" => "14",
            "order" => "9",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder Vsol for Vendor income tax",
            "step_id" => "14",
            "order" => "10",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "pay RPGT",
            "step_id" => "14",
            "order" => "11",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send RPGT receipt to Vsol",
            "step_id" => "14",
            "order" => "12",
            "roles" => "8",
            "days" => "17",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "file CKHT 1A & 3",
            "step_id" => "14",
            "order" => "13",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive CKHT 3 from Vsol",
            "step_id" => "14",
            "order" => "14",
            "roles" => "8",
            "days" => "30",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "file CKHT 2A",
            "step_id" => "14",
            "order" => "15",
            "roles" => "8",
            "days" => "40",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request redemption statement",
            "step_id" => "15",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Vendor Financier for redemption statement",
            "step_id" => "15",
            "order" => "2",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Vendor Financier for redemption statement",
            "step_id" => "15",
            "order" => "3",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to bank lawyer for bank undertaking ",
            "step_id" => "15",
            "order" => "4",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Vendor Financier for redemption statement",
            "step_id" => "15",
            "order" => "5",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to bank lawyer for bank undertaking ",
            "step_id" => "15",
            "order" => "6",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive redemption statement",
            "step_id" => "15",
            "order" => "7",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to bank lawyer for bank undertaking ",
            "step_id" => "15",
            "order" => "8",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "reply bank lawyer to advise redemption",
            "step_id" => "15",
            "order" => "9",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive bank's undertaking in favour of Vendor",
            "step_id" => "15",
            "order" => "10",
            "roles" => "8",
            "days" => "26",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to bank lawyer for redemption sum",
            "step_id" => "15",
            "order" => "11",
            "roles" => "8",
            "days" => "27",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to bank lawyer for redemption sum",
            "step_id" => "15",
            "order" => "12",
            "roles" => "8",
            "days" => "32",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to bank lawyer for redemption sum",
            "step_id" => "15",
            "order" => "13",
            "roles" => "8",
            "days" => "37",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive redemption sum",
            "step_id" => "15",
            "order" => "14",
            "roles" => "8",
            "days" => "40",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "forward Form 16N/DRR/Change of Name/F19G to Vendor Bank",
            "step_id" => "15",
            "order" => "15",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to VF for return executed redeemed docs",
            "step_id" => "15",
            "order" => "16",
            "roles" => "8",
            "days" => "46",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to VF for return executed redeemed docs",
            "step_id" => "15",
            "order" => "17",
            "roles" => "8",
            "days" => "51",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to VF for return executed redeemed docs",
            "step_id" => "15",
            "order" => "18",
            "roles" => "8",
            "days" => "56",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "recv redeemed docs",
            "step_id" => "15",
            "order" => "19",
            "roles" => "8",
            "days" => "60",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "date & adju F16N/DRR/DOA",
            "step_id" => "15",
            "order" => "20",
            "roles" => "8",
            "days" => "61",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp F16N/DRR",
            "step_id" => "15",
            "order" => "21",
            "roles" => "8",
            "days" => "63",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send DRR to High Court for revocation",
            "step_id" => "15",
            "order" => "22",
            "roles" => "8",
            "days" => "63",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive revoked DRR from High Court",
            "step_id" => "15",
            "order" => "23",
            "roles" => "8",
            "days" => "65",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send all original to Purchaser / bank lawyer",
            "step_id" => "15",
            "order" => "24",
            "roles" => "8",
            "days" => "66",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp DOA",
            "step_id" => "15",
            "order" => "25",
            "roles" => "8",
            "days" => "70",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send stamped DOA to Purchaser / Bank lawyer",
            "step_id" => "15",
            "order" => "26",
            "roles" => "8",
            "days" => "71",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to bank lawyer for balance loan sum",
            "step_id" => "16",
            "order" => "1",
            "roles" => "8",
            "days" => "68",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to bank lawyer for balance loan sum",
            "step_id" => "16",
            "order" => "2",
            "roles" => "8",
            "days" => "73",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to bank lawyer for balance loan sum",
            "step_id" => "16",
            "order" => "3",
            "roles" => "8",
            "days" => "78",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "receive balance loan sum",
            "step_id" => "16",
            "order" => "4",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "inform P that we are arranging keys",
            "step_id" => "16",
            "order" => "5",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "inform V to deliver keys",
            "step_id" => "16",
            "order" => "6",
            "roles" => "8",
            "days" => "80",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "pay all outstanding bills",
            "step_id" => "16",
            "order" => "7",
            "roles" => "8",
            "days" => "81",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "apportionment of outgoings done",
            "step_id" => "16",
            "order" => "8",
            "roles" => "8",
            "days" => "82",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Purchaser paid apportionment and collect keys",
            "step_id" => "16",
            "order" => "9",
            "roles" => "8",
            "days" => "83",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "release BPP to V",
            "step_id" => "16",
            "order" => "10",
            "roles" => "8",
            "days" => "84",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "release agent fees",
            "step_id" => "16",
            "order" => "11",
            "roles" => "8",
            "days" => "84",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "original title register under Purchaser name",
            "step_id" => "17",
            "order" => "1",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "NOA served to Developer",
            "step_id" => "17",
            "order" => "2",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "email IWK for change of name",
            "step_id" => "17",
            "order" => "3",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "email JMB for change of name",
            "step_id" => "17",
            "order" => "4",
            "roles" => "8",
            "days" => "90",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
         ]);


         
        DB::table('checklist_template_item')->insert([ 
            "name" => "open file",
            "step_id" => "19",
            "order" => "1",
            "roles" => "5",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Create Group Chat ",
            "step_id" => "19",
            "order" => "2",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "call Purchaser for signing info",
            "step_id" => "19",
            "order" => "3",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "call Psol for signing info",
            "step_id" => "19",
            "order" => "4",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "call Vsol for signing info",
            "step_id" => "19",
            "order" => "5",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "call PV sol for signing info",
            "step_id" => "19",
            "order" => "6",
            "roles" => "7",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Purchaser for signing info",
            "step_id" => "19",
            "order" => "7",
            "roles" => "7",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Psol for signing info",
            "step_id" => "19",
            "order" => "8",
            "roles" => "7",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Vsol for signing info",
            "step_id" => "19",
            "order" => "9",
            "roles" => "7",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Purchaser for signing info",
            "step_id" => "19",
            "order" => "10",
            "roles" => "7",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Psol for signing info",
            "step_id" => "19",
            "order" => "11",
            "roles" => "7",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Vsol for signing info",
            "step_id" => "19",
            "order" => "12",
            "roles" => "7",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Purchaser for signing info",
            "step_id" => "19",
            "order" => "13",
            "roles" => "7",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Psol for signing info",
            "step_id" => "19",
            "order" => "14",
            "roles" => "7",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Vsol for signing info",
            "step_id" => "19",
            "order" => "15",
            "roles" => "7",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Purchaser reply for signing info",
            "step_id" => "19",
            "order" => "16",
            "roles" => "7",
            "days" => "21",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Psol reply for signing info",
            "step_id" => "19",
            "order" => "17",
            "roles" => "7",
            "days" => "21",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Vsol reply for signing info",
            "step_id" => "19",
            "order" => "18",
            "roles" => "7",
            "days" => "21",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "fix appointment for signing",
            "step_id" => "19",
            "order" => "19",
            "roles" => "7",
            "days" => "22",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "advise LF payment",
            "step_id" => "19",
            "order" => "20",
            "roles" => "7",
            "days" => "22",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Conduct Land Search",
            "step_id" => "19",
            "order" => "21",
            "roles" => "7",
            "days" => "22",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Conduct OA search",
            "step_id" => "19",
            "order" => "22",
            "roles" => "7",
            "days" => "22",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Conduct SSM search",
            "step_id" => "19",
            "order" => "23",
            "roles" => "7",
            "days" => "22",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Land Search",
            "step_id" => "19",
            "order" => "24",
            "roles" => "7",
            "days" => "29",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received OA search",
            "step_id" => "19",
            "order" => "25",
            "roles" => "7",
            "days" => "23",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received SSM search",
            "step_id" => "19",
            "order" => "26",
            "roles" => "7",
            "days" => "23",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "email all docs to banker to issue LI",
            "step_id" => "20",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Banker for Bank LI",
            "step_id" => "20",
            "order" => "2",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Banker for Bank LI",
            "step_id" => "20",
            "order" => "3",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Banker for Bank LI",
            "step_id" => "20",
            "order" => "4",
            "roles" => "8",
            "days" => "4",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Bank LI",
            "step_id" => "20",
            "order" => "5",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Psol officially to request docs/LU",
            "step_id" => "20",
            "order" => "6",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Vsol officially to request docs/LU",
            "step_id" => "20",
            "order" => "7",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to PVsol officially to request docs/LU",
            "step_id" => "20",
            "order" => "8",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Developer officially to request docs/LU",
            "step_id" => "20",
            "order" => "9",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Proprietor officially to request docs/LU",
            "step_id" => "20",
            "order" => "10",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Master Chargee officially to request docs/LU",
            "step_id" => "20",
            "order" => "11",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Cosec officially to request docs/LU",
            "step_id" => "20",
            "order" => "12",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Bank for sup LO officially to request docs/LU",
            "step_id" => "20",
            "order" => "13",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Bank to waive lodgement of private caveat",
            "step_id" => "20",
            "order" => "14",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Borrower signing ",
            "step_id" => "22",
            "order" => "1",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Legal Fees paid",
            "step_id" => "22",
            "order" => "2",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "reminder to Bank to return docs",
            "step_id" => "22",
            "order" => "3",
            "roles" => "8",
            "days" => "3",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send to Bank Execution",
            "step_id" => "22",
            "order" => "4",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Bank rejected documents - 1st time",
            "step_id" => "22",
            "order" => "5",
            "roles" => "8",
            "days" => "5",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Psol for Bank rejected docs",
            "step_id" => "22",
            "order" => "6",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Vsol for Bank rejected docs",
            "step_id" => "22",
            "order" => "7",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Dev for Bank rejected docs",
            "step_id" => "22",
            "order" => "8",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Proprietor for Bank rejected docs",
            "step_id" => "22",
            "order" => "9",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Branch for Bank rejected docs",
            "step_id" => "22",
            "order" => "10",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to Borrower for Bank rejected docs",
            "step_id" => "22",
            "order" => "11",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "write in to PVsol for Bank rejected docs",
            "step_id" => "22",
            "order" => "12",
            "roles" => "8",
            "days" => "6",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "resend to Bank for execution - 1st time",
            "step_id" => "22",
            "order" => "13",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "reminder to Bank to return docs",
            "step_id" => "22",
            "order" => "14",
            "roles" => "8",
            "days" => "13",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Bank rejected documents - 2nd time",
            "step_id" => "22",
            "order" => "15",
            "roles" => "8",
            "days" => "16",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "resend to Bank for execution - 2nd time",
            "step_id" => "22",
            "order" => "16",
            "roles" => "8",
            "days" => "19",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "reminder to Bank to return docs",
            "step_id" => "22",
            "order" => "17",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Bank executed documents",
            "step_id" => "22",
            "order" => "18",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Bank Undertaking",
            "step_id" => "22",
            "order" => "19",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "send Bank Undertaking to Vsol / Pvsol",
            "step_id" => "22",
            "order" => "20",
            "roles" => "8",
            "days" => "26",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Affirm SD (Own Occupation & Non-Bankruptcy)",
            "step_id" => "23",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Affirm SD (1st house)",
            "step_id" => "23",
            "order" => "2",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Psol to reply",
            "step_id" => "23",
            "order" => "3",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Vsol to reply",
            "step_id" => "23",
            "order" => "4",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Developer to reply",
            "step_id" => "23",
            "order" => "5",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Proprietor to reply",
            "step_id" => "23",
            "order" => "6",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Valuer to send VR",
            "step_id" => "23",
            "order" => "7",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to CoSec to reply",
            "step_id" => "23",
            "order" => "8",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to PVsol to reply",
            "step_id" => "23",
            "order" => "9",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "conduct Power of Attorney search in High Court",
            "step_id" => "23",
            "order" => "10",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Adju Facilities Agreement",
            "step_id" => "23",
            "order" => "11",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Adju SD (Own Occupation & Non-Bankruptcy)",
            "step_id" => "23",
            "order" => "12",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Lodge Caveat",
            "step_id" => "23",
            "order" => "13",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve 1st notice to Proprietor for Developer PA",
            "step_id" => "23",
            "order" => "14",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Psol to reply",
            "step_id" => "23",
            "order" => "15",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Vsol to reply",
            "step_id" => "23",
            "order" => "16",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Developer to reply",
            "step_id" => "23",
            "order" => "17",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Proprietor to reply",
            "step_id" => "23",
            "order" => "18",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Valuer to send VR",
            "step_id" => "23",
            "order" => "19",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to CoSec to reply",
            "step_id" => "23",
            "order" => "20",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to PVsol to reply",
            "step_id" => "23",
            "order" => "21",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to High Court for PA search",
            "step_id" => "23",
            "order" => "22",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Psol to reply",
            "step_id" => "23",
            "order" => "23",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Vsol to reply",
            "step_id" => "23",
            "order" => "24",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Developer to reply",
            "step_id" => "23",
            "order" => "25",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Proprietor to reply",
            "step_id" => "23",
            "order" => "26",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Valuer to send VR",
            "step_id" => "23",
            "order" => "27",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to CoSec to reply",
            "step_id" => "23",
            "order" => "28",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to PVsol to reply",
            "step_id" => "23",
            "order" => "29",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to High Court for PA search",
            "step_id" => "23",
            "order" => "30",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Stamp Facilities Agreement",
            "step_id" => "23",
            "order" => "31",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Psol reply to advise redemption ",
            "step_id" => "23",
            "order" => "32",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Vsol reply to advise redemption",
            "step_id" => "23",
            "order" => "33",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Developer LU",
            "step_id" => "23",
            "order" => "34",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Proprietor LU",
            "step_id" => "23",
            "order" => "35",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Valuation Report sent to Bank",
            "step_id" => "23",
            "order" => "36",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received repy from Cosec",
            "step_id" => "23",
            "order" => "37",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received reply from PVsol",
            "step_id" => "23",
            "order" => "38",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received PA search",
            "step_id" => "23",
            "order" => "39",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Cosec to amend doc/undertaking",
            "step_id" => "23",
            "order" => "40",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Dev amend doc/undertaking",
            "step_id" => "23",
            "order" => "41",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Proprietor amend doc/undertaking",
            "step_id" => "23",
            "order" => "42",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Psol amend doc/undertaking",
            "step_id" => "23",
            "order" => "43",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request PVsol amend doc/undertaking",
            "step_id" => "23",
            "order" => "44",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Vsol amend doc/undertaking",
            "step_id" => "23",
            "order" => "45",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Prepare Advise",
            "step_id" => "23",
            "order" => "46",
            "roles" => "8",
            "days" => "25",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Check Advise",
            "step_id" => "23",
            "order" => "47",
            "roles" => "7",
            "days" => "26",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Sent out Advise",
            "step_id" => "23",
            "order" => "48",
            "roles" => "8",
            "days" => "26",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Redemption Disbursed",
            "step_id" => "23",
            "order" => "49",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify Psol redemption disbursed",
            "step_id" => "23",
            "order" => "50",
            "roles" => "8",
            "days" => "37",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify Vsol redemption disbursed",
            "step_id" => "23",
            "order" => "51",
            "roles" => "8",
            "days" => "38",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify PVsol redemption disbursed",
            "step_id" => "23",
            "order" => "52",
            "roles" => "8",
            "days" => "39",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Affirm SD (Own Occupation & Non-Bankruptcy)",
            "step_id" => "24",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Affirm SD (1st house)",
            "step_id" => "24",
            "order" => "2",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Psol to reply",
            "step_id" => "24",
            "order" => "3",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Vsol to reply",
            "step_id" => "24",
            "order" => "4",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Developer to reply",
            "step_id" => "24",
            "order" => "5",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Proprietor to reply",
            "step_id" => "24",
            "order" => "6",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Valuer to send VR",
            "step_id" => "24",
            "order" => "7",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to CoSec to reply",
            "step_id" => "24",
            "order" => "8",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to PVsol to reply",
            "step_id" => "24",
            "order" => "9",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Psol or Pvsol for date of DOA",
            "step_id" => "24",
            "order" => "10",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "conduct Power of Attorney search in High Court",
            "step_id" => "24",
            "order" => "11",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Adju Facilities Agreement",
            "step_id" => "24",
            "order" => "12",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Adju Charge",
            "step_id" => "24",
            "order" => "13",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Adju SD (Own Occupation & Non-Bankruptcy)",
            "step_id" => "24",
            "order" => "14",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve 1st notice to Proprietor for Developer PA",
            "step_id" => "24",
            "order" => "15",
            "roles" => "8",
            "days" => "2",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Psol to reply",
            "step_id" => "24",
            "order" => "16",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Vsol to reply",
            "step_id" => "24",
            "order" => "17",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Developer to reply",
            "step_id" => "24",
            "order" => "18",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Proprietor to reply",
            "step_id" => "24",
            "order" => "19",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Valuer to send VR",
            "step_id" => "24",
            "order" => "20",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to CoSec to reply",
            "step_id" => "24",
            "order" => "21",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to PVsol to reply",
            "step_id" => "24",
            "order" => "22",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to High Court for PA search",
            "step_id" => "24",
            "order" => "23",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Psol or Pvsol for date of DOA",
            "step_id" => "24",
            "order" => "24",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Psol to reply",
            "step_id" => "24",
            "order" => "25",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Vsol to reply",
            "step_id" => "24",
            "order" => "26",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Developer to reply",
            "step_id" => "24",
            "order" => "27",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Proprietor to reply",
            "step_id" => "24",
            "order" => "28",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Valuer to send VR",
            "step_id" => "24",
            "order" => "29",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to CoSec to reply",
            "step_id" => "24",
            "order" => "30",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to PVsol to reply",
            "step_id" => "24",
            "order" => "31",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to High Court for PA search",
            "step_id" => "24",
            "order" => "32",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received date of DOA & adju DOA & PA",
            "step_id" => "24",
            "order" => "33",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Stamp Facilities Agreement",
            "step_id" => "24",
            "order" => "34",
            "roles" => "8",
            "days" => "15",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Psol reply to full release",
            "step_id" => "24",
            "order" => "35",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Vsol reply to full release",
            "step_id" => "24",
            "order" => "36",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Developer LU",
            "step_id" => "24",
            "order" => "37",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received Proprietor LU",
            "step_id" => "24",
            "order" => "38",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Valuation Report sent to Bank",
            "step_id" => "24",
            "order" => "39",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received repy from Cosec",
            "step_id" => "24",
            "order" => "40",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received reply from Pvsol for full release",
            "step_id" => "24",
            "order" => "41",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received PA search",
            "step_id" => "24",
            "order" => "42",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp charge",
            "step_id" => "24",
            "order" => "43",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Cosec to amend doc/undertaking",
            "step_id" => "24",
            "order" => "44",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Dev amend doc/undertaking",
            "step_id" => "24",
            "order" => "45",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Proprietor amend doc/undertaking",
            "step_id" => "24",
            "order" => "46",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Psol amend doc/undertaking",
            "step_id" => "24",
            "order" => "47",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request PVsol amend doc/undertaking",
            "step_id" => "24",
            "order" => "48",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "request Vsol amend doc/undertaking",
            "step_id" => "24",
            "order" => "49",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Psol or Pvsol for date of DOA",
            "step_id" => "24",
            "order" => "50",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve 2nd notice to Proprietor for Developer PA",
            "step_id" => "24",
            "order" => "51",
            "roles" => "8",
            "days" => "28",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp DOA & PA",
            "step_id" => "24",
            "order" => "52",
            "roles" => "8",
            "days" => "34",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve DOA to Dev",
            "step_id" => "24",
            "order" => "53",
            "roles" => "8",
            "days" => "35",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve DOA to Proprietor",
            "step_id" => "24",
            "order" => "54",
            "roles" => "8",
            "days" => "35",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received original docs from Psol",
            "step_id" => "24",
            "order" => "55",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received original docs from Vsol",
            "step_id" => "24",
            "order" => "56",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received orignial docs from Pvsol",
            "step_id" => "24",
            "order" => "57",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "pay presentation fees",
            "step_id" => "24",
            "order" => "58",
            "roles" => "8",
            "days" => "37",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "present",
            "step_id" => "24",
            "order" => "59",
            "roles" => "8",
            "days" => "38",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received presentation receipt",
            "step_id" => "24",
            "order" => "60",
            "roles" => "8",
            "days" => "39",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "prepare advise",
            "step_id" => "24",
            "order" => "61",
            "roles" => "8",
            "days" => "40",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "check advise",
            "step_id" => "24",
            "order" => "62",
            "roles" => "7",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "sent out advise",
            "step_id" => "24",
            "order" => "63",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Bank for loan disbursement",
            "step_id" => "24",
            "order" => "64",
            "roles" => "8",
            "days" => "43",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Bank for loan disbursement",
            "step_id" => "24",
            "order" => "65",
            "roles" => "8",
            "days" => "45",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Bank for loan disbursement",
            "step_id" => "24",
            "order" => "66",
            "roles" => "8",
            "days" => "47",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify Psol Full disbursed",
            "step_id" => "24",
            "order" => "67",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify Vsol Full disbursed",
            "step_id" => "24",
            "order" => "68",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify PVsol Full disbursed",
            "step_id" => "24",
            "order" => "69",
            "roles" => "8",
            "days" => "48",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "adju Charge",
            "step_id" => "25",
            "order" => "1",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "file Form 34",
            "step_id" => "25",
            "order" => "2",
            "roles" => "8",
            "days" => "1",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Vsol for date of DRR",
            "step_id" => "25",
            "order" => "3",
            "roles" => "8",
            "days" => "7",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Vsol for date of DRR",
            "step_id" => "25",
            "order" => "4",
            "roles" => "8",
            "days" => "14",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp charge",
            "step_id" => "25",
            "order" => "5",
            "roles" => "8",
            "days" => "20",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Vsol for date of DRR",
            "step_id" => "25",
            "order" => "6",
            "roles" => "8",
            "days" => "21",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received date of DRR & adju DOA & PA",
            "step_id" => "25",
            "order" => "7",
            "roles" => "8",
            "days" => "28",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve 2nd notice to Proprietor for Developer PA",
            "step_id" => "25",
            "order" => "8",
            "roles" => "8",
            "days" => "28",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "stamp DOA & PA",
            "step_id" => "25",
            "order" => "9",
            "roles" => "8",
            "days" => "34",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve DOA to Dev",
            "step_id" => "25",
            "order" => "10",
            "roles" => "8",
            "days" => "35",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "serve DOA to Proprietor",
            "step_id" => "25",
            "order" => "11",
            "roles" => "8",
            "days" => "35",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received original docs from Psol",
            "step_id" => "25",
            "order" => "12",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received original docs from Vsol",
            "step_id" => "25",
            "order" => "13",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received orignial docs from Pvsol",
            "step_id" => "25",
            "order" => "14",
            "roles" => "8",
            "days" => "36",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "pay presentation fees",
            "step_id" => "25",
            "order" => "15",
            "roles" => "8",
            "days" => "37",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "present",
            "step_id" => "25",
            "order" => "16",
            "roles" => "8",
            "days" => "38",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "prepare advise",
            "step_id" => "25",
            "order" => "17",
            "roles" => "8",
            "days" => "40",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "received presentation receipt",
            "step_id" => "25",
            "order" => "18",
            "roles" => "8",
            "days" => "40",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "check advise",
            "step_id" => "25",
            "order" => "19",
            "roles" => "7",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "sent out advise",
            "step_id" => "25",
            "order" => "20",
            "roles" => "8",
            "days" => "41",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "1st reminder to Bank for loan disbursement",
            "step_id" => "25",
            "order" => "21",
            "roles" => "8",
            "days" => "43",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "2nd reminder to Bank for loan disbursement",
            "step_id" => "25",
            "order" => "22",
            "roles" => "8",
            "days" => "45",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "3rd reminder to Bank for loan disbursement",
            "step_id" => "25",
            "order" => "23",
            "roles" => "8",
            "days" => "47",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Balance loan sum disbursed",
            "step_id" => "25",
            "order" => "24",
            "roles" => "8",
            "days" => "50",
            "need_attachment" => "1",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify Psol balance loan sum disbursed",
            "step_id" => "25",
            "order" => "25",
            "roles" => "8",
            "days" => "50",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify Vsol balance loan sum disbursed",
            "step_id" => "25",
            "order" => "26",
            "roles" => "8",
            "days" => "50",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Notify PVsol balance loan sum disbursed",
            "step_id" => "25",
            "order" => "27",
            "roles" => "8",
            "days" => "50",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Original Title / securities sent to Bank",
            "step_id" => "26",
            "order" => "1",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "loan agreements & title sent to Borrower safe keeping",
            "step_id" => "26",
            "order" => "2",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);
        DB::table('checklist_template_item')->insert([ 
            "name" => "Legal Fees fully paid",
            "step_id" => "26",
            "order" => "3",
            "roles" => "8",
            "days" => "10",
            "need_attachment" => "0",
            "created_at" => date('Y-m-d H:i:s')
        ]);


    }
}
