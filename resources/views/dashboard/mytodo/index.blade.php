@extends('dashboard.base')
@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-xl-4 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="cil-clock"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{$pendingCount}}</span>
                <span class="info-box-text">Pending</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-xl-4 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-green"><i class="cil-check"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{$approveCount}}</span>
                <span class="info-box-text">Approved</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix visible-sm-block"></div>

          <div class="col-xl-4 col-md-6 col-12">
            <div class="info-box">
              <span class="info-box-icon bg-danger"><i class="cil-x"></i></span>

              <div class="info-box-content">
                <span class="info-box-number">{{$rejectedCount}}</span>
                <span class="info-box-text">Rejected</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- /.col -->
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-sm-12">



        <!-- <div class="card">
          <div class="card-header">
            <h4>My Todo list</h4>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div class="row ">
              <div class="col-12">
                <button onclick="viewMode()" class="btn btn-danger" type="button"><span><i class="cil-action-redo"></i> Reset filter</span> </button>
                <button type="button" onclick="bulkUpdate()" class="btn btn-success pull-right"><i class="cil-color-border"></i> Bulk update
                </button>
              </div>
            </div>
            <br>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Bill</th>
                  <th>Case No</th>
                  <th>File</th>
                  <th>Transaction ID</th>
                </tr>
              </thead>
              <tbody id="tbl-bill">
                @if(count($mytodo))
                @foreach($mytodo as $index => $todo)
                <tr style="font-size:8px!important;">
                  <td class="text-center">
                    <div class="checkbox">
                      <input type="checkbox" name="bill" value="{{ $todo->id }}" id="chk_{{ $todo->id }}">
                      <label for="chk_{{ $todo->id }}">{{ $index + 1 }}</label>
                    </div>

                  </td>
                  <td>

                    <div class="small text-success">{{ $todo->account_name }} <a data-toggle="tooltip" title="Filter by {{ $todo->account_name }}" onclick="filterDialog('{{ $todo->account_name }}','{{ $todo->account_details_id }}')" href="javascript:void(0)"><i class="cil-filter"></i></a></div>
                    <div class="small text-info">RM {{ $todo->amount }}
                      @if($todo->status == 1)
                      <span class="label label-success">Approved</span>
                      @elseif($todo->status == 0)
                      <span class="label label-warning">Pending</span>
                      @elseif($todo->status == 2)
                      <span class="label bg-danger">Rejected</span>
                      @endif
                    </div>
                    <div class="small text-muted"><b>Payment Type:</b> {{ $todo->payment_type_name }}</div>
                    @if ($todo->payment_type == 2)
                    <div class="small text-muted"><b>Cheque No:</b> {{ $todo->cheque_no }}</div>
                    @elseif ($todo->payment_type == 3)
                    <div class="small text-muted"><b>Bank Account:</b> {{ $todo->bank_account }}</div>
                    @elseif ($todo->payment_type == 4)
                    <div class="small text-muted"><b>Card No:</b> {{ $todo->credit_card_no }}</div>
                    @endif

                    @if ($todo->bank_id != null)
                    <div class="small text-muted"><b>Bank:</b> {{ $todo->bank_name }}</div>
                    @endif

                  </td>
                  <td>

                    <div class="small text-warning">
                      <a href="/case/{{ $todo->case_id }}" target="_blank">{{ $todo->case_ref_no }}</a>
                      <a data-toggle="tooltip" title="Filter by case" href="javascript:void(0)"><i class="cil-filter"></i></a>
                    </div>
                    <div class="small text-muted"><b>Requestor:</b> {{ $todo->user }}</div>
                    <div class="small text-muted"><b>Request time:</b> {{ $todo->created_at }}</div>
                  </td>
                  <td class="small">
                    <input type="file" id="InputFile{{ $todo->id }}">
                  </td>
                  <td class="text-center">
                    <div class="small form-group row">
                      <label class="col-md-3 col-form-label" for="trx_{{ $todo->id }}">TRX ID</label>
                      <div class="col-md-9">
                        <input class="form-control" id="trx_{{ $todo->id }}" type="text" name="hf-email" value="{{ $todo->transaction_id }}">
                      </div>

                    </div>

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
        </div> -->
      </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('javascript')

<script>
  function filterDialog(account_item, account_id) {

    Swal.fire({
      // title: 'Are you sure?',
      text: 'Filter by "' + account_item + '"?',
      // icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes'
    }).then((result) => {
      if (result.isConfirmed) {
        filterByAccountItem(account_id);
      }
    })
  }

  function filterByAccountItem(account_id) {

    $.ajax({
      type: 'GET',
      url: '/getBillTemplate/' + account_id,
      data: null,
      processData: false,
      contentType: false,
      success: function(data) {
        console.log(data);
        if (data.status == 1) {
          Swal.fire(
            'Success!',
            data.message,
            'success'
          )

          location.reload();
        }

      }
    });
  }

  function bulkUpdate() {
    var form_data = new FormData();
    var bill_list = [];
    var error_count = 0;
    var bill = {};


    $.each($("input[name='bill']:checked"), function() {

      itemID = $(this).val();

      if ($("#trx_" + itemID).val() == "") {
        $("#trx_" + itemID).addClass("error-input-box");
        error_count += 1;
      }

      if (error_count <= 0) {

        bill = {
          id: itemID,
          transaction_id: $("#trx_" + itemID).val()
        };

        bill_list.push(bill);
      }
    });

    if (error_count <= 0) {


      form_data.append("bill_list", JSON.stringify(bill_list));

      $.ajax({
        type: 'POST',
        url: '/update_bill_transaction',
        data: form_data,
        processData: false,
        contentType: false,
        success: function(data) {
          console.log(data);
          if (data.status == 1) {
            Swal.fire(
              'Success!',
              data.message,
              'success'
            )

            location.reload();
          }

        }
      });
    }

  }
</script>

@endsection