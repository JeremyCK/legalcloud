@extends('dashboard.base')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fa fa-plus"></i> Create New SST Record</h4>
                            <small class="text-muted">SST transfer record creation</small>
                        </div>
                        <div class="card-body">

                            <form id="sstForm" method="POST" action="/createNewSSTRecordV2">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transaction ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="trx_id" name="trx_id" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                                            </div>
                                        </div>
                                    </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Branch <span class="text-danger">*</span></label>
                                            <select class="form-control" id="branch_sst" name="branch" required>
                                                <option value="0">Select Branch</option>
                                                @foreach($Branchs as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total SST Amount</label>
                                            <input type="text" class="form-control" id="pay_amount" name="pay_amount" readonly>
                                        </div>
                                    </div>
                                    </div> 

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Remark</label>
                                            <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-info" onclick="openInvoiceModal()">
                                            <i class="fa fa-search"></i> Select Invoices for SST
                                        </button>

                                        <button type="button" class="btn btn-success float-right" onclick="saveTransferFee();">
                                            <i class="fa cil-save"> </i>Save
                                        </button>
                                    </div>

                                    <div class="col-sm-12">
                                        <div id="selectedInvoicesSummary" class="mt-3" style="display:none;">
                                            <div class="alert alert-success">
                                                <i class="fa fa-check-circle"></i>
                                                <strong><span id="selectedCount">0</span> invoices selected</strong>
                                                <br>
                                                <strong>Total SST Amount: <span id="selectedTotalAmount">0.00</span></strong>
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
                                                                <thead class="thead-dark">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Ref No</th>
                                                    <th>Client Name</th>
                                                    <th>Invoice No</th>
                                                                        <th>Invoice Date</th>
                                                    <th>Total amt</th>
                                                    <th>Pfee1</th>
                                                    <th>Pfee2</th>
                                                    <th>Collected amt</th>
                                                                        <th>SST</th>
                                                    <th>Payment Date</th>
                                                                        <th>Action</th>
                                                </tr>
                                            </thead>
                                                                <tbody id="selectedInvoicesTableBody">
                                                                    <!-- Selected invoices will be displayed here -->
                                            </tbody>
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

                                    <div class="col-sm-12">
                                        <hr />
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
                  <i class="fa fa-file-invoice"></i> Select Invoices for SST Transfer
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
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <!-- Load jQuery 3.6.0 and Bootstrap 4.6.0 for modal functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Use jQuery 3.6.0 for modal functionality
        var $jq3 = jQuery.noConflict(true);
        
        // Override the global $ and jQuery with version 3.6.0
        window.$ = $jq3;
        window.jQuery = $jq3;
    </script>
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
    line-height: 1.5;
    border-radius: 0.25rem;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    color: #007bff;
    background-color: #fff;
}

