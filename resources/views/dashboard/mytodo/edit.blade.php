@extends('dashboard.base')

<link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/fontawesome.min.css" rel="stylesheet"> -->
<!-- <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet"> -->
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

    .checklist_name {
        font-size: 12px;
    }

    .done .checklist_name {
        color: #33cabb;
    }
</style>
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">

            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                <div class="card ">

                    <div class="card-header">
                        <h4> Voucher details </h4>
                    </div>
                    <div class="card-body">
                        <section id="dVoucherInvoice" class="invoice printableArea d_operation">
                            <!-- title row -->
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="page-header">
                                        Payment Voucher
                                        <small class="pull-right">Date: <?php echo date('d/m/Y') ?> </small>
                                    </h2>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- info row -->
                            <div class="row invoice-info">
                                <div class="col-sm-6 invoice-col">
                                    From
                                    <address>
                                        <strong class="text-red">L H YEO & CO.</strong><br>
                                        No, 62B, 2nd Floor, Jalan SS21/62, Damansara Utama<br>
                                        47400 Petaling Jaya<br>
                                        Selangor Darul Ehsan<br>
                                        Phone: 03-7727 1818<br>
                                        Fax: 03-7732 8818
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 invoice-col text-right">
                                    To
                                    <address>
                                        <strong class="text-blue"><?php echo $mytodo->name ?> </strong><br>
                                        <?php echo $mytodo->address ?><br>
                                        Phone: <?php echo $mytodo->phone_no ?><br>
                                        Email: <?php echo $mytodo->email ?>
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-12 invoice-col">
                                    <div class="invoice-details row no-margin">
                                        <div class="col-md-6 col-lg-6"><b>Case Ref No </b>#<?php echo $mytodo->case_ref_no ?></div>
                                        <!-- <div class="col-md-6 col-lg-3"><b>Order ID:</b> FC12548</div> -->
                                        <!-- <div class="col-md-6 col-lg-3"><b>Payment Due:</b> 14/08/2017</div> -->
                                        <div class="col-md-6 col-lg-6 float-right"><b>Account:</b> 0001245879315</div>
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Description</th>
                                                <!-- <th>Serial #</th> -->
                                                <th class="text-right">Quantity</th>
                                                <th class="text-right">Unit Cost</th>
                                                <th class="text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl-submit-voucher">

                                            @if(count($voucher_details))
                                            @foreach($voucher_details as $index => $voucher)
                                            <tr>
                                                <td >{{ $index+1  }}</td>
                                                <td >{{ $voucher->item_name }}</td>
                                                <td class="text-right">1</td>
                                                <td class="text-right">{{ $voucher->amount }}</td>
                                                <td class="text-right">{{ $voucher->amount }}</td>
                                                <!-- <td class="text-center"><a href="{{ route('mytodo.edit', $voucher->id ) }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="cil-pencil"></i></a>
                                                </td> -->
                                            </tr>

                                            @endforeach
                                            @else
                                            <tr>
                                                <td class="text-center" colspan="7">No data</td>
                                            </tr>
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <!-- accepted payments column -->
                                <div class="col-12 col-sm-6 text-left">
                                    <div class=""><b>Payment Type: </b><span id="span_payment_type"></span></div>
                                    <div class=""><b>Payment Date: </b><span id="span_payment_date"></span></div>
                                    <div id="div_payment_details"></div>
                                </div>
                                <!-- /.col -->
                                <div class="col-12 col-sm-6 text-right">
                                    <!-- <p class="lead"><b>Payment Due</b><span class="text-danger"> 14/08/2017 </span></p> -->


                                    <div class="total-payment">
                                        <h3><b>Total :</b><span id="span_total_amount" class="">Rm 0</span> </h3>
                                    </div>

                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- this row will not appear when printing -->
                            <div class="row no-print" style="margin-top: 50px;">
                                <div class="col-12">
                                    <button onclick="updateStatus(2,'<?php echo $mytodo->id ?>')" class="btn btn-danger" type="button"><span><i class="ion-reply"></i> Reject</span> </button>
                                    <button type="button" onclick="updateStatus(1,'<?php echo $mytodo->id ?>')" class="btn btn-success pull-right"><i class="fa fa-check"></i> Approve
                                    </button>
                                    <!-- <button type="button" class="btn btn-warning pull-right" style="margin-right: 5px;">
                                        <span><i class="fa fa-print"></i> Print</span>
                                    </button> -->
                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection

@section('javascript')

<script>
    function updateStatus($status, $id) {
    $("#span_update_dispatch").hide();
    $(".overlay").show();

    var form_data = new FormData();

    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

    form_data.append('status',$status);

    $.ajax({
      type: 'POST',
      url: '/update_mytodo_status/'+ $id,
      data: form_data,
      processData: false,
      contentType: false,
      success: function(data) {
        console.log(data);
        if (data.status == 1) {
            Swal.fire(
          'Success!',
          'Status updated',
          'success'
        )

          $("#span_update").show();
          $(".overlay").hide();

          viewMode();
        }

        location.reload();
      }
    });

  }
</script>

@endsection