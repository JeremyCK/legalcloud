@extends('dashboard.base')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .invoice-details-table {
        font-size: 0.9rem;
    }
    .invoice-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .detail-item-row {
        cursor: pointer;
    }
    .detail-item-row:hover {
        background-color: #f8f9fa;
    }
    .split-invoice-alert {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 1px solid #64b5f6;
        border-left: 4px solid #2196f3;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .split-invoice-alert h5 {
        color: #1565c0;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .split-invoice-alert .alert-icon {
        font-size: 1.5rem;
        margin-right: 10px;
        color: #1976d2;
    }
    .split-invoice-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .split-invoice-card:hover {
        border-color: #2196f3;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15);
        transform: translateY(-2px);
    }
    .split-invoice-card.current {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-color: #4caf50;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
    }
    .split-invoice-card.current::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #4caf50;
    }
    .split-invoice-card .invoice-number {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1976d2;
        margin-bottom: 5px;
    }
    .split-invoice-card.current .invoice-number {
        color: #2e7d32;
    }
    .split-invoice-card .invoice-amount {
        font-size: 1rem;
        color: #424242;
        font-weight: 500;
    }
    .split-invoice-card .current-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #4caf50;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .split-invoice-card .invoice-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .split-invoice-card .invoice-link:hover {
        text-decoration: none;
        color: inherit;
    }
    .split-invoice-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    /* Back to top floating button */
    #backToTop {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: none;
        z-index: 1000;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #17a2b8;
        color: white;
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    #backToTop:hover {
        background-color: #138496;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    }
    #backToTop i {
        font-size: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <!-- Main Invoice Details Content -->
        <div class="invoice-details-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h4><i class="cil-file"></i> Invoice Details</h4>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('invoice.list') }}" class="btn btn-secondary mr-2">
                                    <i class="cil-arrow-left"></i> Back to List
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-flat">Action</button>
                                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="padding:0">
                                        <button type="button" class="dropdown-item btn-info" id="btn_print_invoice_dropdown" 
                                            data-toggle="modal" 
                                            data-target="#modalPrintInvoice"
                                            data-backdrop="static"
                                            data-keyboard="false"
                                            style="color:white;margin:0">
                                            <i style="margin-right: 10px;" class="cil-print"></i> Print Invoice
                                        </button>
                                        <button type="button" class="dropdown-item btn-info" id="btn_download_pdf_dropdown" style="color:white;margin:0">
                                            <i style="margin-right: 10px;" class="cil-cloud-download"></i> Download Invoice
                                        </button>
                                        <div id="split_invoice_actions_dropdown" style="display: none;">
                                            <div class="dropdown-divider" style="margin:0"></div>
                                            <button type="button" class="dropdown-item btn-warning" onclick="confirmSplitInvoice()" style="color:white;margin:0">
                                                <i style="margin-right: 10px;" class="cil-action-undo"></i> Split Invoice
                                            </button>
                                            <div class="dropdown-divider" style="margin:0"></div>
                                            <button type="button" class="dropdown-item btn-danger" onclick="confirmRemoveInvoice()" style="color:white;margin:0">
                                                <i style="margin-right: 10px;" class="cil-x"></i> Remove Invoice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Split Invoice Alert -->
                        <div id="split_invoice_alert" class="split-invoice-alert" style="display: none;">
                            <h5>
                                <i class="cil-info alert-icon"></i> 
                                Split Invoice
                            </h5>
                            <p class="mb-3">This invoice is part of a split invoice. There are <strong id="split_count">-</strong> invoice(s) for this bill. Click on any invoice below to view its details.</p>
                            <div id="split_invoices_list" class="split-invoice-grid">
                                <!-- Split invoices will be listed here -->
                            </div>
                        </div>

                        <!-- Invoice Header Information -->
                        <div class="invoice-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Invoice Information</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="150"><strong>Invoice No:</strong></td>
                                            <td id="invoice_no_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Invoice Date:</strong></td>
                                            <td id="invoice_date_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Case Ref No:</strong></td>
                                            <td id="case_ref_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amount:</strong></td>
                                            <td id="amount_display"><strong>-</strong></td>
                                        </tr>
                                        <tr id="split_invoice_info_row" style="display: none;">
                                            <td><strong>Invoice Position:</strong></td>
                                            <td id="split_invoice_position">-</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Additional Information</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="150"><strong>Bill No:</strong></td>
                                            <td id="bill_no_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Client Name:</strong></td>
                                            <td id="client_name_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Branch:</strong></td>
                                            <td id="branch_name_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>SST Status:</strong></td>
                                            <td id="sst_status_display">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>SST Rate:</strong></td>
                                            <td id="sst_rate_display">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Party Section -->
                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="cil-user"></i> Billing Party</h5>
                                <button type="button" id="btn_add_billing_party" class="btn btn-sm btn-primary" 
                                    data-toggle="modal" 
                                    data-target="#modalAddBillto"
                                    data-backdrop="static"
                                    data-keyboard="false">
                                    <i class="cil-plus"></i> Add Billing Party
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="billing_party_list">
                                    <p class="text-muted text-center">Loading billing party information...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input for selected_bill_id (required by modal) -->
                        <input type="hidden" id="selected_bill_id" name="selected_bill_id" value="">


                        <!-- Edit Form -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="cil-pencil"></i> Edit Invoice</h5>
                                <button type="button" class="btn btn-success btn-sm" onclick="saveInvoice()">
                                    <i class="cil-save"></i> Save Changes
                                </button>
                            </div>
                            <div class="card-body">
                                <form id="editInvoiceForm">
                                    <input type="hidden" id="edit_invoice_id" name="invoice_id">
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Invoice No</label>
                                                <input type="text" class="form-control" id="edit_invoice_no" name="invoice_no" readonly style="background-color: #e9ecef;">
                                                <small class="form-text text-muted">Invoice number cannot be edited</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Invoice Date</label>
                                                <input type="date" class="form-control" id="edit_invoice_date" name="Invoice_date">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Total Amount (RM)</label>
                                                <input type="text" class="form-control" id="edit_amount" readonly style="background-color: #e9ecef; font-weight: bold; font-size: 1.1rem;">
                                                <small class="form-text text-muted">Calculated automatically from line items</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Invoice Line Items</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered invoice-details-table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="40%">Description</th>
                                                    <th width="15%" class="text-right">Amount (RM)</th>
                                                    <th width="15%" class="text-right">SST (RM)</th>
                                                    <th width="20%" class="text-right">Total (RM)</th>
                                                    <th width="5%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="edit_invoice_details_tbody">
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot id="invoice_totals_footer" style="display: none;">
                                                <tr>
                                                    <td colspan="2" class="text-right"><strong>Grand Total:</strong></td>
                                                    <td class="text-right" id="grand_subtotal">RM 0.00</td>
                                                    <td class="text-right" id="grand_sst">RM 0.00</td>
                                                    <td class="text-right"><strong id="grand_total">RM 0.00</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary" onclick="saveInvoice()">
                                            <i class="cil-save"></i> Save Changes
                                        </button>
                                        <a href="{{ route('invoice.list') }}" class="btn btn-secondary">
                                            <i class="cil-x"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Invoice Details Content -->
        
        <!-- Back to Top Floating Button -->
        <button id="backToTop" type="button" title="Back to Top">
            <i class="cil-arrow-top"></i>
        </button>
    </div>
