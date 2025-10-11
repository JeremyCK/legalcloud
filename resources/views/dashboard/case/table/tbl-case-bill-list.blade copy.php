<?php
$total = 0;
$totalOri = 0;
$totalEdit = 0;
$subtotal = 0;
$totalNoSST = 0;
$sstTotal = 0;

$sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
?>
@if (count($quotation))


    @foreach ($quotation as $index => $cat)

        @if(count($cat['account_details']) > 0)
            <tr style="background-color:grey;color:white">
                @if(in_array($current_user->menuroles, ['account', 'maker']))
            
                    <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}
                        @if ($LoanCaseBillMain->bln_invoice == 0 && $blnCommPaid == 0)
                        <button
                            class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false"
                            onclick="addAccountItemModal('{{ $cat['category']->id }}')" data-toggle="modal"
                            data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button>
                        @endif
                        </td>
                @elseif(in_array($current_user->menuroles, ['admin', 'management']))
                <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }} 
                    <button
                    class="btn btn-info float-right quotation2" data-backdrop="static" data-keyboard="false"
                    onclick="addAccountItemModal('{{ $cat['category']->id }}')" data-toggle="modal"
                    data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button>
                </td>
                @elseif(in_array($current_user->menuroles, ['sales', 'clerk', 'lawyer', 'chambering']))
                    <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}
                        @if ($LoanCaseBillMain->bln_invoice == 0 && $blnCommPaid == 0)
                            <button class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false"
                                onclick="addAccountItemModal('{{ $cat['category']->id }}')" data-toggle="modal"
                                data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button>
                        @endif
                    </td>
                @else
                    <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}</td>
                @endif

                <?php   
                $subtotal = 0;
                $subtotalnosset = 0;
                $subtotalOri = 0;
                $subtotalEdit = 0;
                $sstTotal = 0;
                
                ?>

            </tr>
        @endif
        
        <?php $category_amount = 0; ?>
        @foreach ($cat['account_details'] as $index => $details)
            <?php
            $subtotal += $details->amount;
            $subtotalnosset += $details->quo_amount_no_sst;
            
            $subtotalOri += $details->quo_amount;
            $subtotalEdit += $details->invoice_amount;
            
            // else
            // {
            
            //   $sstTotal +=($details->quo_amount*0.06);
            // }
            // $sstTotal = ($details->quo_amount_no_sst * 0.06);
            
            ?>

            <tr>
                <td class="text-center" style="width:50px">
                    <input class="form-control" type="hidden" value="{{ $details->quo_amount }}"
                        id="quo_amount_{{ $details->id }}">
                    <input class="form-control" type="hidden" value="0" id="bln_modified_{{ $details->id }}">
                    <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}"
                        id="account_item_id_{{ $details->id }}">
                    <input type="hidden" name="need_approval" value="{{ $details->need_approval }}"
                        id="need_approval_{{ $details->id }}">
                    <input type="hidden" name="min" value="{{ $details->min }}" id="min_{{ $details->id }}">
                    <input type="hidden" name="max" value="{{ $details->max }}" id="max_{{ $details->id }}">
                    <div class="checkbox">
                        @if ($cat['category']->id != 1)
                            <input type="checkbox" name="case_bill" value="{{ $details->id }}"
                                id="chk_{{ $details->id }}" @if ($details->amount <= 0) disabled @endif>
                        @else
                        @endif



                        <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                    </div>
                </td>
                <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                <td id="item_{{ $details->id }}">{{ $details->account_name }}  @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
                    @if ($cat['category']->id == 1 || $cat['category']->id == 4)
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
                <td class="text-right" id="amt_{{ $details->id }}">{{ number_format($details->amount, 2, '.', ',') }}
                </td>
                {{-- @if ($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'sales') --}}
                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'chambering', 'maker']))
                    <td class="text-right" id="amt_quo_{{ $details->id }}">
                        {{ number_format($details->quo_amount_no_sst, 2, '.', ',') }}
                @endif
                @if ($LoanCaseBillMain->bln_invoice == 0 && $blnCommPaid == 0)
                    {{-- @if ($current_user->menuroles == 'account' ||
                        $current_user->menuroles == 'admin' ||
                        $current_user->menuroles == 'management' ||
                        $current_user->menuroles == 'sales') --}} 
                    @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'chambering', 'maker']))  
                      
                        @if ($details->pfee1_item != 1 || in_array($current_user->menuroles,['admin','account','maker']))
                            <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                onclick="editQuotationModal('{{ $details->quo_amount_no_sst }}','{{ $details->id }}','{{ $cat['category']->id }}',1,'<?php echo addslashes($details->account_name); ?>')"
                                data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i
                                    class="cil-pencil"></i></a>
                        @endif
                    @endif
                @else
                    @if ($current_user->menuroles == 'admin' )
                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                            onclick="editQuotationModal('{{ $details->quo_amount_no_sst }}','{{ $details->id }}','{{ $cat['category']->id }}',1,'<?php echo addslashes($details->account_name); ?>')"
                            data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i
                                class="cil-pencil"></i></a>
                    @endif

                    @if ($LoanCaseBillMain->id == 175)
                        @if ($current_user->id == 29 && $details->pfee1_item != 1)
                            <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                onclick="editQuotationModal('{{ $details->quo_amount_no_sst }}','{{ $details->id }}','{{ $cat['category']->id }}',1,'<?php echo addslashes($details->account_name); ?>')"
                                data-toggle="modal" data-target="#myModal" class="btn btn-xs btn-primary"><i
                                    class="cil-pencil"></i></a>
                        @endif
                    @endif
                @endif

                </td>
                {{-- @if ($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'sales') --}}
                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'chambering', 'maker']))
                    <td class="text-right" id="amt_sst_{{ $details->id }}">
                        @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                            {{-- {{ number_format($details->quo_amount_no_sst * 0.06, 2, '.', ',') }} --}}
                            {{ number_format($details->quo_amount_no_sst * $sst_rate, 2, '.', ',') }}
                            
                        @else
                            -
                        @endif

                        <?php
                        if ($cat['category']->id == 1 || $cat['category']->id == 4) {
                            // $sstTotal += $details->quo_amount_no_sst * 0.06;
                            // $sstTotal += round($details->quo_amount_no_sst * 0.06, 2);
                            $sstTotal += round($details->quo_amount_no_sst * $sst_rate, 2);
                        }
                        ?>
                    <td class="text-right" id="amt_sst_quo_{{ $details->id }}">
                        @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                            {{ number_format($details->quo_amount_no_sst * (1 + $sst_rate), 2, '.', ',') }}
                        @else
                            {{ number_format($details->quo_amount_no_sst, 2, '.', ',') }}
                        @endif
                        @if ($LoanCaseBillMain->bln_invoice == 0 && $blnCommPaid == 0)
                            {{-- @if ($current_user->menuroles == 'account' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'management' || $current_user->menuroles == 'sales') --}}
                            {{-- @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'chambering'])) --}}
                            
                            @if(in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'chambering', 'maker']))
                                @if ($LoanCaseBillMain->quotation_template_id == 16 || $details->pfee1_item != 1 || in_array($current_user->menuroles, ['admin','account','maker']))
                                    @if($LoanCaseBillMain->quotation_template_id == 16 || $details->mandatory == 0 || (in_array($current_user->menuroles, ['account', 'admin', 'maker'])))
                                    <a href="javascript:void(0)" onclick="deleteQuotationItem('{{ $details->id }}')" class="btn btn-xs btn-danger"><i class="cil-x"></i></a>
                                    @endif
                                  
                                @endif
                            @endif
                            
                        @else
                            @if (in_array($current_user->menuroles, ['admin','account','maker']) && $details->pfee1_item != 1)
                                @if($details->mandatory == 0)
                                    <a href="javascript:void(0)" onclick="deleteQuotationItem('{{ $details->id }}')"
                                        class="btn btn-xs btn-danger"><i class="cil-x"></i></a>
                                @endif
                            @endif

                            @if ($LoanCaseBillMain->id == 175)
                                @if ($current_user->id == 29 && $details->pfee1_item != 1)
                                
                                    <a href="javascript:void(0)" onclick="deleteQuotationItem('{{ $details->id }}')"
                                        class="btn btn-xs btn-danger"><i class="cil-x"></i></a>
                                @endif
                            @endif
                        @endif
                    </td>
                @endif


            </tr>
        @endforeach

        <?php
        $total += $subtotal;
        $totalOri += $subtotalOri;
        $totalEdit += $subtotalEdit;
        
        $totalNoSST += $subtotalnosset;
        
        ?>

        
        @if(count($cat['account_details']) > 0)
            <tr>
                <td>Sub Total</td>
                <td style="text-align:right" colspan="2">RM {{ number_format($subtotal, 2, '.', ',') }}</td>
                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'chambering', 'maker']))
                    <td style="text-align:right">RM {{ number_format($subtotalnosset, 2, '.', ',') }}</td>
                    <td class="" style="text-align:right" id="sub_total_ori_{{ $cat['category']->code }}">
                        @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                            RM {{ number_format($sstTotal, 2, '.', ',') }}
                        @else
                            -
                        @endif


                    </td>

                    <td class="" style="text-align:right" id="sub_total_edit_{{ $cat['category']->code }}">
                        @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                            RM {{ number_format($subtotalnosset + $sstTotal, 2, '.', ',') }}
                        @else
                            RM {{ number_format($subtotalnosset, 2, '.', ',') }}
                        @endif

                    </td>
                @endif


            </tr>
        @endif

       
    @endforeach



    <tr>
        <td>Total</td>
        <td style="text-align:right" class="quotation-total-colspan" colspan="2"> RM
            {{ number_format((float) $total, 2, '.', ',') }}</td>
        {{-- @if ($current_user->menuroles == 'account' ||
            $current_user->menuroles == 'admin' ||
            $current_user->menuroles == 'management' ||
            $current_user->menuroles == 'sales') --}}
        @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'chambering', 'maker']))
            <td style="text-align:right" class=""> RM {{ number_format((float) $totalNoSST, 2, '.', ',') }}
            </td>
            <td style="text-align:right" class="" id="total_edit"> RM
                {{ number_format((float) $sstTotal, 2, '.', ',') }}</td>
            <td style="text-align:right" class="" id="total_edit"> RM
                {{ number_format($totalNoSST + $sstTotal, 2, '.', ',') }}</td>
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
