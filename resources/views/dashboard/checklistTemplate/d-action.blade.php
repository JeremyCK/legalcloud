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
              <label>Step</label>
              <select class="form-control" id="steps" name="steps">
                @foreach($steps as $step)
                <option value="{{ $step->id }}">{{ $step->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>


        





        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">



          <button class="btn btn-success float-right" onclick="submitAction('{{ $templates_main->id }}')" type="button">
            <span id="span_update">Submit</span>
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </button>
          <a href="javascript:void(0);" onclick="listMode()" class="btn btn-danger">Cancel</a>
        </div>
      </div>
    </form>

  </div>
</div>