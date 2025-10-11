<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCasePncNotes extends Model
{
    use HasFactory;

    protected $table = 'loan_case_pnc_notes';
    protected $primaryKey = 'id';
}
