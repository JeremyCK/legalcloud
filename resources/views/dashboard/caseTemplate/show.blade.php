@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">

        <div class="row">
            <div class="col-sm-12">
                <div id="dTable" class="card">
                    <div class="card-header">
                        <h4>{{ $templates_main[0]->display_name }}

                            <a class="btn btn-lg btn-info  float-right" href="{{ $templates_main[0]->id }}/edit">
                                <i class="cil-setting"> </i>Edit template details
                            </a>
                        </h4>

                    </div>
                    <div class="card-body">



                        <div class="row">
                            <div class="col-6">
                                <a class="btn btn-lg btn-danger  float-left" href="/case-template">
                                    <i class="cil-action-redo"> </i>Back
                                </a>
                            </div>

                            <div class="col-6">
                                <a class="btn btn-lg btn-primary  float-right" onclick="addMode()" href="javascript:void(0)">
                                    <i class="cil-plus"> </i>Add new checklist
                                </a>
                            </div>


                            <table id="tbl-data" class="table table-striped table-bordered datatable text-center">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Process</th>
                                        <th>KPI</th>
                                        <th>PIC</th>
                                        <th>Duration</th>
                                        <th>Checkpoint</th>
                                        <!-- <th>System Code</th>
                                        <th>Attachment</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($templates))

                                    @foreach($templates as $index => $template)
                                    <tr class="small" style="font-size:8px!important;">
                                        <input type="hidden" id="role_{{ $template->id }}" value="{{ $template->role_id }}">
                                        <input type="hidden" id="check_point_{{ $template->id }}" value="{{ $template->check_point }}">
                                        <input type="hidden" id="duration_{{ $template->id }}" value="{{ $template->duration }}">
                                        <td>{{ $template->process_number }} </td>
                                        <td class="text-left">
                                            <div class="small " id="checklist_{{ $template->id }}">{{ $template->checklist_name }}</div>
                                        </td>
                                        <td id="kpi_{{ $template->id }}">{{ intval($template->kpi) }} </td>
                                        <td>

                                            @if($template->role_id == 1)
                                            System
                                            @elseif($template->role_id == 5)
                                            Account
                                            @elseif($template->role_id == 6)
                                            Sales
                                            @elseif($template->role_id == 7)
                                            Lawyer
                                            @elseif($template->role_id == 8)
                                            Clerk
                                            @endif
                                        </td>
                                        <td>{{ intval($template->duration) }} </td>
                                        <td id="checkpoint_{{ $template->id }}">
                                            @if($template->check_point > 0)
                                            {{ $template->check_point }}
                                            @else
                                            -
                                            @endif

                                        </td>
                                        <!-- <td>{{ $template->remark }} </td> -->
                                        <!-- <td>
                                            <div class="small checkbox">
                                                <input type="checkbox" name="bill" value="1" id="chk_1">
                                                <label for="chk_1">Attachment</label>
                                            </div>
                                            <div class="small checkbox">
                                                <input type="checkbox" name="bill" value="1" id="chk_1">
                                                <label for="chk_1">Attachment</label>
                                            </div>
                                        </td>
                                        <td>
                                            @if($template->need_attachment == 1)
                                            <label class="c-switch c-switch-label c-switch-pill c-switch-success">
                                                <input class="c-switch-input" type="checkbox" checked=""><span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                            </label>

                                            @else
                                            <label class="c-switch c-switch-label c-switch-pill c-switch-success">
                                                <input class="c-switch-input" type="checkbox" unchecked=""><span class="c-switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                            </label>
                                            @endif
                                        </td> -->
                                        <td>
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" onclick="editMode('{{ $template->id }}')" class="btn btn-primary"><i class="cil-pencil"></i></a>
                                                <a href="javascript:void(0)" onclick="orderSequence('{{ $template->id }}')" class="btn btn-primary"><i class="cil-loop-circular"></i></a>

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

                @include('dashboard.caseTemplate.d-action')
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

            var kpi = parseInt($("#kpi_" + id).html());
            var checkpoint = parseInt($("#check_point_" + id).val());
            var duration = parseInt($("#duration_" + id).val());

            $("#checklist_name").val($("#checklist_" + id).html());
            $("#kpi").val(kpi);
            $("#check_point").val(checkpoint);
            $("#duration").val(duration);
            $("#role_id").val($("#role_" + id).val());
            $("#selected_id").val(id);
        }

        function addMode() {
            $('#dTable').hide();
            $('#div_action').show();

            $("#checklist_name").val('');
            $("#kpi").val(0);
            $("#check_point").val(0);
            $("#duration").val(0);
            $("#selected_id").val(0);
        }

        function listMode() {
            $('#dTable').show();
            $('#div_action').hide();
        }

        function submitAction(template_id) {
            var strUrl = '';
            var checkListId = $("#selected_id").val();

            if (checkListId == '0') {
                strUrl = '/add_checklist_template/' + template_id;
            } else {
                strUrl = '/update_checklist_template/' + $("#selected_id").val();
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

        function orderSequence()
        {
            var form_data = new FormData();


            $.ajax({
                type: 'POST',
                url: '/reorder_sequence_checklist_template/' + template_id,
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
    </script>



    @endsection