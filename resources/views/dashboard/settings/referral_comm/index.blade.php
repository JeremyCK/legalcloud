@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')

<div class="container-fluid">
  <div class="fade-in">

    <div class="row">
      <div class="col-sm-12">

        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h4>Referral Commission Settings</h4>
              </div>

            </div>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif


            <br>

            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr class="text-center">
                    <th>No</th>
                    <th>Name</th>
                    <th>Formula</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if(count($ReferralFormula))
                  @foreach($ReferralFormula as $index => $formula)
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $formula->name }}</td>
                    <td>{{ $formula->formula }} </td>
                    <td class="text-center">
                      @if($formula->status == 1)
                      <span class="badge-pill  badge-success">Active</span>
                      @elseif($formula->status == 0)
                      <span class="badge-pill  badge-warning">Inactive</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <a href="{{ url('/referral-comm/' . $formula->id. '/edit') }}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="edit"><i class="cil-pencil"></i></a>
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
</div>

@endsection

@section('javascript')
<!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->
<script type="text/javascript">

</script>
@endsection