@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Master List</h4>
          </div>
          <div class="card-body">
           
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
            <h4>{{$category->name}}</h4>
          </div>
          <div class="card-body">
            <form id="form_master_{{$category->id}}">

              @csrf

              @foreach($caseMasterListField as $index => $field)

              <!-- @if ($field->case_field_id == $category->id) -->

              <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">{{$field->name}}</label>
                <div class="col-md-9">
                  <input class="form-control" id="{{$field->id}}" type="text" name="{{$field->id}}" value="{{$field->code}}">
                </div>
              </div>

              <!-- @endif -->


              @endforeach


            </form>
          </div>
          <div class="card-footer">
            <!-- <button class="btn btn-sm btn-primary" type="submit"> Submit</button> -->
            <a class="btn btn-sm btn-info float-right mr-1 d-print-none" onclick="submitMasterList()" href="javascript:void(0)">

              <div class="overlay_{{$category->id}}" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
              </div>

              <span id="span_update_{{$category->id}}">Save</span>
              <!-- <svg class="c-icon">
                          <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                        </svg>  -->

            </a>
            <!-- <button class="btn btn-sm btn-danger" type="reset"> Reset</button> -->
          </div>
        </div>

      </div>
      @endforeach

    </div>
  </div>
</div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('javascript')

@endsection