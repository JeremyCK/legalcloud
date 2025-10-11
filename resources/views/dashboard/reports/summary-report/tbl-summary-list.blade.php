@if(count($sumaries))
@foreach($sumaries as $index => $record)
<tr>
  <td class="text-left">{{ date('d-m-Y', strtotime($record->created_at)); }}</td>
</tr>

@endforeach
@else
<tr>
  <td class="text-center" colspan="11">No data</td>
</tr>
@endif