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

class CasesAndTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        DB::table('case_checklist_template_main')->insert([ 
            'display_name' => 'PV-Title/No Consent/Charged to Bank ',
            'type' => 'PV-Title',
            'target_close_day' => 60,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45'
        ]);


        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 6,
            'template_main_id' => 1,
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
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 1,
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
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 3,
            'checklist_name' => 'Ask for PFS details & write in to us',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 12,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'CA_7_ARD',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 4,
            'checklist_name' => 'Recv PFS letter',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 12,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'AR_1_ARD',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 5,
            'template_main_id' => 1,
            'process_number' => 5,
            'checklist_name' => 'Balance Deposit paid (auto generate receipt) to client',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 5,
            'template_main_id' => 1,
            'process_number' => 6,
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
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 1,
            'process_number' => 7,
            'checklist_name' => 'Legal Fees paid (auto generate receipt) to client',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 7,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 2,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => '2021-06-29 15:41:45'
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 1,
            'process_number' => 8,
            'checklist_name' => 'Do bankruptcy search',
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
            'template_main_id' => 1,
            'process_number' => 9,
            'checklist_name' => 'Receive bankruptcy search',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 7,
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
            'template_main_id' => 1,
            'process_number' => 10,
            'checklist_name' => 'Vendor sign',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 9,
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
            'template_main_id' => 1,
            'process_number' => 11,
            'checklist_name' => 'Balance Deposit released to Vendor',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 9,
            'need_attachment' => 0,
            'remark' => 'double ensure all the balance settlement completed before move to next',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 1,
            'process_number' => 12,
            'checklist_name' => 'Vendor legal paid (generate receipt) to vendor',
            'kpi' => 0,
            'duration_base_item' => 11,
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
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 13,
            'checklist_name' => 'stamp SPA',
            'kpi' => 0,
            'duration_base_item' => 12,
            'duration' => 10,
            'need_attachment' => 0,
            'bln_gen_doc' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 14,
            'checklist_name' => 'Receive stamp SPA',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 11,
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
            'template_main_id' => 1,
            'process_number' => 15,
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
            'template_main_id' => 1,
            'process_number' => 16,
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
            'template_main_id' => 1,
            'process_number' => 17,
            'checklist_name' => 'file CKHT (request and receive)',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 16,
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
            'template_main_id' => 1,
            'process_number' => 18,
            'checklist_name' => 'Adjust MOT',
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
            'template_main_id' => 1,
            'process_number' => 19,
            'checklist_name' => 'Stamp MOT',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 56,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => '',
            'check_point' => 5,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 20,
            'checklist_name' => 'Request redemption statement',
            'kpi' => 10,
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
            'role_id' => 1,
            'template_main_id' => 1,
            'process_number' => 21,
            'checklist_name' => 'Auto reminder to Vendor Financier for redemption statement',
            'kpi' => 0,
            'duration_base_item' => 3,
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
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 22,
            'checklist_name' => 'Receive redemption statement',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 267,
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
            'template_main_id' => 1,
            'process_number' => 23,
            'checklist_name' => 'Reply bank lawyer to advise redemption',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 26,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 6,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 24,
            'checklist_name' => 'Auto reminder to bank lawyer for redemption sum',
            'kpi' => 0,
            'duration_base_item' => 5,
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
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 25,
            'checklist_name' => 'Receive redemption sum',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 40,
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
            'template_main_id' => 1,
            'process_number' => 26,
            'checklist_name' => 'Receive bank\'s undertaking in favour of Vendor',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 26,
            'need_attachment' => 1,
            'remark' => '',
            'system_code' => '',
            'check_point' => 7,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 27,
            'checklist_name' => 'Forward Form 16N/DRR/Change of Name/F19G to Vendor Bank',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 41,
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
            'template_main_id' => 1,
            'process_number' => 28,
            'checklist_name' => 'Auto reminder to VF for return executed redeemed docs',
            'kpi' => 0,
            'duration_base_item' => 3,
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
            'role_id' => 8,
            'template_main_id' => 1,
            'process_number' => 29,
            'checklist_name' => 'Recv redeemed docs',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 55,
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
            'template_main_id' => 1,
            'process_number' => 30,
            'checklist_name' => 'Stamp F16N/DRR & revoke DRR',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 57,
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
            'template_main_id' => 1,
            'process_number' => 31,
            'checklist_name' => 'Send all original to bank lawyer',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 60,
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
            'template_main_id' => 1,
            'process_number' => 32,
            'checklist_name' => 'BPP',
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
            'template_main_id' => 1,
            'process_number' => 33,
            'checklist_name' => 'Auto reminder to bank lawyer for balance loan sum',
            'kpi' => 5,
            'duration_base_item' => 5,
            'duration' => 1,
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
            'template_main_id' => 1,
            'process_number' => 34,
            'checklist_name' => 'Receive balance loan sum',
            'kpi' => 5,
            'duration_base_item' => 1,
            'duration' => 80,
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
            'template_main_id' => 1,
            'process_number' => 35,
            'checklist_name' => 'Inform P that we are arranging keys',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 84,
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
            'template_main_id' => 1,
            'process_number' => 36,
            'checklist_name' => 'Inform V to deliver keys',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 84,
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
            'template_main_id' => 1,
            'process_number' => 37,
            'checklist_name' => 'Pay all outstanding bills',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 85,
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
            'template_main_id' => 1,
            'process_number' => 38,
            'checklist_name' => 'Apportionment of outgoings done, when P will collect keys',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 85,
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
            'template_main_id' => 1,
            'process_number' => 39,
            'checklist_name' => 'Purchaser paid apportionment and collect keys',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 86,
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
            'template_main_id' => 1,
            'process_number' => 40,
            'checklist_name' => 'Release BPP to V',
            'kpi' => 10,
            'duration_base_item' => 1,
            'duration' => 86,
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
            'template_main_id' => 1,
            'process_number' => 41,
            'checklist_name' => 'Release agent fees',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 86,
            'need_attachment' => 0,
            'remark' => '',
            'system_code' => 'AR_5_LS',
            'check_point' => 0,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('case_checklist_template_details')->insert([ 
            'role_id' => 7,
            'template_main_id' => 1,
            'process_number' => 42,
            'checklist_name' => 'Close File',
            'kpi' => 0,
            'duration_base_item' => 1,
            'duration' => 90,
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