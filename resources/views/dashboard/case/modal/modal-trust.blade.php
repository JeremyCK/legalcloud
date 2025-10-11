<div id="modalTrust" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-print" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1 mode-trust-request">Request Trust Disbursement</h4>
                        <h4 class="card-title mb-0 flex-grow-1 mode-trust-receive">Receive Trust Fund</h4>
                        <h4 class="card-title mb-0 flex-grow-1 mode-trust-edit">Edit Record</h4>
                    </div>
                    <div class="col-6">
                        <a href="javascript:void(0);" type="button" data-dismiss="modal"
                            class="btn btn-danger float-right no-print btn_close_all"> <i class=" cli-x"></i> Close</a>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div >

                    <div class="card-body">
                        <form id="form_trust" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <input class="form-control" name="trust_id" id="trust_id" type="hidden" value="0" />
                                <input class="form-control" name="trust_is_recon" id="trust_is_recon" type="hidden" value="0" />
                                <input class="form-control" name="account_approval" id="account_approval" type="hidden" value="0" />
                
                                <div id="div_recon_text_trust" class="col-12"  style="display: none">
                                    <span class="text-danger">* This record already recon</span>
                                </div>
                                
                                <div  class="col-12">
                                    <hr/>
                                </div>
                
                                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="payment_type">Payment type</label>
                                        <div class="col-md-8">
                                            <select id="payment_type" class="form-control" name="payment_type" required>
                                                <option value="">-- Please select the payment type -- </option>
                                                @foreach ($parameters as $index => $parameter)
                                                    <option value="{{ $parameter->parameter_value_3 }}">
                                                        {{ $parameter->parameter_value_2 }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                
                                    <div class="form-group row dPersonal">
                                        <label class="col-md-4 col-form-label" for="payee"><span class="text-danger">*</span> Payee Name/Disburse To</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="payee" type="text" required />
                                        </div>
                                    </div>
                
                                    <div class="form-group row dPersonal">
                                        <label class="col-md-4 col-form-label" for="amount"><span class="text-danger">*</span> Request Amount</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="amount" type="number" value="0" required />
                                        </div>
                                    </div>
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="adjudication_no">Adjudication/Smartbox No</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="adjudication_no_trust" name="adjudication_no" type="text"
                                                value="" />
                                        </div>
                                    </div>
                
                                   
                                    <div class="form-group row dPersonal  @if (!in_array($current_user->menuroles, ['admin','account','management','maker'])) hide @endif">
                                            <label class="col-md-4 col-form-label" for="transaction_id"><span class="text-danger">*</span>Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transaction_id" type="text" />
                                            </div>
                                    </div>
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="remark"><span class="text-danger">*</span> Payment Description</label>
                                        <div class="col-md-8">
                                            <textarea class="form-control" name="remark" rows="3" required></textarea>
                                        </div>
                                    </div>
                
                                </div>
                
                
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="payment_date"><span class="text-danger">*</span> Payment Date </label>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="payment_date">
                                            </div>
                                        </div>
                
                                        <div class="form-group row @if (!in_array($current_user->menuroles, ['admin','account','management','maker'])) hide @endif">
                                            <label class="col-md-4 col-form-label" for="office_account_id"><span class="text-danger">*</span> Office bank account</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="office_account_id" required>
                                                    <option value="">-- Please select a bank -- </option>
                                                    @foreach ($OfficeBankAccount as $index => $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name }}
                                                            ({{ $bank->account_no }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="bank_id">Payee Bank</label>
                                        <div class="col-md-8">
                                            <select class="form-control" name="bank_id">
                                                <option value="">-- Please select a bank -- </option>
                                                @foreach ($banks as $index => $bank)
                                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="bank_account">Payee Bank Account</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="bank_account" type="text" />
                                        </div>
                                    </div>
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="email">Payee Email</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="email" name="email" type="text"
                                                value="" />
                                        </div>
                                    </div>
                
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="attachment_file">Attachment</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="attachment_file" name="attachment_file" type="file">
                                        </div>
                                    </div>
                
                                </div>
                
                            </div>
                
                        </form>
                    </div>
                </div>
                

            </div>
            <div class="modal-footer">

                <button id="btnRequestTrustDisb" class="btn btn-success float-right mode-trust-request" onclick="requestTrustDisbusement()" type="button">
                    <span id="span_upload">Request </span>
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

                <button id="btnReceiveTrustDisb" class="btn btn-success float-right  mode-trust-receive" onclick="receiveTrustDisbusement();" type="button">
                    <span id="span_upload">Receive </span>
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

                <button id="btnUpdateTrustReceive" class="btn btn-success float-right  mode-trust-edit" onclick="updateVoucher();" type="button">
                    <span id="span_upload">Update </span>
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
