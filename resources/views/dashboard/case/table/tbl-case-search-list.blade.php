@if(count($cases))
@foreach($cases as $index => $case)
<tr>
  <td>{{ $index + 1 }}</td>
  <td>{{ $case->case_ref_no }}

    @if($case->status == 2)
    <span class="badge badge-warning">New</span>
    @endif
  </td>
  <td>{{ $case->client_name }}</td>>
  <td>{{ $case->client_phone_no }}</td>
  <td>{{ $case->lawyer_name }}</td>
  <td>{{ $case->clerk_name }}</td>
  <td>{{ $case->property_address }}</td>
  <td>
  <a href="{{ route('case.show', $case->id ) }}" class="btn btn-primary"><i class="cil-pencil"></i></a>
  </td>
</tr>
@endforeach
@else
<td class="text-center" colspan="8">No Result</td>
@endif