@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet">

<link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
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

                <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizard">
                        <form action="{{ route('banks.update', $account_template->id) }}" method="POST">
                            <!--        You can switch " data-color="green" "  with one of the next bright colors: "blue", "azure", "orange", "red"       -->
                            @csrf
                            @method('PUT')
                            <div class="wizard-header">
                                <h3 class="wizard-title">Edit Account Template</h3>
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
                                            Main
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#tab2" data-toggle="tab">
                                            <div class="icon-circle done">
                                                <i class="span-checkpoint cil-user"></i>
                                            </div>
                                            Details
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <div class="tab-content bg-color-none">

                                <div class="tab-pane" id="tab1">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <h4 style="margin-bottom: 50px;">Template Information</h4>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="name" value="{{ $account_template->name }}" type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Remark</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="remark" name="remark" rows="2">{{ $account_template->remark }}</textarea>
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
                                        <div class="col-sm-12">
                                            <h4>Template details</h4>
                                            <!-- <table class="table table-striped table-bordered datatable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>No</th>
                                                        <th> Name</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(count($account_template_detail))
                                                    @foreach($account_template_detail as $index => $template)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $template->item_name }}</td>
                                                        <td>{{ $template->amount }}</td>
                                                        <td class="text-center">
                                                            @if($template->status == 1)
                                                            <span class="badge badge-success">Active</span>
                                                            @elseif($template->status == 0)
                                                            <span class="badge badge-danger">Inactive</span>
                                                            @endif
                                                            <a class="btn btn-success" href="http://127.0.0.1:8000/roles/move/move-up?id=9">
                                                                <i class="cil-arrow-thick-top"></i>
                                                            </a>
                                                        </td>

                                                        <td class="text-center">
                                                            <a href="{{ url('/account-template/' . $template->id . '/edit') }}" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

                                                        </td>
                                                    </tr>

                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td class="text-center" colspan="5">No data</td>
                                                    </tr>
                                                    @endif

                                                </tbody>
                                            </table> -->

                                            <table class="table table-striped table-bordered datatable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>No</th>
                                                        <th> Name</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $total = 0;
                                                $subtotal = 0; 
                                                ?>
                                                    @if(count($account_template_with_cat))
                                                    
                                                        
                                                        @foreach($account_template_with_cat as $index => $cat)
                                                        <tr style="background-color:grey;color:white">
                                                            <!-- <td>{{ $template->item_code }}</td> -->
                                                            <td colspan="5">{{ $cat['category']->category }}</td>
                                                            <!-- <td>{{ $cat['category']->percentage }}</td> -->
                                                            <?php  $total += $subtotal ?>
                                                            <?php  $subtotal = 0 ?>

                                                        </tr>
                                                            @foreach($cat['account_details'] as $index => $details)
                                                            <?php  $subtotal += $details->amount ?>
                                                            <tr>
                                                                <td class="text-center">{{ $index + 1 }}</td>
                                                                <!-- <td>{{ $template->item_code }}</td> -->
                                                                <td>{{ $details->item_name }}</td>
                                                                <td>{{ $details->amount }}</td>
                                                                <td class="text-center">
                                                                    <a class="btn btn-success" href="http://127.0.0.1:8000/roles/move/move-up?id=9">
                                                                        <i class="cil-arrow-thick-top"></i>
                                                                    </a>
                                                                </td>

                                                                <td class="text-center">
                                                                    <a href="{{ url('/account-template/' . $details->id . '/edit') }}" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

                                                                </td>
                                                            </tr>

                                                            @endforeach

                                                            @if($cat['category']->taxable == "1")
                                                            <tr >
                                                            <td>{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
                                                            <td style="text-align:right"  colspan="4">{{$subtotal}}</td>

                                                        </tr>
                                                            @endif

                                                            <tr >
                                                            <td></td>
                                                            <td style="text-align:right"  colspan="4">{{$subtotal}}</td>

                                                        </tr>

                                                        @endforeach

                                                        <tr >
                                                            <td>Total </td>
                                                            <td style="text-align:right" colspan="4">{{$total}}</td>

                                                        </tr>
                                                    @else
                                                    <tr>
                                                        <td class="text-center" colspan="5">No data</td>
                                                    </tr>
                                                    @endif

                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
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

@endsection