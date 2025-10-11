<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Munirah
        DB::table('teams')->insert([
            'name' => 'Munirah',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 1,
            'user_id' => 22,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 1,
            'user_id' => 21,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 1,
            'user_id' => 44,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 1,
            'user_id' => 24,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // Munirah
        DB::table('teams')->insert([
            'name' => 'Nadia',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 2,
            'user_id' => 18,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 2,
            'user_id' => 17,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 2,
            'user_id' => 19,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 2,
            'user_id' => 20,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 2,
            'user_id' => 26,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // Xin Ying
        DB::table('teams')->insert([
            'name' => 'Xin Ying',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 3,
            'user_id' => 9,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 3,
            'user_id' => 7,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 3,
            'user_id' => 12,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);




        // Zam
        DB::table('teams')->insert([
            'name' => 'Zam',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 4,
            'user_id' => 39,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 4,
            'user_id' => 4,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 4,
            'user_id' => 11,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // Zila
        DB::table('teams')->insert([
            'name' => 'Zila',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 5,
            'user_id' => 42,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // Sharmini
        DB::table('teams')->insert([
            'name' => 'Sharmini',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 6,
            'user_id' => 15,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 6,
            'user_id' => 16,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 6,
            'user_id' => 14,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        // Iwana
        DB::table('teams')->insert([
            'name' => 'Iwana',
            'desc' => '',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 7,
            'user_id' => 45,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 7,
            'user_id' => 26,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('team_members')->insert([
            'team_main_id' => 7,
            'user_id' => 19,
            'leader' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        $member = [];

        array_push($member, ['22', [5, 6, 7, 8, 9, 21, 25, 26]]);
        array_push($member, ['21', [5, 7, 25, 6]]);
        array_push($member, ['44', [5, 8, 25, 26, 6]]);
        array_push($member, ['24', [5, 8, 9, 21, 6]]);

        array_push($member, ['18', [1, 5, 6, 10, 11, 12, 13, 20, 25, 28, 30]]);
        array_push($member, ['17', [5, 10, 25, 28, 6]]);
        array_push($member, ['19', [5, 6, 13, 25]]);
        array_push($member, ['20', [1, 11, 20]]);
        array_push($member, ['26', [12, 30]]);


        array_push($member, ['9', [15, 16]]);
        array_push($member, ['7', [15, 16]]);
        array_push($member, ['12', [15, 16]]);


        array_push($member, ['39', [15, 16, 17, 28]]);
        array_push($member, ['5', [15, 16, 17, 28]]);
        array_push($member, ['4', [15, 16, 17, 28]]);
        array_push($member, ['11', [15, 16, 17, 28]]);
        array_push($member, ['42', [15, 16, 17, 28]]);


        array_push($member, ['15', [1, 20]]);
        array_push($member, ['16', [1, 20]]);
        array_push($member, ['14', [1, 20]]);

        array_push($member, ['45', [2, 3, 4, 22, 23]]);
        array_push($member, ['26', [3, 4, 23]]);
        array_push($member, ['19', [2, 22]]);

        for ($i = 0; $i < count($member); $i++) {

            for ($j = 0; $j < count($member[$i][1]); $j++)
            {
                DB::table('member_portfolio')->insert([
                    'user_id' => $member[$i][0],
                    'portfolio_id' => $member[$i][1][$j],
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            
        }

        // array_push(['22',''], $member);
        // array_push(['22',[5,7,25,6]], $member);
        // array_push(['21',[5,7,25,6]], $member);


        // DB::table('team_members')->insert([ 
        //     'user_id' => 7,
        //     'portfolio_id' => 1,
        //     'status' => 1,
        //     'created_at' =>date('Y-m-d H:i:s')
        // ]);


    }
}
