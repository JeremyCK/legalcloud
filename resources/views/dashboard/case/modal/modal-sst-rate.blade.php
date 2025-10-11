<div id="modalSSTRate" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 ">Update SST Rate</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <form id="formInvoiceDate">
                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>SST Rate</label>
                                <select class="form-control" id="ddl_sst_modal" onchange="updateSSTRate()" name="ddl_sst_modal">
                                    <option value="6" > 6%</option>
                                    <option value="8" > 8%</option>
                                </select>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="saveSSTRate()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>