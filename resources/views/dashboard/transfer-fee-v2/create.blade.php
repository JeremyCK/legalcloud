@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')

<div class="container-fluid">
  <div class="fade-in">

    <div class="row">
      <div class="col-sm-12">

        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h4><i class="fas fa-plus-circle"></i> Create New Transfer Fee V2</h4>
                <small class="text-muted">Invoice-based transfer fee creation</small>
              </div>

              <div class="col-6">
                <a class="btn btn-lg btn-secondary float-right" href="/transfer-fee-v2">
                  <i class="cil-arrow-left"> </i>Back to List
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <form id="transferFeeFormV2" method="POST" action="/transfer-fee-v2/store">
              @csrf
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="transfer_date">Transfer Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="transfer_date" name="transfer_date" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="trx_id">Transaction ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="trx_id" name="trx_id" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="transfer_from">Transfer From <span class="text-danger">*</span></label>
                    <select class="form-control" id="transfer_from" name="transfer_from" required>
                      <option value="">Select Source Account</option>
                      @foreach($OfficeBankAccount as $account)
                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->account_no }})</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="transfer_to">Transfer To <span class="text-danger">*</span></label>
                    <select class="form-control" id="transfer_to" name="transfer_to" required>
                      <option value="">Select Destination Account</option>
                      @foreach($OfficeBankAccount as $account)
                        <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->account_no }})</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="purpose">Purpose <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
                  </div>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <h5><i class="fas fa-file-invoice"></i> Select Invoices for Transfer</h5>
                  <p class="text-muted">Choose invoices to include in this transfer batch</p>
                  
                  <div class="form-group">
                    <button type="button" class="btn btn-info" onclick="loadTransferFeeInvoiceListV2()">
                      <i class="fas fa-sync-alt"></i> Load Available Invoices
                    </button>
                  </div>

                  <div id="invoice-list-container">
                    <!-- Invoice list will be loaded here -->
                  </div>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="saveTransferFeeV2()">
                      <i class="fas fa-save"></i> Create Transfer Fee
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='/transfer-fee-v2'">
                      <i class="fas fa-times"></i> Cancel
                    </button>
                  </div>
                </div>
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')

<script>
$(document).ready(function() {
    // Set default date to today
    $('#transfer_date').val(new Date().toISOString().split('T')[0]);
    
    // Load available invoices on page load
    loadTransferFeeInvoiceListV2();
});

function loadTransferFeeInvoiceListV2() {
    $.ajax({
        url: '/transfer-fee-v2/getTransferInvoiceListV2',
        type: 'GET',
        beforeSend: function() {
            $('#invoice-list-container').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading invoices...</div>');
        },
        success: function(response) {
            if (response.status == 1) {
                $('#invoice-list-container').html(response.invoiceList);
            } else {
                $('#invoice-list-container').html('<div class="alert alert-warning">No invoices available for transfer</div>');
            }
        },
        error: function() {
            $('#invoice-list-container').html('<div class="alert alert-danger">Error loading invoices</div>');
        }
    });
}

function saveTransferFeeV2() {
    // Validate form
    if (!$('#transfer_date').val()) {
        Swal.fire('Error', 'Please select transfer date', 'error');
        return;
    }
    
    if (!$('#trx_id').val()) {
        Swal.fire('Error', 'Please enter transaction ID', 'error');
        return;
    }
    
    if (!$('#transfer_from').val()) {
        Swal.fire('Error', 'Please select transfer from account', 'error');
        return;
    }
    
    if (!$('#transfer_to').val()) {
        Swal.fire('Error', 'Please select transfer to account', 'error');
        return;
    }
    
    if (!$('#purpose').val()) {
        Swal.fire('Error', 'Please enter purpose', 'error');
        return;
    }

    // Get selected invoices
    var add_invoice = [];
    $('input[name="add_invoice"]:checked').each(function() {
        var invoiceId = $(this).val();
        var invoiceData = getInvoiceDataV2(invoiceId);
        add_invoice.push(invoiceData);
    });

    if (add_invoice.length === 0) {
        Swal.fire('Error', 'Please select at least one invoice', 'error');
        return;
    }

    var formData = {
        transfer_date: $('#transfer_date').val(),
        trx_id: $('#trx_id').val(),
        transfer_from: $('#transfer_from').val(),
        transfer_to: $('#transfer_to').val(),
        purpose: $('#purpose').val(),
        add_invoice: JSON.stringify(add_invoice)
    };

    $.ajax({
        url: '/transfer-fee-v2/store',
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Creating Transfer Fee...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.status == 1) {
                Swal.fire('Success', response.message, 'success').then(() => {
                    window.location.href = '/transfer-fee-v2';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            var errorMessage = 'Something went wrong';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMessage, 'error');
        }
    });
}

function getInvoiceDataV2(invoiceId) {
    // Get invoice data from the table row
    var row = $('#invoice_' + invoiceId);
    return {
        id: invoiceId,
        bill_id: row.data('bill-id'),
        value: parseFloat(row.data('amount')),
        sst: parseFloat(row.data('sst') || 0),
        case_id: row.data('case-id')
    };
}

function selectAllInvoices() {
    $('input[name="add_invoice"]').prop('checked', true);
    calculateTotalAmountV2();
}

function deselectAllInvoices() {
    $('input[name="add_invoice"]').prop('checked', false);
    calculateTotalAmountV2();
}

function calculateTotalAmountV2() {
    var total = 0;
    $('input[name="add_invoice"]:checked').each(function() {
        var invoiceId = $(this).val();
        var row = $('#invoice_' + invoiceId);
        total += parseFloat(row.data('amount') || 0);
    });
    
    $('#total-amount').text('RM ' + total.toFixed(2));
}
</script>

@endsection
