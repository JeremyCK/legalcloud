<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter;
use Illuminate\Support\Facades\DB;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $paramValues = array();

        // Parameter::create([
        //     'parameter_type' => "reassign_duration",
        //     'parameter_value_1' => "14400",
        //     'parameter_value_2' => "",
        //     'status' => "1",
        // ]);

        // Parameter::create([
        //     'parameter_type' => "customer_reminder_cron",
        //     'parameter_value_1' => "0 1 * * *",
        //     'parameter_value_2' => "",
        //     'status' => "1",
        // ]);

        // Parameter::create([
        //     'parameter_type' => "developer_reminder_cron",
        //     'parameter_value_1' => "0 1 * * *",
        //     'parameter_value_2' => "",
        //     'status' => "1",
        // ]);

        // Parameter::create([
        //     'parameter_type' => "balance_loan_reminder_cron",
        //     'parameter_value_1' => "0 1 * * *",
        //     'parameter_value_2' => "",
        //     'status' => "1",
        // ]);

        // parameter for reminder which use to send email (need to put a field in table to specify date based on the parameter value)
        // $paramValues[] = ["parameter_type"=>"CA_7_ARD","parameter_value_1"=>"days","parameter_value_2"=>"7","status"=>"1"];
        // $paramValues[] = ["parameter_type"=>"AR_1_ARD","parameter_value_1"=>"days","parameter_value_2"=>"1","status"=>"1"];
        // $paramValues[] = ["parameter_type"=>"AR_1_VF","parameter_value_1"=>"days","parameter_value_2"=>"1","status"=>"1"];
        // $paramValues[] = ["parameter_type"=>"AR_3_DPC","parameter_value_1"=>"days","parameter_value_2"=>"3","status"=>"1"];
        // $paramValues[] = ["parameter_type"=>"AR_3_MCDL","parameter_value_1"=>"days","parameter_value_2"=>"3","status"=>"1"];
        // $paramValues[] = ["parameter_type"=>"AR_3_VFRS","parameter_value_1"=>"days","parameter_value_2"=>"3","status"=>"1"];
        // $paramValues[] = ["parameter_type"=>"case_running_no","parameter_value_1"=>"00001","parameter_value_2"=>"","status"=>"1"];

        DB::table('parameter')->insert(['parameter_type' => 'CA_7_ARD','parameter_value_1' => "days",'parameter_value_2' => "7",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'AR_1_ARD','parameter_value_1' => "days",'parameter_value_2' => "1",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'AR_1_VF','parameter_value_1' => "days",'parameter_value_2' => "1",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'AR_3_DPC','parameter_value_1' => "days",'parameter_value_2' => "3",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'AR_3_MCDL','parameter_value_1' => "days",'parameter_value_2' => "3",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'AR_3_VFRS','parameter_value_1' => "days",'parameter_value_2' => "3",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'case_running_no','parameter_value_1' => "100001",'parameter_value_2' => "",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'transaction_running_no','parameter_value_1' => "10000001",'parameter_value_2' => "",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'voucher_running_no','parameter_value_1' => "10000001",'parameter_value_2' => "",'status' => "1",]);

        DB::table('parameter')->insert(['parameter_type' => 'bill_running_no','parameter_value_1' => "100000",'parameter_value_2' => "",'status' => "1",]);

        // voucher payment type
        DB::table('parameter')->insert(['parameter_type' => 'payment_type','parameter_value_1' => "CASH",'parameter_value_2' => "Cash",'parameter_value_3' => "1",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'payment_type','parameter_value_1' => "CHEQUE",'parameter_value_2' => "Cheque",'parameter_value_3' => "2",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'payment_type','parameter_value_1' => "BANK",'parameter_value_2' => "Bank transfer",'parameter_value_3' => "3",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'payment_type','parameter_value_1' => "CARD",'parameter_value_2' => "Credit Card",'parameter_value_3' => "4",'status' => "1",]);

        // file template path 
        DB::table('parameter')->insert(['parameter_type' => 'template_file_path','parameter_value_1' => "app/documents/templates/",'parameter_value_2' => "",'parameter_value_3' => "",'status' => "1",]);
        DB::table('parameter')->insert(['parameter_type' => 'case_file_path','parameter_value_1' => "app/documents/cases/",'parameter_value_2' => "",'parameter_value_3' => "",'status' => "1",]);

        // for($i = 0; $i<$paramValues; $i++){
        //     DB::table('parameter')->insert([$paramValues[$i]]);
        // }


        // DB::table('parameter')->insert([$paramValues]);
    }
}
