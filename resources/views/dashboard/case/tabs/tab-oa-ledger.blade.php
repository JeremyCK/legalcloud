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
                        <h3 class="box-title">OA Ledger</h3>
                    </div>
                    <div class="col-6">
                        <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)"
                            onclick="exportOATableToExcel();">
                            <i class="fa fa-file-excel-o"> </i>Download as Excel
                        </a>
                    </div>

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="width:100%;overflow-x:auto">

                @php
                    $total_debit = 0;
                    $total_credit = 0;
                    $sub_debit = 0;
                    $sub_credit = 0;
                    $total = 0;
                @endphp

                <div style="height: 500px; overflow: auto">
                    <table id="tbl-oa-ledger-data" class="table  table-bordered datatable">
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
                            @if (isset($oa_ledgers) && count($oa_ledgers))

                                @foreach ($oa_ledgers as $index => $ledger)
                                    @php
                                        $sub_total = 0;
                                        $sub_debit = 0;
                                        $sub_credit = 0;
                                    @endphp
                                    <tr id="oa_ledger_id_{{ $ledger->id }}" @if(in_array($ledger->type, ['CLOSEFILE_IN','CLOSEFILE_OUT','ABORTFILE_IN','ABORTFILE_OUT'])) style="background-color: black;color:white" @endif>
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
                                        </td>
                                        <td>
                                            @if($ledger->bank_name == '')
                                            -
                                            @else
                                            {{ $ledger->bank_name }} ({{ $ledger->bank_account_no }})
                                            @endif
                                             </td>
                                        <td class="text-right">
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
                                        </td>
                                        <td class="text-right">
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
                                        </td>
                                        <td class="text-right">
                                            @php
                                                $sub_total = $sub_credit - $sub_debit;
                                            @endphp
                                            @if ($total >= 0)
                                                {{ number_format((float) $total, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($total * -1), 2, '.', ',') }})
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="10">No office account ledger data</td>
                                </tr>
                            @endif

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

<script>
function exportOATableToExcel() {
    var tab_text = "<table border='2px'>";
    var textRange;
    var j = 0;
    tab = document.getElementById('tbl-oa-ledger-data'); // id of table

    for (j = 0; j < tab.rows.length; j++) {
        if (j == 2) {
            tab_text = "<tr style='background-color:black;color:white'>" + tab_text + tab.rows[j].innerHTML +
                "</tr>";
        } else {
            tab_text = "<tr >" + tab_text + tab.rows[j].innerHTML + "</tr>";
        }
    }

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
    {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();
        sa = txtArea1.document.execCommand("SaveAs", true, "OA_Ledger_{{ $case->case_ref_no }}.xls");
    } else //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}
</script>

