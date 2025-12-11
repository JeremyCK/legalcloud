<div id="modalEditSplitInvoiceDetail" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Edit Split Invoice Details</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form id="formEditSplitInvoiceDetail">
                    <input type="hidden" id="invoice_id" name="invoice_id" value="">
                    <input type="hidden" id="sst_rate" name="sst_rate" value="">
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Invoice No:</strong> <span id="invoice_no_display">-</span>
                                <span class="ml-3"><strong>SST Rate:</strong> <span id="sst_rate_display">-</span>%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th width="30%">Item</th>
                                    <th class="text-right" width="15%">Quotation Amount (RM)</th>
                                    <th class="text-right" width="15%">Invoice Base Amount (RM)</th>
                                    <th class="text-right" width="15%">SST (<span id="sst_rate_header">8.00</span>%)</th>
                                    <th class="text-right" width="20%">Invoice Amount + SST (RM)</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceDetailsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Loading invoice details...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn_close_all" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="saveSplitInvoiceDetails()">
                    <i class="fa fa-save"></i> Save All Changes
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function editSplitInvoiceDetail(invoiceId) {
    if (!invoiceId) {
        toastController('Invalid invoice ID', 'warning');
        return;
    }
    
    // Show loading
    $("#div_full_screen_loading").show();
    
    // Load invoice details via AJAX
    $.ajax({
        url: '/getSplitInvoiceDetails/' + invoiceId,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $("#div_full_screen_loading").hide();
            
            if (response.status == 1 && response.data) {
                var invoice = response.data;
                
                // Set invoice ID and number
                $("#modalEditSplitInvoiceDetail #invoice_id").val(invoice.id);
                $("#modalEditSplitInvoiceDetail #invoice_no_display").text(invoice.invoice_no || 'N/A');
                $("#modalEditSplitInvoiceDetail #sst_rate").val(invoice.sst_rate || 0);
                $("#modalEditSplitInvoiceDetail #sst_rate_display").text(invoice.sst_rate || 0);
                $("#modalEditSplitInvoiceDetail #sst_rate_header").text(invoice.sst_rate || 0);
                
                // Build table with invoice details
                buildInvoiceDetailsTable(invoice.invoice_details || [], invoice.sst_rate || 0);
                
                // Store original values for change tracking after table is built
                setTimeout(function() {
                    originalValues = {};
                    $('input.invoice-amount-input').each(function() {
                        var detailId = $(this).data('detail-id');
                        var originalValue = parseFloat($(this).val() || 0);
                        originalValues[detailId] = originalValue;
                    });
                }, 100);
                
                // Show modal
                $("#modalEditSplitInvoiceDetail").modal('show');
            } else {
                toastController(response.message || 'Failed to load invoice details', 'warning');
            }
        },
        error: function(xhr, status, error) {
            $("#div_full_screen_loading").hide();
            toastController('Failed to load invoice details', 'warning');
        }
    });
}

