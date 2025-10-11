@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">

                    <div id="dSummaryReport" class="card">
                        <div class="card-header">
                            <h4>Client Account Balance</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">



                                {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
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

                                </div> --}}

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
                                            <label>Status</label>

                                            <select id="ddl-status" class="form-control" name="ddl-status">
                                                <option value="">-- All --</option>
                                                <option value="2">Open</option>
                                                <option value="1">In Progress</option>
                                                <option value="3">KIV</option>
                                                <option value="4">Pending Close</option>
                                                <option value="7">Reviewing</option>
                                                <option value="99">Aborted</option>
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


                                  <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Bank Account</label>

                                            <select class="form-control" name="bank_id" id="bank_id">
                                                <option value="0">-- Select Bank --</option>
                                                @foreach ($OfficeBankAccount as $bankAccount)
                                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                                        ({{ $bankAccount->account_no }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                  


                               
{{-- 
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Ref No</label>
                                            <input class="form-control" type="text" id="ref_no" name="ref_no">
                                        </div>
                                    </div>

                                </div> --}}

                                {{-- <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Office Account</label>

                                            <select id="ddl-bank-account" class="form-control" name="ddl-bank-account">
                                                <option value="0">-- All --</option>
                                                @foreach ($OfficeBankAccount as $index => $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->name }} ({{ $row->account_no }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}


                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="getClientLedger();">
                                        <i class="fa cil-search"> </i>Search
                                    </a>
                                </div>


                                <div class="col-sm-12">
                                    <hr />
                                </div>


                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="fnExcelReport();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>
                                </div>

                            </div>
                            <br />

                            <div id="div-ledger">
                                @include('dashboard.banks.tab-ledger')
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
            tab = document.getElementById('tbl-ledger-data'); // id of table

            if ($("#ddl-bank-account").val() == 0) {
                return;
            }

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

            filename = 'bank_ledger_' + $("#selected_bank").html('') + '_' + Date.now() + '.xls';

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, filename);
            } else //other browser not tested on IE 11
            {
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            }

            return (sa);
        }

        function exportTableToExcel() {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById('tbl-ledger-data');
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
        // document.getElementById('ddl-bank-account').onchange = function() {

        //     var skillsSelect = document.getElementById("ddl-bank-account");
        //     var selectedText = skillsSelect.options[skillsSelect.selectedIndex].text;


        //     if ($("#ddl-bank-account").val() == 0) {
        //         $("#selected_bank").html('');
        //     } else {
        //         $("#selected_bank").html($("#ddl-bank-account option:selected").text());
        //         $("#span_bank_name").html($("#ddl-bank-account option:selected").text());
        //     }


        // };


        function reloadTable() {
            var url = "{{ route('bankLedgerList.list') }}";

            $("#div_full_screen_loading").show();


            var table = $('#tbl_referral_report').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 25,
                ajax: {
                    url: url,
                    data: {
                        "referral_input": $("#ddl_referral_input").val(),
                        "paid": $("#ddl_paid").val(),
                        "date_from": $("#date_from").val(),
                        "date_to": $("#date_to").val(),
                        "branch_id": $("#branch_id").val(),
                        "bank_id": $("#bank_id").val(),
                        "bank_name": $("#ddl-bank-account option:selected").text()
                    }
                },
                columns: [{
                        data: 'transfer_date',
                        name: 'transfer_date',
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'pfee1_inv',
                        name: 'pfee1_inv',
                    },
                    {
                        data: 'pfee2_inv',
                        name: 'pfee2_inv',
                    },
                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                    },
                    {
                        data: 'transfer_amount',
                        name: 'transfer_amount',
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },

                    {
                        data: 'transfer_date',
                        name: 'transfer_date'
                    },
                ],
                drawCallback: function(settings) {
                    // $("#div_full_screen_loading").hide();
                }
            });
        }

        function getClientLedger($id) {
            var form_data = new FormData();

            // if ($("#ddl-bank-account").val() == 0) {
            //     Swal.fire('notice!', 'No bank account selected', 'warning');
            //     return;
            // }

            form_data.append("status", $("#ddl-status").val());
            form_data.append("date_from", $("#date_from").val());
            form_data.append("date_to", $("#date_to").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("mon", $("#ddl_month").val());
            form_data.append("ref_no", $("#ref_no").val());
            form_data.append("branch_id", $("#branch_id").val());
            form_data.append("bank_id", $("#bank_id").val());

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
                url: '/getClientLedger',
                success: function(data) {
                    console.log(data);
                    $("#div-ledger").html(data.view);
                    $("#selected_bank").html($("#ddl-bank-account option:selected").text());
                    $("#span_bank_name").html($("#ddl-bank-account option:selected").text());

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
