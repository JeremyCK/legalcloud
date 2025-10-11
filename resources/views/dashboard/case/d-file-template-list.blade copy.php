<div id="dFileTemplateList" class="card d_operation" style="display:none">

  <div class="card-header">
    <h4>File template list</h4>
  </div>
  <div class="card-body">
    <table class="table table-striped table-bordered datatable">
      <thead>
        <tr class="text-center">
          <th>No</th>
          <th>Name</th>
          <!-- <th>Action</th> -->
        </tr>
      </thead>
      <tbody>
        <form id="form_files" enctype="multipart/form-data">
      @csrf

          @if(count($documentTemplateFile))
          @foreach($documentTemplateFile as $index => $template)
          <tr>
            <td class="text-center">
              <div class="checkbox">
                <input type="checkbox" name="files" value="{{ $template->id }}" id="chk_file_{{ $template->id }}">
                <label for="chk_file_{{ $template->id }}">{{ $index + 1 }}</label>
              </div>
            </td>
            <td>{{ $template->name }}</td>
            <!-- <td class="text-center"><a href="{{ url('/document-file/' . $template->id . '/edit') }}" class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a></td> -->
          </tr>
          @endforeach
          @else
          <tr>
            <td class="text-center" colspan="5">No data</td>
          </tr>
          @endif
        </form>
      </tbody>
    </table>

    <button class="btn btn-success float-right" onclick="generateFileFromTemplate('{{ $case->id }}')" type="button">
      <span id="span_upload">Generate</span>
      <div class="overlay" style="display:none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </button>
    <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-primary">Cancel</a>
  </div>
</div>