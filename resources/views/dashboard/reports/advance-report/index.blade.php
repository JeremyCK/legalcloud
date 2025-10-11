@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">


 
                    <div id="dSummaryReport" class="card">
                        <div class="card-header">
                            <h4>Advance Report</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">


                                {{-- <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Date option</label>
                                            <select class="form-control" id="ddl_date_option" name="ddl_date_option">
                                                <option value="99">-- All --</option>
                                                <option value="1">Yearly</option>
                                                <option value="2">Monthly</option>
                                                <option value="3">Daily</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="col-6 date_option date_option_year">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Year</label>
                                            <select class="form-control" id="ddl_year" name="ddl_year">
                                                {{-- <option value="0">-- All --</option> --}}
                                                <option value="2022">2022</option>
                                                <option value='2023'>2023</option>
                                                <option value='2024'>2024</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 date_option date_option_month">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control" id="ddl_month" name="ddl_month">
                                                {{-- <option value="0">-- All --</option> --}}
                                                <option value="1">January</option>
                                                <option value='2'>February</option>
                                                <option value='3'>March</option>
                                                <option value='4'>April</option>
                                                <option value='5'>May</option>
                                                <option value='6'>June</option>
                                                <option value='7'>July</option>
                                                <option value='8'>August</option>
                                                <option value='9'>September</option>
                                                <option value='10'>October</option>
                                                <option value='11'>November</option>
                                                <option value='12'>December</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 date_option date_option_year" style="display:none">
                                  <div class="form-group row">
                                      <div class="col">
                                          <label>Year</label>
                                          <select class="form-control" id="ddl_year" name="ddl_year">
                                              <option value="0">-- All --</option>
                                              <option value="2022">2022</option>
                                              <option value='2023'>2023</option>
                                              <option value='2024'>2024</option>
                                          </select>
                                      </div>
                                  </div>
                              </div>

                                <div class="col-6 date_option date_option_month" style="display:none">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control" id="ddl_month" name="ddl_month">
                                                <option value="0">-- All --</option>
                                                <option value="1">January</option>
                                                <option value='2'>February</option>
                                                <option value='3'>March</option>
                                                <option value='4'>April</option>
                                                <option value='5'>May</option>
                                                <option value='6'>June</option>
                                                <option value='7'>July</option>
                                                <option value='8'>August</option>
                                                <option value='9'>September</option>
                                                <option value='10'>October</option>
                                                <option value='11'>November</option>
                                                <option value='12'>December</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 date_option date_option_day" style="display:none">
                                  <div class="form-group row">
                                      <div class="col">
                                          <label>Date</label>
                                          <input class="form-control" type="date" id="date_day" name="date_day">
                                      </div>
                                  </div>

                              </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Staff</label>
                                            <select class="form-control" name="user" id="user">
                                                <option value="0">-- All --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Branch</label>
                                            <select class="form-control" id="ddl_branch" name="ddl_branch">
                                                <option value="0">-- All --</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="reloadTable();">
                                        <i class="fa cil-search"> </i>Search
                                    </a>

                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="printReport();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>
                                </div>


                                <div class="col-sm-12">
                                    <hr />
                                </div>


                            </div>
                            <br />
                            <div>
                                <div id="div_summary_report"
                                    style="overflow-x: auto;overflow-y: auto; width:100%;height: 900px; ">
                                    @include('dashboard.reports.advance-report.tbl-advance-report')
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

            var linkSource = "{{ route('download-advance') }}" + "?paid=" + $("#ddl_paid").val() + "&user=" + $("#user")
                .val();
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
    </script>
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        // document.getElementById("ddl_dispatch").onchange = function() {
        //   reloadTable();
        // }

        // document.getElementById("ddl_date_option").onchange = function() {
        //   reloadTable();
        // }

        // document.getElementById("ddl_date_option").onchange = function() {

        //    $(".date_option").hide();

        //     if ($("#ddl_date_option").val() == 1) {
        //       $(".date_option_year").show();
        //     } else if ($("#ddl_date_option").val() == 2) {
        //       $(".date_option_month").show();

        //     } else if ($("#ddl_date_option").val() == 3) {

        //       $(".date_option_day").show();
        //     }
        // }

        function updateSummarySum() {


            var form_data = new FormData();

            form_data.append("user", $("#user").val());
            form_data.append("user", $("#user").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                url: '/updateSummarySum',
                success: function(data) {
                    console.log(data);
                }
            });
        }


        function reloadTable() {

            var form_data = new FormData();

            form_data.append("user", $("#user").val());
            // form_data.append("paid", $("#ddl_paid").val());
            form_data.append("branch", $("#ddl_branch").val());
            form_data.append("date_option", $("#ddl_date_option").val());
            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("day", $("#date_day").val());

            $.ajax({
                type: 'POST',
                url: '/getAdvanceReport',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#div_summary_report').html(data.view);
                    console.log(data);
                    if (data.status == 1) {
                        $('#tbl-summary').html(data.view);
                    }

                }
            });

        }


               $(function() {
                const d = new Date();
                let month = d.getMonth() + 1;
                let year = d.getFullYear();

                $("#ddl_month").val(month);
                $("#ddl_year").val(year);
                // alert(3);

                reloadTable();
            });
    </script>
@endsection
