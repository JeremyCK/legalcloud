<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('portfolio')->insert([ 
            'name' => 'Public Bank',
            'short_code' => 'PBB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'AmBank',
            'short_code' => 'AMB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'RHB Bank',
            'short_code' => 'RHB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'Bank Simpanan Nasional',
            'short_code' => 'BSN',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'CIMB',
            'short_code' => 'CIMB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'CIMB (SME)',
            'short_code' => 'CIMB (SME)',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'Muamalat',
            'short_code' => 'Muamalat',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'standard chartered',
            'short_code' => 'SCB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'Hong Leong Bank',
            'short_code' => 'HLB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'UOB',
            'short_code' => 'UOB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'Affin',
            'short_code' => 'Affin',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'Maybank',
            'short_code' => 'MBB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'MBSB Bank',
            'short_code' => 'MBSB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'MBSB Bank',
            'short_code' => 'MBSB',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'PV',
            'short_code' => 'PV',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'Lembaga Pembiayaan Perumahan Sektor Awam',
            'short_code' => 'LPPSA',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'PV (HLB)',
            'short_code' => 'PV (HLB)',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

        DB::table('portfolio')->insert([ 
            'name' => 'V',
            'short_code' => 'V',
            'category' => 1,
            'tel_no' => NULL,
            'fax' => NULL,
            'address' => NULL,
            'remark' => NULL,
            'status' => 1,
        ]);

    }
}
