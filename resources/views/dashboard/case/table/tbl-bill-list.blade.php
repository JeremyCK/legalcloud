<?php
$total = 0;
$subtotal = 0;
$totalSST = 0;

$sst_percentage = $sst_rate * 0.01;
?>

@if (count($quotation))
    <tr style="background-color:grey;color:white">
        <th width="10%">No</th>
        <th width="50%">Item</th>
        <th>Min </th>
        <th>Max </th>
        <th>Quotation Amount (Without SST)</th>
        <th>SST (<span id="span_sst_rate">{{ $sst_rate }}%</span>)</th>
        <th>Quotation Amount + SST</th>
        <input type="hidden" name="hidden_quotation_template_id" value="{{ $QuotationTemplateMain->id }}"
            id="hidden_quotation_template_id">
    </tr>

    @foreach ($quotation as $index => $cat)
   
        @if(count($cat['account_details']) > 0)
          <tr style="background-color:grey;color:white">
            <th colspan="7">{{ $cat['category']->category }} </th>
            <?php $subtotal = 0;
                $totalSST = 0;
            ?>
        </tr>
        @endif
      
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
            
            $agreement_fee_tier = [500000, 500000, 2000000, 2000000, 2000000];
            $agreement_fee_tier_percentage = [0.01, 0.008, 0.007, 0.006, 0.005];
            
            $memo_of_transfer_tier = [50000, 200000, 500000, 1000000,10000000000];
            $memo_of_transfer_tier_amt = [50, 200, 400, 500,1500];
            
            $stamp_duty_tier = [100000, 400000];
            $stamp_duty_tier_percentage = [0.01, 0.02];
            
            // $memorandum_of_transfer_tier = array(100000, 500000,1000000, 1000000000);
            $memorandum_of_transfer_tier = [100000, 400000, 500000, 100000000000];
            $memorandum_of_transfer_tier_percentage = [0.01, 0.02, 0.03, 0.04];
            
            $output = '';
            
            if ($details->account_formula != null) {
                // if ($details->account_formula == '${scales_fee}' || $details->account_formula == '${stamp_duty}') {
            
                //   if ($details->account_formula == '${scales_fee}') {
                //     $tier = $scanle_fee_tier;
                //     $tier_percentage = $scanle_fee_tier_percentage;
                //   } else if ($details->account_formula == '${stamp_duty}') {
                //     $tier = $stamp_duty_tier;
                //     $tier_percentage = $stamp_duty_tier_percentage;
                //   }
            
                //   if ($loanCase->loan_sum > 0) {
                //     $temp_loan_sum = $loanCase->loan_sum;
                //     for ($i = 0; $i < count($tier); $i++) {
                //       if ($blnContinue == true) {
                //         if ($temp_loan_sum - $tier[$i] > 0) {
                //           $formula_amt += $tier[$i] * $tier_percentage[$i];
                //           $temp_loan_sum = $temp_loan_sum - $tier[$i];
                //         } else {
                //           $formula_amt += $temp_loan_sum * $tier_percentage[$i];
                //           $blnContinue = false;
                //         }
                //       }
                //     }
                //   }
                //   $category_amount = $formula_amt;
                //   $agreement_fee = $formula_amt;
                // } else if ($details->account_formula == '${scales_fee}*10%') {
            
                //   $formula_amt = $category_amount * 0.1;
            
                //   if ($formula_amt < $details->account_min) {
                //     $formula_amt = $details->account_min;
                //   }
                // } else if ($details->account_formula == '${stamp_duty_50}') {
            
                //   $formula_amt = $loanCase->loan_sum * 0.005;
                // } else {
                // }
            
                if ($details->account_formula == '${property_stamp_duty}' || $details->account_formula == '${memorandum_of_transfer}') {
                    if ($details->account_formula == '${loan_agreement}' || $details->account_formula == '${property_agreement}') {
                        $tier = $agreement_fee_tier;
                        $tier_percentage = $agreement_fee_tier_percentage;
            
                        if ($details->account_formula == '${loan_agreement}') {
                            $overall_value = $temp_loan_sum = $loanCase->loan_sum;
                        } elseif ($details->account_formula == '${property_agreement}') {
                            $overall_value = $temp_loan_sum = $loanCase->purchase_price;
                        }
                    } elseif ($details->account_formula == '${property_stamp_duty}') {
                        $tier = $stamp_duty_tier;
                        $tier_percentage = $stamp_duty_tier_percentage;
                        $overall_value = $temp_loan_sum = $loanCase->purchase_price;
                    } elseif ($details->account_formula == '${memorandum_of_transfer}') {
                        $tier = $memorandum_of_transfer_tier;
                        $tier_percentage = $memorandum_of_transfer_tier_percentage;
                        $overall_value = $loanCase->purchase_price;
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
                        $temp_loan_sum = $loanCase->loan_sum;
                    } elseif ($details->account_formula == '${property_agreement}') {
                        $temp_loan_sum = $loanCase->purchase_price;
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
                    $loan_sum = ceil($loanCase->loan_sum / $mult) * $mult;
            
                    // $loan_sum = round($loanCase->loan_sum,-3);
                    $formula_amt = $loan_sum * 0.005;
                } elseif ($details->account_formula == '${memo_transfer}') {
                    $overall_value = $loanCase->purchase_price;
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
            
            if ($details->account_cat_id == 1 || $details->account_cat_id == 4) {
                // $subtotal += $formula_amt * 1.06;
                // $totalSST += round($formula_amt * 0.06, 2);
                $subtotal += $formula_amt * (1 + $sst_percentage);
                $totalSST += round($formula_amt * $sst_percentage, 2);
            } else {
                $subtotal += $formula_amt;
            }
            
            ?>
            <tr>
                @php
                    $max_cap = $details->max;

                    if( $details->account_formula == '${property_price_max_3}')
                    {
                        $max_cap = $loanCase->purchase_price * 0.03;
                    }
                @endphp

                <td class="text-center" style="width:50px">
                    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}"
                        id="account_item_id_{{ $details->id }}">
                    <input type="hidden" name="need_approval" value="{{ $details->need_approval }}"
                        id="need_approval_{{ $details->id }}">
                    <input type="hidden" name="cat_id" value="{{ $cat['category']->id }}"
                        id="cat_{{ $details->id }}">
                    <input type="hidden" name="min" value="{{ $details->account_min }}"
                        id="min_{{ $details->id }}">
                    <input type="hidden" name="max" value="{{ $max_cap }}" id="max_{{ $details->id }}">
                    {{-- <input type="hidden" name="max" value="10" id="max_{{ $details->id }}"> --}}
                    <div class="checkbox">
                         @if ($current_user->branch_id == 1)
                              <input onchange="updateQuotation({{ $details->id }}, {{ $details->account_cat_id }})"
                                type="checkbox" name="quotation" value="{{ $details->id }}" id="chk_{{ $details->id }}"
                                checked @if ($id != 16 && $details->mandatory == 1 && !in_array($current_user->menuroles, ['admin', 'account']) && !in_array($current_user->id, [122,158,165,167,177,176,201])) disabled @endif>
                            <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                         @else
                           <input onchange="updateQuotation({{ $details->id }}, {{ $details->account_cat_id }})"
                            type="checkbox" name="quotation" value="{{ $details->id }}" id="chk_{{ $details->id }}"
                            checked >
                            <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                         @endif
                      
                    </div>
                </td>
                <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                <td id="item_{{ $details->id }}">{{ $details->account_name }} @if($QuotationTemplateMain->isChinese == 1) {{ $details->account_name_cn }} @endif 
                    @if ($details->mandatory == 1)
                        <span style="color:red">*</span>
                    @endif

                    @if ($cat['category']->id == 1)
                        @php
                            $content = '';

                        @endphp
                        @if ($details->pfee_item_desc == 1)
                            @php
                                if ($details->item_remark != '') {
                                    $content = $details->item_remark;
                                } else {
                                    $content = $details->item_desc;
                                }
                            @endphp

                            <textarea class="form-control" id="desc_span_{{ $details->id }}" name="desc" rows="5"
                                onchange="updateQuotationSpan('{{ $details->id }}')">{!! $content !!}</textarea>

                        @else
                            <textarea style="display: none" class="form-control" id="desc_span_{{ $details->id }}" name="desc" rows="5"
                                onchange="updateQuotationSpan('{{ $details->id }}')">{!! $content !!}</textarea>
                        @endif
                        
                       
                    @endif
                </td>


                <td>
                    {{ number_format($details->account_min, 2, '.', ',') }}
                </td>
                
                <td>
                    {{ number_format($max_cap, 2, '.', ',') }}
                </td>

                <td><input onchange="updateQuotation({{ $details->id }}, {{ $details->account_cat_id }})"
                        class="form-control crete_bill_input" name="cat_{{ $cat['category']->code }}" type="number"
                        value="{{ number_format((float) $formula_amt, 2, '.', '') }}" id="quo_amt_{{ $details->id }}"
                        @if ($details->pfee1_item == 1) disabled @endif>
                </td>

                @if ($details->account_cat_id == 1 || $details->account_cat_id == 4)
                    <td class="text-right" id="sst_{{ $details->id }}">
                        {{-- {{ number_format($formula_amt * 0.06, 2, '.', ',') }} --}}
                        {{ number_format($formula_amt * $sst_percentage, 2, '.', ',') }}
                    </td>
                @else
                    <td>-</td>
                @endif

                @if ($details->account_cat_id == 1 || $details->account_cat_id == 4)
                    <td class="text-right " id="amt_sst_{{ $details->id }}">
                        {{ number_format($formula_amt * (1+$sst_percentage), 2, '.', ',') }}</td>
                    <input type="hidden" id="int_amt_sst_{{ $details->id }}"
                        class="sub_total_{{ $details->account_cat_id }}"
                        value="{{ number_format($formula_amt * (1+$sst_percentage), 2, '.', '') }}" />
                @else
                    <td class="text-right " id="amt_sst_{{ $details->id }}">
                        {{ number_format($formula_amt, 2, '.', ',') }}</td>
                    <input type="hidden" id="int_amt_sst_{{ $details->id }}"
                        class="sub_total_{{ $details->account_cat_id }}"
                        value="{{ number_format($formula_amt, 2, '.', '') }}" />
                @endif

            </tr>
        @endforeach


        @if(count($cat['account_details']) > 0)
         <tr>
            <th colspan="5">Sub Total </th>
            @if ($details->account_cat_id == 1 || $details->account_cat_id == 4)
                <th style="text-align:right" id="sub_total_sst_{{ $details->account_cat_id }}">
                    {{ number_format($totalSST, 2, '.', ',') }}</th>
            @endif

            <th style="text-align:right" id="sub_total_{{ $details->account_cat_id }}" colspan="2">
                {{ number_format($subtotal, 2, '.', ',') }}</th>
            <input type="hidden" id="int_sub_total_{{ $details->account_cat_id }}" class="sub_total"
                value="{{ number_format($subtotal * (1+$sst_percentage), 2, '.', '') }}" />

        </tr>
        <?php $total += $subtotal; ?>
        @endif

       
    @endforeach



    <tr style=";background-color:black;color:white">
        <th>Total </th>
        <th style="text-align:right" id="total_sum_bill" colspan="6">{{ number_format($total, 2, '.', ',') }}</th>

    </tr>
@else
    <tr>
        <th class="text-center" colspan="7">No data</th>
    </tr>
@endif
