<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* tenporary known as bank  */
        DB::table('referral')->insert([ 
            'name' => 'Aaron',
            'email' => '',
            'phone_no' => '017-301 3317',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Adrian Thoo',
            'email' => 'adrianthoo.ims@gmail.com',
            'phone_no' => '017-2803796',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Alan Most',
            'email' => '',
            'phone_no' => '01127002088',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Calvin CM',
            'email' => '',
            'phone_no' => '0173727301',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Christine Yap',
            'email' => '',
            'phone_no' => '0162323930',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Chua Evon',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'CK Kuan',
            'email' => '',
            'phone_no' => '',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'CK Yap',
            'email' => '',
            'phone_no' => '0122057499',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Dave Tan Wai Lun',
            'email' => '',
            'phone_no' => '0169067368',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'PBB',
            'email' => '',
            'phone_no' => '',
            'Company' => 'PBB',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Edmes Ong Kim Seng',
            'email' => '',
            'phone_no' => '0126022196',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Elroy Chan',
            'email' => 'xiaoshen0511@hotmail.com',
            'phone_no' => '0167124377',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Esther',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'AmBank',
            'email' => '',
            'phone_no' => '',
            'Company' => 'AmBank',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Ginnier Lim',
            'email' => 'ginnielim.ims@gmail.com',
            'phone_no' => '0142192588',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Hema',
            'email' => 'hema@lhyeo.com',
            'phone_no' => '0182278118',
            'Company' => 'LHY',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Kent Chew',
            'email' => '',
            'phone_no' => '0166162953',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Iqmal',
            'email' => '',
            'phone_no' => '0182494059',
            'Company' => 'PBB',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jack Cheng Shun Cai',
            'email' => '',
            'phone_no' => '0109142566',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Janice',
            'email' => 'jarickhoo@gmail.com',
            'phone_no' => '0126267690',
            'Company' => 'MOST',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jaric Khoo',
            'email' => 'jarickhoo@gmail.com',
            'phone_no' => '0126267690',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jasmine Ng',
            'email' => '',
            'phone_no' => '0105632628',
            'Company' => 'TBC',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jason Leow',
            'email' => '',
            'phone_no' => '0193633481',
            'Company' => 'MOST',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jennifer',
            'email' => '',
            'phone_no' => '0172086941',
            'Company' => 'Arden',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jeremy Tan',
            'email' => 'jeremytan.ims@gmail.com',
            'phone_no' => '0167704604',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Joey Lim Boon Siew',
            'email' => '',
            'phone_no' => '0163113315',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Jumo',
            'email' => '',
            'phone_no' => '0169626197',
            'Company' => 'Most',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Ken Chong Jia Ken',
            'email' => '',
            'phone_no' => '0109022190',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Kenneth wang',
            'email' => '',
            'phone_no' => '0109022190',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Lim Mei Qi',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Lim Tian Wei',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Louis',
            'email' => '',
            'phone_no' => '0175556631',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Low Pei Yee',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Malina',
            'email' => '',
            'phone_no' => '0135143589',
            'Company' => 'PBB',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Mandy',
            'email' => '',
            'phone_no' => '0163928083',
            'Company' => 'MOST',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'MingYi',
            'email' => '',
            'phone_no' => '0174643527',
            'Company' => 'PBB',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'MK Chua Soon Huat',
            'email' => '',
            'phone_no' => '0102204294',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Nelson',
            'email' => '',
            'phone_no' => '0146259600',
            'Company' => 'CIMB',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Nicole',
            'email' => '',
            'phone_no' => '0129252775',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Priscilla',
            'email' => '',
            'phone_no' => '',
            'Company' => 'C.A.P',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Priya',
            'email' => '',
            'phone_no' => '',
            'Company' => 'LHY',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Reeve Cheong',
            'email' => 'reevecheong.ims@gmail.com',
            'phone_no' => '0163949910',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Resh',
            'email' => '',
            'phone_no' => '0128022115',
            'Company' => 'LM',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Sam',
            'email' => '',
            'phone_no' => '0126370711',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Shawn',
            'email' => '',
            'phone_no' => '0126370711',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Shirley Zhen',
            'email' => 'shirleyzhen.ims@gmail.com',
            'phone_no' => '01139433131',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'SK Luk Siew Koon',
            'email' => '',
            'phone_no' => '0122289799',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Sukri',
            'email' => '',
            'phone_no' => '0176329192',
            'Company' => 'LM',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Syira',
            'email' => '',
            'phone_no' => '0133351452',
            'Company' => 'LM',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Tan Chu Yao',
            'email' => '',
            'phone_no' => '0125720300',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Tan Wei Tong',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Tan Zheng Hong',
            'email' => 'zzhenghoong96@gmail.com',
            'phone_no' => '0173122621',
            'Company' => 'IMS',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Tee Tet Wah',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Terry',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Vivian Yap Hui Chin',
            'email' => '',
            'phone_no' => '0122092513',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Christine',
            'email' => '',
            'phone_no' => '0162323930',
            'Company' => 'VPG',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'JY',
            'email' => '',
            'phone_no' => '01135712026',
            'Company' => 'VPG',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Wong Sin Yuen',
            'email' => '',
            'phone_no' => '',
            'Company' => '',
            'status' => 1,
        ]);
        DB::table('referral')->insert([ 
            'name' => 'Zafran',
            'email' => '',
            'phone_no' => '0164216296',
            'Company' => 'LM',
            'status' => 1,
        ]);

    }
}
