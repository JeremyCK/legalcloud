@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4><i class="cil-history"></i> Audit Trail</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <div class="row">
                                <!-- Case Search (Required) -->
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label>Case Reference <span class="text-danger">*</span></label>
                                        <select class="form-control" id="case_search" name="case_search" style="width: 100%;" required>
                                            <option value="">-- Select Case --</option>
                                        </select>
                                        <small class="form-text text-muted">Search by case reference number, bank ref, or client name</small>
                                    </div>
                                </div>

                                <!-- Selected Case Info -->
                                <div class="col-sm-12 col-md-6 col-lg-4" id="case_info_container" style="display: none;">
                                    <div class="form-group">
                                        <label>Selected Case</label>
                                        <div class="alert alert-info mb-0" id="case_info">
                                            <strong id="case_ref_display"></strong><br>
                                            <small id="case_client_display"></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Filter -->
                                <div class="col-sm-6 col-md-3 col-lg-2">
                                    <div class="form-group">
                                        <label>User</label>
                                        <select class="form-control" id="ddl_user" name="ddl_user">
                                            <option value="0">-- All --</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Action Type Filter -->
                                <div class="col-sm-6 col-md-3 col-lg-2">
                                    <div class="form-group">
                                        <label>Action Type</label>
                                        <select class="form-control" id="ddl_action_type" name="ddl_action_type">
                                            <option value="all">-- All --</option>
                                            <option value="Create">Create</option>
                                            <option value="Update">Update</option>
                                            <option value="Delete">Delete</option>
                                            <option value="UploadAttachment">Upload Attachment</option>
                                            <option value="DeleteAttachment">Delete Attachment</option>
                                            <option value="UpdateCaseSummary">Update Case Summary</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Filter Button -->
                                <div class="col-sm-12">
                                    <button class="btn btn-lg btn-info float-right" type="button" onclick="reloadTable();" id="btn_filter">
                                        <i class="fa cil-search"></i> Load Audit Trail
                                    </button>
                                    <button class="btn btn-lg btn-secondary float-right mr-2" type="button" onclick="clearFilters();">
                                        <i class="fa cil-reload"></i> Clear
                                    </button>
                                </div>

                                <div class="col-sm-12">
                                    <hr />
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div id="empty_state" class="text-center py-5">
                                <i class="cil-search" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-3">Please select a case to view audit trail</p>
                            </div>

                            <!-- DataTable -->
                            <div id="table_container" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-12 text-right">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success" id="exportExcelBtn" type="button">
                                                <i class="fa fa-file-excel-o mr-2"></i>Export to Excel
                                            </button>
                                            <button class="btn btn-danger" id="exportPdfBtn" type="button">
                                                <i class="fa fa-file-pdf-o mr-2"></i>Export to PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body no-padding" style="width:100%;overflow-x:auto">
                                    <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Timestamp</th>
                                                <th>Type</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Description</th>
                                                <th>Changes</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script type="text/javascript">
        var table;
        var selectedCaseId = null;
        var selectedCaseData = null;

        $(document).ready(function() {
            // Initialize Select2 for case search
            $('#case_search').select2({
                placeholder: 'Search for a case...',
                allowClear: true,
                ajax: {
                    url: "{{ route('audit_trail.search_case') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                        term: params.term, // search term
                        page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                templateResult: formatCaseResult,
                templateSelection: formatCaseSelection
            });

            // Handle case selection
            $('#case_search').on('select2:select', function (e) {
                var data = e.params.data;
                selectedCaseId = data.id;
                selectedCaseData = data;
                
                // Show case info
                $('#case_info_container').show();
                $('#case_ref_display').text(data.case_ref_no);
                var clientInfo = data.client_name || 'N/A';
                if (data.bank_ref) {
                    clientInfo += ' | Bank Ref: ' + data.bank_ref;
                }
                $('#case_client_display').text(clientInfo);
                
                // Hide empty state, show table container
                $('#empty_state').hide();
                $('#table_container').show();
                
                // Auto-load table if case is selected
                if (selectedCaseId) {
                    reloadTable();
                }
            });

            // Handle case clear
            $('#case_search').on('select2:clear', function (e) {
                selectedCaseId = null;
                selectedCaseData = null;
                $('#case_info_container').hide();
                $('#empty_state').show();
                $('#table_container').hide();
                if (table) {
                    table.destroy();
                }
            });
        });

        function formatCaseResult(data) {
            if (data.loading) {
                return data.text;
            }
            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'><strong>" + data.case_ref_no + "</strong></div>" +
                "<div class='select2-result-repository__description'>" + (data.client_name || 'N/A') + 
                (data.bank_ref ? ' | ' + data.bank_ref : '') + "</div>" +
                "</div>" +
                "</div>"
            );
            return $container;
        }

        function formatCaseSelection(data) {
            return data.case_ref_no || data.text;
        }

        function reloadTable() {
            if (!selectedCaseId || selectedCaseId == 0) {
                alert('Please select a case first');
                return;
            }

            if (table) {
                table.destroy();
            }

            table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 50,
                stateSave: true,
                order: [[1, 'desc']], // Order by timestamp descending
                ajax: {
                    url: "{{ route('audit_trail.list') }}",
                    data: function(d) {
                        d.case_id = selectedCaseId;
                        d.user = $("#ddl_user").val();
                        d.action_type = $("#ddl_action_type").val();
                    },
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'log_type_badge',
                        name: 'log_type',
                        orderable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'action_badge',
                        name: 'action',
                        orderable: false
                    },
                    {
                        data: 'desc',
                        name: 'desc',
                        orderable: false
                    },
                    {
                        data: 'changes',
                        name: 'changes',
                        orderable: false
                    }
                ],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                    emptyTable: 'No audit trail records found for the selected case'
                }
            });
        }

        function clearFilters() {
            $('#case_search').val(null).trigger('change');
            $('#ddl_user').val('0');
            $('#ddl_action_type').val('all');
            selectedCaseId = null;
            selectedCaseData = null;
            $('#case_info_container').hide();
            $('#empty_state').show();
            $('#table_container').hide();
            if (table) {
                table.destroy();
            }
        }

        // Export to Excel
        $('#exportExcelBtn').on('click', function() {
            if (!selectedCaseId || selectedCaseId == 0) {
                alert('Please select a case first');
                return;
            }

            var user = $('#ddl_user').val();
            var actionType = $('#ddl_action_type').val();
            
            var url = '{{ route("audit_trail.export_excel") }}?case_id=' + selectedCaseId;
            if (user && user != '0') {
                url += '&user=' + user;
            }
            if (actionType && actionType != 'all') {
                url += '&action_type=' + actionType;
            }
            
            window.location.href = url;
        });

        // Export to PDF
        $('#exportPdfBtn').on('click', function() {
            if (!selectedCaseId || selectedCaseId == 0) {
                alert('Please select a case first');
                return;
            }

            var user = $('#ddl_user').val();
            var actionType = $('#ddl_action_type').val();
            
            var url = '{{ route("audit_trail.export_pdf") }}?case_id=' + selectedCaseId;
            if (user && user != '0') {
                url += '&user=' + user;
            }
            if (actionType && actionType != 'all') {
                url += '&action_type=' + actionType;
            }
            
            window.location.href = url;
        });
    </script>
@endsection

