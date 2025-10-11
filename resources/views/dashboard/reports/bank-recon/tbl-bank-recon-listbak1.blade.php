@if(count($bank_recon))
@foreach($bank_recon as $index => $recon)
<tr>
  <td class="text-left">{{ date('d-m-Y', strtotime($recon->created_at)); }}</td>
  <td class="text-left">
  @if($recon->voucher_type == 3 || $recon->voucher_type == 2)
  Trust
  @else
  Bill
  @endif
  </td>
  <td class="text-left">{{$recon->transaction_id}}</td>
  <td class="text-left">{{$recon->remark}} <a href="/voucher/{{$recon->id}}/edit" target="_blank">[{{$recon->voucher_no}}]</a>  </td>
  <td class="text-right">{{ number_format($recon->detail_amt, 2, '.', ',') }}</td>
</tr>

@endforeach
@else
<tr>
  <td class="text-center" colspan="11">No data</td>
</tr>
@endif