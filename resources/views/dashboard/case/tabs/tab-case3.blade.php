<style>
    .nav-item {
        width: 100%;
        text-align: center
    }
</style>
<div class="box box-default">
    <!-- /.box-header -->
    <div class="box-body wizard-content">

      <h3 class="box-title mb-5">Checklist</h3>


        @if (count($CheckListMain))
            <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">

                <div class=" clearfix" style="width: 100%">
                    <ul class="nav nav-tabs scrollable-tabs" role="tablist">


                        @foreach ($CheckListMain as $index2 => $detail)
                            @if ($detail->id != '0')
                                <li class="nav-item"><a class="nav-link @if ($index2 == 0) active @endif"
                                        data-toggle="tab" href="#cp_{{ $detail->id }}" role="tab"
                                        aria-controls="log" aria-selected="true">
                                        
                                        <i class="cil-file c-sidebar-nav-icon"></i> {!! $detail->name !!}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="tab-content mt-5">
                    @foreach ($CheckListMain as $index2 => $main)
                        <div class="tab-pane @if ($index2 == 0) active @endif "
                            id="cp_{{ $main->id }}" role="tabpanel">

                            <ul class="timeline">
                                @foreach ($CheckListDetails as $index1 => $detail)
                                    @if ($detail->checklist_main_id == $main->id)
                                        <li class="li-item-{{ $detail->id }}">

                                            @if ($LoanAttachment->where('attachment_type', 9)->where('checklist_id', $detail->id)->count() != 0)
                                                <i id="checklist_{{ $detail->id }}"
                                                    class="ion ion-checkmark bg-done"></i>
                                            @else
                                                @if($detail->is_note == 1)
                                                <i id="checklist_{{ $detail->id }}"
                                                    class="ion ion-information bg-danger"></i>
                                                @else
                                                <i id="checklist_{{ $detail->id }}"
                                                    class="ion ion-checkmark bg-aqua"></i>
                                                @endif
                                               
                                            @endif

                                            <div class="timeline-item  timeline-item-{{ $detail->id }}">

                                                @if($detail->is_note != 1)
                                                    <a class="btn btn-info float-right" href="javascript:void(0)"
                                                        data-backdrop="static" data-keyboard="false"
                                                        onclick="$('#checklist_id').val('{{ $detail->id }}');"
                                                        style="color:white;margin:0" data-toggle="modal"
                                                        data-target="#modalUpload">
                                                        <i style="margin-right: 10px;" class="cil-cloud-upload"></i>Upload
                                                    </a>
                                                @endif

                                              

                                                {{-- </span> --}}
                                                <h3 class="timeline-header no-border">
                                                    {!! $detail->name !!}
                                                </h3>


                                                <div id="body_{{ $detail->id }}" class="timeline-body">

                                                    @if (count($LoanAttachment))
                                                        @foreach ($LoanAttachment as $index => $attachment)
                                                            @if ($attachment->attachment_type == 9)
                                                                @if ($attachment->checklist_id == $detail->id)
                                                                    <a href="javascript:void(0)"
                                                                        onclick="openFileFromS3('{{ $attachment->s3_file_name }}')"
                                                                        class=" "><i class="fa fa-paperclip"></i>
                                                                        {{ $attachment->display_name }}</a>

                                                                    <a href="javascript:void(0)"
                                                                        onclick="deleteMarketingBill('{{ $attachment->id }}')"
                                                                        class=" "><i
                                                                            class="fa fa-close text-danger"></i></a>
                                                                    <br />
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif



                                                </div>

                                            </div>

                                        </li>
                                    @endif
                                @endforeach

                            </ul>

                        </div>
                    @endforeach
                </div>
            </form>
        @else
            @include('dashboard.case.section.d-case-template-selection')
        @endif

    </div>
    <!-- /.box-body -->
</div>
