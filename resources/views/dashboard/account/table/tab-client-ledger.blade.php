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
                {{-- <div class="row no-print">
                    <div class="col-6">
                        <h3 class="box-title">Ledger - <span id="selected_bank"></span></h3>
                    </div>

                </div> --}}
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
                                <th>Client Account Group</th>
                                <th>Ref No</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (count($rows))
                                @foreach ($rows as $index2 => $row)
                                    <tr>
                                        <td>004-1-{{ $row->client_ledger_account_code }}: {{ $row->case_ref_no }}</td>
                                        <td><a target="_blank" href="/case/{{ $row->id }}" >{{ $row->case_ref_no }}</a> </td>
                                        <td>
                                            @if ($row->status == 2)
                                                <span class="badge badge-info">Open</span>
                                            @elseif($row->status == 0)
                                                <span class="badge badge-success">Closed</span>
                                            @elseif($row->status == 1)
                                                <span class="badge bg-purple">In progress</span>
                                            @elseif($row->status == 2)
                                                <span class="badge badge-danger">Overdue</span>
                                            @elseif($row->status == 3)
                                                <span class="badge badge-warning">KIV</span>
                                            @elseif($row->status == 4)
                                                <span class="badge badge-warning">Pending Close</span>
                                            @elseif($row->status == 7)
                                                <span class="badge badge-success">Reviewing</span>
                                            @elseif($row->status == 99)
                                                <span class="badge badge-danger">Aborted</span>
                                            @endif    
                                        </td>
                                        {{-- <td class="text-right">

                                            @if ($row->client_ledger_amount >= 0)
                                                {{ number_format((float) $row->client_ledger_amount, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($row->client_ledger_amount * -1), 2, '.', ',') }})
                                            @endif
                                        </td> --}}
                                        <td class="text-right">

                                            @if ($row->amount_ledger >= 0)
                                                {{ number_format((float) $row->amount_ledger, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($row->amount_ledger * -1), 2, '.', ',') }})
                                            @endif
                                        </td>
                                    </tr>

                                    @php
                                        // $total += $row->client_ledger_amount;
                                        $total += $row->amount_ledger;
                                    @endphp
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="8">No records</td>
                                </tr>
                            @endif



                        </tbody>
                        <tfoot style="background-color: #d8dbe0">
                            <tr  style="background-color: #d8dbe0;position:sticky">
                                <td class="text-left">Grand Total</td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                {{-- <td class="text-right">
                                    @if ($total >= 0)
                                        {{ number_format((float) $total, 2, '.', ',') }}
                                    @else
                                        ({{ number_format((float) ($total * -1), 2, '.', ',') }})
                                    @endif
                                </td> --}}
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
