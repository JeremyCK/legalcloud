@extends('dashboard.base')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">


                    <div id="dList" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <h4>Invoice Record</h4>
                                </div>

                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('einvoice-list') }}">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <form id="form_einvoice" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_date">E-Invoice
                                                Date</label>
                                            <div class="col-md-8">
                                                <input class="form-control" value="{{ $EInvoiceMain->einvoice_date }}"
                                                    name="einvoice_date" id="einvoice_date" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="ref_no">Ref No</label>
                                            <div class="col-md-8">
                                                <input class="form-control" value="{{ $EInvoiceMain->ref_no }}"
                                                    name="ref_no" id="ref_no" type="text" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="total_amount">Total Amount</label>
                                            <div class="col-md-8">
                                                {{-- <input class="form-control" name="total_amount" id="total_amount"
                                                    value="0.00" type="number" step="0.01" readonly/> --}}
                                                <input class="form-control" value="{{ $EInvoiceMain->total_amount }}"
                                                    name="total_amount" id="total_amount" type="text" readonly />
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transaction_id">Transaction
                                                ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" value="{{ $EInvoiceMain->transaction_id }}"
                                                    name="transaction_id" id="transaction_id" type="text" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="branch_id">Branch ID</label>
                                            <div class="col-md-8">
                                                <select class="form-control" id="branch_id" name="branch_id">
                                                    <option value="0"> -- All branch -- </option>
                                                    @foreach ($Branchs as $index => $branch)
                                                        <option value="{{ $branch->id }}"
                                                            @if ($EInvoiceMain->branch_id == $branch->id) selected @endif>
                                                            {{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="batch_status">Batch Status</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="batch_status" id="batch_status">
                                                    <option value="NOTSENT"
                                                        @if ($EInvoiceMain->batch_status == 'SQL') selected @endif>Sent to SQL</option>
                                                    <option value="SENT"
                                                        @if ($EInvoiceMain->batch_status == 'LHDN') selected @endif>Submitted to LHDN</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="description">Description</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="description" name="description" rows="3">{{ $EInvoiceMain->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">

                                        <a id="btnSave" class="btn btn-success  float-right" href="javascript:void(0)"
                                            onclick="SaveEinvoice();">
                                            <i class="fa cil-save"> </i>Save
                                        </a>
                                    </div>
                                </div>

                            </form>



                            <div class="col-12 ">
                                <hr />
                            </div>

                            <div class="col-12 " style="margin-bottom:20px;">
                                {{-- <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteAll();">
                                    <i class="fa cil-x"> </i>Delete all
                                </a> --}}

                                {{-- <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteEinvoiceListSelected();">
                                    <i class="fa cil-x"> </i>Delete selected
                                </a> --}}

                                <div class="btn-group normal-edit-mode">
                                    <button type="button" class="btn btn-success btn-flat dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="cil-settings"></i> Action
                                    </button>
                                    <div class="dropdown-menu" style="padding: 0px; margin: 0px;">
                                        <a class="dropdown-item btn-success" href="javascript:void(0)"
                                            onclick="updateInProgress(89790, 'APPROVE');" style="color:white;margin:0"><i
                                                style="margin-right: 10px;" class="fa fa-file-excel-o"></i>Generate SQL
                                            Excel Template</a>
                                        <div class="dropdown-divider" style="margin:0"></div>
                                        <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                            onclick="updateInProgress(89790, 'INPROGRESS');"
                                            style="color:white;margin:0"><i style="margin-right: 10px;"
                                                class="cil-running"></i>Sent to SQL Server</a>
                                        <div class="dropdown-divider" style="margin:0"></div>
                                        <a class="dropdown-item btn-info" target="_blank" href="/voucher/89790/edit"
                                            style="color:white;margin:0"><i style="margin-right: 10px;"
                                                class="cil-pencil"></i>Send to LHDN</a>

                                        <div class="dropdown-divider" style="margin:0"></div>
                                        <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                            onclick="deleteEinvoiceListSelected()" style="color:white;margin:0"><i
                                                style="margin-right: 10px;" class="cil-x"></i>Delete selected</a>
                                    </div>
                                </div>

                                <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                    data-toggle="modal" data-target="#modalTransferFee"
                                    class="btn btn-info float-right">Add new Invoice <i class="cil-plus"></i> </a>
                            </div>


                            <input class="form-check-input " onchange="checkAllController()" type="checkbox"
                                value="0" name="checkall" id="checkall" >
                            <label class="form-check-label" for="checkall">Check All</label>
                            <hr />

                            <table id="tbl-transfer-bill-added" class="table table-bordered table-striped yajra-datatable"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Ref No</th>
                                        <th>Invoice No</th>
                                        <th>Invoice Date</th>
                                        <th>Total amt</th>
                                        <th>Collected amt</th>
                                        <th>pfee</th>
                                        <th>Status</th>
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="modalTransferFee" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 100% !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_date">Start Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="recv_start_date" id="recv_start_date"
                                        type="date" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_date">End Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="recv_end_date" id="recv_end_date"
                                        type="date" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_from">Branch</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="branch" id="branch">
                                        <option value="0">-- Select Branch --</option>
                                        @foreach ($Branchs as $branch)
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
                        </div>

                    </div>

                    <table id="tbl-transfer-bill" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%;height:300px !important; overflow: auto">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Ref No</th>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Total amt</th>
                                <th>Collected amt</th>
                                <th>pfee</th>
                                <th>sst</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right" onclick="addEinvoice()">Add
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('javascript')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
    <script src="{{ asset('js/jquery.print.js') }}"></script>
    <script>
        // document.getElementById('ddl_month').onchange = function() {
        //     reconDateController();
        // };

        function checkAllController() {
            $('.invoice_all').prop('checked', $('#checkall').is(':checked'));
        }

        function PrintAreaQuotation() {


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

        var transfer_fee_add_list = [];

        $(function() {
            // reconDateController();
            // getTransferList();

            //reloadTable();
            reloadAddTable();
            // reloadTransferredTable();
            // loadTransferredBill();
        });

        function getTransferBillList() {

        }

        function addEinvoice() {

            var voucher_list = [];
            var voucher = {};
            var errorCount = 0;

            $.each($("input[name='bill']:checked"), function() {
                itemID = $(this).val();

                voucher = {
                    id: itemID
                };

                voucher_list.push(voucher);
            })

            if (voucher_list.length <= 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'No bill selected',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            var form_data = new FormData();
            form_data.append("add_invoice", JSON.stringify(voucher_list));

            $("#btnSave").attr("disabled", true);

            $.ajax({
                type: 'POST',
                url: '/AddInvoiceIntoEInvoice/{{ $EInvoiceMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        toastController('Invoice Added');
                        reloadAddTable();
                        closeUniversalModal();
                    } else {
                        $("#btnSave").attr("disabled", false);
                        // Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }

        function deleteEinvoiceListSelected() {

            var voucher_list = [];
            var voucher = {};
            var errorCount = 0;

            $.each($("input[name='add_bill']:checked"), function() {
                itemID = $(this).val();

                voucher = {
                    id: itemID
                };

                voucher_list.push(voucher);
            })

            if (voucher_list.length <= 0) {
                return;
            }

            var form_data = new FormData();
            form_data.append("delete_invoice", JSON.stringify(voucher_list));

            $.ajax({
                type: 'POST',
                url: '/DeleteInvoiceFromEInvoice/{{ $EInvoiceMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        toastController('Invoice updated');
                        reloadAddTable();
                        closeUniversalModal();
                    } else {
                        $("#btnSave").attr("disabled", false);
                        // Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }

        function deleteAll() {
            Swal.fire({
                icon: 'warning',
                text: 'Delete all payments?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    transfer_fee_add_list = [];

                    reloadAddTable();
                    reloadTable();
                }
            })
        }

        function deleteSelected() {
            Swal.fire({
                icon: 'warning',
                text: 'Delete selected payments?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    transfer_fee_add_list = [];
                    $.each($("input[name='add_bill'](:checked)"), function() {
                        itemID = $(this).val();
                        bill = {
                            id: itemID,
                        };
                        transfer_fee_add_list.push(bill);
                    })

                    console.log(transfer_fee_add_list);
                    reloadAddTable();
                    reloadTable();
                }
            })
        }


        function deleteTransferSelected() {
            Swal.fire({
                icon: 'warning',
                text: 'Delete selected payments?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    transfer_fee_add_list = [];
                    $.each($("input[name='trans_bill']:not(:checked)"), function() {
                        itemID = $(this).val();
                        bill = {
                            id: itemID,
                        };
                        transfer_fee_add_list.push(bill);
                    })




                    transfer_fee_delete_list = [];

                    $.each($("input[name='trans_bill']:checked"), function() {
                        itemID = $(this).val();
                        bill = {
                            id: itemID,
                        };
                        transfer_fee_delete_list.push(bill);
                    })


                    var form_data = new FormData();
                    form_data.append("delete_bill", JSON.stringify(transfer_fee_delete_list));

                    $.ajax({
                        type: 'POST',
                        url: '/deleteTransferFee/{{ $EInvoiceMain->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {

                                Swal.fire(
                                    'Success!', 'Record deleted',
                                    'success'
                                )

                                location.reload();

                                // reloadAddTable();
                                // reloadTable();
                                // reloadTransferredTable();

                                // window.location.href = '/transfer-fee-list';

                                // $("#tbl_bill").html(result.billList);
                            } else {
                                // Swal.fire('notice!', result.message, 'warning');
                            }
                        }
                    });

                    console.log(transfer_fee_delete_list);

                }
            })
        }

        function balUpdate() {
            var sum = 0;
            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                sum += parseFloat($("#ban_to_transfer" + itemID).val());
            })

            $("#transfer_amount").val(sum);
        }

        function saveTransferFee() {

            var voucher_list = [];
            var voucher = {};
            var errorCount = 0;

            if ($("#transfer_from").val() == 0 || $("#transfer_to").val() == 0 || $("#trx_id").val() == '' || $(
                    "#transfer_date").val() == '') {

                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure all mandatory fields fill',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            $test = 0;

            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                value = $("#ban_to_transfer" + itemID).val();
                value_limit = $("#ban_to_transfer_limt_" + itemID).val();
                sst = $("#sst_to_transfer_" + itemID).val();

                if (parseFloat(value) > parseFloat(value_limit)) {
                    $("#ban_to_transfer" + itemID).addClass("highLight_input_red");
                    errorCount += 1;

                }

                $test += value;
                voucher = {
                    id: itemID,
                    value: value,
                    sst: sst,
                };

                voucher_list.push(voucher);
            })

            if (errorCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure the balance to transfer not exceed the limit',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }


            // if (voucher_list.length <= 0) {
            //     Swal.fire({
            //         icon: 'warning',
            //         text: 'No bill selected',
            //         confirmButtonText: `Yes`,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //     });
            //     return;
            // }

            var form_data = new FormData();
            form_data.append("add_bill", JSON.stringify(voucher_list));
            form_data.append("transfer_from", $("#transfer_from").val());
            form_data.append("transfer_to", $("#transfer_to").val());
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("transfer_date", $("#transfer_date").val());
            form_data.append("purpose", $("#purpose").val());

            $("#btnSave").attr("disabled", true);

            $.ajax({
                type: 'POST',
                url: '/updateTranferFee/{{ $EInvoiceMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        Swal.fire(
                            'Success!', 'Record created',
                            'success'
                        )


                        location.reload();

                        // reloadAddTable();
                        // reloadTable();
                        // reloadTransferredTable();

                        // alert(result.total_amount);

                        $("#transfer_amount").val(result.total_amount);

                        // window.location.href = '/transfer-fee-list';

                        // $("#tbl_bill").html(result.billList);
                    } else {
                        $("#btnSave").attr("disabled", false);
                        // Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }


        function reconDateController() {
            var date = new Date(),
                y = date.getFullYear(),
                m = $('#ddl_month').val() - 1;
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);

            if ($("#bank_id").val() == 7) {
                lastDay = 28;
            } else {
                lastDay = (("0" + lastDay.getDate()).slice(-2));
            }

            var recon_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" + lastDay;
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + (("0" +
                firstDay.getDate()).slice(-2));
            $("#recon_date").val(recon_date);
            $("#date_from").val(start_date);
            $("#date_to").val(recon_date);
        }

        function checkAll(bool) {
            $.each($("input[name='voucher']"), function() {
                template_id = [];

                itemID = $(this).attr('id');
                console.log(itemID)
                $("#" + itemID).prop('checked', bool);

            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function updateRecon($type) {

            var voucher_list = [];
            var voucher = {};

            Swal.fire({
                icon: 'warning',
                text: 'Update this record?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.each($("input[name='voucher']:checked"), function() {
                        itemID = $(this).val();
                        value = $("#selected_amt_" + itemID).val();

                        voucher = {
                            id: itemID,
                            value: value,
                        };

                        voucher_list.push(voucher);
                    })

                    var form_data = new FormData();
                    form_data.append("voucher_list", JSON.stringify(voucher_list));
                    form_data.append("type", $type);
                    form_data.append("bank_id", $("#bank_id").val());
                    form_data.append("recon_date", $("#recon_date").val());
                    form_data.append("month", $("#ddl_month").val());
                    form_data.append("year", $("#ddl_year").val());

                    $.ajax({
                        type: 'POST',
                        url: '/updateRecon',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {
                                toastController('Record updated');
                                // updateReconValue(result);
                                reloadTable();
                            } else {
                                Swal.fire('notice!', result.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function getTransferList() {
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

            $.ajax({
                type: 'POST',
                url: '/getTransferList',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        $("#tbl_bill").html(result.billList);
                    } else {
                        // Swal.fire('notice!', result.message, 'warning');
                    }
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

            $.ajax({
                type: 'POST',
                url: '/getMonthRecon',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {
                        $("#total_add_clr_deposit").val(numberWithCommas(result.totalAddCLRDeposit));
                        $("#total_less_clr_deposit").val(numberWithCommas(result.totalLessCLRDeposit));
                    } else {
                        Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }

        function AddIntoTransferList() {
            $.each($("input[name='bill']:checked"), function() {
                itemID = $(this).val();
                // itemID = $(this).attr('id');

                bill = {
                    id: itemID,
                };
                transfer_fee_add_list.push(bill);
            })

            console.log(transfer_fee_add_list);
            reloadAddTable();
            reloadTable();
            closeUniversalModal();
        }

        function loadTransferredBill() {

            var form_data = new FormData();
            form_data.append("transfer_list", JSON.stringify(transfer_fee_add_list));
            form_data.append("type", 'transferred');
            form_data.append("transaction_id", {{ $EInvoiceMain->id }});

            $.ajax({
                type: 'POST',
                url: '/getTransferFeeBillListV2',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    $('#tbl-transferred-print').html(data.view);
                }
            });
        }

        // function reloadTransferredTable() {
        //     var table = $('#tbl-transferred-fee').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         pageLength: 200,
        //         destroy: true,
        //         ajax: {
        //             url: "{{ route('EInvoiceSent.list') }}",
        //             data: function(d) {
        //                 d.transfer_list = JSON.stringify(transfer_fee_add_list);
        //                 d.type = 'transferred';
        //                 d.transaction_id = {{ $EInvoiceMain->id }};
        //             },
        //         },
        //         columns: [{
        //                 data: 'DT_RowIndex',
        //                 name: 'DT_RowIndex'
        //             }, {
        //                 data: 'action',
        //                 className: 'text-center no-print',
        //                 name: 'action'
        //             },
        //             {
        //                 data: 'case_ref_no',
        //                 name: 'case_ref_no'
        //             },
        //             {
        //                 data: 'invoice_no',
        //                 name: 'invoice_no'
        //             },
        //             {
        //                 data: 'invoice_date',
        //                 name: 'invoice_date'
        //             },
        //             {
        //                 data: 'total_amt_inv',
        //                 name: 'total_amt_inv',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },
        //             {
        //                 data: 'collected_amt',
        //                 name: 'collected_amt',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },

        //             {
        //                 data: 'pfee_sum',
        //                 name: 'pfee_sum',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },
        //             {
        //                 data: 'sst_inv',
        //                 name: 'sst_inv',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },
        //             {
        //                 data: 'bal_to_transfer_v3',
        //                 name: 'bal_to_transfer_v3',
        //                 render: $.fn.dataTable.render.number(',', '.', 2),
        //                 class: 'text-right',
        //             },
        //             {
        //                 data: 'sst_to_transfer',
        //                 name: 'sst_to_transfer',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },
        //             {
        //                 data: 'transfer_amount',
        //                 name: 'transfer_amount',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },
        //             {
        //                 data: 'sst_amount',
        //                 name: 'sst_amount',
        //                 class: 'text-right',
        //                 render: $.fn.dataTable.render.number(',', '.', 2)
        //             },
        //             {
        //                 data: 'payment_receipt_date',
        //                 name: 'payment_receipt_date'
        //             },
        //         ],
        //         drawCallback: function(settings) {

        //             // var api = this.api(),
        //             //     data;

        //             // var intVal = function(i) {
        //             //     return typeof i === 'string' ?
        //             //         i.replace(/[\$,]/g, '') * 1 :
        //             //         typeof i === 'number' ?
        //             //         i : 0;
        //             // };

        //             // var span_total_amount = api.column(5).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_collected_amount = api.column(6).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_total_pfee = api.column(7).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_total_sst = api.column(8).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_total_pfee_to_transfer = api.column(9).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_total_sst_to_transfer = api.column(10).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_total_transferred_pfee = api.column(11).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);
        //             // var span_total_transferred_sst = api.column(12).data().reduce(function(a, b) {
        //             //     return intVal(a) + intVal(b);
        //             // }, 0);


        //             // $("#span_total_amount").html(numberWithCommas(span_total_amount.toFixed(2)));
        //             // $("#span_collected_amount").html(numberWithCommas(span_collected_amount.toFixed(2)));
        //             // $("#span_total_pfee").html(numberWithCommas(span_total_pfee.toFixed(2)));
        //             // $("#span_total_sst").html(numberWithCommas(span_total_sst.toFixed(2)));
        //             // $("#span_total_pfee_to_transfer").html(numberWithCommas(span_total_pfee_to_transfer.toFixed(
        //             //     2)));
        //             // $("#span_total_sst_to_transfer").html(numberWithCommas(span_total_sst_to_transfer.toFixed(
        //             //     2)));
        //             // $("#span_total_transferred_pfee").html(numberWithCommas(span_total_transferred_pfee.toFixed(
        //             //     2)));
        //             // $("#span_total_transferred_sst").html(numberWithCommas(span_total_transferred_sst.toFixed(
        //             //     2)));


        //         }
        //     });




        // }


        function maxValue(id, value) {
            $("#ban_to_transfer" + id).val(value);
        }

        function reloadTable() {
            var table = $('#tbl-transfer-bill').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('EInvoiceSent.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.recv_start_date = $("#recv_start_date").val();
                        d.recv_end_date = $("#recv_end_date").val();
                        d.branch = $("#branch").val();
                        d.transaction_id = {{ $EInvoiceMain->id }};
                    },
                },
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        name: 'action'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    // {
                    //     data: 'client_name',
                    //     name: 'client_name'
                    // },
                    // {
                    //     data: 'bill_no',
                    //     name: 'bill_no'
                    // },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'total_amt_inv',
                        name: 'total_amt_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'pfee_sum',
                        name: 'pfee_sum',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ]
            });

        }

        function reloadAddTable() {
            var table = $('#tbl-transfer-bill-added').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 200,
                ajax: {
                    url: "{{ route('EInvoiceSent.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'sent';
                        d.id = {{ $EInvoiceMain->id }};
                    },
                },
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        name: 'action',
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    // {
                    //     data: 'client_name',
                    //     name: 'client_name'
                    // },
                    // {
                    //     data: 'bill_no',
                    //     name: 'bill_no'
                    // },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'total_amt_inv',
                        name: 'total_amt_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'pfee_sum',
                        name: 'pfee_sum',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    {
                        data: 'einvoice_status',
                        name: 'einvoice_status',
                        class: 'text-right'
                    },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
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

                    var monTotal = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);


                    $("#total_amount").val(monTotal.toFixed(2));
                    $("#total_amount").val(thousandSeparator($("#total_amount").val()));
                }
            });

        }



        function getLastDay() {

        }
    </script>
@endsection
