<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFeeDetailsDelete extends Model
{
    use HasFactory;

    protected $table = 'transfer_fee_details_delete';
    protected $primaryKey = 'id';
}
