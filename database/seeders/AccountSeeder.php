<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        /* Main cat setup  */
        DB::table('account_main_cat')->insert([ 
            'code' => 'AS',
            'category' => 'Assets',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('account_main_cat')->insert([ 
            'code' => 'EX',
            'category' => 'Expenses',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('account_main_cat')->insert([ 
            'code' => 'LI',
            'category' => 'Liabilities',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('account_main_cat')->insert([ 
            'code' => 'EQ',
            'category' => 'Equity',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        DB::table('account_main_cat')->insert([ 
            'code' => 'RE',
            'category' => 'Revenue',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // setup account category
        DB::table('account_category')->insert([ 
            'code' => 'PF',
            'category' => 'Professional fees',
            'taxable' => 1,
            'percentage' => 6.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_category')->insert([ 
            'code' => 'SD',
            'category' => 'Stamp duties',
            'taxable' => 0,
            'percentage' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_category')->insert([ 
            'code' => 'D',
            'category' => 'Disbursement',
            'taxable' => 0,
            'percentage' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // acctoun===============================

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Facilities/Loan Agreement',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Charge Annexure / Deed of Assignment',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Power of Attorney',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Application for Consent to Charge',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Entry & Withrawal of Private Caveat',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Developer Confirmation',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Entry & Withrawal of Private Caveat',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Supplemental Agreement',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Form 34',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Form 34',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Letter of Guarantee',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 1,
            'name' => 'Statutory Declaration',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Principal Facilities Agreement',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Facilities Agreement',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Charge Annexure / Deed of Assignment',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Power of Attorney',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Letter of Guarantee',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Supplemental Agreement',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Statutory Declaration',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 2,
            'name' => 'Letter of Offer',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // DISBURSEMENTS
        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Affirmation of Statutory Declaration',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Land Search',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'OA Search',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'SSM Search',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Registration Fee for Power of Attorney',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Registration Fee for Charge',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Registration of Entry & Withrawal of Private Caveat',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Registration Fee for Form 34',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Letter Of Undertaking',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'CTC Title / Official Search',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Developer\'s Confirmation Fee',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Purchase of Loan Documents',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Fax/Telephone Charges',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Printing/Photocopying Charges',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Transport/Courier/Postage Charges',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account')->insert([ 
            'account_category_id' => 3,
            'name' => 'Miscellaneous',
            'created_at' =>date('Y-m-d H:i:s')
        ]);

 // acctoun===============================


        // setup main template
        DB::table('account_template_main')->insert([ 
            'name' => 'Hire purchase quotation template',
            'remark' => 'Estimated Quotation',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

         // setup template account_template_details
         DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Facilities/Loan Agreement',
            'account_cat_id' => 1,
            'amount' => 2496.09,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Charge Annexure / Deed of Assignment',
            'account_cat_id' => 1,
            'amount' => 300.00,
            'remark' => '(min RM300.00)',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Power of Attorney',
            'account_cat_id' => 1,
            'amount' => 300.00,
            'remark' => '(min RM300.00)',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Application for Consent to Charge',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Entry & Withrawal of Private Caveat',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Developer Confirmation ',
            'account_cat_id' => 1,
            'amount' => 200.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Entry & Withrawal of Private Caveat',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Supplemental Agreement',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Form 34',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Form 34',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Letter of Guarantee',
            'account_cat_id' => 1,
            'amount' => 0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Statutory Declaration',
            'account_cat_id' => 1,
            'amount' =>200.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Principal Facilities Agreement',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>0.00,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Facilities Agreement',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>30.00,
            'remark' =>'3 copies subsidiary',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Charge Annexure / Deed of Assignment',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>40.00,
            'remark' =>'4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Power of Attorney',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>40.00,
            'remark' =>'4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Letter of Guarantee',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>0.00,
            'remark' =>'4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Supplemental Agreement',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>0.00,
            'remark' =>'4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Statutory Declaration',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>20.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Letter of Offer',
            'need_approval' => 1,
            'account_cat_id' => 2,
            'amount' =>10.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        // DISBURSEMENTS
        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Affirmation of Statutory Declaration',
            'account_cat_id' => 3,
            'amount' =>50.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Land Search',
            'account_cat_id' => 3,
            'amount' =>120.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'OA Search',
            'account_cat_id' => 3,
            'amount' =>100.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'SSM Search',
            'account_cat_id' => 3,
            'amount' =>100.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Registration Fee for Power of Attorney',
            'account_cat_id' => 3,
            'amount' =>80.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Registration Fee for Charge',
            'account_cat_id' => 3,
            'amount' =>0.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Registration of Entry & Withrawal of Private Caveat',
            'account_cat_id' => 3,
            'amount' =>0.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Registration Fee for Form 34',
            'account_cat_id' => 3,
            'amount' =>0.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Letter Of Undertaking',
            'account_cat_id' => 3,
            'amount' =>300.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'CTC Title / Official Search',
            'account_cat_id' => 3,
            'amount' =>100.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Developer\'s Confirmation Fee',
            'account_cat_id' => 3,
            'amount' =>80.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Purchase of Loan Documents',
            'account_cat_id' => 3,
            'amount' =>250.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Fax/Telephone Charges',
            'account_cat_id' => 3,
            'amount' =>100.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Printing/Photocopying Charges',
            'account_cat_id' => 3,
            'amount' =>150.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Transport/Courier/Postage Charges',
            'account_cat_id' => 3,
            'amount' =>280.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_template_details')->insert([ 
            'acc_main_template_id' => 1,
            'item_name' => 'Miscellaneous',
            'account_cat_id' => 3,
            'amount' =>100.00,
            'remark' =>'',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

    }
}
