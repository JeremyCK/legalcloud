@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h4> Manage refferal commision group </h4>
                            </div>
                            <div class="col-6 ">
                                <a href="/referral-comm" class="btn btn-danger float-right">{{ __('coreuiforms.return') }}</a>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="cil-folder-open"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-number" id="totalAccountBox">{{ $ReferralFormula->name }}</span>
                                        <span class="info-box-text">Formula: {{ $ReferralFormula->formula }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModalInvoice">
                                    <i class="cil-plus"> </i>Add referral
                                </a>
                            </div>

                        </div>

                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                        @endif
                        <form id="form_adjudication" method="POST" action="{{ route('safe-keeping.update', 1) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')



                            <table id="tbl-referral-group" class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Action</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>


        </div>

        <div id="myModalInvoice" class="modal fade" role="dialog">
            <div class="modal-dialog" style="max-width: 1000px !important;">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form_edit_quotation">
                            <div class="form-group row ">



                                <div class="col-12">
                                    <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="saveReferralIntoCommGroup()">
                                        <i class="cil-plus"> </i>Save settings
                                    </a>
                                </div>

                                <div class="col-12" style="margin-top:10px">

                                    <table id="tbl-referral" class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Action</th>
                                                <th>Name</th>
                                                <th>Company</th>
                                                <th>Current formula</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')

<script>
    function reloadTable() {
        var table = $('#tbl-referral-group').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('referral-comm.list',$ReferralFormula->id) }}",
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
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'company',
                    name: 'company'
                },
            ]
        });
        2
        var table = $('#tbl-referral').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('referral-comm.referral_list',$ReferralFormula->id) }}",
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
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'formula',
                    name: 'formula'
                },
            ]
        });
    }

    $(function() {
        reloadTable();
    });

    function saveReferralIntoCommGroup() {
        var referral_id_list = [];

        $.each($("input[name='referral']:checked"), function() {
            itemID = $(this).val();
            referral_id_list.push(itemID);
        });

        var form_data = new FormData();
        form_data.append("referral_id_list", JSON.stringify(referral_id_list));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "/referral-comm/saveReferralIntoCommGroup/{{$ReferralFormula->id}}",
            data: form_data,
            processData: false,
            contentType: false,
            success: function(data) {
                toastController('Referral commission updated');
                $('#btnClose').click();
                reloadTable();

            }
        });
    }

    function removeReferralFromCommGroup($id) {
        var referral_id_list = [];

        referral_id_list.push($id);

        var form_data = new FormData();
        form_data.append("referral_id_list", JSON.stringify(referral_id_list));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            icon: 'warning',
            text: 'Remove the referral from this group?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: "/referral-comm/removeReferralFromCommGroup",
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        toastController('Referral commission updated');
                        $('#btnClose').click();
                        reloadTable();

                    }
                });
            }
        })


    }
</script>


@endsection