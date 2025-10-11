<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseFiles extends Model
{
    use HasFactory;

    protected $table = 'loan_case_files';
    protected $primaryKey = 'id';
}
