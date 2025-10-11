<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusEstimate extends Model
{
    use HasFactory;

    protected $table = 'bonus_estimate';
    protected $primaryKey = 'id';
}
