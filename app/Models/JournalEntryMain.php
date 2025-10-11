<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryMain extends Model
{
    use HasFactory;

    protected $table = 'journal_entry_main';
    protected $primaryKey = 'id';
}
