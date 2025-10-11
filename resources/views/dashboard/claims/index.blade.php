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
                                    <h4>Claims Request List</h4>
                                </div>

                            </div>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <div class="row">
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card text-white bg-success">
                                        <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                            <div class="btn-group float-right">
                                            </div>
                                            <div class="text-value-lg">{{ $ClaimRequestApproved }}</div>
                                            <div>Total Approved claims</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card text-white bg-warning">
                                        <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                            <div class="btn-group float-right">
                                            </div>
                                            <div class="text-value-lg">{{ $ClaimRequestPending }}</div>
                                            <div>Total Pending claims</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card text-white bg-info">
                                        <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                            <div class="btn-group float-right">
                                            </div>
                                            <div class="text-value-lg" id="txt_approved_sum">0</div>
                                            <div>Total Approved claims</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row ">

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

                                {{-- <div class="col-6 date_option date_option_year">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Year</label>
                                            <select class="form-control" id="ddl_year" name="ddl_year">
                                                <option value="2022">2022</option>
                                                <option value='2023'>2023</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 date_option date_option_month">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control" id="ddl_month" name="ddl_month">
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
                                </div> --}}

                                @if($ClaimsApproval == true)
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Filter by Staff</label>
                                                <select class="form-control" id="ddl_requestor" name="ddl_requestor">
                                                    <option value="99">-- All --</option>
                                                    @foreach ($staffs as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                               

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Status</label>
                                            <select class="form-control" id="ddl_status" name="ddl_status">
                                                <option value="0">-- All --</option>
                                                <option value="2" selected>Reviewing</option>
                                                <option value="1">Approved</option>
                                                <option value="3">Rejected</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Date Type</label>
                                            <select class="form-control" id="ddl_date_type" name="ddl_date_type">
                                                <option value="request">Request Date</option>
                                                <option value="approval">Approval Date</option>
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
                                            @if($ClaimsApproval == true)
                                            <th>Action</th>
                                            @endif
                                            <th>File Ref</th>
                                            <th>Staff</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Request Type</th>
                                            <th>Request Date</th>
                                            <th>Approved Date</th>
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
            sumClaims();

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('claimsRequest.list') }}",
                    data: function(d) {
                        d.year = $("#ddl_year").val();
                        d.month = $("#ddl_month").val();
                        d.status = $("#ddl_status").val();
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.requestor = $("#ddl_requestor").val();
                        d.date_type = $("#ddl_date_type").val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    @if($ClaimsApproval == true)
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    @endif
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no',
                    },
                    {
                        data: 'user_name',
                        className: "text-center",
                        name: 'user_name'
                    },
                    {
                        data: 'amount',
                        className: "text-center",
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        className: "text-center",
                        name: 'status'
                    },
                    {
                        data: 'type_name',
                        className: "text-center",
                        name: 'type_name'
                    },
                    {
                        data: 'created_at',
                        className: "text-center",
                        name: 'created_at'
                    },
                    {
                        data: 'approved_date',
                        className: "text-center",
                        name: 'approved_date'
                    },
                ]
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }


        function sumClaims() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("requestor", $("#ddl_requestor").val());
            form_data.append("year", $("#ddl_year").val());
            form_data.append("month", $("#ddl_month").val());


            $.ajax({
                type: 'POST',
                url: '/getClaimSum',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    $("#txt_approved_sum").html(numberWithCommas(data));

                }
            });
        }

        $(function() {

            var date = new Date(),
                y = date.getFullYear(),
                m = date.getMonth();
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);


            var last_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" + (("0" + lastDay.getDate()).slice(-2));
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + (("0" +
                firstDay.getDate()).slice(-2));

            $("#date_from").val(start_date);
            $("#date_to").val(last_date);

            reloadTable();
        });
    </script>
@endsection
