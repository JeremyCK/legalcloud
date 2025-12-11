<div id="modalUpdateInvoiceDate" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Update Invoice Date</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form id="formUpdateInvoiceDate" onsubmit="return false;">
                    <input type="hidden" id="invoice_id_date" name="invoice_id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Invoice No:</strong> <span id="invoice_no_date_display">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Invoice Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="txtInvoiceDateUpdate" name="txtInvoiceDateUpdate" required onchange="console.log('Date input changed to:', this.value);">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn_close_all" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btnSaveInvoiceDate" onclick="updateSplitInvoiceInvoiceDate(event)">
                    <i class="fa fa-save"></i> Save Changes
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function loadInvoiceDate(invoiceId, invoiceNo) {
    $("#modalUpdateInvoiceDate #invoice_id_date").val(invoiceId);
    $("#modalUpdateInvoiceDate #invoice_no_date_display").text(invoiceNo);
    
    // Show loading
    $("#div_full_screen_loading").show();
    
    $.ajax({
        type: 'GET',
        url: '/getInvoiceDate/' + invoiceId,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $("#div_full_screen_loading").hide();
            
            if (response.status == 1) {
                // Format date for input field (YYYY-MM-DD)
                var invoiceDate = response.invoice_date;
                console.log("Received invoice_date from server:", invoiceDate);
                
                if (invoiceDate) {
                    // Handle different date formats from database
                    // Database might return: "2025-12-11" or "2025-12-11 00:00:00" or "2025-12-11T00:00:00"
                    var dateStr = invoiceDate.toString().trim();
                    
                    // Extract date part only (YYYY-MM-DD)
                    if (dateStr.includes(' ')) {
                        dateStr = dateStr.split(' ')[0]; // Get date part before space
                    }
                    if (dateStr.includes('T')) {
                        dateStr = dateStr.split('T')[0]; // Get date part before T
                    }
                    
                    // Validate and set the date
                    // dateStr should now be in YYYY-MM-DD format
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        $("#txtInvoiceDateUpdate").val(dateStr);
                        console.log("Set date input to:", dateStr);
                    } else {
                        // Fallback: try to parse as Date object
                        var dateObj = new Date(invoiceDate + 'T00:00:00'); // Add time to avoid timezone issues
                        var year = dateObj.getFullYear();
                        var month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        var day = String(dateObj.getDate()).padStart(2, '0');
                        $("#txtInvoiceDateUpdate").val(year + '-' + month + '-' + day);
                        console.log("Parsed date and set to:", year + '-' + month + '-' + day);
                    }
                } else {
                    // If no date, set today's date
                    var today = new Date();
                    var year = today.getFullYear();
                    var month = String(today.getMonth() + 1).padStart(2, '0');
                    var day = String(today.getDate()).padStart(2, '0');
                    $("#txtInvoiceDateUpdate").val(year + '-' + month + '-' + day);
                    console.log("No date found, set to today:", year + '-' + month + '-' + day);
                }
            } else {
                Swal.fire('Error', response.message || 'Failed to load invoice date', 'error');
            }
        },
        error: function(xhr, status, error) {
            $("#div_full_screen_loading").hide();
            Swal.fire('Error', 'Failed to load invoice date', 'error');
        }
    });
}

function updateSplitInvoiceInvoiceDate(event) {
    console.log("=== updateSplitInvoiceInvoiceDate() FUNCTION CALLED ===");
    
    // Prevent form submission if it's inside a form
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    var invoiceId = $("#modalUpdateInvoiceDate #invoice_id_date").val();
    
    // Get date value directly from DOM element to avoid jQuery issues
    var inputElement = document.getElementById('txtInvoiceDateUpdate');
    var invoiceDate = inputElement ? inputElement.value : $("#txtInvoiceDateUpdate").val();
    
    console.log("=== SAVE INVOICE DATE DEBUG ===");
    console.log("invoiceId:", invoiceId);
    console.log("invoiceDate from DOM:", invoiceDate);
    console.log("invoiceDate from jQuery:", $("#txtInvoiceDateUpdate").val());
    console.log("Input element value:", inputElement ? inputElement.value : 'N/A');
    console.log("Input element:", inputElement);
    
    if (!invoiceId) {
        Swal.fire('Error', 'Invalid invoice ID', 'error');
        return false;
    }
    
    if (!invoiceDate) {
        Swal.fire('Error', 'Please select an invoice date', 'error');
        return false;
    }
    
    // Ensure date is in YYYY-MM-DD format
    // The date input should already be in this format, but let's validate
    if (!invoiceDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
        console.error("Invalid date format:", invoiceDate);
        Swal.fire('Error', 'Invalid date format: ' + invoiceDate, 'error');
        return false;
    }
    
    console.log("Sending to server - invoice_id:", invoiceId, "invoice_date:", invoiceDate);
    
    // Show loading
    $("#div_full_screen_loading").show();
    $('#btnSaveInvoiceDate .overlay').show();
    
    var requestData = {
        invoice_id: invoiceId,
        invoice_date: invoiceDate
    };
    
    console.log("=== AJAX REQUEST ===");
    console.log("URL: /updateInvoiceDate");
    console.log("Data being sent:", requestData);
    console.log("invoice_date value:", invoiceDate);
    
    $.ajax({
        url: '/updateInvoiceDate',
        type: 'POST',
        data: requestData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            console.log("AJAX request starting...");
        },
        success: function(response) {
            console.log("=== UPDATE RESPONSE ===");
            console.log("Full response:", response);
            console.log("Response status:", response.status);
            console.log("Response invoice_date:", response.invoice_date);
            console.log("Expected invoice_date:", invoiceDate);
            
            $("#div_full_screen_loading").hide();
            $('#btnSaveInvoiceDate .overlay').hide();
            
            if (response.status == 1) {
                // Close modal using system function
                closeUniversalModal();
                
                // Show success notification using system function
                toastController(response.message || 'Invoice date updated successfully', 'success');
                
                // Refresh invoice section without reloading whole page
                if (response.bill_id && typeof loadCaseBill === 'function') {
                    loadCaseBill(response.bill_id);
                } else {
                    // Fallback: reload page if loadCaseBill is not available
                    console.warn('loadCaseBill function not found, reloading page');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            } else {
                toastController(response.message || 'Failed to update invoice date', 'warning');
            }
        },
        error: function(xhr, status, error) {
            console.error("=== AJAX ERROR ===");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Response Text:", xhr.responseText);
            console.error("Status Code:", xhr.status);
            console.error("Full XHR:", xhr);
            
            $("#div_full_screen_loading").hide();
            $('#btnSaveInvoiceDate .overlay').hide();
            var errorMsg = 'Failed to update invoice date';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastController(errorMsg, 'warning');
        }
    });
}
</script>

