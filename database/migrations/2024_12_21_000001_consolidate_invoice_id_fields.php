<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConsolidateInvoiceIdFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Copy data from invoice_main_id to loan_case_invoice_main_id where the latter is null
        DB::statement("
            UPDATE transfer_fee_details 
            SET loan_case_invoice_main_id = invoice_main_id 
            WHERE loan_case_invoice_main_id IS NULL 
            AND invoice_main_id IS NOT NULL
        ");

        // Step 2: Verify the data migration was successful
        $nullCount = DB::table('transfer_fee_details')
            ->whereNull('loan_case_invoice_main_id')
            ->whereNotNull('invoice_main_id')
            ->count();

        if ($nullCount > 0) {
            throw new Exception("Data migration failed: {$nullCount} records still have null loan_case_invoice_main_id");
        }

        // Step 3: Remove the redundant invoice_main_id column
        Schema::table('transfer_fee_details', function (Blueprint $table) {
            $table->dropColumn('invoice_main_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Step 1: Add back the invoice_main_id column
        Schema::table('transfer_fee_details', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_main_id')->nullable()->after('loan_case_invoice_main_id');
        });

        // Step 2: Copy data back from loan_case_invoice_main_id to invoice_main_id
        DB::statement("
            UPDATE transfer_fee_details 
            SET invoice_main_id = loan_case_invoice_main_id 
            WHERE loan_case_invoice_main_id IS NOT NULL
        ");
    }
}

