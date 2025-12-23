@extends('dashboard.base')

<link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div id="dBankReport" class="card">
                        <div class="card-header">
                            <h4>Bank Report</h4>
                        </div>

                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">
                                @include('dashboard.shared.components.input-year', [
                                    'fiscal_year' => $fiscal_year,
                                ])
                                <div class="col-6 date_option date_option_month">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control" id="ddl_month" name="ddl_month">
                                                <option value="0" selected>-- All --</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label><strong>Select Banks:</strong></label>
                                            <div class="mb-2">
                                                <input type="text" class="form-control" id="bank-search" 
                                                    placeholder="Search banks by name..." 
                                                    onkeyup="filterBanks()"
                                                    style="max-width: 400px;">
                                            </div>
                                            <div class="row" id="bank-list-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px; margin: 0;">
                                                @foreach ($Portfolio as $bank)
                                                    <div class="col-md-3 col-sm-6 col-12 mb-3 bank-item bank-visible" 
                                                        data-bank-name="{{ strtolower($bank->name) }}"
                                                        style="padding-left: 15px; padding-right: 15px;">
                                                        <div class="form-check" style="margin: 0;">
                                                            <input class="form-check-input bank-checkbox" type="checkbox" 
                                                                value="{{ $bank->id }}" id="bank_{{ $bank->id }}" checked
                                                                style="margin-top: 0.25rem;">
                                                            <label class="form-check-label" for="bank_{{ $bank->id }}" style="margin-left: 0.5rem; word-wrap: break-word;">
                                                                {{ $bank->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="form-text text-muted mt-2">
                                                <button type="button" class="btn btn-sm btn-link p-0" onclick="selectAllBanks()">Select All</button> | 
                                                <button type="button" class="btn btn-sm btn-link p-0" onclick="deselectAllBanks()">Deselect All</button> |
                                                <span id="bank-count-text"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info float-right" href="javascript:void(0)" onclick="reloadBankReport();">
                                        <i class="fa cil-search"> </i>Generate
                                    </a>
                                </div>

                                <div class="col-sm-12">
                                    <hr />
                                </div>

                                <div class="col-12" id="report-chart-section" style="display: none; clear: both;">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Bank Overview</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="div-chart" style="position: relative; width: 100%;">
                                                <div id="chart_bank_report" class="c-chart-wrapper mt-3 mx-3" style="height: 400px; position: relative; width: 100%;">
                                                    <canvas id="bankReportChart" style="max-width: 100%; height: 400px !important;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 mt-3" id="export-buttons-section" style="display: none;">
                                    <a class="btn btn-lg btn-success float-left mr-2" href="javascript:void(0)" onclick="exportToExcel();">
                                        <i class="fa fa-file-excel-o"> </i>Export to Excel
                                    </a>
                                    <a class="btn btn-lg btn-danger float-left" href="javascript:void(0)" onclick="exportToPDF();">
                                        <i class="fa fa-file-pdf-o"> </i>Export to PDF
                                    </a>
                                </div>
                            </div>
                            <br />
                            <div id="report-table-section" style="display: none;">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <strong>Cases by Bank</strong>
                                            </div>
                                            <div class="card-body" style="max-height:500px;overflow:scroll">
                                                <div id="tbl-bank-report" class="div_2024">
                                                    <!-- Table will be loaded here -->
                                                </div>
                                            </div>
                                        </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
    <script src="{{ asset('js/jquery.print.js') }}"></script>
    <script>
        var chart = null;

        function filterBanks() {
            var searchText = $('#bank-search').val().toLowerCase();
            var visibleCount = 0;
            var totalCount = 0;
            
            $('.bank-item').each(function() {
                var bankName = $(this).data('bank-name');
                totalCount++;
                
                if (bankName.indexOf(searchText) !== -1) {
                    $(this).show();
                    $(this).addClass('bank-visible');
                    $(this).removeClass('bank-hidden');
                    visibleCount++;
                } else {
                    $(this).hide();
                    $(this).addClass('bank-hidden');
                    $(this).removeClass('bank-visible');
                    // Uncheck hidden banks to prevent them from being included in the report
                    $(this).find('.bank-checkbox').prop('checked', false);
                }
            });
            
            // Update count text
            if (searchText === '') {
                $('#bank-count-text').text('');
                // If no search, all banks are visible
                $('.bank-item').addClass('bank-visible').removeClass('bank-hidden');
            } else {
                $('#bank-count-text').text('Showing ' + visibleCount + ' of ' + totalCount + ' banks');
            }
        }

        function selectAllBanks() {
            // Only select checkboxes in visible bank items
            $('.bank-item.bank-visible .bank-checkbox').prop('checked', true);
        }

        function deselectAllBanks() {
            // Only deselect checkboxes in visible bank items
            $('.bank-item.bank-visible .bank-checkbox').prop('checked', false);
        }

        function getSelectedBanks() {
            var selectedBanks = [];
            // Only get checked checkboxes from visible bank items
            $('.bank-item.bank-visible .bank-checkbox:checked').each(function() {
                selectedBanks.push($(this).val());
            });
            return selectedBanks;
        }

        function reloadBankReport() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var selectedBanks = getSelectedBanks();
            
            console.log('Selected banks for report:', selectedBanks);
            
            if (selectedBanks.length === 0) {
                alert('Please select at least one bank.');
                return;
            }

            var form_data = new FormData();
            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("banks", JSON.stringify(selectedBanks));

            $.ajax({
                type: 'POST',
                url: 'getBankReport',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {
                    console.log(result);

                    $("#tbl-bank-report").html(result.view);

                    // Destroy existing chart if it exists
                    if (chart != null) {
                        chart.destroy();
                    }
                    
                    // Show report sections first
                    $("#report-chart-section").show();
                    $("#report-table-section").show();
                    $("#export-buttons-section").show();
                    
                    // Wait a bit for DOM to update, then create chart
                    setTimeout(function() {
                        var ctx = document.getElementById('bankReportChart').getContext('2d');
                        chart = new Chart(ctx, {
                            type: "bar",
                            data: {
                                labels: result.bank_label,
                                datasets: [{
                                    data: result.bank_count,
                                    label: 'Cases',
                                    borderColor: "green",
                                    backgroundColor: "green",
                                    fill: false
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
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
                    }, 100);
                }
            });
        }

        function exportToExcel() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var tab = document.getElementById('tbl-bank-count');
            
            if (!tab) {
                alert('Please generate the report first.');
                return;
            }

            for (var j = 0; j < tab.rows.length; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }
            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");
            tab_text = tab_text.replace(/<img[^>]*>/gi, "");
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "bank_report.xls");
            } else {
                var a = document.createElement('a');
                var data_type = 'data:application/vnd.ms-excel';
                a.href = data_type + ', ' + encodeURIComponent(tab_text);
                a.download = 'bank_report_' + Date.now() + '.xlsx';
                a.click();
                e.preventDefault();
            }
        }

        function exportToPDF() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var selectedBanks = getSelectedBanks();
            var form_data = new FormData();
            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("banks", JSON.stringify(selectedBanks));

            $.ajax({
                type: 'POST',
                url: 'exportBankReportPDF',
                processData: false,
                contentType: false,
                data: form_data,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var blob = new Blob([data], { type: 'application/pdf' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'bank_report_' + Date.now() + '.pdf';
                    link.click();
                },
                error: function() {
                    alert('Error generating PDF. Please try again.');
                }
            });
        }

        $(function() {
            chart = new Chart("bankReportChart", {
                type: "bar",
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        label: 'Cases',
                        borderColor: "green",
                        backgroundColor: "green",
                        fill: false
                    }]
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

            // Don't auto-load - wait for user to click Generate
        });
    </script>
@endsection

