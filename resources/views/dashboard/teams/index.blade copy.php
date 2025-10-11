@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Team Setting</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('teams.create') }}">
                  <i class="cil-plus"> </i>Create new Team
                </a>
              </div>

            </div>
            <br>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Name</th>
                  <th>Members</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($teams))
                @foreach($teams as $index => $team)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $team->name }}</td>
                  <td class="text-center">{{ $team->member_count }} </td>
                  <td class="text-center">
                    @if($team->status == 1)
                    <span class="badge-pill small badge-success">Active</span>
                    @elseif($team->status == 0)
                    <span class="badge-pill small badge-warning">Inactive</span>
                    @endif
                  </td>
                  <td class="text-center"><a href="{{ route('teams.edit', $team->id ) }}" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary shado sharp mr-1"><i class="cil-pencil"></i></a></td>
                </tr>
                @endforeach
                @else
                <tr>
                  <td class="text-center" colspan="5">No data</td>
                </tr>
                @endif

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