<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferFeeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Add columns to loan_case_invoice_main table
        Schema::table('loan_case_invoice_main', function (Blueprint $table) {
            // Add transferred amount tracking columns
            $table->decimal('transferred_pfee_amt', 20, 2)->default(0.00)->comment('Transferred professional fee amount');
            $table->decimal('transferred_sst_amt', 20, 2)->default(0.00)->comment('Transferred SST amount');
            
            // Add transfer status column
            $table->tinyInteger('transferred_to_office_bank')->default(0)->comment('Transfer status flag (0=not transferred, 1=transferred)');
            
            // Add fee amount columns
            $table->decimal('pfee1_inv', 20, 2)->default(0.00)->comment('Professional fee 1 amount');
            $table->decimal('pfee2_inv', 20, 2)->default(0.00)->comment('Professional fee 2 amount');
            $table->decimal('sst_inv', 20, 2)->default(0.00)->comment('SST amount');
            
            // Add invoice flag
            $table->tinyInteger('bln_invoice')->default(0)->comment('Invoice flag (0=not invoice, 1=is invoice)');
            
            // Add indexes for performance
            $table->index('transferred_to_office_bank', 'idx_transferred_status');
            $table->index('bln_invoice', 'idx_bln_invoice');
            $table->index(['transferred_pfee_amt', 'transferred_sst_amt'], 'idx_transfer_amounts');
        });

        // Step 2: Add column to transfer_fee_details table
        Schema::table('transfer_fee_details', function (Blueprint $table) {
            // Add new column to track invoice-based transfers
            $table->unsignedBigInteger('loan_case_invoice_main_id')->nullable()->comment('Reference to loan_case_invoice_main table');
            
            // Add index for performance
            $table->index('loan_case_invoice_main_id', 'idx_invoice_main_id');
            
            // Add foreign key constraint (optional - for data integrity)
            // $table->foreign('loan_case_invoice_main_id')->references('id')->on('loan_case_invoice_main')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Step 2: Remove column from transfer_fee_details table
        Schema::table('transfer_fee_details', function (Blueprint $table) {
            $table->dropIndex('idx_invoice_main_id');
            $table->dropColumn('loan_case_invoice_main_id');
        });

        // Step 1: Remove columns from loan_case_invoice_main table
        Schema::table('loan_case_invoice_main', function (Blueprint $table) {
            $table->dropIndex('idx_transfer_amounts');
            $table->dropIndex('idx_bln_invoice');
            $table->dropIndex('idx_transferred_status');
            
            $table->dropColumn([
                'transferred_pfee_amt',
                'transferred_sst_amt',
                'transferred_to_office_bank',
                'pfee1_inv',
                'pfee2_inv',
                'sst_inv',
                'bln_invoice'
            ]);
        });
    }
} 