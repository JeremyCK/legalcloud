@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet">

<link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
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
<link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet">
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
                  <td>{{ $case->case_ref_no }}</td>
                </tr>
                <!-- <tr>
                  <th>Case Type</th>
                  <td>{{ $case->case_ref_no }}</td>
                </tr> -->
                <tr>
                  <th>Percentage Completion</th>
                  <td>{{ $case->percentage }} %
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 40%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>Start Date</th>
                  <td>{{ $case->created_at }}  </td>
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
                    <div>{{ $case->lawyer }}</div>
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
                    <div>{{ $case->clerk }}</div>
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
                    <div>{{ $case->sales }}</div>
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

    <div class="row">
      <div class="col-sm-12 text-center">



        <a class="btn btn-primary nav-link " data-toggle="tab" href="#home" role="tab" href="javascript:void(0)">Progress</a>
        <a class="btn btn-primary nav-link" data-toggle="tab" href="#profile" role="tab" href="javascript:void(0)">Master List</a>
        <a class="btn btn-primary nav-link" data-toggle="tab" href="#notes" role="tab" href="javascript:void(0)">Notes</a>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12 ">

        <div class="nav-tabs-boxed">

          <div class="tab-content bg-color-none">
            <div class="tab-pane active" id="home" role="tabpanel">


              <div class="row">
                <div class="col-sm-12">

                  @if(count($cases_details) > 0)
                  <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizard">
                      <form action="" method="">


                        <div class="wizard-header">
                          <h3 class="wizard-title">Progress</h3>

                        </div>
                        <div class="wizard-navigation">
                          <div class="progress-with-circle">
                            <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="4" style="width: 15%;"></div>
                          </div>
                          <ul>
                            @foreach($loanCaseDetailsCount as $index => $count)

                            <li>
                              <a href="#tab_{{$index+1}}" data-toggle="tab">
                                <div class="icon-circle done">

                                  <span class="span-checkpoint">{{$count->check_point}}</span>
                                </div>
                                {{ $count->checklist_name }}
                              </a>
                            </li>
                            @endforeach

                          </ul>
                        </div>
                        <div class="tab-content bg-color-none" style="margin-top: 50px;">

                          <?php $last_no = 0; ?>
                          @foreach($loanCaseDetailsCount as $index => $count)

                          <div class="tab-pane" id="tab_{{$index+1}}">
                            <div class="row">
                              <div class="col-sm-12">
                                <table class="table table-striped table-bordered datatable">
                                  <thead>
                                    <tr>
                                      <th>No </th>
                                      <th>Process </th>
                                      <th>Target Date</th>
                                      <th>Completion Date</th>
                                      <th>Attachment</th>
                                      <th>Status</th>

                                      <th>Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($cases_details as $index2 => $detail)
                                    @if ($detail->process_number <= $count->process_number && $detail->process_number > $last_no)

                                      <tr>
                                        <td>{{ $detail->process_number }}</td>
                                        <td> {{ $detail->checklist_name }} </td>
                                        <td>{{date('d-m-Y', strtotime($detail->target_date)) }}</td>
                                        <td>{{date('d-m-Y', strtotime($detail->target_date)) }}</td>
                                        <td>
                                          @if($detail->need_attachment == 1)

                                          <input id="file-input" type="file" name="file-input">

                                          @elseif($detail->need_attachment == 2)
                                          <a href="#" >File Name</a>
                                          <!-- <ul class="mailbox-attachments clearfix">
                                            <li>
                                              <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>

                                              <div class="mailbox-attachment-info">
                                                <a href="#" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> Mag.pdf</a>
                                                <span class="mailbox-attachment-size">
                                                  5,215 KB
                                                  <a href="#" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                                </span>
                                              </div>
                                            </li>
                                          </ul> -->
                                          @endif
                                        </td>


                                        <td>

                                          @if($detail->status == 1)
                                          <span class="badge badge-success">Done</span>
                                          @elseif($detail->status == 0)
                                          <span class="badge badge-dark">Pending</span>
                                          @elseif($detail->status == 2)
                                          <span class="badge badge-warning">Done with Overdue</span>
                                          @endif
                                        </td>

                                        <td>
                                          <a href="{{ route('todolist.show', $detail->id ) }}" class="btn btn-primary">Update</a>
                                        </td>
                                      </tr>

                                      @endif
                                      @endforeach

                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>

                          <?php $last_no = $count->process_number; ?>
                          @endforeach
                        </div>
                        <div class="wizard-footer">
                          <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                            <input type='button' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Finish' />
                          </div>

                          <div class="pull-left">
                            <input type='button' class='btn btn-previous btn-default btn-wd' name='previous' value='Previous' />
                          </div>
                          <div class="clearfix"></div>
                        </div>
                      </form>
                    </div>
                  </div>

                  @else

                  @if($current_user->menuroles == 'lawyer')

                  <div class="card">
                    <div class="card-header">
                      <h4>Accept New Case</h4>
                    </div>
                    <div class="card-body">
                      @if(Session::has('message'))
                      <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                      @endif

                    </div>
                  </div>
                  @endif


                  @endif
                </div>


              </div>


            </div>

            <div class="tab-pane " id="profile" role="tabpanel">


              <div class="row">

                <div class="col-sm-4">
                  <div class="card" style="max-height: 50%">

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

                              <!-- <a class="list-group-item" data-toggle="tab" href="#category-1" aria-controls="category-1" role="tab" aria-expanded="false">General</a>
                              <a class="list-group-item active" data-toggle="tab" href="#category-2" aria-controls="category-2" role="tab" aria-expanded="true">Payment</a>
                              <a class="list-group-item" data-toggle="tab" href="#category-3" aria-controls="category-3" role="tab" aria-expanded="false">Offers</a>
                              <a class="list-group-item" data-toggle="tab" href="#category-4" aria-controls="category-4" role="tab" aria-expanded="false">Security and Privacy</a> -->
                            </div>
                          </div>
                        </div>
                        <!-- End Panel -->
                      </div>
                      <!-- /.box-body -->
                    </div>

                    <!-- <div class="navi navi-bold navi-hover navi-active navi-link-rounded" style="overflow-x:overlay">

                      @foreach($caseMasterListCategory as $index => $category)


                      <div class="navi-item mb-2">
                        <a data-toggle="tab" href="#tab_{{$category->code}}" role="tab" href="javascript:void(0)" class="navi-link py-4 {{ $index == 0 ? 'active' : '' }}  ">
                          <span class="navi-icon mr-2">
                            <span class="cil-info">
                            </span>
                          </span>
                          <span class="navi-text font-size-lg">{{$category->name}}</span>
                        </a>
                      </div>
                      @endforeach

                    </div> -->

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
                          <form class="form-horizontal" action="" method="post">


                            @foreach($caseMasterListField as $index => $field)

                            @if ($field->case_field_id == $category->id)

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="hf-email">{{$field->name}}</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_name" type="text" name="purchaser_name">
                              </div>
                            </div>

                            @endif


                            @endforeach


                          </form>
                        </div>
                        <div class="card-footer">
                          <!-- <button class="btn btn-sm btn-primary" type="submit"> Submit</button> -->
                          <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#">
                            <svg class="c-icon">
                              <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                            </svg> Save</a>
                          <!-- <button class="btn btn-sm btn-danger" type="reset"> Reset</button> -->
                        </div>
                      </div>

                    </div>
                    @endforeach

                    <!-- <div class="tab-pane active" id="tab_1" role="tabpanel">

                      <div class="card">
                        <div class="card-header">
                          <h4>Purchaser</h4>
                        </div>
                        <div class="card-body">
                          <form class="form-horizontal" action="" method="post">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_name" type="text" name="purchaser_name">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchaser_nric">NRIC</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_nric" type="text" name="purchaser_nric">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchaser_address">Address</label>
                              <div class="col-md-9">
                                <textarea class="form-control" id="purchaser_address" name="purchaser_address" rows="4"></textarea>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchaser_hp">Mobile Number</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_hp" type="text" name="purchaser_hp">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchaser_tel">Tel</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_tel" type="text" name="purchaser_tel">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchaser_fax">Fax</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_fax" type="text" name="purchaser_fax">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchaser_fax">Email</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchaser_email" type="email" name="purchaser_email">
                              </div>
                            </div>
                          </form>
                        </div>
                        <div class="card-footer">
                          <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                          <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                      </div>

                    </div>

                    <div class="tab-pane " id="tab_2" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <h4>Vendor</h4>
                        </div>
                        <div class="card-body">
                          <form class="form-horizontal" action="" method="post">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_name" type="text" name="vendor_name">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_nric">NRIC</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_nric" type="text" name="vendor_nric">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_address">Address</label>
                              <div class="col-md-9">
                                <textarea class="form-control" id="vendor_address" name="vendor_address" rows="4"></textarea>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_hp">Mobile Number</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_hp" type="text" name="vendor_hp">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_tel">Tel</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_tel" type="text" name="vendor_tel">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_fax">Fax</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_fax" type="text" name="vendor_fax">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_email">Email</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_email" type="email" name="vendor_email">
                              </div>
                            </div>
                          </form>
                        </div>
                        <div class="card-footer">
                          <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                          <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane " id="tab_3" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <h4>Property</h4>
                        </div>
                        <div class="card-body">
                          <form class="form-horizontal" action="" method="post">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="property_title">Title</label>
                              <div class="col-md-9">
                                <input class="form-control" id="property_title" type="text" name="property_title">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="property_address">Address</label>
                              <div class="col-md-9">
                                <textarea class="form-control" id="property_address" name="property_address" rows="4"></textarea>
                              </div>
                            </div>

                          </form>
                        </div>
                        <div class="card-footer">
                          <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                          <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane " id="tab_4" role="tabpanel">

                      <div class="card">
                        <div class="card-header">
                          <h4>Vendor Financier</h4>
                        </div>
                        <div class="card-body">
                          <form class="form-horizontal" action="" method="post">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_name">Name</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_financier_name" type="text" name="vendor_financier_name">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_nric">NRIC</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_financier_nric" type="text" name="vendor_financier_nric">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_address">Address</label>
                              <div class="col-md-9">
                                <textarea class="form-control" id="vendor_financier_address" name="vendor_financier_address" rows="4"></textarea>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_hp">Mobile Number</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_financier_hp" type="text" name="vendor_financier_hp">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_tel">Tel</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_financier_tel" type="text" name="vendor_financier_tel">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_fax">Fax</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_financier_fax" type="text" name="vendor_financier_fax">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_financier_fax">Email</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_financier_email" type="email" name="vendor_financier_email">
                              </div>
                            </div>
                          </form>
                        </div>
                        <div class="card-footer">
                          <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                          <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                      </div>

                    </div>

                    <div class="tab-pane " id="tab_5" role="tabpanel">

                      <div class="card">
                        <div class="card-header">
                          <h4>Purchaser Financier</h4>
                        </div>
                        <div class="card-body">
                          <form class="form-horizontal" action="" method="post">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_name">Name</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_name" type="text" name="purchase_financier_name">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_nric">NRIC</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_nric" type="text" name="purchase_financier_nric">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_address">Address</label>
                              <div class="col-md-9">
                                <textarea class="form-control" id="purchase_financier_address" name="purchase_financier_address" rows="4"></textarea>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_hp">Mobile Number</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_hp" type="text" name="purchase_financier_hp">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_tel">Tel</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_tel" type="text" name="purchase_financier_tel">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_fax">Fax</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_fax" type="text" name="purchase_financier_fax">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_fax">Email</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_email" type="email" name="purchase_financier_email">
                              </div>
                            </div>
                          </form>
                        </div>
                        <div class="card-footer">
                          <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                          <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                      </div>

                    </div>

                    <div class="tab-pane " id="tab_6" role="tabpanel">

                      <div class="card">
                        <div class="card-header">
                          <h4>Vendor Solicitors</h4>
                        </div>
                        <div class="card-body">
                          <form class="form-horizontal" action="" method="post">
                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="vendor_solicitors_name">Name</label>
                              <div class="col-md-9">
                                <input class="form-control" id="vendor_solicitors_name" type="text" name="vendor_solicitors_name">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_nric">NRIC</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_nric" type="text" name="purchase_financier_nric">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_address">Address</label>
                              <div class="col-md-9">
                                <textarea class="form-control" id="purchase_financier_address" name="purchase_financier_address" rows="4"></textarea>
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_hp">Mobile Number</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_hp" type="text" name="purchase_financier_hp">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_tel">Tel</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_tel" type="text" name="purchase_financier_tel">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_fax">Fax</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_fax" type="text" name="purchase_financier_fax">
                              </div>
                            </div>

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="purchase_financier_fax">Email</label>
                              <div class="col-md-9">
                                <input class="form-control" id="purchase_financier_email" type="email" name="purchase_financier_email">
                              </div>
                            </div>
                          </form>
                        </div>
                        <div class="card-footer">
                          <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                          <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                      </div>

                    </div> -->

                  </div>


                </div>

              </div>


            </div>
            <div class="tab-pane" id="notes" role="tabpanel">
              <div class="row">

                @foreach($cases_notes as $index => $note)

                <div class="col-sm-4">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title">{{$note->label}}</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      {{$note->notes}}
                    </div>
                    <!-- /.box-body -->
                    <!-- <div class="box-footer text-center">
                    <a href="javascript:void(0)" class="uppercase">View All Products</a>
                  </div> -->
                    <!-- /.box-footer -->
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

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/paperfish/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.bootstrap.wizard.js') }}"></script>

<script src="{{ asset('js/paperfish/paper-bootstrap-wizard.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<script src="http://s.codepen.io/assets/libs/modernizr.js" type="text/javascript"></script>

@endsection