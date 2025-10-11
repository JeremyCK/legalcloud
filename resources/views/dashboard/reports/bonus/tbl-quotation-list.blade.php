@if (count($quotation))

    @php
        $total = 0;
        $subtotal = 0;
        $pfee2_total = 0;
    @endphp
    <tr style="background-color:grey;color:white">
        <td colspan="2"></td>
        <td class="text-center">Amount</td>
        <td class="text-center">SST</td>
        <td class="text-center">Amount + SST</td>
    </tr>
    @foreach ($quotation as $index => $cat)
        <tr style="background-color:grey;color:white">
            <td colspan="5">{{ $cat['category']->category }}</td>
        </tr>
        @php
            $subtotal = 0;
        @endphp
        <?php $category_amount = 0; ?>
        @foreach ($cat['account_details'] as $index => $details)
            <tr>
                <td class="text-center" style="width:50px">
                    <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                </td>
                <td id="item_{{ $details->id }}">{{ $details->account_name }}
                    @if ($details->pfee1_item == 1)
                    <span class="badge bg-purple">PFee1</span>
                    @endif
                    
                </td>
                <td class="text-right" id="amt_{{ $details->id }}">
                    {{ number_format($details->quo_amount_no_sst, 2, '.', ',') }}</td>
                <td class="text-right" id="amt_{{ $details->id }}">
                    @if ($cat['category']->taxable == 1)
                        {{ number_format($details->quo_amount_no_sst * 0.06, 2, '.', ',') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right" id="amt_{{ $details->id }}">
                    @if ($cat['category']->taxable == 1)
                        {{ number_format($details->quo_amount_no_sst * 1.06, 2, '.', ',') }}
                    @else
                        {{ number_format($details->quo_amount_no_sst, 2, '.', ',') }}
                    @endif
                </td>

            </tr>
            @php
                if ($cat['category']->taxable == 1) {
                    if ($details->pfee1_item != 1)
                    {
                        $pfee2_total += $details->quo_amount_no_sst;
                    }
                    // $subtotal += $details->quo_amount_no_sst * 1.06;
                } else {
                    // $subtotal += $details->quo_amount_no_sst;
                }
                $subtotal += $details->quo_amount_no_sst;
            @endphp
        @endforeach



        <tr>
            <td colspan="2">
                @if ($cat['category']->taxable == 1)
                <span class="badge bg-info">Total Pfee2</span>: {{ number_format($pfee2_total, 2, '.', ',') }}
                @endif
            </td>
            <td class="text-right" id="amt_{{ $details->id }}">{{ number_format($subtotal, 2, '.', ',') }}</td>
            <td class="text-right" id="amt_{{ $details->id }}">
                @if ($cat['category']->taxable == 1)
                    {{ number_format($subtotal * 0.06, 2, '.', ',') }}
                @else
                    -
                @endif
            </td>
            <td class="text-right" id="amt_{{ $details->id }}">
                @if ($cat['category']->taxable == 1)
                    {{ number_format($subtotal * 1.06, 2, '.', ',') }}
                @else
                    {{ number_format($subtotal, 2, '.', ',') }}
                @endif


            </td>
        </tr>

        @php
            if ($cat['category']->taxable == 1) {
                $total += $subtotal * 1.06;
            } else {
                $total += $subtotal;
            }
            
        @endphp
    @endforeach



    <tr>
        <td>Total </td>
        <td style="text-align:right" colspan="4"> {{ number_format((float) $total, 2, '.', ',') }}</td>

    </tr>
@else
    <tr>
        <td class="text-center" colspan="5">No data</td>
    </tr>
@endif
