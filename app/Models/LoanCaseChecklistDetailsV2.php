<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseChecklistDetailsV2 extends Model
{
    use HasFactory;

    protected $table = 'loan_case_checklist_details_v2';
    protected $primaryKey = 'id';
}
