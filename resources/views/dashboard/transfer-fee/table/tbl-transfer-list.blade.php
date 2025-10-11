@if(count($rows))
<tr>
  <th class="text-center">No</th>
  <th class="text-center" style="width:10%">Client Name</th>
  <th class="text-center" style="width:10%">Ref No</th>
  <th class="text-center" style="width:10%">Invoice No</th>
  <th class="text-center" style="width:10%">Invoice Date</th>
  <th class="text-center">Total amt</th>
  <th class="text-center">Collected amt</th>
  <th class="text-center">Bal to transfer</th>
</tr>
@foreach($rows as $index =>$row)
<tr>
  <td>
    <div class="checkbox  bulk-edit-mode" >
      <input type="checkbox" name="bill" value="{{ $row->id }}" id="chk_{{ $row->id }}" >
      <label for="chk_{{ $row->id }}">{{ $index+1 }}</label>
      </div>
  </td>
  <td>{{ $row->client_name }}</td>
  <td>{{ $row->case_ref_no }}</td>
  <td>{{ $row->invoice_no ?? '' }}</td>
  <td>{{ $row->invoice_date ?? '' }}</td>
  <td class="text-right">{{ number_format($row->total_amt ?? 0, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format($row->collected_amt, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format(($row->pfee1 + $row->pfee2 + $row->sst), 2, '.', ',') }}</td>
@endforeach
@else
<tr>
  <td class="text-center" colspan="7">No data</td>
</tr>
@endif

