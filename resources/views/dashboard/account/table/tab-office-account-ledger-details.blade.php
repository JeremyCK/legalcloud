<style>
    table thead,
    table tfoot {
        position: sticky;
    }

    table thead {
        inset-block-start: 0;
    }

    table tfoot {
        inset-block-end: 0;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <div class="row no-print">
                    <div class="col-6">
                        <h3 class="box-title">Ledger - <span id="selected_bank">{{ $bank_name ?? 'Office Account' }}</span></h3>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="width:100%;overflow-x:auto">

                @php
                    $total_debit = 0;
                    $total_credit = 0;
                    $total = 0;
                @endphp

                <div style="height: 700px; overflow: auto" class="printableArea">
                    <table id="tbl-ledger-data" class="table table-bordered datatable">
                        <thead style="background-color: #d8dbe0">
                            <tr style="background-color: #d8dbe0;position:sticky">
                                <th colspan="7">Bank: <span id="span_bank_name">{{ $bank_name ?? 'Office Account' }}</span></th>
                            </tr>
                            <tr style="background-color: #d8dbe0;position:sticky">
                                <th width="90px">Date</th>
                                <th>Transaction ID</th>
                                <th>Ref No</th>
                                <th>Description</th>
                                <th>In/Credit (RM)</th>
                                <th>Out/Debit (RM)</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (isset($rows) && count($rows))
                                @foreach ($rows as $index2 => $row)
                                    @php
                                        if ($row->transaction_type == 'D') {
                                            $total += $row->amount;
                                            $total_debit += $row->amount;
                                        } else {
                                            $total -= $row->amount;
                                            $total_credit += $row->amount;
                                        }
                                        
                                        $tempTotal = $total;
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
                                        <td>{{ date('d-m-Y', strtotime($row->date)) }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>
                                            @if ($row->case_id)
                                                <a target="_blank" href="{{ url('case/' . $row->case_id) }}">{{ $row->case_ref_no }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($row->type == 'TRUST_RECV')
                                                <b>Trust Acc Payment:</b><br />
                                                <b>Payee: </b>{{ $row->payee_voucher ?? $row->payee }} <br />
                                            @elseif ($row->type == 'TRUST_DISB')
                                                <b>Trust Acc Disb:</b><br />
                                                <b>Voucher No:</b>{{ $row->voucher_no }} <br />
                                                <b>Payee: </b>{{ $row->payee_voucher ?? $row->payee }} <br />
                                            @elseif ($row->type == 'SST_OUT')
                                                <b>Transfer of G/SST to OA:</b><br />
                                            @elseif ($row->type == 'TRANSFER_OUT')
                                                <b>Transfer of Pro Fees to OA:</b><br />
                                            @elseif ($row->type == 'SST_IN' || $row->type == 'SST_IN')
                                                <b>Transfer of G/SST from CA:</b><br />
                                            @elseif ($row->type == 'TRANSFER_IN' || $row->type == 'TRANSFER_IN')
                                                <b>Transfer of Pro Fees from CA:</b><br />
                                            @elseif ($row->type == 'BILL_RECV')
                                                <b>Bill Acc Payment:</b><br />
                                                <b>Payee: </b>{{ $row->payee_voucher ?? $row->payee }} <br />
                                            @elseif ($row->type == 'BILL_DISB')
                                                <b>Bill Acc Disb:</b><br />
                                                <b>Voucher No:</b>{{ $row->voucher_no }} <br />
                                                <b>Payee: </b>{{ $row->payee_voucher ?? $row->payee }} <br />
                                            @elseif ($row->type == 'JOURNAL_IN' || $row->type == 'JOURNAL_OUT')
                                                <b>Journal Entry:</b><br />
                                                <b>Cheque No:</b>{{ $row->cheque_no }} <br />
                                            @endif

                                            {{ $row->remark }} <br />
                                            {!! $row->desc_1 ?? '' !!}
                                        </td>
                                        <td class="text-right">
                                            @if ($row->transaction_type == 'C')
                                                {{ number_format((float) $row->amount, 2, '.', ',') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($row->transaction_type == 'D')
                                                {{ number_format((float) $row->amount, 2, '.', ',') }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                    <td colspan="7">No records</td>
                                </tr>
                            @endif

                        </tbody>
                        <tfoot>
                            <tr style="background-color:#ced2d8">
                                <td colspan="4">Grand Total</td>
                                <td class="text-right">{{ number_format((float) $total_credit, 2, '.', ',') }}</td>
                                <td class="text-right">{{ number_format((float) $total_debit, 2, '.', ',') }}</td>
                                <td class="text-right">
                                    @if ($total >= 0)
                                        {{ number_format((float) $total, 2, '.', ',') }}
                                    @else
                                        ({{ number_format((float) ($total * -1), 2, '.', ',') }})
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


