@extends('dashboard.base')
<style>
    /* .fonticon-wrap {
        margin-top: 20px;
        margin-bottom: 20px;
    } */

    .active-folder {
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Folder</h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-primary  " href="javascript:void(0)" data-backdrop="static"
                                        data-keyboard="false" data-target="#modalCreateFolder" data-toggle="modal">
                                        <i class="cil-plus"> </i>Create new folder
                                    </a>
                                </div>

                            </div>

                        </div>
                        <div class="card-body">
                            <div class="collection file-manager-drive mt-3" style="max-height:700px;overflow-y:auto">


                                @if (count($fileFolder))
                                    @foreach ($fileFolder as $index => $folder)
                                        <div class="fonticon-wrap display-inline mr-3" style="font-size: 12px !important">
                                            <input type="hidden" name="selected_folder_id" value="0"
                                                id="selected_folder_id">
                                            <a href="javascript:void(0)"
                                                onclick="addFileMode('{{ $folder->id }}', '{{ $folder->name }}')"
                                                class="btn btn-info shadow sharp mr-1 float-right hide"
                                                data-toggle="tooltip" data-placement="top" title="move"><i
                                                    class="cil-folder"></i></a>

                                            <div class="btn-group  float-right">
                                                <button type="button" class="btn btn-info btn- dropdown-toggle"
                                                    data-toggle="dropdown">
                                                    <span class="caret" style="font-size: 10px">Action</span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu" style="padding:0">

                                                    <div class="dropdown-divider" style="margin:0"></div>


                                                    {{-- <a class="dropdown-item btn-success" href="javascript:void(0)"
                                                        onclick="addFileMode('{{ $folder->id }}', '{{ $folder->name }}')"
                                                        data-backdrop="static" data-keyboard="false"
                                                        data-target="#modalMoveFile" data-toggle="modal"
                                                        style="color:white;margin:0">
                                                        <i style="margin-right: 10px;" class="cil-folder"></i>Move Filesa
                                                    </a> --}}
                                                    <a class="dropdown-item btn-success" href="javascript:void(0)"
                                                        onclick="reloadTable('{{ $folder->id }}', '{{ $folder->name }}')"
                                                        data-backdrop="static" data-keyboard="false"
                                                        data-target="#modalMoveFile" data-toggle="modal"
                                                        style="color:white;margin:0">
                                                        <i style="margin-right: 10px;" class="cil-folder"></i>Move File
                                                    </a>
                                                    <input type="hidden" id="remark_{{ $folder->id }}"
                                                        value="{{ $folder->remarks }}" />
                                                    <a class="dropdown-item btn-info" href="javascript:void(0)"
                                                        onclick="loadEditData('{{ $folder->id }}', '{{ $folder->name }}', '{{ $folder->status }}')"
                                                        data-backdrop="static" data-keyboard="false"
                                                        data-target="#modalEditFolder" data-toggle="modal"
                                                        style="color:white;margin:0">
                                                        <i style="margin-right: 10px;" class="cil-pencil"></i>Edit
                                                        Folder
                                                    </a>
                                                    @if ($folder->no_delete == 0)
                                                        <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                                            onclick="deleteFolder('{{ $folder->id }}')"
                                                            style="color:white;margin:0">
                                                            <i style="margin-right: 10px;" class="cil-x"></i>Delete
                                                            Folder
                                                        </a>
                                                    @endif

                                                </div>
                                            </div>
                                            <a href="javascript:void(0)" id="{{ $folder->id }}"
                                                class="collection-item file-item-action">
                                                <i
                                                    class="@if ($folder->type == 1) cil-folder-open @else cil-folder @endif "></i>
                                                {{ $folder->name }}
                                                @if ($folder->status == 0)
                                                    <span class="text-danger">(Draft)</span>
                                                @endif
                                                <br />
                                                <span style="font-size: 10px;color:gray">{{ $folder->count }} files</span>


                                                @php
                                                    $span = 'success';

                                                    if ($folder->count == 0) {
                                                        $span = 'danger';
                                                    }
                                                @endphp
                                                {{-- <span style="margin: 5px"
                                          class="label bg-{{ $span }} float-right">{{ $folder->count }}</span> --}}
                                        </div>

                                        </a>
                                        <hr style="margin-top:5px;margin-bottom:5px " />
                                    @endforeach
                                @else
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>File Template</h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info float-right" href="{{ route('document-file.create') }}">
                                        <i class="cil-plus"> </i>Create new file template
                                    </a>
                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="">



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
                                                            <th>Files</th>
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
                                                                        <td>{{ $template->name }}</td>
                                                                        <td>{{ $template->remarks }}</td>
                                                                        <td>{{ $template->count }}</td>
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
                                                                                data-toggle="tooltip" data-placement="top"
                                                                                title="Edit"><i
                                                                                    class="cil-pencil"></i></a>
                                                                            <!-- <a href="{{ url('/document-file/' . $template->id . '/edit') }}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="move"><i class="cil-folder"></i></a> -->
                                                                            <a href="javascript:void(0)"
                                                                                onclick="deleteFile('{{ $template->id }}', {{ $template->count }})"
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
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('dashboard.documentFile.modals.modal-file')
    @include('dashboard.documentFile.modals.modal-create-folder')
    @include('dashboard.documentFile.modals.modal-edit-folder')

@endsection

@section('javascript')
    <script>
        var table = null;

        function reloadTable($folder_id, folder_name) {
            $("#lbl-move-file").html("Move file to " + folder_name);
            $("#selected_folder_id").val($folder_id);

            table = $('#tbl-movefile').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                responsive: true,
                ajax: {
                    url: "{{ route('documentFileMainList.list') }}",
                    data: function(d) {
                        d.folder_id = $folder_id;
                    },
                },
                columns: [{
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'folder_name',
                        name: 'folder_name'
                    },
                ]
            });
        }

        $(function() {
            // reloadTable();
        });

        function iniTable() {

            // if ($('#tbl-movefile').destroy() != null)

            if (table != null) {
                // $("#tbl-file-to-move").html('');
                // table.destroy();
                // oTable.draw(false);
                // table.redraw();
                return;
            }

            table = $('#tbl-movefile').DataTable({
                "pagingType": "full_numbers",
                "destroy": true,
                "processing": true,
                // "serverSide": true,
                // "destroy": true,
                // "pageLength": 50,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }
            });
        }


        $(".file-item-action").click(function() {
            // alert(this.id);
            $(".tab-pane").hide();
            $(".tab-pane-" + this.id).show();
        });

        function createFolderMode() {
            $("#divList").hide();
            $("#dFolderCreate").show();

        }

        function loadFileList($selected_folder_id) {
            $.ajax({
                type: 'POST',
                url: '/getDocumentFileMainList/' + $selected_folder_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);


                    $("#tbl-file-to-move").html(data.table);

                    if (table != null) {
                        // $("#tbl-file-to-move").html('');
                        // table.destroy();
                        table.draw(false);
                        // table.redraw();
                        // return;
                    }
                    // table.draw();
                    // iniTable();
                }
            });
        }

        function addFileMode(id, folder_name) {
            $("#divList").hide();
            $("#div-add-file-list").show();

            $(".filter-item").show();
            $(".filter-item-" + id).hide();

            $("#selected_folder_id").val(id);

            $("#lbl-move-file").html("Move file to " + folder_name);
            // loadFileList();
            reloadTable();

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
            console.log(bill_list);
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

        function editFolder() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/edit_folder',
                data: $('#form_edit').serialize(),
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

        function deleteFile($id, $fileCount) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if ($fileCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Confirmation',
                    text: 'There are active file in this template, please remove or deactivate it before delete?',
                    confirmButtonText: `Close`,
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

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

        function loadEditData($id, $fileName, $fileStatus) {
            var form = $("#form_edit");

            form.find('[name=id]').val($id);
            form.find('[name=name]').val($fileName);
            form.find('[name=remarks]').val($("#remarks_" + $id).val());
            form.find('[name=folder_status]').val($fileStatus);
        }

        function deleteFolder($id, $fileCount) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            Swal.fire({
                title: 'Delete this folder?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteFolder/' + $id,
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
