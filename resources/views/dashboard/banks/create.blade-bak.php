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

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="card-title">Create new bank</h3>

                        <!-- <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                        </div> -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body wizard-content">
                        <form class="tab-wizard wizard-circle wizard clearfix" action="{{ route('banks.store') }}" method="POST">
                            <div class="steps clearfix">
                                <ul role="tablist">
                                    <li role="tab" class="first current " aria-disabled="false" aria-selected="true">
                                        <a id="test" href="#tab1" data-toggle="tab">
                                            <span class="current-info audible">current step: </span>
                                            <span class="step">
                                                <i class="span-checkpoint cil-clipboard"></i></span>
                                            Personal Info</a>
                                    </li>
                                    <li role="tab" class="" aria-disabled="true">
                                        <a id="test1" href="#tab2" data-toggle="tab">
                                            <span class="step">
                                                <i class="span-checkpoint cil-user"></i></span> Job Status
                                        </a>
                                    </li>

                                    <!-- <li role="tab" >
                                        <a href="#tab1" data-toggle="tab">
                                            <div class="icon-circle done">
                                                <i class="span-checkpoint cil-clipboard"></i>
                                            </div>
                                            Step 1
                                        </a>
                                    </li> -->
                                    <!-- <li role="tab" class="disabled" aria-disabled="true"><a id="steps-uid-0-t-2" href="#steps-uid-0-h-2" aria-controls="steps-uid-0-p-2"><span class="step">3</span> Interview</a></li>
                                    <li role="tab" class="disabled last" aria-disabled="true"><a id="steps-uid-0-t-3" href="#steps-uid-0-h-3" aria-controls="steps-uid-0-p-3"><span class="step">4</span> Remark</a></li> -->
                                </ul>
                            </div>
                            <div class="tab-content bg-color-none">

                                <div class="tab-pane" id="tab1">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <h4 style="margin-bottom: 50px;">Bank Information</h4>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="name" type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Description</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Short Code</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="short_code" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Phone No</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="fax" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Fax </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="tel_no" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Address</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                                <div class="col-md-9"><select class="form-control" id="status" name="status">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane" id="tab2">





                                    <div class="row ">

                                        <div class="col-sm-6">

                                            <table class="table table-bordered  datatable">
                                                <tr style="background-color: #d8dbe0;">
                                                    <td>
                                                        Lawyers
                                                    </td>
                                                </tr>

                                                @foreach($lawyers as $index => $lawyer)


                                                <tr>
                                                    <td>
                                                        <div class="checkbox">
                                                            <input name="assignTo[]" type="checkbox" id="{{$lawyer->id }}" value="{{$lawyer->id }}">
                                                            <label for="{{$lawyer->id }}">{{$lawyer->name }}</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="col-sm-6">

                                            <table class="table table-bordered  datatable">
                                                <tr style="background-color: #d8dbe0;">
                                                    <td>
                                                        Sales
                                                    </td>
                                                </tr>

                                                @foreach($sales as $index => $sale)


                                                <tr>
                                                    <td>
                                                        <div class="checkbox">
                                                            <input name="assignTo[]" type="checkbox" id="{{$sale->id }}" value="{{$sale->id }}">
                                                            <label for="{{$sale->id }}">{{$sale->name }}</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="col-sm-6">

                                            <table class="table table-bordered  datatable">
                                                <tr style="background-color: #d8dbe0;">
                                                    <td>
                                                        Clerks
                                                    </td>
                                                </tr>

                                                @foreach($clerks as $index => $clerk)


                                                <tr>
                                                    <td>
                                                        <div class="checkbox">
                                                            <input name="assignTo[]" type="checkbox" id="{{$clerk->id }}" value="{{$clerk->id }}">
                                                            <label for="{{$clerk->id }}">{{$clerk->name }}</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="col-sm-6">

                                            <table class="table table-bordered  datatable">
                                                <tr style="background-color: #d8dbe0;">
                                                    <td>
                                                        Accounts
                                                    </td>
                                                </tr>

                                                @foreach($accounts as $index => $account)


                                                <tr>
                                                    <td>
                                                        <div class="checkbox">
                                                            <input name="assignTo[]" type="checkbox" id="{{$account->id }}" value="{{$account->id }}">
                                                            <label for="{{$account->id }}">{{$account->name }}</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>



                                    </div>
                                </div>
                            </div>

                            <div class="actions clearfix">
                                <ul role="menu" aria-label="Pagination">
                                    <li class="disabled" aria-disabled="true"><a href="#previous" role="menuitem">Previous</a></li>
                                    <li aria-hidden="false" aria-disabled="false"><a href="#next" role="menuitem">Next</a></li>
                                    <li aria-hidden="true" style="display: none;"><a href="#finish" role="menuitem">Submit</a></li>
                                </ul>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->
                </div>

                <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizard">
                        <form action="{{ route('banks.store') }}" method="POST">
                            <!--        You can switch " data-color="green" "  with one of the next bright colors: "blue", "azure", "orange", "red"       -->
                            @csrf
                            <div class="wizard-header">
                                <h3 class="wizard-title">Create new bank</h3>
                                <!-- <p class="category">This information will let us know more about your place.</p> -->
                            </div>
                            <div class="wizard-navigation">
                                <div class="progress-with-circle">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="4" style="width: 15%;"></div>
                                </div>
                                <ul>
                                    <li>
                                        <a href="#tab1" data-toggle="tab">
                                            <div class="icon-circle done">
                                                <i class="span-checkpoint cil-clipboard"></i>
                                                <!-- <span class="span-checkpoint cil-clipboard"></span> -->
                                            </div>
                                            Step 1
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#tab2" data-toggle="tab">
                                            <div class="icon-circle done">
                                                <i class="span-checkpoint cil-user"></i>
                                            </div>
                                            Step 2
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <div class="tab-content bg-color-none">

                                <div class="tab-pane" id="tab12">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <h4 style="margin-bottom: 50px;">Bank Information</h4>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="name" type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Description</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Short Code</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="short_code" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Phone No</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="fax" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Fax </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="tel_no" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Address</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                                <div class="col-md-9"><select class="form-control" id="status" name="status">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane" id="tab23">
                                    <!-- <div class="row ">
                                        <div class="col-sm-12">
                                            <h4 style="margin-bottom: 50px;">Assign to</h4>
                                            <table class="table table-bordered  datatable">
                                                <tbody>
                                                    <tr style="background-color: #d8dbe0;">
                                                        <td colspan="2">
                                                            Clerk
                                                        </td>
                                                    </tr>
                                                    @foreach($clerks as $index => $clerk)


                                                    <tr>
                                                        <td>{{$clerk->name }}</td>
                                                        <td>
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$clerk->id }}">
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                    <tr style="background-color: #d8dbe0;">
                                                        <td colspan="2">
                                                            Lawyer
                                                        </td>
                                                    </tr>
                                                    @foreach($lawyers as $index => $lawyer)


                                                    <tr>
                                                        <td>{{$lawyer->name }}</td>
                                                        <td>
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$lawyer->id }}">
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                    <tr style="background-color: #d8dbe0;">
                                                        <td colspan="2">
                                                            Account
                                                        </td>
                                                    </tr>
                                                    @foreach($accounts as $index => $account)


                                                    <tr>
                                                        <td>{{$account->name }}</td>
                                                        <td>
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$account->id }}">
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                    <tr style="background-color: #d8dbe0;">
                                                        <td colspan="2">
                                                            Sales
                                                        </td>
                                                    </tr>
                                                    @foreach($sales as $index => $sale)


                                                    <tr>
                                                        <td>{{$sale->name }}</td>
                                                        <td>
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$sale->id }}">
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>


                                        </div>
                                    </div> -->
                                </div>
                                <div class="wizard-footer">
                                    <div class="pull-right">
                                        <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                                        <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Save' />
                                        <!-- <button class="btn btn-primary float-right" type="submit">Save</button> -->
                                    </div>

                                    <div class="pull-left">
                                        <input type='button' class='btn btn-previous btn-default btn-wd' name='previous' value='Previous' />
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                        </form>
                    </div>
                </div>


                <!-- <div class="card">
                    <div class="card-header">
                        <h4>Create new bank</h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif





                        <form action="{{ route('banks.store') }}" method="POST">
                            @csrf






                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-primary" href="{{ route('banks.index') }}">Return</a>
                        </form>
                    </div>
                </div> -->
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

<script>

</script>

@endsection