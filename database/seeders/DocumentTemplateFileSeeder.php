<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTemplateFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('document_template_file')->insert([ 
            'name' => 'SPA_with_Title_PV_010122',
            'path' => 'resources/template/documents/SPA_with_Title_PV_010122.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file')->insert([ 
            'name' => 'Form 19B & 19G - individual',
            'path' => 'resources/template/documents/Form_19B_n_19G_individual.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file')->insert([ 
            'name' => 'Letter of Confirmation Not lodge Caveat',
            'path' => 'resources/template/documents/Letter_of_Confirmation_Not_lodge_Caveat.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file')->insert([ 
            'name' => 'P - Appointment Letter',
            'path' => 'resources/template/documents/P_Appointment_Letter.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        
        DB::table('document_template_file')->insert([ 
            'name' => 'SPA cover 1 sol',
            'path' => 'resources/template/documents/SPA_cover_1_sol.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);



        // latest file template main

        DB::table('document_template_file_main')->insert([ 
            'name' => 'SPA_with_Title_PV_010122',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_main')->insert([ 
            'name' => 'Form 19B & 19G - individual',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_main')->insert([ 
            'name' => 'Letter of Confirmation Not lodge Caveat',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_main')->insert([ 
            'name' => 'P - Appointment Letter',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        
        DB::table('document_template_file_main')->insert([ 
            'name' => 'SPA cover 1 sol',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // latest file template details

        DB::table('document_template_file_details')->insert([ 
            'document_template_file_main_id' => 1,
            'file_name' => 'SPA_with_Title_PV_010122.docx',
            'ori_file_name' => 'SPA_with_Title_PV_010122.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_details')->insert([ 
            'document_template_file_main_id' => 1,
            'file_name' => 'SPA_with_Title_PV_010122_v0.docx',
            'ori_file_name' => 'SPA_with_Title_PV_010122.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 0,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_details')->insert([ 
            'document_template_file_main_id' => 2,
            'file_name' => 'Form_19B_n_19G_individual.docx',
            'ori_file_name' => 'Form_19B_n_19G_individual.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_details')->insert([ 
            'document_template_file_main_id' => 3,
            'file_name' => 'Letter_of_Confirmation_Not_lodge_Caveat.docx',
            'ori_file_name' => 'Letter_of_Confirmation_Not_lodge_Caveat.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('document_template_file_details')->insert([ 
            'document_template_file_main_id' => 4,
            'file_name' => 'P_Appointment_Letter.docx',
            'ori_file_name' => 'P_Appointment_Letter.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        
        DB::table('document_template_file_details')->insert([ 
            'document_template_file_main_id' => 5,
            'file_name' => 'SPA_cover_1_sol.docx',
            'ori_file_name' => 'SPA_cover_1_sol.docx',
            'type' => 1,
            'remarks' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


    }
}
