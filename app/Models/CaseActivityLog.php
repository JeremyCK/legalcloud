<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseActivityLog extends Model
{
    use HasFactory;

    protected $table = 'case_activity_log';
    protected $primaryKey = 'id';
}
