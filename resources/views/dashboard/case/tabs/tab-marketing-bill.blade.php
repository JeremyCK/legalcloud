@php
    $LoanAttachmentMarketing = $LoanAttachmentMarketing ?? collect();
    $case = $case ?? null;
    $current_user = $current_user ?? null;
@endphp

@if(isset($case))
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
              @if(isset($case) && $case->status <> 99)
                <button class="btn btn-primary" type="button" onclick="marketingBillMode('0', '{{ $case->id }}');">
                  <i class="cil-plus"></i> Upload Attachment
                </button>
              @endif
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- /.box-header -->
      <div id="div_case_marketing_attachment" class="box-body no-padding " style="width:100%;overflow-x:auto">

        
        @include('dashboard.case.table.tbl-case-marketing-attachment')

      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>
@else
<div class="alert alert-warning">
  <p>Unable to load marketing bill content. Please refresh the page.</p>
</div>
@endif