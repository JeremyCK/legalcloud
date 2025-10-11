
@if (!in_array($current_user->menuroles, ['receptionist']))
    @if (!in_array($current_user->id, [14]))
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-primary">
                <div class="card-body pb-0" style="padding-bottom:30px !important;">
                    <div class="btn-group float-right">
                    </div>
                    <div class="text-value-lg"> @if(isset($openCaseCount)) {{$openCaseCount}} @else 0 @endif</div>
                    <div>Total Cases</div>
                </div>
            </div>
        </div>
    @endif
@endif

<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-success">
        <div class="card-body pb-0" style="padding-bottom:30px !important;">

            <div class="text-value-lg">@if(isset($closedCaseCount)) {{$closedCaseCount}} @else 0 @endif</div>
            <div>Total Closed Cases</div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-warning">
        <div class="card-body pb-0" style="padding-bottom:30px !important;">
            <div class="btn-group float-right">
            </div>
            <div class="text-value-lg">@if(isset($InProgressCaseCount)) {{$InProgressCaseCount}} @else 0 @endif</div>
            <div>Total Active Cases</div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-purple">
        <div class="card-body pb-0" style="padding-bottom:30px !important;">
            <div class="btn-group float-right">
            </div>
            <div class="text-value-lg">@if(isset($OverdueCaseCount)) {{$OverdueCaseCount}} @else 0 @endif</div>
            <div>Total Pending Close Cases</div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card text-white bg-danger">
        <div class="card-body pb-0" style="padding-bottom:30px !important;">
            <div class="btn-group float-right">
            </div>
            <div class="text-value-lg">@if(isset($abortCaseCount)) {{$abortCaseCount}} @else 0 @endif</div>
            <div>Total Abort Cases</div>
        </div>
    </div>
</div>
