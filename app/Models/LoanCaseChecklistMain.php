<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseChecklistMain extends Model
{
    use HasFactory;

    protected $table = 'loan_case_checklist_main';
    protected $primaryKey = 'id';
}
