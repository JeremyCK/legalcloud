<table id="tbl-summary-report" class="table  table-bordered datatable" style="overflow-x: auto; width:100%">

  <tbody>


    <tr rowspan="2">
      <td colspan="10">
        Invoices
      </td>
    </tr>

    <tr class="text-center" style="background-color: black;color:white">
      <th>No</th>
      <th>Bill No</th>
      <th>Case</th>
      <th>pfee1</th>
      <th>pfee2</th>
      <th>Disb</th>
      <th>SST</th>
      <th>Total Amount</th>
      <th>Collected Amount</th>
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
      <td class="text-left">{{ $quotation->bill_no }}</td>
      <td><a target="_blank" class="text-info" href="/case/{{ $quotation->case_id }}">{{ $quotation->case_ref_no }}</a></td>
      <td class="text-left">
      <b>Name:</b> @if($quotation->referral_a1_id <> '0') {{$quotation->referral_a1_id }} @else - @endif<br/>
        <b>Payment date:</b>@if($quotation->referral_a1_payment_date <> null) {{$quotation->referral_a1_payment_date }} @else - @endif
      </td>
      <td class="text-right">{{ number_format($quotation->referral_a1, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->disb_inv, 2, '.', ',') }}</td>
      <td class="text-right"> {{ number_format($quotation->sst_inv, 2, '.', ',') }}</td>
      <td class="text-right">{{ number_format($quotation->total_amt_inv, 2, '.', ',') }}</td>
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