@if(count($loanCase))
@foreach($loanCase as $index => $case)
<tr>
  <td>{{ $case->case_ref_no }}</td>
  <td> {{ $case->lawyer_name }}</td>
  <td> {{ $case->clerk_name }}</td>
  <td> 
  <a href="javascript:void(0)" onclick="createCaseWithSameTeam('{{ $case->clerk_id }}','{{ $case->lawyer_id }}','{{ $case->customer_id }}')" class="btn btn-info">Select this team</a>
  </td>
</tr>
@endforeach
@else
<td class="text-center" colspan="8">No data</td>
@endif