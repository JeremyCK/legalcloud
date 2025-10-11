<div id="modalMoveDisb" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Move Disbursement</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">

                <form id="formMoveDisb">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group row">
                                <div class="col">
                                    <label>Bill</label>
                                    <select id="ddl_move_bill" class="form-control" name="ddl_move_bill" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                        </div>
                        <div class="col-6">
                            <input class="form-check-input " onchange="checkAllController()" type="checkbox"
                                value="0" name="checkall" id="checkall" >
                            <label class="form-check-label" for="checkall">Check All</label>
                            
                        </div>

                    </div>



                  
                    <hr />

                    <div class="form-group row ">
                        <div class="col-12 table-responsive" style="height:500px">
                            <table id="tbl-disb-move" class="table table-striped">
                                <thead style="background-color: black;color:white; z-index:100">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Voucher No</th>
                                        <th class="text-center">Trx Id</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center">Desc</th>
                                        <th class="text-center">Amount(RM)</th>
                                        <th class="text-center">Client Bank</th>
                                        <th class="text-center">Payment Date</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Requested By</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center remove-this">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl-bill-disburse">
                                </tbody>

                                <tfoot style="background-color: black;color:white; z-index:100">
                                    <th colspan="5" class="text-left">Total </th>
                                    <th class="text-right"><span id="span_total_disb" class="text-right">0</span>
                                    </th>
                                    <th colspan="6" class="text-left"> </th>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>


                <button type="button" id="btnAbortFile" class="btn btn-danger float-right"
                    onclick="MoveBill()">Move
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>
