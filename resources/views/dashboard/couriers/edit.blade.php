@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <h4> Update courier </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                        @endif
                        <form method="POST" action="{{ route('couriers.update', $courier->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Name</label>
                                            <input class="form-control" type="text" name="name" value="{{ $courier->name}}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Short Code</label>
                                            <input class="form-control" type="text" name="short_code" value="{{ $courier->short_code}}" autocomplete="off">
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Desc</label>
                                            <textarea class="form-control" id="desc" name="desc" rows="3">{{ $courier->desc}}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Tel No</label>
                                            <input class="form-control" type="text" name="tel_no" value="{{ $courier->tel_no}}" >
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Fax</label>
                                            <input class="form-control" type="text" name="fax" value="{{ $courier->fax}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3">{{ $courier->address}}</textarea>
                                        </div>
                                    </div>

                                   

                                </div>
                            </div>


                            <button class="btn btn-success float-right" type="submit">{{ __('coreuiforms.save') }}</button>
                            <a href="{{ route('couriers.index') }}" class="btn btn-primary">{{ __('coreuiforms.return') }}</a>
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