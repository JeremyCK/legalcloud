@extends('dashboard.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Bill Details</h4>
                        <input class="form-control" type="hidden" value="" id="selected_bill_id" name="selected_bill_id">
                        <input class="form-control" type="hidden" value="" id="main_case_id" name="main_case_id" value="{{ $case->id }}">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">


                                <div class="form-group row">
                                    <div class="col-md-4"><b>Case Ref Number</b></div>
                                    <div class="col-md-8 ">{{ $case->case_ref_no }} <a href="" class="btn btn-info btn-xs rounded shadow  mr-1" data-toggle="tooltip" data-placement="top" title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Start Date</b></div>
                                    <div class="col-md-8 ">{{ $case->created_at }}</div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Completion Date</b></div>
                                    <div class="col-md-8 ">-</div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Client</b></div>
                                    <div class="col-md-8 ">{{ $customer->name }}</b></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Client contact No</b></div>
                                    <div class="col-md-8 ">{{ $customer->phone_no }}</b></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Client IC</b></div>
                                    <div class="col-md-8 ">{{ $customer->ic_no }}</b></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Client email</b></div>
                                    <div class="col-md-8 ">{{ $customer->email }}</b></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Client address</b></div>
                                    <div class="col-md-8 ">{{ $customer->address }}</b></div>
                                </div>


                            </div>

                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Purchase Price</b> </div>
                                    <div class="col-md-8 ">RM {{ number_format($case->purchase_price, 2, '.', ',') }} </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Loan Sum</b> </div>
                                    <div class="col-md-8 ">RM {{ number_format($case->loan_sum, 2, '.', ',') }} </div>
                                </div>




                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Case Type</b></div>
                                    <div class="col-md-8 ">{{ $case->portfolio }}</b></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Percentage Completion</b></div>
                                    <div class="col-md-8 ">
                                        {{ $case->percentage }} %
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $case->percentage }}%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 "><b>Status</b></div>
                                    <div class="col-md-8 ">
                                        @if($case->status == 2)
                                        <span class="badge badge-info">Open</span>
                                        @elseif($case->status == 0)
                                        <span class="badge badge-success">Closed</span>
                                        @elseif($case->status == 1)
                                        <span class="badge bg-purple">In progress</span>
                                        @elseif($case->status == 2)
                                        <span class="badge badge-danger">Overdue</span>
                                        @elseif($case->status == 3)
                                        <span class="badge badge-warning">KIV</span>
                                        @elseif($case->status == 99)
                                        <span class="badge badge-danger">Aborted</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 ">
                                <div class="form-group row">
                                    <div class="col-md-2 "><b>Remark </b></div>
                                    <div class="col-md-10 ">{{ $case->remark }} </div>
                                </div>


                            </div>
                            <div class="col-sm-12">
                                <b>Person in charge</b>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link " data-toggle="tab" href="#messages-1" role="tab" aria-controls="messages" aria-selected="true">Bills</a></li>
                            </ul>
                            <div class="tab-content">


                                <div class="tab-pane active" id="messages-1" role="tabpanel" style="width:100%;overflow-x:auto">
                                    @include('dashboard.bill.tabs.tab-bill')
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')


@endsection