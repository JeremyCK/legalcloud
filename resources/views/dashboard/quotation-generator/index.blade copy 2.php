@extends('dashboard.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
       

        <div id="quotation_generator" class="card" >
          <div class="card-header">
            <h4>Quotation Generator</h4>
          </div>
          <div class="card-body">
            <div id="dQuotationInvoice">
              <form id="form_quotation_main" enctype="multipart/form-data">
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

                    <div class="form-group row">
                      <div class="col">
                        <label>Discount</label>
                        <select id="ddl_discount" class="form-control" name="ddl_discount">
                          <option value="0">No</option>
                          <option value="1">Yes</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                    <div class="form-group row">
                      <div class="col">
                        <label>Bill To</label>
                        <input class="form-control" type="text" name="bill_to" id="bill_to">
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

                    <div class="form-group row div_discount" style="display:none;">
                      <div class="col">
                        <label>Discount Amount</label>
                        <input class="form-control" id="discount_amt" type="number" name="discount_amt" autocomplete="off">
                      </div>
                    </div>
                  </div>


                  <!-- <button class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false" onclick="addAccountItemModal()" data-toggle="modal" data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button> -->
                </div>
                <div class="row">
                  <div class="col-sm-12">

                    <button id="btnQuotation" class="btn btn-warning float-left btnQuotation" type="button" onclick="generateQuotation();" style="margin-right: 5px;display:none">
                      <i class="cil-print"></i> Print Quotation
                    </button>

                    <!-- <button id="btnQuotation1" class="btn btn-info float-left btnQuotation" data-backdrop="static" data-keyboard="false"  data-toggle="modal" data-target="#templateModal" type="button" style="margin-right: 5px;display:none">
                      <i class="cil-save"></i> Save Quotation
                    </button> -->

                    <!-- <button id="btnQuotation1" class="btn btn-info float-left btnQuotation" type="button" onclick="saveQuotation();" style="margin-right: 5px;display:none">
                      <i class="cil-save"></i> Save Quotation
                    </button> -->

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
              </form>
            </div>


            <div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation" style="display: none;">
              <style>
                .color-test {
                  background-color: red;
                }
              </style>

              <div class="row">
                <button type="button" class="btn btn-warning float-right no-print" onclick="printlo()" style="margin-right: 5px;">
                  <span><i class="fa fa-print"></i> Print</span>
                </button>
                <a href="javascript:void(0);" onclick="cancelQuotationPrintMode()" class="btn btn-danger  no-print">Cancel</a>
              </div>

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
                  <div id="fromFirm">
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

<div id="templateModal" class="modal fade" role="dialog">
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
              <label>Template Name</label>
              <input type="text" id="template_name" class="form-control" name="template_name"/>
            </div>
          </div>



      </div>
      </form>
      <div class="modal-footer">
        <button type="button" id="btnClose2" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success float-right" onclick="saveQuotation()">Save
          <div class="overlay" style="display:none">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </button>
      </div>
    </div>

  </div>
</div>

@endsection

@section('javascript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
<script src="{{ asset('js/jquery.print.js') }}"></script>
<script>
  function quotationGeneratorMode() {
    $("#quotation_list").hide();
    $("#quotation_generator").show();
  }

  document.getElementById('ddl_firm_name').onchange = function() {
    if ($("#ddl_firm_name").val() == 1) {
      $("#fromFirm").show();
    } else {
      $("#fromFirm").hide();
    }
  };

  document.getElementById('ddl_discount').onchange = function() {
    if ($("#ddl_discount").val() == 1) {
      $(".div_discount").show();
    } else {
      $(".div_discount").hide();
    }
  };

  function loadQuotationTemplate() {

    if ($("#date").val() == "") {
      Swal.fire('Notice!', 'Please select quotation date ', 'warning');
      return;
    }

    if ($("#bill_to").val() == "") {
      Swal.fire('Notice!', 'Please enter name', 'warning');
      return;
    }


    $("#span_date").html($("#date").val());
    $("#p-quo-client-name").html($("#bill_to").val());

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
        $(".btnQuotation").show();
        // $("#quotation_no").val(data.parameter.parameter_value_1);
        $('#tbl-bill-create').html(data.view);
        // $('#tbl-print-quotation').html(data.view2);
        // $('ul.pagination').replaceWith(data.links);
      }
    });
  }

  function saveQuotation() {

    if($("#template_name").val() == "")
    {
      alert($("#template_name").val());
      return;
    }

    $('.btn-submit').attr('disabled', true);

    var bill_list = [];
    var bill = {};

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.each($("input[class='cat_item']:checked"), function() {

      itemID = $(this).val();

      account_item_id = parseFloat($("#account_item_id_" + itemID).val());
      need_approval = parseFloat($("#need_approval_" + itemID).val());
      amount = parseFloat($("#quo_amt_" + itemID).val());
      cat_id = parseFloat($("#cat_" + itemID).val());
      min = parseFloat($("#min_" + itemID).val());
      max = parseFloat($("#max_" + itemID).val());

      bill = {
        account_item_id: account_item_id,
        need_approval: need_approval,
        cat_id: cat_id,
        amount: amount,
        min: min,
        max: max
      };

      bill_list.push(bill);

    });


    var form_data = new FormData();
    form_data.append("bill_list", JSON.stringify(bill_list));
    form_data.append("template_id", $("#ddl_quotation_template").val());
    form_data.append("loan_sum", $("#loan_sum").val());
    form_data.append("date", $("#date").val());
    form_data.append("bill_to", $("#bill_to").val());
    form_data.append("template_name", $("#template_name").val());
    form_data.append("purchase_price", $("#purchase_price").val());

    $.ajax({
      type: 'POST',
      url: '/saveQuotationTemplate',
      data: form_data,
      processData: false,
      contentType: false,
      success: function(data) {

        console.log(data);
        // $(".btnQuotation").show();
        // $("#quotation_no").val(data.parameter.parameter_value_1);
        // $('#tbl-bill-create').html(data.view);
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
    var Total = 0;

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
        <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas(amount.toFixed(2)) + `</td>
        <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas((amount * 0.06).toFixed(2)) + `</td>
        <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas((parseFloat(amount) + parseFloat((amount * 0.06).toFixed(2))).toFixed(2)) + `</td>

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
    Total += subTotal;
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
  <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas(amount.toFixed(2)) + `</td>
  <td style="text-align: right;border-right: 1px solid black;">-</td>
  <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas(amount.toFixed(2)) + `</td>

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

    Total += subTotal;
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
  <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas(amount.toFixed(2)) + `</td>
  <td style="text-align: right;border-right: 1px solid black;">-</td>
  <td style="text-align: right;border-right: 1px solid black;">` + numberWithCommas(amount.toFixed(2)) + `</td>

</tr>
`;

      iCount += 1;

    });


    Total += subTotal;
   

    strHtml += `
<tr style="">
<td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
<td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="3">` + numberWithCommas(subTotal.toFixed(2)) + `</td>
</tr>
<tr>
    <td><h4><b>Total (Final) :</b></h4> </td>
    <td style="text-align:right" colspan="5"> ` + numberWithCommas(Total.toFixed(2)) + `</td>

  </tr>


`;

    var discount= 0;

    if ($("#discount_amt").val() != 0 && $("#discount_amt").val() != '' && $("#discount_amt").val() != null)
    {
      discount = parseFloat($("#discount_amt").val());
      Total = Total- discount;
    }

  if($("#ddl_discount").val() == 1)
  {
    strHtml += `
    <tr>
    <td><h4><b style="color:red">Discount :</b></h4> </td>
    <td style="text-align:right;color:red" colspan="5"> ` + numberWithCommas(discount.toFixed(2)) + `</td>

  </tr>
  <tr>
    <td><h4><b>Total :</b></h4> </td>
    <td style="text-align:right" colspan="5"> ` + numberWithCommas(Total.toFixed(2)) + `</td>

  </tr>
    `;
  }

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