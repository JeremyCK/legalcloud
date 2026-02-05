@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<!-- <link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet"> -->
<!-- <link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet"> -->

<link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
@endsection
@extends('dashboard.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit office bank account</h4>
                    </div>
                    <div class="card-body">

                        <div class="box box-default">
                            <!-- /.box-header -->
                            <div class="box-body wizard-content">
                                <div class="tab-wizard wizard-circle wizard clearfix">

                                    <div class="tab-content bg-color-none">

                                        <div class="tab-pane active" id="tab1">
                                            <div class="">
                                                <div class="">
                                                    <form method="POST" action="{{ route('office-bank-account.update', $banks->id  ) }}">
                                                        @csrf

                                                        @method('PUT')
                                                        <div class="row">
                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Bank name</label>
                                                                        <input class="form-control" value="{{ $banks->name }}"  type="text" name="name" required>
                                                                    </div>
                                                                </div>


                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Tel No</label>
                                                                        <input class="form-control" value="{{ $banks->tel_no }}"  type="text" name="tel_no" >
                                                                    </div>
                                                                </div>

                                                                @if($current_user->branch_id != 3)

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Branch</label>
                                                                        <select class="form-control" name="branch_id">
                                                                            <option value="0">--Select Branch--</option>
                                                                            @foreach($branchs as $index => $branch)
                                                                            <option value="{{$branch->id}}" @if($branch->id == $banks->branch_id) selected @endif >{{$branch->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                @endif

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>{{ __('coreuiforms.notes.status') }}</label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="1" @if($banks->status == 1) selected @endif>Active</option>
                                                                            <option value="0" @if($banks->status == 0) selected @endif>Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div> 

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Remark</label>
                                                                        <textarea class="form-control" id="remark" name="remark" rows="3">{{ $banks->remark }}</textarea>
                                                                    </div>
                                                                </div>


                                                            </div>

                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Code</label>
                                                                        <input class="form-control" type="text" value="{{ $banks->short_code }}" name="short_code" required>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Account No</label>
                                                                        <input class="form-control" value="{{ $banks->account_no }}"  type="text" name="account_no" >
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Account Code</label>
                                                                        <select class="form-control" name="account_code">
                                                                            <option value="0">--Select account code--</option>
                                                                            @foreach($AccountCode as $index => $row)
                                                                            <option value="{{$row->id}}" @if($row->id == $banks->account_code) selected @endif >{{$row->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Opening Balance</label>
                                                                        <input class="form-control" value="{{ $banks->opening_balance }}"  type="number" name="opening_balance" >
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Opening Balance Date</label>
                                                                        <input class="form-control" type="date" name="opening_bal_date" value="{{ $banks->opening_bal_date }}" >
                                                                    </div>
                                                                </div>

                                                              

                                                            </div>
                                                        </div>


                                                        <button class="btn btn-primary float-right" type="submit">Save</button>
                                                        <a class="btn btn-danger" href="{{ route('office-bank-account.index') }}">Return</a>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.box-body -->
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