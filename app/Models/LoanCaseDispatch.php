<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseDispatch extends Model
{
    use HasFactory;

    protected $table = 'loan_case_dispatch';
    protected $primaryKey = 'id';
}
