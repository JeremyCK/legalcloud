<div class="row">

  <div class="col-sm-4">
    <div class="card" style="max-height:700px;overflow-y:auto">

      <!-- <a class="btn btn-primary nav-link" data-toggle="tab" href="#home" role="tab" href="javascript:void(0)">Return</a> -->

      <div class="box box-solid">
        <div class="box-body no-padding mailbox-nav">
          <!-- Panel -->
          <div class="panel">
            <div class="panel-body">
              <div class="list-group faq-list" role="tablist" style="overflow-x:overlay">


                @foreach($caseMasterListCategory as $index => $category)
                <a class="list-group-item {{ $index == 0 ? 'active' : '' }}" data-toggle="tab" href="#tab_{{$category->code}}" aria-controls="category-1" role="tab" aria-expanded="false">{{$category->name}}</a>

                @endforeach

              </div>
            </div>
          </div>
          <!-- End Panel -->
        </div>
        <!-- /.box-body -->
      </div>

    </div>

  </div>


  <div class="col-sm-8">

    <div class="tab-content">

      @foreach($caseMasterListCategory as $index => $category)


      <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="tab_{{$category->code}}" role="tabpanel">

        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h4>{{$category->name}}</h4>
              </div>
              <div>
                <a class="btn btn-sm btn-info float-right mr-1 d-print-none" onclick="submitMasterList('{{$category->id}}', '{{ $case->id }}')" href="javascript:void(0)">

                  <div class="overlay_{{$category->id}}" style="display:none">
                    <i class="fa fa-refresh fa-spin"></i>
                  </div>
    
                  <span id="span_update_{{$category->id}}">Save</span>
    
                </a>
              </div>
            </div>
            
          </div>
          <div class="card-body">
            <form id="form_master_{{$category->id}}">

              @csrf

              @foreach($caseMasterListField as $index => $field)

              @if ($field->case_field_id == $category->id)

              <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email"><a href="javascript:void(0)" onclick="copyContent('{{$field->code}}')" class="btn btn-xs btn-primary"><i class="cil-copy"></i></a> {{$field->name}}</label>
                <div class="col-md-9">
                @if ($field->type == 'area')
                <textarea class="form-control" id="{{$field->id}}" name="{{$field->id}}" rows="3">{{$field->value}}</textarea>
                @else
                <input class="form-control" id="{{$field->id}}" type="{{$field->type}}" name="{{$field->id}}" value="{{$field->value}}">
                @endif
                  
                </div>
              </div>

              @endif


              @endforeach


            </form>
          </div>
          {{-- <div class="card-footer">
            <div class="form-group row">
              <div class="col-12">
                <a class="btn btn-sm btn-info float-right mr-1 d-print-none" onclick="submitMasterList('{{$category->id}}', '{{ $case->id }}')" href="javascript:void(0)">

                  <div class="overlay_{{$category->id}}" style="display:none">
                    <i class="fa fa-refresh fa-spin"></i>
                  </div>
    
                  <span id="span_update_{{$category->id}}">Save</span>
    
                </a>
              </div>
            </div>
          
          </div> --}}
        </div>

      </div>
      @endforeach

    </div>
  </div>
</div>
<script>
  function copyContent(content)
  {
    navigator.clipboard.writeText("${" +content + "}");
    Swal.fire(
            'Copied!','Copied the short code',
            'success'
          )
  }
</script>