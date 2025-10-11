<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCloudCaseActivityLog extends Model
{
    use HasFactory;

    protected $table = 'legalcloud_case_activity_log';
    protected $primaryKey = 'id';
}
