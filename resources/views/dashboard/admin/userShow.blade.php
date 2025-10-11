<style>
  .userlist_large {
    position: relative;
  }

  .userlist_large .media {
    flex-flow: column;
    text-align: center;
    position: relative;
  }

  .userlist_large .media.column {
    flex-flow: row;
  }

  .userlist_large .media figure {
    margin: 0 auto;
    margin-top: 36px !important;
  }

  .userlist_large .media .media-body {
    margin-top: 20px;
    width: 100%;
  }

  .userlist_large .media .media-body h5 {
    font-weight: 400;
    margin-bottom: 0;
  }

  .userlist_large .media .media-body p {
    font-size: 13px;
    line-height: 16px;
  }

  .userlist_large .media .media-body .dropdown-toggle:after {
    display: none;
  }

  .userlist_large .media .icon-center {
    position: absolute;
    height: 50px;
    width: 50px;
    line-height: 46px;
    top: 60px;
    left: 5%;
    margin-top: -25px;
    border-radius: 25px;
    border: 1px solid #8596ae;
    color: #8596ae;
    text-align: center;
    vertical-align: middle;
    font-size: 20px;
  }

  .userlist_large .media .icon-center.right {
    left: auto;
    right: 5%;
  }

  .userlist_large.bg-dark .media figure {
    border: 0;
  }

  .userlist_large.bg-dark .media .icon-center {
    border: 1px solid #ffffff !important;
  }

  .userlist_large.bg-dark .media .media-body {
    color: #b3becd;
  }

  .userlist_large.bg-dark .media .media-body h5 {
    color: #ffffff;
    font-weight: 400;
  }

  .userlist_large.bg-dark .media .media-body p {
    color: #8596ae;
  }

  .userlist_large.bg-dark .media .media-body .btn {
    color: #ffffff;
  }

  .user-full-large {
    position: relative;
    width: 100%;
    display: block;
    height: 280px;
    overflow-y: hidden;
  }

  .user-full-large:before {
    content: " ";
    width: 100%;
    left: 0;
    top: 0;
    display: block;
    position: absolute;
    Z-index: 1;
    height: 80px;
    /* fallback/image non-cover color */
    background-image: -moz-linear-gradient(270deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -webkit-gradient(270deg, linear, 0% 0%, 0% 100%, from(rgba(0, 0, 0, 0.85)), to(transparent));
    background-image: -webkit-linear-gradient(270deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -o-linear-gradient(270deg, rgba(0, 0, 0, 0.85), transparent);
  }

  .user-full-large:after {
    content: " ";
    width: 100%;
    left: 0;
    bottom: 0;
    display: block;
    position: absolute;
    Z-index: 1;
    height: 80px;
    /* fallback/image non-cover color */
    background-image: -moz-linear-gradient(90deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -webkit-gradient(90deg, linear, 0% 0%, 0% 100%, from(rgba(0, 0, 0, 0.85)), to(transparent));
    background-image: -webkit-linear-gradient(90deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -o-linear-gradient(90deg, rgba(0, 0, 0, 0.85), transparent);
  }

  .user-full-large figure {
    width: 100%;
    position: relative;
    z-index: 0;
    height: 100%;
    display: block;
    overflow: hidden;
    margin: 0;
  }

  .user-full-large figure img {
    width: 100%;
    min-height: 100%;
  }

  .user-full-large .album_block {
    height: 160px;
    width: 160px;
    display: block;
    margin: 10% auto 0 auto;
  }

  .user-full-large .album_block img {
    width: 100%;
    min-height: 100%;
  }

  .user-full-large .media {
    flex-direction: column;
    text-align: center;
  }

  .user-full-large .media .media-body {
    margin-top: 20px;
    width: 100%;
    position: absolute;
    left: 0;
    bottom: 0;
    z-index: 2;
  }

  .user-full-large .media .media-body h5,
  .user-full-large .media .media-body h4 {
    font-weight: 400;
    margin-bottom: 0;
    color: #ffffff;
  }

  .user-full-large .media .media-body p {
    font-size: 13px;
    line-height: 16px;
  }

  .user-full-large .media .media-body .dropdown-toggle:after {
    display: none;
  }
</style>
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<style></style>
@extends('dashboard.base')

@section('css')
<link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">

      <div class="col-sm-6">
        <div class="card">
          <div class="card-body  userlist_large">
            <table class="table table-responsive-sm table-hover mb-0">
              <tbody>
                <tr>
                  <td class="text-center">
                    <div class="media">
                      <div class="c-avatar col-xl-12">
                        <img class="c-avatar-img" src="/assets/img/avatars/img-default-profile.png" style="width: 150px !important;height: 150px !important;" alt="user@email.com">
                      </div>
                      <div class="media-body">
                        <h4 class="mt-0">{{ $user->name }} ({{ $user->nick_name }}) </h4>
                        <p class="text-secondary"> {{ $user->menuroles }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">Phone</div><strong>{{ $user->phone_no }}</strong>
                    <br /> <br />
                    <div class="small text-muted">Email</div><strong>{{ $user->email }}</strong>
                    <br /> <br />
                    <div class="small text-muted">Portfolio</div><strong>{{ $user->portfolio }}</strong>
                    <br /> <br />
                    <div class="small text-muted">Leave</div><strong>14 days left</strong>
                  </td>
                </tr>


              </tbody>
            </table>
          </div>
        </div>

      </div>

      <div class="col-sm-6">
        <div class="card">
          <div class="card-header">KPI</div>
          <div class="card-body">
            <div class="c-chart-wrapper">
              <canvas height="250px" id="canvas-1"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <div class="row">
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-aqua" style="padding-top: 17px;"><i class="cil-folder-open"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $openCaseCount }}</span>
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
                <span class="info-box-number">{{ $closedCaseCount }}</span>
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
                <span class="info-box-number">{{ $InProgressCaseCount }}</span>
                <span class="info-box-text">In progress case</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-red" style="padding-top: 17px;"><i class="cil-av-timer"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $OverdueCaseCount }}</span>
                <span class="info-box-text">Overdue cases</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
      </div>

      <div class="col-12">
        
        <div class="card">
          <div class="card-header">
            <h4>Portfolio</h4>
          </div>
          <div class="card-body userlist_large">
            <div class="row">
              @if(count($portfolios) > 0)
                @foreach($portfolios as $row)
                  <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                    <div class="form-group row">
                      <div class="col">
                        <div class="checkbox">
                          <input type="checkbox" name="portfolios" id="portfolio_{{ $row->id }}" @if(in_array($row->id,$TeamPortfolios)) checked  @endif >
                          <label for="portfolio_{{ $row->id }}" >{{ $row->name }}</label>
                        </div>
                      </div>
                    </div>
                </div>
                @endforeach
              @else
              @endif
        
           
          </div>
        </div>
        
      
      
      </div>


      <div class="col-sm-6 col-md-5 col-lg-6 col-xl-6 hide">
        <div class="card">
          <div class="card-header">
            <h4>Activity Log</h4>
          </div>
          <div class="card-body userlist_large">
            <!-- <h4>{{ __('coreuiforms.users.username') }}: {{ $user->name }}</h4>
            <h4>{{ __('coreuiforms.users.email') }}: {{ $user->email }}</h4>
            <a href="{{ route('users.index') }}" class="btn btn-primary">{{ __('coreuiforms.return') }}</a>

            <div class="media">
              <div class="c-avatar col-xl-12">
                <img class="c-avatar-img" src="/assets/img/avatars/img-default-profile.png" style="width: 150px !important;height: 150px !important;" alt="user@email.com">
              </div>
              <div class="media-body">
                <h4 class="mt-0">{{ $user->name }}</h4>
                <p class="text-secondary"> {{ $user->menuroles }}</p>
                <p class="mb-0 ">test</p>
              </div>
            </div> -->


            <ul class="timeline">
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-green"> 15 Jun 2021 </span>
              </li>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <li>
                <i class="ion ion-email bg-blue"></i>

                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 11:48</span>

                  <h3 class="timeline-header">{{ $user->name }} submmited the letter to runner <a href="/todolist/1">#S1/UWD/MBB/00001/lll/RLI</a></h3>

                  <!-- <div class="timeline-body">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ...
                  </div> -->
                  <!-- <div class="timeline-footer text-right">
                    <a href="#" class="btn btn-primary btn-sm">Read more</a>
                  </div> -->
                </div>
              </li>
              <!-- END timeline item -->
              <!-- timeline item -->
              <li>
                <i class="ion ion-person bg-aqua"></i>

                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 11 mins ago</span>

                  <h3 class="timeline-header no-border">{{ $user->name }} Update status for "Recv PFS letter" <a href="/todolist/1">#S1/UWD/MBB/00001/lll/RLI</a>
                    </h3>
                </div>
              </li>
              <!-- END timeline item -->
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-green">
                  13 Jun 2021
                </span>
              </li>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <li>
                <i class="ion ion-ios-reverse-camera bg-green"></i>

                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 8 days ago</span>

                  <h3 class="timeline-header">{{ $user->name }} Uploaded new files <a href="/todolist/1">#S1/UWD/MBB/00001/lll/RLI</a></h3>

                  <div class="timeline-body">


                    <ul class="mailbox-attachments clearfix">
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
                    </ul>

                  </div>
                </div>
              </li>
              <!-- END timeline item -->
              <li>
                <i class="fa fa-clock-o bg-gray"></i>
              </li>
            </ul>
          </div>




        </div>


      </div>


    </div>
  </div>
</div>

@endsection


@section('javascript')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script >
  const random = () => Math.round(Math.random() * 100)

// eslint-disable-next-line no-unused-vars
const lineChart = new Chart(document.getElementById('canvas-1'), {
  type: 'line',
  data: {
    labels : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets : [
      {
        label: 'My First dataset',
        backgroundColor : 'rgba(220, 220, 220, 0.2)',
        borderColor : 'rgba(220, 220, 220, 1)',
        pointBackgroundColor : 'rgba(220, 220, 220, 1)',
        pointBorderColor : '#fff',
        data : [random(), random(), random(), random(), random(), random(), random()]
      }
    ]
  },
  options: {
    responsive: true
  }
})
</script>


@endsection