<?php

namespace Database\Seeders;

use App\Http\Controllers\MyTodoController;
use Illuminate\Database\Seeder;
//use database\seeds\UsersAndNotesSeeder;
//use database\seeds\MenusTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(MenusTableSeeder::class);
        //$this->call(UsersAndNotesSeeder::class);
        /*
        $this->call('UsersAndNotesSeeder');
        $this->call('MenusTableSeeder');
        $this->call('FolderTableSeeder');
        $this->call('ExampleSeeder');
        $this->call('BREADSeeder');
        $this->call('EmailSeeder');
        */
        $this->call([
            UsersAndNotesSeeder::class,
            MenusTableSeeder::class,
            FolderTableSeeder::class,
            ExampleSeeder::class,
            BREADSeeder::class,
            EmailSeeder::class,
            BankSeeder::class,
            PortfolioSeeder::class,
            ParameterSeeder::class,
            CasesAndTemplateSeeder::class,
            CasesAndTemplateSeeder2::class,
            // CasesAndTemplateSeeder3::class,
            MasterListSeeder::class,
            EmailTemplateSeeder::class,
            AuditLogSeeder::class,
            AccountSeeder::class,
            QuotationSeeder::class,
            AccountItemSeeder::class,
            TeamSeeder::class,
            CaseTemplateCategoriesSeeder::class,
            CaseTemplateMainSeeder::class,
            CaseTemplateStepSeeder::class,
            CaseTemplateItemsSeeder::class,
            CaseTemplateMainStepRelSeeder::class,
            LoanCaseSeeder::class,
            // LoanCaseSeeder2::class,
            // LoanCaseSeeder3::class,
            TransactionSeeder::class,
            MyTodoSeeder::class,
            CourierSeeder::class,
            ReferralSeeder::class,
            DocumentTemplateFileSeeder::class,
        ]);
    }
}
