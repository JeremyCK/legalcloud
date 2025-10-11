@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<!-- <link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet"> -->

<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .info-box {
    min-height: 100px;
    background: #fff;
    width: 100%;
    margin-bottom: 20px;
    padding: 15px;
  }

  .info-box small {
    font-size: 14px;
  }

  .info-box .progress {
    background: rgba(0, 0, 0, .2);
    margin: 5px -10px 5px 0;
    height: 2px;
  }

  .info-box .progress,
  .info-box .progress .progress-bar {
    border-radius: 0;
  }

  .info-box .progress .progress-bar {
    background: #fff;
  }

  .info-box-icon {
    float: left;
    height: 70px;
    width: 70px;
    text-align: center;
    font-size: 30px;
    line-height: 74px;
    background: rgba(0, 0, 0, .2);
    border-radius: 100%
  }

  .info-box-icon.push-bottom {
    margin-top: 20px;
  }

  .info-box-icon>img {
    max-width: 100%
  }

  .info-box-content {
    padding: 10px 10px 10px 0;
    margin-left: 90px;
  }

  .info-box-number {
    font-weight: 300;
    font-size: 21px;
  }

  a.a_admin,
  a.a_admin:hover,
  a.a_admin:active,
  a.a_admin:focus {
    color: #f9b115;
  }

  a.a_sales,
  a.a_sales:hover,
  a.a_sales:active,
  a.a_sales:focus {
    color: #e55353;
  }

  a.a_lawyer,
  a.a_lawyer:hover,
  a.a_lawyer:active,
  a.a_lawyer:focus {
    color: #4638c2;
  }

  a.a_clerk,
  a.a_clerk:hover,
  a.a_clerk:active,
  a.a_clerk:focus {
    color: #2ca8ff;
  }

  .bg-done {
    background-color: #46be8a !important;
    color: white !important;
  }

  .bg-overdue {
    background-color: #e55353 !important;
    color: white !important;
  }

  .info-box-text,
  .progress-description {
    display: block;
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    font-weight: 400;
  }
</style>

<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<!-- <link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet"> -->
@endsection

@extends('dashboard.base')
@section('content')


