@extends('dashboard.base')

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">



        <div id="dList" class="card">
          <div class="card-header">
            <h4>Referral</h4>
          </div>
          <div class="card-body">


            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

              <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Ic No</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Referral</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

            </div>


          </div>
        </div>

        <div id="dAction" class="card" style="display:none">
          <div class="card-header">
            <h4>Voucher</h4>
          </div>
          <div class="card-body">
            <form id="form_voucher" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                  <input class="form-control" type="hidden" id="selected_id" name="selected_id" value="">
                  <div class="form-group row">
                    <div class="col">
                      <label>Item</label>
                      <input class="form-control" type="hidden" value="" id="voucher_id" name="voucher_id">
                      <input class="form-control" type="hidden" value="" id="status" name="status">
                      <input class="form-control" type="text" value="" id="item" name="item" disabled>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Available Amount</label>
                      <input class="form-control" type="text" value="" id="amt" name="amt" disabled>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Remarks</label>
                      <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                    </div>
                  </div>

                  <div class="row" style="margin-bottom: 20px;">
                    <div class="col-sm-12">
                      <div class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                      </div>
                      <a id="btnBackToEditMode" class="btn btn-sm btn-info float-left mr-1 d-print-none" href="javascript:void(0)" onclick="modeController('list');">
                        <i class="ion-reply"> </i> Back
                      </a>
                      <a id="btnPrint" class="btn btn-sm btn-success float-right mr-1 d-print-none" href="javascript:void(0)" onclick="updateVoucher(1)">
                        <i class="cil-check-alt"></i> Approve</a>

                      <a id="btnPrint" class="btn btn-sm btn-danger float-right mr-1 d-print-none" href="javascript:void(0)" onclick="updateVoucher(2)">
                        <i class="cil-x"></i> Reject</a>
                    </div>
                  </div>


                </div>
              </div>
            </form>

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
<script>
  var table;
</script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $(function() {



    table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('referral_main.list') }}",
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'name',
          name: 'name'
        },
        {
          data: 'company',
          name: 'company'
        },
        {
          data: 'ic_no',
          name: 'ic_no'
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'phone_no',
          name: 'phone_no'
        },
        {
          data: 'case_count',
          name: 'case_count'
        },
        {
          data: 'action',
          className: "text-center",
          name: 'action',
          orderable: true,
          searchable: true
        },
      ]
    });


  });
</script>
@endsection