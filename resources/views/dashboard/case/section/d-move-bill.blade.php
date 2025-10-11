<option value="0">-- Please select Bill to move to --</option>
@foreach ($LoanCaseBillMain as $row)

    @if($row != null)
        <option  value="{{ $row->id }}">{{$row->bill_no}} - {{$row->name}}</option>
    @endif

@endforeach
