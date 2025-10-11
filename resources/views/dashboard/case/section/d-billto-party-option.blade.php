<option value="0">-- Please select party --</option>
@foreach ($parties_list as $party)
@php
$party_id = 0;
    if(isset($party['id']))
    {
        $party_id = $party['id'];
    }
@endphp
    @if($party['name'] != null)
        <option data-type="{{ $party_id }}" value="{{ $party['name'] }}">{{ $party['party'] }} - {{ $party['name'] }}
        </option>
    @endif

@endforeach
