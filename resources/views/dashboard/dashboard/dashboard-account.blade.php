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
              <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">{{ __('dashboard.action') }}</a><a class="dropdown-item" onclick="populateChart()" href="#">{{ __('dashboard.another_action') }}</a><a class="dropdown-item" href="#">{{ __('dashboard.something_else_here') }}</a></div>
            </div>
            <div class="text-value-lg">{{ $openCaseCount }}</div>
            <div>Total Transfer</div>
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
            <div>Total Client Accounts</div>
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
            <div>Total oustanding Client Account</div>
          </div>
          <div class="c-chart-wrapper mt-3" style="height:70px;">
            <canvas class="chart" id="card-chart3" height="70"></canvas>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-info">
          <div class="card-body pb-0">
            <div class="btn-group float-right">
              <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <svg class="c-icon">
                  <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                </svg>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#">{{ __('dashboard.action') }}</a>
              </div>
            </div>
            <div class="text-value-lg">{{ $mytodoCount }}</div>
            <div>My todo task</div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
            <canvas class="chart" id="card-chart4" height="70"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="row">
      <div class="col-sm-6 col-lg-6">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-5  col-xl-12">
                <h4 class="card-title mb-0">Office account balance (RM)</h4>
                <div class="small text-muted">2021</div>
              </div>

            </div>
            <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
              <canvas class="chart" id="main-chart2" height="300"></canvas>
            </div>
          </div>

        </div>
      </div>

      <div class="col-sm-6 col-lg-6">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-5  col-xl-12">
                <h4 class="card-title mb-0">Office account Spent (RM)</h4>
                <div class="small text-muted">2021</div>
              </div>

            </div>
            <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
              <canvas class="chart" id="line-chart3" height="300"></canvas>
            </div>
          </div>

        </div>
      </div>
    </div> -->

    <!-- <div class="row">
      <div class="col-sm-6 col-lg-12">

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
    </div> -->
  </div>
</div>

<script>
</script>