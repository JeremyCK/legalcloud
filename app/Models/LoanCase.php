<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCase extends Model
{
    use HasFactory;

    protected $table = 'loan_case';
    protected $primaryKey = 'id';
}
