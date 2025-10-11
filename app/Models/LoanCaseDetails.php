<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseDetails extends Model
{
    use HasFactory;

    protected $table = 'loan_case_checklist';
    protected $primaryKey = 'id';
}
