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
</style>
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<style></style>
@extends('dashboard.base')

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="">
          <div class="card-header">
            <h4>test</h4>
          </div>
          <div class="card-body">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div id="div_edit" class="row" style="display:none;">
              <div class="col-sm-12">
                <div class="">
                  <div class="card-body">
                    <div class="col-md-12">
                      <input type="hidden" id="pageId" name="pageId" value="">
                      <textarea class="form-control" id="editor1" name="summary-ckeditor">{{$docTemplatePages[0]->content}}</textarea>
                    </div>
                  </div>
                  <div class="card-footer">
                    <a class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="doneEdit();">
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

            <div id="div_page" class="row">

              <div class="col-sm-3">
                <div class="" style="max-height: 50%">

                  <!-- <a class="btn btn-primary nav-link" data-toggle="tab" href="#home" role="tab" href="javascript:void(0)">Return</a> -->

                  <div class="box box-solid">
                    <div class="box-body no-padding mailbox-nav">
                      <!-- Panel -->
                      <div class="panel">
                        <div class="panel-body">
                          <div class="list-group faq-list" role="tablist" style="overflow-x:overlay">

                            @foreach($docTemplatePages as $index => $page)
                            <a class="list-group-item {{ $index == 0 ? 'active' : '' }}" data-toggle="tab" href="#tab_{{$page->page}}" aria-controls="page-1" role="tab" aria-expanded="false">{{$page->page}}</a>
                            @endforeach

                          </div>
                        </div>
                      </div>
                      <!-- End Panel -->
                    </div>
                    <!-- /.box-body -->
                  </div>

                  <!-- <div class="navi navi-bold navi-hover navi-active navi-link-rounded" style="overflow-x:overlay">

               

                    </div> -->

                </div>

              </div>


              <div class="col-sm-9">
                <div class="tab-content">


                  <div class="tab-pane active" role="tabpanel" style="overflow-y:auto;max-height:600px;">
                    @foreach($docTemplatePages as $index => $page)
                    <div class="card" id="tab_{{$page->page}}">
                      <div class="card-header">
                        <!-- <h4>{{$page->page}}</h4> -->
                        <span class="float-left">{{$page->page}}
                          @if($page->is_locked == 1)
                          <i class="fa fa-lock"></i> {{$page->name}} is edting..
                          @endif

                        </span>

                        <!-- <a class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="editMode('{{$page->page}}')"> -->
                        @if($page->is_locked == 0)
                        <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="editMode('{{$page->id}}')">
                          <svg class="c-icon">
                            <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                          </svg> Edit</a>
                        @endif

                      </div>
                      <div id="div_content_{{$page->page}}" class="card-body">
                        {!! $page->content !!}
                      </div>
                      <!-- <div class="card-footer">
                            <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="javascript:void(0)" onclick="editMode()">
                              <svg class="c-icon">
                                <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-save"></use>
                              </svg> Save</a>
                          </div> -->
                    </div>
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
</div>
</div>

@endsection

@section('javascript')


<script>
  CKEDITOR.replace('summary-ckeditor');
  CKEDITOR.config.height = 600;
</script>

<script>
  function editMode(page) {
    $("#pageId").val(page);
    checkIsLocked();
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

  CKEDITOR.instances.editor1.on('change', function() {
    console.log("TEST");
    clearTimeout(mytime);
  });

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

    var data = CKEDITOR.instances.editor1.getData();

    console.log(data);
    $.ajax({
      type: 'POST',
      url: '/update_page',
      data: {
        page: $("#pageId").val(),
        content: data,
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {
        alert(data);

        document.getElementById('div_edit').style.display = "none";
        document.getElementById('div_page').style.display = "flex";
      }
    });
  }



  function checkIsLocked() {
    $.ajax({
      type: 'POST',
      url: '/check_lock',
      data: {
        id: $("#pageId").val(),
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {
        console.log(data);

        if (data != "0") {
          alert(data)
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

  function updateLock() {
    $.ajax({
      type: 'POST',
      url: '/update_lock',
      data: {
        is_locked: 1,
        id: $("#pageId").val(),
        _token: '<?php echo csrf_token() ?>'
      },
      success: function(data) {
        alert(data);

        document.getElementById('div_edit').style.display = "none";
        document.getElementById('div_page').style.display = "flex";
      }
    });
  }
</script>

@endsection