@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div id="dSummaryReport" class="card">
          <div class="card-header">
            <h4>Summary Report</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row no-print">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-warning  float-right" href="javascript:void(0)" onclick="printSummary();">
                  <i class="fa fa-print"> </i>Print
                </a>
              </div>

            </div>
            <br />
            <div>
              <div class="div2  printableArea  " style="overflow-x: auto; width:100%">

                <span style="font-size:20px;font-weight:bold">Total Report </span>
                <table class="table table-bordered datatable" style="overflow-x: auto; width:100%;font-size:12;">
                  <tr>
                    <!-- <th class="text-center">Invoice No</th> -->
                    <th class="text-center" style="font-size:9px" >Professional Fee</th>
                    <th class="text-center" style="font-size:9px" >Disbursement</th>
                    <th class="text-center" style="font-size:9px" >SST</th>
                    <th class="text-center" style="font-size:9px" >Total Invoice</th>
                    <th class="text-center" style="font-size:9px" >Total Collected Amount</th>
                    <th class="text-center" style="font-size:9px" >Oustanding Amount</th>
                    <th class="text-center" style="font-size:9px" >Referral(A1)</th>
                    <th class="text-center" style="font-size:9px" >Referral(A2)</th>
                    <th class="text-center" style="font-size:9px" >Marketing</th>
                    <th class="text-center" style="font-size:9px" >Uncollected</th>
                  </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <!-- <td class="text-center"  id="sum_invoice_no"> </td> -->
                      <td class="text-right"  style="font-size:9px"  id="sum_pfee"> {{ number_format($total_Pfee, 2, '.', ',') }}</td>
                      <td class="text-right"  style="font-size:9px" id="sum_disb"> {{ number_format($total_disb, 2, '.', ',') }} </td>
                      <td class="text-right"  style="font-size:9px" id="sum_sst"> {{ number_format($total_sst, 2, '.', ',') }} </td>
                      <td class="text-right"  style="font-size:9px" id="sum_total_invoice"> {{ number_format($total_amt, 2, '.', ',') }}</td>
                      <td class="text-right"  style="font-size:9px" id="sum_total_invoice"> {{ number_format($total_collected_amt, 2, '.', ',') }}</td>
                      <td class="text-right"  style="font-size:9px" id="sum_outstanding"> {{ number_format($total_collected_amt - $total_amt, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($total_referral_a1, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($total_referral_a2, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($total_Pfee, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px"  id="sum_uncollected"> {{ number_format($total_amt - $total_collected_amt, 2, '.', ',') }}</td>
                    </tr>
                  </tbody>
                </table>
                <hr />
                <!-- <span style="font-size:20px;font-weight:bold">Report </span> -->
                <table class="table  table-bordered datatable" style="overflow-x: auto; width:100%">
                  <thead>
                    <tr class="text-center">
                      <th style="font-size:9px" >Bill No</th>
                      <!-- <th>Case</th> -->
                      <th style="font-size:9px" >pfee</th>
                      <th style="font-size:9px" >Disb</th>
                      <th style="font-size:9px" >SST</th>
                      <th style="font-size:9px" >Total Amount</th>
                      <th style="font-size:9px" >collected Amount</th>
                      <th style="font-size:9px" >Outstanding</th>
                      <th style="font-size:9px" >Referral 1</th>
                      <th style="font-size:9px" >Referral 2</th>
                      <th style="font-size:9px" >Marketing</th>
                      <th style="font-size:9px" >Uncollected</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(count($quotations))
                    @foreach($quotations as $index => $quotation)
                    <tr>
                      <td class="text-center" style="font-size:9px" >{{ $quotation->bill_no }}</td>
                      <!-- <td class="text-left">{{ $quotation->case_ref_no }}</td> -->
                      <td class="text-right" style="font-size:9px" >{{ number_format($quotation->pfee_recv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px" >{{ number_format($quotation->disb_recv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($quotation->sst_recv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px" >{{ number_format($quotation->total_amt, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px" >{{ number_format($quotation->collected_amt, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px" >{{ $quotation->collected_amt - $quotation->total_amt }}</td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($quotation->referral_a1, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($quotation->referral_a2, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" >{{ number_format($quotation->marketing, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" >{{ $quotation->uncollected }}</td>


                    </tr>

                    @endforeach
                    @else
                    <tr>
                      <td class="text-center" colspan="5">No data</td>
                    </tr>
                    @endif

                  </tbody>
                </table>
              </div>


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')

<link href="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/PrintArea.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
<script src="{{ asset('js/jquery.print.js') }}"></script>
<script>
  function printSummary() {
    // window.print();

    // $("#dVoucherInvoice").print();

    // jQuery.print();

    $("#dSummaryReport").print({
      addGlobalStyles: true,
      stylesheet: true,
      rejectWindow: true,
      noPrintSelector: ".no-print",
      iframe: false,
      append: null,
      prepend: null
    });
  }
</script>

@endsection