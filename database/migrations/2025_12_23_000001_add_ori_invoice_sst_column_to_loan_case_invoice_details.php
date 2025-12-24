<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriInvoiceSstColumnToLoanCaseInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_case_invoice_details', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_case_invoice_details', 'ori_invoice_sst')) {
                $table->decimal('ori_invoice_sst', 20, 2)->nullable()->comment('Original invoice SST total across all split invoices for this account_item_id')->after('ori_invoice_amt');
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
            if (Schema::hasColumn('loan_case_invoice_details', 'ori_invoice_sst')) {
                $table->dropColumn('ori_invoice_sst');
            }
        });
    }
}



