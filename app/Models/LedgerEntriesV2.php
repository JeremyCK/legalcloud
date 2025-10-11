<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntriesV2 extends Model
{
    use HasFactory;

    protected $table = 'ledger_entries_v2';
    protected $primaryKey = 'id';
}
