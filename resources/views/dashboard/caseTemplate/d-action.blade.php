<div id="div_action" class="card d_operation" style="display:none">

  <div class="card-header">
    <h4>Check List</h4>
  </div>
  <div class="card-body">
    <form id="form_action">
      @csrf
      <div class="row">
        <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row">
            <div class="col">
              <label>Checklist</label>
              <input class="form-control" type="text" value="" id="checklist_name" name="checklist_name">
            </div>
          </div>
        </div>


        <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row">
            <div class="col">
              <label>KPI</label>
              <input class="form-control" type="number" value="0" id="kpi" name="kpi">
            </div>
          </div>
        </div>


        <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row">
            <div class="col">
              <label>Checkpoint</label>
              <input class="form-control" type="number" value="0" id="check_point" name="check_point">
            </div>
          </div>
        </div>


        <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row">
            <div class="col">
              <label>PIC</label>
              <select class="form-control" id="role_id" name="role_id">
                <option value="1">System</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row">
            <div class="col">
              <label>Duration (days)</label>
              <input class="form-control" type="number" value="1" id="duration" name="duration">
            </div>
          </div>
        </div>


        <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6 ">
          <div class="form-group row">

            <div class="col">
              <label>Status</label>
              <select class="form-control" id="check_list_status" name="check_list_status">
                <option value="1" selected>Enabled</option>
                <option value="0">Disabled</option>
              </select>
            </div>
          </div>

        </div>



        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">







          <div class="form-group row">
            <div class="col">
              <label>Remarks</label>
              <textarea class="form-control" id="remark" name="remark" rows="2"></textarea>
            </div>
          </div>

          <button class="btn btn-success float-right" onclick="submitAction('{{ $templates_main[0]->id }}')" type="button">
            <span id="span_update">Update</span>
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </button>
          <a href="javascript:void(0);" onclick="listMode()" class="btn btn-primary">Cancel</a>
        </div>
      </div>
    </form>

  </div>
</div>