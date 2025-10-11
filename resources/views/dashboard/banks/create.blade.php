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
                        <h4>Create bank</h4>
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
                                                    <form method="POST" action="{{ route('banks.store') }}">
                                                        @csrf

                                                        <div class="row">
                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Bank name</label>
                                                                        <input class="form-control" type="text" name="name" required>
                                                                    </div>
                                                                </div>


                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Tel No</label>
                                                                        <input class="form-control" type="text" name="tel_no" required>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Remark</label>
                                                                        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>{{ __('coreuiforms.notes.status') }}</label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="1">Active</option>
                                                                            <option value="0">Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>


                                                            </div>

                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Code</label>
                                                                        <input class="form-control" type="text" name="short_code">
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Fax</label>
                                                                        <input class="form-control" type="text" name="fax" required>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Remark</label>
                                                                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>


                                                        <button class="btn btn-primary float-right" type="submit">Save</button>
                                                        <a class="btn btn-danger" href="{{ route('banks.index') }}">Return</a>
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