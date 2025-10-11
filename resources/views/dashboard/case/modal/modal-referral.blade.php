<div id="modalReferral" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Change Referral</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div class="box-body no-padding " style="width:100%;overflow-x:auto">
                    <div class="row">
                        <div class="col-12">
                            @if($newcase == 1)
                                <a class="btn btn-info float-right" href="javascript:void(0)" data-backdrop="static"
                                data-keyboard="false" style="color:white;margin:0;margin-bottom:10px" data-toggle="modal"  data-target="#modalCreateNewReferral"
                                class="btn btn-xs btn-primary"><i style="margin-right: 10px;"
                                    class="fa fa-plus"></i>Add New Referral</a>
                            @endif

                        </div>

                    </div>

                    <table id="tbl-referral2" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Ic No</th>
                                <th>Email</th>
                                <th>Phone No</th>
                                {{-- <th>Referral</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

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
    function loadReferralList($type='case') {
        var table3 = $('#tbl-referral2').DataTable({
            processing: true,
            serverSide: true,
            destroy:true,
            ajax: {
                url: "{{ route('referral_main.list') }}",
                data: {
                    "type": $type,
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'id',
                    className : 'hide',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'company',
                    name: 'company'
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
                @if($newcase != 1)
                {
                    data: 'action_change_referral',
                    className: "text-center",
                    name: 'action_change_referral',
                    orderable: true,
                    searchable: true
                },
                @else
                {
                    data: 'action_select_referral',
                    className: "text-center",
                    name: 'action_select_referral',
                    orderable: true,
                    searchable: true
                },
                @endif

                
                
                
            ]
        });
    }

    @if($newcase != 1)

        function changeReferral(client_id, client_name) {
            Swal.fire({
                icon: 'warning',
                title: 'Confirmation',
                text: 'Change current case\'s referral to [' + client_name + ']?',
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

                    form_data.append("referral_id", client_id);

                    $.ajax({
                        type: 'POST',
                        url: '/changeReferral/{{ $case->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            $("#div_full_screen_loading").hide();
                            if (data.status == 1) {
                                closeUniversalModal();
                                toastController(data.message);

                                $("#div_case_team").html(data.view);
                            } else {
                                toastController(data.message, 'warning');
                            }
                        },
                        error: function(file, response) {
                            $("#div_full_screen_loading").hide();
                        }
                    });
                }
            })
        }
    @endif


    
</script>
