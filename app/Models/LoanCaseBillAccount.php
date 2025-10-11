<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseBillAccount extends Model
{
    use HasFactory;

    protected $table = 'loan_case_bill_account';
    protected $primaryKey = 'id';
}
