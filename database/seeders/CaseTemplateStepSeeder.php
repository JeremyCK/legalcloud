<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTemplateStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('checklist_case_category')->insert([ 
            'name' => 'Loan',
            'name' => 'loan',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_case_category')->insert([ 
            'name' => 'S&P',
            'name' => 'snp',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_case_category')->insert([ 
            'name' => 'Litigation',
            'name' => 'litigation',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_case_category')->insert([ 
            'name' => 'Corporate',
            'name' => 'corporate',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // step

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Open',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'S&P',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'POT',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'DT',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'MOT',
            'From' => '8,10,11,12,13',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'DCL',
            'From' => '',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'NOA',
            'From' => '',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'POT Consent',
            'From' => '',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'DT Consent',
            'From' => '',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Consent1',
            'From' => '2,8,9',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Consent2',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Consent3',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Consent4',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'CKHT',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Redemption',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Handover',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Close',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'BPP',
            'remarks' => '',
            'status' => 1,
            'category_id' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Open (Loan)',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Bank LI',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Consent',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Execution',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Redemption (Loan)',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Full Release',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Balance',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_steps')->insert([ 
            'name' => 'Close (loan)',
            'remarks' => '',
            'status' => 1,
            'category_id' => 2,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

    }
}
