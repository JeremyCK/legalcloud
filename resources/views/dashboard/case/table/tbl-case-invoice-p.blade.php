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
                    // Use custom SST if available, otherwise calculate
                    $row_sst = 0;
                    
                    // Check for custom SST value - $details is an object from DB query
                    // First try to access SST value
                    $sstRaw = null;
                    if (is_object($details) && (property_exists($details, 'sst') || isset($details->sst))) {
                        $sstRaw = $details->sst;
                    } elseif (is_array($details) && isset($details['sst'])) {
                        $sstRaw = $details['sst'];
                    }
                    
                    // Check if we have a valid non-zero SST value
                    $hasCustomSst = false;
                    if ($sstRaw !== null && $sstRaw !== '') {
                        $sstString = trim((string)$sstRaw);
                        // Accept if not empty and not zero
                        if ($sstString !== '' && $sstString !== '0' && $sstString !== '0.00' && $sstString !== '0.0') {
                            $row_sst = (float) $sstRaw;
                            $hasCustomSst = true;
                            
                            // Debug for detail 125032 (Sale and Purchase Agreement)
                            if (isset($details->id) && $details->id == 125032) {
                                \Log::info("VIEW - Using custom SST for detail 125032", [
                                    'detail_id' => $details->id,
                                    'sst_raw' => $sstRaw,
                                    'sst_string' => $sstString,
                                    'row_sst' => $row_sst
                                ]);
                            }
                        }
                    }
                    
                    // Additional debug for detail 125032 if not using custom SST
                    if (!$hasCustomSst && isset($details->id) && $details->id == 125032) {
                        \Log::warning("VIEW - NOT using custom SST for detail 125032", [
                            'detail_id' => $details->id,
                            'sst_raw' => $sstRaw,
                            'sst_type' => $sstRaw !== null ? gettype($sstRaw) : 'NULL',
                            'is_object' => is_object($details),
                            'property_exists' => is_object($details) ? property_exists($details, 'sst') : false,
                            'isset' => isset($details->sst)
                        ]);
                    }
                    
                    // If no custom SST, calculate it
                    if (!$hasCustomSst) {
                        // Calculate SST with special rounding rule
                        $sst_calculation = $details->amount * $sst_rate;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100; // Round down
                        } else {
                            $row_sst = round($sst_calculation, 2); // Normal rounding
                        }
                        
                        // Debug for detail 125032 - log when calculating
                        if (isset($details->id) && $details->id == 125032) {
                            \Log::info("VIEW - Calculating SST (no custom value found) for detail 125032", [
                                'detail_id' => $details->id,
                                'amount' => $details->amount,
                                'sst_rate' => $sst_rate,
                                'calculated_sst' => $row_sst,
                                'sst_property_exists' => property_exists($details, 'sst'),
                                'sst_value' => $details->sst ?? 'NULL',
                                'why_failed' => [
                                    'property_exists' => property_exists($details, 'sst'),
                                    'isset' => isset($details->sst),
                                    'not_null' => isset($details->sst) ? ($details->sst !== null) : false,
                                    'not_empty' => isset($details->sst) ? (trim((string)$details->sst) !== '') : false,
                                ]
                            ]);
                        }
                    }
                    $subtotalSST += round($details->amount + $row_sst, 2);
                    $total_SST_new += $row_sst;
                } else {
                    $subtotalSST += round($details->amount, 2);
                }
                $subtotal += $row_sst;
                
                $totalSST += $row_sst;
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
                            @php
                                // Debug for detail 168726 - log right before display
                                if (isset($details->id) && $details->id == 168726) {
                                    \Log::info("VIEW - Displaying SST", [
                                        'detail_id' => $details->id,
                                        'row_sst_before_format' => $row_sst,
                                        'row_sst_type' => gettype($row_sst),
                                        'formatted_value' => number_format((float) $row_sst, 2, '.', ',')
                                    ]);
                                }
                            @endphp
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
