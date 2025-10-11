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

              @if($case->status <> 99)
              <div class="box-tools">
                <button class="btn btn-primary" type="button" onclick="fileMode('1', '{{ $case->id }}');">
                  <i class="cil-plus"></i> Upload Attachment
                </button>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- /.box-header -->
      <div id="div_case_attachment" class="box-body no-padding " style="width:100%;overflow-x:auto">
        @include('dashboard.case.table.tbl-case-attachment')
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>