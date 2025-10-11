<table id="tbl-case-count" class="table table-responsive-sm table-hover table-bordered mb-0">


    <thead class="thead-light">

        <tr class="text-center">
            <th><b>No</b></th>
            <th><b>Case Ref No</b></th>
            <th><b>Date</b></th>
        </tr>
    </thead>

    <tbody>
        @if (count($caseCount))
            @foreach ($caseCount as $index => $record)
                <tr>
                    <td class="text-left">{{ $index+1}}</td>
                    <td class="text-left">{{ $record->case_ref_no }}</td>
                    <td class="text-left">{{ $record->created_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="11">No data</td>
            </tr>
        @endif
    </tbody>
</table>
