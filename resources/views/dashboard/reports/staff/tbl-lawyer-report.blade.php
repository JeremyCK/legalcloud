<table id="tbl-case-count" class="table table-responsive-sm table-hover table-bordered mb-0">


    <thead class="thead-light">
      <tr class="text-left" rowspan="2">
        <th colspan="7"><b>Lawyer</b></th>
    </tr>

        <tr class="text-center">
            <th><b>No</b></th>
            <th><b>Staff</b></th>
            <th><b>Cases</b></th>
        </tr>
    </thead>

    <tbody>
        @if (count($lawyer_cases))
            @foreach ($lawyer_cases as $index => $record)
                <tr>
                    <td class="text-left">{{ $index+1}}</td>
                    <td class="text-left">{{ $record->name }}</td>
                    <td class="text-left">{{ $record->total }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="11">No data</td>
            </tr>
        @endif
    </tbody>
</table>
