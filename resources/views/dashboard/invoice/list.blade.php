@extends('dashboard.base')

@section('css')
<style>
    .invoice-table {
        font-size: 0.9rem;
    }
    .search-form {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h4><i class="cil-list"></i> Invoice List</h4>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="btn btn-primary" onclick="refreshInvoiceList()">
                                    <i class="cil-reload"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <div class="search-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Quick Search (Invoice No)</label>
                                        <input type="text" class="form-control" id="search_invoice_no" 
                                            placeholder="Type invoice no to search..." autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Case Ref No</label>
                                        <input type="text" class="form-control" id="search_case_ref" 
                                            placeholder="Search by case ref">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Bill No</label>
                                        <input type="text" class="form-control" id="search_bill_no" 
                                            placeholder="Search by bill no">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date From</label>
                                        <input type="date" class="form-control" id="filter_date_from">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Date To</label>
                                        <input type="date" class="form-control" id="filter_date_to">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-primary btn-block" onclick="searchInvoices()">
                                            <i class="cil-magnifying-glass"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>SST Status</label>
                                        <select class="form-control" id="filter_sst_status">
                                            <option value="unpaid" selected>Unpaid</option>
                                            <option value="paid">Paid</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Transferred Status</label>
                                        <select class="form-control" id="filter_transferred_status">
                                            <option value="all" selected>All</option>
                                            <option value="transferred">Transferred</option>
                                            <option value="not_transferred">Not Transferred</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover invoice-table" id="invoice-list-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Invoice Date</th>
                                        <th>Case Ref No</th>
                                        <th>Bill No</th>
                                        <th>Client Name</th>
                                        <th>Branch</th>
                                        <th>Amount (RM)</th>
                                        <th>SST Paid Status</th>
                                        <th>Payment Date</th>
                                        <th>Transferred</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-list-tbody">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <nav id="invoice-pagination">
                                    <!-- Pagination will be loaded here -->
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Invoice</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editInvoiceForm">
                    <input type="hidden" id="edit_invoice_id" name="invoice_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Invoice No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_invoice_no" name="invoice_no" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_invoice_date" name="Invoice_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Case Ref No</label>
                                <input type="text" class="form-control" id="edit_case_ref" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount (RM) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6>Invoice Details</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Account Item</th>
                                    <th>Amount (RM)</th>
                                </tr>
                            </thead>
                            <tbody id="edit_invoice_details_tbody">
                                <!-- Details will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveInvoice()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    var currentPage = 1;

    $(document).ready(function() {
        loadInvoiceList();
    });

    function loadInvoiceList(page = 1) {
        currentPage = page;
        
        var searchInvoiceNo = $('#search_invoice_no').val();
        var searchCaseRef = $('#search_case_ref').val();
        var searchBillNo = $('#search_bill_no').val();
        var filterSstStatus = $('#filter_sst_status').val() || 'unpaid';
        var filterDateFrom = $('#filter_date_from').val();
        var filterDateTo = $('#filter_date_to').val();
        var filterTransferredStatus = $('#filter_transferred_status').val() || 'all';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '{{ route("invoice.list.data") }}',
            type: 'GET',
            data: {
                search_invoice_no: searchInvoiceNo,
                search_case_ref: searchCaseRef,
                search_bill_no: searchBillNo,
                filter_sst_status: filterSstStatus,
                filter_date_from: filterDateFrom,
                filter_date_to: filterDateTo,
                filter_transferred_status: filterTransferredStatus,
                page: page
            },
            success: function(response) {
                if (response.status === 1) {
                    renderInvoiceTable(response.data);
                    renderPagination(response.pagination);
                } else {
                    $('#invoice-list-tbody').html('<tr><td colspan="11" class="text-center text-danger">' + (response.message || 'Failed to load invoices') + '</td></tr>');
                }
            },
            error: function(xhr) {
                $('#invoice-list-tbody').html('<tr><td colspan="11" class="text-center text-danger">Error loading invoices. Please try again.</td></tr>');
                console.error('Error:', xhr);
            }
        });
    }

    function renderInvoiceTable(invoices) {
        var tbody = $('#invoice-list-tbody');
        tbody.empty();

        if (invoices.length === 0) {
            tbody.html('<tr><td colspan="11" class="text-center text-muted">No invoices found</td></tr>');
            return;
        }

        invoices.forEach(function(invoice) {
            var transferredBadge = invoice.transferred_status == 1 ? 
                '<span class="badge badge-success">Yes</span>' : 
                '<span class="badge badge-secondary">No</span>';

            var sstStatusBadge = invoice.sst_paid_status == 1 ? 
                '<span class="badge badge-success">Paid</span>' : 
                '<span class="badge badge-warning">Unpaid</span>';

            var row = '<tr>' +
                '<td>' + (invoice.invoice_no || '-') + '</td>' +
                '<td>' + (invoice.Invoice_date ? formatDate(invoice.Invoice_date) : '-') + '</td>' +
                '<td><a href="/case/' + invoice.case_id + '" target="_blank">' + (invoice.case_ref_no || '-') + '</a></td>' +
                '<td>' + (invoice.bill_invoice_no || invoice.bill_id || '-') + '</td>' +
                '<td>' + (invoice.client_name || '-') + '</td>' +
                '<td>' + (invoice.branch_name || '-') + '</td>' +
                '<td class="text-right">' + formatCurrency(invoice.amount || 0) + '</td>' +
                '<td class="text-center">' + sstStatusBadge + '</td>' +
                '<td>' + (invoice.payment_receipt_date ? formatDate(invoice.payment_receipt_date) : '-') + '</td>' +
                '<td class="text-center">' + transferredBadge + '</td>' +
                '<td>' +
                    '<a href="/invoice/' + invoice.id + '/details" class="btn btn-sm btn-info" title="View Details">' +
                        '<i class="cil-pencil"></i> Edit' +
                    '</a>' +
                '</td>' +
            '</tr>';
            tbody.append(row);
        });
    }

    function renderPagination(pagination) {
        var paginationHtml = '<ul class="pagination justify-content-center">';
        
        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadInvoiceList(' + (pagination.current_page - 1) + ')">Previous</a></li>';
        }

        // Calculate page range to show (max 10 pages at a time)
        var startPage = Math.max(1, pagination.current_page - 4);
        var endPage = Math.min(pagination.last_page, pagination.current_page + 5);
        
        // Adjust if we're near the start
        if (pagination.current_page <= 5) {
            endPage = Math.min(10, pagination.last_page);
        }
        
        // Adjust if we're near the end
        if (pagination.current_page > pagination.last_page - 5) {
            startPage = Math.max(1, pagination.last_page - 9);
        }

        // First page
        if (startPage > 1) {
            paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadInvoiceList(1)">1</a></li>';
            if (startPage > 2) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Page numbers
        for (var i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHtml += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
            } else {
                paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadInvoiceList(' + i + ')">' + i + '</a></li>';
            }
        }

        // Last page
        if (endPage < pagination.last_page) {
            if (endPage < pagination.last_page - 1) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadInvoiceList(' + pagination.last_page + ')">' + pagination.last_page + '</a></li>';
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadInvoiceList(' + (pagination.current_page + 1) + ')">Next</a></li>';
        }

        paginationHtml += '</ul>';
        paginationHtml += '<div class="text-center mt-2"><small>Showing page ' + pagination.current_page + ' of ' + pagination.last_page + ' (Total: ' + pagination.total + ' invoices)</small></div>';
        
        $('#invoice-pagination').html(paginationHtml);
    }

    function searchInvoices() {
        loadInvoiceList(1);
    }

    function refreshInvoiceList() {
        $('#search_invoice_no').val('');
        $('#search_case_ref').val('');
        $('#search_bill_no').val('');
        $('#filter_sst_status').val('unpaid');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#filter_transferred_status').val('all');
        loadInvoiceList(1);
    }

    // Quick search with debounce for invoice no
    var searchTimeout;
    $('#search_invoice_no').on('input', function() {
        clearTimeout(searchTimeout);
        var searchValue = $(this).val();
        
        // Only search if at least 2 characters or empty
        if (searchValue.length >= 2 || searchValue.length === 0) {
            searchTimeout = setTimeout(function() {
                loadInvoiceList(1);
            }, 500); // Wait 500ms after user stops typing
        }
    });

    function editInvoice(invoiceId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/invoice/' + invoiceId + '/details',
            type: 'GET',
            success: function(response) {
                if (response.status === 1) {
                    var invoice = response.invoice;
                    var details = response.details;

                    $('#edit_invoice_id').val(invoice.id);
                    $('#edit_invoice_no').val(invoice.invoice_no || '');
                    $('#edit_invoice_date').val(invoice.Invoice_date ? invoice.Invoice_date.split(' ')[0] : '');
                    $('#edit_case_ref').val(invoice.loanCaseBillMain?.loanCase?.case_ref_no || '');
                    $('#edit_amount').val(invoice.amount || 0);

                    // Render invoice details
                    var detailsTbody = $('#edit_invoice_details_tbody');
                    detailsTbody.empty();

                    if (details.length === 0) {
                        detailsTbody.html('<tr><td colspan="3" class="text-center text-muted">No details found</td></tr>');
                    } else {
                        details.forEach(function(detail) {
                            var row = '<tr>' +
                                '<td>' + (detail.category_name || '-') + '</td>' +
                                '<td>' + (detail.account_name || '-') + '</td>' +
                                '<td>' +
                                    '<input type="number" step="0.01" class="form-control form-control-sm invoice-detail-amount" ' +
                                    'data-detail-id="' + detail.id + '" value="' + (detail.amount || 0) + '">' +
                                '</td>' +
                            '</tr>';
                            detailsTbody.append(row);
                        });
                    }

                    $('#editInvoiceModal').modal('show');
                } else {
                    alert(response.message || 'Failed to load invoice details');
                }
            },
            error: function(xhr) {
                alert('Error loading invoice details. Please try again.');
                console.error('Error:', xhr);
            }
        });
    }

    function saveInvoice() {
        var invoiceId = $('#edit_invoice_id').val();
        var invoiceNo = $('#edit_invoice_no').val();
        var invoiceDate = $('#edit_invoice_date').val();
        var amount = $('#edit_amount').val();

        if (!invoiceNo || !invoiceDate || !amount) {
            alert('Please fill in all required fields');
            return;
        }

        // Collect invoice details
        var details = [];
        $('.invoice-detail-amount').each(function() {
            details.push({
                id: $(this).data('detail-id'),
                amount: $(this).val()
            });
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
                amount: amount,
                details: details
            },
            success: function(response) {
                if (response.status === 1) {
                    alert('Invoice updated successfully');
                    $('#editInvoiceModal').modal('hide');
                    loadInvoiceList(currentPage);
                } else {
                    alert(response.message || 'Failed to update invoice');
                }
            },
            error: function(xhr) {
                alert('Error updating invoice. Please try again.');
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

    // Allow Enter key to trigger search
    $('#search_case_ref, #search_bill_no, #filter_sst_status, #filter_date_from, #filter_date_to, #filter_transferred_status').on('keypress change', function(e) {
        if (e.type === 'change' || (e.type === 'keypress' && e.which === 13)) {
            searchInvoices();
        }
    });
</script>
@endsection

