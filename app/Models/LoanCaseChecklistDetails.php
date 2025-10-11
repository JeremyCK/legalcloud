<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseChecklistDetails extends Model
{
    use HasFactory;

    protected $table = 'loan_case_checklist_details';
    protected $primaryKey = 'id';
}
