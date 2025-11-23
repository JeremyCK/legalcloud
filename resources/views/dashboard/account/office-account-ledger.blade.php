@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">

                    <div id="dSummaryReport" class="card">
                        <div class="card-header">
                            <h4>Office Account Balance</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">

                               <div class="col-6 date_option date_option_year">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Year</label>
                                            <select class="form-control" id="ddl_year" name="ddl_year">
                                                <option value="2022">2022</option>
                                                <option value='2023'>2023</option>
                                                <option value='2024'>2024</option>
                                                <option value='2025' selected>2025</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 date_option date_option_month">
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
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Account Status</label>
                                            <select id="ddl-status" class="form-control" name="ddl-status">
                                                <option value="">-- All --</option>
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-6">
                                    <div class="form-group row">
                                      <div class="col">
                                        <label>Branch</label>
                                        <select class="form-control" name="branch_id" id="branch_id">
                                          <option value="0">-- Select Branch --</option>
                                          @foreach ($Branchs as $branch)
                                              <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                                          @endforeach
                                      </select>
                                      </div>
                                    </div>
                                  </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="getOfficeAccountLedger();">
                                        <i class="fa cil-search"> </i>Search
                                    </a>
                                </div>

                                <div class="col-sm-12">
                                    <hr />
                                </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="exportOfficeAccountLedgerToExcel();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>
                                </div>

                            </div>
                            <br />

                            <div id="div-ledger">
                                @include('dashboard.account.table.tab-office-account-ledger')
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
        function exportOfficeAccountLedgerToExcel() {
            var form_data = new FormData();
            
            form_data.append("status", $("#ddl-status").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("mon", $("#ddl_month").val());
            form_data.append("branch_id", $("#branch_id").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                url: '/exportOfficeAccountLedger',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    $("#div_full_screen_loading").hide();
                    
                    // Get filename from response headers
                    var filename = 'office_account_ledger_' + $("#ddl_year").val() + '_' + $("#ddl_month").val() + '_' + new Date().toISOString().split('T')[0] + '.xlsx';
                    
                    // Create blob and download
                    var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                    window.URL.revokeObjectURL(link.href);
                    
                    toastController('Excel file downloaded successfully');
                },
                error: function(xhr, status, error) {
                    $("#div_full_screen_loading").hide();
                    console.error('Export failed:', error);
                    toastController('Export failed: ' + error, 'error');
                }
            });
        }

        function getOfficeAccountLedger() {
            var form_data = new FormData();

            form_data.append("status", $("#ddl-status").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("mon", $("#ddl_month").val());
            form_data.append("branch_id", $("#branch_id").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                url: '/getOfficeAccountLedger',
                success: function(data) {
                    console.log(data);
                    $("#div-ledger").html(data.view);
                    $("#div_full_screen_loading").hide();
                },
                fail: function(data) {
                    $("#div_full_screen_loading").hide();
                },
                error: function(data){
                    $("#div_full_screen_loading").hide();
                }
            });
        }

        $(function() {
            const d = new Date();
            let month = d.getMonth() + 1;
            let year = d.getFullYear();

            $("#ddl_month").val(month);
            $("#ddl_year").val(year);
        });
    </script>
@endsection


