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
                            <h4>Staff Performance Report</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">

                                @include('dashboard.shared.components.input-year', [
                                    'fiscal_year' => $fiscal_year,
                                ])

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 date_option date_option_day"
                                    style="display:none">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Date</label>
                                            <input class="form-control" type="date" id="date_day" name="date_day">
                                        </div>
                                    </div>

                                </div>


                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Staff</label>
                                            <select class="form-control" id="dl_staff" name="dl_staff">
                                                @foreach ($staffs as $branch)
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
                                    <hr/>
                                </div>


                                <div class="col-sm-12" id ="div-case-summary">
                                    @include('dashboard.reports.staff-details.div-case-summary')
                                </div>
                            </div>
                            <br />
                            <div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card">
                        <div class="card-header">
                            <strong>Monthly Case Statistics - Accepted vs Closed</strong>
                        </div>
                        <div class="card-body">

                            <div class="div-chart div-chart2022  ">
                                <div id="case_Count_Monthly" class="c-chart-wrapper c-chart-wrapper2022 mt-3 mx-3"
                                    style="height: 400px">
                                    <canvas height="392" class="chart" id="caseCountMonthly"></canvas>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="col-12 ">
                    <div class="card">
                        <div class="card-header">
                            <strong>Case Status Breakdown</strong>
                        </div>
                        <div class="card-body">

                          
                            <div class="nav-tabs-custom nav-tabs-custom-ctr">
                                <ul class="nav nav-tabs" role="tablist">

                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#tab_accepted"
                                            role="tab" aria-controls="trust"
                                            aria-selected="true">Accepted Cases
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab_running"
                                            role="tab" aria-controls="trust"
                                            aria-selected="false">Running
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#tab_reviewing"
                                            role="tab" aria-controls="trust"
                                            aria-selected="false">Reviewing Case
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#tab_pending_close"
                                            role="tab" aria-controls="trust"
                                            aria-selected="false">Pending close case
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#tab_close"
                                            role="tab" aria-controls="trust" aria-selected="false">Close case
                                        </a>
                                    </li>


                                </ul>
                            </div>

                            <div class="tab-content" style="max-height: 400px;overflow:scroll">

                                <div class="tab-pane active" id="tab_accepted" role="tabpanel">
                                    <div id="tbl-accepted-content">
                                        <table class="table table-striped table-bordered datatable">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>No.</th>
                                                    <th>Action</th>
                                                    <th>Ref No</th>
                                                    <th>pfee1</th>
                                                    <th>pfee2</th>
                                                    <th>Disb</th>
                                                    <th>sst</th>
                                                    <th>Collected Amount</th>
                                                    <th>Paid</th>
                                                    <th>Payment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" colspan="10">No data</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab_running" role="tabpanel">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Action</th>
                                                <th>Date</th>
                                                <th>TRX ID</th>
                                                <th>Payee</th>
                                                <th>Case Ref No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <td class="text-center" colspan="7">No data</td>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane " id="tab_reviewing" role="tabpanel">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Action</th>
                                                <th>Date</th>
                                                <th>TRX ID</th>
                                                <th>Payee</th>
                                                <th>Case Ref No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="7">No data</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane " id="tab_pending_close" role="tabpanel">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Action</th>
                                                <th>Date</th>
                                                <th>TRX ID</th>
                                                <th>Payee</th>
                                                <th>Case Ref No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="7">No data</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane " id="tab_close" role="tabpanel">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Action</th>
                                                <th>Date</th>
                                                <th>TRX ID</th>
                                                <th>Payee</th>
                                                <th>Case Ref No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="7">No data</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-12 ">
                    <div class="card">
                        <div class="card-header">
                            <strong>Case Type Overview</strong>
                        </div>
                        <div class="card-body">

                            <div class="div-chart div-chart2022  ">
                                <div id="chart_cases_all_2022" class="c-chart-wrapper c-chart-wrapper2022 mt-3 mx-3"
                                    style="height: 400px">
                                    <canvas height="392" class="chart" id="caseCountChartAll2022"></canvas>
                                </div>
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

        var chart = null;
        var chartMon = null;
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
                            },
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
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
                            },
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
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
            form_data.append("staff", $("#dl_staff").val());

            $.ajax({
                type: 'POST',
                url: 'get-staff-details=report',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {
                    console.log(result);

                    var xValues = result.lawyerList;
                    var puchongValue = [];
                    var uptownValue = [];
                    var DesaParkValue = [];

                    // 
                    // $("#tbl-clerk-report").html(result.view_clerk);

                    $("#div-case-summary").html(result.divCaseSummary);
                    $("#tbl-accepted-content").html(result.tblCaseAccepted);
                    $("#tab_running").html(result.tblCaseActive);
                    $("#tab_reviewing").html(result.tblCaseReviewing);
                    $("#tab_pending_close").html(result.tblCasePendingClose);
                    $("#tab_close").html(result.tblCaseClose);
                    
                    // Attach export button handler
                    $(document).off('click', '#btn-export-accepted-excel');
                    $(document).on('click', '#btn-export-accepted-excel', function() {
                        exportAcceptedCasesToExcel();
                    });

                    // Portfolio/Bank chart (existing)
                    chart.data.datasets[0].data = result.case_count;
                    chart.data.labels = result.case_label;
                    chart.update();

                    // Monthly chart - Updated to show Accepted vs Closed
                    if (result.accepted_by_month && result.closed_by_month) {
                        chartMon.data.datasets = [
                            {
                                label: 'Accepted Cases',
                                data: result.accepted_by_month,
                                backgroundColor: 'rgba(40, 167, 69, 0.6)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Closed Cases',
                                data: result.closed_by_month,
                                backgroundColor: 'rgba(108, 117, 125, 0.6)',
                                borderColor: 'rgba(108, 117, 125, 1)',
                                borderWidth: 1
                            }
                        ];
                        chartMon.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        chartMon.update();
                    } else {
                        // Fallback to old chart if new data not available
                        chartMon.data.datasets[0].data = result.cases_count;
                        chartMon.data.labels = result.cases_Mon;
                        chartMon.update();
                    }

                    // chart.data.datasets[0].data = result.lawyer_case_count;
                    // chart.data.labels = result.lawyer_label;
                    // chart.update();


                    // chart_clerk.data.datasets[0].data = result.clerk_case_count;
                    // chart_clerk.data.labels = result.clerk_label;
                    // chart_clerk.update();


                }
            });

            return;

        }

        $(function() {

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


            chartMon = new Chart("caseCountMonthly", {
                type: "bar",
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Accepted Cases',
                            data: [],
                            backgroundColor: 'rgba(40, 167, 69, 0.6)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Closed Cases',
                            data: [],
                            backgroundColor: 'rgba(108, 117, 125, 0.6)',
                            borderColor: 'rgba(108, 117, 125, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    }
                }
            });

            chart_clerk = new Chart("caseClerk", {
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

        function filterDate(year) {
            $(".div_year ").hide();
            $(".div_" + year).show();
        }

        function exportAcceptedCasesToExcel() {
            var $btn = $('#btn-export-accepted-excel');
            var originalHtml = $btn.html();
            $btn.prop('disabled', true);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("staff", $("#dl_staff").val());

            $.ajax({
                type: 'POST',
                url: 'export-staff-accepted-cases-excel',
                data: form_data,
                processData: false,
                contentType: false,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'accepted_cases_' + $("#ddl_year").val() + '_' + Date.now() + '.xlsx';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(link.href);
                    
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                    if (typeof toastController === 'function') {
                        toastController('Excel file downloaded successfully');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'Error generating Excel file.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    console.error('Excel export error:', error);
                    
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }
            });
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
