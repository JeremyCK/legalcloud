<div class="box box-default">
    <!-- /.box-header -->
    <div class="box-body wizard-content">


        @if (count($CheckListMain))
            <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">

                <div class="steps clearfix">
                    <ul role="tablist">

                        <?php $checkpoint_count = 0; ?>
                        @foreach ($CheckListMain as $index2 => $detail)
                            @if ($detail->id != '0')
                                <?php $checkpoint_count += 1; ?>
                                <li id="li_check_point_{{ $checkpoint_count }}" role="tab"
                                    class="first li_check_point @if ($checkpoint_count == '0') current @endif   @if ($detail->status == 1) done @endif"
                                    aria-disabled="false" aria-selected="false">
                                    <a id="steps-uid-0-t-0" href="#cp_{{ $detail->id }}"
                                        aria-controls="steps-uid-0-p-0" data-toggle="tab" role="tab"
                                        aria-controls="home" aria-selected="false">
                                        <span class="step">
                                            @if ($detail->status == 1)
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

                <div class="tab-content">
                    @foreach ($CheckListMain as $index2 => $main)
                        <div class="tab-pane @if ($index2 == 0) active @endif "
                            id="cp_{{ $main->id }}" role="tabpanel">

                            <ul class="timeline">
                                @foreach ($CheckListDetails as $index1 => $detail)
                                    <li class="li-item-{{ $detail->id }}">
                                        <i id="checklist_{{ $detail->id }}" class="ion ion-checkmark bg-done"></i>
                                        <div class="timeline-item  timeline-item-{{ $detail->id }}">

                                            <a class="btn btn-info float-right" href="javascript:void(0)"
                                                data-backdrop="static" data-keyboard="false"
                                                style="color:white;margin:0" data-toggle="modal"
                                                data-target="#modalUpload">
                                                <i style="margin-right: 10px;" class="cil-cloud-upload"></i>Upload </a>

                                            {{-- <span class="time"><i class="fa fa-clock-o"></i> --}}
                                            </span>
                                            <h3 class="timeline-header no-border">
                                                {{ $detail->name }}
                                            </h3>


                                            <div id="body_{{ $detail->id }}" class="timeline-body">
                                                <a href="javascript:void(0)"
                                                    onclick="openFileFromS3('dispatch/r1Tu4lEp5cgKS6rVKkhi1ckXN6a8FhFY98Qv4Ggh.pdf')"
                                                    class=" "><i class="fa fa-paperclip"></i> BILL LOAN RM318,161.00 Lee Wai Koon (WT,FH,SG).pdf</a>
                                                <br />
                                                <a href="javascript:void(0)"
                                                    onclick="openFileFromS3('dispatch/r1Tu4lEp5cgKS6rVKkhi1ckXN6a8FhFY98Qv4Ggh.pdf')"
                                                    class=" "><i class="fa fa-paperclip"></i> 40454-1.pdf</a>

                                                    {{-- <ul class="mailbox-attachments clearfix">
                                                      <li>
                                                        <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>
                                  
                                                        <div class="mailbox-attachment-info">
                                                          <a target="_blank" href="/" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> BILL LOAN RM318,161.00 Lee Wai Koon (WT,FH,SG).pdf</a>
                                                          <span class="mailbox-attachment-size">
                                                            <a target="_blank" href="/" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                                          </span>
                                                        </div>
                                                      </li>
                                                    </ul> --}}


                                            </div>

                                        </div>

                                    </li>
                                @endforeach

                            </ul>

                            <table class="table table-bordered yajra-datatable" id="tbl-cp-{{ $main->id }}"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($CheckListDetails as $index1 => $details)
                                        @if ($details->checklist_main_id == $main->id)
                                            <tr>
                                                <td>{{ $index1 + 1 }}</td>
                                                <td>{{ $details->name }}</td>
                                                <td>
                                                    <a href="javascript:void(0)"
                                                        onclick="openFileFromS3('dispatch/r1Tu4lEp5cgKS6rVKkhi1ckXN6a8FhFY98Qv4Ggh.pdf')"
                                                        class="mailbox-attachment-name "><i
                                                            class="fa fa-paperclip"></i>40454-1.pdf</a>
                                                </td>
                                                <td>
                                                    <a class="btn btn-info" href="javascript:void(0)"
                                                        data-backdrop="static" data-keyboard="false"
                                                        style="color:white;margin:0" data-toggle="modal"
                                                        data-target="#modalUpload">
                                                        <i style="margin-right: 10px;"
                                                            class="cil-cloud-upload"></i>Upload </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>


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
