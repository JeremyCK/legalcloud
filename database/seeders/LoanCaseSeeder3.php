<?php

namespace Database\Seeders;

use App\Models\AccountTemplateDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\caseTemplateDetails;

class LoanCaseSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('loan_case')->insert([ 
            'case_ref_no' => 'A0/BF/MBB/3/ML/SR',
            'customer_id' => 2,
            'property_address' => 'A-17-00, Condo Z, Selangor',
            'referral_name' => 'Mr Liew',
            'referral_phone_no' => '017-1323123',
            'referral_email' => 'mrliew@test.com',
            'purchase_price' => 300000.00,
            'loan_sum' => 100000.00,
            'targeted_bill' => 3636.09,
            'collected_bill' => 3636.09,
            'total_bill' => 3636.09,
            'targeted_trust' => 500000.00,
            'collected_trust' => 500000.00,
            'total_trust' => 500000.00,
            'remark' => 'test',
            'target_close_date' => null,
            'percentage' => 15.00,
            'is_handle' => null,
            'bill_ready' => 0,
            'handle_expired' => null,
            'sales_user_id' => 1,
            'handle_group_id' => null,
            'bank_id' => 2,
            'lawyer_id' => 38,
            'clerk_id' => 6,
            'runner_user_id' => 15,
            'case_type_id' => 1,
            'status' => 1,
            'created_at' => '2021-06-29 15:41:45',
            'updated_at' => null
        ]);

        DB::table('loan_case_notes')->insert([ 
            'case_id' => 1,
            'notes' => 'Check with runners on the status of the land scanning',
            'label' => 'Reminder: Check with runners',
            'created_at' => date('Y-m-d H:i:s') ,
            'updated_at' => null
        ]);

        DB::table('loan_case_notes')->insert([ 
            'case_id' => 1,
            'notes' => 'Follow up with lawyer on the progress before EOD, lorem ipsum kiunjsdf  h skdf hasdf',
            'label' => 'Follow up',
            'created_at' => date('Y-m-d H:i:s') ,
            'updated_at' => null
        ]);

        $accTemplateDetail = AccountTemplateDetails::where('acc_main_template_id', '=', "1")->get();

        for($i=0; $i<count($accTemplateDetail); $i++){

            DB::table('loan_case_account')->insert([ 
                'case_id' => 3,
                'item_name' => $accTemplateDetail[$i]->item_name,
                'account_details_id' => $accTemplateDetail[$i]->id,
                'account_cat_id' =>  $accTemplateDetail[$i]->account_cat_id,
                'need_approval' =>  $accTemplateDetail[$i]->need_approval,
                'amount' => $accTemplateDetail[$i]->amount,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', "1")->get();
        // $caseTemplateMain = caseTemplate::where('id', '=', $id)->get();

        for($i=0; $i<count($caseTemplateDetail); $i++){

            $stop_date = date('Y-m-d H:i:s');
            $iniStatus = 0;
            $pic_id = 0;
            $remarks = '';

            $update_date = null;

            if ($i<=5)
            {
                $iniStatus = 1;
                $update_date = date('Y-m-d H:i:s');
            }

            if ($caseTemplateDetail[$i]->role_id == "6")
            {
                $pic_id = 29;

            }
            else if ($caseTemplateDetail[$i]->role_id == "7")
            {
                $pic_id = 10;
            }
            else if ($caseTemplateDetail[$i]->role_id == "8")
            {
                $pic_id = 12;
            }

            $target_date = $this->getDuration( $caseTemplateDetail[$i]->duration);

            DB::table('loan_case_checklist')->insert([ 
                'case_id' => 3,
                'process_number' => $caseTemplateDetail[$i]->process_number,
                'checklist_name' =>  $caseTemplateDetail[$i]->checklist_name,
                'target_date' => date('Y-m-d H:i:s', strtotime($stop_date . ' +'.$caseTemplateDetail[$i]->duration.' day')),
                'target_close_date' => date('Y-m-d H:i:s'),
                'completion_date' => null,
                'need_attachment' => $caseTemplateDetail[$i]->need_attachment,
                'check_point' => $caseTemplateDetail[$i]->check_point,
                'kpi' => $caseTemplateDetail[$i]->kpi,
                'status' => $iniStatus,
                'is_checkbox' => 0,
                'bln_gen_doc' => $caseTemplateDetail[$i]->bln_gen_doc,
                'remarks' => '',
                'sales_user_id' => $pic_id,
                'handle_group_id' => 0,
                'runner_user_id' => 0,
                'role' => $caseTemplateDetail[$i]->role_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => $update_date
            ]);
        }

        

    }

    public function getDuration($duration)
    {
        $target_date = date('Y-m-d H:i:s');
        $today_date = date('Y-m-d H:i:s');
        $target_date = date('Y-m-d H:i:s', strtotime($target_date . ' +'.$duration.' day'));

        return $target_date;

    }
}
