<?php
$total = 0;
$totalSST = 0;
$totalOri = 0;
$totalEdit = 0;
$subtotal = 0;
$totalNoSST = 0;
$sstTotal = 0;
$sumSST = 0;

$sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
?>
@if (count($invoice))
    @foreach ($invoice as $index => $cat)
        <tr style="background-color:grey;color:white">
            @if (
                $current_user->menuroles == 'account' ||
                    $current_user->menuroles == 'admin' ||
                    $current_user->menuroles == 'management' ||
                    $current_user->menuroles == 'maker')
                <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}
                    @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                        <button class="btn btn-info float-right " data-backdrop="static" data-keyboard="false"
                            onclick="addAccountItemModalInvoice('{{ $cat['category']->id }}')" data-toggle="modal"
                            data-target="#accountItemModalInvoice" type="button"><i class="cil-plus"></i>Add </button>
                    @endif
                </td>
            @elseif($current_user->menuroles == 'sales'  || in_array($current_user->id, [51, 32, 13]))
                <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}<button
                        class="btn btn-info float-right " data-backdrop="static" data-keyboard="false"
                        onclick="addAccountItemModalInvoice('{{ $cat['category']->id }}')" data-toggle="modal"
                        data-target="#accountItemModalInvoice" type="button"><i class="cil-plus"></i>Add </button></td>
            @else
                <td class="quotation-colspan" colspan="3">{{ $cat['category']->category }}</td>
            @endif

            <?php
            $subtotal = 0;
            $subtotalGST = 0;
            $subtotalnosset = 0;
            $subtotalOri = 0;
            $subtotalEdit = 0;
            ?>
        </tr>
        <?php $category_amount = 0; ?>
        @foreach ($cat['account_details'] as $index => $details)
            <?php
            if ($cat['category']->id == 1) {
                // $subtotal += ($details->amount *1.06);
                // $subtotal += round(($details->amount *1.06), 2);
                $subtotalGST += round($details->amount * (1 + $sst_rate), 2);
                $sumSST += round($details->amount * $sst_rate, 2);
            } else {
                // $subtotal += $details->amount;
                $subtotalGST += round($details->amount, 2);
            }
            
            $subtotal += round($details->amount, 2);
            
            $subtotalnosset += $details->quo_amount;
            $subtotalOri += $details->quo_amount;
            $subtotalEdit += $details->quo_amount;
            ?>

            <tr>
                <td class="text-center" style="width:50px">
                    <input class="form-control" type="hidden" value="{{ $details->quo_amount }}"
                        id="quo_amount_{{ $details->id }}">
                    <input class="form-control" type="hidden" value="0" id="bln_modified_{{ $details->id }}">
                    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}"
                        id="account_item_id_{{ $details->id }}">
                    <div class="checkbox">
                        @if ($cat['category']->id != 1)
                            <input type="checkbox" name="case_bill" value="{{ $details->id }}"
                                id="chk_{{ $details->id }}" @if ($details->amount == 0) disabled @endif>
                        @else
                        @endif



                        <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                    </div>
                </td>
                <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                <td id="item_{{ $details->id }}">{{ $details->account_name }} @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
                    @if ($cat['category']->id == 1)
                         @if($details->item_remark)
                            <hr/>
                            {!! $details->item_remark !!}
                        @else
                            @if($details->item_desc)
                            <hr/>
                            {!! $details->item_desc !!}
                            @endif
                        @endif
                    @endif
                </td>
                <td class="text-right" id="amt_{{ $details->id }}">
                    {{ number_format($details->quo_amount, 2, '.', ',') }}</td>
               
                    @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) || in_array($current_user->id, [51, 32, 13]))
                    <td class="text-right" id="amt_quo_{{ $details->id }}">
                        {{ number_format($details->amount, 2, '.', ',') }}
                @endif

                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) || in_array($current_user->id, [51, 32, 13]))
                    @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                            onclick="editInvoiceModal('{{ $details->amount }}','{{ $details->id }}','{{ $cat['category']->id }}',1,'{{ $details->account_name }}')"
                            data-toggle="modal" data-target="#myModalInvoice" class="btn btn-xs btn-primary"><i
                                class="cil-pencil"></i></a>
                    @endif
                @endif

                </td>
                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) || in_array($current_user->id, [51, 32, 13]))
                    <td class="text-right" id="amt_sst_{{ $details->id }}">
                        @if ($cat['category']->id == 1)
                            {{ number_format($details->amount * $sst_rate, 2, '.', ',') }}
                        @else
                            -
                        @endif

                        <?php
                        if ($cat['category']->id == 1) {
                            $sstTotal += $details->amount * $sst_rate;
                        }
                        ?>
                    <td class="text-right" id="amt_sst_quo_{{ $details->id }}">
                        @if ($cat['category']->id == 1)
                            {{ number_format($details->amount * (1 + $sst_rate), 2, '.', ',') }}
                        @else
                            {{ number_format($details->amount, 2, '.', ',') }}
                        @endif
                        @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                            <a href="javascript:void(0)" onclick="deleteInvoiceItem('{{ $details->id }}')"
                                class="btn btn-xs btn-danger"><i class="cil-x"></i></a>
                        @endif
                    </td>
                @endif


            </tr>
        @endforeach

        <?php
        
        $total += $subtotal;
        $totalSST += $subtotalGST;
        $totalOri += $subtotalOri;
        $totalEdit += $subtotalEdit;
        
        $totalNoSST += $subtotalnosset;
        
        ?>


        <tr>
            <td>Sub Total</td>
            <td style="text-align:right" colspan="2">RM {{ number_format($subtotalnosset, 2, '.', ',') }}</td>

            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) || in_array($current_user->id, [51, 32, 13]))
                <td style="text-align:right">RM {{ number_format($subtotal, 2, '.', ',') }}</td>
                <td class="" style="text-align:right" id="sub_total_ori_{{ $cat['category']->code }}">
                    @if ($cat['category']->id == 1)
                        {{-- RM {{ number_format($subtotal*0.06, 2, '.', ',') }} --}}
                        RM {{ number_format((float) $sumSST, 2, '.', ',') }}
                    @else
                        -
                    @endif


                </td>

                <td class="" style="text-align:right" id="sub_total_edit_{{ $cat['category']->code }}">
                    {{-- @if ($cat['category']->id == 1)
    RM {{ number_format($subtotal*1.06, 2, '.', ',') }}
    @else
    RM {{ number_format($subtotal, 2, '.', ',') }} 
    @endif --}}

                    RM {{ number_format((float) $subtotalGST, 2, '.', ',') }}
                </td>
            @endif


        </tr>
    @endforeach



    <tr>
        <td>Total</td>
        <td style="text-align:right" class="quotation-total-colspan" colspan="2"> RM
            {{ number_format((float) $totalNoSST, 2, '.', ',') }}</td>
            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) || in_array($current_user->id, [51, 32, 13]))
            <td style="text-align:right" class=""> RM {{ number_format((float) $total, 2, '.', ',') }}</td>
            <td style="text-align:right" class="" id="total_edit"> RM {{ number_format($sumSST, 2, '.', ',') }}
            </td>
            <td style="text-align:right" class="" id="total_edit"> RM {{ number_format($totalSST, 2, '.', ',') }}
            </td>
            <!-- @if ($current_user->menuroles != 'sales')
<td style="text-align:right" class="quotation" id="total_edit"> RM {{ number_format((float) $totalEdit, 2, '.', ',') }}</td>
@endif -->
        @endif


    </tr>
@else
    <tr>
        <td class="text-center" colspan="5">No data</td>
    </tr>
@endif
