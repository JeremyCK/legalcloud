<div id="dFile" class="card d_operation" style="display:none">

    <div class="card-header">
        <h4>Upload file</h4>
    </div>
    <div class="card-body">
        <form id="form_file" enctype="multipart/form-data" class="dropzone">
            @csrf
            <div class="row">
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                    <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
                    {{-- <input class="form-control" type="hidden" id="case_id" name="case_id" value=""> --}}
                    <input class="form-control" type="hidden" id="file_type" name="file_type" value="1">

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
        
        {{-- <button class="btn btn-success float-right" onclick="uploadFile()" type="button"> --}}
        <button class="btn btn-success float-right" onclick="newUpload()" type="button">
            <span id="span_upload">Submit Upload</span>
            <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </button>
        <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a>

    </div>
</div>
