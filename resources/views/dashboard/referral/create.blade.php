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
                        <h4>New Referral</h4>
                    </div>
                    <div class="card-body">

                        <div class="box box-default">
                            <!-- /.box-header -->
                            <div class="box-body wizard-content">
                                <div class="tab-wizard wizard-circle wizard clearfix">

                                    <div class="tab-content bg-color-none">

                                        <div class="tab-pane active" id="tab1">

                                            <form id="form_new" action="{{ route('referral.store') }}" method="POST">
                                                @csrf
                                                <div class="row" style="margin-top:40px;">
                                                    <div class="col-12 ">
                                                        <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral Information</h4>
                                                    </div>

                                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Referral Name</label>
                                                                <input class="form-control" type="text" name="name" id="referral_name_new">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Referral email</label>
                                                                <input class="form-control" type="text" name="email" id="referral_email_new">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Bank</label>
                                                                <select id="ddl_quotation_template" class="form-control" name="bank_id">
                                                                    <option value="0">-- Select bank --</option>
                                                                    @foreach($banks as $index => $bank)
                                                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>IC NO</label>
                                                                <input class="form-control" name="ic_no" id="referral_ic_no_new" type="text" />
                                                            </div>
                                                        </div>


                                                    </div>

                                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Referral phone no</label>
                                                                <input class="form-control" name="phone_no" id="referral_phone_no_new" type="text" />
                                                            </div>
                                                        </div>

                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Company</label>
                                                                <input class="form-control" name="company" id="referral_company_new" type="text" />
                                                            </div>
                                                        </div>

                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Bank Account</label>
                                                                <input class="form-control" name="bank_account" id="referral_bank_account_new" type="text" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <a href="{{ route('referral.index') }}" class="btn btn-danger">Cancel</a>

                                                <button id="btnSubmit" class="btn btn-success float-right" type="submit">Create Referral</button>
                                            </form>




                                            <!-- <div class="row">
                                                <form id="form_new" action="{{ route('referral.store') }}" method="POST">
                                                    <div class="col-sm-12">
                                                        @csrf
                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label><span class="text-danger">*</span>Name</label>
                                                                <input class="form-control" type="text" name="name" required>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Email</label>
                                                                <input class="form-control" type="email" name="email">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label> Tel No</label>
                                                                <input class="form-control" type="text" name="phone_no">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Remark</label>
                                                                <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                                                            </div>
                                                        </div>


                                                        <button class="btn btn-primary float-right" type="submit">Save</button>
                                                        <a class="btn btn-danger" href="{{ route('referral.index') }}">Return</a>
                                                    </div>
                                                </form>
                                            </div> -->
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

<script>
    function CreateNewChecklistStep() {
        var form_data = new FormData();

        $.ajax({
            type: 'POST',
            url: '/add_checklist_step',
            data: $('#form_new').serialize(),
            success: function(results) {
                console.log(results);
                if (results.status == 1) {
                    Swal.fire(
                        'Success!', results.data.message,
                        'success'
                    )
                    window.location.href = '/checklist-item/' + results.data.id;
                    // location.reload();
                }

            }
        });
    }
</script>

@endsection