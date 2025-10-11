@if(count($users))
@foreach($users as $user)
<tr>
  <td>{{ $user->name }}</td>
  <td>{{ $user->email }}</td>
  <td class="text-center">{{ $user->menuroles }}</td>
  <td class="text-center">{{ $user->menuroles }}</td>
  <td class="text-center">{{ $user->nick_name }}</td>
  <td class="text-center">
    @if($user->status == 0)
    <span class="badge badge-danger">Inactive</span>
    @elseif($user->status == 1)
    <span class="badge badge-success">Active</span>
    @endif
  </td>
  <td class="text-center">
    <a href="{{ url('/users/' . $user->id) }}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-airplay"></i></a>
    <a href="{{ url('/users/' . $user->id . '/edit') }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

  </td>
</tr>
@endforeach
@else
<tr>
  <td class="text-center" colspan="7">No data</td>
</tr>
@endif
{!! $users->links() !!}