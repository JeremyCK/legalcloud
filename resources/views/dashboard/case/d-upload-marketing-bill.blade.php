<div id="dUploadMarketingBill" class="card d_operation"  style="display:none">

  <div class="card-header">
    <h4>Upload file</h4>
  </div>
  <div class="card-body">
    <form id="form_marketing_bill" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
          <input class="form-control" type="hidden" id="case_id" name="case_id" value="">
          <div id="field_file" class="form-group row">
            <div class="col">
              <label>File</label>
              <input class="form-control" type="file" id="marketing_bill_file" name="marketing_bill_file">
            </div>
          </div>

          <button class="btn btn-success float-right" onclick="uploadMarketingBill()" type="button">
            <span id="span_upload">Upload</span>
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