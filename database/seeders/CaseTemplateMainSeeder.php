<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTemplateMainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        #1
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/No Consent/Charged to Bank',
            'checklist_category_id' => 1,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #2
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/No Consent/Free Encumbrances',
            'checklist_category_id' => 1,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #3
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Consent/Charged to Bank',
            'checklist_category_id' => 1,
            'consent_type' => 0,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #4
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Consent/Free Encumbrances',
            'checklist_category_id' => 1,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #5
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Double Consent/Charged to Bank',
            'checklist_category_id' => 1,
            'consent_type' => 2,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #6
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Double Consent/Free Encumbrances',
            'checklist_category_id' => 1,
            'consent_type' => 2,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #7
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Triple Consent/Charged to Bank',
            'checklist_category_id' => 1,
            'consent_type' => 3,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #8
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Triple Consent/Free Encumbrances',
            'checklist_category_id' => 1,
            'consent_type' => 3,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Four Consent/Charged to Bank',
            'checklist_category_id' => 1,
            'consent_type' => 4,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Title/Four Consent/Free Encumbrances',
            'checklist_category_id' => 1,
            'consent_type' => 4,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // PV-Direct Tranfer 
        #9
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/No Consent/Charged to Bank',
            'checklist_category_id' => 2,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #10
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/No Consent/Free Encumbrances',
            'checklist_category_id' => 2,
            'consent_type' => 0,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #11
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Consent/Charged to Bank',
            'checklist_category_id' => 2,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #12
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Consent/Free Encumbrances',
            'checklist_category_id' => 2,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #13
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Double Consent/Charged to Bank',
            'checklist_category_id' => 2,
            'consent_type' => 2,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #14
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Double Consent/Free Encumbrances',
            'checklist_category_id' => 2,
            'consent_type' => 2,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #15
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Triple Consent/Charged to Bank',
            'checklist_category_id' => 2,
            'consent_type' => 3,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #16
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Triple Consent/Free Encumbrances',
            'checklist_category_id' => 2,
            'consent_type' => 3,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #17
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Four Consent/Charged to Bank',
            'checklist_category_id' => 2,
            'consent_type' => 4,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #17
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Direct Transfer/Four Consent/Free Encumbrances',
            'checklist_category_id' => 2,
            'consent_type' => 4,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // PV-Double Transfer
        #17
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/No Consent/Charged to Bank',
            'checklist_category_id' => 3,
            'consent_type' => 0,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #18
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/No Consent/Free Encumbrances',
            'checklist_category_id' => 3,
            'consent_type' => 0,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #19
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/Consent/Charged to Bank',
            'checklist_category_id' => 3,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #20
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/Consent/Free Encumbrances',
            'checklist_category_id' => 3,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #21
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/Double Consent/Charged to Bank',
            'checklist_category_id' => 3,
            'consent_type' => 2,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #22
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/Double Consent/Free Encumbrances',
            'checklist_category_id' => 3,
            'consent_type' => 2,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #23
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/Triple Consent/Charged to Bank',
            'checklist_category_id' => 3,
            'consent_type' => 3,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #24
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Double Transfer/Triple Consent/Free Encumbrances',
            'checklist_category_id' => 3,
            'consent_type' => 3,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // PV-Master Title
         #25
         DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Master Title/Charged to Bank',
            'checklist_category_id' => 4,
            'consent_type' => 99,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #26
        DB::table('checklist_template_main')->insert([ 
            'name' => 'PV-Master Title/Free Emcumbrances',
            'checklist_category_id' => 4,
            'consent_type' => 99,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // P-Master Title
        #27
        DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Master Title',
            'checklist_category_id' => 5,
            'consent_type' => 99,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // V-Master Title
        #28
        DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Master Title/Charged to Bank',
            'checklist_category_id' => 6,
            'consent_type' => 99,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

          #29
          DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Master Title/Free Encumbrances',
            'checklist_category_id' => 6,
            'consent_type' => 99,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // P-Title
        #30
        DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Title/No Consent',
            'checklist_category_id' => 7,
            'consent_type' => 0,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #31
        DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Title/Consent',
            'checklist_category_id' => 7,
            'consent_type' => 1,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #32
        DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Title/Double Consent',
            'checklist_category_id' => 7,
            'consent_type' => 2,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #33
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Title/Triple Consent',
            'checklist_category_id' => 7,
            'consent_type' => 3,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

          #33
          DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Title/Four Consent',
            'checklist_category_id' => 7,
            'consent_type' => 4,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // P-Direct Transfer
         #34
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Direct Transfer/No Consent',
            'checklist_category_id' => 8,
            'consent_type' => 0,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #35
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Direct Transfer/Consent',
            'checklist_category_id' => 8,
            'consent_type' => 1,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #36
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Direct Transfer/Double Consent',
            'checklist_category_id' => 8,
            'consent_type' => 2,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #37
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Direct Transfer/Triple Consent',
            'checklist_category_id' => 8,
            'consent_type' => 3,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #37
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Direct Transfer/Four Consent',
            'checklist_category_id' => 8,
            'consent_type' => 4,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // P-Double Transfer
         #38
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Double Transfer/No Consent',
            'checklist_category_id' => 9,
            'consent_type' => 0,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #39
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Double Transfer/Consent',
            'checklist_category_id' => 9,
            'consent_type' => 1,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #40
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Double Transfer/Double Consent',
            'checklist_category_id' => 9,
            'consent_type' => 2,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #41
         DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Double Transfer/Triple Consent',
            'checklist_category_id' => 9,
            'consent_type' => 3,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #41
        DB::table('checklist_template_main')->insert([ 
            'name' => 'P-Double Transfer/Four Consent',
            'checklist_category_id' => 9,
            'consent_type' => 4,
            'encumbrances_type' => 99,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // V-Title
         #42
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/No Consent/Charged to Bank ',
            'checklist_category_id' => 10,
            'consent_type' => 0,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        
         #43
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/No Consent/Free Encumbrances',
            'checklist_category_id' => 10,
            'consent_type' => 0,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #44
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Consent/Charged to Bank',
            'checklist_category_id' => 10,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #45
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Consent/Free Encumbrances',
            'checklist_category_id' => 10,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #46
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Double Consent/Charged to Bank',
            'checklist_category_id' => 10,
            'consent_type' => 2,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #47
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Double Consent/Free Encumbrances',
            'checklist_category_id' => 10,
            'consent_type' => 2,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #48
        DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Triple Consent/Charged to Bank',
            'checklist_category_id' => 10,
            'consent_type' => 3,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #49
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Triple Consent/Free Encumbrances',
            'checklist_category_id' => 10,
            'consent_type' => 3,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #48
        DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Four Consent/Charged to Bank',
            'checklist_category_id' => 10,
            'consent_type' => 4,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #49
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Title/Four Consent/Free Encumbrances',
            'checklist_category_id' => 10,
            'consent_type' => 4,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // V-Direct Transfer
         #50
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/No Consent/Charged to Bank',
            'checklist_category_id' => 11,
            'consent_type' => 0,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        
         #51
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/No Consent/Free Encumbrances',
            'checklist_category_id' => 11,
            'consent_type' => 0,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #52
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Consent/Charged to Bank',
            'checklist_category_id' => 11,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #53
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Consent/Free Encumbrances',
            'checklist_category_id' => 11,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #54
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Double Consent/Charged to Bank',
            'checklist_category_id' => 11,
            'consent_type' => 2,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #55
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Double Consent/Free Encumbrances',
            'checklist_category_id' => 11,
            'consent_type' => 2,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #56
        DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Triple Consent/Charged to Bank',
            'checklist_category_id' => 11,
            'consent_type' => 3,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #57
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Triple Consent/Free Encumbrances',
            'checklist_category_id' => 11,
            'consent_type' => 3,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #56
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Four Consent/Charged to Bank',
            'checklist_category_id' => 11,
            'consent_type' => 4,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #57
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Direct Transfer/Four Consent/Free Encumbrances',
            'checklist_category_id' => 11,
            'consent_type' => 4,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        //V-Double Transfer
        #58
        DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/No Consent/Charged to Bank',
            'checklist_category_id' => 12,
            'consent_type' => 0,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        
         #59
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/No Consent/Free Encumbrances',
            'checklist_category_id' => 12,
            'consent_type' => 0,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #60
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/Consent/Charged to Bank',
            'checklist_category_id' => 12,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #61
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/Consent/Free Encumbrances',
            'checklist_category_id' => 12,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #62
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/Double Consent/Charged to Bank',
            'checklist_category_id' => 12,
            'consent_type' => 2,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #63
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/Double Consent/Free Encumbrances',
            'checklist_category_id' => 12,
            'consent_type' => 2,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #64
        DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/Triple Consent/Charged to Bank',
            'checklist_category_id' => 12,
            'consent_type' => 3,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         #65
         DB::table('checklist_template_main')->insert([ 
            'name' => 'V-Double Transfer/Triple Consent/Free Encumbrances',
            'checklist_category_id' => 12,
            'consent_type' => 3,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // loan


        DB::table('checklist_template_main')->insert([ 
            'name' => 'Title - Consent - Free Encumbrances',
            'checklist_category_id' => 13,
            'consent_type' => 1,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_main')->insert([ 
            'name' => 'Title - Consent - Charged to Bank',
            'checklist_category_id' => 13,
            'consent_type' => 1,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_main')->insert([ 
            'name' => 'Title - No Consent - Free Encumbrances',
            'checklist_category_id' => 13,
            'consent_type' => 0,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_main')->insert([ 
            'name' => 'Title - No Consent - Charged to Bank',
            'checklist_category_id' => 13,
            'consent_type' => 0,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        DB::table('checklist_template_main')->insert([ 
            'name' => 'Master Title - Free Encumbrances',
            'checklist_category_id' => 14,
            'consent_type' => 99,
            'encumbrances_type' => 2,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('checklist_template_main')->insert([ 
            'name' => 'Master Title - Charged to Bank',
            'checklist_category_id' => 14,
            'consent_type' => 99,
            'encumbrances_type' => 1,
            'target_close_day' => 90,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
    }
}
