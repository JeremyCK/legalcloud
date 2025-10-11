<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h4 class="card-title mb-0 flex-grow-1"><i class="cil-user"></i> Team</h4>

            </div>
            <div class="col-6">
                @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::EditClientPermission()) == true)
                    <div class="btn-group float-right" style="margin-left:10px;">
                        <button type="button" class="btn btn-info btn-flat">Action</button>
                        <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu" style="padding:0">



                            @if ($referral != null)
                                <a class="dropdown-item btn-info" target="_blank"
                                    href="/referral/{{ $referral->id }}/edit" style="color:white;margin:0"
                                    class="btn btn-xs btn-primary"><i style="margin-right: 10px;"
                                        class="fa fa-pencil"></i>Edit Referral Info</a>
                            @endif
                            @if(in_array($case->status, [1,2,3]))
                                <a class="dropdown-item btn-warning" href="javascript:void(0)" data-backdrop="static"
                                data-keyboard="false" style="color:white;margin:0" data-toggle="modal"
                                onclick="loadReferralList()" data-target="#modalReferral"
                                class="btn btn-xs btn-primary"><i style="margin-right: 10px;"
                                    class="fa fa-refresh"></i>Change Referral</a>
                            @endif
                            

                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
    <div class="card-body">

        <table class="table mb-0">
            <tbody>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-xl-3 col-lg-4">
                                <div class="c-avatar"><img class="c-avatar-img"
                                        src="../assets/img/avatars/img-default-profile.png"></div>
                            </div>

                            <div class="col-xl-9 col-lg-8">
                                <div>{{ $lawyer->name }}</div>
                                <div class=" text-muted"><span>Lawyer</span></div>
                            </div>
                        </div>
                    </td>
                </tr>

                @if ($clerk != null)
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-xl-3 col-lg-4">
                                    <div class="c-avatar"><img class="c-avatar-img"
                                            src="../assets/img/avatars/img-default-profile.png"></div>
                                </div>

                                <div class="col-xl-9 col-lg-8">
                                    <div>{{ $clerk->name }}</div>
                                    <div class=" text-muted"><span>Clerk</span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif

                @if ($sales != null)
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-xl-3 col-lg-4">
                                    <div class="c-avatar"><img class="c-avatar-img"
                                            src="../assets/img/avatars/img-default-profile.png"></div>
                                </div>

                                <div class="col-xl-9 col-lg-8">
                                    <div>{{ $sales->name }}</div>
                                    <div class=" text-muted"><span>Sales</span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif

                @if ($referral != null)
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-xl-3 col-lg-4">
                                    <div class="c-avatar"><img class="c-avatar-img"
                                            src="../assets/img/avatars/img-default-profile.png"></div>
                                </div>

                                <div class="col-xl-9 col-lg-8">
                                    <div>{{ $referral->name }}</div>
                                    <div class=" text-muted"><span>Referral</span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
