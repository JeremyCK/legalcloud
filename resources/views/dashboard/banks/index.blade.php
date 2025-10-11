@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Bank Setting</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('banks.create') }}">
                  <i class="cil-plus"> </i>Create new Bank
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
                @if(count($banks))
                @foreach($banks as $index => $bank)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $bank->name }}</td>
                  <td>{{ $bank->short_code }} </td>
                  <td class="text-center">
                    @if($bank->status == 1)
                    <span class="badge-pill badge-success">Active</span>
                    @elseif($bank->status == 0)
                    <span class="badge-pill badge-warning">Inactive</span>
                    @endif
                  </td>
                  <td class="text-center"><a href="{{ route('banks.edit', $bank->id ) }}" class="btn btn-primary shadow  mr-1"><i class="cil-pencil"></i></a>
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