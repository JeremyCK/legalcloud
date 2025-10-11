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
            <h4>Referrals </h4>
          </div>
          <div class="card-body">

          <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('referral.create') }}">
                  <i class="cil-plus"> </i>Create new referral
                </a>
              </div>

            </div>
            <br>
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <!-- <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('banks.create') }}">
                  <i class="cil-plus"> </i>Create new Bank
                </a>
              </div> -->

            </div>
            <br>
            {!! $referrals->links() !!}
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Name</th>
                  <th>IC</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Case count</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($referrals))
                @foreach($referrals as $index => $referral)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $referral->name }}</td>
                  <td>{{ $referral->ic_no }} </td>
                  <td>{{ $referral->email }} </td>
                  <td>{{ $referral->phone_no }} </td>
                  <td>{{ $referral->referral_count }} </td>
                  <td class="text-center">
                    @if($referral->status == 1)
                    <span class="badge-pill small badge-success">Active</span>
                    @elseif($referral->status == 0)
                    <span class="badge-pill small badge-warning">Inactive</span>
                    @endif
                  </td>
                  <td class="text-center"><a href="{{ route('referral.edit', $referral->id ) }}" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary shadow  sharp mr-1"><i class="cil-pencil"></i></a>
                </td>
                </tr>
                @endforeach
                @else
                <tr>
                  <td class="text-center" colspan="8">No data</td>
                </tr>
                @endif

              </tbody>
            </table>

            <!-- <table id="tblReferral" class="table table-bordered table-striped yajra-datatable" style="width:100%">
                      <thead>
                        <tr>
                  <th>Name</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table> -->

                    <table id="tblReferral" class="table table-bordered table-striped yajra-datatable" style="width:100%">
                      <thead>
                        <tr>
                          <th>name</th>
                        </tr>
                      </thead>
                      <tbody>
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
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  var table;
  var table2;
  $(function() {

    // table = $('#tblReferral').DataTable({
    //   processing: true,
    //   serverSide: true,
    //   ajax: {
    //     url: "{{ route('referral.list') }}"
    //   },
    //   columns: [
    //     {
    //       data: 'name',
    //       name: 'name'
    //     },
    //   ]
    // });

    table = $('#tblReferral').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('referral.list' ) }}",
      columns: [
        {
          data: 'name',
          name: 'name'
        },
      ]
    });


    // table = $('#tblReferral').DataTable({
    //   processing: true,
    //   serverSide: true,
    //   ajax: {
    //     url: "{{ route('referral.list') }}",
    //     data: function(d) {}
    //   },
    //   columns: [
    //     {
    //       data: 'name',
    //       name: 'name'
    //     },
    //   ]
    // });

  });
</script>

@endsection