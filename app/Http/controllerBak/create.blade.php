@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <h4> Create new user </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                        @endif
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.users.email') }}</label>
                                            <input class="form-control" type="text" placeholder="{{ __('coreuiforms.users.email') }}" name="email" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.users.username') }}</label>
                                            <input class="form-control" type="text" placeholder="{{ __('coreuiforms.users.username') }}" name="name" required autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.notes.status') }}</label>
                                            <select class="form-control" name="role">
                                                @foreach($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('Password') }}(System will generate default password)</label>
                                            <input class="form-control" type="password" disabled placeholder="{{ __('Password') }}" value="password" name="password" required autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.users.phone_no') }}</label>
                                            <input class="form-control" type="text" placeholder="{{ __('coreuiforms.users.phone_no') }}" name="phone_no" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.users.office_no') }}</label>
                                            <input class="form-control" type="text" placeholder="{{ __('coreuiforms.users.office_no') }}" name="office_no" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Max file</label>
                                            <input class="form-control" type="number" placeholder="Max file" name="max_file">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Min file</label>
                                            <input class="form-control" type="number" placeholder="Min file" name="min_file">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Race</label>
                                            <select class="form-control" id="race" name="race">
                                                <option value="0" >-- Please select race -- </option>
                                                <option value="1" >Chinese</option>
                                                <option value="2" >Malay</option>
                                                <option value="3" >Indian</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-success float-right" type="submit">{{ __('coreuiforms.save') }}</button>
                            <a href="{{ route('users.index') }}" class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
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