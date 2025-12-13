<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseBillMain extends Model
{
    use HasFactory;

    protected $table = 'loan_case_bill_main';
    protected $primaryKey = 'id';
    protected $fillable = ['referral_a1','referral_a1_id','referral_a1_payment_date','referral_a1_ref_id','referral_a1_trx_id',
                            'referral_a2','referral_a2_id','referral_a2_payment_date','referral_a2_ref_id','referral_a2_trx_id',
                            'referral_a3','referral_a3_id','referral_a3_payment_date','referral_a3_ref_id','referral_a3_trx_id',
                            'referral_a4','referral_a4_id','referral_a4_payment_date','referral_a4_ref_id','referral_a4_trx_id',
                            'marketing','marketing_id','marketing_payment_date','marketing_trx_id',
                            'other_amt','other_name','other_payment_date','other_trx_id',
                            'disb_amt_manual','disb_name','disb_payment_date','disb_trx_id',
                            'financed_fee','financed_sum','payment_date',
                            'uncollected','collection_amount',
];

    /**
     * Get the loan case record.
     */
    public function loanCase()
    {
        return $this->belongsTo(LoanCase::class, 'case_id');
    }
}
