<div class="row">

    <div  class="col-12">
        <div id="div-bill-summary-details" class="row"></div>
    </div>
    
    <div  class="col-12">
        <table class="table mb-0">
            <tbody>
                <tr>
                    <td class="fw-medium"><b>SST Rate</b>: <span class="lbl_sst_rate">0</span>%</td>
                </tr>

            </tbody>
        </table>
    </div>
    <div class="col-12">
        <div class="box-tools">

            <div class="btn-group">
                <button type="button" class="btn btn-info btn-lg">Action</button>
                <button type="button" class="btn btn-info btn- dropdown-toggle"
                    data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" style="padding:0">
                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                        @if ($LoanCaseBillMain->bln_invoice == 0)
                            <a class="dropdown-item btn-danger " href="javascript:void(0)"
                            onclick="convertToInvoice();" style="color:white;margin:0"><i
                                style="margin-right: 10px;" class="cil-action-undo"></i>Convert to
                            Invoice</a>
                        @endif
                        
                    @endif

                    <div class="dropdown-divider" style="margin:0"></div>
                    <a class="dropdown-item btn-success" href="javascript:void(0)"
                        onclick="quotationPrintMode();" style="color:white"><i class="cil-print"
                            style="margin-right: 10px;"></i> <span></span>Print Proforma Invoice
                    </a>
                    <div class="dropdown-divider" style="margin:0"></div>
                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::UpdateSSTRatePermission()) == true)
                        @if ($LoanCaseBillMain->bln_invoice == 0)
                            <a class="dropdown-item btn-warning quotation" href="javascript:void(0)"
                                data-backdrop="static" data-keyboard="false" data-keyboard="false"
                                data-toggle="modal" data-target="#modalSSTRate" style="color:white;margin:0">% Update SST Rate</a>
                        @endif 
                    @endif

                    
                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::EInvoicePermission()) == true)
                    <a class="dropdown-item bg-purple" href="javascript:void(0)" data-backdrop="static"
                     data-keyboard="false" data-keyboard="false" data-toggle="modal" onclick="AddPartyInvoiceMode();"
                     data-target="#modalAddBillto" onclick="" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-user"></i>Create Invoice recipient</a>
             <div class="dropdown-divider" style="margin:0"></div>
                    @endif
                           
                    



                </div>  
            </div>
            @if (!in_array($case->status, [0]))

                <a id="btn_request_voucher_modal" class="btn btn-info float-right " href="javascript:void(0)"
                data-backdrop="static" data-keyboard="false" onclick="RequestVoucherModal();" 
                style="color:white;margin:0" data-toggle="modal"
                data-target="#modalRequestVoucher">
                <i style="margin-right: 10px;" class="cil-plus"></i>Request Voucher

</a>
                                {{-- <a id="btn_request_voucher_modal" class="btn btn-danger float-right " href="javascript:void(0)"
                data-backdrop="static" >
                <i style="margin-right: 10px;" class="cil-plus"></i>Under maintance
            </a> --}}
            @endif

        </div>
    </div>
</div>
<div style="margin-top:30px;">
    <table class="table table-striped table-bordered datatable">
        <thead >
            <tr class="text-center" style="background-color: black;color:white">
                <th>No</th>
                <th width="60%">Item</th>
                <th>Current Amount (RM)</th>
                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'maker']))
                    <th class="quotation1">Quotation Base Amount (RM)</th>
                    <th class="quotation1">SST (<span class="lbl_sst_rate">0</span>%)</th>
                    <th class="quotation1">Quotation Amount + SST (RM)</th>
            
                @endif
            </tr>
        </thead>
        <tbody id="tbl-case-bill">
        </tbody>
    </table>
</div>