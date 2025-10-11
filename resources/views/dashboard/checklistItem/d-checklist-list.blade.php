<div id="div-checklist" class="card d_operation" style="display:none">
  <div class="card-header">
    <h4> Checklist</h4>
  </div>
  <div class="card-body">

    <div class="form-group row ">
      <div class="col-12">
        <a href="javascript:void(0);" onclick="backAddMode()" class="btn btn-danger">Cancel</a>
      </div>
    </div>


    <div class="form-group row ">


      <div class="col-6">
        <label>Search</label>
        <input type="text" id="search_referral" name="search_referral" placeholder="Search checklist item" class="form-control" />
      </div>

      <div class="col-6 ">
        <div class="form-group ">
          <label>Steps</label>

          <select id="ddl-steps" class="form-control" name="ddl-steps" required>
            @foreach($CaseTemplateSteps as $index => $step)
            <option value="{{ $step->id }}">{{ $step->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="box-body no-padding " style="width:100%;overflow-x:auto">

        <table class="table table-striped table-bordered datatable">
          <thead>
            <tr class="text-center">
              <th>No</th>
              <th>Name</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="tbl-case-item">
            @if(count($CaseTemplateItems))

            @foreach($CaseTemplateItems as $index => $item)
            <tr id="item_row_{{ $item->id }}" class="checklist-item-row checklist-item_{{ $item->step_id }} " @if($item->step_id != '1') style="display:none" @endif>
              <td>{{ $index+1 }}</td>
              <td>{{ $item->name }}</td>
              <td class="hide">{{ $item->id }}</td>
              <td class="text-center">
                <a href="javascript:void(0)" onclick="selectThisChecklist('{{ $item->id }}', '{{ $item->name }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Select</a>

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





  </div>
</div>