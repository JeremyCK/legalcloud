<div class="row" id="div-case-count">
    <!-- Workflow Metrics (New) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white" style="background-color: #17a2b8 !important; cursor: help;" title="Total number of active cases currently being managed by this staff member. Includes all cases that are not closed or aborted, regardless of when they were created.">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="text-value-lg">@isset($CaseCountTotalActive) {{count($CaseCountTotalActive)}} @else 0 @endisset</div>
                <div>Total Active Cases<br><small>Currently Managing</small></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white" style="background-color: #28a745 !important; cursor: help;" title="Number of cases that were created or assigned to this staff member in the selected year. This represents new cases accepted during the year.">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="text-value-lg">@isset($CaseCountAccepted) {{count($CaseCountAccepted)}} @else 0 @endisset</div>
                <div>Accepted Cases<br><small>Assigned in Selected Year</small></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white" style="background-color: #6c757d !important; cursor: help;" title="Number of cases that were closed during the selected year. This is based on the actual close date of the case, showing cases that were completed in this year.">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="text-value-lg">@isset($CaseCountClosedInYear) {{count($CaseCountClosedInYear)}} @else 0 @endisset</div>
                <div>Cases Closed in Year<br><small>Closed in {{$year ?? 'Selected Year'}}</small></div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown (Existing) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card text-white" style="background-color: #007bff !important; cursor: help;" title="Number of running/active cases that were created in the selected year. These are cases that are currently in progress and were started during the selected year.">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="text-value-lg">@isset($CaseCountActive) {{count($CaseCountActive)}} @else 0 @endisset</div>
                <div>Running Cases<br><small>Created in {{$year ?? 'Selected Year'}}</small></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white" style="background-color: #20c997 !important; cursor: help;" title="Number of cases that are currently under review and were created in the selected year. These cases are being reviewed before closure.">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="text-value-lg">@isset($CaseCountReviewing) {{count($CaseCountReviewing)}} @else 0 @endisset</div>
                <div>Reviewing Cases<br><small>Created in {{$year ?? 'Selected Year'}}</small></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-white" style="background-color: #ffc107 !important; cursor: help;" title="Number of cases that are pending closure and were created in the selected year. These cases are waiting to be finalized and closed.">
            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                <div class="text-value-lg">@isset($CaseCountPendingClose) {{count($CaseCountPendingClose)}} @else 0 @endisset</div>
                <div>Pending Close Cases<br><small>Created in {{$year ?? 'Selected Year'}}</small></div>
            </div>
        </div>
    </div>
</div>