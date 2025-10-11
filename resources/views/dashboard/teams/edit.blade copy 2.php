@section('css')
<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    a.a_admin,
    a.a_admin:hover,
    a.a_admin:active,
    a.a_admin:focus {
        color: #f9b115;
    }

    a.a_sales,
    a.a_sales:hover,
    a.a_sales:active,
    a.a_sales:focus {
        color: #e55353;
    }

    a.a_lawyer,
    a.a_lawyer:hover,
    a.a_lawyer:active,
    a.a_lawyer:focus {
        color: #4638c2;
    }

    a.a_clerk,
    a.a_clerk:hover,
    a.a_clerk:active,
    a.a_clerk:focus {
        color: #2ca8ff;
    }

    .bg-done {
        background-color: #46be8a !important;
        color: white !important;
    }

    .bg-overdue {
        background-color: #e55353 !important;
        color: white !important;
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

    .checklist_name {
        font-size: 12px;
    }

    .done .checklist_name {
        color: #33cabb;
    }
</style>

<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
@endsection
@extends('dashboard.base')


@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12">
                @if ($errors->any())

                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="col-sm-12">

                <div class="box-body wizard-content">




                    <form action="#" class="tab-wizard wizard-circle wizard clearfix" role="application" id="steps-uid-0">
                        <div class="steps clearfix">
                            <ul role="tablist">

                                <li id="li_check_point_1" role="tab" class="first li_check_point " aria-disabled="false" aria-selected="false">
                                    <a id="steps-uid-0-t-0" onclick="stepController('1');" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
                                        <span class="step">
                                            <i class="span-checkpoint cil-clipboard"></i>
                                        </span>
                                        <span class="checklist_name">test</span>
                                    </a>
                                </li>

                                <li id="li_check_point_1" role="tab" class="first li_check_point " aria-disabled="false" aria-selected="false">
                                    <a id="steps-uid-0-t-0" onclick="stepController('1');" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
                                        <span class="step">
                                            <i class="span-checkpoint cil-user"></i>
                                        </span>
                                        <span class="checklist_name">test</span>
                                    </a>
                                </li>

                                <li id="li_check_point_1" role="tab" class="first li_check_point " aria-disabled="false" aria-selected="false">
                                    <a id="steps-uid-0-t-0" onclick="stepController('1');" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
                                        <span class="step">
                                            <i class="span-checkpoint cil-applications"></i>
                                        </span>
                                        <span class="checklist_name">
                                            <i class="span-checkpoint cil-applications"></i>
                                        </span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </form>
                </div>

                <div class="wizard-container">
                    <div class="card wizard-card" data-color="red" id="wizard">

                        <!-- <form id="formMain" > -->
                        <form action="{{ route('teams.update', $teams->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="wizard-header">
                                <h3 class="wizard-title">Edit Team</h3>
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
                                    <li>
                                        <a href="#tab3" data-toggle="tab">
                                            <div class="icon-circle done">
                                                <i class="span-checkpoint cil-applications"></i>
                                            </div>
                                            Step 3
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <div class="tab-content bg-color-none">

                                <div class="tab-pane" id="tab1">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <h4 style="margin-bottom: 50px;">Team Information</h4>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="name" value="{{ $teams->name }}" type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Description</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="desc" name="desc" rows="2">{{ $teams->desc }}</textarea>
                                                </div>
                                            </div>





                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                                <div class="col-md-9">
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="1" selected>Active</option>
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
                                            <h4>Team member</h4>
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
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$clerk->id }}" @if(in_array($clerk->id , $banksUsersRel)) checked @endif>
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
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$lawyer->id }}" @if(in_array($lawyer->id , $banksUsersRel)) checked @endif>
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
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$account->id }}" @if(in_array($account->id , $banksUsersRel)) checked @endif>
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
                                                            <input class="" name="assignTo[]" type="checkbox" value="{{$sale->id }}" @if(in_array($sale->id , $banksUsersRel)) checked @endif>
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab3">
                                    <div class="row ">
                                        <div class="col-sm-12">
                                            <h4>Portfolio setup</h4>
                                            <table class="table table-bordered  datatable">
                                                <tbody>
                                                    @foreach($case_type as $index => $type)


                                                    <tr style="background-color: #d8dbe0;">
                                                        <td>{{$type->name }}</td>
                                                        <td>
                                                            <input class="" name="selectType[]" type="checkbox" value="{{$type->id }}" @if(in_array($type->id , $teamCaseList)) checked @endif>
                                                        </td>
                                                    </tr>

                                                    @if($type->is_bank_required == "1")
                                                    @foreach($banks as $index => $bank)

                                                    <tr>
                                                        <td>{{$bank->name }}</td>
                                                        <td>
                                                            <input class="" name="selectBank[]" type="checkbox" value="{{$bank->id }}" @if(in_array($bank->id , $teamBankList)) checked @endif>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @endif
                                                    @endforeach



                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>
                                <div class="wizard-footer">
                                    <div class="pull-right">
                                        <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                                        <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Save' />
                                        <!-- <input type='button' onclick="getMessage()" class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Save' /> -->
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
    function getMessage() {


        // data = stripslashes(data);
        // data = htmlspecialchars(data);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });


        var form_data = new FormData();

        form_data = document.getElementById("formMain");
        //   form_data.append('_token','{{ csrf_token() }}');

        // console.log(data);
        $.ajax({
            type: 'PUT',
            url: '/teams/update',
            date: form_data,
            // data: {
            //     page: $("#pageId").val(),
            //     request: content,
            //     _token: '<?php echo csrf_token() ?>'
            // },
            success: function(data) {
                alert(data);

                document.getElementById('div_content_' + $("#pageId").val()).innerHTML = content;

                document.getElementById('div_edit').style.display = "none";
                document.getElementById('div_page').style.display = "flex";
            }
        });
    }
</script>

@endsection