@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>E-Invoice Client Info </h4>
          </div>
          <div class="card-body">
           
            
            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Customer Code</th>
                    <th>Case Ref No</th>
                    <th>Customer Name</th>
                    <th>BRN</th>
                    <th>BRN2</th>
                    <th>Customer Category</th>
                    <th>ID No</th>
                    <th>TIN</th>
                    <th>Completed</th>
                    <th>Sent to SQL</th>
                    <th>Created Date</th>
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
        url: "{{ route('einvoice-client-list') }}",
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
          data: 'customer_code',
          name: 'customer_code'
        },
        {
          data: 'case_id',
          name: 'case_id'
        },
        {
          data: 'customer_name',
          name: 'customer_name'
        },
        {
          data: 'brn',
          name: 'brn'
        },
        {
          data: 'brn2',
          name: 'brn2'
        },
        {
          data: 'customer_category',
          name: 'customer_category'
        },
        {
          data: 'id_no',
          name: 'id_no'
        },
        {
          data: 'tin',
          name: 'tin'
        },
        {
          data: 'completed',
          name: 'completed'
        },
        {
          data: 'sent_to_sql',
          name: 'sent_to_sql'
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