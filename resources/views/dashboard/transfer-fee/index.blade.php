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
                <h4>Transfer Fee List</h4>
              </div>

              <div class="col-6">
                <a class="btn btn-lg btn-primary  float-right" href="/transfer-fee-create">
                  <i class="cil-plus"> </i>Create New transfer Fee
                </a>
              </div>
            </div>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div class="row">


              <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                <div class="form-group row">
                  <div class="col">
                    <label>Transfer date from</label>
                    <input class="form-control" type="date" id="transfer_date_from" name="transfer_date_from">
                  </div>
                </div>
              </div>


              <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                <div class="form-group row">
                  <div class="col">
                    <label>Transfer date to</label>
                    <input class="form-control" type="date" id="transfer_date_to" name="transfer_date_to">
                  </div>
                </div>
              </div>

              @if(in_array($current_user->menuroles, ['admin','sales','account']))
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                  <div class="form-group row">
                      <div class="col">
                          <label>Branch</label>
                          <select class="form-control"  id="branch_id" name="branch_id">
                            <option value="0"> -- All branch -- </option>
                              @foreach($Branchs as $index => $branch)
                              <option value="{{$branch->id}}">{{$branch->name}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
              </div>
              @endif


              <div class="col-sm-12">
                <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)" onclick="reloadTable();">
                  <i class="fa cil-search"> </i>Filter
                </a>
              </div>

              <div class="col-sm-12">
                <hr />
              </div>

            </div>
            <br>

            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Transaction ID</th>
                    <th>Purpose</th>
                    <th>Transfer AMT</th>
                    <th>Transfer From</th>
                    <th>Transfer To</th>
                    <th>Transfer Date</th>
                    <th>Recon</th>
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
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  function reloadTable() {
    var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      ajax: {
        url: "{{ route('transferFeeMainList.list') }}",
        data: function(d) {
          d.transfer_date_from = $("#transfer_date_from").val();
          d.transfer_date_to = $("#transfer_date_to").val();
          d.branch_id = $("#branch_id").val();
        },
      },
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'transaction_id',
          name: 'transaction_id'
        },
        {
          data: 'purpose',
          name: 'purpose'
        },
        {
          data: 'transfer_amount',
          name: 'transfer_amount',
          class: 'text-right',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'transfer_from_bank',
          name: 'transfer_from_bank'
        },
        {
          data: 'transfer_to_bank',
          name: 'transfer_to_bank'
        },
        {
          data: 'transfer_date',
          name: 'transfer_date'
        },
        {
          data: 'is_recon',
          name: 'is_recon'
        },
        {
          data: 'action',
          name: 'action'
        },
      ]
    });
  }

  $(function() {
    reloadTable();
  });
</script>
@endsection