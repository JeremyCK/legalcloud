@extends('dashboard.base')

<style>
.case-ref-link {
    color: #007bff !important;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px dotted #007bff;
    transition: all 0.2s ease;
}

.case-ref-link:hover {
    color: #0056b3 !important;
    text-decoration: none;
    border-bottom: 1px solid #0056b3;
    background-color: rgba(0, 123, 255, 0.1);
    padding: 2px 4px;
    border-radius: 3px;
}
</style>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Data Repair - Missing Reimbursement Ledger Entries
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" onclick="loadMissingEntries()">
                            <i class="fas fa-sync"></i> Refresh Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-search"></i> Search Invoices
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="invoice-search">Invoice Numbers:</label>
                                                <textarea id="invoice-search" 
                                                          class="form-control" 
                                                          rows="4" 
                                                          placeholder="Enter multiple invoice numbers separated by commas or new lines&#10;e.g.:&#10;20002412&#10;20002411&#10;DP20000887"
                                                          onkeyup="updateInvoiceCount()"
                                                          onpaste="setTimeout(updateInvoiceCount, 100)"
                                                          onkeydown="handleTextareaKeydown(event)"></textarea>
                                                <small class="form-text text-muted">
                                                    Enter multiple invoice numbers separated by commas or new lines. Press Ctrl+Enter to search quickly. Leave empty to show all invoices with missing entries.
                                                </small>
                                                <div class="mt-2">
                                                    <i class="fas fa-info-circle text-primary"></i>
                                                    <span class="text-primary" id="invoice-count">0 invoice numbers entered</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="d-flex flex-column">
                                                    <button type="button" class="btn btn-primary mb-2" onclick="searchInvoices()">
                                                        <i class="fas fa-search"></i> Search
                                                    </button>
                                                    <button type="button" class="btn btn-secondary ml-2" onclick="clearSearch()">
                                                        <i class="fas fa-times"></i> Clear
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Issues</span>
                                    <span class="info-box-number" id="total-issues">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Invoices with Issues</span>
                                    <span class="info-box-number" id="invoices-with-issues">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Reimbursement Amount</span>
                                    <span class="info-box-number" id="total-reimbursement">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">SST Amount</span>
                                    <span class="info-box-number" id="total-sst">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loading-indicator" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading missing entries...</p>
                    </div>

                    <!-- Data Table -->
                    <div id="data-table-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="missing-entries-table">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Case Ref</th>
                                        <th>Reimbursement Amount</th>
                                        <th>Reimbursement SST</th>
                                        <th>Missing Entries</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="missing-entries-tbody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- No Data Message -->
                    <div id="no-data-message" class="text-center" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> No missing entries found! All reimbursement ledger entries are properly created.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fix All Modal -->
<div class="modal fade" id="fixAllModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fix All Missing Entries</h5>
                <button type="button" class="close" onclick="closeModal()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to fix all missing entries for this invoice?</p>
                <div id="fix-all-details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmFixAll()">Fix All</button>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
// Global variables
let currentInvoiceId = null;
let currentSearchTerm = '';

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadMissingEntries();
    updateInvoiceCount();
});

