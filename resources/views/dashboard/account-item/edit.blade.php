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
                        <form method="POST" action="{{ route('account-item.update', $account->id) }}">
                            @csrf
                            @method('PUT')
                            <!-- <div class="form-group row">
                                <div class="col">
                                    <label>Code</label>
                                    <input class="form-control" type="text" name="code" value="{{ $account->code}}">
                                </div>
                            </div> -->


                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Account name</label>
                                            <input class="form-control" type="text" name="name" value="{{ $account->name}}" required>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Category</label>
                                            <select class="form-control" id="account_cat_id" name="account_cat_id">
                                                <option value="0">--Please select a category--</option>
                                                @foreach($account_category as $category)
                                                <option value="{{ $category->id }}" @if ($category->id == $account->account_cat_id) selected @endif>{{ $category->category }}</option>

                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Remark</label>
                                            <textarea class="form-control" id="desc" name="desc" rows="3">{{ $account->remark }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.notes.status') }}</label>
                                            <select class="form-control" name="status">
                                                <option value="1" @if ($account->status == 1) selected @endif>Active</option>
                                                <option value="0" @if ($account->status == 0) selected @endif>Inactive</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Need approval</label>
                                            <select class="form-control" name="need_approval">
                                                <option value="0" @if ($account->need_approval == 0) selected @endif >No</option>
                                                <option value="1" @if ($account->need_approval == 1) selected @endif>Yes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Default Amount</label>
                                            <input class="form-control" type="number" name="amount" value="{{ $account->amount}}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Min Amount</label>
                                            <input class="form-control" type="number" name="min" value="{{ $account->min}}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Max Amount</label>
                                            <input class="form-control" type="number" name="max" value="{{ $account->max}}" required>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-danger" href="{{ route('account-item.index') }}">Return</a>
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