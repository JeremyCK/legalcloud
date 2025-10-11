<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary">
          <div class="card-body pb-0">
            <div class="btn-group float-right">
              <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <svg class="c-icon">
                  <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                </svg>
              </button>
              <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">{{ __('dashboard.action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.another_action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.something_else_here') }}</a></div>
            </div>
            <div class="text-value-lg">{{ $openCaseCount }}</div>
            <div>Total Open Case</div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <canvas class="chart" id="card-chart1" height="70"></canvas>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-success">
          <div class="card-body pb-0">
            <button class="btn btn-transparent p-0 float-right" type="button">
              <svg class="c-icon">
                <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-location-pin"></use>
              </svg>
            </button>
            <div class="text-value-lg">{{ $closedCaseCount }}</div>
            <div>Total Closed Case</div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <canvas class="chart" id="card-chart2" height="70"></canvas>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-warning">
          <div class="card-body pb-0">
            <div class="btn-group float-right">
              <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <svg class="c-icon">
                  <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                </svg>
              </button>
              <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">{{ __('dashboard.action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.another_action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.something_else_here') }}</a></div>
            </div>
            <div class="text-value-lg">{{ $InProgressCaseCount }}</div>
            <div>In progress Case</div>
          </div>
          <div class="c-chart-wrapper mt-3" style="height:70px;">
            <canvas class="chart" id="card-chart3" height="70"></canvas>
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-danger">
          <div class="card-body pb-0">
            <div class="btn-group float-right">
              <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <svg class="c-icon">
                  <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                </svg>
              </button>
              <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">{{ __('dashboard.action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.another_action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.something_else_here') }}</a></div>
            </div>
            <div class="text-value-lg">{{ $OverdueCaseCount }}</div>
            <div>Overdue Case</div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <canvas class="chart" id="card-chart4" height="70"></canvas>
          </div>
        </div>
      </div>
      <!-- /.col-->
    </div>
    <!-- <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-5">
            <h4 class="card-title mb-0">Total Sales (RM)</h4>
            <div class="small text-muted">2021</div>
          </div>
          <div class="col-sm-7 d-none d-md-block">
            <button class="btn btn-primary float-right" type="button">
              <svg class="c-icon">
                <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-cloud-download"></use>
              </svg>
            </button>
          </div>
        </div>
        <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
          <canvas class="chart" id="main-chart2" height="300"></canvas>
        </div>
      </div>
    </div> -->


    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>KPI (Lawyer)</strong></div>
          <div class="card-body" style="max-height:600px;overflow:scroll">
            <!-- /.row--><br>
            <table class="table table-responsive-sm table-hover table-outline mb-0">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Staff</th>
                  <th class="text-center">KPI Get</th>
                  <th class="text-center">KPI Missed</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>

                @if(count($lawyer_list))
                @foreach($lawyer_list as $index => $lawyer)
                <tr>
                  <td class="text-center">
                    {{$index+1}}
                  </td>
                  <td class="text-center">
                    <div>{{ $lawyer->name }}</div>
                  </td>
                  <td class="text-center">{{ $lawyer->kpi_get }}</td>
                  <td class="text-center">{{ $lawyer->kpi_over }}</td>
                   <td class="text-center">
                  <a target="_blank" href="kpi_list/{{$lawyer->id}}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-chevron-double-right"></i></a>
                </td>

                </tr>
                @endforeach
                @else
                @endif

              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><strong>KPI (Clerk)</strong></div>
          <div class="card-body" style="max-height:600px;overflow:scroll">
            <!-- /.row--><br>
            <table class="table table-responsive-sm table-hover table-outline mb-0">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Staff</th>
                  <th class="text-center">KPI Get</th>
                  <th class="text-center">KPI Missed</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>

                @if(count($clerk_list))
                @foreach($clerk_list as $index => $clerk)
                <tr>
                  <td class="text-center">
                    {{$index+1}}
                  </td>
                  <td>
                    <div>{{ $clerk->name }}</div>
                  </td>
                  <td>{{ $clerk->kpi_get }}</td>
                  <td class="text-center">{{ $clerk->kpi_over + $clerk->kpi_miss }}</td>
                  <td class="text-center">
                  <a target="_blank" href="kpi_list/{{$lawyer->id}}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-chevron-double-right"></i></a>
                </td>

                </tr>
                @endforeach
                @else
                @endif

              </tbody>
            </table>
          </div>
        </div>
      </div>

      
      
    </div>


    <!-- <div class="row">
      <div class="col-sm-6 col-lg-6">

        <div class="card">
          <div class="card-header">Open case Type
          </div>
          <div class="card-body">
            <div class="c-chart-wrapper">
              <div class="chartjs-size-monitor">
                <div class="chartjs-size-monitor-expand">
                  <div class=""></div>
                </div>
                <div class="chartjs-size-monitor-shrink">
                  <div class=""></div>
                </div>
              </div>
              <canvas id="canvas-5" width="784" height="392" style="display: block; height: 490px; width: 981px;" class="chartjs-render-monitor"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-6">
        <div class="card">
          <div class="card-header"><strong>Total Revenue (2021)</strong>
            <div class="card-header-actions"><a class="card-header-action" href="http://www.chartjs.org" target="_blank"><small class="text-muted">docs</small></a></div>
          </div>
          <div class="card-body">
            <div class="c-chart-wrapper">
              <div class="chartjs-size-monitor">
                <div class="chartjs-size-monitor-expand">
                  <div class=""></div>
                </div>
                <div class="chartjs-size-monitor-shrink">
                  <div class=""></div>
                </div>
              </div>
              <canvas id="canvas-2" width="784" height="392" style="display: block; height: 490px; width: 981px;" class="chartjs-render-monitor"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div> -->



    <!-- /.row-->
    <div class="row hide">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header"><strong> Top Performance Employee</strong></div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-6">
                    <div class="c-callout c-callout-info"><small class="text-muted">{{ __('dashboard.new_clients') }}</small>
                      <div class="text-value-lg">9,123</div>
                    </div>
                  </div>
                  <!-- /.col-->
                  <div class="col-6">
                    <div class="c-callout c-callout-danger"><small class="text-muted">{{ __('dashboard.recuring_clients') }}</small>
                      <div class="text-value-lg">22,643</div>
                    </div>
                  </div>
                  <!-- /.col-->
                </div>
                <!-- /.row-->

              </div>
              <!-- /.col-->
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-6">
                    <div class="c-callout c-callout-warning"><small class="text-muted">{{ __('dashboard.pageviews') }}</small>
                      <div class="text-value-lg">78,623</div>
                    </div>
                  </div>
                  <!-- /.col-->
                  <div class="col-6">
                    <div class="c-callout c-callout-success"><small class="text-muted">{{ __('dashboard.organic') }}</small>
                      <div class="text-value-lg">49,123</div>
                    </div>
                  </div>
                  <!-- /.col-->
                </div>
                <!-- /.row-->

              </div>
              <!-- /.col-->
            </div>
            <!-- /.row--><br>
            <table class="table table-responsive-sm table-hover table-outline mb-0">
              <thead class="thead-light">
                <tr>
                  <th>{{ __('dashboard.user') }}</th>
                  <th class="text-center">Closed cases</th>
                  <th class="text-center">In progress Cases</th>
                  <th>KPI</th>
                  <th>{{ __('dashboard.activity') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div>Lawyer 1</div>
                  </td>
                  <td class="text-center">22</td>
                  <td class="text-center">10</td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>250</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>10 {{ __('dashboard.time.sec_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div>Angie Turcotte</div>
                  </td>
                  <td class="text-center">15</td>
                  <td class="text-center">5</td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>200</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>5 {{ __('dashboard.time.minutes_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div>Quintin Ed</div>
                  </td>
                  <td class="text-center">10</td>
                  <td class="text-center">5</td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>175</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: 74%" aria-valuenow="74" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>1 {{ __('dashboard.time.hour_ago') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div>Enéas Kwadwo</div>
                  </td>
                  <td class="text-center">8</td>
                  <td class="text-center">5</td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>170</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-danger" role="progressbar" style="width: 98%" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>{{ __('dashboard.time.last_month') }}</strong>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div>Agapetus Tadeáš</div>
                  </td>
                  <td class="text-center">7</td>
                  <td class="text-center">5</td>
                  <td>
                    <div class="clearfix">
                      <div class="float-left"><strong>125</strong></div>
                      <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul 10, 2015</small></div>
                    </div>
                    <div class="progress progress-xs">
                      <div class="progress-bar bg-info" role="progressbar" style="width: 22%" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">{{ __('dashboard.last_login') }}</div><strong>{{ __('dashboard.time.last_week') }}</strong>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.col-->
    </div>
    <!-- /.row-->
  </div>
</div>