</div>

<!-- Print Invoice Modal -->
<div class="modal fade" id="modalPrintInvoice" tabindex="-1" role="dialog" aria-labelledby="modalPrintInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%; width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPrintInvoiceLabel">
                    <i class="cil-print"></i> Print Invoice
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div id="div-print-inv" style="min-height: 400px;">
                    <div class="text-center p-5">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3">Loading print view...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printInvoiceContent()">
                    <i class="cil-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/bootstrap.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var invoiceId = {{ $invoiceId }};

    // Define AddBilltoInvoice function early so it's available when modal buttons are clicked
    window.AddBilltoInvoice = function() {
        console.log('AddBilltoInvoice called');
        console.log('selectedParty:', typeof selectedParty !== 'undefined' ? selectedParty : 'undefined');
        
        if (typeof selectedParty === 'undefined' || !selectedParty) {
            if (typeof toastController === 'function') {
                toastController('No party selected', 'warning');
            } else {
                alert('No party selected');
            }
            return;
        }

        if (!window.currentBillId || !window.currentInvoiceId) {
            if (typeof toastController === 'function') {
                toastController('Bill ID or Invoice ID not found', 'warning');
            } else {
                alert('Bill ID or Invoice ID not found');
            }
            return;
        }

        var form_data = new FormData();
        form_data.append("bill_to", selectedParty.customer_name);
        form_data.append("case_id", window.currentCaseId);
        form_data.append("invoice_id", window.currentInvoiceId);
        form_data.append("bill_to_type", "existing_party");
        form_data.append("billing_party_id", selectedParty.id);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        console.log('Sending AJAX request to /AddBilltoInvoice/' + window.currentBillId);
        $.ajax({
            type: 'POST',
            url: '/AddBilltoInvoice/' + window.currentBillId,
            data: form_data,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log('AddBilltoInvoice response:', data);
                if (data.status == 1) {
                    // Close modal
                    $('#modalAddBillto').modal('hide');
                    
                    // Show success notification
                    if (typeof toastController === 'function') {
                        toastController(data.message || 'Added party into list', 'success');
                    }
                    
                    // Reload invoice details
                    if (typeof loadInvoiceDetails === 'function') {
                        loadInvoiceDetails();
                    } else {
                        location.reload();
                    }
                } else {
                    if (typeof toastController === 'function') {
                        toastController(data.message || 'Failed to add invoice recipient', 'warning');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", xhr, status, error);
                var errorMsg = 'Failed to add invoice recipient';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastController === 'function') {
                    toastController(errorMsg, 'warning');
                } else {
                    alert(errorMsg);
                }
            }
        });
    };

    // Also define as regular function for compatibility
    function AddBilltoInvoice() {
        window.AddBilltoInvoice();
    }

    // Print invoice function - loads print view in modal
    function printInvoiceContent() {
        // Find the print container inside modal
        var printElement = $('#modalPrintInvoice #dInvoice-p');
        if (printElement.length === 0) {
            printElement = $('#modalPrintInvoice #div-print-inv');
        }
        
        if (printElement.length > 0) {
            // Use jQuery print plugin if available, otherwise use window.print
            if (typeof $.fn.print !== 'undefined') {
                printElement.print({
                    addGlobalStyles: true,
                    stylesheet: true,
                    rejectWindow: true,
                    noPrintSelector: ".no-print",
                    iframe: false
                });
            } else {
                // Fallback to window.print
                window.print();
            }
        } else {
            if (typeof toastController === 'function') {
                toastController('Print content not loaded', 'warning');
            } else {
                alert('Print content not loaded');
            }
        }
    }

    $(document).ready(function() {
        loadInvoiceDetails();
        
        // Debug: Log when page is ready
        console.log('Invoice details page loaded, invoiceId:', invoiceId);
        console.log('AddBilltoInvoice function defined:', typeof window.AddBilltoInvoice === 'function');
        console.log('invoicePrintMode function defined:', typeof window.invoicePrintMode === 'function');
        
        // Load print view when modal is about to show
        $('#modalPrintInvoice').on('show.bs.modal', function() {
            if (!window.currentInvoiceId) {
                if (typeof toastController === 'function') {
                    toastController('Invoice ID not found', 'warning');
                }
                return;
            }

            // Show loading
            $('#div-print-inv').html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading print view...</p></div>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/loadBillToInvWIthInvoice/' + window.currentInvoiceId,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log('Print invoice response:', data);
                    if (data.status == 1) {
                        // Load the print view
                        $('#div-print-inv').html(data.invoicePrint);
                        
                        // Show print view if it exists (dInvoice-p is inside the loaded content)
                        setTimeout(function() {
                            if ($('#modalPrintInvoice #dInvoice-p').length > 0) {
                                $('#modalPrintInvoice #dInvoice-p').show();
                            }
                        }, 100);
                    } else {
                        $('#div-print-inv').html('<div class="alert alert-danger m-3">Failed to load print view. Please try again.</div>');
                        if (typeof toastController === 'function') {
                            toastController('Failed to load print view', 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading print view:', xhr);
                    $('#div-print-inv').html('<div class="alert alert-danger m-3">Error loading print view. Please try again.</div>');
                    if (typeof toastController === 'function') {
                        toastController('Error loading print view. Please try again.', 'error');
                    }
                }
            });
        });

        // Clear print view when modal is hidden
        $('#modalPrintInvoice').on('hidden.bs.modal', function() {
            $('#div-print-inv').empty();
        });

        // Handle PDF download button click (both old button and dropdown)
        $(document).off('click', '#btn_download_pdf, #btn_download_pdf_dropdown').on('click', '#btn_download_pdf, #btn_download_pdf_dropdown', function(e) {
            e.preventDefault();
            
            if (!window.currentInvoiceId) {
                if (typeof toastController === 'function') {
                    toastController('Invoice ID not found', 'warning');
                } else {
                    alert('Invoice ID not found');
                }
                return;
            }

            // Show loading message
            if (typeof toastController === 'function') {
                toastController('Generating PDF...', 'info');
            }

            // Use fetch to handle errors properly
            var downloadUrl = '/generateInvoicePDF/' + window.currentInvoiceId;
            fetch(downloadUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                if (!response.ok) {
                    return response.json().then(function(data) {
                        throw new Error(data.message || 'Failed to generate PDF');
                    });
                }
                return response.blob();
            })
            .then(function(blob) {
                // Create download link
                var url = window.URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = 'Invoice_' + window.currentInvoiceId + '_' + new Date().toISOString().split('T')[0] + '.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                if (typeof toastController === 'function') {
                    toastController('PDF downloaded successfully', 'success');
                }
            })
            .catch(function(error) {
                console.error('PDF download error:', error);
                if (typeof toastController === 'function') {
                    toastController(error.message || 'Error downloading PDF. Please try again.', 'error');
                } else {
                    alert(error.message || 'Error downloading PDF. Please try again.');
                }
            });
        });
        
        // Note: Event handler for Add Billing Party button is bound after modal is loaded
    });

    function loadInvoiceDetails() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/invoice/' + invoiceId + '/details/data',
            type: 'GET',
            success: function(response) {
                if (response.status === 1) {
                    var invoice = response.invoice;
                    var details = response.details;

                    // Store bill ID and invoice ID for split/remove operations
                    window.currentBillId = invoice.loanCaseBillMain?.id;
                    window.currentInvoiceId = invoice.id;
                    window.currentCaseId = invoice.loanCaseBillMain?.case_id;
                    
                    // Show/hide split invoice actions in dropdown
                    if (invoice.loanCaseBillMain && invoice.loanCaseBillMain.id) {
                        $('#split_invoice_actions_dropdown').show();
                    } else {
                        $('#split_invoice_actions_dropdown').hide();
                    }
                    
                    // Populate header display
                    $('#invoice_no_display').text(invoice.invoice_no || '-');
                    $('#invoice_date_display').text(invoice.Invoice_date ? formatDate(invoice.Invoice_date) : '-');
                    $('#case_ref_display').html(invoice.loanCaseBillMain?.loanCase?.case_ref_no ? 
                        '<a href="/case/' + invoice.loanCaseBillMain.loanCase.id + '" target="_blank">' + invoice.loanCaseBillMain.loanCase.case_ref_no + '</a>' : '-');
                    // Amount will be calculated and displayed after renderInvoiceDetails
                    $('#amount_display').html('<strong>-</strong>');
                    $('#bill_no_display').text(invoice.loanCaseBillMain?.id || '-');
                    $('#client_name_display').text(invoice.client_name || '-');
                    $('#branch_name_display').text(invoice.branch_name || '-');
                    $('#sst_status_display').html(invoice.bln_sst == 1 ? 
                        '<span class="badge badge-success">Paid</span>' : 
                        '<span class="badge badge-warning">Unpaid</span>');
                    
                    // Display SST rate
                    var sstRate = response.sst_rate || 6;
                    $('#sst_rate_display').text(sstRate + '%');

                    // Display billing party
                    displayBillingParty(invoice.billing_party);

                    // Handle split invoice display
                    if (invoice.is_split_invoice && invoice.split_invoices && invoice.split_invoices.length > 1) {
                        $('#split_invoice_alert').show();
                        $('#split_count').text(invoice.split_invoice_count);
                        $('#split_invoice_info_row').show();
                        $('#split_invoice_position').text('Invoice ' + invoice.current_invoice_index + ' of ' + invoice.split_invoice_count);
                        
                        // Build split invoices list with improved design
                        var splitListHtml = '';
                        invoice.split_invoices.forEach(function(splitInv, index) {
                            var isCurrent = splitInv.is_current;
                            var cardClass = isCurrent ? 'split-invoice-card current' : 'split-invoice-card';
                            var invoiceDate = splitInv.Invoice_date ? formatDate(splitInv.Invoice_date) : 'N/A';
                            
                            splitListHtml += '<div class="' + cardClass + '">';
                            
                            if (isCurrent) {
                                splitListHtml += '<span class="current-badge">Current</span>';
                            }
                            
                            if (isCurrent) {
                                splitListHtml += '<div>';
                            } else {
                                splitListHtml += '<a href="/invoice/' + splitInv.id + '/details" class="invoice-link">';
                                splitListHtml += '<div>';
                            }
                            
                            splitListHtml += '<div class="invoice-number">';
                            splitListHtml += '<i class="cil-file"></i> Invoice ' + (index + 1);
                            splitListHtml += '</div>';
                            
                            splitListHtml += '<div class="mb-2">';
                            splitListHtml += '<strong>' + (splitInv.invoice_no || 'N/A') + '</strong>';
                            splitListHtml += '</div>';
                            
                            splitListHtml += '<div class="invoice-amount">';
                            splitListHtml += '<i class="cil-money"></i> RM ' + formatCurrency(splitInv.amount || 0);
                            splitListHtml += '</div>';
                            
                            if (invoiceDate !== 'N/A') {
                                splitListHtml += '<div class="mt-2" style="font-size: 0.85rem; color: #757575;">';
                                splitListHtml += '<i class="cil-calendar"></i> ' + invoiceDate;
                                splitListHtml += '</div>';
                            }
                            
                            splitListHtml += '</div>';
                            
                            if (!isCurrent) {
                                splitListHtml += '</a>';
                            }
                            
                            splitListHtml += '</div>';
                        });
                        $('#split_invoices_list').html(splitListHtml);
                    } else {
                        $('#split_invoice_alert').hide();
                        $('#split_invoice_info_row').hide();
                    }

                    // Populate edit form
                    $('#edit_invoice_id').val(invoice.id);
                    $('#edit_invoice_no').val(invoice.invoice_no || '');
                    $('#edit_invoice_date').val(invoice.Invoice_date ? invoice.Invoice_date.split(' ')[0] : '');
                    // Amount will be calculated and set after rendering details

                    // Render invoice details with sections
                    var groupedDetails = response.grouped_details || [];
                    var sstRate = response.sst_rate || 6;
                    renderInvoiceDetails(groupedDetails, sstRate);
                } else {
                    toastController(response.message || 'Failed to load invoice details', 'warning');
                }
            },
            error: function(xhr) {
                toastController('Error loading invoice details. Please try again.', 'warning');
                console.error('Error:', xhr);
            }
        });
    }

    var globalSequence = 1;
    var globalSstRate = 6;

    function renderInvoiceDetails(groupedDetails, sstRate) {
        globalSstRate = sstRate || 6;
        globalSequence = 1;
        var tbody = $('#edit_invoice_details_tbody');
        tbody.empty();

        if (!groupedDetails || groupedDetails.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted">No line items found</td></tr>');
            $('#invoice_totals_footer').hide();
            return;
        }

        var grandSubtotal = 0;
        var grandSst = 0;
        var grandTotal = 0;

        groupedDetails.forEach(function(category) {
            // Reset sequence for each category
            globalSequence = 1;
            
            // Category header row with Add button
            var categoryRow = '<tr class="table-secondary" style="background-color: #6c757d; color: white;">' +
                '<td colspan="6">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                        '<strong>' + (category.category_name || 'Uncategorized') + '</strong>' +
                        '<button type="button" class="btn btn-sm btn-light" onclick="openAddItemModal(' + category.category_id + '); return false;" title="Add Item">' +
                            '<i class="cil-plus"></i> Add' +
                        '</button>' +
                    '</div>' +
                '</td>' +
            '</tr>';
            tbody.append(categoryRow);

            var categorySubtotal = 0;
            var categorySst = 0;
            var categoryTotal = 0;
            var isTaxable = category.category_taxable == 1 || category.category_id == 1;

            // Items in this category
            if (category.items && category.items.length > 0) {
                category.items.forEach(function(detail) {
                    var amount = parseFloat(detail.amount || 0);
                    var sstAmount = 0;
                    var totalWithSst = amount;

                    if (isTaxable) {
                        // Calculate SST with special rounding rule
                        var sstCalculation = amount * (globalSstRate / 100);
                        var sstString = sstCalculation.toFixed(3);
                        
                        if (sstString.slice(-1) === '5') {
                            sstAmount = Math.floor(sstCalculation * 100) / 100; // Round down
                        } else {
                            sstAmount = Math.round(sstCalculation * 100) / 100; // Normal rounding
                        }
                        totalWithSst = amount + sstAmount;
                    }

                    categorySubtotal += amount;
                    categorySst += sstAmount;
                    categoryTotal += totalWithSst;

                    var row = '<tr class="detail-item-row" data-category-id="' + category.category_id + '" data-detail-id="' + detail.id + '">' +
                        '<td class="text-center">' + globalSequence + '</td>' +
                        '<td>' + (detail.account_name || '-') + '</td>' +
                        '<td class="text-right">' +
                            '<input type="number" step="0.01" class="form-control form-control-sm invoice-detail-amount text-right" ' +
                            'data-detail-id="' + detail.id + '" ' +
                            'data-category-id="' + category.category_id + '" ' +
                            'data-taxable="' + (isTaxable ? '1' : '0') + '" ' +
                            'value="' + amount.toFixed(2) + '" ' +
                            'onchange="recalculateRow(this)">' +
                        '</td>' +
                        '<td class="text-right row-sst" data-detail-id="' + detail.id + '">' +
                            (isTaxable ? formatCurrency(sstAmount) : '-') +
                        '</td>' +
                        '<td class="text-right row-total" data-detail-id="' + detail.id + '">' +
                            '<strong>' + formatCurrency(totalWithSst) + '</strong>' +
                        '</td>' +
                        '<td class="text-center">' +
                            '<button type="button" class="btn btn-sm btn-danger" onclick="deleteDetailItem(' + detail.id + ')" title="Delete">' +
                                '<i class="cil-trash"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
                    tbody.append(row);
                    globalSequence++;
                });
            }

            // Category subtotal row
            var subtotalRow = '<tr class="table-info" style="background-color: #d1ecf1;">' +
                '<td colspan="2" class="text-right"><strong>Subtotal (' + (category.category_name || 'Uncategorized') + '):</strong></td>' +
                '<td class="text-right category-subtotal" data-category-id="' + category.category_id + '">' +
                    '<strong>' + formatCurrency(categorySubtotal) + '</strong>' +
                '</td>' +
                '<td class="text-right category-sst" data-category-id="' + category.category_id + '">' +
                    (isTaxable ? '<strong>' + formatCurrency(categorySst) + '</strong>' : '-') +
                '</td>' +
                '<td class="text-right category-total" data-category-id="' + category.category_id + '">' +
                    '<strong>' + formatCurrency(categoryTotal) + '</strong>' +
                '</td>' +
                '<td></td>' +
            '</tr>';
            tbody.append(subtotalRow);

            grandSubtotal += categorySubtotal;
            grandSst += categorySst;
            grandTotal += categoryTotal;
        });

        // Update grand totals
        $('#grand_subtotal').text('RM ' + formatCurrency(grandSubtotal));
        $('#grand_sst').text('RM ' + formatCurrency(grandSst));
        $('#grand_total').text('RM ' + formatCurrency(grandTotal));
        $('#invoice_totals_footer').show();
        
        // Update main amount field
        $('#edit_amount').val('RM ' + formatCurrency(grandTotal));
        $('#amount_display').html('<strong>RM ' + formatCurrency(grandTotal) + '</strong>');
    }

    function recalculateRow(input) {
        var $input = $(input);
        var amount = parseFloat($input.val() || 0);
        var detailId = $input.data('detail-id');
        var categoryId = $input.data('category-id');
        var isTaxable = $input.data('taxable') == '1';

        var sstAmount = 0;
        var totalWithSst = amount;

        if (isTaxable) {
            // Calculate SST with special rounding rule
            var sstCalculation = amount * (globalSstRate / 100);
            var sstString = sstCalculation.toFixed(3);
            
            if (sstString.slice(-1) === '5') {
                sstAmount = Math.floor(sstCalculation * 100) / 100; // Round down
            } else {
                sstAmount = Math.round(sstCalculation * 100) / 100; // Normal rounding
            }
            totalWithSst = amount + sstAmount;
        }

        // Update row SST and Total
        $('.row-sst[data-detail-id="' + detailId + '"]').text(isTaxable ? formatCurrency(sstAmount) : '-');
        $('.row-total[data-detail-id="' + detailId + '"]').html('<strong>' + formatCurrency(totalWithSst) + '</strong>');

        // Recalculate category subtotals
        recalculateCategorySubtotal(categoryId);
        
        // Recalculate grand totals
        recalculateGrandTotals();
    }

    function recalculateCategorySubtotal(categoryId) {
        var categorySubtotal = 0;
        var categorySst = 0;
        var categoryTotal = 0;
        var isTaxable = false;

        $('tr.detail-item-row[data-category-id="' + categoryId + '"]').each(function() {
            var $row = $(this);
            var amount = parseFloat($row.find('.invoice-detail-amount').val() || 0);
            isTaxable = $row.find('.invoice-detail-amount').data('taxable') == '1';
            
            var sstAmount = 0;
            if (isTaxable) {
                var sstCalculation = amount * (globalSstRate / 100);
                var sstString = sstCalculation.toFixed(3);
                
                if (sstString.slice(-1) === '5') {
                    sstAmount = Math.floor(sstCalculation * 100) / 100;
                } else {
                    sstAmount = Math.round(sstCalculation * 100) / 100;
                }
            }
            
            categorySubtotal += amount;
            categorySst += sstAmount;
            categoryTotal += amount + sstAmount;
        });

        // Update category subtotal row
        $('.category-subtotal[data-category-id="' + categoryId + '"]').html('<strong>' + formatCurrency(categorySubtotal) + '</strong>');
        $('.category-sst[data-category-id="' + categoryId + '"]').html(isTaxable ? '<strong>' + formatCurrency(categorySst) + '</strong>' : '-');
        $('.category-total[data-category-id="' + categoryId + '"]').html('<strong>' + formatCurrency(categoryTotal) + '</strong>');
    }

    function recalculateGrandTotals() {
        var grandSubtotal = 0;
        var grandSst = 0;
        var grandTotal = 0;

        $('.category-subtotal').each(function() {
            var text = $(this).text().replace('RM ', '').replace(/,/g, '');
            grandSubtotal += parseFloat(text || 0);
        });

        $('.category-sst').each(function() {
            var text = $(this).text();
            if (text !== '-') {
                text = text.replace('RM ', '').replace(/,/g, '').replace('<strong>', '').replace('</strong>', '');
                grandSst += parseFloat(text || 0);
            }
        });

        $('.category-total').each(function() {
            var text = $(this).text().replace('RM ', '').replace(/,/g, '').replace('<strong>', '').replace('</strong>', '');
            grandTotal += parseFloat(text || 0);
        });

        $('#grand_subtotal').text('RM ' + formatCurrency(grandSubtotal));
        $('#grand_sst').text('RM ' + formatCurrency(grandSst));
        $('#grand_total').text('RM ' + formatCurrency(grandTotal));
        
        // Update main amount field
        $('#edit_amount').val('RM ' + formatCurrency(grandTotal));
        $('#amount_display').html('<strong>RM ' + formatCurrency(grandTotal) + '</strong>');
    }

    function deleteDetailItem(detailId) {
        Swal.fire({
            title: 'Delete this item?',
            text: 'This will remove the item from all split invoices if applicable.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                if (!window.currentBillId) {
                    toastController('Bill ID not found', 'warning');
                    return;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: '/invoice/delete-item/' + window.currentBillId,
                    data: {
                        details_id: detailId
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            toastController(data.message || 'Item deleted successfully', 'success');
                            loadInvoiceDetails(); // Reload to show updated data
                        } else {
                            toastController(data.message || 'Failed to delete item', 'warning');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error deleting item. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastController(errorMsg, 'warning');
                        console.error('Error:', xhr);
                    }
                });
            }
        });
    }

    function saveInvoice() {
        var invoiceId = $('#edit_invoice_id').val();
        var invoiceNo = $('#edit_invoice_no').val();
        var invoiceDate = $('#edit_invoice_date').val();
        var amount = $('#edit_amount').val();

        // Only invoice number is required
        if (!invoiceNo) {
            toastController('Please fill in Invoice No (required field)', 'warning');
            return;
        }

        // Collect invoice details
        var details = [];
        $('.invoice-detail-amount').each(function() {
            var detailId = $(this).data('detail-id');
            var detailAmount = $(this).val();
            if (detailId && detailAmount !== undefined) {
                details.push({
                    id: detailId,
                    amount: detailAmount
                });
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/invoice/' + invoiceId + '/update',
            type: 'POST',
            data: {
                invoice_no: invoiceNo,
                Invoice_date: invoiceDate,
                details: details
            },
            success: function(response) {
                if (response.status === 1) {
                    toastController('Invoice updated successfully', 'success');
                    loadInvoiceDetails(); // Reload to show updated data
                } else {
                    toastController(response.message || 'Failed to update invoice', 'warning');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error updating invoice. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastController(errorMsg, 'warning');
                console.error('Error:', xhr);
            }
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        var date = new Date(dateString);
        return date.toLocaleDateString('en-GB');
    }

    function formatCurrency(amount) {
        return parseFloat(amount || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function confirmSplitInvoice() {
        if (!window.currentBillId) {
            toastController('Bill ID not found', 'warning');
            return;
        }

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
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: '/invoice/split/' + window.currentBillId,
                    data: {},
                    success: function(data) {
                        if (data.status == 1) {
                            toastController(data.message || 'Invoice split successfully', 'success');
                            // Reload the page to show the new split invoice
                            setTimeout(function() {
                                if (data.new_invoice_id) {
                                    window.location.href = '/invoice/' + data.new_invoice_id + '/details';
                                } else {
                                    loadInvoiceDetails();
                                }
                            }, 1500);
                        } else {
                            toastController(data.message || 'Failed to split invoice', 'warning');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred while splitting the invoice';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastController(errorMsg, 'warning');
                        console.error('Error:', xhr);
                    }
                });
            }
        });
    }

    function confirmRemoveInvoice() {
        if (!window.currentBillId) {
            toastController('Bill ID not found', 'warning');
            return;
        }

        // Load all invoices for this bill
        $.ajax({
            url: '/invoice/get-bill-invoices/' + window.currentBillId,
            type: 'GET',
            success: function(response) {
                if (response.status == 1 && response.invoices && response.invoices.length > 0) {
                    showRemoveInvoiceModal(response.invoices, response.main_invoice_id);
                } else {
                    toastController('No invoices found for this bill', 'warning');
                }
            },
            error: function() {
                toastController('Error loading invoices', 'warning');
            }
        });
    }

    function showRemoveInvoiceModal(invoices, mainInvoiceId) {
        var modalHtml = '<div class="modal fade" id="removeInvoiceModal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<h5 class="modal-title">Remove Invoice(s)</h5>' +
            '<button type="button" class="close" onclick="closeRemoveInvoiceModal()" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<p class="text-muted">Select invoice(s) to remove. The main invoice cannot be removed.</p>' +
            '<div class="list-group" style="max-height: 400px; overflow-y: auto;">';

        invoices.forEach(function(invoice) {
            var isMain = invoice.id == mainInvoiceId;
            var isDisabled = isMain;
            var disabledClass = isDisabled ? 'disabled' : '';
            var checkedAttr = isDisabled ? '' : '';
            
            modalHtml += '<div class="list-group-item ' + disabledClass + '">' +
                '<div class="form-check">' +
                '<input class="form-check-input invoice-checkbox" type="checkbox" ' +
                'value="' + invoice.id + '" id="invoice_' + invoice.id + '" ' +
                (isDisabled ? 'disabled' : checkedAttr) + '>' +
                '<label class="form-check-label" for="invoice_' + invoice.id + '" style="width: 100%;">' +
                '<div class="d-flex justify-content-between align-items-center">' +
                '<div>' +
                '<strong>' + (invoice.invoice_no || 'N/A') + '</strong>' +
                (isMain ? ' <span class="badge badge-primary">Main Invoice</span>' : '') +
                '<br>' +
                '<small class="text-muted">Amount: RM ' + parseFloat(invoice.amount || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</small>' +
                '</div>' +
                '</div>' +
                '</label>' +
                '</div>' +
                '</div>';
        });

        modalHtml += '</div>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" onclick="closeRemoveInvoiceModal()">Cancel</button>' +
            '<button type="button" class="btn btn-danger" onclick="removeSelectedInvoices()">Remove Selected</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        // Remove existing modal if any
        $('#removeInvoiceModal').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#removeInvoiceModal').css({
            'display': 'block',
            'z-index': '1050'
        }).addClass('show');
        $('body').addClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').append('<div class="modal-backdrop fade show" style="z-index: 1040;"></div>');
    }

    function closeRemoveInvoiceModal() {
        $('#removeInvoiceModal').css('display', 'none').removeClass('show');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('#removeInvoiceModal').remove();
    }

    function removeSelectedInvoices() {
        var selectedInvoices = [];
        $('.invoice-checkbox:checked').each(function() {
            selectedInvoices.push($(this).val());
        });

        if (selectedInvoices.length === 0) {
            toastController('Please select at least one invoice to remove', 'warning');
            return;
        }

        Swal.fire({
            title: 'Remove Selected Invoices?',
            text: 'This will delete ' + selectedInvoices.length + ' invoice(s) and redistribute amounts to remaining invoices. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove invoices one by one sequentially
                var removedCount = 0;
                var totalCount = selectedInvoices.length;
                var currentIndex = 0;

                function removeNext() {
                    if (currentIndex >= selectedInvoices.length) {
                        // All done
                        if (removedCount > 0) {
                            toastController(removedCount + ' invoice(s) removed successfully', 'success');
                            closeRemoveInvoiceModal();
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            toastController('Failed to remove invoices', 'warning');
                        }
                        return;
                    }

                    var invoiceId = selectedInvoices[currentIndex];
                    currentIndex++;

                    $.ajax({
                        type: 'DELETE',
                        url: '/invoice/' + invoiceId + '/remove',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.status == 1) {
                                removedCount++;
                            }
                            removeNext(); // Continue with next invoice
                        },
                        error: function(xhr) {
                            console.error('Error removing invoice ' + invoiceId, xhr);
                            removeNext(); // Continue even if one fails
                        }
                    });
                }

                // Start removing
                removeNext();
            }
        });
    }

    function showAddItemModal() {
        $('#accountItemModalInvoice').css({
            'display': 'block',
            'z-index': '1050'
        }).addClass('show');
        $('body').addClass('modal-open');
        // Remove existing backdrop
        $('.modal-backdrop').remove();
        // Add backdrop
        $('body').append('<div class="modal-backdrop fade show" style="z-index: 1040;"></div>');
    }

    function hideAddItemModal() {
        // Destroy Select2 before hiding modal
        if ($('#ddlAccountItemInvoice').hasClass('select2-hidden-accessible')) {
            $('#ddlAccountItemInvoice').select2('destroy');
        }
        $('#accountItemModalInvoice').css('display', 'none').removeClass('show');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    }

    function closeAddItemModal() {
        hideAddItemModal();
    }

    function openAddItemModal(categoryId) {
        if (!window.currentBillId) {
            toastController('Bill ID not found', 'warning');
            return;
        }

        // Load account items for this category
        $.ajax({
            url: '/invoice/account-items/' + categoryId,
            type: 'GET',
            success: function(response) {
                if (response.status == 1) {
                    var options = '<option value="0">Select Account Item</option>';
                    response.items.forEach(function(item) {
                        options += '<option value="' + item.id + '" id="' + item.id + '">' + item.name + '</option>';
                    });
                    $('#ddlAccountItemInvoice').html(options);
                    
                    // Initialize Select2 if not already initialized
                    if (!$('#ddlAccountItemInvoice').hasClass('select2-hidden-accessible')) {
                        $('#ddlAccountItemInvoice').select2({
                            placeholder: 'Select Account Item',
                            allowClear: true,
                            width: '100%'
                        });
                    } else {
                        // If already initialized, destroy and reinitialize
                        $('#ddlAccountItemInvoice').select2('destroy');
                        $('#ddlAccountItemInvoice').select2({
                            placeholder: 'Select Account Item',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                    
                    $('#catIDInvoice').val(categoryId);
                    $('#txtAmountInvoice').val(0);
                    // Show modal
                    showAddItemModal();
                } else {
                    toastController('Failed to load account items', 'warning');
                }
            },
            error: function() {
                toastController('Error loading account items', 'warning');
            }
        });
    }

    function addInvoiceItem() {
        var accountItemId = $('#ddlAccountItemInvoice').val();
        var amount = $('#txtAmountInvoice').val();
        var categoryId = $('#catIDInvoice').val();

        console.log('Adding invoice item:', {
            accountItemId: accountItemId,
            amount: amount,
            categoryId: categoryId,
            billId: window.currentBillId
        });

        if (!accountItemId || accountItemId == 0) {
            toastController('Please select an account item', 'warning');
            return;
        }

        if (!amount || parseFloat(amount) <= 0) {
            toastController('Please enter a valid amount', 'warning');
            return;
        }

        if (!window.currentBillId) {
            toastController('Bill ID not found', 'warning');
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '/invoice/add-item/' + window.currentBillId,
            data: {
                details_id: accountItemId,
                NewAmount: amount,
                catID: categoryId
            },
            success: function(data) {
                console.log('Add item response:', data);
                if (data.status == 1) {
                    toastController(data.message || 'Item added successfully', 'success');
                    hideAddItemModal();
                    // Small delay to ensure database is updated
                    setTimeout(function() {
                        console.log('Reloading invoice details...');
                        loadInvoiceDetails(); // Reload to show updated data
                    }, 500);
                } else {
                    toastController(data.message || 'Failed to add item', 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error('Add item error:', {
                    status: status,
                    error: error,
                    response: xhr.responseJSON,
                    responseText: xhr.responseText
                });
                var errorMsg = 'Error adding item. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var errorData = JSON.parse(xhr.responseText);
                        if (errorData.message) {
                            errorMsg = errorData.message;
                        }
                    } catch(e) {
                        errorMsg = 'Server error: ' + xhr.status + ' ' + xhr.statusText;
                    }
                }
                toastController(errorMsg, 'warning');
            }
        });
    }
</script>

<!-- Modal for Adding Invoice Item -->
<div id="accountItemModalInvoice" class="modal fade" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Invoice Item</h5>
                <button type="button" class="close" onclick="closeAddItemModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_add_invoice_item">
                    <div class="form-group">
                        <label>Account Item <span class="text-danger">*</span></label>
                        <select id="ddlAccountItemInvoice" class="form-control select2-single" name="accountItem" style="width: 100%;" required>
                            <option value="0">Select Account Item</option>
                        </select>
                        <input type="hidden" id="catIDInvoice" value="">
                    </div>
                    <div class="form-group">
                        <label>Amount (RM) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" value="0" id="txtAmountInvoice" name="txtAmount" class="form-control" required min="0.01" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="closeAddItemModal()">Close</button>
                <button type="button" class="btn btn-success" onclick="addInvoiceItem()">Add Item</button>
            </div>
        </div>
    </div>
</div>

<script>
    function displayBillingParty(billingParty) {
        var html = '';
        if (billingParty && billingParty.id) {
            // Hide add button if billing party exists
            $('#btn_add_billing_party').hide();
            
            var completedBadge = billingParty.completed == 1 ? 
                '<span class="badge badge-success ml-2">Completed</span>' : '';
            
            html = '<div class="card border-primary">' +
                '<div class="card-body">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                '<div class="flex-grow-1">' +
                '<h6 class="mb-2">' + (billingParty.customer_name || 'N/A') + completedBadge + '</h6>' +
                '<p class="mb-1 text-muted"><small><strong>Code:</strong> ' + (billingParty.customer_code || 'N/A') + '</small></p>' +
                (billingParty.email ? '<p class="mb-1 text-muted"><small><strong>Email:</strong> ' + billingParty.email + '</small></p>' : '') +
                (billingParty.phone ? '<p class="mb-1 text-muted"><small><strong>Phone:</strong> ' + billingParty.phone + '</small></p>' : '') +
                '</div>' +
                '<div>' +
                (billingParty.completed != 1 ? 
                    '<button type="button" class="btn btn-sm btn-danger" onclick="removeBillingParty()" title="Remove Billing Party">' +
                        '<i class="cil-x"></i> Remove' +
                    '</button>' : 
                    '<span class="badge badge-secondary">Cannot Remove</span>') +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
        } else {
            // Show add button if no billing party
            $('#btn_add_billing_party').show();
            html = '<p class="text-muted text-center mb-0">No billing party assigned to this invoice.</p>';
        }
        $('#billing_party_list').html(html);
    }


    function removeBillingParty() {
        if (!window.currentInvoiceId) {
            toastController('Invoice ID not found', 'warning');
            return;
        }

        Swal.fire({
            title: 'Remove Billing Party?',
            text: 'Are you sure you want to remove the billing party from this invoice?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: '/removeBillto/' + window.currentInvoiceId,
                    success: function(data) {
                        if (data.status == 1) {
                            toastController(data.message || 'Billing party removed successfully', 'success');
                            // Show add button after removal
                            $('#btn_add_billing_party').show();
                            loadInvoiceDetails(); // Reload to show updated data
                        } else {
                            toastController(data.message || 'Failed to remove billing party', 'warning');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error removing billing party. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastController(errorMsg, 'warning');
                        console.error('Error:', xhr);
                    }
                });
            }
        });
    }
