@if (count($bank_recon))
    <tr>
        <td width="10%">Date</td>
        <td width="10%">Type</td>
        <td width="10%">Trx ID</td>
        <td width="60%">Desc</td>
        <td width="10%">Amount</td>
    </tr>
    @foreach ($bank_recon as $index => $recon)
        <tr>
            <td class="text-left" style="width:10%">{{ date('d-m-Y', strtotime($recon->date)) }}</td>
            <td class="text-left">
                @if (in_array($recon->type, ['JOURNAL_IN', 'JOURNAL_OUT']))
                    Journal
                @elseif(in_array($recon->type, ['TRUST_RECV', 'TRUST_DISB']))
                    Trust
                @elseif(in_array($recon->type, ['BILL_RECV', 'BILL_DISB']))
                    Bill
                @elseif(in_array($recon->type, ['TRANSFER_IN', 'TRANSFER_OUT']))
                    Transfer
                @elseif(in_array($recon->type, ['SST_IN', 'SST_OUT']))
                    SST
                @elseif(in_array($recon->type, ['REIMB_IN', 'REIMB_OUT']))
                    Reimbursement
                @elseif(in_array($recon->type, ['REIMB_SST_IN', 'REIMB_SST_OUT']))
                    Reimbursement SST
                @elseif(in_array($recon->type, ['CLOSEFILE_IN', 'CLOSEFILE_OUT']))
                    Close File
                @elseif(in_array($recon->type, ['ABORTFILE_IN','ABORTFILE_OUT']))
                    Aborted File
                @else
                @endif
            </td>
            <td class="text-left">{{ $recon->transaction_id }}</td>
            {{-- <td class="text-left">{{$recon->type}}</td> --}}
            <td class="text-left">
                @if ($recon->type == 'BILL_DISB')
                    <b>Bill Acc Disb:</b>
                @elseif($recon->type == 'TRUST_DISB')
                    <b>Trust Acc Disb:</b>
                @elseif($recon->type == 'TRUST_RECV')
                    <b>Trust Acc Payment:</b>
                @elseif($recon->type == 'BILL_DISB')
                @endif
                @if (in_array($recon->type, ['JOURNAL_IN', 'JOURNAL_OUT']))
                    {{ $recon->payee }} - {{ $recon->desc_1 }}<br />
                @endif
                {{ $recon->remark }}

                {{-- @if ($recon->transaction_type == 'C' || $recon->transaction_type == 'D') 
                    <br />
                    @if ($recon->type == 'SST_IN' || $recon->type == 'SST_OUT')
                        SST
                        <br />
                    @endif
                    <b>Ref No:</b><a href="/case/{{ $recon->case_id }}" target="_blank">[{{ $recon->case_ref_no }}]</a>
                    <br />

                    @if (in_array($recon->type, ['JOURNAL_IN', 'JOURNAL_OUT']))
                        <b>Journal No: </b> <a href="/journal-entry/{{ $recon->key_id }}"
                            target="_blank">[{{ $recon->cheque_no }}]</a>
                    @elseif(in_array($recon->type, ['CLOSEFILE_IN', 'CLOSEFILE_OUT']))
                    @else
                        <b>Invoice No: </b> [{{ $recon->invoice_no }}]
                    @endif
                @else
                    <a href="/voucher/{{ $recon->key_id }}/edit" target="_blank">[{{ $recon->cheque_no }}]</a>
                @endif --}}

                @if (in_array($recon->type, ['JOURNAL_IN', 'JOURNAL_OUT']))
                    <br />
                    <b>Journal No: </b> <a href="/journal-entry/{{ $recon->key_id }}"
                        target="_blank">[{{ $recon->cheque_no }}]</a>
                @elseif (in_array($recon->type, ['TRUST_DISB', 'BILL_DISB']))
                <br />
                <b>Voucher No: </b> <a href="/voucher/{{ $recon->key_id }}/edit"
                    target="_blank">[{{ $recon->cheque_no }}]</a>
                @else
                @endif

                {{-- @if ($recon->transaction_type == 'C' || $recon->transaction_type == 'D')
                    <br />
                    @if ($recon->type == 'SST_IN' || $recon->type == 'SST_OUT')
                        SST
                        <br />
                    @endif
                    <b>Ref No:</b><a href="/case/{{ $recon->case_id }}" target="_blank">[{{ $recon->case_ref_no }}]</a>
                    <br />

                    @if (in_array($recon->type, ['JOURNAL_IN', 'JOURNAL_OUT']))
                        <b>Journal No: </b> <a href="/journal-entry/{{ $recon->key_id }}"
                            target="_blank">[{{ $recon->cheque_no }}]</a>
                    @elseif(in_array($recon->type, ['CLOSEFILE_IN', 'CLOSEFILE_OUT']))
                    @else
                        <b>Invoice No: </b> [{{ $recon->invoice_no }}]
                    @endif
                @else
                    <a href="/voucher/{{ $recon->key_id }}/edit" target="_blank">[{{ $recon->cheque_no }}]</a>
                @endif --}}

                @if ($recon->type == 'BILL_DISB')
                    <br />
                    {!! $recon->desc_1 !!}={{ $recon->amount }}
                @endif
            </td>
            <td class="text-right">{{ number_format($recon->amount, 2, '.', ',') }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="text-center" colspan="11">No data</td>
    </tr>
@endif
