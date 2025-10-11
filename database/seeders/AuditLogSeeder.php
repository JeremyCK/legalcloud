<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /* Folders  */
        DB::table('audit_log')->insert([ 
            'user_id' => 1,
            'model' => 'Users',
            'desc' => 'Admin created an users',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('audit_log')->insert([ 
            'user_id' => 2,
            'model' => 'Users',
            'desc' => 'L H YEO created an users',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('audit_log')->insert([ 
            'user_id' => 3,
            'model' => 'Document',
            'desc' => 'STANLEY created documents template',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('audit_log')->insert([ 
            'user_id' => 1,
            'model' => 'Document',
            'desc' => 'Admin modifiend document template',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('audit_log')->insert([ 
            'user_id' => 1,
            'model' => 'Document',
            'desc' => 'Admin modifiend document template',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);



        DB::table('activity_log')->insert([ 
            'user_id' => 29,
            'case_id' => 1,
            'checklist_id' => 1,
            'action' => '',
            'desc' => 'Open file',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // DB::table('activity_log')->insert([ 
        //     'user_id' => 38,
        //     'case_id' => 1,
        //     'checklist_id' => 2,
        //     'action' => '',
        //     'desc' => 'Set checklist 2 status to done',
        //     'status' => 1,
        //     'created_at' =>date('Y-m-d H:i:s')
        // ]);

        // DB::table('activity_log')->insert([ 
        //     'user_id' => 38,
        //     'case_id' => 1,
        //     'checklist_id' => 5,
        //     'action' => '',
        //     'desc' => 'Attached filein checklist 5',
        //     'status' => 1,
        //     'created_at' =>date('Y-m-d H:i:s')
        // ]);

        // DB::table('activity_log')->insert([ 
        //     'user_id' => 38,
        //     'case_id' => 1,
        //     'checklist_id' => 5,
        //     'action' => '',
        //     'desc' => 'Attached file',
        //     'status' => 1,
        //     'created_at' =>date('Y-m-d H:i:s')
        // ]);

        // DB::table('activity_log')->insert([ 
        //     'user_id' => 38,
        //     'case_id' => 1,
        //     'checklist_id' => 5,
        //     'action' => '',
        //     'desc' => 'Update checklist 5 status to done',
        //     'status' => 1,
        //     'created_at' =>date('Y-m-d H:i:s')
        // ]);

    }
}