<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-6">


        <div class="card">
          <div class="card-header">
            <h4>Case Status Details</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <table class="table datatable">
              <tbody>
                <tr>
                  <th>Case Ref Number</th>
                  <td>{{ $cases[0]->case_ref_no }}</td>
                </tr>
                <!-- <tr>
                  <th>Status</th>
                  <td>
                     @if($cases[0]->status == 1)
                        <span class="badge badge-success">Done</span>
                        @elseif($cases[0]->status == 0)
                        <span class="badge badge-dark">Pending</span>
                        @elseif($cases[0]->status == 2)
                        <span class="badge badge-warning">Done with Overdue</span>
                        @endif
                  </td>
                </tr> -->
                <tr>
                  <th>Percentage Completion</th>
                  <td>{{ $cases[0]->percentage }} %
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 40%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>Start Date</th>
                  <td>{{ $cases[0]->created_at }} </td>
                </tr>
                <tr>
                  <th>Day(s)</th>
                  <td> {{ $datediff }} Days </td>
                </tr>
              </tbody>
            </table>
            <!-- <a class="btn btn-primary" href="{{ route('todolist.index') }}">Return</a>
            <a class="btn btn-primary" href="{{ route('todolist.index') }}">Master List</a> -->
          </div>
        </div>

      </div>

      <div class="col-sm-6">
        <div class="card">
          <div class="card-header">
            <h4>Person In Charge</h4>
          </div>
          <div class="card-body">
            <table class="table table-responsive-sm table-hover table-outline mb-0">
              <tbody>
                <tr>
                  <td class="text-center">
                    <div class="c-avatar"><img class="c-avatar-img" src="../assets/img/avatars/img-default-profile.png" alt="user@email.com"><span class="c-avatar-status bg-success"></span></div>
                  </td>
                  <td>
                    <div>{{ $cases[0]->lawyer }}</div>
                    <div class="small text-muted"><span>Lawyer</span></div>
                  </td>
                  <td class="text-center"><i class="flag-icon flag-icon-us c-icon-xl" id="us" title="us"></i></td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>50%</strong></div>
                      <!-- <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div> -->
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td class="text-center">
                    <svg class="c-icon c-icon-xl">
                      <use xlink:href="assets/icons/brands/brands-symbol-defs.svg#cc-mastercard"></use>
                    </svg>
                  </td>
                  <td>
                    <div class="small text-muted">Last action</div><strong>10 {{ __('dashboard.time.sec_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <div class="c-avatar"><img class="c-avatar-img" src="../assets/img/avatars/img-default-profile.png" alt="user@email.com"><span class="c-avatar-status bg-danger"></span></div>
                  </td>
                  <td>
                    <div>{{ $cases[0]->clerk }}</div>
                    <div class="small text-muted">Clerk</div>
                  </td>
                  <td class="text-center"><i class="flag-icon flag-icon-br c-icon-xl" id="br" title="br"></i></td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>10%</strong></div>
                      <!-- <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div> -->
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td class="text-center">
                    <svg class="c-icon c-icon-xl">
                      <use xlink:href="assets/icons/brands/brands-symbol-defs.svg#cc-visa"></use>
                    </svg>
                  </td>
                  <td>
                    <div class="small text-muted">Last action</div><strong>5 {{ __('dashboard.time.minutes_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <div class="c-avatar"><img class="c-avatar-img" src="../assets/img/avatars/img-default-profile.png" alt="user@email.com"><span class="c-avatar-status bg-danger"></span></div>
                  </td>
                  <td>
                    <div>{{ $cases[0]->sales }}</div>
                    <div class="small text-muted">Sales</div>
                  </td>
                  <td class="text-center"><i class="flag-icon flag-icon-br c-icon-xl" id="br" title="br"></i></td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>10%</strong></div>
                      <!-- <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div> -->
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td class="text-center">
                    <svg class="c-icon c-icon-xl">
                      <use xlink:href="assets/icons/brands/brands-symbol-defs.svg#cc-visa"></use>
                    </svg>
                  </td>
                  <td>
                    <div class="small text-muted">Last action</div><strong>5 {{ __('dashboard.time.minutes_ago') }}</strong>
                  </td>
                </tr>


              </tbody>
            </table>
          </div>
        </div>

      </div>


    </div>

    <div class="row hide">
      <div class="col-xl-3 col-md-6 col-12">
        <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="cil-folder-open"></i></span>

          <div class="info-box-content">
            <span class="info-box-number">2</span>
            <span class="info-box-text">Open case</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-xl-3 col-md-6 col-12">
        <div class="info-box">
          <span class="info-box-icon bg-green" style="padding-top: 17px;"><i class="cil-check"></i></span>

          <div class="info-box-content">
            <span class="info-box-number">5</span>
            <span class="info-box-text">Closed Case</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

      <!-- fix for small devices only -->
      <div class="clearfix visible-sm-block"></div>

      <div class="col-xl-3 col-md-6 col-12">
        <div class="info-box">
          <span class="info-box-icon bg-purple" style="padding-top: 17px;"><i class="cil-running"></i></span>

          <div class="info-box-content">
            <span class="info-box-number">1</span>
            <span class="info-box-text">In progress case</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-xl-3 col-md-6 col-12">
        <a class="btn btn-primary nav-link " data-toggle="tab" href="#home" role="tab" href="javascript:void(0)">
          <div class="info-box">
            <span class="info-box-icon bg-red" style="padding-top: 17px;"><i class="cil-av-timer"></i></span>

            <div class="info-box-content">
              <span class="info-box-number">2</span>
              <span class="info-box-text">Overdue cases</span>
            </div>
            <!-- /.info-box-content -->
          </div>
        </a>

        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>

    @if($cases[0]->status == 2)
    @if($current_user->menuroles == 'lawyer')

    <div class="card">
      <div class="card-body">
        @if(Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
        @endif

        <form action="{{ route('case.update', $cases[0]->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-sm-12">

              <h4 style="margin-bottom: 50px;">Accept new case</h4>

              <div class="form-group row">
                <label class="col-md-12 col-form-label">define the template</label>
                <div class="col-md-12">
                  <!-- <input type="hidden" id="" name="fax" value="" class="form-control" /> -->
                  <select class="form-control" id="template" name="template">
                    @foreach($caseTemplate as $index => $template)
                    <option value="{{$template->id }}">{{$template->display_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="wizard-footer">
                <div class="pull-right">
                  <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Accept' />
                </div>

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
        </form>

      </div>
    </div>
    @else
    <div class="card">
      <div class="card-body">

        <form action="{{ route('case.update', $cases[0]->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-sm-12">

              <h4 style="margin-bottom: 50px;">Pending on lawyer to accept the case </h4>


            </div>

          </div>
        </form>

      </div>
    </div>
    @endif
    @elseif($cases[0]->status == 1)

    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#home-1" role="tab" aria-controls="home" aria-selected="false">Checklist</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#profile-1" role="tab" aria-controls="profile" aria-selected="false">Master List</a></li>
        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#messages-1" role="tab" aria-controls="messages" aria-selected="true">Account</a></li>
        <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#dispatch" role="tab" aria-controls="dispatch" aria-selected="true">Dispatch</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="home-1" role="tabpanel" style="max-height:700px;overflow-y:auto">




          <div class="box box-default">
            <!-- /.box-header -->
            <div class="box-body wizard-content">
              <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">
                <div class="steps clearfix">
                  <ul role="tablist">
                    <!-- <li role="tab" class="first current" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-0" href="javascript:void(0)" aria-controls="steps-uid-0-p-0"><span class="step">1</span><span class="">Personal Info</span> </a></li>
                    <li role="tab" class="others " aria-disabled="false" aria-selected="true"><a id="steps-uid-0-t-1" href="javascript:void(0)" aria-controls="steps-uid-0-p-1"><span class="current-info audible">current step: </span><span class="step">2</span> Job Status</a></li>
                    <li role="tab" class="others" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-2" href="javascript:void(0)" aria-controls="steps-uid-0-p-2"><span class="step">3</span> Interview</a></li>
                    <li role="tab" class="last" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-3" href="javascript:void(0)" aria-controls="steps-uid-0-p-3"><span class="step">4</span> Remark</a></li>
                    <li role="tab" class="last" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-3" href="javascript:void(0)" aria-controls="steps-uid-0-p-3"><span class="step">4</span> Remark</a></li>
                    <li role="tab" class="last" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-3" href="javascript:void(0)" aria-controls="steps-uid-0-p-3"><span class="step">4</span> Remark</a></li>
                    <li role="tab" class="last" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-3" href="javascript:void(0)" aria-controls="steps-uid-0-p-3"><span class="step">4</span> Remark</a></li>
                    <li role="tab" class="last" aria-disabled="false" aria-selected="false"><a id="steps-uid-0-t-3" href="javascript:void(0)" aria-controls="steps-uid-0-p-3"><span class="step">4</span> Remark</a></li> -->

                    <?php $checkpoint_count = 0; ?>
                    @foreach($cases_details as $index2 => $detail)

                      @if ($detail->check_point != "0")
                      <?php $checkpoint_count += 1;  ?>
                      <li id="li_check_point_{{$checkpoint_count }}" role="tab" class="first @if ($checkpoint_count == '0') current @endif   @if ($detail->status == 1) done @endif" aria-disabled="false" aria-selected="false">
                        <a id="steps-uid-0-t-0" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
                          <span class="step">
                            @if($detail->status == 1)
                            <i id="checklist_" class="ion ion-checkmark "></i>
                            @else
                            {{ $checkpoint_count }}
                            @endif

                          </span>
                          <span class="checklist_name">{{ $detail->checklist_name }}</span>
                        </a>
                      </li>



                      @endif
                    @endforeach

                  </ul>
                </div>
                <div class="content clearfix">
                  <!-- Step 1 -->
                  @foreach($cases_details as $index2 => $detail)
                  <section id="steps-uid-0-p-0" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current" aria-hidden="true" style="">



                  <ul class="timeline">
            <?php $checkpoint_count = 0; ?>
            @foreach($cases_details as $index2 => $detail)

            @if ($detail->check_point != "0")

            <?php $checkpoint_count += 1; ?>
            <li id="checkpoint-label-{{$checkpoint_count }}" class="time-label" onclick="timelineCOntroller('{{ $checkpoint_count }}');">
              @if($detail->status == 1)
              <span class="bg-green">
                @elseif($detail->status == 0)
                <span class="bg-aqua">
                  @endif
                  Checkpoint {{ $checkpoint_count }}: {{ $detail->checklist_name }} &nbsp;
                  <i id="main_checklist_{{$checkpoint_count }}" class="ion ion-arrow-up-b"></i>
                </span>
            </li>
            @endif

            <li class="li-item-{{ $checkpoint_count }}">
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
                <h3 class="timeline-header no-border">
                  {{$index2 + 1 }}. <a class="a_{{$detail->role_name}}" href="#"> @if($detail->name == null) {{"system"}} @else {{$detail->name}} @endif </a> {{ $detail->checklist_name }}
                </h3>

                <div id="body_{{ $detail->id }}" class="timeline-body">
                  @if($detail->remarks != null)
                  {{$detail->remarks}}
                  @endif
                  @if($detail->need_attachment == 1 && $detail->status == 1)
                  <ul class="mailbox-attachments clearfix">
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
                  </ul>
                  @endif
                </div>
                @if($detail->name != null)
                <div class="timeline-footer text-right">
                  @if($detail->need_attachment == 1 && $detail->status == 1)
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="fileMode('{{ $detail->id }}')">Attach File</a>

                  @endif
                  @if($detail->bln_gen_doc == 1)
                  <a id="btn_action_{{ $detail->id }}" href="/document/{{ $cases[0]->id }}/{{ $cases[0]->id }}" class="btn btn-primary btn-sm">Generate Document</a>

                  @endif
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}')">Remark</a>

                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}')">Action</a>
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
                  @endforeach
                  <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">Personal Info</h6>
                  <section id="steps-uid-0-p-0" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current" aria-hidden="true" style="">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="firstName5">First Name :</label>
                          <input type="text" class="form-control" id="firstName5">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="lastName1">Last Name :</label>
                          <input type="text" class="form-control" id="lastName1">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="emailAddress1">Email Address :</label>
                          <input type="email" class="form-control" id="emailAddress1">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="phoneNumber1">Phone Number :</label>
                          <input type="tel" class="form-control" id="phoneNumber1">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="location3">Select City :</label>
                          <select class="custom-select form-control" id="location3" name="location">
                            <option value="">Select City</option>
                            <option value="Amsterdam">India</option>
                            <option value="Berlin">USA</option>
                            <option value="Frankfurt">Dubai</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="date1">Date of Birth :</label>
                          <input type="date" class="form-control" id="date1">
                        </div>
                      </div>
                    </div>
                  </section>
                  <!-- Step 2 -->
                  <h6 id="steps-uid-0-h-1" tabindex="-1" class="title ">Job Status</h6>
                  <section id="steps-uid-0-p-1" role="tabpanel" aria-labelledby="steps-uid-0-h-1" class="body " aria-hidden="false" style="display: none;">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="jobTitle1">Job Title :</label>
                          <input type="text" class="form-control" id="jobTitle1">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="videoUrl1">Company Name :</label>
                          <input type="text" class="form-control" id="videoUrl1">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="shortDescription1">Job Description :</label>
                          <textarea name="shortDescription" id="shortDescription1" rows="6" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                  </section>
                  <!-- Step 3 -->
                  <h6 id="steps-uid-0-h-2" tabindex="-1" class="title">Interview</h6>
                  <section id="steps-uid-0-p-2" role="tabpanel" aria-labelledby="steps-uid-0-h-2" class="body" aria-hidden="true" style="display: none;">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="int1">Interview For :</label>
                          <input type="text" class="form-control" id="int1">
                        </div>
                        <div class="form-group">
                          <label for="intType1">Interview Type :</label>
                          <select class="custom-select form-control" id="intType1" data-placeholder="Type to search cities" name="intType1">
                            <option value="Banquet">Normal</option>
                            <option value="Fund Raiser">Difficult</option>
                            <option value="Dinner Party">Hard</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="Location1">Location :</label>
                          <select class="custom-select form-control" id="Location1" name="location">
                            <option value="">Select City</option>
                            <option value="India">India</option>
                            <option value="USA">USA</option>
                            <option value="Dubai">Dubai</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="jobTitle2">Interview Date :</label>
                          <input type="date" class="form-control" id="jobTitle2">
                        </div>
                        <div class="form-group">
                          <label>Requirements :</label>
                          <div class="c-inputs-stacked">
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">Employee</span> </label>
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">Contract</span> </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </section>
                  <!-- Step 4 -->
                  <h6 id="steps-uid-0-h-3" tabindex="-1" class="title">Remark</h6>
                  <section id="steps-uid-0-p-3" role="tabpanel" aria-labelledby="steps-uid-0-h-3" class="body" aria-hidden="true" style="display: none;">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="behName1">Behaviour :</label>
                          <input type="text" class="form-control" id="behName1">
                        </div>
                        <div class="form-group">
                          <label for="participants1">Confidance</label>
                          <input type="text" class="form-control" id="participants1">
                        </div>
                        <div class="form-group">
                          <label for="participants2">Result</label>
                          <select class="custom-select form-control" id="participants2" name="location">
                            <option value="">Select Result</option>
                            <option value="Selected">Selected</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Call Second-time">Call Second-time</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="decisions1">Comments</label>
                          <textarea name="decisions" id="decisions1" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                          <label>Rate Interviwer :</label>
                          <div class="c-inputs-stacked">
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">1 star</span> </label>
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">2 star</span> </label>
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">3 star</span> </label>
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">4 star</span> </label>
                            <label class="inline custom-control custom-checkbox block">
                              <input type="checkbox" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description ml-0">5 star</span> </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </section>
                </div>
                <div class="actions clearfix">
                  <ul role="menu" aria-label="Pagination">
                    <li class="" aria-disabled="false"><a href="#previous" role="menuitem">Previous</a></li>
                    <li aria-hidden="false" aria-disabled="false" class="" style=""><a href="#next" role="menuitem">Next</a></li>
                    <li aria-hidden="true" style="display: none;"><a href="#finish" role="menuitem">Submit</a></li>
                  </ul>
                </div>
              </form>
            </div>
            <!-- /.box-body -->
          </div>





          <!-- The timeline -->
          <ul class="timeline">
            <?php $checkpoint_count = 0; ?>
            @foreach($cases_details as $index2 => $detail)

            @if ($detail->check_point != "0")

            <?php $checkpoint_count += 1; ?>
            <li id="checkpoint-label-{{$checkpoint_count }}" class="time-label" onclick="timelineCOntroller('{{ $checkpoint_count }}');">
              @if($detail->status == 1)
              <span class="bg-green">
                @elseif($detail->status == 0)
                <span class="bg-aqua">
                  @endif
                  Checkpoint {{ $checkpoint_count }}: {{ $detail->checklist_name }} &nbsp;
                  <i id="main_checklist_{{$checkpoint_count }}" class="ion ion-arrow-up-b"></i>
                </span>
            </li>
            @endif

            <li class="li-item-{{ $checkpoint_count }}">
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
                <h3 class="timeline-header no-border">
                  {{$index2 + 1 }}. <a class="a_{{$detail->role_name}}" href="#"> @if($detail->name == null) {{"system"}} @else {{$detail->name}} @endif </a> {{ $detail->checklist_name }}
                </h3>

                <div id="body_{{ $detail->id }}" class="timeline-body">
                  @if($detail->remarks != null)
                  {{$detail->remarks}}
                  @endif
                  @if($detail->need_attachment == 1 && $detail->status == 1)
                  <ul class="mailbox-attachments clearfix">
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
                  </ul>
                  @endif
                </div>
                @if($detail->name != null)
                <div class="timeline-footer text-right">
                  @if($detail->need_attachment == 1 && $detail->status == 1)
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="fileMode('{{ $detail->id }}')">Attach File</a>

                  @endif
                  @if($detail->bln_gen_doc == 1)
                  <a id="btn_action_{{ $detail->id }}" href="/document/{{ $cases[0]->id }}/{{ $cases[0]->id }}" class="btn btn-primary btn-sm">Generate Document</a>

                  @endif
                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}')">Remark</a>

                  <a id="btn_action_{{ $detail->id }}" href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="actionMode('{{ $detail->id }}')">Action</a>
                </div>

                @endif


              </div>
            </li>

            @endforeach

            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>

        </div>

        <div class="tab-pane" id="profile-1" role="tabpanel">

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

                        @if ($field->case_field_id == $category->id)

                        <div class="form-group row">
                          <label class="col-md-3 col-form-label" for="hf-email">{{$field->name}}</label>
                          <div class="col-md-9">
                            <input class="form-control" id="{{$field->id}}" type="text" name="{{$field->id}}" value="{{$field->value}}">
                          </div>
                        </div>

                        @endif


                        @endforeach


                      </form>
                    </div>
                    <div class="card-footer">
                      <!-- <button class="btn btn-sm btn-primary" type="submit"> Submit</button> -->
                      <a class="btn btn-sm btn-info float-right mr-1 d-print-none" onclick="submitMasterList('{{$category->id}}', '{{ $cases[0]->id }}')" href="javascript:void(0)">

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

        <div class="tab-pane " id="messages-1" role="tabpanel">

          <table class="table table-striped table-bordered datatable">
            <thead>
              <tr class="text-center">
                <th>No</th>
                <th> Name</th>
                <th>Amount</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              $subtotal = 0;
              ?>
              @if(count($account_template_with_cat))


              @foreach($account_template_with_cat as $index => $cat)
              <tr style="background-color:grey;color:white">
                <td colspan="5">{{ $cat['category']->category }}</td>
                <?php $total += $subtotal ?>
                <?php $subtotal = 0 ?>

              </tr>
              @foreach($cat['account_details'] as $index => $details)
              <?php $subtotal += $details->amount ?>
              <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td id="item_{{ $details->id }}">{{ $details->item_name }}</td>
                <td id="amt_{{ $details->id }}">{{ $details->amount }}</td>

                <td class="text-center">
                  <!-- <a href="{{ url('/account-template/' . $details->id . '/edit') }}" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Coupon">Coupon</a> -->
                  <a href="javascript:void(0)" onclick="voucherMode('{{ $details->id }}', '{{ $cases[0]->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Voucher</a>

                </td>
              </tr>

              @endforeach

              @if($cat['category']->taxable == "1")
              <tr>
                <td>{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
                <td style="text-align:right" colspan="4">{{ number_format((float)($subtotal*0.06), 2, '.', '') }}</td>

              </tr>
              @endif

              <tr>
                <td></td>
                <td style="text-align:right" colspan="4">{{$subtotal}}</td>

              </tr>

              @endforeach

              <tr>
                <td>Total </td>
                <td style="text-align:right" colspan="4">{{$total}}</td>

              </tr>
              @else
              <tr>
                <td class="text-center" colspan="5">No data</td>
              </tr>
              @endif

            </tbody>
          </table>
        </div>


        <div class="tab-pane " id="dispatch" role="tabpanel">
          Dispatch (WIP)
        </div>
      </div>
    </div>


    <div id="div_action" class="card" style="display:none">

      <div class="card-header">
        <h4>Action</h4>
      </div>
      <div class="card-body">
        <form id="form_action" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

              <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
              <div class="form-group row">
                <div class="col">
                  <label>Action</label>
                  <input class="form-control" type="text" value="" id="action" name="action" disabled>
                </div>
              </div>

              <div id="field_file" class="form-group row">
                <div class="col">
                  <label>File</label>
                  <input class="form-control" type="file" id="myfile" name="myfile">
                </div>
              </div>

              <div class="form-group row">
                <div class="col">
                  <label>Remarks</label>
                  <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                </div>
              </div>


              <button class="btn btn-success float-right" onclick="updateChecklist()" type="button">
                <span id="span_update">Update</span>
                <div class="overlay" style="display:none">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
              </button>
              <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
            </div>
          </div>
        </form>

      </div>
    </div>


    <div id="dFile" class="card" style="display:none">

      <div class="card-header">
        <h4>Upload file</h4>
      </div>
      <div class="card-body">
        <form id="form_action" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

              <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">

              <div id="field_file" class="form-group row">
                <div class="col">
                  <label>File</label>
                  <input class="form-control" type="file" id="case_file" name="case_file">
                </div>
              </div>

              <button class="btn btn-success float-right" onclick="uploadFile()" type="button">
                <span id="span_upload">Upload</span>
                <div class="overlay" style="display:none">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
              </button>
              <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
            </div>
          </div>
        </form>

      </div>
    </div>


    <div id="dVoucher" class="card" style="display:none">
      <div class="card-header">
        <h4>Request voucher</h4>
      </div>
      <div class="card-body">
        <form id="form_voucher" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

              <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
              <div class="form-group row">
                <div class="col">
                  <label>Item</label>
                  <input class="form-control" type="hidden" value="" id="account_details_id" name="account_details_id">
                  <input class="form-control" type="text" value="" id="item" name="item" disabled>
                </div>
              </div>

              <div class="form-group row">
                <div class="col">
                  <label>Available Amount</label>
                  <input class="form-control" type="text" value="" id="available_amt" name="available_amt" disabled>
                </div>
              </div>

              <div class="form-group row">
                <div class="col">
                  <label>Amount</label>
                  <input class="form-control" type="number" value="0" id="amt" name="amt">
                </div>
              </div>


              <button class="btn btn-success float-right" onclick="generateVoucher('{{ $cases[0]->id }}')" type="button">
                <span id="span_update">Voucher</span>
                <div class="overlay" style="display:none">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
              </button>
              <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
            </div>
          </div>
        </form>

      </div>
    </div>

    @endif

  </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/paperfish/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.bootstrap.wizard.js') }}"></script>

<script src="{{ asset('js/paperfish/paper-bootstrap-wizard.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.validate.min.js') }}"></script>
<script src="http://s.codepen.io/assets/libs/modernizr.js" type="text/javascript"></script>
<script>
  function actionMode(id) {
    $(".nav-tabs-custom").hide();
    $("#div_action").show();

    $("#action").val($("#act_" + id).val());
    $("#remarks").html($("#remark_" + id).val());
    $("#selected_id").val(id);

    if ($("#file_" + id).val() == "0") {
      $("#field_file").hide();
    } else {
      $("#field_file").show();
    }

  }

  function voucherMode(id, case_id) {

    var available_amt = $("#amt_" + id).html();
    if (available_amt == 0) {
      Swal.fire('Notice!', 'No more available amount for this item', 'error');
      return;
    }

    $(".nav-tabs-custom").hide();
    $("#dVoucher").show();

    $("#item").val($("#item_" + id).html());
    $("#account_details_id").val(id);
    $("#available_amt").val($("#amt_" + id).html());
    $("#amt").val(0);

  }

  function fileMode() {
    $(".nav-tabs-custom").hide();
    $("#dFile").show();
  }

  function generateVoucher(case_id) {
    var voucher_amt = parseFloat($("#amt").val());
    var available_amt = parseFloat($("#available_amt").val());

    if (voucher_amt == 0) {
      return;
    }

    if (voucher_amt > available_amt) {
      Swal.fire('Notice!', 'The available amount not sufficient: RM ' + available_amt, 'error');
      return;
    }


    // $("#span_update_" + case_id).hide();
    // $(".overlay_" + case_id).show();

    $.ajax({
      type: 'POST',
      url: '/request_voucher/' + case_id,
      data: $('#form_voucher').serialize(),
      success: function(data) {
        console.log(data);
        if (data.status == 1) {
          Swal.fire(
            'Success!',
            data.message,
            'success'
          )

          location.reload();
        }

      }
    });


  }

  function timelineCOntroller(timelineID) {
    var isExpanded = document.getElementsByClassName('#main_checklist_' + timelineID + ' ion-arrow-up-b');
    var div = document.querySelector('#main_checklist_' + timelineID);

    var isExpanded = div.classList.contains('ion-arrow-up-b');

    if (isExpanded == true) {
      $("#main_checklist_" + timelineID).removeClass('ion-arrow-up-b');
      $("#main_checklist_" + timelineID).addClass('ion-arrow-down-b');
      $(".li-item-" + timelineID).slideUp('fast');
    } else {
      $("#main_checklist_" + timelineID).removeClass('ion-arrow-down-b');
      $("#main_checklist_" + timelineID).addClass('ion-arrow-up-b');
      $(".li-item-" + timelineID).slideDown('fast');
    }
  }

  function viewMode() {
    // $(".nav-tabs-custom").show();
    // $("#div_action").hide();
    $(".nav-tabs-custom").show();
    $("#div_action").hide();
    $("#dVoucher").hide();
  }

  function updateChecklist($id) {
    $("#span_update").hide();
    $(".overlay").show();
    // var form = $('form_action')[0];
    var formData = new FormData();

    var files = $('#myfile')[0].files;
    console.log(files[0]);
    formData.append('myFile', files[0]);
    formData.append('_token', '<?php echo csrf_token() ?>');

    // $.ajaxSetup({
    //   headers: {
    //     'X-CSRF-TOKEN': $('<?php echo csrf_token() ?>').attr('content')
    //   }
    // });

    $.ajax({
      type: 'POST',
      url: '/update_checklist',
      data: $('#form_action').serialize(),
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {
        console.log(data);
        if (data.status == 1) {
          $("#body_" + $("#selected_id").val()).append($("#remarks").val());
          $("#btn_action_" + $("#selected_id").val()).hide();

          $("#checklist_" + $("#selected_id").val()).removeClass("ion-person bg-aqua");
          $("#checklist_" + $("#selected_id").val()).addClass(" ion-checkmark bg-done ");

          $("#date_" + $("#selected_id").val()).html(data.data);

          $("#remarks").val('');

          $("#span_update").show();
          $(".overlay").hide();

          viewMode();
        }

        Swal.fire(
          'Success!',
          'Checklist Updated',
          'success'
        )

      }
    });

  }

  function uploadFile() {
    $("#span_update").hide();
    $(".overlay").show();

    var formData = new FormData();

    var files = $('#case_file')[0].files;
    console.log(files[0]);
    formData.append('case_file', files[0]);
    // formData.append('_token','<?php echo csrf_token() ?>');

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      type: 'POST',
      url: '/upload_file',
      // data: $('#form_action').serialize(),
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {
        console.log(data);
        if (data.status == 1) {
          $("#body_" + $("#selected_id").val()).append($("#remarks").val());
          $("#btn_action_" + $("#selected_id").val()).hide();

          $("#checklist_" + $("#selected_id").val()).removeClass("ion-person bg-aqua");
          $("#checklist_" + $("#selected_id").val()).addClass(" ion-checkmark bg-done ");

          $("#date_" + $("#selected_id").val()).html(data.data);

          $("#remarks").val('');

          $("#span_update").show();
          $(".overlay").hide();

          viewMode();
        }

        Swal.fire(
          'Success!',
          'Checklist Updated',
          'success'
        )

      }
    });
  }

  function submitMasterList(cat_id, case_id) {

    $("#span_update_" + cat_id).hide();
    $(".overlay_" + cat_id).show();
    var form = $('#form_master_' + cat_id).serialize();
    // form.append("case_id", case_id);
    console.log(form);

    $.ajax({
      type: 'POST',
      url: '/update_masterlist/' + case_id,
      data: form,
      success: function(data) {
        console.log(data);

        $("#span_update_" + cat_id).show();
        $(".overlay_" + cat_id).hide();

        if (data.status == 1) {
          Swal.fire('Success!', 'Masterlist Updated', 'success');
        } else {
          Swal.fire('Notice!', data.message, 'error');
        }
        // if (data.status == 1) {
        //   $("#body_" + $("#selected_id").val()).append($("#remarks").val());
        //   $("#btn_action_" + $("#selected_id").val()).hide();

        //   $("#checklist_" + $("#selected_id").val()).removeClass("ion-person bg-aqua");
        //   $("#checklist_" + $("#selected_id").val()).addClass(" ion-checkmark bg-done ");

        //   $("#date_" + $("#selected_id").val()).html(data.data);

        //   $("#remarks").val('');

        //   $("#span_update").show();
        //   $(".overlay").hide();

        //   viewMode();
        // }

        // Swal.fire(
        //   'Success!',
        //   'Checklist Updated',
        //   'success'
        // )

      }
    });
  }






  $(".tab-wizard").steps({
    headerTag: "h6",
    bodyTag: "section",
    transitionEffect: "none",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
      finish: "Submit"
    },
    onFinished: function(event, currentIndex) {
      swal("Your Form Submitted!", "Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem. Proin ut ligula vel nunc egestas porttitor.");

    }
  });


  var form = $(".validation-wizard").show();

  function stepIN() {

  }

  $(".validation-wizard").steps({
    headerTag: "h6",
    bodyTag: "section",
    transitionEffect: "none",
    titleTemplate: '<span class="step">#index#</span> #title#',
    labels: {
      finish: "Submit"
    },
    onStepChanging: function(event, currentIndex, newIndex) {
      return currentIndex > newIndex || !(3 === newIndex && Number($("#age-2").val()) < 18) && (currentIndex < newIndex && (form.find(".body:eq(" + newIndex + ") label.error").remove(), form.find(".body:eq(" + newIndex + ") .error").removeClass("error")), form.validate().settings.ignore = ":disabled,:hidden", form.valid())
    },
    onFinishing: function(event, currentIndex) {
      return form.validate().settings.ignore = ":disabled", form.valid()
    },
    onFinished: function(event, currentIndex) {
      swal("Your Form Submitted!", "Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem. Proin ut ligula vel nunc egestas porttitor.");
    }
  }), $(".validation-wizard").validate({
    ignore: "input[type=hidden]",
    errorClass: "text-danger",
    successClass: "text-success",
    highlight: function(element, errorClass) {
      $(element).removeClass(errorClass)
    },
    unhighlight: function(element, errorClass) {
      $(element).removeClass(errorClass)
    },
    errorPlacement: function(error, element) {
      error.insertAfter(element)
    },
    rules: {
      email: {
        email: !0
      }
    }
  })
</script>

@endsection