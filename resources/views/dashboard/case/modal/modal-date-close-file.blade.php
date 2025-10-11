<div id="modalDateCloseFile" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Update Close File Info - [{{ $case->case_ref_no}}]</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <form id="formCloseFileDate">

                    
            
                    <div class="form-group row ">
                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_date">Transfer Date  </label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_d_transfer_date" id="cf_d_transfer_date" value="@if($closeFileEntry){{ Carbon\Carbon::parse($closeFileEntry->date)->format('Y-m-d')}}@endif" type="date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_d_trx_id" id="cf_d_trx_id" value="@if($closeFileEntry){{$closeFileEntry->transaction_id}} @endif" type="text">
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_d_transfer_from">Transfer From</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="cf_d_transfer_from" id="cf_d_transfer_from">
                                        <option value="0">-- Select bank account --</option>
                                        @foreach ($OfficeBankAccount as $bankAccount)
                                            <option value="{{ $bankAccount->id }}" data-account-type="{{ $bankAccount->account_type }}" 
                                                @if($closeFileEntry) @if($closeFileEntry->bank_id == $bankAccount->id) selected @endif @endif>
                                                {{ $bankAccount->name }}
                                                ({{ $bankAccount->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_d_transfer_to">Transfer To</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="cf_d_transfer_to" id="cf_d_transfer_to">
                                        <option value="0">-- Select bank account --</option>
                                        @foreach ($OfficeBankAccount as $bankAccount)
                                            <option value="{{ $bankAccount->id }}"  data-account-type="{{ $bankAccount->account_type }}"
                                                @if($closeFileEntry_in) @if($closeFileEntry_in->bank_id == $bankAccount->id) selected @endif @endif>
                                                {{ $bankAccount->name }}
                                                ({{ $bankAccount->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_d_remark_update">Remarks</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" id="cf_d_remark_update" name="cf_d_remark_update" row="3">@if($closeFileEntry){{$closeFileEntry->remark}}@endif</textarea>
                                </div>
                            </div>
                        </div>
                      
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="updateCloseFileDate()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function updateCloseFileTotalAmt()
    {
        $sumCloseFileTotal = 0;

        $.each($("input[name='close_file_bill']:checked"), function() {
            itemID = $(this).val();

            $sumCloseFileTotal += parseFloat($("#sum_close_file_" + itemID).val());
            console.log($("#sum_close_file_" + itemID).val());
        });

        if($sumCloseFileTotal.toFixed(2) == 0)
        {
            sumCloseFileTotal = sumCloseFileTotal.replace("-", "");
            $("#cf_transfer_amount").val($sumCloseFileTotal.toFixed(2));
        }
        else{
            $("#cf_transfer_amount").val($sumCloseFileTotal.toFixed(2));
        }

        
    }
</script>
