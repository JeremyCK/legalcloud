@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Clients </h4>
          </div>
          <div class="card-body">
           
            
            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>IC</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Case count</th>
                    <th>created Date</th>
                    <th>Action</th>
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

@endsection

@section('javascript')
<script type="text/javascript">
  function reloadTable() {
    var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      ajax: {
        url: "{{ route('client.list') }}",
        data: function(d) {
          d.date_from = $("#date_from").val();
          d.date_to = $("#date_to").val();
          d.status = $("#ddl_status").val();
          d.branch = $("#ddl_branch").val();
        },
      },
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'name',
          name: 'name'
        },
        {
          data: 'ic_no',
          name: 'ic_no'
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'phone_no',
          name: 'phone_no'
        },
        {
          data: 'case_count',
          name: 'case_count'
        },
        {
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
      ]
    });
  }

  $(function() {
    reloadTable();
  });
</script>
@endsection