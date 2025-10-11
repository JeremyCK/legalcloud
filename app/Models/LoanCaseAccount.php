<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseAccount extends Model
{
    use HasFactory;

    protected $table = 'loan_case_account';
    protected $primaryKey = 'id';
}
