

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
                    {{ number_format($LoanCaseBillMain->referral_a1 + $LoanCaseBillMain->referral_a2 + $LoanCaseBillMain->referral_a3, 2, '.', ',') }}</div>
                <div>R1 - R3</div>
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
                    {{ number_format($LoanCaseBillMain->referral_a4, 2, '.', ',') }}</div>
                <div>MISC</div>
            </div>
            <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary">
            <div class="card-body pb-0">
                <div class="btn-group float-right">
    
                </div>
                <div class="text-value-lg" id="lb_bill_collected">RM
                    {{ number_format($LoanCaseBillMain->marketing, 2, '.', ',') }}</div>
                <div>Marketing</div>
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
                    $balance =  ($LoanCaseBillMain->pfee1_inv + $LoanCaseBillMain->pfee2_inv)
                    - $LoanCaseBillMain->referral_a1 - $LoanCaseBillMain->referral_a2 - $LoanCaseBillMain->referral_a3
                    - $LoanCaseBillMain->marketing - $LoanCaseBillMain->referral_a4;
                @endphp
                <div class="text-value-lg" id="lb_bill_balance">RM
                    {{ number_format($balance, 2, '.', ',') }}
                </div>
                <div>Total OA</div>
            </div>
            <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

            </div>
        </div>
    </div>
@endif
