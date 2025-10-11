<?php
$total = 0;
$totalOri = 0;
$totalEdit = 0;
$subtotal = 0;
?>
@if(count($quotation))


@foreach($quotation as $index => $cat)
<tr style="background-color:grey;color:white">
  @if($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management')
  <td class="quotation-colspan" colspan="5">{{$cat['category']->category}}<button class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false" onclick="addAccountItemModal('{{$cat['category']->id}}')" data-toggle="modal" data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button></td>
  @elseif($current_user->menuroles == 'sales')
  <td class="quotation-colspan" colspan="4">{{$cat['category']->category}}<button class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false" onclick="addAccountItemModal('{{$cat['category']->id}}')" data-toggle="modal" data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button></td>
  @else
  <td class="quotation-colspan" colspan="3">{{$cat['category']->category}}</td>
  @endif

  <?php
  $subtotal = 0;
  $subtotalnosset = 0;
  $subtotalOri = 0;
  $subtotalEdit = 0;

  ?>

</tr>
<?php $category_amount = 0 ?>
@foreach($cat['account_details'] as $index => $details)
<?php
$subtotal += $details->amount;
$subtotalnosset += $details->quo_amount_no_sst;

$subtotalOri += $details->quo_amount;
$subtotalEdit += $details->invoice_amount;
?>

<tr>
  <td class="text-center" style="width:50px">
    <input class="form-control" type="hidden" value="{{ $details->quo_amount }}" id="quo_amount_{{ $details->id }}">
    <input class="form-control" type="hidden" value="0" id="bln_modified_{{ $details->id }}">
    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}" id="account_item_id_{{ $details->id }}">
    <input type="hidden" name="need_approval" value="{{ $details->need_approval }}" id="need_approval_{{ $details->id }}">
    <input type="hidden" name="min" value="{{ $details->min }}" id="min_{{ $details->id }}">
    <input type="hidden" name="max" value="{{ $details->max }}" id="max_{{ $details->id }}">
    <div class="checkbox">
      <input type="checkbox" name="case_bill" value="{{ $details->id }}" id="chk_{{ $details->id }}" @if($details->amount == 0) disabled @endif>


      <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
    </div>
  </td>
  <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
  <td id="item_{{ $details->id }}">{{ $details->account_name }}</td>
  <td class="text-right" id="amt_{{ $details->id }}">
    {{ number_format($details->amount, 2, '.', ',') }}
    {{$LoanCaseBillMain->id}}
    @if($LoanCaseBillMain->bln_invoice== 0)
    @if($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'sales')
    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" onclick="editQuotationModal('{{ $details->quo_amount }}','{{ $details->id }}','{{$cat['category']->id}}',1,'{{ $details->account_name }}')" data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>

    @endif
    @else
    @if($current_user->menuroles == 'admin')
    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" onclick="editQuotationModal('{{ $details->quo_amount }}','{{ $details->id }}','{{$cat['category']->id}}',1,'{{ $details->account_name }}')" data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>

    @endif
    @endif

    @if($LoanCaseBillMain->id == 175)
    @if($current_user->id == '26')
    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" onclick="editQuotationModal('{{ $details->quo_amount }}','{{ $details->id }}','{{$cat['category']->id}}',1,'{{ $details->account_name }}')" data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>

    @endif
    @endif

  </td>
  @if($LoanCaseBillMain->bln_invoice== 0)
  @if($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'sales')
  <td class="text-right">{{ $details->quo_amount }}
    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" onclick="('{{ $details->quo_amount }}','{{ $details->id }}','{{$cat['category']->id}}',1,'{{ $details->account_name }}')" data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>
  </td>
  <!-- @if($current_user->menuroles <> 'sales')
    <td class="text-right">{{ $details->invoice_amount }}
      <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" onclick="editQuotationModal('{{ $details->invoice_amount }}','{{ $details->id }}','{{$cat['category']->id}}',2)"  data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>
    </td>
    @endif -->


  @endif
  <!-- @if($current_user->menuroles == 'sales')
  <td class="text-right">{{ $details->quo_amount }} </td>
  <td class="text-right"><input class="text-right totalprice" onchange="modifiedCheck('{{ $details->id }}')" type="number" name="sub_total_edit_{{$cat['category']->code}}" value="{{ $details->quo_amount }}" id="edit_quo_amount_{{ $details->id }}">
    <span style="display: none;" id="ic_modified_{{ $details->id }}"><i class="text-warning cil-pencil"></i></span>
  </td>
  @endif -->
  @endif
  <!-- <td ><input class="form-control" type="number" value="{{ $details->amount }}" id="quo_amt_{{ $details->id }}" > -->




  </td>

</tr>

@endforeach

<?php
$total += $subtotal;
$totalOri += $subtotalOri;
$totalEdit += $subtotalEdit;

?>

@if($cat['category']->taxable == "1")
<tr>
  <td colspan="2">{{$cat['category']->percentage}}% GOVERNMENT TAX </td>

  <td style="text-align:right" class="quotation-sst-colspan" colspan="4">RM {{ number_format((float)($subtotalnosset*0.06), 2, '.', ',') }}</td>
</tr>
@endif

<tr>
  <td>Sub Total</td>
  <td style="text-align:right" colspan="2">RM {{ number_format($subtotal, 2, '.', ',') }}</td>

  @if($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management'|| $current_user->menuroles == 'sales')
  <td class="quotation" style="text-align:right" id="sub_total_ori_{{$cat['category']->code}}">RM {{ number_format($subtotalOri, 2, '.', ',') }}</td>

  @if($current_user->menuroles <> 'sales')
    <td class="quotation" style="text-align:right" id="sub_total_edit_{{$cat['category']->code}}">RM{{ number_format($subtotalEdit, 2, '.', ',') }}</td>
    @endif
    @endif


</tr>
@endforeach



<tr>
  <td>Total </td>
  <td style="text-align:right" class="quotation-total-colspan" colspan="2"> RM {{ number_format((float)$total, 2, '.', ',');  }}</td>
  @if($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management'|| $current_user->menuroles == 'sales')
  <td style="text-align:right" class="quotation"> RM {{ number_format((float)$totalOri, 2, '.', ',');  }}</td>
  @if($current_user->menuroles <> 'sales')
    <td style="text-align:right" class="quotation" id="total_edit"> RM {{ number_format((float)$totalEdit, 2, '.', ',');  }}</td>
    @endif
    @endif


</tr>
@else
<tr>
  <td class="text-center" colspan="5">No data</td>
</tr>
@endif