<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EInvoiceMain extends Model
{
    use HasFactory;

    protected $table = 'einvoice_main';
    protected $primaryKey = 'id';
}
