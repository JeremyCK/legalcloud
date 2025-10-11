@extends('dashboard.base')

<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div class="card">
          <div class="card-header">
            <h4>Audit Log</h4>
          </div>
          <div class="card-body">
            <button type="button" onclick="printing()" class="btn btn-warning pull-right" style="margin: 5px;">
              <span><i class="fa fa-print"></i> Print</span>
            </button>
            <div id="dDoc">
              <table class="table table-striped table-bordered datatable">
                <thead>
                  <tr class="text-center">
                    <th>No</th>
                    <th>Page</th>
                    <th>Action</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  @if(count($templates))
                  @foreach($templates as $index => $template)
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $template->model }}</td>
                    <td>{{ $template->desc }} </td>
                    <td class="text-center">{{ $template->created_at }}
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
<script>
  function printing() {
    var contents = document.getElementById("dDoc").innerHTML;
    var frame1 = document.createElement('iframe');
    frame1.name = "frame1";
    frame1.style.position = "absolute";
    frame1.style.top = "-1000000px";
    document.body.appendChild(frame1);
    var frameDoc = (frame1.contentWindow) ? frame1.contentWindow : (frame1.contentDocument.document) ? frame1.contentDocument.document : frame1.contentDocument;
    frameDoc.document.open();
    frameDoc.document.write('<html><head><title>DIV Contents</title>');
    frameDoc.document.write('</head><body>');
    frameDoc.document.write(contents);
    frameDoc.document.write('</body></html>');
    frameDoc.document.close();
    setTimeout(function() {
      window.frames["frame1"].focus();
      window.frames["frame1"].print();
      document.body.removeChild(frame1);
    }, 500);
  }
</script>
@endsection