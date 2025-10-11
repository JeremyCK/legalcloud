@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

<div class="container-fluid">
  <div class="fade-in">



    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-xl-4 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-warning" style="padding-top: 17px;"><i class="cil-folder-open"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $preparingCount }}</span>
                <span class="info-box-text">Sending</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-xl-4 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-green" style="padding-top: 17px;"><i class="cil-check"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $deliveredCount }}</span>
                <span class="info-box-text">Completed</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix visible-sm-block"></div>

          <!-- <div class="col-xl-4 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-purple" style="padding-top: 17px;"><i class="cil-running"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{ $sendingCount }}</span>
                <span class="info-box-text">Sending</span>
              </div>
            </div>
          </div> -->
          <!-- /.col -->

          <!-- /.col -->
        </div>
      </div>
    </div>



    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Dispatch list</h4>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-6">
                <div class="form-group row">
                  <div class="col">
                    <label>Filter by Dispatch</label>
                    <select class="form-control" id="ddl_dispatch" name="ddl_dispatch">
                      <option value="0">-- All --</option>
                      @foreach($couriers as $courier)
                      <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('dispatch.create') }}">
                  <i class="cil-plus"> </i>Create New Dispatch
                </a>
              </div>

            </div>
            <br>



            <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Action</th>
                  <!-- <th>Dispatch No</th> -->
                  <th>File Ref</th>
                  <th>Client</th>
                  <th>Send to</th>
                  <th>Job Desc</th>
                  <th>Dispatch Name</th>
                  <th>Return to office</th>
                  <th>Status</th>
                  <th>Attachment</th>
                  <th>Assgined By</th>
                  <th>Date</th>
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
<!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  document.getElementById("ddl_dispatch").onchange = function() {
    reloadTable();
  }

  function reloadTable() {
    var url = "{{ route('dispatch_list.list',['dispatchID']) }}";

    url = url.replace('dispatchID', $("#ddl_dispatch").val());

    var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      ajax: url,
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'action',
          name: 'action',
          className: "text-center",
          orderable: true,
          searchable: true
        },
        // {
        //   data: 'dispatch_no',
        //   name: 'dispatch_no',
        // },
        {
          data: 'case_ref_no',
          name: 'case_ref_no',
        },
        {
          data: 'client_name',
          name: 'client_name'
        },
        {
          data: 'send_to',
          name: 'send_to'
        },
        {
          data: 'job_desc',
          name: 'job_desc'
        },
        {
          data: 'courier_name',
          name: 'courier_name'
        },
        {
          data: 'return_to_office_datetime',
          name: 'return_to_office_datetime'
        },
        {
          data: 'status',
          className: "text-center",
          name: 'status'
        },
        {
          data: 'file',
          name: 'file'
        },
        {
          data: 'assign_by',
          name: 'assign_by'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
      ]
    });
  }

  function deleteDispatch($id)
  {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    Swal.fire({
      title: 'Delete this dispatch?',
      showCancelButton: true,
      confirmButtonText: `Yes`,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          url: '/deleteDispatch/' + $id,
          success: function(data) {
            console.log(data);
            if (data.status == 1) {

              // Swal.fire('Success!', data.message, 'success');
              toastController('Case closed');
              reloadTable();
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
</script>
@endsection