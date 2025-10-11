<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyTodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('case_todo')->insert([ 
            'type' => 1,  //bill approval
            'ref_id' => 1,
            'case_id' => 1,
            'start_dttm' =>date('Y-m-d H:i:s'),
            'expired_dttm' =>date('Y-m-d H:i:s'),
            'request_user_id' => 9,
            'approval_user_id' => 1,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('case_todo')->insert([ 
            'type' => 1,  //bill approval
            'ref_id' => 2,
            'case_id' => 1,
            'start_dttm' =>date('Y-m-d H:i:s'),
            'expired_dttm' =>date('Y-m-d H:i:s'),
            'request_user_id' => 9,
            'approval_user_id' => 1,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

    }
}
