<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTemplateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'PV-Title',
            'code' => 'pv_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'PV-Direct Transfer',
            'code' => 'pv_direct_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'PV-Double Transfer',
            'code' => 'pv_double_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'PV-Master Title',
            'code' => 'pv_master_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'P-Master Title',
            'code' => 'p_master_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'V-Master Title',
            'code' => 'v_master_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'P-Title',
            'code' => 'p_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'P-Direct Transfer',
            'code' => 'p_direct_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'P-Double Transfer',
            'code' => 'p_double_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'P-Double Transfer',
            'code' => 'p_double_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'V-Title',
            'code' => 'v_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'V-Direct Transfer',
            'code' => 'v_direct_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'V-Double Transfer',
            'code' => 'v_double_transfer',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'Title',
            'code' => 'title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_categories')->insert([ 
            'name' => 'Master Title',
            'code' => 'master_title',
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        

    }
}
