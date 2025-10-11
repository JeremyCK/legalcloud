@extends('dashboard.base')
@section('content')
<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">

        <div class="card div-main">
          <div class="card-header">
            <h4>Update quotation template </h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <form method="POST" action="{{ route('quotation.update', $quotation->id) }}">
              @csrf
              @method('PUT')

              <input class="form-control" type="hidden" id="quotation_id" value="{{ $quotation->id}}" required>

              <div class="row">
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                  <div class="form-group row">
                    <div class="col">
                      <label>Quotation name</label>
                      <input class="form-control" type="text" name="name" value="{{ $quotation->name}}" required>
                    </div>
                  </div>




                  <div class="form-group row">
                    <div class="col">
                      <label>Remark</label>
                      <textarea class="form-control" id="remark" name="remark" rows="3">{{ $quotation->remark }}</textarea>
                    </div>
                  </div>




                </div>


                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                  <div class="form-group row">
                    <div class="col">
                      <label>{{ __('coreuiforms.notes.status') }}</label>
                      <select class="form-control" name="status">
                        <option value="1" @if ($quotation->status == 1) selected @endif>Active</option>
                        <option value="0" @if ($quotation->status == 0) selected @endif>Inactive</option>
                      </select>
                    </div>
                  </div>
                </div>

              </div>

              <button class="btn btn-primary float-right" type="submit">Save</button>
              <a class="btn btn-danger" href="{{ route('quotation.index') }}">Return</a>
            </form>
          </div>
        </div>



      </div>


      <div class="col-sm-12">

        <div class="card div-main">



          <div class="card-body">

            <button class="btn btn-primary float-right" onclick="saveAll('{{ $quotation->id}}')" type="button">Save all</button>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Item</th>
                  <th>Order</th>
                  <th>Min</th>
                  <th>Max</th>
                  <th>Amount (RM)</th>
                  <!-- <th>Approval</th> -->
                  <!-- <th>Action</th> -->
                </tr>
              </thead>
              <tbody id="tbl-case-bill">
                <?php
                $total = 0;
                $subtotal = 0;
                ?>
                @if(count($quotation_details))


                @foreach($quotation_details as $index => $cat)
                <tr style="background-color:grey;color:white">

                  <td class="hide">0</td>
                  <td colspan="7">{{ $cat['category']->category }}
                    <button class="btn btn-info float-right" onclick="addAccountItemMode('{{ $cat['category']->id }}')" type="button"><i class="cil-plus"></i></span>Add </button>
                  </td>
                  <?php $total += $subtotal ?>
                  <?php $subtotal = 0 ?>

                </tr>
                @foreach($cat['account_details'] as $index => $details)
                <?php $subtotal += $details->amount ?>
                <tr>
                  <td class="text-center" style="width:50px">
                    <div class="checkbox">
                      <!-- <input type="checkbox" name="bill" value="{{ $details->id }}" id="chk_{{ $details->id }}" @if($details->amount == 0) disabled @endif> -->
                      <span style="display: none;" id="ic_modified_{{ $details->id }}"><i class="text-warning cil-pencil"></i></span><label for="chk_{{ $details->id }}">{{ $index + 1 }} </label>
                    </div>
                  </td>
                  <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                  <td class="hide" id="modified_{{ $details->id }}">0</td>
                  <td id="item_{{ $details->id }}">{{ $details->account_name }} - {{ $details->account_formula }} </td>
                  <td class="hide"><input class="form-control" type="text" value="0" id="bln_modified_{{ $details->id }}"></td>
                  @php
                  $min_val = 0;

                  if ($details->min == 0)
                  {
                    $min_val = $details->account_min;
                  }
                  @endphp
                  <td class="hide"><input class="form-control" type="number" value="{{ $min_val }}" id="quo_min_{{ $details->id }}_ori"></td>
                  <td class="hide"><input class="form-control" type="number" value="{{ $details->max }}" id="quo_max_{{ $details->id }}_ori"></td>
                  <td class="hide"><input class="form-control" type="number" value="{{ $details->amount }}" id="quo_amt_{{ $details->id }}_ori"></td>
                  <td class="hide"><input class="form-control" type="number" value="{{ $details->order_no }}" id="order_no{{ $details->id }}_ori"></td>
                  <td><input class="form-control " onchange="modifiedCheck('{{ $details->id }}')" type="number" value="{{ $details->order_no }}" id="order_no{{ $details->id }}"></td>
                  <td><input class="form-control " onchange="modifiedCheck('{{ $details->id }}')" type="number" value="{{ $min_val }}" id="quo_min_{{ $details->id }}"></td>
                  <td><input class="form-control " onchange="modifiedCheck('{{ $details->id }}')" type="number" value="{{ $details->max }}" id="quo_max_{{ $details->id }}"></td>
                  <td><input class="form-control " onchange="modifiedCheck('{{ $details->id }}')" type="number" value="{{ $details->amount }}" id="quo_amt_{{ $details->id }}"></td>
                  <!-- <td>
                    <div class="checkbox text-center" >
                      <input type="checkbox" onchange="modifiedCheck('{{ $details->id }}')"name="need_attachment" id="chk_need_approval_{{ $details->id }}" @if($details->need_approval == 1) checked @endif>
                      <label for="chk_need_attachment_{{ $details->id }}"></label>
                    </div>
                  </td> -->
                  <td class="text-center">
                  
                 <a href="javascript:void(0)" onclick="deleteAccountItem('{{ $details->id }}')" class="btn btn-danger shadow sharp " data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>
                  </td>

                </tr>

                @endforeach


                @endforeach


                @else
                <tr>
                  <td class="text-center" colspan="5">No data</td>
                </tr>
                @endif

              </tbody>
            </table>
          </div>


        </div>

        @include('dashboard.quotation.d-add-account-item')
      </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('javascript')
