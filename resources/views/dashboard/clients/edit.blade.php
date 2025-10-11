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
                            <h4>Edit client</h4>
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
                                                        <form method="POST"
                                                            action="{{ route('clients.update', $client->id) }}">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="row">
                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>Name</label>
                                                                            <input class="form-control"
                                                                                value="{{ $client->name }}" type="text"
                                                                                name="name" required>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label><span class="text-danger">* </span>Client Type</label>
                                                                            <select id="customer_type" class="form-control" name="customer_type"
                                                                                required>
                                                                                <option value="1" @if($client->client_type == 1) selected @endif>Personal</option>
                                                                                <option value="2" @if($client->client_type == 2) selected @endif>Company</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>Tel No</label>
                                                                            <input class="form-control"
                                                                                value="{{ $client->phone_no }}"
                                                                                type="text" name="phone_no">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>Email</label>
                                                                            <input class="form-control"
                                                                                value="{{ $client->email }}" type="text"
                                                                                name="email">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " @if($client->client_type == 2) style="display:none" @endif>
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>IC No</label>
                                                                            <input class="form-control"
                                                                                value="{{ $client->ic_no }}" type="text"
                                                                                name="ic_no">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " @if($client->client_type == 1) style="display:none" @endif>
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>Company Reg No</label>
                                                                            <input class="form-control"
                                                                                value="{{ $client->company_ref_no }}" type="text"
                                                                                name="company_ref_no">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>Address</label>
                                                                            <textarea class="form-control" id="address" name="address" rows="2">{{ $client->address }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <label>{{ __('coreuiforms.notes.status') }}</label>
                                                                            <select class="form-control" name="status">
                                                                                <option value="1">Active</option>
                                                                                <option value="0">Inactive</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div> --}}

                                                            </div>


                                                            <button class="btn btn-primary float-right"
                                                                type="submit">Save</button>
                                                            <a class="btn btn-danger"
                                                                href="{{ route('clients.index') }}">Return</a>
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

                    <div id="d-listing" class="card">
                        <div class="card-header">
                            <h4>Case List</h4>
                        </div>
                        <div class="card-body">
                          
                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-center">No</th>
                                        <th class="text-center">Ref No</th> 
                                        <th class="text-center">Lawyer</th>
                                        <th class="text-center">Clerk</th>
                                        <th class="text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($LoanCase))
                                        @foreach ($LoanCase as $index => $case)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="text-left">
                                                    <a target="_blank" href="/case/{{ $case->id }}" class="" >{{ $case->case_ref_no }}</a>    
                                                </td>
                                                <td class="text-center">{{ $case->lawyer }}</td>
                                                <td class="text-center" >{{ $case->clerk }}</td>
                                                <td class="text-center">{{ $case->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="7">No data</td>
                                        </tr>
                                    @endif

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
@endsection
