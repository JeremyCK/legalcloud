<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFeeMain extends Model
{
    use HasFactory;

    protected $table = 'transfer_fee_main';
    protected $primaryKey = 'id';
}
