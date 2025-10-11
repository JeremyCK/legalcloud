@extends('dashboard.base')
@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>My Bonus Request</h4>
                                </div>

                            </div>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <div class="row">
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card mb-4" style="--cui-card-cap-bg: #00aced">
                                        <div class="card-header position-relative d-flex justify-content-center align-items-center">
                                            <h4 class="text-center">Total Requested file</span>
                                        </div>
                                        <div class="card-body row text-center">
                  
                                            <div class="col">
                                              <div class="fs-5 fw-semibold"> <span id="total_approved_3">{{ $bonus_approved_count }} </span> </div>
                                              <div class="text-uppercase text-medium-emphasis ">Approved</div>
                                          </div>
                                          <div class="vr"></div>
                                          <div class="col">
                                              <div class="fs-5 fw-semibold"><span id="total_approved_5">{{ $bonus_reviewing_count }}</span> </div>
                                              <div class="text-uppercase text-medium-emphasis ">Reviewing</div>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row ">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Status</label>
                                            <select class="form-control" id="ddl_status" name="ddl_status">
                                                <option value="0">-- All --</option>
                                                <option value="1">Reviewing</option>
                                                <option value="2">Approved</option>
                                                {{-- <option value="3">Rejected</option> --}}
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
                                            <th>File Ref</th>
                                            <th>Amount</th>
                                            <th>Requester</th>
                                            <th>Status</th>
                                            <th>Bonus Type</th>
                                            <th>Request Date</th>
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
    <script type="text/javascript">
        function reloadTable() {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('Staffbonusrequest.list') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.status = $("#ddl_status").val();
                        d.requestor = $("#ddl_requestor").val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no',
                    },
                    {
                        data: 'bonus_amt',
                        name: 'bonus_amt',
                        className: "text-right",
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'user_name',
                        className: "text-center",
                        name: 'user_name'
                    },
                    {
                        data: 'status',
                        className: "text-center",
                        name: 'status'
                    },
                    {
                        data: 'bonus_type',
                        className: "text-center",
                        name: 'bonus_type'
                    },
                    {
                        data: 'created_at',
                        className: "text-center",
                        name: 'created_at'
                    },
                ]
            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(function() {
            reloadTable();
        });
    </script>
@endsection
