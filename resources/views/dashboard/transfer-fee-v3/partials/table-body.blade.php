@forelse($TransferFeeMain as $index => $transfer)
<tr>
  <td>{{ $TransferFeeMain->firstItem() + $index }}</td>
  <td>
    <strong>{{ $transfer->transaction_id ?? 'N/A' }}</strong>
  </td>
  <td>{{ $transfer->purpose ?? 'N/A' }}</td>
  <td class="text-right">
    <strong>RM {{ number_format($transfer->transfer_amount ?? 0, 2) }}</strong>
  </td>
  <td>
    <strong>{{ $transfer->transfer_from_bank ?? 'N/A' }}</strong><br/>
    <small class="text-muted">({{ $transfer->transfer_from_bank_acc_no ?? 'N/A' }})</small>
  </td>
  <td>
    <strong>{{ $transfer->transfer_to_bank ?? 'N/A' }}</strong><br/>
    <small class="text-muted">({{ $transfer->transfer_to_bank_acc_no ?? 'N/A' }})</small>
  </td>
  <td>{{ $transfer->transfer_date ? date('d/m/Y', strtotime($transfer->transfer_date)) : 'N/A' }}</td>
  <td>{{ $transfer->branch_name ?? 'N/A' }}</td>
  <td>{{ $transfer->created_by_name ?? 'N/A' }}</td>
  <td>
    @if($transfer->is_recon == '1')
      <span class="badge badge-success">Yes</span>
    @else
      <span class="badge badge-warning">No</span>
    @endif
  </td>
  <td>
    <div class="btn-group" role="group">
      <a href="{{ route('transferfee.edit', $transfer->id) }}" class="btn btn-warning btn-sm" title="Edit">
        <i class="fa fa-edit"></i>
      </a>
      @php
        $current_user = auth()->user();
        $allowedRoles = ['admin', 'account', 'maker'];
        $canDelete = in_array($current_user->menuroles, $allowedRoles) && $transfer->is_recon != '1';
      @endphp
      @if($canDelete)
        <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deleteTransferFee({{ $transfer->id }})">
          <i class="fa fa-trash"></i>
        </button>
      @else
        <button type="button" class="btn btn-secondary btn-sm" title="Delete not allowed" disabled>
          <i class="fa fa-trash"></i>
        </button>
      @endif
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="11" class="text-center">
    <div class="alert alert-warning mb-0">
      <i class="fa fa-exclamation-triangle"></i>
      No transfer fee records found.
    </div>
  </td>
</tr>
@endforelse
