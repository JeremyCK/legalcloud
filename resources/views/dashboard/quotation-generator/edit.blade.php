@extends('dashboard.base')
@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h4>Update Account </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <form method="POST" action="{{ route('account.update', $account->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <div class="col">
                                    <label>Code</label>
                                    <input class="form-control" type="text" name="code" value="{{ $account->code}}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Account name</label>
                                    <input class="form-control" type="text" name="name" value="{{ $account->name}}" required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col">
                                    <label>Category</label>
                                    <select class="form-control" id="account_category_id" name="account_category_id">
                                        <option value="0">Please select a category</option>
                                        @foreach($account_category as $category)
                                        <option value="{{ $category->id }}" @if ($category->id == $account->account_category_id) selected @endif>{{ $category->category }}</option>
                                    
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col">
                                    <label>Need approval</label>
                                    <select class="form-control" name="approval">
                                        <option value="0" @if ($account->approval == 0) selected @endif >No</option>
                                        <option value="1"  @if ($account->approval == 1) selected @endif>Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Remark</label>
                                    <textarea class="form-control" id="remark" name="remark" rows="3">{{ $account->remark}}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>{{ __('coreuiforms.notes.status') }}</label>
                                    <select class="form-control" name="status">
                                        <option value="1" @if ($account->approval == 1) selected @endif>Active</option>
                                        <option value="0" @if ($account->approval == 0) selected @endif>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-danger" href="{{ route('account.index') }}">Return</a>
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