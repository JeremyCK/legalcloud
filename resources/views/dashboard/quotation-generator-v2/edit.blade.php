@section('css')
    <link href="{{ asset('css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-coreui.css') }}" rel="stylesheet">
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

        .tbl-print-quotation>tbody>tr>td,
        .tbl-print-quotation>tfoot>tr>td,
        .tbl-print-quotation>tfoot>tr>th,
        .tbl-print-quotation>thead>tr>td,
        .tbl-print-quotation>thead>tr>th {
            /* border-top: 1px solid #f4f4f4;
                                padding: 3px 3px 3px 3px !important; */
            /* width: 100%; */
            /* border: 1px solid black; */
            font-size: 12px;
            padding: 3px 10px 3px 10px !important;
            /* padding: 10px; */
        }

        .crete_bill_input {
            height: calc(1.5em + 0.35rem) !important;
            padding: 0px 10px 0px 10px !important;
            font-size: 12px !important;
        }

        @media print {
            .pagebreak {
                page-break-before: always;
            }

            /* page-break-after works, as well */
        }
    </style>

    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
@endsection
@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">

                                <div class="col-6">
                                    <h4>Edit Quotation Generator</h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="/quotation-generator">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

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
                                                    id="template_name_field" value="{{ $QuotationGeneratorMain->name }}">
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
                                                        <option @if ($QuotationGeneratorMain->template_id == $account->id) selected @endif
                                                            value="{{ $account->id }}">{{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Loan Sum</label>
                                                <input class="form-control" id="loan_sum" type="number" name="loan_sum"
                                                    value="{{ $QuotationGeneratorMain->loan_sum }}" autocomplete="off">
                                            </div>
                                        </div>



                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Discount</label>
                                                <select id="ddl_discount" class="form-control" name="ddl_discount">
                                                    <option value="0"
                                                        @if ($QuotationGeneratorMain->bln_discount == 0) selected @endif>No</option>
                                                    <option value="1"
                                                        @if ($QuotationGeneratorMain->bln_discount == 1) selected @endif>Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row div_discount" style="display:none;">
                                            <div class="col">
                                                <label>Discount Amount</label>
                                                <input class="form-control" id="discount_amt" type="number"
                                                    value="{{ $QuotationGeneratorMain->discount }}" name="discount_amt"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Bill To</label>
                                                <input class="form-control" type="text" name="bill_to" id="bill_to"
                                                    value="{{ $QuotationGeneratorMain->bill_to }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Date</label>
                                                <input class="form-control" type="date" id="date" name="date"
                                                    value="{{ $QuotationGeneratorMain->template_date }}"
                                                    autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Purchase Price</label>
                                                <input onkeyup="updateDynamicMinMax()" class="form-control" type="number" id="purchase_price"
                                                    value="{{ $QuotationGeneratorMain->purchase_price }}"
                                                    name="purchase_price" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>With firm name</label>
                                                <select id="ddl_firm_name" class="form-control" name="ddl_firm_name">
                                                    <option value="0"
                                                        @if ($QuotationGeneratorMain->bln_firmname == 0) selected @endif>No</option>
                                                    <option value="1"
                                                        @if ($QuotationGeneratorMain->bln_firmname == 1) selected @endif>Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row" style="display: none">
                                            <div class="col">
                                                <label>SST</label>
                                                <select class="form-control" id="ddl_sst" onchange="updateSSTRate()"
                                                    name="ddl_sst">
                                                    <option value="8"
                                                        @if ($QuotationGeneratorMain->sst_rate == 8) selected @endif> 8%</option>
                                                    <option value="6"
                                                        @if ($QuotationGeneratorMain->sst_rate == 6) selected @endif> 6%</option>
                                                    
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row show_firm" style="display:none;">
                                            <div class="col">
                                                <label>Branch</label>
                                                <select id="ddl_branch" class="form-control " name="ddl_branch">
                                                    @foreach ($branchInfo as $index => $bran)
                                                        <option value="{{ $bran->id }}">{{ $bran->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                    </div>

                                </div>

                                <div class="row" style="margin-bottom:10px;">

                                    <div class="col-sm-12">
                                        <button id="btnSaveQuotationAsNew"
                                            onclick="updateQuotation({{ $QuotationGeneratorMain->id }})"
                                            class="btn btn-info float-righleftt btnQuotation" data-backdrop="static"
                                            data-keyboard="false" data-toggle="modal" data-target="#templateModal"
                                            type="button">
                                            <i class="cil-save"></i> Update Template
                                        </button>

                                        <button onclick="copyTemplate({{ $QuotationGeneratorMain->id }})"
                                            class="btn btn-success float-righleftt btnQuotation" type="button"> <i
                                                class="cil-search"></i> Copy Template
                                        </button>

                                        <button class="btn btn-primary float-right" type="button"
                                            onclick="loadQuotationTemplate('');">
                                            <i class="cil-caret-right"></i> Generate
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div id="div-quotation-edit" class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Quotation Generator</h4>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-success float-right btnQuotation mr-3"
                                        type="button" onclick="showGenerateQuotationModal({{ $QuotationGeneratorMain->id }});">
                                        <i class="cil-cloud-download"></i> Download Quotation
                                    </button>
                                    <button id="btnPrintQuotation" class="btn btn-warning float-right btnQuotation"
                                        type="button" onclick="generateQuotation();">
                                        <i class="cil-print"></i> Print Quotation
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="tbl-bill" class="table table-striped table-bordered datatable  tbl-bill">
                                <tbody id="tbl-bill-create">

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div id="div-quotation-preview" class="col-12" style="display:none;">

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Quotation Preview</h4>
                                </div>
                                <div class="col-6">
                                    {{-- <button type="button" class="btn btn-warning float-right no-print"
                                    onclick="PrintAreaQuotation()" style="margin-right: 5px;">
                                    <span><i class="fa fa-print"></i> Print</span> --}}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="row mb-5">

                                <div class="col-6">
                                    <a href="javascript:void(0);" onclick="cancelQuotationPreview()"
                                        class="btn btn-danger float-left no-print">Cancel</a>
                                </div>
                                <div class="col-6">

                                    <button type="button" class="btn btn-warning float-right no-print"
                                        onclick="PrintAreaQuotation()" style="margin-right: 5px;">
                                        <span><i class="fa fa-print"></i> Print</span>
                                    </button>
                                </div>
                            </div>

                            <div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation"
                                style="padding-top:20px;padding-bottom:20px;padding-left:40px;padding-right:40px;background-color:white !important; border:1px solid black">



                                <div class="row show_firm" style="display: none">
                                    <div class="col-6">
                                        @foreach ($branchInfo as $index => $bran)
                                            <address id="branch_{{ $bran->id }}"
                                                class="print-formal branch_all branch_{{ $bran->id }}"
                                                @if ($current_user->branch_id != $bran->id) style="display: none" @endif>
                                                <strong style="color: #2d659d">{{ $bran->office_name }}</strong><br>
                                                Advocates & Solicitors<br>
                                                {!! $bran->address !!}<br>
                                                <b>Phone</b>: {{ $bran->tel_no }} <b>Fax</b>: {{ $bran->fax }}<br>
                                                <b>Email</b>: {{ $bran->email }}
                                            </address>
                                        @endforeach

                                    </div>


                                    <div class="col-12">
                                        <hr />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 text-center">
                                        <h4><strong>Estimated Quotation (Draft Only)</strong></h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 text-center">
                                        <hr />
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-4 invoice-col">
                                        <b>To</b> <strong class="text-blue"><span
                                                id="p-quo-client-name"></span></strong><br>

                                        <div class="row" style="display: none" id="span_loan_sum">
                                            <div class="col-6">
                                                <span><b>Loan Sum:</b>
                                            </div>
                                            <div class="col-6 text-right">
                                                <span class="text-bluea" id="quo_loan_sum"></span>
                                            </div>
                                        </div>

                                        <div class="row" style="display: none" id="span_purchase_price">
                                            <div class="col-6">
                                                <span><b>Purchase Price:</b>
                                            </div>
                                            <div class="col-6 text-right">
                                                <span class="text-bluea" id="quo_purchase_price"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4 invoice-col">
                                        {{-- Estimated Quotation (Draft Only)<br /> --}}

                                    </div>

                                    <div class="col-4 invoice-col ">
                                        <div class="row">
                                            <div class="col-6">
                                                <span><b>Quotation No: </b>
                                            </div>


                                            <div class="col-6 text-right">
                                                <span>
                                                    {{ $current_user->nick_name }}_{{ $parameter->parameter_value_1 }}</span>
                                            </div>

                                        </div>


                                        <div class="row" style="display: none" id="span_date">
                                            <div class="col-6">
                                                <span><b>Date:</b>
                                            </div>
                                            <div class="col-6 text-right">
                                                <span class="text-bluea" id="quo_date"></span>
                                            </div>
                                        </div>



                                        {{-- <span style="display: none"><b>Ref No: <span id="ref_no"></span></b>
                                            Quotation/{{ $current_user->name }}</span><br />
                                        <span><b>Quotation No: <span 
                                                    id="quotation_no"></span></b>{{ $current_user->nick_name }}_{{ $parameter->parameter_value_1 }}</span><br /> --}}
                                        {{-- <span style="display: none" id="span_date"><b>Date: </b><span
                                                id="quo_date"></span><br /></span> --}}
                                        {{-- <span style="display: none" id="span_loan_sum"><b>Loan Sum: </b><span class="text-right" id="quo_loan_sum"></span><br /></span> --}}
                                        {{-- <span style="display: none" id="span_purchase_price"><b>Purchase Price: </b><span
                                                id="quo_purchase_price"></span></span> --}}

                                    </div>
                                </div>


                                {{-- <div class="row">

                                    <div class="col-4 invoice-col">
                                        <b>To</b> <strong class="text-blue"><span
                                                id="p-quo-client-name"></span></strong><br>
                                    </div>

                                    <div class="col-4 invoice-col">
                                        Estimated Quotation (Draft Only)<br />

                                    </div>

                                    <div class="col-4 invoice-col">
                                        <span style="display: none"><b>Ref No: <span id="ref_no"></span></b>
                                            Quotation/{{ $current_user->name }}</span><br />
                                        <span><b>Quotation No: <span
                                                    id="quotation_no"></span></b>{{ $current_user->nick_name }}_{{ $parameter->parameter_value_1 }}</span><br />
                                        <span style="display: none" id="span_date"><b>Date: </b><span
                                                id="quo_date"></span><br /></span>
                                        <span style="display: none" id="span_loan_sum"><b>Loan Sum: </b><span
                                                id="quo_loan_sum"></span><br /></span>
                                        <span style="display: none" id="span_purchase_price"><b>Purchase Price: </b><span
                                                id="quo_purchase_price"></span></span>

                                    </div>
                                </div> --}}

                                <div class="row mt-2">
                                    <div class="col-12 table-responsive">
                                        <table class=" tbl-print-quotation " id="tbl-print-quotation"
                                            style="font-size:10px !important;width:100%">

                                        </table>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('dashboard.quotation-generator-v2.modal.modal-account-item')
@endsection

@section('javascript')
    @include('dashboard.quotation-generator-v2.script.src-quotation')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/jquery.print.js') }}"></script>
    <script>
        function addAccountItemModal(catId) {
            $("#ddlAccountItem").val(0);
            $(".cat_all").hide();
            $(".cat_" + catId).show();
            // $("#catID").val(catId);

            // $("#txtCalculateAccountAmount").val(0);
            // $("#txtAmount").val(0);

        }

        function hideSelectedAndMatched(catId) {
            $("#ddlAccountItem").val(0);
            $(".cat_all").hide();
            $(".cat_" + catId).show();
            // $("#catID").val(catId);

            // $("#txtCalculateAccountAmount").val(0);
            // $("#txtAmount").val(0);

        }


        function copyTemplate(id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                text: 'Copy this template?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {

                    window.location.href = '/copyTemplate/' + id;
                }
            })

        }

        // document.getElementById('ddl_firm_name').onchange = function() {
        //     if ($("#ddl_firm_name").val() == 1) {
        //         $(".show_firm").show();
        //     } else {
        //         $(".show_firm").hide();
        //     }
        // };

        // document.getElementById('ddl_branch').onchange = function() {

        //     $(".branch_all").hide();
        //     $(".branch_" + $("#ddl_branch").val()).show();
        // };

        // document.getElementById('date').onchange = function() {

        //     if ($("#date").val() != '') {
        //         $("#quo_date").html($("#date").val());
        //         $("#span_date").show();
        //     } else {
        //         $("#quo_date").html('');
        //         $("#span_date").hide();
        //     }
        // };

        // document.getElementById('loan_sum').onchange = function() {

        //     if ($("#loan_sum").val() != '' && $("#loan_sum").val() != 0) {
        //         $("#quo_loan_sum").html(numberWithCommas($("#loan_sum").val()));
        //         $("#span_loan_sum").show();
        //     } else {
        //         $("#quo_loan_sum").html('0');
        //         $("#span_loan_sum").hide();
        //     }
        // };

        // document.getElementById('purchase_price').onchange = function() {

        //     if ($("#purchase_price").val() != '' && $("#purchase_price").val() != 0) {
        //         $("#quo_purchase_price").html(numberWithCommas($("#purchase_price").val()));
        //         $("#span_purchase_price").show();
        //     } else {
        //         $("#quo_purchase_price").html('0');
        //         $("#span_purchase_price").hide();
        //     }
        // };

        // document.getElementById('bill_to').onchange = function() {

        //     if ($("#bill_to").val() != '') {
        //         $("#p-quo-client-name").html($("#bill_to").val());
        //     } else {
        //         $("#p-quo-client-name").html('');
        //     }
        // };

        loadSavedQuotation({{ $QuotationGeneratorMain->id }});

        $(document).ready(function() {
            $('#ddl_quotation_template').select2({
                placeholder: "Select or type to search Quotation Template",
                allowClear: true,
                width: '100%'
            });
        });


        // var form_data = new FormData();
        // form_data.append("template_id", $("#ddl_quotation_template").val());
        // form_data.append("sst_rate", $("#ddl_sst").val());

        function loadSavedQuotation(id) {

            var form_data = new FormData();
            form_data.append("template_id", $("#ddl_quotation_template").val());
            form_data.append("sst_rate", $("#ddl_sst").val());

            $.ajax({
                type: 'POST',
                url: '/loadSavedQuotationTemplateGenerator/' + id,
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {

                    $('#tbl-bill-create').html(data.view);
                    $('#ddlAccountItem').html(data.view2);
                    console.log(data);
                    updateBillTo();
                    updatePurchasePrice();
                    updateLoanSum();
                    updateDate();
                    updateBranch();
                    updateShowFirm();
                    updateDiscountRow();
                    updateDiscountValue();

                    // $select =$('#ddlAccountItem').select2({
                    //     width: "100%",
                    // });

                }
            });
        }

        function updateSSTRate() {
            $("#span_sst_rate").html($("#ddl_sst").val() + "%");

            var sst_percentage = $("#ddl_sst").val() * 0.01;

            $.each($("input[name='quotation']"), function() {

                itemID = $(this).val();
                console.log(itemID);

                quo_amt = parseFloat($("#quo_amt_" + itemID).val());
                sst = 0;

                if ($("#cat_" + itemID).val() == 1) {
                    // sst = quo_amt * 0.06;
                    sst = quo_amt * sst_percentage;
                    $("#sst_" + itemID).html(numberWithCommas(sst.toFixed(2)));

                    var amt_sst = parseFloat(quo_amt) + sst;

                    $("#amt_sst_" + itemID).html(numberWithCommas(amt_sst.toFixed(2)));
                    $("#int_amt_sst_" + itemID).val(numberWithCommas(amt_sst.toFixed(2)));
                }

            });

            updateAllQuotationAmount();
        }

        function saveQuotation() {

            if ($("#template_name_field").val() == "") {
                Swal.fire('Notice!', 'Please Key in template Name', 'warning');
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
            form_data.append("template_name", $("#template_name_field").val());
            form_data.append("purchase_price", $("#purchase_price").val());
            form_data.append("bln_discount", $("#ddl_discount").val());
            form_data.append("discount", $("#discount_amt").val());
            
            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/saveQuotationTemplate',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {

                    console.log(data);
                    Swal.fire('Success!', 'Saved', 'success');
                    $("#div_full_screen_loading").hide();
                    location.reload();
                    // $(".btnQuotation").show();
                    // $("#quotation_no").val(data.parameter.parameter_value_1);
                    // $('#tbl-bill-create').html(data.view);
                    // $('#tbl-print-quotation').html(data.view2);
                    // $('ul.pagination').replaceWith(data.links);
                },
                error: function(file, response) {
                    Swal.fire('Notice!', 'Error Occur', 'warning');
                    $("#div_full_screen_loading").hide();
                }
            });
        }

        function updateQuotation($id) {

            if (validateQuotation() == false) {
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
                item_desc = $("#desc_span_" + itemID).val();

                bill = {
                    account_item_id: account_item_id,
                    need_approval: need_approval,
                    item_desc: item_desc,
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
            form_data.append("bln_firmname", $("#ddl_firm_name").val());
            form_data.append("discount", $("#discount_amt").val());
            form_data.append("sst_rate", $("#ddl_sst").val());

            $.ajax({
                type: 'POST',
                url: '/updateQuotationTemplate/' + $id,
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

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function PrintAreaQuotation() {
            $("#dQuotationInvoice-p").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
        }

        function loadQuotationTemplate() {

            // if ($("#date").val() == "") {
            //   Swal.fire('Notice!', 'Please select quotation date ', 'warning');
            //   return;
            // }

            if ($("#ddl_quotation_template").val() == 0) {
                Swal.fire('Notice!', 'Please select a template', 'warning');
                return;
            }


            $("#print_payment_date").html($("#date").val());
            $("#p-quo-client-name").html($("#bill_to").val());

            var formData = new FormData();
            formData.append('loan_sum', $("#loan_sum").val());
            formData.append('purchase_price', $("#purchase_price").val());
            formData.append('sst_rate', $("#ddl_sst").val());


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

                    $("#div-quotation-edit").show();
                    $("#div-quotation-preview").hide();

                    document.getElementById('div-quotation-edit').scrollIntoView();
                }
            });
        }


        function cancelQuotationPreview() {
            $("#div-quotation-edit").show();
            $("#div-quotation-preview").hide();
        }

        function updateAllQuotationAmount() {
            var sum_total = 0;
            var sum_cat = 0;
            var sst_percentage = $("#ddl_sst").val() * 0.01;

            $.each($("input[name='Professional fees']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                console.log(amount);
                sst = 0;

                if ($("#cat_" + itemID).val() == 1) {
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

            $("#int_sub_total_1").val(sum_cat.toFixed(2));
            $("#sub_total_1").html(numberWithCommas(sum_cat.toFixed(2)));

            var sum_cat = 0;

            $.each($("input[name='Stamp duties']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                console.log(amount);
                sst = 0;

                if ($("#cat_" + itemID).val() == 1) {
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

        function showGenerateQuotationModal(quotationId) {
            // Generate quotation PDF directly
            var url = '/quotation-generator/generate/' + quotationId;
            window.open(url, '_blank');
        }
    </script>
@endsection
