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
                        <h4>Edit Step</h4>
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
                                                                <input class="form-control" type="text" name="name" value="{{ $caseTemplate->name }}" required>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>Remark</label>
                                                                <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $caseTemplate->remarks}}</textarea>
                                                            </div>
                                                        </div>


                                                        <div class="form-group row">
                                                            <div class="col">
                                                                <label>{{ __('coreuiforms.notes.status') }}</label>
                                                                <select class="form-control" name="status">
                                                                    <option value="1" @if ($caseTemplate->status ==1) selected @endif>Active</option>
                                                                    <option value="0" @if ($caseTemplate->status ==0) selected @endif>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <button class="btn btn-primary float-right" type="button" onclick="updateChecklistStep('{{ $caseTemplate->id }}')">Save</button>
                                                        <a class="btn btn-danger" href="/checklist-item/{{ $caseTemplate->id }}">Return</a>

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
    function updateChecklistStep(id) {
        var form_data = new FormData();

        $.ajax({
            type: 'POST',
            url: '/update_checklist_step/' + id,
            data: $('#form_new').serialize(),
            success: function(results) {
                console.log(results);
                if (results.status == 1) {
                    Swal.fire(
                        'Success!', 'Step updated',
                        'success'
                    )
                    window.location.href = '/checklist-item/' + id;
                    // location.reload();
                }

            }
        });
    }
</script>
@endsection