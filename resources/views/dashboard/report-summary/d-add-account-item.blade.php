<div id="dAddAccountItem" class="card d_operation" style="display:none">

  <div class="card-header">
    <h4>Add account</h4>
  </div>
  <div class="card-body">
    <form id="form_account">
      @csrf
      <div class="row">
        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
          <input class="form-control" type="hidden" id="case_id_dispatch" name="case_id_dispatch" value="">

          <div class="form-group row">
            <div class="col">
              <label>Account</label>
              <select class="form-control" id="selected_account_id" name="selected_account_id">
              <option class="" value="0">-- please select account --</option>
               @foreach($accounts as $account)
                <option class="account_item_all account_cat_{{$account->account_cat_id}} account_{{$account->id}}" value="{{$account->id}}">{{ $account->name }}</option>
                @endforeach
              </select>
            </div>

          </div>


          <button class="btn btn-success float-right" onclick="addNewAccount('')" type="button">
            <span id="span_update_dispatch">Update</span>
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