@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">

                    <div class="card" style="width:100%;overflow-x:auto">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Closed Cases <span class="label bg-success">Closed</span></h4>
                                </div>

                            </div>

                        </div>

                        <div class="card-body">
                            @include('dashboard.case.section.d-search-case')
                        </div>
                        <div class="card-body">

                            <div class="tab-pane">
                                <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                      <tr class="text-center">
                                          <th>Action</th>
                                          <th>Ref No
                                              <a href="javascript:void(0)"
                                                  class="btn btn-info btn-xs rounded shadow  mr-1"
                                                  data-toggle="tooltip" data-placement="top"
                                                  title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a>
                                          </th>
                                          <th>Client</th>
                                          <th>Sales</th>
                                          <th>Lawyer</th>
                                          <th>Clerk</th>
                                          <th>Open File Date</th>
                                          <th>SPA Date</th>
                                          <th>Completion Date</th>
                                          <th>Branch</th>
                                          {{-- <th>Latest Notes</th> --}}
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
                        <input type="hidden" value="0" id="txtId" name="txtId" />

                        <div class="form-group row">
                            <div class="col">
                                <label>Lawyer</label>
                                <select class="form-control" id="lawyer_id" name="lawyer_id">
                                    @foreach ($lawyerList as $index => $lawyer)
                                        <option value="{{ $lawyer->id }}" selected>{{ $lawyer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Clerk</label>
                                <select class="form-control" id="clerk_id" name="clerk_id">
                                    <option value="0" selected>-- No clerk --</option>
                                    @foreach ($clerkList as $index => $clerk)
                                        <option value="{{ $clerk->id }}" selected>{{ $clerk->name }}</option>
                                    @endforeach
                                    @foreach ($chamberList as $index => $chamber)
                                        <option value="{{ $chamber->id }}" selected>{{ $chamber->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
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
@endsection

@section('javascript')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
                function transferModal(id, lawyer_id, clerk_id) {
            $("#txtId").val(id);
            $("#lawyer_id").val(lawyer_id);
            $("#clerk_id").val(clerk_id);
        }

        function TransferCase() {
            var formData = new FormData();

            formData.append('lawyer_id', $("#lawyer_id").val());
            formData.append('clerk_id', $("#clerk_id").val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: 'transferSystemCase/' + $("#txtId").val(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    toastController('Case Transferred');
                    reloadTable();
                    closeUniversalModal();
                }
            });
        }

        function updateCaseStatus($case_id, type) {
            $confirmationMSG = '';
            $SuccessMSG = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (type == 'CLOSED') {
                $confirmationMSG = 'Close this case?';
                $SuccessMSG = 'Case closed';
            } else if (type == 'ABORTED') {
                $confirmationMSG = 'Abort this case?';
                $SuccessMSG = 'Case aborted';
            }

            var form_data = new FormData();

            form_data.append('type', type);

            Swal.fire({
                text: $confirmationMSG,
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/updateCaseStatus/' + $case_id,
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', $SuccessMSG, 'success');
                                reloadTable();
                                // window.location.href = '/case';
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        function reopenCase($case_id, type) {
            $confirmationMSG = '';
            $SuccessMSG = '';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                text: 'Reopen this case?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/reopenCase/' + $case_id,
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', 'Case reopen', 'success');
                                reloadTable();
                                // window.location.href = '/case';
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }

        $(function() {
            reloadTable();
        });

        function reloadTable() {
            table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('case_list.list') }}",
                    data: {
                        "status": $("#ddl_status").val(),
                        "case_type": $("#ddl_portfolio").val(),
                        "lawyer": $("#ddl-lawyer").val(),
                        "status": 0,
                        "clerk": $("#ddl-clerk").val(),
                        "chambering": $("#ddl-chamber").val(),
                        "referral": $("#ddl-referral").val(),
                        "parties": $("#parties_name").val(),
                        "branch": $("#branch").val(),
                        "month": $("#ddl_month").val(),
                        "sales": $("#ddl-sales").val(),
                        "year": $("#ddl_year").val()
                    }
                },
                order: [6, 'desc'],
                columns: [{
                        data: 'action',
                        className: "text-center",
                        name: 'action',
                        searchable: false
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
                        data: 'sales',
                        name: 'sales'
                    },
                    {
                        data: 'lawyer_name',
                        name: 'lawyer_name'
                    },
                    {
                        data: 'clerk_name',
                        name: 'clerk_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at', 
                    },
                    {
                        data: 'spa_date',
                        name: 'spa_date'
                    },
                    {
                        data: 'completion_date',
                        name: 'completion_date'
                    },
                    {
                        data: 'branch',
                        name: 'branch'
                    },
                    // {
                    //     data: 'notes',
                    //     name: 'notes',
                    //     searchable: false
                    // },
                ]
            });
        }

        document.getElementById("ddl-clerk").onchange = function() {
            if ($("#ddl-clerk").val() != "0") {
                $("#ddl-lawyer").val("0");
                $("#ddl-chamber").val("0");
                filterCaseByRole(8, $("#ddl-clerk").val());
            }
        }

        document.getElementById("ddl-chamber").onchange = function() {
            if ($("#ddl-chamber").val() != "0") {
                $("#ddl-lawyer").val("0");
                $("#ddl-clerk").val("0");
                filterCaseByRole(11, $("#ddl-chamber").val());
            }
        }

        function filterCaseByRole(role_id, id) {
            var formData = new FormData();


            formData.append('role_id', role_id);
            formData.append('id', id);

            $.ajax({
                type: 'POST',
                url: 'filter_case_by_role',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#tbl-data').html(data.view);
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function filterCaseByBranch(role_id, branch_id) {
            var formData = new FormData();


            formData.append('branch_id', branch_id);
            // formData.append('id', id);

            $.ajax({
                type: 'POST',
                url: 'filter_case_by_branch',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#tbl-data').html(data.view);
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        // function filterCase() {
        //     var formData = new FormData();

        //     if ($('#case_ref_no_search').val() == '') {
        //         return;
        //     }

        //     formData.append('case_ref_no_search', $("#case_ref_no_search").val());

        //     $.ajax({
        //         type: 'POST',
        //         url: 'getSearchCase',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(data) {
        //             $('#tbl-data').html(data.view);
        //         }
        //     });
        // }

        function filterCase() {
            $("#parties_name").val('');
            reloadTable();
        }

        function clearSearch() {
            location.reload();
        }
    </script>
@endsection
