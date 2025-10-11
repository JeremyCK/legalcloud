<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateDetails extends Model
{
    use HasFactory;

    protected $table = 'email_template_details';
    protected $primaryKey = 'id';
}
