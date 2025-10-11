<div id="dTrustEdit" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4>Trust account Edit</h4>
  </div>
  <div class="card-body">
    <form id="form_trust_edit" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
          <input class="form-control" type="hidden" id="trust_edit_id" name="trust_edit_id" value="">
          <!-- <div class="form-group row">
            <div class="col">
              <label>Item</label>
              <input class="form-control" type="hidden" value="" id="account_details_id" name="account_details_id">
              <input class="form-control" type="hidden" value="" id="payment_type" name="payment_type">
              <input class="form-control" type="hidden" value="" id="cheque_no" name="cheque_no">
              <input class="form-control" type="text" value="" id="item" name="item" disabled>
            </div>
          </div> -->

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="payment_name_edit">Payee name</label>
            <div class="col-md-9">
              <input class="form-control" type="text" id="payment_name_edit" name="payment_name">
            </div>
          </div>

          <div class="form-group row">

            <label class="col-md-3 col-form-label" for="payment_name_edit">Payment Description</label>
            <div class="col-md-9">
              <textarea class="form-control" id="payment_desc_edit" name="payment_desc_edit" rows="3"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="hf-email">Movement</label>
            <div class="col-md-9">
              <select id="payment_movement_edit" class="form-control" name="payment_movement_edit" required>
                <option value="">-- Please select the payment type -- </option>
                <option value="1">Received</option>
                <option value="2">Disbursement</option>
                <option value="3">Transfer to bill</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="payment_name">Transaction ID</label>
            <div class="col-md-9">
              <input class="form-control" type="text" id="transaction_id_edit" name="transaction_id_edit">
            </div>
          </div>


          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="payment_name_edit">Amount</label>
            <div class="col-md-9">
              <input class="form-control" type="number" id="payment_amt_edit" name="payment_amt_edit" disabled>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="hf-email">Payment type</label>
            <div class="col-md-9">
              <select id="ddl_payment_type_trust_edit" class="form-control" name="ddl_payment_type_trust_edit" required>
                <option value="">-- Please select the payment type -- </option>
                @foreach($parameters as $index => $parameter)
                <option value="{{ $parameter->parameter_value_3 }}">{{ $parameter->parameter_value_2 }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-3 col-form-label" for="hf-email">Payment Date</label>
            <div class="col-md-9">
              <input class="form-control" type="date" id="voucher_payment_time_trust_edit" name="voucher_payment_time_trust_edit">
            </div>
          </div>

          <div class="form-group row ">
            <label class="col-md-3 col-form-label" for="txt_bank_name">Office bank account</label>
            <div class="col-md-9">
              <select class="form-control" name="OfficeBankAccount_id_trust_edit" id="OfficeBankAccount_id_trust_edit" required>
                <option value="">-- Please select a bank -- </option>
                @foreach($OfficeBankAccount as $index => $bank)
                <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_no }})</option>
                @endforeach
              </select>
            </div>
          </div>



          <!-- <div class="form-group row   " >
            <label class="col-md-3 col-form-label" for="txt_bank_name_trust_edit">Bank Name</label>
            <div class="col-md-9">
              <select id="txt_bank_name_trust_edit" class="form-control" name="bank" required>
                <option value="">-- Please select a bank -- </option>
                @foreach($banks as $index => $bank)
                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                @endforeach
              </select>
            </div>
          </div> -->

          <div class="form-group row  ">
            <label class="col-md-3 col-form-label" for="txt_cheque_no_trust">Cheque No</label>
            <div class="col-md-9">
              <input class="form-control" id="txt_cheque_no_trust_edit" name="txt_cheque_no_trust_edit" type="text" value="" />
            </div>
          </div>

          <!-- <div class="form-group row  " >
            <label class="col-md-3 col-form-label" for="txt_bank_account_trust">Bank Account</label>
            <div class="col-md-9">
              <input class="form-control" id="txt_bank_account_trust_edit" name="txt_bank_account_trust_edit" type="text" value="" />
            </div>
          </div> -->

          <div class="form-group row  " >
            <label class="col-md-3 col-form-label" for="txt_cheque_no">Voucher No</label>
            <div class="col-md-9">
              <input class="form-control" id="txt_voucher_no_trust_edit" name="txt_voucher_no_trust_edit" type="text" value="" />
            </div>
          </div>


          <button class="btn btn-success float-right" onclick="update_trust_value()" type="button">
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