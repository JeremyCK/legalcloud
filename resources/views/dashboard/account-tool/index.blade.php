@extends('dashboard.base')

<style>
.case-ref-link {
    color: #007bff !important;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px dotted #007bff;
}
.nav-tabs .nav-link {
    cursor: pointer;
}
</style>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Account Tool
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="accountToolTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="fix-invoice-tab" data-toggle="tab" href="#fix-invoice" role="tab" aria-controls="fix-invoice" aria-selected="true">
                                <i class="fas fa-wrench"></i> Fix Invoice
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="convert-reimb-tab" data-toggle="tab" href="#convert-reimb" role="tab" aria-controls="convert-reimb" aria-selected="false">
                                <i class="fas fa-exchange-alt"></i> Convert Reimbursement
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="accountToolTabContent">
                        <!-- Fix Invoice Tab -->
                        <div class="tab-pane fade show active" id="fix-invoice" role="tabpanel" aria-labelledby="fix-invoice-tab">
                            <div class="mt-4">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h5 class="card-title">
                                                    <i class="fas fa-bolt"></i> Fix Invoices by Invoice Number
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i> 
                                                    <strong>What this does:</strong> Fixes invoice amounts (pfee1, pfee2, SST, reimbursement, reimbursement SST) and ensures ledger entries are correct.
                                                    It will update invoice amounts, transfer fee details, and create/update ledger entries automatically.
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="invoice-numbers-input">
                                                                <strong>Enter Invoice Numbers:</strong>
                                                            </label>
                                                            <textarea 
                                                                id="invoice-numbers-input" 
                                                                class="form-control" 
                                                                rows="5" 
                                                                placeholder="Enter invoice numbers separated by commas or new lines&#10;&#10;Example:&#10;DP20000849&#10;DP20001000&#10;DP20000887&#10;&#10;Or: DP20000849, DP20001000, DP20000887"></textarea>
                                                            <small class="form-text text-muted">
                                                                You can enter multiple invoice numbers separated by commas or new lines.
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <div>
                                                                <button type="button" class="btn btn-warning btn-lg btn-block" onclick="fixInvoices()">
                                                                    <i class="fas fa-wrench"></i> Fix Invoices
                                                                </button>
                                                            </div>
                                                            <div class="mt-2">
                                                                <button type="button" class="btn btn-secondary btn-block" onclick="clearInput()">
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

                                <!-- Results Section -->
                                <div id="results-section" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">
                                                        <i class="fas fa-check-circle"></i> Fix Results
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="results-content"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading Indicator -->
                                <div id="loading-section" class="text-center" style="display: none;">
                                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p class="mt-3"><strong>Fixing invoices, please wait...</strong></p>
                                </div>
                            </div>
                        </div>

                        <!-- Convert Reimbursement Tab -->
                        <div class="tab-pane fade" id="convert-reimb" role="tabpanel" aria-labelledby="convert-reimb-tab">
                            <div class="mt-4">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card card-success">
                                            <div class="card-header">
                                                <h5 class="card-title">
                                                    <i class="fas fa-exchange-alt"></i> Convert Reimbursement to New Format
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i> 
                                                    <strong>What this does:</strong> Converts bill details from old disbursement format (account_cat_id = 3) to new reimbursement format (account_cat_id = 4).
                                                    Only bills that haven't been invoiced (bln_invoice = 0) can be converted.
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="bill-search-input">
                                                                <strong>Search by Case Number or Bill Number:</strong>
                                                            </label>
                                                            <input 
                                                                type="text" 
                                                                id="bill-search-input" 
                                                                class="form-control" 
                                                                placeholder="Enter case ref no or bill no (e.g., 4863 or 101973)"
                                                                onkeyup="searchBills(this.value)">
                                                            <small class="form-text text-muted">
                                                                Search for bills that haven't been invoiced yet.
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <div>
                                                                <button type="button" class="btn btn-success btn-lg btn-block" onclick="convertSelectedBills()">
                                                                    <i class="fas fa-exchange-alt"></i> Convert Selected
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bills List -->
                                                <div id="bills-list-section" style="display: none;">
                                                    <div class="mt-3">
                                                        <h6>Found Bills:</h6>
                                                        <div id="bills-list-content"></div>
                                                    </div>
                                                </div>

                                                <!-- Conversion Results -->
                                                <div id="conversion-results-section" style="display: none;">
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">
                                                                        <i class="fas fa-check-circle"></i> Conversion Results
                                                                    </h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div id="conversion-results-content"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Loading Indicator -->
                                                <div id="conversion-loading-section" class="text-center" style="display: none;">
                                                    <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p class="mt-3"><strong>Converting reimbursement, please wait...</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedBillIds = [];

