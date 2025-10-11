
          <div class="box box-default">
            <!-- /.box-header -->
            <div class="box-body wizard-content">




              <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">
                <div class="steps clearfix">
                  <ul role="tablist">

                    <?php $checkpoint_count = 0; ?>
                    @foreach($loanCaseCheckPoint as $index2 => $detail)

                    @if ($detail->check_point != "0")
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
                        <span class="checklist_name">
                          

                          @if ( $checkpoint_count == 1) Open Case
                          @elseif ( $checkpoint_count == 2) S&P
                          @elseif ( $checkpoint_count == 3) CKHT
                          @elseif ( $checkpoint_count == 4) MOT2
                          @elseif ( $checkpoint_count == 5) Redemption
                          @elseif ( $checkpoint_count == 6) BPP
                          @elseif ( $checkpoint_count == 7) Handover
                          @elseif ( $checkpoint_count == 8) Close
                          @elseif ( $checkpoint_count == 9) CKHT
                          @elseif ( $checkpoint_count == 10) CKHT
                          @elseif ( $checkpoint_count == 11) CKHT
                          @else none
                          @endif

                        </span>
                      </a>
                    </li>



                    @endif
                    @endforeach

                  </ul>
                </div>
                <div class="content clearfix">
                  <!-- Step 1 -->
                  <?php $checkpoint_count = 0; ?>
                  @foreach($loanCaseCheckPoint as $index2 => $checklist)

                  @if ($detail->check_point != "0")
                  <?php $checkpoint_count += 1;  ?>


                  <section id="s_check_point_{{$checkpoint_count}}" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current sCheckpoint" aria-hidden="true" style="@if ($checkpoint_count > 1) display: none; @endif">

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
                        @endif


                        <div class="timeline-item">
                          <span class="time"><i class="fa fa-clock-o"></i><span id="date_{{ $detail->id }}">{{ $detail->updated_at }}</span> </span>

                          <input class="form-control" type="hidden" id="act_{{ $detail->id }}" value="{{ $detail->checklist_name }}">
                          <input class="form-control" type="hidden" id="file_{{ $detail->id }}" value="{{ $detail->need_attachment }}">
                          <input class="form-control" type="hidden" id="remark_{{ $detail->id }}" value="{{ $detail->remarks }}">
                          <input class="form-control" type="hidden" id="status_{{ $detail->id }}" value="{{ $detail->status }}">
                          <h3 class="timeline-header no-border">
                            {{ $detail->process_number }}. <a class="a_{{$detail->role_name}}" href="#"> @if($detail->name == null) {{"system"}} @else {{$detail->name}} @endif </a> {{ $detail->checklist_name }}
                          </h3>

                          <div id="body_{{ $detail->id }}" class="timeline-body">
                            @if($detail->remarks != null)
                            {{$detail->remarks}}
                            @endif
                            @if($detail->need_attachment == 1)
                            <ul class="mailbox-attachments clearfix">
                              @foreach($detail->files as $index5 => $file)
                              <li>
                                <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>

                                <div class="mailbox-attachment-info">
                                  <a target="_blank" href="/{{$file->filename}}" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{$file->display_name}}</a>
                                  <span class="mailbox-attachment-size">
                                    <!-- 5,215 KB -->
                                    <a target="_blank" href="/{{$file->filename}}" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                  </span>
                                </div>
                              </li>
                              @endforeach
                            </ul>
                            <!-- <ul class="mailbox-attachments clearfix">
                              <li>
                                <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>

                                <div class="mailbox-attachment-info">
                                  <a href="#" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> Mag.pdf</a>
                                  <span class="mailbox-attachment-size">
                                    5,215 KB
                                    <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                  </span>
                                </div>
                              </li>
                            </ul> -->
                            @endif
                          </div>
                          @if($detail->name != null)
                          <div class="timeline-footer text-right">
                            @if($detail->need_attachment == 1 )
                            <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-info btn-sm" onclick="fileMode('{{ $detail->id }}', '{{ $cases[0]->id }}')"><i class="ion-document"></i> Attach File</a>

                            @endif
                            @if($detail->bln_gen_doc == 1)
                            <a id="btn_action_{{ $detail->id }}" href="/document/{{ $cases[0]->id }}/{{ $cases[0]->id }}" class="btn btn-warning btn-sm"><i class="ion-archive"></i> Generate Document</a>

                            @endif
                            <!-- <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}')">Remark</a> -->

                            <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}')"><i class="ion-compose"></i> Action</a>
                          </div>

                          @endif


                        </div>
                      </li>

                      @endforeach

                      <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                      </li>
                    </ul>


                  </section>



                  @endif
                  @endforeach


                </div>

              </form>
            </div>
            <!-- /.box-body -->
          </div>
