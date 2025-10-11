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
                <h4><i class="fas fa-exchange-alt"></i> Transfer Fee V2 - Invoice Based (Simple View)</h4>
                <small class="text-muted">Enhanced transfer fee management with invoice-based tracking</small>
              </div>

              <div class="col-6">
                <a class="btn btn-lg btn-primary float-right" href="/transfer-fee-v2/create">
                  <i class="cil-plus"> </i>Create New Transfer Fee V2
                </a>
              </div>
            </div>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div class="row">
              <div class="col-sm-12">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i>
                  <strong>Simple View:</strong> This page displays transfer fee records directly without AJAX loading.
                  <br>
                  <small>Total Records: {{ count($TransferFeeMain) }}</small>
                </div>
              </div>
            </div>

            <div class="box-body no-padding" style="width:100%;overflow-x:auto">
              
              @if(count($TransferFeeMain) > 0)
                <table class="table table-bordered table-striped" style="width:100%">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Transaction ID</th>
                      <th>Purpose</th>
                      <th>Transfer AMT</th>
                      <th>Transfer From</th>
                      <th>Transfer To</th>
                      <th>Transfer Date</th>
                      <th>Branch</th>
                      <th>Created By</th>
                      <th>Recon</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($TransferFeeMain as $index => $item)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>{{ $item->transaction_id ?? 'N/A' }}</td>
                      <td>{{ $item->purpose ?? 'N/A' }}</td>
                      <td class="text-right">RM {{ number_format($item->transfer_amount ?? 0, 2) }}</td>
                      <td>
                        <strong>{{ $item->transfer_from_bank ?? 'N/A' }}</strong><br/>
                        <small class="text-muted">({{ $item->transfer_from_bank_acc_no ?? 'N/A' }})</small>
                      </td>
                      <td>
                        <strong>{{ $item->transfer_to_bank ?? 'N/A' }}</strong><br/>
                        <small class="text-muted">({{ $item->transfer_to_bank_acc_no ?? 'N/A' }})</small>
                      </td>
                      <td>{{ $item->transfer_date ? \Carbon\Carbon::parse($item->transfer_date)->format('d/m/Y') : 'N/A' }}</td>
                      <td>{{ $item->branch_name ?? 'N/A' }}</td>
                      <td>{{ $item->created_by_name ?? 'N/A' }}</td>
                      <td>
                        @if($item->is_recon == '1')
                          <span class="label bg-success">Yes</span>
                        @else
                          <span class="label bg-warning">No</span>
                        @endif
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="/transfer-fee-v2/{{ $item->id }}" class="btn btn-info btn-sm" title="View Details">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="/transfer-fee-v2/{{ $item->id }}/edit" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="/transfer-fee-v2/{{ $item->id }}/download" class="btn btn-success btn-sm" title="Download" target="_blank">
                            <i class="fas fa-download"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              @else
                <div class="alert alert-warning text-center">
                  <i class="fas fa-exclamation-triangle"></i>
                  <strong>No transfer fee records found.</strong>
                  <br>
                  <small>There are no transfer fee records available for your access level.</small>
                </div>
              @endif

            </div>
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
    // Simple page - no AJAX needed
    console.log('Transfer Fee V2 Simple View loaded successfully');
});

// Enhanced action functions
function viewTransferFeeV2(id) {
    window.location.href = '/transfer-fee-v2/' + id;
}

function editTransferFeeV2(id) {
    window.location.href = '/transfer-fee-v2/' + id + '/edit';
}

function downloadTransferFeeV2(id) {
    window.open('/transfer-fee-v2/' + id + '/download', '_blank');
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
                            window.location.reload(); // Simple reload
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
