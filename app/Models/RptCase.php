<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptCase extends Model
{
    use HasFactory;

    protected $table = 'rpt_case';
    protected $primaryKey = 'id';
}
