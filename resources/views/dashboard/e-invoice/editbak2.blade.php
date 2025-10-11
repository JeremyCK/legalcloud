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

                            <form id="form_einvoice" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">

                                    
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_date">E-Invoice Date</label>
                                            <div class="col-md-8">
                                                <input class="form-control" value="{{ $EInvoiceMain->einvoice_date }}"  name="einvoice_date" id="einvoice_date"
                                                    type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="ref_no">Ref No</label>
                                            <div class="col-md-8">
                                                <input class="form-control" value="{{ $EInvoiceMain->ref_no }}" name="ref_no" id="ref_no"
                                                    type="text" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="total_amount">Total Amount</label>
                                            <div class="col-md-8">
                                                {{-- <input class="form-control" name="total_amount" id="total_amount"
                                                    value="0.00" type="number" step="0.01" readonly/> --}}
                                                    <input class="form-control" value="{{ $EInvoiceMain->total_amount }}"  name="total_amount" id="total_amount"
                                                        type="text" readonly />
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transaction_id">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" value="{{ $EInvoiceMain->transaction_id }}"  name="transaction_id" id="transaction_id" type="text" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="branch_id">Branch ID</label>
                                            <div class="col-md-8">
                                                <select class="form-control"  id="branch_id" name="branch_id">
                                                    <option value="0"> -- All branch -- </option>
                                                      @foreach($Branchs as $index => $branch)
                                                      <option value="{{$branch->id}}" @if($EInvoiceMain->branch_id == $branch->id) selected @endif>{{$branch->name}}</option>
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
                                                    <option value="NOTSENT" @if($EInvoiceMain->batch_status == "NOTSENT") selected @endif>Not Sent</option>
                                                    <option value="SENT" @if($EInvoiceMain->batch_status == "SENT") selected @endif>Sent</option>
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
                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                            data-toggle="modal" data-target="#modalEInvoice" class="btn btn-info">Add new
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

                            </form>

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
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="modalEInvoice" class="modal fade" role="dialog">
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
                                <label class="col-md-4 col-form-label" for="start_date">Start Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="start_date" id="start_date"
                                        type="date" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="end_date">End Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="end_date" id="recv_end_date"
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
                                <i class="fa cil-search"> </i>Filter
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
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>

        var transfer_fee_add_list = [];

        $(function() {
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
                            itemID,
                        };
                        transfer_fee_add_list.push(itemID);
                    })

                    console.log(transfer_fee_add_list);
                    reloadAddTable();
                    reloadTable();
                }
            })
        }

        function AddIntoTransferListTest() {
            $.each($("input[name='bill']:checked"), function() {
                itemID = $(this).val();
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

        // $(".bal_to_transfer").change(function(){
        //     alert(3);
        //     var sum = 0;
        //     $.each($("input[name='add_bill']"), function() {
        //         itemID = $(this).val();
        //         // value = $("#selected_amt_" + itemID).val();
        //         sum = parseFloat($("#ban_to_transfer" + itemID).val());
        //     })

        //     $("#transfer_amount").val(sum.toFixed(2));
        // });

        

        function SaveEinvoice() {

            var voucher_list = [];
            var voucher = {};

            var errorCount = 0;

            if ($("#ref_no").val() == '' || $("#total_amount").val() == 0 || $("#transaction_id").val() == '' || $(
                    "#einvoice_date").val() == '') {

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
                    text: 'No Invoice selected',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }


            var form_data = new FormData();
            form_data.append("add_invoice", JSON.stringify(voucher_list));
            form_data.append("ref_no", $("#ref_no").val());
            form_data.append("total_amount", $("#total_amount").val());
            form_data.append("transaction_id", $("#transaction_id").val());
            form_data.append("einvoice_date", $("#einvoice_date").val());
            form_data.append("batch_status", $("#batch_status").val());
            form_data.append("description", $("#description").val());

            $("#btnSave").attr("disabled", true);

            $.ajax({
                type: 'POST',
                url: '/SaveNewEInvoice',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 'success') {

                        Swal.fire(
                            'Success!', 'E-Invoice created successfully',
                            'success'
                        )

                        window.location.href = '/einvoice/list';

                        // $("#tbl_bill").html(result.billList);
                    } else {
                        $("#btnSave").attr("disabled", false);
                        Swal.fire('Error!', result.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $("#btnSave").attr("disabled", false);
                    Swal.fire('Error!', 'Failed to create E-Invoice. Please try again.', 'error');
                    console.error(xhr.responseText);
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


        function reloadTable() {
            var table = $('#tbl-transfer-bill').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('EInvoiceSent.list') }}",
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
                ajax: {
                    url: "{{ route('EInvoiceSent.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'add';
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
                        data: 'sst_inv',
                        name: 'sst_inv',
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
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);


                    $("#total_amount").val(monTotal.toFixed(2));
                    $("#total_amount").val(thousandSeparator($("#total_amount").val()));

                    balUpdate();
                }
            });

        }

    </script>
@endsection
