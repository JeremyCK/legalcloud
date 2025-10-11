@extends('dashboard.base')

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <div class="card ">

                        <div class="card-header">
                            <h4> Create new Safe Keeping </h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            {{-- <form id="form_adjudication" class=" dropzone"  method="POST" action="{{ route('safe-keeping.store') }}"  enctype="multipart/form-data"> --}}
                            <form id="form_adjudication" class=" dropzone" enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="col-12 " style="margin-bottom:10px;">
                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                            data-toggle="modal" data-target="#myModalInvoice" onclick="editInvoiceModal()"
                                            class="btn btn-info  sharp " data-toggle="tooltip" data-placement="top"
                                            title="View">Search Case Ref No/Client</a>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Case Ref No </label>
                                                <input class="form-control" type="text" name="case_ref_no" required>
                                                <input class="form-control" type="hidden" name="case_id" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client</label>
                                                <input class="form-control" type="text" name="client" required>
                                                <input class="form-control" type="hidden" name="client_id" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Document Sent</label>
                                                <input class="form-control" type="text" name="document_sent">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Attention To</label>
                                                <input class="form-control" type="text" name="attention_to">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Received</label>
                                                <select class="form-control" name="received">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" name="branch">
                                                    @foreach ($branch as $index => $bran)
                                                        <option value="{{ $bran->id }}">{{ $bran->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Attachment</label>
                                            <input class="form-control" id="attachment_file" name="attachment_file" type="file">
                                        </div>
                                    </div>

                                </div> --}}

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remark</label>
                                                <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">

                                                <div class="fallback">
                                                    <input type="file" name="file" multiple />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="dz-message needsclick">
                                    <i class="ki-duotone ki-file-up fs-3x text-primary"><span class="path1"></span><span
                                            class="path2"></span></i>

                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Upload up to 5 files</span>
                                    </div>
                                </div>


                                {{-- <button class="btn btn-success float-right" type="submit">{{ __('coreuiforms.save') }}</button> --}}

                            </form>
                            <button class="btn btn-success float-right" type="button"
                                onclick="SaveRecord()">{{ __('coreuiforms.save') }}</button>
                            <a href="{{ route('adjudication.index') }}"
                                class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
                        </div>
                    </div>
                </div>


            </div>

            <div id="myModalInvoice" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="form_edit_quotation">
                                <div class="form-group row ">

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="col">
                                            <label>Search Case</label>
                                            <input type="text" id="search_case" name="search_case"
                                                class="form-control search_referral" placeholder="Search Case"
                                                autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="col">
                                            <label>Search Client</label>
                                            <input type="text" id="search_client" name="search_client"
                                                class="form-control search_referral" placeholder="Search Client"
                                                autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-12" style="margin-top:10px">
                                        <table class="table  table-bordered datatable">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Case Ref No</th>
                                                    <th>Client</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl-case">
                                                @if (count($loan_case))
                                                    @foreach ($loan_case as $index => $case)
                                                        <tr id="case_{{ $case->id }}" style="display:none">
                                                            <td>{{ $case->case_ref_no }}</td>
                                                            <td>{{ $case->name }}</td>
                                                            <td style="display:none">{{ $case->id }}</td>
                                                            <td style="display:none">{{ $case->customer_id }}</td>
                                                            <td class="text-center">
                                                                <a href="javascript:void(0)"
                                                                    onclick="selectedCase('{{ $case->id }}');"
                                                                    class="btn btn-primary shadow btn-xs sharp mr-1"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="case">Select</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="3">No data</td>
                                                    </tr>
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btnClose" class="btn btn-default"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script>
        $record_id = 0;
        Dropzone.autoDiscover = false;
        var drop = document.getElementById('form_adjudication')
        var myDropzone = new Dropzone(drop, {
            url: "/uploadSafeKeepingFile",
            addRemoveLinks: true,
            autoProcessQueue: false,
            maxFilesize: 10, // MB
            maxFiles: 5,
            uploadMultiple: true,
            parallelUploads: 10,
            sending: function(file, xhr, formData) {
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("record_id", $record_id);
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeFile(file);
                    // showAlert("File Limit exceeded!", "error");
                });
            },
            success: function(file, response) {
                // console.log(response);
                $.each(myDropzone.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });

                if (response.status == 1) {
                    Swal.fire('Success!', response.message, 'success');
                    window.location.href = '/safe-keeping';
                } else {

                }
            },
            error: function(file, response) {
                $.each(myDropzone.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });
            }


        });

        function SaveRecord() {
            $.ajax({
                type: 'POST',
                url: '/storeSafeKeepingRecord/',
                data: $('#form_adjudication').serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {

                        if(myDropzone.files.length > 0)
                        {
                            $record_id = data.record_id;
                            myDropzone.processQueue();
                        }
                        else
                        {
                            Swal.fire('Success!', data.message, 'success');
                            window.location.href = '/safe-keeping';
                        }

                    } else {
                        Swal.fire('notice!', data.message, 'warning');
                    }

                }
            });
        }

        function senf() {
            console.log(myDropzone.files);

            var form_data = new FormData();

            var files = $('#name')[0].files;
            console.log(files[0]);
            formData.append('case_file', files[0]);

            // form_data.append('files', myDropzone.files);
            form_data.append('files', $(""));

            $.ajax({
                type: 'POST',
                url: '/uploadSafeKeepingFile/',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    // if (data.status == 1) {

                    //     Swal.fire('Success!', $SuccessMSG, 'success');
                    //     window.location.href = '/case';
                    // } else {
                    //     Swal.fire('notice!', data.message, 'warning');
                    // }

                }
            });
            // myDropzone.processQueue();
        }


        $('#search_case').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_case");
            filter = input.value.toUpperCase();
            $("#search_client").val();

            $("#tbl-case tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(0)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            if (filter == "") {
                $("#tbl-case tr").each(function() {
                    var self = $(this);
                    $(this).hide();
                })
            }
        });

        $('#search_client').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_client");
            filter = input.value.toUpperCase();
            $("#search_case").val();

            $("#tbl-case tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(1)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            if (filter == "") {
                $("#tbl-case tr").each(function() {
                    var self = $(this);
                    $(this).hide();
                })
            }
        });

        function selectedCase(id) {
            $("#tbl-case tr#case_" + id).each(function() {
                var self = $(this);
                var case_ref_no = self.find("td:eq(0)").text().trim();
                var client = self.find("td:eq(1)").text().trim();
                var case_id = self.find("td:eq(2)").text().trim();
                var client_id = self.find("td:eq(3)").text().trim();

                var form = $("#form_adjudication");

                form.find('[name=case_ref_no]').val(case_ref_no);
                form.find('[name=case_id]').val(case_id);
                form.find('[name=client]').val(client);
                form.find('[name=client_id]').val(client_id);

                $('#btnClose').click();
                $(".modal-backdrop").remove();

            })
        }
    </script>
@endsection