<script>
  function addAccountItemMode(id) {

    $(".account_item_all").hide();
    $(".account_cat_" + id).show();
    hideExistAccount();

    $(".div-main").hide();
    $("#dAddAccountItem").show();
  }

  function viewMode() {
    $(".div-main").show();
    $("#dAddAccountItem").hide();
  }

  function modifiedCheck(id) {
    blnEdited = false;

    convertDecimal("#quo_min_" + id);
    convertDecimal("#quo_max_" + id);
    convertDecimal("#quo_amt_" + id);
    // convertDecimal("#order_no" + id);


    if ($("#quo_min_" + id).val() != $("#quo_min_" + id + "_ori").val()) {
      blnEdited = true;
    }

    if ($("#quo_max_" + id).val() != $("#quo_max_" + id + "_ori").val()) {
      blnEdited = true;
    }

    if ($("#quo_amt_" + id).val() != $("#quo_amt_" + id + "_ori").val()) {
      blnEdited = true;
    }

    if ($("#order_no" + id).val() != $("#order_no" + id + "_ori").val()) {
      blnEdited = true;
    }



    if (blnEdited == true) {

      $("#bln_modified_" + id).val(1);
      $("#modified_" + id).html(1);
      $("#ic_modified_" + id).show();

    } else {
      $("#bln_modified_" + id).val(0);
      $("#ic_modified_" + id).hide();
    }


  }

  function convertDecimal(object) {
    var Value = $(object).val();

    if (Value == "") {
      Value = 0;
    }

    $(object).val(parseFloat(Value).toFixed(2));
  }

  function saveAll(quotation_id) {
    var bill_list = [];
    var bill = {};
    var intCountModified = 0;
    var amount = 0;
    var min = 0;
    var max = 0;

    $("#tbl-case-bill tr").each(function() {
      var self = $(this);

      var item_id = self.find("td:eq(1)").text().trim();
      
      if (item_id != 0) {
        if ($("#modified_" + item_id).html() == "1") {
          intCountModified += 1;

          console.log("#quo_amt_" + item_id);
          order_no = $("#order_no" + item_id).val();
          amount = $("#quo_amt_" + item_id).val();
          min = $("#quo_min_" + item_id).val();
          max = $("#quo_max_" + item_id).val();

          bill = {
            id: item_id,
            order_no: order_no,
            amount: amount,
            min: min,
            max: max
          };

          bill_list.push(bill);
        }
      }
    })


    var form_data = new FormData();

    form_data.append("bill_list", JSON.stringify(bill_list));
    form_data.append('_token', '{{ csrf_token() }}');

    $.ajax({
      type: 'POST',
      url: '/update_quotation_bill/' + quotation_id,
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

  function hideExistAccount()
  {
    $("#tbl-case-bill tr").each(function() {
      var self = $(this);
      var item_id = self.find("td:eq(1)").text().trim();

      console.log("#account_"+item_id);

      $(".account_"+item_id).hide();
    })
  }

  function addNewAccount() {
    if($("#selected_account_id").val() == "0" || $("#selected_account_id").val() == "")
    {
      return;
    }

    var form_data = new FormData();

    form_data.append("selected_account_id", $("#selected_account_id").val());
    form_data.append('_token', '{{ csrf_token() }}');

    $.ajax({
      type: 'POST',
      url: '/add_account_item_to_quotation/' + $("#quotation_id").val(),
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

  function deleteAccountItem($id)
  {
    Swal.fire({
      title: 'Delete this account?',
      showCancelButton: true,
      confirmButtonText: `Yes`,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          url: '/delete_account_item_from_quotation/' + $id,
          success: function(data) {
            console.log(data);
            if (data.status == 1) {

              Swal.fire('Success!', data.message, 'success');
              // iniFileTable();
              // table.ajax.reload();
              location.reload();
            } else {
              Swal.fire('notice!', data.message, 'warning');
            }

          }
        });
      }
    })
  }
</script>
@endsection