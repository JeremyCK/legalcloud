@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <h4>Voucher list <span class="label bg-info">In Progress</span></h4>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            <input type="hidden" value="actionButton" id="editMode" />

                            <div class="row">

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by voucher type</label>
                                            <select class="form-control" id="ddl_type" name="ddl_type">
                                                <option value="99">-- All --</option>
                                                <option value="1">Bill</option>
                                                <option value="2">Trust</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if ($current_user->menuroles == 'admin' ||
                                    $current_user->menuroles == 'management' ||
                                    $current_user->menuroles == 'account')
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Filter by Requestor</label>
                                                <select class="form-control" id="ddl_requestor" name="ddl_requestor">
                                                    <option value="0">-- All --</option>

                                                    @foreach ($requestor_list as $index => $requestor)
                                                        <option value="{{ $requestor->id }}">{{ $requestor->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="reloadTable();">
                                        <i class="fa cil-search"> </i>Filter
                                    </a>
                                </div>

                                <div class="col-sm-12">
                                    <hr />
                                </div>
                            </div>
                            <br>
                            <div class="col-sm-12" style="margin-bottom:50px;">
                                {{-- <a class="btn btn-lg btn-info  float-left" href="javascript:void(0)"
                                    data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                    data-target="#searchModal"><i style="margin-right: 10px;"
                                        class="fa fa-search"></i>Advance Search</a> --}}
                                @if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker']))
                                    {{-- <div class="col-sm-12" style="margin-bottom:50px;"> --}}

                                        <a class="btn btn-lg btn-success bulk-edit-mode  float-left" style="margin-right:10px;display:none" 
                                        href="javascript:void(0)" onclick="checkAll(true);">
                                        <i class="fa cil-check"> </i>Check all
                                    </a>
        
                                    <a class="btn btn-lg bg-question bulk-edit-mode  float-left" href="javascript:void(0)" style="margin-right:10px;display:none"
                                        onclick="checkAll(false);">
                                        <i class="fa cil-x"> </i>Uncheck all
                                    </a>

                                    <a class="btn btn-lg btn-danger bulk-edit-mode  float-left" style="display:none"
                                        href="javascript:void(0)" onclick="cancelBulkUpdateMode();">
                                        <i class="fa cil-x"> </i>Cancel Buld Update
                                    </a>
                                    <a class="btn btn-lg btn-success normal-edit-mode  float-right"
                                        href="javascript:void(0)" onclick="bulkUpdateMode();">
                                        Bulk Mode
                                    </a>
                                    <button class="btn btn-lg btn-success  bulk-edit-mode btn-submit  float-right"
                                        style="display:none; margin-left:10px;" href="javascript:void(0)"
                                        onclick="saveBulkUpdate('APPROVE');">
                                        <i class="cil-check"> </i>Approve selected
                                    </button>
                                    {{-- </div> --}}
                                @endif
                            </div>


                            <div class="box-body no-padding " style="width:100%;overflow-x:auto">
                                <div class="nav-tabs-custom nav-tabs-custom-ctr">
                                    <ul class="nav nav-tabs" role="tablist">

                                        @foreach ($branchs as $branch)
                                            <li class="nav-item">
                                                <a class="nav-link @if($current_user->branch_id == $branch->id) active @endif" data-toggle="tab" href="#branch_{{ $branch->id }}" role="tab" aria-controls="trust" aria-selected="true">{{ $branch->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" style="padding:30px;">

                                        @foreach ($branchs as $branch)
                                        <div class="tab-pane  @if($current_user->branch_id == $branch->id) active @endif"
                                            id="branch_{{ $branch->id }}" role="tabpanel">
                                            <table id="tblbranch_{{ $branch->id }}"
                                                class="table table-bordered table-striped yajra-datatable"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <!-- <th>No</th> -->
                                                        <th>Action</th>
                                                        <th>Date</th>
                                                        <th>Voucher No</th>
                                                        <th>Total Amt</th>
                                                        <th>TRX ID</th>
                                                        <th>Payee</th>
                                                        <th>Desc</th>
                                                        <th>Client</th>
                                                        <th>case Ref No</th>
                                                        <th>Lawyer</th>
                                                        <th>Account</th>
                                                        {{-- <th>Receipt issued</th> --}}
                                                        <th>Voucher Type</th>
                                                        <th>Request</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                
                                                <tfoot>
                                                    <tr style="background-color: black; color:white">
                                                        <td colspan="3">Total</td>
                                                        <td class="text-right"><span id="total_sum_{{ $branch->id }}">0.00</span></td>
                                                        <td colspan="9"> </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        @endforeach     

                                    </div>
                                </div>
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
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        function deleteVoucher($voucher_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            Swal.fire({
                title: 'Delete this voucher?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteVoucher/' + $voucher_id,
                        data: null,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function checkAll(bool) {
            $.each($("input[name='voucher']"), function() {
                template_id = [];

                if($(this).is(":visible"))
                {
                    itemID = $(this).attr('id');
                    console.log(itemID)
                    $("#" + itemID).prop('checked', bool);
                }

            });
        }

        function bulkUpdateMode() {
            $(".normal-edit-mode").hide();
            $(".bulk-edit-mode").show();

            $("#editMode").val('checkbox');
        }

        function cancelBulkUpdateMode() {
            $(".normal-edit-mode").show();
            $(".bulk-edit-mode").hide();

            $("#editMode").val('actionButton');
            checkAll(false);
        }

        blnAjax = true;
        function saveBulkUpdate($status) {
            blnAjax = true;
            var voucher_list = [];
            var voucher = {};
            var dialog_msg = '';
            var result_msg = '';

            $('.btn-submit').prop('disabled', true);
            if (blnAjax == false) return;
            blnAjax = false;

            if ($status == 'APPROVE') {
                dialog_msg = 'Approve this voucher?';
                result_msg = 'Voucher approved';
            } else if ($status == 'INPROGRESS') {
                dialog_msg = 'Set this voucher to in progress?';
                result_msg = 'Moved to in progress';
            }

            $.each($("input[name='voucher']:checked"), function() {

                itemID = $(this).val();
                voucher = {
                    voucher_id: itemID,
                };

                voucher_list.push(voucher);
            });


            console.log(voucher_list);

            var form_data = new FormData();
            form_data.append("voucher_list", JSON.stringify(voucher_list));
            form_data.append("status", $status);

            $.ajax({
                type: 'POST',
                url: '/bulk_update_voucher_status',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    
                    $('.btn-submit').attr('disabled', false);
                        blnAjax = true;
                    if (data.status == 1) {

                        toastController(result_msg);
                        reloadTable();

                    } else {
                         Swal.fire('Notice!', data.message, 'error');
                    }

                }
            });
        }

        function updateInProgress($voucher_id, $status) {
            blnAjax = true;
            var voucher_list = [];
            var voucher = {};
            var dialog_msg = '';
            var result_msg = '';

            if ($status == 'APPROVE') {
                dialog_msg = 'Approve this voucher?';
                result_msg = 'Voucher approved';
            } else if ($status == 'INPROGRESS') {
                dialog_msg = 'Set this voucher to in progress?';
                result_msg = 'Moved to in progress';
            }

            Swal.fire({
                icon: 'warning',
                text: dialog_msg,
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    if (blnAjax == false) return;
                    voucher = {
                        voucher_id: $voucher_id,
                    };

                    voucher_list.push(voucher);

                    var form_data = new FormData();
                    form_data.append("voucher_list", JSON.stringify(voucher_list));
                    form_data.append("status", $status);

                    $.ajax({
                        type: 'POST',
                        url: '/bulk_update_voucher_status',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                toastController(result_msg);
                                reloadTable();
                            } else {
                                $('.btn-submit').attr('disabled', false);
                            }

                        }
                    });
                }
            })
        }

        function reloadTable() {


            @foreach ($branchs as $branch)
            table = $('#tblbranch_{{ $branch->id }}').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 100,
                order: [
                    [2, "desc"]
                ],
                ajax: {
                    url: "{{ route('voucher.list') }}",
                    data: {
                        // "status": $("#ddl_status").val(),
                        // "type": $("#ddl_type").val(),
                        // "requestor": $("#ddl_requestor").val(),
                        // "branch_id": {{ $branch->id }},
                        // "mode": $("#editMode").val()

                        
                        "branch_id":  {{ $branch->id }},
                        "requestor": $("#ddl_requestor").val(),
                        "account_approval_status": 6
                    }
                },
                columns: [{
                        data: 'action',
                        className: "text-center",
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    // {
                    //   data: 'DT_RowIndex',
                    //   name: 'DT_RowIndex'
                    // },
                    {
                        data: 'voucher_no',
                        name: 'voucher_no'
                    },
                    {
                        data: 'details_amount',
                        name: 'details_amount',
                        className: "text-right",
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id'
                    },
                    {
                        data: 'payee',
                        name: 'payee'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'hrefcase',
                        name: 'hrefcase'
                    },
                    {
                        data: 'lawyer_approval',
                        className: "text-center",
                        name: 'lawyer_approval'
                    },
                    {
                        data: 'account_approval',
                        className: "text-center",
                        name: 'account_approval'
                    },
                    // {
                    //   data: 'receipt_issued',
                    //   className: "text-center",
                    //   name: 'receipt_issued'
                    // },
                    {
                        data: 'voucher_type',
                        name: 'voucher_type'
                    },
                    {
                        data: 'requestor',
                        name: 'requestor'
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
                        .column(3)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    transfer_amount = parseFloat(monTotal).toFixed(2);
                    $("#total_sum_{{ $branch->id }}").html(numberWithCommas(transfer_amount));
                }
            });
            @endforeach
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(function() {
            reloadTable();
        });
    </script>
@endsection
