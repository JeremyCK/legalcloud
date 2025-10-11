@extends('dashboard.base')

@section('content')
    <script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Create new document template</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif





                            <form action="{{ route('document-file.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="name">Template Name</label>
                                            <div class="col-md-9">
                                                <input class="form-control" name="name" type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="remarks">Remark</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="status">Folder</label>
                                            <div class="col-md-9">
                                                <select id="folder_id" class="form-control" name="folder_id" required>
                                                    @foreach ($fileFolder as $index => $folder)
                                                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>




                                        <div class="form-group row hide">
                                            <label class="col-md-3 col-form-label" for="status">Status</label>
                                            <div class="col-md-9"><select class="form-control" id="status"
                                                    name="status">
                                                    {{-- <option value="1" >Active</option> --}}
                                                    <option value="0">Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <button class="btn btn-primary float-right" type="submit">Save</button>
                                <a class="btn btn-danger" href="{{ route('document-file.index') }}">Return</a>
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
    <!-- <script>
        CKEDITOR.replace('summary-ckeditor');
        CKEDITOR.config.height = 500;
    </script> -->
@endsection
