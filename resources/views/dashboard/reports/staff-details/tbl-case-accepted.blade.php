<div class="mb-2 text-right">
    <button class="btn btn-success btn-sm" id="btn-export-accepted-excel">
        <i class="fa fa-file-excel-o"></i> Export to Excel
    </button>
</div>
<table id="tbl-case-accepted" class="table table-responsive-sm table-hover table-bordered mb-0">
    <thead class="thead-light">
        <tr class="text-center">
            <th><b>No.</b></th>
            <th><b>Action</b></th>
            <th><b>Ref No</b></th>
            <th><b>pfee1</b></th>
            <th><b>pfee2</b></th>
            <th><b>Disb</b></th>
            <th><b>sst</b></th>
            <th><b>Collected Amount</b></th>
            <th><b>Paid</b></th>
            <th><b>Payment Date</b></th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_pfee1 = 0;
            $total_pfee2 = 0;
            $total_disb = 0;
            $total_sst = 0;
            $total_collected = 0;
        @endphp
        @if (count($caseCount))
            @foreach ($caseCount as $index => $record)
                @php
                    $total_pfee1 += $record->pfee1 ?? 0;
                    $total_pfee2 += $record->pfee2 ?? 0;
                    $total_disb += $record->disb ?? 0;
                    $total_sst += $record->sst ?? 0;
                    $total_collected += $record->collected_amount ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <a href="{{ url('/case/' . $record->id . '/edit') }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="cil-pencil"></i>
                        </a>
                    </td>
                    <td class="text-left">{{ $record->case_ref_no }}</td>
                    <td class="text-right">{{ number_format($record->pfee1 ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->pfee2 ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->disb ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->sst ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->collected_amount ?? 0, 2) }}</td>
                    <td class="text-center">
                        @if(isset($record->paid_status) && $record->paid_status == 'Paid')
                            <span class="badge badge-success">Paid</span>
                        @else
                            <span class="badge badge-secondary">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(isset($record->payment_date) && $record->payment_date)
                            {{ date('Y-m-d', strtotime($record->payment_date)) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="10">No data</td>
            </tr>
        @endif
    </tbody>
    <tfoot class="thead-light">
        <tr class="text-center font-weight-bold">
            <td colspan="3" class="text-right"><b>Total:</b></td>
            <td class="text-right"><b>{{ number_format($total_pfee1, 2) }}</b></td>
            <td class="text-right"><b>{{ number_format($total_pfee2, 2) }}</b></td>
            <td class="text-right"><b>{{ number_format($total_disb, 2) }}</b></td>
            <td class="text-right"><b>{{ number_format($total_sst, 2) }}</b></td>
            <td class="text-right"><b>{{ number_format($total_collected, 2) }}</b></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
