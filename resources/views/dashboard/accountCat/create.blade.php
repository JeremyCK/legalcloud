@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<!-- <link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet"> -->
<!-- <link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet"> -->

<link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<style>
    .info-box {
        min-height: 100px;
        background: #fff;
        width: 100%;
        margin-bottom: 20px;
        padding: 15px;
    }

    .info-box small {
        font-size: 14px;
    }

    .info-box .progress {
        background: rgba(0, 0, 0, .2);
        margin: 5px -10px 5px 0;
        height: 2px;
    }

    .info-box .progress,
    .info-box .progress .progress-bar {
        border-radius: 0;
    }

    .info-box .progress .progress-bar {
        background: #fff;
    }

    .info-box-icon {
        float: left;
        height: 70px;
        width: 70px;
        text-align: center;
        font-size: 30px;
        line-height: 74px;
        background: rgba(0, 0, 0, .2);
        border-radius: 100%
    }

    .info-box-icon.push-bottom {
        margin-top: 20px;
    }

    .info-box-icon>img {
        max-width: 100%
    }

    .info-box-content {
        padding: 10px 10px 10px 0;
        margin-left: 90px;
    }

    .info-box-number {
        font-weight: 300;
        font-size: 21px;
    }

    .info-box-text,
    .progress-description {
        display: block;
        font-size: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        font-weight: 400;
    }
</style>
@endsection
@extends('dashboard.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create account category</h4>
                    </div>
                    <div class="card-body">

                        <div class="box box-default">
                            <!-- /.box-header -->
                            <div class="box-body wizard-content">
                                <!-- <form class="tab-wizard wizard-circle wizard clearfix" action="{{ route('banks.store') }}" method="POST"> -->
                                    <div class="tab-wizard wizard-circle wizard clearfix">
                                   
                                    <div class="tab-content bg-color-none">

                                        <div class="tab-pane active" id="tab1">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                <form method="POST" action="{{ route('account-cat.store') }}">
                                                
                                                    @csrf
                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label>Code</label>
                                                            <input class="form-control" type="text" placeholder="code" name="code" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label>Category name</label>
                                                            <input class="form-control" type="text" placeholder="Category name" name="category" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label>Taxable</label>
                                                            <select class="form-control" name="taxable">
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label>Percentage</label>
                                                            <input class="form-control" type="number" value="0" placeholder="Percentage" name="percentage" required>
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

                                                    <button class="btn btn-primary float-right" type="submit">Save</button>
                                                    <a class="btn btn-danger" href="{{ route('account-cat.index') }}">Return</a>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="actions clearfix">
                                        <ul role="menu" aria-label="Pagination">
                                            <li class="disabled" aria-disabled="true"><a href="#previous" role="menuitem">Previous</a></li>
                                            <li aria-hidden="false" aria-disabled="false"><a href="#next" role="menuitem">Next</a></li>
                                            <li aria-hidden="true" style="display: none;"><a href="#finish" role="menuitem">Submit</a></li>
                                        </ul>
                                    </div> -->
                                <!-- </form> -->
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <form style="display:none;" method="POST" action="{{ route('account-cat.store') }}">
                            @csrf
                            <div class="form-group row">
                                <div class="col">
                                    <label>Code</label>
                                    <input class="form-control" type="text" placeholder="code" name="code" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Category name</label>
                                    <input class="form-control" type="text" placeholder="Category name" name="category" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Taxable</label>
                                    <select class="form-control" name="taxable">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Percentage</label>
                                    <input class="form-control" type="number" value="0" placeholder="Percentage" name="percentage" required>
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

                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-primary" href="{{ route('account-cat.index') }}">Return</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/paperfish/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.bootstrap.wizard.js') }}"></script>

<script src="{{ asset('js/paperfish/paper-bootstrap-wizard.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<script src="http://s.codepen.io/assets/libs/modernizr.js" type="text/javascript"></script>


@endsection