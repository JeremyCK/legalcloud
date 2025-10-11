@section('css')

<link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
@endsection
@extends('dashboard.base')
@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>New Case Template</h4>
                    </div>
                    <div class="card-body">

                        <div class="box box-default">
                            <!-- /.box-header -->
                            <div class="box-body wizard-content">
                                <div class="tab-wizard wizard-circle wizard clearfix">

                                    <div class="tab-content bg-color-none">

                                        <div class="tab-pane active" id="tab1">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <form id="form_new">
                                                        @csrf
                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Name</label>
                                                                <input class="form-control" type="text" name="display_name" value="{{ $caseTemplate->display_name }}" required>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Type</label>
                                                                <select class="form-control" name="type">
                                                                    <option value="hire_purchase">Hire Purchase</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Target Close Day</label>
                                                                <input class="form-control" type="number" name="target_close_day" value="{{ $caseTemplate->target_close_day }}" required>
                                                            </div>
                                                        </div>


                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>{{ __('coreuiforms.notes.status') }}</label>
                                                                <select class="form-control" name="status">
                                                                    <option value="1">Active</option>
                                                                    <option value="0">Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <button class="btn btn-primary float-right" type="button" onclick="createNewChecklistTemplate('{{ $caseTemplate->id }}')">Save</button>
                                                        <a class="btn btn-danger" href="/case-template/{{ $caseTemplate->id }}">Return</a>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.box-body -->
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
<script>
    function createNewChecklistTemplate(id) {
        var form_data = new FormData();

        $.ajax({
            type: 'POST',
            url: '/update_template_details/' + id,
            data: $('#form_new').serialize(),
            success: function(results) {
                console.log(results);
                if (results.status == 1) {
                    Swal.fire(
                        'Success!', 'New template created',
                        'success'
                    )
                    window.location.href = '/case-template/' + id;
                    // location.reload();
                }

            }
        });
    }
</script>
@endsection