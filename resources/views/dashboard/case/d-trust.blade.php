<div id="dTrust" class="card d_operation" style="display:none">
    <div class="card-header">
        <h4 id="header-trust-entry">Trust account entry</h4>
    </div>
    <div class="card-body">
        {{-- <form id="form_trust" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12 ">
                    <div class="form-group row dPersonal">
                        <div class="col">
                            <input class="form-control" name="trust_id" id="trust_id" type="hidden" value="0" />
                        </div>
                    </div>

                </div>

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
                        <label class="col-md-4 col-form-label" for="payee_name"><span class="text-danger">*</span> Payee Name/Disburse To</label>
                        <div class="col-md-8">
                            <input class="form-control" name="payee_name" type="text" required />
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
                            <input class="form-control" id="adjudication_no_trust" name="adjudication_no_trust" type="text"
                                value="" />
                        </div>
                    </div>

                    @if (in_array($current_user->menuroles, ['admin','account','management','maker']) || $current_user->id == 51)
                    <div class="form-group row dPersonal">
                            <label class="col-md-4 col-form-label" for="transaction_id"><span class="text-danger">*</span>Transaction ID</label>
                            <div class="col-md-8">
                                <input class="form-control" name="transaction_id" type="text" />
                            </div>
                    </div>
                    @endif

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label" for="amount"><span class="text-danger">*</span> Payment Description</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="payment_desc" rows="3" required></textarea>
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

                    @if (in_array($current_user->menuroles, ['admin','account','management','maker']) || $current_user->id == 51)
                        <div class="form-group row">
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
                    @endif

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
                        <label class="col-md-4 col-form-label" for="adjudication_no">Payee Email</label>
                        <div class="col-md-8">
                            <input class="form-control" id="payee_email" name="payee_email" type="text"
                                value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label" for="trust_attachment_file">Attachment</label>
                        <div class="col-md-8">
                            <input class="form-control" id="trust_attachment_file" name="trust_attachment_file" type="file">
                        </div>
                    </div>

                  

                </div>

            


            </div>

            <div class="row">
                <div class="col-12">
                    <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger float-left">Cancel</a>

                    <button id="btnUpdateTrustReceive" class="btn btn-success float-right" type="button" onclick="submitTrust();">
                        <i class="cil-plus"></i> Submit
                    </button>
                </div>
            </div>
        </form> --}}

    </div>
</div>
