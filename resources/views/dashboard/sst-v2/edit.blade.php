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
                                                value="@php
                                                    $totalSst = $SSTDetails->sum('amount') ?? 0;
                                                    $totalReimbSst = $SSTDetails->sum('reimbursement_sst') ?? 0;
                                                    $grandTotalSst = $totalSst + $totalReimbSst;
                                                    echo number_format($grandTotalSst, 2);
                                                @endphp"
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
                                                        id="selectedTotalAmount">@php
                                                            $totalSst = $SSTDetails->sum('amount') ?? 0;
                                                            $totalReimbSst = $SSTDetails->sum('reimbursement_sst') ?? 0;
                                                            $grandTotalSst = $totalSst + $totalReimbSst;
                                                            echo number_format($grandTotalSst, 2);
                                                        @endphp</span></strong>
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
                                                    <!-- Search Box for Current Invoices -->
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            <i class="fa fa-search"></i>
                                                                        </span>
                                                                    </div>
                                                                    <input type="text" id="currentInvoicesSearch" class="form-control" 
                                                                        placeholder="Search by Invoice No, Case Ref, Client Name..." 
                                                                        onkeyup="filterCurrentInvoices()">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-secondary" onclick="clearCurrentInvoicesSearch()" title="Clear search">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted">Type to filter table rows instantly</small>
                                                            </div>
                                                            <div class="col-md-6 text-right">
                                                                <span class="badge badge-info" id="currentInvoicesCount" style="display:none;">
                                                                    <span id="currentInvoicesVisibleCount">0</span> of <span id="currentInvoicesTotalCount">0</span> invoices
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                                            data-sort="total_pfee"
                                                                            style="cursor: pointer;">
                                                                            P1+P2 (excl SST)
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-total_pfee"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="reimbursement_amount"
                                                                            style="cursor: pointer;">
                                                                            Reimbursement (excl SST)
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-reimbursement_amount"></span>
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
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="reimb_sst" style="cursor: pointer;">
                                                                            Reimb SST
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-reimb_sst"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="total_sst" style="cursor: pointer;">
                                                                            Total SST
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-total_sst"></span>
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
                                                                                @php
                                                                                    $totalPfee = ($detail->pfee1 ?? 0) + ($detail->pfee2 ?? 0);
                                                                                    $reimbursementAmount = $detail->reimbursement_amount ?? 0;
                                                                                @endphp
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($totalPfee, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($reimbursementAmount, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->collected_amount ?? 0, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($detail->amount, 2) }}</td>
                                                                                @php
                                                                                    $reimbSst = $detail->reimbursement_sst ?? 0;
                                                                                    $totalSstRow = ($detail->amount ?? 0) + $reimbSst;
                                                                                @endphp
                                                                                <td class="text-right" style="font-size: 11px;">{{ number_format($reimbSst, 2) }}</td>
                                                                                <td class="text-right" style="font-size: 11px; font-weight: bold;">{{ number_format($totalSstRow, 2) }}</td>
                                                                                <td style="font-size: 11px;">{{ $detail->payment_date ?? 'N/A' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="13" class="text-center text-muted">No invoices found</td>
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
                                                                        @php
                                                                            $totalPfeeSum = $SSTDetails->sum(function($detail) {
                                                                                return ($detail->pfee1 ?? 0) + ($detail->pfee2 ?? 0);
                                                                            });
                                                                            $totalReimbursementSum = $SSTDetails->sum('reimbursement_amount') ?? 0;
                                                                        @endphp
                                                                        <th class="text-right" id="footerTotalPfee">
                                                                            {{ number_format($totalPfeeSum, 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerReimbursement">
                                                                            {{ number_format($totalReimbursementSum, 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerCollectedAmt">
                                                                            {{ number_format($SSTDetails->sum('collected_amount'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="selectedTotalSST">
                                                                            {{ number_format($SSTDetails->sum('amount'), 2) }}
                                                                        </th>
                                                                        @php
                                                                            $totalReimbSst = $SSTDetails->sum('reimbursement_sst') ?? 0;
                                                                            $grandTotalSst = $SSTDetails->sum('amount') + $totalReimbSst;
                                                                        @endphp
                                                                        <th class="text-right">{{ number_format($totalReimbSst, 2) }}</th>
                                                                        <th class="text-right" style="font-weight: bold; font-size: 1.1em;">{{ number_format($grandTotalSst, 2) }}</th>
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
                                                                <th width="80">Reimb SST</th>
                                                                <th width="80">Total SST</th>
                                                                <th width="90">Payment Date</th>
                                    </tr>
                                </thead>
                                                        <tbody id="newTransferTableBody">
                                                            <tr>
                                                                <td colspan="14" class="text-center text-muted">No new invoices selected</td>
                                                            </tr>
                                </tbody>
                                                        <tfoot class="table-info" style="position: sticky; bottom: 0; z-index: 1; font-weight: bold; background-color: #d1ecf1;">
                                                            <tr>
                                                                <th colspan="6" class="text-right">
                                                                    <strong>TOTAL:</strong>
                                                                </th>
                                                                <th class="text-right" id="newTransferTotalAmt">0.00</th>
                                                                <th class="text-right" id="newTransferTotalPfee1">0.00</th>
                                                                <th class="text-right" id="newTransferTotalPfee2">0.00</th>
                                                                <th class="text-right" id="newTransferTotalCollected">0.00</th>
                                                                <th class="text-right" id="newTransferTotalSST">0.00</th>
                                                                <th class="text-right" id="newTransferTotalReimbSST">0.00</th>
                                                                <th class="text-right font-weight-bold" style="font-size: 1.1em;" id="newTransferGrandTotalSST">0.00</th>
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
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        .sort-icon {
            margin-left: 5px;
            font-weight: bold;
            color: #6c757d;
            font-size: 0.9em;
        }
        
        .sortable-header:hover .sort-icon {
            color: #fff;
        }
        
        .sortable-header .sort-icon:not(:empty) {
            color: #007bff;
        }
        
        .thead-dark .sortable-header .sort-icon:not(:empty) {
            color: #80bdff;
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
            initializeInvoiceTableSorting();
            
            // Initialize Current Invoices table sorting
            initializeCurrentInvoicesSorting();
            
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
                search_invoice_no: $('#searchInvoiceNo').val(),
                search_case_ref: $('#searchCaseRef').val(),
                search_client: $('#searchClient').val(),
                search_billing_party: $('#searchBillingParty').val(),
                filter_branch: $('#filterBranch').val(),
                filter_start_date: $('#filterStartDate').val(),
                filter_end_date: $('#filterEndDate').val(),
                sort_field: sortColumn,
                sort_order: sortDirection,
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
            
            // Re-initialize table sorting after loading new data
            initializeInvoiceTableSorting();
            
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
            // Pagination is handled server-side and rendered in the table template
            // No additional client-side update needed
        }
        
        function loadInvoicePage(page) {
            currentPage = page;
            searchInvoices();
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
            $('.invoice-checkbox:checked').each(function() {
                const invoiceId = $(this).val();
                const billId = $(this).data('bill-id');
                const sstAmount = parseFloat($(this).data('sst')) || 0;
                const reimbSstAmount = parseFloat($(this).data('reimb-sst')) || 0;
                const totalSstAmount = parseFloat($(this).data('total-sst')) || 0;
                const totalAmt = parseFloat($(this).data('total-amt')) || 0;
                const collectedAmt = parseFloat($(this).data('collected-amt')) || 0;
                const pfee1 = parseFloat($(this).data('pfee1')) || 0;
                const pfee2 = parseFloat($(this).data('pfee2')) || 0;
                
                // Get invoice details from the table row
                // Column indices: 0=No, 1=Action, 2=Ref No, 3=Client Name, 4=Invoice No, 5=Invoice Date, 6=Total amt, 7=Collected amt, 8=SST, 9=Reimb SST, 10=Total SST, 11=Payment Date
                const row = $(this).closest('tr');
                const caseRef = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() || 'N/A';
                const clientName = row.find('td:eq(3)').text() || 'N/A';
                const invoiceNo = row.find('td:eq(4)').text() || 'N/A';
                const invoiceDate = row.find('td:eq(5)').text() || 'N/A';
                const paymentDate = row.find('td:eq(11)').text() || 'N/A';
                const caseId = row.find('td:eq(2) a').attr('href') ? row.find('td:eq(2) a').attr('href').split('/').pop() : null;
                
                selectedInvoices.push({
                    id: invoiceId,
                    bill_id: billId,
                    invoice_no: invoiceNo,
                    case_ref: caseRef,
                    client_name: clientName,
                    invoice_date: invoiceDate,
                    payment_date: paymentDate,
                    case_id: caseId,
                    total_amt: totalAmt.toFixed(2),
                    collected_amt: collectedAmt.toFixed(2),
                    sst_amt: sstAmount.toFixed(2),
                    reimb_sst_amt: reimbSstAmount.toFixed(2),
                    total_sst_amt: totalSstAmount.toFixed(2),
                    pfee1: pfee1.toFixed(2),
                    pfee2: pfee2.toFixed(2)
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
            let totalReimbSST = 0;
            let grandTotalSST = 0;
            let totalAmt = 0;
            let totalPfee1 = 0;
            let totalPfee2 = 0;
            let totalCollected = 0;
            
            invoices.forEach((invoice, index) => {
                const sstAmount = parseFloat(invoice.sst_amt) || 0;
                const reimbSstAmount = parseFloat(invoice.reimb_sst_amt) || 0;
                const totalSstAmount = parseFloat(invoice.total_sst_amt) || 0;
                const totalAmount = parseFloat(invoice.total_amt) || 0;
                const collectedAmount = parseFloat(invoice.collected_amt) || 0;
                const pfee1 = parseFloat(invoice.pfee1) || 0;
                const pfee2 = parseFloat(invoice.pfee2) || 0;
                
                totalSST += sstAmount;
                totalReimbSST += reimbSstAmount;
                totalAmt += totalAmount;
                totalPfee1 += pfee1;
                totalPfee2 += pfee2;
                totalCollected += collectedAmount;
                
                // Calculate row total SST as SST + Reimb SST
                const rowTotalSst = sstAmount + reimbSstAmount;
                
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
                        <td class="text-right" style="font-size: 11px;">${totalAmount.toFixed(2)}</td>
                        <td class="text-right" style="font-size: 11px;">${pfee1.toFixed(2)}</td>
                        <td class="text-right" style="font-size: 11px;">${pfee2.toFixed(2)}</td>
                        <td class="text-right" style="font-size: 11px;">${collectedAmount.toFixed(2)}</td>
                        <td class="text-right sst-amount" style="font-size: 11px;">${sstAmount.toFixed(2)}</td>
                        <td class="text-right" style="font-size: 11px;">${reimbSstAmount.toFixed(2)}</td>
                        <td class="text-right" style="font-size: 11px; font-weight: bold;">${rowTotalSst.toFixed(2)}</td>
                        <td style="font-size: 11px;">${invoice.payment_date}</td>
                    </tr>
                `;
            });
            
            // Calculate grand total as sum of SST + Reimb SST
            grandTotalSST = totalSST + totalReimbSST;
            
            tbody.html(html);
            updateNewTransferTotals(totalSST, totalReimbSST, grandTotalSST, totalAmt, totalPfee1, totalPfee2, totalCollected);
        }
        
        function updateNewTransferTotals(totalSST, totalReimbSST, grandTotalSST, totalAmt, totalPfee1, totalPfee2, totalCollected) {
            $('#newTransferTotalSST').text(totalSST.toFixed(2));
            $('#newTransferTotalReimbSST').text(totalReimbSST.toFixed(2));
            $('#newTransferGrandTotalSST').text(grandTotalSST.toFixed(2));
            $('#newTransferTotalAmt').text(totalAmt.toFixed(2));
            $('#newTransferTotalPfee1').text(totalPfee1.toFixed(2));
            $('#newTransferTotalPfee2').text(totalPfee2.toFixed(2));
            $('#newTransferTotalCollected').text(totalCollected.toFixed(2));
            
            // Update the overall transfer total amount
            updateTransferTotalAmount();
        }
        
        // Function to update Transfer Total Amount by combining Current Invoices and New Transfer List
        function updateTransferTotalAmount() {
            let currentInvoicesTotal = 0;
            let newTransferListTotal = 0;
            
            // Calculate total from Current Invoices
            $('.selected-invoice-row').each(function() {
                const sstCell = $(this).find('td:nth-child(11)');
                const reimbSstCell = $(this).find('td:nth-child(12)');
                
                if (sstCell.length && reimbSstCell.length) {
                    const sstValue = parseFloat(sstCell.text().replace(/,/g, '')) || 0;
                    const reimbSstValue = parseFloat(reimbSstCell.text().replace(/,/g, '')) || 0;
                    currentInvoicesTotal += sstValue + reimbSstValue;
                }
            });
            
            // Calculate total from New Transfer List
            $('#newTransferTableBody tr.new-invoice-row').each(function() {
                const sstCell = $(this).find('td:nth-child(11)');
                const reimbSstCell = $(this).find('td:nth-child(12)');
                
                if (sstCell.length && reimbSstCell.length) {
                    const sstValue = parseFloat(sstCell.text().replace(/,/g, '')) || 0;
                    const reimbSstValue = parseFloat(reimbSstCell.text().replace(/,/g, '')) || 0;
                    newTransferListTotal += sstValue + reimbSstValue;
                }
            });
            
            // Update the Transfer Total Amount field
            const combinedTotal = currentInvoicesTotal + newTransferListTotal;
            $('#transferTotalAmount').val(combinedTotal.toFixed(2));
        }
        
        function removeNewInvoice(invoiceId) {
            $(`tr[data-invoice-id="${invoiceId}"]`).remove();
            updateNewTransferTotal();
        }
        
        function updateNewTransferTotal() {
            let totalSST = 0;
            let totalReimbSST = 0;
            let grandTotalSST = 0;
            let totalAmt = 0;
            let totalPfee1 = 0;
            let totalPfee2 = 0;
            let totalCollected = 0;
            
            $('#newTransferTableBody tr').each(function() {
                // Column indices: 0=No, 1=Action, 2=Ref No, 3=Client Name, 4=Invoice No, 5=Invoice Date, 6=Total amt, 7=Pfee1, 8=Pfee2, 9=Collected amt, 10=SST, 11=Reimb SST, 12=Total SST, 13=Payment Date
                const totalCell = $(this).find('td:nth-child(7)');
                const pfee1Cell = $(this).find('td:nth-child(8)');
                const pfee2Cell = $(this).find('td:nth-child(9)');
                const collectedCell = $(this).find('td:nth-child(10)');
                const sstCell = $(this).find('td:nth-child(11)');
                const reimbSstCell = $(this).find('td:nth-child(12)');
                const totalSstCell = $(this).find('td:nth-child(13)');
                
                if (totalCell.length) {
                    const totalValue = parseFloat(totalCell.text().replace(/,/g, '')) || 0;
                    const pfee1Value = parseFloat(pfee1Cell.text().replace(/,/g, '')) || 0;
                    const pfee2Value = parseFloat(pfee2Cell.text().replace(/,/g, '')) || 0;
                    const collectedValue = parseFloat(collectedCell.text().replace(/,/g, '')) || 0;
                    const sstValue = parseFloat(sstCell.text().replace(/,/g, '')) || 0;
                    const reimbSstValue = parseFloat(reimbSstCell.text().replace(/,/g, '')) || 0;
                    const totalSstValue = parseFloat(totalSstCell.text().replace(/,/g, '')) || 0;
                    
                    totalAmt += totalValue;
                    totalPfee1 += pfee1Value;
                    totalPfee2 += pfee2Value;
                    totalCollected += collectedValue;
                    totalSST += sstValue;
                    totalReimbSST += reimbSstValue;
                }
            });
            
            // Calculate grand total as sum of SST + Reimb SST
            grandTotalSST = totalSST + totalReimbSST;
            
            updateNewTransferTotals(totalSST, totalReimbSST, grandTotalSST, totalAmt, totalPfee1, totalPfee2, totalCollected);
            
            // Update the overall transfer total amount
            updateTransferTotalAmount();
            
            // If no invoices left, show empty message
            if ($('#newTransferTableBody tr').length === 0) {
                $('#newTransferTableBody').html('<tr><td colspan="14" class="text-center text-muted">No new invoices selected</td></tr>');
            }
        }

        function deleteAllNewInvoices() {
            if (confirm('Are you sure you want to delete all new invoices?')) {
                $('#newTransferTableBody').html('<tr><td colspan="14" class="text-center text-muted">No new invoices selected</td></tr>');
                updateNewTransferTotals(0, 0, 0, 0, 0, 0, 0);
                // updateTransferTotalAmount() is already called by updateNewTransferTotals()
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
                                
                                // Update SST main total amount (includes both current and new invoices)
                                // The updateCurrentInvoiceTotals() already calls updateTransferTotalAmount()
                                // but if server provides a new_total, we still need to recalculate to include new transfer list
                                updateTransferTotalAmount();
                                
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
            let totalReimbSST = 0;
            let grandTotalSST = 0;
            let totalAmt = 0;
            let totalPfeeSum = 0;
            let totalReimbursementSum = 0;
            let totalCollected = 0;
            
            $('.selected-invoice-row').each(function() {
                // Column indices: 0=No, 1=Action, 2=Ref No, 3=Client Name, 4=Invoice No, 5=Invoice Date, 6=Total amt, 7=P1+P2, 8=Reimbursement, 9=Collected amt, 10=SST, 11=Reimb SST, 12=Total SST, 13=Payment Date
                const totalCell = $(this).find('td:nth-child(7)');
                const totalPfeeCell = $(this).find('td:nth-child(8)');
                const reimbursementCell = $(this).find('td:nth-child(9)');
                const collectedCell = $(this).find('td:nth-child(10)');
                const sstCell = $(this).find('td:nth-child(11)');
                const reimbSstCell = $(this).find('td:nth-child(12)');
                const totalSstCell = $(this).find('td:nth-child(13)');
                
                if (totalCell.length) {
                    const totalValue = parseFloat(totalCell.text().replace(/,/g, '')) || 0;
                    const totalPfeeValue = parseFloat(totalPfeeCell.text().replace(/,/g, '')) || 0;
                    const reimbursementValue = parseFloat(reimbursementCell.text().replace(/,/g, '')) || 0;
                    const collectedValue = parseFloat(collectedCell.text().replace(/,/g, '')) || 0;
                    const sstValue = parseFloat(sstCell.text().replace(/,/g, '')) || 0;
                    const reimbSstValue = parseFloat(reimbSstCell.text().replace(/,/g, '')) || 0;
                    const totalSstValue = parseFloat(totalSstCell.text().replace(/,/g, '')) || 0;
                    
                    totalAmt += totalValue;
                    totalPfeeSum += totalPfeeValue;
                    totalReimbursementSum += reimbursementValue;
                    totalCollected += collectedValue;
                    totalSST += sstValue;
                    totalReimbSST += reimbSstValue;
                    grandTotalSST += totalSstValue;
                }
            });
            
            $('#selectedTotalSST').text(totalSST.toFixed(2));
            $('#footerTotalAmt').text(totalAmt.toFixed(2));
            $('#footerTotalPfee').text(totalPfeeSum.toFixed(2));
            $('#footerReimbursement').text(totalReimbursementSum.toFixed(2));
            $('#footerCollectedAmt').text(totalCollected.toFixed(2));
            
            // Update summary box
            $('#selectedTotalAmount').text(grandTotalSST.toFixed(2));
            
            // Update the overall transfer total amount (includes both current and new invoices)
            updateTransferTotalAmount();
            
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

        // Initialize sorting for Current Invoices table
        function initializeCurrentInvoicesSorting() {
            // Remove existing event handlers to prevent duplicates
            $('#selectedInvoicesTable .sortable-header').off('click');
            
            // Add click handlers to sortable headers in the Current Invoices table
            $('#selectedInvoicesTable .sortable-header').on('click', function() {
                const sortField = $(this).data('sort');
                const currentDirection = $(this).data('direction') || 'asc';
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                
                // Update sort direction
                $(this).data('direction', newDirection);
                
                // Clear all sort icons in the Current Invoices table
                $('#selectedInvoicesTable .sort-icon').html('');
                
                // Update current sort icon
                const sortIcon = $(this).find('.sort-icon');
                if (newDirection === 'asc') {
                    sortIcon.html('↑');
                } else {
                    sortIcon.html('↓');
                }
                
                // Sort the table
                sortCurrentInvoicesTable(sortField, newDirection);
            });
        }
        
        // Sort Current Invoices table
        function sortCurrentInvoicesTable(sortField, direction) {
            const tbody = $('#selectedInvoicesTableBody');
            const rows = tbody.find('tr:visible').toArray();
            
            rows.sort(function(a, b) {
                let aValue, bValue;
                
                // Get the appropriate cell value based on sort field
                // Column indices: 0=No, 1=Action, 2=Ref No, 3=Client Name, 4=Invoice No, 5=Invoice Date, 
                // 6=Total amt, 7=Pfee1, 8=Pfee2, 9=Collected amt, 10=SST, 11=Reimb SST, 12=Total SST, 13=Payment Date
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
                    case 'total_pfee':
                        aValue = parseFloat($(a).find('td:nth-child(8)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(8)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'reimbursement_amount':
                        aValue = parseFloat($(a).find('td:nth-child(9)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(9)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'collected_amount':
                        aValue = parseFloat($(a).find('td:nth-child(10)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(10)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'sst':
                        aValue = parseFloat($(a).find('td:nth-child(11)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(11)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'reimb_sst':
                        aValue = parseFloat($(a).find('td:nth-child(12)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(12)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'total_sst':
                        aValue = parseFloat($(a).find('td:nth-child(13)').text().replace(/,/g, '')) || 0;
                        bValue = parseFloat($(b).find('td:nth-child(13)').text().replace(/,/g, '')) || 0;
                        break;
                    case 'payment_date':
                        aValue = $(a).find('td:nth-child(14)').text().trim();
                        bValue = $(b).find('td:nth-child(14)').text().trim();
                        break;
                    default:
                        return 0;
                }
                
                // Handle date sorting
                if (sortField === 'invoice_date' || sortField === 'payment_date') {
                    if (aValue === 'N/A') aValue = new Date(0);
                    else aValue = new Date(aValue) || new Date(0);
                    if (bValue === 'N/A') bValue = new Date(0);
                    else bValue = new Date(bValue) || new Date(0);
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
            updateCurrentInvoicesRowNumbers();
        }
        
        // Update row numbers for Current Invoices table
        function updateCurrentInvoicesRowNumbers() {
            $('#selectedInvoicesTableBody tr:visible').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }
        
        // Filter Current Invoices table
        function filterCurrentInvoices() {
            const term = $('#currentInvoicesSearch').val().toLowerCase();
            let visibleCount = 0;
            const totalCount = $('#selectedInvoicesTableBody tr').length;
            
            $('#selectedInvoicesTableBody tr').each(function() {
                const row = $(this);
                const text = row.text().toLowerCase();
                
                if (text.includes(term)) {
                    row.show();
                    visibleCount++;
                } else {
                    row.hide();
                }
            });
            
            // Update count badge
            if (term) {
                $('#currentInvoicesVisibleCount').text(visibleCount);
                $('#currentInvoicesTotalCount').text(totalCount);
                $('#currentInvoicesCount').show();
            } else {
                $('#currentInvoicesCount').hide();
            }
        }
        
        // Clear Current Invoices search
        function clearCurrentInvoicesSearch() {
            $('#currentInvoicesSearch').val('');
            filterCurrentInvoices();
        }
        
        // Table sorting functionality for invoice selection modal
        function initializeInvoiceTableSorting() {
            // Remove existing event handlers to prevent duplicates
            $('#invoiceListContainer .sortable-header').off('click');
            
            // Add click handlers to sortable headers in the modal
            $('#invoiceListContainer .sortable-header').on('click', function() {
                const sortField = $(this).data('sort');
                
                // If clicking the same column, toggle direction; otherwise, set to asc
                if (sortColumn === sortField) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    sortColumn = sortField;
                    sortDirection = 'asc';
                }
                
                // Clear all sort icons in the modal table
                $('#invoiceListContainer .sort-icon').html('');
                
                // Update current sort icon
                const sortIcon = $(this).find('.sort-icon');
                if (sortDirection === 'asc') {
                    sortIcon.html('↑');
                } else {
                    sortIcon.html('↓');
                }
                
                // Reset to first page when sorting changes
                currentPage = 1;
                
                // Reload invoices with new sort parameters
                searchInvoices();
            });
        }

    </script>
@endsection
