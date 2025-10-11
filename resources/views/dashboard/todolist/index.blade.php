@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header"><h4>Todo-List</h4></div>
            <div class="card-body">
                @if(Session::has('message'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert"><strong>{{ Session::get('message') }}
                    <button class="close" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                  </div>
                @endif
                @if($allowCreateCase == "true")
                <div class="row mb-3 ml-3">
                    <a class="btn btn-lg btn-primary" href="{{ route('todolist.create') }}">{{ __('coreuiforms.case.add_new_case') }}</a>
                </div>
                @endif
                <br>
                <table class="table table-striped table-bordered datatable">
                    <thead>
                        <tr>
                            <th>No </th>
                            <th>Case Number</th>
                            <th>Process Type</th>
                            <th>Current Stage</th>
                            <th>Open File Date</th>
                            <th>Target Date</th>
                            <th>Target Closed Date</th>
                            <th>Percentage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $index => $case)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $case->case_ref_no }}
                                  @if($case->status == 2)
                                  <span class="badge badge-warning">New</span>
                                  @endif
                                </td>
                                <!-- <td>{{ $case->case_ref_no }} </td> -->
                                <td>  </td>
                                <td>{{date('d-m-Y', strtotime($case->created_at)) }}</td>
                                <td>{{date('d-m-Y', strtotime($case->created_at)) }}</td>
                                <td>{{date('d-m-Y', strtotime($case->created_at)) }}</td>
                                <td>{{date('d-m-Y', strtotime($case->target_close_date)) }}</td>
                                <td> {{ $case->percentage }}%</td>
                                <td>
                                    <a href="{{ route('todolist.show', $case->id ) }}" class="btn btn-primary">View</a>
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