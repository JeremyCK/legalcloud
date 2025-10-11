@extends('dashboard.base')
@section('content')
<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h4>Checklist Step</h4>
          </div>
          <div class="card-body">

            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('checklist-item.create') }}">
                  <i class="cil-plus"> </i>Create new Steps
                </a>
              </div>
            </div>
            <br>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($templates))
                @foreach($templates as $index => $template)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $template->name }}</td>
                  <td>
                    @if($template->status == 1)
                    <span class="badge badge-success">Active</span>
                    @elseif($template->status == 0)
                    <span class="badge badge-danger">Inactive</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('checklist-item.show', $template->id ) }}" class="btn btn-primary"><i class="cil-pencil"></i></a>
                  </td>
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