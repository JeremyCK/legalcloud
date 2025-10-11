@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet">

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

<style>
    /*!
 * jQuery SmartWizard v5
 * jQuery Wizard Plugin
 * http://www.techlaboratory.net/smartwizard
 *
 * Created by Dipu Raj
 * http://dipu.me
 *
 * Licensed under the terms of MIT License
 * https://github.com/techlab/jquery-smartwizard/blob/master/LICENSE
 */.sw{position:relative}.sw *,.sw ::after,.sw ::before{box-sizing:border-box}.sw>.tab-content{position:relative;overflow:hidden}.sw .toolbar{padding:.8rem}.sw .toolbar>.btn{display:inline-block;text-decoration:none;text-align:center;text-transform:none;vertical-align:middle;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;margin-left:.2rem;margin-right:.2rem;cursor:pointer}.sw .toolbar>.btn.disabled,.sw .toolbar>.btn:disabled{opacity:.65}.sw>.nav{display:flex;flex-wrap:wrap;list-style:none;padding-left:0;margin-top:0;margin-bottom:0}@media screen and (max-width:640px){.sw>.nav{flex-direction:column!important;flex:1 auto}}.sw>.nav .nav-link{display:block;padding:.5rem 1rem;text-decoration:none}.sw>.nav .nav-link:active,.sw>.nav .nav-link:focus,.sw>.nav .nav-link:hover{text-decoration:none}.sw>.nav .nav-link::-moz-focus-inner{border:0!important}.sw>.nav .nav-link.disabled{color:#ccc!important;pointer-events:none;cursor:default}.sw>.nav .nav-link.hidden{display:none!important}.sw.sw-justified>.nav .nav-link,.sw.sw-justified>.nav>li{flex-basis:0;flex-grow:1;text-align:center}.sw.sw-dark{color:rgba(255,255,255,.95);background:#000}.sw.sw-loading{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.sw.sw-loading::after{content:"";display:block;position:absolute;opacity:1;top:0;left:0;height:100%;width:100%;background:rgba(255,255,255,.7);z-index:2;transition:all .2s ease}.sw.sw-loading::before{content:'';display:inline-block;position:absolute;top:45%;left:45%;width:2rem;height:2rem;border:10px solid #f3f3f3;border-top:10px solid #3498db;border-radius:50%;z-index:10;-webkit-animation:spin 1s linear infinite;animation:spin 1s linear infinite}@-webkit-keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.sw-theme-default{border:1px solid #eee}.sw-theme-default>.tab-content>.tab-pane{padding:10px}.sw-theme-default .toolbar>.btn{color:#fff;background-color:#17a2b8;border:1px solid #17a2b8;padding:.375rem .75rem;border-radius:.25rem;font-weight:400}.sw-theme-default>.nav{box-shadow:0 .125rem .25rem rgba(0,0,0,.1)!important}.sw-theme-default>.nav .nav-link{position:relative;height:100%;min-height:100%}.sw-theme-default>.nav .nav-link::after{content:"";position:absolute;height:2px;width:0;left:0;bottom:-1px;background:#999;transition:all .35s ease .15s}.sw-theme-default>.nav .nav-link.inactive{color:#999;cursor:not-allowed}.sw-theme-default>.nav .nav-link.active{color:#17a2b8!important;cursor:pointer}.sw-theme-default>.nav .nav-link.active::after{background:#17a2b8!important;width:100%}.sw-theme-default>.nav .nav-link.done{color:#5cb85c!important;cursor:pointer}.sw-theme-default>.nav .nav-link.done::after{background:#5cb85c;width:100%}.sw-theme-default>.nav .nav-link.disabled{color:#ddd!important;cursor:not-allowed}.sw-theme-default>.nav .nav-link.disabled::after{background:#ddd;width:100%}.sw-theme-default>.nav .nav-link.danger{color:#d9534f!important;cursor:pointer}.sw-theme-default>.nav .nav-link.danger::after{background:#d9534f;width:100%}.sw-theme-arrows{border:1px solid #eee}.sw-theme-arrows>.tab-content>.tab-pane{padding:10px}.sw-theme-arrows .toolbar>.btn{color:#fff;background-color:#17a2b8;border:1px solid #17a2b8;padding:.375rem .75rem;border-radius:.25rem;font-weight:400}.sw-theme-arrows>.nav{overflow:hidden;border-bottom:1px solid #eee}.sw-theme-arrows>.nav .nav-link{position:relative;height:100%;min-height:100%;margin-right:30px;margin-left:-30px;padding-left:40px}@media screen and (max-width:640px){.sw-theme-arrows>.nav .nav-link{overflow:hidden;margin-bottom:1px;margin-right:unset}}.sw-theme-arrows>.nav .nav-link::after{content:"";position:absolute;display:block;width:0;height:0;top:50%;left:100%;margin-top:-50px;border-top:50px solid transparent;border-bottom:50px solid transparent;border-left:30px solid #f8f8f8;z-index:2}.sw-theme-arrows>.nav .nav-link::before{content:" ";position:absolute;display:block;width:0;height:0;top:50%;left:100%;margin-top:-50px;margin-left:1px;border-top:50px solid transparent;border-bottom:50px solid transparent;border-left:30px solid #eee;z-index:1}.sw-theme-arrows>.nav .nav-link.inactive{color:#999;border-color:#f8f8f8;background:#f8f8f8;cursor:not-allowed}.sw-theme-arrows>.nav .nav-link.active{color:#fff;border-color:#5bc0de;background:#5bc0de;cursor:pointer}.sw-theme-arrows>.nav .nav-link.active::after{border-left-color:#5bc0de}.sw-theme-arrows>.nav .nav-link.done{color:#fff;border-color:#5cb85c;background:#5cb85c;cursor:pointer}.sw-theme-arrows>.nav .nav-link.done::after{border-left-color:#5cb85c}.sw-theme-arrows>.nav .nav-link.disabled{color:#eee;border-color:#f9f9f9;background:#f9f9f9;cursor:not-allowed}.sw-theme-arrows>.nav .nav-link.disabled::after{border-left-color:#f9f9f9}.sw-theme-arrows>.nav .nav-link.danger{color:#fff;border-color:#d9534f;background:#d9534f;cursor:pointer}.sw-theme-arrows>.nav .nav-link.danger::after{border-left-color:#d9534f}.sw-theme-arrows.sw-dark{color:rgba(255,255,255,.95);background:#000}.sw-theme-arrows.sw-dark>.nav{border-bottom:1px solid #555}.sw-theme-arrows.sw-dark>.nav .nav-link::after{border-left:30px solid #5f5f5f}.sw-theme-arrows.sw-dark>.nav .nav-link::before{border-left:30px solid #555}.sw-theme-arrows.sw-dark>.nav .nav-link.inactive{color:#fff;border-color:#5f5f5f;background:#5f5f5f}.sw-theme-arrows.sw-dark>.nav .nav-link.inactive::after{border-left-color:#5f5f5f}.sw-theme-arrows.sw-dark>.nav .nav-link.active{color:#fff;border-color:#010506;background:#0a2730}.sw-theme-arrows.sw-dark>.nav .nav-link.active::after{border-left-color:#0a2730}.sw-theme-arrows.sw-dark>.nav .nav-link.done{color:#fff;border-color:#000;background:#000}.sw-theme-arrows.sw-dark>.nav .nav-link.done::after{border-left-color:#000}.sw-theme-arrows.sw-dark>.nav .nav-link.disabled{color:#555!important;border-color:#f9f9f9;background:#474747}.sw-theme-arrows.sw-dark>.nav .nav-link.disabled::after{border-left-color:#474747}.sw-theme-arrows.sw-dark>.nav .nav-link.danger{color:#fff;border-color:#d9534f;background:#d9534f}.sw-theme-arrows.sw-dark>.nav .nav-link.danger::after{border-left-color:#d9534f}.sw-theme-dots>.tab-content>.tab-pane{padding:10px}.sw-theme-dots .toolbar>.btn{color:#fff;background-color:#17a2b8;border:1px solid #17a2b8;padding:.375rem .75rem;border-radius:.25rem;font-weight:400}.sw-theme-dots>.nav{position:relative;margin-bottom:10px}.sw-theme-dots>.nav::before{content:" ";position:absolute;top:18px;left:0;width:100%;height:5px;background-color:#eee;border-radius:3px;z-index:1}.sw-theme-dots>.nav .nav-link{position:relative;margin-top:40px}.sw-theme-dots>.nav .nav-link::before{content:" ";position:absolute;display:block;top:-36px;left:0;right:0;margin-left:auto;margin-right:auto;width:32px;height:32px;border-radius:50%;border:none;background:#f5f5f5;color:#428bca;text-decoration:none;z-index:98}.sw-theme-dots>.nav .nav-link::after{content:" ";position:absolute;display:block;top:-28px;left:0;right:0;margin-left:auto;margin-right:auto;width:16px;height:16px;border-radius:50%;z-index:99}.sw-theme-dots>.nav .nav-link.inactive{color:#999;cursor:not-allowed}.sw-theme-dots>.nav .nav-link.inactive::after{background-color:#999}.sw-theme-dots>.nav .nav-link.active{color:#5bc0de!important;cursor:pointer}.sw-theme-dots>.nav .nav-link.active::after{background-color:#5bc0de!important}.sw-theme-dots>.nav .nav-link.done{color:#5cb85c;cursor:pointer}.sw-theme-dots>.nav .nav-link.done::after{background-color:#5cb85c}.sw-theme-dots>.nav .nav-link.disabled{color:#f9f9f9;cursor:not-allowed}.sw-theme-dots>.nav .nav-link.disabled::after{background-color:#f9f9f9}.sw-theme-dots>.nav .nav-link.danger{color:#d9534f;cursor:pointer}.sw-theme-dots>.nav .nav-link.danger::after{background-color:#d9534f}.sw-theme-dots.sw-dark{color:rgba(255,255,255,.95);background:#000}.sw-theme-dots.sw-dark>.nav::before{background-color:#3c3c3c}.sw-theme-dots.sw-dark>.nav .nav-link::before{background:#434343;color:#000}.sw-theme-progress{border:1px solid #eee}.sw-theme-progress>.tab-content>.tab-pane{padding:10px}.sw-theme-progress .toolbar>.btn{color:#fff;background-color:#17a2b8;border:1px solid #17a2b8;padding:.375rem .75rem;border-radius:.25rem;font-weight:400}.sw-theme-progress>.nav{box-shadow:0 .125rem .25rem rgba(0,0,0,.1)!important}.sw-theme-progress>.nav .nav-link{position:relative;height:100%;min-height:100%;background:0 0;overflow:hidden;z-index:2}.sw-theme-progress>.nav .nav-link::after{content:"";position:absolute;height:150%;width:0;left:0;top:0;background:#fff;z-index:-1;transition:all .35s ease .1s}.sw-theme-progress>.nav .nav-link.inactive{color:#999;cursor:not-allowed}.sw-theme-progress>.nav .nav-link.active{color:#fff!important;cursor:pointer}.sw-theme-progress>.nav .nav-link.active::after{background-color:#5cb85c;width:100%}.sw-theme-progress>.nav .nav-link.done{color:#fff!important;cursor:pointer}.sw-theme-progress>.nav .nav-link.done::after{background:#5cb85c;width:100%}.sw-theme-progress>.nav .nav-link.disabled{color:#ddd!important;cursor:not-allowed}.sw-theme-progress>.nav .nav-link.disabled::after{background:#f9f9f9;width:100%}.sw-theme-progress>.nav .nav-link.danger{color:#fff!important;cursor:pointer}.sw-theme-progress>.nav .nav-link.danger::after{background:#d9534f;width:100%}.sw-theme-progress.sw-dark{color:rgba(255,255,255,.95)}.sw-theme-progress.sw-dark>.nav .nav-link.active{color:#fff}.sw-theme-progress.sw-dark>.nav .nav-link.active::after{background-color:#333}.sw-theme-progress.sw-dark>.nav .nav-link.done{color:#fff!important}.sw-theme-progress.sw-dark>.nav .nav-link.done::after{background:#333}.sw-theme-progress.sw-dark>.nav .nav-link.disabled{color:#2b2b2b!important}.sw-theme-progress.sw-dark>.nav .nav-link.disabled::after{background:#474747}.sw-theme-progress.sw-dark>.nav .nav-link.danger{color:#fff!important}.sw-theme-progress.sw-dark>.nav .nav-link.danger::after{background:#d9534f}
</style>
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
            <table class="table table-bordered datatable">
              <tbody>
                <tr>
                  <th>Case Ref Number</th>
                  <td>{{ $cases[0]->case_ref_no }}</td>
                </tr>
                <tr>
                  <th>Case Type</th>
                  <td>{{ $cases[0]->case_ref_no }}</td>
                </tr>
                <tr>
                  <th>Percentage Completion</th>
                  <td>{{ $cases[0]->percentage }} %
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>Date</th>
                  <td>{{ date('Y-m-d H:i:s') }} </td>
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
                    <div class="c-avatar"><img class="c-avatar-img" src="../assets/img/avatars/1.jpg" alt="user@email.com"><span class="c-avatar-status bg-success"></span></div>
                  </td>
                  <td>
                    <div>{{ $cases[0]->lawyer }}</div>
                    <div class="small text-muted"><span>Lawyer</span></div>
                  </td>
                  <td class="text-center"><i class="flag-icon flag-icon-us c-icon-xl" id="us" title="us"></i></td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>50%</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
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
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>10 {{ __('dashboard.time.sec_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <div class="c-avatar"><img class="c-avatar-img" src="../assets/img/avatars/1.jpg" alt="user@email.com"><span class="c-avatar-status bg-danger"></span></div>
                  </td>
                  <td>
                    <div>{{ $cases[0]->clerk }}</div>
                    <div class="small text-muted">Clerk</div>
                  </td>
                  <td class="text-center"><i class="flag-icon flag-icon-br c-icon-xl" id="br" title="br"></i></td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>10%</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
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
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>5 {{ __('dashboard.time.minutes_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <div class="c-avatar"><img class="c-avatar-img" src="../assets/img/avatars/1.jpg" alt="user@email.com"><span class="c-avatar-status bg-danger"></span></div>
                  </td>
                  <td>
                    <div>{{ $cases[0]->sales }}</div>
                    <div class="small text-muted">Sales</div>
                  </td>
                  <td class="text-center"><i class="flag-icon flag-icon-br c-icon-xl" id="br" title="br"></i></td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>10%</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
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
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>5 {{ __('dashboard.time.minutes_ago') }}</strong>
                  </td>
                </tr>


              </tbody>
            </table>
          </div>
        </div>

      </div>


    </div>

    <div class="row">
      <div class="col-sm-12 text-center">
        <div class="row ">
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-aqua"><i class="ion ion-stats-bars"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Progress</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-green"><i class="ion ion-thumbsup"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Master List</span>
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
              <span class="info-box-icon bg-green"><i class="ion ion-thumbsup"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Notes</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        </div>
        <a class="btn btn-primary nav-link " data-toggle="tab" href="#home" role="tab" href="javascript:void(0)">Progress</a>
        <a class="btn btn-primary nav-link" data-toggle="tab" href="#profile" role="tab" href="javascript:void(0)">Master List</a>
        <a class="btn btn-primary nav-link" data-toggle="tab" href="#notes" role="tab" href="javascript:void(0)">Notes</a>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12 ">



        <div class="nav-tabs-boxed">
          <!-- <a class="btn btn-block btn-outline-primary" class="nav-link" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Primary</button> -->

          <!-- <ul class="nav nav-tabs" role="tablist"> -->
          <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Home</a></li> -->
          <!-- <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profile</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Messages</a></li> -->
          <!-- </ul> -->
          <div class="tab-content bg-color-none">
            <div class="tab-pane active" id="home" role="tabpanel">


              <div class="row">





              
                <div class="col-sm-12">
                  

                  @if(count($cases_details) > 0)
                  <div class="wizard-container">


                  <div class="card-body smartwizard-dots sw sw-theme-dots sw-justified">
<ul class="nav">
<li class="nav-item"><a class="nav-link inactive active" href="#step-1"><strong>Step 1</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive done" href="#step-2"><strong>Step 2</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-3"><strong>Step 3</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-4"><strong>Step 4</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-4"><strong>Step 4</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-4"><strong>Step 4</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-4"><strong>Step 4</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-4"><strong>Step 4</strong> <br>This is dots Wizard</a></li>
<li class="nav-item"><a class="nav-link inactive" href="#step-4"><strong>Step 4</strong> <br>This is dots Wizard</a></li>
</ul>
<div class="tab-content" style="height: 171.333px;">
<div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1" style="display: block;">
<h5>Step 1 Content</h5>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
</div>
<div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2" style="display: none;">
<h5>Step 2 Content</h5>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
</div>
<div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3" style="display: none;">
<h5>Step 3 Content</h5>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
</div>
<div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4" style="display: none;">
<h5>Step 4 Content</h5>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,</p>
</div>
</div><div class="toolbar toolbar-bottom" role="toolbar" style="text-align: right;"><button class="btn sw-btn-prev disabled" type="button">Previous</button><button class="btn sw-btn-next" type="button">Next</button><button class="btn btn-info">Finish</button><button class="btn btn-danger">Cancel</button></div>
</div>

                    <div class="card wizard-card" data-color="red" id="wizard">
                      <form action="" method="">
                        <!--        You can switch " data-color="green" "  with one of the next bright colors: "blue", "azure", "orange", "red"       -->

                        <div class="wizard-header">
                          <h3 class="wizard-title">Progress</h3>
                          <!-- <p class="category">This information will let us know more about your place.</p> -->
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
                                  <!-- <i class="ti-map"></i> -->
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
                                      <!-- <th>Check Point</th> -->
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
                                        <!-- <td> {{ $detail->check_point }}</td> -->
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
                  </div> <!-- wizard container -->
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

                    <div class="navi navi-bold navi-hover navi-active navi-link-rounded" style="overflow-x:overlay">

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
            <div class="tab-pane" id="notes" role="tabpanel">3. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
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