<style>
  .userlist_large {
    position: relative;
  }

  .userlist_large .media {
    flex-flow: column;
    text-align: center;
    position: relative;
  }

  .userlist_large .media.column {
    flex-flow: row;
  }

  .userlist_large .media figure {
    margin: 0 auto;
    margin-top: 36px !important;
  }

  .userlist_large .media .media-body {
    margin-top: 20px;
    width: 100%;
  }

  .userlist_large .media .media-body h5 {
    font-weight: 400;
    margin-bottom: 0;
  }

  .userlist_large .media .media-body p {
    font-size: 13px;
    line-height: 16px;
  }

  .userlist_large .media .media-body .dropdown-toggle:after {
    display: none;
  }

  .userlist_large .media .icon-center {
    position: absolute;
    height: 50px;
    width: 50px;
    line-height: 46px;
    top: 60px;
    left: 5%;
    margin-top: -25px;
    border-radius: 25px;
    border: 1px solid #8596ae;
    color: #8596ae;
    text-align: center;
    vertical-align: middle;
    font-size: 20px;
  }

  .userlist_large .media .icon-center.right {
    left: auto;
    right: 5%;
  }

  .userlist_large.bg-dark .media figure {
    border: 0;
  }

  .userlist_large.bg-dark .media .icon-center {
    border: 1px solid #ffffff !important;
  }

  .userlist_large.bg-dark .media .media-body {
    color: #b3becd;
  }

  .userlist_large.bg-dark .media .media-body h5 {
    color: #ffffff;
    font-weight: 400;
  }

  .userlist_large.bg-dark .media .media-body p {
    color: #8596ae;
  }

  .userlist_large.bg-dark .media .media-body .btn {
    color: #ffffff;
  }

  .user-full-large {
    position: relative;
    width: 100%;
    display: block;
    height: 280px;
    overflow-y: hidden;
  }

  .user-full-large:before {
    content: " ";
    width: 100%;
    left: 0;
    top: 0;
    display: block;
    position: absolute;
    Z-index: 1;
    height: 80px;
    /* fallback/image non-cover color */
    background-image: -moz-linear-gradient(270deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -webkit-gradient(270deg, linear, 0% 0%, 0% 100%, from(rgba(0, 0, 0, 0.85)), to(transparent));
    background-image: -webkit-linear-gradient(270deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -o-linear-gradient(270deg, rgba(0, 0, 0, 0.85), transparent);
  }

  .user-full-large:after {
    content: " ";
    width: 100%;
    left: 0;
    bottom: 0;
    display: block;
    position: absolute;
    Z-index: 1;
    height: 80px;
    /* fallback/image non-cover color */
    background-image: -moz-linear-gradient(90deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -webkit-gradient(90deg, linear, 0% 0%, 0% 100%, from(rgba(0, 0, 0, 0.85)), to(transparent));
    background-image: -webkit-linear-gradient(90deg, rgba(0, 0, 0, 0.85), transparent);
    background-image: -o-linear-gradient(90deg, rgba(0, 0, 0, 0.85), transparent);
  }

  .user-full-large figure {
    width: 100%;
    position: relative;
    z-index: 0;
    height: 100%;
    display: block;
    overflow: hidden;
    margin: 0;
  }

  .user-full-large figure img {
    width: 100%;
    min-height: 100%;
  }

  .user-full-large .album_block {
    height: 160px;
    width: 160px;
    display: block;
    margin: 10% auto 0 auto;
  }

  .user-full-large .album_block img {
    width: 100%;
    min-height: 100%;
  }

  .user-full-large .media {
    flex-direction: column;
    text-align: center;
  }

  .user-full-large .media .media-body {
    margin-top: 20px;
    width: 100%;
    position: absolute;
    left: 0;
    bottom: 0;
    z-index: 2;
  }

  .user-full-large .media .media-body h5,
  .user-full-large .media .media-body h4 {
    font-weight: 400;
    margin-bottom: 0;
    color: #ffffff;
  }

  .user-full-large .media .media-body p {
    font-size: 13px;
    line-height: 16px;
  }

  .user-full-large .media .media-body .dropdown-toggle:after {
    display: none;
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
    
    /* padding: 27mm 16mm 27mm 16mm; */
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
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<style></style>
@extends('dashboard.base')

<script src="//cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.1.0/classic/ckeditor.js"></script> -->
<!-- <link href="{{ asset('js/ckeditor/ckeditor.js') }}" rel="stylesheet"> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="">
          <!-- <div class="card-header">
            <h4>{{$docTemplateMain[0]->name}}</h4>
          </div> -->
          <div class="accordion" id="accordion" role="tablist">
            <div class="card mb-0">
              <div class="card-header" id="headingOne" role="tab">
                <!-- <h4 class="mb-0"><a data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">{{$docTemplateMain[0]->name}}</a></h4> -->

                <div class="box-header with-border">
                  <h3 class="box-title">{{$docTemplateMain[0]->name}}</h3>

                  <div class="box-tools pull-right">
                    <button type="button" data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="btn btn-box-tool collapsed"><i class=" cil-settings"></i>
                    </button>
                  </div>
                </div>


              </div>



              <div class="collapse" id="collapseOne" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                <div class="card-body">

                  <form method="POST" action="{{ route('document-template.update', $template->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="hf-email">Template Name</label>

                      <input class="form-control" id="template_id" name="template_id" value="{{ $template->id }}" type="hidden" />
                      <div class="col-md-9">
                        <input class="form-control" name="name" value="{{ $template->name }}" type="text" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="hf-password">Template Description</label>
                      <div class="col-md-9">
                        <textarea class="form-control" id="desc" name="desc" rows="2">{{ $template->desc}}</textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="hf-password">Template Short Code</label>
                      <div class="col-md-9">
                        <input type="text" name="code" value="{{ $template->code }}" class="form-control" />
                      </div>
                    </div>



                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="hf-password">Status</label>
                      <div class="col-md-9"><select class="form-control" id="status" name="status">
                          <option value="0">Please select</option>
                          <option value="1">Active</option>
                          <option value="2">Draft</option>
                        </select>
                      </div>
                    </div>

                    <button class="btn btn-primary float-right" type="submit">Save</button>
                    <a class="btn btn-primary" href="{{ route('email-template.index') }}">Return</a>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div id="btn_add_page" class="row">
              <div class="col-sm-12">
                <!-- <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="PrintElem(0)">
                  <i class="cil-plus"> </i>Add new page
                </a> -->
                <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="PageLockChecking(0)">
                  <i class="cil-plus"> </i>Add new page
                </a>
              </div>

            </div>
            <br>

            <div id="div_master" class="row" style="display:none;">
              <div class="row">

                <div class="col-sm-4">
                  <div class="card" style="max-height:700px;overflow-y:auto">

                    <!-- <a class="btn btn-primary nav-link" data-toggle="tab" href="#home" role="tab" href="javascript:void(0)">Return</a> -->

                    <div class="box box-solid">
                      <div class="box-body no-padding mailbox-nav">
                        <!-- Panel -->
                        <div class="panel">
                          <div class="panel-body">
                            <div class="list-group faq-list" role="tablist" style="overflow-x:overlay">


                              @foreach($caseMasterListCategory as $index => $category)
                              <a class="list-group-item {{ $index == 0 ? 'active' : '' }}" data-toggle="tab" href="#tab_{{$category->code}}" aria-controls="category-1" role="tab" aria-expanded="false">{{$category->name}}</a>

                              @endforeach

                            </div>
                          </div>
                        </div>
                        <!-- End Panel -->
                      </div>
                      <!-- /.box-body -->
                    </div>

                  </div>

                </div>


                <div class="col-sm-8">

                  <div class="tab-content">

                    @foreach($caseMasterListCategory as $index => $category)


                    <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="tab_{{$category->code}}" role="tabpanel">

                      <div class="card">
                        <div class="card-header">
                          <h4>{{$category->name}}</h4>
                        </div>
                        <div class="card-body">
                          <form id="form_master_{{$category->id}}">

                            @csrf

                            @foreach($caseMasterListField as $index => $field)

                            @if ($field->case_field_id == $category->id)

                            <div class="form-group row">
                              <label class="col-md-3 col-form-label" for="hf-email">{{$field->name}}</label>
                              <div class="col-md-9">
                                <input class="form-control" id="{{$field->code}}" type="text" name="{{$field->code}}">
                              </div>
                            </div>

                            @endif


                            @endforeach


                          </form>
                        </div>
                        <div class="card-footer">
                          <!-- <button class="btn btn-sm btn-primary" type="submit"> Submit</button> -->
                          <a class="btn btn-sm btn-info float-right mr-1 d-print-none" onclick="submitMasterList('{{$category->id}}')" href="javascript:void(0)">

                            <div class="overlay" style="display:none">
                              <i class="fa fa-refresh fa-spin"></i>
                            </div>

                            <svg class="c-icon">
                              <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                            </svg> Save
                          </a>
                          <!-- <button class="btn btn-sm btn-danger" type="reset"> Reset</button> -->
                        </div>
                      </div>

                    </div>
                    @endforeach

                  </div>
                </div>
              </div>
            </div>

            <div id="div_edit" class="row" style="display:none;">

              <div class="col-sm-12">
                <!-- <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="moveController('master')">
                  <i class="cil-plus"> </i>Get code
                </a> -->
                <!-- <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="PageLockChecking(0)">
                  <i class="cil-plus"> </i>Add new page
                </a> -->
              </div>
              <br>

              <!-- <div class="col-sm-3 " >
                <div class="box box-solid">
                  <div class="box-body no-padding mailbox-nav">
                    <div class="panel">
                      <div class="panel-body">
                        <div class="list-group faq-list" role="tablist" style="overflow-x:overlay">


                          @foreach($caseMasterListCategory as $index => $category)
                          <a class="list-group-item {{ $index == 0 ? 'active' : '' }}" data-toggle="tab" href="#tab_{{$category->code}}" aria-controls="category-1" role="tab" aria-expanded="false">{{$category->name}}</a>

                          @endforeach

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->

              <!-- <div class="wrapperDisplay col-sm-3">
                <page size="A4" class="card">
                  test
                </page>
              </div> -->

              <div class="wrapperDisplay col-sm-12">
                <page size="A4" class="card">
                  <textarea class="form-control" id="editor1" name="summary-ckeditor"></textarea>
                </page>
              </div>

              <div class="col-sm-12">
                <div class="">
                  <div class="card-body">
                    <div class="col-md-12">
                      <input type="hidden" id="pageId" name="pageId" value="">
                    </div>
                  </div>
                  <div class="">
                    <a class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="backToViewMode();">
                      <svg class="c-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                      </svg> Back</a>
                    <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="savePage()">
                      <svg class="c-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                      </svg> Save</a>
                  </div>
                </div>
              </div>
            </div>

            <div id="div_page" style="display: block;">
              <div class="wrapperDisplay">
                @foreach($docTemplatePages as $index => $page)
                <page size="A4" class="card" id="tab_{{$page->page}}">
                  <div class="card-header">
                    <span class="float-left">{{$page->page}}
                      <span id="lock_span_{{$page->id}}" class="" @if($page->is_locked == 0) style="display:none;" @endif>
                        <i class="fa fa-lock"></i> <span id="lock_text_{{$page->id}}">{{$page->name}} is editing this page..</span>
                      </span>
                    </span>

                    <a id="btn_edit_{{$page->id}}" class="btn btn-sm btn-info float-right mr-1 d-print-none" @if($page->is_locked == 1) style="display:none;" @endif href="javascript:void(0)" onclick="PageLockChecking('{{$page->id}}')">
                      <svg class="c-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                      </svg> Edit</a>

                  </div>
                  <div id="div_content_{{$page->id}}" class="card-body">
                    {!! $page->content !!}
                  </div>
                </page>
                @endforeach
              </div>
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
  CKEDITOR.replace('summary-ckeditor');
  CKEDITOR.config.height = 900;
  CKEDITOR.config.tabSpaces = 4;

  CKEDITOR.config.coreStyles_underline = {
    element: 'span',
    attributes: {
      'class': 'Underline'
    }
  };

  // CKEDITOR.replace( 'editor1' );
</script>

<script>
  var blnDirty = false;
  var init = 0;


  function moveController(mode) {

    if (mode == 'view') {
      document.getElementById('div_edit').style.display = "none";
      document.getElementById('div_master').style.display = "none";
      document.getElementById('div_page').style.display = "flex";
      document.getElementById('accordion').style.display = "block";
      document.getElementById('btn_add_page').style.display = "flex";
      init = 0;

    } else if (mode == 'edit') {
      document.getElementById('div_edit').style.display = "flex";
      document.getElementById('div_master').style.display = "none";
      document.getElementById('div_page').style.display = "none";
      document.getElementById('accordion').style.display = "none";
      document.getElementById('btn_add_page').style.display = "none";

      blnDirty = false;
    } else if (mode == 'master') {
      document.getElementById('div_edit').style.display = "none";
      document.getElementById('div_page').style.display = "none";
      document.getElementById('div_master').style.display = "block";
      document.getElementById('accordion').style.display = "none";
      document.getElementById('btn_add_page').style.display = "none";

    }

  }

  function backToViewMode() {
    var pageId = $("#pageId").val();

    if (pageId == '0') {
      moveController('view');
    }



    if (blnDirty == true) {
      var contentData = document.getElementById('div_content_' + pageId).innerHTML;
      var editedcontent = CKEDITOR.instances.editor1.getData();

      Swal.fire({
        title: 'You already make some changes to this page, do you want to save before you leave?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Yes`,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          Swal.fire('Saved!', '', 'success')
        } else if (result.isDenied) {
          moveController('view');
        }
      })
    } else {
      moveController('view');
    }

    // updateLock();
  }

  CKEDITOR.instances.editor1.on('change', function() {
    if (init > 0) {
      blnDirty = true;
    }

    init += 1;
    // clearTimeout(mytime);
  });

  function PrintElem(elem) {
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');
    elem = 'div_page';

    mywindow.document.write('<html><head><title>' + document.title + '</title>');
    mywindow.document.write('</head><body >');
    mywindow.document.write('<h1>' + document.title + '</h1>');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    // mywindow.close();

    return true;
  }

  function PageLockChecking(pageId) {

    $("#pageId").val(pageId);

    if (pageId == "0") {
      // window.print();
      CKEDITOR.instances.editor1.setData('');
      moveController('edit');
      return;
    }

    $.ajax({
      type: 'POST',
      url: '/check_lock',
      data: {
        id: $("#pageId").val(),
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {

        

        if (data.length > 0) {
          var errorcount = 0;
          for (var i = 0; i < data.length; i++) {
            var editing_user = data[i].name + ' is editing this page. . .';
            var return_page_id = data[i].id;

            console.log('div_content_' + pageId);
            $("#lock_text_" + return_page_id).html(editing_user);
            document.getElementById('div_content_' + return_page_id).innerHTML = data[i].content;

            if (data[i].is_locked == "0") {
              $("#lock_span_" + return_page_id).hide();
              $("#btn_edit_" + return_page_id).show();
            } else {
              $("#lock_span_" + return_page_id).show();
              $("#btn_edit_" + return_page_id).hide();
            }

            if (pageId == return_page_id) {
              if (data[i].is_locked != "0") {
                errorcount += 1;
                Swal.fire('Notice!', editing_user, 'error');
              }
            }
          }

          if (errorcount == 0) {
            if (pageId == "0") {

            } else {
              var contentData = document.getElementById('div_content_' + return_page_id).innerHTML;
              $("#pageId").val(pageId);
              CKEDITOR.instances.editor1.setData(contentData);
              // updateLock();

              blnDirty = false;
            }

            moveController('edit');

          }
        }

        return;

        if (data != "0") {
          Swal.fire('Notice!', data, 'error');
          $("#lock_text_" + pageId).html(data);
          $("#lock_span_" + pageId).show();
          $("#btn_edit_" + pageId).hide();

        } else {
          document.getElementById('div_edit').style.display = "flex";
          document.getElementById('div_page').style.display = "none";

          var test = document.getElementById('div_content_' + page).innerHTML;
          $("#pageId").val(page);
          CKEDITOR.instances.editor1.setData(test);

          updateLock();
        }

        // document.getElementById('div_edit').style.display = "none";
        // document.getElementById('div_page').style.display = "flex";
      }
    });
  }

  function goToEditorMode(pageId) {

    if (pageId == "0") {
      moveController('');
    } else {

    }

    moveController('edit');

  }

  function editMode(page) {
    $("#pageId").val(page);
    checkIsLocked(page);
  }

  function addMode() {
    $("#pageId").val("0");
  }

  function editorMode(pageId) {
    $("#pageId").val(pageId);
    checkIsLocked(pageId);

    return;

    if (pageId == "0") {

    } else {

    }

    document.getElementById('div_edit').style.display = "flex";
    document.getElementById('div_page').style.display = "none";

    var test = document.getElementById('div_content_' + page).innerHTML;

    CKEDITOR.instances.editor1.setData(test);
  }

  function checkIsLocked(pageId) {

    $.ajax({
      type: 'POST',
      url: '/check_lock',
      data: {
        id: $("#pageId").val(),
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {

        console.log(data);

        if (data.length > 0) {
          var errorcount = 0;
          for (var i = 0; i < data.length; i++) {
            var editing_user = data[i].name + ' is editing this page. . .';
            var return_page_id = data[i].id;
            $("#lock_text_" + return_page_id).html(editing_user);
            // $("#div_content_" + return_page_id).innerHTML(data[i].content);
            document.getElementById('div_content_' + return_page_id).innerHTML = data[i].content;

            if (data[i].is_locked == "0") {
              $("#lock_span_" + return_page_id).hide();
              $("#btn_edit_" + return_page_id).show();
            } else {
              $("#lock_span_" + return_page_id).show();
              $("#btn_edit_" + return_page_id).hide();
            }

            if (pageId == return_page_id) {
              if (data[i].is_locked != "0") {
                errorcount += 1;
                Swal.fire('Notice!', editing_user, 'error');
              }
            }
          }

          if (errorcount == 0) {
            document.getElementById('div_edit').style.display = "flex";
            document.getElementById('div_page').style.display = "none";

            var contentData = document.getElementById('div_content_' + page).innerHTML;
            $("#pageId").val(pageId);
            CKEDITOR.instances.editor1.setData(contentData);

            updateLock();
          }
        }

        return;

        if (data != "0") {
          Swal.fire('Notice!', data, 'error');
          $("#lock_text_" + pageId).html(data);
          $("#lock_span_" + pageId).show();
          $("#btn_edit_" + pageId).hide();

        } else {
          document.getElementById('div_edit').style.display = "flex";
          document.getElementById('div_page').style.display = "none";

          var test = document.getElementById('div_content_' + page).innerHTML;
          $("#pageId").val(page);
          CKEDITOR.instances.editor1.setData(test);

          updateLock();
        }

        // document.getElementById('div_edit').style.display = "none";
        // document.getElementById('div_page').style.display = "flex";
      }
    });
  }


  function savePage() {
    // document.getElementById('div_edit').style.display = "none";
    // document.getElementById('div_page').style.display = "flex";

    getMessage();
  }

  var needToConfirm = true;

  window.onbeforeunload = confirmExit;

  function confirmExit() {
    if (needToConfirm) {
      alert('no');
      // check on the elements whether any change has been done on the fields.
      // If any change has been done, then set message here.
    }
  }


  var resetClock = 15 * 60 * 1000;

  function remind(msg1) {
    var msg = "This is a reminder after " + msg1 + " Secs";
    alert(msg);
  }



  mytime = setTimeout('remind(2)', resetClock);

  function saveClicked() {
    needToConfirm = false;
  }

  function doneEdit() {
    // window.re = 'document-template/1/edit'

  }

  function getMessage() {

    var content = CKEDITOR.instances.editor1.getData();

    // data = stripslashes(data);
    // data = htmlspecialchars(data);

    // console.log(data);
    $.ajax({
      type: 'POST',
      url: '/update_page',
      data: {
        pageId: $("#pageId").val(),
        template_id: $("#template_id").val(),
        content: content,
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {


        Swal.fire('Saved!', '', 'success')

        if ($("#pageId").val() == '0') {
          location.reload();
        } else {
          document.getElementById('div_content_' + $("#pageId").val()).innerHTML = content;
          // document.getElementById('div_edit').style.display = "none";
          // document.getElementById('div_page').style.display = "flex";
          moveController('view');
        }

      }
    });
  }





  function updateLock(lock_state) {
    $.ajax({
      type: 'POST',
      url: '/update_lock',
      data: {
        is_locked: lock_state,
        id: $("#pageId").val(),
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {
        // alert(data);

        document.getElementById('div_edit').style.display = "none";
        document.getElementById('div_page').style.display = "flex";
      }
    });
  }
</script>

@endsection