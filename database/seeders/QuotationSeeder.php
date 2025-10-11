<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // setup main template
        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation LOAN,master title,RHB Islamic',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 2,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 3,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 4,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 5,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 6,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 7,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 8,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 9,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 10,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 30,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 31,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 32,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 33,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 34,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 35,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 36,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 38,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 53,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 54,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 55,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 56,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 57,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 58,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 59,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 60,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 1,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);




        // ====================================================================



        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation LOAN,with title,FH,RHB Islamic',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 2,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 3,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 4,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 5,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 6,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 7,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 8,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 9,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 10,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 30,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 31,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 32,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 33,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 34,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 35,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 36,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 38,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 53,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 54,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 55,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 56,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 65,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 58,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 59,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 60,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 2,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // ====================================================================


        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation LOAN,with title,FH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 2,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 11,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 12,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 5,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 6,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 13,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 14,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 15,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 10,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 30,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 31,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 32,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 39,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 40,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 41,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 36,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 53,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 54,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 55,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 56,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 65,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 58,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 59,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 60,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 3,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // ====================================================================
        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation LOAN,with title,LH,RHB Islamic',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 2,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 3,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 4,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 5,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 12,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 13,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 14,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 15,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 10,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 30,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 31,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 32,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 33,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 34,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 35,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 36,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 38,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 53,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 54,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 55,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 56,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 65,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 58,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 59,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 60,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 4,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation LOAN,with title,LH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 2,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 11,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 12,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 5,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 6,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 13,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 14,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 15,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 10,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);




        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 30,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 31,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 32,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 39,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 40,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 41,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 38,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 53,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 54,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 55,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 56,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 66,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 58,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 59,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 60,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 5,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation P master title,FH,LH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 17,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 18,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 19,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 20,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 22,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 24,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 42,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 43,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 44,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 45,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 67,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 68,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 69,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 70,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 71,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 72,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 73,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 6,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation P,with title,FH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 17,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 18,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 19,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 20,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 22,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 24,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 42,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 43,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 46,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 45,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 67,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 68,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 69,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 70,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 71,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 72,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 73,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 7,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation P,with title,FH2',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 17,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 18,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 19,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 20,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 22,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 24,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 42,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 43,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 46,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 45,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 67,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 68,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 69,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 70,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 71,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 72,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 73,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 8,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation P,with title,LH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 17,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 18,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 19,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 20,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 22,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 24,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 42,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 43,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 46,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 45,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 67,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 68,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 69,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 70,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 71,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 72,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 73,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 9,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);




        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation V(Join Lawyer) master title',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 17,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 26,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 27,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 28,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 29,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 21,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 22,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 80,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 47,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 48,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 75,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 76,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 77,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 78,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 79,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 10,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);




        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation V(Join Lawyer) with title,FH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 25,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 26,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 27,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 28,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 29,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 21,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 22,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 80,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 47,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 48,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 75,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 76,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 77,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 78,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 79,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 11,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // ====================================================================

        DB::table('quotation_template_main')->insert([
            'name' => 'Quotation V(Join Lawyer) with title,LH',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        #1
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 16,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 25,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 26,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 27,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 28,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 29,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 21,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 23,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        #2
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 37,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 80,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 47,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 48,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);



         #3
         DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 49,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 50,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 51,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 52,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 75,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 76,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 77,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 78,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 79,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 61,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 62,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 63,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('quotation_template_details')->insert([
            'acc_main_template_id' => 12,
            'account_item_id' => 64,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
