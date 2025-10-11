<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusRequestRecords extends Model
{
    use HasFactory;

    protected $table = 'bonus_request_records';
    protected $primaryKey = 'id';
}
