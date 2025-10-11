@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap Modal JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

@section('content')

<div class="container-fluid">
  <div class="fade-in">

    <div class="row">
      <div class="col-sm-12">

        <div class="card">
          <div class="card-header">
            <h4><i class="fa fa-plus"></i> Create New Transfer Fee</h4>
            <small class="text-muted">Invoice-based transfer fee creation</small>
          </div>
          <div class="card-body">

            <form id="transferFeeForm" method="POST" action="{{ route('transferfee.store') }}">
              @csrf
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Transfer Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="transfer_date" required>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Transaction ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="trx_id" required>
                  </div>
                </div>
              </div>

                             <div class="row">
                 <div class="col-md-6">
                   <div class="form-group">
                     <label>Transfer From <span class="text-danger">*</span></label>
                     <select class="form-control" name="transfer_from" required>
                       <option value="">Select Bank Account</option>
                       @foreach($OfficeBankAccount as $bank)
                       <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_no }})</option>
                       @endforeach
                     </select>
                   </div>
                 </div>
                 
                                <div class="col-md-6">
                 <div class="form-group">
                   <label>Transfer Total Amount</label>
                   <input type="text" class="form-control" name="transfer_total_amount" id="transferTotalAmount" value="0.00" readonly style="background-color: #f8f9fa; font-weight: bold; color: #495057;">
                   <small class="text-muted">This amount will be automatically calculated based on selected invoices</small>
                 </div>
               </div>
               </div>
               
               <div class="row">
                 <div class="col-md-6">
                   <div class="form-group">
                     <label>Transfer To <span class="text-danger">*</span></label>
                     <select class="form-control" name="transfer_to" required>
                       <option value="">Select Bank Account</option>
                       @foreach($OfficeBankAccount as $bank)
                       <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_no }})</option>
                       @endforeach
                     </select>
                   </div>
                 </div>
                 
                 <div class="col-md-6">
                   <div class="form-group">
                     <label>Purpose <span class="text-danger">*</span></label>
                     <textarea class="form-control" name="purpose" rows="3" required></textarea>
                   </div>
                 </div>
               </div>

              

              <hr>

                             <div class="row">
                 <div class="col-md-12">
                   <h5><i class="fa fa-file-invoice"></i> Select Invoices for Transfer</h5>
                   <p class="text-muted">Click the button below to open invoice selection modal</p>
                   
                   <button type="button" class="btn btn-info" onclick="openInvoiceModal()">
                     <i class="fa fa-search"></i> Select Invoices
                   </button>
                   
                                       <div id="selectedInvoicesSummary" class="mt-3" style="display:none;">
                      <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        <strong><span id="selectedCount">0</span> invoices selected</strong>
                        <br>
                                                 <strong>Total Amount: <span id="selectedTotalAmount">0.00</span></strong>
                      </div>
                      
                      <!-- Selected Invoices Table -->
                      <div class="card mt-3">
                        <div class="card-header">
                          <h6><i class="fa fa-list-check"></i> Selected Invoices</h6>
                        </div>
                        <div class="card-body">
                          <div id="selectedInvoicesTable" style="display:none;">
                            <div class="table-responsive">
                                                             <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                                 <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
                                   <tr>
                                     <th width="30">No</th>
                                     <th width="40">Action</th>
                                     <th width="120">Ref No</th>
                                     <th width="100">Invoice No</th>
                                     <th width="90">Invoice Date</th>
                                     <th width="80">Total amt</th>
                                     <th width="80">Collected amt</th>
                                     <th width="70">pfee</th>
                                     <th width="60">sst</th>
                                     <th width="80">Pfee to transfer</th>
                                     <th width="70">SST to transfer</th>
                                     <th width="80">Transferred Bal</th>
                                     <th width="80">Transferred SST</th>
                                     <th width="90">Payment Date</th>
                                   </tr>
                                 </thead>
                                <tbody id="selectedInvoicesTableBody">
                                  <!-- Selected invoices will be displayed here -->
                                </tbody>
                                <tfoot class="table-dark" style="position: sticky; bottom: 0; z-index: 1;">
                                  <tr>
                                    <th colspan="5" class="text-right"><strong>TOTAL:</strong></th>
                                    <th class="text-right" id="footerTotalAmt">0.00</th>
                                    <th class="text-right" id="footerCollectedAmt">0.00</th>
                                    <th class="text-right" id="footerPfee">0.00</th>
                                    <th class="text-right" id="footerSst">0.00</th>
                                    <th class="text-right" id="footerPfeeToTransfer">0.00</th>
                                    <th class="text-right" id="footerSstToTransfer">0.00</th>
                                    <th class="text-right" id="footerTransferredBal">0.00</th>
                                    <th class="text-right" id="footerTransferredSst">0.00</th>
                                    <th></th>
                                  </tr>
                                </tfoot>
                              </table>
                            </div>
                          </div>
                          <div id="noSelectedInvoices" class="text-center text-muted">
                            <i class="fa fa-info-circle"></i> No invoices selected yet
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
               </div>

              <input type="hidden" name="add_invoice" id="add_invoice">

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fa fa-save"></i> Create Transfer Fee
                  </button>
                  <a href="{{ route('transferfee.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                  </a>
                </div>
              </div>

                         </form>

           </div>
         </div>
       </div>
     </div>
   </div>
 </div>

