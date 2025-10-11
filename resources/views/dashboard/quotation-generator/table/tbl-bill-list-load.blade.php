<?php
$total = 0;
$subtotal = 0;
?>
@if(count($quotation))


@foreach($quotation as $index => $cat)
<tr style="background-color:grey;color:white">
  <td colspan="5">{{$cat['category']->category}}</td>
  <?php $total += $subtotal ?>
  <?php $subtotal = 0 ?>

</tr>
<?php $category_amount = 0 ?>
<?php $agreement_fee = 0 ?>
@foreach($cat['account_details'] as $index => $details)
<?php $subtotal += $details->amount ?>




<tr>
  <td class="text-center" style="width:50px">
    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}" id="account_item_id_{{ $details->id }}">
    <input type="hidden" name="need_approval" value="{{ $details->need_approval }}" id="need_approval_{{ $details->id }}">
    <input type="hidden" name="cat_id" value="{{$cat['category']->id}}" id="cat_{{ $details->id }}">
    <input type="hidden" name="item_desc" value="{{ $details->item_desc }}"
        id="item_desc_{{ $details->id }}">
    <input type="hidden" name="account_name" value="{{$details->account_name}}" id="account_name_{{ $details->id }}">
    <input type="hidden" name="min" value="{{ $details->min }}" id="min_{{ $details->id }}">
    <input type="hidden" name="max" value="{{ $details->max }}" id="max_{{ $details->id }}">
    <div class="checkbox">
      <input style="display:none" class="cat_item" type="checkbox" name="{{$cat['category']->category}}" value="{{ $details->id }}" id="chk_{{ $details->id }}" checked>
      <label for="chk_{{ $details->id }}">{{ $index+1 }}</label>
    </div>
  </td>
  <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
  <td id="item_{{ $details->id }}">{{ $details->account_name }}
    @if ($cat['category']->id == 1)
    @if($details->item_desc)
    <hr/>
    {{ $details->item_desc }}
    @endif

@endif
</td>
  <td><input class="form-control" name="cat_{{$cat['category']->code}}" type="number" value="{{ number_format((float)$details->amount, 2, '.', '');  }}" id="quo_amt_{{ $details->id }}">




  </td>

</tr>

@endforeach


<div>
  {{$subtotal}}
</div>

@endforeach



<tr>
  <td>Total </td>
  <td style="text-align:right" colspan="4">{{$subtotal}}</td>

</tr>
@else
<tr>
  <td class="text-center" colspan="5">No data</td>
</tr>
@endif