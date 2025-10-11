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
                        <h4>Create new quotation template</h4>
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
                                                    <form method="POST" action="{{ route('quotation.store') }}">
                                                        @csrf


                                                        <input class="form-control" type="hidden" id="quotation_id" value="" required>

                                                        <div class="row">
                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Quotation name</label>
                                                                        <input class="form-control" type="text" name="name" value="" required>
                                                                    </div>
                                                                </div>




                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Remark</label>
                                                                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                                                                    </div>
                                                                </div>




                                                            </div>


                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>{{ __('coreuiforms.notes.status') }}</label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="1" >Active</option>
                                                                            <option value="0" >Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <button class="btn btn-primary float-right" type="submit">Create</button>
                                                        <a class="btn btn-danger" href="{{ route('quotation.index') }}">Return</a>
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