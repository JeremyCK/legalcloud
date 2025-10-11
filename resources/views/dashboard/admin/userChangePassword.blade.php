@extends('dashboard.base')


@section('content')

<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row justify-content-center">
            <div class="col-sm-6 col-md-5 col-lg-4 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4> Change password </h4>
                    </div>
                    <div class="card-body">
                        @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                        <form id="formPassword" >
                            @csrf

                            <div class=" row">
                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">
                                <div class="form-group row">
                                    <div class="col">
                                        <label>Current Password</label>
                                        <input class="form-control" type="password" placeholder="" value="" id="current_password" name="current_password" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col">
                                        <label>New Password</label>
                                        <input class="form-control" type="password" placeholder="" value="" id="new_password" name="new_password" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col">
                                        <label>Confirm New Password</label>
                                        <input class="form-control" type="password" placeholder="" value="" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>


                            </div>

                    </div>

                    <div class="row">
                    </div>


                    <button class="btn btn-success float-right" onclick="submitPassword()" type="button">{{ __('coreuiforms.save') }}</button>
                    <!-- <a href="" class="btn btn-danger">{{ __('coreuiforms.return') }}</a> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('javascript')

<script>
    function submitPassword() {
        var form_data = new FormData();

        if(InputValidation('formPassword') == false)
        {
            return;
        }

        if ($("#new_password").val() != $("#confirm_password").val())
        {
            $("#new_password").addClass('error-input-box');
            $("#confirm_password").addClass('error-input-box');
            return;
        }

        form_data = appendFormData('formPassword');

        $.ajax({
            type: 'POST',
            url: '/update_password',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(result) {
                console.log(result);
                if (result.status == 1) {
                    Swal.fire('Success!',result.data,'success');

                    location.reload();
                }
                else
                {
                    Swal.fire('Notice!',result.data,'warning');
                }

            }
        });
    }
</script>

@endsection