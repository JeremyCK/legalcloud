<div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
  <div class="form-group row">
    <label class="col-md-4 col-form-label" for="bank_name"><b>Bank Name:</b></label>
    <div class="col-md-8">
      <label class=" col-form-label" id="bank_name">@if(isset($OfficeBank)) {{$OfficeBank->name}} @else  -  @endif</label>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-md-4 col-form-label" for="account_no"><b>Account No:</b></label>
    <div class="col-md-8">
      <label class=" col-form-label" id="account_no">@if(isset($OfficeBank)) {{$OfficeBank->account_no}} @else  -  @endif</label>
    </div>
  </div>
</div>

<div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
  <div class="form-group row">
    <label class="col-md-4 col-form-label" for="bank_code"><b>Bank Code:</b></label>
    <div class="col-md-8">
      <label class="  col-form-label" id="bank_code">@if(isset($OfficeBank)) {{$OfficeBank->short_code}} @else  -  @endif</label>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-md-4 col-form-label" for="account_type"><b>Account Type:</b></label>
    <div class="col-md-8">
      <label class="  col-form-label" id="account_type">-</label>
    </div>
  </div>
</div>