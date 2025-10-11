<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseAccountFiles extends Model
{
    use HasFactory;

    protected $table = 'loan_case_account_files';
    protected $primaryKey = 'id';
}
