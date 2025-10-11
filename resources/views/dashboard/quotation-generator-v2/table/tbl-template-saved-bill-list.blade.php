<?php
$total = 0;
$subtotal = 0;
$total_sst = 0;

$sst_percentage = $sst_rate * 0.01;
?>

@if (count($quotation))

    <tr style="background-color:grey;color:white">
        <th width="10%">No</th>
        <th width="40%">Item</th>
        {{-- <th>Min Value</th> --}}
        <th width="10%" >Min</th>
        <th width="10%" >Max</th>
        <th width="10%">Quotation Amount (Without SST)</th>
        <th width="10%">SST {{ $sst_rate }}%</th>
        <th width="10%">Quotation Amount + SST</th>
    </tr>
    @foreach ($quotation as $index => $cat)
        <tr style="background-color:grey;color:white">
            <th colspan="7">{{ $cat['category']->category }}

                {{-- @if(isset($allow_add_item))
                    @if($allow_add_item == true)
                        <button  class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false"
                        onclick="addAccountItemModal('{{ $cat['category']->id }}')" data-toggle="modal"
                        data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button>
                    @endif
                @endif --}}
            
               
            </th>
            <?php $subtotal = 0; ?>

        </tr>
        <?php $category_amount = 0; ?>
        <?php $agreement_fee = 0; ?>
        @foreach ($cat['account_details'] as $index => $details)
            <?php
            
            $formula_amt = 0;
            $blnContinue = true;
            $scanle_fee_tier = [500000, 500000, 2000000, 2000000, 2000000];
            $scanle_fee_tier_percentage = [0.01, 0.008, 0.007, 0.006, 0.005];
            
            $scale_fee_tier_2023 = [500000, 7000000, 10000000000];
            $scale_fee_tier_2023_percentage = [0.0125, 0.01, 0.01];
            $scale_fee_min_2023 = 500;
            
            $scanle_fee_tier_2023 = [50000, 250000, 5000000, 10000000, 10000000000];
            $scanle_fee_tier_percentage_2023 = [0.01, 0.75, 0.7, 0.65, 0.5];
            
            $agreement_fee_tier = [500000, 500000, 2000000, 2000000, 2000000];
            $agreement_fee_tier_percentage = [0.01, 0.008, 0.007, 0.006, 0.005];
            
            $memo_of_transfer_tier = [50000, 200000, 500000, 1000000];
            $memo_of_transfer_tier_amt = [50, 200, 400, 500];
            
            $stamp_duty_tier = [100000, 400000];
            $stamp_duty_tier_percentage = [0.01, 0.02];
            
            // $memorandum_of_transfer_tier = array(100000, 500000, 1000000, 1000000000);
            $memorandum_of_transfer_tier = [100000, 400000, 500000, 1000000000];
            $memorandum_of_transfer_tier_percentage = [0.01, 0.02, 0.03, 0.04];
            
            $output = '';
            
            if ($details->account_formula != null) {
                // if ($details->account_formula == '${loan_agreement}' || $details->account_formula == '${property_agreement}' || $details->account_formula == '${property_stamp_duty}' || $details->account_formula == '${memorandum_of_transfer}') {
                if ($details->account_formula == '${property_stamp_duty}' || $details->account_formula == '${memorandum_of_transfer}') {
                    if ($details->account_formula == '${loan_agreement}' || $details->account_formula == '${property_agreement}') {
                        $tier = $agreement_fee_tier;
                        $tier_percentage = $agreement_fee_tier_percentage;
            
                        if ($details->account_formula == '${loan_agreement}') {
                            $overall_value = $temp_loan_sum = $loan_sum;
                        } elseif ($details->account_formula == '${property_agreement}') {
                            $overall_value = $temp_loan_sum = $purchase_price;
                        }
                    } elseif ($details->account_formula == '${property_stamp_duty}') {
                        $tier = $stamp_duty_tier;
                        $tier_percentage = $stamp_duty_tier_percentage;
                        $overall_value = $temp_loan_sum = $purchase_price;
                    } elseif ($details->account_formula == '${memorandum_of_transfer}') {
                        $tier = $memorandum_of_transfer_tier;
                        $tier_percentage = $memorandum_of_transfer_tier_percentage;
                        $overall_value = $temp_loan_sum = $purchase_price;
                    }
            
                    if ($overall_value > 0) {
                        $temp_loan_sum = $overall_value;
                        for ($i = 0; $i < count($tier); $i++) {
                            if ($blnContinue == true) {
                                if ($temp_loan_sum - $tier[$i] > 0) {
                                    $formula_amt += $tier[$i] * $tier_percentage[$i];
                                    $temp_loan_sum = $temp_loan_sum - $tier[$i];
                                } else {
                                    $formula_amt += $temp_loan_sum * $tier_percentage[$i];
                                    $blnContinue = false;
                                }
                            }
                        }
                    }
            
                    $category_amount = $formula_amt;
                } elseif (in_array($details->account_formula, ['${loan_agreement}', '${property_agreement}'])) {
                    $overall_value = 0;
                    $value = 0;
            
                    if ($details->account_formula == '${loan_agreement}') {
                        $temp_loan_sum = $loan_sum;
                    } elseif ($details->account_formula == '${property_agreement}') {
                        $temp_loan_sum = $purchase_price;
                    }
            
                    if ($temp_loan_sum > 0) {
                        for ($i = 0; $i < count($scale_fee_tier_2023); $i++) {
                            if ($blnContinue == true) {
                                if ($temp_loan_sum - $scale_fee_tier_2023[$i] >= 0) {
                                    $value += $scale_fee_tier_2023[$i] * $scale_fee_tier_2023_percentage[$i];
                                    $temp_loan_sum = $temp_loan_sum - $scale_fee_tier_2023[$i];
                                } else {
                                    $value += $temp_loan_sum * $scale_fee_tier_2023_percentage[$i];
                                    $blnContinue = false;
                                }
                            }
                        }
                    }
            
                    if ($value < $scale_fee_min_2023) {
                        $value = $scale_fee_min_2023;
                    }
            
                    $formula_amt = $value;
                    $category_amount = $formula_amt;
                } elseif ($details->account_formula == '${agreement_fee_10}') {
                    $formula_amt = $category_amount * 0.1;
            
                    if ($formula_amt < $details->account_min) {
                        $formula_amt = $details->account_min;
                    }
                } elseif ($details->account_formula == '${percentage_1}') {
                    $formula_amt = $category_amount * 0.01;
            
                    if ($formula_amt < $details->account_min) {
                        $formula_amt = $details->account_min;
                    }
                } elseif ($details->account_formula == '${stamp_duty_50}') {
                    $mult = pow(10, abs(-3));
                    $loan_sum = ceil($loan_sum / $mult) * $mult;
            
                    $formula_amt = $loan_sum * 0.005;
                } elseif ($details->account_formula == '${memo_transfer}') {
                    $tier = $memo_of_transfer_tier;
                    for ($i = 0; $i < count($tier); $i++) {
                        if ($overall_value <= $tier[$i]) {
                            $formula_amt = $memo_of_transfer_tier_amt[$i];
                            break;
                        } else {
                            $formula_amt = 1500;
                        }
                    }
                } else {
                }
            } else {
                if ($details->amount != 0) {
                    $formula_amt = $details->amount;
                } else {
                    $formula_amt = $details->account_amt;
                }

                
            }

            $formula_amt = $details->amount;
            
            ?>
            @php
                $max_cap = $details->max;

                if( $details->account_formula == '${property_price_max_3}')
                {
                    $max_cap = $purchase_price * 0.03;
                }
            @endphp
            <tr>
                <td class="text-center" style="width:50px">
                    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}"
                        id="account_item_id_{{ $details->id }}">
                    <input type="hidden" name="item_desc" value="{{ $details->item_desc }}"
                        id="item_desc_{{ $details->id }}">
                    <input type="hidden" name="need_approval" value="{{ $details->need_approval }}"
                        id="need_approval_{{ $details->id }}">
                    <input type="hidden" name="cat_id" value="{{ $cat['category']->id }}"
                        id="cat_{{ $details->id }}">
                        
                    <input type="hidden" name="order_no" value="{{ $details->order_no }}"
                    id="order_no_{{ $details->id }}">
                    <input type="hidden" name="account_name" value="{{ $details->account_name }}"
                        id="account_name_{{ $details->id }}">
                    <input type="hidden" name="account_name_cn" value="@if($QuotationTemplateMain->isChinese == 1) {{ $details->account_name_cn }} @endif"
                        id="account_name_cn_{{ $details->id }}">
                    <input type="hidden" name="formula" value="{{ $details->account_formula }}" id="formula_{{ $details->id }}">
                    <input type="hidden" name="min" value="{{ $details->min }}" id="min_{{ $details->id }}">
                    <input type="hidden" name="max" value="{{ $max_cap }}" id="max_{{ $details->id }}">
                    <div class="checkbox">
                        <input style="display:none" class="cat_item" type="checkbox"
                            onchange="updateAllQuotationAmount()" name="{{ $cat['category']->category }}"
                            value="{{ $details->id }}" id="chk_{{ $details->id }}" checked>
                        <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                    </div>
                </td>
                <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                <td id="item_{{ $details->id }}">{{ $details->account_name }} @if($QuotationTemplateMain->isChinese == 1) {{ $details->account_name_cn }} @endif

                    @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                        @if ($details->pfee_item_desc == 1)
                            <hr />
                            @php
                                $content = '';
                                
                                if ($details->remark != '') {
                                    $content = $details->remark;
                                } else {
                                    $content = $details->item_desc;
                                }
                            @endphp
                            <textarea class="form-control" id="desc_span_{{ $details->id }}" name="desc" rows="5"
                                onchange="updateQuotationSpan('{{ $details->id }}')">{!! $content !!}</textarea>
                        @endif
                    @endif
                </td>
                <td id="span_min_{{ $details->id }}">{{ number_format($details->min, 2, '.', ',') }}</td>
                <td id="span_max_{{ $details->id }}">{{ number_format($max_cap, 2, '.', ',') }}</td>
                <td><input onchange="updateAllQuotationAmount()" class="form-control crete_bill_input"
                        name="cat_{{ $cat['category']->code }}" type="number"
                        value="{{ number_format((float) $formula_amt, 2, '.', '') }}"
                        id="quo_amt_{{ $details->id }}">




                </td>
                <td class="text-right">
                    @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                        <span id="sst_{{ $details->id }}">
                            {{-- {{ number_format((float) ($formula_amt * 0.06), 2, '.', ',') }} --}}
                            {{ number_format((float) ($formula_amt * $sst_percentage), 2, '.', ',') }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                        <span id="amt_sst_{{ $details->id }}">
                            {{-- {{ number_format((float) ($formula_amt * 1.06), 2, '.', ',') }} --}}
                            {{ number_format((float) ($formula_amt * (1 + $sst_percentage)), 2, '.', ',') }}
                        </span>
                    @else
                        <span id="amt_sst_{{ $details->id }}">
                            {{ number_format((float) $formula_amt, 2, '.', ',') }}</span>
                    @endif
                </td>

            </tr>

            @php
                
                if ($cat['category']->id == 1 || $cat['category']->id == 4)
                {
                    // $sst = round($formula_amt * 0.06, 2);
                    $sst = round($formula_amt * $sst_percentage, 2);
                    $subtotal += ($formula_amt + $sst);
                    $total_sst += $sst;
                    // $subtotal += ($formula_amt * 1.06);
                }
                else {
                    $subtotal += $formula_amt;
                }
                
            @endphp
        @endforeach
        @php
            $total += $subtotal;
            
        @endphp
        <tr>
            <th colspan="@if ($cat['category']->id == 1 || $cat['category']->id == 4)5 @else 6 @endif">Sub Total </th>
            @if ($cat['category']->id == 1 || $cat['category']->id == 4)
            <th style="text-align:right" id="sst_total_{{ $cat['category']->id }}" > {{ number_format($total_sst, 2, '.', ',') }}</th>
            @endif
            <th style="text-align:right" id="sub_total_{{ $cat['category']->id }}" colspan="5">
                {{ number_format($subtotal, 2, '.', ',') }}</th>
            <input type="hidden" id="int_sub_total_{{ $cat['category']->id }}" class="sub_total"
                value="{{ number_format($subtotal * 1.06, 2, '.', '') }}" />

        </tr>
       

        {{-- <div>
            {{ $subtotal }}
        </div> --}}
    @endforeach



    <tr style=";background-color:black;color:white">
        <th>Total </th>
        <th style="text-align:right" id="total_sum_bill" colspan="6"> {{ number_format($total, 2, '.', ',') }}
        
            <input type="hidden" value="{{$total}}" id="int_total_sum_bill" /></th>

    </tr>
    <tr class="div_discount" style="display: none">
        <th>Discount </th>
        <th style="text-align:right" id="span_discount_amt" colspan="7">0.00</th>
    </tr>
    <tr class="div_discount" style=";background-color:black;color:white;display: none">
        <th>Final Total </th>
        <th style="text-align:right" id="final_amt" colspan="7">0.00</th>
    </tr>
@else
    <tr>
        <th class="text-center" colspan="5">No data</th>
    </tr>
@endif
