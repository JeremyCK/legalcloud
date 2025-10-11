<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimsType extends Model
{
    use HasFactory;

    protected $table = 'claims_type';
    protected $primaryKey = 'id';
}
