@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap Modal JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Use jQuery 3.6.0 for modal functionality
    var $jq3 = jQuery.noConflict(true);
    
    // Override the global $ and jQuery with version 3.6.0
    window.$ = $jq3;
    window.jQuery = $jq3;
</script>

@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fa fa-edit"></i> Edit SST
                                @if ($SSTMain->is_recon == '1')
                                    <span class="badge badge-warning ml-2">RECONCILED - READ ONLY</span>
                                @endif
                            </h4>
                            <small class="text-muted">
                                @if ($SSTMain->is_recon == '1')
                                    View SST information (modifications disabled)
                                @else
                                    Invoice-based SST editing
                                @endif
                            </small>
                        </div>
                        <div class="card-body">

                            <form id="sstForm" method="POST"
                                action="{{ url('updateSSTV2/' . $SSTMain->id) }}" onsubmit="prepareFormData()">
                                @csrf
                                <!-- Hidden input for new invoices -->
                                <input type="hidden" name="add_bill" id="add_bill_input" value="">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="payment_date"
                                                value="{{ $SSTMain->payment_date }}" required
                                                {{ $SSTMain->is_recon == '1' ? 'disabled' : '' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transaction ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="trx_id"
                                                value="{{ $SSTMain->transaction_id }}" required
                                                {{ $SSTMain->is_recon == '1' ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transfer Total Amount</label>
                                            <input type="text" class="form-control" name="transfer_total_amount"
                                                id="transferTotalAmount"
                                                value="{{ number_format($SSTMain->amount ?? 0, 2) }}"
                                                readonly
                                                style="background-color: #f8f9fa; font-weight: bold; color: #495057;">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Remark</label>
                                            <textarea class="form-control" name="remark" rows="3"
                                                {{ $SSTMain->is_recon == '1' ? 'disabled' : '' }}>{{ $SSTMain->remark }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h5><i class="fa fa-file-invoice"></i> Manage Invoices for SST Transfer</h5>
                                        <p class="text-muted">Click the button below to add/remove invoices</p>

                                        @if ($SSTMain->is_recon != '1')
                                            <button type="button" class="btn btn-info" onclick="openInvoiceModal()">
                                                <i class="fa fa-search"></i> Manage Invoices
                                            </button>
                                        @endif

                                        <div id="selectedInvoicesSummary" class="mt-3" style="display:block;">
                                            <div class="alert alert-info hide">
                                                <i class="fa fa-info-circle"></i>
                                                <strong><span id="selectedCount">{{ count($SSTDetails) }}</span>
                                                    invoices selected</strong>
                                                <br>
                                                <strong>Total Amount: <span
                                                        id="selectedTotalAmount">{{ number_format(
                                                            $SSTDetails->sum('amount'),
                                                            2,
                                                        ) }}</span></strong>
                                </div>

                                            <!-- Selected Invoices Table -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0"><i class="fa fa-list-check"></i> Current Invoices
                                                        </h6>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                onclick="exportToExcel()" title="Export to Excel">
                                                                <i class="fa fa-file-excel-o"></i> Excel
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="exportToPDF()" title="Export to PDF">
                                                                <i class="fa fa-file-pdf-o"></i> PDF
                                                            </button> 
                                    </div>
                                    </div>
                                    </div>
                                                <div class="card-body">
                                                    <div id="selectedInvoicesTable" style="display:block;">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped"
                                                                style="margin-bottom: 0;">
                                                                <thead class="thead-dark"
                                                                    style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
                                                                    <tr>
                                                                        <th width="30">No</th>
                                                                        <th width="40">Action</th>
                                                                        <th width="120" class="sortable-header"
                                                                            data-sort="case_ref_no"
                                                                            style="cursor: pointer;">
                                                                            Ref No
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-case_ref_no"></span>
                                                                        </th>
                                                                        <th width="120" class="sortable-header"
                                                                            data-sort="client_name"
                                                                            style="cursor: pointer;">
                                                                            Client Name
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-client_name"></span>
                                                                        </th>
                                                                        <th width="100" class="sortable-header"
                                                                            data-sort="invoice_no"
                                                                            style="cursor: pointer;">
                                                                            Invoice No
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-invoice_no"></span>
                                                                        </th>
                                                                        <th width="90" class="sortable-header"
                                                                            data-sort="invoice_date"
                                                                            style="cursor: pointer;">
                                                                            Invoice Date
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-invoice_date"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="total_amount"
                                                                            style="cursor: pointer;">
                                                                            Total amt
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-total_amount"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="pfee1"
                                                                            style="cursor: pointer;">
                                                                            Pfee1
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-pfee1"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="pfee2"
                                                                            style="cursor: pointer;">
                                                                            Pfee2
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-pfee2"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="collected_amount"
                                                                            style="cursor: pointer;">
                                                                            Collected amt
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-collected_amount"></span>
                                                                        </th>
                                                                        <th width="60" class="sortable-header"
                                                                            data-sort="sst" style="cursor: pointer;">
                                                                            SST
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-sst"></span>
                                                                        </th>
                                                                        <th width="90" class="sortable-header"
                                                                            data-sort="payment_date"
                                                                            style="cursor: pointer;">
                                                                            Payment Date
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-payment_date"></span>
                                                                        </th>
                                                </tr>
                                            </thead>
                                                                <tbody id="selectedInvoicesTableBody">
                                                                    @if($SSTDetails && count($SSTDetails) > 0)
                                                                        @foreach ($SSTDetails as $index => $detail)
                                                                            <tr class="selected-invoice-row" data-invoice-id="{{ $detail->loan_case_invoice_main_id ?? 0 }}">
                                                                                <td class="text-center" style="font-size: 11px;">{{ $index + 1 }}</td>
                                                                                <td>
                                                                                    @if ($SSTMain->is_recon != '1')
                                                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                                            onclick="removeCurrentInvoice({{ $detail->id }}, {{ $detail->loan_case_invoice_main_id ?? 0 }})"
                                                                                            title="Remove invoice">
                                                                                            <i class="fa fa-times"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </td>
                                                                                <td style="font-size: 11px;">
                                                                                    @if ($detail->case_ref_no && $detail->case_id)
                                                                                        <a href="/case/{{ $detail->case_id }}" target="_blank" class="text-primary" style="text-decoration: none;">
                                                                                            {{ $detail->case_ref_no }}
                                                                                        </a>
                                                                                    @else
                                                                                        <span style="color: #007bff;">{{ $detail->case_ref_no ?? 'N/A' }}</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td style="font-size: 11px;">{{ $detail->client_name ?? 'N/A' }}</td>
                                                                                <td style="font-size: 11px;">{{ $detail->invoice_no ?? 'N/A' }}</td>
                                                                                <td style="font-size: 11px;">{{ $detail->invoice_date ?? 'N/A' }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->total_amount ?? 0, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->pfee1 ?? 0, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->pfee2 ?? 0, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->collected_amount ?? 0, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->amount, 2) }}</td>
                                                                                <td style="font-size: 11px;">{{ $detail->payment_date ?? 'N/A' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="9" class="text-center text-muted">No invoices found</td>
                                                                        </tr>
                                                                    @endif
                                            </tbody>
                                                                <tfoot class="table-dark" style="position: sticky; bottom: 0; z-index: 1;">
                                                                    <tr>
                                                                        <th colspan="4" class="text-right">
                                                                            <strong>TOTAL:</strong>
                                                                        </th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th class="text-right" id="footerTotalAmt">
                                                                            {{ number_format($SSTDetails->sum('total_amount'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerPfee1">
                                                                            {{ number_format($SSTDetails->sum('pfee1'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerPfee2">
                                                                            {{ number_format($SSTDetails->sum('pfee2'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerCollectedAmt">
                                                                            {{ number_format($SSTDetails->sum('collected_amount'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="selectedTotalSST">
                                                                            {{ number_format($SSTDetails->sum('amount'), 2) }}
                                                                        </th>
                                                                        <th></th>
                                                                    </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            </div>
                                        </div>
                                    </div>
                            </div>

                                <hr>

                                <!-- New Transfer List Section -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5><i class="fa fa-plus-circle"></i> New Transfer List</h5>
                                        <p class="text-muted">Invoices to be added to this SST transfer</p>

                                        <div class="card">
                                            <div class="card-header">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0"><i class="fa fa-list-plus"></i> New Invoices
                                                    </h6>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteAllNewInvoices()" title="Delete All">
                                                            <i class="fa fa-trash"></i> Delete All
                                                        </button>
                            </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                                                        <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
                                                            <tr>
                                                                <th width="30">No</th>
                                                                <th width="40">Action</th>
                                                                <th width="120">Ref No</th>
                                                                <th width="120">Client Name</th>
                                                                <th width="100">Invoice No</th>
                                                                <th width="90">Invoice Date</th>
                                                                <th width="80">Total amt</th>
                                                                <th width="80">Pfee1</th>
                                                                <th width="80">Pfee2</th>
                                                                <th width="80">Collected amt</th>
                                                                <th width="60">SST</th>
                                                                <th width="90">Payment Date</th>
                                    </tr>
                                </thead>
                                                        <tbody id="newTransferTableBody">
                                                            <tr>
                                                                <td colspan="9" class="text-center text-muted">No new invoices selected</td>
                                                            </tr>
                                </tbody>
                                                        <tfoot class="table-dark" style="position: sticky; bottom: 0; z-index: 1;">
                                                            <tr>
                                                                <th colspan="4" class="text-right">
                                                                    <strong>TOTAL:</strong>
                                                                </th>
                                                                <th></th>
                                                                <th></th>
                                                                <th class="text-right" id="newTransferTotalAmt">
                                                                    0.00
                                                                </th>
                                                                <th class="text-right" id="newTransferTotalCollected">
                                                                    0.00
                                                                </th>
                                                                <th class="text-right font-weight-bold" id="newTransferTotalSST">
                                                                    0.00
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </tfoot>
                            </table>
                                                </div>
                                            </div>
                                        </div>
                        </div>
                    </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <a href="{{ route('sst-v2.list') }}" class="btn btn-secondary">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                        @if ($SSTMain->is_recon != '1')
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Update SST
                                            </button>
                                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sortable-header {
            cursor: pointer;
            user-select: none;
            position: relative;
        }
        
        .sortable-header:hover {
            background-color: #f8f9fa;
        }
        
        .sort-icon {
            margin-left: 5px;
            font-weight: bold;
            color: #007bff;
        }
        
        .sortable-header:hover .sort-icon {
            color: #0056b3;
        }
    </style>
    <script>
        // Global variables
        var transfer_fee_add_list = [];
        var transfer_fee_delete_list = [];
        var currentPage = 1;
        var perPage = 20;
        var totalPages = 1;
        var sortColumn = '';
        var sortDirection = 'asc';
        var quickSearchTerm = '';

        $(document).ready(function() {
            // Initialize the page
            updateSelectedInvoicesSummary();
            
            // Load existing SST details into transfer_fee_add_list
            loadExistingSSTDetails();
            
            // Initialize table sorting
            initializeTableSorting();
            
            // Set up event listeners
            $('#perPageSelect').on('change', function() {
                perPage = parseInt($(this).val());
                currentPage = 1;
                searchInvoices();
            });

            // Set up invoice number counting
            $('#searchInvoiceNo').on('input', function() {
                const text = $(this).val();
                const lines = text.split('\n').filter(line => line.trim() !== '');
                const commaSeparated = text.split(',').filter(item => item.trim() !== '');
                const count = Math.max(lines.length, commaSeparated.length);
                
                if (count > 0) {
                    $('#invoiceCountText').text(count);
                    $('#invoiceCount').show();
                } else {
                    $('#invoiceCount').hide();
                }
            });
        });

        // Load existing SST details for editing
        function loadExistingSSTDetails() {
            console.log('Loading existing SST details for ID: {{ $SSTMain->id }}');
            
            // Initialize with existing SST details from the server
            @php
                $existingSSTDetails = \App\Models\SSTDetails::where('sst_main_id', $SSTMain->id)->get();
            @endphp
            
                    transfer_fee_add_list = [];
            
            @foreach($existingSSTDetails as $detail)
                @if($detail->loan_case_invoice_main_id)
                    transfer_fee_add_list.push({
                        id: {{ $detail->loan_case_invoice_main_id }},
                        value: {{ $detail->amount }}
                    });
                @endif
            @endforeach
            
            console.log('Loaded existing SST details:', transfer_fee_add_list);
            
            // Update the summary display
            updateSelectedInvoicesSummary();
        }

        // Modal functions
        function openInvoiceModal() {
            $jq3('#invoiceModal').modal('show');
            // Load invoices immediately when modal opens
            setTimeout(function() {
                searchInvoices();
            }, 500);
        }

        function closeModal() {
            $jq3('#invoiceModal').modal('hide');
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

        function clearInvoiceNumbers() {
            $('#searchInvoiceNo').val('');
            $('#invoiceCount').hide();
        }

        function clearDateFilters() {
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
        }

        function clearSearch() {
            $('#searchInvoiceNo').val('');
            $('#searchCaseRef').val('');
            $('#searchClient').val('');
            $('#searchBillingParty').val('');
            $('#filterBranch').val('');
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            $('#invoiceCount').hide();
            searchInvoices();
        }

        function searchInvoices() {
            console.log('Searching invoices...');
            showLoading();
            
            const searchData = {
                page: currentPage,
                per_page: perPage,
                invoice_no: $('#searchInvoiceNo').val(),
                case_ref: $('#searchCaseRef').val(),
                client_name: $('#searchClient').val(),
                billing_party: $('#searchBillingParty').val(),
                branch: $('#filterBranch').val(),
                start_date: $('#filterStartDate').val(),
                end_date: $('#filterEndDate').val(),
                sort_column: sortColumn,
                sort_direction: sortDirection,
                transfer_list: JSON.stringify(transfer_fee_add_list)
            };

                    $.ajax({
                url: '{{ route("sst-v2.invoice-list") }}',
                method: 'GET',
                data: searchData,
                success: function(response) {
                    console.log('Invoice search response:', response);
                    hideLoading();
                    displayInvoiceList(response);
                },
                error: function(xhr, status, error) {
                    console.error('Invoice search error:', error);
                    console.error('Response:', xhr.responseText);
                    hideLoading();
                    showError('Failed to load invoices. Please try again.');
                }
            });
        }

        function displayInvoiceList(data) {
            console.log('Displaying invoice list:', data);
            
            const container = $('#invoiceListContainer');
            container.html(data.invoiceList || data.html || '');
            
            // Update pagination info
            if (data.pagination) {
                currentPage = data.pagination.current_page;
                totalPages = data.pagination.last_page;
                updatePagination();
            } else if (data.currentPage) {
                currentPage = data.currentPage;
                totalPages = data.totalPages;
                updatePagination();
            }
            
            // Update modal selection summary
            updateModalSelectionSummary();
        }

        function showLoading() {
            $('#invoiceLoading').show();
            $('#invoiceListContainer').hide();
        }

        function hideLoading() {
            $('#invoiceLoading').hide();
            $('#invoiceListContainer').show();
        }

        function showError(message) {
            $('#invoiceListContainer').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> ${message}
                </div>
            `);
        }

        function updatePagination() {
            // Update pagination controls if they exist
            // This would be implemented based on the pagination structure
        }

        function updateModalSelectionSummary() {
            const selectedCount = transfer_fee_add_list.length;
            const totalAmount = transfer_fee_add_list.reduce((sum, item) => sum + parseFloat(item.value || 0), 0);
            
            $('#modalSelectedCount').text(selectedCount);
            $('#modalTotalAmount').text(totalAmount.toFixed(2));
        }

        function updateSelectedInvoicesSummary() {
            const selectedCount = transfer_fee_add_list.length;
            const totalAmount = transfer_fee_add_list.reduce((sum, item) => sum + parseFloat(item.value || 0), 0);
            
            $('#selectedCount').text(selectedCount);
            $('#selectedTotalAmount').text(totalAmount.toFixed(2));
            
            if (selectedCount > 0) {
                $('#selectedInvoicesSummary .alert').removeClass('hide');
                            } else {
                $('#selectedInvoicesSummary .alert').addClass('hide');
            }
        }

        function reloadAddTable() {
            // Reload the selected invoices table
            const tbody = $('#selectedInvoicesTableBody');
            tbody.empty();
            
            transfer_fee_add_list.forEach((item, index) => {
                // This would populate the table with selected invoices
                // Implementation depends on the data structure
            });
        }

        function reloadTable() {
            // Reload the main table if needed
            updateSelectedInvoicesSummary();
        }

        function removeSelectedInvoice(invoiceId) {
            const index = transfer_fee_add_list.findIndex(item => item.id == invoiceId);
            if (index > -1) {
                transfer_fee_add_list.splice(index, 1);
                reloadAddTable();
                reloadTable();
            }
        }

        function confirmInvoiceSelection() {
            // Add selected invoices to new transfer list
            addSelectedInvoicesToNewTransferList();
            updateSelectedInvoicesSummary();
            closeModal();
        }

        function addSelectedInvoicesToNewTransferList() {
            // Get selected invoices from modal checkboxes
            const selectedInvoices = [];
            $('#invoiceListContainer input[type="checkbox"]:checked').each(function() {
                const row = $(this).closest('tr');
                const invoiceId = $(this).val();
                const invoiceNo = row.find('td:nth-child(4)').text().trim();
                const caseRef = row.find('td:nth-child(3)').text().trim();
                const invoiceDate = row.find('td:nth-child(5)').text().trim();
                const totalAmt = row.find('td:nth-child(6)').text().trim();
                const collectedAmt = row.find('td:nth-child(7)').text().trim();
                const sstAmt = row.find('td:nth-child(8)').text().trim();
                const paymentDate = row.find('td:nth-child(9)').text().trim();
                
                selectedInvoices.push({
                    id: invoiceId,
                    invoice_no: invoiceNo,
                    case_ref: caseRef,
                    invoice_date: invoiceDate,
                    total_amt: totalAmt,
                    collected_amt: collectedAmt,
                    sst_amt: sstAmt,
                    payment_date: paymentDate
                });
            });
            
            console.log('Selected invoices:', selectedInvoices);
            
            // Add to new transfer list
            if (selectedInvoices.length > 0) {
                populateNewTransferList(selectedInvoices);
            }
        }
        
        function populateNewTransferList(invoices) {
            const tbody = $('#newTransferTableBody');
            let html = '';
            let totalSST = 0;
            let totalAmt = 0;
            let totalCollected = 0;
            
            invoices.forEach((invoice, index) => {
                const sstAmount = parseFloat(invoice.sst_amt.replace(/,/g, '')) || 0;
                const totalAmount = parseFloat(invoice.total_amt.replace(/,/g, '')) || 0;
                const collectedAmount = parseFloat(invoice.collected_amt.replace(/,/g, '')) || 0;
                
                totalSST += sstAmount;
                totalAmt += totalAmount;
                totalCollected += collectedAmount;
                
                html += `
                    <tr class="new-invoice-row" data-invoice-id="${invoice.id}">
                        <td class="text-center" style="font-size: 11px;">${index + 1}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" 
                                onclick="removeNewInvoice(${invoice.id})" title="Remove invoice">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                        <td style="font-size: 11px;">
                            ${invoice.case_id ? 
                                `<a href="/case/${invoice.case_id}" target="_blank" class="text-primary" style="text-decoration: none;">${invoice.case_ref}</a>` : 
                                `<span style="color: #007bff;">${invoice.case_ref}</span>`
                            }
                        </td>
                        <td style="font-size: 11px;">${invoice.client_name || 'N/A'}</td>
                        <td style="font-size: 11px;">${invoice.invoice_no}</td>
                        <td style="font-size: 11px;">${invoice.invoice_date}</td>
                        <td class="text-right" style="font-size: 11px;">${invoice.total_amt}</td>
                        <td class="text-right" style="font-size: 11px;">${invoice.pfee1 || '0.00'}</td>
                        <td class="text-right" style="font-size: 11px;">${invoice.pfee2 || '0.00'}</td>
                        <td class="text-right" style="font-size: 11px;">${invoice.collected_amt}</td>
                        <td class="text-right sst-amount" style="font-size: 11px;">${invoice.sst_amt}</td>
                        <td style="font-size: 11px;">${invoice.payment_date}</td>
                    </tr>
                `;
            });
            
            tbody.html(html);
            $('#newTransferTotalSST').text(totalSST.toFixed(2));
            $('#newTransferTotalAmt').text(totalAmt.toFixed(2));
            $('#newTransferTotalCollected').text(totalCollected.toFixed(2));
        }
        
        function removeNewInvoice(invoiceId) {
            $(`tr[data-invoice-id="${invoiceId}"]`).remove();
            updateNewTransferTotal();
        }
        
        function updateNewTransferTotal() {
            let totalSST = 0;
            let totalAmt = 0;
            let totalCollected = 0;
            
            $('#newTransferTableBody tr').each(function() {
                const totalCell = $(this).find('td:nth-child(6)');
                const collectedCell = $(this).find('td:nth-child(7)');
                const sstCell = $(this).find('td:nth-child(8)');
                
                if (totalCell.length) {
                    const totalValue = parseFloat(totalCell.text().replace(/,/g, '')) || 0;
                    const collectedValue = parseFloat(collectedCell.text().replace(/,/g, '')) || 0;
                    const sstValue = parseFloat(sstCell.text().replace(/,/g, '')) || 0;
                    
                    totalAmt += totalValue;
                    totalCollected += collectedValue;
                    totalSST += sstValue;
                }
            });
            
            $('#newTransferTotalSST').text(totalSST.toFixed(2));
            $('#newTransferTotalAmt').text(totalAmt.toFixed(2));
            $('#newTransferTotalCollected').text(totalCollected.toFixed(2));
            
            // If no invoices left, show empty message
            if ($('#newTransferTableBody tr').length === 0) {
                $('#newTransferTableBody').html('<tr><td colspan="9" class="text-center text-muted">No new invoices selected</td></tr>');
            }
        }

        function deleteAllNewInvoices() {
            if (confirm('Are you sure you want to delete all new invoices?')) {
                $('#newTransferTableBody').html('<tr><td colspan="9" class="text-center text-muted">No new invoices selected</td></tr>');
                $('#newTransferTotalSST').text('0.00');
                $('#newTransferTotalAmt').text('0.00');
                $('#newTransferTotalCollected').text('0.00');
            }
        }

        // Remove current invoice with SweetAlert confirmation
        function removeCurrentInvoice(sstDetailId, invoiceMainId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! This will remove the invoice from SST transfer and update the invoice record.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we remove the invoice.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX request to delete
            $.ajax({
                        url: '{{ url("deleteSSTDetail") }}',
                type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            sst_detail_id: sstDetailId,
                            invoice_main_id: invoiceMainId
                        },
                        success: function(response) {
                            if (response.status === 1) {
                                // Remove the row from table
                                $(`tr[data-invoice-id="${invoiceMainId}"]`).remove();
                                
                                // Update totals
                                updateCurrentInvoiceTotals();
                                
                                // Update SST main total amount
                                if (response.new_total !== undefined) {
                                    $('#transferTotalAmount').val(parseFloat(response.new_total).toFixed(2));
                                }
                                
                                // Refresh modal invoice list to show the deleted invoice as available again
                                if ($('#invoiceModal').hasClass('show')) {
                                    searchInvoices();
                                }
                                
                                // Show success message
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The invoice has been removed from SST transfer and is now available for selection.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                    } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to delete the invoice.',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while deleting the invoice.',
                                icon: 'error'
                            });
                }
            });
        }
            });
        }

        // Update current invoice totals
        function updateCurrentInvoiceTotals() {
            let totalSST = 0;
            let totalAmt = 0;
            let totalCollected = 0;
            
            $('.selected-invoice-row').each(function() {
                const totalCell = $(this).find('td:nth-child(6)');
                const collectedCell = $(this).find('td:nth-child(7)');
                const sstCell = $(this).find('td:nth-child(8)');
                
                if (totalCell.length) {
                    const totalValue = parseFloat(totalCell.text().replace(/,/g, '')) || 0;
                    const collectedValue = parseFloat(collectedCell.text().replace(/,/g, '')) || 0;
                    const sstValue = parseFloat(sstCell.text().replace(/,/g, '')) || 0;
                    
                    totalAmt += totalValue;
                    totalCollected += collectedValue;
                    totalSST += sstValue;
                }
            });
            
            $('#selectedTotalSST').text(totalSST.toFixed(2));
            $('#footerTotalAmt').text(totalAmt.toFixed(2));
            $('#footerCollectedAmt').text(totalCollected.toFixed(2));
            
            // Update row numbers
            $('.selected-invoice-row').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }


        function exportToExcel() {
            // Show loading
            Swal.fire({
                title: 'Preparing Excel Export...',
                text: 'Please wait while we generate the file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Try the simple export method first (bypasses middleware)
            window.location.href = '{{ url("export-sst-excel/" . $SSTMain->id) }}';
            
            // Show success notification after a short delay
            setTimeout(function() {
                Swal.close();
                const notification = $(`
                    <div class="alert alert-success alert-dismissible fade show position-fixed" 
                         style="top: 20px; right: 20px; z-index: 9999;">
                        <i class="fa fa-check-circle"></i>
                        <strong>Success!</strong> Excel file download started
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);

                $('body').append(notification);

                // Auto-remove after 3 seconds
                setTimeout(function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            }, 1000);
        }

        function exportToExcelDirect() {
            // Show loading
            Swal.fire({
                title: 'Preparing Excel Export (Alternative Method)...',
                text: 'Please wait while we generate the file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request to alternative export endpoint
            $.ajax({
                url: '{{ url("exportSSTV2ExcelDirect/" . $SSTMain->id) }}',
                type: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Cache-Control', 'no-cache');
                    xhr.setRequestHeader('Pragma', 'no-cache');
                },
                success: function(response, status, xhr) {
                    // Close the loading modal first
                    Swal.close();
                    
                    // For direct download, we expect a file download response
                    // The browser should automatically handle the download
                    console.log('Alternative export method successful');
                    
                    // Show success notification
                    const notification = $(`
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999;">
                            <i class="fa fa-check-circle"></i>
                            <strong>Success!</strong> Excel file downloaded successfully (Alternative Method)
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);

                    $('body').append(notification);

                    // Auto-remove after 3 seconds
                    setTimeout(function() {
                        notification.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.error('Alternative export error:', error, xhr.responseText);
                    
                    let errorMessage = 'Failed to export to Excel using alternative method.';
                    if (xhr.responseText) {
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            if (errorData.error) {
                                errorMessage = errorData.error;
                            }
                        } catch (e) {
                            errorMessage = 'Server error: ' + xhr.responseText;
                        }
                    }
                    
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        }

        function exportToPDF() {
            // Show loading
            Swal.fire({
                title: 'Preparing PDF Export...',
                text: 'Please wait while we generate the file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request to export endpoint
            $.ajax({
                url: '{{ url("exportSSTV2PDF/" . $SSTMain->id) }}',
                type: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, xhr) {
                    // Close the loading modal first
                    Swal.close();
                    
                    // Create download link for PDF
                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `SST_{{ $SSTMain->id }}_${new Date().toISOString().split('T')[0]}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    // Show success notification
                    const notification = $(`
                        <div class="alert alert-success alert-dismissible fade show position-fixed" 
                             style="top: 20px; right: 20px; z-index: 9999;">
                            <i class="fa fa-check-circle"></i>
                            <strong>Success!</strong> PDF file downloaded successfully
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);

                    $('body').append(notification);

                    // Auto-remove after 3 seconds
                    setTimeout(function() {
                        notification.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.error('Export error:', error);
                    Swal.fire('Error', 'Failed to export to PDF. Please try again.', 'error');
                }
            });
        }

        function quickSearch() {
            const term = $('#quickSearch').val().toLowerCase();
            quickSearchTerm = term;
            
            $('#invoiceListContainer table tbody tr').each(function() {
                const row = $(this);
                const text = row.text().toLowerCase();
                
                if (text.includes(term)) {
                    row.show();
                } else {
                    row.hide();
                }
            });
            
            // Update quick search count
            const visibleRows = $('#invoiceListContainer table tbody tr:visible').length;
            const totalRows = $('#invoiceListContainer table tbody tr').length;
            
            if (term) {
                $('#quickSearchResultCount').text(visibleRows);
                $('#quickSearchTotalCount').text(totalRows);
                $('#quickSearchCount').show();
            } else {
                $('#quickSearchCount').hide();
            }
        }

        function clearQuickSearch() {
            $('#quickSearch').val('');
            quickSearch();
        }

        // Prepare form data before submission
        function prepareFormData() {
            // Collect data from New Transfer List
            var newInvoices = [];
            var rowCount = $('#newTransferTableBody tr').length;
            console.log('Found ' + rowCount + ' rows in New Transfer List');
            
            $('#newTransferTableBody tr').each(function(index) {
                var row = $(this);
                var invoiceId = row.data('invoice-id');
                var sstAmount = parseFloat(row.find('.sst-amount').text().replace(/,/g, ''));
                
                console.log('Row ' + index + ' - Invoice ID:', invoiceId, 'SST Amount:', sstAmount);
                
                if (invoiceId && sstAmount > 0) {
                    newInvoices.push({
                        id: invoiceId,
                        value: sstAmount
                    });
                }
            });
            
            // Set the hidden input value
            $('#add_bill_input').val(JSON.stringify(newInvoices));
            
            console.log('Preparing form data:', newInvoices);
            console.log('Hidden input value:', $('#add_bill_input').val());
            return true; // Allow form submission
        }

        // Table sorting functionality
        function initializeTableSorting() {
            $('.sortable-header').on('click', function() {
                const sortField = $(this).data('sort');
                const currentDirection = $(this).data('direction') || 'asc';
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                
                // Update sort direction
                $(this).data('direction', newDirection);
                
                // Clear all sort icons
                $('.sort-icon').html('');
                
                // Update current sort icon
                const sortIcon = $(this).find('.sort-icon');
                if (newDirection === 'asc') {
                    sortIcon.html('↑');
                } else {
                    sortIcon.html('↓');
                }
                
                // Sort the table
                sortTable(sortField, newDirection);
            });
        }

        function sortTable(sortField, direction) {
            const tbody = $('#selectedInvoicesTableBody');
            const rows = tbody.find('tr').toArray();
            
            rows.sort(function(a, b) {
                let aValue, bValue;
                
                // Get the appropriate cell value based on sort field
                switch(sortField) {
                    case 'case_ref_no':
                        aValue = $(a).find('td:nth-child(3)').text().trim();
                        bValue = $(b).find('td:nth-child(3)').text().trim();
                        break;
                    case 'client_name':
                        aValue = $(a).find('td:nth-child(4)').text().trim();
                        bValue = $(b).find('td:nth-child(4)').text().trim();
                        break;
                    case 'invoice_no':
                        aValue = $(a).find('td:nth-child(5)').text().trim();
                        bValue = $(b).find('td:nth-child(5)').text().trim();
                        break;
                    case 'invoice_date':
                        aValue = $(a).find('td:nth-child(6)').text().trim();
                        bValue = $(b).find('td:nth-child(6)').text().trim();
                        break;
                    case 'total_amount':
                        aValue = parseFloat($(a).find('td:nth-child(7)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(7)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'collected_amount':
                        aValue = parseFloat($(a).find('td:nth-child(8)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(8)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'sst':
                        aValue = parseFloat($(a).find('td:nth-child(9)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(9)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'payment_date':
                        aValue = $(a).find('td:nth-child(10)').text().trim();
                        bValue = $(b).find('td:nth-child(10)').text().trim();
                        break;
                    default:
                        return 0;
                }
                
                // Handle date sorting
                if (sortField === 'invoice_date' || sortField === 'payment_date') {
                    aValue = new Date(aValue);
                    bValue = new Date(bValue);
                }
                
                // Compare values
                if (aValue < bValue) {
                    return direction === 'asc' ? -1 : 1;
                } else if (aValue > bValue) {
                    return direction === 'asc' ? 1 : -1;
                } else {
                    return 0;
                }
            });
            
            // Re-append sorted rows
            tbody.empty();
            rows.forEach(function(row) {
                tbody.append(row);
            });
            
            // Update row numbers after sorting
            updateRowNumbers();
        }

        function updateRowNumbers() {
            $('#selectedInvoicesTableBody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }
    </script>
@endsection
