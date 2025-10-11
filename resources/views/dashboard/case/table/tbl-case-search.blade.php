@if (count($search_case) > 0 || count($search_case_oos) > 0)
    @foreach ($search_case as $index => $row)
        <tr>
            <td>
              <a target="_blank" href="/case/{{ $row->id }}">{{ $row->case_ref_no }}</a> 
            </td>
            <td>
              <b>Client: </b> {{ $row->client_name }} <br/>
              <b>Referral: </b> {{ $row->referral_name }} <br/>
              <b>Sales: </b> {{ $row->sales_name }} <br/>
              <b>Lawyer: </b> {{ $row->lawyer_name }} <br/>
              <b>Clerk: </b> {{ $row->clerk_name }} <br/>
              
            </td>
            <td class="text-center">
              @if($row->status == 2)  
                <span class="label bg-info">Open</span>
              @elseif($row->status == 1)  
              <span class="label bg-purple">Running</span>
              @elseif($row->status == 0)  
              <span class="label bg-success">Closed</span>
              @elseif($row->status == 3)  
              <span class="label bg-warning">KIV</span>
              @elseif($row->status == 99)  
              <span class="label bg-danger">Aborted</span>
              @elseif($row->status == 4)  
                  <span class="label" style="background-color:orange">Pending Close</span>
              @elseif($row->status == 7)  
                  <span class="label" style="background-color:Purple">Reviewing</span>
              @else
              <span class="label bg-danger">Overdue</span>
              @endif
            </td>
            <td>{{ $row->branch_name }}</td>
        </tr>
    @endforeach

    @foreach ($search_case_oos as $index => $row)
    <tr>
        <td>
          <a href="javascript:void(0)">{{ $row->ref_no }}</a> 
        </td>
        <td>
          <b>Prev PIC: </b> {{ $row->old_pic_name }} <br/>
          <b>Current PIC: </b> {{ $row->old_pic_name }} <br/>
          <b>Lawyer: </b> {{ $row->lawyer_name }} <br/>
          
        </td>
        <td class="text-center">
          @if($row->status == 1)  
            <span class="label bg-info">Open</span>
          @elseif($row->status == 2)  
          <span class="label bg-success">Closed</span>
          @endif
        </td>
        <td>-</td>
    </tr>
@endforeach
@else
    <td class="text-center" colspan="8">No result</td>
@endif