<!-- Invoice Selection Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 99%; width: 99%; margin: 0.25rem auto;">
          <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="invoiceModalLabel">
          <i class="fa fa-file-invoice"></i> Select Invoices for Transfer
        </h5>
                 <button type="button" class="close" onclick="closeModal()" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
        <!-- Collapsible Search Filters -->
        <div class="card mb-2">
          <div class="card-header py-2" style="background-color: #f8f9fa; cursor: pointer;" onclick="toggleFilters()">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0" style="font-size: 14px;">
                <i class="fa fa-search"></i> Search & Filter Options
              </h6>
              <span class="badge badge-secondary" id="filterToggleIcon">
                <i class="fa fa-chevron-up"></i>
              </span>
            </div>
          </div>
          <div class="card-body py-2" id="filterSection">
            <div class="row mb-2">
          <div class="col-md-3">
            <label for="searchInvoiceNo" style="font-size: 12px; margin-bottom: 2px;">Invoice No:</label>
            <textarea id="searchInvoiceNo" class="form-control" rows="2" style="font-size: 11px;" placeholder="Enter invoice numbers (one per line or comma-separated)&#10;Example:&#10;INV-001&#10;INV-002, INV-003&#10;INV-004"></textarea>
            <small class="text-muted" style="font-size: 10px;">Enter multiple invoice numbers separated by commas or new lines</small>
            <div id="invoiceCount" class="text-info" style="display:none;">
              <small style="font-size: 10px;"><i class="fa fa-info-circle"></i> <span id="invoiceCountText">0</span> invoice numbers entered</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" style="font-size: 10px; padding: 2px 6px;" onclick="clearInvoiceNumbers()">
              <i class="fa fa-times"></i> Clear Invoice Numbers
            </button>
          </div>
          <div class="col-md-3">
            <label for="searchCaseRef" style="font-size: 12px; margin-bottom: 2px;">Case Ref No:</label>
            <input type="text" id="searchCaseRef" class="form-control" style="font-size: 11px;" placeholder="Search case ref...">
          </div>
          <div class="col-md-3">
            <label for="searchClient" style="font-size: 12px; margin-bottom: 2px;">Client Name:</label>
            <input type="text" id="searchClient" class="form-control" style="font-size: 11px;" placeholder="Search client...">
          </div>
          <div class="col-md-3">
            <label for="searchBillingParty" style="font-size: 12px; margin-bottom: 2px;">Billing Party:</label>
            <input type="text" id="searchBillingParty" class="form-control" style="font-size: 11px;" placeholder="Search billing party...">
          </div>
        </div>
        
        <!-- Additional Filters -->
        <div class="row mb-2">
          <div class="col-md-3">
            <label for="filterBranch" style="font-size: 12px; margin-bottom: 2px;">Branch:</label>
            <select id="filterBranch" class="form-control" style="font-size: 11px;">
              <option value="">-- All Branches --</option>
              @foreach($Branchs as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label for="filterStartDate" style="font-size: 12px; margin-bottom: 2px;">Start Date:</label>
            <input type="date" id="filterStartDate" class="form-control" style="font-size: 11px;">
          </div>
          <div class="col-md-3">
            <label for="filterEndDate" style="font-size: 12px; margin-bottom: 2px;">End Date:</label>
            <input type="date" id="filterEndDate" class="form-control" style="font-size: 11px;">
          </div>
          <div class="col-md-3">
            <label style="font-size: 12px; margin-bottom: 2px;">&nbsp;</label>
            <div>
              <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size: 10px; padding: 2px 6px;" onclick="clearDateFilters()">
                <i class="fa fa-times"></i> Clear Dates
              </button>
            </div>
          </div>
        </div>
        
                 <div class="row mb-2">
           <div class="col-md-6">
             <label for="perPageSelect" style="font-size: 12px; margin-bottom: 2px;">Records per page:</label>
             <select id="perPageSelect" class="form-control" style="width: auto; display: inline-block; font-size: 11px;">
               <option value="10">10 records</option>
               <option value="20" selected>20 records</option>
               <option value="50">50 records</option>
               <option value="100">100 records</option>
             </select>
             <small class="text-muted" style="font-size: 10px;">Changing this will refresh the list</small>
           </div>
           <div class="col-md-6 text-right">
             <button type="button" class="btn btn-primary btn-sm" style="font-size: 11px; padding: 4px 8px;" onclick="searchInvoices()">
               <i class="fa fa-search"></i> Search
             </button>
             <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 8px;" onclick="clearSearch()">
               <i class="fa fa-times"></i> Clear
             </button>
           </div>
         </div>
           </div>
         </div>
        
        <div id="invoiceLoading" style="display:none;" class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading invoices...</p>
        </div>
        
        <!-- Quick Search Box -->
        <div class="card mb-2">
          <div class="card-body py-2">
            <div class="row">
              <div class="col-md-6">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="font-size: 12px;">
                      <i class="fa fa-search"></i>
                    </span>
                  </div>
                  <input type="text" id="quickSearch" class="form-control" style="font-size: 12px;" placeholder="Quick search by Invoice No or Case Ref No..." onkeyup="quickSearch()">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" style="font-size: 12px;" onclick="clearQuickSearch()">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                <small class="text-muted" style="font-size: 10px;">Type to filter table rows instantly</small>
              </div>
              <div class="col-md-6 text-right">
                <span class="badge badge-info" id="quickSearchCount" style="display:none;">
                  <span id="quickSearchResultCount">0</span> of <span id="quickSearchTotalCount">0</span> records
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div id="invoiceListContainer">
          <!-- Invoice list will be loaded here -->
        </div>
      </div>
      <div class="modal-footer">
        <div class="row w-100">
          <div class="col-md-8 text-left">
            <div class="d-flex align-items-center">
              <span class="badge badge-info mr-2">
                <span id="modalSelectedCount">0</span> invoices
              </span>
              <span class="badge badge-success mr-2">
                Total: <span id="modalTotalAmount">0.00</span>
              </span>
              <small class="text-muted">
                (Scroll to see more invoices)
              </small>
            </div>
          </div>
          <div class="col-md-4 text-right">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="confirmInvoiceSelection()">
              <i class="fa fa-check"></i> Confirm Selection
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')
<style>
/* Custom scrollbar for the table container */




/* Ensure table header stays visible */
.invoice-table-container thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: #343a40 !important;
    color: white !important;
    font-size: 11px !important;
    padding: 8px 4px !important;
}

