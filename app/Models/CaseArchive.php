<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseArchive extends Model
{
    use HasFactory;

    protected $table = 'cases_outside_system';
    protected $primaryKey = 'id';
}
