@extends('dashboard.base')
<style>
    .fonticon-wrap {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .active-folder {
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div id="divList" class="row"
                style="background-color: white; max-height:980px; min-height:980px;padding-top:30px;overflow-y: auto;">

                <div class="col-sm-4">
                    {{-- <a class="btn btn-lg btn-primary  " href="javascript:void(0)" onclick="createFolderMode()">
                        <i class="cil-plus"> </i>Create new team
                    </a> --}}

                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                    onclick="editAccountItemModal('')" data-toggle="modal" data-target="#lawyerModal"
                    class="btn  btn-info no-print "><i class="cil-plus"> </i>Create new team</a>

                    <div class="collection file-manager-drive mt-3" style="background-color: white; max-height:980px; overflow-y: auto;">


                        @if (count($teams))
                            @foreach ($teams as $index => $team)
                                <a href="javascript:void(0)" id="{{ $team->team_main_id }}"
                                    class="collection-item file-item-action @if ($index == 0) active @endif">
                                    <div class="fonticon-wrap display-inline mr-3">
                                        <i class="@if ($team->id == 1) cil-user @else cil-people @endif "></i>


                                        <span style="margin-right:10px"
                                        class="label @if ($team->status == 1) bg-success @else bg-danger @endif ">
                                        @if ($team->status == 1)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                    </span>

                                        <span> {{ $team->name }}</span>
                                        <a href="javascript:void(0)"
                                            onclick="addFileMode('{{ $team->id }}', '{{ $team->name }}')"
                                            class="btn  btn-info shadow sharp ml-1 float-right hide" data-toggle="tooltip"
                                            data-placement="top" title="move"><i class="cil-folder"></i></a>

                                        <div class="btn-group  float-right" style="height: 30px;">
                                            <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                                data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/case/"><i style="margin-right: 10px;"
                                                        class="cil-chevron-double-right"></i>Set Status</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="/case/"><i style="margin-right: 10px;"
                                                        class="cil-x"></i>Remove Team</a>
                                            </div>
                                        </div>
                                        {{-- <span style="margin-left:10px"
                                            class="label @if ($team->status == 1) bg-success @else bg-danger @endif ">
                                            @if ($team->status == 1)
                                                Active
                                            @else
                                                Inactive
                                            @endif
                                        </span> --}}

                                        <!-- <span class="chip red lighten-5 float-right red-text">2</span> -->


                                        <!-- <div class="btn-group btn btn-info shadow sharp mr-1 float-right">
                              <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="notesEditMode('197')"><i class="cil-pencil"></i>Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="deleteNotes('197')"><i class="cil-x"></i>Delete</a>
                              </div>
                            </div> -->
                                    </div>

                                </a>
                                <hr />
                            @endforeach
                        @else
                        @endif

                    </div>
                </div>

                <div class="col-sm-8">

                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                        onclick="editAccountItemModal('')" data-toggle="modal" data-target="#staffModal"
                        class="btn  btn-primary no-print float-right"><i class="cil-plus"> </i>Add new member</a>

                    {{-- <a class="btn btn-lg btn-info float-right" href="{{ route('document-file.create') }}">
                        <i class="cil-plus"> </i>Add new member
                    </a> --}}

                    <div class="tab-content">


                        @if (count($teams))
                            @foreach ($teams as $index2 => $team)
                                <div class="tab-pane tab-pane-{{ $team->team_main_id }} @if ($index2 == 0) active @endif"
                                    id="tab_{{ $team->team_main_id }}" role="tabpanel"
                                    style="max-height:700px;overflow-y:auto">

                                    <div class="card-header">
                                        <h4>Team {{ $team->name }}</h4>
                                    </div>
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <!-- <th>No</th> -->
                                                <th>Name</th>
                                                <th>Remark</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 0;
                                            $total_count = 0;
                                            $file_count = 0;
                                            ?>
                                            @if (count($team->teams_members) > 0)
                                                @foreach ($team->teams_members as $index => $member)
                                                    <?php
                                                    $total_count += 1;
                                                    if (($index + 1) % 10 == 1) {
                                                        $count += 1;
                                                    }
                                                    ?>
                                                    @if ($member->id == $member->id)
                                                        <?php $file_count += 1; ?>
                                                        <tr
                                                            class="file-item filte-item-{{ $team->id }}-{{ $count }} ">
                                                            <td>{{ $member->name }}</td>
                                                            <td>{{ $member->id }}</td>
                                                            <td class="text-center">
                                                                @if ($member->status == 1)
                                                                    <span class="badge-pill badge-success">Active</span>
                                                                @else
                                                                    <span class="badge-pill badge-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ url('/document-file/' . $member->id . '/edit') }}"
                                                                    class="btn btn-primary shadow sharp mr-1"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Edit"><i class="cil-pencil"></i></a>
                                                                <a href="javascript:void(0)"
                                                                    onclick="deleteFile('{{ $member->id }}')"
                                                                    class="btn btn-danger"><i class="cil-x"></i></a>

                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">No member</td>
                                                </tr>
                                            @endif


                                        </tbody>
                                    </table>


                                </div>
                            @endforeach
                        @else
                        @endif


                    </div>


                </div>
            </div>

            <div id="dFolderCreate" class="card d_operation" style="display:none">
                <div class="card-header">
                    <h4>Create new folder</h4>
                </div>
                <div class="card-body">
                    <form id="form_action">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                                <input class="form-control" type="hidden" id="selected_id" name="selected_id"
                                    value="">
                                <input class="form-control" type="hidden" id="case_id_action" name="case_id_action"
                                    value="">
                                <div class="form-group row">
                                    <div class="col">
                                        <label>Folder name</label>
                                        <input class="form-control" type="text" value="" id="name"
                                            name="name" required>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col">
                                        <label>Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                                    </div>
                                </div>


                                <div class="form-group row">

                                    <div class="col">
                                        <label>Status</label>
                                        <select class="form-control" id="check_list_status" name="check_list_status">
                                            <option value="1" selected>Completed</option>
                                            <option value="0">Pending</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>

                    <button class="btn btn-success float-right" onclick="createFolder()" type="button">
                        <span id="span_upload">Create Folder</span>
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                    <a href="javascript:void(0);" onclick="listMode()" class="btn btn-danger">Cancel</a>

                </div>


            </div>


            <div id="div-add-file-list" class="card d_operation" style="display:none">
                <div class="card-header">
                    <h4 id="lbl-move-file"> </h4>
                </div>
                <div class="card-body">

                    <div class="form-group row ">
                        <div class="col-12">
                            <a href="javascript:void(0);" onclick="listMode()" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>


                    <div class="form-group row ">


                        <div class="col-6">
                            <!-- <label>Referral Name</label> -->
                            <input type="text" id="search_referral" name="search_referral"
                                placeholder="Search referral name" class="form-control" />
                        </div>
                        <div class="col-6">
                            <div class="form-group float-right ">

                                <input type="hidden" name="selected_folder_id" value="0" id="selected_folder_id">
                                <a class="btn btn-lg btn-primary" href="javascript:void(0)"
                                    onclick="moveFileFolder()">Move selected</a>
                            </div>
                        </div>

                    </div>


                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Name</th>
                                <!-- <th>Email</th>
                          <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody id="tbl-file-to-move">
                            @if (count($teams))
                                @foreach ($teams as $index => $team)
                                    <tr class="filter-item filter-item-{{ $team->id }}">
                                        <td class="text-center">
                                            <div class="checkbox">
                                                <input type="checkbox" name="files" value="{{ $team->id }}"
                                                    id="chk_{{ $team->id }}">
                                                <label for="chk_{{ $team->id }}">
                                                    {{ $index + 1 }}</label>
                                            </div>
                                        </td>
                                        <td>{{ $team->name }}</td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>

                    <!-- <button class="btn btn-primary  float-right" type="button" onclick="">
                  <i class="cil-plus"></i> Group voucher
                  <div class="overlay" style="display:none">
                    <i class="fa fa-refresh fa-spin"></i>
                  </div>
                </button> -->


                </div>
            </div>

            <div id="staffModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="form_clerk">
                                <div class="form-group row ">
                                    <div class="col">
                                        <label>Account Item</label>
                                        <select id="ddlclerk" class="form-control" name="ddlclerk">
                                            @if (count($clerks))
                                                @foreach ($clerks as $index => $clerk)
                                                    <option id="clerk{{ $clerk->id }}" value="{{ $clerk->id }}"
                                                        class="cat_id_all cat_id_{{ $clerk->id }}">
                                                        {{ $clerk->name }} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnClose2" class="btn btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right"
                                onclick="updateVoucherAccountItem()">Save
                                <div class="overlay" style="display:none">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div id="lawyerModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="form_clerk">
                                <div class="form-group row ">
                                    <div class="col">
                                        <label>Account Item</label>
                                        <select id="ddlclerk" class="form-control" name="ddlclerk">
                                            @if (count($lawyers))
                                                @foreach ($lawyers as $index => $lawyer)
                                                    <option id="lawyer{{ $lawyer->id }}" value="{{ $lawyer->id }}"
                                                        class="cat_id_all cat_id_{{ $lawyer->id }}">
                                                        {{ $lawyer->name }} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnClose2" class="btn btn-default"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success float-right"
                                onclick="updateVoucherAccountItem()">Save
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
    <script>
        $(".file-item-action").click(function() {
            // alert(this.id);
            $(".tab-pane").hide();
            $(".tab-pane-" + this.id).show();
        });

        function createFolderMode() {
            $("#divList").hide();
            $("#dFolderCreate").show();

        }

        function addFileMode(id, folder_name) {
            $("#divList").hide();
            $("#div-add-file-list").show();

            $(".filter-item").show();
            $(".filter-item-" + id).hide();

            $("#selected_folder_id").val(id);

            $("#lbl-move-file").html("Move file to " + folder_name);

        }

        function listMode() {
            $("#divList").show();
            $("#dFolderCreate").hide();
            $("#div-add-file-list").hide();

        }

        function moveFileFolder() {
            var bill_list = [];
            var bill = {};

            $.each($("input[name='files']:checked"), function() {

                itemID = $(this).val();

                bill = {
                    file_id: itemID,
                };

                bill_list.push(bill);


            });

            var form_data = new FormData();
            form_data.append("bill_list", JSON.stringify(bill_list));

            $.ajax({
                type: 'POST',
                url: '/move_file_folder/' + $("#selected_folder_id").val(),
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        location.reload();
                    }

                }
            });

            console.log(bill_list);
        }


        function generateFile(template_id) {
            $.ajax({
                type: 'POST',
                url: '/gen_file',
                data: {
                    template_id: template_id,
                    _token: '<?php echo csrf_token(); ?>'
                },
                success: function(data) {
                    alert(data);

                }
            });
        }

        function searchQuery() {
            if ($("#search_query").val() != '') {
                window.location.href = '/document-file?query=' + $("#search_query").val();
            }

        }

        function clearSearch() {
            window.location.href = '/document-file';

        }

        function createFolder() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/create_folder',
                data: $('#form_action').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {

                        Swal.fire('Success!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }

                }
            });

        }

        function deleteFile($id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Delete this template?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/delete_file/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })

        }
    </script>
@endsection
