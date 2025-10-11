<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_case_invoice_main', function (Blueprint $table) {
            $table->decimal('reimbursement_amount', 20, 2)->default(0.00)->comment('Reimbursement amount (calculated from loan_case_invoice_details where account_cat_id = 4)');
            $table->decimal('reimbursement_sst', 20, 2)->default(0.00)->comment('Reimbursement SST amount (calculated from loan_case_invoice_details where account_cat_id = 4)');
            $table->decimal('transferred_reimbursement_amt', 20, 2)->default(0.00)->comment('Total transferred reimbursement amount');
            $table->decimal('transferred_reimbursement_sst_amt', 20, 2)->default(0.00)->comment('Total transferred reimbursement SST amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_case_invoice_main', function (Blueprint $table) {
            $table->dropColumn([
                'reimbursement_amount',
                'reimbursement_sst', 
                'transferred_reimbursement_amt',
                'transferred_reimbursement_sst_amt'
            ]);
        });
    }
};
