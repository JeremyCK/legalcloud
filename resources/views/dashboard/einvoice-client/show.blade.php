@extends('dashboard.base')

@section('content')


<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header"><h4>{{$customer->name}}</h4></div>
            <div class="card-body">
                @if(Session::has('message'))
                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                @endif

                    <table class="table table-striped table-bordered datatable text-center">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Process</th>
                            <th>KPI</th>
                            <th>PIC</th>
                            <th>Duration</th>
                            <th>Checkpoint</th>
                            <!-- <th>Remarks</th> -->
                            <th>System Code</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loanCase as $index => $case)
                            <tr>
                                <td>{{ $case->case_ref_no }} </td>
                                <td class="text-left">{{ $case->property_name }}</td>
                                <td>test </td>
                                <td>

                                </td>
                                <td>
                                    
                                </td>
                                <td>{{ $case->remark }} </td>
                                <td>
                               </td>  
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')


@endsection