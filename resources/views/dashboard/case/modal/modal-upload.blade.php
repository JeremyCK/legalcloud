{{-- <style>
    .print-table-receipt>tbody>tr>td,
    .print-table-receipt>tbody>tr>th,
    .print-table-receipt>tfoot>tr>td,
    .print-table-receipt>tfoot>tr>th,
    .print-table-receipt>thead>tr>td,
    .print-table-receipt>thead>tr>th {
        border: 1px solid black;
    }

    .print-receipt>tbody>tr>td,
    .print-receipt>tbody>tr>th,
    .print-receipt>tfoot>tr>td,
    .print-receipt>tfoot>tr>th,
    .print-receipt>thead>tr>td,
    .print-receipt>thead>tr>th {
        padding: 5px !important;
    }

    .wrap {
        margin: 10px;
        display: flex;
    }

    .wrap span {
        align-self: flex-end;
    }

 
</style> --}}
<div id="modalUpload" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-print" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Upload Documents</h4>
                    </div>
                    <div class="col-6">
                        {{-- <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button> --}}
                        <a href="javascript:void(0);" type="button" data-dismiss="modal"
                            class="btn btn-danger float-right no-print btn_close_all"> <i class=" cli-x"></i> Close</a>
                    </div>
                </div>

            </div>
            <div class="modal-body">

                <form id="form_file_modal" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                    <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
                    {{-- <input class="form-control" type="hidden" id="case_id" name="case_id" value=""> --}}
                    <input class="form-control" type="hidden" id="file_type_modal" name="file_type_modal" value="1">
                    <input class="form-control" type="hidden" id="checklist_id" name="checklist_id" value="1">

                    {{-- <div id="div_attachment_type" class="form-group row">
                        <div class="col">
                            <label>File</label>
                            <select class="form-control" id="attachment_type" name="attachment_type">
                                @foreach ($attachment_type as $index => $type)
                                    @if ($type->parameter_value_3 == 1)
                                        @if (in_array($current_user->menuroles, ['admin','account', 'maker']) || in_array($current_user->id, [51]))
                                        <option value="{{ $type->parameter_value_2 }}" @if($type->parameter_value_2 == 6) selected @endif >{{ $type->parameter_value_1 }}</option>
                                        @endif
                                        
                                    @else
                                        <option value="{{ $type->parameter_value_2 }}" @if($type->parameter_value_2 == 4) selected @endif>
                                            {{ $type->parameter_value_1 }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="need-remark form-group row">
                        <div class="col">
                            <label>Remarks</label>
                            <textarea class="form-control" id="file_remark" name="remark" rows="3"></textarea>
                        </div>
                    </div> --}}

                    <div id="field_file" class="form-group row">
                        <div class="col">
                            {{-- <label>File</label> --}}
                            {{-- <input class="form-control" type="file" id="case_file" name="case_file"> --}}
                            <div class="fallback">
                                <input type="file" name="file" multiple />
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="dz-message needsclick">
                            <i class="ki-duotone ki-file-up fs-3x text-primary"><span
                                    class="path1"></span><span class="path2"></span></i>

                            <div class="ms-4">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to
                                    upload.</h3>
                                <span class="fs-7 fw-semibold text-gray-400">Upload up to 5 files</span>
                            </div>
                            
                        </div>
                    </div>

                    

                    
                </div>
            </div>
        </form>
            </div>
            <div class="modal-footer">

                <button class="btn btn-success float-right" onclick="newUploadModal()" type="button">
                    <span id="span_upload">Submit Upload</span>
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

