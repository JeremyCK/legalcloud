@extends('dashboard.base')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div id="dList" class="card">
          <div class="card-header">
            <h4>Ledger</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Transaction ID</th>
                  <th>Item</th>
                  <th>Credit (RM)</th>
                  <th>Debit (RM)</th>
                  <th>Cheque No</th>
                  <th>Bank</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($transactions))
                  @foreach($transactions as $index => $transaction)
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->name }} </td>
                    <td>
                      @if($transaction->transaction_type == 'C')
                      {{ $transaction->amount }}
                      @else
                      -
                      @endif
                    </td>
                    <td>
                    @if($transaction->transaction_type == 'D')
                      {{ $transaction->amount }}
                      @else
                      -
                      @endif
                    </td>
                    <td>{{ $transaction->cheque_no }} </td>
                    <td>{{ $transaction->bank_name }} </td>
                    <td class="text-center">
                      {{ $transaction->created_at }}
                    </td>
                    <td class="text-center">
                      @if($transaction->status == 0)
                        <a href="javascript:void(0);" onclick="viewVoucher('{{ $transaction->id }}')" class="btn btn-primary shadow btn-xs sharp mr-1">
                          <i class="cil-pencil"></i>
                        </a>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                @else
                  <tr>
                    <td class="text-center" colspan="7">No data</td>
                  </tr>
                @endif
              </tbody>
            </table>
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
</script>

@endsection