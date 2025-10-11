@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet">

<link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
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
                        <form action="{{ route('case.store') }}" method="POST">
                            <!--        You can switch " data-color="green" "  with one of the next bright colors: "blue", "azure", "orange", "red"       -->
                            @csrf
                            <div class="wizard-header">
                                <h3 class="wizard-title">Create new case</h3>
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

                                <div class="tab-pane" id="tab1">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <h4 style="margin-bottom: 50px;">Client Information</h4>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="client_name" type="text" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">IC</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="client_ic" type="text" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Email </label>
                                                <div class="col-md-9">
                                                    <input type="email" name="client_email" class="form-control" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Phone No</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="client_phone_no" class="form-control" required />
                                                </div>
                                            </div>


                                            <!-- <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Address</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                                </div>
                                            </div> -->


                                            <!-- 
                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                                <div class="col-md-9"><select class="form-control" id="status" name="status">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div> -->
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane" id="tab2">
                                    <div class="row ">
                                        <div class="col-sm-12">
                                            <h4 style="margin-bottom: 50px;">Case information</h4>
                                            <div class="form-group row hide">
                                                <label s class="col-md-3 col-form-label" for="hf-email">Case Type</label>
                                                <div class="col-md-9">
                                                    <select id="case_type" class="form-control" name="case_type" required>
                                                        <!-- <option value="">-- Please select the template -- </option> -->
                                                        @foreach($case_type as $index => $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-email">Assign</label>
                                                <div class="col-md-9">
                                                    <select id="bank" class="form-control" name="bank" required>
                                                        <option value="">-- Please select the template -- </option>
                                                        <option value="group">Group</option>
                                                        <option value="individual">Individual</option>
                                                        <option value="manual">Manual</option>
                                                    </select>
                                                </div>
                                            </div> -->



                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Referral name</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="referral_name" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Referral phone no</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="referral_phone_no" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Referral email</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="referral_email" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="spa">

                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="hf-email">Bank</label>
                                                    <div class="col-md-9">
                                                        <select id="bank" class="form-control" name="bank" required>
                                                            @foreach($banks as $index => $bank)
                                                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Property address</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="property_address" name="property_address" rows="3" placeholder="" required></textarea>
                                                </div>
                                            </div>

                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="hf-password">Purchase Price</label>
                                                    <div class="col-md-9">
                                                        <input type="number" value="0" id="purchase_price" name="purchase_price" class="form-control" required />
                                                    </div>
                                                </div>
                                            </div>




                                            <div class="form-group row">
                                                <label class="col-md-3 col-form-label" for="hf-password">Remark</label>
                                                <div class="col-md-9">
                                                    <textarea class="form-control" id="remark" name="remark" rows="3" placeholder=""></textarea>
                                                </div>
                                            </div>


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