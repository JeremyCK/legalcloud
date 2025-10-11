<div id="modalAddBillto" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1" id="lbl_title_split_inv">Add Party into Invoice</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">

                <form id="formAddBillto">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group row">
                                <div class="col">
                                    <label>Bill To</label>
                                    <select class="form-control  ddl-party" id="ddl_party_inv_v2"
                                        name="ddl_party_inv_v2"></select>
                                </div>
                            </div>
                        </div>


                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>


                <button type="button" id="btnAddBilltoParty"
                    class="btn inv-btn add-party-invoice btn-danger float-right" onclick="AddBilltoInvoice()">Add
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

                <button type="button" id="btnSplitInvoice" class="btn inv-btn split-invoice btn-danger float-right"
                    onclick="SplitInvoice()">Split Invoice
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    $invoice_id = '';

    function SplitInvoiceMode() {
        $(".inv-btn").hide();
        $(".split-invoice").show();

        $("#lbl_title_split_inv").html("Split Invoice");
    }

    function AddPartyInvoiceMode(invoiceId) {
        $(".inv-btn").hide();
        $(".add-party-invoice").show();
        $invoice_id = invoiceId;

        $("#lbl_title_split_inv").html("Add party into Invoice");
    }

    @if(isset($case))
          function SplitInvoice() {
        Swal.fire({
            icon: 'warning',
            text: 'Split Invoice',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $("#div_full_screen_loading").show();

                var form_data = new FormData();
                form_data.append("bill_to", $("#ddl_party_inv_v2").val());
                form_data.append("case_id", {{ $case->id }});

                $.ajax({
                    type: 'POST',
                    url: '/splitInvoice/' + $("#selected_bill_id").val(),
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        $("#div_full_screen_loading").hide();
                        if (data.status == 1) {

                            // Swal.fire('Success!', data.message, 'success');
                            toastController(data.message);
                            location.reload();
                        } else {
                            toastController(data.message, 'warning');
                        }

                    },
                    error: function(file, response) {
                        $("#div_full_screen_loading").hide();
                    }
                });
            }
        })
    }

    function AddBilltoInvoice() {

        var form_data = new FormData();
        form_data.append("bill_to", $("#ddl_party_inv_v2").val());
        form_data.append("case_id", {{ $case->id }});
        form_data.append("invoice_id", $invoice_id);
        form_data.append("bill_to_type", $("#ddl_party").find(':selected').attr('data-type'));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: '/AddBilltoInvoice/' + $("#selected_bill_id").val(),
            data: form_data,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log(data);
                if (data.status == 1) {
                    toastController(data.message);
                    // var billto = $("#ddl_party").val();
                    // $("#lbl_bill_to_party").html(data.view);
                    closeUniversalModal();
                    location.reload();
                    // loadCaseBill($("#selected_bill_id").val());
                } else {
                    toastController(data.message, 'warning');
                }

            }

        });

    }
    @endif

  

    

    function removeBillto($id) {

        var form_data = $("#formAddBilltoInfo").serialize();

        Swal.fire({
            icon: 'warning',
            title: 'Remove this client from this invoice?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/removeBillto/' + $id,
                    data: form_data,
                    // processData: false,
                    // contentType: false,
                    success: function(data) {
                        console.log(data);
                        if (data.status == 1) {
                            toastController('deleted');
                            // $("#lbl_bill_to_party").html(data.view);
                            location.reload();
                        } else {
                            toastController(data.message, 'warning');
                        }

                    }

                });
            }
        })

    }

    function removeInvoice($id) {

        var form_data = $("#formAddBilltoInfo").serialize();

        Swal.fire({
            icon: 'warning',
            title: 'Remove this invoice?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/removeInvoice/' + $id,
                    data: form_data,
                    // processData: false,
                    // contentType: false,
                    success: function(data) {
                        console.log(data);
                        if (data.status == 1) {
                            toastController('deleted');
                            $("#lbl_bill_to_party").html(data.view);
                            location.reload();
                        } else {
                            toastController(data.message, 'warning');
                        }

                    }

                });
            }
        })

    }
</script>
