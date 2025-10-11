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
                <h4><i class="fas fa-eye"></i> Transfer Fee V2 Details</h4>
                <small class="text-muted">View transfer fee information</small>
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
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Transaction ID:</strong></label>
                  <p>{{ $TransferFeeMain->transaction_id }}</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Transfer Date:</strong></label>
                  <p>{{ \Carbon\Carbon::parse($TransferFeeMain->transfer_date)->format('d/m/Y') }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Transfer From:</strong></label>
                  @php
                    $transferFrom = \App\Models\OfficeBankAccount::where('id', $TransferFeeMain->transfer_from)->first();
                  @endphp
                  <p>{{ $transferFrom ? $transferFrom->name . ' (' . $transferFrom->account_no . ')' : 'N/A' }}</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Transfer To:</strong></label>
                  @php
                    $transferTo = \App\Models\OfficeBankAccount::where('id', $TransferFeeMain->transfer_to)->first();
                  @endphp
                  <p>{{ $transferTo ? $transferTo->name . ' (' . $transferTo->account_no . ')' : 'N/A' }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Transfer Amount:</strong></label>
                  <p><strong>RM {{ number_format($TransferFeeMain->transfer_amount, 2) }}</strong></p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Purpose:</strong></label>
                  <p>{{ $TransferFeeMain->purpose }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Created By:</strong></label>
                  @php
                    $createdBy = \App\Models\User::where('id', $TransferFeeMain->transfer_by)->first();
                  @endphp
                  <p>{{ $createdBy ? $createdBy->name : 'N/A' }}</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Created Date:</strong></label>
                  <p>{{ \Carbon\Carbon::parse($TransferFeeMain->created_at)->format('d/m/Y H:i:s') }}</p>
                </div>
              </div>
            </div>

            <hr>

            <div class="row">
              <div class="col-md-12">
                <h5><i class="fas fa-file-invoice"></i> Transfer Details</h5>
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

// Enhanced action functions
function editTransferFeeV2(id) {
    window.location.href = '/transfer-fee-v2/' + id + '/edit';
}

function deleteTransferFeeV2(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/transfer-fee-v2/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            window.location.href = '/transfer-fee-v2';
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong', 'error');
                }
            });
        }
    });
}
</script>

@endsection
