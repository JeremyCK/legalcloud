
            {!! $cases->links() !!}
@if(count($cases))
              @foreach($cases as $index => $case)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $case->case_ref_no }}
                 
                    @if($case->status == 2)
                    <span class="badge badge-warning">New</span>
                    @endif
                  </td>
                  <td>{{ $case->client_name }}</td>
                  <!-- <td>{{ $case->case_ref_no }} </td> -->
                  <!-- <td> {{ $case->type_name }} </td> -->
                  <!-- <td>-</td> -->
                  <td>{{date('d-m-Y', strtotime($case->created_at)) }}</td>
                  <td>

                    @if ($case->case_accept_date == null)
                    -
                    @else
                    {{date('d-m-Y', strtotime($case->case_accept_date)) }}
                    @endif
                  </td>
                  <!-- <td>{{date('d-m-Y', strtotime($case->created_at)) }}</td> -->
                  <td>
                    @if ($case->target_close_date == null)
                    -
                    @else
                    {{date('d-m-Y', strtotime($case->target_close_date)) }}
                    @endif

                  </td>
                  <td>
                    @if($case->status == 2)
                    <span class="badge badge-info">Open</span>
                    @elseif($case->status == 0)
                    <span class="badge badge-success">Closed</span>
                    @elseif($case->status == 1)
                    <span class="badge bg-purple">In progress</span>
                    @elseif($case->status == 2)
                    <span class="badge badge-danger">Overdue</span>
                    @elseif($case->status == 3)
                    <span class="badge badge-warning">KIV</span>
                    @endif
                  </td>
                  <td> {{ $case->percentage }}%</td>
                  <td>
                    <a href="{{ route('case.show', $case->id ) }}" class="btn btn-primary"><i class="cil-pencil"></i></a>
                  </td>
                </tr>
                @endforeach
              @else
              <td class="text-center" colspan="8">No data</td>
              @endif