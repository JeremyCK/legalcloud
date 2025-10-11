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
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Referral</h4>
                        </div>
                        <div class="card-body">

                            <div class="box box-default">
                                <!-- /.box-header -->
                                <div class="box-body wizard-content">
                                    <div class="tab-wizard wizard-circle wizard clearfix">


                                        <form id="form_new" action="{{ route('referral.update', $referral->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">


                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                                                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i>
                                                        Referral Information</h4>

                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label>Referral Name</label>
                                                            <input class="form-control" value="{{ $referral->name }}"
                                                                type="text" name="name" id="referral_name_new">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row ">
                                                        <div class="col">
                                                            <label>Referral email</label>
                                                            <input class="form-control" type="text"
                                                                value="{{ $referral->email }}" name="email"
                                                                id="referral_email_new">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label>{{ __('coreuiforms.notes.status') }}</label>
                                                            <select class="form-control" name="status">
                                                                <option value="1"
                                                                    @if ($referral->status == 1) selected @endif>Active
                                                                </option>
                                                                <option value="0"
                                                                    @if ($referral->status == 0) selected @endif>
                                                                    Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row ">
                                                        <div class="col">
                                                            <label>Referral phone no</label>
                                                            <input class="form-control" name="phone_no"
                                                                value="{{ $referral->phone_no }}"
                                                                id="referral_phone_no_new" type="text" />
                                                        </div>
                                                    </div>

                                                    <div class="form-group row ">
                                                        <div class="col">
                                                            <label>Company</label>
                                                            <input class="form-control" name="company"
                                                                value="{{ $referral->company }}" id="referral_company_new"
                                                                type="text" />
                                                        </div>
                                                    </div>

                                                    <div class="form-group row ">
                                                        <div class="col">
                                                            <label>Case count</label>
                                                            <input class="form-control" name="case_count"
                                                                value="{{ $case_count }}" id="case_count"
                                                                type="text" readonly disabled/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Bank</label>
                                                                <select id="ddl_quotation_template" class="form-control" name="bank_id">
                                                                    <option value="0">-- Select bank --</option>
                                                                    @foreach ($banks as $index => $bank)
                                                                    <option value="{{ $bank->id }}" @if ($referral->bank_id == $bank->id) selected @endif>{{ $bank->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                    <div class="form-group row ">
                                                            <div class="col">
                                                                <label>IC No</label>
                                                                <input class="form-control" name="ic_no" id="referral_ic_no_new" value="{{ $referral->ic_no }}" type="text" />
                                                            </div>
                                                        </div>

                                                        <div class="form-group row ">
                                                            <div class="col">
                                                                <label>Bank Account</label>
                                                                <input class="form-control" name="bank_account" value="{{ $referral->bank_account }}" id="referral_bank_account_new" type="text" />
                                                            </div>
                                                        </div>


                                                </div>
                                            </div>


                                            <a href="{{ route('referral.index') }}" class="btn btn-danger">Cancel</a>

                                            <button id="btnSubmit" class="btn btn-success float-right"
                                                type="submit">Save</button>
                                        </form>



                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-8">
                    <div id="d-listing" class="card">
                        <div class="card-header">
                            <h4>Referral Case List</h4>
                        </div>
                        <div class="card-body">

                          

                            <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Ref No</th>
                                        <th class="text-center">Lawyer</th>
                                        <th class="text-center">Clerk</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
        function reloadtable() {

            var url = "{{ route('referralcase.list') }}";

            // url = url.replace('id', '{{ $referral->id }}');

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: url,
                ajax: {
                    url: url,
                    data: {
                        "id": {{ $referral->id }},
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                    data: 'case_ref_no',
                    name: 'case_ref_no'
                    },
                    {
                    data: 'lawyer',
                    name: 'lawyer'
                    },
                    {
                    data: 'clerk',
                    name: 'clerk'
                    },
                    {
                    data: 'status',
                    name: 'status'
                    },
                ]
            });
        }

        $(function() {

            reloadtable();

        });
    </script>
@endsection
