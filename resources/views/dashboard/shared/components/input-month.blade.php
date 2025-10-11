@php
    $fiscal_month = [['January',1],
                    ['February',2],
                    ['March',3],
                    ['April',4],
                    ['May',5],
                    ['June',6],
                    ['July',7],
                    ['August',8],
                    ['September',9],
                    ['October',10],
                    ['November',11],
                    ['December',12]];
@endphp

<div class="col-6 date_option date_option_month @if(isset($class)) {{ $class }} @endif" >
    <div class="form-group row">
        <div class="col">
            <label>Month</label>
            <select class="form-control" id="ddl_month" name="ddl_month">
                <option value="0">-- All --</option>
                @foreach ($fiscal_month as $year)
                    <option value="{{ $year[1] }}"  @if(now()->month == $year[1]) selected @endif>{{ $year[0] }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
