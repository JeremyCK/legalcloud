<?php
$total = 0;
$subtotal = 0;
?>
@if(count($quotation))


@foreach($quotation as $index => $cat)
<tr style="background-color:grey;color:white">
  <td colspan="5">{{$cat['category']->category}}</td>
  <?php $subtotal = 0 ?>

</tr>
<?php $category_amount = 0 ?>
@foreach($cat['account_details'] as $index => $details)
<?php $subtotal += $details->amount ?>

<tr>
  <td class="text-center" style="width:50px">
      <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}" id="account_item_id_{{ $details->id }}" >
      <input type="hidden" name="need_approval" value="{{ $details->need_approval }}" id="need_approval_{{ $details->id }}" >
      <input type="hidden" name="min" value="{{ $details->min }}" id="min_{{ $details->id }}" >
      <input type="hidden" name="max" value="{{ $details->max }}" id="max_{{ $details->id }}" >
    <div class="checkbox">
      <input type="checkbox"  name="case_bill" value="{{ $details->id }}" id="chk_{{ $details->id }}" @if($details->amount == 0) disabled @endif>
      <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
    </div>
  </td>
  <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
  <td id="item_{{ $details->id }}">{{ $details->account_name }}</td>
  <td class="text-right" id="amt_{{ $details->id }}">{{ number_format($details->amount, 2, '.', ',') }}</td>
  <!-- <td ><input class="form-control" type="number" value="{{ $details->amount }}" id="quo_amt_{{ $details->id }}" > -->

    


  </td>

</tr>

@endforeach

<?php $total += $subtotal ?>

@if($cat['category']->taxable == "1")
        <tr>
          <td colspan="2">{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
          <td style="text-align:right" colspan="4">RM {{ number_format((float)($subtotal*0.06), 2, '.', ',') }}</td>
        </tr>
        @endif

        <tr>
          <td>Sub Total</td>
          <td style="text-align:right" colspan="4">RM {{ number_format($subtotal, 2, '.', ',') }}</td>
        </tr>
@endforeach



<tr>
  <td>Total </td>
  <td style="text-align:right" colspan="4"> RM {{ number_format((float)$total, 2, '.', ',');  }}</td>

</tr>
@else
<tr>
  <td class="text-center" colspan="5">No data</td>
</tr>
@endif