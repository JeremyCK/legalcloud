<div id="modalCreateFolder" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4>Create new folder</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
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
                                    <select class="form-control" id="folder_status"
                                        name="folder_status">
                                        <option value="1" selected>Published</option>
                                        <option value="0">Draft</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" id="btnCloseFile"
                    class="btn btn-close-abort btn-close-file btn-success float-right"
                    onclick="createFolder()">Create
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

            </div>
        </div>

    </div>
</div>
