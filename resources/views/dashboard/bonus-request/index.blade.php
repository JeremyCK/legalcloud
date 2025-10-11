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
            <div class="row">
              <div class="col-6">
                <h4>Bonus Request</h4>
              </div>

            </div>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            <div class="row">
              <div class="col-sm-6 col-lg-4">
                  <div class="card mb-4" style="--cui-card-cap-bg: #00aced">
                      <div class="card-header position-relative d-flex justify-content-center align-items-center">
                          <h4 class="text-center">Total Bonus Approved</span>
                      </div>
                      <div class="card-body row text-center">
                          {{-- <div class="col">
                              <div class="fs-5 fw-semibold">RM <span id="total_approved_2">{{$bonus_total_sum_2}} </span> </div>
                              <div class="text-uppercase text-medium-emphasis ">(2%)</div>
                          </div>
                          <div class="vr"></div>
                          <div class="col">
                              <div class="fs-5 fw-semibold">RM <span id="total_approved_3"> {{$bonus_total_sum_3}} </span> </div>
                              <div class="text-uppercase text-medium-emphasis ">(3%)</div>
                          </div> --}}

                          <div class="col">
                            <div class="fs-5 fw-semibold">RM <span id="total_approved_3">{{ number_format($bonus_total_sum_3, 2, '.', ',') }} </span> </div>
                            <div class="text-uppercase text-medium-emphasis ">(3%)</div>
                        </div>
                        <div class="vr"></div>
                        <div class="col">
                            <div class="fs-5 fw-semibold">RM <span id="total_approved_5">{{ number_format($bonus_total_sum_5, 2, '.', ',') }} </span> </div>
                            <div class="text-uppercase text-medium-emphasis ">(5%)</div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>

            <div class="row ">


              <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                <div class="form-group row">
                  <div class="col">
                    <label>From date</label>
                    <input class="form-control" type="date" id="date_from" name="date_from">
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                <div class="form-group row">
                  <div class="col">
                    <label>To date</label>
                    <input class="form-control" type="date" id="date_to" name="date_to">
                  </div>
                </div>

              </div>

            

              
              <div class="col-6">
                <div class="form-group row">
                  <div class="col">
                    <label>Filter by Requestor</label>
                    <select class="form-control" id="ddl_requestor" name="ddl_requestor">
                      <option value="99">-- All --</option>
                      @foreach ($staffs as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}
                        </option>
                    @endforeach
                    </select>
                  </div>
                </div>

                
              </div>

              <div class="col-6">
                <div class="form-group row">
                  <div class="col">
                    <label>Filter by Status</label>
                    <select class="form-control" id="ddl_status" name="ddl_status">
                      <option value="0">-- All --</option>
                      <option value="1">Reviewing</option>
                      <option value="2">Approved</option>
                      <option value="3">Rejected</option>
                    </select>
                  </div>
                </div>

                

                
              </div>

              <div class="col-6">
                <div class="form-group row">
                    <div class="col">
                        <label>Filter by Date Type</label>
                        <select class="form-control" id="ddl_date_type" name="ddl_date_type">
                            <option value="request">Request Date</option>
                            <option value="approval">Approval Date</option>
                        </select>
                    </div>
                </div>

            </div>
              





              <div class="col-sm-12">
                <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)" onclick="reloadTable();">
                  <i class="fa cil-search"> </i>Filter
                </a>
              </div>

              <div class="col-sm-12">
                <hr />
              </div>

            </div>
            <br>

            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              {{-- @if($current_user->menuroles == 'admin' || $current_user->id == ) --}}

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Action</th>
                    <th>File Ref</th>
                    <th>Requester</th>
                    <th>Status</th>
                    <th>Bonus Type</th>
                    <th>Request Date</th>
                    <th>Approved Date</th>
                    {{-- <th>First House</th>
                    <th>Adju No</th>
                    <th>Adju Docs</th>
                    <th>Adju Date</th>
                    <th>Notis Date</th>
                    <th>Stamp Duty Paid</th>
                    <th>Remark</th>
                    <th>Assgined By</th>
                    <th>Date</th> --}}
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
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  function reloadTable() {
    sumStaffBonus();
    var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      pageLength: 50,
      ajax: {
        url: "{{ route('bonusrequest.list') }}",
        data: function(d) {
          d.date_from = $("#date_from").val();
          d.date_to = $("#date_to").val();
          d.status = $("#ddl_status").val();
          d.requestor = $("#ddl_requestor").val();
                        d.date_type = $("#ddl_date_type").val();
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
          data: 'case_ref_no',
          name: 'case_ref_no',
        },
        {
          data: 'user_name',
          className: "text-center",
          name: 'user_name'
        },
        {
          data: 'status',
          className: "text-center",
          name: 'status'
        },
        {
          data: 'bonus_type',
          className: "text-center",
          name: 'bonus_type'
        },
        {
          data: 'created_at',
          className: "text-center",
          name: 'created_at'
        },
        {
          data: 'approved_date',
          className: "text-center",
          name: 'approved_date'
        },
        // {
        //   data: 'adju_no',
        //   name: 'adju_no'
        // },
        // {
        //   data: 'adju_doc',
        //   name: 'adju_doc'
        // },
        // {
        //   data: 'adju_date',
        //   name: 'adju_date'
        // },
        // {
        //   data: 'notis_date',
        //   name: 'notis_date' 
        // },
        // {
        //   data: 'stamp_duty_paid',
        //   className: "text-center",
        //   name: 'stamp_duty_paid'
        // },
        // {
        //   data: 'remark',
        //   name: 'remark'
        // },
        // {
        //   data: 'assign_by',
        //   name: 'assign_by'
        // },
        // {
        //   data: 'created_at',
        //   name: 'created_at'
        // },
      ]
    });
  }

  function numberWithCommas(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }


  function sumStaffBonus() {

    $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

    var form_data = new FormData();
    form_data.append("requestor", $("#ddl_requestor").val());

    
    $.ajax({
            type: 'POST',
            url: '/sumStaffBonus',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log(data);
                if (data.status == 1) {

                    $("#total_approved_3").html( numberWithCommas(data.bonus_total_sum_3));
                    $("#total_approved_5").html( numberWithCommas(data.bonus_total_sum_5));
                } else {
                }

            }
        });
    }

  $(function() {
    reloadTable();
  });
</script>
@endsection