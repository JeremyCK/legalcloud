@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            
                            <div class="row">
                                <div class="col-6">
                                    <h4>{{ __('Staff Management') }}</h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-primary  float-right" href="{{ route('users.create') }}">
                                        <i class="cil-plus"> </i>{{ __('Add new Staff') }}
                                    </a>
                                </div>
                            </div>
                           
                        </div>
                        <div class="card-body">

                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row">

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by Role</label>
                                            <select class="form-control" id="ddl_role" name="ddl_role">
                                                <option value="">-- all --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">
                                                        @if(in_array($current_user->menuroles, ['admin', 'management']))
                                                            @if(in_array($role->name, ['maker']))
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

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Branch</label>
                                            <select class="form-control" id="ddl_branch" name="ddl_branch">
                                                <option value="">-- all --</option>
                                                @foreach ($branchList as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Status</label>
                                            <select class="form-control" id="ddl_status" name="ddl_status">
                                                <option value="">-- all --</option>
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            {{-- <div class="row @if (
                                $current_user->menuroles == 'lawyer' ||
                                    $current_user->menuroles == 'clerk' ||
                                    $current_user->menuroles == 'chambering') hide @endif">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Filter by PIC</label>
                                            <select class="form-control" id="ddl-role" name="role">
                                              <option value="0">-- all --</option>
                                              @foreach ($roles as $role)
                                                  <option value="{{ $role->name }}">{{ $role->name }}</option>
                                              @endforeach
                                          </select>
                                        </div>
                                    </div>
                                </div>

                            </div> --}}
                            <br>
                            {{-- <table class="table table-responsive-sm  table-bordered table-striped">
                                <thead class="text-center">
                                    <tr>
                                        <th>{{ __('coreuiforms.users.username') }}</th>
                                        <th>{{ __('coreuiforms.users.email') }}</th>
                                        <th>{{ __('coreuiforms.users.roles') }}</th>
                                        <th>Initial</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl-data">
                                    @include('dashboard.admin.table.tbl-list')
                                </tbody>
                            </table> --}}

                            <div class="col-sm-12 " style="margin-bottom:20px;">
                                <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                    onclick="reloadtable();">
                                    <i class="fa cil-search"> </i>Filter
                                </a>
                            </div>
                            <br>

                            <div class="box-body no-padding "
                                style="width:100%;overflow-x:auto;padding-bottom:100px;margin-top:20px;">

                                <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Initial</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                            {{-- <th>Action</th>
                                            <th>File Ref</th>
                                            <th>Sales</th>
                                            <th>PIC</th>
                                            <th>Client (P)</th>
                                            <th>Client (V)</th>
                                            <th>Case Date</th>
                                            <th>Completion Date</th>
                                            <th>Status</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

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
        // document.getElementById("ddl-role").onchange = function() {

        //     var formData = new FormData();
        //     formData.append('role', $("#ddl-role").val());

        //     $.ajax({
        //         type: 'POST',
        //         url: 'filter',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(data) {
        //             $('#tbl-data').html(data.view);
        //             // $('ul.pagination').replaceWith(data.links); 
        //         }
        //     });
        // }

        function reloadtable() {

            var url = "{{ route('staffList.list') }}";


            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength: 25,
                ajax: {
                    url: url,
                    data: {
                        "role": $("#ddl_role").val(),
                        "status": $("#ddl_status").val(),
                        "branch": $("#ddl_branch").val(),
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },{
                        data: 'name',
                        name: 'name'
                    },{
                        data: 'email',
                        name: 'email'
                    },{
                        data: 'nick_name',
                        name: 'nick_name'
                    },{
                        data: 'menuroles',
                        name: 'menuroles'
                    },{
                        data: 'status',
                        name: 'status'
                    },{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    // {
                    //     data: 'ref_no',
                    //     name: 'ref_no',
                    // },
                    // {
                    //     data: 'sales_name',
                    //     name: 'sales_name'
                    // },
                    // {
                    //     data: 'pic',
                    //     name: 'pic'
                    // },
                    // {
                    //     data: 'client_name_p',
                    //     name: 'client_name_p'
                    // },
                    // {
                    //     data: 'client_name_v',
                    //     name: 'client_name_v'
                    // },

                    // {
                    //     data: 'case_date',
                    //     name: 'case_date'
                    // },
                    // {
                    //     data: 'completion_date',
                    //     name: 'completion_date'
                    // },
                    // {
                    //     data: 'notes',
                    //     name: 'notes',
                    //     width: '400px'
                    // },
                ]
            });
        }

        $(function() {
            reloadtable()

        });
    </script>
@endsection
