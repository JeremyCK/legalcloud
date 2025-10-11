<script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
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
    document.getElementById('ddl_firm_name').onchange = function() {
        updateShowFirm();
    };

    document.getElementById('ddl_branch').onchange = function() {

        updateBranch();
    };

    document.getElementById('date').onchange = function() {

        updateDate();
    };

    document.getElementById('loan_sum').onchange = function() {
        updateLoanSum();
    };

    document.getElementById('purchase_price').onchange = function() {
        updatePurchasePrice();
    };

    document.getElementById('bill_to').onchange = function() {
        updateBillTo();
    };

    document.getElementById('ddl_discount').onchange = function() {
        updateDiscountRow();
    };

    document.getElementById('discount_amt').onchange = function() {
        updateDiscountValue();
    };

    function updateBillTo() {
        if ($("#bill_to").val() != '') {
            $("#p-quo-client-name").html($("#bill_to").val());
        } else {
            $("#p-quo-client-name").html('');
        }
    }

    function updatePurchasePrice() {
        if ($("#purchase_price").val() != '' && $("#purchase_price").val() != 0) {
            $("#quo_purchase_price").html("RM " + numberWithCommas($("#purchase_price").val()));
            $("#span_purchase_price").show();
        } else {
            $("#quo_purchase_price").html('RM 0.00');
            $("#span_purchase_price").hide();
        }

        updateDynamicMinMax();
    }

    function updateLoanSum() {
        if ($("#loan_sum").val() != '' && $("#loan_sum").val() != 0) {
            $("#quo_loan_sum").html("RM " + numberWithCommas($("#loan_sum").val()));
            $("#span_loan_sum").show();
        } else {
            $("#quo_loan_sum").html('RM 0.00');
            $("#span_loan_sum").hide();
        }
    }

    function updateDate() {
        if ($("#date").val() != '') {
            $("#quo_date").html($("#date").val());
            $("#span_date").show();
        } else {
            $("#quo_date").html('');
            $("#span_date").hide();
        }
    }

    function updateBranch() {
        $(".branch_all").hide();
        $(".branch_" + $("#ddl_branch").val()).show();
    }

    function updateShowFirm() {
        if ($("#ddl_firm_name").val() == 1) {
            $(".show_firm").show();
        } else {
            $(".show_firm").hide();
        }
    }

    function updateDiscountRow() {
        if ($("#ddl_discount").val() == 1) {
            $(".div_discount").show();
        } else {
            $(".div_discount").hide();
        }
    }

    function updateDynamicMinMax() {
        if ($("#purchase_price").val() != '' && $("#purchase_price").val() != 0) {
            $.each($("input[name='Professional fees']:checked"), function() {
                itemID = $(this).val();

                console.log(itemID);

                $purchase_price = parseFloat($("#purchase_price").val());
                min = parseFloat($("#min_" + itemID).val());
                max = parseFloat($("#max_" + itemID).val());
                account_formula = $("#formula_" + itemID).val();

                if( account_formula == '${property_price_max_3}')
                {
                    $max_cap = $purchase_price * 0.03;
                    $max_cap  = parseFloat($max_cap).toFixed(2);
                    $("#max_" + itemID).val($max_cap);
                    $("#span_max_" + itemID).html(numberWithCommas($max_cap));
                }
            });
        } 
    }

    function updateDiscountValue() {
        $("#span_discount_amt").html(numberWithCommas($("#discount_amt").val()));
        $("#span_discount_amt_quo").html(numberWithCommas($("#discount_amt").val()));

        $total_sum = $("#int_total_sum_bill").val() - $("#discount_amt").val();
        console.log($("#int_total_sum_bill").val());
        console.log($("#discount_amt").val());


        $("#final_amt").html(numberWithCommas($total_sum));
        $("#final_amt_quo").html(numberWithCommas($total_sum));

    }

    function validateQuotation() {
        var min_hit_count = 0;
        var max_hit_count = 0;

        $.each($("input[name='Professional fees']:checked"), function() {

            itemID = $(this).val();

            amount = parseFloat($("#quo_amt_" + itemID).val());
            min = parseFloat($("#min_" + itemID).val());
            max = parseFloat($("#max_" + itemID).val());

            if (min != 0) {
                if (amount < min) {
                    min_hit_count += 1;
                    $("#quo_amt_" + itemID).addClass('error-input-box');
                } else {
                    $("#quo_amt_" + itemID).removeClass('error-input-box');
                }
            }

            if (max != 0) {
                if (amount > max) {
                    max_hit_count += 1;
                    $("#quo_amt_" + itemID).addClass('error-input-box');
                } else {
                    $("#quo_amt_" + itemID).removeClass('error-input-box');
                }
            }
        });

        $.each($("input[name='Stamp duties']:checked"), function() {

            itemID = $(this).val();

            amount = parseFloat($("#quo_amt_" + itemID).val());
            min = parseFloat($("#min_" + itemID).val());
            max = parseFloat($("#max_" + itemID).val());

            if (min != 0) {
                if (amount < min) {
                    min_hit_count += 1;
                    $("#quo_amt_" + itemID).addClass('error-input-box');
                } else {
                    $("#quo_amt_" + itemID).removeClass('error-input-box');
                }
            }

            if (max != 0) {
                if (amount > max) {
                    max_hit_count += 1;
                    $("#quo_amt_" + itemID).addClass('error-input-box');
                } else {
                    $("#quo_amt_" + itemID).removeClass('error-input-box');
                }
            }
        });

        $.each($("input[name='Disbursement']:checked"), function() {

            itemID = $(this).val();

            amount = parseFloat($("#quo_amt_" + itemID).val());
            min = parseFloat($("#min_" + itemID).val());
            max = parseFloat($("#max_" + itemID).val());

            if (min != 0) {
                if (amount < min) {
                    min_hit_count += 1;
                    $("#quo_amt_" + itemID).addClass('error-input-box');
                } else {
                    $("#quo_amt_" + itemID).removeClass('error-input-box');
                }
            }

            if (max != 0) {
                if (amount > max) {
                    max_hit_count += 1;
                    $("#quo_amt_" + itemID).addClass('error-input-box');
                } else {
                    $("#quo_amt_" + itemID).removeClass('error-input-box');
                }
            }
        });

        if (min_hit_count > 0) {
            Swal.fire('notice!', 'Please make sure all item not lower than min avalue', 'warning');
            return false;
        }

        if (max_hit_count > 0) {
            Swal.fire('notice!', 'Please make sure all item not higher than max value', 'warning');
            return false;
        }
        
        return true;
    }

    function generateQuotation() {
        var account_list_1 = [];
        var account_list_2 = [];
        var account_list_3 = [];
        var account_list_4 = [];
        var account_item = {};

        if (validateQuotation() == false) {
            return;
        }

        $.each($("input[name='Professional fees']:checked"), function() {

            itemID = $(this).val();

            account_item = {
                account_item_id: parseFloat($("#account_item_id_" + itemID).val()),
                need_approval: parseFloat($("#need_approval_" + itemID).val()),
                account_name: $("#account_name_" + itemID).val() + " " + $("#account_name_cn_" + itemID).val(),
                account_name_cn: $("#account_name_cn_" + itemID).val(),
                amount: parseFloat($("#quo_amt_" + itemID).val()),
                cat_id: parseFloat($("#cat_" + itemID).val()),
                min: parseFloat($("#min_" + itemID).val()),
                max: parseFloat($("#max_" + itemID).val()),
                item_desc: $("#desc_span_" + itemID).val(),
            };

            account_list_1.push(account_item);
        });


        $.each($("input[name='Stamp duties']:checked"), function() {
            itemID = $(this).val();

            account_item = {
                account_item_id: parseFloat($("#account_item_id_" + itemID).val()),
                need_approval: parseFloat($("#need_approval_" + itemID).val()),
                account_name: $("#account_name_" + itemID).val() + " " + $("#account_name_cn_" + itemID).val(),
                amount: parseFloat($("#quo_amt_" + itemID).val()),
                cat_id: parseFloat($("#cat_" + itemID).val()),
                min: parseFloat($("#min_" + itemID).val()),
                max: parseFloat($("#max_" + itemID).val()),
                item_desc: $("#desc_span_" + itemID).val(),
            };

            account_list_2.push(account_item);
        });


        $.each($("input[name='Disbursement']:checked"), function() {
            itemID = $(this).val();

            account_item = {
                account_item_id: parseFloat($("#account_item_id_" + itemID).val()),
                need_approval: parseFloat($("#need_approval_" + itemID).val()),
                account_name: $("#account_name_" + itemID).val() + " " + $("#account_name_cn_" + itemID).val(),
                amount: parseFloat($("#quo_amt_" + itemID).val()),
                cat_id: parseFloat($("#cat_" + itemID).val()),
                min: parseFloat($("#min_" + itemID).val()),
                max: parseFloat($("#max_" + itemID).val()),
                item_desc: $("#desc_span_" + itemID).val(),
            };

            account_list_3.push(account_item);
        });

        $.each($("input[name='Reimbursement']:checked"), function() {
            itemID = $(this).val();

            account_item = {
                account_item_id: parseFloat($("#account_item_id_" + itemID).val()),
                need_approval: parseFloat($("#need_approval_" + itemID).val()),
                account_name: $("#account_name_" + itemID).val() + " " + $("#account_name_cn_" + itemID).val(),
                amount: parseFloat($("#quo_amt_" + itemID).val()),
                cat_id: parseFloat($("#cat_" + itemID).val()),
                min: parseFloat($("#min_" + itemID).val()),
                max: parseFloat($("#max_" + itemID).val()),
                item_desc: $("#desc_span_" + itemID).val(),
            };

            account_list_4.push(account_item);
        });

        var form_data = new FormData();
        form_data.append("account_list_1", JSON.stringify(account_list_1));
        form_data.append("account_list_2", JSON.stringify(account_list_2));
        form_data.append("account_list_3", JSON.stringify(account_list_3));
        form_data.append("account_list_4", JSON.stringify(account_list_4));
        form_data.append("discount", $("#discount_amt").val());
        form_data.append("bln_discount", $("#ddl_discount").val());
        form_data.append("sst_percentage", $("#ddl_sst").val());

        $.ajax({
            type: 'POST',
            url: '/generateQuotationPrint',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(result) {
                $("#tbl-print-quotation").html(result.view);

                $("#div-quotation-edit").hide();
                $("#div-quotation-preview").show();

                document.getElementById('div-quotation-preview').scrollIntoView();
            }
        });
    }

    function addAccountItem() {
        var form_data = new FormData();
        form_data.append("template_id", $("#ddl_quotation_template").val());


        $.ajax({
            type: 'POST',
            url: '/quotationGenAddAccountItem/892',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(data) {

                $('#tbl-bill-create').html(data.view);
                $('#ddlAccountItem').html(data.view2);

                updateBillTo();
                updatePurchasePrice();
                updateLoanSum();
                updateDate();
                updateBranch();
                updateShowFirm();
                updateDiscountRow();
                updateDiscountValue();

                $("#div-quotation-edit").show();
                $("#div-quotation-preview").hide();


                // $select =$('#ddlAccountItem').select2({
                //     width: "100%",
                // });

            }
        });
    }
</script>
