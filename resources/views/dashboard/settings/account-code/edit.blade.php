@extends('dashboard.base')
@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h4>Update Account Item </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <form method="POST" action="{{ route('account-code.update', $AccountCode->id) }}">
                            @csrf
                            @method('PUT')
                        
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Account Code</label>
                                            <input class="form-control" type="text" name="code" value="{{ $AccountCode->code}}"  required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Account Name</label>
                                            <input class="form-control" type="text" name="name" value="{{ $AccountCode->name}}"  required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Group</label>
                                            <select class="form-control" name="group">
                                                <option value="0">-- Select account code --</option>
                                                <option @if($AccountCode->group == 1) selected @endif value="1">Office Bank Account</option>
                                                <option @if($AccountCode->group == 2) selected @endif value="2" >Client client Account</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Status</label>
                                            <select class="form-control" name="status">
                                                <option @if($AccountCode->status == 1) selected @endif value="1">Active</option>
                                                <option @if($AccountCode->status == 0) selected @endif value="0" >Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Description</label>
                                            <textarea class="form-control" rows="4" id="desc" name="desc">{{ $AccountCode->desc}}</textarea>
                                        </div>
                                    </div>
                                </div>

                            
                            </div>



                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-danger" href="{{ route('account-code.index') }}">Return</a>
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
@endsection