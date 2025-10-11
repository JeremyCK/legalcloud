<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseDocumentPage extends Model
{
    use HasFactory;

    protected $table = 'loan_case_document_page';
    protected $primaryKey = 'id';
}
