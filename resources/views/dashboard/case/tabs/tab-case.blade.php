<div class="box box-default">
  <!-- /.box-header -->
  <div class="box-body wizard-content">


    @if(count($loan_case_checklist_main))
    <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">

      <div class="steps clearfix">
        <ul role="tablist">

          <?php $checkpoint_count = 0; ?>
          @foreach($loan_case_checklist_main as $index2 => $detail)

          @if ($detail->id != "0")
          <?php $checkpoint_count += 1;  ?>
          <li id="li_check_point_{{$checkpoint_count }}" role="tab" class="first li_check_point @if ($checkpoint_count == '0') current @endif   @if ($detail->status == 1) done @endif" aria-disabled="false" aria-selected="false">
            <a id="steps-uid-0-t-0" onclick="stepController('{{$checkpoint_count }}');" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
              <span class="step">
                @if($detail->status == 1)
                <i id="checklist_" class="ion ion-checkmark "></i>
                @else
                {{ $checkpoint_count }}
                @endif
              </span>
              <span class="checklist_name">{{ $detail->name }}a</span>
            </a>
          </li>
          @endif
          @endforeach

        </ul>
      </div>
      <div class="content clearfix">

        <?php $checkpoint_count = 0; ?>
        @foreach($loan_case_checklist_main as $index2 => $checklist)
        <?php $checkpoint_count += 1;  ?>
        <section id="s_check_point_{{$checkpoint_count}}" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current sCheckpoint" aria-hidden="true" style="@if ($checkpoint_count > 1) display: none; @endif">
          <div class="timeline-footer text-right">
            <a id="btn_action_2010" href="javascript:void(0)" class="btn btn-info btn-sm action-view-group" onclick="editActionMode('{{$detail->id}}')"><i class="ion-compose"></i> Edit Mode</a>
            <a id="btn_action_2010" href="javascript:void(0)" class="btn btn-danger btn-sm action-edit-group " style="display:none" onclick="cancelEditActionMode('{{$detail->id}}')"><i class="ion-compose"></i> Cancel</a>
            <a id="btn_action_2010" href="javascript:void(0)" class="btn btn-primary btn-sm action-edit-group " style="display:none" onclick="saveAllCheckListEvent('{{$detail->id}}')"><i class="ion-compose"></i>
              
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
              @if($detail->status == 1)
              <i id="checklist_{{ $detail->id }}" class="ion ion-checkmark bg-done"></i>
              @elseif($detail->status == 0)
              <i id="checklist_{{ $detail->id }}" class="ion ion-person bg-aqua"></i>
              @elseif($detail->status == 2)
              <i id="checklist_{{ $detail->id }}" class="ion ion-clock bg-overdue"></i>
              @elseif($detail->status == 99)
              <i id="checklist_{{ $detail->id }}" class="ion ion-clock bg-info"></i>
              @endif

              <div class="timeline-item  timeline-item-{{ $detail->id }}">
                <span class="time"><i class="fa fa-clock-o"></i>
                  <span id="date_{{ $detail->id }}">
                    <!-- {{ $detail->target_close_date }} -->

                    @if($detail->status == 1)
                    <span id="status_span_{{ $detail->id }}" class="badge badge-success"> Completed</span>
                    @elseif($detail->status == 99)
                    <span id="status_span_{{ $detail->id }}" class="badge badge-info"> Not Applicable</span>
                    @else
                    @php($date_facturation = \Carbon\Carbon::parse($detail->target_close_date))
                    @if ($date_facturation->isPast())
                    <td>
                      <span id="status_span_{{ $detail->id }}" class="badge badge-danger"> {{ $detail->target_close_date }}</span>
                    </td>
                    @else
                    <span id="status_span_{{ $detail->id }}" class="badge badge-info"> {{ $detail->target_close_date }}</span>
                    @endif
                    @endif



                  </span>
                </span>
                <input class="form-control" type="hidden" id="act_{{ $detail->id }}" value="{{ $detail->name }}">
                <input class="form-control" type="hidden" id="file_{{ $detail->id }}" value="{{ $detail->need_attachment }}">
                <input class="form-control" type="hidden" id="remark_{{ $detail->id }}" value="{{ $detail->remark }}">
                <input class="form-control" type="hidden" id="status_{{ $detail->id }}" value="{{ $detail->status }}">

                <h3 class="timeline-header no-border">
                  <!-- <a class="a_{{$detail->role_name}}" href="#"> @if($detail->roles == 1) {{"system"}} @else {{$detail->user_name}} @endif </a> -->
                  {{ $detail->name }}
                </h3>

                <div id="body_{{ $detail->id }}" class="timeline-body">
                  <span class="action-view-group" id="ori_remark_{{ $detail->id }}">{{$detail->remark}}</span>

                  <!-- <input class="form-control" type="hidden" name="selected_id" value=""> -->
                  <div style="display:none" class="checkbox">
                    <input type="checkbox" name="checklist_id" value="{{ $detail->id }}" id="chklist_{{ $detail->id }}">
                    <label for="chklist_{{ $detail->id }}"></label>
                  </div>
                  <div class="action-edit-group" style="display:none">
                    <textarea onchange="checkListRemarkevent('{{ $detail->id }}')" class="form-control " id="edited_remarks_{{ $detail->id }}" name="edited_remarks" rows="5">{{$detail->remark}}</textarea>

                    <div class="checkbox">
                      <input type="checkbox" onchange="checkListRemarkevent('{{ $detail->id }}')" name="complete" value="{{ $detail->id }}" id="complete_{{ $detail->id }}" @if($detail->status == 1) checked @endif>
                      <label for="complete_{{ $detail->id }}">Completed</label>
                    </div>


                    <div class="checkbox">
                      <input type="checkbox" onchange="checkListRemarkevent('{{ $detail->id }}')" name="not_applicable" value="{{ $detail->id }}" id="na_{{ $detail->id }}" @if($detail->status == 99) checked @endif>
                      <label for="na_{{ $detail->id }}">Not Applicable</label>
                    </div>
                  </div>

                  @if($detail->need_attachment == 1)
                  <ul class="mailbox-attachments clearfix">
                    @foreach($detail->files as $index5 => $file)
                    <li>
                      <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>

                      <div class="mailbox-attachment-info">
                        <a target="_blank" href="/{{$file->filename}}" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{$file->display_name}}</a>
                        <span class="mailbox-attachment-size">
                          <a target="_blank" href="/{{$file->filename}}" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                      </div>
                    </li>
                    @endforeach
                  </ul>
                  @endif
                </div>

                @if($case->status == 1)
                <div class="timeline-footer text-right">
                  <!-- @if($detail->need_attachment == 1 )
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-info btn-sm" onclick="fileMode('{{ $detail->id }}', '{{ $case->id }}')"><i class="ion-document"></i> Attach File</a>
                  @endif

                  @if($detail->open_case != 1 && $detail->close_case != 1)
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}', '{{ $case->id }}')"><i class="ion-compose"></i> Action</a>
                  @endif

                  @if($detail->close_case == 1)
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-warning btn-sm" onclick="closeCase('{{ $detail->id }}', '{{ $case->id }}')"><i class="ion-compose"></i> Close Case</a>
                  @endif -->

                </div>

                @endif
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