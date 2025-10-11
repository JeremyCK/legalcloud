<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixEinvoiceDetailsInvoiceNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix existing incorrect invoice_no records in einvoice_details
        DB::statement("
            UPDATE einvoice_details ed
            INNER JOIN loan_case_invoice_main im ON ed.loan_case_invoice_id = im.id
            SET ed.invoice_no = im.invoice_no
            WHERE ed.invoice_no != im.invoice_no OR ed.invoice_no IS NULL
        ");

        // Add a comment to the column for future reference
        DB::statement("
            ALTER TABLE einvoice_details 
            MODIFY COLUMN invoice_no VARCHAR(255) 
            COMMENT 'DEPRECATED: Use loan_case_invoice_main.invoice_no via join instead'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the comment
        DB::statement("
            ALTER TABLE einvoice_details 
            MODIFY COLUMN invoice_no VARCHAR(255)
        ");
    }
}