.pagination-container .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination-container .page-item.active .page-link {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.pagination-container .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Compact table styling for better space utilization */
.table-compact th,
.table-compact td {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.875rem;
    line-height: 1.2;
}

.table-compact th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

/* Responsive table container */
.table-responsive-compact {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.table-responsive-compact::-webkit-scrollbar {
    width: 8px;
}

.table-responsive-compact::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive-compact::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive-compact::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Sticky header for better navigation */
.table-responsive-compact thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #343a40 !important;
    color: white !important;
    border-bottom: 2px solid #495057;
}

/* Compact form styling */
.form-compact .form-control {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.2;
}

.form-compact .form-control-sm {
    padding: 0.2rem 0.4rem;
    font-size: 0.8rem;
    line-height: 1.1;
}

/* Badge styling for better visibility */
.badge-compact {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Button compact styling */
.btn-compact {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.2;
}

/* Modal sizing adjustments */
@media (min-width: 1200px) {
    .modal-xl {
        max-width: 95%;
    }
}

/* Ensure proper spacing in modal */
.modal-body {
    padding: 1rem;
    max-height: 70vh;
    overflow-y: auto;
}

/* Compact card styling */
.card-compact .card-header {
    padding: 0.5rem 0.75rem;
}

.card-compact .card-body {
    padding: 0.75rem;
}

/* Loading indicator styling */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

/* Selection summary styling */
.selection-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.5rem;
    margin-bottom: 1rem;
}

.selection-summary .badge {
    margin-right: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 0.5rem auto;
    }
    
    .table-responsive-compact {
        max-height: 300px;
    }
    
    .form-compact .col-md-3 {
        margin-bottom: 0.5rem;
    }
}
    </style>
    <script>
        // Global variables
        var selectedInvoices = [];
        var currentPage = 1;
        var perPage = 20;
        var totalPages = 1;
        var totalCount = 0;

        $(document).ready(function() {
            // Initialize the page
            updateSelectedInvoicesSummary();
            
            // Set up event listeners
            $('#perPageSelect').on('change', function() {
                perPage = parseInt($(this).val());
                currentPage = 1;
                searchInvoices();
            });

            // Quick search functionality
            $('#quickSearch').on('input', function() {
                clearTimeout(window.quickSearchTimeout);
                window.quickSearchTimeout = setTimeout(function() {
                    quickSearch();
                }, 300);
            });

            // Invoice number input tracking
            $('#searchInvoiceNo').on('input', function() {
                const value = $(this).val().trim();
                if (value) {
                    const lines = value.split(/[,\n\r]+/).filter(line => line.trim());
                    $('#invoiceCountText').text(lines.length);
                    $('#invoiceCount').show();
                } else {
                    $('#invoiceCount').hide();
                }
            });
        });

        // Modal functions
        function openInvoiceModal() {
            $('#invoiceModal').modal('show');
            // Load invoices immediately when modal opens
            setTimeout(function() {
                searchInvoices();
            }, 500);
        }

        function closeModal() {
            $('#invoiceModal').modal('hide');
        }

        function toggleFilters() {
            const filterSection = $('#filterSection');
            const icon = $('#filterToggleIcon i');
            
            if (filterSection.is(':visible')) {
                filterSection.slideUp();
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                filterSection.slideDown();
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        }

        // Search and filter functions
        function searchInvoices() {
            console.log('Searching invoices...');
            showLoading();
            
            const searchParams = {
                search_invoice_no: $('#searchInvoiceNo').val() || '',
                search_case_ref: $('#searchCaseRef').val() || '',
                search_client: $('#searchClient').val() || '',
                search_billing_party: $('#searchBillingParty').val() || '',
                filter_branch: $('#filterBranch').val() || '',
                filter_start_date: $('#filterStartDate').val() || '',
                filter_end_date: $('#filterEndDate').val() || '',
                page: currentPage,
                per_page: perPage
            };

            console.log('Search params:', searchParams);

            $.ajax({
                url: "{{ route('sst-v2.invoice-list') }}",
                type: 'GET',
                data: searchParams,
                success: function(response) {
                    console.log('AJAX Response:', response);
                    if (response.status === 1) {
                        displayInvoiceList(response.invoiceList);
                        updatePagination(response);
                    } else {
                        showError('Error loading invoices: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    showError('Error loading invoices: ' + error);
                },
                complete: function() {
                    hideLoading();
                }
            });
        }

        function quickSearch() {
            const searchTerm = $('#quickSearch').val().toLowerCase();
            
            if (searchTerm === '') {
                $('.invoice-row').show();
            } else {
                $('.invoice-row').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            
            updateSelectAllCheckbox();
        }

        function clearSearch() {
            $('#searchInvoiceNo').val('');
            $('#searchCaseRef').val('');
            $('#searchClient').val('');
            $('#searchBillingParty').val('');
            $('#filterBranch').val('');
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            $('#quickSearch').val('');
            $('#invoiceCount').hide();
            
            currentPage = 1;
            searchInvoices();
        }

        function clearInvoiceNumbers() {
            $('#searchInvoiceNo').val('');
            $('#invoiceCount').hide();
        }

        function clearDateFilters() {
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
        }

        function clearQuickSearch() {
            $('#quickSearch').val('');
            $('.invoice-row').show();
            updateSelectAllCheckbox();
        }

        // Display functions
        function displayInvoiceList(html) {
            console.log('Displaying invoice list:', html);
            $('#invoiceListContainer').html(html);
            updateSelectAllCheckbox();
        }

        function showLoading() {
            console.log('Showing loading...');
            $('#invoiceLoading').show();
            $('#invoiceListContainer').hide();
        }

        function hideLoading() {
            console.log('Hiding loading...');
            $('#invoiceLoading').hide();
            $('#invoiceListContainer').show();
        }

        function showError(message) {
            console.log('Showing error:', message);
            $('#invoiceListContainer').html('<div class="alert alert-danger">' + message + '</div>');
        }

        function updatePagination(response) {
            currentPage = response.currentPage;
            totalPages = response.totalPages;
            totalCount = response.totalCount;
            
            $('#paginationInfo').html(`Showing ${((currentPage - 1) * perPage) + 1} to ${Math.min(currentPage * perPage, totalCount)} of ${totalCount} entries`);
            
            let paginationHtml = '';
            if (totalPages > 1) {
                paginationHtml += '<nav><ul class="pagination pagination-sm">';
                
                // Previous button
                if (currentPage > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${currentPage - 1})">Previous</a></li>`;
                }
                
                // Page numbers
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, currentPage + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === currentPage ? 'active' : '';
                    paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="goToPage(${i})">${i}</a></li>`;
                }
                
                // Next button
                if (currentPage < totalPages) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${currentPage + 1})">Next</a></li>`;
                }
                
                paginationHtml += '</ul></nav>';
            }
            
            $('#paginationControls').html(paginationHtml);
        }

        function goToPage(page) {
            currentPage = page;
            searchInvoices();
        }

        // Selection functions
        function toggleSelectAll() {
            const selectAll = $('#selectAll').is(':checked');
            $('.invoice-checkbox:visible').prop('checked', selectAll);
            updateSelectedCount();
        }

        function updateSelectAllCheckbox() {
            const visibleCheckboxes = $('.invoice-checkbox:visible');
            const checkedVisibleCheckboxes = visibleCheckboxes.filter(':checked');
            
            if (visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCheckboxes.length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        }

        function updateSelectedCount() {
            const checkedCount = $('.invoice-checkbox:checked').length;
            $('#selectedCount').text(checkedCount + ' selected');
        }

        // Invoice selection functions
        function confirmInvoiceSelection() {
            let newSelections = [];
            
            $('.invoice-checkbox:checked').each(function() {
                const invoiceId = $(this).val();
                const billId = $(this).data('bill-id');
                const sstAmount = parseFloat($(this).data('sst')) || 0;
                
                // Check if this invoice is already selected
                const existingIndex = selectedInvoices.findIndex(invoice => invoice.id == invoiceId);
                
                if (existingIndex === -1) {
                    // Get invoice details from the table row
                    const row = $(this).closest('tr');
                    const invoiceNo = row.find('td:eq(3)').text() || 'N/A';
                    const invoiceDate = row.find('td:eq(4)').text() || 'N/A';
                    const caseRef = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() || 'N/A';
                    const clientName = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() || 'N/A';
                    const paymentDate = row.find('td:eq(8)').text() || 'N/A';
                    const caseId = row.find('td:eq(2) a').attr('href') ? row.find('td:eq(2) a').attr('href').split('/').pop() : null;
                    
                    newSelections.push({
                        id: invoiceId,
                        bill_id: billId,
                        sst_amount: sstAmount,
                        invoice_no: invoiceNo,
                        invoice_date: invoiceDate,
                        case_ref: caseRef,
                        client_name: clientName,
                        payment_date: paymentDate,
                        case_id: caseId
                    });
                }
            });
            
            // Add new selections to the main array
            selectedInvoices = selectedInvoices.concat(newSelections);
            
            // Update the display
            updateSelectedInvoicesSummary();
            closeModal();
        }

        function updateSelectedInvoicesSummary() {
            if (selectedInvoices.length > 0) {
                $('#selectedInvoicesSummary').show();
                $('#selectedCount').text(selectedInvoices.length);
                
                let totalSstAmount = 0;
                selectedInvoices.forEach(invoice => {
                    totalSstAmount += parseFloat(invoice.sst_amount) || 0;
                });
                
                $('#selectedTotalAmount').text(totalSstAmount.toFixed(2));
                $('#pay_amount').val(totalSstAmount.toFixed(2));
                
                // Update the selected invoices table
                updateSelectedInvoicesTable();
            } else {
                $('#selectedInvoicesSummary').hide();
                $('#pay_amount').val('0.00');
            }
        }

        function updateSelectedInvoicesTable() {
            let tableHtml = '<table class="table table-bordered table-striped" style="margin-bottom: 0;">';
            tableHtml += '<thead class="thead-dark"><tr>';
            tableHtml += '<th>No</th><th>Ref No</th><th>Client Name</th><th>Invoice No</th><th>Invoice Date</th><th>Total amt</th><th>Pfee1</th><th>Pfee2</th><th>Collected amt</th><th>SST</th><th>Payment Date</th><th>Action</th>';
            tableHtml += '</tr></thead><tbody>';
            
            selectedInvoices.forEach((invoice, index) => {
                tableHtml += '<tr>';
                tableHtml += `<td>${index + 1}</td>`;
                tableHtml += `<td>${invoice.case_ref}</td>`;
                tableHtml += `<td>${invoice.client_name}</td>`;
                tableHtml += `<td>${invoice.invoice_no}</td>`;
                tableHtml += `<td>${invoice.invoice_date}</td>`;
                tableHtml += `<td class="text-right">0.00</td>`;
                tableHtml += `<td class="text-right">${parseFloat(invoice.pfee1 || 0).toFixed(2)}</td>`;
                tableHtml += `<td class="text-right">${parseFloat(invoice.pfee2 || 0).toFixed(2)}</td>`;
                tableHtml += `<td class="text-right">0.00</td>`;
                tableHtml += `<td class="text-right">${parseFloat(invoice.sst_amount).toFixed(2)}</td>`;
                tableHtml += `<td>${invoice.payment_date}</td>`;
                tableHtml += `<td><button class="btn btn-sm btn-danger" onclick="removeSelectedInvoice(${index})"><i class="fa fa-times"></i></button></td>`;
                tableHtml += '</tr>';
            });
            
            tableHtml += '</tbody></table>';
            $('#selectedInvoicesTable').html(tableHtml).show();
        }

        function removeSelectedInvoice(index) {
            selectedInvoices.splice(index, 1);
            updateSelectedInvoicesSummary();
        }

        function saveTransferFee() {
            if ($("#trx_id").val() == '' || $("#payment_date").val() == '' || $("#branch_sst").val() == 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure all mandatory fields are filled',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            if (selectedInvoices.length <= 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'No invoices selected',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            // Prepare invoice data for submission
            let invoiceData = [];
            selectedInvoices.forEach(invoice => {
                invoiceData.push({
                    id: invoice.id,
                    value: invoice.sst_amount
                });
            });

            var form_data = new FormData();
            form_data.append("add_bill", JSON.stringify(invoiceData));
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("payment_date", $("#payment_date").val());
            form_data.append("remark", $("#remark").val());
            form_data.append("branch", $("#branch_sst").val());

            $.ajax({
                type: 'POST',
                url: '/createNewSSTRecordV2',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {
                        Swal.fire('Success!', 'SST record created successfully', 'success')
                            .then(() => {
                        window.location.href = '/sst-list';
                            });
                    } else {
                        Swal.fire('Error', result.message || 'Failed to create SST record', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to create SST record', 'error');
                }
            });
        }
    </script>
@endsection