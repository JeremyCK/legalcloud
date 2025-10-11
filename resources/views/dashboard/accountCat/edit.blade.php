@extends('dashboard.base')

@section('content')


<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">



                <div class="card">
                    <div class="card-header">
                        <h4>Update category </h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <form method="POST" action="{{ route('account-cat.update', $account_cat->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <div class="col">
                                    <label>Code</label>
                                    <input class="form-control" type="text" placeholder="code" value="{{ $account_cat->code }}" name="code" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Category name</label>
                                    <input class="form-control" type="text" placeholder="Category name" value="{{ $account_cat->category }}" name="category" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Taxable</label>
                                    <select class="form-control" name="taxable">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Percentage</label>
                                    <input class="form-control" type="number" value="{{ $account_cat->percentage }}"  placeholder="Percentage" name="percentage" required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col">
                                    <label>{{ __('coreuiforms.notes.status') }}</label>
                                    <select class="form-control" name="status">
                                        <option value="1" >Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-primary" href="{{ route('account-cat.index') }}">Return</a>
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
<script>
    CKEDITOR.replace('summary-ckeditor');
    CKEDITOR.config.height = 600;
</script>

@endsection