</script>

<!-- Include Billing Party Modal -->
@include('dashboard.case.modal.modal-add-billto')

<script>
    // Override openAddBillingPartyModal to show modal instead of redirecting
    // Define in window scope to ensure it's accessible
    window.openAddBillingPartyModal = function() {
        console.log('=== openAddBillingPartyModal called ===');
        console.log('currentBillId:', window.currentBillId);
        console.log('currentInvoiceId:', window.currentInvoiceId);
        
        if (!window.currentBillId || !window.currentInvoiceId) {
            console.error('Missing IDs - currentBillId:', window.currentBillId, 'currentInvoiceId:', window.currentInvoiceId);
            if (typeof toastController === 'function') {
                toastController('Bill ID or Invoice ID not found', 'warning');
            }
            return;
        }

        // Check if modal exists
        var modalElement = $('#modalAddBillto');
        console.log('Modal element found:', modalElement.length > 0);
        if (modalElement.length === 0) {
            console.error('Modal #modalAddBillto not found in DOM');
            if (typeof toastController === 'function') {
                toastController('Modal not loaded. Please refresh the page.', 'error');
            } else {
                alert('Modal not loaded. Please refresh the page.');
            }
            return;
        }

        // Set the selected_bill_id for the modal
        if ($('#selected_bill_id').length === 0) {
            $('body').append('<input type="hidden" id="selected_bill_id" name="selected_bill_id" value="' + window.currentBillId + '">');
            console.log('Created selected_bill_id input');
        } else {
            $('#selected_bill_id').val(window.currentBillId);
            console.log('Updated selected_bill_id to:', window.currentBillId);
        }

        // Initialize modal in "Add Party to Invoice" mode
        if (typeof AddPartyInvoiceMode === 'function') {
            console.log('Calling AddPartyInvoiceMode with invoiceId:', window.currentInvoiceId);
            AddPartyInvoiceMode(window.currentInvoiceId);
        } else {
            console.warn('AddPartyInvoiceMode function not found - modal may not initialize correctly');
        }
        
        // Show the modal using Bootstrap
        console.log('Attempting to show modal...');
        try {
            // Try Bootstrap 3/4 method
            if (typeof $.fn.modal !== 'undefined') {
                modalElement.modal('show');
                console.log('Modal.show() called successfully');
            } else {
                console.error('Bootstrap modal plugin not available');
                // Fallback: show modal manually
                modalElement.css('display', 'block');
                modalElement.addClass('show');
                $('body').addClass('modal-open');
                $('.modal-backdrop').remove();
                $('body').append('<div class="modal-backdrop fade show"></div>');
                console.log('Modal shown using fallback method');
            }
        } catch (e) {
            console.error('Error showing modal:', e);
            console.error('Error stack:', e.stack);
            if (typeof toastController === 'function') {
                toastController('Error opening modal: ' + e.message, 'error');
            } else {
                alert('Error opening modal: ' + e.message);
            }
        }
    };

    // Also define as regular function for compatibility
    function openAddBillingPartyModal() {
        window.openAddBillingPartyModal();
    }

    // Back to Top Button Functionality
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('#backToTop').fadeIn();
        } else {
            $('#backToTop').fadeOut();
        }
    });

    $('#backToTop').click(function() {
        $('html, body').animate({scrollTop: 0}, 600);
        return false;
    });

    // Use Bootstrap's modal event to initialize when modal is about to show
    $(document).ready(function() {
        $('#modalAddBillto').on('show.bs.modal', function() {
            console.log('Modal is about to show');
            
            if (!window.currentBillId || !window.currentInvoiceId) {
                console.error('Missing IDs - currentBillId:', window.currentBillId, 'currentInvoiceId:', window.currentInvoiceId);
                if (typeof toastController === 'function') {
                    toastController('Bill ID or Invoice ID not found', 'warning');
                }
                return;
            }

            // Set the selected_bill_id for the modal
            if ($('#selected_bill_id').length === 0) {
                $('body').append('<input type="hidden" id="selected_bill_id" name="selected_bill_id" value="' + window.currentBillId + '">');
            } else {
                $('#selected_bill_id').val(window.currentBillId);
            }

            // Initialize modal in "Add Party to Invoice" mode
            if (typeof AddPartyInvoiceMode === 'function') {
                console.log('Calling AddPartyInvoiceMode with invoiceId:', window.currentInvoiceId);
                AddPartyInvoiceMode(window.currentInvoiceId);
            } else {
                console.warn('AddPartyInvoiceMode function not found');
            }
            
            // Remove inline onclick handlers from buttons (in case they were re-added)
            $('#btnAddSelectedParty, #btnAddBilltoParty').removeAttr('onclick');
        });
        
        // Remove inline onclick handlers and use event delegation
        // Remove onclick attributes from buttons
        $('#btnAddSelectedParty, #btnAddBilltoParty').removeAttr('onclick');
        
        // Bind click handlers using event delegation
        $(document).off('click', '#btnAddSelectedParty, #btnAddBilltoParty').on('click', '#btnAddSelectedParty, #btnAddBilltoParty', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add party button clicked:', this.id);
            if (typeof window.AddBilltoInvoice === 'function') {
                window.AddBilltoInvoice();
            } else {
                console.error('AddBilltoInvoice function not found');
                if (typeof toastController === 'function') {
                    toastController('AddBilltoInvoice function not available', 'error');
                } else {
                    alert('AddBilltoInvoice function not available');
                }
            }
        });
        
        console.log('Modal show.bs.modal event handler bound');
        console.log('Add party button handlers bound');
    });

    // AddBilltoInvoice function is already defined in the first script block above
    // This ensures it's available when modal buttons with inline onclick are clicked

    // invoicePrintMode and cancelInvoicePrintMode functions are already defined in the first script block above

    // Add cancel button handler if it exists in the print view
    $(document).ready(function() {
        $(document).on('click', '.cancel-invoice-print, #btn_cancel_print, .btn-close-print', function(e) {
            e.preventDefault();
            cancelInvoicePrintMode();
        });
        
        // Also handle the cancel button that might be in the print view
        $(document).on('click', '[onclick*="cancelInvoicePrintMode"]', function(e) {
            e.preventDefault();
            cancelInvoicePrintMode();
        });
    });


@endsection

