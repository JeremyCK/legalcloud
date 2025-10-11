@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Account - Main categories</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="/account-cat/create">
                  <i class="cil-plus"> </i>Create new category
                </a>
              </div>

            </div>
            <br />
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Code</th>
                  <th>Category Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($account_cat))
                @foreach($account_cat as $index => $cat)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $cat->code }}</td>
                  <td>{{ $cat->category }} </td>
                  <td class="text-center">
                    @if($cat->status == 1)
                    <span class="badge badge-success">Active</span>
                    @elseif($cat->status == 0)
                    <span class="badge badge-danger">Inactive</span>
                    @endif

                  </td>

                  <td class="text-center">
                    <a href="{{ url('/account-cat/' . $cat->id . '/edit') }}" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

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