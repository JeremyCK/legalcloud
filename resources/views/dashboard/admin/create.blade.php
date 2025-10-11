@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">

                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <div class="card ">

                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4> Add new Staff </h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('users.index') }}">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            @if (Session::has('error'))
                                <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
                            @endif
                            <form method="POST" action="{{ route('users.store') }}">
                                @csrf
                                <div class="row">



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>{{ __('coreuiforms.users.username') }} <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text"
                                                    placeholder="{{ __('coreuiforms.users.username') }}" name="name"
                                                    required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>{{ __('Initial') }} <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" placeholder="{{ __('Initial') }}"
                                                    name="nick_name" required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>{{ __('coreuiforms.users.email') }} <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" type="text"
                                                    placeholder="{{ __('coreuiforms.users.email') }}" name="email"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>IC Display name</label>
                                                <input class="form-control" type="text" placeholder="IC Display name"
                                                    name="ic_name" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Branch <span class="text-danger">*</span></label>
                                                <select class="form-control" name="branch_id" required>
                                                    <option value="">-- Please select Branch -- </option>
                                                    @foreach ($branchList as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Role <span class="text-danger">*</span></label>
                                                <select onchange="roleChange()" class="form-control" name="role"
                                                    id="role" required>
                                                    <option value="">-- Please select Role -- </option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->name }}"
                                                            data-role-id="{{ $role->id }}">
                                                            @if (in_array($current_user->menuroles, ['admin', 'management']))
                                                                @if (in_array($role->name, ['maker']))
                                                                    Branch Account
                                                                @else
                                                                    {{ $role->name }}
                                                                @endif
                                                            @else
                                                                {{ $role->name }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>BC No</label>
                                            <input class="form-control" type="text" placeholder="BC No" name="bc_no"  autocomplete="off">
                                        </div>
                                    </div>
                                </div> --}}



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>{{ __('Password') }}(System will generate default password)</label>
                                                <input class="form-control" type="password" disabled
                                                    placeholder="{{ __('Password') }}" value="password" name="password"
                                                    required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>{{ __('coreuiforms.users.phone_no') }}</label>
                                                <input class="form-control" type="text"
                                                    placeholder="{{ __('coreuiforms.users.phone_no') }}" name="phone_no"
                                                    autofocus>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>{{ __('coreuiforms.users.office_no') }}</label>
                                            <input class="form-control" type="text" placeholder="{{ __('coreuiforms.users.office_no') }}" name="office_no" autofocus>
                                        </div>
                                    </div>
                                </div> --}}

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 hide">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Max file</label>
                                                <input class="form-control" type="number" placeholder="Max file"
                                                    value="99" name="max_file">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 hide">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Min file</label>
                                                <input class="form-control" type="number" placeholder="Min file"
                                                    value="99" name="min_file">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-form-label col-5 required" for="customer_acc">Is
                                                Sales</label>
                                            <div class="col-sm-2">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input " type="checkbox" name="is_sales"
                                                        id="is_sales">
                                                    <label class="form-check-label" for="is_sales"></label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-12">

                                        <hr />
                                        <div class="nav-tabs-custom nav-tabs-custom-ctr">
                                            <ul class="nav nav-tabs scrollable-tabs" role="tablist">
                                                <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                        id="a_permission" href="#permission" role="tab"
                                                        aria-controls="home" aria-selected="false">Permission</a></li>


                                                <li class="nav-item"><a class="nav-link " data-toggle="tab"
                                                        id="a_case" href="#case" role="tab"
                                                        aria-controls="home" aria-selected="false">Cases accessbility </a>
                                                </li>

                                                <li class="nav-item handle_case" style="display: none"><a
                                                        class="nav-link " id="a_portfolio" data-toggle="tab"
                                                        href="#portfolio" role="tab" aria-controls="profile"
                                                        aria-selected="false">Portfolio</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="permission" role="tabpanel">
                                                    <h4>Permissions </h4>
                                                    <span>* Permissions already set based on role, only change if want to
                                                        grant this user special permissions</span>
                                                    <hr />

                                                    <div class="row">
                                                        @foreach ($UserAccessControlType as $rowMain)
                                                            <div class="col-12 mt-5">
                                                                <h5><u><b>{{ $rowMain->type_name }}</b></u></h5>
                                                            </div>

                                                            @if (count($UserAccessControl) > 0)
                                                                @foreach ($UserAccessControl as $row)
                                                                    @if ($row->type_name == $rowMain->type_name)
                                                                        @php
                                                                            $role_class = '';
                                                                            $role_id_list = explode(
                                                                                ',',
                                                                                $row->role_id_list,
                                                                            );
                                                                            if (count($role_id_list) > 0) {
                                                                                foreach ($role_id_list as $role_id) {
                                                                                    if ($role_id != '') {
                                                                                        $role_class .=
                                                                                            ' role_' . $role_id;
                                                                                    }
                                                                                }
                                                                            }

                                                                        @endphp
                                                                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                            <div class="form-group row">
                                                                                <label
                                                                                    class="col-form-label col-5 required"
                                                                                    for="customer_acc">{{ $row->name }}</label>
                                                                                <div class="col-sm-2">
                                                                                    <div class="form-check form-switch">
                                                                                        {{-- <input class="form-check-input row_all {{$role_class}}"   type="checkbox" value="{{ $row->code }}" name="switch" id="switch_{{ $row->id }}"> --}}
                                                                                        <input
                                                                                            class="form-check-input role_all {{ $role_class }}"
                                                                                            type="checkbox"
                                                                                            value="{{ $row->code }}"
                                                                                            name="permissions[]"
                                                                                            id="switch_{{ $row->id }}">
                                                                                        <label class="form-check-label"
                                                                                            for="switch_{{ $row->id }}"></label>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="tab-pane " id="case" role="tabpanel">

                                                    <h4>Cases accessbility </h4>
                                                    <span>* Initial permission user will able to access their own case
                                                        special permission</span><br />
                                                    <span>* Management & Account can view all cases by default</span><br />
                                                    <hr />



                                                    <div class="form-group row">
                                                        @if (count($branchList) > 0)
                                                            <div class="col-12">

                                                                <h4 class="card-title mb-0 flex-grow-1">Branch</h4>
                                                                <span>* Allow this user to access all cases in following
                                                                    branch</span><br />
                                                                <hr />

                                                            </div>

                                                            @foreach ($branchList as $row)
                                                                {{-- @php
                                                                    $role_class = '';
                                                                    $checked = '';

                                                                    if ($user->branch_case != null) {
                                                                        $branch_case_access = json_decode(
                                                                            $user->branch_case,
                                                                        );

                                                                        if (in_array($row->id, $branch_case_access)) {
                                                                            $checked = 'checked';
                                                                        }
                                                                    }

                                                                @endphp --}}

                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <label class="col-form-label col-5 required"
                                                                            for="customer_acc">{{ $row->name }}</label>
                                                                        <div class="col-sm-2">
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input role_all "
                                                                                    type="checkbox"
                                                                                    value="{{ $row->id }}"
                                                                                    name="branch_case[]"
                                                                                    id="branch_case_{{ $row->id }}">
                                                                                <label class="form-check-label"
                                                                                    for="branch_case_{{ $row->id }}"></label>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endforeach



                                                            <div class="col-12 mt-5">
                                                                <hr />
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <h4 class="card-title mb-0 flex-grow-1">Link User's
                                                                            Case</h4>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <a class="btn btn-lg btn-info  float-right"
                                                                            href="javascript:void(0)"
                                                                            data-backdrop="static" data-keyboard="false"
                                                                            data-toggle="modal" data-target="#modalLink">
                                                                            Add user</a>
                                                                    </div>
                                                                </div>
                                                                <span>* Allow this user to access following users
                                                                    cases</span>
                                                                <hr />

                                                            </div>

                                                            <div class="col-12">
                                                                <ul class="list-group" id="ul-link-user">
                                                                    <input type="hidden" id="link_case_user"
                                                                        name="link_case_user" value="" />
                                                                    {{-- @foreach ($LinkCaseUser as $index => $row)
                                                                    <li id="link_case_user_{{$row->id}}" class="list-group-item">
                                                                        {{$row->name}}
                                                                        <a href="javascript:void(0)" onclick="removeLinkUser({{$row->id}})" class=" float-right"><i class="fa fa-close text-danger"></i></a>
                                                                    </li>
                                                                    @endforeach --}}
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="tab-pane " id="portfolio" role="tabpanel">
                                                    <h4>Portfolio </h4>
                                                    <span></span>

                                                    <input class="form-check-input " onchange="checkAllController()"
                                                        type="checkbox" value="0" name="checkall" id="checkall"
                                                        checked>
                                                    <label class="form-check-label" for="checkall">Check All</label>
                                                    <hr />

                                                    <div class="form-group row">
                                                        @if (count($Portfolio) > 0)
                                                            @foreach ($Portfolio as $row)
                                                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                                                    <div class="form-group row">
                                                                        <label class="col-form-label col-5 required"
                                                                            for="customer_acc">{{ $row->name }}</label>
                                                                        <div class="col-sm-2">
                                                                            <div class="form-check form-switch">
                                                                                <input
                                                                                    class="form-check-input portfolio_all"
                                                                                    type="checkbox"
                                                                                    value="{{ $row->id }}"
                                                                                    name="portfolios[]"
                                                                                    id="portfolio_{{ $row->id }}"
                                                                                    checked>
                                                                                <label class="form-check-label"
                                                                                    for="portfolio_{{ $row->id }}"></label>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>


                                                <hr />

                                            </div>








                                            {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                   
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Race</label>
                                            <select class="form-control" id="race" name="race">
                                                <option value="0" >-- Please select race -- </option>
                                                <option value="1" >Chinese</option>
                                                <option value="2" >Malay</option>
                                                <option value="3" >Indian</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}
                                        </div>


                                        <button class="btn btn-success float-right"
                                            type="submit">{{ __('coreuiforms.save') }}</button>
                                        <a href="{{ route('users.index') }}"
                                            class="btn btn-danger">{{ __('coreuiforms.return') }}</a>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    @include('dashboard.admin.modal.modal-link-user')
@endsection

@section('javascript')
    <script>
        function checkAllController() {
            $('.portfolio_all').prop('checked', $('#checkall').is(':checked'));
        }

        function roleChange() {
            role_id = $("#role").val();

            $('.role_all').prop('checked', false);

            if (role_id != 0) {
                $role_id = $("#role").find(':selected').attr('data-role-id');
                $('.role_' + $role_id).prop('checked', true);
                // role_id

                // console.log($role_id);
                console.log(role_id);
                $handle_case = ['lawyer', 'clerk'];
                console.log($handle_case.includes(role_id));

                if ($handle_case.includes(role_id) == false) {
                    $(".handle_case").hide();
                    $('.role_all').prop('checked', false);

                    $("#a_permission").addClass('active');
                    $("#a_portfolio").removeClass('active');
                    $("#permission").addClass('active');
                    $("#portfolio").removeClass('active');
                    $("#case").removeClass('active');
                    $("#a_case").removeClass('active');


                } else {
                    $(".handle_case").show();
                }

                if (role_id == 'sales') {
                    $('#is_sales').prop('checked', true);
                } else {
                    $('#is_sales').prop('checked', false);
                }
            }

        }
    </script>
@endsection
