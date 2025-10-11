<div class="row">
  <div class="col-12">
    <div class="box">
      <div class="box-header">

        <div class="row">

          <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-success">
              <div class="card-body pb-0">
                <button class="btn btn-transparent p-0 float-right" type="button">
                  <svg class="c-icon">
                    <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-location-pin"></use>
                  </svg>
                </button>
                <div class="text-value-lg">RM {{$case->targeted_trust}}</div>
                <div>Target Received</div>
              </div>
              <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">
                <div class="chartjs-size-monitor">
                  <div class="chartjs-size-monitor-expand">
                    <div class=""></div>
                  </div>
                  <div class="chartjs-size-monitor-shrink">
                    <div class=""></div>
                  </div>
                </div>
                <canvas class="chart chartjs-render-monitor" id="card-chart2" height="94" width="292" style="display: block; height: 70px; width: 217px;"></canvas>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-primary">
              <div class="card-body pb-0">
                <div class="btn-group float-right">
                  <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg class="c-icon">
                      <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                    </svg>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right" style="margin: 0px;">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="trustMode('{{ $case->id }}')">Entry</a>
                  </div>
                </div>
                <div class="text-value-lg">RM {{$case->collected_trust}}</div>
                <div>Total Received</div>
              </div>
              <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

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
                  <div class="dropdown-menu dropdown-menu-right" style="margin: 0px;">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="trustMode('{{ $case->id }}')">Entry</a>
                  </div>
                </div>
                <div class="text-value-lg">RM {{$case->collected_trust}}</div>
                <div>Total Used</div>
              </div>
              <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-warning">
              <div class="card-body pb-0">
                <div class="btn-group float-right">
                  <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg class="c-icon">
                      <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-settings"></use>
                    </svg>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right" style="margin: 0px;">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="trustMode('{{ $case->id }}')">Entry</a>
                  </div>
                </div>
                <div class="text-value-lg">RM {{$case->collected_trust}}</div>
                <div>Balance</div>
              </div>
              <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

              </div>
            </div>
          </div>


        </div>


      </div>


      <div class="row">
          <div class="col-12">
            <div class="box">
              <div class="box-header">
                <h3 class="box-title"></h3>

                <div class="box-tools">
                  <button class="btn btn-primary" type="button" onclick="trustMode('0', '{{ $case->id }}');">
                    <i class="cil-plus"></i> New entry
                  </button>
                </div>

              </div>
            </div>
          </div>
        </div>

      <!-- /.box-header -->
      <div class="box-body no-padding" style="width:100%;overflow-x:auto">

        

        <table class="table table-striped table-bordered datatable">
          <tbody>
            <tr class="text-center">
              <th>Transaction ID</th>
              <th>Item</th>
              <th>Credit (RM)</th>
              <th>Debit (RM)</th>
              <th>Cheque No</th>
              <th>Bank</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
            @if(count($loan_case_trust))

            @foreach($loan_case_trust as $index => $transaction)
            <tr>
              <td class="text-center">{{ $transaction->id }}</td>
              <td>{{ $transaction->item_name }}</td>
              <td>
                @if($transaction->transaction_type == 'C')
                {{ $transaction->amount }}
                @else
                -
                @endif
              </td>
              <td>
                @if($transaction->transaction_type == 'D')
                {{ $transaction->amount }}
                @else
                -
                @endif
              </td>

              <td>{{ $transaction->cheque_no }} </td>
              <td>{{ $transaction->bank_name }} </td>
              <td class="text-center">
                {{ $transaction->created_at }}
              </td>

              <td class="text-center">
                <a href="javascript:void(0)" onclick="dispatchMode('{{ $transaction->id }}', '{{ $case->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Edit</a>

              </td>
            </tr>

            @endforeach
            @else
            <tr>
              <td class="text-center" colspan="5">No data</td>
            </tr>
            @endif


          </tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>