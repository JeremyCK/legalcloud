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
                                    <h4>Create New Transfer Prof Fee Record</h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('transfer-fee-list') }}">
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
                                            <label class="col-md-4 col-form-label" for="transfer_date">Transfer Date</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transfer_date" id="transfer_date"
                                                    type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_amount">Transfer Total
                                                Amount</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transfer_amount" id="transfer_amount"
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
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_from">Transfer To</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="transfer_to" id="transfer_to">
                                                    <option value="0">-- Select bank account --</option>
                                                    @foreach ($OfficeBankAccount as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                                            ({{ $bankAccount->account_no }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="trx_id" id="trx_id" type="text" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Purpose</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="purpose" name="purpose" row="3"></textarea>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="col-sm-12">
                                        <label for="invoice_search">Add Invoice(s) to Transfer</label>
                                        <select id="invoice_search" class="form-control" multiple="multiple" style="width:100%"></select>
                                    </div>

                                    <div class="col-sm-12">
                                        <a id="btnSave" class="btn btn-success  float-right" href="javascript:void(0)"
                                            onclick="saveTransferFee();">
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

                            {{-- Clean Bootstrap Card for Invoice Search and Transfer List --}}
                            <div class="card mt-4">
                                <div class="card-header bg-light">
                                    <label for="invoice_search" class="mb-0"><strong>Add Invoice(s) to Transfer</strong></label>
                                    <select id="invoice_search" class="form-control mt-2" multiple="multiple" style="width:100%"></select>
                                </div>
                                <div class="card-body">
                                    <h6 class="mb-3"><strong>Selected Invoices for Transfer</strong></h6>
                                    <table id="tbl-transfer-bill-added" class="table table-bordered table-striped yajra-datatable" style="width:100%">
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
                                                <th>Pfee to transfer</th>
                                                <th>SST to transfer</th>
                                                <th>Transferred Bal</th>
                                                <th>Transferred SST</th>
                                                <th>Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div class="mt-2">
                                        <a class="btn btn-danger" href="javascript:void(0)" onclick="deleteAll();">
                                            <i class="fa cil-x"> </i>Delete all
                                        </a>
                                        <a class="btn btn-danger" href="javascript:void(0)" onclick="deleteSelected();">
                                            <i class="fa cil-x"> </i>Delete selected
                                        </a>
                                    </div>
                                </div>
                            </div>




                        </div>
                    </div>

                    {{-- <div id="dAction" class="card" style="display:none">
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
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- <div id="modalTransferFee" class="modal fade" role="dialog">
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
                                <th>No</th>
                                <th>Ref No</th>
                                <th>Client Name</th>
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
    </div> --}}
@endsection

@section('javascript')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        // document.getElementById('ddl_month').onchange = function() {
        //     reconDateController();
        // };

        var transfer_fee_add_list = [];

        $(function() {
            // reconDateController();
            // getTransferList();
            reloadTable();
            // reloadAddTable(); // Removed - this was causing empty AJAX requests
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
                    $.each($("input[name='add_bill']:not(:checked)"), function() {
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

        function maxValue(id, value)
        {
            $("#ban_to_transfer" + id).val(value);
        }

        function validateTransferAmount(invoiceId, maxAmount) {
            var input = $('#ban_to_transfer_' + invoiceId);
            var value = parseFloat(input.val()) || 0;
            var max = parseFloat(maxAmount) || 0;
            
            if (value > max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amount',
                    text: 'Transfer amount cannot exceed the available professional fee amount of ' + max.toFixed(2),
                    confirmButtonText: 'OK'
                });
                input.val(max.toFixed(2));
                value = max;
            }
            
            // Update the total transfer amount
            balUpdate();
        }

        function balUpdate()
        {
            var sum = 0;
            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                sum += parseFloat($("#ban_to_transfer" + itemID).val()) || 0;
            })

            $("#transfer_amount").val(sum.toFixed(2));
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

            // Validate transfer amounts
            var validationError = false;
            var errorMessage = '';
            
            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                var transferAmount = parseFloat($("#ban_to_transfer" + itemID).val()) || 0;
                var maxAmount = parseFloat($("#ban_to_transfer_limt_" + itemID).val()) || 0;
                
                if (transferAmount > maxAmount) {
                    validationError = true;
                    errorMessage += 'Invoice ' + itemID + ': Transfer amount (' + transferAmount.toFixed(2) + ') exceeds available amount (' + maxAmount.toFixed(2) + ')\n';
                }
            });
            
            if (validationError) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                });
                return;
            } 

            $(".highLight_input_red").removeClass("highLight_input_red");

            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                value = parseFloat($("#ban_to_transfer" + itemID).val()) || 0;
                value_limit = parseFloat($("#ban_to_transfer_limt_" + itemID).val()) || 0;
                sst = parseFloat($("#sst_to_transfer_" + itemID).val()) || 0;
                invoice_id = $("#inp_inv_" + itemID).val();

                // Final validation before save
                if (value > value_limit) {
                    $("#ban_to_transfer" + itemID).addClass("highLight_input_red");
                    errorCount += 1;
                }
                
                voucher = {
                    id: itemID,
                    invoice_id: invoice_id,
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
            console.log("AddIntoTransferList called");
            
            // Clear the array first
            transfer_fee_add_list = [];
            
            // Get all checked checkboxes in the modal
            $("input[name='bill']:checked").each(function() {
                var itemID = $(this).val();
                console.log("Adding itemID:", itemID);
                
                // Add to the array
                transfer_fee_add_list.push({
                    id: itemID
                });
            });
            
            console.log("Final transfer_fee_add_list:", transfer_fee_add_list);
            
            // Close the modal - use alternative method since modal() function is not available
            $('#modalTransferFee').hide();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            
            // Manually populate the table with selected items
            populateAddedTable();
            
            // Also refresh the modal table to remove selected items
            reloadTable();
        }

        function populateAddedTable() {
            console.log("populateAddedTable called");
            
            if (transfer_fee_add_list.length === 0) {
                console.log("No items to add");
                return;
            }
            
            // Get the table body
            var tbody = $('#tbl-transfer-bill-added tbody');
            tbody.empty();
            
            // For each selected item, add a row with real data from the modal
            transfer_fee_add_list.forEach(function(item, index) {
                // Get the data from the modal row
                var modalRow = $('#tbl-transfer-bill tbody tr').find('input[name="bill"][value="' + item.id + '"]').closest('tr');
                
                // Extract data from the modal row - fixed column mapping
                var refNo = modalRow.find('td:eq(1)').text().trim();
                var invoiceNo = modalRow.find('td:eq(1)').text().trim();
                var invoiceDate = modalRow.find('td:eq(2)').text().trim();
                var totalAmt = modalRow.find('td:eq(3)').text().trim();
                var collectedAmt = modalRow.find('td:eq(4)').text().trim();
                var pfee = modalRow.find('td:eq(5)').text().trim();
                var sst = modalRow.find('td:eq(6)').text().trim();
                var pfeeToTransfer = modalRow.find('td:eq(7) input').val() || '0.00';
                var sstToTransfer = modalRow.find('td:eq(8)').text().trim();
                
                // Clean up the data - remove commas and convert to numbers
                var pfeeClean = parseFloat(pfee.replace(/,/g, '')) || 0;
                var pfeeToTransferClean = parseFloat(pfeeToTransfer.replace(/,/g, '')) || 0;
                var sstClean = parseFloat(sst.replace(/,/g, '')) || 0;
                var sstToTransferClean = parseFloat(sstToTransfer.replace(/,/g, '')) || 0;
                
                console.log("Extracted data for ID " + item.id + ":", {
                    refNo: refNo,
                    invoiceNo: invoiceNo,
                    pfeeToTransfer: pfeeToTransfer
                });
                
                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + (refNo || 'Invoice ' + item.id) + '</td>' +
                    '<td>' + (invoiceNo || 'INV-' + item.id) + '</td>' +
                    '<td>' + (invoiceDate || new Date().toLocaleDateString()) + '</td>' +
                    '<td class="text-right">' + (totalAmt || '0.00') + '</td>' +
                    '<td class="text-right">' + (collectedAmt || '0.00') + '</td>' +
                    '<td class="text-right">' + (pfee || '0.00') + '</td>' +
                    '<td class="text-right">' + (sst || '0.00') + '</td>' +
                    '<td><input type="number" class="form-control bal_to_transfer" id="ban_to_transfer_' + item.id + '" value="' + pfeeToTransferClean.toFixed(2) + '" max="' + pfeeClean.toFixed(2) + '" onchange="validateTransferAmount(' + item.id + ', ' + pfeeClean + ')" /></td>' +
                    '<td class="text-right">' + (sstToTransfer || '0.00') + '</td>' +
                    '<td class="text-right">0.00</td>' +
                    '<td class="text-right">0.00</td>' +
                    '<td>' + new Date().toLocaleDateString() + '</td>' +
                    '<td style="display:none;"><input type="hidden" name="add_bill" value="' + item.id + '" /></td>' +
                    '<td style="display:none;"><input type="hidden" id="ban_to_transfer_limt_' + item.id + '" value="' + pfeeClean.toFixed(2) + '" /></td>' +
                    '<td style="display:none;"><input type="hidden" id="sst_to_transfer_' + item.id + '" value="' + sstToTransferClean.toFixed(2) + '" /></td>' +
                    '<td style="display:none;"><input type="hidden" id="inp_inv_' + item.id + '" value="' + item.id + '" /></td>' +
                    '</tr>';
                tbody.append(row);
            });
            
            console.log("Table populated with " + transfer_fee_add_list.length + " items");
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
                        // d.recv_start_date = $("#recv_start_date").val(); // Removed as per edit hint
                        // d.recv_end_date = $("#recv_end_date").val(); // Removed as per edit hint
                        // d.branch = $("#branch").val(); // Removed as per edit hint
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
                ]
            });

        }

        function reloadAddTable() {
            console.log("reloadAddTable called");
            console.log("transfer_fee_add_list:", transfer_fee_add_list);
            console.log("transfer_fee_add_list length:", transfer_fee_add_list.length);
            
            var table = $('#tbl-transfer-bill-added').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillAddList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'add';
                        console.log("AJAX data sent:", d);
                        console.log("transfer_list JSON:", d.transfer_list);
                    },
                    error: function(xhr, error, thrown) {
                        console.error("AJAX Error:", error);
                        console.error("Response:", xhr.responseText);
                    }
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

                    var api = this.api(),
                        data;

                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    var monTotal = api
                        .column(9)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    $("#transfer_amount").val(monTotal.toFixed(2));

                    balUpdate();
                }
            });

        }



        function getLastDay() {

        }
    </script>
@endsection
