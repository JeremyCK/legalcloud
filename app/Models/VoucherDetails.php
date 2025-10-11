<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherDetails extends Model
{
    use HasFactory;

    protected $table = 'voucher_details';
    protected $primaryKey = 'id';
}
