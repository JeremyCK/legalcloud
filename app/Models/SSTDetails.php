<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SSTDetails extends Model
{
    use HasFactory;

    protected $table = 'sst_details';
    protected $primaryKey = 'id';
}
