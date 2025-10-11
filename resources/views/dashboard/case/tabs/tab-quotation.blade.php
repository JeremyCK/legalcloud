<div class="row">
  <div class="col-12">
    <div class="box">
      <div class="box-header">
        <div class="form-group row">

          <div class="col-12">
            <label>
              Define bill template
            </label>
          </div>

          <div class="col-6">

            <select id="ddl_account_template" class="form-control" name="ddl_account_template">
              <option value="0">-- Select account template --</option>
              @foreach($account_template as $index => $account)
              <option value="{{ $account->id }}">{{ $account->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-6 float-right">
            <button class="btn btn-primary" type="button" onclick="getAccountTemplate('{{ $case->id }}');">
              <i class="cil-caret-right"></i> Load Template
            </button>
          </div>
        </div>

        <!-- <h3 class="box-title">Quotation list</h3> -->

        <!-- <div class="box-tools">
          <button class="btn btn-primary" type="button" onclick="getAccountTemplate('{{ $case->id }}');">
            <i class="cil-plus"></i> Submit
          </button>
        </div> -->

      </div>
    </div>
  </div>
</div>

<div class="col-12 mb-1">
  <h3 id="h3_template_name" class="box-title">-</h3>
</div>

<table class="table table-striped table-bordered datatable">
  <thead>
    <tr class="text-center">
      <th>No</th>
      <th>Item</th>
      <th>Amount (RM)</th>
      <!-- <th>Action</th> -->
    </tr>
  </thead>
  <tbody>
    <?php
    $total = 0;
    $subtotal = 0;
    ?>
    @if(count($account_template_with_cat))


    @foreach($account_template_with_cat as $index => $cat)
    <tr style="background-color:grey;color:white">
      <td colspan="5">{{ $cat['category']->category }}</td>
      <?php $total += $subtotal ?>
      <?php $subtotal = 0 ?>

    </tr>
    @foreach($cat['account_details'] as $index => $details)
    <?php $subtotal += $details->amount ?>
    <tr>
      <td class="text-center" style="width:50px">
        <div class="checkbox">
          <input type="checkbox" name="bill" value="{{ $details->id }}" id="chk_{{ $details->id }}">
          <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
        </div>
      </td>
      <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
      <td id="item_{{ $details->id }}">{{ $details->item_name }}</td>
      <td id="amt_{{ $details->id }}">{{ $details->amount }}</td>

      <!-- <td class="text-center">
        <a href="javascript:void(0)" onclick="voucherMode('{{ $details->id }}', '{{ $case->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Voucher</a>
      </td> -->
    </tr>

    @endforeach

    @if($cat['category']->taxable == "1")
    <tr>
      <td colspan="2">{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
      <td style="text-align:right" colspan="4">{{ number_format((float)($subtotal*0.06), 2, '.', '') }}</td>
    </tr>
    @endif

    <tr>
      <td></td>
      <td style="text-align:right" colspan="4">{{$subtotal}}</td>
    </tr>


    @endforeach

    <tr style="background-color:grey;color:white">
      <td colspan="5">Others</td>
      <?php $total += $subtotal ?>
      <?php $subtotal = 0 ?>

    </tr>
    <tr>
      <td class="text-center" style="width:50px">
        <div class="checkbox">
          <input type="checkbox" name="bill" value="299" id="chk_299" disabled>
          <label for="chk_299">1</label>
        </div>
      </td>
      <td class="hide" id="item_id_299">29</td>
      <td id="item_299">sales commission (6%)</td>
      <td id="amt_299"> {{ number_format((float)($case->targeted_bill * 0.06), 2, '.', '') }}</td>

      <!-- <td class="text-center">
        <a href="javascript:void(0)" onclick="voucherMode('28', '1');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Voucher</a>
      </td> -->
    </tr>

    <tr>
      <td>Total </td>
      <td style="text-align:right" colspan="4">{{$total}}</td>

    </tr>
    @else
    <tr>
      <td class="text-center" colspan="5">No data</td>
    </tr>
    @endif

  </tbody>
</table>