<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 12px;">
        <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1; background-color: #343a40;">
            <tr>
                <th width="30">No</th>
                <th width="40">Action</th>
                <th width="120">Ref No</th>
                <th width="120">Client Name</th>
                <th width="100">Invoice No</th>
                <th width="90">Invoice Date</th>
                <th width="80">Total amt</th>
                <th width="80">Collected amt</th>
                <th width="60">SST</th>
                <th width="80">Reimb SST</th>
                <th width="80">Total SST</th>
                <th width="90">Payment Date</th>
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
