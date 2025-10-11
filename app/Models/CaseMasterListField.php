<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseMasterListField extends Model
{
    use HasFactory;

    protected $table = 'case_masterlist_field';
    protected $primaryKey = 'id';
}
