<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYears extends Model
{
    use HasFactory;

    protected $table = 'fiscal_years';
    protected $primaryKey = 'id';
}
