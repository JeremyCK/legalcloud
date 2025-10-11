<div class="col-2">
    <b>To:</b>
</div>
<div class="col-10 ">
    <strong id="p-quo-client-name" class="text-blue">{{ $InvoiceBillingParty->customer_name }} </strong>
</div>

@if (isset($InvoiceBillingParty->tin))
    <div class="col-2">
        <b>Tax No:</b>
    </div>
    <div class="col-10 ">
        {{ $InvoiceBillingParty->tin }}
    </div>
@endif

@if (isset($InvoiceBillingParty->address_1))
    <div class="col-2">
        <b>Address:</b>
    </div>
    <div class="col-10 ">
        {!! nl2br(htmlspecialchars($InvoiceBillingParty->address_1)) !!}
    </div>
@endif