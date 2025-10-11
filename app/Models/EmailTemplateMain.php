<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateMain extends Model
{
    use HasFactory;

    protected $table = 'email_template_main';
    protected $primaryKey = 'id';
}
