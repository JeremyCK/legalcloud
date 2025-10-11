<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasesNotes extends Model
{
    use HasFactory;

    protected $table = 'cases_note';
    protected $primaryKey = 'id';
}
