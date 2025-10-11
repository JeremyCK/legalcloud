<style>
    .print-table-receipt>tbody>tr>td,
    .print-table-receipt>tbody>tr>th,
    .print-table-receipt>tfoot>tr>td,
    .print-table-receipt>tfoot>tr>th,
    .print-table-receipt>thead>tr>td,
    .print-table-receipt>thead>tr>th {
        border: 1px solid black;
    }

    .print-receipt>tbody>tr>td,
    .print-receipt>tbody>tr>th,
    .print-receipt>tfoot>tr>td,
    .print-receipt>tfoot>tr>th,
    .print-receipt>thead>tr>td,
    .print-receipt>thead>tr>th {
        padding: 5px !important;
    }

    .wrap {
        margin: 10px;
        display: flex;
    }

    .wrap span {
        align-self: flex-end;
    }

 
</style>
<div id="modalReceipt" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-print" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Print Receipt</h4>
                    </div>
                    <div class="col-6">
                        {{-- <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button> --}}
                        <a href="javascript:void(0);" type="button" data-dismiss="modal"
                            class="btn btn-danger float-right no-print btn_close_all"> <i class=" cli-x"></i> Close</a>
                    </div>
                </div>

            </div>
            <div class="modal-body">

                <div class="row no-print" style="margin-bottom:30px;">
                    <div class="col-6">

                        <button type="button" class="btn btn-warning pull-left " onclick="printloReceipt()"
                            style="margin-right: 5px;">
                            <span><i class="fa fa-print"></i> Print</span>
                        </button>
                    </div>
                    {{-- <div class="col-6">
                        <a href="javascript:void(0);" onclick="cancelInvoicePrintMode()"
                            class="btn btn-danger float-right no-print">Cancel</a>
                    </div> --}}

                </div>

                <hr class="row" />

                <div id="dReceipt-p" class="div2  printableArea "
                    style="padding:30px;background-color:white !important;">
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
    function updateCloseFileTotalAmt() {
        $sumCloseFileTotal = 0;

        $.each($("input[name='close_file_bill']:checked"), function() {
            itemID = $(this).val();

            $sumCloseFileTotal += parseFloat($("#sum_close_file_" + itemID).val());
        });

        $("#cf_transfer_amount").val($sumCloseFileTotal.toFixed(2));
    }
</script>
