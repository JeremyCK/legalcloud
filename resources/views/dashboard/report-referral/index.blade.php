@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">



                    <div id="dSummaryReport" class="card">
                        <div class="card-header">
                            <h4>Referral Report</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">


                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>From date</label>
                                            <input class="form-control" type="date" id="date_from" name="date_from">
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>To date</label>
                                            <input class="form-control" type="date" id="date_to" name="date_to">
                                        </div>
                                    </div>

                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Referral Input</label>
                                            <select class="form-control" id="ddl_referral_input" name="ddl_referral_input">
                                                <option value="99">-- All --</option>
                                                <option value="1" selected>Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by paid status</label>
                                            <select class="form-control" id="ddl_paid" name="ddl_paid">
                                                <option value="99">-- All --</option>
                                                <option value="1">Yes</option>
                                                <option value="0" selected>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                      <div class="col">
                                        <label>Filter by Branch</label>
                                        <select class="form-control" id="ddl_branch" name="ddl_branch">
                                          <option value="0">-- All --</option>
                                          @foreach($branchs as $branch)
                                          <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                          @endforeach
                                        </select>
                                      </div>
                                    </div> 
                                  </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>TRX ID</label>
                                            <input class="form-control" type="text" id="trx_id" name="trx_id">
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="reloadTable();">
                                        <i class="fa cil-search"> </i>Search
                                    </a>
                                </div>


                                <div class="col-sm-12">
                                    <hr />
                                </div>


                                <!-- <div class="col-6">
                    <div class="form-group row">
                      <div class="col">
                        <label>Filter by Status</label>
                        <select class="form-control" id="ddl_status" name="ddl_status">
                          <option value="2">-- All --</option>
                          <option value="1">Paid</option>
                          <option value="0">Unpaid</option>
                        </select>
                      </div>
                    </div>
                  </div> -->
                                <div class="col-sm-12">
                                    {{-- <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="printReport();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a> --}}

                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="exportTableToExcel2();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>
                                </div>

                            </div>
                            <br />
                            <div>

                                <div id="div_referral_report" class="div2  printableArea  "
                                    style="overflow-x: auto;overflow-y: auto; width:100%;">
                                    <table id="tbl_referral_report" class="table table-bordered yajra-datatable"
                                        style="width:100%;">
                                        <thead style="background-color: black;color:white">
                                            <tr class="text-center">
                                                <th rowspan="2">Case Number <a href=""
                                                        class="btn btn-info btn-xs rounded shadow  mr-1"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></th>
                                                <th colspan="2">Referral 1</th>
                                                <th colspan="2">Referral 2</th>
                                                <th colspan="2">Referral 3</th>
                                                <th colspan="2">Misc</th>
                                                <th colspan="2">Marketing</th>
                                                <th colspan="2">Disb</th>
                                                <th colspan="2">Other</th>
                                                <th rowspan="2">Bill Amt</th>
                                                <th rowspan="2">Collected Amt</th>
                                                <th rowspan="2">Uncollected</th>
                                                <th rowspan="2">Finance fee</th>
                                            </tr>

                                            <tr>

                                                <th>Amount</th>
                                                <th>Details</th>
                                                {{-- <th>Payment Date</th> --}}
                                                {{-- <th>Status</th> --}}
                                                <th>Amount</th>
                                                <th>Details</th>
                                                {{-- <th>Payment Date</th>
                      <th>Status</th> --}}
                                                <th>Amount</th>
                                                <th>Details</th>
                                                {{-- <th>Payment Date</th>
                      <th>Status</th> --}}
                      
                      <th>Amount</th>
                      <th>Details</th>
                                                <th>Amount</th>
                                                <th>Details</th>
                                                <th>Amount</th>
                                                <th>Details</th>
                                                {{-- <th>Payment Date</th>
                      <th>Status</th> --}}
                                                <th>Amount</th>
                                                <th>Details</th>
                                                {{-- <th>Payment Date</th> --}}
                                                <!-- <th>Status</th> -->

                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot style="background-color: black;color:white">
                                            <tr>
                                                <td>Total</td>
                                                <td class="text-right"> RM <span id="total_r1">0.00</span></td>
                                                <td class="text-center"> -</td>

                                                <td class="text-right"> RM <span id="total_r2">0.00</span></td>
                                                <td class="text-center"> -</td>
                                                <td class="text-right"> RM <span id="total_r3">0.00</span></td>
                                                <td class="text-center"> -</td>
                                                <td class="text-right"> RM <span id="total_r4">0.00</span></td>
                                                <td class="text-center"> -</td>
                                                <td class="text-right"> RM <span id="total_marketing">0.00</span></td>
                                                <td class="text-center"> -</td>
                                                <td class="text-right"> RM <span id="total_disb">0.00</span></td>
                                                <td class="text-center"> -</td>
                                                <td class="text-right"> RM <span id="total_other">0.00</span></td>
                                                <td class="text-center"> -</td>
                                                <td class="text-right">  <span id="total_bill"></span></td>
                                                <td class="text-right">  <span id="total_collected"></span></td>
                                                <td class="text-right">  <span id="total_uncollected"></span></td>
                                                <td class="text-right">  <span id="total_financed"></span></td>


                                            </tr>
                                        </tfoot>
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
        function filterReport() {

            var form_data = new FormData();
            form_data.append("date_from", $("#date_from").val());
            form_data.append("date_to", $("#date_to").val());

            $.ajax({
                type: 'POST',
                url: '/filterInvoiceReport',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data.view);

                    $('#tbl_all').html(data.view);

                }
            });
        }

        function printReport() {

            var form_data = new FormData();
            form_data.append("date_from", $("#date_from").val());
            form_data.append("date_to", $("#date_to").val());

            var linkSource = "{{ route('download-referral') }}" + "?paid=" + $("#ddl_paid").val() + "&referral_input=" + $(
                "#ddl_referral_input").val();
            var downloadLink = document.createElement("a");

            downloadLink.href = linkSource;
            downloadLink.click();

            toastController('Downloading file');
        }

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
            tab = document.getElementById('tbl_referral_report'); // id of table

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
                sa = txtArea1.document.execCommand("SaveAs", true, "file.xlsx");
            } else //other browser not tested on IE 11
            {
                var a = document.createElement('a');
                //getting data from our div that contains the HTML table
                var data_type = 'data:application/vnd.ms-excel';
                var table_div = document.getElementById('tbl_referral_report');
                var table_html = table_div.outerHTML.replace(/ /g, '%20');
                a.href = data_type + ', ' +  encodeURIComponent(tab_text);
                //setting the file name
                // a.download = 'docket_report_' + Date.now() + '.xlsx';
                a.download = 'referral_report_' + Date.now() + '.xls';
                //triggering the function
                a.click();
                //just in case, prevent default behaviour
                e.preventDefault();
            }

            return (sa);
        }

        function exportTableToExcel() {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById('tbl_referral_report');
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

        function exportTableToExcel2() {
            var tab_text = "<table border='2px'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('tbl_referral_report'); // id of table

            for (j = 0; j < tab.rows.length; j++) {
                if (j == 2) {
                    tab_text = "<tr style='background-color:black;color:white'>" + tab_text + tab.rows[j].innerHTML +
                        "</tr>";
                } else {
                    tab_text = "<tr >" + tab_text + tab.rows[j].innerHTML + "</tr>";
                }

                //tab_text=tab_text+"</tr>" ;
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
                sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
            } else //other browser not tested on IE 11
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

            return (sa);
        }
    </script>
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        // document.getElementById("ddl_dispatch").onchange = function() {
        //   reloadTable();
        // }

        // document.getElementById("ddl_status").onchange = function() {
        //   reloadTable();
        // }


        function reloadTable() {
            var url = "{{ route('referral_report.list') }}";

            // url = url.replace('dispatchID', $("#ddl_dispatch").val());
            // url = url.replace('Status', $("#ddl_status").val());

            var table = $('#tbl_referral_report').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 100,
                ajax: {
                    url: url,
                    data: {
                        "referral_input": $("#ddl_referral_input").val(),
                        "paid": $("#ddl_paid").val(),
                        "date_from": $("#date_from").val(),
                        "date_to": $("#date_to").val(),
                        "trx_id": $("#trx_id").val(),
                        "branch": $("#ddl_branch").val()
                    }
                },
                columns: [{
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    {
                        data: 'referral_a1',
                        className: 'text-right',
                        name: 'referral_a1',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'referral_a1_data',
                        width: '200px',
                        name: 'referral_a1_data'
                    },
                    // {
                    //   data: 'referral_a1_payment_date',
                    //   name: 'referral_a1_payment_date'
                    // },
                    // {
                    //   data: 'referral_a1_payment_status',
                    //   className: 'text-center',
                    //   name: 'referral_a1_payment_status'
                    // },
                    {
                        data: 'referral_a2',
                        className: 'text-right',
                        name: 'referral_a2',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'referral_a2_data',
                        width: '200px',
                        name: 'referral_a2_data'
                    },
                    // {
                    //   data: 'referral_a2_payment_date',
                    //   name: 'referral_a2_payment_date'
                    // },
                    // {
                    //   data: 'referral_a2_payment_status',
                    //   className: 'text-center',
                    //   name: 'referral_a2_payment_status'
                    // },
                    {
                        data: 'referral_a3',
                        className: 'text-right',
                        name: 'referral_a3',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'referral_a3_data',
                        width: '200px',
                        name: 'referral_a3_data'
                    },
                    // {
                    //   data: 'referral_a3_payment_date',
                    //   name: 'referral_a3_payment_date'
                    // },
                    // {
                    //   data: 'referral_a3_payment_status',
                    //   className: 'text-center',
                    //   name: 'referral_a3_payment_status'
                    // },
                    {
                        data: 'referral_a4',
                        className: 'text-right',
                        name: 'referral_a4',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'referral_a4_data',
                        width: '200px',
                        name: 'referral_a4_data'
                    },
                    // {
                    //   data: 'referral_a4_payment_date',
                    //   name: 'referral_a4_payment_date'
                    // },
                    // {
                    //   data: 'referral_a4_payment_status',
                    //   className: 'text-center',
                    //   name: 'referral_a4_payment_status' 
                    // },
                    {
                        data: 'marketing',
                        className: 'text-right',
                        name: 'marketing',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'marketing_data',
                        name: 'marketing_data'
                    },
                    {
                        data: 'disb_amt_manual',
                        className: 'text-right',
                        name: 'disb_amt_manual',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'disb_data',
                        name: 'disb_data'
                    },
                    {
                        data: 'other_amt',
                        className: 'text-right',
                        name: 'other_amt',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'other_data',
                        name: 'other_data'
                    },
                    {
                        data: 'total_amt',
                        name: 'total_amt',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'uncollected',
                        name: 'uncollected',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'financed_fee',
                        name: 'financed_fee',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //   data: 'marketing_payment_date',
                    //   name: 'marketing_payment_date'
                    // },
                    // {
                    //   data: 'marketing_payment_status',
                    //   className: 'text-center',
                    //   name: 'marketing_payment_status'
                    // }
                ],
                drawCallback: function(settings) {

                    var api = this.api(),
                        data;

                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    var span_total_r1 = api.column(1).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_r2 = api.column(3).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_r3 = api.column(5).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_r4 = api.column(7).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_marketing = api.column(9).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    var span_total_disb = api.column(11).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    var span_total_other = api.column(13).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)


                    $("#total_r1").html(numberWithCommas(span_total_r1.toFixed(2)));
                    $("#total_r2").html(numberWithCommas(span_total_r2.toFixed(2)));
                    $("#total_r3").html(numberWithCommas(span_total_r3.toFixed(2)));
                    $("#total_r4").html(numberWithCommas(span_total_r4.toFixed(2)));
                    $("#total_marketing").html(numberWithCommas(span_total_marketing.toFixed(2)));
                    $("#total_disb").html(numberWithCommas(span_total_disb.toFixed(2)));
                    $("#total_other").html(numberWithCommas(span_total_other.toFixed(2)));
                    // $("#span_collected_amount").html(numberWithCommas(span_collected_amount.toFixed(2)));
                    // $("#span_total_pfee").html(numberWithCommas(span_total_pfee.toFixed(2)));
                    // $("#span_total_sst").html(numberWithCommas(span_total_sst.toFixed(2)));
                    // $("#span_total_pfee_to_transfer").html(numberWithCommas(span_total_pfee_to_transfer.toFixed(
                    //     2)));
                    // $("#span_total_sst_to_transfer").html(numberWithCommas(span_total_sst_to_transfer.toFixed(
                    //     2)));
                    // $("#span_total_transferred_pfee").html(numberWithCommas(span_total_transferred_pfee.toFixed(
                    //     2)));
                    // $("#span_total_transferred_sst").html(numberWithCommas(span_total_transferred_sst.toFixed(
                    //     2)));


                    // transfer_amt_hidden = parseFloat($("#transfer_amount_hidden").val());
                    console.log(span_total_r1);

                    // transfer_amount = monTotal + transfer_amt_hidden;
                    // alert($("#transfer_amount_hidden").val());
                    // $("#transfer_amount").val(transfer_amount);
                }
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function updatePaidStatus($id) {

            var invoice_id = [];
            var invoice = {};



            $.each($("input[name='invoice']:checked"), function() {

                itemID = $(this).val();

                invoice = {
                    invoice_id: itemID,
                };
                invoice_id.push(invoice);

            });


            if (invoice_id.length <= 0) {
                return;
            }

            var form_data = new FormData();

            form_data.append("invoice_id", JSON.stringify(invoice_id));
            console.log(JSON.stringify(invoice_id));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Update following invoice to paid?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        url: '/updatePaidStatus/',
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                toastController(data.message);
                                filterReport();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        $(function() {

            
            var date = new Date(),
                y = date.getFullYear(),
                m = date.getMonth();
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);
            lastDay = (("0" + lastDay.getDate()).slice(-2));

            var last_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" +
                lastDay;
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + ((
                "0" +
                firstDay.getDate()).slice(-2));

            $("#date_from").val(start_date);
            $("#date_to").val(last_date);
            
            reloadTable();
        });
    </script>
@endsection
