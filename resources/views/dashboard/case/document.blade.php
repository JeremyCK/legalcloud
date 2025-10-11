@section('css')

<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
<!-- <link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet"> -->

<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .info-box {
    min-height: 100px;
    background: #fff;
    width: 100%;
    margin-bottom: 20px;
    padding: 15px;
  }

  .info-box small {
    font-size: 14px;
  }

  .info-box .progress {
    background: rgba(0, 0, 0, .2);
    margin: 5px -10px 5px 0;
    height: 2px;
  }

  .info-box .progress,
  .info-box .progress .progress-bar {
    border-radius: 0;
  }

  .info-box .progress .progress-bar {
    background: #fff;
  }

  .info-box-icon {
    float: left;
    height: 70px;
    width: 70px;
    text-align: center;
    font-size: 30px;
    line-height: 74px;
    background: rgba(0, 0, 0, .2);
    border-radius: 100%
  }

  .info-box-icon.push-bottom {
    margin-top: 20px;
  }

  .info-box-icon>img {
    max-width: 100%
  }

  .info-box-content {
    padding: 10px 10px 10px 0;
    margin-left: 90px;
  }

  .info-box-number {
    font-weight: 300;
    font-size: 21px;
  }

  a.a_admin,
  a.a_admin:hover,
  a.a_admin:active,
  a.a_admin:focus {
    color: #f9b115;
  }

  a.a_sales,
  a.a_sales:hover,
  a.a_sales:active,
  a.a_sales:focus {
    color: #e55353;
  }

  a.a_lawyer,
  a.a_lawyer:hover,
  a.a_lawyer:active,
  a.a_lawyer:focus {
    color: #4638c2;
  }

  a.a_clerk,
  a.a_clerk:hover,
  a.a_clerk:active,
  a.a_clerk:focus {
    color: #2ca8ff;
  }

  .bg-done {
    background-color: #46be8a !important;
    color: white !important;
  }

  .bg-overdue {
    background-color: #e55353 !important;
    color: white !important;
  }

  .info-box-text,
  .progress-description {
    display: block;
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    font-weight: 400;
  }



  .pageA4 {
    width: 210mm !important;
    /* height: 297mm; */
    size: auto;
    /* margin: 0 0 0 0; */
    padding: 50px;
    /* padding-bottom: 100px; */
    box-shadow: 0;
    -webkit-print-color-adjust: exact;
  }



  page {
    background: white;
    display: block;
    margin: 0 auto;
    margin-bottom: 0.5cm;
    box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
  }

  page[size="A4"] {
    width: 21cm;
    height: 29.7cm;
    padding-left: 16mm;
    padding-right: 16mm;
  }

  page[size="A4"][layout="portrait"] {
    width: 33.7cm;
    height: 21cm;
  }

  .wrapperDisplay {
    margin: 0 auto 10px;
    padding: 0 0 0 0;
    color: #000;
    display: block;
    position: relative;
  }
</style>

<style type="text/css" media="print">
  @media print {
    @page {
      margin: 0;
    }

    body {
      margin: 1.6cm;
    }

    .noprint {
      visibility: hidden;
    }
  }
</style>
<script src="//cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!-- <link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet"> -->
@endsection

@extends('dashboard.base')
@section('content')


