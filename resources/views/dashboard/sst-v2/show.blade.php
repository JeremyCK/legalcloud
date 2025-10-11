@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

<div class="container-fluid">
  <div class="fade-in">

    <div class="row">
      <div class="col-sm-12">

        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h4>SST Record Details (Invoice Based)</h4>
              </div>

              <div class="col-6">
                <a class="btn btn-lg btn-info  float-right" href="{{ route('sst-v2.list') }}">
                  <i class="cil-arrow-left"> </i>Back to list
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Payment Date:</label>
                  <p class="form-control-static">{{ $SSTMain->payment_date }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Transaction ID:</label>
                  <p class="form-control-static">{{ $SSTMain->transaction_id }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Total SST Amount:</label>
                  <p class="form-control-static">RM {{ number_format($SSTMain->amount, 2) }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Branch:</label>
                  <p class="form-control-static">{{ $SSTMain->branch->name ?? 'N/A' }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Remark:</label>
                  <p class="form-control-static">{{ $SSTMain->remark ?? 'N/A' }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Created By:</label>
                  <p class="form-control-static">{{ $SSTMain->paidBy->name ?? 'N/A' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Created At:</label>
                  <p class="form-control-static">{{ $SSTMain->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
              </div>
            </div>

            <hr>

            <h5>Invoice Details</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Ref No</th>
                    <th>Client Name</th>
                    <th>Bill No</th>
                    <th>Invoice No</th>
                    <th>Total Amount</th>
                    <th>Collected Amount</th>
                    <th>SST Amount</th>
                    <th>Payment Date</th>
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
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $(function() {
    var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('sst-v2.invoice-list') }}",
        data: function(d) {
          d.type = 'transferred';
          d.transaction_id = {{ $SSTMain->id }};
        },
      },
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
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
          data: 'bill_invoice_no',
          name: 'bill_invoice_no'
        },
        {
          data: 'invoice_no',
          name: 'invoice_no'
        },
        {
          data: 'total_amt_inv',
          name: 'total_amt_inv',
          class: 'text-right',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'collected_amt',
          name: 'collected_amt',
          class: 'text-right',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'sst_inv',
          name: 'sst_inv',
          class: 'text-right',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'payment_receipt_date',
          name: 'payment_receipt_date'
        },
      ]
    });
  });
</script>
@endsection

