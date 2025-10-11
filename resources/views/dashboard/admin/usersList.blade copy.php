@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h4>{{ __('coreuiforms.users.users') }}</h4>
          </div>
          <div class="card-body">

            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-6">
                <select class="form-control" id="ddl-role" name="role">
                  <option value="0">-- all --</option>
                  @foreach($roles as $role)
                  <option value="{{ $role->name }}">{{ $role->name }}</option>

                  @endforeach
                </select>
              </div>
              <div class="col-sm-6">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('users.create') }}">
                  <i class="cil-plus"> </i>{{ __('coreuiforms.users.add_new_user') }}
                </a>
              </div>

            </div>
            <br>
            <table class="table table-responsive-sm  table-bordered table-striped">
              <thead class="text-center">
                <tr>
                  <th>{{ __('coreuiforms.users.username') }}</th>
                  <th>{{ __('coreuiforms.users.email') }}</th>
                  <th>{{ __('coreuiforms.users.roles') }}</th>
                  <th>Initial</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tbl-data">
                @include('dashboard.admin.table.tbl-list')
              </tbody>
            </table>
            <!-- {!! $users->links() !!} -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection


@section('javascript')

<script>
  document.getElementById("ddl-role").onchange = function() {

    var formData = new FormData();
    formData.append('role', $("#ddl-role").val());

    $.ajax({
      type: 'POST',
      url: 'filter',
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#tbl-data').html(data.view);
        // $('ul.pagination').replaceWith(data.links);
      }
    });
  }
</script>
@endsection