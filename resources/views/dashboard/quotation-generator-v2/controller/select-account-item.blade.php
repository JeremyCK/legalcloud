<option value="0">Select Account Item</option>
@if (count($quotationSelect))
    @foreach ($quotationSelect as $index => $cat)
        @foreach ($cat['account_details'] as $index => $details)
        <option class="cat_all cat_{{$cat['category']->id}}" value="{{ $details->id }}">{{ $details->account_name }}</option>
        
        @endforeach
    @endforeach
@endif
