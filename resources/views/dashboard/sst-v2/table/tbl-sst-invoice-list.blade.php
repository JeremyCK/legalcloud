<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 12px;">
        <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
            <tr>
                <th width="30">No</th>
                <th width="40">Action</th>
                <th width="120" class="sortable-header" data-sort="case_ref_no" style="cursor: pointer;">
                    Ref No
                    <span class="sort-icon" id="sort-case_ref_no"></span>
                </th>
                <th width="120" class="sortable-header" data-sort="client_name" style="cursor: pointer;">
                    Client Name
                    <span class="sort-icon" id="sort-client_name"></span>
                </th>
                <th width="100" class="sortable-header" data-sort="invoice_no" style="cursor: pointer;">
                    Invoice No
                    <span class="sort-icon" id="sort-invoice_no"></span>
                </th>
                <th width="90" class="sortable-header" data-sort="invoice_date" style="cursor: pointer;">
                    Invoice Date
                    <span class="sort-icon" id="sort-invoice_date"></span>
                </th>
                <th width="80" class="sortable-header" data-sort="total_amt" style="cursor: pointer;">
                    Total amt
                    <span class="sort-icon" id="sort-total_amt"></span>
                </th>
                <th width="80" class="sortable-header" data-sort="collected_amt" style="cursor: pointer;">
                    Collected amt
                    <span class="sort-icon" id="sort-collected_amt"></span>
                </th>
                <th width="60" class="sortable-header" data-sort="sst" style="cursor: pointer;">
                    SST
                    <span class="sort-icon" id="sort-sst"></span>
                </th>
                <th width="80" class="sortable-header" data-sort="reimb_sst" style="cursor: pointer;">
                    Reimb SST
                    <span class="sort-icon" id="sort-reimb_sst"></span>
                </th>
                <th width="80" class="sortable-header" data-sort="total_sst" style="cursor: pointer;">
                    Total SST
                    <span class="sort-icon" id="sort-total_sst"></span>
                </th>
                <th width="90" class="sortable-header" data-sort="payment_date" style="cursor: pointer;">
                    Payment Date
                    <span class="sort-icon" id="sort-payment_date"></span>
                </th>
            </tr>
        </thead>
        <tbody>
            @if(count($rows) > 0)
                @foreach ($rows as $index => $row)
                    @if($row->loan_case_main_bill_id && $row->loan_case_main_bill_id > 0)
                    <tr class="invoice-row">
                        <td class="text-center" style="font-size: 11px;">{{ (($currentPage - 1) * 20) + $loop->index + 1 }}</td>
                        <td>
                            <div class="checkbox bulk-edit-mode">
                                @php
                                    // If bln_sst = 0, invoice hasn't been transferred, so show full SST amounts
                                    // If bln_sst = 1, invoice has been transferred, so show remaining SST
                                    $totalSst = $row->sst_inv ?? 0;
                                    $totalReimbSst = $row->reimbursement_sst ?? 0;
                                    
                                    if (($row->bln_sst ?? 0) == 0) {
                                        // Not transferred yet - show full amounts
                                        $remainingSst = $totalSst;
                                        $remainingReimbSst = $totalReimbSst;
                                    } else {
                                        // Already transferred - show remaining amounts
                                        $remainingSst = max(0, $totalSst - ($row->transferred_sst_amt ?? 0));
                                        $remainingReimbSst = max(0, $totalReimbSst - ($row->transferred_reimbursement_sst_amt ?? 0));
                                    }
                                    
                                    $totalSstAmount = $remainingSst + $remainingReimbSst;
                                @endphp
                                <input type="checkbox" class="invoice-checkbox" name="invoice"
                                    value="{{ $row->id }}" id="chk_{{ $row->id }}"
                                    data-bill-id="{{ $row->loan_case_main_bill_id }}"
                                    data-sst="{{ $remainingSst }}"
                                    data-original-sst="{{ $totalSst }}"
                                    data-transferred-sst="{{ $row->transferred_sst_amt ?? 0 }}"
                                    data-reimb-sst="{{ $remainingReimbSst }}"
                                    data-original-reimb-sst="{{ $totalReimbSst }}"
                                    data-transferred-reimb-sst="{{ $row->transferred_reimbursement_sst_amt ?? 0 }}"
                                    data-total-sst="{{ $totalSstAmount }}"
                                    data-total-amt="{{ $row->total_amt_inv ?? 0 }}"
                                    data-collected-amt="{{ $row->collected_amt_inv ?? 0 }}"
                                    data-pfee1="{{ $row->pfee1 ?? 0 }}"
                                    data-pfee2="{{ $row->pfee2 ?? 0 }}">
                                <label for="chk_{{ $row->id }}"></label>
                            </div>
                        </td>
                        <td style="font-size: 11px;">
                            <a href="{{ route('case.show', $row->case_id) }}" target="_blank" style="color: #007bff; text-decoration: none;">
                                {{ $row->case_ref_no }}
                            </a>
                        </td>
                        <td style="font-size: 11px;">{{ $row->client_name ?? 'N/A' }}</td>
                        <td style="font-size: 11px;">{{ $row->invoice_no }}</td>
                        <td style="font-size: 11px;">{{ $row->invoice_date ?? $row->bill_invoice_date ?? 'N/A' }}</td>
                        <td class="text-right" style="font-size: 11px;">{{ number_format($row->total_amt_inv ?? 0, 2) }}</td>
                        <td class="text-right" style="font-size: 11px;">{{ number_format($row->collected_amt_inv ?? 0, 2) }}</td>
                        <td class="text-right" style="font-size: 11px;">{{ number_format($remainingSst, 2) }}</td>
                        <td class="text-right" style="font-size: 11px;">{{ number_format($remainingReimbSst, 2) }}</td>
                        <td class="text-right" style="font-size: 11px; font-weight: bold;">{{ number_format($totalSstAmount, 2) }}</td>
                        <td style="font-size: 11px;">{{ $row->payment_receipt_date ?? 'N/A' }}</td>
                    </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="12" class="text-center text-muted py-4">
                        <i class="fa fa-info-circle"></i> No invoices found matching your criteria
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($totalPages) && $totalPages > 1)
<div class="pagination-container mt-3" style="background: white; border-top: 1px solid #dee2e6; padding: 10px 0;">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="Invoice pagination">
                <ul class="pagination justify-content-center mb-0" style="font-size: 12px;">
                    <!-- First Page -->
                    @if ($currentPage > 1)
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" onclick="loadInvoicePage(1)" style="cursor: pointer; padding: 4px 8px;" title="First Page">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" onclick="loadInvoicePage({{ $currentPage - 1 }})" style="cursor: pointer; padding: 4px 8px;" title="Previous Page">
                                <i class="fa fa-angle-left"></i>
                            </a>
                        </li>
                    @endif

                    <!-- Page Numbers with Smart Display -->
                    @php
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
                                <span class="page-link" style="padding: 4px 8px;">...</span>
                            </li>
                        @elseif ($page == $currentPage)
                            <li class="page-item active">
                                <span class="page-link" style="padding: 4px 8px;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" onclick="loadInvoicePage({{ $page }})" style="cursor: pointer; padding: 4px 8px;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    <!-- Next Page -->
                    @if ($currentPage < $totalPages)
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" onclick="loadInvoicePage({{ $currentPage + 1 }})" style="cursor: pointer; padding: 4px 8px;" title="Next Page">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" onclick="loadInvoicePage({{ $totalPages }})" style="cursor: pointer; padding: 4px 8px;" title="Last Page">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="text-center mt-2">
                    <small style="font-size: 11px; color: #6c757d;">
                        Showing page {{ $currentPage }} of {{ $totalPages }} 
                        @if (isset($totalCount))
                            (Total: {{ number_format($totalCount) }} invoices)
                        @endif
                    </small>
                </div>
            </nav>
        </div>
    </div>
</div>
@endif
