<table id="tbl-bank-count" class="table table-responsive-sm table-hover table-bordered mb-0">
    <thead class="thead-light">
        <tr class="text-left">
            <th colspan="8"><b>Total case count: {{ count($LoanCase) }}</b></th>
        </tr>
        <tr class="text-center">
            <th width="10%"><b>Create Date</b></th>
            <th width="15%"><b>Ref No</b></th>
            <th width="15%"><b>Bank</b></th>
            <th width="20%"><b>Property Address</b></th>
            <th width="15%"><b>Customer Name</b></th>
            <th width="10%"><b>Purchase Price</b></th>
            <th width="10%"><b>Loan Sum</b></th>
            <th width="5%"><b>Status</b></th>
        </tr>
    </thead>
    <tbody>
        @if (count($LoanCase))
            @foreach ($LoanCase as $index => $record)
                <tr>
                    <td class="text-left">{{ date('d-m-Y', strtotime($record->created_at)) }}</td>
                    <td class="text-left">
                        <a target="_blank" href="/case/{{ $record->id }}">{{ $record->case_ref_no }}</a>
                    </td>
                    <td class="text-left">{{ $record->portfolio_name ?? 'N/A' }}</td>
                    <td class="text-left">{{ $record->property_address ?? '-' }}</td>
                    <td class="text-left">{{ $record->customer_name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($record->purchase_price ?? 0, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->loan_sum ?? 0, 2, '.', ',') }}</td>
                    <td class="text-center">
                        @if($record->status == 0)
                            <span class="badge badge-success">Closed</span>
                        @elseif($record->status == 1)
                            <span class="badge bg-purple">In progress</span>
                        @elseif($record->status == 2)
                            <span class="badge badge-info">Open</span>
                        @elseif($record->status == 3)
                            <span class="badge badge-warning">KIV</span>
                        @elseif($record->status == 4)
                            <span class="badge badge-warning">Pending Close</span>
                        @elseif($record->status == 7)
                            <span class="badge bg-purple">Reviewing</span>
                        @elseif($record->status == 99)
                            <span class="badge badge-danger">Aborted</span>
                        @else
                            <span class="badge badge-secondary">Unknown ({{ $record->status }})</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">No data found</td>
            </tr>
        @endif
    </tbody>
</table>

