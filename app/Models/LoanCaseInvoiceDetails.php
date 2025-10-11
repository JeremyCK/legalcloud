<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCaseInvoiceDetails extends Model
{
    use HasFactory;

    protected $table = 'loan_case_invoice_details';
    protected $primaryKey = 'id';
}
