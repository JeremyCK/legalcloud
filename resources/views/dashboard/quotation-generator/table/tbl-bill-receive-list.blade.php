@if(count($bill_receive))
@foreach($bill_receive as $index =>$disb)
<tr>
  <td>

  <div class="checkbox">
      <input type="checkbox"  name="receive_list" value="{{ $disb->amount }}" id="receive_list{{ $disb->id }}" >
      <label for="receive_list{{ $disb->id }}">{{ $index + 1 }}</label>
    </div>
  </td>
  <!-- <td><a target="_blank" href="/voucher/{{ $disb->voucher_id }}/edit">{{ $disb->voucher_no }}</a> </td> -->
  <td>{{ $disb->payee }}</td>
  <td>{{ $disb->remark }}</td>
  <!-- <td>{{ $disb->account_name }}</td> -->
  <td class="text-right">{{ $disb->amount }}</td>
  <td class="text-center">{{ $disb->created_at }}</td>

  

  <td class="text-center">
    <a href="javascript:void(0)" onclick="generateBillReceipt('{{ $disb->voucher_id }}');" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-print"></i></a>
    <a href="javascript:void(0)" onclick="billReceiveEditMode('{{ $disb->voucher_id }}');" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

  </td>
</tr>
@endforeach
@else
<tr>
  <td class="text-center" colspan="7">No data</td>
</tr>
@endif