<div class="container-fluid">



  <div id="dpage" style="display: block;">


    <div class=" row " style="margin-bottom: 20px;">
      <div class="col-sm-12">
        <a id="btnBackToEditMode" class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="window.history.back();">
          <i class="ion-reply"> </i> Back to case</a>
        <a id="btnEditMode" class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="editMode();">
        <i class="cil-pencil"> </i> Edit mode</a>
        <a id="btnPrint" class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="printing()">
          <i class="cil-print"> </i> Print </a>
        <!-- <a id="btnPrint" class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="generatedocument()">
          <i class="cil-print"> </i> Generate </a> -->
      </div>

    </div>
    <div id="dDoc" class="wrapperDisplay">
      @foreach($docTemplatePages as $index => $page)
      <page size="A4" id="page_{{$page->page}}" class="pbreak card pageA4">
        {!! $page->content !!}
      </page>
      @endforeach
    </div>
  </div>

  <div id="dEdit" style="display: none;">

    <div class="row" style="margin-bottom: 20px;">
      <div class="col-sm-12">
        <a id="btnBackMode" style="display: none;" class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="backMode();">
          <i class="ion-reply"> </i> Back</a>
        <a id="btnPrint" class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="saveAsNewVersion()">
        <i class="cil-save"></i> Save as new version</a>
      </div>
    </div>



    <div class="wrapperDisplay">
      @foreach($docTemplatePages as $index => $page)
      <page size="A4" class="card" id="tab_{{$page->page}}">
        <div class="card-header">
          <span class="float-left">{{$page->page}}
            <span id="lock_span_{{$page->id}}" class="" @if($page->is_locked == 0) style="display:none;" @endif>
              <i class="fa fa-lock"></i> <span id="lock_text_{{$page->id}}">{{$page->name}} is editing this page..</span>
            </span>
          </span>

          <a id="btn_edit_{{$page->id}}" class="btn btn-sm btn-info float-right mr-1 d-print-none" @if($page->is_locked == 1) style="display:none;" @endif href="javascript:void(0)" onclick="editorMode('{{$page->id}}')">
          <i class="cil-pencil"></i> Edit</a>

        </div>
        <div id="div_content_{{$page->page}}" class="card-body">
          {!! $page->content !!}
        </div>
      </page>
      @endforeach
    </div>
  </div>

  <div id="dEditor" style="display: none;">

    <div class="row" style="margin-bottom: 20px;">
      <div class="col-sm-12">
        <a id="btnBackToEditMode" class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="editMode();">
          <i class="ion-reply"> </i> Back</a>
        <a id="btnPrint" class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="savePage()">
        <i class="cil-save"></i> Save</a>
      </div>
    </div>

    <div class="wrapperDisplay ">
      <page size="A4" class="card">
        <input type="hidden" id="pageId" name="pageId" value="">
        <textarea class="form-control" id="editor1" name="summary-ckeditor"></textarea>
      </page>
    </div>
  </div>

</div>

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/paperfish/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.bootstrap.wizard.js') }}"></script>

<script src="{{ asset('js/paperfish/paper-bootstrap-wizard.js') }}"></script>
<script src="{{ asset('js/paperfish/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script src="http://s.codepen.io/assets/libs/modernizr.js" type="text/javascript"></script>
<script>
  CKEDITOR.replace('summary-ckeditor');
  CKEDITOR.config.height = 1000;

  CKEDITOR.config.coreStyles_underline = {
    element: 'span',
    attributes: {
      'class': 'Underline'
    }
  };

  // CKEDITOR.replace( 'editor1' );
</script>
<script>
  var doc = new jsPDF();

  function editMode() {
    $("#dEdit").show();
    $("#dpage").hide();
    $("#btnEditMode").hide();
    $("#btnPrint").hide();
    $("#btnBackMode").show();
  }

  function backMode() {

    $("#dEdit").hide();
    $("#dpage").show();
    $("#btnEditMode").show();
    $("#btnPrint").show();
    $("#btnBackMode").hide();
  }

  function editorMode(id) {

    var contentData = document.getElementById('div_content_' + id).innerHTML;
    $("#pageId").val(id);
    CKEDITOR.instances.editor1.setData(contentData);

    $("#dEdit").hide();
    $("#dpage").hide();
    $("#dEditor").show();
  }

  function savePage() {
    var content = CKEDITOR.instances.editor1.getData();
    document.getElementById('div_content_' + $("#pageId").val()).innerHTML = content;
    document.getElementById('page_' + $("#pageId").val()).innerHTML = content;
    editMode();
  }

  function saveAsNewVersion() {

  }

  function generatedocument() {
    var contents = document.getElementById("dDoc").innerHTML;
    // var frame1 = document.createElement('iframe');
    // frame1.name = "frame1";
    // frame1.style.position = "absolute";
    // frame1.style.top = "-1000000px";
    // document.body.appendChild(frame1);
    // var frameDoc = (frame1.contentWindow) ? frame1.contentWindow : (frame1.contentDocument.document) ? frame1.contentDocument.document : frame1.contentDocument;
    // frameDoc.document.open();
    // frameDoc.document.write('<html><head><title>DIV Contents</title>');
    // frameDoc.document.write('</head><body>');
    // frameDoc.document.write(contents);
    // frameDoc.document.write('</body></html>');
    // frameDoc.document.close();
    // setTimeout(function() {
    //   window.frames["frame1"].focus();
    //   window.frames["frame1"].print();
    //   document.body.removeChild(frame1);
    // }, 500);

    // return false;


    var specialElementHandlers = {
      '#elementH': function(element, renderer) {
        return true;
      }
    };
    doc.fromHTML(contents, 15, 15, {
      'elementHandlers': specialElementHandlers
    });

    // Save the PDF
    doc.save('sample-document.pdf');
  }



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

    return false;
  }
</script>

@endsection