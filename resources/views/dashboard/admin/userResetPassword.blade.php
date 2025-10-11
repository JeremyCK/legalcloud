@extends('dashboard.base')


@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row justify-content-center">
                <div class="col-sm-6 col-md-5 col-lg-4 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4> Reset [{{ $user->name }}] password </h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('users.index') }}">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="formPassword">
                                @csrf

                                <div class=" row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>New Password</label>
                                                <input class="form-control" type="password" placeholder="" value=""
                                                    id="new_password" name="new_password" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Confirm New Password</label>
                                                <input class="form-control" type="password" placeholder="" value=""
                                                    id="confirm_password" name="confirm_password" required>
                                            </div>
                                        </div>


                                    </div>

                                </div>

                                <div class="row">
                                </div>

                                <button class="btn btn-warning float-left" onclick="submitPassword(1)" type="button">Reset
                                    to default password "password"</button>

                                <button class="btn btn-success float-right" onclick="submitPassword(0)"
                                    type="button">{{ __('coreuiforms.save') }}</button>
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
        function submitPassword($default_pass) {
            var form_data = new FormData();

            $confirmationMSG = 'Reset password for {{ $user->name }}?';

            if ($default_pass == 1) {
                $("#new_password").val('password');
                $("#confirm_password").val('password');
            } else {
                if (InputValidation('formPassword') == false) {
                    return;
                }

                if ($("#new_password").val() != $("#confirm_password").val()) {
                    $("#new_password").addClass('error-input-box');
                    $("#confirm_password").addClass('error-input-box');

                    Swal.fire('Notice!', 'Password and confirm password not match', 'warning');
                    return;
                }
            }

            Swal.fire({

                icon: 'warning',
                text: $confirmationMSG,
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    form_data = appendFormData('formPassword');

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('resetpassword', $user->id) }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {
                                Swal.fire('Success!', result.data, 'success');
                                $("#formPassword")[0].reset();

                                // location.reload();
                            } else {
                                Swal.fire('Notice!', result.data, 'warning');
                            }

                        }
                    });
                }
            })





        }
    </script>
@endsection
