@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h4>KPI Overdue</h4>
          </div>
          <div class="card-body">

            <div class="row">
              <div class="col-12">
                <h4 class="card-title mb-0">{{$users->name}}</h4>
                <div class="small text-medium-emphasis">Today to do list</div>
              </div>
            </div>
            <br>

            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Case Id</th>
                    <th>Checklist</th>
                    <th>Status</th>
                    <th>Target Close date</th>
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



<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script>
  var table;
</script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $(function() {



    table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('user_kpi_list.list', $users->id) }}",
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'case_ref_no',
          name: 'case_ref_no',
        },
        {
          data: 'name',
          name: 'name'
        },
        {
          data: 'status',
          name: 'status'
        },
        {
          data: 'target_close_date',
          name: 'target_close_date'
        },
      ]
    });


  });
</script>
@endsection