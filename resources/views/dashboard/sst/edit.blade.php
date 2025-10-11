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
                                    <h4>Edit SST Records</h4>
                                </div>
                                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('sst.list') }}">
                                        <i class="cil-arrow-left"> </i>Back to list
                                      </a>
                                </div>
                               
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <form method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="payment_date">Payment Date</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="payment_date" id="payment_date"
                                                    value="{{ $SSTMain->payment_date }}" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="amount">Total Paid Amount</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transfer_amount_hidden"
                                                    value="{{ $SSTMain->amount }}"
                                                    id="transfer_amount_hidden" value="0.00" type="hidden" />
                                                <input class="form-control" name="transfer_amount"
                                                    value="{{ $SSTMain->amount }}" id="transfer_amount"
                                                    value="0.00" type="number" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="trx_id" id="trx_id" type="text"
                                                    value="{{ $SSTMain->transaction_id }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Remark</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="remark" name="remark" row="3">{{ $SSTMain->remark }}</textarea>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-12">

                                        <a class="btn btn-success  float-right" href="javascript:void(0)"
                                            onclick="saveSST();">
                                            <i class="fa cil-save"> </i>Save
                                        </a>
                                    </div>


                                    <div class="col-sm-12">
                                        <hr />
                                    </div>



                                </div>


                                <div class="row">
                                    <div class="col-12 ">
                                        <h4>Transferred List</h4>
                                    </div>
                                    <div class="col-12 ">
                                        <hr />
                                    </div>

                                    <div class="col-12 " style="margin-bottom:20px;">

                                        <a class="btn btn-danger " href="javascript:void(0)"
                                            onclick="deleteTransferSelected();">
                                            <i class="fa cil-x"> </i>Delete selected
                                        </a>

                                    </div>
                                    <div class="col-sm-12">
                                        <table id="tbl-transferred-fee"
                                            class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Ref No</th>
                                                    <th>Client Name</th>
                                                    <th>Bill No</th>
                                                    <th>Invoice No</th>
                                                    <th>Total amt</th>
                                                    <th>Collected amt</th>
                                                    <th>pfee1</th>
                                                    <th>pfee2</th>
                                                    <th>sst</th>
                                                    {{-- <th>Bal to transfer</th> --}}
                                                    <th>Payment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            
                                            <tfoot style="background-color: black;color:white">
                                                <th colspan="5" class="text-left">Total </th>
                                                <th><span id="span_total_amount">0.00</span></th>
                                                <th><span id="span_collected_amount">0.00</span> </th>
                                                <th><span id="span_total_pfee1">0.00</span> </th>
                                                <th><span id="span_total_pfee2">0.00</span> </th>
                                                <th><span id="span_total_sst">0.00</span> </th>
                                                <th></th>
                                                {{-- <th class="text-left"> </th> --}}
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>



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

                            <div class="col-12 ">
                                <hr />
                            </div>

                            <div class="col-12 ">
                                <h4>New transfer list</h4>
                            </div>



                            <div class="col-12 ">
                                <hr />
                            </div>

                            <div class="col-12 " style="margin-bottom:20px;">
                                <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteAll();">
                                    <i class="fa cil-x"> </i>Delete all
                                </a>

                                <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteSelected();">
                                    <i class="fa cil-x"> </i>Delete selected
                                </a>

                                <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                    data-toggle="modal" data-target="#modalTransferFee"
                                    class="btn btn-info float-right">Add new payment <i class="cil-plus"></i> </a>
                            </div>

                            <table id="tbl-transfer-bill-added" class="table table-bordered table-striped yajra-datatable"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Ref No</th>
                                        <th>Client Name</th>
                                        <th>Bill No</th>
                                        <th>Invoice No</th>
                                        <th>Total amt</th>
                                        <th>Collected amt</th>
                                        <th>pfee1</th>
                                        <th>pfee2</th>
                                        <th>sst</th>
                                        {{-- <th>Bal to transfer</th> --}}
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
                                    <input class="form-control" name="transfer_total_amt1" id="transfer_total_amt1"
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
                                <th>Bill No</th>
                                <th>Invoice No</th>
                                <th>Total amt</th>
                                <th>Collected amt</th>
                                <th>pfee1</th>
                                <th>pfee2</th>
                                <th>sst</th>
                                {{-- <th>Bal to transfer</th> --}}
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
                        url: '/deleteSST/{{ $SSTMain->id }}',
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

        function saveSST() {

            var voucher_list = [];
            var voucher = {};

            if ($("#trx_id").val() == '' || $(
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
                value = $("#selected_amt_" + itemID).val();
                $test += value;
                voucher = {
                    id: itemID,
                    value: value,
                };

                voucher_list.push(voucher);
            })


            var form_data = new FormData();
            form_data.append("add_bill", JSON.stringify(voucher_list));
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("payment_date", $("#payment_date").val());
            form_data.append("remark", $("#remark").val());

            $.ajax({
                type: 'POST',
                url: '/updateSST/{{ $SSTMain->id }}',
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

                        voucher = {
                            id: itemID,
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

        function reloadTransferredTable() {
            var table = $('#tbl-transferred-fee').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 500,
                destroy: true,
                ajax: {
                    url: "{{ route('sstInvoiceList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'transferred';
                        d.transaction_id = {{ $SSTMain->id }};
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
                        data: 'invoice_no',
                        name: 'invoice_no'
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
                        data: 'pfee1_inv',
                        name: 'pfee1_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'pfee2_inv',
                        name: 'pfee2_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    
                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'bal_to_transfer',
                    //     name: 'bal_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
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

                    var span_total_amount = api.column(5).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_collected_amount = api.column(6).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_pfee1 = api.column(7).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_pfee2 = api.column(8).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_sst = api.column(9).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    // var span_total_pfee_to_transfer = api.column(8).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_sst_to_transfer = api.column(9).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_transferred_pfee = api.column(10).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_transferred_sst = api.column(11).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);


                    $("#span_total_amount").html(numberWithCommas(span_total_amount.toFixed(2)));
                    $("#span_collected_amount").html(numberWithCommas(span_collected_amount.toFixed(2)));
                    $("#span_total_pfee1").html(numberWithCommas(span_total_pfee1.toFixed(2)));
                    $("#span_total_pfee2").html(numberWithCommas(span_total_pfee2.toFixed(2)));
                    $("#span_total_sst").html(numberWithCommas(span_total_sst.toFixed(2)));
                    // $("#span_total_pfee_to_transfer").html(numberWithCommas(span_total_pfee_to_transfer.toFixed(
                    //     2)));
                    // $("#span_total_sst_to_transfer").html(numberWithCommas(span_total_sst_to_transfer.toFixed(
                    //     2)));
                    // $("#span_total_transferred_pfee").html(numberWithCommas(span_total_transferred_pfee.toFixed(
                    //     2)));
                    // $("#span_total_transferred_sst").html(numberWithCommas(span_total_transferred_sst.toFixed(
                    //     2)));

                        


                    // transfer_amt_hidden = parseFloat($("#transfer_amount_hidden").val());
                    console.log(span_total_amount);

                    // transfer_amount = monTotal + transfer_amt_hidden;
                    // alert($("#transfer_amount_hidden").val());
                    // $("#transfer_amount").val(transfer_amount);
                }
            });

        }


        function reloadTable() {
            var table = $('#tbl-transfer-bill').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 500,
                destroy: true,
                ajax: {
                    url: "{{ route('sstInvoiceList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'not_transfer';
                        d.recv_start_date = $("#recv_start_date").val();
                        d.recv_end_date = $("#recv_end_date").val();
                        d.branch = $("#branch").val();
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
                        data: 'invoice_no',
                        name: 'invoice_no'
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
                        data: 'pfee1_inv',
                        name: 'pfee1_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'pfee2_inv',
                        name: 'pfee2_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    
                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'bal_to_transfer',
                    //     name: 'bal_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
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
                pageLength: 500,
                destroy: true,
                ajax: {
                    url: "{{ route('sstInvoiceAddList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'add';
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
                        data: 'invoice_no',
                        name: 'invoice_no'
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
                        data: 'pfee1_inv',
                        name: 'pfee1_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'pfee2_inv',
                        name: 'pfee2_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    
                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'bal_to_transfer',
                    //     name: 'bal_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
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

                    console.log(monTotal);

                    transfer_amt_hidden = parseFloat($("#transfer_amount_hidden").val());
                    console.log(transfer_amt_hidden);

                    transfer_amount = monTotal + transfer_amt_hidden;
                    // alert($("#transfer_amount_hidden").val());
                    $("#transfer_amount").val(transfer_amount);
                }
            });

        }



        function getLastDay() {

        }
    </script>
@endsection
