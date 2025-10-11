@extends('dashboard.base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">
                                <i class="fa fa-eye"></i> View Transfer Fee Details
                            </h4>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($TransferFeeMain->is_recon != '1')
                                <a href="{{ route('transferfee.edit', $TransferFeeMain->id) }}" class="btn btn-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-warning" onclick="reconTransferFee()">
                                    <i class="fa fa-check-circle"></i> Bank Recon
                                </button>
                            @endif
                            <a href="{{ route('transferfee.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Transfer Fee Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Transfer Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Transfer Date:</strong></td>
                                    <td>{{ $TransferFeeMain->transfer_date ? date('d-m-Y', strtotime($TransferFeeMain->transfer_date)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transaction ID:</strong></td>
                                    <td>{{ $TransferFeeMain->trx_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transfer From:</strong></td>
                                    <td>{{ $TransferFeeMain->transfer_from_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transfer To:</strong></td>
                                    <td>{{ $TransferFeeMain->transfer_to_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong>RM {{ number_format($TransferFeeMain->transfer_amount ?? 0, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Additional Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Purpose:</strong></td>
                                    <td>{{ $TransferFeeMain->purpose ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($TransferFeeMain->is_recon == '1')
                                            <span class="badge badge-success">Reconciled</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $TransferFeeMain->created_by_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date:</strong></td>
                                    <td>{{ $TransferFeeMain->created_at ? date('d-m-Y H:i:s', strtotime($TransferFeeMain->created_at)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $TransferFeeMain->updated_at ? date('d-m-Y H:i:s', strtotime($TransferFeeMain->updated_at)) : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Selected Invoices Summary -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong>{{ $TransferFeeDetails->count() }} invoices selected</strong>
                                <br>
                                <strong>Total Amount: RM {{ number_format($TransferFeeDetails->sum(function($detail) { return ($detail->transfer_amount ?? 0) + ($detail->sst_amount ?? 0) + ($detail->reimbursement_amount ?? 0) + ($detail->reimbursement_sst_amount ?? 0); }), 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Invoices Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Selected Invoices</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="30">No</th>
                                                    <th width="120">Ref No</th>
                                                    <th width="100">Invoice No</th>
                                                    <th width="90">Invoice Date</th>
                                                    <th width="80">Total amt</th>
                                                    <th width="80">Collected amt</th>
                                                    <th width="70">pfee</th>
                                                    <th width="60">sst</th>
                                                    <th width="80">Pfee transferred</th>
                                                    <th width="70">SST transferred</th>
                                                    <th width="80">Transferred Bal</th>
                                                    <th width="80">Transferred SST</th>
                                                    <th width="90">Payment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($TransferFeeDetails as $index => $detail)
                                                <tr>
                                                    <td class="text-center" style="font-size: 11px;">{{ $index + 1 }}</td>
                                                    <td style="font-size: 11px;">
                                                        <a href="/case/{{ $detail->case_id ?? '' }}" target="_blank" class="text-primary" style="text-decoration: none;">
                                                            {{ $detail->case_ref_no ?? 'N/A' }}
                                                        </a>
                                                    </td>
                                                    <td style="font-size: 11px;">{{ $detail->invoice_no ?? $detail->bill_invoice_no ?? 'N/A' }}</td>
                                                    <td style="font-size: 11px;">{{ $detail->invoice_date ?? $detail->bill_invoice_date ?? 'N/A' }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format((($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) + ($detail->sst_inv ?? 0)), 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format((($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) + ($detail->sst_inv ?? 0)), 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format(($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0), 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format($detail->sst_inv ?? 0, 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format($detail->transfer_amount ?? 0, 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format($detail->sst_amount ?? 0, 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format($detail->transferred_pfee_amt ?? 0, 2) }}</td>
                                                    <td class="text-right" style="font-size: 11px;">{{ number_format($detail->transferred_sst_amt ?? 0, 2) }}</td>
                                                    <td style="font-size: 11px;">{{ $detail->payment_receipt_date ?? 'N/A' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-dark">
                                                <tr>
                                                    <th colspan="4" class="text-right"><strong>TOTAL:</strong></th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum(function($detail) { return ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) + ($detail->sst_inv ?? 0); }), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum(function($detail) { return ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) + ($detail->sst_inv ?? 0); }), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum(function($detail) { return ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0); }), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum('sst_inv'), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum('transfer_amount'), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum('sst_amount'), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum('transferred_pfee_amt'), 2) }}</th>
                                                    <th class="text-right">{{ number_format($TransferFeeDetails->sum('transferred_sst_amt'), 2) }}</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Reconciliation function
function reconTransferFee() {
    Swal.fire({
        title: 'Confirm Reconciliation',
        text: 'Are you sure you want to reconcile this transfer fee? This action cannot be undone and will prevent further modifications.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reconcile',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Reconciling Transfer Fee...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Call reconciliation endpoint
            $.ajax({
                url: '{{ route("transferfee.reconcile", $TransferFeeMain->id) }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Refresh the page to show reconciled status
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Reconciliation error:', error);
                    let errorMessage = 'An error occurred while reconciling the transfer fee';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        }
    });
}
</script>
@endsection
