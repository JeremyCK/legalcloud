<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFeeDetails extends Model
{
    use HasFactory;

    protected $table = 'transfer_fee_details';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transfer_fee_main_id',
        'loan_case_main_bill_id',
        'loan_case_invoice_main_id', // Primary link to loan_case_invoice_main table
        'transfer_amount',
        'sst_amount',
        'reimbursement_amount',
        'reimbursement_sst_amount',
        'created_by',
        'status'
    ];

    /**
     * Get the transfer fee main record that owns this detail.
     */
    public function transferFeeMain()
    {
        return $this->belongsTo(TransferFeeMain::class, 'transfer_fee_main_id');
    }

    /**
     * Get the loan case bill main record.
     */
    public function loanCaseBillMain()
    {
        return $this->belongsTo(LoanCaseBillMain::class, 'loan_case_main_bill_id');
    }

    /**
     * Get the loan case invoice main record.
     */
    public function loanCaseInvoiceMain()
    {
        return $this->belongsTo(LoanCaseInvoiceMain::class, 'loan_case_invoice_main_id');
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
