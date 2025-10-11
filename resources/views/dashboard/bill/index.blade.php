@extends('dashboard.base')

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div id="dList" class="card">
          <div class="card-header">
            <h4>Bill</h4>
          </div>
          <div class="card-body">


            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>case Ref No</th>
                    <th>Bill</th>
                    <th>Client</th>
                    <th>Total Amt</th>
                    <th>Collected Amt</th>
                    <th>Used Amt</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

            </div>

            <!-- <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>item</th>
                  <th>Case No</th>
                  <th>Amount</th>
                  <th>Request by</th>
                  <th>Approve by</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($bills))
                @foreach($bills as $index => $bill)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $bill->name }}</td>
                  <td>{{ $bill->case_ref_no }}</td>
                  <td class="text-center">
                    @if($bill->status == 1)
                    <span class="badge-pill badge-success">Approved</span>
                    @elseif($bill->status == 0)
                    <span class="badge-pill badge-warning">Pending</span>
                    @elseif($bill->status == 2)
                    <span class="badge-pill badge-danger">Rejected</span>
                    @endif
                  </td>

                  <td class="text-center">
                  <a href="{{ route('case.show', $bill->id ) }}" class="btn btn-primary"><i class="cil-pencil"></i></a>
                  </td>
                </tr>
                @endforeach
                @else
                <tr>
                  <td class="text-center" colspan="7">No data</td>
                </tr>
                @endif

              </tbody>
            </table> -->

          </div>
        </div>

        <div id="dAction" class="card" style="display:none">
          <div class="card-header">
            <h4>Voucher</h4>
          </div>
          <div class="card-body">
            <form id="form_voucher" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                  <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
                  <div class="form-group row">
                    <div class="col">
                      <label>Item</label>
                      <input class="form-control" type="hidden" value="" id="voucher_id" name="voucher_id">
                      <input class="form-control" type="hidden" value="" id="status" name="status">
                      <input class="form-control" type="text" value="" id="item" name="item" disabled>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Available Amount</label>
                      <input class="form-control" type="text" value="" id="amt" name="amt" disabled>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Remarks</label>
                      <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                    </div>
                  </div>

                  <div class="row" style="margin-bottom: 20px;">
                    <div class="col-sm-12">
                      <div class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                      </div>
                      <a id="btnBackToEditMode" class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="modeController('list');">
                        <i class="ion-reply"> </i> Back
                      </a>
                      <a id="btnPrint" class="btn btn-sm btn-success float-right mr-1 d-print-none" href="javascript:void(0)" onclick="updateVoucher(1)">
                        <i class="cil-check-alt"></i> Approve</a>

                      <a id="btnPrint" class="btn btn-sm btn-danger float-right mr-1 d-print-none" href="javascript:void(0)" onclick="updateVoucher(2)">
                        <i class="cil-x"></i> Reject</a>
                    </div>
                  </div>


                </div>
              </div>
            </form>

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
  function editMode() {
    $("#dList").hide();
    $("#dEdit").show();

  }

  function viewVoucher(id) {
    $("#voucher_id").val(id);
    modeController('action');

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      type: 'POST',
      url: '/view_voucher/' + id,
      success: function(result) {
        console.log(result);
        if (result.status == 1) {
          $("#remarks").val(result.data.remark);
          $("#amt").val(result.data.amount);
          $("#item").val(result.data.item_name);
        }
      }
    });
  }

  function updateVoucher(status) {
    $("#status").val(status);

    $.ajax({
      type: 'POST',
      url: '/update_voucher',
      data: $('#form_voucher').serialize(),
      success: function(result) {
        console.log(result);
        if (result.status == 1) {
          Swal.fire('Success!', result.data, 'success');
          location.reload();
        }
      }
    });
  }

  function modeController(mode) {
    if (mode == 'action') {
      $("#dList").hide();
      $("#dAction").show();

    } else if (mode == 'list') {
      $("#dList").show();
      $("#dAction").hide();
    }

  }

  var table;
</script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $(function() {



    table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('bill.list') }}",
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'case_ref_no',
          name: 'case_ref_no'
        },
        {
          data: 'name',
          name: 'name'
        },
        {
          data: 'client_name',
          name: 'client_name'
        },
        {
          data: 'total_amt',
          name: 'total_amt'
        },
        {
          data: 'collected_amt',
          name: 'collected_amt'
        },
        {
          data: 'used_amt',
          name: 'used_amt'
        },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'action',
          className: "text-center",
          name: 'action',
          orderable: true,
          searchable: true
        },
      ]
    });


  });
</script>
@endsection