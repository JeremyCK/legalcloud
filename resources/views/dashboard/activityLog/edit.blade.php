@extends('dashboard.base')

@section('content')


<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <!-- <div class="col-sm-3">
                <div class="card">
                    <div class="sidebar-inner position-relative ">

                        <div class="nav flex-column nav-pills faqnav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <div class="title text-uppercase mb-2 small">Getting started</div>

                            @foreach($templateEmailDetails as $index => $details)

                               
                                    <a class="nav-link {{ $details->status == 1 ? 'active show' : '' }}  " id="v-pills-Introduction-tab" data-toggle="pill" href="#v-pills-Introduction" role="tab" aria-controls="v-pills-Introduction" aria-selected="false">{{$details->version_name}}</a>
                            
                             @endforeach

                        </div>

                    </div>
                </div>
            </div> -->
            <div class="col-sm-12">



                <div class="card">
                    <div class="card-header">
                        <h4>Edit Document Template</h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <form method="POST" action="{{ route('email-template.update', $template->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-email">Template Name</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="name" value="{{ $template->name }}" type="text" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Template Description</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="desc" name="desc" rows="2">{{ $template->desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Template Short Code</label>
                                <div class="col-md-9">
                                    <input type="text" name="code" value="{{ $template->code }}" class="form-control" />
                                </div>
                            </div>
                            


                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                                <div class="col-md-9"><select class="form-control" id="status" name="status">
                                        <option value="0">Please select</option>
                                        <option value="1">Active</option>
                                        <option value="2">Draft</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="hf-password">Content</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor">{{ $templateEmailDetails[0]->content}}</textarea>
                                </div>
                            </div> -->
                            <button class="btn btn-primary float-right" type="submit">Save as new draft</button>
                            <button class="btn btn-primary float-right" type="submit">Save</button>
                            <a class="btn btn-primary" href="{{ route('email-template.index') }}">Return</a>
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