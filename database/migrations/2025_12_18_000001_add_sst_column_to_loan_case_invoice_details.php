<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSstColumnToLoanCaseInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_case_invoice_details', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_case_invoice_details', 'sst')) {
                $table->decimal('sst', 20, 2)->nullable()->comment('Custom SST amount (if manually set, otherwise NULL to auto-calculate)')->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_case_invoice_details', function (Blueprint $table) {
            if (Schema::hasColumn('loan_case_invoice_details', 'sst')) {
                $table->dropColumn('sst');
            }
        });
    }
}

