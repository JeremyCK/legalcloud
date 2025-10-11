<div id="div_action" class="card d_operation" style="display:none">

      <div class="card-header">
        <h4>Action</h4>
      </div>
      <div class="card-body">
        <form id="form_action">
          @csrf
          <div class="row">
            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

              <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
              <div class="form-group row">
                <div class="col">
                  <label>Action</label>
                  <input class="form-control" type="text" value="" id="action" name="action" disabled>
                </div>
              </div>

              <!-- <div id="field_file" class="form-group row">
                <div class="col">
                  <label>File</label>
                  <input class="form-control" type="file" id="myfile" name="myfile">
                </div>
              </div> -->

              <div class="form-group row">
                <div class="col">
                  <label>Remarks</label>
                  <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                </div>
              </div>


              <div class="form-group row">

                <div class="col">
                  <label>Status</label>
                  <select class="form-control" id="check_list_status" name="check_list_status">
                    <option value="1" selected>Completed</option>
                    <option value="0">Pending</option>
                  </select>
                </div>
              </div>


              <button class="btn btn-success float-right" onclick="updateChecklist()" type="button">
                <span id="span_update">Update</span>
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