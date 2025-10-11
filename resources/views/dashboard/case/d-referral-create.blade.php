{{-- <div id="div-referral-create" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4> Referral</h4>
  </div>
  <div class="card-body">

  

    <form method="POST" id="form_referral">
      <div class="row" style="margin-top:40px;">
        <div class="col-12 ">
          <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral Information</h4>
        </div>

        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

          <div class="form-group row">
            <div class="col">
              <label>Referral Name</label>
              <input class="form-control" type="text" name="name" id="referral_name_new" >
            </div>
          </div>

          <div class="form-group row ">
            <div class="col">
              <label>Referral email</label>
              <input class="form-control" type="text" name="email" id="referral_email_new" >
            </div>
          </div>

          <div class="form-group row ">
            <div class="col">
              <label>Bank</label>
              <select id="ddl_bank" class="form-control" name="bank_id">
                  <option value="0">-- Select bank  --</option>
                  @foreach($banks as $index => $bank)
                  <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                  @endforeach
                </select>
            </div>
          </div>

          <div class="form-group row ">
            <div class="col">
              <label>IC NO</label>
              <input class="form-control" name="ic_no" id="referral_ic_no_new" type="text"  />
            </div>
          </div>

          
        </div>

        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row ">
            <div class="col">
              <label>Referral phone no</label>
              <input class="form-control" name="phone_no" id="referral_phone_no_new" type="text"  />
            </div>
          </div>

          <div class="form-group row ">
            <div class="col">
              <label>Company</label>
              <input class="form-control" name="company" id="referral_company_new" type="text"  />
            </div>
          </div>

          <div class="form-group row ">
            <div class="col">
              <label>Bank Account</label>
              <input class="form-control" name="bank_account" id="referral_bank_account_new" type="text"  />
            </div>
          </div>
        </div>
      </div>

      
      <a href="javascript:void(0);" onclick="referralMode()" class="btn btn-danger">Cancel</a>
      
      <button id="btnSubmit" class="btn btn-success float-right" type="button" onclick="createReferral()">Create Referral</button>
    </form>


  </div>
</div> --}}