<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\RoleHierarchy;
use App\Models\caseTemplate;
use App\Models\caseTemplateDetails;

class CasesAndTemplateSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        DB::table('case_checklist_template_main')->insert([ 
            'display_name' => 'Title (PV)-FFE-Low Cost',
            'type' => 'Hire Purchase',
            'target_close_day' => 60,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45'
        ]);


        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 6,
            'template_main_id' => 2,
            'process_number' => 1,
            'checklist_name' => 'Open file',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 2,
            'checklist_name' => 'Call client',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 1,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 1,
            'template_main_id' => 2,
            'process_number' => 3,
            'checklist_name' => 'auto req docs & make appointment sign 7 days later',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'CA_7_ARD',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 1,
            'template_main_id' => 2,
            'process_number' => 4,
            'checklist_name' => 'Auto reminder to client for documents',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'AR_1_ARD',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 5,
            'checklist_name' => 'Received docs from client',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 6,
            'checklist_name' => 'Do Land Search',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 7,
            'checklist_name' => 'Land Search received',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 1,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 2,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 8,
            'checklist_name' => 'Request Developer / Proprietor Confirmation',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 2,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 1,
            'template_main_id' => 2,
            'process_number' => 9,
            'checklist_name' => 'Auto reminder to Developer / Proprietor for Confirmation',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 2,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'AR_3_VF',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 10,
            'checklist_name' => 'Received DCL',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 16,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 3,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 11,
            'checklist_name' => 'S&P finalised',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 3,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 12,
            'checklist_name' => 'Advise Payment to Purchaser & double confirm appointment',
            'kpi' => 0,
            'duration_base_item' => 11,
            'duration' => 5,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 13,
            'checklist_name' => 'Prepare documents for signing',
            'kpi' => 0,
            'duration_base_item' => 12,
            'duration' => 5,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 14,
            'checklist_name' => 'Purchaser sign',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 4,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 15,
            'checklist_name' => 'Ask for PFS details & write in to us',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 12,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 16,
            'checklist_name' => 'Recv PFS letter',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 12,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 5,
            'template_main_id' => 2,
            'process_number' => 17,
            'checklist_name' => 'Balance Deposit paid (auto generate receipt) to client',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 5,
            'template_main_id' => 2,
            'process_number' => 18,
            'checklist_name' => 'Differential Sum Paid (auto generate receipt) to client',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 19,
            'checklist_name' => 'Legal Fees paid (auto generate receipt) to client',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 5,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 20,
            'checklist_name' => 'Do bankruptcy search',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 21,
            'checklist_name' => 'Receive bankruptcy search',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 21,
            'checklist_name' => 'Receive bankruptcy search',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 22,
            'checklist_name' => 'Vendor sign',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 6,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 23,
            'checklist_name' => 'Vendor deposit all ORIGINAL docs to us',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 9,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 24,
            'checklist_name' => 'Balance Deposit released to Vendor',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 9,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 5,
            'template_main_id' => 2,
            'process_number' => 25,
            'checklist_name' => 'Vendor legal paid (generate receipt) to vendor',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 9,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 7,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 26,
            'checklist_name' => 'Stamp SPA',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 10,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 27,
            'checklist_name' => 'Receive stamp SPA',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 11,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 28,
            'checklist_name' => 'Send stamp SPA to agent, Purchaser, Vendor, Valuer and Bank Lawyer',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 12,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 29,
            'checklist_name' => 'Agent Fees released to Agent',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 12,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 30,
            'checklist_name' => 'Request Letter of Disclaimer from Master Charge',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 3,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 1,
            'template_main_id' => 2,
            'process_number' => 31,
            'checklist_name' => 'auto reminder to master chargee for Disclaimer Letter',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 3,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => 'AR_3_DL',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 32,
            'checklist_name' => 'Disclaimer Letter received',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 16,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 8,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);


        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 33,
            'checklist_name' => 'Serve 1st NOA to Developer (request and receive)',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 14,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 34,
            'checklist_name' => 'File CKHT (request and receive)',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 16,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 35,
            'checklist_name' => 'Receive bank\'s undertaking in favour of Vendor',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 26,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 36,
            'checklist_name' => 'Date and adju DOA',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 26,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 36,
            'checklist_name' => 'Date and adju DOA',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 26,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 37,
            'checklist_name' => 'DOA stamped',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 46,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 38,
            'checklist_name' => 'Send all original + PDS 15 for DOA to bank lawyer',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 30,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);
        
        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 1,
            'template_main_id' => 2,
            'process_number' => 39,
            'checklist_name' => 'Auto reminder to bank lawyer for loan sum',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 30,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'AR_5_LS',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 40,
            'checklist_name' => 'Receive loan sum',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 50,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 41,
            'checklist_name' => 'Send stamped ori DOA to bank lawyer',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 50,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 42,
            'checklist_name' => 'Serve 2nd NOA',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 51,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 42,
            'checklist_name' => 'Inform P',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 50,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 43,
            'checklist_name' => 'Inform P',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 50,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 44,
            'checklist_name' => 'Inform V to deliver keys',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 50,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 45,
            'checklist_name' => 'Pay all outstanding bills',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 51,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 46,
            'checklist_name' => 'Apportionment of outgoings done, when P will collect keys',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 52,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 46,
            'checklist_name' => 'Apportionment of outgoings done, when P will collect keys',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 52,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 47,
            'checklist_name' => 'Purchaser paid apportionment and collect keys',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 53,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 48,
            'checklist_name' => 'Release BPP to V',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 54,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 2,
            'process_number' => 49,
            'checklist_name' => 'Release agent fees',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 54,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 2,
            'process_number' => 50,
            'checklist_name' => 'Close File',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 60,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);
    }
}