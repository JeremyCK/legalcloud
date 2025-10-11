<div class="row">
  <div class="col-12">
    <div class="box">
      <div class="box-header">



      </div>


      <div class="row">
        <div class="col-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>

              <div class="box-tools">
                <button class="btn btn-primary" type="button" onclick="fileTemplateListMode('0', '{{ $cases[0]->id }}');">
                  <i class="cil-plus"></i> Generate file
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- /.box-header -->
      <div class="box-body no-padding " style="width:100%;overflow-x:auto">

        <table class="table table-bordered yajra-datatable" id="tbl-file-yadra" style="width:100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>

