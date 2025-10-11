<style>
    #myBtn {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Fixed/sticky position */
        bottom: 20px;
        /* Place the button at the bottom of the page */
        right: 30px;
        /* Place the button 30px from the right */
        z-index: 99;
        /* Make sure it does not overlap */
        border: none;
        /* Remove borders */
        outline: none;
        /* Remove outline */
        background-color: red;
        /* Set a background color */
        color: white;
        /* Text color */
        cursor: pointer;
        /* Add a mouse pointer on hover */
        padding: 15px;
        /* Some padding */
        border-radius: 10px;
        /* Rounded corners */
        font-size: 18px;
        /* Increase font size */
    }

    #myBtn:hover {
        background-color: #555;
        /* Add a dark-grey background on hover */
    }
</style>
@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">

                    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

                    <div id="dSummaryReport" class="card">
                        <div class="card-header">

                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <h4>Bank Recon Report</h4>
                                </div>
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <a class="btn btn-lg btn-success  float-right" href="/bank-recon">
                                        <i class="fa cil-find-in-page"> </i>Bank Recon
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row no-print">

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Bank Account</label>
                                            <select class="form-control" name="bank_id" id="bank_id">
                                                @foreach ($OfficeBankAccount as $bankAccount)
                                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                                        ({{ $bankAccount->account_no }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Details</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" onchange="showHideDetails()"
                                                    id="showdetails" checked>
                                                <label class="form-check-label" for="showdetails">Show Details</label>
                                            </div>
                                        </div>

                                    </div>
 
                                </div>



                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">


                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Recon Date</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select id="ddl_month" class="form-control" name="ddl_month">
                                                        <option value="1"
                                                            @if (date('m') == '01') selected @endif>January
                                                        </option>
                                                        <option value='2'
                                                            @if (date('m') == '02') selected @endif>February
                                                        </option>
                                                        <option value='3'
                                                            @if (date('m') == '03') selected @endif>March</option>
                                                        <option value='4'
                                                            @if (date('m') == '04') selected @endif>April</option>
                                                        <option value='5'
                                                            @if (date('m') == '05') selected @endif>May</option>
                                                        <option value='6'
                                                            @if (date('m') == '06') selected @endif>June</option>
                                                        <option value='7'
                                                            @if (date('m') == '07') selected @endif>July</option>
                                                        <option value='8'
                                                            @if (date('m') == '08') selected @endif>August
                                                        </option>
                                                        <option value='9'
                                                            @if (date('m') == '09') selected @endif>September
                                                        </option>
                                                        <option value='10'
                                                            @if (date('m') == '10') selected @endif>October
                                                        </option>
                                                        <option value='11'
                                                            @if (date('m') == '11') selected @endif>November
                                                        </option>
                                                        <option value='12'
                                                            @if (date('m') == '12') selected @endif>December
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select id="ddl_year" class="form-control" name="ddl_year">
                                                        <option value="2022">2022</option>
                                                        <option value="2023">2023</option>
                                                        <option value="2024" >2024</option>
                                                        <option value="2025" selected>2025</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row">
                                      <label class="col-md-4 col-form-label" for="hf-email">Recon Date</label>
                                      <div class="col-md-8">
                                          <input class="form-control" name="recon_date" id="recon_date"
                                              type="date" readonly />
                                      </div>
                                  </div> --}}

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Recon Date</label>
                                            <input class="form-control" name="recon_date" id="recon_date" type="date"
                                                readonly />
                                            {{-- <select class="form-control" name="recon_date" id="recon_date">
                                                @foreach ($recon_date as $date)
                                                    <option value="{{ $date->recon_date }}">{{ $date->recon_date }}</option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>




                                </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="getMonthRecon()">
                                        <i class="fa cil-search"> </i>Generate
                                    </a>
                                </div>


                                <div class="col-sm-12">
                                    <hr />
                                </div>

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="printlo();">
                                        <i class="fa fa-file-excel-o"> </i>Print Report
                                    </a>

                                </div>

                            </div>
                            <br />
                            <div id="dQuotationInvoice-p" class="row">
                                <div class="col-12 float-center">
                                    <h2 class="text-center" id="txt_firm_name">L H YEO & CO</h2>
                                    <h4 class="text-center">Bank Reconciliation As at <span id="lbl_recon_date"></span></h4>

                                    <hr />
                                    <hr />
                                </div>

                                <div class="col-12">
                                    <div id="div-bank-details" class="row">
                                        @include('dashboard.reports.bank-recon.bank-details')
                                    </div>
                                </div>

                                <!-- <div class="col-12 ">

                            <table id="tbl-all" class="table  datatable" style="overflow-x: auto; width:100%">
                            @include('dashboard.reports.bank-recon.tbl-bank-recon-listv2')
                            </table>
                          </div> -->


                                <div class="col-12 ">
                                    {{-- <hr />
                                    <table class="table  datatable" style="overflow-x: auto; width:100%">
                                        <tr>
                                            <td class="text-left">Date</td>
                                            <td class="text-left">Type</td>
                                            <td class="text-left">TRX ID</td>
                                            <td class="text-left">Desc</td>
                                            <td class="text-right">Amount</td>
                                        </tr>
                                    </table>
                                    <hr /> --}}
                                    <h4 class="text-left">Initial Opening Balance</h4>
                                    <table class="table  datatable" style="overflow-x: auto; width:100%">
                                        <tr>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-right"><span class="openingBalance"></span></td>
                                        </tr>
                                    </table>
                                    <hr />
                                    <h4 class="text-left">Last Recon Balance</h4>
                                    <table class="table  datatable" style="overflow-x: auto; width:100%">
                                        <tr>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-left">Last Recon Balance</td>
                                            <td class="text-right"><span class="lastReconBalance"></span></td>
                                        </tr>
                                    </table>
                                    <hr />
                                    <b>
                                        <p class="text-right">Subtotal: Last Recon Balance = <span
                                                class="lastReconBalance"></span> </p>
                                    </b>
                                    <hr />
                                    <h4 class="text-left">Add Cleared Deposits</h4>
                                    <table id="tbl-bank-recon-add" class="table  datatable"
                                        style="overflow-x: auto; width:100%"></table>
                                    <hr />
                                    <b>
                                        <p class="text-right">Subtotal: Add Cleared Deposits = <span
                                                id="totalAddCLRDeposit"></span> </p>
                                    </b>
                                    <hr />
                                    <h4 class="text-left">Less Cleared Cheques</h4>
                                    <table id="tbl-bank-recon-less" class="table  datatable"
                                        style="overflow-x: auto; width:100%"></table>
                                    <hr />
                                    <b>
                                        <p class="text-right">Subtotal: Less Cleared Cheques = <span
                                                id="totalLessCLRDeposit"></span> </p>
                                    </b>
                                    <hr />
                                    <h4 class="text-left">Balance In Hand as per Bank Statement</h4>
                                    <table class="table  datatable" style="overflow-x: auto; width:100%">
                                        <tr>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-left"></td>
                                            <td class="text-left">Balance In Hand as per Bank Statement</td>
                                            <td class="text-right"><span class="totalBalance"></span></td>
                                        </tr>
                                    </table>
                                    <hr />
                                    <b>
                                        <p class="text-right">Subtotal: Balance In Hand as per Bank Statement = <span
                                                style="border: 1px solid black; padding:10px; font-size: 15px"
                                                class="totalBalance" id="totalBalanceSub"></span> </p>
                                    </b>
                                    <hr />
                                    <h4 class="text-left">Add Uncredited Deposits</h4>
                                    <table id="tbl-bank-recon-uncredit" class="table  datatable"
                                        style="overflow-x: auto; width:100%"></table>
                                    <hr />

                                    <b>
                                        <p class="text-right">Subtotal: Add Uncredited Deposits = <span
                                                style="padding:10px; font-size: 15px"
                                                class="totalAddUncreditDeposit"></span> </p>
                                    </b>
                                    <hr />
                                    <h4 class="text-left">Less Unpresented Cheques</h4>
                                    <table id="tbl-bank-recon-unpresented" class="table  datatable"
                                        style="overflow-x: auto; width:100%"></table>
                                    <hr />

                                    <b>
                                        <p class="text-right">Subtotal: Less Unpresented Cheques = <span
                                                style=" padding:10px; font-size: 15px"
                                                class="totalLessPresentedCheuque"></span> </p>
                                    </b>
                                    <hr />
                                    <table class="table  " style="overflow-x: auto; width:100%">
                                        <tr>
                                            <td class="text-left">
                                                <h4 class="text-left">Balance In Hand as per Bank Account</h4>
                                            </td>
                                            <td class="text-right">
                                                <b>
                                                    <h4 class="text-right" id="totalBalance">0.00 </h4>
                                                </b>
                                            </td>
                                        </tr>
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
        document.getElementById('ddl_month').onchange = function() {
            reconDateController();
        };

        document.getElementById('bank_id').onchange = function() {
            reconDateController();
        };

        document.getElementById('ddl_year').onchange = function() {
            reconDateController();
        };

        function reconDateController() {
            var date = new Date(),
                // y = date.getFullYear(),
                y = $('#ddl_year').val(),
                m = $('#ddl_month').val() - 1;
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);

            // if ($("#bank_id").val() == 7) {
            //     lastDay = 28;
            // } else {
            //     lastDay = (("0" + lastDay.getDate()).slice(-2));
            // }

            lastDay = (("0" + lastDay.getDate()).slice(-2));

            var recon_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" + lastDay;
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + (("0" +
                firstDay.getDate()).slice(-2));
            $("#recon_date").val(recon_date);
            $("#date_from").val(start_date);
            $("#date_to").val(recon_date);
        }

        function filterReport() {

            var form_data = new FormData();
            form_data.append("date_from", $("#date_from").val());
            form_data.append("date_to", $("#date_to").val());

            $.ajax({
                type: 'POST',
                url: '/filterSSTReport',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data.view);

                    $('#tbl_all').html(data.view);

                }
            });
        }

        function getMonthRecon() {


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("bank_id", $("#bank_id").val());
            form_data.append("recon_date", $("#recon_date").val());
            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());

            $("#lbl_recon_date").html($("#recon_date").val());

            $.ajax({
                type: 'POST',
                url: '/get_bank_recon_report',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {
                        $('#tbl-bank-recon-add').html(result.AddCLRDeposit);
                        $('#tbl-bank-recon-less').html(result.LessCLRDeposit);
                        $('#tbl-bank-recon-uncredit').html(result.UncreditDeposit);
                        $('#tbl-bank-recon-unpresented').html(result.LessPresented);
                        $('#div-bank-details').html(result.bank_details);
                        $("#txt_firm_name").html(result.firm_name);


                        var subTotal = result.totalLastReconBalance + result.totalAddCLRDeposit - result
                            .totalLessCLRDeposit;



                        $("#totalAddCLRDeposit").html(numberWithCommas(result.totalAddCLRDeposit.toFixed(2)));
                        $("#totalLessCLRDeposit").html(numberWithCommas(result.totalLessCLRDeposit.toFixed(2)));
                        $(".totalAddUncreditDeposit").html(numberWithCommas((result.totalAddUncreditDeposit
                            .toFixed(2))));
                        $(".totalLessPresentedCheuque").html(numberWithCommas((result.totalLessPresentedCheuque
                            .toFixed(2))));
                        // $("#totalLessPresentedCheuque").html(numberWithCommas(result.totalLessPresentedCheuque.toFixed(2)));
                        $(".lastReconBalance").html(numberWithCommas(result.totalLastReconBalance.toFixed(2)));
                        // $(".openingBalance").html(numberWithCommas(result.OfficeBankOpeningBalanace.toFixed(2)));
                        $(".openingBalance").html(numberWithCommas(result.OfficeBankOpeningBalanace));

                        $("#totalBalanceSub").html(numberWithCommas(subTotal.toFixed(2)));

                        subTotal = subTotal + result.totalAddUncreditDeposit - result.totalLessPresentedCheuque;

                        $("#totalBalance").html(numberWithCommas(subTotal.toFixed(2)));
                    } else {
                        Swal.fire('notice!', result.message, 'warning');
                    }

                }
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
            var url = "{{ route('invoice_report.list', ['dispatchID', 'Status', 1]) }}";

            // url = url.replace('dispatchID', $("#ddl_dispatch").val());
            // url = url.replace('Status', $("#ddl_status").val());

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 25,
                ajax: url,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    // {
                    //   data: 'dispatch_no',
                    //   name: 'dispatch_no',
                    // },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    {
                        data: 'pfee1_inv',
                        name: 'pfee1_inv'
                    },
                    {
                        data: 'pfee2_inv',
                        name: 'pfee2_inv'
                    },
                    {
                        data: 'disb_inv',
                        name: 'disb_inv'
                    },
                    {
                        data: 'sst_inv',
                        name: 'sst_inv'
                    },
                ]
            });
        }

        function printlo() {
            $("#dQuotationInvoice-p").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
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

        function showHideDetails()
        {
            if($('#showdetails').is(':checked') == true)
            {
                $(".datatable").show();
            }
            else
            {
                $(".datatable").hide();
            }
        }

        function reconDateController() {
            var date = new Date(),
                // y = date.getFullYear(),
                y = $('#ddl_year').val(),
                m = $('#ddl_month').val() - 1;
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);

            // if ($("#bank_id").val() == 7) {
            //     lastDay = 28;
            // } else {
            //     lastDay = (("0" + lastDay.getDate()).slice(-2));
            // }

            lastDay = (("0" + lastDay.getDate()).slice(-2));

            var recon_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" + lastDay;
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + (("0" +
                firstDay.getDate()).slice(-2));
            $("#recon_date").val(recon_date);
            $("#date_from").val(start_date);
            $("#date_to").val(recon_date);
        }

        // Get the button:
        let mybutton = document.getElementById("myBtn");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        }

        $(function() {
            reconDateController();
            reloadTable();
        });
    </script>
@endsection
