@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div id="dSummaryReport" class="card">
          <div class="card-header">
            <h4>Invoice Report</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row no-print">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)" onclick="exportTableToExcel();">
                  <i class="fa fa-file-excel-o"> </i>Download as Excel
                </a>
              </div>

            </div>
            <br />
            <div>
              <div id="tbl_all" class="div2  printableArea  " style="overflow-x: auto; width:100%">

                <!-- <span style="font-size:20px;font-weight:bold">Total Report </span> -->
                <!-- <table class="table table-bordered datatable" style="overflow-x: auto; width:100%;font-size:12;">
                  <tr>
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
                </table> -->

                <!-- <span style="font-size:20px;font-weight:bold">Report </span> -->
                <table id="tbl-summary-report" class="table  table-bordered datatable" style="overflow-x: auto; width:100%">
                  <!-- <thead>
                    <tr class="text-center">
                      <th style="font-size:9px" >Bill No</th>
                      <th style="font-size:9px" >Case</th>
                      <th style="font-size:9px" >pfee</th>
                      <th style="font-size:9px" >Disb</th>
                      <th style="font-size:9px" >SST</th>
                      <th style="font-size:9px" >Total Amount</th>
                      <th style="font-size:9px" >Collected Amount</th>
                      <th style="font-size:9px" >Outstanding</th>
                      <th style="font-size:9px" >Referral 1</th>
                      <th style="font-size:9px" >Referral 2</th>
                      <th style="font-size:9px" >Marketing</th>
                      <th style="font-size:9px" >Uncollected</th>
                    </tr>
                  </thead> -->
                  <tbody>
                    <tr rowspan="2">
                      <td colspan="10">
                        Total Invoice summary
                      </td>
                    </tr>
                    <tr class="text-center" style="background-color: black;color:white">
                      <!-- <th class="text-center">Invoice No</th> -->
                      <!-- <th class="text-center" colspan="2"  style="font-size:9px" >Professional Fee</th> -->
                      <th class="text-center" style="font-size:9px">Pfee1</th>
                      <th class="text-center" style="font-size:9px">Pfee2</th>
                      <th class="text-center" style="font-size:9px">Disbursement</th>
                      <th class="text-center" style="font-size:9px">SST</th>
                      <th class="text-center" style="font-size:9px">Total Invoice</th>
                      <th class="text-center" colspan="2" style="font-size:9px">Total Collected Amount</th>
                      <!-- <th class="text-center" style="font-size:9px" >Oustanding Amount</th> -->
                      <th class="text-center" style="font-size:9px">Referral(A1)</th>
                      <th class="text-center" style="font-size:9px">Referral(A2)</th>
                      <th class="text-center" style="font-size:9px">Referral(A3)</th>
                      <th class="text-center" style="font-size:9px">Referral(A4)</th>
                      <th class="text-center" style="font-size:9px">Marketing</th>
                      <th colspan="2" class="text-center" style="font-size:9px">Uncollected</th>
                    </tr>
                    <tr>
                      <!-- <td class="text-center"  id="sum_invoice_no"> </td> -->
                      <!-- <td class="text-right" colspan="2"  style="font-size:9px"  id="sum_pfee"> {{ number_format($total_Pfee, 2, '.', ',') }}</td> -->
                      <td class="text-right" style="font-size:9px" id="sum_disb"> {{ number_format($total_Pfee1, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" id="sum_disb"> {{ number_format($total_Pfee2, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" id="sum_disb"> {{ number_format($total_disb, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" id="sum_sst"> {{ number_format($total_sst, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" id="sum_total_invoice"> {{ number_format($total_amt, 2, '.', ',') }}</td>
                      <td class="text-right" colspan="2" style="font-size:9px" id="sum_total_invoice"> {{ number_format($total_collected_amt, 2, '.', ',') }}</td>
                      <!-- <td class="text-right"  style="font-size:9px;display:npn" id="sum_outstanding"> {{ number_format($total_collected_amt - $total_amt, 2, '.', ',') }} </td> -->
                      <td class="text-right" style="font-size:9px"> {{ number_format($total_referral_a1, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px"> {{ number_format($total_referral_a2, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px"> {{ number_format($total_referral_a3, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px"> {{ number_format($total_referral_a4, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px"> {{ number_format($total_Pfee, 2, '.', ',') }}</td>
                      <td colspan="2" class="text-right" style="font-size:9px" id="sum_uncollected"> {{ number_format($total_uncollected, 2, '.', ',') }}</td>
                    </tr>
                    <tr rowspan="2">
                      <td colspan="10">
                      </td>
                    </tr>

                    <tr rowspan="2">
                      <td colspan="10">
                        Invoices
                      </td>
                    </tr>

                    <tr class="text-center" style="background-color: black;color:white">
                      <th style="font-size:9px">No</th>
                      <th style="font-size:9px">Invoice No</th>
                      <th style="font-size:9px">SST</th>
                      <th style="font-size:9px">Case</th>
                      <th style="font-size:9px">pfee1</th>
                      <th style="font-size:9px">pfee2</th>
                      <th style="font-size:9px">Disb</th>
                      <th style="font-size:9px">SST</th>
                      <th style="font-size:9px">Total Amount</th>
                      <th style="font-size:9px">Collected Amount</th>
                      <!-- <th style="font-size:9px" >Outstanding</th> -->
                      <!-- <th style="font-size:9px" >Referral (A1)</th>
                      <th style="font-size:9px" >Referral (A2)</th>
                      <th style="font-size:9px" >Referral (A3)</th>
                      <th style="font-size:9px" >Referral (A4)</th>
                      <th style="font-size:9px" >Marketing</th>
                      <th style="font-size:9px" >Uncollected</th> -->
                      <th style="font-size:9px">Received Date</th>
                    </tr>
                    @if(count($quotations))
                    @foreach($quotations as $index => $quotation)
                    <tr>
                      <td class="text-left" style="font-size:9px">
                        <div class="checkbox">
                          <input type="checkbox" name="member" value="{{ $quotation->id }}" id="chk_{{ $quotation->id }}" >
                          <label for="chk_{{ $quotation->id }}">{{$index+1}}</label>
                        </div>
                        
                      </td>
                      <td class="text-left" style="font-size:9px">{{ $quotation->invoice_no }}</td>
                      <td class="text-left" style="font-size:9px">
                        @if($quotation->bln_sst== 1)
                        Paid
                        @else
                        -
                        @endif
                      </td>
                      <td style="font-size:9px"><a target="_blank" class="text-info" href="/case/{{ $quotation->case_id }}">{{ $quotation->case_ref_no }}</a></td>
                      <td class="text-right" style="font-size:9px">{{ number_format($quotation->pfee1_inv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px">{{ number_format($quotation->pfee2_inv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px">{{ number_format($quotation->disb_inv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px"> {{ number_format($quotation->sst_inv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px">{{ number_format($quotation->total_amt_inv, 2, '.', ',') }}</td>
                      <td class="text-right" style="font-size:9px">{{ number_format($quotation->collected_amt, 2, '.', ',') }}</td>
                      <!-- <td class="text-right" style="font-size:9px" >{{  number_format(($quotation->collected_amt - $quotation->total_amt), 2, '.', ',') }}</td> -->
                      <!-- <td class="text-right" style="font-size:9px" > {{ number_format($quotation->referral_a1, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($quotation->referral_a2, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($quotation->referral_a3, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" > {{ number_format($quotation->referral_a4, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" >{{ number_format($quotation->marketing, 2, '.', ',') }} </td>
                      <td class="text-right" style="font-size:9px" >{{number_format($quotation->uncollected, 2, '.', ',')  }}</td> -->
                      <td class="text-right" style="width:100px;font-size:9px">{!! $quotation->paydate !!}</td>
                      <!-- <td class="text-right" style="font-size:9px" >
                      {{number_format($quotation->pfee1_recv + $quotation->pfee2_recv - $quotation->referral_a1 - $quotation->referral_a2- $quotation->referral_a3- $quotation->referral_a4- $quotation->marketing -  $quotation->uncollected, 2, '.', ',')  }}
                    </td> -->


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

  function exportData() {
    /* Get the HTML data using Element by Id */
    var table = document.getElementById("tbl-summary-report");

    /* Declaring array variable */
    var rows = [];

    //iterate through rows of table
    for (var i = 0, row; row = table.rows[i]; i++) {
      //rows would be accessed using the "row" variable assigned in the for loop
      //Get each cell value/column from the row
      column1 = row.cells[0].innerText;
      column2 = row.cells[1].innerText;
      column3 = row.cells[2].innerText;
      column4 = row.cells[3].innerText;
      column5 = row.cells[4].innerText;

      /* add a new records in the array */
      rows.push(
        [
          column1,
          column2,
          column3,
          column4,
          column5
        ]
      );

    }
    csvContent = "data:text/xls;charset=utf-8,";
    /* add the column delimiter as comma(,) and each row splitted by new line character (\n) */
    rows.forEach(function(rowArray) {
      row = rowArray.join(",");
      csvContent += row + "\r\n";
    });

    /* create a hidden <a> DOM node and set its download attribute */
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "Stock_Price_Report.xlsx");
    document.body.appendChild(link);
    /* download the data file named "Stock_Price_Report.csv" */
    link.click();
  }

  function fnExcelReport() {
    var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('tbl-summary-report'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
      tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
      //tab_text=tab_text+"</tr>";
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
    {
      txtArea1.document.open("txt/html", "replace");
      txtArea1.document.write(tab_text);
      txtArea1.document.close();
      txtArea1.focus();
      sa = txtArea1.document.execCommand("SaveAs", true, "test.xls");
    } else //other browser not tested on IE 11
      sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
  }

  function exportTableToExcel() {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById('tbl-summary-report');
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = 'invoice_report' + Date.now();

    // Specify file name
    filename = filename ? filename + '.xls' : 'excel_data.xls';

    // Create download link element
    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
      var blob = new Blob(['\ufeff', tableHTML], {
        type: dataType
      });
      navigator.msSaveOrOpenBlob(blob, filename);
    } else {
      // Create a link to the file
      downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

      // Setting the file name
      downloadLink.download = filename;

      //triggering the function
      downloadLink.click();
    }
  }
</script>

@endsection