<div class="col-sm-6 col-lg-4">
    <div class="card text-white bg-primary">
        <div class="card-body pb-0">
            <div class="btn-group float-right">
                <div class="dropdown-menu dropdown-menu-right" style="margin: 0px;">
                    <a class="dropdown-item" href="javascript:void(0)"
                        onclick="trustMode('{{ $case->id }}')">Entry</a>
                </div>
            </div>
            <div class="text-value-lg">RM
                {{-- {{ number_format(isset($case->collected_trust) ? $case->collected_trust : 0, 2, '.', ',') }} --}}
                {{ number_format(isset($LoanCaseTrustMain->total_received) ? $LoanCaseTrustMain->total_received : 0, 2, '.', ',') }}
            </div>
            <div>Total Received</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card text-white bg-info">
        <div class="card-body pb-0">
            <div class="btn-group float-right">
                <div class="dropdown-menu dropdown-menu-right" style="margin: 0px;">
                    <a class="dropdown-item" href="javascript:void(0)"
                        onclick="trustMode('{{ $case->id }}')">Entry</a>
                </div>
            </div>
            <div class="text-value-lg">RM
                {{-- {{ number_format(isset($case->total_trust) ? $case->total_trust : 0, 2, '.', ',') }} --}}
                {{ number_format(isset($LoanCaseTrustMain->total_used) ? $LoanCaseTrustMain->total_used : 0, 2, '.', ',') }}
            </div>
            <div>Total Used</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card text-white bg-warning">
        <div class="card-body pb-0">
            <div class="btn-group float-right">
                <div class="dropdown-menu dropdown-menu-right" style="margin: 0px;">
                    <a class="dropdown-item" href="javascript:void(0)"
                        onclick="trustMode('{{ $case->id }}')">Entry</a>
                </div>
            </div>
            <?php $total_used = (isset($LoanCaseTrustMain->total_received) ? $LoanCaseTrustMain->total_received : 0) - (isset($LoanCaseTrustMain->total_used) ? $LoanCaseTrustMain->total_used : 0); ?>
            <div class="text-value-lg">RM {{ number_format($total_used, 2, '.', ',') }}</div>
            <div>Balance</div>
        </div>
        <div class="c-chart-wrapper mt-3 mx-3" style="height:20px;">

        </div>
    </div>
</div>