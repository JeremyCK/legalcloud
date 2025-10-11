@extends('dashboard.base')

@section('content')


<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create new email template</h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif

                        <form action="{{ route('email-template.store') }}" method="POST">
                            @csrf

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-email">Template Name</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="name" type="text" required />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Template Description</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Template Short Code</label>
                                <div class="col-md-9">
                                    <input type="text" name="code" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Email Subject</label>
                                <div class="col-md-9">
                                    <input type="text" name="subject" class="form-control" />
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                <div class="col-md-9"><select class="form-control" id="status" name="status">
                                        <!-- <option value="0">Please select</option> -->
                                        <option value="1">Active</option>
                                        <option value="0">Draft</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Content</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>
                                </div>
                            </div>

                            <button class="btn btn-success float-right" type="submit">Save</button>
                            <a class="btn btn-danger" href="{{ route('email-template.index') }}">Return</a>
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
    CKEDITOR.config.height = 500;
</script>

@endsection