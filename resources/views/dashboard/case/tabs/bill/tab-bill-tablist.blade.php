<ul class="nav nav-tabs scrollable-tabs" role="tablist">
    <li class="nav-item bill_link  " style="margin:0px"><a onclick="onBillTabActive('voucher')"
            class="nav-link @if($current_bill_tab=='voucher') active @endif text-center tab-bill tab-bill-voucher" data-toggle="tab"
            href="#billVoucher" role="tab" aria-controls="disbursement"
            aria-selected="true">Voucher</a></li>


    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccessInvoicePermission()) == true)
        @if($LoanCaseBillMain->bln_invoice == 1)
            <li id="li_invoice" class="nav-item " style="margin:0px"><a onclick="onBillTabActive('invoice')"
                class="nav-link @if($current_bill_tab=='invoice') active @endif tab-bill text-center" data-toggle="tab" href="#billInvoice2"
                role="tab" aria-controls="invoice" aria-selected="true">Invoice</a></li>
        @endif
    @endif

    <li class="nav-item bill_link" style="margin:0px"><a class="nav-link @if($current_bill_tab=='receive') active @endif tab-bill text-center " onclick="onBillTabActive('receive')"
            data-toggle="tab" href="#billReceive" role="tab" aria-controls="receive"
            aria-selected="true">Received</a></li>
    <li class="nav-item bill_link" style="margin:0px"><a class="nav-link @if($current_bill_tab=='disb') active @endif tab-bill text-center " onclick="onBillTabActive('disb')"
            data-toggle="tab" href="#billDisburse" role="tab" aria-controls="disbursement"
            aria-selected="true">Disbursement</a></li>


    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillSummaryReportPermission()) == true)
        <li class="nav-item bill_link" style="margin:0px"><a class="nav-link @if($current_bill_tab=='summary') active @endif tab-bill text-center " onclick="onBillTabActive('summary')"
                data-toggle="tab" href="#summaryReport" role="tab" aria-controls="receive"
                aria-selected="true">Summary Report</a></li>
    @endif

</ul>