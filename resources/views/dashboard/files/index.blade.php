@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

    <div class="container-fluid">
        <div class="fade-in">

            <div class="row hide">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning" style="padding-top: 17px;"><i
                                        class="cil-folder-open"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-number">{{ $exemptedCount }}</span>
                                    <span class="info-box-text">Exempted</span>
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
                                    <span class="info-box-number">{{ $paidCount }}</span>
                                    <span class="info-box-text">Paid</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->

                        <!-- fix for small devices only -->
                        <div class="clearfix visible-sm-block"></div>

                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-purple" style="padding-top: 17px;"><i
                                        class="cil-running"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-number">{{ $pendingCount }}</span>
                                    <span class="info-box-text">Pending</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
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
                                    <h4>Attachments</h4>
                                </div>

                                {{-- <div class="col-6">
                                    <a class="btn btn-lg btn-primary  float-right" href="{{ route('return-call.create') }}">
                                        <i class="cil-plus"> </i>Create New
                                    </a>
                                </div> --}}
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

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>File Type</label>
                                            <select class="form-control" id="type" name="type">
                                                <option value="99">-- All --</option>
                                                <option value="1">Correspondences</option>
                                                <option value="2">Documents</option>
                                                <option value="3">Account Receipt</option>
                                                <option value="4">Adjudicate</option>
                                                <option value="5">Marketing</option>
                                                @if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker', 'jr_account' ]) ||
                                                in_array($current_user->id, [51]))
                                                    <option value="6">Official Receipt</option>
                                                @endif
                                                <option value="7">Other Receipt</option>
                                                <option value="8">Presentation Receipt</option>
                                                <option value="9">Payment Receipt</option>


                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if (in_array($current_user->menuroles, ['admin','management','account']))
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

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Ref No</label>
                                            <input class="form-control" type="text" id="ref_no" name="ref_no" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Receipt Done</label>
                                            <select class="form-control" id="receipt_done" name="receipt_done">
                                                <option value="0">Pending</option>
                                                <option value="1">Done</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                        onclick="reloadTable();">
                                        <i class="fa cil-search"> </i>Search
                                    </a>
                                </div>

                                <div class="col-sm-12">
                                    <hr />
                                </div>

                                <div class="col-12">
                                    <a class="btn btn-lg btn-warning float-left" onclick="SaveReceiptDone()"
                                        href="javascript:void(0)">
                                        <i class="cil-save"> </i>Save Receipt Done</a>
                                </div>

                            </div>
                            <br>

                            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

                                <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Receipt Done</th>
                                            <th>User</th>
                                            <th>Ref No</th>
                                            <th>File</th>
                                            <th>Type</th>
                                            <th>Remark</th>
                                            <th>Date</th>
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
            </div>
        </div>
    </div>
    </div>

    <div id="fileTypeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="form_add">

                        <div class="form-group row ">
                            <div class="col">
                                <h4>Update File Type</h4>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>File Type</label>
                                <input type="hidden" val="0" name="fileID" id="fileID" />
                                <select class="form-control" id="type_id" name="type_id">
                                    @foreach ($attachment_type as $index => $type)
                                        <option value="{{ trim($type->parameter_value_2) }}">{{ $type->parameter_value_1 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                </div>
                </form>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right" onclick="updateFileType()">Assign
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
    {{-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> --}}
    <script type="text/javascript">
        function reloadTable() {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('files.list') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.type = $("#type").val();
                        d.branch = $("#ddl_branch").val();
                        d.receipt_done = $("#receipt_done").val();
                        d.ref_no = $("#ref_no").val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'receipt_done',
                        name: 'receipt_done'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name',
                        orderable: true,
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no',
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'remark',
                        name: 'remark',
                    },
                    {
                        data: 'created_at',
                        orderable: true,
                        name: 'created_at',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                ]
            });
        }

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

        function getFileID(fileID, FileType) {
            $("#type_id").val(FileType);
            $("#fileID").val(fileID);
        }

        function updateFileType() {

            var file_list = [];
            var file = {};

            var form_data = new FormData();
            form_data.append("fileID", $("#fileID").val());
            form_data.append("type_id", $("#type_id").val());


            $.ajax({
                type: 'POST',
                url: '/updateFileType',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {
                        toastController('Record updated');
                        // updateReconValue(result);
                        closeUniversalModal();
                        reloadTable();
                    } else {
                        Swal.fire('notice!', result.message, 'warning');
                    }

                }
            });
        }

        function SaveReceiptDone() {

            var file_list = [];
            var file = {};
            var file_type_list = [];
            var file_type = {};

            $.each($("input[name='file']:checked"), function() {
                itemID = $(this).val();

                Type = $("#filetype_" + itemID).val();

                file = {
                    id: itemID,
                };

                file_type = {
                    type: Type,
                };

                file_list.push(file);
                file_type_list.push(file_type);
            })
            


            if (file_list.length <= 0) {
                Swal.fire('notice!', 'No attachment selected', 'warning');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("file_list", JSON.stringify(file_list));
            form_data.append("file_type_list", JSON.stringify(file_type_list));

            $.ajax({
                type: 'POST',
                url: '/updateReceiptDone',
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
