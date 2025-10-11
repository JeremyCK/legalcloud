<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeedercs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /* team zam  */
        DB::table('team_main')->insert([ 
            'name' => 'Team Zam',
            'desc' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 1,
            'user_id' => 4,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 1,
            'user_id' => 5,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 1,
            'user_id' => 6,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 1,
            'user_id' => 38,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        /* team xin yin  */
        DB::table('team_main')->insert([ 
            'name' => 'Team Xin Yin',
            'desc' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 2,
            'user_id' => 7,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' =>2,
            'user_id' => 8,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 2,
            'user_id' => 9,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        /* team breanda  */
        DB::table('team_main')->insert([ 
            'name' => 'Team Brenda',
            'desc' => '',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 3,
            'user_id' => 11,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' =>3,
            'user_id' => 12,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('team_member')->insert([ 
            'team_main_id' => 3,
            'user_id' => 10,
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);


        // team handle banks

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 1,
            'group_id' => 1,
            'type' => 'CASE',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 1,
            'group_id' => 1,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 2,
            'group_id' => 1,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 1,
            'group_id' => 2,
            'type' => 'CASE',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 2,
            'group_id' => 2,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 3,
            'group_id' => 2,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 1,
            'group_id' => 3,
            'type' => 'CASE',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 1,
            'group_id' => 3,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 2,
            'group_id' => 3,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);

        DB::table('group_portfolio')->insert([ 
            'portfolio_id' => 3,
            'group_id' => 3,
            'type' => 'BANK',
            'status' => 1,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        

    }
}
