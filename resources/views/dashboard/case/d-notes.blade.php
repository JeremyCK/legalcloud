<div id="dNotes" class="card d_operation" style="display:none">

      <div class="card-header">
        <h4>Notes</h4>
      </div>
      <div class="card-body">
        <form id="form_notes">
          @csrf
          <div class="row">
            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">
            <input class="form-control" type="hidden" value="" id="note_type" name="note_type">
            <input class="form-control" type="hidden" value="" id="note_edit_mode" name="note_edit_mode">
            <input class="form-control" type="hidden" value="" id="note_edit_id" name="note_edit_id">


              <div class="form-group row">
                <div class="col">
                  <label>Notes</label>
                  <!-- <textarea class="form-control" id="notes_msg" name="notes_msg" rows="6"></textarea> -->
                  <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>
                </div>
              </div>


              <button class="btn btn-success float-right" onclick="submitNotes()" type="button">
                <span id="span_update">Send <i class="cil-send"></i></span>
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