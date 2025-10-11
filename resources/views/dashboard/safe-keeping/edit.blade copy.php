@extends('dashboard.base')

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <div class="card ">

                        <div class="card-header">
                            <h4> Edit Safe Keeping </h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            <form id="form_adjudication" method="POST"
                                action="{{ route('safe-keeping.update', $adjudication->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
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
                                                <label>Case Ref No</label>
                                                <input class="form-control"
                                                    value="@if ($adjudication->case_id == 0) {{ $adjudication->case_ref }} @else {{ $adjudication->case_ref_no }} @endif "
                                                    type="text" name="case_ref_no" required>
                                                <input class="form-control" type="hidden"
                                                    value="{{ $adjudication->case_id }}" name="case_id">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client</label>
                                                <input class="form-control" type="text" name="client"
                                                    value="{{ $adjudication->client_name }}" required>
                                                <input class="form-control" type="hidden" name="client_id"
                                                    value="{{ $adjudication->client_id }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Document Sent</label>
                                                <input class="form-control" type="text" name="document_sent"
                                                    value="{{ $adjudication->document_sent }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Attention To</label>
                                                <input class="form-control" type="text" name="attention_to"
                                                    value="{{ $adjudication->attention_to }}">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" name="branch">
                                                    @foreach ($branch as $index => $bran)
                                                        <option value="{{ $bran->id }}"
                                                            @if ($bran->id == $adjudication->branch) selected @endif>
                                                            {{ $bran->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Received</label>
                                                <select class="form-control" name="received">
                                                    <option value="0"
                                                        @if ($adjudication->received == 1) selected @endif>No</option>
                                                    <option value="1"
                                                        @if ($adjudication->received == 1) selected @endif>Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remark</label>
                                                <textarea class="form-control" id="remark" name="remark" rows="3">{{ $adjudication->remark }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Attachment</label>
                                                <input class="form-control" id="attachment_file" name="attachment_file"
                                                    type="file">
                                            </div>
                                        </div>
                                        @if ($adjudication->file_new_name != '')
                                            <div class="form-group row">

                                                <div class="col">
                                                    <ul class="mailbox-attachments clearfix">
                                                        <li>
                                                            <span class="mailbox-attachment-icon"><i
                                                                    class="fa fa-file-pdf-o"></i></span>

                                                            <div class="mailbox-attachment-info">

                                                                @if ($adjudication->s3_file_name)
                                                                    <a href="javascript:void(0)"
                                                                        onclick="openFileFromS3('{{ $adjudication->s3_file_name }}')"
                                                                        class="mailbox-attachment-name"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="Download">{{ $adjudication->file_ori_name }}</a>
                                                                @else
                                                                    <a target="_blank"
                                                                        href="/app/documents/safekeeping/{{ $adjudication->file_new_name }}"
                                                                        class="mailbox-attachment-name"><i
                                                                            class="fa fa-paperclip"></i>
                                                                        {{ $adjudication->file_ori_name }}</a>
                                                                @endif


                                                                <span class="mailbox-attachment-size">
                                                                    download
                                                                    <a href="javascript:void(0)"
                                                                        class="btn btn-default btn-xs pull-right"><i
                                                                            class="fa fa-cloud-download"></i></a>
                                                                </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <button class="btn btn-success float-right"
                                    type="submit">{{ __('coreuiforms.save') }}</button>
                                <a href="{{ route('safe-keeping.index') }}"
                                    class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
                            </form>



                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div id="d-listing" class="card">
                        <div class="card-header">

                            <div class="row">

                                <div class="col-sm-6">
                                    <h4><i class="fa fa-briefcase  "></i> Files</h4>
                                </div>

                                <div class="col-sm-6">
                                    <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)"
                                        onclick="fileMode()">
                                        <i class="cil-cloud-upload"> </i>Upload new file
                                    </a>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">

                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>File Name</th>
                                        {{-- <th>Date</th>
                                        <th>Remarks</th> --}}
                                        <th>Upload By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($Attachment))
                                        @foreach ($Attachment as $index => $file)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $file->file_ori_name }} <br />
                                                </td>
                                                <td>
                                                    {{ $file->upload_by }} <br /><span
                                                        style="font-size: 10px;color:gray">{{ $file->created_at }}</span>
                                                </td>
                                                <td class="text-center">

                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-info btn-flat">Action</button>
                                                        <button type="button"
                                                            class="btn btn-info btn-flat dropdown-toggle"
                                                            data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <div class="dropdown-menu" style="padding:0">

                                                            @if ($file->s3_file_name)
                                                                <a href="javascript:void(0)"
                                                                    onclick="openFileFromS3('{{ $file->s3_file_name }}')"
                                                                    class="mailbox-attachment-name" data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title="Download">{{ $file->file_ori_name }}</a>

                                                                    <a class="dropdown-item btn-success" 
                                                                    href="javascript:void(0)"
                                                                    onclick="openFileFromS3('{{ $file->s3_file_name }}')"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="cil-cloud-download"></i>Download</a>
                                                            @else
                                                                <a class="dropdown-item btn-success" target="_blank"
                                                                    href="/{{ $template_path . $file->file_ori_name }}"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="cil-cloud-download"></i>Download</a>
                                                                <div class="dropdown-divider" style="margin:0"></div>
                                                                <a class="dropdown-item btn-warning" target="_blank"
                                                                    href="/{{ $template_path_old . $file->file_ori_name }}"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="cil-cloud-download"></i>Download Old
                                                                    File</a>
                                                            @endif

                                                        </div>
                                                    </div>
                                                    {{-- <a target="_blank" href="/{{ $template_path . $file->file_name }}"
                                                        class="btn btn-info shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Download"><i
                                                            class="cil-cloud-download"></i></a><br />

                                                    <a target="_blank"
                                                        href="/{{ $template_path_old . $file->file_name }}"
                                                        class="btn btn-warning shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Download">Old File</a><br />

                                                    @if ($voucherMain->account_approval == 0 || in_array($current_user->menuroles, ['admin']))
                                                        @if ($current_user->id == $file->created_by || in_array($current_user->menuroles, ['admin', 'management', 'account']))
                                                            <a href="javascript:void(0)"
                                                                onclick="deleteVoucherAttachment('{{ $file->id }}')"
                                                                class="btn btn-danger"><i class="cil-x"></i></a>
                                                        @endif
                                                    @endif --}}





                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="7">No data</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
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
    <script>
        function openFileFromS3(filename) {
            var form_data = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            form_data.append("filename", filename);
            // form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

            $.ajax({
                type: 'POST',
                url: '/getFileFromS3',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                        window.location.href = data;
                    }
                    else
                    {
                        window.open(data, "_blank");
                    }
                }
            });
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
