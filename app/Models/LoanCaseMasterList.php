<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseMasterList extends Model
{
    use HasFactory;

    protected $table = 'loan_case_masterlist';
    protected $primaryKey = 'id';
}
