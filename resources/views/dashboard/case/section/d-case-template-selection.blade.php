<form id="form_accept_case">

  <div class="form-group row">
    <label class="col-md-12 col-form-label">define the template</label>

    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

      <div class="form-group row">
        <div class="col">
          <label>Template</label>

          <select class="form-control" id="template_cat" name="template_cat" required>
            <option value="0">-- Please select category --</option>
            @foreach($caseTemplateCategories as $index => $category)
            <option value="{{$category->id }}">{{$category->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group row">
        <div class="col">
          <label>Template</label>

          <select class="form-control" id="template" name="template" disabled required>
            <option value="0">-- Please select template --</option>
            @foreach($caseTemplate as $index => $template)
            <option value="{{$template->id }}" class="all_cat temp_cat_{{$template->checklist_category_id }}">{{$template->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    @if($case->status == 2)
    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
      <div class="form-group row">
        <div class="col">
          <label>Reason (Only for KIV)</label>
          <textarea class="form-control" id="reason" name="reason" rows="3">{{$case->kiv_remark }}</textarea>
        </div>
      </div>
    </div>
    @endif
  </div>

  <div class="wizard-footer">
    @if($case->status == 2)
    <div class="pull-right">
      <input type='button' class='btn btn-finish btn-fill btn-info btn-wd' onclick="setKIV('{{$case->id}}')" value='KIV' />
    </div>
    @endif
    @if($case->status <> 99)
    <div class="pull-left">
      @if($case->status == 0)
      <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd' onclick="acceptCase('{{$case->id}}')" name='finish' value='Accept case' />
      @else
      <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd' onclick="acceptCase('{{$case->id}}')" name='finish' value='Select this template' />
      @endif
    </div>
    @endif
    <div class="clearfix"></div>
  </div>
</form>