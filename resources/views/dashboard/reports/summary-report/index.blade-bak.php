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
                <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)" onclick="exportTableToExcel();">
                  <i class="fa fa-file-excel-o"> </i>Download as Excel
                </a>
              </div>

            </div>
            <br />
            <div>
              <!-- <div id="tbl_all" class="div2  printableArea  " style="overflow-x: auto;overflow-y: auto; width:100%;"> -->
              <div id="tbl_all" class="div2  printableArea  " style="overflow-x: auto;width:100%" >

                <table id="tbl-summary" class="table table-bordered   yajra-datatable" style="width:100%">
                  <thead>
                    <tr>
                      <th>Ref No</th>
                      <th>Amount</th>
                      <th>Receipt Date</th>
                      <th>TRX ID</th>
                      <th>Amount</th>
                      <th>Receipt Date</th>
                      <th>TRX ID</th>
                      <th>SST</th>
                      <th>PYMT Date</th>
                      <th>TRX ID</th>
                      <th>Uncollected</th>
                      <th>Finaced Fees (RM)</th>
                      <th>Finaced Sum (RM)</th>
                      <th>Prof Balance</th>
                      <th>Staff Bonus(2%)</th>
                      <th>Staff Bonus(3%)</th>
                    </tr>
                    
                  </thead>
                  <tbody>
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

<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/PrintArea.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
<script src="{{ asset('js/jquery.print.js') }}"></script> -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
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

    filename = 'summary_report' + Date.now();

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

  function reloadTable() {
    table = $('#tbl-summary').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      pageLength: 25,
      // scrollY: 1000, 
      // scrollX: 800,
      // responsive: true,
      ajax: {
        url: "{{ route('summary-report.list') }}",
        data: {
          "status": $("#ddl_status").val(),
          "type": $("#ddl_type").val(),
          "requestor": $("#ddl_requestor").val(),
          "mode": $("#editMode").val()
        }
      },
      columns: [{
          data: 'case_ref_no',
          name: 'case_ref_no'
        },
        {
          data: 'pfee1',
          className: 'text-right',
          name: 'pfee1',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'pfee1_receipt_date',
          name: 'pfee1_receipt_date'
        },
        {
          data: 'pfee1_receipt_trx_id',
          name: 'pfee1_receipt_trx_id'
        },
        {
          data: 'pfee2',
          className: 'text-right',
          name: 'pfee2',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'pfee2_receipt_date',
          name: 'pfee2_receipt_date'
        },
        {
          data: 'pfee2_receipt_trx_id',
          name: 'pfee2_receipt_trx_id'
        },
        {
          data: 'sst',
          className: 'text-right',
          name: 'sst',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'sst_payment_date',
          name: 'sst_payment_date'
        },
        {
          data: 'sst_trx_id',
          name: 'sst_trx_id'
        },
        {
          data: 'uncollected',
          className: 'text-right',
          name: 'uncollected',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'financed_fee',
          className: 'text-right',
          name: 'financed_fee',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'financed_sum',
          className: 'text-right',
          name: 'financed_sum',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'prof_balance',
          className: 'text-right',
          name: 'prof_balance',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'staff_bonus_2_per',
          className: 'text-right',
          name: 'staff_bonus_2_per',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'staff_bonus_3_per',
          className: 'text-right',
          name: 'staff_bonus_3_per',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
      ]
    });

  }


  $(function() {
    reloadTable();
  });
</script>

@endsection