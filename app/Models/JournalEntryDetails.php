<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryDetails extends Model
{
    use HasFactory;

    protected $table = 'journal_entry_details';
    protected $primaryKey = 'id';
}