/* Compact table styling */
.invoice-table-container .table td {
    padding: 6px 4px !important;
    vertical-align: middle;
}

/* Hyperlink styling */
.invoice-table-container .table a {
    color: #007bff !important;
    text-decoration: none;
}

.invoice-table-container .table a:hover {
    color: #0056b3 !important;
    text-decoration: underline;
}

 /* Compact checkbox styling */
 .checkbox.bulk-edit-mode {
     margin: 0;
 }
 
 .checkbox.bulk-edit-mode input[type="checkbox"] {
     margin: 0;
     transform: scale(0.8);
 }
 
 /* Selected invoices table styling to match modal */
 #selectedInvoicesTable .table-responsive {
     height: 250px;
     overflow-y: auto;
     border: 1px solid #dee2e6;
     border-radius: 4px;
 }
 
 #selectedInvoicesTable .table-responsive::-webkit-scrollbar {
     width: 8px;
 }
 
 #selectedInvoicesTable .table-responsive::-webkit-scrollbar-track {
     background: #f1f1f1;
     border-radius: 4px;
 }
 
 #selectedInvoicesTable .table-responsive::-webkit-scrollbar-thumb {
     background: #888;
     border-radius: 4px;
 }
 
 #selectedInvoicesTable .table-responsive::-webkit-scrollbar-thumb:hover {
     background: #555;
 }
 
 #selectedInvoicesTable .table-responsive thead th {
     position: sticky;
     top: 0;
     z-index: 1;
     background-color: #343a40 !important;
     color: white !important;
     font-size: 11px !important;
     padding: 8px 4px !important;
 }
 
 #selectedInvoicesTable .table-responsive .table {
     margin-bottom: 0;
 }
 
 #selectedInvoicesTable .table-responsive .table td {
     padding: 6px 4px !important;
     vertical-align: middle;
     font-size: 11px !important;
 }
 
 /* Modal content constraints */
 #invoiceModal .modal-content {
     display: flex;
     flex-direction: column;
 }
 
 #invoiceModal .modal-body {
     flex: 1;
     padding: 1rem;
 }
 
 #invoiceModal .modal-footer {
     flex-shrink: 0;
     padding: 0.75rem 1rem;
 }
 
 /* Ensure the invoice list container doesn't overflow */
 #invoiceListContainer {
     border: 1px solid #dee2e6;
     border-radius: 4px;
     padding: 10px;
     margin-bottom: 10px;
 }
 
 /* Enhanced Pagination styling */
