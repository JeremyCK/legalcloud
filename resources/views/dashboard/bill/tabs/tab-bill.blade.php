<div class="row">

  <div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-success">
      <div class="card-body pb-0">
        <button class="btn btn-transparent p-0 float-right" type="button">
          <svg class="c-icon">
            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-location-pin"></use>
          </svg>
        </button>
        <div class="text-value-lg">RM {{$case->targeted_bill}}</div>
        <div>Target bill collection</div>
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
        <canvas class="chart chartjs-render-monitor" id="card-chart2" height="94" width="292" style="display: block; height: 20px; width: 217px;"></canvas>
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
            <a class="dropdown-item" href="javascript:void(0)" onclick="billMode('{{ $case->id }}')">Entry</a>
          </div>
        </div>
        <div class="text-value-lg">RM {{$case->collected_bill}}</div>
        <div>Total Collected bill</div>
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
        <div class="text-value-lg">RM {{$case->total_bill}}</div>
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
        <div class="text-value-lg">RM {{$case->collected_bill - $case->total_bill}}</div>
        <div>Balance</div>
      </div>
      <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Bill list</h3>
        @if (!in_array($case->status,[99,0]))
        <div class="box-tools">
          <button class="btn btn-primary float-right" type="button" onclick="billEntryMode('{{ $case->id }}');">
            <i class="cil-plus"></i> Received Payment
          </button>
        </div>
        @endif

      </div>
    </div>
  </div>
</div>



<!-- <div style="max-height:500px; overflow:scroll"> -->
<div style="">
  <table class="table table-striped table-bordered datatable">
    <thead>
      <tr class="text-center">
        <th>No</th>
        <th>Item</th>
        <th>Amount (RM)</th>
        <!-- <th>Action</th> -->
      </tr>
    </thead>
    <tbody id="tbl-case-bill">
      <?php
      $total = 0;
      $subtotal = 0;
      ?>
      @if(count($quotation))


      @foreach($quotation as $index => $cat)
      <tr style="background-color:grey;color:white">
        <td colspan="5">{{$cat['category']->category}}</td>
        <?php $total += $subtotal ?>
        <?php $subtotal = 0 ?>

      </tr>
      <?php $category_amount = 0 ?>
      @foreach($cat['account_details'] as $index => $details)
      <?php $subtotal += $details->amount ?>

      <tr>
        <td class="text-center" style="width:50px">
          <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}" id="account_item_id_{{ $details->id }}">
          <input type="hidden" name="need_approval" value="{{ $details->need_approval }}" id="need_approval_{{ $details->id }}">
          <input type="hidden" name="min" value="{{ $details->min }}" id="min_{{ $details->id }}">
          <input type="hidden" name="max" value="{{ $details->max }}" id="max_{{ $details->id }}">
          <div class="checkbox">
            <input type="checkbox" name="case_bill" value="{{ $details->id }}" id="chk_{{ $details->id }}" @if($details->amount == 0) disabled @endif>
            <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
          </div>
        </td>
        <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
        <td id="item_{{ $details->id }}">{{ $details->account_name }}</td>
        <td id="amt_{{ $details->id }}">{{ $details->amount }}</td>
        <td id="amt_{{ $details->id }}">{{ $details->amount*0.06 }}</td>
        <!-- <td ><input class="form-control" type="number" value="{{ $details->amount }}" id="quo_amt_{{ $details->id }}" > -->




        </td>

      </tr>

      @endforeach

      @if($cat['category']->taxable == "1")
      <tr>
        <td colspan="2">{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
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
        <td style="text-align:right" colspan="4"> {{ number_format((float)$total, 2, '.', '');  }}</td>

      </tr>
      @else
      <tr>
        <td class="text-center" colspan="5">No data</td>
      </tr>
      @endif

    </tbody>
  </table>
</div>