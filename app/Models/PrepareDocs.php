<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepareDocs extends Model
{
    use HasFactory;

    protected $table = 'prepare_docs';
    protected $primaryKey = 'id';
}
