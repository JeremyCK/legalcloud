<div class="box box-default">
  
  <div class="box-body wizard-content">


    @if(count($CheckListTemplateMainV2))
    <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">

      <div class="steps clearfix">
        <ul role="tablist">

          <?php $checkpoint_count = 0; ?>
          @foreach($CheckListTemplateMainV2 as $index2 => $detail)

          @if ($detail->id != "0")
          <?php $checkpoint_count += 1;  ?>
          <li id="li_check_point_{{$checkpoint_count }}_v2" role="tab" class="first li_check_pointv2 @if ($checkpoint_count == '0') current @endif   @if ($detail->MainCheckStatus == 1) done @endif" aria-disabled="false" aria-selected="false">
            <a id="steps-uid-0-t-0" onclick="stepControllerV2('{{$checkpoint_count }}');" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
              <span class="step">
                @if($detail->status == 1)
                <i id="checklist_" class="ion ion-checkmark "></i>
                @else
                {{ $checkpoint_count }}
                @endif
              </span>
              <span class="checklist_name">{{ $detail->name }}</span>
            </a>
          </li>
          @endif
          @endforeach

        </ul>
      </div>
      <div class="content clearfix">

        <?php $checkpoint_count = 0; ?>
        @foreach($CheckListTemplateMainV2 as $index2 => $checklist)
        <?php $checkpoint_count += 1;  ?>
        <section id="s_check_point_{{$checkpoint_count}}_v2" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current sCheckpointv2" aria-hidden="true" style="@if ($checkpoint_count > 1) display: none; @endif">
          <div class="timeline-footer text-right">
            <a id="btn_action_2010" href="javascript:void(0)" class="btn btn-info btn-sm action-view-group" onclick="editActionMode('{{$detail->id}}')"><i class="ion-compose"></i> Edit Mode</a>
            <a id="btn_action_2010" href="javascript:void(0)" class="btn btn-danger btn-sm action-edit-group " style="display:none" onclick="cancelEditActionMode('{{$detail->id}}')"><i class="ion-compose"></i> Cancel</a>
            <a id="btn_action_2010" href="javascript:void(0)" class="btn btn-primary btn-sm action-edit-group " style="display:none" onclick="saveAllCheckListEventV2('{{$detail->id}}')"><i class="ion-compose"></i>
              
              <span id="span_update">Save all</span>
              <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
              </div>
            </a>
          </div>

          <ul class="timeline">
            <?php $checkpoint_count1 = 0; ?>
            @foreach($checklist->details as $index3 => $detail)
            <li class="li-item-{{ $checkpoint_count1 }}">
              @if($detail->subCheckStatus == 1)
              <i id="checklist_{{ $detail->id }}" class="ion ion-checkmark bg-done"></i>
              @elseif($detail->subCheckStatus == 0)
              <i id="checklist_{{ $detail->id }}" class="ion ion-person bg-aqua"></i>
              @elseif($detail->subCheckStatus == 2)
              <i id="checklist_{{ $detail->id }}" class="ion ion-clock bg-overdue"></i>
              @elseif($detail->subCheckStatus == 99)
              <i id="checklist_{{ $detail->id }}" class="ion ion-clock bg-info"></i>
              @endif

              <div class="timeline-item  timeline-item-{{ $detail->id }}">
                <span class="time"><i class="fa fa-clock-o"></i>
                  <span id="date_{{ $detail->id }}">

                    @if($detail->subCheckStatus == 1)
                    <span id="status_span_{{ $detail->id }}_v2" class="badge badge-success"> Completed</span>
                    @elseif($detail->subCheckStatus == 99)
                    <span id="status_span_{{ $detail->id }}_v2" class="badge badge-info"> Not Applicable</span>
                    @else
       
                    @endif



                  </span>
                </span>
                <input class="form-control" type="hidden" id="act_{{ $detail->id }}" value="{{ $detail->name }}">
                <input class="form-control" type="hidden" id="remark_{{ $detail->id }}" value="{{ $detail->remark }}">
                <input class="form-control" type="hidden" id="status_{{ $detail->id }}" value="{{ $detail->status }}">

                <h3 class="timeline-header no-border">
                  {{ $detail->name }} 
                </h3>

                <div id="body_{{ $detail->id }}" class="timeline-body">
                  <span class="action-view-group" id="ori_remark_{{ $detail->id }}">{{$detail->remark}}</span>

                  <!-- <input class="form-control" type="hidden" name="selected_id" value=""> -->
                  <div style="display:none" class="checkbox">
                    <input type="checkbox" name="checklist_id_v2" value="{{ $detail->id }}" id="chklist_{{ $detail->id }}_v2">
                    <label for="chklist_{{ $detail->id }}_v2"></label>
                  </div>
                  <div class="action-edit-group" style="display:none">
                    {{-- <textarea onchange="checkListRemarkevent('{{ $detail->id }}')" class="form-control " id="edited_remarks_{{ $detail->id }}" name="edited_remarks" rows="5">{{$detail->remark}}</textarea> --}}

                    <div class="checkbox">
                      <input type="checkbox" onchange="checkListRemarkeventV2('{{ $detail->id }}')" name="complete" value="{{ $detail->id }}" id="complete_{{ $detail->id }}_v2" @if($detail->subCheckStatus == 1) checked @endif>
                      <label for="complete_{{ $detail->id }}_v2">Completed</label>
                    </div>
                    <input type="hidden" id="checklist_id_{{ $detail->id }}_v2" value="@if($detail->checklist_details_id) {{$detail->checklist_details_id}} @else 0 @endif" />


                    <div class="checkbox">
                      <input type="checkbox" onchange="checkListRemarkeventV2('{{ $detail->id }}')" name="not_applicable" value="{{ $detail->id }}" id="na_{{ $detail->id }}_v2" @if($detail->subCheckStatus == 99) checked @endif>
                      <label for="na_{{ $detail->id }}_v2">Not Applicable</label>
                    </div>
                  </div>


                </div>


              </div>
            </li>
            @endforeach
          </ul>
        </section>
        @endforeach
      </div>

    </form>
    @else
    @include('dashboard.case.section.d-case-template-selection')
    @endif


  </div>
  <!-- /.box-body -->
</div>