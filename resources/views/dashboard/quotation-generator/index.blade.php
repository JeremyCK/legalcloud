@extends('dashboard.base')
<style>
    .tbl-bill>tbody>tr>td,
    .tbl-bill>tfoot>tr>td,
    .tbl-bill>tfoot>tr>th,
    .tbl-bill>thead>tr>td,
    .tbl-bill>thead>tr>th {
        border-top: 1px solid #f4f4f4;
        padding: 3px 10px 3px 10px !important;
        vertical-align: middle;
    }

    .crete_bill_input {
        height: calc(1.5em + 0.35rem) !important;
        padding: 0px 10px 0px 10px !important;
    }
</style>
@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">

                    <div id="quotation_list" class="card">
                        <div class="card-header">
                            <h4>Quotation Template</h4>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif
                            <div class="row">
                                <div class="col-sm-12">
                                    <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0);"
                                        onclick="quotationGeneratorMode(1)">
                                        <i class="cil-plus"> </i>Create new Quotation
                                    </a>
                                </div>

                            </div>
                            <br />
                            <table class="table table-striped table-bordered datatable">
                                <tbody id="tbl-quotation_">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Template Name</th>
                                            @if ($current_user->menuroles == 'admin')
                                                <th>Owner</th>
                                            @endif
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    @if (count($QuotationGeneratorMain))
                                        @foreach ($QuotationGeneratorMain as $index => $quotation)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $quotation->name }}</td>

                                                @if ($current_user->menuroles == 'admin')
                                                    <td>{{ $quotation->user }}</td>
                                                @endif
                                                <td class="text-center">
                                                    <a href="javascript:void(0)"
                                                        onclick="loadSavedQuotation('{{ $quotation->id }}');"
                                                        class="btn btn-primary shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Edit"><i class="cil-pencil"></i></a>
                                                    <a href="javascript:void(0)"
                                                        onclick="deleteSavedQuotation('{{ $quotation->id }}');"
                                                        class="btn btn-danger shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Edit"><i class="cil-x"></i></a>

                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="5">No saved template</td>
                                        </tr>
                                    @endif


                                </tbody>
                            </table>
                            {!! $QuotationGeneratorMain->links() !!}
                        </div>
                    </div>

                    <div id="quotation_generator" class="card" style="display:none">
                        <div class="card-header">
                            <h4>Quotation Generator</h4>
                        </div>
                        <div class="card-body">
                            <div id="dQuotationInvoice">
                                <form id="form_quotation_main" enctype="multipart/form-data">


                                    <div class="form-group row">
                                        <input class="form-control" id="quotation_no" type="hidden" name="quotation_no">
                                        <input class="form-control" id="operation_mode" type="hidden" name="operation_mode"
                                            value="1">
                                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                            <div class="form-group row update_mode">
                                                <div class="col">
                                                    <label>Template name</label>
                                                    <input class="form-control" type="text" name="template_name_field"
                                                        id="template_name_field" value="">
                                                    <input class="form-control" type="hidden" name="template_id"
                                                        id="template_id" value="">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Quotation Template</label>
                                                    <select id="ddl_quotation_template" class="form-control"
                                                        name="ddl_quotation_template">
                                                        <option value="0">-- Select Quotation Template --</option>
                                                        @foreach ($quotation_template as $index => $account)
                                                            <option value="{{ $account->id }}">{{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Loan Sum</label>
                                                    <input class="form-control" id="loan_sum" type="number"
                                                        name="loan_sum" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Date</label>
                                                    <input class="form-control" type="date" id="date" name="date"
                                                        autocomplete="off">
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
                                                    <input class="form-control" type="text" name="bill_to"
                                                        id="bill_to" value="Client">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Purchase Price</label>
                                                    <input class="form-control" type="number" id="purchase_price"
                                                        name="purchase_price" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>With firm name</label>
                                                    <select id="ddl_firm_name" class="form-control" name="ddl_firm_name">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row div_discount" style="display:none;">
                                                <div class="col">
                                                    <label>Discount Amount</label>
                                                    <input class="form-control" id="discount_amt" type="number"
                                                        name="discount_amt" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>


                                        <!--  <button class="btn btn-info float-right quotation" data-backdrop="static" data-keyboard="false" onclick="addAccountItemModal()" data-toggle="modal" data-target="#accountItemModal" type="button"><i class="cil-plus"></i>Add </button> -->
                                    </div>
                                    <div class="row" style="margin-bottom:10px;">

                                        <div class="col-sm-12">
                                            <button class="btn btn-danger float-left" type="button"
                                                onclick="cancelGeneratorMode();">
                                                Cancel
                                            </button>
                                            <button class="btn btn-primary float-right" type="button"
                                                onclick="loadQuotationTemplate('');">
                                                <i class="cil-caret-right"></i> Generate
                                            </button>
                                        </div>
                                    </div>

                                    <hr />
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <button id="btnPrintQuotation" class="btn btn-warning float-left btnQuotation"
                                                type="button" onclick="generateQuotation();"
                                                style="margin-right: 5px;display:none">
                                                <i class="cil-print"></i> Print Quotation
                                            </button>

                                            <button id="btnSaveQuotation" class="btn btn-info float-right btnQuotation"
                                                onclick="updateQuotation();" type="button"
                                                style="margin-right: 5px;display:none">
                                                <i class="cil-save"></i> Update Quotation Template
                                            </button>

                                            <button id="btnSaveQuotationAsNew"
                                                class="btn btn-info float-right btnQuotation" data-backdrop="static"
                                                data-keyboard="false" data-toggle="modal" data-target="#templateModal"
                                                type="button" style="margin-right: 5px;display:none">
                                                <i class="cil-save"></i> Save As New Template
                                            </button>

                                            <!-- <button id="btnQuotation1" class="btn btn-info float-left btnQuotation" type="button" onclick="saveQuotation();" style="margin-right: 5px;display:none">
                              <i class="cil-save"></i> Save Quotation
                            </button> -->


                                        </div>

                                    </div>
                                    <br />
                                    <table id="tbl-bill" class="table table-striped table-bordered datatable  tbl-bill">
                                        <tbody id="tbl-bill-create">



                                        </tbody>
                                    </table>
                                </form>
                            </div>


                            <div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation"
                                style="display: none;padding-top:30px;padding-bottom:10px;padding-left:50px;padding-right:50px;background-color:white !important; border:1px solid black">
                                <style>
                                    .color-test {
                                        background-color: red;
                                    }
                                </style>

                                <div class="row">
                                    <button type="button" class="btn btn-warning float-right no-print"
                                        onclick="printlo()" style="margin-right: 5px;">
                                        <span><i class="fa fa-print"></i> Print</span>
                                    </button>
                                    <a href="javascript:void(0);" onclick="cancelQuotationPrintMode()"
                                        class="btn btn-danger  no-print">Cancel</a>
                                </div>


                                <!-- <div class="row">
                        <div class="col-12">
                          <h2 class="page-header">
                            Quotation
                            <small class="pull-right">Date: <span id="span_date"></span> </small>
                          </h2>
                        </div>
                      </div> -->

                                <div class="row" style="border-bottom: 1px solid #0066CC ">

                                    <div class="col-4">
                                        {{-- <span class="print-formal" style="display: none">
                    <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                    Unit 26-6, Oval Damansara, Jalan Damansara<br>
                    60000 Kuala Lumpur<br>
                    <b>Phone</b>: 03-7727 1818 <b>Fax</b>: 03-7732 8818<br>
                    <b>Email</b>: legal@lhyeo.com

                  </span> --}}
                                        <address class="print-formal" style="display: none">
                                            <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>
                                            Advocates & Solicitors<br>
                                            {!! $Branch->address !!}<br>
                                            <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
                                            <b>Email</b>: {{ $Branch->email }}
                                        </address>
                                    </div>

                                    <div class="col-4">

                                        <h4 class="text-center"
                                            style="position:absolute;bottom:0;left:40%;font-weight:bold">
                                            Quotation
                                            </h2>

                                    </div>

                                    <div class="col-4">
                                        <span class="text-center" style="position:absolute;bottom:0;right:0">
                                            <small class="pull-right"><b>Date</b>: <span id="print_payment_date"></span>
                                            </small>
                                        </span>
                                    </div>

                                </div>

                                <div class="row invoice-info">
                                    <div class="col-sm-6 invoice-col">
                                        <b>To</b> <strong class="text-blue"><span
                                                id="p-quo-client-name"></span></strong><br>
                                    </div>

                                    <div class="col-sm-6 invoice-col">
                                        <span class="pull-right"><b>Quotation No: <span
                                                    id="quotation_no">{{ $current_user->nick_name }}_{{ $parameter->parameter_value_1 }}</span></b></span>

                                    </div>




                                    <div id="tr_purchase_price" class="col-sm-6 invoice-col">
                                        <b>Purchase Price:</b> <strong class="text-blue"><span
                                                id="span_purchase_price"></span></strong><br>
                                    </div>

                                    <div id="tr_loan_sum" class="col-sm-6 invoice-col">
                                        <b>Loan Sum:</b> <strong class="text-blue"><span
                                                id="span_loan_sum"></span></strong><br>
                                    </div>


                                    <!-- <table class="table table-border">
                          <tr id="tr_purchase_price" width="85%" style="padding:0px !important;padding-left:10px  !important;">
                            <td style="padding:0px !important;padding-left:20px  !important"><b>Purchase Price:</b></td>
                            <td style="padding:0px !important;padding-left:10px  !important">
                              <span id="span_purchase_price"></span>
                            </td>
                          </tr>
                          <tr id="tr_loan_sum" style="padding:0px !important;padding-left:10px  !important;">
                            <td style="padding:0px !important;padding-left:20px  !important"><b>Loan Sum:</b></td>
                            <td style="padding:0px !important;padding-left:10px  !important">
                              <span id="span_loan_sum"></span>
                            </td>
                          </tr>

                        </table> -->
                                    <!-- <div class="col-sm-12 invoice-col">
                          <div class="row no-margin">
                            <div class="col-md-6 col-lg-6">
                              <span class="pull-left"><b>Quotation No: <span id="quotation_no">{{ $current_user->nick_name }}_{{ $parameter->parameter_value_1 }}</span></b></span>
                            </div>
                            <div class="col-md-6 col-lg-6 pull-right">
                            </div>
                          </div>
                        </div> -->


                                </div>
                                <!-- /.row -->

                                <!-- Table row -->
                                <div class="row">
                                    <?php $total_amt = 0; ?>
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped" id="tbl-print-quotation"
                                            style="font-size:10px !important;">

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
                                <input type="text" id="template_name" class="form-control" name="template_name" />
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
        function quotationGeneratorMode(mode) {
            $("#operation_mode").val(mode);
            $("#quotation_list").hide();
            $("#quotation_generator").show();

            buttonEventController(mode);
        }

        $("#date").val(new Date());

        function buttonEventController(mode) {
            if (mode == 2) {
                $("#btnSaveQuotationAsNew").show();
                $(".update_mode").show();
            } else {
                $("#btnSaveQuotation").hide();
                $("#btnSaveQuotationAsNew").hide();
                $("#btnPrintQuotation").hide();
                $('#form_quotation_main')[0].reset();
                $('#tbl-bill-create').html('');
                $(".update_mode").hide();
            }
        }

        function updateQuotationSpan(id)
        {
            console.log($("#desc_span_" + id).val());
            
            $("#details_span_" + id).html($("#desc_span_" + id).val());
        }

        function cancelGeneratorMode() {
            $("#quotation_list").show();
            $("#quotation_generator").hide();
        }

        document.getElementById('ddl_firm_name').onchange = function() {
            if ($("#ddl_firm_name").val() == 1) {
                $("#fromFirm").show();
                $(".print-formal").show();
            } else {
                $("#fromFirm").hide();
                $(".print-formal").hide();
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

            // if ($("#date").val() == "") {
            //   Swal.fire('Notice!', 'Please select quotation date ', 'warning');
            //   return;
            // }

            // if ($("#bill_to").val() == "") {
            //   Swal.fire('Notice!', 'Please enter name', 'warning');
            //   return;
            // }


            $("#print_payment_date").html($("#date").val());
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
                    // $(".btnQuotation").show();

                    $("#btnSaveQuotationAsNew").show();
                    $("#btnPrintQuotation").show();
                    // $("#quotation_no").val(data.parameter.parameter_value_1);
                    $('#tbl-bill-create').html(data.view);
                    // $('#tbl-print-quotation').html(data.view2);
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function saveQuotation() {

            if ($("#template_name").val() == "") {
                // alert($("#template_name").val());
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
                item_desc = $("#item_desc_" + itemID).val();

                bill = {
                    account_item_id: account_item_id,
                    need_approval: need_approval,
                    item_desc: item_desc,
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
            form_data.append("template_date", $("#date").val());
            form_data.append("bill_to", $("#bill_to").val());
            form_data.append("template_name", $("#template_name").val());
            form_data.append("purchase_price", $("#purchase_price").val());
            form_data.append("bln_discount", $("#ddl_discount").val());
            form_data.append("discount", $("#discount_amt").val());

            $.ajax({
                type: 'POST',
                url: '/saveQuotationTemplate',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    Swal.fire('Success!', 'Saved', 'success');
                    location.reload();
                    // $(".btnQuotation").show();
                    // $("#quotation_no").val(data.parameter.parameter_value_1);
                    // $('#tbl-bill-create').html(data.view);
                    // $('#tbl-print-quotation').html(data.view2);
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function updateQuotation() {


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
                    max: max,
                    itemID: itemID
                };

                bill_list.push(bill);

            });

            console.log(bill_list);

            var form_data = new FormData();
            form_data.append("bill_list", JSON.stringify(bill_list));
            form_data.append("template_id", $("#ddl_quotation_template").val());
            form_data.append("loan_sum", $("#loan_sum").val());
            form_data.append("template_date", $("#date").val());
            form_data.append("bill_to", $("#bill_to").val());
            form_data.append("template_name", $("#template_name_field").val());
            form_data.append("purchase_price", $("#purchase_price").val());
            form_data.append("bln_discount", $("#ddl_discount").val());
            form_data.append("discount", $("#discount_amt").val());

            $.ajax({
                type: 'POST',
                url: '/updateQuotationTemplate/' + $("#template_id").val(),
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    Swal.fire('Success!', 'Saved', 'success');
                    location.reload();
                    // $(".btnQuotation").show();
                    // $("#quotation_no").val(data.parameter.parameter_value_1);
                    // $('#tbl-bill-create').html(data.view);
                    // $('#tbl-print-quotation').html(data.view2);
                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function deleteSavedQuotation(id) {

            Swal.fire({
                title: 'Delete this template?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteSavedQuotation/' + id,
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(data) {

                            Swal.fire('Success!', 'Deleted', 'success');
                            location.reload();

                        }
                    });
                }
            })

        }

        function loadSavedQuotation(id) {
            $.ajax({
                type: 'POST',
                url: '/loadSavedQuotationTemplateGenerator/' + id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {

                    if (data.QuotationGeneratorMain != null) {
                        var obj = data.QuotationGeneratorMain;

                        $("#ddl_quotation_template").val(obj.template_id);
                        $("#loan_sum").val(obj.loan_sum);
                        $("#purchase_price").val(obj.purchase_price);
                        $("#date").val(obj.template_date);
                        $("#bill_to").val(obj.bill_to);
                        $("#ddl_discount").val(obj.bln_discount);
                        $("#discount_amt").val(obj.discount);
                        $("#template_name_field").val(obj.name);
                        $("#template_id").val(obj.id);

                        if (obj.bln_discount == 1) {
                            $(".div_discount").show();
                        }
                    }

                    quotationGeneratorMode(2);

                    $(".btnQuotation").show();
                    // $("#quotation_no").val(data.parameter.parameter_value_1);
                    $('#tbl-bill-create').html(data.view);


                    console.log(data);
                }
            });
        }

        function generateQuotation() {

            $("#tr_purchase_price").hide();
            $("#tr_loan_sum").hide();

            if ($("#purchase_price").val() != "" && $("#purchase_price").val() > 0 && $("#purchase_price").val() != null) {
                $("#span_purchase_price").html("RM " + numberWithCommas($("#purchase_price").val()));
                $("#tr_purchase_price").show();
            }


            if ($("#loan_sum").val() != "" && $("#loan_sum").val() > 0 && $("#loan_sum").val() != null) {
                $("#span_loan_sum").html("RM " + numberWithCommas($("#loan_sum").val()));
                $("#tr_loan_sum").show();
            }

            $("#print_payment_date").html($("#date").val());
            $("#p-quo-client-name").html($("#bill_to").val());

            var strHtml = `  
    <tr style="padding:0px !important;border: 1px solid black;padding-left:10px;"> 
    <!-- <th>#</th> -->
    <th style="padding:0px !important;border-top: 1px solid black;border-bottom: 1px solid black;padding-left:10px !important;">Description</th>
    <th style="padding:0px !important;border-top: 1px solid black;border-bottom: 1px solid black;padding-right:10px !important;" class="text-right">Amount (RM)</th>
    <th style="padding:0px !important;border-top: 1px solid black;border-bottom: 1px solid black;padding-right:10px !important;" class="text-right">SST (6%)</th>
    <th style="padding:0px !important;border-top: 1px solid black;border-bottom: 1px solid black;padding-right:10px !important;" class="text-right">Total (RM)</th>
  </tr>
  <tr >
    <td colspan="5" style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
      <span><b style="color:white;font-size:15px">Professional fees</b></span>
    </td>
  </tr>
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
                item_desc = $("#item_desc_" + itemID).val();
                item_desc = $("#desc_span_" + itemID).val();
                subTotal += parseFloat(amount) + parseFloat((amount * 0.06));

                if (item_desc != '') {
                    item_desc = '<hr style="margin-top:10px !important;margin-bottom:10px !important" />' + item_desc;
                }



                strHtml +=
                    `
  <tr style="padding:0px !important;border: 1px solid black">
      <td style="border-left: 1px solid black;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;padding-bottom:10px !important">` +
                    iCount + `. ` + account_name + item_desc + `</td>
  <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas(amount.toFixed(2)) +
                    `</td>
        <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas((amount *
                        0.06).toFixed(2)) +
                    `</td>
        <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas(
                        (parseFloat(amount) + parseFloat((amount * 0.06).toFixed(2))).toFixed(2)) + `</td>

      </tr>
      `;

                iCount += 1;

            });

            strHtml +=
                `
  <tr style="padding:0px !important;border: 1px solid black">
    <td class="text-left" style="padding:0px !important;text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;padding-left:10px !important;" colspan="2">Subtotal:</td>
    <td style="padding:0px !important;text-align:right;border-top: 1px solid black;border-right: 1px solid black;padding-right:10px !important" colspan="3">` +
                numberWithCommas(subTotal.toFixed(2)) + `</td>
  </tr>
    `;
            Total += subTotal;
            subTotal = 0;
            iCount = 1;

            strHtml += `
    <tr >
    <td colspan="5" style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
      <span><b style="color:white;font-size:15px">Stamp duties</b></span>
    </td>
  </tr>
    `;

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

                strHtml +=
                    `
  <tr style="padding:0px !important;border: 1px solid black">
  <td style="border-left: 1px solid black;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;">` +
                    iCount + `. ` + account_name + `</td>
    
  <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas(amount.toFixed(2)) + `</td>
  <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">-</td>
  <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas(amount.toFixed(2)) + `</td>

</tr>
`;

                iCount += 1;

            });

            strHtml +=
                `
  <tr style="padding:0px !important;border: 1px solid black">
<td class="text-left" style="padding:0px !important;text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;padding-left:10px !important;" colspan="2">Subtotal:</td>
<td style="padding:0px !important;text-align:right;border-top: 1px solid black;border-right: 1px solid black;padding-right:10px !important" colspan="3">` +
                numberWithCommas(subTotal.toFixed(2)) + `</td>
</tr>
`;

            Total += subTotal;
            subTotal = 0;
            iCount = 1;

            strHtml += `
    <tr >
    <td colspan="5" style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
      <span><b style="padding:0px !important;color:white;font-size:15px;padding-right:10px !important;">Disbursement</b></span>
    </td>
  </tr>
    `;

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

                strHtml +=
                    `
  <tr style="padding:0px !important;border: 1px solid black">
<td style="border-left: 1px solid black;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;">` +
                    iCount + `. ` + account_name + `</td>
 <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas(amount.toFixed(2)) + `</td>
  <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">-</td>
  <td style="padding:0px !important;text-align: right;border-right: 1px solid black;padding-right:10px !important;">` +
                    numberWithCommas(amount.toFixed(2)) + `</td>

</tr>
`;

                iCount += 1;

            });


            Total += subTotal;


            strHtml +=
                `
  <tr style="padding:0px !important;border: 1px solid black">
<td class="text-left" style="padding:0px !important;text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;padding-left:10px !important;">Subtotal:</td>
<td style="padding:0px !important;text-align:right;border-top: 1px solid black;border-right: border-bottom: 1px solid black;1px solid black;padding-right:10px !important;" colspan="3">` +
                numberWithCommas(subTotal.toFixed(2)) +
                `</td>
</tr>
  <tr style="padding:0px !important;border: 1px solid black">
    <td style="padding:0px !important;border-top: 1px solid black;border-bottom: 1px solid black;padding-left:10px !important;"><span><b>Total (Final) :</b></span></td>
    <td style="padding:0px !important;text-align:right;padding-right:10px !important;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="5"> <span><b > ` +
                numberWithCommas(Total.toFixed(2)) + `</b></span></td>

  </tr>


`;

            var discount = 0;

            if ($("#discount_amt").val() != 0 && $("#discount_amt").val() != '' && $("#discount_amt").val() != null) {
                discount = parseFloat($("#discount_amt").val());
                Total = Total - discount;
            }

            if ($("#ddl_discount").val() == 1) {
                strHtml +=
                    `
  <tr style="padding:0px !important;border: 1px solid black">
    <td style="padding-left:10px !important;padding:0px !important;border-top: 1px solid black;border-bottom: 1px solid black;padding-left:10px !important;"><span><b style="color:red">Discount :</b></span> </td>
    <td style="padding:0px !important;text-align:right;color:red;padding-right:10px !important;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="5"> <b "> ` +
                    numberWithCommas(discount.toFixed(2)) + `</b></td>

  </tr>
  <tr style="padding:0px !important;border: 1px solid black">
    <td style="padding-left:10px !important;padding:0px !important;padding-left:10px !important;"><span><b>Total :</b></span> </td>
    <td style="padding:0px !important;text-align:right;padding-right:10px !important;" colspan="5"><b >  ` +
                    numberWithCommas(Total.toFixed(2)) + `</b></td>

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
            formData.append('quotation_no', '{{ $current_user->nick_name }}' + '_' +
                '{{ $parameter->parameter_value_1 }}');

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

        function updateAllQuotationAmount() {
            var sum_total = 0;

            var sum_cat = 0;

            $.each($("input[name='Professional fees']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                console.log(amount);
                sst = 0;

                if ($("#cat_" + itemID).val() == 1) {
                    sst = amount * 0.06;
                }

                sum_cat += (parseFloat(amount) + sst);
                sum_total += (parseFloat(amount) + sst);

                $("#sst_" + itemID).html(numberWithCommas(sst.toFixed(2)));
                value = parseFloat(amount) + sst;
                $("#amt_sst_" + itemID).html(numberWithCommas(value.toFixed(2)));

                $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));


            });

            $("#int_sub_total_1").val(sum_cat.toFixed(2));
            $("#sub_total_1").html(numberWithCommas(sum_cat.toFixed(2)));

            var sum_cat = 0;

            $.each($("input[name='Stamp duties']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                console.log(amount);
                sst = 0;

                if ($("#cat_" + itemID).val() == 1) {
                    sst = amount * 0.06;
                }

                sum_cat += (parseFloat(amount) + sst);
                sum_total += (parseFloat(amount) + sst);

                $("#sst_" + itemID).html(numberWithCommas(sst.toFixed(2)));
                value = parseFloat(amount) + sst;
                $("#amt_sst_" + itemID).html(numberWithCommas(value.toFixed(2)));

                $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));


            });

            console.log(sum_cat);

            $("#int_sub_total_2").val(sum_cat.toFixed(2));
            $("#sub_total_2").html(numberWithCommas(sum_cat.toFixed(2)));

            var sum_cat = 0;

            $.each($("input[name='Disbursement']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                console.log(amount);
                sst = 0;

                if ($("#cat_" + itemID).val() == 1) {
                    sst = amount * 0.06;
                }

                sum_cat += (parseFloat(amount) + sst);
                sum_total += (parseFloat(amount) + sst);

                $("#sst_" + itemID).html(numberWithCommas(sst.toFixed(2)));
                value = parseFloat(amount) + sst;
                $("#amt_sst_" + itemID).html(numberWithCommas(value.toFixed(2)));

                $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));


            });

            console.log(sum_cat);

              $.each($("input[name='Reimbursement']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                console.log(amount);
                sst = 0;

                if ($("#cat_" + itemID).val() == 4) {
                    // sst = amount * 0.06;
                    sst = amount * sst_percentage;
                }
                

                sum_cat += (parseFloat(amount) + sst);
                sum_total += (parseFloat(amount) + sst);

                $("#sst_" + itemID).html(numberWithCommas(sst.toFixed(2)));
                value = parseFloat(amount) + sst;
                $("#amt_sst_" + itemID).html(numberWithCommas(value.toFixed(2)));

                $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));


            });

            $("#int_sub_total_3").val(sum_cat.toFixed(2));
            $("#sub_total_3").html(numberWithCommas(sum_cat.toFixed(2)));


            $("#total_sum_bill").html(numberWithCommas(sum_total.toFixed(2)));
            $("#span_total_create_bill").html(numberWithCommas(sum_total.toFixed(2)));

        }
    </script>
@endsection
