<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandleGroup extends Model
{
    use HasFactory;

    protected $table = 'handle_group';
    protected $primaryKey = 'id';
}
