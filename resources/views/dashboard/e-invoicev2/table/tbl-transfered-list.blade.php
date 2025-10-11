@if(count($rows))
@php
  $total_amount = 0;
  $total_collected_amount = 0;
  $total_pfee = 0;
  $total_sst = 0;
  $total_pfee_to_transfer = 0;
  $total_sst_to_transfer = 0;
  $total_transferred_pfee = 0;
  $total_transferred_sst = 0;
@endphp
<thead>
  <tr>
      <th><b>No</b></th>
      <th><b>Ref No</b></th>
      <th><b>Invoice No</b></th>
      <th><b>Invoice Date</b></th>
      <th><b>Total amt</b></th>
      <th><b>Collected amt</b></th>
      <th><b>pfee</b></th>
      <th><b>sst</b></th>
      <th><b>Pfee to transfer</b></th>
      <th><b>SST to transfer</b></th>
      <th><b>Transferred Bal</b></th>
      <th><b>Transferred SST</b></th>
      <th><b>Payment Date</b></th>
  </tr>
</thead>
@foreach($rows as $index =>$row)
<tr>
  <td>
    {{ $index+1 }}
  </td>
  <td>{{ $row->client_name }}<br/>{{ $row->case_ref_no }}</td>
  <td>{{ $row->invoice_no }}</td>
  <td>{{ date('d-m-Y', strtotime($row->invoice_date)); }}</td>
  <td class="text-right">{{ number_format($row->total_amt_inv, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format($row->collected_amt, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format(($row->pfee1_inv + $row->pfee2_inv), 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format($row->sst_inv, 2, '.', ',') }}</td>
  @php
     $pf1 = number_format((float)$row->pfee1_inv, 2, '.', '');
      $pf2 = number_format((float)$row->pfee2_inv, 2, '.', '');
      $pftf = number_format((float)$row->transfer_amount, 2, '.', '');

     


      // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt); 

      $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

      if ($bal_to_transfer < 0) {
          $bal_to_transfer = 0.00;
      }

      $sst_to_transfer = $row->sst_inv  - $row->transferred_sst_amt;

      $total_amount += $row->total_amt_inv;
      $total_collected_amount += $row->collected_amt;
      $total_pfee += ($row->pfee1_inv + $row->pfee2_inv);
      $total_sst += $row->sst_inv;

      $total_pfee_to_transfer += $bal_to_transfer;
      $total_sst_to_transfer += $sst_to_transfer;
      $total_transferred_pfee += $row->transfer_amount;
      $total_transferred_sst += $row->sst_amount;
  @endphp
  <td class="text-right">{{ number_format($bal_to_transfer, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format($sst_to_transfer, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format($row->transfer_amount, 2, '.', ',') }}</td>
  <td class="text-right">{{ number_format($row->sst_amount, 2, '.', ',') }}</td>
  <td class="text-right">{{ $row->payment_receipt_date }}</td>
@endforeach
<tfoot style="background-color: #d8dbe0;color:black">
  <th colspan="4" class="text-left">Total </th>
  <th  class="text-right">{{ number_format($total_amount, 2, '.', ',') }}</th>
  <th  class="text-right">{{ number_format($total_collected_amount, 2, '.', ',') }} </th>
  <th  class="text-right">{{ number_format($total_pfee, 2, '.', ',') }} </th>
  <th  class="text-right">{{ number_format($total_sst, 2, '.', ',') }}</th>
  <th  class="text-right">{{ number_format($total_pfee_to_transfer, 2, '.', ',') }}</th>
  <th  class="text-right">{{ number_format($total_sst_to_transfer, 2, '.', ',') }}</th>
  <th  class="text-right">{{ number_format($total_transferred_pfee, 2, '.', ',') }}</th>
  <th  class="text-right">{{ number_format($total_transferred_sst, 2, '.', ',') }}</th>
  <th class="text-left"> </th>
</tfoot>
@else
<tr>
  <td class="text-center" colspan="7">No data</td>
</tr>
@endif

