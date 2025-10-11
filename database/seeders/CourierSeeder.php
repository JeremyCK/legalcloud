<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('courier')->insert([ 
            'name' => 'DHL',
            'short_code' => 'DHL',
            'desc' => '',
            'tel_no' => '',
            'fax' => '',
            'address' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('courier')->insert([ 
            'name' => 'Ninja Van',
            'short_code' => 'NV',
            'desc' => '',
            'tel_no' => '',
            'fax' => '',
            'address' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        

    }
}
