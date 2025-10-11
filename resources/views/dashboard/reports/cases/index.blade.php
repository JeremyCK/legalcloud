@extends('dashboard.base')

<link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">



                    <div id="dSummaryReport" class="card">
                        <div class="card-header">
                            <h4>Cases Report</h4>
                        </div>
                        


                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">


                                @include('dashboard.shared.components.input-year', [
                                    'fiscal_year' => $fiscal_year,
                                ])
                                @include('dashboard.shared.components.input-month')



                                {{-- <div class="col-6 date_option date_option_month" >
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control" id="ddl_month" name="ddl_month">
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
                                </div> --}}



                                {{-- <div class="col-6  ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Status</label>
                                            <select class="form-control" id="ddl_status" name="ddl_status">
                                                <option value="0">-- All --</option>
                                                <option value="1">Active</option>
                                                <option value='2'>Closed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}

                                {{-- <div class="col-6  ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Role</label>
                                            <select class="form-control" id="ddl_role" name="ddl_role">
                                                <option value="lawyer">Lawyer</option>
                                                <option value="clerk">Clerk</option>\
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 date_option date_option_day"
                                    style="display:none">
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
                                            <label>Case Type</label>
                                            <select class="form-control" name="ddl_type" id="ddl_type">
                                                <option value="0">-- All --</option>
                                                @foreach ($Portfolio as $port)
                                                    <option value="{{ $port->id }}">{{ $port->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Branch</label>
                                            <select class="form-control" name="ddl_branch" id="ddl_branch">
                                                <option value="0">-- All --</option>
                                                @foreach ($branchs as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="reloadTable2();">
                                        <i class="fa cil-search"> </i>Generate
                                    </a>

                                </div>


                                <div class="col-sm-12">
                                    <hr />
                                </div>

                                <div class="col-12 ">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Case Type Overview</strong>
                                        </div>
                                        <div class="card-body">

                                            <div class="div-chart div-chart2022  ">
                                                <div id="chart_cases_all_2022"
                                                    class="c-chart-wrapper c-chart-wrapper2022 mt-3 mx-3"
                                                    style="height: 400px">
                                                    <canvas height="392" class="chart"
                                                        id="caseCountChartAll2022"></canvas>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>



                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="fnExcelReport();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>
                                </div>


                            </div>
                            <br />
                            <div>




                                @if (count($staffCaseCount) > 0)
                                    <div class="row">

                                        {{-- <div class="col-xl-12 col-md-12 col-sm-12">
                                            <h4>Yearly report</h4>
                                        </div> --}}
                                        <div class="col-xl-12 col-md-12 col-sm-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <strong>Cases</strong>
                                                </div>

                                                <div class="card-body" style="max-height:500px;overflow:scroll">

                                                    <div id="tbl-case-report" class="  div_2024">

                                                    </div>





                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
    {{-- <script src="{{ asset('js/plugins/datatables.js') }}"></script> --}}
    <script src="/js/plugins/datatables.js"></script>
    <script>
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

        var chart = null;
        var chart_clerk = null;

        function reloadTable() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("role", $("#ddl_role").val());

            $.ajax({
                type: 'POST',
                url: 'getStaffCaseReport',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                    var xValues = result.lawyerList;
                    var puchongValue = [];
                    var uptownValue = [];
                    var DesaParkValue = [];


                    chart = new Chart("caseCountChartAll2022", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                data: result.lawyercount,
                                label: 'Cases',
                                borderColor: "green",
                                backgroundColor: "green",
                                fill: false
                            }, ]
                        },
                        options: {
                            legend: {
                                display: true
                            }
                        }
                    });


                    var xValues = result.clerkList;

                    chart_clerk = new Chart("caseClerk", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                data: result.clerkcount,
                                label: 'Cases',
                                borderColor: "green",
                                backgroundColor: "green",
                                fill: false
                            }, ]
                        },
                        options: {
                            legend: {
                                display: true
                            }
                        }
                    });


                }
            });

            return;

        }

        function reloadTable2() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("type", $("#ddl_type").val());
            form_data.append("branch", $("#ddl_branch").val());

            $.ajax({
                type: 'POST',
                url: 'getCaseReport',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {
                    console.log(result);

                    $("#tbl-case-report").html(result.view);

                    // if (chart != null)
                    // {
                    //     chart.destroy();
                    // }




                    // var xValues = result.case_label;
                    chart.data.datasets[0].data = result.case_count;
                    chart.data.labels = result.case_label;
                    chart.update();


                    // chart = new Chart("caseCountChartAll2022", {
                    //     type: "bar",
                    //     data: {
                    //         labels: xValues,
                    //         datasets: [{
                    //             data: result.case_count,
                    //             label: 'Cases',
                    //             borderColor: "green",
                    //             backgroundColor: "green",
                    //             fill: false
                    //         }, ]
                    //     },
                    //     options: {
                    //         legend: {
                    //             display: true
                    //         }
                    //     }
                    // });

                }
            });

            return;

        }

        $(function() {

            // reloadTable();

            chart = new Chart("caseCountChartAll2022", {
                type: "bar",
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        label: 'Cases',
                        borderColor: "green",
                        backgroundColor: "green",
                        fill: false
                    }, ]
                },
                options: {
                    legend: {
                        display: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            reloadTable2();

        });

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
            tab = document.getElementById('tbl-case-count'); // id of table

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
            {
                var a = document.createElement('a');
                var case_type = '';

                if ($("#ddl_type").val() != 0) {
                    case_type = $("#ddl_type option:selected").text() + '_';
                }

                //getting data from our div that contains the HTML table
                var data_type = 'data:application/vnd.ms-excel';
                var table_div = document.getElementById('tbl-case-count');
                var table_html = table_div.outerHTML.replace(/ /g, '%20');
                a.href = data_type + ', ' + encodeURIComponent(tab_text);
                //setting the file name
                a.download = case_type + 'cases_type_report_' + Date.now() + '.xlsx';
                //triggering the function
                a.click();
                //just in case, prevent default behaviour
                e.preventDefault();
            }

            return (sa);
        }

        function filterDate(year) {
            $(".div_year ").hide();
            $(".div_" + year).show();
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

        document.getElementById("ddl_date_option").onchange = function() {

            $(".date_option").hide();

            if ($("#ddl_date_option").val() == 1) {
                $(".date_option_year").show();
            } else if ($("#ddl_date_option").val() == 2) {
                $(".date_option_month").show();

            } else if ($("#ddl_date_option").val() == 3) {

                $(".date_option_day").show();
            }
        }

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


        function reloadTable1() {

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
    </script>
@endsection
