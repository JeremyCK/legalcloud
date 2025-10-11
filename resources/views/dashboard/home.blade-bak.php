@extends('dashboard.base')

@section('css')
    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
@endsection

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                @if ($current_user->menuroles != 'account' && $current_user->menuroles != 'receptionist')
                    <div class="col-12">
                        <h2>Legalcloud cases</h2>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                <div class="btn-group float-right">
                                </div>
                                <div class="text-value-lg">{{ $openCaseCount }}</div>
                                <div>Total Cases</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-success">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                <div class="text-value-lg">{{ $closedCaseCount }}</div>
                                <div>Total Closed Cases</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                <div class="btn-group float-right">
                                </div>
                                <div class="text-value-lg">{{ $InProgressCaseCount }}</div>
                                <div>Total Active Cases</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                <div class="btn-group float-right">
                                </div>
                                <div class="text-value-lg">{{ $OverdueCaseCount }}</div>
                                <div>Total Aborted cases</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($current_user->menuroles == 'admin')
                    <div class="col-12">
                        <h2>Cases Before 2022</h2>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                <div class="btn-group float-right">
                                </div>
                                <div class="text-value-lg">{{ $B2022AllCases }}</div>
                                <div>Total Cases</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-success">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                <div class="text-value-lg">{{ $B2022ClosedCases }}</div>
                                <div>Total Closed Cases</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                <div class="text-value-lg">{{ $B2022ActiveCases }}</div>
                                <div>Total Active Cases</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-secondary">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                <div class="text-value-lg">{{ $B2022PendingCloseCases }}</div>
                                <div>Total Pending Close Cases</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            @if (in_array($current_user->menuroles, ['admin','sales']))
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header"><strong>Sales open case report (2022)</strong>
                            </div>
                            <div class="card-body" style="max-height:500px;overflow:scroll">
                                <table class="table table-responsive-sm table-hover table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Sales</th>
                                            @foreach ($month as $index => $mon)
                                                <th class="text-center">{{ $mon }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (count($sales))
                                            @foreach ($sales as $index => $user)
                                                <tr>
                                                    <td class="text-left">{{ $user->name }}</td>

                                                    @foreach ($user->sales_count as $index2 => $count)
                                                        <td class="text-center">{{ $count }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No receipt</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            @endif

            {{-- @if ($current_user->menuroles == 'admin') --}}
            @if (in_array($current_user->menuroles, ['admin']))



                <div class="row">
                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header"><strong>All cases</strong>
                            </div>
                            <div class="card-body">
                                <div id="chart_cases_all" class="c-chart-wrapper mt-3 mx-3" style="height: 400px">
                                    <canvas height="392" class="chart" id="caseCountChartAll"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header"><strong>Cases by branch</strong>
                            </div>
                            <div class="card-body">
                                <div id="chart_cases_branch" class="c-chart-wrapper mt-3 mx-3" style="height: 400px">
                                    <canvas height="392" class="chart" id="caseCountChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header"><strong>Lawyer Bonus</strong>
                            </div>
                            <div class="card-body" style="max-height:500px;overflow:scroll">
                                <table class="table table-responsive-sm table-hover table-outline mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Lawyer</th>
                                            <th class="text-center">Claimed (2%)</th>
                                            <th class="text-center">Claimed (3%)</th>
                                            <th class="text-center">Accrued (2%)</th>
                                            <th class="text-center">Accrued (3%)</th>
                                            <th class="text-center">P1 (2%)</th>
                                            <th class="text-center">P1 (3%)</th>
                                            <!-- <th class="text-center">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (count($lawyer_list))
                                            @foreach ($lawyer_list as $index => $user)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="text-left">
                                                        <div>{{ $user->name }}</div>
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->lawyer_bonus_2_per_paid, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->lawyer_bonus_3_per_paid, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->lawyer_bonus_2_per_accrued, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->lawyer_bonus_3_per_accrued, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->lawyer_bonus_2_per_p1, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->lawyer_bonus_3_per_p1, 2, '.', ',') }}
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No receipt</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header"><strong>Staff Bonus</strong>
                            </div>
                            <div class="card-body" style="max-height:500px;overflow:scroll">
                                <table class="table table-responsive-sm table-hover table-outline mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Staff</th>
                                            <th class="text-center">Claimed (2%)</th>
                                            <th class="text-center">Claimed (3%)</th>
                                            <th class="text-center">Accrued (2%)</th>
                                            <th class="text-center">Accrued (3%)</th>
                                            <th class="text-center">P1 (2%)</th>
                                            <th class="text-center">P1 (3%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (count($clerk_list))
                                            @foreach ($clerk_list as $index => $user)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="text-left">
                                                        <div>{{ $user->name }}</div>
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->staff_bonus_2_per_paid, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->staff_bonus_3_per_paid, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->staff_bonus_2_per_accrued, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->staff_bonus_3_per_accrued, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->staff_bonus_2_per_p1, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format((float) $user->staff_bonus_3_per_p1, 2, '.', ',') }}
                                                    </td>


                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No receipt</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row hide">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><strong> Top Performance Employee</strong></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="c-callout c-callout-info"><small
                                                        class="text-muted">{{ __('dashboard.new_clients') }}</small>
                                                    <div class="text-value-lg">9,123</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                            <div class="col-6">
                                                <div class="c-callout c-callout-danger"><small
                                                        class="text-muted">{{ __('dashboard.recuring_clients') }}</small>
                                                    <div class="text-value-lg">22,643</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                        </div>
                                        <!-- /.row-->

                                    </div>
                                    <!-- /.col-->
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="c-callout c-callout-warning"><small
                                                        class="text-muted">{{ __('dashboard.pageviews') }}</small>
                                                    <div class="text-value-lg">78,623</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                            <div class="col-6">
                                                <div class="c-callout c-callout-success"><small
                                                        class="text-muted">{{ __('dashboard.organic') }}</small>
                                                    <div class="text-value-lg">49,123</div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                        </div>
                                        <!-- /.row-->

                                    </div>
                                    <!-- /.col-->
                                </div>
                                <!-- /.row--><br>
                                <table class="table table-responsive-sm table-hover table-outline mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('dashboard.user') }}</th>
                                            <th class="text-center">Closed cases</th>
                                            <th class="text-center">In progress Cases</th>
                                            <th>KPI</th>
                                            <th>{{ __('dashboard.activity') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div>Lawyer 1</div>
                                            </td>
                                            <td class="text-center">22</td>
                                            <td class="text-center">10</td>
                                            <td>
                                                <div class="clearfix">
                                                    <div class="float-left"><strong>250</strong></div>
                                                    <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul
                                                            10, 2015</small></div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">{{ __('dashboard.last_login') }}</div>
                                                <strong>10 {{ __('dashboard.time.sec_ago') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>Angie Turcotte</div>
                                            </td>
                                            <td class="text-center">15</td>
                                            <td class="text-center">5</td>
                                            <td>
                                                <div class="clearfix">
                                                    <div class="float-left"><strong>200</strong></div>
                                                    <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul
                                                            10, 2015</small></div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 10%" aria-valuenow="10" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">{{ __('dashboard.last_login') }}</div>
                                                <strong>5 {{ __('dashboard.time.minutes_ago') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>Quintin Ed</div>
                                            </td>
                                            <td class="text-center">10</td>
                                            <td class="text-center">5</td>
                                            <td>
                                                <div class="clearfix">
                                                    <div class="float-left"><strong>175</strong></div>
                                                    <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul
                                                            10, 2015</small></div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: 74%" aria-valuenow="74" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">{{ __('dashboard.last_login') }}</div>
                                                <strong>1 {{ __('dashboard.time.hour_ago') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>Enéas Kwadwo</div>
                                            </td>
                                            <td class="text-center">8</td>
                                            <td class="text-center">5</td>
                                            <td>
                                                <div class="clearfix">
                                                    <div class="float-left"><strong>170</strong></div>
                                                    <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul
                                                            10, 2015</small></div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 98%" aria-valuenow="98" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">{{ __('dashboard.last_login') }}</div>
                                                <strong>{{ __('dashboard.time.last_month') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>Agapetus Tadeáš</div>
                                            </td>
                                            <td class="text-center">7</td>
                                            <td class="text-center">5</td>
                                            <td>
                                                <div class="clearfix">
                                                    <div class="float-left"><strong>125</strong></div>
                                                    <div class="float-right"><small class="text-muted">Jun 11, 2015 - Jul
                                                            10, 2015</small></div>
                                                </div>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 22%" aria-valuenow="22" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">{{ __('dashboard.last_login') }}</div>
                                                <strong>{{ __('dashboard.time.last_week') }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>

    {{-- @if ($current_user->menuroles == 'account')
        @include('dashboard.dashboard.dashboard-account')
    @endif --}}

 




    @if (count($kiv_note))
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Notes</strong> <span class=" badge badge-pill badge-danger">Today new messages:
                                    {{ $today_message_count }}</span>
                                <a href="javacript:void(0)" id="btn-filter-month"
                                    class="btn btn-filter-date pull-right btn-selected">Month</a>
                                <a href="javacript:void(0)" id="btn-filter-week"
                                    class="btn btn-filter-date pull-right">Week</a>
                                <a href="javacript:void(0)" id="btn-filter-today"
                                    class="btn btn-filter-date pull-right">Today</a>
                            </div>
                            <div class="card-body" style="max-height:600px;overflow:scroll">
                                <br>
                                <table id="table_notes_month"
                                    class="table table-responsive-sm table-hover table-outline mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Message</th>
                                            <th class="text-center">File Ref</th>
                                            <th class="text-center">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (count($kiv_note))
                                            @foreach ($kiv_note as $index => $note)
                                                <?php
                                                $color = 'info';
                                                if ($note->menuroles == 'account') {
                                                    $color = 'warning';
                                                } elseif ($note->menuroles == 'admin') {
                                                    $color = 'danger';
                                                } elseif ($note->menuroles == 'sales') {
                                                    $color = 'success';
                                                } elseif ($note->menuroles == 'clerk') {
                                                    $color = 'primary';
                                                } elseif ($note->menuroles == 'lawyer') {
                                                    $color = 'info';
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td><span style="padding-left: 10px;"
                                                            class="text-{{ $color }}"><b>{{ $note->user_name }}</b></span>
                                                    </td>
                                                    <td>{!! $note->notes !!}</td>
                                                    <td class=""><a target="_blank"
                                                            href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                                class="cil-arrow-right"></i></a></td>
                                                    <td>{{ $note->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                        @endif

                                    </tbody>
                                </table>

                                <table id="table_notes_week"
                                    class="table table-responsive-sm table-hover table-outline mb-0 "
                                    style="display:none;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Message</th>
                                            <th class="text-center">File Ref</th>
                                            <th class="text-center">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (count($kiv_note))
                                            @foreach ($kiv_note as $index => $note)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td>{{ $note->name }}</td>
                                                    <td>{!! $note->notes !!}</td>
                                                    <td class=""><a target="_blank"
                                                            href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                                class="cil-arrow-right"></i></a></td>
                                                    <td>{{ $note->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                        @endif

                                    </tbody>
                                </table>

                                <table id="table_notes_today"
                                    class="table table-responsive-sm table-hover table-outline mb-0 "
                                    style="display:none;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Message</th>
                                            <th class="text-center">File Ref</th>
                                            <th class="text-center">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if (count($kiv_note))
                                            @foreach ($kiv_note as $index => $note)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td>{{ $note->name }}</td>
                                                    <td>{!! $note->notes !!}</td>
                                                    <td class=""><a target="_blank"
                                                            href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                                class="cil-arrow-right"></i></a></td>
                                                    <td>{{ $note->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    @endif

    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><strong>Uploaded Attachment</strong></div>
                        <div class="card-body" style="max-height:600px;overflow:scroll">
                            <!-- /.row--><br>
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>User</th>
                                        <th class="text-center">Ref No</th>
                                        <th class="text-center">File</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Date</th>
                                        <!-- <th class="text-center">Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>

                                    @if (count($case_file))
                                        @foreach ($case_file as $index => $file)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="">
                                                    <div>{{ $file->user_name }}</div>
                                                </td>
                                                <td class=""><a target="_blank"
                                                        href="/case/{{ $file->case_id }}">{{ $file->case_ref_no }} <i
                                                            class="cil-arrow-right"></i></a></td>
                                                <td class=""><a target="_blank" href="/{{ $file->filename }}"><i
                                                            class="cil-paperclip"></i> {{ $file->display_name }}</a></td>
                                                <td class="text-center">
                                                    @if ($file->attachment_type == 1)
                                                        <span
                                                            class=" badge badge-pill badge-warning">Correspondences</span>
                                                    @elseif($file->attachment_type == 2)
                                                        <span class=" badge badge-pill badge-info">Documents</span>
                                                    @elseif($file->attachment_type == 3)
                                                        <span class=" badge badge-pill badge-success">Receipt</span>
                                                    @elseif($file->attachment_type == 4)
                                                        <span class=" badge badge-pill badge-danger">Adjudicate</span>
                                                    @elseif($file->attachment_type == 5)
                                                        <span class=" badge badge-pill bg-question">Marketing</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="">{{ $file->created_at }}</td>
                                                <!-- <td class="text-center">
                              <a target="_blank" href="kpi_list/{{ $file->id }}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
                            </td> -->

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No receipt</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($current_user->menuroles == 'lawyer' ||
        $current_user->menuroles == 'clerk' ||
        $current_user->menuroles == 'sales' ||
        $current_user->menuroles == 'chambering')
        <div class="container-fluid">
            <div class="fade-in">
                <!-- @if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'clerk')
    <div class="row">
                      <div class="col-sm-6 col-lg-4">
                        <div class="card mb-4" style="--cui-card-cap-bg: #4875b4">
                          <div class="card-header position-relative d-flex justify-content-center align-items-center">
                           
                            <div class=" w-100 text-center">
                              <h2 >KPI ({{ $current_user->name }})</h2>
                            </div>
                          </div>
                          <div class="card-body row text-center">
                            <div class="col">
                              <div class="fs-5 fw-semibold"><b>{{ $current_user->kpi_get }}</b></div>
                              <div class="text-uppercase text-medium-emphasis small">Point Collected</div>
                            </div>
                            <div class="vr"></div>
                            <div class="col">
                              <div class="fs-5 fw-semibold"><b>{{ $current_user->kpi_miss }}</b></div>
                              <div class="text-uppercase text-medium-emphasis small">Point failed to collect</div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
    @endif -->



            </div>
        </div>
    @endif



    @if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'clerk')
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><strong> To Do List</strong></div>
                            <div class="card-body">
                                <div>
                                    <h4 class="card-title mb-0">Good day</h4>
                                    <div class="small text-medium-emphasis">Today to do list</div>
                                </div>
                                <br>


                                <div class="box-body no-padding " style="width:100%;overflow-x:auto">

                                    <table id="tbl-todo-yadra" class="table table-bordered table-striped yajra-datatable"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Case Ref No</th>
                                                <th>Checklist</th>
                                                <th>Target Close date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /.col-->
                </div>
            </div>
        </div>
    @endif



    @if ($current_user->menuroles == 'receptionist')
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="card">
                            <div class="card-header">
                                <h4>Search Case</h4>
                            </div>
                            <div class="card-body">
                                <form id="form_search" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="name">Case Ref No</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" id="case_ref_no" name="case_ref_no"
                                                        value="" type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="name">Client name</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" id="name" name="name"
                                                        value="" type="text" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="name">Phone No</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" id="tel_no" name="tel_no"
                                                        value="" type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="name">IC</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" id="ic" name="ic"
                                                        value="" type="text" />
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <button class="btn btn-info float-right" onclick="searchCase()"
                                        type="button">Search</button>
                                    <button class="btn btn-danger " onclick="clearForm()" type="button">Clear
                                        filter</button>
                                </form>
                            </div>
                        </div>




                        <div class="card" style="width:100%;overflow-x:auto">
                            <div class="card-header">
                                <h4>Cases</h4>
                            </div>
                            <div class="card-body">
                                @if (Session::has('message'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>{{ Session::get('message') }}
                                            <button class="close" type="button" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                    </div>
                                @endif


                                <div class="row">

                                    <!-- <div class="col-3">
                                <div class="form-group  ">
                                  <div class="input-group">
                                    <input type="text" name="case_ref_no_search" id="case_ref_no_search" class="form-control" placeholder="Search case">
                                    <span class="input-group-btn">
                                      <button type="button" onclick="searchCase()" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                      </button>
                                    </span>
                                  </div>
                                </div>
                              </div>

                              <div class="col-3 ">
                                <div class="form-group  ">
                                  <a class="btn btn-lg btn-info" href="javascript:void(0)" onclick="clearSearch()">Clear search</a>
                                </div>
                              </div>
                            </div> -->

                                    <br>
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>No </th>
                                                <th>Case Number <a href=""
                                                        class="btn btn-info btn-xs rounded shadow  mr-1"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></th>
                                                <!-- <th>Type</th> -->
                                                <th>Client</th>
                                                <th>Client's Tel No</th>
                                                <th>Lawyer</th>
                                                <th>Clerk</th>
                                                <th>Property Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl-data">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif



@endsection

@section('javascript')
    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script>
        var table;
    </script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        // var xValues = [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000];

        // new Chart("myChart", {
        //   type: "line",
        //   data: {
        //     labels: xValues,
        //     datasets: [{
        //       data: [860, 1140, 1060, 1060, 1070, 1110, 1330, 2210, 7830, 2478],
        //       borderColor: "red",
        //       fill: false
        //     }, {
        //       data: [1600, 1700, 1700, 1900, 2000, 2700, 4000, 5000, 6000, 7000],
        //       borderColor: "green",
        //       fill: false
        //     }, {
        //       data: [300, 700, 2000, 5000, 6000, 4000, 2000, 1000, 200, 100],
        //       borderColor: "blue",
        //       fill: false
        //     }]
        //   },
        //   options: {
        //     legend: {
        //       display: false
        //     }
        //   }
        // });
    </script>
    <script type="text/javascript">
        $(".btn-filter-date").click(function() {
            $(".btn-filter-date").removeClass('btn-selected');
            $("#" + this.id).addClass('btn-selected');


        });

        function viewCaseChartByBranch() {
            $("#chart_cases_branch").show();
            $("#chart_cases_all").hide();
            // $("#caseCountChart").height(329);

            // var canvas = document.getElementById('caseCountChart'); 
            // canvas.style.width = 329;
        }

        function viewCaseChartByAll() {
            $("#chart_cases_branch").hide();
            $("#chart_cases_all").show();
        }

        function getDashBoardCaseCount() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: 'getDashboardCaseCount',
                processData: false,
                contentType: false,
                success: function(result) {

                    var xValues = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ];
                    var puchongValue = [];
                    var uptownValue = [];
                    var DesaParkValue = [];


                    console.log(result);

                    new Chart("caseCountChart", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                data: result.data[0].count,
                                label: 'Uptown',
                                borderColor: "blue",
                                backgroundColor: "blue",
                                fill: false
                            }, {
                                data: result.data[1].count,
                                label: 'Puchong',
                                borderColor: "green",
                                backgroundColor: "green",
                                fill: false
                            }, {
                                data: result.data[2].count,
                                label: 'DPC',
                                borderColor: "purple",
                                backgroundColor: "purple",
                                fill: false
                            }, ]
                        },
                        options: {
                            legend: {
                                display: true
                            }
                        }
                    });

                    new Chart("caseCountChartAll", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                data: result.data[3].count,
                                label: 'Cases',
                                borderColor: "green",
                                backgroundColor: "green",
                                fill: false
                            }, ]
                        },
                        options: {
                            legend: {
                                display: true
                            }
                        }
                    });
                }
            });

            return;


        }


        $(function() {

            getDashBoardCaseCount();

            table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user_todo_list.list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'target_close_date',
                        name: 'target_close_date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                ]
            });


        });
    </script>


    <script>
        function searchCase() {
            var form_data = new FormData();

            if ($("#case_ref_no").val() != "") {
                form_data.append("case_ref_no", $("#case_ref_no").val());
            }

            if ($("#tel_no").val() != "") {
                form_data.append("tel_no", $("#tel_no").val());
            }

            if ($("#name").val() != "") {
                form_data.append("name", $("#name").val());
            }

            if ($("#ic").val() != "") {
                form_data.append("ic", $("#ic").val());
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });





            $.ajax({
                type: 'POST',
                url: 'search_case',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#tbl-data').html(data.view);
                    // $('ul.pagination').replaceWith(data.links); 
                }
            });
        }



        function clearForm() {
            document.getElementById("form_search").reset();
        }
    </script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/coreui-chartjs.js') }}"></script>
    <script src="{{ asset('js/main.js?00001') }}"></script>
@endsection
