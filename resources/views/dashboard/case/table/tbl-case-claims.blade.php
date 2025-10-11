<table class="table table-bordered yajra-datatable" id="tbl-marketing-file-yadra" style="width:100%">
    <thead>
        <tr class="text-center">
            <th>No</th>
            <th>Claims</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (count($claims))

            @foreach ($claims as $index => $row)
                <tr id="claims_{{ $row->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->name }}</td>
                    <td class="text-right">{{ number_format($row->amount, 2, '.', ',') }}</td>
                    <td class="text-center">
                        @if ($row->claims_status == 0)
                            <span class=" badge badge-pill badge-danger">Not request</span>
                        @elseif($row->claims_status == 2)
                            <span class=" badge badge-pill badge-warning">Pending</span>
                        @elseif($row->claims_status == 1)
                            <span class=" badge badge-pill badge-success">Approved</span>
                        @else
                            <span class=" badge badge-pill badge-danger">Not request</span>
                        @endif
                    </td>
                    <td>{{ $row->submit_date }}</td>
                    <td>
                        @if (!$row->claims_status)
                            <a href="javascript:void(0)"
                                onclick="submitClaimsRequest({{ $row->percentage }}, '{{ $row->id }}')"
                                class="btn btn-info"><i class="cil-env"></i> Submit Claims</a>
                        @else
                        @endif

                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="5">No file</td>
            </tr>
        @endif
    </tbody>
</table>
