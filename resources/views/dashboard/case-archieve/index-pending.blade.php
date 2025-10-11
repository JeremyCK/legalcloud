@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
@section('content')

<div class="container-fluid">
  <div class="fade-in">

    <div class="row no-print">
      <div class="col-sm-12">

        <div class="card">
          <div class="card-header">
            <h4>Files before 2022 - Pending Close</h4>

          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif
          
            <div class="row @if($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'clerk' || $current_user->menuroles == 'chambering') hide @endif">
              <div class="col-6">
                <div class="form-group row">
                  <div class="col">
                    <label>Filter by PIC</label>
                    <select class="form-control" id="ddl_pic" name="ddl_pic">
                      <option value="0">-- All --</option>
                      @foreach($old_pic as $pic)
                      <option value="{{ $pic->old_pic_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="form-group row">
                  <div class="col">
                    <label>Filter by new PIC</label>
                    <select class="form-control" id="ddl_pic_new" name="ddl_pic_new">
                      <option value="0">-- All --</option>
                      @foreach($new_pic as $pic)
                      <option value="{{ $pic->new_pic_id }}">{{ $pic->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <br>

            <div class="box-body no-padding " style="width:100%;overflow-x:auto;padding-bottom:100px">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Action</th>
                    <th>File Ref</th>
                    <th>Client (P)</th>
                    <th>Client (V)</th>
                    <th>PIC</th>
                    <th>New PIC</th>
                    <th>Sales</th>
                    <th>Case Date</th>
                    <th>Completion Date</th>
                    <th>Status</th>
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

    <div id="modalStatus" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form id="form_add">
              <input type="hidden" value="0" id="txtId" name="txtId" />

              <div class="form-group row ">
                <div class="col">
                  <label>Status</label>
                  <textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>
                </div>
              </div>

          </div>
          </form>
          <div class="modal-footer">
            <button type="button" id="btnClose" class="btn btn_close_all btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success float-right" onclick="updateStatus()">Save
              <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
              </div>
            </button>
          </div>
        </div>

      </div>
    </div>

    <div id="modalTransfer" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form id="form_add">

              <div class="form-group row ">
                <div class="col">
                  <h4>Assign case to</h4>
                </div>
              </div>

              <div class="form-group row">
                <div class="col">
                  <label>User</label>
                  <select class="form-control" id="new_pic_id" name="new_pic_id">
                    @foreach($users as $index =>$user)
                    <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

          </div>
          </form>
          <div class="modal-footer">
            <button type="button" id="btnClose2" class="btn btn_close_all btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success float-right" onclick="TransferCase()">Assign
              <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
              </div>
            </button>
          </div>
        </div>

      </div>
    </div>

    <div id="modalCompletionDate" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <form id="form_add">

              <div class="form-group row ">
                <div class="col">
                  <h4>Update completion date</h4>
                </div>
              </div>

              <div class="form-group row">
                <div class="col">
                  <label>Completion Date</label>

                  <input type="date" id="completion_date" name="completion_date" />
                </div>
              </div>

          </div>
          </form>
          <div class="modal-footer">
            <button type="button" id="btnClose2" class="btn btn_close_all btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success float-right" onclick="updateCompletionDate()">Assign
              <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
              </div>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('javascript')
<!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<!-- <script src="{{ asset('js/jquery.toast.min.js') }}"></script> -->
<script type="text/javascript">
  CKEDITOR.replace('summary-ckeditor');
  CKEDITOR.config.height = 300;
  CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
  CKEDITOR.config.removeButtons = 'Image';

  document.getElementById("ddl_pic").onchange = function() {
    reloadtable();
  }

  document.getElementById("ddl_pic_new").onchange = function() {
    reloadtable();
  }

  function getValue(id) {
    $("#txtId").val(id);
    CKEDITOR.instances['summary-ckeditor'].setData($("#remark_" + id).html());
  }

  function transferModal(id) {
    $("#txtId").val(id);
  }

  function completionDateModal(id) {
    $("#txtId").val(id);
    $("#completion_date").val($("#completion_date_" + id).html());
  }

  function moveCaseToPerfectionCase($id) {
            Swal.fire({
                icon: 'warning',
                text: 'Move this case to perfection case list?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/moveCaseToPerfectionCase/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                var count = $("#totalAccountBox").html();
                                count -= 1;

                                $("#totalAccountBox").html(count);

                                toastController('Moved to prefection list');
                                reloadtable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

  function reopenCase($id) {
    Swal.fire({
      title: 'Reopen this case?',
      showCancelButton: true,
      confirmButtonText: `Yes`,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          url: '/reopenArchieveCase/' + $id,
          success: function(data) {
            console.log(data);
            if (data.status == 1) {

              toastController('Case reopen');
              reloadtable();
            } else {
              Swal.fire('notice!', data.message, 'warning');
            }

          }
        });
      }
    })
  }

  function closeCase($id) {
    Swal.fire({
      title: 'Close this case?',
      showCancelButton: true,
      confirmButtonText: `Yes`,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          url: '/closeArchieveCase/' + $id,
          success: function(data) {
            console.log(data);
            if (data.status == 1) {

              toastController('Case closed');
              reloadtable();
            } else {
              Swal.fire('notice!', data.message, 'warning');
            }

          }
        });
      }
    })
  }

  

  function updateStatus() {
    var formData = new FormData();

    var desc = CKEDITOR.instances['summary-ckeditor'].getData();

    if ($("#desc").val() == "") {
      // Swal.fire('Notice!', '', 'warning');
      return
    }

    formData.append('status', desc);


    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      type: 'POST',
      url: '/updateArchieveCaseRemark/' + $("#txtId").val(),
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {

        toastController('Status updated');
        $("#remark_" + $("#txtId").val()).html(desc);
        closeUniversalModal();
        // location.reload();
      }
    });
  }

  function updateCompletionDate() {
    var formData = new FormData();


    if ($("#completion_date").val() == "") {
      // Swal.fire('Notice!', '', 'warning');
      return
    }

    formData.append('completion_date', $("#completion_date").val());

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      type: 'POST',
      url: '/updateArchieveCaseCompletionDate/' + $("#txtId").val(),
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {

        toastController('Completion date updated');
        $("#completion_date_" + $("#txtId").val()).html($("#completion_date").val());
        closeUniversalModal();
        // location.reload();
      }
    });
  }

  function TransferCase() {
    var formData = new FormData();

    formData.append('new_pic_id', $("#new_pic_id").val());

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      type: 'POST',
      url: '/TransferCase/' + $("#txtId").val(),
      data: formData,
      processData: false,
      contentType: false,
      success: function(data) {

        toastController('Case Transferred');
        $("#new_pic_id_" + $("#txtId").val()).html($("#new_pic_id option:selected").text());
        closeUniversalModal();
      }
    });
  }

  function reloadtable() {
    
    var url = "{{ route('case_archieve_pending.list',['userID', 'newPIC']) }}";

    url = url.replace('userID', $("#ddl_pic").val());
    url = url.replace('newPIC', $("#ddl_pic_new").val());

    var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      pageLength: 100,
      ajax: {
        url: url,
        data: function(d) {
          d.status = 3;
          d.userID = $("#userID").val();
          d.newPIC = $("#newPIC").val();
        },
      },
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'action',
          name: 'action',
          className: "text-center",
          orderable: true,
          searchable: true
        },
        {
          data: 'ref_no',
          name: 'ref_no',
        },
        {
          data: 'client_name_p',
          name: 'client_name_p'
        },
        {
          data: 'client_name_v',
          name: 'client_name_v'
        },
        {
          data: 'old_pic',
          name: 'old_pic'
        },
        {
          data: 'new_pic',
          name: 'new_pic'
        },
        {
          data: 'sales_id',
          name: 'sales_id'
        },
        {
          data: 'case_date',
          name: 'case_date'
        },
        {
          data: 'completion_date',
          name: 'completion_date'
        },
        {
          data: 'remarks',
          name: 'remarks',
          width: '400px'
        },
      ]
    });
  }

  $(function() {


    reloadtable()

  });
</script>
@endsection