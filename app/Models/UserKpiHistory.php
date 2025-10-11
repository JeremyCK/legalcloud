<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKpiHistory extends Model
{
    use HasFactory;

    protected $table = 'user_kpi_history';
    protected $primaryKey = 'id';
}