// Core functions
function loadMissingEntries() {
    console.log('loadMissingEntries called');
    document.getElementById('loading-indicator').style.display = 'block';
    document.getElementById('data-table-container').style.display = 'none';
    document.getElementById('no-data-message').style.display = 'none';
    
    $.ajax({
        url: '{{ route("data-repair.get-missing-entries") }}',
        method: 'GET',
        data: {
            search: currentSearchTerm
        },
        success: function(response) {
            console.log('AJAX success:', response);
            document.getElementById('loading-indicator').style.display = 'none';
            
            if (response.success && response.data.length > 0) {
                displayMissingEntries(response.data);
                updateSummary(response.data);
                showAlert('Found ' + response.data.length + ' invoices with missing entries', 'success');
            } else {
                document.getElementById('no-data-message').style.display = 'block';
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            document.getElementById('loading-indicator').style.display = 'none';
            showAlert('Error loading data: ' + xhr.responseText, 'danger');
        }
    });
}

function updateInvoiceCount() {
    const input = document.getElementById('invoice-search').value;
    if (!input.trim()) {
        document.getElementById('invoice-count').textContent = '0 invoice numbers entered';
        return;
    }
    
    const invoiceNumbers = input.split(/[,\n\s]+/)
        .map(num => num.trim())
        .filter(num => num.length > 0);
    
    const count = invoiceNumbers.length;
    document.getElementById('invoice-count').textContent = count + ' invoice number' + (count !== 1 ? 's' : '') + ' entered';
}

function handleTextareaKeydown(event) {
    if (event.key === 'Enter' && event.ctrlKey) {
        event.preventDefault();
        searchInvoices();
    }
}

function searchInvoices() {
    const rawInput = document.getElementById('invoice-search').value;
    const searchTerm = normalizeSearchInput(rawInput);
    currentSearchTerm = searchTerm;
    
    if (searchTerm === '') {
        loadMissingEntries();
        return;
    }
    
    console.log('Searching for invoices:', searchTerm);
    document.getElementById('loading-indicator').style.display = 'block';
    document.getElementById('data-table-container').style.display = 'none';
    document.getElementById('no-data-message').style.display = 'none';
    
    $.ajax({
        url: '{{ route("data-repair.get-missing-entries") }}',
        method: 'GET',
        data: {
            search: searchTerm
        },
        success: function(response) {
            console.log('Search AJAX success:', response);
            document.getElementById('loading-indicator').style.display = 'none';
            
            if (response.success && response.data.length > 0) {
                displayMissingEntries(response.data);
                updateSummary(response.data);
                showAlert('Found ' + response.data.length + ' invoices matching your search', 'success');
            } else {
                document.getElementById('no-data-message').innerHTML = 
                    '<div class="alert alert-warning">' +
                        '<i class="fas fa-search"></i> No invoices found matching "' + searchTerm + '". ' +
                        '<button class="btn btn-sm btn-outline-primary ml-2" onclick="clearSearch()">Clear Search</button>' +
                    '</div>';
                document.getElementById('no-data-message').style.display = 'block';
            }
        },
        error: function(xhr, status, error) {
            console.error('Search AJAX error:', error);
            document.getElementById('loading-indicator').style.display = 'none';
            showAlert('Error searching: ' + xhr.responseText, 'danger');
        }
    });
}

function clearSearch() {
    document.getElementById('invoice-search').value = '';
    currentSearchTerm = '';
    updateInvoiceCount();
    loadMissingEntries();
    showAlert('Search cleared. Showing all invoices with missing entries.', 'info');
}

function normalizeSearchInput(input) {
    return input.replace(/\s+/g, ' ').trim();
}

function closeModal() {
    const modal = document.getElementById('fixAllModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
}

function displayMissingEntries(data) {
    const tbody = document.getElementById('missing-entries-tbody');
    tbody.innerHTML = '';
    
    data.forEach(function(item) {
        const invoice = item.invoice;
        const missingEntries = item.missing_entries;
        
        const row = 
            '<tr>' +
                '<td>' + invoice.invoice_no + '</td>' +
                '<td><a href="/case/' + invoice.case_id + '" target="_blank" class="case-ref-link" title="Click to view case details (opens in new tab)">' + invoice.case_ref_no + '</a></td>' +
                '<td>RM ' + parseFloat(invoice.reimbursement_amount).toFixed(2) + '</td>' +
                '<td>RM ' + parseFloat(invoice.reimbursement_sst || 0).toFixed(2) + '</td>' +
                '<td>' + missingEntries.map(entry => '<span class="badge badge-danger">' + entry + '</span>').join(' ') + '</td>' +
                '<td>' +
                    '<button class="btn btn-sm btn-warning" onclick="fixSingleEntry(' + invoice.invoice_id + ', \'' + missingEntries[0] + '\')">' +
                        'Fix ' + missingEntries[0] +
                    '</button>' +
                    (missingEntries.length > 1 ? 
                        '<button class="btn btn-sm btn-primary" onclick="showFixAllModal(' + invoice.invoice_id + ', ' + JSON.stringify(missingEntries).replace(/"/g, '&quot;') + ')">' +
                            'Fix All (' + missingEntries.length + ')' +
                        '</button>' : '') +
                '</td>' +
            '</tr>';
        tbody.innerHTML += row;
    });
    
    document.getElementById('data-table-container').style.display = 'block';
}

function updateSummary(data) {
    let totalIssues = 0;
    let totalReimbursement = 0;
    let totalSst = 0;
    
    data.forEach(function(item) {
        totalIssues += item.total_missing;
        totalReimbursement += parseFloat(item.invoice.reimbursement_amount);
        totalSst += parseFloat(item.invoice.reimbursement_sst || 0);
    });
    
    document.getElementById('total-issues').textContent = totalIssues;
    document.getElementById('invoices-with-issues').textContent = data.length;
    document.getElementById('total-reimbursement').textContent = 'RM ' + totalReimbursement.toFixed(2);
    document.getElementById('total-sst').textContent = 'RM ' + totalSst.toFixed(2);
}

function fixSingleEntry(invoiceId, entryType) {
    $.ajax({
        url: '{{ route("data-repair.fix-single-entry") }}',
        method: 'POST',
        data: {
            invoice_id: invoiceId,
            entry_type: entryType,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                loadMissingEntries();
            } else {
                showAlert(response.message, 'danger');
            }
        },
        error: function(xhr) {
            showAlert('Error: ' + xhr.responseText, 'danger');
        }
    });
}

function showFixAllModal(invoiceId, missingEntries) {
    currentInvoiceId = invoiceId;
    
    const details = missingEntries.map(entry => 
        '<span class="badge badge-danger">' + entry + '</span>'
    ).join(' ');
    
    document.getElementById('fix-all-details').innerHTML = 
        '<p><strong>Missing Entries:</strong> ' + details + '</p>';
    
    const modal = document.getElementById('fixAllModal');
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }
}

function confirmFixAll() {
    if (!currentInvoiceId) return;
    
    closeModal();
    
    $.ajax({
        url: '{{ route("data-repair.fix-all-entries") }}',
        method: 'POST',
        data: {
            invoice_id: currentInvoiceId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                loadMissingEntries();
            } else {
                showAlert(response.message, 'danger');
            }
        },
        error: function(xhr) {
            showAlert('Error: ' + xhr.responseText, 'danger');
        }
    });
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alert = 
        '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas ' + icon + '"></i> ' + message +
            '<button type="button" class="close" data-dismiss="alert">' +
                '<span>&times;</span>' +
            '</button>' +
        '</div>';
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top
    $('.card-body').prepend(alert);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>