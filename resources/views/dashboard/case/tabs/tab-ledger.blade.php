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
                        <h3 class="box-title">Ledger</h3>
                    </div>
                    <div class="col-6">
                        <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)"
                            onclick="exportTableToExcel();">
                            <i class="fa fa-file-excel-o"> </i>Download as Excel
                        </a>
                    </div>

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="width:100%;overflow-x:auto">

                <div class="box-body no-padding hide" style="width:100%;overflow-x:auto">

                    <table class="table table-striped table-bordered  yajra-datatable" id="tbl-ledger-yadra"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Transaction ID</th>
                                <th>Bank Account</th>
                                <th>Voucher No</th>
                                <th>Payee</th>
                                <th>Remark</th>
                                <th>Debit (RM)</th>
                                <th>Credit (RM)</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
                @php
                    $total_debit = 0;
                    $total_credit = 0;
                    $sub_debit = 0;
                    $sub_credit = 0;
                    $total = 0;
                @endphp

                <div style="height: 500px; overflow: auto">
                    <table id="tbl-ledger-data" class="table  table-bordered datatable">
                        <thead>
                            <tr >
                                <td ><b>Ref No:</b> </td>
                                <td colspan="9">{{ $case->case_ref_no }}</td>
                            </tr>
                            <tr >
                                <td ><b>Client</b> </td>
                                <td colspan="9">{{ $customer->name }}</td>
                            </tr>
                        </thead>
                        <thead style="background-color: #d8dbe0">
                            <tr style="background-color: #d8dbe0;position:sticky">
                                <th width="90px">Payment Date</th>
                                <th>Transaction ID</th>
                                <th>Voucher No</th>
                                <th>Payee</th>
                                <th>Remarks</th>
                                <th>Type</th>
                                <th>Bank</th>
                                <th>In/Credit (RM)</th>
                                <th>Out/Debit (RM)</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($ledgers))

                                @foreach ($ledgers as $index => $ledger)
                                    @php
                                        $sub_total = 0;
                                        $sub_debit = 0;
                                        $sub_credit = 0;
                                    @endphp
                                    <tr id="ledger_id_{{ $ledger->id }}" @if(in_array($ledger->type, ['CLOSEFILE_IN','CLOSEFILE_OUT','ABORTFILE_IN','ABORTFILE_OUT'])) style="background-color: black;color:white" @endif>
                                        <td>{{ date('d-m-Y', strtotime($ledger->date)) }}</td>
                                        <td>{{ $ledger->transaction_id }}</td>
                                        <td> 
                                            @if(in_array($ledger->type, ['TRANSFER_IN','TRANSFER_OUT','SST_IN','SST_OUT','REIMB_IN','REIMB_OUT','REIMB_SST_IN','REIMB_SST_OUT']))
                                            -
                                            @elseif(in_array($ledger->type, ['JOURNAL_IN','JOURNAL_OUT']))
                                            <a target="_blank" class="text-info" href="/journal-entry/{{ $ledger->key_id }}">{{ $ledger->cheque_no }}</a>
                                            @else
                                                @if ($ledger->transaction_type == 'D')
                                                {{ $ledger->voucher_no }}
                                                @else
                                                <a target="_blank" class="text-info" href="/voucher/{{ $ledger->key_id }}/edit">{{ $ledger->voucher_no }}</a>
                                                @endif
                                            
                                            @endif
                                            
                                        </td>
                                        <td>
                                            @if(in_array($ledger->type, ['TRANSFER_IN','TRANSFER_OUT','SST_IN','SST_OUT','REIMB_IN','REIMB_OUT','REIMB_SST_IN','REIMB_SST_OUT']))
                                            -
                                            @elseif(in_array($ledger->type, ['JOURNAL_IN','JOURNAL_OUT']))
                                            {{ $ledger->payee }}
                                            @else
                                            {{ $ledger->payee_voucher }}
                                            @endif
                                            
                                        </td>
                                        <td>
                                            @if(in_array($ledger->type, ['TRANSFER_IN','TRANSFER_OUT','SST_IN','SST_OUT','REIMB_IN','REIMB_OUT','REIMB_SST_IN','REIMB_SST_OUT']))
                                            {{ $ledger->remark }}
                                            @elseif(in_array($ledger->type, ['JOURNAL_IN','JOURNAL_OUT']))
                                            {{-- {{ $ledger->desc_1 }}<br/> --}}
                                            {{ $ledger->remark }}
                                            @else
                                            {!! $ledger->remark !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array($ledger->type, ['BILL_DISB','BILL_RECV']))
                                                Bill
                                            @elseif(in_array($ledger->type, ['TRUST_DISB','TRUST_RECV']))
                                                Trust
                                            @elseif(in_array($ledger->type, ['JOURNAL_IN','JOURNAL_OUT']))
                                                Journal
                                            @elseif(in_array($ledger->type, ['TRANSFER_IN','TRANSFER_OUT']))
                                                Transfer
                                            @elseif(in_array($ledger->type, ['SST_IN','SST_OUT']))
                                                SST
                                            @elseif(in_array($ledger->type, ['REIMB_IN','REIMB_OUT']))
                                                Reimbursement
                                            @elseif(in_array($ledger->type, ['REIMB_SST_IN','REIMB_SST_OUT']))
                                                Reimbursement SST
                                            @elseif(in_array($ledger->type, ['CLOSEFILE_IN','CLOSEFILE_OUT']))
                                                Closed file
                                            @elseif(in_array($ledger->type, ['ABORTFILE_IN','ABORTFILE_OUT']))
                                                Aborted file
                                            @endif

                                            {{-- @if (in_array($ledger->type, ['BILLDISB','BILLRECEIVE']))
                                                Bill
                                            @elseif(in_array($ledger->type, ['TRUSTDISB','TRUSTRECEIVE']))
                                                Trust
                                            @elseif(in_array($ledger->type, ['JOURNALIN','JOURNALOUT']))
                                                Journal
                                            @elseif(in_array($ledger->type, ['TRANSFERIN','TRANSFEROUT']))
                                                Transfer
                                            @elseif(in_array($ledger->type, ['SSTIN','SSTOUT']))
                                                SST
                                            @elseif(in_array($ledger->type, ['CLOSEFILEIN','CLOSEFILEOUT']))
                                                Closed file
                                            @endif --}}
                                        </td>
                                        <td>
                                            @if($ledger->bank_name == '')
                                            -
                                            @else
                                            {{ $ledger->bank_name }} ({{ $ledger->bank_account_no }})
                                            @endif
                                             </td>
                                        <td class="text-right">
                                            {{-- @if ($ledger->transaction_type == 'D' || in_array($ledger->type, ['REIMB_OUT','REIMB_SST_OUT'])) --}}
                                            @if ($ledger->transaction_type == 'D')
                                                {{ number_format((float) $ledger->amount, 2, '.', ',') }}
                                                @php
                                                    $total_debit += $ledger->amount;
                                                    $sub_debit += $ledger->amount;
                                                    $sub_total += $ledger->amount;
                                                    $total -= $ledger->amount;
                                                @endphp
                                            @else
                                                - 
                                            @endif
                                            {{-- @if ($ledger->transaction_type == 3 || $ledger->transaction_type == 4)
                                                {{ number_format((float) $ledger->amount, 2, '.', ',') }}
                                                @php
                                                    $total_debit += $ledger->amount;
                                                    $sub_debit += $ledger->amount;
                                                    $sub_total += $ledger->amount;
                                                    $total -= $ledger->amount;
                                                @endphp
                                            @else   
                                                - 
                                            @endif --}}
                                        </td>
                                        <td class="text-right">
                                            {{-- @if ($ledger->transaction_type == 'C' || in_array($ledger->type, ['REIMB_IN','REIMB_SST_IN'])) --}}
                                            @if ($ledger->transaction_type == 'C' )
                                            {{ number_format((float) $ledger->amount, 2, '.', ',') }}
                                            @php
                                                $total_credit += $ledger->amount;
                                                $sub_credit += $ledger->amount;
                                                $sub_total = $ledger->amount;
                                                $total += $ledger->amount;
                                            @endphp
                                        @else
                                            -
                                        @endif
                                            {{-- @if (($ledger->transaction_type == '1' && $ledger->status != '4') || $ledger->transaction_type == '2')
                                                {{ number_format((float) $ledger->amount, 2, '.', ',') }}
                                                @php
                                                    $total_credit += $ledger->amount;
                                                    $sub_credit += $ledger->amount;
                                                    $sub_total = $ledger->amount;
                                                    $total += $ledger->amount;
                                                @endphp
                                            @else
                                                -
                                            @endif --}}
                                        </td>
                                        <td class="text-right">
                                            @php
                                                // $total_credit += $ledger->amount;
                                                // $sub_crebit += $ledger->amount;
                                                $sub_total = $sub_credit - $sub_debit;
                                            @endphp
                                            @if ($total >= 0)
                                                {{ number_format((float) $total, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($total * -1), 2, '.', ',') }})
                                            @endif
                                        </td>

                                    </tr>
                                    {{-- @if(in_array($ledger->type, ['CLOSEFILE_IN','CLOSEFILE_OUT']))
                                    <tr style="background-color: black">
                                        <td colspan="10"></td>
                                    </tr>
                                    @endif --}}
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="8">No data</td>
                                </tr>
                            @endif

                            {{-- @if (count($transfer_fee))
                                @foreach ($transfer_fee as $index2 => $row)
                                    @php
                                        $total += $row->pfee1_inv + $row->pfee2_inv;
                                        $tempTotal = $total;
                                        // $calTotal = '('.$total .')';
                                         $total_credit += $row->pfee1_inv + $row->pfee2_inv;
                                         $positiveVal = 1;

                                         if ($total < 0)
                                         {
                                            $tempTotal = $tempTotal * -1;
                                            $positiveVal = 0;
                                         }

                                         $calTotal = number_format((float) ($tempTotal), 2, '.', ',');

                                         if ($positiveVal == 0)
                                         {
                                            $calTotal = '('.$calTotal .')';
                                         }

                                    @endphp
                                    <tr>
                                        <td>{{ $row->transfer_date }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>{{ $row->purpose }}</td>
                                        <td>Transfer Fee</td>
                                        <td>{{ $row->bank_name }} ({{ $ledger->bank_account_no }})</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">
                                            {{ number_format((float) ($row->pfee1_inv + $row->pfee2_inv), 2, '.', ',') }} </td>
                                        <td class="text-right">
                                            {{  $calTotal }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            @if (count($transfer_fee))
                                @foreach ($transfer_fee as $index2 => $row)
                                    @php
                                        $total += $row->sst_inv;
                                        $tempTotal = $total;
                                        // $calTotal = '('.$total .')';
                                         $total_credit += $row->sst_inv;
                                         $positiveVal = 1;

                                         if ($total < 0)
                                         {
                                            $tempTotal = $tempTotal * -1;
                                            $positiveVal = 0;
                                         }

                                         $calTotal = number_format((float) ($tempTotal), 2, '.', ',');

                                         if ($positiveVal == 0)
                                         {
                                            $calTotal = '('.$calTotal .')';
                                         }

                                    @endphp
                                    <tr>
                                        <td>{{ $row->transfer_date }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>{{ $row->purpose }}</td>
                                        <td>SST</td>
                                        <td>

                                            @if($row->bank_name == '')
                                            -
                                            @else
                                            {{ $row->bank_name }} ({{ $ledger->bank_account_no }})
                                            @endif
                                            
                                            
                                        </td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">
                                            {{ number_format((float) ($row->sst_inv), 2, '.', ',') }} </td>
                                        <td class="text-right">
                                            {{  $calTotal }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif --}}

                        </tbody>
                        <tfoot>
                            <tr style="background-color:#ced2d8">
                                <td colspan="7">Grand Total</td>
                                <td class="text-right">{{ number_format((float) $total_debit, 2, '.', ',') }}</td>
                                <td class="text-right">{{ number_format((float) $total_credit, 2, '.', ',') }}</td>
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
