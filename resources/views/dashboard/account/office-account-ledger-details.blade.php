@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div id="dSummaryReport" class="card">
                        <div class="card-header">
                            <h4>Office Account Ledger Details - {{ $bankAccount->name }}</h4>
                            <div class="card-header-actions">
                                <a href="{{ url('office-account-ledger') }}" class="btn btn-sm btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Office Account Ledger
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            
                            <div class="row no-print">
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label>Account Name</label>
                                        <input class="form-control" type="text" value="{{ $bankAccount->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label>Account No</label>
                                        <input class="form-control" type="text" value="{{ $bankAccount->account_no }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input class="form-control" type="date" id="date_from" name="date_from">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input class="form-control" type="date" id="date_to" name="date_to">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info float-right" href="javascript:void(0)" onclick="getOfficeAccountLedgerDetails();">
                                        <i class="fa cil-search"></i> Search
                                    </a>
                                </div>
                                <div class="col-sm-12">
                                    <hr />
                                </div>
                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-success float-left" href="javascript:void(0)" onclick="fnExcelReport();">
                                        <i class="fa fa-file-excel-o"></i> Download as Excel
                                    </a>
                                </div>
                            </div>
                            <br />

                            <div id="div-ledger">
                                @include('dashboard.account.table.tab-office-account-ledger-details')
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
        function getOfficeAccountLedgerDetails() {
            var form_data = new FormData();
            form_data.append('bank_id', '{{ $bank_id }}');
            form_data.append('date_from', $("#date_from").val());
            form_data.append('date_to', $("#date_to").val());
            form_data.append('_token', '{{ csrf_token() }}');

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                url: '/getOfficeAccountLedgerDetails',
                success: function(data) {
                    console.log(data);
                    $("#div-ledger").html(data.view);
                    $("#div_full_screen_loading").hide();
                },
                fail: function(data) {
                    $("#div_full_screen_loading").hide();
                },
                error: function(data) {
                    $("#div_full_screen_loading").hide();
                }
            });
        }

        function fnExcelReport() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('tbl-ledger-data');
            for (j = 0; j < tab.rows.length; j++) {
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
                sa = txtArea1.document.execCommand("SaveAs", true, "Office Account Ledger Details.xls");
            } else {
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            }
            return (sa);
        }

        $(function() {
            const d = new Date();
            let month = d.getMonth() + 1;
            let year = d.getFullYear();
            let firstDay = new Date(year, month - 1, 1);
            let lastDay = new Date(year, month, 0);

            $("#date_from").val(firstDay.toISOString().split('T')[0]);
            $("#date_to").val(lastDay.toISOString().split('T')[0]);
            
            // Load initial data
            getOfficeAccountLedgerDetails();
        });
    </script>
@endsection






