@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
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
                                    <span class="info-box-number" id="totalAccountBox">{{ $totalAcount }}</span>
                                    <span class="info-box-text">Total</span>
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
                                    <span class="info-box-number">{{ $totalAssigned }}</span>
                                    <span class="info-box-text">Assgined</span>
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
                                    <span class="info-box-number">{{ $totalUpdated }}</span>
                                    <span class="info-box-text">Status Updated</span>
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
                            <h4>Files before 2022</h4>

                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif


                            <div class="row @if (
                                $current_user->menuroles == 'lawyer' ||
                                    $current_user->menuroles == 'clerk' ||
                                    $current_user->menuroles == 'chambering') hide @endif">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by PIC</label>
                                            <select class="form-control" id="ddl_pic" name="ddl_pic">
                                                <option value="0">-- All --</option>
                                                {{-- <option value="26">Subra</option>
                      <option value="21">Ega</option> --}}
                                                @foreach ($old_pic as $pic)
                                                    <option value="{{ $pic->old_pic_id }}">{{ $pic->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by new PIC</label>
                                            <select class="form-control" id="ddl_pic_new" name="ddl_pic_new">
                                                <option value="0">-- All --</option>
                                                @foreach ($new_pic as $pic)
                                                    <option value="{{ $pic->new_pic_id }}">{{ $pic->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Lawyer</label>
                                            <select class="form-control" id="ddl_lawyer" name="ddl_lawyer">
                                                <option value="0">-- All --</option>
                                                @foreach ($lawyers as $lawyer)
                                                    <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <br>

                            <div class="box-body no-padding " style="width:100%;overflow-x:auto;padding-bottom:200px">

                                <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Action</th>
                                            <th>File Ref</th>
                                            <th>Client</th>
                                            {{-- <th>Client (V)</th> --}}
                                            <th>PIC</th>
                                            <th>New PIC</th>
                                            <th>Sales</th>
                                            <th>lawyer</th>
                                            <th>Case Date</th>
                                            <th>Completion Date</th>
                                            <th>Status</th>
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

            <div id="modalStatus" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="form_add">
                                <input type="hidden" value="0" id="txtId" name="txtId" />

                                <div class="form-group row ">
                                    <div class="col">
                                        <label>Status</label>
                                        <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>
                                    </div>
                                </div>

                        </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right" onclick="updateStatus()">Save
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div id="modalTransfer" class="modal fade" role="dialog">
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
                                        <h4>Assign case to</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col">
                                        <label>User</label>
                                        <select class="form-control" id="new_pic_id" name="new_pic_id">
                                            @foreach ($users as $index => $user)
                                                <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                        </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right" onclick="TransferCase()">Assign
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div id="AssignSalesModal" class="modal fade" role="dialog">
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
                                        <h4>Assign Sales</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col">
                                        <label>Sales</label>
                                        <select class="form-control" id="sales_id" name="sales_id">
                                            <option value="0" selected>--No sales--</option>
                                            <option value="2">Eve</option>
                                            <option value="3">Stanley</option>
                                            @foreach ($sales as $index => $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                        </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right" onclick="AssignSales()">Assign
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div id="AssignLawyerModal" class="modal fade" role="dialog">
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
                                        <h4>Assign Lawyer</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col">
                                        <label>Lawyer</label>
                                        <select class="form-control" id="lawyer_id" name="lawyer_id">
                                            <option value="0" selected>--No Lawyer--</option>
                                            @foreach ($lawyers as $index => $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                        </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right" onclick="AssignLawyer()">Assign
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div id="modalCompletionDate" class="modal fade" role="dialog">
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
                                        <h4>Update completion date</h4>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col">
                                        <label>Completion Date</label>

                                        <input type="date" id="completion_date" name="completion_date" />
                                    </div>
                                </div>

                        </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right"
                                onclick="updateCompletionDate()">Assign
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('javascript')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <!-- <script src="{{ asset('js/jquery.toast.min.js') }}"></script> -->
    <script type="text/javascript">
        CKEDITOR.replace('summary-ckeditor');
        CKEDITOR.config.height = 300;
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        CKEDITOR.config.removeButtons = 'Image';

        document.getElementById("ddl_pic").onchange = function() {
            reloadtable();
        }

        document.getElementById("ddl_pic_new").onchange = function() {
            reloadtable();
        }

        document.getElementById("ddl_lawyer").onchange = function() {
            reloadtable();
        }

        function getValue(id) {
            $("#txtId").val(id);
            CKEDITOR.instances['summary-ckeditor'].setData($("#remark_" + id).html());
        }

        function transferModal(id) {
            $("#txtId").val(id);
        }

        function AssignSalesModal(id) {
            $("#txtId").val(id);
        }

        function AssignLawyerModal(id) {
            $("#txtId").val(id);
        }

        function completionDateModal(id) {
            $("#txtId").val(id);
            $("#completion_date").val($("#completion_date_" + id).html());
        }

        function moveCaseToPerfectionCase($id) {
            Swal.fire({
                icon: 'warning',
                text: 'Move this case to perfection case list?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/moveCaseToPerfectionCase/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                var count = $("#totalAccountBox").html();
                                count -= 1;

                                $("#totalAccountBox").html(count);

                                toastController('Moved to prefection list');
                                reloadtable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function closeCase($id) {
            Swal.fire({
                icon: 'warning',
                text: 'Close this case?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/closeArchieveCase/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                var count = $("#totalAccountBox").html();
                                count -= 1;

                                $("#totalAccountBox").html(count);

                                toastController('Case closed');
                                reloadtable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function pendingCloseCase($id) {
            Swal.fire({
                icon: 'warning',
                text: 'Update this case to pending close?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/pendingCloseArchieveCase/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                var count = $("#totalAccountBox").html();
                                count -= 1;

                                $("#totalAccountBox").html(count);

                                toastController('Case pending close');
                                reloadtable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function updateStatus() {
            var formData = new FormData();

            var desc = CKEDITOR.instances['summary-ckeditor'].getData();

            if ($("#desc").val() == "") {
                // Swal.fire('Notice!', '', 'warning');
                return
            }

            formData.append('status', desc);


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/updateArchieveCaseRemark/' + $("#txtId").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    toastController('Status updated');
                    $("#remark_" + $("#txtId").val()).html(desc);
                    closeUniversalModal();
                    // location.reload();
                }
            });
        }

        function updateCompletionDate() {
            var formData = new FormData();


            if ($("#completion_date").val() == "") {
                // Swal.fire('Notice!', '', 'warning');
                return
            }

            formData.append('completion_date', $("#completion_date").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/updateArchieveCaseCompletionDate/' + $("#txtId").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    toastController('Completion date updated');
                    $("#completion_date_" + $("#txtId").val()).html($("#completion_date").val());
                    closeUniversalModal();
                    // location.reload();
                }
            });
        }

        function TransferCase() {
            var formData = new FormData();

            formData.append('new_pic_id', $("#new_pic_id").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/TransferCase/' + $("#txtId").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    toastController('Case Transferred');
                    $("#new_pic_id_" + $("#txtId").val()).html($("#new_pic_id option:selected").text());
                    closeUniversalModal();
                }
            });
        }

        function AssignSales() {
            var formData = new FormData();

            formData.append('sales_id', $("#sales_id").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/AssignSales/' + $("#txtId").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    toastController('Case Transferred');

                    if ($("#sales_id").val() == 0) {
                        $("#sales_id_" + $("#txtId").val()).html('');
                    } else {
                        $("#sales_id_" + $("#txtId").val()).html($("#sales_id option:selected").text());
                    }
                    closeUniversalModal();
                }
            });
        }

        function AssignLawyer() {
            var formData = new FormData();

            formData.append('lawyer_id', $("#lawyer_id").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/AssignLawyer/' + $("#txtId").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    toastController('Case Transferred');

                    if ($("#lawyer_id").val() == 0) {
                        $("#lawyer_id_" + $("#txtId").val()).html('');
                    } else {
                        $("#lawyer_id_" + $("#txtId").val()).html($("#lawyer_id option:selected").text());
                    }
                    closeUniversalModal();
                }
            });
        }

        var $userID = 0;

        function reloadtable() {

            var url = "{{ route('case_archieve.list', ['userID', 'newPIC', 'lawyer']) }}";

            url = url.replace('userID', $("#ddl_pic").val());
            url = url.replace('newPIC', $("#ddl_pic_new").val());
            url = url.replace('lawyer', $("#ddl_lawyer").val());

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 100,
                ajax: url,
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
                        data: 'ref_no',
                        name: 'ref_no',
                    },
                    {
                        data: 'client',
                        name: 'client'
                    },
                    // {
                    //   data: 'client_name_v',
                    //   name: 'client_name_v'
                    // },
                    {
                        data: 'old_pic',
                        name: 'old_pic'
                    },
                    {
                        data: 'new_pic',
                        name: 'new_pic'
                    },
                    {
                        data: 'sales_id',
                        name: 'sales_id'
                    },
                    {
                        data: 'lawyer_id',
                        name: 'lawyer_id'
                    },
                    {
                        data: 'case_date',
                        name: 'case_date'
                    },
                    {
                        data: 'completion_date',
                        name: 'completion_date'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks',
                        width: '400px'
                    },
                ]
            });
        }

        $(function() {


            reloadtable()

        });
    </script>
@endsection
