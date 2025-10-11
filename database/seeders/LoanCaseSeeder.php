<?php

namespace Database\Seeders;

use App\Models\AccountTemplateDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\caseTemplateDetails;
use App\Models\CaseTemplateItems;
use App\Models\CaseTemplateMain;
use App\Models\CaseTemplateMainStepsRel;
use App\Models\CaseTemplateSteps;
use App\Models\LoanCaseChecklistDetails;
use App\Models\LoanCaseChecklistMain;

class LoanCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('loan_case')->insert([
            'case_ref_no' => 'S1/UWD/MBB/100001/lll/RLI',
            'customer_id' => 1,
            'property_address' => 'A-18-00, Condo B, Selangor',
            'referral_name' => 'Mr Teow',
            'referral_phone_no' => '017-2837483',
            'referral_email' => 'mrterow@test.com',
            'purchase_price' => 1000000.00,
            'loan_sum' => 700000.00,
            'targeted_bill' => 3636.09,
            'collected_bill' => 3636.09,
            'total_bill' => 3636.09,
            'targeted_trust' => 160000.00,
            'collected_trust' => 0.00,
            'total_trust' => 0.00,
            'remark' => 'test',
            'target_close_date' => date('Y-m-d H:i:s', strtotime("+60 days")),
            'case_accept_date' => date('Y-m-d H:i:s'),
            'percentage' => 15.00,
            'bill_ready' => 1,
            'is_handle' => null,
            'handle_expired' => null,
            'sales_user_id' => 29,
            'handle_group_id' => null,
            'bank_id' => 1,
            'lawyer_id' => 38,
            'clerk_id' => 4,
            'account_id' => 35,
            'runner_user_id' => 15,
            'case_type_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null
        ]);

        DB::table('loan_case_notes')->insert([
            'case_id' => 1,
            'notes' => 'Check with runners on the status of the land scanning',
            'label' => 'Reminder: Check with runners',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null
        ]);

        DB::table('loan_case_notes')->insert([
            'case_id' => 1,
            'notes' => 'Follow up with lawyer on the progress before EOD, lorem ipsum kiunjsdf  h skdf hasdf',
            'label' => 'Follow up',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null
        ]);

        $accTemplateDetail = AccountTemplateDetails::where('acc_main_template_id', '=', "1")->get();

        for ($i = 0; $i < count($accTemplateDetail); $i++) {

            DB::table('loan_case_account')->insert([
                'case_id' => 1,
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

        for ($i = 0; $i < count($caseTemplateDetail); $i++) {

            $stop_date = date('Y-m-d H:i:s');
            $iniStatus = 0;
            $pic_id = 0;
            $remarks = '';

            $update_date = null;

            if ($i <= 5) {
                $iniStatus = 1;
                $update_date = date('Y-m-d H:i:s');
            }

            if ($caseTemplateDetail[$i]->role_id == "6") {
                $pic_id = 29;
            } else if ($caseTemplateDetail[$i]->role_id == "7") {
                $pic_id = 38;
            } else if ($caseTemplateDetail[$i]->role_id == "8") {
                $pic_id = 4;
            }

            $target_date = $this->getDuration($caseTemplateDetail[$i]->duration);

            DB::table('loan_case_checklist')->insert([
                'case_id' => 1,
                'process_number' => $caseTemplateDetail[$i]->process_number,
                'checklist_name' =>  $caseTemplateDetail[$i]->checklist_name,
                'target_date' => date('Y-m-d H:i:s', strtotime($stop_date . ' +' . $caseTemplateDetail[$i]->duration . ' day')),
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

        $caseTemplateSteps = DB::table('checklist_template_steps AS s')
            ->leftJoin('checklist_template_main_step_rel AS r', 'r.checklist_step_id', '=', 's.id')
            ->select('s.*', 'r.template_main_id')
            ->where('r.template_main_id', '=',  '1')
            ->get();


        // $caseTemplateSteps = CaseTemplateMainStepsRel::where('templzzate_main_id', '=', "1")->get();

        $openfile=0;

        for ($i = 0; $i < count($caseTemplateSteps); $i++) {

            $loanCaseChecklistMain = new LoanCaseChecklistMain();

            $loanCaseChecklistMain->case_id = 1;
            $loanCaseChecklistMain->name =  $caseTemplateSteps[$i]->name;
            $loanCaseChecklistMain->status = 0;
            $loanCaseChecklistMain->created_at = date('Y-m-d H:i:s');

            $loanCaseChecklistMain->save();

            $caseTemplateItems = CaseTemplateItems::where('step_id', '=', $caseTemplateSteps[$i]->id)->get();

            if (count($caseTemplateItems) > 0) {
                for ($j = 0; $j < count($caseTemplateItems); $j++) {

                    if ($caseTemplateItems[$j]->roles == "6") {
                        $pic_id = 30;
                    } else if ($caseTemplateItems[$j]->roles == "7") {
                        $pic_id = 38;
                    } else if ($caseTemplateItems[$j]->roles == "8") {
                        $pic_id = 4;
                    }else if ($caseTemplateItems[$j]->roles == "5") {
                        $pic_id = 34;
                    }

                    $loanCaseChecklistDetails = new LoanCaseChecklistDetails();

                    $loanCaseChecklistDetails->case_id = 1;
                    $loanCaseChecklistDetails->name =  $caseTemplateItems[$j]->name;
                    $loanCaseChecklistDetails->loan_case_main_id =  $loanCaseChecklistMain->id;
                    $loanCaseChecklistDetails->order =  $caseTemplateItems[$j]->order;
                    $loanCaseChecklistDetails->kpi = $caseTemplateItems[$j]->kpi;
                    $loanCaseChecklistDetails->roles = $caseTemplateItems[$j]->roles;
                    $loanCaseChecklistDetails->days = $caseTemplateItems[$j]->days;
                    $loanCaseChecklistDetails->start = $caseTemplateItems[$j]->start;
                    $loanCaseChecklistDetails->duration = $caseTemplateItems[$j]->duration;
                    $loanCaseChecklistDetails->need_attachment = $caseTemplateItems[$j]->need_attachment;
                    $loanCaseChecklistDetails->auto_dispatch = $caseTemplateItems[$j]->auto_dispatch;
                    $loanCaseChecklistDetails->auto_receipt = $caseTemplateItems[$j]->auto_receipt;
                    $loanCaseChecklistDetails->close_case = $caseTemplateItems[$j]->close_case;
                    $loanCaseChecklistDetails->open_case = $caseTemplateItems[$j]->open_case;
                    $loanCaseChecklistDetails->pic_id = $pic_id;
                    $loanCaseChecklistDetails->email_template_id = $caseTemplateItems[$j]->email_template_id;
                    $loanCaseChecklistDetails->email_recurring = $caseTemplateItems[$j]->email_recurring;
                    $loanCaseChecklistDetails->remark = '';

                    if($openfile == 0)
                    {
                        $loanCaseChecklistDetails->status = 1;
                    }
                    else
                    {
                        $loanCaseChecklistDetails->status = 0;
                    }

                    $openfile += 1;
                    
                   
                    $loanCaseChecklistDetails->created_at = date('Y-m-d H:i:s');

                    $loanCaseChecklistDetails->save();

                }
            }

            
        }

    }

    public function getDuration($duration)
    {
        $target_date = date('Y-m-d H:i:s');
        $today_date = date('Y-m-d H:i:s');
        $target_date = date('Y-m-d H:i:s', strtotime($target_date . ' +' . $duration . ' day'));

        return $target_date;
    }
}
