<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlnSstColumnToLoanCaseInvoiceMain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_case_invoice_main', function (Blueprint $table) {
            $table->tinyInteger('bln_sst')->default(0)->comment('SST transfer flag: 0=not transferred, 1=transferred')->after('transferred_sst_amt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_case_invoice_main', function (Blueprint $table) {
            $table->dropColumn('bln_sst');
        });
    }
}
