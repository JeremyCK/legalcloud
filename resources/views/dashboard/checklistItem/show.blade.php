@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">

        <div class="row">
            <div class="col-sm-12">
                <div id="dTable" class="card">
                    <div class="card-header">
                        <h4>{{ $templates_main->name }}

                            <a class="btn btn-lg btn-info  float-right" href="{{ $templates_main->id }}/edit">
                                <i class="cil-setting"> </i>Edit Step
                            </a>
                        </h4>

                    </div>
                    <div class="card-body">



                        <div class="row">
                            <div class="col-6">
                                <a class="btn btn-lg btn-danger  float-left" href="/checklist-item">
                                    <i class="fa fa-reply"> </i>Back
                                </a>
                            </div>


                            <div class="col-6">
                                <a class="btn btn-lg btn-warning  float-right" onclick="checklistSeqMode()" href="javascript:void(0)" style="margin-left:10px">
                                    <i class="cil-loop-circular"> </i>Checklist Sequence
                                </a>
                                <a class="btn btn-lg btn-primary  float-right" onclick="addMode()" href="javascript:void(0)">
                                    <i class="cil-plus"> </i>Add new checklist
                                </a>
                            </div>


                            <table id="tbl-data" class="table table-striped table-bordered datatable text-center">
                                <thead class="text-center">
                                    <tr>
                                        <!-- <th>No</th> -->
                                        <th>Process</th>
                                        <th>KPI</th>
                                        <th>Days</th>
                                        <th>PIC</th>
                                        <th>Start from</th>
                                        <th>Need attachment</th>
                                        <th>Email</th>
                                        <!-- <th>System Code</th>
                                        <th>Attachment</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($templates))

                                    @foreach($templates as $index => $template)
                                    <tr class="small" style="font-size:8px!important;">
                                        <input type="hidden" id="role_{{ $template->id }}" value="{{ $template->roles }}">
                                        <input type="hidden" id="check_point_{{ $template->id }}" value="0">
                                        <input type="hidden" id="days_{{ $template->id }}" value="{{ $template->days }}">
                                        <input type="hidden" id="start_{{ $template->id }}" value="{{ $template->start }}">
                                        <input type="hidden" id="start_name_{{ $template->id }}" value="{{ $template->checklist_name }}">
                                        <input type="hidden" id="remarks_{{ $template->id }}" value="{{ $template->remark }}">
                                        <input type="hidden" id="status_{{ $template->id }}" value="{{ $template->status }}">
                                        <input type="hidden" id="kpi_{{ $template->id }}" value="{{ $template->kpi }}">
                                        <input type="hidden" id="need_attachment_{{ $template->id }}" value="{{ $template->need_attachment }}">
                                        <input type="hidden" id="auto_dispatch_{{ $template->id }}" value="{{ $template->auto_dispatch }}">
                                        <input type="hidden" id="auto_receipt_{{ $template->id }}" value="{{ $template->auto_receipt }}">
                                        <!-- <td>
                                            <div class="small ">{{ $index+1 }} </div>
                                        </td> -->
                                        <td class="text-left">
                                            <div class="small " id="checklist_{{ $template->id }}">{{ $template->name }}</div>
                                        </td>
                                        <td id="kpi_{{ $template->id }}">
                                            <div class="small ">{{ intval($template->kpi) }}</div>
                                        </td>
                                        <td id="kpiday_{{ $template->id }}">
                                            <div class="small ">{{ intval($template->days) }}</div>
                                        </td>
                                        <td>

                                            <div class="small ">
                                                @if($template->roles == 1)
                                                System
                                                @elseif($template->roles == 5)
                                                Account
                                                @elseif($template->roles == 6)
                                                Sales
                                                @elseif($template->roles == 7)
                                                Lawyer
                                                @elseif($template->roles == 8)
                                                Clerk
                                                @endif
                                            </div>
                                        </td>
                                        <!-- <td>{{ intval($template->duration) }} </td> -->
                                        <td>
                                            <div class="small ">{{ ($template->checklist_name) }}</div>
                                        </td>
                                        <td class="text-left">
                                            <div class="small ">
                                                <b>Attachment:</b>
                                                @if($template->need_attachment == 1)
                                                <span class="badge-pill small badge-success">Yes</span>
                                                @elseif($template->need_attachment == 0)
                                                <span class="badge-pill small badge-warning">No</span>
                                                @endif<br />

                                                <b> Auto Dispatch:</b>
                                                @if($template->auto_dispatch == 1)
                                                <span class="badge-pill small badge-success">Yes</span>
                                                @elseif($template->auto_dispatch == 0)
                                                <span class="badge-pill small badge-warning">No</span>
                                                @endif<br />

                                                <b> Auto Receipt:</b>
                                                @if($template->auto_receipt == 1)
                                                <span class="badge-pill small badge-success">Yes</span>
                                                @elseif($template->auto_receipt == 0)
                                                <span class="badge-pill small badge-warning">No</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td id="checkpoint_{{ $template->id }}">
                                            @if($template->email_template_id > 0)
                                            {{ $template->email_template_id }}
                                            @else
                                            -
                                            @endif

                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" onclick="editMode('{{ $template->id }}')" class="btn btn-primary"><i class="cil-pencil"></i></a>
                                                <!-- <a href="javascript:void(0)" onclick="checklistSeqMode('{{ $template->id }}')" class="btn btn-info"><i class="cil-loop-circular"></i></a> -->
                                                <a href="javascript:void(0)" onclick="deleteChecklistItem('{{ $template->id }}')" class="btn btn-danger"><i class="cil-x"></i></a>

                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @else
                                    <tr>
                                        <td class="text-center" colspan="8">No checklist</td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>

                @include('dashboard.checklistItem.d-action')
                @include('dashboard.checklistItem.d-checklist-list')
                @include('dashboard.checklistItem.d-checklist-seq')
            </div>
        </div>
    </div>

    @endsection

    @section('javascript')

    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        function editMode(id) {
            $('#dTable').hide();
            $('#div_action').show();

            var kpi = parseInt($("#kpi_" + id).val());
            var checkpoint = parseInt($("#check_point_" + id).val());
            var duration = parseInt($("#days_" + id).val());
            var start = parseInt($("#start_" + id).val());
            var start_name = ($("#start_name_" + id).val());

            var remarks = $("#remarks_" + id).val();

            if ($("#need_attachment_" + id).val() == 1) {
                document.getElementById("chk_need_attachment").checked = true;
            }

            if ($("#auto_receipt_" + id).val() == 1) {
                document.getElementById("chk_auto_receipt").checked = true;
            }


            $("#check_list_status").val($("#status_" + id).val());

            $("#checklist_name").val($("#checklist_" + id).html());
            $("#kpi").val(kpi);
            $("#check_point").val(checkpoint);
            $("#days").val(duration);
            $("#start").val(start);
            $("#start_name").val(start_name);
            $("#remarks").val(remarks);
            $("#role_id").val($("#role_" + id).val());
            $("#selected_id").val(id);
        }

        function deleteChecklistItem(id) {
            Swal.fire({
                title: 'Delete this checklist?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: 'POST',
                        url: '/delete_checklist/' + id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                // iniFileTable();
                                // table.ajax.reload();
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function addMode() {
            $('#dTable').hide();
            $('#div-checklist').hide();
            $('#div_action').show();
            $('#div-checklist').hide();

            document.getElementById("chk_need_attachment").checked = false;
            document.getElementById("chk_auto_dispatch").checked = false;
            document.getElementById("chk_auto_receipt").checked = false;

            $("#checklist_name").val('');
            $("#kpi").val(0);
            $("#check_point").val(0);
            $("#days").val(0);
            $("#start").val(0);
            $("#selected_id").val(0);
            $("#remarks").val('');
        }

        function listMode() {
            $('#dTable').show();
            $('.d_operation').hide();
        }

        function backAddMode() {
            $('#div-checklist').hide();
            $('#div_action').show();
        }

        function checklistMode() {
            $('#div-checklist').show();
            $('#div_action').hide();
        }

        function checklistSeqMode() {


            // $(".checklist-item-row").show();
            // $(".checklist-item-row_" + id).hide();

            $('#div-checklist-seq').show();
            $('#dTable').hide();
            $('#div_action').hide();
        }

        $("#start_name").click(function() {
            checklistMode();
        });

        function selectThisChecklist(id, name) {
            $("#start_name").val(name);
            $("#start").val(id);

            backAddMode();
        }

        function submitAction(template_id) {
            var strUrl = '';
            var checkListId = $("#selected_id").val();

            if (checkListId == '0') {
                strUrl = '/add_checklist_item/' + template_id;
            } else {
                strUrl = '/update_checklist_item/' + $("#selected_id").val();
            }


            $.ajax({
                type: 'POST',
                url: strUrl,
                data: $('#form_action').serialize(),
                success: function(results) {
                    console.log(results);
                    if (results.status == 1) {
                        Swal.fire(
                            'Success!',
                            results.data,
                            'success'
                        )

                        location.reload();
                    }

                }
            });

        }

        function updateChecklistTemplate(template_id) {
            var form_data = new FormData();

            $.ajax({
                type: 'POST',
                url: '/update_checklist_template/' + $("#selected_id").val(),
                data: $('#form_action').serialize(),
                success: function(results) {
                    console.log(results);
                    if (results.status == 1) {
                        Swal.fire(
                            'Success!',
                            results.data,
                            'success'
                        )

                        location.reload();
                    }

                }
            });
        }

        function addChecklistTemplate(template_id) {
            var form_data = new FormData();

            $.ajax({
                type: 'POST',
                url: '/add_checklist_template/' + template_id,
                data: $('#form_action').serialize(),
                success: function(results) {
                    console.log(results);
                    // if (results.status == 1) {
                    //     Swal.fire(
                    //         'Success!',
                    //         results.data,
                    //         'success'
                    //     )

                    //     location.reload();
                    // }

                }
            });
        }

        function orderSequence() {

            var check_list = [];
            var bill = {};

            $.each($("input[name='seq']"), function() {

                itemID = $(this).attr('id');
                itemSEQ = $(this).val();
                console.log(itemID);

                // account_item_id = parseFloat($("#account_item_id_" + itemID).val());
                // need_approval = parseFloat($("#need_approval_" + itemID).val());
                // amount = parseFloat($("#quo_amt_" + itemID).val());
                // min = parseFloat($("#min_" + itemID).val());
                // max = parseFloat($("#max_" + itemID).val());

                bill = {
                    id: itemID,
                    seq: itemSEQ
                };

                check_list.push(bill);


            });


            var form_data = new FormData();

            form_data.append("check_list", JSON.stringify(check_list));


            $.ajax({
                type: 'POST',
                url: '/reorder_sequence_checklist_template',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(results) {
                    console.log(results);
                    if (results.status == 1) {
                        Swal.fire(
                            'Success!',
                            results.data,
                            'success'
                        )

                        location.reload();
                    }

                }
            });
        }
        document.getElementById("ddl-steps").onchange = function() {
            $(".checklist-item-row").hide();
            $(".checklist-item_" + $("#ddl-steps").val()).show();

            $("#search_referral").val("");
        }

        $('#search_referral').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_referral");
            filter = input.value.toUpperCase();



            $("#tbl-case-item tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(1)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            // console.log(input);

            // if (filter == "") {
            //     $("#tbl-case-item tr").each(function() {
            //         var self = $(this);
            //         $(this).hide();
            //     })
            // }
        });

        var table;
    </script>

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        //   $(function() {



        //     table = $('#tbl-checklist-yadra').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: "{{ route('checklistdetails.list' ) }}",
        //         columns: [
        //           {
        //             data: 'step_name',
        //             name: 'step_name'
        //           },
        //           {
        //             data: 'name',
        //             name: 'name'
        //           },
        //           {
        //             data: 'action',
        //             name: 'action',
        //             class: 'text-center',
        //             orderable: true,
        //             searchable: true
        //           },
        //         ]
        //       });



        //   });
    </script>

    @endsection