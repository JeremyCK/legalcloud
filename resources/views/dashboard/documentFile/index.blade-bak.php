@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>File templates</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">

              <div class="col-sm-4">
                <div class="form-group  ">
                  <div class="input-group">
                    <input type="text" name="search_query" id="search_query" class="form-control" placeholder="Search template">
                    <span class="input-group-btn">
                      <button type="button" onclick="searchQuery()" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-2 ">
                <div class="form-group  ">
                  <a class="btn btn-lg btn-info" href="javascript:void(0)" onclick="clearSearch()">Clear search</a>
                </div>
              </div>
              <div class="col-sm-6">

                <a class="btn btn-lg btn-primary  float-right" href="{{ route('document-file.create') }}">
                  <i class="cil-plus"> </i>Create new file template
                </a>
              </div>

            </div>
            <br>

            {!! $documentTemplateFile->withQueryString()->links() !!}
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Name</th>
                  <th>Remark</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if(count($documentTemplateFile))
                @foreach($documentTemplateFile as $index => $template)
                <tr>
                  <td class="text-center">{{ $index + 1 }}</td>
                  <td>{{ $template->name }}</td>
                  <td>{{ $template->remarks }}</td>
                  <!-- <td>{{ $template_path.$template->file_name }} </td> -->
                  <td class="text-center">
                    @if($template->status == 1)
                    <span class="badge-pill badge-success">Active</span>
                    @elseif($template->status == 0)
                    <span class="badge-pill badge-warning">Draft</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <a href="{{ url('/document-file/' . $template->id . '/edit') }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>
                    <a href="javascript:void(0)" onclick="deleteFile('{{ $template->id }}')" class="btn btn-danger"><i class="cil-x"></i></a>

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
            {!! $documentTemplateFile->withQueryString()->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('javascript')
<script>
  function generateFile(template_id) {
    $.ajax({
      type: 'POST',
      url: '/gen_file',
      data: {
        template_id: template_id,
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {
        alert(data);

      }
    });
  }

  function searchQuery() {
    if ($("#search_query").val() != '') {
      window.location.href = '/document-file?query=' + $("#search_query").val();
    }

  }

  function clearSearch() {
      window.location.href = '/document-file';

  }

  function deleteFile($id) {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    Swal.fire({
      title: 'Delete this template?',
      showCancelButton: true,
      confirmButtonText: `Yes`,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          url: '/delete_file/' + $id,
          success: function(data) {
            console.log(data);
            if (data.status == 1) {

              Swal.fire('Success!', data.message, 'success');
              location.reload();
            } else {
              Swal.fire('notice!', data.message, 'warning');
            }

          }
        });
      }
    })

  }
</script>

@endsection