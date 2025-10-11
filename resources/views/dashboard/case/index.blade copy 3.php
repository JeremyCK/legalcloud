@extends('dashboard.base')


<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    @if($current_user->menuroles != 'receptionist' && $current_user->menuroles != 'account')
    <div class="row hide">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-aqua" style="padding-top: 17px;"><i class="cil-folder-open"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $openCaseCount }}</span>
                <span class="info-box-text">Open case</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box" onclick="alert(4);">
              <span class="info-box-icon bg-green" style="padding-top: 17px;"><i class="cil-check"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $closedCaseCount }}</span>
                <span class="info-box-text">Closed Case</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix visible-sm-block"></div>

          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-purple" style="padding-top: 17px;"><i class="cil-running"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $InProgressCaseCount }}</span>
                <span class="info-box-text">In progress case</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-xl-3 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-red" style="padding-top: 17px;"><i class="cil-av-timer"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $OverdueCaseCount }}</span>
                <span class="info-box-text">Overdue cases</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
      </div>
    </div>
    @endif



    <div class="row">
      <div class="col-sm-12">




        <div class="card" style="width:100%;overflow-x:auto">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h4>Cases</h4>
              </div>

              @if($allowCreateCase == "true")
              <div class="col-6">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('case.create') }}">
                  <i class="cil-plus"> </i>{{ __('coreuiforms.case.add_new_case') }}
                </a>
              </div>
              @endif
            </div>

          </div>
          <div class="card-body">
           



            <div class="row">
              <div class="col-12">
              @if($current_user->id == 1)
            <input type='button' class='btn btn-finish btn-fill btn-success btn-wd float-right' onclick="adminUpdateValue()" value='Update All value for admin' />
            <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right' onclick="updateAllcheckListDate()" value='Update All checklist date for admin' />
            @endif
              </div>




              <!-- <div class="col-6">
                <div class="form-group  ">
                  <div class="input-group">
                    <input type="search" name="case_ref_no_search" id="case_ref_no_search" class="form-control" placeholder="Search case">
                    <span class="input-group-btn">
                      <button type="button" onclick="filterCase()" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div> -->

              <!-- <div class="col-3 ">
                <div class="form-group  float-right">
                  <a class="btn btn-lg btn-info" href="javascript:void(0)" onclick="clearSearch()">Clear search</a>
                </div>
              </div> -->






            </div>

            <div class="row">
              <div class="col-12 ">
                <div class="accordion" id="accordion" role="tablist">
                  <div class="card mb-0">
                    <div class="card-header" id="headingOne" role="tab">
                      <h5 class="mb-0">Filter <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">[Expand/Collapse]</a></h5>
                    </div>
                    <div class="collapse show" id="collapseOne" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
                      <div class="card-body">
                        <div class="row">
                          @if($current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'sales')
                          <div class="col-6 col-xl-4">
                            <div class="form-group ">
                              <label>Lawyer</label>

                              <select id="ddl-lawyer" class="form-control" name="ddl-lawyer" required>
                                <option value="0">-- All --</option>
                                @foreach($lawyerList as $index => $lawyers)
                                <option value="{{ $lawyers->id }}">{{ $lawyers->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="col-6 col-xl-4">
                            <div class="form-group ">
                              <label>Clerk</label>

                              <select id="ddl-clerk" class="form-control" name="ddl-clerk" required>
                                <option value="0">-- All --</option>
                                @foreach($clerkList as $index => $clerk)
                                <option value="{{ $clerk->id }}">{{ $clerk->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="col-6 col-xl-4">
                            <div class="form-group ">
                              <label>Chambering</label>

                              <select id="ddl-chamber" class="form-control" name="ddl-chamber" required>
                                <option value="0">-- All --</option>
                                @foreach($chamberList as $index => $chamber)
                                <option value="{{ $chamber->id }}">{{ $chamber->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          @endif

                          <div class="col-6 col-xl-4">
                            <div class="form-group ">
                              <label>Status</label>

                              <select id="ddl_status" class="form-control" name="ddl_status">
                                <option value="">-- All --</option>
                                <option value="2">Open</option>
                                <option value="1">Running</option>
                                <option value="3">KIV</option>
                              </select>
                            </div>
                          </div>

                          <div class="col-6 col-xl-4">
                            <div class="form-group ">
                              <label>Case Type</label>

                              <select id="ddl_portfolio" class="form-control" name="ddl_portfolio" required>
                                <option value="0">-- All --</option>
                                @foreach($portfolio as $index => $port)
                                <option value="{{ $port->id }}">{{ $port->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="col-6 col-xl-4">
                            <div class="form-group ">
                              <label>Month</label>
                              <select id="ddl_month" class="form-control" name="ddl_month" required>
                                <option value="0">-- All --</option>
                                <option value="1">January</option>
                                <option value='2'>February</option>
                                <option value='3'>March</option>
                                <option value='4'>April</option>
                                <option value='5'>May</option>
                                <option value='6'>June</option>
                                <option value='7'>July</option>
                                <option value='8'>August</option>
                                <option value='9'>September</option>
                                <option value='10'>October</option>
                                <option value='11'>November</option>
                                <option value='12'>December</option>
                              </select>
                            </div>
                          </div>

                          <div class="col-sm-12">
                            <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)" onclick="reloadTable();">
                              <i class="fa cil-search"> </i>Filter
                            </a>
                          </div>

                        </div>

                      </div>
                    </div>
                  </div>
                </div>
              </div>






            </div>






            <br />


            <div class="row">
              <div class="col-12">
                <div class="tab-content" style="padding:30px;">
                  <div class="tab-pane  @if($current_user->branch_id == 1 ) active @endif" id="uptown" role="tabpanel">
                    <table id="tblCase" class="table table-bordered table-striped yajra-datatable" style="width:100%">
                      <thead>
                        <tr class="text-center">
                          <th>Action</th>
                          <th>Case Number <a href="" class="btn btn-info btn-xs rounded shadow  mr-1" data-toggle="tooltip" data-placement="top" title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></th>
                          <!-- <th>Type</th> -->
                          <th>Client</th>
                          <th>Open file</th>
                          <th>SPA Date</th>
                          <th>Completion Date</th>
                          <!-- <th>Status</th> -->
                          <th>Latest Notes</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>

                  <div class="tab-pane @if($current_user->branch_id == 2 ) active @endif" id="puchong" role="tabpanel">
                    <table id="tblCasePuchong" class="table table-bordered table-striped yajra-datatable" style="width:100%">
                      <thead>
                        <tr class="text-center">
                          <th>Action</th>
                          <th>Case Number <a href="" class="btn btn-info btn-xs rounded shadow  mr-1" data-toggle="tooltip" data-placement="top" title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></th>
                          <!-- <th>Type</th> -->
                          <th>Client</th>
                          <th>Open file</th>
                          <th>SPA Date</th>
                          <th>Completion Date</th>
                          <!-- <th>Status</th> -->
                          <th>Latest Notes</th>
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
    </div>
  </div>
</div>

<div id="modalStatus" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="form_add">
          <input type="hidden" value="0" id="txtId" name="txtId" />

          <div class="form-group row ">
            <div class="col">
              <label>Status</label>
              <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>
            </div>
          </div>

      </div>
      </form>
      <div class="modal-footer">
        <button type="button" id="btnClose" class="btn btn_close_all btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success float-right" onclick="updateStatus()">Save
          <div class="overlay" style="display:none">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </button>
      </div>
    </div>

  </div>
</div>



@endsection

@section('javascript')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
  // document.getElementById("ddl-lawyer").onchange = function() {
  //   if ($("#ddl-lawyer").val() != "0") {
  //     $("#ddl-clerk").val("0");
  //     $("#ddl-chamber").val("0");
  //     filterCaseByRole(7, $("#ddl-lawyer").val());
  //   }

  // }

  // document.getElementById("ddl-branch").onchange = function() {
  //   if ($("#ddl-branch").val() != "0") {
  //     $("#ddl-clerk").val("0");
  //     $("#ddl-lawyer").val("0");
  //     $("#ddl-chamber").val("0");

  //     if ($("#ddl-branch").val() == 2) {
  //       filterCaseByBranch(7, $("#ddl-branch").val());
  //     } else {
  //       location.reload();
  //     }
  //   }

  // }

  function updateCaseStatus($case_id, type) {
    $confirmationMSG = '';
    $SuccessMSG = '';

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    if (type == 'CLOSED') {
      $confirmationMSG = 'Close this case?';
      $SuccessMSG = 'Case closed';
    } else if (type == 'ABORTED') {
      $confirmationMSG = 'Abort this case?';
      $SuccessMSG = 'Case aborted';
    }

    var form_data = new FormData();

    form_data.append('type', type);

    Swal.fire({
      icon: 'warning',
      text: $confirmationMSG,
      showCancelButton: true,
      confirmButtonText: `Yes`,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          url: '/updateCaseStatus/' + $case_id,
          data: form_data,
          processData: false,
          contentType: false,
          success: function(data) {
            console.log(data);
            if (data.status == 1) {

              Swal.fire('Success!', $SuccessMSG, 'success');
              reloadTable();
              // window.location.href = '/case';
            } else {
              Swal.fire('notice!', data.message, 'warning');
            }

          }
        });
      }
    })

  }

  $(function() {
    reloadTable();
  });



  function reloadTable() {
    table = $('#tblCase').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      ajax: {
        url: "{{ route('case_list.list') }}",
        data: {
          "status": $("#ddl_status").val(),
          "case_type": $("#ddl_portfolio").val(),
          "lawyer": $("#ddl-lawyer").val(),
          "clerk": $("#ddl-clerk").val(),
          "branch": 1,
          "chambering": $("#ddl-chamber").val(),
          "month": $("#ddl_month").val()
        }
      },
      columns: [{
          data: 'action',
          className: "text-center",
          name: 'action',
          orderable: true,
          searchable: true
        },
        {
          data: 'case_ref_no',
          name: 'case_ref_no'
        },
        {
          data: 'client_name',
          name: 'client_name'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'spa_date',
          name: 'spa_date'
        },
        {
          data: 'completion_date',
          name: 'completion_date'
        },
        // {
        //   data: 'status',
        //   className: 'text-center',
        //   name: 'status'
        // },
        {
          data: 'notes',
          name: 'notes',
        },
      ]
    });

    table = $('#tblCasePuchong').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      ajax: {
        url: "{{ route('case_list.list') }}",
        data: {
          "status": $("#ddl_status").val(),
          "case_type": $("#ddl_portfolio").val(),
          "lawyer": $("#ddl-lawyer").val(),
          "clerk": $("#ddl-clerk").val(),
          "branch": 2,
          "chambering": $("#ddl-chamber").val(),
          "month": $("#ddl_month").val()
        }
      },
      columns: [{
          data: 'action',
          className: "text-center",
          name: 'action',
          orderable: true,
          searchable: true
        },
        {
          data: 'case_ref_no',
          name: 'case_ref_no'
        },
        {
          data: 'client_name',
          name: 'client_name'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'spa_date',
          name: 'spa_date'
        },
        {
          data: 'completion_date',
          name: 'completion_date'
        },
        // {
        //   data: 'status',
        //   className: 'text-center',
        //   name: 'status'
        // },
        {
          data: 'notes',
          name: 'notes',
        },
      ]
    });
  }

  document.getElementById("ddl-clerk").onchange = function() {
    if ($("#ddl-clerk").val() != "0") {
      $("#ddl-lawyer").val("0");
      $("#ddl-chamber").val("0");
      filterCaseByRole(8, $("#ddl-clerk").val());
    }
  }

  document.getElementById("ddl-chamber").onchange = function() {
    if ($("#ddl-chamber").val() != "0") {
      $("#ddl-lawyer").val("0");
      $("#ddl-clerk").val("0");
      filterCaseByRole(11, $("#ddl-chamber").val());
    }
  }

  function filterCaseByRole(role_id, id) {
    var formData = new FormData();


    formData.append('role_id', role_id);
    formData.append('id', id);

    $.ajax({
      type: 'POST',
      url: 'filter_case_by_role',
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#tbl-data').html(data.view);
        // $('ul.pagination').replaceWith(data.links);
      }
    });
  }

  function filterCaseByBranch(role_id, branch_id) {
    var formData = new FormData();


    formData.append('branch_id', branch_id);
    // formData.append('id', id);

    $.ajax({
      type: 'POST',
      url: 'filter_case_by_branch',
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#tbl-data').html(data.view);
        // $('ul.pagination').replaceWith(data.links);
      }
    });
  }

  function filterCase() {
    var formData = new FormData();

    if ($('#case_ref_no_search').val() == '') {
      return;
    }

    formData.append('case_ref_no_search', $("#case_ref_no_search").val());

    $.ajax({
      type: 'POST',
      url: 'getSearchCase',
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#tbl-data').html(data.view);
        // $('ul.pagination').replaceWith(data.links);
      }
    });
  }

  function clearSearch() {
    location.reload();
  }

  @if($current_user-> menuroles == 'admin')

  function adminUpdateValue() {
    var formData = new FormData();

    formData.append('case_ref_no_search', $("#case_ref_no_search").val());


    alert(3);
    $.ajax({
      type: 'POST',
      url: 'adminUpdateValue',
      data: null,
      processData: false,
      contentType: false,
      success: function(data) {
        console.log(data);
      }
    });
  }

  function updateAllcheckListDate() {
    var formData = new FormData();

    formData.append('case_ref_no_search', $("#case_ref_no_search").val());


    $.ajax({
      type: 'POST',
      url: 'updateAllcheckListDate',
      data: null,
      processData: false,
      contentType: false,
      success: function(data) {
        console.log(data);
      }
    });
  }
  @endif
</script>

@endsection