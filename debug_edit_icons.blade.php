{{-- Temporary debug file to add to edit.blade.php --}}
{{-- Add this right after line 282 (after @foreach starts) --}}

@foreach ($TransferFeeDetails as $index => $detail)
    {{-- DEBUG: Check if loop is running --}}
    {{-- DEBUG: Detail ID = {{ $detail->id }} --}}
    {{-- DEBUG: User role = {{ auth()->user()->menuroles }} --}}
    {{-- DEBUG: is_recon = {{ $TransferFeeMain->is_recon }} --}}
    {{-- DEBUG: Permission check = {{ in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1' ? 'PASS' : 'FAIL' }} --}}
    
    <tr>
        {{-- ... existing code ... --}}
        
        {{-- Around line 341, the edit icon should be here --}}
        @if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
            {{-- DEBUG: Icon should render here --}}
            <i class="fa fa-pencil edit-pfee ml-1" 
               style="cursor: pointer; color: #007bff; font-size: 11px;" 
               data-detail-id="{{ $detail->id }}"
               data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
               data-bill-id="{{ $detail->loan_case_main_bill_id }}"
               data-pfee1="{{ $detail->pfee1_inv ?? 0 }}"
               data-pfee2="{{ $detail->pfee2_inv ?? 0 }}"
               title="Edit Professional Fee">DEBUG-ICON</i>
        @else
            {{-- DEBUG: Permission check failed --}}
            <!-- DEBUG: Permission failed - Role: {{ auth()->user()->menuroles }}, is_recon: {{ $TransferFeeMain->is_recon }} -->
        @endif
    </tr>
@endforeach

