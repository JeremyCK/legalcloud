<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseEmailSchedule extends Model
{
    use HasFactory;

    protected $table = 'case_email_schedule';
    protected $primaryKey = 'id';
}
