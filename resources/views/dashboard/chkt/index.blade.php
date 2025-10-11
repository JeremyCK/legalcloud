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
                            <div class="row">
                                <div class="col-6">
                                    <h4>CHKT</h4>
                                </div>

                                <div class="col-6">
                                    <a class="btn btn-lg btn-primary  float-right" href="{{ route('chkt.create') }}">
                                        <i class="cil-plus"> </i>Create New CHKT
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <div class="row">


                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
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

                                </div>


                                <!-- <div class="col-6">
                    <div class="form-group row">
                      <div class="col">
                        <label>Filter by received Notis Taksiran</label>
                        <select class="form-control" id="ddl_status" name="ddl_status">
                          <option value="99">-- All --</option>
                          <option value="0">No</option>
                          <option value="1">Yes</option>
                        </select>
                      </div>
                    </div>
                  </div> -->

                                @if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'management')
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Filter by Branch</label>
                                                <select class="form-control" id="ddl_branch" name="ddl_branch">
                                                    <option value="0">-- All --</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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

                            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

                                <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Action</th>
                                            <th>File Ref</th>
                                            <th>Client</th>
                                            <th>Last SPA Date</th>
                                            <th>Current SPA Date</th>
                                            <th>3% RPGT Paid</th>
                                            <th>Received Notis Taksiran</th>
                                            <th>Created By</th>
                                            <th>Received Date</th>
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

@endsection

@section('javascript')
    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        function reloadTable() {
            // var table = $('.yajra-datatable').DataTable({
            //   processing: true,
            //   serverSide: true,
            //   destroy: true,
            //   pageLength: 50,
            //   ajax: {
            //     url: "{{ route('safe_keeping_list.list') }}",
            //     data: function(d) {
            //       // d.date_from = $("#date_from").val();
            //       // d.date_to = $("#date_to").val();
            //       // d.status = $("#ddl_status").val();
            //       // d.branch = $("#ddl_branch").val();
            //     },
            //   },
            //   columns: [{
            //       data: 'DT_RowIndex',
            //       name: 'DT_RowIndex'
            //     },
            //     // {
            //     //   data: 'action',
            //     //   name: 'action',
            //     //   className: "text-center",
            //     //   orderable: true,
            //     //   searchable: true
            //     // },
            //     // {
            //     //   data: 'case_ref_no',
            //     //   name: 'case_ref_no',
            //     // },
            //     // {
            //     //   data: 'client_name',
            //     //   name: 'client_name'
            //     // },
            //     // {
            //     //   data: 'last_spa_date',
            //     //   name: 'last_spa_date'
            //     // },
            //     // {
            //     //   data: 'current_spa_date',
            //     //   name: 'current_spa_date'
            //     // },
            //     // {
            //     //   data: 'received_on',
            //     //   name: 'received_on'
            //     // },
            //     // {
            //     //   data: 'per3_rpgt_paid',
            //     //   className: "text-center",
            //     //   name: 'per3_rpgt_paid'
            //     // },
            //     // {
            //     //   data: 'remark',
            //     //   name: 'remark'
            //     // },
            //     // {
            //     //   data: 'assign_by',
            //     //   name: 'assign_by'
            //     // },
            //     // {
            //     //   data: 'created_at',
            //     //   name: 'created_at'
            //     // },
            //   ]
            // });


            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('chkt_list.list') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.status = $("#ddl_status").val();
                        d.branch = $("#ddl_branch").val();
                    },
                },
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
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no',
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'last_spa_date',
                        name: 'last_spa_date'
                    },
                    {
                        data: 'current_spa_date',
                        name: 'current_spa_date'
                    },
                    {
                        data: 'per3_rpgt_paid',
                        className: "text-center",
                        name: 'per3_rpgt_paid'
                    },
                    {
                        data: 'file',
                        className: "text-center",
                        name: 'file'
                    },
                    // {
                    //   data: 'remark',
                    //   name: 'remark'
                    // },
                    {
                        data: 'assign_by',
                        name: 'assign_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ]
            });
        }

        $(function() {
            var date = new Date(),
                y = date.getFullYear(),
                m = date.getMonth();
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);
            lastDay = (("0" + lastDay.getDate()).slice(-2));

            var last_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" +
                lastDay;
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + ((
                "0" +
                firstDay.getDate()).slice(-2));

            $("#date_from").val(start_date);
            $("#date_to").val(last_date);

            reloadTable();
        });
    </script>
@endsection