// Fix Invoice Functions (existing)
function fixInvoices() {
    const invoiceNumbers = document.getElementById('invoice-numbers-input').value.trim();
    
    if (!invoiceNumbers) {
        alert('Please enter at least one invoice number');
        return;
    }
    
    const invoiceList = invoiceNumbers.split(/[,\n]+/).map(s => s.trim()).filter(s => s);
    
    if (invoiceList.length === 0) {
        alert('Please enter valid invoice numbers');
        return;
    }
    
    document.getElementById('results-section').style.display = 'none';
    document.getElementById('loading-section').style.display = 'block';
    
    fetch('/invoice-fix/fix-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            invoice_numbers: invoiceList
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-section').style.display = 'none';
        document.getElementById('results-section').style.display = 'block';
        
        let html = '';
        if (data.success && data.results && Array.isArray(data.results)) {
            const successCount = data.results.filter(r => r.success).length;
            const errorCount = data.results.filter(r => !r.success).length;
            
            html += `<div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Success! Fixed ${successCount} invoice(s) successfully. ${errorCount} error(s).
            </div>`;
            
            if (data.results.length > 0) {
                html += '<div class="table-responsive"><table class="table table-bordered table-striped">';
                html += '<thead><tr><th>Invoice No</th><th>Status</th><th>Details</th></tr></thead><tbody>';
                
                data.results.forEach(result => {
                    if (result.success && result.data) {
                        let ledgerEntriesHtml = '';
                        if (result.data.ledger_entries && result.data.ledger_entries.length > 0) {
                            ledgerEntriesHtml = '<div class="mt-2"><strong>Ledger Entries:</strong>';
                            ledgerEntriesHtml += '<div class="table-responsive mt-2"><table class="table table-bordered table-sm">';
                            ledgerEntriesHtml += '<thead><tr><th>Type</th><th>Amount</th><th>Transaction ID</th></tr></thead><tbody>';
                            result.data.ledger_entries.forEach(le => {
                                ledgerEntriesHtml += `<tr>
                                    <td>${le.type}</td>
                                    <td class="text-right">RM ${parseFloat(le.amount).toFixed(2)}</td>
                                    <td>${le.transaction_id || 'N/A'}</td>
                                </tr>`;
                            });
                            ledgerEntriesHtml += '</tbody></table></div></div>';
                        }
                        
                        html += `<tr>
                            <td><strong>${result.data.invoice_no}</strong></td>
                            <td><span class="badge badge-success">SUCCESS</span></td>
                            <td>
                                <strong>Invoice Amounts:</strong><br>
                                Pfee1: RM ${parseFloat(result.data.pfee1).toFixed(2)}<br>
                                Pfee2: RM ${parseFloat(result.data.pfee2).toFixed(2)}<br>
                                SST: RM ${parseFloat(result.data.sst).toFixed(2)}<br>
                                Reimbursement: RM ${parseFloat(result.data.reimbursement_amount).toFixed(2)}<br>
                                Reimbursement SST: RM ${parseFloat(result.data.reimbursement_sst).toFixed(2)}<br>
                                Total: RM ${parseFloat(result.data.total).toFixed(2)}<br><br>
                                <strong>Ledger:</strong> ${result.data.ledger_entries_updated} entries updated, ${result.data.ledger_entries_created || 0} entries created
                                ${ledgerEntriesHtml}
                            </td>
                        </tr>`;
                    } else {
                        html += `<tr>
                            <td>${result.invoice_no || 'N/A'}</td>
                            <td><span class="badge badge-danger">ERROR</span></td>
                            <td>${result.message || 'Unknown error'}</td>
                        </tr>`;
                    }
                });
                
                html += '</tbody></table></div>';
            }
        } else {
            html += `<div class="alert alert-danger">${data.message || 'Error occurred'}</div>`;
        }
        
        document.getElementById('results-content').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('loading-section').style.display = 'none';
        document.getElementById('results-section').style.display = 'block';
        document.getElementById('results-content').innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}

function clearInput() {
    document.getElementById('invoice-numbers-input').value = '';
    document.getElementById('results-section').style.display = 'none';
}

// Convert Reimbursement Functions
function searchBills(searchTerm) {
    if (!searchTerm || searchTerm.length < 2) {
        document.getElementById('bills-list-section').style.display = 'none';
        selectedBillIds = [];
        return;
    }
    
    fetch('/invoice-fix/search-bills?search=' + encodeURIComponent(searchTerm))
        .then(response => response.json())
        .then(data => {
            if (data.bills && data.bills.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-hover" style="width: 100%;">';
                html += '<thead class="thead-light"><tr>';
                html += '<th style="width: 80px; text-align: center;"><strong>Select</strong><br><div class="checkbox" style="margin-top: 5px;"><input type="checkbox" id="select-all-bills" onchange="toggleAllBills(this.checked)" title="Select All"><label for="select-all-bills"></label></div></th>';
                html += '<th>Bill No</th><th>Case Ref No</th><th>Details Count</th>';
                html += '</tr></thead><tbody>';
                
                data.bills.forEach(bill => {
                    html += '<tr>';
                    html += `<td style="text-align: center; vertical-align: middle;"><div class="checkbox"><input type="checkbox" class="bill-checkbox" name="bill" value="${bill.id}" id="chk_${bill.id}" onchange="updateSelectedBills()"><label for="chk_${bill.id}"></label></div></td>`;
                    html += `<td><strong>${bill.bill_no}</strong></td>`;
                    html += `<td>${bill.case_ref_no || 'N/A'}</td>`;
                    html += `<td>${bill.detail_count || 0}</td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                html += `<div class="mt-2 alert alert-info"><i class="fas fa-info-circle"></i> Selected: <strong><span id="selected-count">0</span></strong> bill(s) ready for conversion</div>`;
                document.getElementById('bills-list-content').innerHTML = html;
                document.getElementById('bills-list-section').style.display = 'block';
                selectedBillIds = [];
                updateSelectedBills(); // Initialize count
            } else {
                document.getElementById('bills-list-section').style.display = 'none';
                document.getElementById('bills-list-content').innerHTML = '<div class="alert alert-warning">No bills found</div>';
            }
        })
        .catch(error => {
            console.error('Error searching bills:', error);
        });
}

function toggleAllBills(checked) {
    document.querySelectorAll('input[name="bill"]').forEach(cb => {
        cb.checked = checked;
    });
    updateSelectedBills();
}

function updateSelectedBills() {
    selectedBillIds = Array.from(document.querySelectorAll('input[name="bill"]:checked')).map(cb => parseInt(cb.value));
    const countElement = document.getElementById('selected-count');
    if (countElement) {
        countElement.textContent = selectedBillIds.length;
    }
}

function convertSelectedBills() {
    if (selectedBillIds.length === 0) {
        alert('Please select at least one bill to convert');
        return;
    }
    
    if (!confirm(`Are you sure you want to convert ${selectedBillIds.length} bill(s)? This action cannot be undone.`)) {
        return;
    }
    
    document.getElementById('conversion-results-section').style.display = 'none';
    document.getElementById('conversion-loading-section').style.display = 'block';
    
    fetch('/invoice-fix/convert-reimbursement', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            bill_ids: selectedBillIds
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('conversion-loading-section').style.display = 'none';
        document.getElementById('conversion-results-section').style.display = 'block';
        
        let html = '';
        if (data.success) {
            html += `<div class="alert alert-success">
                <i class="fas fa-check-circle"></i> ${data.message}
            </div>`;
            
            if (data.errors && data.errors.length > 0) {
                html += `<div class="alert alert-warning">
                    <strong>Warnings:</strong><ul>${data.errors.map(e => '<li>' + e + '</li>').join('')}</ul>
                </div>`;
            }
        } else {
            html += `<div class="alert alert-danger">${data.message || 'Error occurred'}</div>`;
        }
        
        document.getElementById('conversion-results-content').innerHTML = html;
        
        // Clear selection and refresh search
        selectedBillIds = [];
        const searchTerm = document.getElementById('bill-search-input').value;
        if (searchTerm) {
            setTimeout(() => searchBills(searchTerm), 1000);
        }
    })
    .catch(error => {
        document.getElementById('conversion-loading-section').style.display = 'none';
        document.getElementById('conversion-results-section').style.display = 'block';
        document.getElementById('conversion-results-content').innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}
</script>
@endsection

