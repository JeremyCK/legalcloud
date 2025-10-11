<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* tenporary known as bank  */
        DB::table('banks')->insert([ 
            'name' => 'Maybank',
            'short_code' => 'MBB',
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'status' => 1,
        ]);
        DB::table('banks')->insert([ 
            'name' => 'Public Bank',
            'short_code' => 'PBB',
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'status' => 1,
        ]);
        DB::table('banks')->insert([ 
            'name' => 'Hong Leong Bank',
            'short_code' => 'HLB',
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'status' => 1,
        ]);

        DB::table('case_type')->insert([ 
            'name' => 'Hire Purchase',
            'is_bank_required' => 1,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('case_type')->insert([ 
            'name' => 'Ligitation',
            'is_bank_required' => 0,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('case_type')->insert([ 
            'name' => 'LPPSA',
            'is_bank_required' => 0,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('case_type')->insert([ 
            'name' => 'SPA',
            'is_bank_required' => 0,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('client')->insert([ 
            'name' => 'Mr Tan',
            'ic_no' => '1234567890',
            'client_type' => 1,
            'phone_no' => '1234567890',
            'email' => 'mrtan@gmail.com',
            'status' => 1,
            'case_count' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('client')->insert([ 
            'name' => 'Mr Lim',
            'ic_no' => '222222222',
            'phone_no' => '2222222222',
            'client_type' => 1,
            'email' => 'mrlim@gmail.com',
            'status' => 1,
            'case_count' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('client')->insert([ 
            'name' => 'Mr Liew',
            'ic_no' => '3333333333',
            'phone_no' => '333333',
            'client_type' => 1,
            'email' => 'mrliew@gmail.com',
            'status' => 1,
            'case_count' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        DB::table('client')->insert([ 
            'name' => 'Mrs Yeo',
            'ic_no' => '44444444',
            'phone_no' => '44444444',
            'client_type' => 1,
            'email' => 'mrsyeo@gmail.com',
            'status' => 1,
            'case_count' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        

    }
}
