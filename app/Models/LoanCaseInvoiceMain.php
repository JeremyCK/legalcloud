<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseInvoiceMain extends Model
{
    use HasFactory;

    protected $table = 'loan_case_invoice_main';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_case_main_bill_id',
        'invoice_no',
        'Invoice_date',
        'amount',
        'bill_party_id',
        'transferred_pfee_amt', // NEW - transferred professional fee amount
        'transferred_sst_amt', // NEW - transferred SST amount
        'transferred_to_office_bank', // NEW - transfer status flag
        'pfee1_inv', // NEW - professional fee 1 amount
        'pfee2_inv', // NEW - professional fee 2 amount
        'sst_inv', // NEW - SST amount
        'bln_invoice', // NEW - invoice flag
        'bln_sst', // SST paid status flag
        'reimbursement_amount', // NEW - reimbursement amount
        'reimbursement_sst', // NEW - reimbursement SST amount
        'transferred_reimbursement_amt', // NEW - transferred reimbursement amount
        'transferred_reimbursement_sst_amt', // NEW - transferred reimbursement SST amount
        'created_by',
        'status'
    ];

    /**
     * Get the loan case bill main record.
     */
    public function loanCaseBillMain()
    {
        return $this->belongsTo(LoanCaseBillMain::class, 'loan_case_main_bill_id');
    }

    /**
     * Get the transfer fee details for this invoice.
     */
    public function transferFeeDetails()
    {
        return $this->hasMany(TransferFeeDetails::class, 'loan_case_invoice_main_id');
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate total professional fee amount.
     */
    public function getTotalPfeeAttribute()
    {
        return $this->pfee1_inv + $this->pfee2_inv;
    }

    /**
     * Calculate remaining professional fee amount to transfer.
     */
    public function getRemainingPfeeAttribute()
    {
        $totalPfee = $this->getTotalPfeeAttribute();
        return max(0, $totalPfee - $this->transferred_pfee_amt);
    }

    /**
     * Calculate remaining SST amount to transfer.
     */
    public function getRemainingSstAttribute()
    {
        return max(0, $this->sst_inv - $this->transferred_sst_amt);
    }

    /**
     * Check if invoice is fully transferred.
     */
    public function isFullyTransferred()
    {
        return $this->transferred_to_office_bank == 1;
    }
}
