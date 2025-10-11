@extends('dashboard.base')

@section('content')


    <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    {{-- @include('dashboard.shared.components.alert') --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit File Template</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('document-file.update', $template->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="name">Template Name</label>
                                            <input class="form-control" id="template_id" name="template_id" value="{{ $template->id }}"  type="hidden" />
                                            <div class="col-md-9">
                                                <input class="form-control" name="name" value="{{ $template->name }}"
                                                    type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="remarks">Remark</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" id="remarks" name="remarks" rows="2">{{ $template->remarks }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="status">Folder</label>
                                            <div class="col-md-9">
                                                <select id="folder_id" class="form-control" name="folder_id" required>
                                                    @foreach ($fileFolder as $index => $folder)
                                                        <option value="{{ $folder->id }}" @if($template->folder_id == $folder->id) selected @endif>{{ $folder->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="status">Status</label>
                                            <div class="col-md-9"><select class="form-control" id="status"
                                                    name="status">
                                                    <option value="1" @if($template->status == 1) selected @endif> Active</option>
                                                    <option value="0" @if($template->status == 0) selected @endif> Draft</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <button class="btn btn-primary float-right" type="submit">Save</button>
                                <a class="btn btn-danger" href="{{ route('document-file.index') }}">Return</a>
                            </form>
                        </div>
                    </div>

                    <div id="d-listing" class="card">
                        <div class="card-header">
                            <h4>File history</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)"
                                        onclick="fileMode()">
                                        <i class="cil-cloud-upload"> </i>Upload new file
                                    </a>
                                </div>

                            </div>
                            <br>
                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>File Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($details))
                                        @foreach ($details as $index => $detail)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $detail->file_name }}</td>
                                                <td>{{ $detail->created_at }} </td>
                                                <td class="text-center">
                                                    @if ($detail->status == 1)
                                                        <span class="badge-pill badge-success">Active</span>
                                                    @elseif($detail->status == 0)
                                                        <span class="badge-pill badge-warning">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a target="_blank"
                                                        href="/{{ $template_path . 'file_template_' . $template->id . '/' . $detail->file_name }}"
                                                        class="btn btn-info shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Download"><i
                                                            class="cil-cloud-download"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="setFileActive('{{ $template->id }}','{{ $detail->id }}')"
                                                        class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Set active"><i
                                                            class="cil-check"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="deleteUploadedFile('{{ $template->id }}','{{ $detail->id }}')"
                                                        class="btn btn-danger shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Set active"><i class="cil-x"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="5">No data</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div id="dFile" class="card d_operation" style="display:none">

                        <div class="card-header">
                            <h4>Upload file</h4>
                        </div>
                        <div class="card-body">
                            <form id="form_file" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                                        <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
                                        <input class="form-control" type="hidden" id="case_id" name="case_id"
                                            value="">
                                        <div id="field_file" class="form-group row">
                                            <div class="col">
                                                <label>File</label>
                                                <input class="form-control" type="file" id="inp_file"
                                                    name="inp_file">
                                            </div>
                                        </div>

                                        <button class="btn btn-success float-right"
                                            onclick="uploadFile('{{ $template->id }}')" type="button">
                                            <span id="span_upload">Upload</span>
                                            <div class="overlay" style="display:none">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>
                                        </button>
                                        <a href="javascript:void(0);" onclick="viewMode()"
                                            class="btn btn-primary">Cancel</a>
                                    </div>
                                </div>
                            </form>

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

    @if (Session::has('message'))
        toastController('{{ Session::get('message') }}');
    @endif
        function fileMode() {
            $("#d-listing").hide();
            $("#dFile").show();
        }

        function viewMode() {
            $("#d-listing").show();
            $("#dFile").hide();
        }

        function setFileActive($template_id, $fileID) {

            var formData = new FormData();
            formData.append('file_id', $fileID);
            formData.append('id', $template_id);

            $.ajax({
                type: 'POST',
                url: '/set_file_active',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);

                    Swal.fire('Success!', 'File Updated', 'success')
                    location.reload();
                }
            });
        }

        function deleteUploadedFile($templteID, $fileID) {

            Swal.fire({
                title: 'Delete this file?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteUploadedFile/' + $templteID + '/' + $fileID,
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(data) {

                            console.log(data);

                            if (data.status == 1) {
                                Swal.fire('Success!', 'File deleted', 'success')
                                location.reload();
                            } else {
                                Swal.fire('Notice!', data.message, 'warning')
                            }


                        }
                    });
                } else if (result.isDenied) {}
            })



        }

        function downloadOnlyChecked()
        {
            updateMainTemplateInfo($("#template_id").val());
        }

        function updateMainTemplateInfo($template_id) {
            
            $downloadOnly = false;

            if($('#downloadOnly').is(':checked') == true)
            {
                $downloadOnly = true;
            }

            var formData = new FormData();
            formData.append('name', $name);
            formData.append('remarks', $remarks);
            formData.append('status', $status);
            formData.append('downloadOnly', $downloadOnly);

            $.ajax({
                type: 'POST',
                url: '/update_file_template_info/' + $template_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);

                    Swal.fire('Success!', 'Template updated', 'success')
                    location.reload();
                }
            });
        }

        function uploadFile($template_id) {
            $("#span_update").hide();
            $(".overlay").show();

            $downloadOnly = false;

            var formData = new FormData();

            if($('#downloadOnly').is(':checked') == true)
            {
                $downloadOnly = true;
            }

            var files = $('#inp_file')[0].files;
            console.log(files[0]);
            formData.append('inp_file', files[0]);
            formData.append('downloadOnly', $downloadOnly);

            // formData.append('_token','<?php echo csrf_token(); ?>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/upload_file_template/' + $template_id,
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $("#span_update").hide();
                    $(".overlay").hide();
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            'Checklist Updated',
                            'success'
                        )
                        location.reload();
                    } else {
                        Swal.fire(
                            'Notice!',
                            data.data,
                            'warning'
                        )
                    }

                }
            });
        }
    </script>
@endsection
