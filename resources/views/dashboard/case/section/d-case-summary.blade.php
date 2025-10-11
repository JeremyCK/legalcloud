<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-4">
                <h4 class="card-title mb-0 flex-grow-1"> <i class="cil-balance-scale"></i> Case Summary
                </h4>
            </div>
            <div class="col-8">
                <div class="btn-group float-right" style="margin-left:10px;">
                    <button type="button" class="btn btn-info btn-flat">Action</button>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" style="padding:0">

                        @if (in_array($case->status, [1, 2, 3]))
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::SubmitReviewPermission()) == true)
                                <a class="dropdown-item btn-success" href="javascript:void(0)"
                                style="color:white;margin:0"
                                onclick="updateCaseStatus('{{ $case->id }}','REVIEWING')"><i
                                    style="margin-right: 10px;" class="fa  fa-check"></i>Submit for Review</a>
                            @endif
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::PendingClosePermission()) == true)
                                <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                style="color:white;margin:0"
                                onclick="updateCaseStatus('{{ $case->id }}','PENDINGCLOSED')"><i
                                    style="margin-right: 10px;" class="fa  fa-check"></i>Pending close case</a>
                            @endif
                            
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AbortCasePermission()) == true)
                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                onclick="checkCloseFileBalance('abort');" style="color:white;margin:0"
                                data-backdrop="static" data-keyboard="false" data-target="#modalCloseFile"
                                data-toggle="modal">
                                <i style="margin-right: 10px;" class="fa  fa-check"></i>Abort case</a>
                            @endif
                        @elseif (in_array($case->status, [4]))
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::CloseCasePermission()) == true)
                                <a class="dropdown-item btn-success" href="javascript:void(0)"
                                onclick="checkCloseFileBalance('close');" style="color:white;margin:0"
                                data-backdrop="static" data-keyboard="false" data-target="#modalCloseFile"
                                data-toggle="modal">
                                <i style="margin-right: 10px;" class="fa  fa-check"></i>Close case</a>
                            @endif
                        @elseif (in_array($case->status, [7]))
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::PendingClosePermission()) == true)
                                <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                style="color:white;margin:0"
                                onclick="updateCaseStatus('{{ $case->id }}','PENDINGCLOSED')"><i
                                    style="margin-right: 10px;" class="fa  fa-check"></i>Pending close case</a>
                            @endif
                            
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AbortCasePermission()) == true)
                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                onclick="checkCloseFileBalance('abort');" style="color:white;margin:0"
                                data-backdrop="static" data-keyboard="false" data-target="#modalCloseFile"
                                data-toggle="modal">
                                <i style="margin-right: 10px;" class="fa  fa-check"></i>Abort case</a>
                            @endif
                        @elseif (in_array($case->status, [99]) && $case->aborted_keep_track == 0)
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AbortCasePermission()) == true)
                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                onclick="checkCloseFileBalance('abort');" style="color:white;margin:0"
                                data-backdrop="static" data-keyboard="false" data-target="#modalCloseFile"
                                data-toggle="modal">
                                <i style="margin-right: 10px;" class="fa  fa-check"></i>Abort case</a>
                            @endif
                        @elseif (in_array($case->status, [0]))
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::EditCloseCasePermission()) == true)
                                <a class="dropdown-item btn-success" href="javascript:void(0)" 
                                style="color:white;margin:0" data-backdrop="static" data-keyboard="false"  data-target="#modalDateCloseFile"  data-toggle="modal" >
                                <i style="margin-right: 10px;" class="fa  fa-check"></i>Update Closed File Details</a>
                                <div class="dropdown-divider" style="margin:0"></div>
                            @endif
                        @endif

                        <a class="dropdown-item btn-info" href="javascript:void(0)" data-backdrop="static"
                        data-keyboard="false" style="color:white;margin:0" data-toggle="modal"
                        data-target="#caseSummaryModal" class="btn btn-xs btn-primary"
                        onclick="UpdateCaseSummary('{{ $case->id }}','ABORTED')"><i style="margin-right: 10px;"
                            class="fa fa-pencil"></i>Update summary</a>


                    </div>
                </div>

                @if (in_array($current_user->menuroles, ['lawyer', 'chambering', 'clerk']))
                    @if (($case->status == 0 || $case->status == 4) && $BonusRequestListSent == 0)
                        {{-- <a id="btnRequestBonus" class="btn  btn-warning float-right" href="javascript:void(0)"
                            style="color:white;margin:0" onclick="submitBonusReview('CLOSEDCASE')"><i
                                style="margin-right: 10px;" class="fa  fa-check"></i>Submit for 3% bonus review</a> --}}
                    @elseif($BonusRequestListSent == 1)
                        <span class="badge badge-warning float-right" style="padding:10px;">Reviewing 3% Bonus</span>
                    @endif

                    @if (($case->status == 1 || $case->status == 3) && $SMPBonusRequestListSent == 0)
                        <a id="btnRequestBonus" class="btn  btn-warning float-right" href="javascript:void(0)"
                            style="color:white;margin:0" onclick="submitBonusReview('SMPSIGNED')"><i
                                signed="margin-right: 10px;" class="fa  fa-check"></i>Submit for 2% bonus review</a>
                    @elseif($SMPBonusRequestListSent == 1)
                        <span class="badge badge-warning float-right" style="padding:10px;">Reviewing 2% Bonus</span>
                    @endif
                @endif
            </div>
        </div>

    </div>
    <div class="card-body ">
        <div class="row ">
            <div >

            </div>

            <table class="table mb-0">
                <tbody>
                    <tr>
                        <td class="fw-medium"><b>Ref No</b></td>
                        <td>{{ $case->case_ref_no }} <a href="javascript:void(0)"
                                class="btn btn-info btn-xs rounded shadow  mr-1" data-toggle="tooltip"
                                data-placement="top" title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></td>
                    </tr>
                    <tr>
                        <td class="fw-medium"><b>Purchase Price</b></td>
                        <td class="fw-medium"><b>RM {{ number_format($case->purchase_price, 2, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"><b>Loan Sum</b></td>
                        <td>RM {{ number_format($case->loan_sum, 2, '.', ',') }}
                        </td>
                    </tr>

                    @if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account']))
                        <tr>
                            <td class="fw-medium"><b>Agreed Fees</b></td>
                            <td class="fw-medium">RM {{ number_format($case->agreed_fee, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium"><b>Targeted Collection Amount</b></td>
                            <td class="fw-medium">RM {{ number_format($case->targeted_collect_amount, 2, '.', ',') }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td class="fw-medium"><b>Case Type </b></td>
                        <td>{{ $case->portfolio }} </td>
                    </tr>

                    <tr>
                        <td class="fw-medium"><b>Branch</b></td>
                        <td>{{ $case->branch_name }} </td>
                    </tr>

                    <tr>
                        <td class="fw-medium"><b>Bank Reference</b></td>
                        <td>{{ $case->bank_ref }} </td>
                    </tr>

                    <tr>
                        <td class="fw-medium"><b>Bank LI Date</b></td>
                        <td>{{ $case->bank_li_date }} </td>
                    </tr>

                    <tr>
                        <td class="fw-medium"><b>Bank LO Date</b></td>
                        <td>@if($bank_lo_date) {{ $bank_lo_date->value }} @endif</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"><b>Property Address</b></td>
                        <td class="">{{ $case->property_address }} </td>
                    </tr>


                </tbody>
            </table>


        </div>

        <div class="row pt-3 border-top border-top-dashed mt-4">
            <div class="col-lg-3 col-sm-6">
                <div>
                    <p class=""><b>Start Date</b></p>
                    <i class="fa fa-calendar "></i> {{ date('d-m-Y', strtotime($case->created_at)) }}
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div>
                    <p class=""><b>Completion Date</b></p>
                    <i class="fa fa-calendar "></i> {{ $case->completion_date }}
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div>
                    <p class=""><b>Day(s)</b></p>
                    {{ $datediff }} Days
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div>
                    <p class=""><b>Status</b></p>
                    @if ($case->status == 2)
                        <span class="badge badge-info">Open</span>
                    @elseif($case->status == 0)
                        <span class="badge badge-success">Closed</span>
                    @elseif($case->status == 1)
                        <span class="badge bg-purple">In progress</span>
                    @elseif($case->status == 2)
                        <span class="badge badge-danger">Overdue</span>
                    @elseif($case->status == 3)
                        <span class="badge badge-warning">KIV</span>
                    @elseif($case->status == 4)
                        <span class="badge badge-warning">Pending Close</span>
                    @elseif($case->status == 7)
                        <span class="badge bg-purple">Reviewing</span>
                    @elseif($case->status == 99)
                        <span class="badge badge-danger">Aborted</span>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
