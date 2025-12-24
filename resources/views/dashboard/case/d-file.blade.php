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
                    {{-- <input class="form-control" type="hidden" id="case_id" name="case_id" value=""> --}}
                    <input class="form-control" type="hidden" id="file_type" name="file_type" value="1">

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

                    <!-- Custom file list table -->
                    <div class="col-12 mt-3" id="file-list-container" style="display:none;">
                        <table class="table table-bordered" id="file-list-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">File Name</th>
                                    <th style="width: 25%;">Attachment Type</th>
                                    <th style="width: 35%;">Remarks</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="file-list-tbody">
                                <!-- Files will be added here -->
                            </tbody>
                        </table>
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
