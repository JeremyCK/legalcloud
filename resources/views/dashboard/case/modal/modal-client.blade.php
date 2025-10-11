<div id="modalClient" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Change Client</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div class="box-body no-padding " style="width:100%;overflow-x:auto">
                    <div class="row">
                        {{-- <div class="col-12">
                            <button type="button" id="btnCloseFile" class="btn btn-close-abort btn-close-file btn-success float-right" onclick="addNewClientMode()">Add New client
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div> --}}
                        
                    </div>
                    <div id="divClientTable">
                        <table id="tbl-client" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>IC</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>

                   
                    
                <form id="formAddNewClient"  style="display: none">
                    <div class="row">
                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_trx_id" id="cf_trx_id" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_trx_id" id="cf_trx_id" type="text">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                    

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                {{-- <button type="button" id="btnCloseFile" class="btn btn-close-abort btn-close-file btn-success float-right" onclick="closeFile('close')">Close File
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button> --}}

            </div>
        </div>

    </div>
</div>

<script>
    function loadClientList() {
        var table = $('#tbl-client').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('client.list') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'ic_no',
                    name: 'ic_no'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone_no',
                    name: 'phone_no'
                },
                {
                    data: 'action_change_client',
                    name: 'action_change_client',
                    className: "text-center",
                    orderable: true,
                    searchable: true
                },
            ]
        });
    }

    function addNewClientMode()
    {
        $("#divClientTable").hide();
        $("#formAddNewClient").show();

    }

    function changeClient(client_id, client_name) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Change current case\'s client to [' + client_name + ']?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

                $("#div_full_screen_loading").show();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var form_data = new FormData();

                form_data.append("client_id", client_id);

                $.ajax({
                    type: 'POST',
                    url: '/changeClient/{{ $case->id }}',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        $("#div_full_screen_loading").hide();
                        if (data.status == 1) {
                            closeUniversalModal();
                            toastController(data.message);

                            $("#div_case_client").html(data.view);
                            $("#div_case_summary").html(data.summary);
                        } else {
                            toastController(data.message,'warning');
                            // toastController('Error occur, please try again later', 'warning');
                        }
                    },
                    error: function(file, response) {
                        $("#div_full_screen_loading").hide();
                    }
                });
            }
        })
    }
</script>
