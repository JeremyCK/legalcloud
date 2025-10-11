<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoanCaseInvoiceMainIdToTransferFeeDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_fee_details', function (Blueprint $table) {
            // Add new column for invoice-based transfers
            $table->unsignedBigInteger('loan_case_invoice_main_id')->nullable()->after('loan_case_main_bill_id')->comment('Reference to loan_case_invoice_main table for V2');
            
            // Add index for performance
            $table->index('loan_case_invoice_main_id', 'idx_invoice_main_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_fee_details', function (Blueprint $table) {
            // Remove index first
            $table->dropIndex('idx_invoice_main_id');
            
            // Remove column
            $table->dropColumn('loan_case_invoice_main_id');
        });
    }
}
