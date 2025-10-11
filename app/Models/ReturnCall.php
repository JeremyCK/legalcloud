<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnCall extends Model
{
    use HasFactory;

    protected $table = 'return_call';
    protected $primaryKey = 'id';
}
