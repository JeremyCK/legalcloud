@extends('dashboard.base')


<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            @if ($current_user->menuroles != 'receptionist' && $current_user->menuroles != 'account')
                <div class="row hide">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua" style="padding-top: 17px;"><i
                                            class="cil-folder-open"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-number">{{ $openCaseCount }}</span>
                                        <span class="info-box-text">Open case</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="info-box" onclick="alert(4);">
                                    <span class="info-box-icon bg-green" style="padding-top: 17px;"><i
                                            class="cil-check"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-number">{{ $closedCaseCount }}</span>
                                        <span class="info-box-text">Closed Case</span>
                                    </div>
                                </div>
                            </div>

                            <!-- fix for small devices only -->
                            <div class="clearfix visible-sm-block"></div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-purple" style="padding-top: 17px;"><i
                                            class="cil-running"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-number">{{ $InProgressCaseCount }}</span>
                                        <span class="info-box-text">In progress case</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red" style="padding-top: 17px;"><i
                                            class="cil-av-timer"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-number">{{ $OverdueCaseCount }}</span>
                                        <span class="info-box-text">Overdue cases</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-sm-12">

                    <div class="card" style="width:100%;overflow-x:auto">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>{{ $case_status_text }} Cases <span
                                            class="label bg-{{ $case_status_label }}">{{ $case_status_text }}</span></h4>
                                </div>

                                @if ($allowCreateCase == 'true')
                                    <div class="col-6">
                                        <a class="btn btn-lg btn-primary  float-right" href="{{ route('case.create') }}">
                                            <i class="cil-plus"> </i>{{ __('coreuiforms.case.add_new_case') }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                        </div>
                        <div class="card-body">



                            @if ($current_user->id == 1)
                                <div class="row">
                                    <div class="col-12">
                                        {{-- <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="adminResizeImage()"
                                            value='Admin resize image' />
                                            <input type='button' class='btn btn-finish btn-fill btn-success btn-wd float-right'
                                                onclick="adminMigrateLedger()" value='Migrate Ledger' />
                                    <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="adminUpdateTransferAmount()"
                                            value='Admin transfer amount' />
                                        <input type='button' class='btn btn-finish btn-fill btn-success btn-wd float-right'
                                            onclick="adminUpdateValue()" value='Update All value for admin' />
                                        <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="updateAllcheckListDate()"
                                            value='Update All checklist date for admin' />
                                        <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="adminUpdateOperation()" value='Update Operation for admin' />
                                        <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="adminUpdateCaseCount()" value='Update case count for admin' />
                                            <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                                onclick="adminUpdateInvoiceBranch()" value='Update invoice branch' />
                                                <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                                onclick="adminUpdateBillSum()" value='Update Bill Sum' />

                                        <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="adminBonusCalculation()" value='Update bonus' /> --}}

                                        <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                            onclick="adminBulkTransferCase()" value='Bluk Transfer case' />

                                        {{-- <div class="col">
                                                <label>File</label>
                                                <input class="form-control" type="file" id="excel_file" name="excel_file">
                                            </div>
            
                                            <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right'
                                                onclick="adminUploadExcelFile()" value='Upload' /> --}}



                                    </div>



                                </div>
                            @endif

                            @include('dashboard.case.section.d-search-case')
                        </div>

                        <br />

                        <div class="row">
                            <div class="col-12">

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
                                                {{-- <th>Case details</th> --}}
                                                <th>Client</th>
                                                {{-- <th>Sales</th>
                                                <th>Lawyer</th>
                                                <th>Clerk</th> --}}
                                                {{-- <th>Hidden ref no</th>
                                                <th>Hidden date no</th> --}}
                                                <th>Open File Date</th>
                                                <th>SPA Date</th>
                                                <th>Completion Date</th>
                                                <th>Branch</th>
                                                <th>Latest Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- <div class="box-body no-padding " style="width:100%;overflow-x:auto">
                                    <div class="nav-tabs-custom nav-tabs-custom-ctr">
                                        <ul class="nav nav-tabs" role="tablist">
                                            @if (count($branchs))
                                                @foreach ($branchs as $index => $branch)
                                                    <li class="nav-item"><a
                                                            class="nav-link @if ($index == 0) active @endif"
                                                            data-toggle="tab" href="#tab_{{ $branch->short_code }}"
                                                            role="tab" aria-controls="branch"
                                                            aria-selected="true">{{ $branch->name }}</a></li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="tab-content" style="padding:30px;">
                                    @if (count($branchs))
                                        @foreach ($branchs as $index => $branch)
                                            <div class="tab-pane  @if ($current_user->branch_id == $branch->id) active @endif"
                                                id="tab_{{ $branch->short_code }}" role="tabpanel">
                                                <table id="tblCase_{{ $branch->short_code }}"
                                                    class="table table-bordered table-striped yajra-datatable"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Action</th>
                                                            <th>Case Number <a href=""
                                                                    class="btn btn-info btn-xs rounded shadow  mr-1"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a>
                                                            </th>
                                                            <th>Client</th>
                                                            <th>Sales</th>
                                                            <th>Lawyer</th>
                                                            <th>Clerk</th>
                                                            <th>Open file</th>
                                                            <th>SPA Date</th>
                                                            <th>Completion Date</th>
                                                            <th>Latest Notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    @endif
                                </div> --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div id="modalTransfer" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width: 60%">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="display: block !important">
                    {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
                    <div class="row">
                        <div class="col-6">
                            <h4 class="card-title mb-0 flex-grow-1"><i style="margin-right: 10px;" class="cil-transfer"></i>
                                Transfer Case</h4>
                            <input type="hidden" id="input_close_abort" />
                        </div>
                        <div class="col-6">
                            <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="form_add">

                        {{-- <div class="form-group row ">
                            <div class="col">
                                <h4>Transfer Case</h4>
                            </div>
                        </div> --}}

                        @if(count($branchs) > 1)
                        <div class="form-group row ">
                            <div class="col">
                                

                                <div class="nav-tabs-custom nav-tabs-custom-ctr">
                                    <div class="col">
                                        <strong>Filter PIC by Branch</strong>
                                    </div>

                                    <ul class="nav nav-tabs scrollable-" role="tablist">

                                        @foreach ($branchs as $index => $branch)
                                            {{-- <li class="nav-item"><a
                                                    class="nav-link nav-link-{{ $branch->id }} @if ($index == 0) active @endif" onclick="filterTransferBranch({{ $branch->id }})"
                                                    data-toggle="tab" href="#" role="tab"
                                                    aria-controls="branch" aria-selected="true">{{ $branch->name }}</a>
                                            </li> --}}

                                            <li class="nav-item"><a class="nav-link nav-link-{{ $branch->id }}"
                                                    data-toggle="tab" href="#notes" role="tab"
                                                    onclick="filterTransferBranch({{ $branch->id }})"
                                                    aria-controls="log" aria-selected="true">{{ $branch->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif

                        



                        <div class="form-group row">
                            <div class="col">
                                <label>Lawyer</label>
                                <select class="form-control" id="lawyer_id" name="lawyer_id">
                                    @foreach ($lawyerList as $index => $lawyer)
                                        <option value="{{ $lawyer->id }}"
                                            class="option_branch option_branch_{{ $lawyer->branch_id }}" selected>
                                            {{ $lawyer->name }}</option>
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
                                        <option value="{{ $clerk->id }}"
                                            class="option_branch option_branch_{{ $clerk->branch_id }}" selected>
                                            {{ $clerk->name }}</option>
                                    @endforeach
                                    @foreach ($chamberList as $index => $chamber)
                                        <option value="{{ $chamber->id }}"
                                            class="option_branch option_branch_{{ $chamber->branch_id }}"selected>
                                            {{ $chamber->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($allowTransferSales == true)
                        <div class="form-group row">
                            <div class="col">
                                <label>Sales</label>
                                <select class="form-control" id="sales_id" name="sales_id">
                                    @foreach ($salesList as $index => $sales)
                                        <option value="{{ $sales->id }}"
                                            class="option_branch option_branch_{{ $sales->branch_id }}" selected>
                                            {{ $sales->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right" onclick="TransferCase()">Transfer
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
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
                    </form>
                </div>
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

@endsection

<script>
    function reloadTable() {

        table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('case_list.list') }}",
                data: {
                    "status": {{ $case_status }},
                    "case_type": $("#ddl_portfolio").val(),
                    "lawyer": $("#ddl-lawyer").val(),
                    "clerk": $("#ddl-clerk").val(),
                    "sales": $("#ddl-sales").val(),
                    "chambering": $("#ddl-chamber").val(),
                    "referral": $("#ddl-referral").val(),
                    "parties": $("#parties_name").val(),
                    "branch": $("#branch").val(),
                    "month": $("#ddl_month").val(),
                    "year": $("#ddl_year").val()
                }
            },
            order: [3, 'desc'],
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
                {
                    data: 'latest_notes',
                    name: 'latest_notes',
                    searchable: false
                },
            ]
        });
    }

    function filterTransferBranch(branch_id) {
        $(".option_branch").hide();
        $(".option_branch_" + branch_id).show();


        $(".nav-link").removeClass('active');
        $(".nav-link_" + branch_id).addClass('active');

    }

    function searchCase() {
        // $("#form_filter_case").reset();
        document.getElementById("form_filter_case").reset();
        reloadTable();
    }

    function transferModal(id, lawyer_id, clerk_id, sales_id) {
        $("#txtId").val(id);
        $("#lawyer_id").val(lawyer_id);
        $("#clerk_id").val(clerk_id);
        $("#sales_id").val(sales_id);
    }

    function TransferCase() {
        var formData = new FormData();

        formData.append('lawyer_id', $("#lawyer_id").val());
        formData.append('clerk_id', $("#clerk_id").val());
        @if ($allowTransferSales == true)
        formData.append('sales_id', $("#sales_id").val());
        @endif
        
        formData.append('skip_lawyer', 1);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '/transferSystemCase/' + $("#txtId").val(),
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
        } else if (type == 'PENDINGCLOSED') {
            $confirmationMSG = 'set pending close to this case?';
            $SuccessMSG = 'Case set to pending close';
        } else if (type == 'REVIEWING') {
            $confirmationMSG = 'Send this case for review and close?';
            $SuccessMSG = 'Case set to for review';
        } else if (type == 'REOPEN') {
            $confirmationMSG = 'Reopen this case?';
            $SuccessMSG = 'Case reopen';
        }

        var form_data = new FormData();

        form_data.append('type', type);

        Swal.fire({
            icon: 'warning',
            text: $confirmationMSG,
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
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
</script>

@section('javascript')
    <script>
        $(function() {
            reloadTable();
        });







        // function reloadTable() {

        //     table = $('.yajra-datatable').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         destroy: true,
        //         ajax: {
        //             url: "{{ route('case_list.list') }}",
        //             data: {
        //                 "status": $("#ddl_status").val(),
        //                 "case_type": $("#ddl_portfolio").val(),
        //                 "lawyer": $("#ddl-lawyer").val(),
        //                 "clerk": $("#ddl-clerk").val(),
        //                 "sales": $("#ddl-sales").val(),
        //                 "chambering": $("#ddl-chamber").val(),
        //                 "referral": $("#ddl-referral").val(),
        //                 "parties": $("#parties_name").val(),
        //                 "branch": $("#branch").val(),
        //                 "month": $("#ddl_month").val(),
        //                 "year": $("#ddl_year").val()
        //             }
        //         },
        //         order: [3, 'desc'],
        //         columns: [{
        //                 data: 'action',
        //                 className: "text-center",
        //                 name: 'action',
        //                 searchable: false
        //             },
        //             {
        //                 data: 'case_ref_no',
        //                 name: 'case_ref_no'
        //             },
        //             {
        //                 data: 'client_name',
        //                 name: 'client_name'
        //             },
        //             {
        //                 data: 'created_at',
        //                 name: 'created_at', 
        //             },
        //             {
        //                 data: 'spa_date',
        //                 name: 'spa_date'
        //             },
        //             {
        //                 data: 'completion_date',
        //                 name: 'completion_date'
        //             },
        //             {
        //                 data: 'branch',
        //                 name: 'branch'
        //             },
        //             {
        //                 data: 'latest_notes',
        //                 name: 'latest_notes',
        //                 searchable: false
        //             },
        //         ]
        //     });
        // }


        function clearSearch() {
            location.reload();
        }

        @if ($current_user->id == '1')

            function adminUpdateValue() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminUpdateValue',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminUpdateBillSum() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminUpdateBillSum',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminMigrateLedger() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminMigrateLedger',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminBulkTransferCase() {
                var formData = new FormData();

                formData.append('clerk_id', 137);
                formData.append('lawyer_id', 0);
                formData.append('skip_lawyer', 1);

                $.ajax({
                    type: 'POST',
                    url: 'adminBulkTransferCase',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminUpdateOperation() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminUpdateOperation',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminUpdateOperation() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'updateExcelFile',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminUpdateCaseCount() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminUpdateCaseCount',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }



            function adminUpdateInvoiceBranch() {
                var formData = new FormData();

                // formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminUpdateInvoiceBranch',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminBonusCalculation() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminBonusCalculation',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function openFileFromS3() {
                var form_data = new FormData();

                form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

                $.ajax({
                    type: 'POST',
                    url: 'getFileFromS3',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                                .userAgent)) {
                            window.location.href = data;
                        } else {
                            window.open(data, "_blank");
                        }
                    }
                });
            }

            function adminUploadExcelFile() {

                var formData = new FormData();

                var files = $('#excel_file')[0].files;
                console.log(files[0]);
                formData.append('excel_file', files[0]);

                $.ajax({
                    type: 'POST',
                    url: 'adminUploadExcelFile',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        // window.open(data, "_blank")
                    }
                });
            }

            function updateAllcheckListDate() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'updateAllcheckListDate',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminUpdateTransferAmount() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminUpdateTransferAmount',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }

            function adminResizeImage() {
                var formData = new FormData();

                formData.append('case_ref_no_search', $("#case_ref_no_search").val());

                $.ajax({
                    type: 'POST',
                    url: 'adminResizeImage',
                    data: null,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            }
        @endif
    </script>
@endsection
