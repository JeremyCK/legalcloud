<div id="modalAccountSummaryInput" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-10">
                        <h4 class="card-title mb-0 flex-grow-1">Update <span id="h4_account_summary">Summary
                                Report</span></h4>

                    </div>
                    <div class="col-2">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <form id="formAccountSummary">
                    {{-- <div class="col-12 ">
                        <h4 id="h4_account_summary"></h4>
                        <hr/>
                    </div> --}}
                    <input class="form-control" type="hidden" name="field_type" required>
                    <div class="col-12 input_summary input_referral input_name">
                        <div class="form-group row">
                            <div class="col">
                                <label>Name</label>
                                {{-- <input class="form-control" type="text" name="name" required> --}}

                                <div class="input-group">
                                    <input class="form-control" type="text" name="name" readonly>
                                    <input class="form-control" type="hidden" name="referral_id" >
                                    <div class="input-group-append"><span class="input-group-text">
                                            <a class="" href="javascript:void(0)" data-backdrop="static"
                                                data-keyboard="false" style="margin:0" data-toggle="modal"
                                                onclick="loadReferralList('summary')" data-target="#modalReferral"
                                                class="btn btn-xs btn-primary"><i style="margin-right: 10px;"
                                                    class="fa fa-refresh"></i>Select</a>
                                        </span></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 input_summary input_other">
                        <div class="form-group row">
                            <div class="col">
                                <label>Desc</label>
                                <input class="form-control" type="text" name="desc" required>
                            </div>
                        </div>

                    </div>


                    <div class="col-12 input_summary input_referral input_account">
                        <div class="form-group row">
                            <div class="col">
                                <label>Transaction ID</label>
                                <input class="form-control" type="text" name="transaction_id" required>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 input_summary input_amt input_referral">
                        <div class="form-group row">
                            <div class="col">
                                <label>Amount</label>
                                <input class="form-control" type="number" name="amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 input_summary input_financed">
                        <div class="form-group row">
                            <div class="col">
                                <label>Financed Fee</label>
                                <input class="form-control" type="number" name="financed_fee" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 input_summary input_financed">
                        <div class="form-group row">
                            <div class="col">
                                <label>Financed Sum</label>
                                <input class="form-control" type="number" name="financed_sum" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 input_summary input_financed input_referral input_account">
                        <div class="form-group row">
                            <div class="col">
                                <label>Payment Date</label>
                                <input class="form-control" type="date" name="payment_date" required>
                            </div>
                        </div>
                    </div>



                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="SaveAccountSummary()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function accountSummaryInputController(type, id, amount,name, trx_id, payment_date, financed_fee=0, finaced_sum=0, desc='') {
        var form = $("#formAccountSummary");
        form.find('[name=field_type]').val(type);

        $(".input_summary").hide();
        $amount_array = ['Uncollected', 'Collected'];
        $financed_array = ['Financed'];
        $other_array = ['Disb Manual', 'Other'];
        $exclude_name_array = ['Marketing'];

        if ($amount_array.includes(type)) {
            $(".input_amt").show();
            form.find('[name=amount]').val(amount);
        } else if ($financed_array.includes(type)) {
            $(".input_financed").show();

            form.find('[name=financed_fee]').val(financed_fee);
            form.find('[name=financed_sum]').val(finaced_sum);
            form.find('[name=payment_date]').val(payment_date);
        } else if ($exclude_name_array.includes(type)) {
            $(".input_referral").show();
            $(".input_name").hide();

            form.find('[name=amount]').val(amount);
            form.find('[name=transaction_id]').val(trx_id);
            form.find('[name=payment_date]').val(payment_date);
        }else if ($other_array.includes(type)) {
            $(".input_referral").show();
            $(".input_other").show();
            $(".input_name").hide();

            form.find('[name=desc]').val(name);
            form.find('[name=amount]').val(amount);
            form.find('[name=transaction_id]').val(trx_id);
            form.find('[name=payment_date]').val(payment_date);
        }  else {
            $(".input_referral").show();

            form.find('[name=name]').val(name);
            form.find('[name=amount]').val(amount);
            form.find('[name=referral_id]').val(id);
            form.find('[name=transaction_id]').val(trx_id);
            form.find('[name=payment_date]').val(payment_date);
        }

        @if(in_array($current_user->menuroles, ['admin', 'account', 'maker']) || in_array($current_user->id, [179,182]))
            $(".input_account").show();
        @else
            $(".input_account").hide();
        @endif

        $("#h4_account_summary").html(type);

    }

    function selectSummaryReportReferral(txtId, txtName)
    {
        var form = $("#formAccountSummary");
        form.find('[name=name]').val(txtName);
        form.find('[name=referral_id]').val(txtId);

        $('#modalReferral').modal('hide');
        $(".modal-backdrop").removeClass('show');
        $(".modal-backdrop").removeClass('modal-backdrop');
    }

    function SaveAccountSummary() {
        var formData = new FormData();

        $("#div_full_screen_loading").show();
    

        $.ajax({
            type: 'POST',
            url: '/SaveAccountSummary/' + $("#selected_bill_id").val(),
            data: $('#formAccountSummary').serialize(),
            success: function(data) {
                $("#div_full_screen_loading").hide();

                if (data.status == 1) {
                    loadCaseBill($("#selected_bill_id").val());
                    toastController(data.message);
                        closeUniversalModal();
                }
            },
            error: function(xhr, status, error) {
                $("#div_full_screen_loading").hide();
            }
        });
    }


</script>
