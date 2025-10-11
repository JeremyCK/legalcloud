<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseEmailSentLog extends Model
{
    use HasFactory;

    protected $table = 'case_email_sent_log';
    protected $primaryKey = 'id';
}
