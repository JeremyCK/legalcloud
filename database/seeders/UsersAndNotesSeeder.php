<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\RoleHierarchy;
use App\Http\Helper\Helper;

class UsersAndNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUsers = 10;
        $numberOfLawyer = 5;
        $numberOfSales = 5;
        $numberOfClerk = 5;
        $numberOfNotes = 100;
        $usersIds = array();
        $statusIds = array();
        $faker = Faker::create();
        /* Create roles */
        $adminRole = Role::create(['name' => 'admin']); 
        RoleHierarchy::create([
            'role_id' => $adminRole->id,
            'hierarchy' => 1,
        ]);
        $userRole = Role::create(['name' => 'user']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 2,
        ]); 
        $guestRole = Role::create(['name' => 'guest', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $guestRole->id,
            'hierarchy' => 3,
        ]);
        $managementRole = Role::create(['name' => 'management', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $managementRole->id,
            'hierarchy' => 2,
        ]);
        $accountRole = Role::create(['name' => 'account', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $accountRole->id,
            'hierarchy' => 3,
        ]);
        $salesRole = Role::create(['name' => 'sales', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $salesRole->id,
            'hierarchy' => 4,
        ]);
        $lawyerRole = Role::create(['name' => 'lawyer', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $lawyerRole->id,
            'hierarchy' => 5,
        ]);
        $clerkRole = Role::create(['name' => 'clerk', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $clerkRole->id,
            'hierarchy' => 6,
        ]);
        $runnerRole = Role::create(['name' => 'runner', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $runnerRole->id,
            'hierarchy' => 7,
        ]);
        $receptionistRole = Role::create(['name' => 'receptionist', 'status' => '1']); 
        RoleHierarchy::create([
            'role_id' => $receptionistRole->id,
            'hierarchy' => 8,
        ]);
        /*  insert status  */
        DB::table('status')->insert([
            'name' => 'ongoing',
            'class' => 'badge badge-pill badge-primary',
        ]);
        array_push($statusIds, DB::getPdo()->lastInsertId());
        DB::table('status')->insert([
            'name' => 'stopped',
            'class' => 'badge badge-pill badge-secondary',
        ]);
        array_push($statusIds, DB::getPdo()->lastInsertId());
        DB::table('status')->insert([
            'name' => 'completed',
            'class' => 'badge badge-pill badge-success',
        ]);
        array_push($statusIds, DB::getPdo()->lastInsertId());
        DB::table('status')->insert([
            'name' => 'expired',
            'class' => 'badge badge-pill badge-warning',
        ]);
        array_push($statusIds, DB::getPdo()->lastInsertId());
        /*  insert users   */
        /*
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'menuroles' => 'user,admin'
        ]);
        for($i = 0; $i<$numberOfUsers; $i++){
            DB::table('users')->insert([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'menuroles' => 'user'
            ]);
            array_push($usersIds, DB::getPdo()->lastInsertId());
        }
        */
        $user = User::create([ 
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'A0',
            'menuroles' => 'admin' 
        ]);
        $user->assignRole('admin');
        // $user->assignRole('user');

        // assign internal staff
        $user = User::create([ 
            'name' => 'L H YEO',
            'email' => 'lhyeo@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'LHY',
            'menuroles' => 'management',
            'portfolio' => '',
            'phone_no' => '017-364 5019',
            'min_files' => 0,
            'max_files' => 0 ,
            'status' => 1 
        ]);
        $user->assignRole('management');


        $user = User::create([ 
            'name' => 'STANLEY',
            'email' => 'stanley@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'STN',
            'menuroles' => 'management',
            'portfolio' => '',
            'phone_no' => '012-364 5019',
            'min_files' => 0,
            'max_files' => 0 ,
            'status' => 1 
        ]);
        $user->assignRole('management');



        $user = User::create([ 
            'name' => 'AZLINA',
            'email' => 'azlina@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'AA',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA,LPPSA',
            'phone_no' => '013-237 7118',
            'min_files' => 15,
            'max_files' => 30 ,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'RAJES',
            'email' => 'rajes@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'RR',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA',
            'phone_no' => '013-885 1152',
            'min_files' => 15,
            'max_files' => 30 ,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'BIBI',
            'email' => 'bibi@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'BB',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA',
            'phone_no' => '013-885 1152',
            'min_files' => 10,
            'max_files' => 15,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'IRA',
            'email' => 'ira@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'IRA',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA,HLB',
            'phone_no' => '010-261 6118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'SALLY',
            'email' => 'sally@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'SY',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA',
            'phone_no' => '016-251 4118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'XIN YIN',
            'email' => 'xinying@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'XY',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '019-260 7118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');

        $user = User::create([ 
            'name' => 'BRENDA',
            'email' => 'brenda@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'BF',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '016-242 7118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');


        $user = User::create([ 
            'name' => 'PATHMA',
            'email' => 'pathma@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'PM',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA',
            'phone_no' => '013-289 7118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'SARAH',
            'email' => 'sarah@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'SR',
            'menuroles' => 'clerk',
            'portfolio' => 'SPA',
            'phone_no' => '016-533 0818',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'ANGEL',
            'email' => 'angel@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'AG',
            'menuroles' => 'clerk',
            'portfolio' => 'PBB',
            'phone_no' => '016-259 7118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'MOON',
            'email' => 'mn@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'M',
            'menuroles' => 'clerk',
            'portfolio' => 'PBB',
            'phone_no' => '016-270 0118',
            'min_files' => 20,
            'max_files' => 35,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'SHARMINI',
            'email' => 'sharmini@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'SS',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '018-233 8118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');


        $user = User::create([ 
            'name' => 'SITI',
            'email' => 'siti@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'M',
            'menuroles' => 'clerk',
            'portfolio' => 'PBB',
            'phone_no' => '017-788 1152',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'HEMA',
            'email' => 'hema@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'HL',
            'menuroles' => 'clerk',
            'portfolio' => 'CIMB,UOB',
            'phone_no' => '017-788 1152',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'NADIA',
            'email' => 'nadia@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'NS',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '013-301 7118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');


        $user = User::create([ 
            'name' => 'ZAILA',
            'email' => 'zaila@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'ZAI',
            'menuroles' => 'clerk',
            'portfolio' => 'MBB',
            'phone_no' => '018-229 8118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'ZARLIDA',
            'email' => 'zarlida@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'ZS',
            'menuroles' => 'clerk',
            'portfolio' => 'PBB',
            'phone_no' => '016-533 3818',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'EGA',
            'email' => 'ega@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'EGA',
            'menuroles' => 'clerk',
            'portfolio' => 'CIMB',
            'phone_no' => '018-226 8118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');


        $user = User::create([ 
            'name' => 'MUNIRAH',
            'email' => 'munirah@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'MM',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '016-607 3118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');


        $user = User::create([ 
            'name' => 'RAFIDAH',
            'email' => 'rafidah@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'RF',
            'menuroles' => 'clerk',
            'portfolio' => 'CIMB,SCB',
            'phone_no' => '016-268 0118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'SUHANA',
            'email' => 'suhana@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'SU',
            'menuroles' => 'clerk',
            'portfolio' => 'CIMB,HLB',
            'phone_no' => '016-270 4118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'ZANA',
            'email' => 'zana@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'R',
            'menuroles' => 'clerk',
            'portfolio' => 'CIMB',
            'phone_no' => '010-263 3118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'SUBRA',
            'email' => 'subra@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'SB',
            'menuroles' => 'clerk',
            'portfolio' => 'RHB,AMB,BSN',
            'phone_no' => '016-617 7118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');

        $user = User::create([ 
            'name' => 'VASANTHA',
            'email' => 'vasantha@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'V',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '018-366 8118',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');

        $user = User::create([ 
            'name' => 'WATI',
            'email' => 'wati@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'WT',
            'menuroles' => 'clerk',
            'portfolio' => 'CIMB,CIMB(SME)',
            'phone_no' => '016-822 7818',
            'min_files' => 15,
            'max_files' => 30,
            'status' => 1 
        ]);
        $user->assignRole('clerk');



        $user = User::create([ 
            'name' => 'KEITH',
            'email' => 'keith@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'K',
            'menuroles' => 'sales',
            'portfolio' => '',
            'phone_no' => '017-768 2352',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('sales');

        $user = User::create([ 
            'name' => 'MAX',
            'email' => 'max@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'MX',
            'menuroles' => 'sales',
            'portfolio' => '',
            'phone_no' => '017-833 3532',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('sales');

        $user = User::create([ 
            'name' => 'STEPH',
            'email' => 'steph@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'SH',
            'menuroles' => 'sales',
            'portfolio' => '',
            'phone_no' => '016-281 8183',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('sales');

        $user = User::create([ 
            'name' => 'TAN',
            'email' => 'tan@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'T',
            'menuroles' => 'sales',
            'portfolio' => '',
            'phone_no' => '019-217 6118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('sales');

        $user = User::create([ 
            'name' => 'XU HUAN',
            'email' => 'xuhuan@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'XH',
            'menuroles' => 'sales',
            'portfolio' => '',
            'phone_no' => '019-530 3488',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('sales');

        $user = User::create([ 
            'name' => 'ELLE',
            'email' => 'elle@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'EL',
            'menuroles' => 'account',
            'portfolio' => '',
            'phone_no' => '017-710 9788',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('account');

        $user = User::create([ 
            'name' => 'HESTER',
            'email' => 'hester@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'HT',
            'menuroles' => 'account',
            'portfolio' => '',
            'phone_no' => '017-903 8065',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('account');

        $user = User::create([ 
            'name' => 'JESS',
            'email' => 'jess@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'HT',
            'menuroles' => 'account',
            'portfolio' => '',
            'phone_no' => '012-679 6629',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('account');

        $user = User::create([ 
            'name' => 'LYANA',
            'email' => 'lyana@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'LY',
            'menuroles' => 'account',
            'portfolio' => '',
            'phone_no' => '012-269 7224',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('account');

        $user = User::create([ 
            'name' => 'PRIYA',
            'email' => 'priya@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'PY',
            'menuroles' => 'lawyer',
            'portfolio' => 'LITIGATION',
            'phone_no' => '016-954 0115',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');

        $user = User::create([ 
            'name' => 'ZAM',
            'email' => 'zam@lhyeo.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'ZM',
            'menuroles' => 'lawyer',
            'portfolio' => '',
            'phone_no' => '016-607 3118',
            'min_files' => 0,
            'max_files' => 0,
            'status' => 1 
        ]);
        $user->assignRole('lawyer');

        // assign internal staff (end)


        $user = User::create([ 
            'name' => 'Lawyer 1',
            'email' => 'lawyer1@legalcloud.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'L1',
            'menuroles' => 'lawyer' 
        ]);
        $user->assignRole('lawyer');
        // $user->assignRole('user');

        $user = User::create([ 
            'name' => 'Sales 1',
            'email' => 'sales1@legalcloud.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'S1',
            'menuroles' => 'user,sales' 
        ]);
        $user->assignRole('sales');
        $user->assignRole('user');


        $user = User::create([ 
            'name' => 'reception 1',
            'email' => 'recep@legalcloud.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'nick_name' => 'S1',
            'menuroles' => 'receptionist' 
        ]);
        $user->assignRole('receptionist');

        // for($i = 0; $i<$numberOfLawyer; $i++){
        //     $name = $faker->name();
        //     $nickName  = Helper::generateNickName($name);
            
        //     $user = User::create([ 
        //         'name' => $name,
        //         'email' => $faker->unique()->safeEmail(),
        //         'email_verified_at' => now(),
        //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //         'remember_token' => Str::random(10),
        //         'nick_name' => $nickName,
        //         'menuroles' => 'user,lawyer'
        //     ]);
        //     $user->assignRole('user');
        //     $user->assignRole('lawyer');
        //     array_push($usersIds, $user->id);
        // }

        // for($i = 0; $i<$numberOfSales; $i++){
        //     $name = $faker->name();
        //     $nickName  = Helper::generateNickName($name);
            
        //     $user = User::create([ 
        //         'name' => $name,
        //         'email' => $faker->unique()->safeEmail(),
        //         'email_verified_at' => now(),
        //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //         'remember_token' => Str::random(10),
        //         'nick_name' => $nickName,
        //         'menuroles' => 'user,sales'
        //     ]);
        //     $user->assignRole('user');
        //     $user->assignRole('sales');
        //     array_push($usersIds, $user->id);
        // }

        // for($i = 0; $i<$numberOfClerk; $i++){
        //     $name = $faker->name();
        //     $nickName  = Helper::generateNickName($name);
            
        //     $user = User::create([ 
        //         'name' => $name,
        //         'email' => $faker->unique()->safeEmail(),
        //         'email_verified_at' => now(),
        //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //         'remember_token' => Str::random(10),
        //         'nick_name' => $nickName,
        //         'menuroles' => 'user,clerk'
        //     ]);
        //     $user->assignRole('user');
        //     $user->assignRole('clerk');
        //     array_push($usersIds, $user->id);
        // }

        // /*  insert notes  */
        // for($i = 0; $i<$numberOfNotes; $i++){
        //     $noteType = $faker->word();
        //     if(random_int(0,1)){
        //         $noteType .= ' ' . $faker->word();
        //     }
        //     DB::table('notes')->insert([
        //         'title'         => $faker->sentence(4,true),
        //         'content'       => $faker->paragraph(3,true),
        //         'status_id'     => $statusIds[random_int(0,count($statusIds) - 1)],
        //         'note_type'     => $noteType,
        //         'applies_to_date' => $faker->date(),
        //         'users_id'      => $usersIds[random_int(0,$numberOfUsers-1)]
        //     ]);
        // }
    }
}