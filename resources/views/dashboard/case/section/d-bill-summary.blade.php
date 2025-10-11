<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-success">
        <div class="card-body pb-0">

            <div id="target_bill_case" class="text-value" style="font-size: 1.1rem !important">RM
                {{ number_format($case->targeted_bill, 2, '.', ',') }}</div>
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
            <canvas class="chart chartjs-render-monitor" id="card-chart2" height="94" width="292"
                style="display: block; height: 20px; width: 217px;"></canvas>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-primary">
        <div class="card-body pb-0">
            <div class="text-value" style="font-size: 1.1rem !important">RM
                {{ number_format($case->collected_bill, 2, '.', ',') }}</div>
            <div>Total Collected bill</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>

@if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillBalancePermission()) == true)

<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-info">
        <div class="card-body pb-0">
            <div class="text-value" style="font-size: 1.1rem !important">RM
                {{ number_format($case->total_bill, 2, '.', ',') }}</div>
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
            </div>
            @php
            $balance = 0;
            foreach ($loanCaseBillMain as $index => $bill)
            {
                $balance += $bill->collected_amt - $bill->used_amt - ($bill->pfee1_inv + $bill->pfee2_inv)
                - $bill->sst_inv - $bill->disb_amt_manual;
            }

        @endphp
            <div class="text-value" style="font-size: 1.1rem !important">RM
                {{ number_format($balance, 2, '.', ',') }}</div>
            <div>Total CA</div> 
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>
@endif
