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
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding" style="width:100%;overflow-x:auto">

                @php
                    $total = 0;
                @endphp

                <div style="height: 700px; overflow: auto" class="printableArea">
                    <table id="tbl-ledger-data" class="table  table-bordered datatable">
                        <thead style="background-color: #d8dbe0">
                            <tr style="background-color: #d8dbe0;position:sticky">
                                <th>Office Account Group</th>
                                <th>Account Name</th>
                                <th>Account No</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (count($rows))
                                @foreach ($rows as $index2 => $row)
                                    <tr>
                                        <td>
                                            @if ($row->account_code_name)
                                                {{ $row->account_code_name }}: {{ $row->name }}
                                            @else
                                                {{ $row->name }}
                                            @endif
                                        </td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->account_no }}</td>
                                        <td class="text-right">
                                            @if ($row->amount_ledger >= 0)
                                                {{ number_format((float) $row->amount_ledger, 2, '.', ',') }}
                                            @else
                                                ({{ number_format((float) ($row->amount_ledger * -1), 2, '.', ',') }})
                                            @endif
                                        </td>
                                    </tr>

                                    @php
                                        $total += $row->amount_ledger;
                                    @endphp
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="4">No records</td>
                                </tr>
                            @endif

                        </tbody>
                        <tfoot style="background-color: #d8dbe0">
                            <tr  style="background-color: #d8dbe0;position:sticky">
                                <td class="text-left">Grand Total</td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
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


