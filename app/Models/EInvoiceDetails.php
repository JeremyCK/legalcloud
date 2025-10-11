<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EInvoiceDetails extends Model
{
    use HasFactory;

    protected $table = 'einvoice_details';
    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'einvoice_main_id',
        'loan_case_invoice_id',
        'loan_case_main_bill_id',
        'case_id',
        'amt',
        'einvoice_status',
        'client_profile_completed',
        'status',
        'voucher_no',
        'created_by',
        'is_recon'
        // Note: invoice_no is intentionally excluded as it should be retrieved via join
    ];

    /**
     * Get the loan case invoice main record.
     */
    public function loanCaseInvoiceMain()
    {
        return $this->belongsTo(LoanCaseInvoiceMain::class, 'loan_case_invoice_id');
    }

    /**
     * Get the loan case bill main record.
     */
    public function loanCaseBillMain()
    {
        return $this->belongsTo(LoanCaseBillMain::class, 'loan_case_main_bill_id');
    }

    /**
     * Get the e-invoice main record.
     */
    public function einvoiceMain()
    {
        return $this->belongsTo(EInvoiceMain::class, 'einvoice_main_id');
    }

    /**
     * Get the correct invoice number from the related loan_case_invoice_main.
     * This replaces the deprecated invoice_no field.
     */
    public function getCorrectInvoiceNoAttribute()
    {
        return $this->loanCaseInvoiceMain?->invoice_no;
    }
}
