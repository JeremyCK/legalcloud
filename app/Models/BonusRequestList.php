<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusRequestList extends Model
{
    use HasFactory;

    protected $table = 'bonus_request_list';
    protected $primaryKey = 'id';
}
