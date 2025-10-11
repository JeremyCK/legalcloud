<style>
    table thead,
    table tfoot {
        position: sticky;
    }

    table thead {
        inset-block-start: 0;
        /* "top" */
    }

    table tfoot {
        inset-block-end: 0;
        /* "bottom" */
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <div class="row no-print">
                    <div class="col-6">
                        <h3 class="box-title">Ledger <span id="selected_bank"></span></h3>
                    </div>

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="width:100%;overflow-x:auto">

                @php
                    $total_debit = 0;
                    $total_credit = 0;
                    $total_pfee1 = 0;
                    $total_pfee2 = 0;
                    $total_sst = 0;
                    $sub_debit = 0;
                    $sub_credit = 0;
                    $total = 0;
                @endphp

                <div style="height: 700px; overflow: auto" class="printableArea">
                    <table id="tbl-ledger-data" class="table  table-bordered datatable">
                        <thead style="background-color: #d8dbe0">
                            <tr style="background-color: #d8dbe0;position:sticky">
                                <th width="90px">Date</th>
                                <th>Transaction ID</th>
                                <th>Invoice No</th>
                                <th>Ref No</th>
                                <th>Remarks</th>
                                <th>Type</th>
                                {{-- <th>Pfee1</th>
                                <th>Pfee2</th>
                                <th>SST</th> --}}
                                <th>In/Credit (RM)</th>
                                <th>Out/Debit (RM)</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- @if (count($case_receive))
                                @foreach ($case_receive as $index1 => $row)
                                    <tr>
                                        <td>{{ $row->payment_date }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>{{ $row->voucher_no }}</td>
                                        <td>{{ $row->case_ref_no }}</td>
                                        <td>{{ $row->remark }}</td>
                                        <td>Trust</td>
                                        <td>
                                            -
                                        </td>
                                        <td>
                                            {{ number_format((float) $row->amount, 2, '.', ',') }}
                                        </td>
                                        <td>-</td>
                                    </tr>
                                @endforeach
                            @endif --}}


                            @if (count($rows))
                                @foreach ($rows as $index2 => $row)
                                    @php
                                        // $total += $row->transfer_amount;
                                        $total += ($row->pfee1_inv + $row->pfee2_inv);
                                        $tempTotal = $total;
                                        // $calTotal = '('.$total .')';
                                        $total_pfee1 += $row->pfee1_inv;
                                        $total_pfee2 += $row->pfee2_inv;
                                        $total_sst += $row->sst_inv;
                                        // $total_credit += $row->transfer_amount;
                                        $total_credit += $row->transfer_amount;
                                        $positiveVal = 1;
                                        
                                        if ($total < 0) {
                                            $tempTotal = $tempTotal * -1;
                                            $positiveVal = 0;
                                        }
                                        
                                        $calTotal = number_format((float) $tempTotal, 2, '.', ',');
                                        
                                        if ($positiveVal == 0) {
                                            $calTotal = '(' . $calTotal . ')';
                                        }
                                        
                                    @endphp
                                    <tr>
                                        <td>{{ $row->transfer_date }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>{{ $row->invoice_no }}</td>
                                        <td> <a target="_blank" href="case/{{ $row->case_id }}">{{ $row->case_ref_no }}</a></td>
                                        <td>{{ $row->purpose }}</td>
                                        <td>Transfer</td>
                                        {{-- <td class="text-right">
                                            {{ number_format((float) $row->pfee1_inv, 2, '.', ',') }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format((float) $row->pfee2_inv, 2, '.', ',') }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format((float) $row->sst_inv, 2, '.', ',') }}
                                        </td> --}}
                                        <td class="text-right">
                                            {{ number_format((float) ($row->pfee1_inv + $row->pfee2_inv), 2, '.', ',') }}
                                        </td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">
                                            @if ($total >= 0)
                                                {{ number_format((float) $total, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($total * -1), 2, '.', ',') }})
                                            @endif
                                        </td>
                                    </tr>

                                    @php
                                    // $total += $row->transfer_amount;
                                    $total += ($row->sst_inv);
                               
                                    
                                @endphp

                                    <tr>
                                        <td>{{ $row->transfer_date }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>{{ $row->invoice_no }}</td>
                                        <td> <a target="_blank" href="case/{{ $row->case_id }}">{{ $row->case_ref_no }}</a></td>
                                        <td>{{ $row->purpose }}</td>
                                        <td>SST</td>
                                        {{-- <td class="text-right">
                                            {{ number_format((float) $row->pfee1_inv, 2, '.', ',') }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format((float) $row->pfee2_inv, 2, '.', ',') }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format((float) $row->sst_inv, 2, '.', ',') }}
                                        </td> --}}
                                        <td class="text-right">
                                            {{ number_format((float) $row->sst_inv, 2, '.', ',') }}
                                        </td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">
                                            @if ($total >= 0)
                                                {{ number_format((float) $total, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($total * -1), 2, '.', ',') }})
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                
                            @else
                                <tr class="text-center">
                                    <td colspan="9">No records</td>
                                </tr>
                            @endif



                        </tbody>
                        <tfoot>
                            <tr style="background-color:#ced2d8">
                                <td colspan="6">Grand Total</td>
                                {{-- <td class="text-right">{{ number_format((float) $total_pfee1, 2, '.', ',') }}</td>
                                <td class="text-right">{{ number_format((float) $total_pfee2, 2, '.', ',') }}</td>
                                <td class="text-right">{{ number_format((float) $total_sst, 2, '.', ',') }}</td> --}}
                                <td class="text-right">{{ number_format((float) $total_credit, 2, '.', ',') }}</td>
                                <td class="text-right">{{ number_format((float) $total_debit, 2, '.', ',') }}</td>
                                <td class="text-right">

                                    @if ($total_credit - $total_debit >= 0)
                                        {{ number_format((float) ($total_credit - $total_debit), 2, '.', ',') }}
                                    @else
                                        ({{ number_format((float) (($total_credit - $total_debit) * -1), 2, '.', ',') }})
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
