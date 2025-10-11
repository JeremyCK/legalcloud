{{-- <table id="tbl-summary-report" class="table  table-bordered datatable" style="overflow-x: auto; width:100%"> --}}
  
  <table id="tbl-summary-report" class="table  table-bordered datatable">

  <tbody>
    

    <tr rowspan="2">
      <td colspan="10">
        Invoices
      </td>
    </tr>
 
    <tr class="text-center" style="background-color: black;color:white">
      <th>No</th>
      <th >Invoice No</th>
      <th>SST</th>
      <th>Case</th>
      <th>pfee1</th>
      <th>pfee2</th>
      <th>Disb</th>
      <th>SST</th>
      <th>Total Amount</th>
      <th>Collected Amount</th>
      <th>Pfee to transfer</th>
      <th>SST to transfer</th>
      <th>Transferred Bal</th>
      <th>Transferred SST</th>
      <th>Received Date</th>
    </tr>
    @if(count($quotations))
    @foreach($quotations as $index => $quotation)
    <tr>
      <td class="text-left">
        <div class="checkbox">
          <input type="checkbox" name="invoice" value="{{ $quotation->id }}" id="chk_{{ $quotation->id }}" @if($quotation->bln_sst == 1) disabled @endif>
          <label for="chk_{{ $quotation->id }}">{{$index+1}}</label>
        </div>

      </td>
      <td class="text-left">{{ $quotation->invoice_no }}</td>
      <td class="text-center">
        @if($quotation->bln_sst== 1) 
        Paid
        @else
        -
        @endif
      </td>
      <td><a target="_blank" class="text-info" href="/case/{{ $quotation->case_id }}">{{ $quotation->case_ref_no }}</a></td> 
      <td class="text-right">{{ number_format($quotation->pfee1_inv, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->pfee2_inv, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->disb_inv, 2, '.', ',') }}</td>
      <td class="text-right"> {{ number_format($quotation->sst_inv, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->total_amt_inv, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->collected_amt, 2, '.', ',') }}</td>
      <td class="text-right">
        @php
            $bal_to_transfer = 0;
            $pf1 = number_format((float)$quotation->pfee1_inv, 2, '.', '');
            $pf2 = number_format((float)$quotation->pfee2_inv, 2, '.', '');
            $pftf = number_format((float)$quotation->transfer_amount, 2, '.', '');

            $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

            if ($bal_to_transfer < 0)
            {
                $bal_to_transfer = 0.00;
            }
        @endphp
        {{ number_format($bal_to_transfer, 2, '.', ',') }}
      </td>
      <td class="text-right">{{ number_format($quotation->collected_amt, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->collected_amt, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->collected_amt, 2, '.', ',') }}</td>
      <td class="text-right">{!! $quotation->paydate !!}</td>

    </tr>

    @endforeach
    @else
    <tr>
                      <td class="text-center" colspan="11">No data</td>
    </tr>
    @endif

  </tbody>
</table>