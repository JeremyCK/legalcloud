<div id="dBillList" class="card d_operation" style="display:none; min-height:200px;">
    <div class="card-header">
        <h4 id="bill_name"></h4>
    </div>
    <div class="card-body">
        <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger" style="margin-bottom:10px">Cancel</a>
        {{-- <div id="div-bill-summary-details" class="row"></div> --}}

        <div id="tabBillCase" class="nav-tabs-custom" style="margin:20px">
            <ul class="nav nav-tabs scrollable-tabs" role="tablist">
                <li class="nav-item bill_link  " style="margin:0px"><a
                        class="nav-link active text-center tab-bill tab-bill-voucher" data-toggle="tab"
                        href="#billVoucher" role="tab" aria-controls="disbursement"
                        aria-selected="true">Voucher</a></li>

                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']) ||
                        in_array($current_user->id, [51, 32, 13]))
                    <li id="li_invoice" class="nav-item bill_link" style="margin:0px;display:none"><a
                            class="nav-link tab-bill text-center" data-toggle="tab" href="#billInvoice"
                            role="tab" aria-controls="invoice" aria-selected="true">Invoice</a></li>
                @endif
                <li class="nav-item bill_link" style="margin:0px"><a class="nav-link tab-bill text-center "
                        data-toggle="tab" href="#billReceive" role="tab" aria-controls="receive"
                        aria-selected="true">Received</a></li>
                <li class="nav-item bill_link" style="margin:0px"><a class="nav-link tab-bill text-center "
                        data-toggle="tab" href="#billDisburse" role="tab" aria-controls="disbursement"
                        aria-selected="true">Disbursement</a></li>


                @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillSummaryReportPermission()) == true)
                    <li class="nav-item bill_link" style="margin:0px"><a class="nav-link tab-bill text-center "
                            data-toggle="tab" href="#summaryReport" role="tab" aria-controls="receive"
                            aria-selected="true">Summary Report</a></li>
                @endif

            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane tab-bill tab-bill-voucher active" id="billVoucher" role="tabpanel"
                style="margin-top:30px;min-height:400px">
                {{-- <div class="row">

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
                                        @if ($loanCaseBillMain[0]->bln_invoice == 0)
                                            <a class="dropdown-item btn-danger " href="javascript:void(0)"
                                            onclick="convertToInvoice();" style="color:white;margin:0"><i
                                                style="margin-right: 10px;" class="cil-action-undo"></i>Convert to
                                            Invoice</a>
                                        @endif
                                        
                                    @endif

                                    <div class="dropdown-divider" style="margin:0"></div>
                                    <a class="dropdown-item btn-success" href="javascript:void(0)"
                                        onclick="quotationPrintMode();" style="color:white"><i class="cil-print"
                                            style="margin-right: 10px;"></i> <span></span>Print Quotation
                                    </a>
                                    <div class="dropdown-divider" style="margin:0"></div>
                                    @if ($loanCaseBillMain[0]->bln_invoice == 1)
                                        <a class="dropdown-item btn-warning quotation" href="javascript:void(0)"
                                            data-backdrop="static" data-keyboard="false" data-keyboard="false"
                                            data-toggle="modal" data-target="#modalSSTRate" style="color:white;margin:0">% Update SST Rate</a>
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
                            @endif

                        </div>
                    </div>
                </div>
                <div style="margin-top:30px;">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr class="text-center">
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
                            <?php
                            $total = 0;
                            $subtotal = 0;
                            ?>
                            @if (count($account_template_with_cat))


                                @foreach ($account_template_with_cat as $index => $cat)
                                    <tr style="background-color:grey;color:white">
                                        <td colspan="5">{{ $cat['category']->category }}</td>
                                        <?php $total += $subtotal; ?>
                                        <?php $subtotal = 0; ?>

                                    </tr>
                                    @foreach ($cat['account_details'] as $index => $details)
                                        <?php $subtotal += $details->amount; ?>
                                        <tr>
                                            <td class="text-center" style="width:50px">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="bill" value="{{ $details->id }}"
                                                        id="chk_{{ $details->id }}"
                                                        @if ($details->amount == 0) disabled @endif>
                                                    <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                                                </div>
                                            </td>
                                            <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}
                                            </td>
                                            <td id="item_{{ $details->id }}">{{ $details->item_name }}</td>
                                            <td id="amt_{{ $details->id }}">{{ $details->amount }} </td>

                                            @if (
                                                $current_user->menuroles == 'account' ||
                                                    $current_user->menuroles == 'admin' ||
                                                    $current_user->menuroles == 'management' ||
                                                    $current_user->menuroles == 'sales')
                                                <td id="amt_{{ $details->id }}">{{ $details->quo_amount }} </td>
                                            @endif

                                        </tr>
                                    @endforeach

                                    @if ($cat['category']->taxable == '1')
                                        <tr>
                                            <td colspan="2">{{ $cat['category']->percentage }}% GOVERNMENT TAX
                                            </td>
                                            <td style="text-align:right" colspan="4">
                                                {{ number_format((float) ($subtotal * 0.06), 2, '.', '') }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td></td>
                                        <td style="text-align:right" colspan="4">{{ $subtotal }}</td>
                                    </tr>
                                @endforeach

                                <tr style="background-color:grey;color:white">
                                    <td colspan="5">Others</td>
                                    <?php $total += $subtotal; ?>
                                    <?php $subtotal = 0; ?>

                                </tr>
                                <tr>
                                    <td class="text-center" style="width:50px">
                                        <div class="checkbox">
                                            <input type="checkbox" name="bill" value="299" id="chk_299"
                                                disabled>
                                            <label for="chk_299">1</label>
                                        </div>
                                    </td>
                                    <td class="hide" id="item_id_299">29</td>
                                    <td id="item_299">sales commission (6%)</td>
                                    <td id="amt_299">
                                        {{ number_format((float) ($case->targeted_bill * 0.06), 2, '.', '') }}</td>

                                </tr>

                                <tr>
                                    <td>Total </td>
                                    <td style="text-align:right" colspan="4">{{ $total }}</td>

                                </tr>
                            @else
                                <tr>
                                    <td class="text-center" colspan="5">No data</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div> --}}
            </div>

            <div class="tab-pane tab-bill " id="billInvoice2" role="tabpanel" style="margin-top:30px;min-height:400px">
                
                {{-- <div class="row">
                    <div class="col-12">
                        <div class="box-tools">

                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'maker']) ||
                                    in_array($current_user->id, [13]))

                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium"><b>Invoice No</b></td>
                                            <td id="lbl_invoice_no">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium"><b>Invoice Date</b></td>
                                            <td id="lbl_invoice_date"><b> - </b></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium"><b>Status</b></td>
                                            <td id="lbl_invoice_status"><b> - </b></td>
                                        </tr>

                                    </tbody>
                                </table>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-lg">Action</button>
                                    <button type="button" class="btn btn-info btn- dropdown-toggle"
                                        data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" style="padding:0">

                                        <div class="dropdown-divider" style="margin:0"></div>
                                        <a class="dropdown-item btn-success" href="javascript:void(0)"
                                            onclick="invoicePrintMode();" style="color:white"><i class="cil-print"
                                                style="margin-right: 10px;"></i> <span></span>Print Invoice
                                            <div class="dropdown-divider" style="margin:0"></div>


                                            <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                                data-backdrop="static" data-keyboard="false" data-keyboard="false"
                                                data-toggle="modal" data-target="#modalInvoiceDate"
                                                onclick="InvoiceDateModal();" style="color:white;margin:0"><i
                                                    style="margin-right: 10px;" class="cil-calendar"></i>Update
                                                Invoice Date</a>

                                            @if ($case->status != 0)
                                                <a id="btn_revert_invoice" class="dropdown-item btn-danger"
                                                    href="javascript:void(0)" onclick="revertToQuotation();"
                                                    style="color:white;margin:0"><i style="margin-right: 10px;"
                                                        class="cil-action-undo"></i>Revert Invoice</a>
                                                
                                                    <a id="btn_revert_invoice" class="dropdown-item "
                                                        href="javascript:void(0)" onclick="revertToQuotationWithReserveINVNo();"
                                                        style="color:white;margin:0;background-color:orange"><i style="margin-right: 10px;"
                                                            class="cil-action-undo"></i>Revert Invoice with reserve running no</a>
                                            @endif


                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
                <div style="margin-top:30px;">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Item</th>
                                <th>Quotation Amount (RM)</th>
                                @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'maker']))
                                    <th class="">Invoice Base Amount (RM)</th>
                                    <th class="">SST  (<span class="lbl_sst_rate">0</span>%)</th>
                                    <th class="">Invoice Amount + SST (RM)</th>
                                    
                                @endif
                            </tr>
                        </thead>
                        <tbody id="tbl-invoice-bill">
                            <?php
                            $total = 0;
                            $subtotal = 0;
                            ?>
                            @if (count($account_template_with_cat))


                                @foreach ($account_template_with_cat as $index => $cat)
                                    <tr style="background-color:grey;color:white">
                                        <td colspan="5">{{ $cat['category']->category }}</td>
                                        <?php $total += $subtotal; ?>
                                        <?php $subtotal = 0; ?>

                                    </tr>
                                    @foreach ($cat['account_details'] as $index => $details)
                                        <?php $subtotal += $details->amount; ?>
                                        <tr>
                                            <td class="text-center" style="width:50px">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="bill"
                                                        value="{{ $details->id }}" id="chk_{{ $details->id }}"
                                                        @if ($details->amount == 0) disabled @endif>
                                                    <label for="chk_{{ $details->id }}">{{ $index + 1 }}</label>
                                                </div>
                                            </td>
                                            <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}
                                            </td>
                                            <td id="item_{{ $details->id }}">{{ $details->item_name }}</td>
                                            <td id="amt_{{ $details->id }}">{{ $details->amount }} </td>

                                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'sales', 'clerk', 'lawyer', 'maker']))
                                                <td id="amt_{{ $details->id }}">{{ $details->quo_amount }} </td>
                                            @endif

                                        </tr>
                                    @endforeach

                                    @if ($cat['category']->taxable == '1')
                                        <tr>
                                            <td colspan="2">{{ $cat['category']->percentage }}% GOVERNMENT TAX
                                            </td>
                                            <td style="text-align:right" colspan="4">
                                                {{ number_format((float) ($subtotal * 0.06), 2, '.', '') }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td></td>
                                        <td style="text-align:right" colspan="4">{{ $subtotal }}</td>
                                    </tr>
                                @endforeach

                                <tr style="background-color:grey;color:white">
                                    <td colspan="5">Others</td>
                                    <?php $total += $subtotal; ?>
                                    <?php $subtotal = 0; ?>

                                </tr>
                                <tr>
                                    <td class="text-center" style="width:50px">
                                        <div class="checkbox">
                                            <input type="checkbox" name="bill" value="299" id="chk_299"
                                                disabled>
                                            <label for="chk_299">1</label>
                                        </div>
                                    </td>
                                    <td class="hide" id="item_id_299">29</td>
                                    <td id="item_299">sales commission (6%)</td>
                                    <td id="amt_299">
                                        {{ number_format((float) ($case->targeted_bill * 0.06), 2, '.', '') }}</td>

                                </tr>

                                <tr>
                                    <td>Total </td>
                                    <td style="text-align:right" colspan="4">{{ $total }}</td>

                                </tr>
                            @else
                                <tr>
                                    <td class="text-center" colspan="6">No data</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div> --}}
            </div>

            <div class="tab-pane tab-bill " id="billDisburse" role="tabpanel">
                <div class="row">
                    <div class="col-12 mb-2">
                        @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::MoveBillPermission()) == true)
                            @if (!in_array($case->status, [7,4,99,0]))
                                <button class="btn bg-orange float-left" type="button" onclick="loadCaseDisb();" data-backdrop="static" data-keyboard="false" data-keyboard="false"
                                data-toggle="modal" data-target="#modalMoveDisb" style="margin-right: 5px;">
                                    <i class="cil-move"></i> Move Disbursement
                                </button>
                            @endif
                        @endif
                       
                       

                        <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)"
                            onclick="exportTableToExcelDisb();">
                            <i class="fa fa-file-excel-o"> </i>Download as Excel
                        </a>
                    </div>
                    <?php $total_amt = 0; ?>
                    <div class="col-12 table-responsive" style="height:500px">
                        <table id="tbl-disb-case" class="table table-striped">
                            <thead style="background-color: black;color:white; z-index:100">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Voucher No</th>
                                    <th class="text-center">Trx Id</th>
                                    <th class="text-center">Item</th>
                                    <th class="text-center">Desc</th>
                                    <th class="text-center">Amount(RM)</th>
                                    <th class="text-center">Client Bank</th>
                                    <th class="text-center">Payment Date</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Requested By</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center remove-this">Action</th>
                                </tr> 
                            </thead>
                            <tbody id="tbl-bill-disburse">
                            </tbody>

                            <tfoot style="background-color: black;color:white; z-index:100">
                                <th colspan="5" class="text-left">Total </th>
                                <th class="text-right"><span id="span_total_disb" class="text-right">0</span>
                                </th>
                                <th colspan="6" class="text-left"> </th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane tab-bill " id="billReceive" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="box-tools">


                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']) || in_array($current_user->id, [187]))
                                @if (!in_array($case->status, [0]))
                                    {{-- <button class="btn btn-primary float-right" type="button"
                                        onclick="billEntryMode('{{ $case->id }}');" style="margin-right: 5px;">
                                        <i class="cil-cash"></i> Received Payment
                                    </button> --}}

                                    <a class="btn btn-info float-right " href="javascript:void(0)"
                                        data-backdrop="static" data-keyboard="false"
                                        style="color:white;margin:0" data-toggle="modal"
                                        data-target="#modalReceiveBill">
                                        <i style="margin-right: 10px;" class="cil-plus"></i>Received Payment
                                    </a>
                                @endif

                                <button class="btn btn-warning float-left" type="button"
                                    onclick="billReceiveMainEditMode('{{ $case->id }}');"
                                    style="margin-right: 5px;">
                                    <i class="cil-print"></i> Print Mode
                                </button>
                            @endif


                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:30px;min-height:500px">
                    <?php $total_amt = 0; ?>
                    <div id="div-bill-receive" class="col-12 table-responsive">
                        <table id="tbl-bill-receive" class="table table-striped">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">TRX ID</th>
                                <th class="text-center">Payee</th>
                                <th class="text-center">Desc</th>
                                <th class="text-center">Amount(RM)</th>
                                <th class="text-center">Client Bank</th>
                                <th class="text-center">Date Receive</th>
                                <th class="text-center">System Date</th>
                                <th class="text-center">Requested By</th>
                                @if (
                                    $current_user->menuroles == 'account' ||
                                        $current_user->menuroles == 'admin' ||
                                        $current_user->menuroles == 'management' ||
                                        $current_user->menuroles == 'maker' ||
                                        $current_user->id == 51)
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                            </thead>
                            {{-- <tbody id="tbl-bill-receive"> --}}
                                
                            <tbody >
                            </tbody>
                        </table>
                    </div>


                    <div id="dBillReceiveV2" class="card col-12 d_operation" style="display:none">
                        <div class="card-header">
                            <h4 id="header-trust-entry">Bill Receive Edit</h4>
                        </div>
                        <div class="card-body">
                            <form id="form_bill_receive" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12 ">
                                        <!-- <h4 style="margin-bottom: 20px;"><i class="fa fa-user"></i> Trust Account</h4> -->


                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <input class="form-control" name="bill_receive_id"
                                                    id="bill_receive_id" type="hidden" value="0" />
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>


                                                        <a href="javascript:void(0);"
                                                            onclick="cancelBillReceiveEdit()"
                                                            class="btn btn-danger">Cancel</a>

                                                        <button id="btnUpdateBillReceive"
                                                            class="btn btn-success float-right" type="button"
                                                            onclick="updateBillReceive();">
                                                            <i class="cil-plus"></i> Submit
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="div_recon_text" class="col-12" style="display: none">
                                        <span class="text-danger">* This record already recon</span>
                                    </div>

                                    <div class="col-12">
                                        <hr />
                                    </div>
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <label><span class="text-danger">*</span> Payee Name/Disburse
                                                    To</label>
                                                <input class="form-control" name="payee_name" type="text"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label><span class="text-danger">*</span> Request Amount</label>
                                                <input class="form-control" name="amount" type="number"
                                                    value="0" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Payment Description</label>
                                                <textarea class="form-control" name="payment_desc" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <label>Transaction ID</label>
                                                <input class="form-control" name="transaction_id" type="text" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Date Receive</label>
                                                <input class="form-control" type="date" name="payment_date">
                                            </div>
                                        </div>





                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Transaction Type</label>
                                                <select class="form-control" name="payment_type" required>
                                                    <option value="">-- Please select the payment type --
                                                    </option>
                                                    @foreach ($parameters as $index => $parameter)
                                                        <option value="{{ $parameter->parameter_value_3 }}">
                                                            {{ $parameter->parameter_value_2 }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>



                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">


                                        @if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker']))
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Office bank account</label>
                                                    <select class="form-control" name="office_account_id" required>
                                                        <option value="">-- Please select a bank -- </option>
                                                        @foreach ($OfficeBankAccount as $index => $bank)
                                                            <option value="{{ $bank->id }}">{{ $bank->name }}
                                                                ({{ $bank->account_no }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif



                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Cheque No</label>
                                                <input class="form-control" name="cheque_no" type="text" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client Bank</label>
                                                <select class="form-control" name="bank_id">
                                                    <option value="">-- Please select a bank -- </option>
                                                    @foreach ($banks as $index => $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client Bank Account</label>
                                                <input class="form-control" name="bank_account" type="text" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Credit Card No</label>
                                                <input class="form-control" name="credit_card_no" type="text" />
                                            </div>
                                        </div>

                                    </div>


                                </div>
                            </form>

                        </div>
                    </div>

                    <div id="dBillMainPrint" class="card col-12 d_operation" style="display:none">

                        <div class="card-header">
                            <h4 id="header-trust-entry">Print Lumpsum</h4>
                        </div>
                        <div class="card-body">
                            <form id="form_bill_main_info" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12 ">
                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <input class="form-control" name="bill_id" id="bill_id"
                                                    type="hidden" value="0" />
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-12">
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>


                                                        <a href="javascript:void(0);"
                                                            onclick="cancelBillReceiveEdit()"
                                                            class="btn btn-danger">Cancel</a>

                                                        <button class="btn btn-warning float-right" type="button"
                                                            onclick="generateBillLumdReceipt('{{ $case->id }}');"
                                                            data-keyboard="false" data-target="#modalReceipt"
                                                            data-toggle="modal" style="margin-right: 5px;">
                                                            <i class="cil-print"></i> Print
                                                        </button>

                                                        <button class="btn btn-success float-right" type="button"
                                                            onclick="updateBillPrintDetails();">
                                                            <i class="cil-plus"></i> Update
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <label><span class="text-danger">*</span> Payee Name/Disburse
                                                    To</label>
                                                <input class="form-control" name="payee_name" type="text"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label><span class="text-danger">*</span> Amount</label>
                                                <input class="form-control" name="amount" type="number"
                                                    value="0" readonly />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Payment Description</label>
                                                <textarea class="form-control" name="payment_desc" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row dPersonal">
                                            <div class="col">
                                                <label>Transaction ID</label>
                                                <input class="form-control" name="transaction_id" type="text" />
                                                <input class="form-control" name="voucher_no" type="hidden" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Date Receive</label>
                                                <input class="form-control" type="date" name="payment_date">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Transaction Type</label>
                                                <select class="form-control" name="payment_type" required>
                                                    <option value="">-- Please select the payment type --
                                                    </option>
                                                    @foreach ($parameters as $index => $parameter)
                                                        <option value="{{ $parameter->parameter_value_3 }}">
                                                            {{ $parameter->parameter_value_2 }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                         @if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker']))
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Office bank account</label>
                                                    <select class="form-control" name="office_account_id" required>
                                                        <option value="">-- Please select a bank -- </option>
                                                        @foreach ($OfficeBankAccount as $index => $bank)
                                                            <option value="{{ $bank->id }}">{{ $bank->name }}
                                                                ({{ $bank->account_no }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif



                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Cheque No</label>
                                                <input class="form-control" name="cheque_no" type="text" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client Bank</label>
                                                <select class="form-control" name="bank_id">
                                                    <option value="">-- Please select a bank -- </option>
                                                    @foreach ($banks as $index => $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Client Bank Account</label>
                                                <input class="form-control" name="bank_account" type="text" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Credit Card No</label>
                                                <input class="form-control" name="credit_card_no" type="text" />
                                            </div>
                                        </div>

                                    </div>


                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane tab-bill " id="summaryReport" role="tabpanel">

               {{-- @include('dashboard.case.tabs.tab-bill-summary-report') --}}
            </div>
        </div>

        <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a>



    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_edit_quotation">
                    <div class="form-group row ">
                        <div class="col">
                            <label>Original amount</label>
                            <input type="number" value="0" id="txtOriginalAmount" name="txtOriginalAmount"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="txtID" name="txtID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="catID" name="catID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="typeID" name="typeID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="maxID" name="maxID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="minID" name="minID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="item_name" name="item_name"
                                class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>New amount</label>
                            <input type="number" value="0" id="txtNewAmount" name="txtNewAmount"
                                onchange="quotationCalculationEvent()" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Minimum amount</label>
                            <input type="number" value="0" id="txtMinAmt" name="txtMinAmt"
                                class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Max amount</label>
                            <input type="number" value="0" id="txtMaxAmt" name="txtMaxAmt"
                                class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Auto calculate amount</label>
                            <input type="number" value="0" id="txtCalculateAmount"
                                name="txtCalculateAmount" class="form-control" disabled />
                        </div>
                    </div>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="updateQuotationValue()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<div id="myModalInvoice" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_edit_quotation">
                    <div class="form-group row ">
                        <div class="col">
                            <label>Original amount</label>
                            <input type="number" value="0" id="txtOriginalAmountInvoice"
                                name="txtOriginalAmount" class="form-control" disabled />
                            <input type="hidden" value="" id="txtIDInvoice" name="txtID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="catIDInvoice" name="catID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="typeIDInvoice" name="typeID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="item_nameInvoice" name="item_name"
                                class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>New amount</label>
                            <input type="number" value="0" id="txtNewAmountInvoice" name="txtNewAmountInvoice"
                                onchange="quotationCalculationEventInvoice()" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Auto calculate amount</label>
                            <input type="number" value="0" id="txtCalculateAmountInvoice"
                                name="txtCalculateAmount" class="form-control" disabled />
                        </div>
                    </div>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnCloseInv" class="btn btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="updateInvoiceValue()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Modal for Editing Invoice SST -->
<div id="myModalInvoiceSST" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Invoice SST</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_edit_invoice_sst">
                    <div class="form-group row ">
                        <div class="col">
                            <label>Item Name</label>
                            <input type="text" value="" id="item_nameInvoiceSST" name="item_name"
                                class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Original SST</label>
                            <input type="number" value="0" id="txtOriginalSSTInvoice"
                                name="txtOriginalSST" class="form-control" disabled />
                            <input type="hidden" value="" id="txtIDInvoiceSST" name="txtID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="catIDInvoiceSST" name="catID"
                                class="form-control" disabled />
                            <input type="hidden" value="" id="typeIDInvoiceSST" name="typeID"
                                class="form-control" disabled />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>New SST</label>
                            <div class="input-group">
                                <input type="number" step="0.01" value="0" id="txtNewSSTInvoice" name="txtNewSSTInvoice"
                                    class="form-control" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-info" onclick="calculateInvoiceSST()">
                                        <i class="cil-calculator"></i> Calculate
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Click Calculate to auto-calculate SST from invoice amount × SST rate</small>
                            <input type="hidden" value="0" id="txtInvoiceAmountSST" name="txtInvoiceAmount" />
                            <input type="hidden" value="0" id="txtSSTRateSST" name="txtSSTRate" />
                        </div>
                    </div>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnCloseInvSST" class="btn btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="updateInvoiceSST()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Modal for Editing Description -->
<div id="editDescriptionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Description</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form_edit_description">
                    <input type="hidden" id="txtDescriptionID" name="details_id" />
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="txtDescription" name="description" class="form-control" rows="10" style="min-height: 200px;"></textarea>
                        <small class="form-text text-muted">You can use HTML formatting in the description.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="updateDescription()">
                    Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="accountItemModal" class="modal fade" role="dialog">
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
                            <label>Account Item</label>
                            <select id="ddlAccountItem" class="form-control" name="accountItem">
                            </select>
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>New amount</label>
                            <input type="number" value="0" id="txtAmount" name="txtAmount"
                                onchange="quotationCalculationEventAccountItem()" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Auto calculate amount</label>
                            <input type="number" value="0" id="txtCalculateAccountAmount"
                                name="txtCalculateAccountAmount" class="form-control" disabled />
                        </div>
                    </div>

            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnClose2" class="btn btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="addQuotationItem()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<div id="accountItemModalInvoice" class="modal fade" role="dialog">
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
                            <label>Account Item</label>
                            <select id="ddlAccountItemInvoice" class="form-control" name="accountItem">
                            </select>
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>New amount</label>
                            <input type="number" value="0" id="txtAmountInvoice" name="txtAmount"
                                onchange="quotationCalculationEventAccountItem()" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Auto calculate amount</label>
                            <input type="number" value="0" id="txtCalculateAccountAmountInvoice"
                                name="txtCalculateAccountAmount" class="form-control" disabled />
                        </div>
                    </div>

            </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="btnCloseInv2" class="btn btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="addInvoiceItem()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<div id="div-referral" class="card d_operation" style="display:none;min-height:700px">
    <div class="card-header">
        <h4> Referral</h4>
    </div>
    <div class="card-body">

        <label>Search and select the referral</label>
        <div class="form-group row ">
            <div class="col-12">
                <a href="javascript:void(0);" onclick="CancelReferralMode()" class="btn btn-danger">Cancel</a>
            </div>
        </div>


        <div class="form-group row ">


            <div class="col-6">
                <label>Referral Name</label>
                <!-- <input type="text" id="search_referral" name="search_referral" onclick="searchReferaral()" placeholder="Search referral name" class="form-control" /> -->
                <input type="text" id="search_referral" name="search_referral2"
                    class="form-control search_referral" placeholder="Search referral name" autocomplete="off" />
            </div>
            <div class="col-6">
                <div class="form-group float-right ">
                    <a class="btn btn-lg btn-primary" href="javascript:void(0)"
                        onclick="createReferralMode()">Create new referral</a>
                </div>
            </div>

        </div>


        <table class="table table-striped table-bordered datatable">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Company</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbl-referral">
                @if (count($referrals))

                    @foreach ($referrals as $index => $referral)
                        <tr id="referral_row_{{ $referral->id }}" style="display:none">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $referral->name }}</td>
                            <td>{{ $referral->email }}</td>
                            <td>{{ $referral->phone_no }}</td>
                            <td>{{ $referral->company }}</td>
                            <td style="display:none">{{ $referral->ic_no }}</td>
                            <td style="display:none">{{ $referral->bank_id }}</td>
                            <td style="display:none">{{ $referral->bank_account }}</td>
                            <td style="display:none">{{ $referral->bank_name }}</td>
                            <td class="hide">{{ $referral->id }}</td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="selectedReferral('{{ $referral->id }}');"
                                    class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip"
                                    data-placement="top" title="voucer">Select</a>

                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="5">No data</td>
                    </tr>
                @endif

            </tbody>
        </table>



    </div>
</div>

<div id="div-referral-create" class="card d_operation" style="display:none">
    <div class="card-header">
        <h4> Referral</h4>
    </div>
    <div class="card-body">



        <form method="POST" id="form_referral">
            <div class="row" style="margin-top:40px;">
                <div class="col-12 ">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral Information</h4>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                    <div class="form-group row">
                        <div class="col">
                            <label>Referral Name</label>
                            <input class="form-control" type="text" name="name" id="referral_name_new">
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Referral email</label>
                            <input class="form-control" type="text" name="email" id="referral_email_new">
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Bank</label>
                            <select id="ddlBank" class="form-control" name="bank_id">
                                <option value="0">-- Select bank --</option>
                                @foreach ($banks as $index => $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>IC NO</label>
                            <input class="form-control" name="ic_no" id="referral_ic_no_new" type="text" />
                        </div>
                    </div>


                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                    <div class="form-group row ">
                        <div class="col">
                            <label>Referral phone no</label>
                            <input class="form-control" name="phone_no" id="referral_phone_no_new"
                                type="text" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Company</label>
                            <input class="form-control" name="company" id="referral_company_new"
                                type="text" />
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col">
                            <label>Bank Account</label>
                            <input class="form-control" name="bank_account" id="referral_bank_account_new"
                                type="text" />
                        </div>
                    </div>
                </div>
            </div>


            <a href="javascript:void(0);" onclick="referralMode()" class="btn btn-danger">Cancel</a>

            <button id="btnSubmit" class="btn btn-success float-right" type="button"
                onclick="createReferral()">Create Referral</button>
        </form>


    </div>
</div>
