<div id="modalSummary" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 50% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Case Details </h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>
               
            </div>
            <div class="modal-body">
                <form id="formCaseSummary">
                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Ref No</label>
                                <input class="form-control" value="{{ $case->ref_no }}"
                                    type="text" name="ref_no" required>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Client (Purchaser)</label>
                                <input class="form-control" value="{{ $case->client_name_p }}" type="text"
                                    name="client_name_p" >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Client (Vendor)</label>
                                <input class="form-control" value="{{ $case->client_name_v }}" type="text"
                                    name="client_name_v" >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Case Date</label>
                                <input class="form-control" value="{{ $case->case_date }}" type="date"
                                    name="case_date" >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Completion Date</label>
                                <input class="form-control" value="{{ $case->completion_date }}" type="date"
                                    name="completion_date" >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Sales</label>

                                <select class="form-control" id="sales_id" name="sales_id" required>
                                    <option value="0"> -- Select Sales -- </option>
                                       
                                    @if (count($Sales))
                                        @foreach ($Sales as $index => $sale)
                                            <option class="" @if($sale->id == $case->sales_id) selected @endif value="{{ $sale->id }}"> {{ $sale->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right"
                    onclick="updateCaseDetails()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>