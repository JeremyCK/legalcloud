@extends('dashboard.base')

<!-- Enhanced CSS for modern UI -->
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap4.min.css" rel="stylesheet">
<style>
    .transfer-fee-enhanced {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .stats-label {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .enhanced-btn {
        border-radius: 25px;
        padding: 10px 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    
    .enhanced-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .enhanced-table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .enhanced-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-recon {
        background: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
    }
    
    .search-section {
        position: relative;
    }
    
    .search-input {
        border-radius: 25px;
        padding: 12px 50px 12px 20px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
    }
    
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="fade-in">
        
        <!-- Enhanced Header Section -->
        <div class="transfer-fee-enhanced">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-0"><i class="fa fa-exchange mr-2"></i>Transfer Fee Management v2.0</h2>
                    <p class="mb-0 mt-2 opacity-75">Enhanced transfer fee management system with advanced features</p>
                </div>
                <div class="col-md-4 text-right">
                    <a class="btn btn-light enhanced-btn" href="/transfer-fee-create">
                        <i class="fa fa-plus mr-2"></i>Create New Transfer
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number" id="totalTransfers">-</div>
                    <div class="stats-label">Total Transfers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number text-success" id="totalAmount">-</div>
                    <div class="stats-label">Total Amount</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number text-warning" id="pendingRecon">-</div>
                    <div class="stats-label">Pending Recon</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number text-info" id="thisMonth">-</div>
                    <div class="stats-label">This Month</div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="filter-section">
            <h5 class="mb-3"><i class="fa fa-filter mr-2"></i>Advanced Filters</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Date Range From</label>
                        <input class="form-control" type="date" id="transfer_date_from" name="transfer_date_from">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Date Range To</label>
                        <input class="form-control" type="date" id="transfer_date_to" name="transfer_date_to">
                    </div>
                </div>
                @if(in_array($current_user->menuroles, ['admin','sales','account']))
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Branch</label>
                        <select class="form-control" id="branch_id" name="branch_id">
                            <option value="0">-- All Branches --</option>
                            @foreach($Branchs as $branch)
                            <option value="{{$branch->id}}">{{$branch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Reconciliation Status</label>
                        <select class="form-control" id="recon_status" name="recon_status">
                            <option value="">-- All Status --</option>
                            <option value="1">Reconciled</option>
                            <option value="0">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group search-section">
                        <label class="font-weight-bold">Quick Search</label>
                        <input class="form-control search-input" type="text" id="global_search" placeholder="Search by Transaction ID, Purpose, Bank...">
                        <i class="fa fa-search search-icon"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Amount Range From</label>
                        <input class="form-control" type="number" id="amount_from" placeholder="Min Amount">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Amount Range To</label>
                        <input class="form-control" type="number" id="amount_to" placeholder="Max Amount">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-right">
                    <button class="btn btn-primary enhanced-btn mr-2" id="applyFiltersBtn">
                        <i class="fa fa-search mr-2"></i>Apply Filters
                    </button>
                    <button class="btn btn-secondary enhanced-btn" id="clearFiltersBtn">
                        <i class="fa fa-undo mr-2"></i>Clear All
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Data Table -->
        <div class="card">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0"><i class="fa fa-list mr-2"></i>Transfer Fee Records</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-success enhanced-btn" id="exportExcelBtn">
                                <i class="fa fa-file-excel-o mr-2"></i>Excel
                            </button>
                            <button class="btn btn-outline-danger enhanced-btn" id="exportPdfBtn">
                                <i class="fa fa-file-pdf-o mr-2"></i>PDF
                            </button>
                            <button class="btn btn-outline-info enhanced-btn" id="refreshTableBtn">
                                <i class="fa fa-refresh mr-2"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle mr-2"></i>{{ Session::get('message') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped enhanced-table yajra-datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Transaction ID</th>
                                <th width="20%">Purpose</th>
                                <th width="12%">Amount</th>
                                <th width="15%">From Bank</th>
                                <th width="15%">To Bank</th>
                                <th width="10%">Date</th>
                                <th width="8%">Status</th>
                                <th width="8%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple loading indicator -->
<style>
.yajra-datatable.loading {
    opacity: 0.6;
    pointer-events: none;
}
</style>

@endsection

@section('javascript')
<script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script type="text/javascript">
let dataTable;

$(document).ready(function() {
    initializeDataTable();
    loadStatistics();
    
    // Real-time search
    $('#global_search').on('keyup', debounce(function() {
        dataTable.search(this.value).draw();
    }, 500));
    
    // Auto-apply filters on change
    $('#transfer_date_from, #transfer_date_to, #branch_id, #recon_status').on('change', function() {
        dataTable.ajax.reload();
    });
    
    // Button event handlers
    $('#applyFiltersBtn').on('click', function() {
        applyFilters();
    });
    
    $('#clearFiltersBtn').on('click', function() {
        clearFilters();
    });
    
    $('#exportExcelBtn').on('click', function() {
        exportData('excel');
    });
    
    $('#exportPdfBtn').on('click', function() {
        exportData('pdf');
    });
    
    $('#refreshTableBtn').on('click', function() {
        refreshTable();
    });
});

function initializeDataTable() {
    dataTable = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('transferFeeMainListV2.list') }}",
            data: function(d) {
                d.transfer_date_from = $("#transfer_date_from").val();
                d.transfer_date_to = $("#transfer_date_to").val();
                d.branch_id = $("#branch_id").val();
                d.recon_status = $("#recon_status").val();
                d.amount_from = $("#amount_from").val();
                d.amount_to = $("#amount_to").val();
                d.global_search = $("#global_search").val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'transaction_id',
                name: 'transaction_id',
                render: function(data, type, row) {
                    return '<strong class="text-primary">' + data + '</strong>';
                }
            },
            {
                data: 'purpose',
                name: 'purpose',
                render: function(data, type, row) {
                    if (data && data.length > 50) {
                        return '<span title="' + data + '">' + data.substring(0, 50) + '...</span>';
                    }
                    return data || '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'transfer_amount',
                name: 'transfer_amount',
                className: 'text-right',
                render: function(data, type, row) {
                    var amount = parseFloat(data) || 0;
                    return '<strong class="text-success">RM ' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</strong>';
                }
            },
            {
                data: 'transfer_from_bank',
                name: 'transfer_from_bank',
                render: function(data, type, row) {
                    return '<small>' + data + '</small>';
                }
            },
            {
                data: 'transfer_to_bank',
                name: 'transfer_to_bank',
                render: function(data, type, row) {
                    return '<small>' + data + '</small>';
                }
            },
            {
                data: 'transfer_date',
                name: 'transfer_date',
                render: function(data, type, row) {
                    if (!data) return '<small class="text-muted">-</small>';
                    
                    // Handle different date formats
                    var date = new Date(data);
                    if (isNaN(date.getTime())) {
                        // Try parsing MySQL date format (YYYY-MM-DD)
                        var parts = data.split('-');
                        if (parts.length === 3) {
                            date = new Date(parts[0], parts[1] - 1, parts[2]);
                        }
                    }
                    
                    if (isNaN(date.getTime())) {
                        return '<small class="text-muted">' + data + '</small>';
                    }
                    
                    var options = { year: 'numeric', month: 'short', day: 'numeric' };
                    return '<small class="text-muted">' + date.toLocaleDateString('en-US', options) + '</small>';
                }
            },
            {
                data: 'is_recon',
                name: 'is_recon',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data == '1') {
                        return '<span class="badge badge-success"><i class="fa fa-check mr-1"></i>Reconciled</span>';
                    } else {
                        return '<span class="badge badge-warning"><i class="fa fa-clock-o mr-1"></i>Pending</span>';
                    }
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group" role="group">
                            <a href="/transfer-fee/${row.id}" class="btn btn-info btn-sm" title="View Details">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="/transfer-fee/${row.id}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="/transfer-fee/${row.id}" class="btn btn-success btn-sm" title="Download" target="_blank">
                                <i class="fa fa-download"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ],
        order: [[6, 'desc']], // Order by date descending
        pageLength: 25
    });
}

function loadStatistics() {
    // Set default values first
    $('#totalTransfers').text('0');
    $('#totalAmount').text('RM 0');
    $('#pendingRecon').text('0');
    $('#thisMonth').text('0');
    
    $.ajax({
        url: '{{ route("transfer-fee-statistics") }}',
        method: 'GET',
        data: {
            transfer_date_from: $("#transfer_date_from").val(),
            transfer_date_to: $("#transfer_date_to").val(),
            branch_id: $("#branch_id").val()
        },
        success: function(response) {
            $('#totalTransfers').text(response.total_transfers || 0);
            var totalAmount = parseFloat(response.total_amount) || 0;
            $('#totalAmount').text('RM ' + totalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            $('#pendingRecon').text(response.pending_recon || 0);
            $('#thisMonth').text(response.this_month || 0);
        },
        error: function() {
            console.log('Failed to load statistics - using defaults');
        }
    });
}

function applyFilters() {
    dataTable.ajax.reload();
    loadStatistics();
}

function clearFilters() {
    $('#transfer_date_from, #transfer_date_to, #amount_from, #amount_to, #global_search').val('');
    $('#branch_id, #recon_status').val('');
    dataTable.ajax.reload();
    loadStatistics();
}

function refreshTable() {
    dataTable.ajax.reload();
    loadStatistics();
    showSuccessToast('Table refreshed successfully!');
}

function exportData(type) {
    // Simple export functionality - can be enhanced later
    alert('Export feature will be implemented in next version');
}

function showLoading() {
    // Using a simpler loading approach without modal
    $('.yajra-datatable').addClass('loading');
}

function hideLoading() {
    $('.yajra-datatable').removeClass('loading');
}



function showSuccessToast(message) {
    // You can implement a toast notification here
    console.log(message);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endsection 