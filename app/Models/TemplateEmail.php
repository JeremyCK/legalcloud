<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateEmail extends Model
{
    use HasFactory;

    protected $table = 'template_email';
    protected $primaryKey = 'id';
}
