@if(count($cases))
@foreach($cases as $case)
<tr>
  <td>{{ $case->checklist_name }}</td>
  <td>{{ $case->checklist_name }}</td>
  <td class="text-center">{{ $case->checklist_name }}</td>
  <td class="text-center">{{ $case->checklist_name }}</td>
  <td class="text-center">
    <a href="{{ url('/case/' . $case->id) }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-airplay"></i></a>
    <a href="{{ url('/case/' . $case->id . '/edit') }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

  </td>
</tr>
@endforeach
@else
<tr>
  <td class="text-center" colspan="7">No data</td>
</tr>
@endif
{!! $users->links() !!}