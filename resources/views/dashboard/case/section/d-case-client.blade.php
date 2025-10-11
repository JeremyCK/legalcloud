<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <h4 class="card-title mb-0 flex-grow-1"><i class="cil-user"></i> Client</h4>

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


                            <a class="dropdown-item btn-info" target="_blank" href="/clients/{{ $customer->id }}/edit"
                                style="color:white;margin:0" class="btn btn-xs btn-primary"><i
                                    style="margin-right: 10px;" class="fa fa-pencil"></i>Edit Client Info</a>
                                    
                            @if(in_array($case->status, [1,2,3]))
                            <a class="dropdown-item btn-warning" href="javascript:void(0)" data-backdrop="static"
                                data-keyboard="false" style="color:white;margin:0" data-toggle="modal"
                                onclick="loadClientList()" data-target="#modalClient" class="btn btn-xs btn-primary"><i
                                    style="margin-right: 10px;" class="fa fa-refresh"></i>Change Client</a>
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
                    <td class="fw-medium"><b>Client</b></td>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Contact No</b></td>
                    <td>{{ $customer->phone_no }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>IC No</b></td>
                    <td>{{ $customer->ic_no }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Email</b></td>
                    <td>{{ $customer->email }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Address</b></td>
                    <td>{!!  nl2br(htmlspecialchars($customer->address)) !!}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Other cases</b></td>
                    <td>
                        @if (count($ClientOtherLoanCase) > 0)
                            @foreach ($ClientOtherLoanCase as $index => $clientCase)
                                <a target="_blank" href="/case/{{ $clientCase->id }}">{{ $clientCase->case_ref_no }}
                                    <i class="cil-chevron-double-right"></i></a> <br />
                            @endforeach
                        @else
                            -
                        @endif

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
