<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseDocumentVersion extends Model
{
    use HasFactory;

    protected $table = 'loan_case_document_version';
    protected $primaryKey = 'id';
}
