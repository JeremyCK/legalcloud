@extends('dashboard.base')

<style>
.case-ref-link {
    color: #007bff !important;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px dotted #007bff;
}
</style>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-wrench"></i> Fix Invoice Amounts and Ledger Entries
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Simple Fix Section -->
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
        </div>
    </div>
</div>

@endsection

<script>
function fixInvoices() {
    const input = document.getElementById('invoice-numbers-input').value.trim();
    
    if (!input) {
        alert('Please enter at least one invoice number');
        return;
    }
    
    if (!confirm('Are you sure you want to fix these invoices?\n\nThis will update invoice amounts and ledger entries.\n\nProceed?')) {
        return;
    }
    
    // Show loading
    document.getElementById('loading-section').style.display = 'block';
    document.getElementById('results-section').style.display = 'none';
    
    // Parse invoice numbers
    const invoiceNumbers = input.split(/[,\n\r]+/)
        .map(num => num.trim())
        .filter(num => num.length > 0);
    
    $.ajax({
        url: '{{ route("invoice-fix.fix-multiple") }}',
        method: 'POST',
        data: {
            invoice_numbers: invoiceNumbers.join(','),
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            document.getElementById('loading-section').style.display = 'none';
            document.getElementById('results-section').style.display = 'block';
            
            if (response.success) {
                let html = '<div class="alert alert-success"><h5><i class="fas fa-check-circle"></i> Success!</h5>';
                html += '<p>' + (response.message || 'Fixed ' + invoiceNumbers.length + ' invoice(s) successfully.') + '</p></div>';
                
                if (response.data && response.data.length > 0) {
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-bordered table-striped mt-3">';
                    html += '<thead><tr><th>Invoice No</th><th>Status</th><th>Details</th></tr></thead>';
                    html += '<tbody>';
                    
                    response.data.forEach(function(item) {
                        const statusClass = item.status === 'SUCCESS' ? 'success' : 'danger';
                        html += '<tr>';
                        html += '<td><strong>' + (item.invoice_no || 'N/A') + '</strong></td>';
                        html += '<td><span class="badge badge-' + statusClass + '">' + item.status + '</span></td>';
                        html += '<td>';
                        
                        if (item.details) {
                            html += '<div class="small">';
                            html += '<strong>Invoice Amounts:</strong><br>';
                            html += 'Pfee1: RM ' + parseFloat(item.details.pfee1 || 0).toFixed(2) + '<br>';
                            html += 'Pfee2: RM ' + parseFloat(item.details.pfee2 || 0).toFixed(2) + '<br>';
                            html += 'SST: RM ' + parseFloat(item.details.sst || 0).toFixed(2) + '<br>';
                            html += 'Reimbursement: RM ' + parseFloat(item.details.reimbursement_amount || 0).toFixed(2) + '<br>';
                            html += 'Reimbursement SST: RM ' + parseFloat(item.details.reimbursement_sst || 0).toFixed(2) + '<br>';
                            html += 'Total: RM ' + parseFloat(item.details.total || 0).toFixed(2) + '<br>';
                            
                            if (item.details.ledger_entries_updated !== undefined) {
                                html += '<br><strong>Ledger:</strong> ' + item.details.ledger_entries_updated + ' entries updated<br>';
                            }
                            
                            if (item.details.ledger_entries && item.details.ledger_entries.length > 0) {
                                html += '<br><strong>Ledger Entries:</strong><br>';
                                html += '<table class="table table-sm table-bordered mt-2">';
                                html += '<thead><tr><th>Type</th><th>Amount</th><th>Transaction ID</th></tr></thead><tbody>';
                                item.details.ledger_entries.forEach(function(le) {
                                    html += '<tr>';
                                    html += '<td>' + le.type + '</td>';
                                    html += '<td>RM ' + parseFloat(le.amount || 0).toFixed(2) + '</td>';
                                    html += '<td>' + (le.transaction_id || 'N/A') + '</td>';
                                    html += '</tr>';
                                });
                                html += '</tbody></table>';
                            }
                            
                            html += '</div>';
                        } else {
                            html += item.message || 'N/A';
                        }
                        
                        html += '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    html += '</div>';
                }
                
                document.getElementById('results-content').innerHTML = html;
            } else {
                document.getElementById('results-content').innerHTML = 
                    '<div class="alert alert-danger"><h5><i class="fas fa-exclamation-circle"></i> Error</h5><p>' + 
                    (response.message || 'An error occurred') + '</p></div>';
            }
        },
        error: function(xhr) {
            document.getElementById('loading-section').style.display = 'none';
            document.getElementById('results-section').style.display = 'block';
            
            const errorMsg = xhr.responseJSON?.message || xhr.responseText || 'An error occurred';
            document.getElementById('results-content').innerHTML = 
                '<div class="alert alert-danger"><h5><i class="fas fa-exclamation-circle"></i> Error</h5><p>' + 
                errorMsg + '</p></div>';
        }
    });
}

function clearInput() {
    document.getElementById('invoice-numbers-input').value = '';
    document.getElementById('results-section').style.display = 'none';
    document.getElementById('results-content').innerHTML = '';
}
</script>
