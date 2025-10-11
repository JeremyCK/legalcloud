@extends('dashboard.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Account Code</h4>
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
                                                    <form method="POST" action="{{ route('account-code.store') }}">
                                                        @csrf


                                                        <div class="row">
                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Account Code</label>
                                                                        <input class="form-control" type="text" name="code"  required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Account Name</label>
                                                                        <input class="form-control" type="text" name="name"  required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Group</label>
                                                                        <select class="form-control" name="group">
                                                                            <option value="0">-- Select account code --</option>
                                                                            <option value="1">Office Bank Account</option>
                                                                            <option value="2" >Client client Account</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Status</label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="1">Active</option>
                                                                            <option value="0" >Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                <div class="form-group row">
                                                                    <div class="col">
                                                                        <label>Description</label>
                                                                        <textarea class="form-control" rows="4" id="desc" name="desc"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        
                                                        </div>


                                                        <button class="btn btn-primary float-right" type="submit">Save</button>
                                                        <a class="btn btn-danger" href="{{ route('account-code.index') }}">Return</a>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
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
@endsection