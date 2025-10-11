<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherMain extends Model
{
    use HasFactory;

    protected $table = 'voucher_main';
    protected $primaryKey = 'id';
}
