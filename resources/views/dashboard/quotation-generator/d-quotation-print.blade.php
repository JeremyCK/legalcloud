<div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation" style="display: none;">
  <button type="button" class="btn btn-warning pull-left no-print" onclick="printlo()" style="margin-right: 5px;">
    <span><i class="fa fa-print"></i> Print</span>
  </button>
  <a href="javascript:void(0);" onclick="cancelQuotationPrintMode()" class="btn btn-danger  no-print">Cancel</a>
  <div class="row">
    <div class="col-12">
      <h2 class="page-header">
        Quotation
        <small class="pull-right">Date: <?php echo date('d/m/Y') ?> </small>
      </h2>
  </div>
  <div class="row invoice-info">
    <div class="col-sm-6 invoice-col">
      <b>From</b>
      {{-- <address>
        <strong style="color: #2d659d">L H YEO & CO.</strong><br>
        No, 62B, 2nd Floor, Jalan SS21/62, <br>
        Damansara Utama<br>
        47400 Petaling Jaya<br>
        Selangor Darul Ehsan<br>
        <b>Phone</b>: 03-7727 1818<br>
        <b>Fax</b>: 03-7732 8818<br>
        <b>Email</b>: lhyeo@lhyeo.com
      </address> --}} 
      <address class="print-formal">
        <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>
        Advocates & Solicitors<br>
        {!! $Branch->address !!}<br>
        <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
        <b>Email</b>: {{ $Branch->email }}
    </address>
    </div>
    <div class="col-sm-6 invoice-col text-right">
      <b>To</b>
      <address>
        <strong class="text-blue"><span id="p-quo-client-name"></span></strong><br>
        <div class="hide">
          <span id="p-quo-client-address"></span> <br>
          Phone: <span id="p-quo-client-phone"></span><br>
          Email: <span id="p-quo-client-email"></span>
        </div>

      </address>
    </div>
    <div class="col-sm-12 invoice-col">
      <div class="invoice-details row no-margin">
        <div class="col-md-6 col-lg-6"><b>Case Ref No: </b>#{{ $case->case_ref_no }} </div>
        <div class="col-md-6 col-lg-6 pull-right">
          <span class="pull-right"><b>Quotation No: <span id="quotation_no"></span></b></span>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <?php $total_amt = 0; ?>
    <div class="col-12 table-responsive">
      <table class="table table-striped" id="tbl-print-quotation">
      </table>
    </div>
  </div>