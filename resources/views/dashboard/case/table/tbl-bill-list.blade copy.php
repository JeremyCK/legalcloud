<?php
$total = 0;
$subtotal = 0;
?>
@if (count($quotation))
    <tr style="background-color:grey;color:white">
        <td colspan="2"></td>
        <td>Min Value</td>
        <td>Quotation Amount (Without SST)</td>
        <td>SST</td>
        <td>Quotation Amount + SST</td>
    </tr>

    @foreach ($quotation as $index => $cat)
        <tr style="background-color:grey;color:white">
            <td colspan="6">{{ $cat['category']->category }}</td>
            {{-- <?php $total += $subtotal; ?> --}}
            <?php $subtotal = 0; ?>
        </tr>
        <?php $category_amount = 0; ?>
        <?php $agreement_fee = 0; ?>
        @foreach ($cat['account_details'] as $index => $details)
            {{-- <?php $subtotal += $details->amount; ?> --}}
            <?php
            
            $formula_amt = 0;
            $blnContinue = true;
            $scanle_fee_tier = [500000, 500000, 2000000, 2000000, 2000000];
            $scanle_fee_tier_percentage = [0.01, 0.008, 0.007, 0.006, 0.005];

            $scale_fee_tier_2023 = [500000,7000000,7500000];
            $scale_fee_tier_2023_percentage = [0.0125,0.01,0.01];
            $scale_fee_min_2023 = 500;
            
            $agreement_fee_tier = [500000, 500000, 2000000, 2000000, 2000000];
            $agreement_fee_tier_percentage = [0.01, 0.008, 0.007, 0.006, 0.005];
            
            $memo_of_transfer_tier = [50000, 200000, 500000, 1000000];
            $memo_of_transfer_tier_amt = [50, 200, 400, 500];
            
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

                            if ($blnContinue == true)
                            {
                                if ($temp_loan_sum - $scale_fee_tier_2023[$i] >= 0)
                                {
                                    $value += $scale_fee_tier_2023[$i] * $scale_fee_tier_2023_percentage[$i];
                                    $temp_loan_sum = $temp_loan_sum - $scale_fee_tier_2023[$i];
                                }
                                else
                                {
                                    $value += $temp_loan_sum * $scale_fee_tier_2023_percentage[$i];
                                    $blnContinue = false;
                                }
                            }
                        }
                    }

                    if($value < $scale_fee_min_2023)
                    {
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
            
            if ($details->account_cat_id == 1) {
                $subtotal += $formula_amt * 1.06;
            } else {
                $subtotal += $formula_amt;
            }
            
            ?>
            <tr>
                <td class="text-center" style="width:50px">
                    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}"
                        id="account_item_id_{{ $details->id }}">
                    <input type="hidden" name="need_approval" value="{{ $details->need_approval }}"
                        id="need_approval_{{ $details->id }}">
                    <input type="hidden" name="cat_id" value="{{ $cat['category']->id }}"
                        id="cat_{{ $details->id }}">
                    <input type="hidden" name="min" value="{{ $details->account_min }}"
                        id="min_{{ $details->id }}">
                    <input type="hidden" name="max" value="{{ $details->max }}" id="max_{{ $details->id }}">
                    <div class="checkbox">
                        <input type="checkbox" name="quotation" value="{{ $details->id }}"
                            id="chk_{{ $details->id }}" checked @if($details->mandatory == 1 && !in_array($current_user->menuroles, ['admin', 'account'])) disabled @endif>
                        <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                    </div>
                </td>
                <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                <td id="item_{{ $details->id }}">{{ $details->account_name }} @if($details->mandatory == 1) <span style="color:red">*</span> @endif</td>


                <td>
                    {{ number_format($details->account_min, 2, '.', ',') }}
                </td>

                <td><input onchange="updateQuotation({{ $details->id }}, {{ $details->account_cat_id }})" class="form-control" name="cat_{{ $cat['category']->code }}" type="number"
                        value="{{ number_format((float) $formula_amt, 2, '.', '') }}"
                        id="quo_amt_{{ $details->id }}" @if($details->pfee1_item == 1) disabled @endif>
                </td>

                @if ($details->account_cat_id == 1)
                    <td class="text-right" id="sst_{{ $details->id }}">
                        {{ number_format($formula_amt * 0.06, 2, '.', ',') }}</td>
                @else
                    <td>-</td>
                @endif

                @if ($details->account_cat_id == 1)
                    <td class="text-right sub_total_{{ $details->account_cat_id }}" id="amt_sst_{{ $details->id }}">
                        {{ number_format($formula_amt * 1.06, 2, '.', ',') }}</td>
                @else
                    <td class="text-right sub_total_{{ $details->account_cat_id }}">{{ number_format($formula_amt, 2, '.', ',') }}</td>
                @endif

            </tr>
        @endforeach



        <tr>
            <td>Sub Total </td>
            <td style="text-align:right" id="sub_total_{{ $details->account_cat_id }}" colspan="5">{{ number_format($subtotal, 2, '.', ',') }}</td>

        </tr>
        <?php $total += $subtotal; ?>
    @endforeach



    <tr>
        <td>Total </td>
        <td style="text-align:right" colspan="5">{{ number_format($total, 2, '.', ',') }}</td>

    </tr>
@else
    <tr>
        <td class="text-center" colspan="6">No data</td>
    </tr>
@endif

