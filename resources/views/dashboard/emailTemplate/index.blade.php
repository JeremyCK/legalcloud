@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Email Template</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('email-template.create') }}">
                  <i class="cil-plus"> </i>Create new Template
                </a>
              </div>

            </div>
            <br>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Name</th>
                  <th>Code</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($templates))
                @foreach($templates as $index => $template)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $template->name }}</td>
                  <td>{{ $template->code }} </td>
                  <td class="text-center">
                    @if($template->status == 1)
                    <span class="badge badge-success">Active</span>
                    @elseif($template->status == 0)
                    <span class="badge badge-warning">Draft</span>
                    @endif
                  </td>
                  <td class="text-center"><a href="{{ route('email-template.edit', $template->id ) }}" class="btn btn-primary shadow  sharp mr-1"><i class="cil-pencil"></i></a></td>
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