function buildInvoiceDetailsTable(invoiceDetails, sstRate) {
    var tbody = $("#invoiceDetailsTableBody");
    tbody.empty();
    
    if (!invoiceDetails || invoiceDetails.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center text-muted">No invoice details found</td></tr>');
        return;
    }
    
    var rowIndex = 1;
    var sstRateDecimal = parseFloat(sstRate) / 100;
    
    invoiceDetails.forEach(function(category) {
        var categoryCode = category.category.code || 'CAT' + category.category.id;
        var categoryId = category.category.id;
        
        // Category header row
        tbody.append(
            '<tr style="background-color:#6c757d;color:white;font-weight:bold;" data-category-code="' + categoryCode + '" data-category-id="' + categoryId + '">' +
            '<td colspan="6">' + category.category.category + '</td>' +
            '</tr>'
        );
        
        // Items in this category
        category.account_details.forEach(function(detail) {
            var quoAmount = parseFloat(detail.quo_amount || 0);
            var invoiceAmount = parseFloat(detail.amount || 0); // Use 'amount' column, not 'ori_invoice_amt'
            var sstAmount = 0;
            var totalWithSst = invoiceAmount;
            
            // Calculate SST if category is 1 or 4
            if (categoryId == 1 || categoryId == 4) {
                sstAmount = invoiceAmount * sstRateDecimal;
                totalWithSst = invoiceAmount + sstAmount;
            }
            
            var row = '<tr data-detail-id="' + detail.id + '" data-category-code="' + categoryCode + '" data-category-id="' + categoryId + '">' +
                '<td class="text-center">' + rowIndex + '</td>' +
                '<td>' + (detail.account_name || 'N/A') + '</td>' +
                '<td class="text-right">' + quoAmount.toFixed(2) + '</td>' +
                '<td class="text-right">' +
                '<input type="number" step="0.01" min="0" class="form-control form-control-sm text-right invoice-amount-input" ' +
                'value="' + invoiceAmount.toFixed(2) + '" ' +
                'data-detail-id="' + detail.id + '" ' +
                'data-category-id="' + categoryId + '" ' +
                'data-category-code="' + categoryCode + '" ' +
                'onchange="calculateRowTotal(' + detail.id + ', ' + categoryId + ', ' + sstRate + ')">' +
                '</td>' +
                '<td class="text-right" id="sst_' + detail.id + '">' + 
                ((categoryId == 1 || categoryId == 4) ? sstAmount.toFixed(2) : '-') + 
                '</td>' +
                '<td class="text-right" id="total_' + detail.id + '">' + totalWithSst.toFixed(2) + '</td>' +
                '</tr>';
            
            tbody.append(row);
            rowIndex++;
        });
        
        // Subtotal row for category
        tbody.append(
            '<tr style="background-color:#f8f9fa;font-weight:bold;" data-category-code="' + categoryCode + '">' +
            '<td colspan="2">Sub Total</td>' +
            '<td class="text-right" id="sub_quo_' + categoryCode + '">0.00</td>' +
            '<td class="text-right" id="sub_base_' + categoryCode + '">0.00</td>' +
            '<td class="text-right" id="sub_sst_' + categoryCode + '">' + 
            ((categoryId == 1 || categoryId == 4) ? '0.00' : '-') + 
            '</td>' +
            '<td class="text-right" id="sub_total_' + categoryCode + '">0.00</td>' +
            '</tr>'
        );
    });
    
    // Calculate subtotals
    calculateCategorySubtotals(invoiceDetails, sstRate);
}

function calculateRowTotal(detailId, categoryId, sstRate) {
    var input = $('input[data-detail-id="' + detailId + '"]');
    var row = input.closest('tr');
    var invoiceAmount = parseFloat(input.val() || 0); // Use 'amount' column value
    var sstRateDecimal = parseFloat(sstRate) / 100;
    var sstAmount = 0;
    var totalWithSst = invoiceAmount;
    
    if (categoryId == 1 || categoryId == 4) {
        sstAmount = invoiceAmount * sstRateDecimal;
        totalWithSst = invoiceAmount + sstAmount;
    }
    
    $('#sst_' + detailId).text(sstAmount > 0 ? sstAmount.toFixed(2) : '-');
    $('#total_' + detailId).text(totalWithSst.toFixed(2));
    
    // Get category code from row data attribute
    var categoryCode = row.data('category-code');
    if (categoryCode) {
        recalculateCategorySubtotal(categoryCode, categoryId, sstRate);
    }
}

function calculateCategorySubtotals(invoiceDetails, sstRate) {
    invoiceDetails.forEach(function(category) {
        var subQuo = 0;
        var subBase = 0;
        var subSst = 0;
        var subTotal = 0;
        var sstRateDecimal = parseFloat(sstRate) / 100;
        var categoryId = category.category.id;
        var categoryCode = category.category.code;
        
        category.account_details.forEach(function(detail) {
            var quoAmount = parseFloat(detail.quo_amount || 0);
            var invoiceAmount = parseFloat(detail.amount || 0); // Use 'amount' column
            var sstAmount = 0;
            var totalWithSst = invoiceAmount;
            
            if (categoryId == 1 || categoryId == 4) {
                sstAmount = invoiceAmount * sstRateDecimal;
                totalWithSst = invoiceAmount + sstAmount;
            }
            
            subQuo += quoAmount;
            subBase += invoiceAmount;
            subSst += sstAmount;
            subTotal += totalWithSst;
        });
        
        $('#sub_quo_' + categoryCode).text(subQuo.toFixed(2));
        $('#sub_base_' + categoryCode).text(subBase.toFixed(2));
        if (categoryId == 1 || categoryId == 4) {
            $('#sub_sst_' + categoryCode).text(subSst.toFixed(2));
        }
        $('#sub_total_' + categoryCode).text(subTotal.toFixed(2));
    });
}

