<div class="card">
    <div class="card-header">
        {{-- <h6><i class="fa fa-list"></i> Available Invoices for Transfer <small class="text-muted">(Scrollable
                table)</small></h6> --}}
        <div class="float-right">
            <div class="checkbox bulk-edit-mode">
                <input type="checkbox" id="selectAll" name="selectAll">
                <label for="selectAll">Select All</label>
            </div>
        </div>
    </div>
    <div class="card-body">

        @if (count($rows) > 0)
            <div class="table-responsive" style="margin-bottom: 0;">
                <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                    <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
                        <tr>
                            <th width="30">No</th>
                            <th width="40">Action</th>
                            <th width="120" class="sortable-header" data-sort="case_ref_no" style="cursor: pointer;">
                                Ref No
                                <span class="sort-icon" id="sort-case_ref_no"></span>
                            </th>
                            <th width="100" class="sortable-header" data-sort="invoice_no" style="cursor: pointer;">
                                Invoice No
                                <span class="sort-icon" id="sort-invoice_no"></span>
                            </th>
                            <th width="90" class="sortable-header" data-sort="invoice_date" style="cursor: pointer;">
                                Invoice Date
                                <span class="sort-icon" id="sort-invoice_date"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="total_amount" style="cursor: pointer;">
                                Total amt
                                <span class="sort-icon" id="sort-total_amount"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="collected_amount" style="cursor: pointer;">
                                Collected amt
                                <span class="sort-icon" id="sort-collected_amount"></span>
                            </th>
                            <th width="70" class="sortable-header" data-sort="pfee" style="cursor: pointer;">
                                pfee
                                <span class="sort-icon" id="sort-pfee"></span>
                            </th>
                            <th width="60" class="sortable-header" data-sort="sst" style="cursor: pointer;">
                                sst
                                <span class="sort-icon" id="sort-sst"></span>
                            </th>
                            <th width="60" class="sortable-header" data-sort="reimbursement" style="cursor: pointer;">
                                reimb
                                <span class="sort-icon" id="sort-reimbursement"></span>
                            </th>
                            <th width="60" class="sortable-header" data-sort="reimbursement_sst" style="cursor: pointer;">
                                reimb sst
                                <span class="sort-icon" id="sort-reimbursement_sst"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="pfee_to_transfer" style="cursor: pointer;">
                                Pfee to transfer
                                <span class="sort-icon" id="sort-pfee_to_transfer"></span>
                            </th>
                            <th width="70" class="sortable-header" data-sort="sst_to_transfer" style="cursor: pointer;">
                                SST to transfer
                                <span class="sort-icon" id="sort-sst_to_transfer"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="reimbursement_to_transfer" style="cursor: pointer;">
                                Reimb to transfer
                                <span class="sort-icon" id="sort-reimbursement_to_transfer"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="reimbursement_sst_to_transfer" style="cursor: pointer;">
                                Reimb SST to transfer
                                <span class="sort-icon" id="sort-reimbursement_sst_to_transfer"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="transferred_bal" style="cursor: pointer;">
                                Transferred Bal
                                <span class="sort-icon" id="sort-transferred_bal"></span>
                            </th>
                            <th width="80" class="sortable-header" data-sort="transferred_sst" style="cursor: pointer;">
                                Transferred SST
                                <span class="sort-icon" id="sort-transferred_sst"></span>
                            </th>
                            <th width="90" class="sortable-header" data-sort="payment_date" style="cursor: pointer;">
                                Payment Date
                                <span class="sort-icon" id="sort-payment_date"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $index => $row)
                            @if($row->loan_case_main_bill_id && $row->loan_case_main_bill_id > 0)
                            <tr>
                                <td class="text-center" style="font-size: 11px;">{{ $loop->index + 1 }}</td>
                                <td>
                                    <div class="checkbox bulk-edit-mode">
                                        @php
                                            $totalPfee = ($row->pfee1_inv ?? 0) + ($row->pfee2_inv ?? 0);
                                            $remainingPfee = max(0, $totalPfee - ($row->transferred_pfee_amt ?? 0));
                                            $totalSst = $row->sst_inv ?? 0;
                                            $remainingSst = max(0, $totalSst - ($row->transferred_sst_amt ?? 0));
                                            $totalReimbursement = $row->reimbursement_amount ?? 0;
                                            $remainingReimbursement = max(0, $totalReimbursement - ($row->transferred_reimbursement_amt ?? 0));
                                            $totalReimbursementSst = $row->reimbursement_sst ?? 0;
                                            $remainingReimbursementSst = max(0, $totalReimbursementSst - ($row->transferred_reimbursement_sst_amt ?? 0));
                                        @endphp
                                        <input type="checkbox" class="invoice-checkbox" name="invoice"
                                            value="{{ $row->id }}" id="chk_{{ $row->id }}"
                                            data-bill-id="{{ $row->loan_case_main_bill_id }}"
                                            data-amount="{{ $remainingPfee }}"
                                            data-sst="{{ $remainingSst }}"
                                            data-reimbursement="{{ $remainingReimbursement }}"
                                            data-reimbursement-sst="{{ $remainingReimbursementSst }}"
                                            data-original-pfee="{{ $totalPfee }}"
                                            data-original-sst="{{ $totalSst }}"
                                            data-original-reimbursement="{{ $totalReimbursement }}"
                                            data-original-reimbursement-sst="{{ $totalReimbursementSst }}"
                                            data-transferred-pfee="{{ $row->transferred_pfee_amt ?? 0 }}"
                                            data-transferred-sst="{{ $row->transferred_sst_amt ?? 0 }}"
                                            data-transferred-reimbursement="{{ $row->transferred_reimbursement_amt ?? 0 }}"
                                            data-transferred-reimbursement-sst="{{ $row->transferred_reimbursement_sst_amt ?? 0 }}">
                                        <label for="chk_{{ $row->id }}"></label>
                                    </div>
                                </td>
                                <td style="font-size: 11px;">
                                    @if ($row->case_ref_no && $row->case_id)
                                        <a href="/case/{{ $row->case_id }}" target="_blank" class="text-primary"
                                            style="text-decoration: none;">
                                            {{ $row->case_ref_no }}
                                        </a>
                                    @else
                                        {{ $row->case_ref_no ?? 'N/A' }}
                                    @endif
                                </td>
                                <td style="font-size: 11px;">{{ $row->invoice_no ?? 'N/A' }}</td>
                                <td style="font-size: 11px;">{{ $row->invoice_date ?? 'N/A' }}</td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format(($row->pfee1_inv ?? 0) + ($row->pfee2_inv ?? 0) + ($row->sst_inv ?? 0), 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format(($row->pfee1_inv ?? 0) + ($row->pfee2_inv ?? 0) + ($row->sst_inv ?? 0), 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format(($row->pfee1_inv ?? 0) + ($row->pfee2_inv ?? 0), 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format($row->sst_inv ?? 0, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format($row->reimbursement_amount ?? 0, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format($row->reimbursement_sst ?? 0, 2, '.', ',') }}
                                </td>

                                <td class="text-right" style="font-size: 11px;">
                                    @php
                                        $totalPfee = ($row->pfee1_inv ?? 0) + ($row->pfee2_inv ?? 0);
                                        $remainingPfee = max(0, $totalPfee - ($row->transferred_pfee_amt ?? 0));
                                    @endphp
                                    {{ number_format($remainingPfee, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    @php
                                        $totalSst = $row->sst_inv ?? 0;
                                        $remainingSst = max(0, $totalSst - ($row->transferred_sst_amt ?? 0));
                                    @endphp
                                    {{ number_format($remainingSst, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    @php
                                        $totalReimbursement = $row->reimbursement_amount ?? 0;
                                        $remainingReimbursement = max(0, $totalReimbursement - ($row->transferred_reimbursement_amt ?? 0));
                                    @endphp
                                    {{ number_format($remainingReimbursement, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    @php
                                        $totalReimbursementSst = $row->reimbursement_sst ?? 0;
                                        $remainingReimbursementSst = max(0, $totalReimbursementSst - ($row->transferred_reimbursement_sst_amt ?? 0));
                                    @endphp
                                    {{ number_format($remainingReimbursementSst, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format($row->transferred_pfee_amt ?? 0, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-size: 11px;">
                                    {{ number_format($row->transferred_sst_amt ?? 0, 2, '.', ',') }}
                                </td>
                                <td style="font-size: 11px;">
                                    @if($row->payment_receipt_date)
                                        {{ $row->payment_receipt_date }}
                                    @elseif($row->bill_invoice_date)
                                        {{ $row->bill_invoice_date }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                
                @if($rows->where('loan_case_main_bill_id', '>', 0)->count() == 0)
                <div class="alert alert-warning text-center">
                    <i class="fa fa-exclamation-triangle"></i>
                    No invoices with valid bill information found. All invoices must have a valid bill ID to be eligible for transfer.
                </div>
                @endif
            </div>

            <!-- Enhanced Pagination - Fixed Position -->
            @if (isset($totalPages) && $totalPages > 1)
                <div class="pagination-container" style="background: white; border-top: 1px solid #dee2e6; padding: 8px 0; margin-top: 5px; z-index: 10;">
                    <div class="row">
                        <div class="col-md-12">
                            <nav aria-label="Invoice pagination">
                                <ul class="pagination justify-content-center mb-0">
                                    <!-- First Page -->
                                    @if ($currentPage > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)"
                                                onclick="loadInvoicePage(1)"
                                                style="cursor: pointer;" title="First Page">
                                                <i class="fa fa-angle-double-left"></i>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)"
                                                onclick="loadInvoicePage({{ $currentPage - 1 }})"
                                                style="cursor: pointer;" title="Previous Page">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    <!-- Page Numbers with Smart Display -->
                                    @php
                                        // Always show current page and 2 pages on each side
                                        $startRange = max(1, $currentPage - 2);
                                        $endRange = min($totalPages, $currentPage + 2);
                                        
                                        // Determine which pages to show
                                        $pagesToShow = [];
                                        
                                        // Always show page 1 if we're not near the beginning
                                        if ($currentPage > 4) {
                                            $pagesToShow[] = 1;
                                            if ($currentPage > 5) {
                                                $pagesToShow[] = '...';
                                            }
                                        }
                                        
                                        // Show the range around current page
                                        for ($i = $startRange; $i <= $endRange; $i++) {
                                            $pagesToShow[] = $i;
                                        }
                                        
                                        // Always show last page if we're not near the end
                                        if ($currentPage < ($totalPages - 3)) {
                                            if ($currentPage < ($totalPages - 4)) {
                                                $pagesToShow[] = '...';
                                            }
                                            $pagesToShow[] = $totalPages;
                                        }
                                    @endphp

                                    @foreach ($pagesToShow as $page)
                                        @if ($page === '...')
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @else
                                            <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                                                <a class="page-link" href="javascript:void(0)"
                                                    onclick="loadInvoicePage({{ $page }})"
                                                    style="cursor: pointer;">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    <!-- Next and Last Page -->
                                    @if ($currentPage < $totalPages)
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)"
                                                onclick="loadInvoicePage({{ $currentPage + 1 }})"
                                                style="cursor: pointer;" title="Next Page">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)"
                                                onclick="loadInvoicePage({{ $totalPages }})"
                                                style="cursor: pointer;" title="Last Page">
                                                <i class="fa fa-angle-double-right"></i>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>{{ $totalCount ?? count($rows) }}</strong> invoices available for transfer (not yet
                        transferred)
                        @if (isset($currentPage) && isset($totalPages))
                            (Page {{ $currentPage }} of {{ $totalPages }} - {{ count($rows) }} records shown)
                        @endif
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="alert alert-success">
                        <strong>Total Selected Amount: <span id="totalAmount">0.00</span></strong>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                No invoices available for transfer. All invoices may have been transferred already.
            </div>
        @endif

    </div>
</div>
