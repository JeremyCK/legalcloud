<table id="tbl-case-count" class="table table-responsive-sm table-hover table-bordered mb-0">


    <thead class="thead-light">
      <tr class="text-left" rowspan="2">
        <th colspan="7"><b>Total case count: {{count($LoanCase)}}</b></th>
    </tr>
    <tr class="text-left" rowspan="2">
        <th colspan="7"><b>Case Type: {{ $case_type}}</b></th>
    </tr>
    <tr class="text-left" rowspan="2">
        <th colspan="7"><b>Branch: {{$Branch_name}}</b></th>
    </tr>
        <tr class="text-center">
            <th  width="10%"><b>Create Date</b></th>
            <th width="20%"><b>Ref No</b></th>
            <th ><b>Case Type</b></th>
            <th width="20%"><b>Property Address</b></th>
            <th><b>Customer Name</b></th>
            <th><b>Purchase Price</b></th>
            <th><b>Loan Sum</b></th>
        </tr>
    </thead>

    <tbody>
        @if (count($LoanCase))
            @foreach ($LoanCase as $index => $record)
                <tr>
                    <td class="text-left">{{ date('d-m-Y', strtotime($record->created_at)) }}</td>
                    <td class="text-left"><a target="_blank"
                            href="/case/{{ $record->id }}">{{ $record->case_ref_no }}</a> </td>
                            <td class="text-left">{{ $record->portfolio_name }}</td>
                    <td class="text-left">{{ $record->property_address }}</td>
                    <td class="text-left">{{ $record->customer_name }}</td>
                    <td class="text-right">{{ number_format($record->purchase_price, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->loan_sum, 2, '.', ',') }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="11">No data</td>
            </tr>
        @endif
    </tbody>
</table>
