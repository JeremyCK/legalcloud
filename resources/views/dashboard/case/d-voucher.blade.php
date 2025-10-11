
<div id="dVoucher" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4>Request voucher</h4>
  </div>
  <div class="card-body">
    <form id="form_voucher" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
          <div class="form-group row">
            <div class="col">
              <label>Item</label>
              <input class="form-control" type="hidden" value="" id="account_details_id" name="account_details_id">
              <input class="form-control" type="hidden" value="" id="payment_type" name="payment_type">
              <input class="form-control" type="hidden" value="" id="cheque_no" name="cheque_no">
              <input class="form-control" type="hidden" value="" id="payee" name="payee">
              <input class="form-control" type="text" value="" id="item" name="item" disabled>
            </div>
          </div>

          <div class="form-group row">
            <div class="col">
              <label>Available Amount</label>
              <input class="form-control" type="text" value="" id="available_amt" name="available_amt" disabled>
            </div>
          </div>

          <div class="form-group row">
            <div class="col">
              <label>Amount</label>
              <input class="form-control" type="number" value="0" id="amt" name="amt">
            </div>
          </div>


          <button class="btn btn-success float-right" onclick="generateVoucher('{{ $case->id }}')" type="button">
            <span id="span_update">Generate voucher</span>
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </button>
          <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
        </div>
      </div>
    </form>

  </div>
</div>