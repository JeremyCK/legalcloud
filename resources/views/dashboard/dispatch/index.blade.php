@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

    <div class="container-fluid">
        <div class="fade-in">





            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning" style="padding-top: 17px;"><i
                                        class="cil-folder-open"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-number">{{ $preparingCount }}</span>
                                    <span class="info-box-text">Sending</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-green" style="padding-top: 17px;"><i
                                        class="cil-check"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-number">{{ $deliveredCount }}</span>
                                    <span class="info-box-text">Completed</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->

                        <!-- fix for small devices only -->
                        <div class="clearfix visible-sm-block"></div>

                        <!-- <div class="col-xl-4 col-md-6 col-12">
                <div class="info-box">
                  <span class="info-box-icon bg-purple" style="padding-top: 17px;"><i class="cil-running"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-number">{{ $sendingCount }}</span>
                    <span class="info-box-text">Sending</span>
                  </div>
                </div>
              </div> -->
                        <!-- /.col -->

                        <!-- /.col -->
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Dispatch - Outgoing list</h4>
                                </div>

                                @if ($blnAllowEdit == true)
                                    <div class="col-6">
                                        <a class="btn btn-lg btn-primary  float-right"
                                            href="{{ route('dispatch.create') }}">
                                            <i class="cil-plus"> </i>Create New Dispatch
                                        </a>
                                    </div>
                                @endif


                            </div>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Dispatch</label>
                                            <select class="form-control" id="ddl_dispatch" name="ddl_dispatch">
                                                <option value="0">-- All --</option>
                                                @foreach ($couriers as $courier)
                                                    <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Status</label>
                                            <select class="form-control" id="ddl_status" name="ddl_status">
                                                <option value="99">-- All --</option>
                                                <option value="0">Sending</option>
                                                <option value="1">Completed</option>
                                                <option value="2">In progress</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

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

                                @if (
                                    $current_user->menuroles == 'admin' ||
                                        $current_user->menuroles == 'management' ||
                                        $current_user->menuroles == 'account')
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



                            <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Action</th>
                                        <!-- <th>Dispatch No</th> -->
                                        <th>File Ref</th>
                                        <th>Client</th>
                                        <th>Send to</th>
                                        <th>Job Desc</th>
                                        <th>Dispatch Name</th>
                                        <th>Return to office</th>
                                        <th>Status</th>
                                        {{-- <th>Attachment</th> --}}
                                        <th>Assgined By</th>
                                        <th>Date</th>
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

@endsection

@section('javascript')

@include('dashboard.shared.script.src-operation')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        // document.getElementById("ddl_dispatch").onchange = function() {
        //   reloadTable();
        // }

        // document.getElementById("ddl_status").onchange = function() {
        //   reloadTable();
        // }

        function openFileFromS3(filename) {
            var form_data = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            form_data.append("filename", filename);
            // form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

            $.ajax({
                type: 'POST',
                url: '/getFileFromS3',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                        window.location.href = data;
                    }
                    else
                    {
                        window.open(data, "_blank");
                    }
                }
            });
        }

        function reloadTable() {
            var url = "{{ route('dispatch_list.list', ['dispatchID', 'Status', 1]) }}";

            url = url.replace('dispatchID', $("#ddl_dispatch").val());
            url = url.replace('Status', $("#ddl_status").val());

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 25,
                ajax: {
                    url: url,
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
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
                        searchable: false
                    },
                    // {
                    //   data: 'dispatch_no',
                    //   name: 'dispatch_no',
                    // },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no',
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'send_to',
                        name: 'send_to'
                    },
                    {
                        data: 'job_desc',
                        name: 'job_desc'
                    },
                    {
                        data: 'courier_name',
                        name: 'courier_name'
                    },
                    {
                        data: 'return_to_office_datetime',
                        name: 'return_to_office_datetime'
                    },
                    {
                        data: 'status',
                        className: "text-center",
                        name: 'status'
                    },
                    // {
                    //     data: 'file',
                    //     name: 'file'
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

        function deleteDispatch($id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Delete this dispatch?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteDispatch/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                // Swal.fire('Success!', data.message, 'success');
                                toastController('Case closed');
                                reloadTable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
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
