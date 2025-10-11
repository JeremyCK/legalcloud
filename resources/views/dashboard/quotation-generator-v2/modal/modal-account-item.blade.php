<div id="accountItemModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_add">
                    <div class="form-group row ">
                        <div class="col">
                            <label>Account Item</label>
                            {{-- <select id="ddlAccountItem" class="form-control select2-single" name="accountItem" style="width: 100%"> --}}
                            <select id="ddlAccountItem" class="form-control select2-single" name="accountItem">
                            </select>
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>New amount</label>
                            <input type="number" value="0" id="txtAmount" name="txtAmount"
                                onchange="quotationCalculationEventAccountItem()" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Auto calculate amount</label>
                            <input type="number" value="0" id="txtCalculateAccountAmount"
                                name="txtCalculateAccountAmount" class="form-control" disabled />
                        </div>
                    </div>

            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnClose2" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="addAccountItem()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>