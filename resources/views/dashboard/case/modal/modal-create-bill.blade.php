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
        height: calc(1.5em + 0.75rem);
        padding: 0px 10px 0px 10px !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div id="modalCreateBill" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-print" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Create New Bill</h4>
                    </div>
                    <div class="col-6">
                        {{-- <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button> --}}
                        <a href="javascript:void(0);" type="button" data-dismiss="modal"
                            class="btn btn-danger float-right no-print btn_close_all"> <i class=" cli-x"></i> Close</a>
                    </div>
                </div>

            </div>
            <div class="modal-body">

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header">

                                    <div class="form-group row">


                                        {{-- <div class="col-6">
            
                                            <select id="quotation_template" class="form-control" name="quotation_template">
                                                <option value="0">-- Select Quotation Template --</option>
                                                @foreach ($quotation_template as $index => $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}

                                        
                                        @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::QuotationGeneratorPermission()) == true)
                                            <div class="col-12 ">
                                                <div class="form-group row">
                                                    <div class="form-check form-switch form-switch-xl">
                                                        <input class="form-check-input" id="ddlQuotationCheck" type="checkbox" onchange="quotationDDLController()">
                                                        <label class="form-check-label" for="ddlQuotationCheck">Load From My Quotation Template</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                          
                                       

                                        <div id="div_quotation_template_option"  class="col-6 ">
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Quotation Template</label>
                                                    <select id="quotation_template" class="form-control"
                                                        name="quotation_template">
                                                        <option value="0">-- Select Quotation Template --</option>
                                                        @foreach ($quotation_template as $index => $account)
                                                            <option value="{{ $account->id }}">{{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::QuotationGeneratorPermission()) == true)
                                        <div id="ddl_my_quotation_template" class="col-6 " style="display: none">
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>My Quotation Template</label>
                                                    <select id="my_quotation_template" class="form-control"
                                                        name="my_quotation_template">
                                                        <option value="0">-- Select Quotation Template --</option>
                                                        @if (isset($QuotationGeneratorMain))
                                                            @foreach ($QuotationGeneratorMain as $index => $account)
                                                                <option value="{{ $account->id }}">{{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        

                                       





                                        <div class="col-6 float-right">
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Bill To</label>
                                                    <select class="form-control" id="ddl_party_quo"
                                                        name="ddl_party_quo">
                                                        @foreach ($parties_list as $party)
                                                            <option value="{{ $party['name'] }}">{{ $party['party'] }} -
                                                                {{ $party['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6 float-right" style="display:none">
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>SST</label>
                                                    <select class="form-control" id="ddl_sst"
                                                        onchange="updateSSTRate()" name="ddl_sst">
                                                        {{-- <option value="6" > 6%</option> --}}
                                                        <option value="8" selected> 8%</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 ">

                                            {{-- <button class="btn btn-info" type="button" onclick="loadMyQuotationTemplate('{{ $case->id }}');">
                                                <i class="cil-caret-right"></i> Load from my Quotation
                                              </button> --}}

                                            <button class="btn btn-primary float-right" type="button"
                                                onclick="loadQuotationTemplate('{{ $case->id }}');">
                                                <i class="cil-caret-right"></i> Load Template
                                            </button>
                                        </div>

                                        <div class="col-12 ">
                                            <hr />
                                        </div>

                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding" style="width:100%;overflow-x:auto">

                                    <div class="form-group row" style="margin-left:0px;margin-right:0px">
                                        <div class="col-4">
                                            <h3 class="box-title">Total Bill: RM <span
                                                    id="span_total_create_bill">0.00</span></h3>
                                        </div>

                                        <div class="col-4">
                                            <div class="checkbox float-right">
                                                <input type="checkbox" name="check_all" value="1"
                                                    onchange="quotationItemCheckAllController(1)" id="check_all"
                                                    checked>
                                                <label for="check_all">Check All</label>
                                            </div>

                                        </div>

                                        <div class="col-4">

                                            <div class="checkbox float-left">
                                                <input type="checkbox" name="uncheck_all" value="1"
                                                    onchange="quotationItemCheckAllController(0)" id="uncheck_all">
                                                <label for="uncheck_all">Uncheck All</label>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-striped table-bordered datatable tbl-bill">
                                        <tbody id="tbl-quotation-create"></tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>

                    <button class="btn btn-submit btn-success float-right" onclick="CreateBill('{{ $case->id }}')"
                        type="button">
                        <span id="span_upload">Create</span>
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                    {{-- <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a> --}}

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>


    function quotationDDLController()
    {
        $("#ddl_my_quotation_template").hide();
        $("#div_quotation_template_option").hide();

        if ($("#ddlQuotationCheck").is(':checked'))
        {
           $("#ddl_my_quotation_template").show();
        }
        else
        {
            $("#div_quotation_template_option").show();
        }
    }

    // function updateCloseFileTotalAmt() {
    //     $sumCloseFileTotal = 0;

    //     $.each($("input[name='close_file_bill']:checked"), function() {
    //         itemID = $(this).val();

    //         $sumCloseFileTotal += parseFloat($("#sum_close_file_" + itemID).val());
    //     });

    //     $("#cf_transfer_amount").val($sumCloseFileTotal.toFixed(2));
    // }

    function updateQuotation(id, cat_id) {
        var quo_amt = $("#quo_amt_" + id).val();
        var sst = 0;

        var sst_percentage = $("#ddl_sst").val() * 0.01;

        if (cat_id == 1 || cat_id == 4) {
            // sst = quo_amt * 0.06;
            sst = quo_amt * sst_percentage;
            $("#sst_" + id).html(numberWithCommas(sst.toFixed(2)));
        }
        var amt_sst = parseFloat(quo_amt) + sst;


        console.log(sum_total);

        $("#amt_sst_" + id).html(numberWithCommas(amt_sst.toFixed(2)));
        $("#int_amt_sst_" + id).val(numberWithCommas(amt_sst.toFixed(2)));

        var sum_cat = 0;
        var sum_total = 0;
        var sst_total = 0;

        $.each($("input[name='quotation']:checked"), function() {

            itemID = $(this).val();

            amount = parseFloat($("#quo_amt_" + itemID).val());
            sst = 0;

            if ($("#cat_" + itemID).val() == 1 || $("#cat_" + itemID).val() == 4) {
                // sst = amount * 0.06;
                sst = amount * sst_percentage;
                // sum_cat += (parseFloat(amount) + sst);
                sst = parseFloat(sst).toFixed(2);
            }

            if ($("#cat_" + itemID).val() == cat_id) {
                // sum_cat += (parseFloat(amount) + sst);
                // sum_cat += parseFloat(amount + sst).toFixed(2);
                sum_cat += parseFloat(amount) + parseFloat(sst);
            }

            $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));

            sum_total += (parseFloat(amount) + parseFloat(sst));
            sst_total += (parseFloat(sst));
        });


        // $("#int_sub_total_" + cat_id).val(sum_cat.toFixed(2));
        $("#int_sub_total_" + cat_id).val(sum_cat);
        // $("#sub_total_" + cat_id).html(numberWithCommas(sum_cat.toFixed(2)));
        $("#sub_total_" + cat_id).html(numberWithCommas(parseFloat(sum_cat).toFixed(2)));
        // $("#sub_total_sst_" + cat_id).html(sst_total.toFixed(2));
        $("#total_sum_bill").html(numberWithCommas(sum_total.toFixed(2)));
        $("#span_total_create_bill").html(numberWithCommas(sum_total.toFixed(2)));

    }

    function updateAllQuotationAmount() {
        var sum_total = 0;
        var sst_total = 0;
        var sst_sub_total = 0;

        var sst_percentage = $("#ddl_sst").val() * 0.01;

        for ($i = 1; $i <= 4; $i++) {

            var sum_cat = 0;
            sst_total = 0;
            
            $.each($("input[name='quotation']:checked"), function() {

                itemID = $(this).val();

                amount = parseFloat($("#quo_amt_" + itemID).val());
                sst = 0;

                if ($("#cat_" + itemID).val() == 1 || $("#cat_" + itemID).val() == 4) {
                    // sst = amount * 0.06;
                    sst = amount * sst_percentage;
                    sst = parseFloat(sst).toFixed(2);
                }

                if ($("#cat_" + itemID).val() == $i) {
                    // sum_cat += (parseFloat(amount) + sst);
                    sum_cat += parseFloat(amount) + parseFloat(sst);
                    // sum_total += (parseFloat(amount) + sst);
                    sum_total += parseFloat(amount) + parseFloat(sst);
                        $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));
                sst_total += (parseFloat(sst));
                }

            


            });

            console.log($i);

            $("#int_sub_total_" + $i).val(sum_cat.toFixed(2));
            $("#sub_total_sst_" + $i).html(sst_total.toFixed(2));
            $("#sub_total_" + $i).html(numberWithCommas(sum_cat.toFixed(2)));
        }


        $("#total_sum_bill").html(numberWithCommas(sum_total.toFixed(2)));
        $("#span_total_create_bill").html(numberWithCommas(sum_total.toFixed(2)));

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




            // if ($("#cat_" + itemID).val() == 1) {
            //     // sst = amount * 0.06;
            //     sst = amount * sst_percentage;
            //     // sum_cat += (parseFloat(amount) + sst);
            //     sst = parseFloat(sst).toFixed(2);
            // }

            // if ($("#cat_" + itemID).val() == cat_id) {
            //     // sum_cat += (parseFloat(amount) + sst);
            //     // sum_cat += parseFloat(amount + sst).toFixed(2);
            //     sum_cat += parseFloat(amount) + parseFloat(sst);
            // }

            // $("#int_amt_sst_" + itemID).val(numberWithCommas(parseFloat(amount) + sst));

            // sum_total += (parseFloat(amount) + parseFloat(sst));
            // sst_total += (parseFloat(sst));
        });

        updateAllQuotationAmount();
    }

    // Initialize Select2 for both template dropdowns after DOM is ready
    $(document).ready(function() {
        $('#quotation_template').select2({
            placeholder: "Select or type to search Quotation Template",
            allowClear: true,
            width: '100%'
        });
        $('#my_quotation_template').select2({
            placeholder: "Select or type to search My Quotation Template",
            allowClear: true,
            width: '100%'
        });
    });
</script>
