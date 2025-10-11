<tbody id="tbl-print-invoices">

    <tr style="padding:0px !important;border: 1px solid black;padding-left:10px;">
        <th>Description</th>
        <th class="text-right">Amount (RM)</th>
        <th class="text-right">SST ({{ round($LoanCaseBillMain->sst_rate, 0) }}%)</th>
        <th class="text-right">Total (RM)</th>
    </tr>
    <?php
    $total = 0;
    $subtotal = 0;
    $totalSST = 0;
    $pf_total = 0;
    $total_SUM_Amt_SST = 0;
    $total_SST_new = 0;
    ?>
    @if (count($invoice))


        @foreach ($invoice as $index => $cat)
            <td colspan="5"
                style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
                <span><b style="color:white;font-size:15px">{{ $cat['category']->category }}</b></span>
            </td>

            <?php
            $subtotal = 0;
            $totalSST = 0;
            $pf_total = 0;
            $subtotalSST = 0;
            $sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
            ?>
            @if ($cat['category']->id == 1)
            @endif

            <?php $category_amount = 0; ?>
            @foreach ($cat['account_details'] as $index => $details)
                <?php
                $row_sst = 0;
                if ($cat['category']->taxable == '1') {
                    $row_sst = (float) ($details->amount * $sst_rate);
                    $subtotalSST += round($details->amount * ($sst_rate + 1), 2);
                    $total_SST_new += round($details->amount * $sst_rate, 2);
                } else {
                    $subtotalSST += round($details->amount, 2);
                }
                $subtotal += $row_sst;
                
                $totalSST += (float) ($details->amount * $sst_rate);
                $pf_total += $details->amount;
                $row_total = $details->amount + $row_sst;
                ?>

                <tr>
                    <td
                        style="border-left: 1px solid black;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;padding-bottom:10px !important;">
                        {{ $index + 1 }}. {{ $details->account_name }}  @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
                        @if ($cat['category']->id == 1)
                            @if ($details->item_remark)
                                <hr style="margin-top:1px !important;margin-bottom:1px !important" />
                                {!! $details->item_remark !!}
                            @else
                                @if ($details->item_desc)
                                    <hr style="margin-top:1px !important;margin-bottom:1px !important" />
                                    {!! $details->item_desc !!}
                                @endif
                            @endif
                        @endif
                    </td>
                    <td
                        style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">
                        {{ number_format((float) $details->amount, 2, '.', ',') }}</td>
                    <td
                        style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">

                        @if ($cat['category']->taxable == '1')
                            {{ number_format((float) $row_sst, 2, '.', ',') }}
                        @else
                            -
                        @endif
                    </td>
                    <td
                        style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">
                        {{ number_format((float) $row_total, 2, '.', ',') }}</td>

                </tr>
            @endforeach

            <?php $total += $subtotal; ?>
            @php

                $total_SUM_Amt_SST += $subtotalSST;
            @endphp
            @if ($cat['category']->taxable == '1')
            @endif


            <tr style="padding:0px !important;border: 1px solid black">
                <td class="text-left"
                    style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;">Subtotal:</td>
                <td
                    style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">
                    {{ number_format((float) $pf_total, 2, '.', ',') }}</td>
                <td
                    style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">
                    @if ($cat['category']->taxable == '1')
                        {{ number_format((float) $total_SST_new, 2, '.', ',') }}
                    @else
                        -
                    @endif
                </td>
                <td
                    style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;">
                    {{ number_format((float) $subtotalSST, 2, '.', ',') }}</td>
            </tr>
        @endforeach



        <tr style="padding:0px !important;border: 1px solid black;padding-top:10px !important;">
            <td style="padding:0px !important;padding-left:10px !important"><span><b style="font-size:15px">Total
                        :</b></span> </td>
            <td style="text-align:right;padding:0px !important;padding-right:10px !important;" colspan="5"><b
                    style="font-size:15px"> {{ number_format((float) $total_SUM_Amt_SST, 2, '.', ',') }}</b></td>

        </tr>
    @else
        <tr>
            <td class="text-center" colspan="5">No data</td>
        </tr>
    @endif

</tbody>
