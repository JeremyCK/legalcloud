<thead>
    <tr>
        <th style="width:55%; border: 1px solid black; padding: 8px;">Description</th>
        <th style="width:15%; border: 1px solid black; padding: 8px; text-align: right;">Amount (RM)</th>
        <th style="width:15%; border: 1px solid black; padding: 8px; text-align: right;">SST ({{$sst_rate}}%)</th>
        <th style="width:15%; border: 1px solid black; padding: 8px; text-align: right;">Total (RM)</th>
    </tr>
</thead>
<tbody id="tbl-print-quotationds">


    @php
        $total = 0;
        $subtotal = 0;
        $total_sst = 0;

        $sst_percentage = $sst_rate * 0.01;
    @endphp
    @if (count($account_list_1))
        {{-- <td colspan="5" style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
            <span><b style="color:white;font-size:15px">Professional fees</b></span>
        </td> --}}
        <tr>
            <td colspan="4" style="padding:8px !important;border: 1px solid black;padding-left:10px  !important;background-color:#9fcff0 !important">
                <span><b style="font-size:15px;color:black">Professional fees</b></span>
            </td>
        </tr>
        @foreach ($account_list_1 as $index => $item)
            @php
                // $subtotal += $item['amount'] * 1.06;
                $sst = round($item['amount'] * $sst_percentage, 2);
                $total_sst += $sst;
                $subtotal += $item['amount'] + $sst;
            @endphp
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black; padding: 8px;">{{ $index + 1 }}. {{ $item['account_name'] }} 

                    @if (isset($item['item_desc']))
                        @if ($item['item_desc'])
                            <hr style="margin-top:5px !important;margin-bottom:5px !important" />
                            {!! nl2br($item['item_desc']) !!}
                        @endif
                        
                    @endif

                </td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'], 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'] * $sst_percentage, 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'] * ($sst_percentage + 1), 2, '.', ',') }}</td>
            </tr>
        @endforeach
        <tr style="border: 1px solid black;">
            <td colspan="2" style="border: 1px solid black; padding: 8px;">
                <strong>Subtotal</strong>
            </td>
            <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right"> {{ number_format($total_sst, 2, '.', ',') }}</td>
            <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right"> {{ number_format($subtotal, 2, '.', ',') }}</td>
        </tr>
    @endif

    @php
        $total += $subtotal;
        $subtotal = 0;
    @endphp

    @if (count($account_list_4))
        {{-- <td colspan="5" style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
            <span><b style="color:white;font-size:15px">Professional fees</b></span>
        </td> --}}

        @php
                $total_sst =0;
            @endphp
        <tr>
            <td colspan="4" style="padding:8px !important;border: 1px solid black;padding-left:10px  !important;background-color:#9fcff0 !important">
                <span><b style="font-size:15px;color:black">Reimbursement</b></span>
            </td>
        </tr>
        @foreach ($account_list_4 as $index => $item)
            @php
                // $subtotal += $item['amount'] * 1.06;
                $sst = round($item['amount'] * $sst_percentage, 2);
                $total_sst += $sst;
                $subtotal += $item['amount'] + $sst;
            @endphp
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black; padding: 8px;">{{ $index + 1 }}. {{ $item['account_name'] }}

                    @if (isset($item['item_desc']))
                        @if ($item['item_desc'])
                            <hr style="margin-top:5px !important;margin-bottom:5px !important" />
                            {!! nl2br($item['item_desc']) !!}
                        @endif
                        
                    @endif

                </td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'], 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'] * $sst_percentage, 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'] * ($sst_percentage + 1), 2, '.', ',') }}</td>
            </tr>
        @endforeach
        <tr style="border: 1px solid black;">
            <td colspan="2" style="border: 1px solid black; padding: 8px;">
                <strong>Subtotal</strong>
            </td>
            <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right"> {{ number_format($total_sst, 2, '.', ',') }}</td>
            <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right"> {{ number_format($subtotal, 2, '.', ',') }}</td>
        </tr>
    @endif

    @php
        $total += $subtotal;
        $subtotal = 0;
    @endphp

    @if (count($account_list_2))

        <tr>
            {{-- <td colspan="5"
                style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
                <span><b style="color:white;font-size:15px">Stamp duties</b></span>
            </td> --}}
            <td colspan="4" style="padding:8px !important;border: 1px solid black;padding-left:10px  !important;background-color:#9fcff0 !important">
                <span><b style="font-size:15px;color:black">Stamp duties</b></span>
            </td>
        </tr>
        @foreach ($account_list_2 as $index => $item)
            @php
                $subtotal += $item['amount'];
            @endphp
            <tr style="border: 1px solid black;">
                <td style="text-align: left; border: 1px solid black; padding: 8px;">{{ $index + 1 }}. {{ $item['account_name'] }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'], 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> -</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'], 2, '.', ',') }}</td>
            </tr>
        @endforeach

        <tr style="border: 1px solid black;">
            <td colspan="3" style="border: 1px solid black; padding: 8px;">
                <strong>Subtotal</strong>
            </td>
            <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right"> {{ number_format($subtotal, 2, '.', ',') }}</td>
        </tr>
    @endif

    @php
        $total += $subtotal;
        $subtotal = 0;
    @endphp


    @if (count($account_list_3))

        <tr>
            {{-- <td colspan="5"
                style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
                <span><b style="color:white;font-size:15px">Disbursement</b></span>
            </td> --}}
            <td colspan="4" style="padding:8px !important;border: 1px solid black;padding-left:10px  !important;background-color:#9fcff0 !important">
                <span><b style="font-size:15px;color:black">Disbursement</b></span>
            </td>
        </tr>
        @foreach ($account_list_3 as $index => $item)
            @php
                $subtotal += $item['amount'];
            @endphp
            <tr style="border: 1px solid black;">
                <td style="text-align: left; border: 1px solid black; padding: 8px;">{{ $index + 1 }}. {{ $item['account_name'] }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'], 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> -</td>
                <td style="text-align: right; border: 1px solid black; padding: 8px;" class="text-right"> {{ number_format($item['amount'], 2, '.', ',') }}</td>
            </tr>
        @endforeach

        <tr style="border: 1px solid black !important;">
            <td colspan="3" style="border: 1px solid black; padding: 8px;">
                <strong>Subtotal</strong>
            </td>
            <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right"> {{ number_format($subtotal, 2, '.', ',') }}</td>
        </tr>
    @endif

    @php
        $total += $subtotal;
        $subtotal = 0;
    @endphp


    <tr style="border: 1px solid black; background-color:#d8dbe0 !important">
        <td colspan="3" style="border: 1px solid black; padding: 8px; color: black">
            <strong>Total</strong>
        </td>
        <td style="border: 1px solid black; padding: 8px; text-align: right; color: black;" class="text-right"> {{ number_format($total, 2, '.', ',') }}</td>
    </tr>

    @if ($bln_discount == 1)
    <tr style="border: 1px solid black; color: red">
        <td colspan="3" style="border: 1px solid black; padding: 8px;">
            Discount
        </td>
        <td style="border: 1px solid black; padding: 8px; text-align: right;" class="text-right" id="span_discount_amt_quo">{{ number_format($discount, 2, '.', ',') }}</td>
    </tr>

    <tr style="border: 1px solid black; background-color:#d8dbe0 !important">
        <td colspan="3" style="border: 1px solid black; padding: 8px; color: black">
            Final Total
        </td>
        <td style="border: 1px solid black; padding: 8px; text-align: right; color: black;" class="text-right"><span id="final_amt_quo">{{ number_format($total- $discount, 2, '.', ',') }}</span></td>
    </tr>
    @endif


    {{-- <tr class="div_discount">
        <th>Discount </th>
        <th style="text-align:right" class="span_discount_amt" colspan="5">0.00</th>
    </tr>
    <tr class="div_discount" style=";background-color:black;color:white">
        <th>Final Total </th>
        <th style="text-align:right" class="final_amt" colspan="5">0.00</th>
    </tr> --}}

</tbody>
