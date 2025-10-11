@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Gen File Test</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
            <div class="row">
              <div class="col-sm-12">
                <a class="btn btn-lg btn-primary  float-right" href="{{ route('document-template.create') }}">
                  <i class="cil-plus"> </i>Gen all docs
                </a>
              </div>

            </div>
            <br>
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr class="text-center">
                  <th>No</th>
                  <th>Name</th>
                  <th>Path</th>
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
                  <td>{{ $template->path }} </td>
                  <td class="text-center">
                    @if($template->status == 1)
                    <span class="badge-pill badge-success">Active</span>
                    @elseif($template->status == 0)
                    <span class="badge-pill badge-warning">Draft</span>
                    @endif
                  </td>
                  <td class="text-center"><a href="javascript:void(0)" onclick="generateFile('{{ $template->id }}')" class="btn btn-primary shadow sharp mr-1"><i class="cil-cloud-download"></i></a></td>
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
</script>

@endsection