<section id="dVoucherInvoice" class="invoice printableArea d_operation" style="display:none;">
    <!-- title row -->
    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                Payment Voucher
                <small class="pull-right">Date: <?php echo date('d/m/Y'); ?> </small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-6 invoice-col">
            From
            {{-- <address>
                <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                No, 62B, 2nd Floor, Jalan SS21/62, <br>
                Damansara Utama<br>
                47400 Petaling Jaya<br>
                Selangor Darul Ehsan<br>
                Phone: 03-7727 1818<br>
                Fax: 03-7732 8818
            </address> --}}

            <address class="print-formal">
                <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>
                Advocates & Solicitors<br>
                {!! $Branch->address !!}<br>
                <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
                <b>Email</b>: {{ $Branch->email }}
              </address>

        </div>
        <!-- /.col -->
        <div class="col-sm-6 invoice-col text-right">
            To
            <address>
                <strong id="payee_voucher_name" class="text-blue"> </strong><br>
                <!-- <?php echo $customer->address; ?><br>
        Phone: <?php echo $customer->phone_no; ?><br>
        Email: <?php echo $customer->email; ?> -->
            </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-12 invoice-col">
            <div class="invoice-details row no-margin">
                <div class="col-md-6 col-lg-6"><b>Case Ref No </b>#<?php echo $case->case_ref_no; ?></div>
                <!-- <div class="col-md-6 col-lg-3"><b>Order ID:</b> FC12548</div> -->
                <!-- <div class="col-md-6 col-lg-3"><b>Payment Due:</b> 14/08/2017</div> -->
                {{-- <div class="col-md-6 col-lg-6 float-right"><b>Account:</b> 0001245879315</div> --}}
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
                        {{-- <th class="text-right">Quantity</th> --}}
                        {{-- <th class="text-right">Unit Cost</th> --}}
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="tbl-submit-voucher">
                    <tr>
                        <td>1</td>
                        <td id="td_item_name"></td>
                        <td>12345678912514</td>
                        {{-- <td class="text-right">1</td> --}}
                        {{-- <td class="text-right td_item_price">RM 0</td> --}}
                        <td class="text-right td_item_price">RM 0</td>
                    </tr>

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
            <button onclick="billModeGroupBack()" class="btn btn-danger" type="button"><span><i class="ion-reply"></i>
                    Cancel</span> </button>
            <button type="button" onclick="submitVoucher('<?php echo $case->id; ?>')" class="btn btn-success pull-right"><i
                    class="fa fa-credit-card"></i> Submit
            </button>
            <!-- <button type="button" class="btn btn-warning pull-right" style="margin-right: 5px;">
        <span><i class="fa fa-print"></i> Print</span>
      </button> -->
        </div>
    </div>
</section>
