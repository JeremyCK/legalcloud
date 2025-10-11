@extends('dashboard.base')

@section('css')
    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
@endsection

@section('content')


    <div class="container-fluid">
        <div class="fade-in">

            
            @if (!in_array($current_user->menuroles, ['receptionist']))
            
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>Legalcloud cases</strong>
                        </div>
                        <div class="card-body">

                            <div class="row">

                                <div class="col-4 date_option date_option_year">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Year</label>
                                            <select class="form-control" id="ddl_year_case"
                                                onchange="getDashBoardCaseCount()">
                                                <option value="0">-- All --</option>
                                                <option value="2022">2022</option>
                                                <option value='2023' >2023</option>
                                                <option value='2024' >2024</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4 date_option date_option_month">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Month</label>
                                            <select class="form-control ddl_month" id="ddl_month_case" 
                                                name="ddl_month" onchange="getDashBoardCaseCount()">
                                                <option value="0">-- All --</option>
                                                <option value="1">January</option>
                                                <option value='2'>February</option>
                                                <option value='3'>March</option>
                                                <option value='4'>April</option>
                                                <option value='5'>May</option>
                                                <option value='6'>June</option>
                                                <option value='7'>July</option>
                                                <option value='8'>August</option>
                                                <option value='9'>September</option>
                                                <option value='10'>October</option>
                                                <option value='11'>November</option>
                                                <option value='12'>December</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                               
                                    <div class="col-4 date_option @if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) hide  @endif"  >
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control"  id="ddl_branch_case" 
                                                    onchange="getDashBoardCaseCount()">
                                                    <option value="0">-- All --</option>
                                                    @foreach ($Branch as $index => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                

                              

                            </div>

                            <div class="row" id="div-case-count">
                                @include('dashboard.dashboard.dashboard-legal-case')
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>

           
           
            @endif

            @if (in_array($current_user->menuroles, ['admin', 'account']))
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>Case Before 2022</strong>
                        </div>
                        <div class="card-body">

                            <div class="row" id="div-case-count">
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
                                    <div class="card text-white bg-purple">
                                        <div class="card-body pb-0" style="padding-bottom:30px !important;">
            
                                            <div class="text-value-lg">{{ $B2022PendingCloseCases }}</div>
                                            <div>Total Pending Close Cases</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            @endif
            
            {{-- <div class="row">
                @if (!in_array($current_user->menuroles, ['receptionist']))
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
                        <div class="card text-white bg-purple">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                <div class="btn-group float-right">
                                </div>
                                <div class="text-value-lg">{{ $OverdueCaseCount }}</div>
                                <div>Total Pending Close Cases</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array($current_user->menuroles, ['admin', 'account']))
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
                        <div class="card text-white bg-purple">
                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                <div class="text-value-lg">{{ $B2022PendingCloseCases }}</div>
                                <div>Total Pending Close Cases</div>
                            </div>
                        </div>
                    </div>
                @endif


            </div> --}}

            {{-- @if (in_array($current_user->menuroles, ['admin', 'management'])) --}}
            @if (in_array($current_user->id, [1,2,37]))
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Monthly Report</strong>
                            </div>
                            <div class="card-body">

                                <div class="row">

                                    <div class="col-4 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Year</label>
                                                <select class="form-control" id="ddl_year_report" name="ddl_year"
                                                    onchange="reloadDashBoardReport()">
                                                    <option value="2022">2022</option>
                                                    <option value='2023' >2023</option>
                                                    <option value='2024' selected>2024</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4 date_option date_option_month">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Month</label>
                                                <select class="form-control ddl_month_all" id="ddl_month_report"
                                                    name="ddl_month" onchange="reloadDashBoardReport()">
                                                    <option value="1">January</option>
                                                    <option value='2'>February</option>
                                                    <option value='3'>March</option>
                                                    <option value='4'>April</option>
                                                    <option value='5'>May</option>
                                                    <option value='6'>June</option>
                                                    <option value='7'>July</option>
                                                    <option value='8'>August</option>
                                                    <option value='9'>September</option>
                                                    <option value='10'>October</option>
                                                    <option value='11'>November</option>
                                                    <option value='12'>December</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4 date_option ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" id="ddl_branch" name="ddl_branch"
                                                    onchange="reloadDashBoardReport()">
                                                    <option value="0">-- All --</option>
                                                    @foreach ($Branch as $index => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white bg-success">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                                <div class="btn-group float-right">
                                                </div>
                                                <div id="txt_total_receipt" class="text-value-lg">0.00</div>
                                                <div>Total Receipt</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3 hide">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                                <div class="btn-group float-right">
                                                </div>
                                                <div id="txt_total_trust" class="text-value-lg">0.00</div>
                                                <div>Total Trust Receive</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white " style="background-color:orange">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_balance_disb" class="text-value-lg">0.00</div>
                                                {{-- <div>Balance Disbursement</div> --}}
                                                <div>Disbursement</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white bg-info">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_actual_bal" class="text-value-lg">0.00</div>
                                                <div>Prof Fees</div>
                                                {{-- <div>Actual Balance</div> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white " style="background-color: #009688">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_sst" class="text-value-lg">0.00</div>
                                                <div>SST</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white "  style="background-color:coral">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_total_check" class="text-value-lg">0.00</div>
                                                <div>Total Check</div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white "  style="background-color:coral">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_SumBonus3Per" class="text-value-lg">0.00</div>
                                                <div>Total Bonus Approved (2%)</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white "  style="background-color:blueviolet">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_SumBonus5Per" class="text-value-lg">0.00</div>
                                                <div>Total Bonus Approved (3%)</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white "  style="background-color:rgb(226, 43, 104)">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_closefile_bal" class="text-value-lg">0.00</div>
                                                <div>Close File Balance</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Quotation Report</strong>
                            </div>
                            <div class="card-body">

                                <div class="row">

                                    {{-- <div class="col-4 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Year</label>
                                                <select class="form-control" id="ddl_year_report" name="ddl_year"
                                                    onchange="reloadDashBoardReport()">
                                                    <option value="2022">2022</option>
                                                    <option value='2023' selected>2023</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4 date_option date_option_month">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Month</label>
                                                <select class="form-control ddl_month_all" id="ddl_month_report"
                                                    name="ddl_month" onchange="reloadDashBoardReport()">
                                                    <option value="1">January</option>
                                                    <option value='2'>February</option>
                                                    <option value='3'>March</option>
                                                    <option value='4'>April</option>
                                                    <option value='5'>May</option>
                                                    <option value='6'>June</option>
                                                    <option value='7'>July</option>
                                                    <option value='8'>August</option>
                                                    <option value='9'>September</option>
                                                    <option value='10'>October</option>
                                                    <option value='11'>November</option>
                                                    <option value='12'>December</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4 date_option ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" id="ddl_branch" name="ddl_branch"
                                                    onchange="reloadDashBoardReport()">
                                                    <option value="0">-- All --</option>
                                                    @foreach ($Branch as $index => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div> --}}

                                </div>

                                <div class="row">
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white bg-success">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                                <div class="btn-group float-right">
                                                </div>
                                                <div id="txt_total_receipt_q" class="text-value-lg">0.00</div>
                                                <div>Total Receipt</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3 hide">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">
                                                <div class="btn-group float-right">
                                                </div>
                                                <div id="txt_total_trust" class="text-value-lg">0.00</div>
                                                <div>Total Trust Receive</div>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-sm-6 col-lg-3 hide">
                                        <div class="card text-white " style="background-color:orange">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_balance_disb_q" class="text-value-lg">0.00</div>
                                                <div>Balance Disbursement</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3 hide">
                                        <div class="card text-white bg-info">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_actual_bal_q" class="text-value-lg">0.00</div>
                                                <div>Actual Balance</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white "  style="background-color:coral">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_SumBonus3Per" class="text-value-lg">0.00</div>
                                                <div>Total Bonus Approved (3%)</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3">
                                        <div class="card text-white "  style="background-color:blueviolet">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_SumBonus5Per" class="text-value-lg">0.00</div>
                                                <div>Total Bonus Approved (5%)</div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-sm-6 col-lg-3 hide">
                                        <div class="card text-white " style="background-color:red">
                                            <div class="card-body pb-0" style="padding-bottom:30px !important;">

                                                <div id="txt_uncollected" class="text-value-lg">0.00</div>
                                                <div>Uncollected</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">




                @if (in_array($current_user->menuroles, ['admin', 'management']) || in_array($current_user->id, [80, 51, 32, 37]))
                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>All cases</strong>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-6 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Year</label>
                                                <select class="form-control" id="ddl_year" name="ddl_year"
                                                    onchange="reloadDashBoardCaseCount()">
                                                    <option value="2022">2022</option>
                                                    <option value='2023' >2023</option>
                                                    <option value='2024' selected>2024</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="div-chart div-chart2023">
                                    <div id="chart_cases_all" class="c-chart-wrapper c-chart-wrapper2023 mt-3 mx-3"
                                        style="height: 400px">
                                        <canvas height="392" class="chart" id="caseCountChartAll"></canvas>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array($current_user->menuroles, ['admin', 'management']))
                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Cases by branch (2023)</strong>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-6 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Year</label>
                                                <select class="form-control" id="ddl_year_branch" name="ddl_year"
                                                    onchange="reloadDashBoardCaseCountByBranch()">
                                                    <option value="2022">2022</option>
                                                    <option value='2023' >2023</option>
                                                    <option value='2024' selected>2024</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="div-chart div-chart2022">
                                    <div id="chart_cases_branch" class="c-chart-wrapper c-chart-wrapper2022 mt-3 mx-3"
                                        style="height: 400px;">
                                        <canvas height="392" class="chart" id="caseCountChartBranch"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array($current_user->menuroles, ['admin', 'management']) || in_array($current_user->id, [80, 51, 32]))
                    <div class="col-xl-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Sales </strong>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-6 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Year</label>
                                                <select class="form-control ddl_year_all" id="ddl_year_sales"
                                                    name="ddl_year" onchange="reloadDashBoardCaseCountBySales()">
                                                    <option value="2022">2022</option>
                                                    <option value='2023'>2023</option>
                                                    <option value='2024' >2024</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 date_option date_option_month">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Month</label>
                                                <select class="form-control ddl_month_all" id="ddl_month_sales"
                                                    name="ddl_month" onchange="reloadDashBoardCaseCountBySales()">
                                                    <option value="1">January</option>
                                                    <option value='2'>February</option>
                                                    <option value='3'>March</option>
                                                    <option value='4'>April</option>
                                                    <option value='5'>May</option>
                                                    <option value='6'>June</option>
                                                    <option value='7'>July</option>
                                                    <option value='8'>August</option>
                                                    <option value='9'>September</option>
                                                    <option value='10'>October</option>
                                                    <option value='11'>November</option>
                                                    <option value='12'>December</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="div-chart div-chart2022">
                                    <div id="chart_cases_branch" class="c-chart-wrapper c-chart-wrapper2022 mt-3 mx-3"
                                        style="height: 400px;">
                                        <canvas height="392" class="chart" id="caseCountChartSales"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (in_array($current_user->menuroles, ['admin', 'management', 'sales']) ||
                        in_array($current_user->id, [14, 80, 51, 32, 89]))
                    {{-- @if (in_array($current_user->menuroles, ['admin', 'management']) || in_array($current_user->id, [80])) --}}
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Case(s) handling by staff</strong>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-3 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Year</label>
                                                <select class="form-control" id="ddl_year_staff" name="ddl_year"
                                                    onchange="reloadDashBoardCaseCountByStaff()">
                                                    <option value="2022">2022</option>
                                                    <option value='2023' >2023</option>
                                                    <option value='2024' selected>2024</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-3 date_option date_option_month">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Month</label>
                                                <select class="form-control ddl_month_all" id="ddl_month_staff"
                                                    name="ddl_month" onchange="reloadDashBoardCaseCountByStaff()">
                                                    <option value="1">January</option>
                                                    <option value='2'>February</option>
                                                    <option value='3'>March</option>
                                                    <option value='4'>April</option>
                                                    <option value='5'>May</option>
                                                    <option value='6'>June</option>
                                                    <option value='7'>July</option>
                                                    <option value='8'>August</option>
                                                    <option value='9'>September</option>
                                                    <option value='10'>October</option>
                                                    <option value='11'>November</option>
                                                    <option value='12'>December</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-3 date_option date_option_year">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Role</label>
                                                <select class="form-control" id="ddl_role_staff" name="ddl_year"
                                                    onchange="reloadDashBoardCaseCountByStaff()">
                                                    <option value="all" selected>All</option>
                                                    <option value="lawyer">Lawyer</option>
                                                    <option value='clerk'>Clerk</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-3 date_option ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" id="ddl_branch_staff" name="ddl_branch_staff"
                                                    onchange="reloadDashBoardCaseCountByStaff()">
                                                    <option value="0">-- All --</option>
                                                    @foreach ($Branch as $index => $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="div-chart div-chart2022">
                                    <div id="chart_cases_branch" class="c-chart-wrapper c-chart-wrapper2022 mt-3 mx-3"
                                        style="height: 400px;">
                                        <canvas height="392" class="chart" id="caseCountChartStaff"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- @if ($current_user->menuroles == 'admin') --}}
            @if (in_array($current_user->menuroles, ['admin']) || in_array($current_user->id, [80]))





                {{-- @if (in_array($current_user->id, [1]))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <strong>Bonus Request</strong> <span class=" badge badge-pill badge-danger">Total bonus
                                        request: {{ count($BonusRequestList) }}</span>
                                </div>
                                <div class="card-body" style="max-height:600px;overflow:scroll">
                                    <br>
                                    <table id="table_notes_month"
                                        class="table table-responsive-sm table-striped table-hover  mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Requester</th>
                                                <th class="text-center">Ref No</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Request Date</th>
                                            </tr>
                                        </thead>s
                                        <tbody>

                                            @if (count($BonusRequestList))
                                                @foreach ($BonusRequestList as $index => $row)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $index + 1 }}
                                                        </td>
                                                        <td class="text-center">{{ $row->user_name }} </td>
                                                        <td class="text-center"><a target="_blank"
                                                                href="/case/{{ $row->case_id }}">{{ $row->case_ref_no }}
                                                                <i class="cil-arrow-right"></i></a></td>
                                                        <td class="text-center">
                                                            @if ($row->status == 1)
                                                                <span class=" badge badge-pill badge-warning">Pending
                                                                    Review</span>
                                                            @else
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            {{ date('d-m-Y h:i A', strtotime($row->created_at)) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="5">No request</td>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif --}}



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





    @if (!in_array($current_user->id, [2, 3]))
        @if (count($kiv_note))
            <div class="container-fluid">
                <div class="fade-in">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <strong>Notes</strong> <span class=" badge badge-pill badge-danger">Today new messages:
                                        {{ $today_message_count }}</span>
                                    {{-- <a href="javacript:void(0)" id="btn-filter-month"
                                        class="btn btn-filter-date pull-right btn-selected">Month</a>
                                    <a href="javacript:void(0)" id="btn-filter-week"
                                        class="btn btn-filter-date pull-right">Week</a>
                                    <a href="javacript:void(0)" id="btn-filter-today"
                                        class="btn btn-filter-date pull-right">Today</a> --}}
                                </div>
                                <div class="card-body" style="max-height:600px;overflow:scroll">
                                    <br>
                                    <table id="table_notes_month"
                                        class="table table-responsive-sm table-striped table-hover  mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th class="text-center">User</th>
                                                <th class="text-center">Message</th>
                                                {{-- <th class="text-center">File Ref</th>
                                                <th class="text-center">Date</th> --}}
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
                                                        <td><span style=""
                                                                class="text-{{ $color }}"><b>{{ $note->user_name }}</b></span><br /><br />
                                                            <b>Ref No: </b><br />
                                                            <a target="_blank"
                                                                href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }}
                                                                <i class="cil-arrow-right"></i></a><br />
                                                            <span class="small">
                                                                {{ date('d-m-Y h:i A', strtotime($note->created_at)) }}
                                                            </span>
                                                        </td>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $message = $note->notes;
                                                                if ($note->label == 'operation|dispatch')
                                                                {
                                                                    $prefix = '<a target="_blank" href="/app/documents/dispatch/';

                                                                    $postfix = '" class="mailbox-attachment-name"';
                                                                    $replace = '<a href="javascript:void(0)" onclick="openFileFromS3(\'';
                                                                    $replace2 = '\')"  class="mailbox-attachment-name"';

                                                                    if (!str_contains($message , '<a target="_blank" href="/app/documents/dispatch/dispatch/'))
                                                                    {
                                                                        $prefix = '<a target="_blank" href="/app/documents/';
                                                                    }

                                                                    $message = str_replace($prefix,$replace,$message);
                                                                    $message = str_replace($postfix,$replace2,$message);
                                                                }
                                                                else  if ($note->label == 'operation|safekeeping')
                                                                {
                                                                    $prefix = '<a target="_blank" href="/app/documents/safe_keeping/';

                                                                    $postfix = '" class="mailbox-attachment-name"';
                                                                    $replace = '<a href="javascript:void(0)" onclick="openFileFromS3(\'';
                                                                    $replace2 = '\')"  class="mailbox-attachment-name"';

                                                                    $message = str_replace($prefix,$replace,$message);
                                                                    $message = str_replace($postfix,$replace2,$message);
                                                                }
                                                                else  if ($note->label == 'operation|landoffice')
                                                                {
                                                                    $prefix = '<a target="_blank" href="/app/documents/land_office/';

                                                                    $postfix = '" class="mailbox-attachment-name"';
                                                                    $replace = '<a href="javascript:void(0)" onclick="openFileFromS3(\'';
                                                                    $replace2 = '\')"  class="mailbox-attachment-name"';

                                                                    $message = str_replace($prefix,$replace,$message);
                                                                    $message = str_replace($postfix,$replace2,$message);
                                                                }

                                                            @endphp
                                                            
                                                            {!! $message !!}
                                                           
                                                        </td>
                                                        {{-- <td class=""><a target="_blank"
                                                                href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                                    class="cil-arrow-right"></i></a></td>
                                                        <td>{{ $note->created_at }}</td> --}}
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
    @endif

    @if (in_array($current_user->menuroles, ['admin', 'account', 'sales', 'maker']))
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    @if (!in_array($current_user->id, [2]))
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <strong>Marketing Notes</strong> <span class=" badge badge-pill badge-danger">Past 7
                                        days
                                        messages:
                                        {{ count($LoanMarketingNotes) }}</span>
                                    {{-- <a href="javacript:void(0)" id="btn-filter-month"
                                    class="btn btn-filter-date pull-right btn-selected">Month</a>
                                <a href="javacript:void(0)" id="btn-filter-week"
                                    class="btn btn-filter-date pull-right">Week</a>
                                <a href="javacript:void(0)" id="btn-filter-today"
                                    class="btn btn-filter-date pull-right">Today</a> --}}
                                </div>
                                <div class="card-body" style="max-height:600px;overflow:scroll">
                                    <br>
                                    <table id="table_notes_month"
                                        class="table table-responsive-sm table-striped table-hover  mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th class="text-center">User</th>
                                                <th class="text-center">Message</th>
                                                {{-- <th class="text-center">File Ref</th>
                                            <th class="text-center">Date</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if (count($LoanMarketingNotes))
                                                @foreach ($LoanMarketingNotes as $index => $note)
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
                                                        <td><span style=""
                                                                class="text-{{ $color }}"><b>{{ $note->user_name }}</b></span><br /><br />
                                                            <b>Ref No: </b><br />
                                                            <a target="_blank"
                                                                href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }}
                                                                <i class="cil-arrow-right"></i></a><br />
                                                            <span class="small">
                                                                {{ date('d-m-Y h:i A', strtotime($note->created_at)) }}
                                                            </span>
                                                        </td>
                                                        </td>
                                                        <td>{!! $note->notes !!}</td>
                                                        {{-- <td class=""><a target="_blank"
                                                            href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                                class="cil-arrow-right"></i></a></td>
                                                    <td>{{ $note->created_at }}</td> --}}
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
                                                {{-- <th class="text-center">File Ref</th>
                                            <th class="text-center">Date</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if (count($kiv_note))
                                                @foreach ($kiv_note as $index => $note)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $index + 1 }}
                                                        </td>
                                                        <td>{{ $note->name }} <br />
                                                            <a target="_blank"
                                                                href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }}
                                                                <i class="cil-arrow-right"></i></a><br />
                                                            {{ $note->created_at }}
                                                        </td>
                                                        <td>{!! $note->notes !!}</td>
                                                        {{-- <td class=""><a target="_blank"
                                                            href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                                class="cil-arrow-right"></i></a></td> --}}
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
                                                                href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }}
                                                                <i class="cil-arrow-right"></i></a></td>
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
                    @endif

                    @if ($current_user->management == 1)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <strong>PNC Notes</strong> <span class=" badge badge-pill badge-danger">messages:
                                        {{ count($pnc_note) }}</span>
                                    {{-- <a href="javacript:void(0)" id="btn-filter-month"
                                class="btn btn-filter-date pull-right btn-selected">Month</a>
                            <a href="javacript:void(0)" id="btn-filter-week"
                                class="btn btn-filter-date pull-right">Week</a>
                            <a href="javacript:void(0)" id="btn-filter-today"
                                class="btn btn-filter-date pull-right">Today</a> --}}
                                </div>
                                <div class="card-body" style="max-height:600px;overflow:scroll">
                                    <br>
                                    <table id="table_notes_month"
                                        class="table table-responsive-sm table-striped table-hover  mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th class="text-center">User</th>
                                                <th class="text-center">Message</th>
                                                {{-- <th class="text-center">File Ref</th>
                                        <th class="text-center">Date</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if (count($pnc_note))
                                                @foreach ($pnc_note as $index => $note)
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
                                                        <td><span style=""
                                                                class="text-{{ $color }}"><b>{{ $note->user_name }}</b></span><br /><br />
                                                            <b>Ref No: </b><br />
                                                            <a target="_blank"
                                                                href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }}
                                                                <i class="cil-arrow-right"></i></a><br />
                                                            <span class="small">
                                                                {{ date('d-m-Y h:i A', strtotime($note->created_at)) }}
                                                            </span>
                                                        </td>
                                                        </td>
                                                        <td>{!! $note->notes !!}</td>
                                                        {{-- <td class=""><a target="_blank"
                                                        href="/case/{{ $note->case_id }}">{{ $note->case_ref_no }} <i
                                                            class="cil-arrow-right"></i></a></td>
                                                <td>{{ $note->created_at }}</td> --}}
                                                    </tr>
                                                @endforeach
                                            @else
                                            @endif

                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    @endif

    @if (!in_array($current_user->id, [2, 3]))
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6"><strong>Uploaded Attachment</strong> </div>
                                    @if (in_array($current_user->menuroles, ['admin', 'account']))
                                        <div class="col-6">
                                            <a class="btn btn-lg btn-info float-right" href="/files">View more<i
                                                    class="cil-arrow-right"> </i></a>
                                        </div>
                                    @endif

                                </div>


                            </div>
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
                                                    <td class="">

                                                        @if ($file->s3_file_name)
                                                            <a href="javascript:void(0)"
                                                                onclick="openFileFromS3('{{ $file->filename }}')"><i
                                                                    class="cil-paperclip"></i>
                                                                {{ $file->display_name }}</a>

                                                        @else
                                                            <a target="_blank" href="/{{ $file->filename }}"><i
                                                                    class="cil-paperclip"></i>
                                                                {{ $file->display_name }}</a>
                                                        @endif

                                                    </td>
                                                    <td class="text-center">
                                                        @if ($file->attachment_type == 1)
                                                            <span
                                                                class=" badge badge-pill badge-warning">Correspondences</span>
                                                        @elseif($file->attachment_type == 2)
                                                            <span class=" badge badge-pill badge-info">Documents</span>
                                                        @elseif($file->attachment_type == 3)
                                                            <span class=" badge badge-pill badge-success">Account
                                                                Receipt</span>
                                                        @elseif($file->attachment_type == 4)
                                                            <span class=" badge badge-pill badge-danger">Adjudicate</span>
                                                        @elseif($file->attachment_type == 5)
                                                            <span class=" badge badge-pill bg-question">Marketing</span>
                                                        @elseif($file->attachment_type == 6)
                                                            <span class=" badge badge-pill bg-question">Official
                                                                Receipt</span>
                                                        @elseif($file->attachment_type == 7)
                                                            <span class=" badge badge-pill bg-red">Other Receipt</span>
                                                        @elseif($file->attachment_type == 8)
                                                            <span class=" badge badge-pill bg-light-blue">Presentation
                                                                Receipt</span>
                                                        @else
                                                        <span class=" badge badge-pill " style="background-color: #ebedef">Payment Voucher</span>
                                                        @endif
                                                    </td>
                                                    <td class="">
                                                        {{-- {{ $file->created_at }} --}}
                                                        {{ date('d-m-Y h:i A', strtotime($file->created_at)) }}
                                                    </td>
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
        var chartAllCases = null;
        var chartAllCasesbyBranch = null;
        var chartAllCasesbyStaff = null;
        var chartAllCasesbySales = null;
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


        function filterDate(year) {
            $(".div_year ").hide();
            $(".div_" + year).show();
        }


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

            var form_data = new FormData();

            form_data.append("month", $("#ddl_month_case").val());
            form_data.append("year", $("#ddl_year_case").val());
            form_data.append("branch", $("#ddl_branch_case").val());

            $.ajax({
                type: 'POST',
                url: 'getDashboardCaseCount',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                    $("#div-case-count").html(result.view);

                    return 1;

                    var xValues = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ];
                    var puchongValue = [];
                    var uptownValue = [];
                    var DesaParkValue = [];


                    console.log(result);

                    @if ($current_user->id == 80)
                        new Chart("caseCountChartAll", {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    data: result.data[1].count,
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
                    @else
                        chartAllCases = new Chart("caseCountChartAll", {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    data: result.data[5].count,
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
                    @endif



                    chartAllCasesbyBranch = new Chart("caseCountChart", {
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
                            }, {
                                data: result.data[3].count,
                                label: 'Rama',
                                borderColor: "red",
                                backgroundColor: "red",
                                fill: false
                            }, {
                                data: result.data[4].count,
                                label: 'Dataran Prima',
                                borderColor: "yellow",
                                backgroundColor: "yellow",
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

        async function reloadDashBoardCaseCount() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("year", $("#ddl_year").val());

            $.ajax({
                type: 'POST',
                url: 'getDashboardCaseChart',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                    var xValues = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ];
                    var puchongValue = [];
                    var uptownValue = [];
                    var DesaParkValue = [];

                    console.log(result);

                    if (chartAllCases == null) {
                        chartAllCases = new Chart("caseCountChartAll", {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    data: result.data[0].count,
                                    label: 'Cases',
                                    borderColor: "green",
                                    backgroundColor: "green",
                                    fill: false
                                }, ]
                            },
                            options: {
                                legend: {
                                    display: true
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    } else {
                        chartAllCases.data.datasets[0].data = result.data[0].count;
                        chartAllCases.update();
                    }

                }
            });

            return;
        }

        async function reloadDashBoardReport() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("year", $("#ddl_year_report").val());
            form_data.append("month", $("#ddl_month_report").val());
            form_data.append("branch", $("#ddl_branch").val());

            $.ajax({
                type: 'POST',
                url: 'getDashboardReport',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                   console.log(result);

                   $('#txt_total_receipt').html( "RM " + numberWithCommas(result.total_receive_inv))
                   $('#txt_total_receipt_q').html( "RM " + numberWithCommas(result.total_receive_q.toFixed(2)));;
                   $('#txt_uncollected').html( "RM " + numberWithCommas(result.uncollected));
                //    $('#txt_balance_disb').html( "RM " + numberWithCommas(result.bal_disb.toFixed(2)));
                   $('#txt_balance_disb').html( "RM " + numberWithCommas(result.bal_disb));
                   $('#txt_sst').html( "RM " + numberWithCommas(result.total_sst));
                   $('#txt_total_check').html( "RM " + numberWithCommas((result.total_check.toFixed(2))));
                   $('#txt_actual_bal').html( "RM " + numberWithCommas(result.actual_bal.toFixed(2)));
                   $('#txt_actual_bal_q').html( "RM " + numberWithCommas(result.actual_bal_q.toFixed(2)));
                   $('#txt_balance_disb_q').html( "RM " + numberWithCommas(result.bal_disb_q.toFixed(2)));
                   $('#txt_SumBonus3Per').html( "RM " + numberWithCommas(result.SumBonus3Per));
                   $('#txt_SumBonus5Per').html( "RM " + numberWithCommas(result.SumBonus5Per));
                   $('#txt_closefile_bal').html( "RM " + numberWithCommas(result.close_file_bal.toFixed(2)));
                //    $('#txt_SumBonus3Per').html( "RM " + numberWithCommas(result.total_staff_bonus_2_per));
                //    $('#txt_SumBonus5Per').html( "RM " + numberWithCommas(result.total_staff_bonus_3_per));
                   $('#txt_total_trust').html( "RM " + numberWithCommas(result.total_trust_receive));

                }
            });

            return;
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        async function reloadDashBoardCaseCountByBranch() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("year", $("#ddl_year_branch").val());

            $.ajax({
                type: 'POST',
                url: 'getDashboardCaseChartByBranch',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                    var xValues = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ];

                    if (chartAllCasesbyBranch == null) {
                        chartAllCasesbyBranch = new Chart("caseCountChartBranch", {
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
                                }, {
                                    data: result.data[3].count,
                                    label: 'Rama',
                                    borderColor: "red",
                                    backgroundColor: "red",
                                    fill: false
                                }, {
                                    data: result.data[4].count,
                                    label: 'Dataran Prima',
                                    borderColor: "yellow",
                                    backgroundColor: "yellow",
                                    fill: false
                                }, {
                                    data: result.data[5].count,
                                    label: 'Ismail',
                                    borderColor: "orange",
                                    backgroundColor: "orange",
                                    fill: false
                                }, ]
                            },
                            options: {
                                legend: {
                                    display: true
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    } else {
                        chartAllCasesbyBranch.data.datasets[0].data = result.data[0].count;
                        chartAllCasesbyBranch.data.datasets[1].data = result.data[1].count;
                        chartAllCasesbyBranch.data.datasets[2].data = result.data[2].count;
                        chartAllCasesbyBranch.data.datasets[3].data = result.data[3].count;
                        chartAllCasesbyBranch.update();
                    }



                }
            });

            return;


        }

        async function reloadDashBoardCaseCountByStaff() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("year", $("#ddl_year_staff").val());
            form_data.append("month", $("#ddl_month_staff").val());
            form_data.append("role", $("#ddl_role_staff").val());
            form_data.append("branch", $("#ddl_branch_staff").val());

            $.ajax({
                type: 'POST',
                url: 'getDashboardCaseChartByStaff',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                    var xValues = result.lawyerList;
                    var puchongValue = [];
                    var uptownValue = [];
                    var DesaParkValue = [];

                    if (chartAllCasesbyStaff == null) {
                        chartAllCasesbyStaff = new Chart("caseCountChartStaff", {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    data: result.lawyercount,
                                    label: 'Cases',
                                    borderColor: "blue",
                                    backgroundColor: "blue",
                                    fill: false
                                }, ]
                            },
                            options: {
                                legend: {
                                    display: true
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    } else {
                        chartAllCasesbyStaff.data.datasets[0].data = result.lawyercount;
                        chartAllCasesbyStaff.data.labels = xValues;
                        chartAllCasesbyStaff.update();
                    }



                }
            });

            return;


        }

        async function reloadDashBoardCaseCountBySales() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();

            form_data.append("year", $("#ddl_year_sales").val());
            form_data.append("month", $("#ddl_month_sales").val());

            $.ajax({
                type: 'POST',
                url: 'getDashboardCaseChartBySales',
                processData: false,
                contentType: false,
                data: form_data,
                success: function(result) {

                    var xValues = result.salesList;
                    console.log(result);

                    if (chartAllCasesbySales == null) {
                        chartAllCasesbySales = new Chart("caseCountChartSales", {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    data: result.caseCount,
                                    label: 'Cases',
                                    borderColor: "orange",
                                    backgroundColor: "orange",
                                    fill: false
                                }, ]
                            },
                            options: {
                                legend: {
                                    display: true
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    } else {
                        chartAllCasesbySales.data.datasets[0].data = result.caseCount;
                        chartAllCasesbySales.data.labels = xValues;
                        chartAllCasesbySales.update();
                    }



                }
            });

            return;


        }

        function openFileFromS3(filename) {
            var form_data = new FormData();

            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            //     }
            // });

            form_data.append("filename", filename);
            // form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

            $.ajax({
                type: 'POST',
                url: '/getFileFromS3',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    // console.log(data);
                    // window.open(data, "_blank")

                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                        window.location.href = data;
                    }
                    else
                    {
                        window.open(data, "_blank");
                    }
                    
                }
            });
        }

        // function openFileFromS3Test(filename) {
        //     var form_data = new FormData();

        //     // $.ajaxSetup({
        //     //     headers: {
        //     //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
        //     //     }
        //     // });

        //     form_data.append("filename", filename);
        //     // form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

        //     $.ajax({
        //         type: 'POST',
        //         url: '/getFileFromS3',
        //         data: form_data,
        //         processData: false,
        //         contentType: false,
        //         success: function(data) {
        //             // alert(data);
        //             // window.open(data);
        //             window.location.href = data;
        //         }
        //     });
        // }





        $(function() {

            const d = new Date();
            let month = d.getMonth() + 1;
            let year = d.getFullYear();

            $(".ddl_month_all").val(month);
            $(".ddl_year_all").val(year);

            if ($("#caseCountChartAll").is(":visible")) {
                // reloadDashBoardCaseCount();

                reloadDashBoardCaseCount().then(
                    function(value) {},
                    function(error) {}
                );
            }

            if ($("#caseCountChartBranch").is(":visible")) {
                // reloadDashBoardCaseCountByBranch();

                reloadDashBoardCaseCountByBranch().then(
                    function(value) {},
                    function(error) {}
                );
            }

            if ($("#caseCountChartStaff").is(":visible")) {
                // reloadDashBoardCaseCountByStaff();

                reloadDashBoardCaseCountByStaff().then(
                    function(value) {},
                    function(error) {}
                );
            }

            if ($("#caseCountChartSales").is(":visible")) {
                // reloadDashBoardCaseCountBySales();

                reloadDashBoardCaseCountBySales().then(
                    function(value) {},
                    function(error) {}
                );
            }

            if ($("#txt_total_receipt").is(":visible")) {
                // reloadDashBoardReport();

                reloadDashBoardReport().then(
                    function(value) {},
                    function(error) {}
                );
            }

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
