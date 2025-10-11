<div class="col-2">
    <b>To:</b>
</div>
<div class="col-10 ">
    <strong id="p-quo-client-name" class="text-blue">{{ $LoanCaseBillMain->bill_to }} </strong>
</div>

@if (isset($LoanCaseBillMain->invoice_to_tax_no))
    <div class="col-2">
        <b>Tax No:</b>
    </div>
    <div class="col-10 ">
        {{ $LoanCaseBillMain->invoice_to_tax_no }}
    </div>
@endif

@if (isset($LoanCaseBillMain->invoice_to_address))
    <div class="col-2">
        <b>Address:</b>
    </div>
    <div class="col-10 ">
        {!! nl2br(htmlspecialchars($LoanCaseBillMain->invoice_to_address)) !!}
    </div>
@endif