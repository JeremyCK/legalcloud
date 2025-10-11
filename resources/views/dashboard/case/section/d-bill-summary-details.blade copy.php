<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-success">
        <div class="card-body pb-0">

            <div class="text-value-lg" id="lb_bill_target">RM
                {{ number_format($LoanCaseBillMain->total_amt, 2, '.', ',') }}</div>
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
            <div class="btn-group float-right">

            </div>
            <div class="text-value-lg" id="lb_bill_collected">RM
                {{ number_format($LoanCaseBillMain->collected_amt, 2, '.', ',') }}</div>
            <div>Total Collected bill</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>

@if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillBalancePermission()) == true)

<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-info">
        <div class="card-body pb-0">
            <div class="btn-group float-right">
            </div>
            <div class="text-value-lg" id="lb_bill_spent">RM
                {{ number_format($LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv, 2, '.', ',') }}</div>
            <div>Prof Fee</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-orange">
        <div class="card-body pb-0">
            <div class="btn-group float-right">
            </div>
            <div class="text-value-lg" id="lb_bill_spent">RM
                {{ number_format($LoanCaseBillMain->sst_inv, 2, '.', ',') }}</div>
            <div>SST</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>    

<div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-purple">
            <div class="card-body pb-0">
                <div class="btn-group float-right">
                </div>
                <div class="text-value-lg" id="lb_bill_spent">RM
                    {{ number_format($LoanCaseBillMain->used_amt, 2, '.', ',') }}</div>
                <div>Total Spent</div>
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
                <div class="text-value-lg" id="lb_bill_spent">RM
                    {{ number_format($LoanCaseBillMain->disb_amt_manual, 2, '.', ',') }}</div>
                <div>Disb Manual</div>
            </div>
            <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

            </div>
        </div>
    </div>


    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-danger">
            <div class="card-body pb-0">
                <div class="btn-group float-right">
                </div>
                @php
                    $balance = $LoanCaseBillMain->collected_amt - $LoanCaseBillMain->used_amt - ($LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv)
                    - $LoanCaseBillMain->sst_inv - $LoanCaseBillMain->disb_amt_manual;
                @endphp
                <div class="text-value-lg" id="lb_bill_balance">RM
                    {{ number_format($balance, 2, '.', ',') }}
                </div>
                <div>Total CA</div>
            </div>
            <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

            </div>
        </div>
    </div>
@endif
