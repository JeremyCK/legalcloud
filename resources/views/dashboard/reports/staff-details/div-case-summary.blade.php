<div class="row" id="div-case-count">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-primary">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="btn-group float-right">
                </div>
                <div class="text-value-lg">@isset($CaseCountActive) {{count($CaseCountActive)}} @else 0 @endisset</div>
                <div>Running Case</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-success">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                <div class="text-value-lg"> @isset($CaseCountReviewing) {{count($CaseCountReviewing)}} @else 0 @endisset</div>
                <div>Reviewing Case</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-warning">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                <div class="text-value-lg"> @isset($CaseCountPendingClose) {{count($CaseCountPendingClose)}} @else 0 @endisset</div>
                <div>Pending Close Cases</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white bg-purple">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                <div class="text-value-lg"> @isset($CaseCountClose) {{count($CaseCountClose)}} @else 0 @endisset</div>
                <div>Close Cases</div>
            </div>
        </div>
    </div>
</div>