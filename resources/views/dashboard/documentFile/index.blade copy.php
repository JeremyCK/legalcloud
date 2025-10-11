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
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Case Template</h4>
                        </div>
                        <div class="card-body">
                            <div id="divList" class="row"
                                style="background-color: white; min-height:980px;padding-top:30px">
                                {{-- style="background-color: white; max-height:980px; min-height:980px;padding-top:30px"> --}}

                                <div class="col-sm-3">
                                    <a class="btn btn-lg btn-primary  " href="javascript:void(0)"
                                        onclick="createFolderMode()">
                                        <i class="cil-plus"> </i>Create new folder
                                    </a>
                                    <div class="collection file-manager-drive mt-3" style="">


                                        @if (count($fileFolder))
                                            @foreach ($fileFolder as $index => $folder)
                                                <a href="javascript:void(0)" id="{{ $folder->id }}"
                                                    class="collection-item file-item-action">
                                                    <div class="fonticon-wrap display-inline mr-3">

                                                        <i
                                                            class="@if ($folder->type == 1) cil-folder-open @else cil-folder @endif "></i>
                                                        {{ $folder->name }}



                                                        <a href="javascript:void(0)"
                                                            onclick="addFileMode('{{ $folder->id }}', '{{ $folder->name }}')"
                                                            class="btn btn-info shadow sharp mr-1 float-right hide"
                                                            data-toggle="tooltip" data-placement="top" title="move"><i
                                                                class="cil-folder"></i></a>

                                                        <div class="btn-group  float-right">
                                                            <button type="button" class="btn btn-info btn- dropdown-toggle"
                                                                data-toggle="dropdown">
                                                                <span class="caret">Action</span>
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <div class="dropdown-menu" style="padding:0">

                                                                <div class="dropdown-divider" style="margin:0"></div>


                                                                <a class="dropdown-item btn-success"
                                                                    href="javascript:void(0)"
                                                                    onclick="addFileMode('{{ $folder->id }}', '{{ $folder->name }}')"
                                                                    style="color:white;margin:0">
                                                                    <i style="margin-right: 10px;"
                                                                        class="cil-folder"></i>Move Files
                                                                </a>
                                                                <a class="dropdown-item btn-info" href="javascript:void(0)"
                                                                    onclick="billListMode('4854', 'Filing Claim')"
                                                                    style="color:white;margin:0">
                                                                    <i style="margin-right: 10px;"
                                                                        class="cil-pencil"></i>Edit Folder
                                                                </a>

                                                            </div>
                                                        </div>


                                                        @php
                                                            $span = 'success';

                                                            if ($folder->count == 0) {
                                                                $span = 'danger';
                                                            }
                                                        @endphp
                                                        <span style="margin: 5px"
                                                            class="label bg-{{ $span }} float-right">{{ $folder->count }}</span>
                                                    </div>

                                                </a>
                                                <hr />
                                            @endforeach
                                        @else
                                        @endif

                                    </div>
                                </div>

                                <div class="col-sm-9">

                                    <a class="btn btn-lg btn-info " href="{{ route('document-file.create') }}">
                                        <i class="cil-plus"> </i>Create new file template
                                    </a>

                                    <div class="tab-content">


                                        @if (count($fileFolder))
                                            @foreach ($fileFolder as $index2 => $folder)
                                                <div class="tab-pane tab-pane-{{ $folder->id }} @if ($folder->id == 1) active @endif"
                                                    id="tab_{{ $folder->id }}" role="tabpanel"
                                                    style="max-height:700px;overflow-y:auto">

                                                    <div class="card-header">
                                                        <h4> {{ $folder->name }}</h4>
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
                                                            @if (count($documentTemplateFilev2))
                                                                @foreach ($documentTemplateFilev2 as $index => $template)
                                                                    <?php
                                                                    $total_count += 1;
                                                                    if (($index + 1) % 10 == 1) {
                                                                        $count += 1;
                                                                    }
                                                                    ?>
                                                                    @if ($template->folder_id == $folder->id)
                                                                        <?php $file_count += 1; ?>
                                                                        <tr
                                                                            class="file-item filte-item-{{ $template->type }}-{{ $count }} ">
                                                                            <!-- <td class="text-center">
                                    <div class="checkbox">
                                      <input type="checkbox" name="files" value="{{ $template->id }}" id="chk_{{ $template->id }}" checked>
                                      <label for="chk_{{ $template->id }}">{{ $index + 1 }}</label>
                                    </div>
                                  </td> -->
                                                                            <td>{{ $template->name }}</td>
                                                                            <td>{{ $template->remarks }}</td>
                                                                            <td class="text-center">
                                                                                @if ($template->status == 1)
                                                                                    <span
                                                                                        class="badge-pill badge-success">Active</span>
                                                                                @elseif($template->status == 0)
                                                                                    <span
                                                                                        class="badge-pill badge-warning">Draft</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <a href="{{ url('/document-file/' . $template->id . '/edit') }}"
                                                                                    class="btn btn-primary shadow sharp mr-1"
                                                                                    data-toggle="tooltip"
                                                                                    data-placement="top" title="Edit"><i
                                                                                        class="cil-pencil"></i></a>
                                                                                <!-- <a href="{{ url('/document-file/' . $template->id . '/edit') }}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="move"><i class="cil-folder"></i></a> -->
                                                                                <a href="javascript:void(0)"
                                                                                    onclick="deleteFile('{{ $template->id }}')"
                                                                                    class="btn btn-danger"><i
                                                                                        class="cil-x"></i></a>

                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach

                                                                @if ($file_count == 0)
                                                                    <tr>
                                                                        <td class="text-center" colspan="5">No data</td>
                                                                    </tr>
                                                                @else
                                                                    @foreach ($documentTemplateFilev2 as $index => $template)
                                                                    @endforeach
                                                                @endif
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

                                                <input class="form-control" type="hidden" id="selected_id"
                                                    name="selected_id" value="">
                                                <input class="form-control" type="hidden" id="case_id_action"
                                                    name="case_id_action" value="">
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Folder name</label>
                                                        <input class="form-control" type="text" value=""
                                                            id="name" name="name" required>
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
                                                        <select class="form-control" id="check_list_status"
                                                            name="check_list_status">
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
                                            <a href="javascript:void(0);" onclick="listMode()"
                                                class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>


                                    <div class="form-group row ">


                                        <div class="col-6">
                                            <input type="text" id="search_referral" name="search_referral"
                                                placeholder="Search referral name" class="form-control" />
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group float-right ">

                                                <input type="hidden" name="selected_folder_id" value="0"
                                                    id="selected_folder_id">
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
                                            </tr>
                                        </thead>
                                        <tbody id="tbl-file-to-move">
                                            @if (count($documentTemplateFilev2))
                                                @foreach ($documentTemplateFilev2 as $index => $template)
                                                    <tr class="filter-item filter-item-{{ $template->type }}">
                                                        <td class="text-center">
                                                            <div class="checkbox">
                                                                <input type="checkbox" name="files"
                                                                    value="{{ $template->id }}"
                                                                    id="chk_{{ $template->id }}">
                                                                <label for="chk_{{ $template->id }}">
                                                                    {{ $index + 1 }}</label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $template->name }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif

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