.pagination-container {
    box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
    position: relative;
    z-index: 10;
}

.pagination-container .pagination {
    margin-bottom: 0;
}

.pagination-container .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
    margin: 0 2px;
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: all 0.15s ease-in-out;
}

.pagination-container .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
    text-decoration: none;
}

.pagination-container .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination-container .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination-container .page-link i {
    font-size: 0.75rem;
}
 
 /* Collapsible filter styling */
 .card-header {
     transition: background-color 0.2s ease;
 }
 
 .card-header:hover {
     background-color: #e9ecef !important;
 }
 
 /* Sortable headers styling */
 .sortable-header {
     position: relative;
     user-select: none;
 }
 
 .sortable-header:hover {
     background-color: #495057 !important;
 }
 
 .sort-icon {
     margin-left: 5px;
     font-size: 10px;
     opacity: 0.6;
 }
 
 .sortable-header.sort-asc .sort-icon::after {
     content: "▲";
     color: #28a745;
     opacity: 1;
 }
 
 .sortable-header.sort-desc .sort-icon::after {
     content: "▼";
     color: #dc3545;
     opacity: 1;
 }
</style>

<script>
$(document).ready(function() {
    // Ensure everything is loaded
    console.log('Document ready - jQuery version:', $.fn.jquery);
    console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
    
         // Add keyboard support for search
     $('#searchCaseRef, #searchClient, #searchBillingParty').keypress(function(e) {
         if (e.which == 13) { // Enter key
             searchInvoices();
         }
     });
     
           // Show invoice count for multiple invoice numbers
      $('#searchInvoiceNo').on('input', function() {
          var value = $(this).val();
          if (value.trim()) {
              var invoiceNumbers = value.split(/[,\n\r]+/).filter(function(item) {
                  return item.trim().length > 0;
              });
              
              if (invoiceNumbers.length > 1) {
                  $('#invoiceCountText').text(invoiceNumbers.length);
                  $('#invoiceCount').show();
              } else {
                  $('#invoiceCount').hide();
              }
          } else {
              $('#invoiceCount').hide();
          }
      });
      
      // Auto-refresh when per page selection changes
      $('#perPageSelect').change(function() {
          loadMainInvoiceList(1); // Reset to page 1 when changing per page
      });
      
      // Initialize sortable headers
      initializeSortableHeaders();
});

let selectedInvoices = [];

function openInvoiceModal() {
    // Try Bootstrap modal first
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $('#invoiceModal').modal('show');
    } else {
        // Fallback to vanilla JavaScript
        const modal = document.getElementById('invoiceModal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modalBackdrop';
            document.body.appendChild(backdrop);
        }
    }
    loadInvoices();
}

function loadInvoices() {
    $('#invoiceLoading').show();
    $('#invoiceListContainer').hide();
    
    // Load the main invoice list directly
    loadMainInvoiceList();
}

function loadMainInvoiceList(page = 1) {
    // Get search values
    var searchData = {
        page: page,
        per_page: $('#perPageSelect').val(),
        search_invoice_no: $('#searchInvoiceNo').val(),
        search_case_ref: $('#searchCaseRef').val(),
        search_client: $('#searchClient').val(),
        search_billing_party: $('#searchBillingParty').val(),
        filter_branch: $('#filterBranch').val(),
        filter_start_date: $('#filterStartDate').val(),
        filter_end_date: $('#filterEndDate').val(),
        sort_field: currentSortField,
        sort_order: currentSortOrder
    };
    
    $.ajax({
                    url: '{{ route("transferfee.invoice-list") }}',
        type: 'GET',
        data: searchData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.status == 1) {
                $('#invoiceListContainer').html(response.invoiceList).show();
                initializeInvoiceSelection();
                updateSortIndicators(); // Reapply sort indicators after table reload
                console.log('Invoice list loaded successfully. Found ' + response.count + ' invoices on page ' + page + ' of ' + response.totalPages);
            } else {
                alert('Error loading invoices: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Main invoice list failed:', xhr.responseText);
            alert('Main invoice list failed: ' + error);
        }
    });
}

// Function to load specific page - FIXED: No href="#" redirects
function loadInvoicePage(page) {
    loadMainInvoiceList(page);
    return false; // Prevent any default action
}

