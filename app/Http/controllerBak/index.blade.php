@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">





        <div class="card">
          <div class="card-header">
            <h4>Quotation Generator</h4>
          </div>
          <div class="card-body">






            <div id="dQuotationInvoice">
              <div class="form-group row">

                <input class="form-control" id="quotation_no" type="hidden" name="quotation_no">
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                  <div class="form-group row">
                    <div class="col">
                      <label>Quotation Template</label>
                      <select id="ddl_quotation_template" class="form-control" name="ddl_quotation_template">
                        <option value="0">-- Select Quotation Template --</option>
                        @foreach($quotation_template as $index => $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Loan Sum</label>
                      <input class="form-control" id="loan_sum" type="number" name="loan_sum" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Date</label>
                      <input class="form-control" type="date" id="date" name="date" autocomplete="off">
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                  <div class="form-group row">
                    <div class="col">
                      <label>Bill To</label>
                      <input class="form-control" type="text" name="name" id="name">
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>Purchase Price</label>
                      <input class="form-control" type="number" id="purchase_price" name="purchase_price" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col">
                      <label>With firm name</label>
                      <select id="ddl_firm_name" class="form-control" name="ddl_firm_name">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                      </select>
                    </div>
                  </div>
                </div>

                

              </div>
              <div class="row">
                <div class="col-sm-12">

                  <button id="btnQuotation" class="btn btn-warning float-left" type="button" onclick="generateQuotation();" style="margin-right: 5px;display:none">
                    <i class="cil-print"></i> Print Quotation
                  </button>

                  <button class="btn btn-primary float-right" type="button" onclick="loadQuotationTemplate('');">
                    <i class="cil-caret-right"></i> Generate Template
                  </button>
                </div>

              </div>
              <br />
              <table class="table table-striped table-bordered datatable">
                <tbody id="tbl-bill-create">



                </tbody>
              </table>
            </div>


            <div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation" style="display: none;">
              <style>
                .color-test {
                  background-color: red;
                }
              </style>
              <button type="button" class="btn btn-warning pull-left no-print" onclick="printlo()" style="margin-right: 5px;">
                <span><i class="fa fa-print"></i> Print</span>
              </button>
              <a href="javascript:void(0);" onclick="cancelQuotationPrintMode()" class="btn btn-danger  no-print">Cancel</a>
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h2 class="page-header">
                    <!-- Payment Voucher -->
                    Quotation
                    <small class="pull-right">Date: <span id="span_date"></span> </small>
                  </h2>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-6 invoice-col">
                  <div  id="fromFirm">
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
                  
                </div>
                <!-- /.col -->
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
                <!-- /.col -->
                <div class="col-sm-12 invoice-col">
                  <div class="invoice-details row no-margin">
                    <div class="col-md-6 col-lg-6">
                      <span class="pull-left"><b>Quotation No: <span id="quotation_no">{{$current_user->nick_name}}_{{ $parameter->parameter_value_1}}</span></b></span>
                    </div>
                    <!-- <div class="col-md-6 col-lg-3"><b>Order ID:</b> FC12548</div> -->
                    <!-- <div class="col-md-6 col-lg-3"><b>Payment Due:</b> 14/08/2017</div> -->
                    <div class="col-md-6 col-lg-6 pull-right">
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
                    <table class="table table-striped" id="tbl-print-quotation">

                    </table>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-12" style="border: 1px solid black;">
                    Please Note:
                    Please issue cheque/bank draft in favour of "L H YEO & CO." or deposit to:
                    <br />
                    Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of one (1) month from the date of the bill until the date of the actual payment in accordance with clause 6 of the Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976.
                    E & OE
                  </div>



                </div> -->

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @endsection

  @section('javascript')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
  <script src="{{ asset('js/jquery.print.js') }}"></script>
  <script>

document.getElementById('ddl_firm_name').onchange = function() {
        if ($("#ddl_firm_name").val() == 1)
        {
          $("#fromFirm").show();
        }
        else
        {
          $("#fromFirm").hide();
        }
    };
    function loadQuotationTemplate() {

      if ($("#date").val() == "") {
        Swal.fire('Notice!', 'Please select quotation date ', 'warning');
        return;
      }

      if ($("#name").val() == "") {
        Swal.fire('Notice!', 'Please enter name', 'warning');
        return;
      }

      $("#span_date").html($("#date").val());
      $("#p-quo-client-name").html($("#name").val());

      var formData = new FormData();
      formData.append('loan_sum', $("#loan_sum").val());
      formData.append('purchase_price', $("#purchase_price").val());


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        type: 'POST',
        url: '/load_quotation_template_generator/' + $("#ddl_quotation_template").val(),
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {

          console.log(data);
          $("#btnQuotation").show();
          // $("#quotation_no").val(data.parameter.parameter_value_1);
          $('#tbl-bill-create').html(data.view);
          // $('#tbl-print-quotation').html(data.view2);
          // $('ul.pagination').replaceWith(data.links);
        }
      });
    }

    function generateQuotation() {

      var strHtml = `<tr>
                    <th>#</th>
                    <th>Description</th>
                    <th class="text-right">RM</th>
                    <th class="text-right">SST (6%)</th>
                    <th class="text-right">RM</th>
                  </tr>
                  <td colspan="5"><h4><b>Professional fees</b></h4></td>
                  `;

      var iCount = 1;
      var subTotal = 0;

      $.each($("input[name='Professional fees']:checked"), function() {

        itemID = $(this).val();

        account_item_id = parseFloat($("#account_item_id_" + itemID).val());
        need_approval = parseFloat($("#need_approval_" + itemID).val());
        account_name = $("#account_name_" + itemID).val()
        amount = parseFloat($("#quo_amt_" + itemID).val());
        cat_id = parseFloat($("#cat_" + itemID).val());
        min = parseFloat($("#min_" + itemID).val());
        max = parseFloat($("#max_" + itemID).val());
        subTotal += parseFloat(amount) + parseFloat((amount * 0.06));

        strHtml += `
      <tr>
        <td class="text-center" style="border-right: 1px solid black;">` + iCount + `</td>
        <td style="border-right: 1px solid black;">` + account_name + `</td>
        <td style="text-align: right;border-right: 1px solid black;">` + amount.toFixed(2) + `</td>
        <td style="text-align: right;border-right: 1px solid black;">` + (amount * 0.06).toFixed(2) + `</td>
        <td style="text-align: right;border-right: 1px solid black;">` + (parseFloat(amount) + parseFloat((amount * 0.06).toFixed(2))).toFixed(2) + `</td>

      </tr>
      `;

        iCount += 1;

      });

      strHtml += `
    <tr style="">
    <td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="3">` + numberWithCommas(subTotal.toFixed(2)) + `</td>
  </tr>
    `;

      subTotal = 0;
      iCount = 1;

      strHtml += `<td colspan="5"><h4><b>Stamp duties</b></h4></td>`;

      $.each($("input[name='Stamp duties']:checked"), function() {

        itemID = $(this).val();

        account_item_id = parseFloat($("#account_item_id_" + itemID).val());
        need_approval = parseFloat($("#need_approval_" + itemID).val());
        account_name = $("#account_name_" + itemID).val()
        amount = parseFloat($("#quo_amt_" + itemID).val());
        cat_id = parseFloat($("#cat_" + itemID).val());
        min = parseFloat($("#min_" + itemID).val());
        max = parseFloat($("#max_" + itemID).val());
        subTotal += amount;

        strHtml += `
<tr>
  <td class="text-center" style="border-right: 1px solid black;">` + iCount + `</td>
  <td style="border-right: 1px solid black;">` + account_name + `</td>
  <td style="text-align: right;border-right: 1px solid black;">` + amount.toFixed(2) + `</td>
  <td style="text-align: right;border-right: 1px solid black;">-</td>
  <td style="text-align: right;border-right: 1px solid black;">` + amount.toFixed(2) + `</td>

</tr>
`;

        iCount += 1;

      });

      strHtml += `
<tr style="">
<td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
<td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="3">` + numberWithCommas(subTotal.toFixed(2)) + `</td>
</tr>
`;

      subTotal = 0;
      iCount = 1;

      strHtml += `<td colspan="5"><h4><b>Disbursement</b></h4></td>`;

      $.each($("input[name='Disbursement']:checked"), function() {

        itemID = $(this).val();

        account_item_id = parseFloat($("#account_item_id_" + itemID).val());
        need_approval = parseFloat($("#need_approval_" + itemID).val());
        account_name = $("#account_name_" + itemID).val()
        amount = parseFloat($("#quo_amt_" + itemID).val());
        cat_id = parseFloat($("#cat_" + itemID).val());
        min = parseFloat($("#min_" + itemID).val());
        max = parseFloat($("#max_" + itemID).val());
        subTotal += amount;

        strHtml += `
<tr>
  <td class="text-center" style="border-right: 1px solid black;">` + iCount + `</td>
  <td style="border-right: 1px solid black;">` + account_name + `</td>
  <td style="text-align: right;border-right: 1px solid black;">` + amount.toFixed(2) + `</td>
  <td style="text-align: right;border-right: 1px solid black;">-</td>
  <td style="text-align: right;border-right: 1px solid black;">` + amount.toFixed(2) + `</td>

</tr>
`;

        iCount += 1;

      });

      strHtml += `
<tr style="">
<td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
<td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="3">` + numberWithCommas(subTotal.toFixed(2)) + `</td>
</tr>
`;

      $("#tbl-print-quotation").html(strHtml);

      $("#dQuotationInvoice-p").show();
      $("#dQuotationInvoice").hide();

      // console.log(bill_list);
    }

    function cancelQuotationPrintMode() {
      $("#dQuotationInvoice-p").hide();
      $("#dQuotationInvoice").show();
    }

    function numberWithCommas(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function printlo() {
      // window.print();

      // $("#dVoucherInvoice").print();

      // jQuery.print();

      $("#dQuotationInvoice-p").print({
        addGlobalStyles: true,
        stylesheet: true,
        rejectWindow: true,
        noPrintSelector: ".no-print",
        iframe: false,
        append: null,
        prepend: null
      });

      var formData = new FormData();
      formData.append('quotation_no', '{{$current_user->nick_name}}' + '_' + '{{ $parameter->parameter_value_1}}');

      $.ajax({
        type: 'POST',
        url: '/logPrintedQuotation',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {

          console.log(data);
          $("#btnQuotation").show();
          // $("#quotation_no").val(data.parameter.parameter_value_1);
          $('#tbl-bill-create').html(data.view);
          // $('#tbl-print-quotation').html(data.view2);
          // $('ul.pagination').replaceWith(data.links);
        }
      });
    }
  </script>

  @endsection