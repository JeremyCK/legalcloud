<div id="dTrustReceipt-p" class="div2 invoice printableArea d_operation" style="display: none;">

  <button type="button" class="btn btn-warning pull-left no-print" onclick="printlo()" style="margin-right: 5px;">
    <span><i class="fa fa-print"></i> Print</span>
  </button>
  <a href="javascript:void(0);" onclick="cancelTrustReceiptPrintMode()" class="btn btn-danger  no-print">Cancel</a>
  <!-- title row -->
  <div class="row">
    <div class="col-12">
      <h2 class="page-header">
        <!-- Payment Voucher -->
        Receipt
        <small class="pull-right">Date: <?php echo date('d/m/Y') ?> </small>
      </h2>
    </div>
    <!-- /.col -->
  </div>
  <!-- info row -->
  <div class="row invoice-info">
    <div class="col-sm-6 invoice-col">
      <b>From</b>
      <address>
        <strong style="color: #2d659d">L H YEO & CO.</strong><br>
        No, 62B, 2nd Floor, Jalan SS21/62, <br>
        Damansara Utama<br>
        47400 Petaling Jaya<br>
        Selangor Darul Ehsan<br>
        <b>Phone</b>: 03-7727 1818<br>
        <b>Fax</b>: 03-7732 8818<br>
        <b>Email</b>: lhyeo@lhyeo.com
      </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-6 invoice-col text-right">
      <b>To</b>
      <address>
        <strong class="text-blue"><span id="p-trust-client-name"></span></strong><br>
        <div class="hide">
          <span id="p-quo-client-address"></span> <br>
          Phone: <span id="p-quo-client-phone"></span><br>
          Email: <span id="p-quo-client-email"></span>
        </div>

      </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-12 invoice-col">
      <div class="invoice-details row no-margin">
        <div class="col-md-6 col-lg-6"><b>Case Ref No: </b>#{{ $case->case_ref_no }} </div>
        <!-- <div class="col-md-6 col-lg-3"><b>Order ID:</b> FC12548</div> -->
        <!-- <div class="col-md-6 col-lg-3"><b>Payment Due:</b> 14/08/2017</div> -->
        <div class="col-md-6 col-lg-6 pull-right">
          <span class="pull-right"><b>Receipt No: <span id="receipt_no"></span></b></span>
        </div>
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <!-- Table row -->
  <div class="row">
    <?php $total_amt = 0; ?>
    <div class="col-12 table-responsive">
      <table class="table table-striped" id="tbl-print-trust-receipt">
        <!-- <thead>
          <tr>
            <th>#</th>
            <th>Description</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody id="tbl-print-quotation">

         

        </tbody> -->
      </table>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <div class="row">
    <div class="col-12" style="border: 1px solid black;">
      @php
        $firm_name = 'L H YEO & CO';
        if($case->branch_id == "4")
        {
          $firm_name = 'Ramakrishnan & Co';
        }
        else if($case->branch_id == "6")
        {
          $firm_name = 'ISMAIL & LIM';
        }
    @endphp
      Please Note:
      Please issue cheque/bank draft in favour of "{{$firm_name}}." or deposit to:
      <br />
      Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of one (1) month from the date of the bill until the date of the actual payment in accordance with clause 6 of the Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976.
      E & OE
    </div>
    <!-- accepted payments column -->

    <!-- /.row -->



  </div>
  </div>