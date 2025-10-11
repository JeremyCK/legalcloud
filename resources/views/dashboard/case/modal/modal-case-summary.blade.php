<div id="caseSummaryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 ">Case Summary</h4>
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
                                <label>Purchase Price</label>
                                <input class="form-control" value="{{ $case->purchase_price }}"
                                    type="number" name="purchase_price" required>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Loan Sum</label>
                                <input class="form-control" value="{{ $case->loan_sum }}" type="number"
                                    name="loan_sum" required>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Case Type</label>

                                <select class="form-control" name="portfolio" required>
                                    @if (count($Portfolio))
                                        @foreach ($Portfolio as $index => $item)
                                            <option @if ($item->id == $case->bank_id) selected @endif
                                                value="{{ $item->id }}">
                                                {{ $item->name }} </option>
                                        @endforeach
                                    @endif
                                    AccountItem
                                </select>
                            </div>
                        </div>

                    </div>

                    
                    <div class="col-12 ">
                        <div class="form-group row dPersonal">
                            <div class="col">
                                <label>Bank Reference</label>
                                <input class="form-control" name="bank_ref" type="text" value="{{ $case->bank_ref }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 ">
                        <div class="form-group row dPersonal">
                            <div class="col">
                                <label>Bank LI Date</label>
                                <input class="form-control" name="bank_li_date" type="date" value="{{ $case->bank_li_date }}" />
                            </div>
                        </div>
                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Property Address</label>
                                <textarea class="form-control" name="property_address" rows="2" required>{{ $case->property_address }}</textarea>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right"
                    onclick="updateCaseSummary()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>