// Search invoices function
function searchInvoices() {
    loadMainInvoiceList(1); // Always start from page 1 when searching
}

 // Clear search function
 function clearSearch() {
     $('#searchInvoiceNo').val('');
     $('#searchCaseRef').val('');
     $('#searchClient').val('');
     $('#searchBillingParty').val('');
     $('#filterBranch').val('');
     $('#filterStartDate').val('');
     $('#filterEndDate').val('');
     $('#invoiceCount').hide();
     loadMainInvoiceList(1); // Reload with cleared filters
 }
 
 // Clear only invoice numbers
 function clearInvoiceNumbers() {
     $('#searchInvoiceNo').val('');
     $('#invoiceCount').hide();
     loadMainInvoiceList(1); // Reload with cleared invoice filters
 }
 
 // Clear date filters
 function clearDateFilters() {
     $('#filterStartDate').val('');
     $('#filterEndDate').val('');
     loadMainInvoiceList(1); // Reload with cleared date filters
 }

function initializeInvoiceSelection() {
    // Sync checkboxes with existing selections
    selectedInvoices.forEach(invoice => {
        $(`.invoice-checkbox[value="${invoice.id}"]`).prop('checked', true);
    });
    
    $('.invoice-checkbox').change(function() {
        updateSelectedInvoices();
    });
    
    $('#selectAll').change(function() {
        $('.invoice-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedInvoices();
    });
}

function updateSelectedInvoices() {
    // Don't reset selectedInvoices array - preserve existing selections
    let totalAmount = 0;
    
    // Get all checked checkboxes in the modal
    $('.invoice-checkbox:checked').each(function() {
        const invoiceId = $(this).val();
        const billId = $(this).data('bill-id');
        const amount = parseFloat($(this).data('amount'));
        const sst = parseFloat($(this).data('sst') || 0);
        
        // Check if this invoice is already in selectedInvoices
        const existingIndex = selectedInvoices.findIndex(invoice => invoice.id == invoiceId);
        
        if (existingIndex === -1) {
            // Get invoice details from the table row
            const row = $(this).closest('tr');
            const invoiceNo = row.find('td:eq(3)').text() || 'N/A'; // Invoice No column
            const invoiceDate = row.find('td:eq(4)').text() || 'N/A'; // Invoice Date column
            const caseRef = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() || 'N/A'; // Ref No column (handle hyperlink)
            const clientName = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() || 'N/A'; // Using Ref No as client name for now
            const paymentDate = row.find('td:eq(13)').text() || 'N/A'; // Payment Date column
            const caseId = row.find('td:eq(2) a').attr('href') ? row.find('td:eq(2) a').attr('href').split('/').pop() : null; // Extract case ID from href
            
            // Add new invoice to selectedInvoices
            selectedInvoices.push({
                id: invoiceId,
                bill_id: billId,
                value: amount,
                sst: sst,
                invoice_no: invoiceNo,
                invoice_date: invoiceDate,
                case_ref: caseRef,
                client_name: clientName,
                payment_date: paymentDate,
                case_id: caseId
            });
        }
    });
    
         // Calculate total amount from all selected invoices
     selectedInvoices.forEach(invoice => {
         totalAmount += invoice.value;
     });
     
     // Remove invoices that are no longer checked in the modal
     selectedInvoices = selectedInvoices.filter(invoice => {
         return $(`.invoice-checkbox[value="${invoice.id}"]`).is(':checked');
     });
     
     // Recalculate total after filtering
     totalAmount = selectedInvoices.reduce((sum, invoice) => sum + invoice.value, 0);
     
     $('#add_invoice').val(JSON.stringify(selectedInvoices));
               $('#modalSelectedCount').text(selectedInvoices.length);
     $('#modalTotalAmount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
     $('#totalAmount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
     $('#submitBtn').prop('disabled', selectedInvoices.length === 0);
      
     // Update the transfer total amount field
     $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
      
     // Update the selected invoices table on the main form
     updateSelectedInvoicesTable()
}

function confirmInvoiceSelection() {
    if (selectedInvoices.length === 0) {
        alert('Please select at least one invoice for transfer.');
        return;
    }
    
         // Update the summary on the main form
     $('#selectedCount').text(selectedInvoices.length);
     $('#selectedTotalAmount').text(parseFloat($('#totalAmount').text().replace(/,/g, '')).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
     $('#selectedInvoicesSummary').show();
     
     // Update the transfer total amount field
     $('#transferTotalAmount').val(parseFloat($('#totalAmount').text().replace(/,/g, '')).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    
    // Update the selected invoices table
    updateSelectedInvoicesTable();
    
    // Close the modal
    closeModal();
}

function updateSelectedInvoicesTable() {
    const tableBody = $('#selectedInvoicesTableBody');
    const tableContainer = $('#selectedInvoicesTable');
    const noInvoicesMsg = $('#noSelectedInvoices');
    
    if (selectedInvoices.length === 0) {
        tableContainer.hide();
        noInvoicesMsg.show();
        return;
    }
    
    tableContainer.show();
    noInvoicesMsg.hide();
    
    let tableHTML = '';
    let totals = {
        totalAmt: 0,
        collectedAmt: 0,
        pfee: 0,
        sst: 0,
        pfeeToTransfer: 0,
        sstToTransfer: 0,
        transferredBal: 0,
        transferredSst: 0
    };
    
         selectedInvoices.forEach((invoice, index) => {
         const totalAmount = invoice.value + invoice.sst;
         const pfeeAmount = invoice.value;
         const sstAmount = invoice.sst;
         
         // Add to totals
         totals.totalAmt += totalAmount;
         totals.collectedAmt += totalAmount;
         totals.pfee += pfeeAmount;
         totals.sst += sstAmount;
         totals.pfeeToTransfer += pfeeAmount;
         totals.sstToTransfer += sstAmount;
         
         tableHTML += `
             <tr>
                 <td class="text-center" style="font-size: 11px;">${index + 1}</td>
                 <td>
                     <button type="button" class="btn btn-sm btn-danger" onclick="removeSelectedInvoice(${index})" title="Remove Invoice" style="font-size: 10px; padding: 2px 4px;">
                         <i class="fa fa-times"></i>
                     </button>
                 </td>
                 <td style="font-size: 11px;">
                     <a href="/case/${invoice.case_id || ''}" target="_blank" class="text-primary" style="text-decoration: none;">
                         ${invoice.case_ref}
                     </a>
                 </td>
                 <td style="font-size: 11px;">${invoice.invoice_no}</td>
                 <td style="font-size: 11px;">${invoice.invoice_date}</td>
                 <td class="text-right" style="font-size: 11px;">${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                 <td class="text-right" style="font-size: 11px;">${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                 <td class="text-right" style="font-size: 11px;">${pfeeAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                 <td class="text-right" style="font-size: 11px;">${sstAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                 <td class="text-right" style="font-size: 11px;">
                     <span class="form-control-plaintext" style="font-size: 10px; text-align: right; padding: 0; border: none; background: transparent;">
                         ${pfeeAmount.toFixed(2)}
                     </span>
                 </td>
                 <td class="text-right" style="font-size: 11px;">
                     <span class="form-control-plaintext" style="font-size: 10px; text-align: right; padding: 0; border: none; background: transparent;">
                         ${sstAmount.toFixed(2)}
                     </span>
                 </td>
                 <td class="text-right" style="font-size: 11px;">0.00</td>
                 <td class="text-right" style="font-size: 11px;">0.00</td>
                 <td style="font-size: 11px;">${invoice.payment_date}</td>
             </tr>
         `;
     });
     
    tableBody.html(tableHTML);
    
    // Update footer totals
    updateFooterTotals();
}

function updateTransferAmounts(index, type) {
    // This function is no longer needed since we're using text instead of input fields
    // But keeping it for backward compatibility
    console.log('updateTransferAmounts called but no longer needed - using text display instead');
}
        


        
        // Get the input values
        let pfeeValue = parseFloat(pfeeInputElement.val()) || 0;
        let sstValue = parseFloat(sstInputElement.val()) || 0;
        
        // Get original values
        const originalPfee = parseFloat(pfeeInputElement.data('original-pfee')) || 0;
        const originalSst = parseFloat(sstInputElement.data('original-sst')) || 0;
        
        // Validate maximum limits
        if (pfeeValue > originalPfee) {
            pfeeValue = originalPfee;
            pfeeInputElement.val(pfeeValue.toFixed(2));
        }
        
        if (sstValue > originalSst) {
            sstValue = originalSst;
            sstInputElement.val(sstValue.toFixed(2));
        }
        
        // Ensure non-negative values
        if (pfeeValue < 0) {
            pfeeValue = 0;
            pfeeInputElement.val('0.00');
        }
        
        if (sstValue < 0) {
            sstValue = 0;
            sstInputElement.val('0.00');
        }
        
        // Update the selectedInvoices array
        invoice.value = pfeeValue;
        invoice.sst = sstValue;
        
        // Debug logging
        console.log('Updated invoice:', invoice);
        console.log('New pfee value:', pfeeValue);
        console.log('New sst value:', sstValue);
        
        // Update the hidden input
        $('#add_invoice').val(JSON.stringify(selectedInvoices));
        
        // Update summary
        const totalAmount = selectedInvoices.reduce((sum, inv) => sum + inv.value + inv.sst, 0);
        const totalPfee = selectedInvoices.reduce((sum, inv) => sum + inv.value, 0);
        const totalSst = selectedInvoices.reduce((sum, inv) => sum + inv.sst, 0);
        
        $('#selectedCount').text(selectedInvoices.length);
        $('#selectedTotalAmount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Update the transfer total amount field
        $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Update footer totals
        updateFooterTotals();
    }
}

function updateFooterTotals() {
    let totals = {
        totalAmt: 0,
        collectedAmt: 0,
        pfee: 0,
        sst: 0,
        pfeeToTransfer: 0,
        sstToTransfer: 0,
        transferredBal: 0,
        transferredSst: 0
    };
    
    selectedInvoices.forEach((invoice) => {
        const totalAmount = invoice.value + invoice.sst;
        const pfeeAmount = invoice.value;
        const sstAmount = invoice.sst;
        
        totals.totalAmt += totalAmount;
        totals.collectedAmt += totalAmount;
        totals.pfee += pfeeAmount;
        totals.sst += sstAmount;
        totals.pfeeToTransfer += pfeeAmount;
        totals.sstToTransfer += sstAmount;
    });
    
    // Update footer totals
    $('#footerTotalAmt').text(totals.totalAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerCollectedAmt').text(totals.collectedAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerPfee').text(totals.pfee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerSst').text(totals.sst.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerPfeeToTransfer').text(totals.pfeeToTransfer.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerSstToTransfer').text(totals.sstToTransfer.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerTransferredBal').text(totals.transferredBal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#footerTransferredSst').text(totals.transferredSst.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

function removeSelectedInvoice(index) {
    if (index >= 0 && index < selectedInvoices.length) {
        // Remove from selectedInvoices array
        const removedInvoice = selectedInvoices.splice(index, 1)[0];
        
        // Update the hidden input
        $('#add_invoice').val(JSON.stringify(selectedInvoices));
        
        // Update summary
        const totalAmount = selectedInvoices.reduce((sum, invoice) => sum + (parseFloat(invoice.value) || 0) + (parseFloat(invoice.sst) || 0), 0);
        $('#selectedCount').text(selectedInvoices.length);
        $('#selectedTotalAmount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Update the transfer total amount field
        $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Update table
        updateSelectedInvoicesTable();
        
        // Update submit button
        $('#submitBtn').prop('disabled', selectedInvoices.length === 0);
        
        // If no invoices left, hide the summary
        if (selectedInvoices.length === 0) {
            $('#selectedInvoicesSummary').hide();
        }
        
        // Uncheck the corresponding checkbox in the modal (if modal is open)
        $(`.invoice-checkbox[value="${removedInvoice.id}"]`).prop('checked', false);
        $('#modalSelectedCount').text(selectedInvoices.length);
        $('#totalAmount').text(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Update the transfer total amount field
        $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
}

// Global variables for sorting
let currentSortField = '';
let currentSortOrder = '';

function initializeSortableHeaders() {
    // Add click event listeners to sortable headers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.sortable-header')) {
            const header = e.target.closest('.sortable-header');
            const sortField = header.getAttribute('data-sort');
            
            if (sortField) {
                handleSort(sortField);
            }
        }
    });
}

function handleSort(sortField) {
    // Toggle sort order if same field, otherwise default to ascending
    if (currentSortField === sortField) {
        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        currentSortField = sortField;
        currentSortOrder = 'asc';
    }
    
    // Update visual indicators
    updateSortIndicators();
    
    // Reload the invoice list with sorting
    loadMainInvoiceList(1);
}

function updateSortIndicators() {
    // Remove all sort indicators
    document.querySelectorAll('.sortable-header').forEach(header => {
        header.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Add indicator to current sort field
    if (currentSortField) {
        const header = document.querySelector(`[data-sort="${currentSortField}"]`);
        if (header) {
            header.classList.add(`sort-${currentSortOrder}`);
        }
    }
}

function toggleFilters() {
    const filterSection = document.getElementById('filterSection');
    const toggleIcon = document.getElementById('filterToggleIcon');
    
    if (filterSection.style.display === 'none') {
        filterSection.style.display = 'block';
        toggleIcon.innerHTML = '<i class="fa fa-chevron-up"></i>';
    } else {
        filterSection.style.display = 'none';
        toggleIcon.innerHTML = '<i class="fa fa-chevron-down"></i>';
    }
}

function closeModal() {
    // Try Bootstrap modal first
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $('#invoiceModal').modal('hide');
    } else {
        // Fallback to vanilla JavaScript
        const modal = document.getElementById('invoiceModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            
            // Remove backdrop
            const backdrop = document.getElementById('modalBackdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
}

// Quick Search Functions
function quickSearch() {
    const searchTerm = $('#quickSearch').val().toLowerCase().trim();
    const table = $('#invoiceListContainer table tbody');
    const rows = table.find('tr');
    let visibleCount = 0;
    const totalCount = rows.length;
    
    console.log('Quick search term:', searchTerm);
    console.log('Total rows found:', totalCount);
    
    if (searchTerm === '') {
        // Show all rows if search is empty
        rows.show();
        visibleCount = totalCount;
        $('#quickSearchCount').hide();
    } else {
        // Filter rows based on search term
        rows.each(function(index) {
            const row = $(this);
            const invoiceNo = row.find('td:eq(3)').text().toLowerCase().trim(); // Invoice No column
            const caseRef = row.find('td:eq(2)').text().toLowerCase().trim(); // Case Ref No column
            
            console.log(`Row ${index + 1}: Invoice="${invoiceNo}", CaseRef="${caseRef}"`);
            console.log(`Row ${index + 1}: Checking if "${invoiceNo}" includes "${searchTerm}" or "${caseRef}" includes "${searchTerm}"`);
            
            // Use indexOf instead of includes for better compatibility
            const invoiceMatch = invoiceNo.indexOf(searchTerm) !== -1;
            const caseRefMatch = caseRef.indexOf(searchTerm) !== -1;
            
            console.log(`Row ${index + 1}: Invoice match: ${invoiceMatch}, CaseRef match: ${caseRefMatch}`);
            console.log(`Row ${index + 1}: Invoice indexOf result: ${invoiceNo.indexOf(searchTerm)}, CaseRef indexOf result: ${caseRef.indexOf(searchTerm)}`);
            
            if (invoiceMatch || caseRefMatch) {
                row.show();
                visibleCount++;
                console.log(`Row ${index + 1}: MATCH - showing row`);
            } else {
                row.hide();
                console.log(`Row ${index + 1}: NO MATCH - hiding row`);
            }
        });
        
        // Show search count
        $('#quickSearchResultCount').text(visibleCount);
        $('#quickSearchTotalCount').text(totalCount);
        $('#quickSearchCount').show();
        
        console.log('Search results:', visibleCount, 'of', totalCount, 'rows visible');
    }
    
    // Update select all checkbox state
    updateSelectAllCheckbox();
}

function clearQuickSearch() {
    $('#quickSearch').val('');
    quickSearch();
}

function updateSelectAllCheckbox() {
    const visibleCheckboxes = $('#invoiceListContainer table tbody tr:visible .invoice-checkbox');
    const checkedVisibleCheckboxes = visibleCheckboxes.filter(':checked');
    
    if (visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCheckboxes.length) {
        $('#selectAll').prop('checked', true);
    } else {
        $('#selectAll').prop('checked', false);
    }
}

// Form submission with enhanced validation and response handling
$('#transferFeeForm').submit(function(e) {
    e.preventDefault();
    
    // Validate required fields
    const transferDate = $('input[name="transfer_date"]').val();
    const transactionId = $('input[name="trx_id"]').val();
    const transferFrom = $('select[name="transfer_from"]').val();
    const transferTo = $('select[name="transfer_to"]').val();
    const purpose = $('textarea[name="purpose"]').val();
    
    if (!transferDate || !transactionId || !transferFrom || !transferTo || !purpose) {
        Swal.fire('Error', 'All required fields must be filled', 'error');
        return false;
    }
    
    // Validate that transfer_from and transfer_to are different
    if (transferFrom === transferTo) {
        Swal.fire('Error', 'Transfer from and transfer to accounts cannot be the same', 'error');
        return false;
    }
    
    // Validate that invoices are selected
    if (selectedInvoices.length === 0) {
        Swal.fire('Error', 'Please select at least one invoice for transfer', 'error');
        return false;
    }
    
    // Show loading
    Swal.fire({
        title: 'Creating Transfer Fee...',
        text: 'Please wait while we process your request',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit form
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status == 1) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message + ' (Total Amount: ' + response.data.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ')',
                    icon: 'success',
                    confirmButtonText: 'View Transfer'
                }).then((result) => {
                    // Always redirect to listing page after successful creation
                    window.location.href = '{{ route("transferfee.index") }}';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            let errorMessage = 'An error occurred while creating the transfer fee';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire('Error', errorMessage, 'error');
        }
    });
});
</script>
@endsection
