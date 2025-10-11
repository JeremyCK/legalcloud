<div id="modalMoveFile" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 id="lbl-move-file"> </h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div id="div-add-file-list" class="card d_operation" >
                    
                    <div class="card-body">
                        <table  id="tbl-movefile" class="table table-striped table-bordered datatable" style="width: 100%">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Current Folder</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-file-to-move">
                                

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" id="btnCloseFile"
                    class="btn btn-close-abort btn-close-file btn-success float-right"
                    onclick="moveFileFolder()">Move into folder
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

            </div>
        </div>

    </div>
</div>