function recalculateCategorySubtotal(categoryCode, categoryId, sstRate) {
    var subQuo = 0;
    var subBase = 0;
    var subSst = 0;
    var subTotal = 0;
    var sstRateDecimal = parseFloat(sstRate) / 100;
    
    // Get all detail rows for this category
    var rows = $('#invoiceDetailsTableBody tr[data-category-code="' + categoryCode + '"][data-detail-id]');
    
    rows.each(function() {
        var detailId = $(this).data('detail-id');
        var input = $('input[data-detail-id="' + detailId + '"]');
        var quoAmount = parseFloat($(this).find('td').eq(2).text().replace(/,/g, '') || 0);
        var invoiceAmount = parseFloat(input.val() || 0); // Use 'amount' column value
        var sstAmount = 0;
        var totalWithSst = invoiceAmount;
        
        if (categoryId == 1 || categoryId == 4) {
            sstAmount = invoiceAmount * sstRateDecimal;
            totalWithSst = invoiceAmount + sstAmount;
        }
        
        subQuo += quoAmount;
        subBase += invoiceAmount;
        subSst += sstAmount;
        subTotal += totalWithSst;
    });
    
    $('#sub_quo_' + categoryCode).text(subQuo.toFixed(2));
    $('#sub_base_' + categoryCode).text(subBase.toFixed(2));
    if (categoryId == 1 || categoryId == 4) {
        $('#sub_sst_' + categoryCode).text(subSst.toFixed(2));
    }
    $('#sub_total_' + categoryCode).text(subTotal.toFixed(2));
}

function saveSplitInvoiceDetails() {
    var invoiceId = $("#modalEditSplitInvoiceDetail #invoice_id").val();
    
    if (!invoiceId) {
        toastController('Invalid invoice ID', 'warning');
        return;
    }
    
    // Collect only CHANGED detail values - compare with original values
    var details = [];
    $('input.invoice-amount-input').each(function() {
        var detailId = $(this).data('detail-id');
        var currentValue = parseFloat($(this).val() || 0);
        var originalValue = originalValues[detailId] || 0;
        
        // Only include if value has changed
        if (currentValue !== originalValue) {
            details.push({
                id: detailId,
                ori_invoice_amt: currentValue, // Field name is ori_invoice_amt but we're updating 'amount' column
                old_amount: originalValue // Store old value for logging
            });
        }
    });
    
    if (details.length === 0) {
        toastController('No changes detected', 'warning');
        return;
    }
    
    var formData = {
        invoice_id: invoiceId,
        details: details
    };
    
    // Show loading
    $("#div_full_screen_loading").show();
    $('.btn-success .overlay').show();
    
    console.log("Sending AJAX request to /updateSplitInvoiceDetail");
    console.log("Form data:", formData);
    
    $.ajax({
        url: '/updateSplitInvoiceDetail',
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            console.log("AJAX request started");
        },
        success: function(response) {
            console.log("AJAX success response:", response);
            $("#div_full_screen_loading").hide();
            $('.btn-success .overlay').hide();
            
            if (response.status == 1) {
                // Close modal using system function
                closeUniversalModal();
                
                // Show success notification using system function
                toastController(response.message || 'Invoice details updated successfully', 'success');
                
                // Refresh invoice section without reloading whole page
                if (response.bill_id && typeof loadCaseBill === 'function') {
                    loadCaseBill(response.bill_id);
                } else {
                    // Fallback: reload page if loadCaseBill is not available
                    console.warn('loadCaseBill function not found or bill_id missing, reloading page');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            } else {
                toastController(response.message || 'Failed to update invoice details', 'warning');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", xhr, status, error);
            console.error("Response text:", xhr.responseText);
            $("#div_full_screen_loading").hide();
            $('.btn-success .overlay').hide();
            var errorMsg = 'Failed to update invoice details';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastController(errorMsg, 'warning');
        }
    });
}
</script>
