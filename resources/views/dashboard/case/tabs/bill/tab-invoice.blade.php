<div class="col-12">
    <div id="div-invoice-summary-details" class="row"></div>
</div>

<div class="col-12">
    <div class="box-tools">
        @if (App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccessInvoicePermission()) == true)


            <table class="table mb-0">
                <tbody>
                    <tr>
                        <td class="fw-medium"><b>Invoice No (Main)</b></td>
                        <td id="lbl_invoice_no">{{ $LoanCaseBillMain->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"><b>Invoice Date</b></td>
                        <td><b class="invoice-date">{{ date('d-m-Y', strtotime(isset($invoiceMain) && $invoiceMain && $invoiceMain->Invoice_date ? $invoiceMain->Invoice_date : $LoanCaseBillMain->invoice_date)) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium"><b>Bill To</b>
                        </td>
                        <td id="lbl_bill_to_party"></td>
                    </tr>
                    <tr>
                        <td class="fw-medium"><b>Status</b></td>
                        <td id="lbl_invoice_status">
                            <b>
                                @if ($LoanCaseBillMain->transferred_pfee_amt > 0)
                                    <span style="color:red">Pfee transffered for this invoice, edit featured
                                        disabled</span>
                                @endif
                            </b>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="btn-group">
                <button type="button" class="btn btn-info btn-lg">Action</button>
                <button type="button" class="btn btn-info btn- dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" style="padding:0">

                    @php
                        $invoiceDateForComparison = isset($invoiceMain) && $invoiceMain && $invoiceMain->Invoice_date ? $invoiceMain->Invoice_date : $LoanCaseBillMain->invoice_date;
                        $today = \Carbon\Carbon::parse($invoiceDateForComparison);
                        $targetDate = \Carbon\Carbon::parse('2025-06-23');
                    @endphp

                    @if ($today < $targetDate)
                        <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-success" href="javascript:void(0)" onclick="invoicePrintMode();"
                            style="color:white"><i class="cil-print" style="margin-right: 10px;"></i> <span></span>Print
                            Invoice
                            <div class="dropdown-divider" style="margin:0"></div>
                    @endif


                    <a class="dropdown-item btn-warning" href="javascript:void(0)" data-backdrop="static"
                        data-keyboard="false" data-keyboard="false" data-toggle="modal" data-target="#modalInvoiceDate"
                        onclick="InvoiceDateModal();" style="color:white;margin:0"><i style="margin-right: 10px;"
                            class="cil-calendar"></i>Update
                        Invoice Date</a>

                    {{-- @if (count($InvoiceBillingParty) == 0)
                        <a class="dropdown-item bg-purple" href="javascript:void(0)" data-backdrop="static"
                            data-keyboard="false" data-keyboard="false" data-toggle="modal"
                                onclick="AddPartyInvoiceMode();"
                            data-target="#modalAddBillto" onclick="" style="color:white;margin:0"><i
                                style="margin-right: 10px;" class="cil-calendar"></i>Add Invoice recipient</a>
                    @endif --}}

                             {{-- @if (count($InvoiceBillingParty) > 0) --}}
                                <a id="btn_split_invoice" class="dropdown-item " href="javascript:void(0)"
                                onclick="confirmSplitInvoice();"
                                style="color:white;margin:0;background-color:purple"><i style="margin-right: 10px;"
                                    class="cil-action-undo"></i>Split Invoice</a>
                        {{-- @endif --}}


                    <a class="dropdown-item btn-info" href="javascript:void(0)" data-backdrop="static"
                        data-keyboard="false" data-keyboard="false" data-toggle="modal"
                        data-target="#modalCloseFileUpdate" onclick="" style="color:white;margin:0"><i
                            style="margin-right: 10px;" class="cil-calendar"></i>Quick Update Master List</a>

                    @if ($case->status != 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0 && $LoanCaseBillMain->bln_sst != 1)
                        <a id="btn_revert_invoice1" class="dropdown-item btn-danger" href="javascript:void(0)"
                            onclick="revertToQuotation();" style="color:white;margin:0"><i style="margin-right: 10px;"
                                class="cil-action-undo"></i>Revert Invoice</a>

                        <a id="btn_revert_invoice2" class="dropdown-item " href="javascript:void(0)"
                            onclick="revertToQuotationWithReserveINVNo();"
                            style="color:white;margin:0;background-color:orange"><i style="margin-right: 10px;"
                                class="cil-action-undo"></i>Revert Invoice with reserve running no</a>

               
                           
                    @endif


                </div>
            </div>
            <br />
            <span class="text-danger"> For new invoice, need to add invoice recipient for E-invoice purpose</span>
        @endif

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
                    <th class="">SST (<span class="lbl_sst_rate">0</span>%)</th>
                    <th class="">Invoice Amount + SST (RM)</th>
                @endif
            </tr>
        </thead>
        <tbody id="tbl-invoice-bill">

            <?php
            $total = 0;
            $totalSST = 0;
            $totalOri = 0;
            $totalEdit = 0;
            $subtotal = 0;
            $totalNoSST = 0;
            $sstTotal = 0;
            $sumSST = 0;
            
            $sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
            ?>
            @if (count($invoice))
                @foreach ($invoice as $index => $cat)
                    <tr style="background-color:grey;color:white">
                        @if (
                            $current_user->menuroles == 'account' ||
                                $current_user->menuroles == 'admin' ||
                                $current_user->menuroles == 'management' ||
                                $current_user->menuroles == 'maker')
                            <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}
                                @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                                    <button class="btn btn-info float-right " data-backdrop="static"
                                        data-keyboard="false"
                                        onclick="addAccountItemModalInvoice('{{ $cat['category']->id }}')"
                                        data-toggle="modal" data-target="#accountItemModalInvoice" type="button"><i
                                            class="cil-plus"></i>Add </button>
                                @endif
                            </td>
                        @elseif($current_user->menuroles == 'sales' || in_array($current_user->id, [51, 32, 13]))
                            <td class="quotation-colspan" colspan="6">{{ $cat['category']->category }}<button
                                    class="btn btn-info float-right " data-backdrop="static" data-keyboard="false"
                                    onclick="addAccountItemModalInvoice('{{ $cat['category']->id }}')"
                                    data-toggle="modal" data-target="#accountItemModalInvoice" type="button"><i
                                        class="cil-plus"></i>Add </button></td>
                        @else
                            <td class="quotation-colspan" colspan="3">{{ $cat['category']->category }}</td>
                        @endif

                        <?php
                        $subtotal = 0;
                        $subtotalGST = 0;
                        $subtotalnosset = 0;
                        $subtotalOri = 0;
                        $subtotalEdit = 0;
            $sumSST = 0;
                        ?>
                    </tr>
                    <?php $category_amount = 0; ?>
                    @foreach ($cat['account_details'] as $index => $details)
                        <?php
                        $row_sst = 0;
                        if ($cat['category']->id == 1 || $cat['category']->id == 4) {
                            // Priority order:
                            // 1. Use custom SST from database (sst column) - this is the manually edited value
                            // 2. Use ori_invoice_sst if available (for split invoices, this stores the total SST)
                            // 3. Otherwise calculate from ori_invoice_amt * sst_rate
                            
                            $hasCustomSst = false;
                            
                            // First, check for custom SST value (sst column) - this takes highest priority
                            $sstRaw = null;
                            if (is_object($details)) {
                                $sstRaw = property_exists($details, 'sst') ? $details->sst : null;
                            } elseif (is_array($details)) {
                                $sstRaw = isset($details['sst']) ? $details['sst'] : null;
                            }
                            
                            // Check if we have a valid custom SST value
                            // Improved check: also accept numeric 0 values and check if it's a valid number
                            $isValidSst = false;
                            if ($sstRaw !== null) {
                                // Convert to string and check if it's a valid number (not empty string)
                                $sstString = trim((string)$sstRaw);
                                if ($sstString !== '' && is_numeric($sstRaw)) {
                                    $isValidSst = true;
                                }
                            }
                            
                            if ($isValidSst) {
                                $row_sst = (float) $sstRaw;
                                $hasCustomSst = true;
                            }
                            
                            // If no custom SST, check ori_invoice_sst (for split invoices)
                            // For split invoices, ori_invoice_sst contains the total SST across all split invoices
                            // We should use it if it's set and greater than 0
                            if (!$hasCustomSst && property_exists($details, 'ori_invoice_sst') && isset($details->ori_invoice_sst) && $details->ori_invoice_sst !== null) {
                                $oriSstValue = (float)$details->ori_invoice_sst;
                                // Use ori_invoice_sst if it's greater than 0 (for split invoices, this is the total SST)
                                if ($oriSstValue > 0) {
                                    $row_sst = $oriSstValue;
                                    $hasCustomSst = true;
                                }
                            }
                            
                            // If still no SST value, calculate from ori_invoice_amt (fallback)
                            // This is important for split invoices where ori_invoice_sst might be 0 or NULL
                            if (!$hasCustomSst && isset($details->ori_invoice_amt) && $details->ori_invoice_amt > 0) {
                                // Calculate from ori_invoice_amt (fallback for backward compatibility)
                                $sst_calculation = $details->ori_invoice_amt * $sst_rate;
                                $sst_string = number_format($sst_calculation, 3, '.', '');
                                
                                if (substr($sst_string, -1) == '5') {
                                    $row_sst = floor($sst_calculation * 100) / 100; // Round down
                                } else {
                                    $row_sst = round($sst_calculation, 2); // Normal rounding
                                }
                                
                            } else if (!$hasCustomSst) {
                                // If ori_invoice_amt is also 0 or not set, SST should be 0
                                $row_sst = 0;
                            }
                            
                            $subtotalGST += $details->ori_invoice_amt + $row_sst;
                            $sumSST += $row_sst;
                        } else {
                            $subtotalGST += round($details->ori_invoice_amt, 2);
                        }
                        
                        $subtotal += round($details->ori_invoice_amt, 2);
                        
                        $subtotalnosset += $details->quo_amount;
                        $subtotalOri += $details->quo_amount;
                        $subtotalEdit += $details->quo_amount;
                        ?>

                        <tr>
                            <td class="text-center" style="width:50px">
                                <input class="form-control" type="hidden" value="{{ $details->quo_amount }}"
                                    id="quo_amount_{{ $details->id }}">
                                <input class="form-control" type="hidden" value="0"
                                    id="bln_modified_{{ $details->id }}">
                                <input type="hidden" name="account_item_id" value="{{ $details->account_item_id }}"
                                    id="account_item_id_{{ $details->id }}">
                                {{ $index + 1 }}
                            </td>
                            <td class="hide" id="item_id_{{ $details->id }}">{{ $details->id }}</td>
                            <td id="item_{{ $details->id }}">{{ $details->account_name }} @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
                                @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                                    @if ($details->item_remark)
                                        <hr />
                                        {!! $details->item_remark !!}
                                    @else
                                        @if ($details->item_desc)
                                            <hr />
                                            {!! $details->item_desc !!}
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td class="text-right" id="amt_{{ $details->id }}">
                                {{ number_format($details->quo_amount, 2, '.', ',') }}</td>

                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) ||
                                    in_array($current_user->id, [51, 32, 13]))
                                <td class="text-right" id="amt_quo_{{ $details->id }}">
                                    {{-- {{ number_format($details->amount, 2, '.', ',') }} --}}
                                    {{ number_format($details->ori_invoice_amt, 2, '.', ',') }}
                            @endif

                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) ||
                                    in_array($current_user->id, [51, 32, 13]))
                                @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                        onclick="editInvoiceModal('{{ $details->ori_invoice_amt }}','{{ $details->id }}','{{ $cat['category']->id }}',1,'{{ $details->account_name }}')"
                                        data-toggle="modal" data-target="#myModalInvoice"
                                        class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>
                                @endif
                            @endif

                            </td>
                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) ||
                                    in_array($current_user->id, [51, 32, 13]))
                                <td class="text-right" id="amt_sst_{{ $details->id }}">
                                    @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                                        {{ number_format($row_sst, 2, '.', ',') }}
                                        @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                                            <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                                onclick="editInvoiceSSTModal('{{ $row_sst }}','{{ $details->id }}','{{ $cat['category']->id }}',1,'{{ $details->account_name }}','{{ $details->ori_invoice_amt }}','{{ $sst_rate }}')"
                                                data-toggle="modal" data-target="#myModalInvoiceSST"
                                                class="btn btn-xs btn-primary"><i class="cil-pencil"></i></a>
                                        @endif
                                    @else
                                        -
                                    @endif

                                    <?php
                                    if ($cat['category']->id == 1 || $cat['category']->id == 4) {
                                        $sstTotal += $row_sst;
                                    }
                                    ?>
                                <td class="text-right" id="amt_sst_quo_{{ $details->id }}">
                                    @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                                        {{ number_format($details->ori_invoice_amt + $row_sst, 2, '.', ',') }}
                                    @else
                                        {{ number_format($details->ori_invoice_amt, 2, '.', ',') }}
                                    @endif
                                    @if ($LoanCaseBillMain->bln_sst == 0 && $LoanCaseBillMain->transferred_pfee_amt <= 0)
                                        <a href="javascript:void(0)"
                                            onclick="deleteInvoiceItem('{{ $details->id }}')"
                                            class="btn btn-xs btn-danger"><i class="cil-x"></i></a>
                                    @endif
                                </td>
                            @endif

                        </tr>
                    @endforeach

                    <?php
                    $total += $subtotal;
                    $totalSST += $subtotalGST;
                    $totalOri += $subtotalOri;
                    $totalEdit += $subtotalEdit;
                    $totalNoSST += $subtotalnosset;
                    ?>

                    <tr>
                        <td>Sub Total</td>
                        <td style="text-align:right" colspan="2">RM
                            {{ number_format($subtotalnosset, 2, '.', ',') }}</td>

                        @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) ||
                                in_array($current_user->id, [51, 32, 13]))
                            <td style="text-align:right">RM {{ number_format($subtotal, 2, '.', ',') }}</td>
                            <td class="" style="text-align:right"
                                id="sub_total_ori_{{ $cat['category']->code }}">
                                @if ($cat['category']->id == 1 || $cat['category']->id == 4)
                                    RM {{ number_format((float) $sumSST, 2, '.', ',') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="" style="text-align:right"
                                id="sub_total_edit_{{ $cat['category']->code }}">
                                RM {{ number_format((float) $subtotalGST, 2, '.', ',') }}
                            </td>
                        @endif

                    </tr>
                @endforeach

                <tr>
                    <td>Total</td>
                    <td style="text-align:right" class="quotation-total-colspan" colspan="2"> RM
                        {{ number_format((float) $totalNoSST, 2, '.', ',') }}</td>
                    @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker', 'sales']) ||
                            in_array($current_user->id, [51, 32, 13]))
                        <td style="text-align:right" class=""> RM
                            {{ number_format((float) $total, 2, '.', ',') }}</td>
                        <td style="text-align:right" class="" id="total_edit"> RM
                            {{ number_format($sumSST, 2, '.', ',') }}
                        </td>
                        <td style="text-align:right" class="" id="total_edit"> RM
                            {{ number_format($totalSST, 2, '.', ',') }}
                        </td>
                    @endif
                </tr>
            @else
                <tr>
                    <td class="text-center" colspan="5">No data</td>
                </tr>
            @endif

        </tbody>
    </table>
</div>

<script>
function confirmSplitInvoice() {
    Swal.fire({
        title: 'Split Invoice',
        text: 'Are you sure you want to split this invoice? This will create a new invoice with divided amounts.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Split Invoice',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            $("#div_full_screen_loading").show();
            
            var form_data = new FormData();
            form_data.append("case_id", {{ $case->id }});
            
            $.ajax({
                type: 'POST',
                url: '/splitInvoice/{{ $LoanCaseBillMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $("#div_full_screen_loading").hide();
                    if (data.status == 1) {
                        // Show success notification using system function
                        toastController(data.message || 'Invoice split successfully', 'success');
                        
                        // Refresh invoice section without reloading whole page
                        if (data.bill_id && typeof loadCaseBill === 'function') {
                            loadCaseBill(data.bill_id);
                        } else {
                            // Fallback: reload page if loadCaseBill is not available
                            console.warn('loadCaseBill function not found or bill_id missing, reloading page');
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        toastController(data.message || 'Failed to split invoice', 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    $("#div_full_screen_loading").hide();
                    var errorMsg = 'An error occurred while splitting the invoice';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastController(errorMsg, 'warning');
                }
            });
        }
    });
}
</script>
