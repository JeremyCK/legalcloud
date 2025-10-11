@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Settings | Account Code</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="/account-code/create">
                  <i class="cil-plus"> </i>Create new account code
                </a>
              </div>
            </div>
            <br />
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Desc</th>
                  <th>Group</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($AccountCode))
                @foreach($AccountCode as $index => $item)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $item->code }}</td>
                  <td>{{ $item->name }} </td>
                  <td>{{ $item->desc }} </td>
                  <td class="text-center">
                    @if($item->group == 1)
                    Office Account
                    @elseif($item->group == 2)
                    Client Account
                    @endif

                  </td>

                  <td class="text-center">
                    @if($item->status == 1)
                    <span class="badge badge-success">Active</span>
                    @elseif($item->status == 0)
                    <span class="badge badge-danger">Inactive</span>
                    @endif

                  </td>

                  <td class="text-center">
                    <a href="{{ url('/account-code/' . $item->id . '/edit') }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>

                  </td>
                </tr>

                @endforeach
                @else
                <tr>
                  <td class="text-center" colspan="7">No data</td>
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