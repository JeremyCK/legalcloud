<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusRequestHistory extends Model
{
    use HasFactory;

    protected $table = 'bonus_request_history';
    protected $primaryKey = 'id';
}
