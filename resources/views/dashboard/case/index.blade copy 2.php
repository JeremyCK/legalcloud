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
            <h4>Cases</h4>
          </div>
          <div class="card-body">
          @if($current_user->id == 1)
            <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right' onclick="adminUpdateValue()" value='Update All value for admin' />
            <input type='button' class='btn btn-finish btn-fill btn-info btn-wd float-right' onclick="updateAllcheckListDate()" value='Update All checklist date for admin' />
            @endif
           


            <div class="row">
            @if($allowCreateCase == "true")
            <div class="col-3 ">
                <div class="form-group  ">
                  <a class="btn btn-lg btn-primary" href="{{ route('case.create') }}">{{ __('coreuiforms.case.add_new_case') }}</a>
                </div>
              </div>
            @endif

             

              <div class="col-6">
                <div class="form-group  ">
                  <div class="input-group">
                    <input type="search" name="case_ref_no_search" id="case_ref_no_search" class="form-control" placeholder="Search case">
                    <span class="input-group-btn">
                      <button type="button" onclick="filterCase()" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>

              <!-- <div class="col-3 ">
                <div class="form-group  float-right">
                  <a class="btn btn-lg btn-info" href="javascript:void(0)" onclick="clearSearch()">Clear search</a>
                </div>
              </div> -->






            </div>

            @if($current_user->menuroles == 'admin' || $current_user->menuroles == 'management')
            <div class="row">
              <div class="col-4 ">
                <div class="form-group ">
                  <label>Lawyer</label>

                  <select id="ddl-lawyer" class="form-control" name="ddl-lawyer" required>
                    <option value="0">-- Filter by lawyer --</option>
                    @foreach($lawyerList as $index => $lawyers)
                    <option value="{{ $lawyers->id }}">{{ $lawyers->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-4 ">
                <div class="form-group ">
                  <label>Clerk</label>

                  <select id="ddl-clerk" class="form-control" name="ddl-clerk" required>
                    <option value="0">-- Filter by clerk --</option>
                    @foreach($clerkList as $index => $clerk)
                    <option value="{{ $clerk->id }}">{{ $clerk->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-4 ">
                <div class="form-group ">
                  <label>Lawyer</label>

                  <select id="ddl-chamber" class="form-control" name="ddl-chamber" required>
                    <option value="0">-- Filter by chambering --</option>
                    @foreach($chamberList as $index => $chamber)
                    <option value="{{ $chamber->id }}">{{ $chamber->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-4 ">
                <div class="form-group ">
                  <label>Branch</label>

                  <select id="ddl-branch" class="form-control" name="ddl-branch" required>
                    <option value="0">-- Filter by Branch --</option>
                    @foreach($branch as $index => $bran)
                    <option value="{{ $bran->id }}">{{ $bran->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              
              <div class="col-4 ">
                <div class="form-group ">
                  <label>Case Type</label>

                  <select id="ddl-portfolio" class="form-control" name="ddl-portfolio" required>
                    <option value="0">-- Filter by Case Type --</option>
                    @foreach($portfolio as $index => $port)
                    <option value="{{ $port->id }}">{{ $port->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            @endif





            <br>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr>
                  <th>No </th>
                  <th>Case Number <a href="" class="btn btn-info btn-xs rounded shadow  mr-1" data-toggle="tooltip" data-placement="top" title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a></th>
                  <!-- <th>Type</th> -->
                  <th>Client</th>
                  <th>Open file</th>
                  <th>SPA Date</th>
                  <th>Completion Date</th>
                  <th>Status</th>
                  <th>Percentage</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tbl-data">
                @include('dashboard.case.table.tbl-case-list')
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
  document.getElementById("ddl-lawyer").onchange = function() {
    if ($("#ddl-lawyer").val() != "0") {
      $("#ddl-clerk").val("0");
      $("#ddl-chamber").val("0");
      filterCaseByRole(7, $("#ddl-lawyer").val());
    }

  }

  document.getElementById("ddl-branch").onchange = function() {
    if ($("#ddl-branch").val() != "0") {
      $("#ddl-clerk").val("0");
      $("#ddl-lawyer").val("0");
      $("#ddl-chamber").val("0");
      
      if($("#ddl-branch").val() == 2)
      {
        filterCaseByBranch(7, $("#ddl-branch").val());
      }
      else{
        location.reload();
      }
    }

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
      url: 'filter_case',
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

    // var formData = new FormData();

    // $('#case_ref_no_search').val('');

    // formData.append('case_ref_no_search', $("#case_ref_no_search").val());

    // $.ajax({
    //   type: 'POST',
    //   url: 'filter_case',
    //   data: formData,
    //   processData: false,
    //   contentType: false,
    //   success: function(data) {
    //     $('#tbl-data').html(data.view);
    //     // $('ul.pagination').replaceWith(data.links);
    //   }
    // });
  }

  @if($current_user->menuroles == 'admin')
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