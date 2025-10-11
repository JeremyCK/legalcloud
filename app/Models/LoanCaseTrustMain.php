<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseTrustMain extends Model
{
    use HasFactory;

    protected $table = 'loan_case_trust_main';
    protected $primaryKey = 'id';
}
