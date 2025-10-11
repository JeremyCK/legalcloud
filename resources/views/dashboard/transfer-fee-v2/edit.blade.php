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
                <h4><i class="fas fa-edit"></i> Edit Transfer Fee V2</h4>
                <small class="text-muted">Modify transfer fee information</small>
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

            @if($TransferFeeMain)
            <form id="editTransferFeeFormV2" method="POST" action="/transfer-fee-v2/{{ $TransferFeeMain->id }}">
              @csrf
              @method('PUT')
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="transfer_date">Transfer Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="transfer_date" name="transfer_date" 
                           value="{{ $TransferFeeMain->transfer_date }}" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="transaction_id">Transaction ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                           value="{{ $TransferFeeMain->transaction_id }}" required>
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
                        <option value="{{ $account->id }}" 
                                {{ $TransferFeeMain->transfer_from == $account->id ? 'selected' : '' }}>
                          {{ $account->name }} ({{ $account->account_no }})
                        </option>
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
                        <option value="{{ $account->id }}" 
                                {{ $TransferFeeMain->transfer_to == $account->id ? 'selected' : '' }}>
                          {{ $account->name }} ({{ $account->account_no }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="purpose">Purpose <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="3" required>{{ $TransferFeeMain->purpose }}</textarea>
                  </div>
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <h5><i class="fas fa-file-invoice"></i> Transfer Details (Read Only)</h5>
                  @php
                    $transferDetails = \App\Models\TransferFeeDetails::where('transfer_fee_main_id', $TransferFeeMain->id)->get();
                  @endphp

                  @if(count($transferDetails) > 0)
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead class="thead-dark">
                        <tr>
                          <th>Invoice No</th>
                          <th>Case Ref</th>
                          <th>Client Name</th>
                          <th>Transfer Amount</th>
                          <th>SST Amount</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($transferDetails as $detail)
                          @php
                            $invoice = \App\Models\LoanCaseInvoiceMain::where('id', $detail->loan_case_invoice_main_id)->first();
                            $bill = $invoice ? \App\Models\LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first() : null;
                            $case = $bill ? \App\Models\LoanCase::where('id', $bill->case_id)->first() : null;
                            $client = $case ? \App\Models\Customer::where('id', $case->customer_id)->first() : null;
                          @endphp
                          <tr>
                            <td>{{ $invoice ? $invoice->invoice_no : 'N/A' }}</td>
                            <td>{{ $case ? $case->case_ref_no : 'N/A' }}</td>
                            <td>{{ $client ? $client->name : 'N/A' }}</td>
                            <td class="text-right">RM {{ number_format($detail->transfer_amount, 2) }}</td>
                            <td class="text-right">RM {{ number_format($detail->sst_amount ?? 0, 2) }}</td>
                            <td class="text-right"><strong>RM {{ number_format(($detail->transfer_amount + ($detail->sst_amount ?? 0)), 2) }}</strong></td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  @else
                  <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No transfer details found for this record.
                  </div>
                  @endif
                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="updateTransferFeeV2()">
                      <i class="fas fa-save"></i> Update Transfer Fee
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='/transfer-fee-v2'">
                      <i class="fas fa-times"></i> Cancel
                    </button>
                  </div>
                </div>
              </div>

            </form>
            @else
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle"></i>
              Transfer fee record not found.
            </div>
            @endif

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
    // Initialize any JavaScript functionality here
});

function updateTransferFeeV2() {
    // Validate form
    if (!$('#transfer_date').val()) {
        Swal.fire('Error', 'Please select transfer date', 'error');
        return;
    }
    
    if (!$('#transaction_id').val()) {
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

    var formData = {
        transfer_date: $('#transfer_date').val(),
        transaction_id: $('#transaction_id').val(),
        transfer_from: $('#transfer_from').val(),
        transfer_to: $('#transfer_to').val(),
        purpose: $('#purpose').val()
    };

    $.ajax({
        url: $('#editTransferFeeFormV2').attr('action'),
        type: 'PUT',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Updating Transfer Fee...',
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
</script>

@endsection
