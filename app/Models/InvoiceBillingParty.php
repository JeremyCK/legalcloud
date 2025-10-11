<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceBillingParty extends Model
{
    protected $table = 'invoice_billing_party';
    public $timestamps = false;
    protected $fillable = [
        'customer_name', 'brn', 'brn2', 'customer_category', 'id_type', 'id_no', 'tin',
        'address_1', 'address_2', 'address_3', 'address_4', 'postcode', 'city', 'state', 'country',
        'phone1', 'mobile', 'fax1', 'fax2', 'email', 'customer_code', 'sales_tax_no', 'service_tax_no'
    ];
}
