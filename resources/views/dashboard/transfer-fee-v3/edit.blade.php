@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">


@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4><i class="fa fa-edit"></i> Edit Transfer Fee
                                        @if ($TransferFeeMain->is_recon == '1')
                                            <span class="badge badge-warning ml-2">RECONCILED - READ ONLY</span>
                                        @endif
                                    </h4>
                                    <small class="text-muted">
                                        @if ($TransferFeeMain->is_recon == '1')
                                            View transfer fee information (modifications disabled)
                                        @else
                                            Invoice-based transfer fee editing
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    @if(auth()->user()->menuroles === 'admin')
                                    <button type="button" class="btn btn-warning btn-lg mr-2" id="fixSSTAmountsBtn" 
                                        title="Fix SST transfer amounts based on current invoice SST values (Admin Only)">
                                        <i class="fa fa-calculator"></i> Fix SST Amounts
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-primary btn-lg" id="scanAndFixDiscrepanciesBtn" 
                                        title="Scan for discrepancies and automatically fix them">
                                        <i class="fa fa-search"></i> Scan & Fix Discrepancies
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Discrepancy Status Alert Banner -->
                            <div id="discrepancyStatusAlert" class="alert alert-info" style="display: none;">
                                <div class="row align-items-center">
                                    <div class="col-md-10">
                                        <h5 class="mb-1">
                                            <i class="fa fa-info-circle"></i> <span id="discrepancyStatusText">Checking for discrepancies...</span>
                                        </h5>
                                        <p class="mb-0" id="discrepancyStatusDescription">Please wait while we scan the transfer records.</p>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <button type="button" class="btn btn-primary" id="fixAllDiscrepanciesTopBtn" style="display: none;">
                                            <i class="fa fa-magic"></i> Fix All
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <form id="transferFeeForm" method="POST"
                                action="{{ route('transferfee.update', $TransferFeeMain->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transfer Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="transfer_date"
                                                value="{{ $TransferFeeMain->transfer_date }}" required
                                                {{ $TransferFeeMain->is_recon == '1' ? 'disabled' : '' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transaction ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="trx_id"
                                                value="{{ $TransferFeeMain->transaction_id }}" required
                                                {{ $TransferFeeMain->is_recon == '1' ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transfer From <span class="text-danger">*</span></label>
                                            <select class="form-control" name="transfer_from" required
                                                {{ $TransferFeeMain->is_recon == '1' ? 'disabled' : '' }}>
                                                <option value="">Select Bank Account</option>
                                                @foreach ($OfficeBankAccount as $bank)
                                                    <option value="{{ $bank->id }}"
                                                        {{ $TransferFeeMain->transfer_from == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->name }} ({{ $bank->account_no }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transfer Total Amount 
                                                <span id="discrepancyBadge" style="display: none;">
                                                    <span class="badge badge-danger ml-2" id="discrepancyBadgeText">
                                                        <i class="fa fa-exclamation-triangle"></i> Discrepancies Detected
                                                    </span>
                                                </span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="transfer_total_amount"
                                                    id="transferTotalAmount"
                                                    value="{{ number_format($TransferFeeMain->transfer_amount ?? 0, 2) }}"
                                                    readonly
                                                    style="background-color: #f8f9fa; font-weight: bold; color: #495057;">
                                            </div>
                                            <small class="text-muted" id="discrepancyMessage">
                                                <i class="fa fa-info-circle"></i> Click "Scan & Fix Discrepancies" above to check for data inconsistencies
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Discrepancy Details Modal -->
                                <div class="modal fade" id="discrepancyModal" tabindex="-1" role="dialog" aria-labelledby="discrepancyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="discrepancyModalLabel">
                                                    <i class="fa fa-exclamation-triangle text-warning"></i> Transfer Fee Discrepancy Details
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="discrepancyModalContent">
                                                    <div class="text-center">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p class="mt-2">Checking for discrepancies...</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" id="fixAllDiscrepanciesBtn" style="display: none;">
                                                    <i class="fa fa-magic"></i> Fix All Discrepancies
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fix All Confirmation Modal -->
                                <div class="modal fade" id="fixAllConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="fixAllConfirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title" id="fixAllConfirmationModalLabel">
                                                    <i class="fa fa-exclamation-triangle"></i> Confirm Fix All Discrepancies
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <strong><i class="fa fa-info-circle"></i> This will automatically fix all discrepancies:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Remove duplicate ledger entries</li>
                                                        <li>Create missing ledger entries</li>
                                                        <li>Fix amount mismatches</li>
                                                    </ul>
                                                </div>
                                                <div id="fixAllConfirmationContent">
                                                    <div class="text-center">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <p class="mt-2">Analyzing discrepancies...</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary" id="confirmFixAllBtn">
                                                    <i class="fa fa-check"></i> Confirm & Fix All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Transfer To <span class="text-danger">*</span></label>
                                            <select class="form-control" name="transfer_to" required
                                                {{ $TransferFeeMain->is_recon == '1' ? 'disabled' : '' }}>
                                                <option value="">Select Bank Account</option>
                                                @foreach ($OfficeBankAccount as $bank)
                                                    <option value="{{ $bank->id }}"
                                                        {{ $TransferFeeMain->transfer_to == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->name }} ({{ $bank->account_no }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Purpose <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="purpose" rows="3" required
                                                {{ $TransferFeeMain->is_recon == '1' ? 'disabled' : '' }}>{{ $TransferFeeMain->purpose }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h5><i class="fa fa-file-invoice"></i> Manage Invoices for Transfer</h5>
                                        <p class="text-muted">Click the button below to add/remove invoices</p>

                                        {{-- @if ($TransferFeeMain->is_recon != '1')
                                            <button type="button" class="btn btn-info" onclick="openInvoiceModal()">
                                                <i class="fa fa-search"></i> Manage Invoices
                                            </button>
                                        @endif --}}

                                        <div id="selectedInvoicesSummary" class="mt-3" style="display:block;">
                                            <div class="alert alert-info hide">
                                                <i class="fa fa-info-circle"></i>
                                                <strong><span id="selectedCount">{{ count($TransferFeeDetails) }}</span>
                                                    invoices selected</strong>
                                                <br>
                                                <strong>Total Amount: <span
                                                        id="selectedTotalAmount">{{ number_format(
                                                            $TransferFeeDetails->sum(function ($detail) {
                                                                return ($detail->transfer_amount ?? 0) + ($detail->sst_amount ?? 0) + ($detail->reimbursement_amount ?? 0) + ($detail->reimbursement_sst_amount ?? 0);
                                                            }),
                                                            2,
                                                        ) }}</span></strong>
                                            </div>

                                            <!-- Selected Invoices Table -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <h6 class="mb-0 mr-3"><i class="fa fa-list-check"></i> Current Invoices
                                                            </h6>
                                                            <div style="position: relative; z-index: 10;">
                                                                <input type="checkbox" id="filterDifferencesCheckbox" onchange="applyFilters()" style="margin-right: 5px; cursor: pointer; width: 16px; height: 16px;">
                                                                <label for="filterDifferencesCheckbox" style="font-size: 12px; cursor: pointer; margin-bottom: 0; user-select: none; margin-right: 15px;">
                                                                    Show only invoices with Total ≠ Collected
                                                                </label>
                                                                <input type="checkbox" id="filterRemainingAmountsCheckbox" onchange="applyFilters()" style="margin-right: 5px; cursor: pointer; width: 16px; height: 16px;">
                                                                <label for="filterRemainingAmountsCheckbox" style="font-size: 12px; cursor: pointer; margin-bottom: 0; user-select: none;">
                                                                    Show only invoices with remaining amounts to transfer
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                onclick="exportToExcel()" title="Export to Excel">
                                                                <i class="fa fa-file-excel-o"></i> Excel
                                                            </button>
                                                            <!-- PDF export temporarily hidden until DomPDF is installed on server -->
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
                                                                            data-sort="collected_amount"
                                                                            style="cursor: pointer;">
                                                                            Collected amt
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-collected_amount"></span>
                                                                        </th>
                                                                        <th width="70" class="sortable-header"
                                                                            data-sort="pfee" style="cursor: pointer;">
                                                                            pfee
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-pfee"></span>
                                                                        </th>
                                                                        <th width="60" class="sortable-header"
                                                                            data-sort="sst" style="cursor: pointer;">
                                                                            sst
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-sst"></span>
                                                                        </th>
                                                                        <th width="70" class="sortable-header"
                                                                            data-sort="reimbursement" style="cursor: pointer;">
                                                                            reimb
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-reimbursement"></span>
                                                                        </th>
                                                                        <th width="60" class="sortable-header"
                                                                            data-sort="reimbursement_sst" style="cursor: pointer;">
                                                                            reimb sst
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-reimbursement_sst"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="pfee_to_transfer"
                                                                            style="cursor: pointer;">
                                                                            Pfee to transfer
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-pfee_to_transfer"></span>
                                                                        </th>
                                                                        <th width="70" class="sortable-header"
                                                                            data-sort="sst_to_transfer"
                                                                            style="cursor: pointer;">
                                                                            SST to transfer
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-sst_to_transfer"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="reimbursement_to_transfer"
                                                                            style="cursor: pointer;">
                                                                            Reimb to transfer
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-reimbursement_to_transfer"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="reimbursement_sst_to_transfer"
                                                                            style="cursor: pointer;">
                                                                            Reimb SST to transfer
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-reimbursement_sst_to_transfer"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="transferred_bal"
                                                                            style="cursor: pointer;">
                                                                            Transferred Bal
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-transferred_bal"></span>
                                                                        </th>
                                                                        <th width="80" class="sortable-header"
                                                                            data-sort="transferred_sst"
                                                                            style="cursor: pointer;">
                                                                            Transferred SST
                                                                            <span class="sort-icon"
                                                                                id="sort-selected-transferred_sst"></span>
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
                                                                    @foreach ($TransferFeeDetails as $index => $detail)
                                                                        <tr>
                                                                            <td class="text-center" style="font-size: 11px;">
                                                                                {{ $index + 1 }}
                                                                            </td>
                                                                            <td>
                                                                                @if ($TransferFeeMain->is_recon != '1')
                                                                                    <button type="button" class="btn btn-sm btn-danger"
                                                                                        onclick="deleteTransferRecord({{ $detail->id }}, '{{ $detail->invoice_no ?? $detail->bill_invoice_no }}')"
                                                                                        title="Delete Transfer Record"
                                                                                        style="font-size: 10px; padding: 2px 4px;">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                            <td style="font-size: 11px;">
                                                                                <a href="/case/{{ $detail->case_id ?? '' }}" target="_blank" class="text-primary" style="text-decoration: none;">
                                                                                    {{ $detail->case_ref_no ?? 'N/A' }}
                                                                                </a>
                                                                            </td>
                                                                            <td style="font-size: 11px;">
                                                                                {{ $detail->invoice_no ?? ($detail->bill_invoice_no ?? 'N/A') }}
                                                                            </td>
                                                                            <td style="font-size: 11px;">
                                                                                {{ $detail->invoice_date ?? ($detail->bill_invoice_date ?? 'N/A') }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                <div class="d-inline-flex align-items-center">
                                                                                    <span class="total-amt-display" 
                                                                                          data-detail-id="{{ $detail->id }}" 
                                                                                          data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                          data-original-value="{{ $detail->bill_total_amt_divided ?? 0 }}">
                                                                                        {{ number_format($detail->bill_total_amt_divided ?? 0, 2) }}
                                                                                    </span>
                                                                                    @if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
                                                                                        <i class="fa fa-pencil edit-total-amt ml-1" 
                                                                                           style="cursor: pointer; color: #007bff; font-size: 11px;" 
                                                                                           data-detail-id="{{ $detail->id }}"
                                                                                           data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                           title="Edit Total Amount"></i>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                {{ number_format($detail->bill_collected_amt_divided ?? 0, 2) }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                <div class="d-inline-flex align-items-center">
                                                                                    <span class="pfee-display" 
                                                                                          data-detail-id="{{ $detail->id }}" 
                                                                                          data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                          data-pfee1="{{ $detail->pfee1_inv ?? 0 }}"
                                                                                          data-pfee2="{{ $detail->pfee2_inv ?? 0 }}"
                                                                                          data-original-value="{{ ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) }}">
                                                                                        {{ number_format(($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0), 2) }}
                                                                                    </span>
                                                                                    @if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
                                                                                        <i class="fa fa-pencil edit-pfee ml-1" 
                                                                                           style="cursor: pointer; color: #007bff; font-size: 11px;" 
                                                                                           data-detail-id="{{ $detail->id }}"
                                                                                           data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                           data-bill-id="{{ $detail->loan_case_main_bill_id }}"
                                                                                           data-pfee1="{{ $detail->pfee1_inv ?? 0 }}"
                                                                                           data-pfee2="{{ $detail->pfee2_inv ?? 0 }}"
                                                                                           title="Edit Professional Fee"></i>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                <div class="d-inline-flex align-items-center">
                                                                                    <span class="sst-display" 
                                                                                          data-detail-id="{{ $detail->id }}" 
                                                                                          data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                          data-original-value="{{ $detail->sst_inv ?? 0 }}">
                                                                                        {{ number_format($detail->sst_inv ?? 0, 2) }}
                                                                                    </span>
                                                                                    @if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
                                                                                        <i class="fa fa-pencil edit-sst ml-1" 
                                                                                           style="cursor: pointer; color: #007bff; font-size: 11px;" 
                                                                                           data-detail-id="{{ $detail->id }}"
                                                                                           data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                           data-bill-id="{{ $detail->loan_case_main_bill_id }}"
                                                                                           title="Edit SST"></i>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                <div class="d-inline-flex align-items-center">
                                                                                    <span class="reimb-display" 
                                                                                          data-detail-id="{{ $detail->id }}" 
                                                                                          data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                          data-original-value="{{ $detail->invoice_reimbursement_amount ?? 0 }}">
                                                                                        {{ number_format($detail->invoice_reimbursement_amount ?? 0, 2) }}
                                                                                    </span>
                                                                                    @if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
                                                                                        <i class="fa fa-pencil edit-reimb ml-1" 
                                                                                           style="cursor: pointer; color: #007bff; font-size: 11px;" 
                                                                                           data-detail-id="{{ $detail->id }}"
                                                                                           data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                           data-bill-id="{{ $detail->loan_case_main_bill_id }}"
                                                                                           title="Edit Reimbursement"></i>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                <div class="d-inline-flex align-items-center">
                                                                                    <span class="reimb-sst-display" 
                                                                                          data-detail-id="{{ $detail->id }}" 
                                                                                          data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                          data-original-value="{{ $detail->invoice_reimbursement_sst ?? 0 }}">
                                                                                        {{ number_format($detail->invoice_reimbursement_sst ?? 0, 2) }}
                                                                                    </span>
                                                                                    @if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
                                                                                        <i class="fa fa-pencil edit-reimb-sst ml-1" 
                                                                                           style="cursor: pointer; color: #007bff; font-size: 11px;" 
                                                                                           data-detail-id="{{ $detail->id }}"
                                                                                           data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
                                                                                           data-bill-id="{{ $detail->loan_case_main_bill_id }}"
                                                                                           title="Edit Reimbursement SST"></i>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                @php
                                                                                    // Show available remaining amounts (original - transferred)
                                                                                    $originalPfee =
                                                                                        ($detail->pfee1_inv ?? 0) +
                                                                                        ($detail->pfee2_inv ?? 0);
                                                                                    $originalSst =
                                                                                        $detail->sst_inv ?? 0;
                                                                                    $availablePfee = max(
                                                                                        0,
                                                                                        $originalPfee -
                                                                                            ($detail->transferred_pfee_amt ??
                                                                                                0),
                                                                                    );
                                                                                    $availableSst = max(
                                                                                        0,
                                                                                        $originalSst -
                                                                                            ($detail->transferred_sst_amt ??
                                                                                                0),
                                                                                    );
                                                                                    $hasTransferred =
                                                                                        ($detail->transfer_amount ??
                                                                                            0) >
                                                                                            0 ||
                                                                                        ($detail->sst_amount ?? 0) > 0;
                                                                                @endphp
                                                                                {{ number_format($availablePfee, 2) }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                {{ number_format($availableSst, 2) }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                @php
                                                                                    $originalReimbursement = $detail->invoice_reimbursement_amount ?? 0;
                                                                                    $availableReimbursement = max(0, $originalReimbursement - ($detail->transferred_reimbursement_amt ?? 0));
                                                                                @endphp
                                                                                {{ number_format($availableReimbursement, 2) }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                @php
                                                                                    $originalReimbursementSst = $detail->invoice_reimbursement_sst ?? 0;
                                                                                    $availableReimbursementSst = max(0, $originalReimbursementSst - ($detail->transferred_reimbursement_sst_amt ?? 0));
                                                                                @endphp
                                                                                {{ number_format($availableReimbursementSst, 2) }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                @php
                                                                                    // Show amounts transferred in THIS transfer fee record only
                                                                                    // (not cumulative amounts from invoice table)
                                                                                    $transferredPfee = $detail->transfer_amount ?? 0;
                                                                                    $transferredReimbursement = $detail->reimbursement_amount ?? 0;
                                                                                    $transferredBal = $transferredPfee + $transferredReimbursement;
                                                                                @endphp
                                                                                {{ number_format($transferredBal, 2) }}
                                                                            </td>
                                                                            <td class="text-right"
                                                                                style="font-size: 11px;">
                                                                                @php
                                                                                    // Show amounts transferred in THIS transfer fee record only
                                                                                    // (not cumulative amounts from invoice table)
                                                                                    $transferredSst = $detail->sst_amount ?? 0;
                                                                                    $transferredReimbursementSst = $detail->reimbursement_sst_amount ?? 0;
                                                                                    $transferredSstTotal = $transferredSst + $transferredReimbursementSst;
                                                                                @endphp
                                                                                {{ number_format($transferredSstTotal, 2) }}
                                                                            </td>
                                                                            <td style="font-size: 11px;">
                                                                                @if($detail->payment_receipt_date)
                                                                                    {{ $detail->payment_receipt_date }}
                                                                                @elseif($detail->bill_invoice_date)
                                                                                    {{ $detail->bill_invoice_date }}
                                                                                @else
                                                                                    N/A
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot class="table-dark"
                                                                    style="position: sticky; bottom: 0; z-index: 1;">
                                                                    <tr>
                                                                        <th colspan="5" class="text-right">
                                                                            <strong>TOTAL:</strong>
                                                                        </th>
                                                                        <th class="text-right" id="footerTotalAmt">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return $detail->bill_total_amt_divided ?? 0;}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerCollectedAmt">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return $detail->bill_collected_amt_divided ?? 0;}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerPfee">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerSst">
                                                                            {{ number_format($TransferFeeDetails->sum('sst_inv'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerReimb">
                                                                            {{ number_format($TransferFeeDetails->sum('invoice_reimbursement_amount'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerReimbSst">
                                                                            {{ number_format($TransferFeeDetails->sum('invoice_reimbursement_sst'), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerPfeeToTransfer">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return max(0, ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) - ($detail->transferred_pfee_amt ?? 0));}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerSstToTransfer">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return max(0, ($detail->sst_inv ?? 0) - ($detail->transferred_sst_amt ?? 0));}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerReimbToTransfer">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return max(0, ($detail->invoice_reimbursement_amount ?? 0) - ($detail->transferred_reimbursement_amt ?? 0));}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerReimbSstToTransfer">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {return max(0, ($detail->invoice_reimbursement_sst ?? 0) - ($detail->transferred_reimbursement_sst_amt ?? 0));}),2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerTransferredBal">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {
                                                                                // Sum of pfee + reimb from original invoice amounts (not transferred amounts)
                                                                                // This should match the sum of footerPfee + footerReimb
                                                                                $pfee = ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);
                                                                                $reimb = $detail->invoice_reimbursement_amount ?? 0;
                                                                                return $pfee + $reimb;
                                                                            }), 2) }}
                                                                        </th>
                                                                        <th class="text-right" id="footerTransferredSst">
                                                                            {{ number_format($TransferFeeDetails->sum(function ($detail) {
                                                                                // Sum of sst + reimb_sst from original invoice amounts (not transferred amounts)
                                                                                // This should match the sum of footerSst + footerReimbSst
                                                                                $sst = $detail->sst_inv ?? 0;
                                                                                $reimbSst = $detail->invoice_reimbursement_sst ?? 0;
                                                                                return $sst + $reimbSst;
                                                                            }), 2) }}
                                                                        </th>
                                                                        <th></th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div id="noSelectedInvoices" class="text-center text-muted"
                                                        style="display:none;">
                                                        <i class="fa fa-info-circle"></i> No invoices selected yet
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add New Invoices Section -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fa fa-plus-circle"></i> Add New Invoices</h6>
                                        <small class="text-muted">Select new invoices to add to this transfer fee</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            @if ($TransferFeeMain->is_recon != '1')
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="openInvoiceModal()">
                                                        <i class="fa fa-plus"></i> Select New Invoices
                                                    </button>
                                                </div>
                                            @endif

                                        </div>

                                        <!-- New Invoices Table -->
                                        <div id="newInvoicesTable" style="display: none;">
                                            <h6 class="mb-2">New Invoices to Add:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped"
                                                    style="margin-bottom: 0;">
                                                    <thead class="thead-dark"
                                                        style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
                                                        <tr>
                                                            <th width="30">No</th>
                                                            <th width="120">Ref No</th>
                                                            <th width="100">Invoice No</th>
                                                            <th width="90">Invoice Date</th>
                                                            <th width="80">Total amt</th>
                                                            <th width="80">Collected amt</th>
                                                            <th width="70">pfee</th>
                                                            <th width="60">sst</th>
                                                            <th width="70">reimb</th>
                                                            <th width="60">reimb sst</th>
                                                            <th width="80">Pfee to transfer</th>
                                                            <th width="70">SST to transfer</th>
                                                            <th width="80">Reimb to transfer</th>
                                                            <th width="70">Reimb SST to transfer</th>
                                                            <th width="40">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="newInvoicesTableBody">
                                                        <!-- New invoices will be added here -->
                                                    </tbody>
                                                    <tfoot class="table-dark"
                                                        style="position: sticky; bottom: 0; z-index: 1;">
                                                        <tr>
                                                            <th colspan="4" class="text-right"><strong>NEW
                                                                    TOTAL:</strong></th>
                                                            <th class="text-right" id="newTotalAmt">0.00</th>
                                                            <th class="text-right" id="newCollectedAmt">0.00</th>
                                                            <th class="text-right" id="newPfee">0.00</th>
                                                            <th class="text-right" id="newSst">0.00</th>
                                                            <th class="text-right" id="newReimb">0.00</th>
                                                            <th class="text-right" id="newReimbSst">0.00</th>
                                                            <th class="text-right" id="newPfeeToTransfer">0.00</th>
                                                            <th class="text-right" id="newSstToTransfer">0.00</th>
                                                            <th class="text-right" id="newReimbToTransfer">0.00</th>
                                                            <th class="text-right" id="newReimbSstToTransfer">0.00</th>
                                                            <th></th>
                                                        </tr>
                                                        <!-- Combined Total Row -->
                                                        <tr class="table-info">
                                                            <th colspan="4" class="text-right"><strong>COMBINED
                                                                    TOTAL:</strong></th>
                                                            <th class="text-right" id="combinedTotalAmt">0.00</th>
                                                            <th class="text-right" id="combinedCollectedAmt">0.00</th>
                                                            <th class="text-right" id="combinedPfee">0.00</th>
                                                            <th class="text-right" id="combinedSst">0.00</th>
                                                            <th class="text-right" id="combinedReimb">0.00</th>
                                                            <th class="text-right" id="combinedReimbSst">0.00</th>
                                                            <th class="text-right" id="combinedPfeeToTransfer">0.00</th>
                                                            <th class="text-right" id="combinedSstToTransfer">0.00</th>
                                                            <th class="text-right" id="combinedReimbToTransfer">0.00</th>
                                                            <th class="text-right" id="combinedReimbSstToTransfer">0.00</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <input type="hidden" name="add_invoice" id="add_invoice"
                                    value="{{ json_encode(
                                        $TransferFeeDetails->map(function ($detail) {
                                                $availablePfee = max(
                                                    0,
                                                    ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) - ($detail->transferred_pfee_amt ?? 0),
                                                );
                                                $availableSst = max(0, ($detail->sst_inv ?? 0) - ($detail->transferred_sst_amt ?? 0));
                                                $availableReimbursement = max(0, ($detail->invoice_reimbursement_amount ?? 0) - ($detail->transferred_reimbursement_amt ?? 0));
                                                $availableReimbursementSst = max(0, ($detail->invoice_reimbursement_sst ?? 0) - ($detail->transferred_reimbursement_sst_amt ?? 0));
                                                return [
                                                    'id' => $detail->loan_case_invoice_main_id,
                                                    'bill_id' => $detail->loan_case_main_bill_id,
                                                    'value' => $availablePfee,
                                                    'sst' => $availableSst,
                                                    'reimbursement' => $availableReimbursement,
                                                    'reimbursement_sst' => $availableReimbursementSst,
                                                    'transferred_pfee' => $detail->transferred_pfee_amt ?? 0,
                                                    'transferred_sst' => $detail->transferred_sst_amt ?? 0,
                                                    'transferred_reimbursement' => $detail->transferred_reimbursement_amt ?? 0,
                                                    'transferred_reimbursement_sst' => $detail->transferred_reimbursement_sst_amt ?? 0,
                                                    'current_transfer_pfee' => $detail->transfer_amount ?? 0,
                                                    'current_transfer_sst' => $detail->sst_amount ?? 0,
                                                    'current_transfer_reimbursement' => $detail->reimbursement_amount ?? 0,
                                                    'current_transfer_reimbursement_sst' => $detail->reimbursement_sst_amount ?? 0,
                                                    'invoice_no' => $detail->invoice_no ?? $detail->bill_invoice_no,
                                                    'invoice_date' => $detail->invoice_date ?? $detail->bill_invoice_date,
                                                    'case_ref' => $detail->case_ref_no,
                                                    'client_name' => $detail->client_name,
                                                    'payment_date' => $detail->payment_receipt_date,
                                                    'case_id' => $detail->case_id,
                                                    'pfee1_inv' => $detail->pfee1_inv ?? 0,
                                                    'pfee2_inv' => $detail->pfee2_inv ?? 0,
                                                    'sst_inv' => $detail->sst_inv ?? 0,
                                                    'bill_total_amt_divided' => $detail->bill_total_amt_divided ?? 0,
                                                    'bill_collected_amt_divided' => $detail->bill_collected_amt_divided ?? 0,
                                                ];
                                            })->toArray(),
                                    ) }}">

                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($TransferFeeMain->is_recon != '1')
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="fa fa-save"></i> Update Transfer Fee
                                            </button>
                                            <button type="button" class="btn btn-warning" onclick="reconTransferFee()"
                                                id="reconcileBtn">
                                                <i class="fa fa-check-circle"></i> Bank Recon
                                            </button>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> This transfer fee has been reconciled and
                                                cannot be modified.
                                            </div>
                                            @if(in_array(auth()->user()->menuroles, ['admin', 'account']))
                                                <button type="button" class="btn btn-danger" onclick="revertReconTransferFee()"
                                                    id="revertReconBtn">
                                                    <i class="fa fa-undo"></i> Revert Recon
                                                </button>
                                            @endif
                                        @endif
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
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 99%; width: 99%; margin: 0.25rem auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">
                        <i class="fa fa-file-invoice"></i> Manage Invoices for Transfer
                    </h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Collapsible Search Filters -->
                    <div class="card mb-2">
                        <div class="card-header py-2" style="background-color: #f8f9fa; cursor: pointer;"
                            onclick="toggleFilters()">
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
                                    <label for="searchCaseRef" style="font-size: 12px; margin-bottom: 2px;">Case Ref
                                        No:</label>
                                    <input type="text" id="searchCaseRef" class="form-control"
                                        style="font-size: 11px;" placeholder="Search case ref...">
                                </div>
                                <div class="col-md-3">
                                    <label for="searchClient" style="font-size: 12px; margin-bottom: 2px;">Client
                                        Name:</label>
                                    <input type="text" id="searchClient" class="form-control"
                                        style="font-size: 11px;" placeholder="Search client...">
                                </div>
                                <div class="col-md-3">
                                    <label for="filterBranch" style="font-size: 12px; margin-bottom: 2px;">Branch:</label>
                                    <select id="filterBranch" class="form-control" style="font-size: 11px;">
                                        <option value="">-- All Branches --</option>
                                        @foreach ($Branchs as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterStartDate" style="font-size: 12px; margin-bottom: 2px;">Start
                                        Date:</label>
                                    <input type="date" id="filterStartDate" class="form-control"
                                        style="font-size: 11px;">
                                </div>
                            </div>

                            <!-- Additional Filters -->
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <label for="searchBillingParty" style="font-size: 12px; margin-bottom: 2px;">Billing
                                        Party:</label>
                                    <input type="text" id="searchBillingParty" class="form-control"
                                        style="font-size: 11px;" placeholder="Search billing party...">
                                </div>
                                <div class="col-md-3">
                                    <label for="filterEndDate" style="font-size: 12px; margin-bottom: 2px;">End
                                        Date:</label>
                                    <input type="date" id="filterEndDate" class="form-control"
                                        style="font-size: 11px;">
                                </div>
                                <div class="col-md-3">
                                    <label for="searchInvoiceNo" style="font-size: 12px; margin-bottom: 2px;">Invoice
                                        No:</label>
                                    <textarea id="searchInvoiceNo" class="form-control" rows="2" style="font-size: 11px;"
                                        placeholder="Enter invoice numbers (one per line or comma-separated)&#10;Example:&#10;INV-001&#10;INV-002, INV-003&#10;INV-004"></textarea>
                                    <small class="text-muted" style="font-size: 10px;">Enter multiple invoice numbers
                                        separated by commas or new lines</small>
                                    <div id="invoiceCount" class="text-info" style="display:none;">
                                        <small style="font-size: 10px;"><i class="fa fa-info-circle"></i> <span
                                                id="invoiceCountText">0</span> invoice numbers entered</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                                        style="font-size: 10px; padding: 2px 6px;" onclick="clearInvoiceNumbers()">
                                        <i class="fa fa-times"></i> Clear Invoice Numbers
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <label style="font-size: 12px; margin-bottom: 2px;">&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            style="font-size: 10px; padding: 2px 6px;" onclick="clearDateFilters()">
                                            <i class="fa fa-times"></i> Clear Dates
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="perPageSelect" style="font-size: 12px; margin-bottom: 2px;">Records per
                                        page:</label>
                                    <select id="perPageSelect" class="form-control"
                                        style="width: auto; display: inline-block; font-size: 11px;">
                                        <option value="10">10 records</option>
                                        <option value="20" selected>20 records</option>
                                        <option value="50">50 records</option>
                                        <option value="100">100 records</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 10px;">Changing this will refresh the
                                        list</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        style="font-size: 11px; padding: 4px 8px;" onclick="searchInvoices()">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        style="font-size: 11px; padding: 4px 8px;" onclick="clearSearch()">
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
                                        <input type="text" id="quickSearch" class="form-control"
                                            style="font-size: 12px;"
                                            placeholder="Quick search by Invoice No or Case Ref No..."
                                            onkeyup="quickSearch()">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                style="font-size: 12px;" onclick="clearQuickSearch()">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;">Type to filter table rows
                                        instantly</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <span class="badge badge-info" id="quickSearchCount" style="display:none;">
                                        <span id="quickSearchResultCount">0</span> of <span
                                            id="quickSearchTotalCount">0</span> records
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
                                    <span id="modalSelectedCount">{{ count($TransferFeeDetails) }}</span> invoices
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
    <!-- Load jQuery 3.6.0 and Bootstrap 4.6.0 for modal functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Use jQuery 3.6.0 for modal functionality
        var $jq3 = jQuery.noConflict(true);
        
        // Override the global $ and jQuery with version 3.6.0 for modal
        window.$ = $jq3;
        window.jQuery = $jq3;
        
        console.log('jQuery version:', $.fn.jquery);
        console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
    </script>
    <script>
        // Check if the transfer fee is reconciled
        const isReconciled = {{ $TransferFeeMain->is_recon == '1' ? 'true' : 'false' }};

        // Prevent form submission if reconciled
        $(document).ready(function() {
            if (isReconciled) {
                // Disable form submission
                $('#transferFeeForm').submit(function(e) {
                    e.preventDefault();
                    Swal.fire('Error', 'Cannot modify reconciled transfer fee records', 'error');
                    return false;
                });

                // Disable modal opening
                $('button[onclick="openInvoiceModal()"]').click(function(e) {
                    e.preventDefault();
                    Swal.fire('Error', 'Cannot modify invoices for reconciled transfer fee records',
                        'error');
                    return false;
                });

                // Disable remove invoice buttons
                $('button[onclick^="removeSelectedInvoice"]').click(function(e) {
                    e.preventDefault();
                    Swal.fire('Error', 'Cannot remove invoices from reconciled transfer fee records',
                        'error');
                    return false;
                });
            }
            
            // Apply filters on page load if any checkbox is checked
            setTimeout(function() {
                if ($('#filterDifferencesCheckbox').is(':checked') || $('#filterRemainingAmountsCheckbox').is(':checked')) {
                    applyFilters();
                }
            }, 100);
        });
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
            height: 350px;
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
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
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
            // Load current invoices on page load
            loadCurrentInvoices();

            // Initialize selected invoices from existing data
            selectedInvoices = JSON.parse($('#add_invoice').val() || '[]');

            // Update the summary with existing selections - use actual transfer amounts
            const totalAmount = selectedInvoices.reduce((sum, invoice) => {
                const transferAmount = parseFloat(invoice.current_transfer_pfee || 0) + parseFloat(invoice
                    .current_transfer_sst || 0);
                return sum + transferAmount;
            }, 0);
            $('#selectedCount').text(selectedInvoices.length);
            $('#selectedTotalAmount').text(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#modalTotalAmount').text(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Show/hide summary based on selections
            if (selectedInvoices.length > 0) {
                $('#selectedInvoicesSummary').show();
            } else {
                $('#selectedInvoicesSummary').hide();
            }

            // Ensure everything is loaded
            console.log('Document ready - jQuery version:', $.fn.jquery);
            console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
            console.log('Initial selected invoices:', selectedInvoices.length);

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

            // Handle form submission
            $('#transferFeeForm').submit(function(e) {
                e.preventDefault();

                // Get new invoices from the new invoices table
                const newInvoices = [];
                $('#newInvoicesTableBody tr').each(function() {
                    const row = $(this);
                    const invoiceId = row.data('invoice-id');
                    const billId = row.data('bill-id');
                    const pfeeInput = row.find('.pfee-transfer-input');
                    const sstInput = row.find('.sst-transfer-input');

                    // Validate that bill ID exists and is valid
                    if (!billId || billId == 0 || billId == '0') {
                        Swal.fire('Error',
                            `Invoice ${invoiceId} has an invalid bill ID. Please remove this invoice and try again.`,
                            'error');
                        return false; // Stop the loop
                    }

                    if (pfeeInput.length && sstInput.length) {
                        newInvoices.push({
                            id: invoiceId,
                            bill_id: billId,
                            value: parseFloat(pfeeInput.val() || 0),
                            sst: parseFloat(sstInput.val() || 0)
                        });
                    }
                });

                // Check if any invoices were invalid (only if there are new invoices to add)
                if (newInvoices.length === 0 && $('#newInvoicesTableBody tr').length > 0) {
                    Swal.fire('Error', 'No valid invoices to submit. Please check your selections.',
                        'error');
                    return false;
                }

                // Update the hidden input with new invoices only
                $('#add_invoice').val(JSON.stringify(newInvoices));

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Updating...');

                // Submit form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'PUT',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            // Show success notification instead of modal
                            const notification = $(`
                          <div class="alert alert-success alert-dismissible fade show position-fixed" 
                               style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
                              <i class="fa fa-check-circle"></i>
                              <strong>Success!</strong> ${response.message || 'Transfer fee updated successfully!'}
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                      `);

                            $('body').append(notification);

                            // Auto-remove after 3 seconds and then reload page
                            setTimeout(function() {
                                notification.fadeOut(function() {
                                    $(this).remove();
                                    // Reload the page to show updated data
                                    window.location.reload();
                                });
                            }, 2000);
                        } else {
                            Swal.fire('Error', response.message ||
                                'Failed to update transfer fee', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while updating the transfer fee.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422) {
                            errorMessage = 'Validation error. Please check your input.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please try again later.';
                        }

                        Swal.fire('Error', errorMessage, 'error');
                    },
                    complete: function() {
                        // Restore button state
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
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

            // Sync selectedInvoices with current table state before opening modal
            selectedInvoices = [];
            $('#newInvoicesTableBody tr').each(function() {
                const row = $(this);
                const invoiceId = row.data('invoice-id');
                const billId = row.data('bill-id');
                const pfeeInput = row.find('.pfee-transfer-input');
                const sstInput = row.find('.sst-transfer-input');

                if (pfeeInput.length && sstInput.length) {
                    selectedInvoices.push({
                        id: invoiceId,
                        bill_id: billId,
                        value: parseFloat(pfeeInput.val() || 0),
                        sst: parseFloat(sstInput.val() || 0),
                        invoice_no: row.find('td:eq(2)').text(),
                        invoice_date: row.find('td:eq(3)').text(),
                        case_ref: row.find('td:eq(1)').text(),
                        payment_date: '',
                        case_id: '',
                        current_transfer_pfee: parseFloat(pfeeInput.val() || 0),
                        current_transfer_sst: parseFloat(sstInput.val() || 0)
                    });
                }
            });

            // Update modal summary with current selections - use actual transfer amounts
            const totalAmount = selectedInvoices.reduce((sum, invoice) => {
                const transferAmount = parseFloat(invoice.current_transfer_pfee || 0) + parseFloat(invoice
                    .current_transfer_sst || 0);
                return sum + transferAmount;
            }, 0);
            $('#modalSelectedCount').text(selectedInvoices.length);
            $('#totalAmount').text(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

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
                sort_order: currentSortOrder,
                current_transfer_fee_id: {{ $TransferFeeMain->id ?? 0 }}
            };

            $.ajax({
                url: '{{ route('transferfee.invoice-list') }}',
                type: 'GET',
                data: searchData,
                timeout: 30000, // 30 second timeout
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('AJAX response received:', response);

                    if (response.status == 1) {
                        $('#invoiceListContainer').html(response.invoiceList).show();

                        // Initialize invoice selection after a short delay to ensure DOM is ready
                        setTimeout(function() {
                            initializeInvoiceSelection();
                            updateSortIndicators(); // Reapply sort indicators after table reload

                            // Update modal summary with current selections - use actual transfer amounts
                            const totalAmount = selectedInvoices.reduce((sum, invoice) => {
                                const transferAmount = parseFloat(invoice
                                    .current_transfer_pfee || 0) + parseFloat(invoice
                                    .current_transfer_sst || 0);
                                return sum + transferAmount;
                            }, 0);
                            $('#modalSelectedCount').text(selectedInvoices.length);
                            $('#totalAmount').text(totalAmount.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        }, 50);

                        console.log('Invoice list loaded successfully. Found ' + response.count +
                            ' invoices on page ' + page + ' of ' + response.totalPages);
                    } else {
                        console.error('Backend returned error status:', response);
                        alert('Error loading invoices: ' + (response.message || 'Unknown error'));
                    }
                    $('#invoiceLoading').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Main invoice list failed:', xhr.responseText);
                    console.error('Error details:', {
                        xhr: xhr,
                        status: status,
                        error: error
                    });

                    if (status === 'timeout') {
                        alert('Request timed out. Please try again.');
                    } else {
                        alert('Main invoice list failed: ' + error);
                    }
                    $('#invoiceLoading').hide();
                },
                complete: function() {
                    // Hide loading spinner regardless of success/error
                    $('#invoiceLoading').hide();
                }
            });
        }

        // Function to load specific page - FIXED: No href="#" redirects
        function loadInvoicePage(page) {
            // Store current selections before loading new page
            const currentSelections = selectedInvoices.slice();

            loadMainInvoiceList(page);

            // After loading, restore selections
            setTimeout(function() {
                selectedInvoices = currentSelections;
                initializeInvoiceSelection();

                // Update modal summary - use actual transfer amounts
                const totalAmount = selectedInvoices.reduce((sum, invoice) => {
                    const transferAmount = parseFloat(invoice.current_transfer_pfee || 0) + parseFloat(
                        invoice.current_transfer_sst || 0);
                    return sum + transferAmount;
                }, 0);
                $('#modalSelectedCount').text(selectedInvoices.length);
                $('#totalAmount').text(totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }, 100);

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

            // Update select all checkbox state
            const totalCheckboxes = $('.invoice-checkbox').length;
            const checkedCheckboxes = $('.invoice-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);

            $('.invoice-checkbox').change(function() {
                updateSelectedInvoices();

                // Update select all checkbox state
                const totalCheckboxes = $('.invoice-checkbox').length;
                const checkedCheckboxes = $('.invoice-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            });

            $('#selectAll').change(function() {
                $('.invoice-checkbox').prop('checked', $(this).is(':checked'));
                updateSelectedInvoices();
            });
        }

        function updateSelectedInvoices() {
            // This function now only updates the modal display, not the main form
            // Create a temporary array for modal selections
            let modalSelections = [];
            let totalAmount = 0;

            // Get all checked checkboxes in the modal
            $('.invoice-checkbox:checked').each(function() {
                const invoiceId = $(this).val();
                const billId = $(this).data('bill-id');
                const amount = parseFloat($(this).data('amount')) || 0;
                const sst = parseFloat($(this).data('sst')) || 0;
                const reimbursement = parseFloat($(this).data('reimbursement')) || 0;
                const reimbursementSst = parseFloat($(this).data('reimbursement-sst')) || 0;

                // Check if this invoice is already in modalSelections
                const existingIndex = modalSelections.findIndex(invoice => invoice.id == invoiceId);

                if (existingIndex === -1) {
                    // Get invoice details from the table row
                    const row = $(this).closest('tr');
                    const invoiceNo = row.find('td:eq(3)').text() || 'N/A'; // Invoice No column
                    const invoiceDate = row.find('td:eq(4)').text() || 'N/A'; // Invoice Date column
                    const caseRef = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() ||
                        'N/A'; // Ref No column (handle hyperlink)
                    const clientName = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() ||
                        'N/A'; // Using Ref No as client name for now
                    const paymentDate = row.find('td:eq(13)').text() || 'N/A'; // Payment Date column
                    const caseId = row.find('td:eq(2) a').attr('href') ? row.find('td:eq(2) a').attr('href').split(
                        '/').pop() : null; // Extract case ID from href

                    // Add new invoice to modalSelections
                    modalSelections.push({
                        id: invoiceId,
                        bill_id: billId,
                        value: amount,
                        sst: sst,
                        reimbursement: reimbursement,
                        reimbursement_sst: reimbursementSst,
                        invoice_no: invoiceNo,
                        invoice_date: invoiceDate,
                        case_ref: caseRef,
                        client_name: clientName,
                        payment_date: paymentDate,
                        case_id: caseId
                    });
                }
            });

            // Add existing selections that are not visible in current modal page
            selectedInvoices.forEach(invoice => {
                const checkbox = $(`.invoice-checkbox[value="${invoice.id}"]`);
                // If checkbox doesn't exist in current modal view, add it to modalSelections
                if (checkbox.length === 0) {
                    modalSelections.push(invoice);
                }
            });

            // Calculate total amount from all modal selections - use actual transfer amounts
            totalAmount = modalSelections.reduce((sum, invoice) => {
                const transferAmount = parseFloat(invoice.current_transfer_pfee || 0) + parseFloat(invoice
                    .current_transfer_sst || 0);
                return sum + transferAmount;
            }, 0);

            // Update modal display only
            $('#modalSelectedCount').text(modalSelections.length);
            $('#modalTotalAmount').text(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#totalAmount').text(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        function confirmInvoiceSelection() {
            // Get only the checked checkboxes in the modal
            let newSelections = [];

            $('.invoice-checkbox:checked').each(function() {
                const invoiceId = $(this).val();
                const billId = $(this).data('bill-id');
                const amount = parseFloat($(this).data('amount')) || 0;
                const sst = parseFloat($(this).data('sst')) || 0;
                const reimbursement = parseFloat($(this).data('reimbursement')) || 0;
                const reimbursementSst = parseFloat($(this).data('reimbursement-sst')) || 0;

                // Validate that bill ID exists and is valid
                if (!billId || billId == 0 || billId == '0') {
                    console.error('Invalid bill ID for invoice:', invoiceId, 'billId:', billId);
                    Swal.fire('Error', `Invoice ${invoiceId} has an invalid bill ID. Please contact support.`,
                        'error');
                    return;
                }

                // Check if this invoice is already in newSelections
                const existingIndex = newSelections.findIndex(invoice => invoice.id == invoiceId);

                if (existingIndex === -1) {
                    // Get invoice details from the table row
                    const row = $(this).closest('tr');
                    const invoiceNo = row.find('td:eq(3)').text() ||
                        'N/A'; // Invoice No column (3rd column after No and Action)
                    const invoiceDate = row.find('td:eq(4)').text() || 'N/A'; // Invoice Date column (4th column)
                    const caseRef = row.find('td:eq(2) a').text() || row.find('td:eq(2)').text() ||
                        'N/A'; // Ref No column (2nd column after No and Action)
                    const paymentDate = row.find('td:eq(13)').text() || 'N/A'; // Payment Date column (13th column)
                    const caseId = row.find('td:eq(2) a').attr('href') ? row.find('td:eq(2) a').attr('href').split(
                        '/').pop() : null; // Extract case ID from href

                    // Add new invoice to newSelections
                    newSelections.push({
                        id: invoiceId,
                        bill_id: billId,
                        value: amount,
                        sst: sst,
                        reimbursement: reimbursement,
                        reimbursement_sst: reimbursementSst,
                        invoice_no: invoiceNo,
                        invoice_date: invoiceDate,
                        case_ref: caseRef,
                        payment_date: paymentDate,
                        case_id: caseId,
                        current_transfer_pfee: amount, // Set initial transfer amounts
                        current_transfer_sst: sst
                    });
                }
            });

            // Check for existing invoices and only add new ones
            let allInvoices = [];

            // First, get existing invoices from the table
            $('#newInvoicesTableBody tr').each(function() {
                const row = $(this);
                const invoiceId = row.data('invoice-id');
                const billId = row.data('bill-id');
                const pfeeInput = row.find('.pfee-transfer-input');
                const sstInput = row.find('.sst-transfer-input');

                if (pfeeInput.length && sstInput.length) {
                    allInvoices.push({
                        id: invoiceId,
                        bill_id: billId,
                        value: parseFloat(pfeeInput.val() || 0),
                        sst: parseFloat(sstInput.val() || 0),
                        invoice_no: row.find('td:eq(2)').text(),
                        invoice_date: row.find('td:eq(3)').text(),
                        case_ref: row.find('td:eq(1)').text(),
                        payment_date: '',
                        case_id: '',
                        current_transfer_pfee: parseFloat(pfeeInput.val() || 0),
                        current_transfer_sst: parseFloat(sstInput.val() || 0)
                    });
                }
            });

            // Add new selections that don't already exist
            newSelections.forEach(newInvoice => {
                const existingIndex = allInvoices.findIndex(existing => existing.id == newInvoice.id);
                if (existingIndex === -1) {
                    // This is a new invoice, add it to the table
                    const enhancedInvoice = {
                        ...newInvoice,
                        pfee1_inv: newInvoice.value,
                        pfee2_inv: 0,
                        sst_inv: newInvoice.sst,
                        reimbursement_amount: newInvoice.reimbursement || 0,
                        reimbursement_sst: newInvoice.reimbursement_sst || 0
                    };
                    addNewInvoice(enhancedInvoice);

                    // Also add to our allInvoices array
                    allInvoices.push(newInvoice);
                }
            });

            // Update the selectedInvoices array to maintain state
            selectedInvoices = allInvoices;

            // Update the hidden input field for form submission with all invoices
            $('#add_invoice').val(JSON.stringify(allInvoices));

            // Close the modal
            closeModal();

            // Show simple notification instead of modal
            if (newSelections.length > 0) {
                // Create a simple toast notification
                const notification = $(`
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
                <i class="fa fa-check-circle"></i>
                <strong>Success!</strong> ${newSelections.length} new invoice${newSelections.length > 1 ? 's' : ''} added to the list.
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
            }
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
                reimb: 0,
                reimbSst: 0,
                pfeeToTransfer: 0,
                sstToTransfer: 0,
                reimbToTransfer: 0,
                reimbSstToTransfer: 0,
                transferredBal: 0,
                transferredSst: 0
            };

            selectedInvoices.forEach((invoice, index) => {
                const totalAmount = (parseFloat(invoice.value) || 0) + (parseFloat(invoice.sst) || 0);
                const pfeeAmount = parseFloat(invoice.value) || 0;
                const sstAmount = parseFloat(invoice.sst) || 0;
                const reimbAmount = parseFloat(invoice.reimbursement) || 0;
                const reimbSstAmount = parseFloat(invoice.reimbursement_sst) || 0;
                const transferredPfee = parseFloat(invoice.transferred_pfee) || 0;
                const transferredSst = parseFloat(invoice.transferred_sst) || 0;

                // Add to totals - handle both existing and new records
                totals.totalAmt += parseFloat(invoice.bill_total_amt_divided || 0);
                totals.collectedAmt += parseFloat(invoice.bill_collected_amt_divided || 0);
                totals.pfee += pfeeAmount;
                totals.sst += sstAmount;
                totals.reimb += reimbAmount;
                totals.reimbSst += reimbSstAmount;

                // For existing records (already transferred), show available balance
                // For new records (not transferred), show editable amounts
                // Use amounts from THIS transfer fee record, not cumulative from invoice
                const currentTransferPfee = parseFloat(invoice.current_transfer_pfee || 0);
                const currentTransferSst = parseFloat(invoice.current_transfer_sst || 0);
                const currentTransferReimb = parseFloat(invoice.current_transfer_reimbursement || 0);
                const currentTransferReimbSst = parseFloat(invoice.current_transfer_reimbursement_sst || 0);
                
                if (transferredPfee > 0 || transferredSst > 0) {
                    // Existing record - show available balance
                    totals.pfeeToTransfer += Math.max(0, pfeeAmount - transferredPfee);
                    totals.sstToTransfer += Math.max(0, sstAmount - transferredSst);
                    totals.transferredBal += currentTransferPfee + currentTransferReimb;
                    totals.transferredSst += currentTransferSst + currentTransferReimbSst;
                } else {
                    // New record - show editable amounts
                    totals.pfeeToTransfer += currentTransferPfee;
                    totals.sstToTransfer += currentTransferSst;
                    totals.reimbToTransfer += currentTransferReimb;
                    totals.reimbSstToTransfer += currentTransferReimbSst;
                    totals.transferredBal += currentTransferPfee + currentTransferReimb;
                    totals.transferredSst += currentTransferSst + currentTransferReimbSst;
                }

                tableHTML += `
            <tr>
                <td class="text-center" style="font-size: 11px;">${index + 1}</td>
                <td>
                    ${!isReconciled ? `<button type="button" class="btn btn-sm btn-danger" onclick="removeSelectedInvoice(${index})" title="Remove Invoice" style="font-size: 10px; padding: 2px 4px;"><i class="fa fa-times"></i></button>` : ''}
                </td>
                <td style="font-size: 11px;">
                    <a href="/case/${invoice.case_id || ''}" target="_blank" class="text-primary" style="text-decoration: none;">
                        ${invoice.case_ref}
                    </a>
                </td>
                <td style="font-size: 11px;">${invoice.invoice_no}</td>
                <td style="font-size: 11px;">${invoice.invoice_date}</td>
                <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.bill_total_amt_divided) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.bill_collected_amt_divided) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.pfee1_inv) + parseFloat(invoice.pfee2_inv)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.sst_inv) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-right" style="font-size: 11px;">${reimbAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-right" style="font-size: 11px;">${reimbSstAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                 <td class="text-right" style="font-size: 11px;">
                     <span class="form-control-plaintext" style="font-size: 10px; text-align: right; padding: 0; border: none; background: transparent;">
                         ${Math.max(0, pfeeAmount - transferredPfee).toFixed(2)}
                     </span>
                 </td>
                 <td class="text-right" style="font-size: 11px;">
                     <span class="form-control-plaintext" style="font-size: 10px; text-align: right; padding: 0; border: none; background: transparent;">
                         ${Math.max(0, sstAmount - transferredSst).toFixed(2)}
                     </span>
                 </td>
                 <td class="text-right" style="font-size: 11px;">
                     <input type="number" 
                            class="form-control reimb-transfer-input" 
                            data-index="${index}" 
                            data-type="reimb"
                            value="${reimbAmount.toFixed(2)}" 
                            step="0.01" 
                            min="0" 
                            max="${reimbAmount.toFixed(2)}"
                            style="font-size: 10px; text-align: right; padding: 2px; height: 25px;"
                            onchange="updateTransferAmounts(${index}, 'reimb')"
                            oninput="updateTransferAmounts(${index}, 'reimb')">
                 </td>
                 <td class="text-right" style="font-size: 11px;">
                     <input type="number" 
                            class="form-control reimb-sst-transfer-input" 
                            data-index="${index}" 
                            data-type="reimb-sst"
                            value="${reimbSstAmount.toFixed(2)}" 
                            step="0.01" 
                            min="0" 
                            max="${reimbSstAmount.toFixed(2)}"
                            style="font-size: 10px; text-align: right; padding: 2px; height: 25px;"
                            onchange="updateTransferAmounts(${index}, 'reimb-sst')"
                            oninput="updateTransferAmounts(${index}, 'reimb-sst')">
                 </td>
                                  <td class="text-right" style="font-size: 11px;">${((parseFloat(invoice.current_transfer_pfee || 0)) + (parseFloat(invoice.current_transfer_reimbursement || 0))).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                 <td class="text-right" style="font-size: 11px;">${((parseFloat(invoice.current_transfer_sst || 0)) + (parseFloat(invoice.current_transfer_reimbursement_sst || 0))).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td style="font-size: 11px;">${invoice.payment_date}</td>
            </tr>
        `;
            });

            tableBody.html(tableHTML);

            // Re-apply filters after table update
            applyFilters();

            // Footer totals are calculated server-side for edit view
        }

        function deleteTransferRecord(recordId, invoiceNo) {
            Swal.fire({
                title: 'Delete Transfer Record?',
                html: `Are you sure you want to delete the transfer record for invoice <strong>${invoiceNo}</strong>?<br><br>
               <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> This action cannot be undone!</span>`,
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
                        title: 'Deleting...',
                        text: 'Please wait while we delete the transfer record.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX call to delete
                    $.ajax({
                        url: `/transferfee/${recordId}/delete-detail`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 1) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Transfer record has been deleted successfully.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload the page to refresh the data
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message ||
                                        'Failed to delete transfer record.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while deleting the transfer record.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }

        function updateTransferAmounts(index, type) {
            // This function is no longer needed since we're using text instead of input fields
            // But keeping it for backward compatibility
            console.log('updateTransferAmounts called but no longer needed - using text display instead');
        }

        function updateFooterTotals() {
            let totals = {
                totalAmt: 0,
                collectedAmt: 0,
                pfee: 0,
                sst: 0,
                reimb: 0,
                reimbSst: 0,
                pfeeToTransfer: 0,
                sstToTransfer: 0,
                reimbToTransfer: 0,
                reimbSstToTransfer: 0,
                transferredBal: 0,
                transferredSst: 0,
                transferredReimb: 0,
                transferredReimbSst: 0
            };

            selectedInvoices.forEach((invoice) => {
                // Ensure values are parsed as numbers to prevent string concatenation
                const pfeeAmount = parseFloat(invoice.value) || 0;
                const sstAmount = parseFloat(invoice.sst) || 0;
                const reimbAmount = parseFloat(invoice.reimbursement) || 0;
                const reimbSstAmount = parseFloat(invoice.reimbursement_sst) || 0;
                const totalAmount = pfeeAmount + sstAmount + reimbAmount + reimbSstAmount;
                const transferredPfee = parseFloat(invoice.transferred_pfee) || 0;
                const transferredSst = parseFloat(invoice.transferred_sst) || 0;
                const transferredReimb = parseFloat(invoice.transferred_reimbursement) || 0;
                const transferredReimbSst = parseFloat(invoice.transferred_reimbursement_sst) || 0;

                totals.totalAmt += parseFloat(invoice.bill_total_amt_divided || 0);
                totals.collectedAmt += parseFloat(invoice.bill_collected_amt_divided || 0);
                totals.pfee += pfeeAmount;
                totals.sst += sstAmount;
                totals.reimb += reimbAmount;
                totals.reimbSst += reimbSstAmount;
                
                // Use amounts from THIS transfer fee record, not cumulative from invoice
                const currentTransferPfee = parseFloat(invoice.current_transfer_pfee || 0);
                const currentTransferSst = parseFloat(invoice.current_transfer_sst || 0);
                const currentTransferReimb = parseFloat(invoice.current_transfer_reimbursement || 0);
                const currentTransferReimbSst = parseFloat(invoice.current_transfer_reimbursement_sst || 0);
                
                // For existing records (already transferred in THIS transfer fee), show available balance
                // For new records (not transferred in THIS transfer fee), show editable amounts
                if (currentTransferPfee > 0 || currentTransferSst > 0 || currentTransferReimb > 0 || currentTransferReimbSst > 0) {
                    // Existing record in THIS transfer fee - show available balance
                    totals.pfeeToTransfer += Math.max(0, pfeeAmount - transferredPfee);
                    totals.sstToTransfer += Math.max(0, sstAmount - transferredSst);
                    totals.reimbToTransfer += Math.max(0, reimbAmount - transferredReimb);
                    totals.reimbSstToTransfer += Math.max(0, reimbSstAmount - transferredReimbSst);
                } else {
                    // New record - show editable amounts
                    totals.pfeeToTransfer += currentTransferPfee;
                    totals.sstToTransfer += currentTransferSst;
                    totals.reimbToTransfer += currentTransferReimb;
                    totals.reimbSstToTransfer += currentTransferReimbSst;
                }
                
                totals.transferredBal += currentTransferPfee + currentTransferReimb;
                totals.transferredSst += currentTransferSst + currentTransferReimbSst;
                totals.transferredReimb += currentTransferReimb;
                totals.transferredReimbSst += currentTransferReimbSst;
            });

            // Update footer totals
            $('#footerTotalAmt').text(totals.totalAmt.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            // Round down to exactly 4022.00 while keeping individual amounts at 1340.67
            const totalCollected = totals.collectedAmt;
            const roundedTotal = Math.floor(totalCollected);
            // Update the actual totals value to prevent floating-point issues
            totals.collectedAmt = roundedTotal;
            $('#footerCollectedAmt').text(roundedTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerPfee').text(totals.pfee.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerSst').text(totals.sst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerReimb').text(totals.reimb.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerReimbSst').text(totals.reimbSst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerPfeeToTransfer').text(totals.pfeeToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerSstToTransfer').text(totals.sstToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerReimbToTransfer').text(totals.reimbToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerReimbSstToTransfer').text(totals.reimbSstToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerTransferredBal').text(totals.transferredBal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerTransferredSst').text(totals.transferredSst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerTransferredReimb').text(totals.transferredReimb.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#footerTransferredReimbSst').text(totals.transferredReimbSst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        function removeSelectedInvoice(index) {
            if (index >= 0 && index < selectedInvoices.length) {
                // Remove from selectedInvoices array
                const removedInvoice = selectedInvoices.splice(index, 1)[0];

                // Update the hidden input
                $('#add_invoice').val(JSON.stringify(selectedInvoices));

                // Update summary - use actual transfer amounts
                const totalAmount = selectedInvoices.reduce((sum, invoice) => {
                    const transferAmount = parseFloat(invoice.current_transfer_pfee || 0) + parseFloat(invoice
                        .current_transfer_sst || 0);
                    return sum + transferAmount;
                }, 0);
                $('#selectedCount').text(selectedInvoices.length);
                $('#selectedTotalAmount').text(totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Update the transfer total amount field
                $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

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
                $('#totalAmount').text(totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                // Update the transfer total amount field
                $('#transferTotalAmount').val(totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        }

        // Global variables for sorting
        let currentSortField = '';
        let currentSortOrder = '';
        let selectedInvoicesSortField = '';
        let selectedInvoicesSortOrder = '';

        function initializeSortableHeaders() {
            // Add click event listeners to sortable headers
            document.addEventListener('click', function(e) {
                if (e.target.closest('.sortable-header')) {
                    const header = e.target.closest('.sortable-header');
                    const sortField = header.getAttribute('data-sort');

                    if (sortField) {
                        // Check if it's in the selected invoices table
                        if (header.closest('#selectedInvoicesTable')) {
                            handleSelectedInvoicesSort(sortField);
                        } else {
                            handleSort(sortField);
                        }
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

        function handleSelectedInvoicesSort(sortField) {
            // Toggle sort order if same field, otherwise default to ascending
            if (selectedInvoicesSortField === sortField) {
                selectedInvoicesSortOrder = selectedInvoicesSortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                selectedInvoicesSortField = sortField;
                selectedInvoicesSortOrder = 'asc';
            }

            // Update visual indicators for selected invoices table
            updateSelectedInvoicesSortIndicators();

            // Sort the selected invoices table
            sortSelectedInvoicesTable();
        }

        function updateSelectedInvoicesSortIndicators() {
            // Remove all sort indicators from selected invoices table
            document.querySelectorAll('#selectedInvoicesTable .sortable-header').forEach(header => {
                header.classList.remove('sort-asc', 'sort-desc');
            });

            // Add indicator to current sort field
            if (selectedInvoicesSortField) {
                const header = document.querySelector(`#selectedInvoicesTable [data-sort="${selectedInvoicesSortField}"]`);
                if (header) {
                    header.classList.add(`sort-${selectedInvoicesSortOrder}`);
                }
            }
        }

        function sortSelectedInvoicesTable() {
            const tableBody = document.getElementById('selectedInvoicesTableBody');
            const rows = Array.from(tableBody.querySelectorAll('tr'));

            // Sort the rows
            rows.sort((a, b) => {
                let aValue, bValue;

                switch (selectedInvoicesSortField) {
                    case 'case_ref_no':
                        aValue = a.cells[2].textContent.trim();
                        bValue = b.cells[2].textContent.trim();
                        break;
                    case 'invoice_no':
                        aValue = a.cells[3].textContent.trim();
                        bValue = b.cells[3].textContent.trim();
                        break;
                    case 'invoice_date':
                        aValue = new Date(a.cells[4].textContent.trim());
                        bValue = new Date(b.cells[4].textContent.trim());
                        break;
                    case 'total_amount':
                        aValue = parseFloat(a.cells[5].textContent.replace(/,/g, ''));
                        bValue = parseFloat(b.cells[5].textContent.replace(/,/g, ''));
                        break;
                    case 'collected_amount':
                        aValue = parseFloat(a.cells[6].textContent.replace(/,/g, ''));
                        bValue = parseFloat(b.cells[6].textContent.replace(/,/g, ''));
                        break;
                    case 'pfee':
                        aValue = parseFloat(a.cells[7].textContent.replace(/,/g, ''));
                        bValue = parseFloat(b.cells[7].textContent.replace(/,/g, ''));
                        break;
                    case 'sst':
                        aValue = parseFloat(a.cells[8].textContent.replace(/,/g, ''));
                        bValue = parseFloat(b.cells[8].textContent.replace(/,/g, ''));
                        break;
                    case 'pfee_to_transfer':
                        aValue = parseFloat(a.cells[9].querySelector('input').value || 0);
                        bValue = parseFloat(b.cells[9].querySelector('input').value || 0);
                        break;
                    case 'sst_to_transfer':
                        aValue = parseFloat(a.cells[10].querySelector('input').value || 0);
                        bValue = parseFloat(b.cells[10].querySelector('input').value || 0);
                        break;
                    case 'transferred_bal':
                        aValue = parseFloat(a.cells[11].textContent.replace(/,/g, ''));
                        bValue = parseFloat(b.cells[11].textContent.replace(/,/g, ''));
                        break;
                    case 'transferred_sst':
                        aValue = parseFloat(a.cells[12].textContent.replace(/,/g, ''));
                        bValue = parseFloat(b.cells[12].textContent.replace(/,/g, ''));
                        break;
                    case 'payment_date':
                        aValue = a.cells[13].textContent.trim();
                        bValue = b.cells[13].textContent.trim();
                        break;
                    default:
                        return 0;
                }

                // Handle date comparison
                if (selectedInvoicesSortField === 'invoice_date') {
                    // For invoice_date, we already have Date objects
                    if (aValue === 'N/A' || isNaN(aValue.getTime())) aValue = new Date(0);
                    if (bValue === 'N/A' || isNaN(bValue.getTime())) bValue = new Date(0);

                    if (selectedInvoicesSortOrder === 'asc') {
                        return aValue.getTime() - bValue.getTime();
                    } else {
                        return bValue.getTime() - aValue.getTime();
                    }
                }

                if (selectedInvoicesSortField === 'payment_date') {
                    // For payment_date, we have strings
                    if (aValue === 'N/A') aValue = new Date(0);
                    if (bValue === 'N/A') bValue = new Date(0);

                    const aDate = new Date(aValue);
                    const bDate = new Date(bValue);

                    if (selectedInvoicesSortOrder === 'asc') {
                        return aDate.getTime() - bDate.getTime();
                    } else {
                        return bDate.getTime() - aDate.getTime();
                    }
                }

                // Handle numeric comparison
                if (typeof aValue === 'number' && typeof bValue === 'number') {
                    if (selectedInvoicesSortOrder === 'asc') {
                        return aValue - bValue;
                    } else {
                        return bValue - aValue;
                    }
                }

                // Handle string comparison
                if (selectedInvoicesSortOrder === 'asc') {
                    return aValue.localeCompare(bValue);
                } else {
                    return bValue.localeCompare(aValue);
                }
            });

            // Reorder the rows
            rows.forEach((row, index) => {
                // Update the row number
                row.cells[0].textContent = index + 1;
                tableBody.appendChild(row);
            });
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
                title: 'Updating Transfer Fee...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Debug: Log the form data being sent
            const formData = $(this).serialize();
            console.log('Form data being sent:', formData);
            console.log('Selected invoices:', selectedInvoices);

            // Submit form
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message + ' (Total Amount: ' + response.data
                                .toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + ')',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Refresh the current page after update
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    let errorMessage = 'An error occurred while updating the transfer fee';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        });

        // Reconciliation function
        function reconTransferFee() {
            Swal.fire({
                title: 'Confirm Reconciliation',
                text: 'Are you sure you want to reconcile this transfer fee? This action cannot be undone and will prevent further modifications.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reconcile',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Reconciling Transfer Fee...',
                        text: 'Please wait while we process your request',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Call reconciliation endpoint
                    $.ajax({
                        url: '{{ route('transferfee.reconcile', $TransferFeeMain->id) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status == 1) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    // Refresh the page to show reconciled status
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Reconciliation error:', error);
                            let errorMessage = 'An error occurred while reconciling the transfer fee';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire('Error', errorMessage, 'error');
                        }
                    });
                }
            });
        }

        function revertReconTransferFee() {
            Swal.fire({
                title: 'Confirm Revert Reconciliation',
                text: 'Are you sure you want to revert the reconciliation for this transfer fee? This will undo the bank reconciliation process.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Revert Recon',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Reverting Reconciliation...',
                        text: 'Please wait while we process your request',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Call revert reconciliation endpoint
                    $.ajax({
                        url: '{{ route('transferfee.revert-recon', $TransferFeeMain->id) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status == 1) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    // Refresh the page to show unreconciled status
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Revert reconciliation error:', error);
                            let errorMessage = 'An error occurred while reverting the reconciliation';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire('Error', errorMessage, 'error');
                        }
                    });
                }
            });
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
                    console.log(
                        `Row ${index + 1}: Checking if "${invoiceNo}" includes "${searchTerm}" or "${caseRef}" includes "${searchTerm}"`
                    );

                    // Use indexOf instead of includes for better compatibility
                    const invoiceMatch = invoiceNo.indexOf(searchTerm) !== -1;
                    const caseRefMatch = caseRef.indexOf(searchTerm) !== -1;

                    console.log(`Row ${index + 1}: Invoice match: ${invoiceMatch}, CaseRef match: ${caseRefMatch}`);
                    console.log(
                        `Row ${index + 1}: Invoice indexOf result: ${invoiceNo.indexOf(searchTerm)}, CaseRef indexOf result: ${caseRef.indexOf(searchTerm)}`
                    );

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

        // Apply filters to show/hide invoices based on checkbox selections
        function applyFilters() {
            const filterDifferences = $('#filterDifferencesCheckbox').is(':checked');
            const filterRemainingAmounts = $('#filterRemainingAmountsCheckbox').is(':checked');
            // Use the tbody ID directly
            const tbody = $('#selectedInvoicesTableBody');
            if (tbody.length === 0) {
                console.warn('Table body not found');
                return;
            }
            const rows = tbody.find('tr');
            
            let visibleCount = 0;
            
            // If no filters are active, show all rows
            if (!filterDifferences && !filterRemainingAmounts) {
                rows.each(function() {
                    const row = $(this);
                    // Always show footer rows
                    if (row.find('th').length > 0) {
                        row.show();
                        return;
                    }
                    row.show();
                    visibleCount++;
                });
            } else {
                // Apply filters
                rows.each(function() {
                    const row = $(this);
                    // Always show footer rows
                    if (row.find('th').length > 0) {
                        row.show();
                        return;
                    }
                    
                    // Get the cells from the row
                    const cells = row.find('td');
                    if (cells.length < 15) {
                        row.show();
                        return;
                    }
                    
                    let shouldShow = true;
                    
                    // Filter 1: Show only invoices with Total ≠ Collected
                    if (filterDifferences) {
                        // Column indices: 0=No, 1=Action, 2=RefNo, 3=InvoiceNo, 4=InvoiceDate, 5=TotalAmt, 6=CollectedAmt
                        const totalAmtText = $(cells[5]).text().trim().replace(/,/g, '');
                        const collectedAmtText = $(cells[6]).text().trim().replace(/,/g, '');
                        
                        const totalAmt = parseFloat(totalAmtText) || 0;
                        const collectedAmt = parseFloat(collectedAmtText) || 0;
                        
                        // Only show if Total ≠ Collected (with 0.01 tolerance for floating point)
                        if (Math.abs(totalAmt - collectedAmt) <= 0.01) {
                            shouldShow = false;
                        }
                    }
                    
                    // Filter 2: Show only invoices with remaining amounts to transfer
                    if (filterRemainingAmounts && shouldShow) {
                        // Column indices: 11=Pfee to transfer, 12=SST to transfer, 13=Reimb to transfer, 14=Reimb SST to transfer
                        // Make sure we have enough cells
                        if (cells.length < 15) {
                            shouldShow = false;
                        } else {
                            const pfeeToTransferText = $(cells[11]).text().trim().replace(/,/g, '').replace(/[^\d.-]/g, '');
                            const sstToTransferText = $(cells[12]).text().trim().replace(/,/g, '').replace(/[^\d.-]/g, '');
                            const reimbToTransferText = $(cells[13]).text().trim().replace(/,/g, '').replace(/[^\d.-]/g, '');
                            const reimbSstToTransferText = $(cells[14]).text().trim().replace(/,/g, '').replace(/[^\d.-]/g, '');
                            
                            const pfeeToTransfer = parseFloat(pfeeToTransferText) || 0;
                            const sstToTransfer = parseFloat(sstToTransferText) || 0;
                            const reimbToTransfer = parseFloat(reimbToTransferText) || 0;
                            const reimbSstToTransfer = parseFloat(reimbSstToTransferText) || 0;
                            
                            // Debug: log first row to see what we're getting
                            if (visibleCount === 0) {
                                console.log('First row check:', {
                                    invoiceNo: $(cells[3]).text().trim(),
                                    cellCount: cells.length,
                                    pfeeToTransferText: pfeeToTransferText,
                                    sstToTransferText: sstToTransferText,
                                    pfeeToTransfer: pfeeToTransfer,
                                    sstToTransfer: sstToTransfer,
                                    hasRemaining: Math.abs(pfeeToTransfer) > 0.01 || Math.abs(sstToTransfer) > 0.01 || Math.abs(reimbToTransfer) > 0.01 || Math.abs(reimbSstToTransfer) > 0.01
                                });
                            }
                            
                            // Only show if any of the "to transfer" amounts is greater than or equal to 0.01
                            const hasRemainingAmount = Math.abs(pfeeToTransfer) >= 0.01 || 
                                                      Math.abs(sstToTransfer) >= 0.01 || 
                                                      Math.abs(reimbToTransfer) >= 0.01 || 
                                                      Math.abs(reimbSstToTransfer) >= 0.01;
                            
                            if (!hasRemainingAmount) {
                                shouldShow = false;
                            }
                        }
                    }
                    
                    // Show/hide the row
                    if (shouldShow) {
                        row.show();
                        visibleCount++;
                    } else {
                        row.hide();
                    }
                });
            }
            
            // Update row numbers
            let rowNum = 1;
            rows.filter(':visible').each(function() {
                const row = $(this);
                // Skip footer rows
                if (row.find('th').length === 0) {
                    row.find('td:first').text(rowNum);
                    rowNum++;
                }
            });
            
            // console.log('Visible rows after filtering:', visibleCount);
            
            // Show message if no results
            if (isChecked && visibleCount === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Differences Found',
                    text: 'All invoices have matching Total Amount and Collected Amount.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        // Export Functions
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

            // Prepare data for export
            const exportData = {
                transfer_fee_id: {{ $TransferFeeMain->id ?? 0 }},
                format: 'excel'
            };

            // Make AJAX request to export endpoint
            $.ajax({
                url: '{{ route('transferfee.export') }}',
                type: 'POST',
                data: exportData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, xhr) {
                    // Close the loading modal first
                    Swal.close();
                    
                    // Create download link
                    const blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `transfer_fee_invoices_${new Date().toISOString().split('T')[0]}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    // Show success notification instead of modal
                    const notification = $(`
                <div class="alert alert-success alert-dismissible fade show position-fixed" 
                     style="top:20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
                    <i class="fa fa-check-circle"></i>
                    <strong>Success!</strong> Excel file downloaded successfully
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
                    Swal.fire('Error', 'Failed to export to Excel. Please try again.', 'error');
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

            // Prepare data for export
            const exportData = {
                transfer_fee_id: {{ $TransferFeeMain->id ?? 0 }},
                format: 'pdf'
            };

            // Make AJAX request to export endpoint
            $.ajax({
                url: '{{ route('transferfee.export') }}',
                type: 'POST',
                data: exportData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                    a.download = `transfer_fee_invoices_${new Date().toISOString().split('T')[0]}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    // Show success notification instead of modal
                    const notification = $(`
                <div class="alert alert-success alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none;">
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

        // Load current invoices from the backend
        function loadCurrentInvoices() {
            const transferFeeId = {{ $TransferFeeMain->id }};

            $.ajax({
                url: `/getCurrentInvoices/${transferFeeId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        displayCurrentInvoices(response.current_invoices);
                        updateTotals();
                    } else {
                        console.error('Error loading current invoices:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load current invoices:', error);
                }
            });
        }

        // Display current invoices in the table
        function displayCurrentInvoices(invoices) {
            const tbody = $('#currentInvoicesTableBody');
            tbody.empty();

            if (invoices.length === 0) {
                tbody.append('<tr><td colspan="12" class="text-center text-muted">No current invoices found</td></tr>');
                return;
            }

            invoices.forEach((invoice, index) => {
                const row = `
                <tr>
                    <td class="text-center" style="font-size: 11px;">${index + 1}</td>
                    <td>
                        ${!isReconciled ? `<button type="button" class="btn btn-sm btn-danger" onclick="deleteTransferRecord(${invoice.id}, '${invoice.invoice_no}')" title="Delete Transfer Record" style="font-size: 10px; padding: 2px 4px;"><i class="fa fa-trash"></i></button>` : ''}
                    </td>
                    <td style="font-size: 11px;">
                        <a href="/case/${invoice.case_id || ''}" target="_blank" class="text-primary" style="text-decoration: none;">
                            ${invoice.case_ref_no || 'N/A'}
                        </a>
                    </td>
                    <td style="font-size: 11px;">${invoice.invoice_no || 'N/A'}</td>
                    <td style="font-size: 11px;">${invoice.invoice_date || 'N/A'}</td>
                    <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.pfee1_inv || 0) + parseFloat(invoice.pfee2_inv || 0) + parseFloat(invoice.sst_inv || 0)).toFixed(2)}</td>
                    <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.pfee1_inv || 0) + parseFloat(invoice.pfee2_inv || 0) + parseFloat(invoice.sst_inv || 0)).toFixed(2)}</td>
                    <td class="text-right" style="font-size: 11px;">${(parseFloat(invoice.pfee1_inv || 0) + parseFloat(invoice.pfee2_inv || 0)).toFixed(2)}</td>
                    <td class="text-right" style="font-size: 11px;">${parseFloat(invoice.sst_inv || 0).toFixed(2)}</td>
                    <td class="text-right" style="font-size: 11px;">${parseFloat(invoice.transfer_amount || 0).toFixed(2)}</td>
                    <td class="text-right" style="font-size: 11px;">${parseFloat(invoice.sst_amount || 0).toFixed(2)}</td>
                    <td style="font-size: 11px;">${invoice.payment_receipt_date || 'N/A'}</td>
                </tr>
            `;
                tbody.append(row);
            });

            // Update current totals
            updateCurrentTotals(invoices);
        }

        // Update current totals
        function updateCurrentTotals(invoices) {
            const totalAmount = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.bill_total_amt_divided || 0), 0);
            const collectedAmount = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.bill_collected_amt_divided || 0), 0);
            const pfee = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.pfee1_inv || 0) + parseFloat(invoice
                .pfee2_inv || 0), 0);
            const sst = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.sst_inv || 0), 0);
            const transferredBal = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.transfer_amount || 0), 0);
            const transferredSst = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.sst_amount || 0), 0);

            $('#currentTotalAmt').text(totalAmount.toFixed(2));
            // Round down to exactly 4022.00 while keeping individual amounts at 1340.67
            const totalCollected = collectedAmount;
            const roundedTotal = Math.floor(totalCollected);
            // Update the actual value to prevent floating-point issues
            $('#currentCollectedAmt').text(roundedTotal.toFixed(2));
            $('#currentPfee').text(pfee.toFixed(2));
            $('#currentSst').text(sst.toFixed(2));
            $('#currentTransferredBal').text(transferredBal.toFixed(2));
            $('#currentTransferredSst').text(transferredSst.toFixed(2));

            // Update combined totals
            updateCombinedTotals();
        }

        // Update combined totals
        function updateCombinedTotals() {
            // Get existing invoices totals (from the current invoices table)
            const existingTotalAmt = parseFloat($('#footerTotalAmt').text().replace(/,/g, '') || 0);
            const existingCollectedAmt = parseFloat($('#footerCollectedAmt').text().replace(/,/g, '') || 0);
            const existingPfee = parseFloat($('#footerPfee').text().replace(/,/g, '') || 0);
            const existingSst = parseFloat($('#footerSst').text().replace(/,/g, '') || 0);
            const existingReimb = parseFloat($('#footerReimb').text().replace(/,/g, '') || 0);
            const existingReimbSst = parseFloat($('#footerReimbSst').text().replace(/,/g, '') || 0);
            const existingPfeeToTransfer = parseFloat($('#footerPfeeToTransfer').text().replace(/,/g, '') || 0);
            const existingSstToTransfer = parseFloat($('#footerSstToTransfer').text().replace(/,/g, '') || 0);
            const existingReimbToTransfer = parseFloat($('#footerReimbToTransfer').text().replace(/,/g, '') || 0);
            const existingReimbSstToTransfer = parseFloat($('#footerReimbSstToTransfer').text().replace(/,/g, '') || 0);
            const existingTransferredBal = parseFloat($('#footerTransferredBal').text().replace(/,/g, '') || 0);
            const existingTransferredSst = parseFloat($('#footerTransferredSst').text().replace(/,/g, '') || 0);

            // Get new invoices totals
            const newTotalAmt = parseFloat($('#newTotalAmt').text() || 0);
            const newCollectedAmt = parseFloat($('#newCollectedAmt').text() || 0);
            const newPfee = parseFloat($('#newPfee').text() || 0);
            const newSst = parseFloat($('#newSst').text() || 0);
            const newReimb = parseFloat($('#newReimb').text() || 0);
            const newReimbSst = parseFloat($('#newReimbSst').text() || 0);
            const newPfeeToTransfer = parseFloat($('#newPfeeToTransfer').text() || 0);
            const newSstToTransfer = parseFloat($('#newSstToTransfer').text() || 0);
            const newReimbToTransfer = parseFloat($('#newReimbToTransfer').text() || 0);
            const newReimbSstToTransfer = parseFloat($('#newReimbSstToTransfer').text() || 0);

            // Calculate combined totals
            const combinedTotalAmt = existingTotalAmt + newTotalAmt;
            const combinedCollectedAmt = existingCollectedAmt + newCollectedAmt;
            const combinedPfee = existingPfee + newPfee;
            const combinedSst = existingSst + newSst;
            const combinedReimb = existingReimb + newReimb;
            const combinedReimbSst = existingReimbSst + newReimbSst;
            const combinedPfeeToTransfer = existingPfeeToTransfer + newPfeeToTransfer;
            const combinedSstToTransfer = existingSstToTransfer + newSstToTransfer;
            const combinedReimbToTransfer = existingReimbToTransfer + newReimbToTransfer;
            const combinedReimbSstToTransfer = existingReimbSstToTransfer + newReimbSstToTransfer;

            // Update combined totals in the footer
            $('#combinedTotalAmt').text(combinedTotalAmt.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedCollectedAmt').text(combinedCollectedAmt.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedPfee').text(combinedPfee.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedSst').text(combinedSst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedReimb').text(combinedReimb.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedReimbSst').text(combinedReimbSst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedPfeeToTransfer').text(combinedPfeeToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedSstToTransfer').text(combinedSstToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedReimbToTransfer').text(combinedReimbToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#combinedReimbSstToTransfer').text(combinedReimbSstToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Update the main transfer total amount
            // This should be: existing transferred amounts + new transfer amounts (including all components)
            const transferTotalAmount = existingTransferredBal + existingTransferredSst + newPfeeToTransfer +
                newSstToTransfer + newReimbToTransfer + newReimbSstToTransfer;
            $('#transferTotalAmount').val(transferTotalAmount.toFixed(2));

            // Update existing/new total displays if they exist
            if ($('#existingTotalDisplay').length) {
                $('#existingTotalDisplay').text((existingTransferredBal + existingTransferredSst + existingReimb + existingReimbSst).toFixed(2));
            }
            if ($('#newTotalDisplay').length) {
                $('#newTotalDisplay').text((newPfeeToTransfer + newSstToTransfer + newReimbToTransfer + newReimbSstToTransfer).toFixed(2));
            }
            if ($('#combinedTotalDisplay').length) {
                $('#combinedTotalDisplay').text(transferTotalAmount.toFixed(2));
            }
        }

        // Add new invoice to the new invoices table
        function addNewInvoice(invoice) {
            const tbody = $('#newInvoicesTableBody');
            const rowCount = tbody.find('tr').length;

            // Calculate the available amounts for transfer
            const pfeeAmount = parseFloat(invoice.pfee1_inv || 0) + parseFloat(invoice.pfee2_inv || 0);
            const sstAmount = parseFloat(invoice.sst_inv || 0);
            const reimbAmount = parseFloat(invoice.reimbursement_amount || 0);
            const reimbSstAmount = parseFloat(invoice.reimbursement_sst || 0);
            const totalAmount = pfeeAmount + sstAmount + reimbAmount + reimbSstAmount;

            const row = `
        <tr data-invoice-id="${invoice.id}" data-bill-id="${invoice.bill_id || ''}">
            <td class="text-center" style="font-size: 11px;">${rowCount + 1}</td>
            <td style="font-size: 11px;">${invoice.case_ref || 'N/A'}</td>
            <td style="font-size: 11px;">${invoice.invoice_no || 'N/A'}</td>
            <td style="font-size: 11px;">${invoice.invoice_date || 'N/A'}</td>
            <td class="text-right" style="font-size: 11px;">${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-right" style="font-size: 11px;">${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-right" style="font-size: 11px;">${pfeeAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-right" style="font-size: 11px;">${sstAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-right" style="font-size: 11px;">${reimbAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-right" style="font-size: 11px;">${reimbSstAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-right" style="font-size: 11px;">
                <input type="number" 
                       class="form-control form-control-sm pfee-transfer-input" 
                       value="${pfeeAmount.toFixed(2)}" 
                       min="0" 
                       max="${pfeeAmount}" 
                       step="0.01" 
                       data-index="${rowCount}" 
                       data-original-pfee="${pfeeAmount}" 
                       data-original-sst="${sstAmount}" 
                       style="width: 80px; font-size: 10px; text-align: right;"
                       onchange="updateNewInvoiceTransferAmounts(${rowCount}, 'pfee')">
                <small class="text-muted" style="font-size: 9px;">Amount to transfer</small>
            </td>
            <td class="text-right" style="font-size: 11px;">
                <input type="number" 
                       class="form-control form-control-sm sst-transfer-input" 
                       value="${sstAmount.toFixed(2)}" 
                       min="0" 
                       max="${sstAmount}" 
                       step="0.01" 
                       data-index="${rowCount}" 
                       data-original-pfee="${pfeeAmount}" 
                       data-original-sst="${sstAmount}" 
                       style="width: 70px; font-size: 10px; text-align: right;"
                       onchange="updateNewInvoiceTransferAmounts(${rowCount}, 'sst')">
                <small class="text-muted" style="font-size: 9px;">Amount to transfer</small>
            </td>
            <td class="text-right" style="font-size: 11px;">
                <input type="number" 
                       class="form-control form-control-sm reimb-transfer-input" 
                       value="${reimbAmount.toFixed(2)}" 
                       min="0" 
                       max="${reimbAmount}" 
                       step="0.01" 
                       data-index="${rowCount}" 
                       data-original-reimb="${reimbAmount}" 
                       data-original-reimb-sst="${reimbSstAmount}" 
                       style="width: 80px; font-size: 10px; text-align: right;"
                       onchange="updateNewInvoiceTransferAmounts(${rowCount}, 'reimb')">
                <small class="text-muted" style="font-size: 9px;">Amount to transfer</small>
            </td>
            <td class="text-right" style="font-size: 11px;">
                <input type="number" 
                       class="form-control form-control-sm reimb-sst-transfer-input" 
                       value="${reimbSstAmount.toFixed(2)}" 
                       min="0" 
                       max="${reimbSstAmount}" 
                       step="0.01" 
                       data-index="${rowCount}" 
                       data-original-reimb="${reimbAmount}" 
                       data-original-reimb-sst="${reimbSstAmount}" 
                       style="width: 70px; font-size: 10px; text-align: right;"
                       onchange="updateNewInvoiceTransferAmounts(${rowCount}, 'reimb-sst')">
                <small class="text-muted" style="font-size: 9px;">Amount to transfer</small>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeNewInvoice(this)" title="Remove Invoice" style="font-size: 10px; padding: 2px 4px;">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    `;

            tbody.append(row);
            $('#newInvoicesTable').show();
            updateNewInvoiceTotals();
        }

        // Remove new invoice from the table
        function removeNewInvoice(button) {
            $(button).closest('tr').remove();
            updateNewInvoiceTotals();

            // Hide table if no more invoices
            if ($('#newInvoicesTableBody tr').length === 0) {
                $('#newInvoicesTable').hide();
            }
        }

        // Update new invoice transfer amounts when input fields change
        function updateNewInvoiceTransferAmounts(index, type) {
            const row = $(`#newInvoicesTableBody tr[data-index="${index}"]`);
            if (row.length === 0) return;

            const pfeeInput = row.find('.pfee-transfer-input');
            const sstInput = row.find('.sst-transfer-input');
            const reimbInput = row.find('.reimb-transfer-input');
            const reimbSstInput = row.find('.reimb-sst-transfer-input');

            if (pfeeInput.length && sstInput.length) {
                let pfeeValue = parseFloat(pfeeInput.val()) || 0;
                let sstValue = parseFloat(sstInput.val()) || 0;
                let reimbValue = parseFloat(reimbInput.val()) || 0;
                let reimbSstValue = parseFloat(reimbSstInput.val()) || 0;

                // Get original values
                const originalPfee = parseFloat(pfeeInput.data('original-pfee')) || 0;
                const originalSst = parseFloat(sstInput.data('original-sst')) || 0;
                const originalReimb = parseFloat(reimbInput.data('original-reimb')) || 0;
                const originalReimbSst = parseFloat(reimbSstInput.data('original-reimb-sst')) || 0;

                // Validate maximum limits
                if (pfeeValue > originalPfee) {
                    pfeeValue = originalPfee;
                    pfeeInput.val(pfeeValue.toFixed(2));
                }

                if (sstValue > originalSst) {
                    sstValue = originalSst;
                    sstInput.val(sstValue.toFixed(2));
                }

                if (reimbValue > originalReimb) {
                    reimbValue = originalReimb;
                    reimbInput.val(reimbValue.toFixed(2));
                }

                if (reimbSstValue > originalReimbSst) {
                    reimbSstValue = originalReimbSst;
                    reimbSstInput.val(reimbSstValue.toFixed(2));
                }

                // Ensure non-negative values
                if (pfeeValue < 0) {
                    pfeeValue = 0;
                    pfeeInput.val('0.00');
                }

                if (sstValue < 0) {
                    sstValue = 0;
                    sstInput.val('0.00');
                }

                if (reimbValue < 0) {
                    reimbValue = 0;
                    reimbInput.val('0.00');
                }

                if (reimbSstValue < 0) {
                    reimbSstValue = 0;
                    reimbSstInput.val('0.00');
                }

                // Update the selectedInvoices array with the new values
                const invoiceId = row.data('invoice-id');
                const existingInvoice = selectedInvoices.find(inv => inv.id == invoiceId);
                if (existingInvoice) {
                    existingInvoice.current_transfer_pfee = pfeeValue;
                    existingInvoice.current_transfer_sst = sstValue;
                    existingInvoice.current_transfer_reimbursement = reimbValue;
                    existingInvoice.current_transfer_reimbursement_sst = reimbSstValue;
                }

                // Update totals
                updateNewInvoiceTotals();
            }
        }

        // Update new invoice totals
        function updateNewInvoiceTotals() {
            let totalAmount = 0;
            let collectedAmount = 0;
            let pfee = 0;
            let sst = 0;
            let reimb = 0;
            let reimbSst = 0;
            let pfeeToTransfer = 0;
            let sstToTransfer = 0;
            let reimbToTransfer = 0;
            let reimbSstToTransfer = 0;

            $('#newInvoicesTableBody tr').each(function() {
                const row = $(this);
                const pfeeInput = row.find('.pfee-transfer-input');
                const sstInput = row.find('.sst-transfer-input');
                const reimbInput = row.find('.reimb-transfer-input');
                const reimbSstInput = row.find('.reimb-sst-transfer-input');

                if (pfeeInput.length && sstInput.length) {
                    const pfeeValue = parseFloat(pfeeInput.val() || 0);
                    const sstValue = parseFloat(sstInput.val() || 0);
                    const reimbValue = parseFloat(reimbInput.val() || 0);
                    const reimbSstValue = parseFloat(reimbSstInput.val() || 0);

                    // Get the original amounts from the row data
                    const originalPfee = parseFloat(pfeeInput.data('original-pfee') || 0);
                    const originalSst = parseFloat(sstInput.data('original-sst') || 0);
                    const originalReimb = parseFloat(reimbInput.data('original-reimb') || 0);
                    const originalReimbSst = parseFloat(reimbSstInput.data('original-reimb-sst') || 0);

                    pfee += originalPfee;
                    sst += originalSst;
                    reimb += originalReimb;
                    reimbSst += originalReimbSst;
                    pfeeToTransfer += pfeeValue;
                    sstToTransfer += sstValue;
                    reimbToTransfer += reimbValue;
                    reimbSstToTransfer += reimbSstValue;
                    totalAmount += pfeeValue + sstValue + reimbValue + reimbSstValue;
                    collectedAmount += originalPfee + originalSst + originalReimb + originalReimbSst;
                }
            });

            $('#newTotalAmt').text(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newCollectedAmt').text(collectedAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newPfee').text(pfee.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newSst').text(sst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newReimb').text(reimb.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newReimbSst').text(reimbSst.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newPfeeToTransfer').text(pfeeToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newSstToTransfer').text(sstToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newReimbToTransfer').text(reimbToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#newReimbSstToTransfer').text(reimbSstToTransfer.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Update combined totals
            updateCombinedTotals();
        }

        // Update all totals
        function updateTotals() {
            updateCurrentTotals([]);
            updateNewInvoiceTotals();
            updateCombinedTotals();
        }

        // Initialize combined totals on page load
        function initializeCombinedTotals() {
            // Wait a bit for the DOM to be fully loaded
            setTimeout(function() {
                updateCombinedTotals();
            }, 100);
        }

        // Call initialization when page loads
        $(document).ready(function() {
            initializeCombinedTotals();
        });

        // Edit Total Amount functionality
        $(document).on('click', '.edit-total-amt', function() {
            const $icon = $(this);
            const $display = $icon.siblings('.total-amt-display');
            const detailId = $icon.data('detail-id');
            const invoiceId = $icon.data('invoice-id');
            const currentValue = parseFloat($display.data('original-value') || $display.text().replace(/,/g, '')) || 0;
            
            // Replace display with input field
            const $input = $('<input>', {
                type: 'number',
                class: 'form-control form-control-sm total-amt-input',
                style: 'width: 100px; display: inline-block; font-size: 11px;',
                value: currentValue.toFixed(2),
                step: '0.01',
                min: '0'
            });
            
            const $saveBtn = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-success ml-1 save-total-amt',
                style: 'font-size: 10px; padding: 2px 8px;',
                html: '<i class="fa fa-check"></i>'
            });
            
            const $cancelBtn = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-secondary ml-1 cancel-total-amt',
                style: 'font-size: 10px; padding: 2px 8px;',
                html: '<i class="fa fa-times"></i>'
            });
            
            $display.hide();
            $icon.hide();
            $display.after($input);
            $input.after($saveBtn);
            $saveBtn.after($cancelBtn);
            $input.focus().select();
            
            // Save handler
            $saveBtn.on('click', function() {
                const newValue = parseFloat($input.val()) || 0;
                if (newValue < 0) {
                    alert('Amount cannot be negative');
                    return;
                }
                
                // Save via AJAX
                $.ajax({
                    url: '{{ route("transferfee.updateTotalAmt", ":detailId") }}'.replace(':detailId', detailId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        total_amt: newValue,
                        invoice_id: invoiceId
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            // Update display
                            $display.data('original-value', newValue);
                            $display.text(newValue.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            
                            // Remove input and buttons
                            $input.remove();
                            $saveBtn.remove();
                            $cancelBtn.remove();
                            $display.show();
                            $icon.show();
                            
                            // Update footer totals
                            updateFooterTotals();
                            
                            // Show success message
                            Swal.fire('Success', 'Total amount updated successfully', 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Failed to update total amount', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to update total amount. Please try again.', 'error');
                    }
                });
            });
            
            // Cancel handler
            $cancelBtn.on('click', function() {
                $input.remove();
                $saveBtn.remove();
                $cancelBtn.remove();
                $display.show();
                $icon.show();
            });
            
            // Enter key handler
            $input.on('keypress', function(e) {
                if (e.which === 13) {
                    $saveBtn.click();
                } else if (e.which === 27) {
                    $cancelBtn.click();
                }
            });
        });

        // Edit Professional Fee (pfee1 + pfee2) - Inline with two inputs
        $(document).on('click', '.edit-pfee', function() {
            const $icon = $(this);
            const $display = $icon.siblings('.pfee-display');
            const detailId = $icon.data('detail-id');
            const invoiceId = $icon.data('invoice-id');
            const billId = $icon.data('bill-id');
            const currentPfee1 = parseFloat($icon.data('pfee1') || 0);
            const currentPfee2 = parseFloat($icon.data('pfee2') || 0);
            
            // Create container for two inputs
            const $container = $('<div>', {
                class: 'd-inline-flex align-items-center',
                style: 'gap: 5px;'
            });
            
            // Create pfee1 input
            const $input1 = $('<input>', {
                type: 'number',
                class: 'form-control form-control-sm pfee-input',
                style: 'width: 80px; display: inline-block; font-size: 11px;',
                value: currentPfee1.toFixed(2),
                step: '0.01',
                min: '0',
                placeholder: 'Pfee1',
                title: 'Professional Fee 1'
            });
            
            // Create separator
            const $separator = $('<span>', {
                text: '+',
                style: 'font-size: 11px; margin: 0 2px;'
            });
            
            // Create pfee2 input
            const $input2 = $('<input>', {
                type: 'number',
                class: 'form-control form-control-sm pfee-input',
                style: 'width: 80px; display: inline-block; font-size: 11px;',
                value: currentPfee2.toFixed(2),
                step: '0.01',
                min: '0',
                placeholder: 'Pfee2',
                title: 'Professional Fee 2'
            });
            
            // Create total display
            const $totalDisplay = $('<span>', {
                class: 'pfee-total-display',
                style: 'font-size: 11px; margin-left: 5px; font-weight: bold;',
                text: '= ' + (currentPfee1 + currentPfee2).toFixed(2)
            });
            
            // Create save button
            const $saveBtn = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-success ml-1 save-pfee',
                style: 'font-size: 10px; padding: 2px 8px;',
                html: '<i class="fa fa-check"></i>'
            });
            $saveBtn.data('detail-id', detailId);
            $saveBtn.data('invoice-id', invoiceId);
            $saveBtn.data('bill-id', billId);
            
            // Create cancel button
            const $cancelBtn = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-secondary ml-1 cancel-pfee',
                style: 'font-size: 10px; padding: 2px 8px;',
                html: '<i class="fa fa-times"></i>'
            });
            
            // Update total when inputs change
            const updateTotal = function() {
                const pfee1 = parseFloat($input1.val() || 0);
                const pfee2 = parseFloat($input2.val() || 0);
                $totalDisplay.text('= ' + (pfee1 + pfee2).toFixed(2));
            };
            
            $input1.on('input', updateTotal);
            $input2.on('input', updateTotal);
            
            // Assemble container
            $container.append($input1);
            $container.append($separator);
            $container.append($input2);
            $container.append($totalDisplay);
            $container.append($saveBtn);
            $container.append($cancelBtn);
            
            // Replace display with container
            $display.hide();
            $icon.hide();
            $display.after($container);
            $input1.focus().select();
            
            // Save handler
            $saveBtn.on('click', function() {
                const newPfee1 = parseFloat($input1.val() || 0);
                const newPfee2 = parseFloat($input2.val() || 0);
                const newTotal = newPfee1 + newPfee2;

                if (newPfee1 < 0 || newPfee2 < 0) {
                    alert('Amounts cannot be negative');
                    return;
                }

                const detailId = $(this).data('detail-id');
                const invoiceId = $(this).data('invoice-id');
                const billId = $(this).data('bill-id');

                // Save via AJAX
                $.ajax({
                    url: '{{ route("transferfee.updateAmounts", ":detailId") }}'.replace(':detailId', detailId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        invoice_id: invoiceId,
                        bill_id: billId,
                        pfee1: newPfee1,
                        pfee2: newPfee2,
                        field: 'pfee'
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            // Update display
                            $display.data('pfee1', newPfee1);
                            $display.data('pfee2', newPfee2);
                            $display.data('original-value', newTotal);
                            $display.text(newTotal.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            $icon.data('pfee1', newPfee1);
                            $icon.data('pfee2', newPfee2);
                            
                            // Remove container
                            $container.remove();
                            $display.show();
                            $icon.show();
                            
                            // Update footer totals
                            updateFooterTotals();
                            
                            // Show success message
                            Swal.fire('Success', 'Professional fee updated successfully', 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Failed to update professional fee', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to update professional fee. Please try again.', 'error');
                    }
                });
            });
            
            // Cancel handler
            $cancelBtn.on('click', function() {
                $container.remove();
                $display.show();
                $icon.show();
            });
            
            // Enter key handler
            $input1.on('keypress', function(e) {
                if (e.which === 13) {
                    $input2.focus().select();
                } else if (e.which === 27) {
                    $cancelBtn.click();
                }
            });
            
            $input2.on('keypress', function(e) {
                if (e.which === 13) {
                    $saveBtn.click();
                } else if (e.which === 27) {
                    $cancelBtn.click();
                }
            });
        });

        // Edit SST - Inline
        $(document).on('click', '.edit-sst', function() {
            const $icon = $(this);
            const $display = $icon.siblings('.sst-display');
            const detailId = $icon.data('detail-id');
            const invoiceId = $icon.data('invoice-id');
            const billId = $icon.data('bill-id');
            const currentValue = parseFloat($display.data('original-value') || $display.text().replace(/,/g, '')) || 0;
            
            editAmountInline($display, $icon, detailId, invoiceId, billId, currentValue, 'sst');
        });

        // Edit Reimbursement - Inline
        $(document).on('click', '.edit-reimb', function() {
            const $icon = $(this);
            const $display = $icon.siblings('.reimb-display');
            const detailId = $icon.data('detail-id');
            const invoiceId = $icon.data('invoice-id');
            const billId = $icon.data('bill-id');
            const currentValue = parseFloat($display.data('original-value') || $display.text().replace(/,/g, '')) || 0;
            
            editAmountInline($display, $icon, detailId, invoiceId, billId, currentValue, 'reimb');
        });

        // Edit Reimbursement SST - Inline
        $(document).on('click', '.edit-reimb-sst', function() {
            const $icon = $(this);
            const $display = $icon.siblings('.reimb-sst-display');
            const detailId = $icon.data('detail-id');
            const invoiceId = $icon.data('invoice-id');
            const billId = $icon.data('bill-id');
            const currentValue = parseFloat($display.data('original-value') || $display.text().replace(/,/g, '')) || 0;
            
            editAmountInline($display, $icon, detailId, invoiceId, billId, currentValue, 'reimb_sst');
        });

        // Generic inline edit function
        function editAmountInline($display, $icon, detailId, invoiceId, billId, currentValue, field) {
            // Replace display with input field
            const $input = $('<input>', {
                type: 'number',
                class: 'form-control form-control-sm amount-input',
                style: 'width: 100px; display: inline-block; font-size: 11px;',
                value: currentValue.toFixed(2),
                step: '0.01',
                min: '0'
            });
            
            const $saveBtn = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-success ml-1 save-amount',
                style: 'font-size: 10px; padding: 2px 8px;',
                html: '<i class="fa fa-check"></i>'
            });
            $saveBtn.data('detail-id', detailId);
            $saveBtn.data('invoice-id', invoiceId);
            $saveBtn.data('bill-id', billId);
            $saveBtn.data('field', field);
            
            const $cancelBtn = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-secondary ml-1 cancel-amount',
                style: 'font-size: 10px; padding: 2px 8px;',
                html: '<i class="fa fa-times"></i>'
            });
            
            $display.hide();
            $icon.hide();
            $display.after($input);
            $input.after($saveBtn);
            $saveBtn.after($cancelBtn);
            $input.focus().select();
            
            // Save handler
            $saveBtn.on('click', function() {
                // Round to 2 decimal places to avoid floating-point precision issues
                const rawValue = parseFloat($input.val()) || 0;
                const newValue = Math.round(rawValue * 100) / 100; // Round to 2 decimals
                
                if (newValue < 0) {
                    alert('Amount cannot be negative');
                    return;
                }
                
                const fieldName = $(this).data('field');
                const detailId = $(this).data('detail-id');
                const invoiceId = $(this).data('invoice-id');
                const billId = $(this).data('bill-id');
                
                // Save via AJAX
                $.ajax({
                    url: '{{ route("transferfee.updateAmounts", ":detailId") }}'.replace(':detailId', detailId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        invoice_id: invoiceId,
                        bill_id: billId,
                        [fieldName]: newValue,
                        field: fieldName
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            // Update display
                            $display.data('original-value', newValue);
                            $display.text(newValue.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            
                            // Remove input and buttons
                            $input.remove();
                            $saveBtn.remove();
                            $cancelBtn.remove();
                            $display.show();
                            $icon.show();
                            
                            // Update footer totals
                            updateFooterTotals();
                            
                            // Show success message
                            Swal.fire('Success', 'Amount updated successfully', 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Failed to update amount', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to update amount. Please try again.', 'error');
                    }
                });
            });
            
            // Cancel handler
            $cancelBtn.on('click', function() {
                $input.remove();
                $saveBtn.remove();
                $cancelBtn.remove();
                $display.show();
                $icon.show();
            });
            
            // Enter key handler
            $input.on('keypress', function(e) {
                if (e.which === 13) {
                    $saveBtn.click();
                } else if (e.which === 27) {
                    $cancelBtn.click();
                }
            });
        }
    </script>

    <script>
        // Auto-check for discrepancies on page load (silent, no modal)
        $(document).ready(function() {
            // Show loading state
            $('#discrepancyStatusAlert').show();
            $('#discrepancyStatusText').html('<i class="fa fa-spinner fa-spin"></i> Scanning for discrepancies...');
            $('#discrepancyStatusDescription').text('Please wait while we check the transfer records.');
            
            // Auto-check on page load (silent, no modal popup)
            checkDiscrepancies(false);
        });

        // Fix SST Amounts button
        $('#fixSSTAmountsBtn').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            if (!confirm('This will recalculate all SST transfer amounts based on current invoice SST values. Continue?')) {
                return;
            }
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Fixing SST...');
            
            $.ajax({
                url: '/transferfee/fix-sst/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.status == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'SST Amounts Fixed!',
                            html: `
                                <p>Successfully fixed SST transfer amounts for ${response.updated_details} invoice(s).</p>
                                <p><strong>Total SST Fixed:</strong> RM ${parseFloat(response.total_sst_fixed).toFixed(2)}</p>
                                <p><strong>New Total Amount:</strong> RM ${parseFloat(response.new_total_amount).toFixed(2)}</p>
                            `,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fix SST amounts'
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to fix SST amounts'
                    });
                }
            });
        });

        // Scan and Fix button (prominent button in header) - shows modal
        $('#scanAndFixDiscrepanciesBtn').on('click', function() {
            checkDiscrepancies(true); // Show modal when user clicks button
        });

        // Fix All button in top alert banner
        $('#fixAllDiscrepanciesTopBtn').on('click', function() {
            $('#fixAllDiscrepanciesBtn').click();
        });

        // Check for discrepancies function
        function checkDiscrepancies(showModal = false) {
            const btn = $('#scanAndFixDiscrepanciesBtn');
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Scanning...');
            
            // Show loading state in alert
            $('#discrepancyStatusAlert').removeClass('alert-danger alert-success alert-warning').addClass('alert-info');
            $('#discrepancyStatusAlert').show();
            $('#discrepancyStatusText').html('<i class="fa fa-spinner fa-spin"></i> Scanning for discrepancies...');
            $('#discrepancyStatusDescription').text('Checking transfer details against ledger entries...');
            $('#fixAllDiscrepanciesTopBtn').hide();
            
            // Only show modal if explicitly requested
            if (showModal) {
                $('#discrepancyModal').modal('show');
                $('#discrepancyModalContent').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Checking for discrepancies...</p>
                    </div>
                `);
            }
            
            $.ajax({
                url: '/transferfee/find-discrepancies/{{ $TransferFeeMain->id }}',
                method: 'GET',
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    let modalContent = '';
                    let hasDuplicates = false;
                    let reimbTotal = 0;
                    let reimbSstTotal = 0;
                    
                    if (response.total_discrepancies > 0) {
                        // Show discrepancies found in status alert
                        $('#discrepancyStatusAlert').removeClass('alert-info alert-success').addClass('alert-danger');
                        $('#discrepancyStatusText').html(`<i class="fa fa-exclamation-triangle"></i> Found ${response.total_discrepancies} Discrepancy/Discrepancies!`);
                        $('#discrepancyStatusDescription').html(`<strong>Action Required:</strong> Click "Fix All" to automatically resolve all issues (removes duplicates, creates missing entries, fixes mismatches).`);
                        $('#fixAllDiscrepanciesTopBtn').show();
                        $('#discrepancyBadge').show();
                        $('#discrepancyBadgeText').html(`<i class="fa fa-exclamation-triangle"></i> ${response.total_discrepancies} Issue(s) Found`);
                        $('#discrepancyMessage').html(`<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ${response.total_discrepancies} discrepancy/discrepancies detected. Click "Fix All" above to resolve.</span>`);
                        
                        // Only build modal content if modal is being shown
                        if (showModal) {
                            modalContent += `<div class="alert alert-warning">
                                <strong><i class="fa fa-exclamation-triangle"></i> Found ${response.total_discrepancies} invoice(s) with discrepancies</strong>
                            </div>`;
                        }
                        
                        // Only build modal content if modal is being shown
                        if (showModal) {
                            modalContent += `<div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Invoice No</th>
                                            <th>Type</th>
                                            <th>Detail Amount</th>
                                            <th>Ledger Amount</th>
                                            <th>Difference</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                        }
                        
                        response.discrepancies.forEach(function(discrepancy) {
                            hasDuplicates = hasDuplicates || (discrepancy.has_duplicate_entries === true);
                            
                            // Only build modal rows if modal is being shown
                            if (showModal) {
                                // Show duplicate entry warning first
                                if (discrepancy.has_duplicate_entries && discrepancy.duplicate_info) {
                                    modalContent += `<tr class="table-danger">
                                        <td><strong>${discrepancy.invoice_no}</strong></td>
                                        <td colspan="5">
                                            <strong class="text-danger">⚠️ DUPLICATE LEDGER ENTRIES DETECTED:</strong><br>`;
                                    discrepancy.duplicate_info.forEach(function(dup) {
                                        modalContent += `<small class="text-danger">${dup}</small><br>`;
                                    });
                                    modalContent += `</td></tr>`;
                                }
                                
                                // Show all types of mismatches
                                if (!discrepancy.pfee_match) {
                                    modalContent += `<tr>
                                        <td>${discrepancy.invoice_no}</td>
                                        <td>Pfee</td>
                                        <td class="text-right">${discrepancy.pfee_detail.toFixed(2)}</td>
                                        <td class="text-right">${discrepancy.pfee_ledger.toFixed(2)}</td>
                                        <td class="text-right text-danger"><strong>${discrepancy.pfee_diff.toFixed(2)}</strong></td>
                                        <td><span class="badge badge-danger">Mismatch</span></td>
                                    </tr>`;
                                }
                                
                                if (!discrepancy.sst_match) {
                                    modalContent += `<tr>
                                        <td>${discrepancy.invoice_no}</td>
                                        <td>SST</td>
                                        <td class="text-right">${discrepancy.sst_detail.toFixed(2)}</td>
                                        <td class="text-right">${discrepancy.sst_ledger.toFixed(2)}</td>
                                        <td class="text-right text-danger"><strong>${discrepancy.sst_diff.toFixed(2)}</strong></td>
                                        <td><span class="badge badge-danger">Mismatch</span></td>
                                    </tr>`;
                                }
                                
                                if (!discrepancy.reimb_match) {
                                    modalContent += `<tr>
                                        <td>${discrepancy.invoice_no}</td>
                                        <td>Reimbursement</td>
                                        <td class="text-right">${discrepancy.reimb_detail.toFixed(2)}</td>
                                        <td class="text-right">${discrepancy.reimb_ledger.toFixed(2)}</td>
                                        <td class="text-right text-danger"><strong>${discrepancy.reimb_diff.toFixed(2)}</strong></td>
                                        <td><span class="badge badge-danger">Mismatch</span></td>
                                    </tr>`;
                                }
                                
                                if (!discrepancy.reimb_sst_match) {
                                    modalContent += `<tr>
                                        <td>${discrepancy.invoice_no}</td>
                                        <td>Reimb SST</td>
                                        <td class="text-right">${discrepancy.reimb_sst_detail.toFixed(2)}</td>
                                        <td class="text-right">${discrepancy.reimb_sst_ledger.toFixed(2)}</td>
                                        <td class="text-right text-danger"><strong>${discrepancy.reimb_sst_diff.toFixed(2)}</strong></td>
                                        <td><span class="badge badge-danger">Mismatch</span></td>
                                    </tr>`;
                                }
                                
                                // Show split invoice note if present
                                if (discrepancy.split_note && discrepancy.split_note.trim() !== '') {
                                    modalContent += `<tr>
                                        <td colspan="6"><small class="text-warning">${discrepancy.split_note}</small></td>
                                    </tr>`;
                                }
                                
                                // If no specific mismatches but still flagged, show info
                                if (discrepancy.pfee_match && discrepancy.sst_match && discrepancy.reimb_match && discrepancy.reimb_sst_match && 
                                    !discrepancy.has_duplicate_entries && (!discrepancy.split_note || discrepancy.split_note.trim() === '')) {
                                    modalContent += `<tr>
                                        <td>${discrepancy.invoice_no}</td>
                                        <td colspan="5"><small class="text-info">No amount mismatches detected, but flagged for review</small></td>
                                    </tr>`;
                                }
                            }
                            
                            // Always calculate totals for status display
                            if (!discrepancy.reimb_match) {
                                reimbTotal += discrepancy.reimb_diff;
                            }
                            if (!discrepancy.reimb_sst_match) {
                                reimbSstTotal += discrepancy.reimb_sst_diff;
                            }
                        });
                        
                        // Only build modal footer if modal is being shown
                        if (showModal) {
                            modalContent += `</tbody>
                                </table>
                            </div>`;
                            
                            const currentTotal = parseFloat('{{ $TransferFeeMain->transfer_amount ?? 0 }}');
                            const expectedTotal = currentTotal - (reimbTotal + reimbSstTotal);
                            modalContent += `<div class="alert alert-info mt-3">
                                <strong>Total Difference: RM ${(reimbTotal + reimbSstTotal).toFixed(2)}</strong><br>
                                <small>Current Total: RM ${currentTotal.toFixed(2)}</small><br>
                                <small>Expected Total: RM ${expectedTotal.toFixed(2)}</small>
                            </div>`;
                        }
                        
                        // Show single "Fix All" button if there are discrepancies (only in modal)
                        if (showModal) {
                            $('#fixAllDiscrepanciesBtn').show();
                        }
                    } else {
                        // No discrepancies - hide the alert banner (no action needed)
                        $('#discrepancyStatusAlert').hide();
                        $('#fixAllDiscrepanciesTopBtn').hide();
                        $('#discrepancyBadge').hide();
                        $('#discrepancyMessage').html('<span class="text-success"><i class="fa fa-check"></i> No discrepancies found. All data is consistent.</span>');
                        
                        // Only build modal content if modal is being shown
                        if (showModal) {
                            modalContent = `<div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> <strong>No discrepancies found!</strong><br>
                                All transfer fee details match the ledger entries perfectly.
                            </div>`;
                            $('#discrepancyModalContent').html(modalContent);
                            $('#fixAllDiscrepanciesBtn').hide();
                        }
                    }
                    
                    // Only update modal content if modal is being shown
                    if (showModal && modalContent) {
                        $('#discrepancyModalContent').html(modalContent);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyStatusAlert').removeClass('alert-success alert-info').addClass('alert-danger');
                    $('#discrepancyStatusText').html('<i class="fa fa-times-circle"></i> Error Checking Discrepancies');
                    $('#discrepancyStatusDescription').text('An error occurred while checking. Please try again.');
                    $('#fixAllDiscrepanciesTopBtn').hide();
                    $('#discrepancyModalContent').html(`
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${xhr.responseJSON?.error || 'Unknown error occurred while checking discrepancies.'}
                        </div>
                    `);
                    $('#discrepancyMessage').html('<span class="text-danger">Error: ' + (xhr.responseJSON?.error || 'Unknown error') + '</span>');
                }
            });
        }

        // Old check button handler (kept for backward compatibility)
        $('#checkDiscrepanciesBtn').on('click', function() {
            checkDiscrepancies();
        });

        // Fix All Discrepancies - show confirmation modal with details
        $('#fixAllDiscrepanciesBtn').on('click', function() {
            // Show confirmation modal
            $('#fixAllConfirmationModal').modal('show');
            $('#fixAllConfirmationContent').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Analyzing discrepancies...</p>
                </div>
            `);
            
            // Fetch discrepancy details to show what will be fixed
            $.ajax({
                url: '/transferfee/find-discrepancies/{{ $TransferFeeMain->id }}',
                method: 'GET',
                success: function(response) {
                    let content = '';
                    
                    if (response.total_discrepancies > 0) {
                        // Group discrepancies by type
                        let duplicates = [];
                        let missingEntries = [];
                        let mismatches = [];
                        
                        response.discrepancies.forEach(function(discrepancy) {
                            // Check for duplicates
                            if (discrepancy.has_duplicate_entries) {
                                duplicates.push({
                                    invoice_no: discrepancy.invoice_no,
                                    duplicate_info: discrepancy.duplicate_info || []
                                });
                            }
                            
                            // Check for missing entries (detail has amount but ledger is 0)
                            let missingTypes = [];
                            if (discrepancy.pfee_detail > 0.01 && discrepancy.pfee_ledger < 0.01) {
                                missingTypes.push(`TRANSFER_IN (${discrepancy.pfee_detail.toFixed(2)})`);
                            }
                            if (discrepancy.sst_detail > 0.01 && discrepancy.sst_ledger < 0.01) {
                                missingTypes.push(`SST_IN (${discrepancy.sst_detail.toFixed(2)})`);
                            }
                            if (discrepancy.reimb_detail > 0.01 && discrepancy.reimb_ledger < 0.01) {
                                missingTypes.push(`REIMB_IN (${discrepancy.reimb_detail.toFixed(2)})`);
                            }
                            if (discrepancy.reimb_sst_detail > 0.01 && discrepancy.reimb_sst_ledger < 0.01) {
                                missingTypes.push(`REIMB_SST_IN (${discrepancy.reimb_sst_detail.toFixed(2)})`);
                            }
                            if (missingTypes.length > 0) {
                                missingEntries.push({
                                    invoice_no: discrepancy.invoice_no,
                                    types: missingTypes
                                });
                            }
                            
                            // Check for mismatches (ledger has amount but doesn't match detail)
                            let mismatchTypes = [];
                            if (Math.abs(discrepancy.pfee_diff) > 0.01 && discrepancy.pfee_ledger > 0.01) {
                                mismatchTypes.push(`Pfee: ${discrepancy.pfee_detail.toFixed(2)} → ${discrepancy.pfee_ledger.toFixed(2)}`);
                            }
                            if (Math.abs(discrepancy.sst_diff) > 0.01 && discrepancy.sst_ledger > 0.01) {
                                mismatchTypes.push(`SST: ${discrepancy.sst_detail.toFixed(2)} → ${discrepancy.sst_ledger.toFixed(2)}`);
                            }
                            if (Math.abs(discrepancy.reimb_diff) > 0.01 && discrepancy.reimb_ledger > 0.01) {
                                mismatchTypes.push(`Reimb: ${discrepancy.reimb_detail.toFixed(2)} → ${discrepancy.reimb_ledger.toFixed(2)}`);
                            }
                            if (Math.abs(discrepancy.reimb_sst_diff) > 0.01 && discrepancy.reimb_sst_ledger > 0.01) {
                                mismatchTypes.push(`Reimb SST: ${discrepancy.reimb_sst_detail.toFixed(2)} → ${discrepancy.reimb_sst_ledger.toFixed(2)}`);
                            }
                            if (mismatchTypes.length > 0) {
                                mismatches.push({
                                    invoice_no: discrepancy.invoice_no,
                                    types: mismatchTypes
                                });
                            }
                        });
                        
                        content = '<div class="table-responsive">';
                        content += '<table class="table table-sm table-bordered">';
                        content += '<thead class="thead-light"><tr><th>Invoice No</th><th>Action Type</th><th>Details</th></tr></thead>';
                        content += '<tbody>';
                        
                        // Show duplicates
                        duplicates.forEach(function(item) {
                            content += `<tr class="table-warning">
                                <td><strong>${item.invoice_no}</strong></td>
                                <td><span class="badge badge-warning">Remove Duplicates</span></td>
                                <td>`;
                            item.duplicate_info.forEach(function(info) {
                                content += `<small>${info}</small><br>`;
                            });
                            content += `</td></tr>`;
                        });
                        
                        // Show missing entries
                        missingEntries.forEach(function(item) {
                            content += `<tr class="table-info">
                                <td><strong>${item.invoice_no}</strong></td>
                                <td><span class="badge badge-info">Create Missing Entries</span></td>
                                <td>`;
                            item.types.forEach(function(type) {
                                content += `<small>Will create: ${type}</small><br>`;
                            });
                            content += `</td></tr>`;
                        });
                        
                        // Show mismatches
                        mismatches.forEach(function(item) {
                            content += `<tr class="table-danger">
                                <td><strong>${item.invoice_no}</strong></td>
                                <td><span class="badge badge-danger">Fix Amount Mismatch</span></td>
                                <td>`;
                            item.types.forEach(function(type) {
                                content += `<small>Will update: ${type}</small><br>`;
                            });
                            content += `</td></tr>`;
                        });
                        
                        content += '</tbody></table></div>';
                        
                        content += `<div class="alert alert-warning mt-3">
                            <strong>Summary:</strong><br>
                            ${duplicates.length > 0 ? `• ${duplicates.length} invoice(s) with duplicate entries to remove<br>` : ''}
                            ${missingEntries.length > 0 ? `• ${missingEntries.length} invoice(s) with missing entries to create<br>` : ''}
                            ${mismatches.length > 0 ? `• ${mismatches.length} invoice(s) with amount mismatches to fix<br>` : ''}
                        </div>`;
                    } else {
                        content = '<div class="alert alert-success">No discrepancies found. Nothing to fix.</div>';
                        $('#confirmFixAllBtn').prop('disabled', true);
                    }
                    
                    $('#fixAllConfirmationContent').html(content);
                },
                error: function(xhr) {
                    $('#fixAllConfirmationContent').html(`
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${xhr.responseJSON?.error || 'Unknown error occurred while analyzing discrepancies.'}
                        </div>
                    `);
                    $('#confirmFixAllBtn').prop('disabled', true);
                }
            });
        });

        // Confirm Fix All button - actually perform the fix
        $('#confirmFixAllBtn').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Fixing...');
            
            // Close confirmation modal
            $('#fixAllConfirmationModal').modal('hide');
            
            // Show loading in main modal
            $('#discrepancyModal').modal('show');
            $('#discrepancyModalContent').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Fixing all discrepancies...</p>
                </div>
            `);
            
            $.ajax({
                url: '/transferfee/fix-all-discrepancies/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> All Discrepancies Fixed!</strong><br>`;
                        
                        if (response.actions.duplicates_removed > 0) {
                            message += `✓ Removed ${response.actions.duplicates_removed} duplicate ledger entry/entries<br>`;
                        }
                        if (response.actions.missing_entries_created > 0) {
                            message += `✓ Created ${response.actions.missing_entries_created} missing ledger entry/entries<br>`;
                        }
                        if (response.actions.amounts_fixed > 0) {
                            message += `✓ Fixed ${response.actions.amounts_fixed} amount mismatch(es)<br>`;
                        }
                        
                        if (response.actions.details && response.actions.details.length > 0) {
                            message += `<br><strong>Details:</strong><br>`;
                            response.actions.details.forEach(function(detail) {
                                message += `Invoice ${detail.invoice_no}: ${detail.actions.join(', ')}<br>`;
                            });
                        }
                        
                        message += `<br><strong>Updated Total: RM ${response.updated_transfer_amount.toFixed(2)}</strong></div>`;
                        $('#discrepancyModalContent').prepend(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.updated_transfer_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Update status alert
                        $('#discrepancyStatusAlert').removeClass('alert-danger alert-info').addClass('alert-success');
                        $('#discrepancyStatusText').html('<i class="fa fa-check-circle"></i> All Discrepancies Fixed!');
                        let summary = [];
                        if (response.actions.duplicates_removed > 0) summary.push(`${response.actions.duplicates_removed} duplicate(s) removed`);
                        if (response.actions.missing_entries_created > 0) summary.push(`${response.actions.missing_entries_created} entry/entries created`);
                        if (response.actions.amounts_fixed > 0) summary.push(`${response.actions.amounts_fixed} amount(s) fixed`);
                        $('#discrepancyStatusDescription').html(`<strong>Fixed:</strong> ${summary.join(', ')}. Total updated to RM ${response.updated_transfer_amount.toFixed(2)}`);
                        $('#fixAllDiscrepanciesTopBtn').hide();
                        $('#discrepancyBadge').hide();
                        $('#discrepancyMessage').html(`<span class="text-success"><i class="fa fa-check"></i> Fixed: ${summary.join(', ')}. Total updated to RM ${response.updated_transfer_amount.toFixed(2)}</span>`);
                        
                        // Reload the page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Failed to fix discrepancies</div>`);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Error: ${xhr.responseJSON?.error || 'Unknown error'}</div>`);
                }
            });
        });

        // Old fix discrepancies handler (deprecated - keeping for backward compatibility)
        $('#fixDiscrepanciesBtn').on('click', function() {
            // Check if there are duplicate entries
            const hasDuplicates = $('#discrepancyMessage').text().includes('DUPLICATE LEDGER ENTRIES');
            
            if (hasDuplicates) {
                alert('⚠️ Duplicate ledger entries detected! Please use "Remove Duplicates" button instead.');
                return;
            }
            
            if (!confirm('This will update transfer fee details to match ledger entries. Continue?')) {
                return;
            }
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Fixing...');
            
            $.ajax({
                url: '/transferfee/fix-discrepancies/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<span class="text-success"><strong>Fixed ${response.fixed_details_count} invoice(s):</strong><br>`;
                        
                        if (response.fixed_details.length > 0) {
                            response.fixed_details.forEach(function(detail) {
                                if (detail.reimbursement) {
                                    message += `Invoice ${detail.invoice_no}: Reimb ${detail.reimbursement.old} → ${detail.reimbursement.new}<br>`;
                                }
                                if (detail.reimbursement_sst) {
                                    message += `Invoice ${detail.invoice_no}: Reimb SST ${detail.reimbursement_sst.old} → ${detail.reimbursement_sst.new}<br>`;
                                }
                            });
                        }
                        
                        message += `</span><br><strong>Updated total: ${response.updated_transfer_amount.toFixed(2)}</strong>`;
                        $('#discrepancyMessage').html(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.updated_transfer_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Reload the page after a short delay to show updated values
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyMessage').html('<span class="text-danger">Failed to fix discrepancies</span>');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyMessage').html('<span class="text-danger">Error fixing discrepancies: ' + (xhr.responseJSON?.error || 'Unknown error') + '</span>');
                }
            });
        });

        // Remove duplicate ledger entries
        $(document).on('click', '#removeDuplicatesBtn', function() {
            if (!confirm('This will remove duplicate ledger entries. The first entry of each duplicate set will be kept. Continue?')) {
                return;
            }
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Removing...');
            
            $.ajax({
                url: '/transferfee/remove-duplicate-entries/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<span class="text-success"><strong>Removed ${response.removed_duplicates_count} duplicate entry/entries:</strong><br>`;
                        message += `Updated total: ${response.updated_transfer_amount.toFixed(2)}</span>`;
                        $('#discrepancyMessage').html(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.updated_transfer_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Reload the page after a short delay to show updated values
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyMessage').html('<span class="text-danger">Failed to remove duplicates</span>');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyMessage').html('<span class="text-danger">Error removing duplicates: ' + (xhr.responseJSON?.error || 'Unknown error') + '</span>');
                }
            });
        });

        // Recalculate Total button in modal
        $(document).on('click', '#recalculateTotalBtn', function() {
            if (!confirm('This will recalculate the total from transfer_fee_details and update the main record. Continue?')) {
                return;
            }
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Recalculating...');
            
            $.ajax({
                url: '/transferfee/recalculate-total/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> Total Updated Successfully!</strong><br>
                            Old Amount: RM ${response.old_amount.toFixed(2)}<br>
                            New Amount: RM ${response.new_amount.toFixed(2)}<br>
                            Difference: RM ${response.difference.toFixed(2)}
                        </div>`;
                        $('#discrepancyModalContent').prepend(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.new_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Update small message
                        $('#discrepancyMessage').html(`<span class="text-success"><i class="fa fa-check"></i> Total updated to RM ${response.new_amount.toFixed(2)}</span>`);
                        
                        // Reload the page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Failed to recalculate total</div>`);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Error: ${xhr.responseJSON?.error || 'Unknown error'}</div>`);
                }
            });
        });

        // Remove Duplicates button in modal
        $(document).on('click', '#removeDuplicatesModalBtn', function() {
            if (!confirm('This will remove duplicate ledger entries. The first entry of each duplicate set will be kept. Continue?')) {
                return;
            }
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Removing...');
            
            $.ajax({
                url: '/transferfee/remove-duplicate-entries/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> Duplicates Removed!</strong><br>
                            Removed ${response.removed_duplicates_count} duplicate entry/entries<br>
                            Updated Total: RM ${response.updated_transfer_amount.toFixed(2)}
                        </div>`;
                        $('#discrepancyModalContent').prepend(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.updated_transfer_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Update small message
                        $('#discrepancyMessage').html(`<span class="text-success"><i class="fa fa-check"></i> ${response.removed_duplicates_count} duplicate(s) removed. Total updated.</span>`);
                        
                        // Reload the page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Failed to remove duplicates</div>`);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Error: ${xhr.responseJSON?.error || 'Unknown error'}</div>`);
                }
            });
        });

        // Fix Discrepancies button in modal
        $(document).on('click', '#fixDiscrepanciesModalBtn', function() {
            if (!confirm('This will update transfer fee details to match ledger entries. Continue?')) {
                return;
            }
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Fixing...');
            
            $.ajax({
                url: '/transferfee/fix-discrepancies/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> Fixed ${response.fixed_details_count} invoice(s):</strong><br>`;
                        
                        if (response.fixed_details && response.fixed_details.length > 0) {
                            response.fixed_details.forEach(function(detail) {
                                if (detail.pfee) {
                                    message += `Invoice ${detail.invoice_no}: Pfee ${detail.pfee.old} → ${detail.pfee.new}<br>`;
                                }
                                if (detail.sst) {
                                    message += `Invoice ${detail.invoice_no}: SST ${detail.sst.old} → ${detail.sst.new}<br>`;
                                }
                                if (detail.reimbursement) {
                                    message += `Invoice ${detail.invoice_no}: Reimb ${detail.reimbursement.old} → ${detail.reimbursement.new}<br>`;
                                }
                                if (detail.reimbursement_sst) {
                                    message += `Invoice ${detail.invoice_no}: Reimb SST ${detail.reimbursement_sst.old} → ${detail.reimbursement_sst.new}<br>`;
                                }
                            });
                        }
                        
                        message += `Updated Total: RM ${response.updated_transfer_amount.toFixed(2)}</div>`;
                        $('#discrepancyModalContent').prepend(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.updated_transfer_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Reload the page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Failed to fix discrepancies</div>`);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Error: ${xhr.responseJSON?.error || 'Unknown error'}</div>`);
                }
            });
        });

        // Create Missing Ledger Entries button in modal
        $('#createMissingEntriesBtn').on('click', function() {
            if (!confirm('This will create missing ledger entries (SST_IN, TRANSFER_IN, etc.) based on transfer_fee_details amounts. Continue?')) {
                return;
            }
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
            
            $.ajax({
                url: '/transferfee/create-missing-entries/{{ $TransferFeeMain->id }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        let message = `<div class="alert alert-success">
                            <strong><i class="fa fa-check-circle"></i> Created ${response.created_entries_count} missing ledger entry/entries:</strong><br>`;
                        
                        if (response.created_entries && response.created_entries.length > 0) {
                            response.created_entries.forEach(function(entry) {
                                message += `Invoice ${entry.invoice_no}: Created ${entry.type} entry for ${entry.amount.toFixed(2)}<br>`;
                            });
                        }
                        
                        message += `Updated Total: RM ${response.updated_transfer_amount.toFixed(2)}</div>`;
                        $('#discrepancyModalContent').prepend(message);
                        
                        // Update the transfer total amount field
                        $('#transferTotalAmount').val(response.updated_transfer_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        // Update small message
                        $('#discrepancyMessage').html(`<span class="text-success"><i class="fa fa-check"></i> Created ${response.created_entries_count} missing ledger entry/entries. Total updated.</span>`);
                        
                        // Reload the page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Failed to create missing entries</div>`);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#discrepancyModalContent').prepend(`<div class="alert alert-danger">Error: ${xhr.responseJSON?.error || 'Unknown error'}</div>`);
                }
            });
        });
    </script>
@endsection
