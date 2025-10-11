<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Facilities/Loan Agreement',
            'formula' => '${scales_fee}',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Charge Annexure / Deed of Assignment',
            'min' => 200,
            'formula' => '${scales_fee}*10%',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Limited Declaration of Trust',
            'min' => 200,
            'formula' => '${scales_fee}*10%',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Purchase Undertaking',
            'min' => 200,
            'formula' => '${scales_fee}*10%',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Entry & Withrawal of Private Caveat',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Developer Confirmation',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Ijarah Agreement',
            'min' => 200,
            'formula' => '${scales_fee}*10%',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Service Agency Agreement',
            'min' => 200,
            'formula' => '${scales_fee}*10%',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Letter of Undertaking',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Statutory Declaration',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Power of Attorney',
            'min' => 300,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Application for Consent to Charge',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Supplemental Agreement',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Form 34',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Letter of Guarantee',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Sale and Purchase Agreement',
            'formula' => '${scales_fee}',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Deed of Assignment/Memorandum of Transfer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Deed of Mutual Covenants',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Developer Confirmation Fees',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Entry and Withdrawal of Caveat',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'CKHT 1A',
            'remark' => 'RM300 on 1st party, additional party RM200',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'CKHT 2A',
            'remark' => 'RM300 on 1st party, additional party RM300',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'CKHT 3A',
            'remark' => '(RM100/person)',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Low Cost Consent / Foreign Consent',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Deed of Receipt & Reassignment',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Discharge of Charge',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Consent to Transfer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Transfer from Developer to you',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 1,
            'name' => 'Bumi Consent',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        #2
        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Principal Facilities Agreement',
            'formula' => '${stamp_duty_50}',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Facilities Agreement',
            'remark' => '3 copies subsidiary',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Charge Annexure / Deed of Assignment',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Limited Declaration of Trust',
            'remark' => '3 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Purchase Undertaking',
            'remark' => '3 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Ijarah Agreement',
            'remark' => '3 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Service Agency Agreement',
            'remark' => '3 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Statutory Declaration',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Letter of Offer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Power of Attorney',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Letter of Guarantee',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Supplemental Agreement',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Memorandum of Transfer / Deed of Assignment (estimate)',
            'formula' => '${stamp_duty}',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Sale and Purchase Agreement',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Deed of Assignment',
            'remark' => '3 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Deed of Mutual Covenants',
            'remark' => '4 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Deed of Assignment (Subsidiary)',
            'remark' => '3 copies',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Deed of Receipt & Reassignment',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Memorandum of Transfer',
            'remark' => '(transfer from Developer to you)',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        #3
        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Affirmation of Statutory Declaration',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Land Search',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'OA Search',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'SSM Search',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration Fee for Power of Attorney',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration Fee for Charge',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration of Entry & Withrawal of Private Caveat',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration Fee for Form 34',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Application & Registration Fee for Consent',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'CTC Title / Official Search',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Developer\'s Confirmation Fee',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Purchase of Loan Documents',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Fax/Telephone Charges',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Printing/Photocopying Charges',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Transport/Courier/Postage Charges',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Miscellaneous',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Letter Of Undertaking',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Application Of Consent To Charge',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Carian Rasmi',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Application for Consent',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration for Consent',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration for Memorandum of Transfer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration for Entry of Private Caveat',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration for Withdrawal of Private Caveat',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Administration Fee for Developer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Administration Fee for Developer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration Fee for Discharge of Charge',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Purchase of Deed of Receipt & Reassignment',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Carian Rasmi, Application & Registration of Consent',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Registration of Transfer',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('account_item')->insert([ 
            'account_cat_id' => 3,
            'name' => 'Revocation of Power of Attorney',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        DB::table('account_item')->insert([ 
            'account_cat_id' => 2,
            'name' => 'Discharge of Charge',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

    }
}
