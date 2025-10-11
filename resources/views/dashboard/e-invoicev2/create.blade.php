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
                                    <h4>New E-invoice Record</h4>
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

                            <form id="form_transfer_fee" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="ref_no">Ref No</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="ref_no" id="ref_no"
                                                    type="text" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="total_amount">Total Amount</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="total_amount" id="total_amount"
                                                    value="0.00" type="number" step="0.01" />
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transaction_id">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transaction_id" id="transaction_id" type="text" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="description">Description</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="batch_status">Batch Status</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="batch_status" id="batch_status">
                                                    <option value="NOTSENT">Not Sent</option>
                                                    <option value="SENT">Sent</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="col-sm-12">
                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                            data-toggle="modal" data-target="#modalTransferFee" class="btn btn-info">Add new
                                            Invoice <i class="cil-zoom"></i> </a>

                                        <a id="btnSave" class="btn btn-success  float-right" href="javascript:void(0)"
                                            onclick="SaveEinvoice();">
                                            <i class="fa cil-save"> </i>Save
                                        </a>
                                    </div>

                                    <div class="col-sm-12">
                                        <hr />
                                    </div>



                                </div>


                                {{-- <div class="row">
                                    <div class="col-sm-12">
                                        <table id="tbl-transferred-fee"
                                            class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Client Name</th>
                                                    <th>Ref No</th>
                                                    <th>Bill No</th>
                                                    <th>Total amt</th>
                                                    <th>Collected amt</th>
                                                    <th>Bal to transfer</th>
                                                    <th>Payment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> --}}



                            </form>

                            {{-- <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Payment Date</th>
                                        <th>Voucher No</th>
                                        <th>Transaction ID</th>
                                        <th>Payee</th>
                                        <th>Desc</th>
                                        <th>Ref No</th>
                                        <th>Amount</th>
                                        <th>Recon Date</th>
                                        <th>Is Recon</th>
                                        <th>Voucher Type</th>
                                        <th>Transaction Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table> --}}

                            <div class="row">
                                <div class="col-12 " style="margin-bottom:20px;">
                                    <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteAll();">
                                        <i class="fa cil-x"> </i>Delete all
                                    </a>

                                    <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteSelected();">
                                        <i class="fa cil-x"> </i>Delete selected
                                    </a>
                                </div>

                                <div class="col-12 ">

                                    <table id="tbl-transfer-bill-added"
                                        class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all"></th>
                                                <th>No</th>
                                                <th>Ref No</th>
                                                <th>Invoice No</th>
                                                <th>Invoice Date</th>
                                                <th>Total Amount</th>
                                                <th>Transfer Amount</th>
                                                <th>SST Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>




                        </div>
                    </div>

                    <div id="dAction" class="card" style="display:none">
                        <div class="card-header">
                            <h4>Voucher</h4>
                        </div>
                        <div class="card-body">
                            <form id="form_voucher" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                                        <input class="form-control" type="hidden" id="selected_id" name="selected_id"
                                            value="">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Item</label>
                                                <input class="form-control" type="hidden" value=""
                                                    id="voucher_id" name="voucher_id">
                                                <input class="form-control" type="hidden" value="" id="status"
                                                    name="status">
                                                <input class="form-control" type="text" value="" id="item"
                                                    name="item" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Available Amount</label>
                                                <input class="form-control" type="text" value="" id="amt"
                                                    name="amt" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-sm-12">
                                                <div class="overlay">
                                                    <i class="fa fa-refresh fa-spin"></i>
                                                </div>
                                                <a id="btnBackToEditMode"
                                                    class="btn btn-sm btn-info float-left mr-1 d-print-none"
                                                    href="javascript:void(0)" onclick="modeController('list');">
                                                    <i class="ion-reply"> </i> Back
                                                </a>
                                                <a id="btnPrint"
                                                    class="btn btn-sm btn-success float-right mr-1 d-print-none"
                                                    href="javascript:void(0)" onclick="updateVoucher(1)">
                                                    <i class="cil-check-alt"></i> Approve</a>

                                                <a id="btnPrint"
                                                    class="btn btn-sm btn-danger float-right mr-1 d-print-none"
                                                    href="javascript:void(0)" onclick="updateVoucher(2)">
                                                    <i class="cil-x"></i> Reject</a>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </form>

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

                    {{-- <div style="height: 600px; overflow: auto">
                        <table id="tbl_bill" class="table  " style="height: 600px; overflow: auto" >
                        </table>
                    </div> --}}
                    {{-- <table id="tbl_bill" class="table  datatable" style="overflow-x: auto; width:100%; max-height:700px">
                    </table> --}}

                    <div class="row">

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_date">Recv Start Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="recv_start_date" id="recv_start_date"
                                        type="date" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_date">Recv End Date</label>
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

                        {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_total_amt1">Transfer Total
                                    Amount</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="transfer_total_amta" id="transfer_total_amta"
                                        value="0.00" type="number" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_from">Transfer From</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="transfer_from" id="transfer_from">
                                        <option value="0">-- Select bank account --</option>
                                        @foreach ($OfficeBankAccount as $bankAccount)
                                            <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                                ({{ $bankAccount->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-sm-12">

                            <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                onclick="reloadTable();">
                                <i class="fa cil-search"> </i>Filter
                            </a>
                        </div>

                    </div>

                    <table id="tbl-transfer-bill" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%;height:300px !important; overflow: auto"">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-bills"></th>
                                <th>No</th>
                                {{-- <th>Client Name</th> --}}
                                {{-- <th>Bill No</th> --}}
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Total amt</th>
                                <th>Collected amt</th>
                                <th>pfee</th>
                                {{-- <th>pfee2</th> --}}
                                <th>sst</th>
                                <th>Pfee to transfer</th>
                                <th>SST to transfer</th>
                                <th>Transferred Bal</th>
                                <th>Transferred SST</th>
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
                    <button type="button" class="btn btn-success float-right" onclick="AddIntoTransferList()">Add
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
    <script>
        // document.getElementById('ddl_month').onchange = function() {
        //     reconDateController();
        // };

        var transfer_fee_add_list = [];
        let selectedInvoices = [];
        let selectedBills = [];

        $(function() {
            // reconDateController();
            // getTransferList();
            reloadTable();
            reloadAddTable();
            reloadTransferredTable();
        });

        function getTransferBillList() {

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
            if (selectedInvoices.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select invoices to delete'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete selected invoices?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('einvoice.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            invoice_ids: selectedInvoices
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Selected invoices have been deleted.',
                                    'success'
                                );
                                // Clear selected invoices after successful deletion
                                selectedInvoices = [];
                                $('#tbl-transfer-bill-added').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete invoices.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting invoices.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function AddIntoTransferListTest() {
            $.each($("input[name='bill']:checked"), function() {
                itemID = $(this).val();
                // itemID = $(this).attr('id');

                // bill = {
                //     id: itemID,
                // };

                
                bill = {
                    itemID,
                };
                transfer_fee_add_list.push(itemID);
            })

            console.log(transfer_fee_add_list);
            reloadAddTable();
            reloadTable();
            closeUniversalModal();
        }


        function maxValue(id, value)
        {
            $("#ban_to_transfer" + id).val(value);
        }

        function balUpdate()
        {
            var sum = 0;
            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                sum += parseFloat($("#ban_to_transfer" + itemID).val());
            })

            $("#transfer_amount").val(sum);
        }

        $(".bal_to_transfer").change(function(){
            alert(3);
            var sum = 0;
            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                sum = parseFloat($("#ban_to_transfer" + itemID).val());
            })

            $("#transfer_amount").val(sum.toFixed(2));
        });

        

        function SaveEinvoice() {

            var voucher_list = [];
            var voucher = {};

            var errorCount = 0;

            if ($("#ref_no").val() == '' || $("#total_amount").val() == 0 || $("#transaction_id").val() == '' || $(
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

            $(".highLight_input_red").removeClass("highLight_input_red");

            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                value = $("#ban_to_transfer" + itemID).val();
                value_limit = $("#ban_to_transfer_limt_" + itemID).val();
                sst = $("#sst_to_transfer_" + itemID).val();

                if (parseFloat(value) > parseFloat(value_limit))
                {
                    $("#ban_to_transfer" + itemID).addClass("highLight_input_red");
                    errorCount+=1;

                }
                
                voucher = {
                    id: itemID,
                    value: value,
                    sst: sst,
                };

                voucher_list.push(voucher);
            })


            if (errorCount > 0)
            {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure the balance to transfer not exceed the limit',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            console.log(voucher_list);

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
            form_data.append("add_bill", JSON.stringify(voucher_list));
            form_data.append("transfer_from", $("#transfer_from").val());
            form_data.append("transfer_to", $("#transfer_to").val());
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("transfer_date", $("#transfer_date").val());
            form_data.append("purpose", $("#purpose").val());

            $("#btnSave").attr("disabled", true);

            $.ajax({
                type: 'POST',
                url: '/createNewTranferFee',
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

                        window.location.href = '/transfer-fee-list';

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

                // bill = {
                //     id: itemID,
                // };

                
                bill = {
                    itemID,
                };
                transfer_fee_add_list.push(itemID);
            })

            console.log(transfer_fee_add_list);
            reloadAddTable();
            reloadTable();
            closeUniversalModal();
        }

        function reloadTransferredTable() {
            var table = $('#tbl-transferred-fee').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'transferred';
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
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'bill_no',
                        name: 'bill_no'
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
                        data: 'pfee',
                        name: 'pfee',
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
                        data: 'bal_to_transfer',
                        name: 'bal_to_transfer',
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

        // Function to handle checkbox state persistence
        function initializeCheckboxHandling(tableId, checkboxClass, selectedArray) {
            // Handle select all checkbox
            $(`#${tableId} #select-all`).on('change', function() {
                var isChecked = $(this).prop('checked');
                $(`#${tableId} .${checkboxClass}`).prop('checked', isChecked);
                
                if (isChecked) {
                    // Add all visible checkboxes to selectedArray
                    $(`#${tableId} .${checkboxClass}:visible`).each(function() {
                        if (!selectedArray.includes($(this).val())) {
                            selectedArray.push($(this).val());
                        }
                    });
                } else {
                    // Remove all visible checkboxes from selectedArray
                    $(`#${tableId} .${checkboxClass}:visible`).each(function() {
                        var index = selectedArray.indexOf($(this).val());
                        if (index > -1) {
                            selectedArray.splice(index, 1);
                        }
                    });
                }
            });

            // Handle individual checkboxes
            $(document).on('change', `#${tableId} .${checkboxClass}`, function() {
                var value = $(this).val();
                if ($(this).prop('checked')) {
                    if (!selectedArray.includes(value)) {
                        selectedArray.push(value);
                    }
                } else {
                    var index = selectedArray.indexOf(value);
                    if (index > -1) {
                        selectedArray.splice(index, 1);
                    }
                }
                
                // Update select all checkbox state
                var totalCheckboxes = $(`#${tableId} .${checkboxClass}:visible`).length;
                var checkedCheckboxes = $(`#${tableId} .${checkboxClass}:visible:checked`).length;
                $(`#${tableId} #select-all`).prop('checked', totalCheckboxes === checkedCheckboxes);
            });
        }

        // Update your reloadTable function
        function reloadTable() {
            var table = $('#tbl-transfer-bill').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'not_transfer';
                        d.recv_start_date = $("#recv_start_date").val();
                        d.recv_end_date = $("#recv_end_date").val();
                        d.branch = $("#branch").val();
                    },
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="bill-checkbox" value="' + data + '">';
                        }
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
                        data: 'invoice_no_v2',
                        name: 'invoice_no_v2'
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
                        data: 'bal_to_transfer_v2',
                        name: 'bal_to_transfer_v2',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'sst_to_transfer',
                        name: 'sst_to_transfer',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_pfee_amt',
                        name: 'transferred_pfee_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_sst_amt',
                        name: 'transferred_sst_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ],
                drawCallback: function(settings) {
                    // Reapply checkbox states after table redraw
                    $('.bill-checkbox').each(function() {
                        if (selectedBills.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                }
            });

            // Initialize checkbox handling for this table
            initializeCheckboxHandling('tbl-transfer-bill', 'bill-checkbox', selectedBills);
        }

        // Update your reloadAddTable function
        function reloadAddTable() {
            var table = $('#tbl-transfer-bill-added').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('EInvoiceSent.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'add';
                    },
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="invoice-checkbox" value="' + data + '">';
                        }
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
                        data: 'invoice_no_v2',
                        name: 'invoice_no_v2'
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
                        data: 'bal_to_transfer_v2',
                        name: 'bal_to_transfer_v2',
                        class: 'text-right',
                    },
                    {
                        data: 'sst_to_transfer',
                        name: 'sst_to_transfer',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_pfee_amt',
                        name: 'transferred_pfee_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_sst_amt',
                        name: 'transferred_sst_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ],
                drawCallback: function(settings) {
                    // Reapply checkbox states after table redraw
                    $('.invoice-checkbox').each(function() {
                        if (selectedInvoices.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                }
            });

            // Initialize checkbox handling for this table
            initializeCheckboxHandling('tbl-transfer-bill-added', 'invoice-checkbox', selectedInvoices);
        }



        function getLastDay() {

        }

        $(document).ready(function() {
            // Initialize tables
            reloadTable();
            reloadAddTable();
        });

        function deleteInvoice(invoiceId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this invoice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('einvoice.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            invoice_ids: [invoiceId]
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Invoice has been deleted.',
                                    'success'
                                );
                                $('#tbl-transfer-bill-added').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete invoice.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting invoice.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
