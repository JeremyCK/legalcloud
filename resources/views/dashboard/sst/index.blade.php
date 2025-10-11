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
                <h4>SST Payment Record</h4>
              </div>

              <div class="col-6">
                <a class="btn btn-lg btn-primary  float-right" href="/sst-create">
                  <i class="cil-plus"> </i>Create New records
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
                    <label>Payment date from</label>
                    <input class="form-control" type="date" id="transfer_date_from" name="transfer_date_from">
                  </div>
                </div>
              </div>


              <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                <div class="form-group row">
                  <div class="col">
                    <label>Payment date to</label>
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
                    <th>Payment Date</th>
                    <th>Transaction ID</th>
                    <th>Remark</th>
                    <th>Total SST Paid</th>
                    <th>Branch</th>
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
        url: "{{ route('sstMainList.list') }}",
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
          data: 'payment_date',
          name: 'payment_date'
        },
        {
          data: 'transaction_id',
          name: 'transaction_id'
        },
        {
          data: 'remark',
          name: 'remark'
        },
        {
          data: 'amount',
          name: 'amount',
          class: 'text-right',
          render: $.fn.dataTable.render.number(',', '.', 2)
        },
        {
          data: 'branch_name',
          name: 'branch_name'
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