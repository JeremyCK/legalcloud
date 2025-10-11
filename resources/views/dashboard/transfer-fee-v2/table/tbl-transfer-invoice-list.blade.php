@if(count($rows) > 0)
<div class="table-responsive">
    <div class="row mb-3">
        <div class="col-md-6">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllInvoices()">
                <i class="fas fa-check-square"></i> Select All
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllInvoices()">
                <i class="fas fa-square"></i> Deselect All
            </button>
        </div>
        <div class="col-md-6 text-right">
            <strong>Total Selected Amount: <span id="total-amount">RM 0.00</span></strong>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th width="5%">
                    <input type="checkbox" id="select-all" onchange="toggleAllInvoices(this)">
                </th>
                <th width="10%">Invoice No</th>
                <th width="10%">Case Ref</th>
                <th width="15%">Client Name</th>
                <th width="15%">Billing Party</th>
                <th width="10%">Invoice Date</th>
                <th width="10%">Amount</th>
                <th width="10%">SST</th>
                <th width="10%">Total</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr id="invoice_{{ $row->id }}" 
                data-bill-id="{{ $row->loan_case_main_bill_id }}"
                data-amount="{{ $row->amount }}"
                data-sst="{{ $row->sst_inv }}"
                data-case-id="{{ $row->case_id ?? '' }}">
                <td>
                    <input type="checkbox" name="add_invoice" value="{{ $row->id }}" 
                           onchange="calculateTotalAmountV2()" class="invoice-checkbox">
                </td>
                <td>
                    <strong>{{ $row->invoice_no }}</strong>
                    @if($row->bill_invoice_no && $row->bill_invoice_no != $row->invoice_no)
                        <br><small class="text-muted">Bill: {{ $row->bill_invoice_no }}</small>
                    @endif
                </td>
                <td>{{ $row->case_ref_no }}</td>
                <td>{{ $row->client_name }}</td>
                <td>
                    {{ $row->billing_party_name }}
                    @if($row->customer_code)
                        <br><small class="text-muted">Code: {{ $row->customer_code }}</small>
                    @endif
                </td>
                <td>
                    @if($row->Invoice_date)
                        {{ \Carbon\Carbon::parse($row->Invoice_date)->format('d/m/Y') }}
                    @elseif($row->bill_invoice_date)
                        {{ \Carbon\Carbon::parse($row->bill_invoice_date)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    RM {{ number_format($row->amount, 2) }}
                </td>
                <td class="text-right">
                    @if($row->sst_inv > 0)
                        RM {{ number_format($row->sst_inv, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    <strong>RM {{ number_format($row->amount + $row->sst_inv, 2) }}</strong>
                </td>
                <td>
                    @if($row->transferred_to_office_bank == 1)
                        <span class="badge badge-success">Transferred</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Total Invoices:</strong> {{ count($rows) }} | 
                <strong>Selected:</strong> <span id="selected-count">0</span>
            </div>
        </div>
        <div class="col-md-6 text-right">
            <div class="alert alert-success">
                <strong>Total Transfer Amount: <span id="total-amount-display">RM 0.00</span></strong>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAllInvoices(checkbox) {
    if (checkbox.checked) {
        selectAllInvoices();
    } else {
        deselectAllInvoices();
    }
}

function selectAllInvoices() {
    $('input[name="add_invoice"]').prop('checked', true);
    $('#select-all').prop('checked', true);
    calculateTotalAmountV2();
}

function deselectAllInvoices() {
    $('input[name="add_invoice"]').prop('checked', false);
    $('#select-all').prop('checked', false);
    calculateTotalAmountV2();
}

function calculateTotalAmountV2() {
    var total = 0;
    var selectedCount = 0;
    
    $('input[name="add_invoice"]:checked').each(function() {
        var invoiceId = $(this).val();
        var row = $('#invoice_' + invoiceId);
        var amount = parseFloat(row.data('amount') || 0);
        var sst = parseFloat(row.data('sst') || 0);
        total += amount + sst;
        selectedCount++;
    });
    
    $('#total-amount').text('RM ' + total.toFixed(2));
    $('#total-amount-display').text('RM ' + total.toFixed(2));
    $('#selected-count').text(selectedCount);
}

// Initialize on page load
$(document).ready(function() {
    calculateTotalAmountV2();
});
</script>

@else
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>No invoices available for transfer.</strong>
    <br>
    All invoices may have been transferred or there are no pending invoices in your branch.
</div>
@endif
