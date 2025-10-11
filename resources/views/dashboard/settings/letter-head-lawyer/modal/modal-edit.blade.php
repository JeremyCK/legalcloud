<div id="modalEdit" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 50% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4>Edit Lawyer</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <form id="form_edit">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                            <div class="form-group row">
                                <div class="col">
                                    <label>Lawyer name</label>
                                    <input class="form-control" type="hidden" value="" id="id" name="id" required>
                                    <input class="form-control" type="text" value="" id="name" name="name" required>
                                </div>
                            </div>

                            
                            <div class="form-group row">
                                <div class="col">
                                    <label>IC</label>
                                    <input class="form-control" type="text" value="" id="ic_no" name="ic_no" required>
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
                    onclick="updateLetterHead()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

            </div>
        </div>

    </div>
</div>
