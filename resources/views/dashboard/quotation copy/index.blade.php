@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Quotation Template</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="/quotation/create">
                  <i class="cil-plus"> </i>Create new Quotation
                </a>
              </div>

            </div>
            <br />
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Quotation</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($quotations))
                @foreach($quotations as $index => $quotation)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $quotation->name }}</td>
                  <td class="text-center">
                    @if($quotation->status == 1)
                    <span class="badge badge-success">Active</span>
                    @elseif($quotation->status == 0)
                    <span class="badge badge-danger">Inactive</span>
                    @endif

                  </td>

                  <td class="text-center">
                    <a href="{{ url('/quotation/' . $quotation->id . '/edit') }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>
                    <!-- <a href="{{ url('/quotation/' . $quotation->id ) }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a> -->

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

            {!! $quotations->links() !!}
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