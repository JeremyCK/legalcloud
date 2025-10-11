<div id="dBillEntry" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4>Receive Payment</h4>
  </div>
  <div class="card-body">
    <form id="form_bill_receive1" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <input class="form-control" type="hidden" id="case_id_trust" name="case_id_trust" value="">

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="payment_name">Payee name <span class="text-danger">*</span></label>
            <div class="col-md-9">
              <input class="form-control" type="text" id="payment_name" name="payment_name" required>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="payment_name">Amount <span class="text-danger">*</span></label>
            <div class="col-md-9">
              <input class="form-control" type="number" id="payment_amt" name="payment_amt">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="hf-email">Payment type</label>
            <div class="col-md-9">
              <select class="form-control ddl_payment_type" name="ddl_payment_type_trust" required>
                <option value="">-- Please select the payment type -- </option>
                @foreach($parameters as $index => $parameter)
                <option value="{{ $parameter->parameter_value_3 }}">{{ $parameter->parameter_value_2 }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" >Transaction ID <span class="text-danger">*</span></label>
            <div class="col-md-9">
              <input class="form-control" type="text" name="transaction_id">
            </div>
          </div> 

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="hf-email">Payment Date <span class="text-danger">*</span></label>
            <div class="col-md-9">
              <input class="form-control" type="date" name="payment_date">
            </div>
          </div>

          <div class="form-group row ">
            <label class="col-md-3 col-form-label" for="txt_bank_name">Office bank account <span class="text-danger">*</span></label>
            <div class="col-md-9">
              <select class="form-control" name="OfficeBankAccount_id" id="OfficeBankAccount_id" required>
                <option value="">-- Please select a bank -- </option>
                @foreach($OfficeBankAccount as $index => $bank)
                <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_no }})</option>
                @endforeach
              </select>
            </div>
          </div>



          <div class="form-group row dBankTransfer dChequeNo dCreditCard" style="display: none;">
            <label class="col-md-3 col-form-label" for="txt_bank_name_trust">Bank Name</label>
            <div class="col-md-9">
              <select id="txt_bank_name_trust" class="form-control" name="bank_id" required>
                <option value="">-- Please select a bank -- </option>
                @foreach($banks as $index => $bank)
                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row dPaymentType dChequeNo" style="display: none;">
            <label class="col-md-3 col-form-label" for="txt_cheque_no_trust">Cheque No</label>
            <div class="col-md-9">
              <input class="form-control" id="txt_cheque_no_trust" name="txt_cheque_no_trust" type="text" value="" />
            </div>
          </div>

          <div class="form-group row dPaymentType dBankTransfer" style="display: none;">
            <label class="col-md-3 col-form-label" for="txt_bank_account_trust">Bank Account</label>
            <div class="col-md-9">
              <input class="form-control" id="txt_bank_account_trust" name="txt_bank_account_trust" type="text" value="" />
            </div>
          </div>

          <div class="form-group row dPaymentType dCreditCard" style="display: none;">
            <label class="col-md-3 col-form-label" for="txt_cheque_no">Credit Card No</label>
            <div class="col-md-9">
              <input class="form-control" id="txt_card_no_trust" name="txt_card_no_trust" type="text" value="" />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="txt_cheque_no">Payment Description</label>
            <div class="col-md-9">
              <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
            </div>
          </div>

          <button class="btn btn-success float-right" onclick="submitBillEntry()" type="button">
            <span id="span_update_trust">Submit</span>
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </button>
          <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a>
        </div>
      </div>
    </form>

  </div>
</div>