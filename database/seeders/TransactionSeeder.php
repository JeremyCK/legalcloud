<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction')->insert([ 
            'transaction_id' => '10000001',
            'case_id' => 1,
            'user_id' => 38,
            'account_details_id' => 12,
            'transaction_type' => 'C',
            'amount' => 1500.00,
            'cheque_no' => 'BT130220 ',
            'bank_id' => 1,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('transaction')->insert([ 
            'transaction_id' => '10000002',
            'case_id' => 1,
            'user_id' => 38,
            'account_details_id' => 13,
            'transaction_type' => 'D',
            'amount' => 200.00,
            'cheque_no' => 'BT130221 ',
            'bank_id' => 1,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        

    }
}
