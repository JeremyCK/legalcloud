<div id="dDispatch" class="card d_operation" style="display:none">

  <div class="card-header">
    <h4>Dispatch</h4>
  </div>
  <div class="card-body">
    <form id="form_dispatch">
      @csrf
      <div class="row">
        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

          <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
          <input class="form-control" type="hidden" id="case_id_dispatch" name="case_id_dispatch" value="">

          <div class="form-group row">
            <div class="col">
              <label>Package Name</label>
              <input class="form-control" type="text" id="package_name" name="package_name" >
            </div>
          </div>

          <div class="form-group row">
            <div class="col">
              <label>Courier</label>
              <select class="form-control" id="courier_id" name="courier_id">
                @foreach($couriers as $courier)
                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                @endforeach
              </select>
            </div>

          </div>

          <div class="form-group row">
            <div class="col">
              <label>Departure Address</label>
              <textarea class="form-control" id="departure_address" name="departure_address" rows="5"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <div class="col">
              <label>Destination Address</label>
              <textarea class="form-control" id="destination_address" name="destination_address" rows="5"></textarea>
            </div>
          </div>

          <div class="form-group row">
            <div class="col">
              <label>Departure Time</label>
              <input class="form-control" type="datetime-local" id="departure_time" name="departure_time">
            </div>
          </div>

          <div class="form-group row">
            <div class="col">
              <label>Delivered Time</label>
              <input class="form-control" type="datetime-local" id="delivered_time" name="delivered_time">
            </div>
          </div>


          <div class="form-group row">
            <div class="col">
              <label>Status</label>
              <select class="form-control" id="cl_delivery_status" name="cl_delivery_status">
                <option value="1" selected>Delivered</option>
                <option value="0">Preparing</option>
                <option value="2">Sending</option>
              </select>
            </div>
          </div>


          <button class="btn btn-success float-right" onclick="createDispatch('{{ $case->id }}')" type="button">
            <span id="span_update_dispatch">Update</span>
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