<div id="div-checklist-seq" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4> Checklist sequence</h4>
  </div>
  <div class="card-body">

    <div class="form-group row ">
      <div class="col-12">
        <a href="javascript:void(0);" onclick="listMode()" class="btn btn-danger">Cancel</a>
      </div>
    </div>


    <div class="form-group row ">

    
    <!-- <div></div> -->


      <!-- <div class="col-6">
        <label>Search</label>
        <input type="text" id="search_referral" name="search_referral" placeholder="Search checklist item" class="form-control" />
      </div> -->


      <div class="box-body no-padding " style="width:100%;overflow-x:auto">

        <table class="table table-striped table-bordered datatable">
          <thead>
            <tr class="text-center">
              <!-- <th>No</th> -->
              <th>Name</th>
              <th>Sequence</th>
            </tr>
          </thead>
          <tbody id="tbl-checklist-seq">
            @if(count($templates))

            @foreach($templates as $index => $template)
            <tr id="item_row_{{ $template->id }}" class="checklist-item-row checklist-item-row_{{ $template->id }} " >
              <!-- <td>{{ $index+1 }}</td> -->
              <td>{{ $template->name }}</td>
              <td class="hide">{{ $template->id }}</td>
              <td class="text-center">
                <!-- <a href="javascript:void(0)" onclick="selectThisChecklist('{{ $template->id }}', '{{ $template->name }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Select</a> -->
                <input class="form-control" type="text"  id="{{ $template->id }}" name="seq" value="{{ $template->order }}">
              </td>
            </tr>

            @endforeach
            @else
            <tr>
              <td class="text-center" colspan="5">No data</td>
            </tr>
            @endif

          </tbody>
        </table>

        <!-- <table class="table table-striped table-bordered  yajra-datatable hide" id="tbl-checklist-yadra" style="width:100%">
          <thead>
            <tr>
              <th>Steps</th>
              <th>items</th>
              <th>action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table> -->

      </div>

    </div>

    <button class="btn btn-success float-right" onclick="orderSequence()" type="button">
            <span id="span_update">Update</span>
            <div class="overlay" style="display:none">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </button>



  </div>
</div>