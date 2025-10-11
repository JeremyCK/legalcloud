<style>
    table thead,
    table tfoot {
        position: sticky;
    }

    table thead {
        inset-block-start: 0;
        /* "top" */
    }

    table tfoot {
        inset-block-end: 0;
        /* "bottom" */
    }
</style>
@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">



                    <div id="dSummaryReport" class="card">
                        <div class="card-header">

                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <h4>Staff Bonus Estimation Report </h4>
                                </div>

                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row no-print">

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Staff</label>
                                            <select class="form-control" name="staff_id" id="staff_id">
                                                <option value="{0">-- Staff -- </option>
                                                @foreach ($staffs as $staff)
                                                    <option value="{{ $staff->id }}">{{ $staff->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Status</label>
                                            <select class="form-control" name="claimed" id="claimed">
                                                <option value="0">Not Claimed</option>
                                                <option value="1">Claimed</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Ready for claim</label>
                                            <select class="form-control" name="claimed" id="claimed">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Recon Date</label>
                                            <select class="form-control" name="recon_date" id="recon_date">
                                                @foreach ($recon_date as $date)
                                                    <option value="{{ $date->recon_date }}">{{ $date->recon_date }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div> --}}


                                <div class="col-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="getReport()">
                                        <i class="fa cil-search"> </i>View
                                    </a>

                                    <a class="btn btn-lg btn-success  " href="javascript:void(0)" onclick="exportTableToExcel();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                      </a>
                                </div>

                                {{-- <div class="col-6">
                                    <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)" onclick="exportTableToExcel();">
                                      <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>
                                  </div> --}}

                                <div class="col-sm-12">
                                    <hr />
                                </div>




                                {{-- <div class="col-sm-12">
                                    <hr />
                                </div> --}}

                                {{-- <div class="col-sm-12">
                                    <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                        onclick="exportTableToExcel();">
                                        <i class="fa fa-file-excel-o"> </i>Download as Excel
                                    </a>

                                </div> --}}

                            </div>
                            <br />
                            <div class="row">
                                {{-- <div class="col-12 float-center">
                                    <h2 class="text-center">L H YEO & CO</h2>
                                    <h4 class="text-center">Bank Reconciliation As at <span id="lbl_recon_date"></span>
                                    </h4>

                                    <hr />
                                    <hr />
                                </div> --}}


                                <div class="col-12 " id="tbl-bank-recon-add">

                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalBonusDetail" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 900px !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="div-bonus-details" class="row">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div id="modalBonusClaimNotes" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 900px !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        A. The Estimated Bonus will be auto adjusted after deduction of the Uncollected Sum
B. Bonus is payable every June & Dec subjected to following:
1. All claims made before 30th June will be paid on 31st Dec
2. All claims made before 31st Dec will be paid on 30th June
3. Upon resignation / termination of employment, closed files claim will be forfeited, 
active files claim will be transferred to staff that take over
4. KIV /Defective files not causing deactivation / suspension of Bank Panelship
5. No Complain to Bar Council 
6. No public complain in google review or any social media 
C. 2% claim will be rewarded based on KPI below:
1. PV cases (S&P stamped within 10 days from the file opened & upload to LC)
2. Represented cases (S&P stamped within 21 days from the file opened & upload to 
LC)
3. Loan cases (received Bank executed loan agreements within 14 days after the S&P 
stamped & upload to LC our acknowledgement of the letter from bank returning 
executed docs)
D. 3% claim will be rewarded based on KPI below: - On files completed within ACTUAL Completion Date (not extended completion date free 
of interest)
1. SPA cases (to upload in LC proof of completion date, delivery of keys, BPP released, 
legal fees all paid & title registered)
2. Loan cases (to upload in LC proof of completion date, all loan sum disbursed, legal 
fees all paid, title registered & acknowledgement from Bank confirm received 
original title / agreements)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div id="modalBillDetail" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 900px !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table id="tbl_bill" class="table  datatable" style="overflow-x: auto; width:100%">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-success float-right" onclick="updateCompletionDate()">Assign
                      <div class="overlay" style="display:none">
                          <i class="fa fa-refresh fa-spin"></i>
                      </div>
                  </button> --}}
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

        function exportTableToExcel() {
            var tab_text = "<table border='2px'><tr bgcolor='black' style='color:white'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('tbl-bonus-yadra'); // id of table

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
                sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
            } else //other browser not tested on IE 11
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

            return (sa);
        }

        function loadCaseQuotation(bill_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/loadCaseQuotation/' + bill_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#tbl_bill').html(result.view);
                }
            });
        }

        function loadBonusDetails(bill_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/loadBonusDetails/' + bill_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    $('#div-bonus-details').html(result.view);
                }
            });
        }

        function getReport() {


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("claimed", $("#claimed").val());

            $.ajax({
                type: 'POST',
                url: '/getBonusReportEstimate/' + $('#staff_id').val(),
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);

                    $('#tbl-bank-recon-add').html(result.bonusList);
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

        // function exportTableToExcel() {
        //     var downloadLink;
        //     var dataType = 'application/vnd.ms-excel';
        //     var tableSelect = document.getElementById('tbl-summary-report');
        //     var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        //     filename = 'invoice_report' + Date.now();

        //     // Specify file name
        //     filename = filename ? filename + '.xls' : 'excel_data.xls';

        //     // Create download link element
        //     downloadLink = document.createElement("a");

        //     document.body.appendChild(downloadLink);

        //     if (navigator.msSaveOrOpenBlob) {
        //         var blob = new Blob(['\ufeff', tableHTML], {
        //             type: dataType
        //         });
        //         navigator.msSaveOrOpenBlob(blob, filename);
        //     } else {
        //         // Create a link to the file
        //         downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

        //         // Setting the file name
        //         downloadLink.download = filename;

        //         //triggering the function
        //         downloadLink.click();
        //     }
        // }
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
            reloadTable();
        });
    </script>
@endsection
