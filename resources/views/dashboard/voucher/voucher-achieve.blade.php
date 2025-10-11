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
                            <h4>Voucher list <span class="label bg-success">Archived</span></h4>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message')) 
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row">

                                <div class="col-6 date_option date_option_year">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Year</label>
                                            <select class="form-control" id="ddl_year" name="ddl_year">
                                                {{-- <option value="0">-- All --</option> --}}
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025" selected>2025</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 date_option date_option_month">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control" id="ddl_month" name="ddl_month">
                                                {{-- <option value="0">-- All --</option> --}}
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
                                            <label>Filter by account Approval</label>
                                            <select class="form-control" id="ddl_status" name="ddl_status">
                                                <option value="99">-- All --</option>
                                                <option value="1">Approved</option>
                                                <option value="2">Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

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

                                @if (in_array($current_user->menuroles, ['admin', 'management', 'account']))
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

                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Search By Smartbox No</label>
                                                <input class="form-control" id="search_box" name="search_box" />
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


                            <div class="box-body no-padding " style="width:100%;overflow-x:auto">
                                <div class="nav-tabs-custom nav-tabs-custom-ctr">
                                    <ul class="nav nav-tabs" role="tablist">
                                        {{-- @if (in_array($current_user->menuroles, ['admin', 'management', 'account']))
                                            @if ($current_user->branch_id != 3)
                                                <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                        href="#uptown" role="tab" aria-controls="trust"
                                                        aria-selected="true">Uptown</a></li>
                                                <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#puchong"
                                                        role="tab" aria-controls="trust"
                                                        aria-selected="true">Puchong</a></li>
                                                <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#arkadia"
                                                        role="tab" aria-controls="trust"
                                                        aria-selected="true">Arkadia</a></li>
                                                        
                                                <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#rama"
                                                    role="tab" aria-controls="trust"
                                                    aria-selected="true">Ramakrishnan</a></li>
                                            @else
                                                <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                        href="#arkadia" role="tab" aria-controls="trust"
                                                        aria-selected="true">Arkadia</a></li>
                                            @endif
                                        @else
                                            @if ($current_user->branch_id == 1)
                                                <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                        href="#uptown" role="tab" aria-controls="trust"
                                                        aria-selected="true">Uptown</a></li>
                                            @endif
                                            @if ($current_user->branch_id == 2)
                                                <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                        href="#puchong" role="tab" aria-controls="trust"
                                                        aria-selected="true">Puchong</a></li>
                                            @endif
                                        @endif --}}

                                        @foreach ($branchs as $branch)
                                            <li class="nav-item">
                                                <a class="nav-link @if ($current_user->branch_id == $branch->id) active @endif"
                                                    data-toggle="tab" href="#branch_{{ $branch->id }}" role="tab"
                                                    aria-controls="trust" aria-selected="true"><span style="margin-left:10px" class="label bg-danger" id="span_voucher_count_{{ $branch->id }}">0</span>{{ $branch->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" style="padding:30px;">
                                        @foreach ($branchs as $branch)
                                            <div class="tab-pane  @if ($current_user->branch_id == $branch->id) active @endif"
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
                                                            <td class="text-right"><span
                                                                    id="total_sum_{{ $branch->id }}">0.00</span></td>
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
                            "branch_id": {{ $branch->id }},
                            "status": $("#ddl_status").val(),
                            "month": $("#ddl_month").val(),
                            "year": $("#ddl_year").val(),
                            "account_approval_status": 1,
                            "requestor": $("#ddl_requestor").val(),
                            "type": $("#ddl_type").val(),
                            "search_box": $("#search_box").val()
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

                    var table = $('#tblbranch_{{ $branch->id }}').DataTable();
                    $("#span_voucher_count_{{ $branch->id }}").html(table.data().count());
                }
                });
            @endforeach

        }

        
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(function() {
                const d = new Date();
                let month = d.getMonth() + 1;
                let year = d.getFullYear();

                $("#ddl_month").val(month);
                $("#ddl_year").val(year);
                // alert(3);

                reloadTable();
            });

    </script>
@endsection
