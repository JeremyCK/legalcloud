@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <h4> Create new courier </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                        @endif
                        <form method="POST" action="{{ route('couriers.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Name</label>
                                            <input class="form-control" type="text" name="name" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Short Code</label>
                                            <input class="form-control" type="text" name="short_code"  autocomplete="off">
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Desc</label>
                                            <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.users.phone_no') }}</label>
                                            <input class="form-control" type="text"  name="tel_no" required >
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Fax</label>
                                            <input class="form-control" type="text" name="fax" >
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <button class="btn btn-success float-right" type="submit">{{ __('coreuiforms.save') }}</button>
                            <a href="{{ route('users.index') }}" class="btn btn-primary">{{ __('coreuiforms.return') }}</a>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection

@section('javascript')


@endsection