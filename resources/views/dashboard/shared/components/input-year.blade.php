<div class="col-6 date_option date_option_year @if(isset($class)) {{ $class }} @endif" >
    <div class="form-group row">
        <div class="col">
            <label>Year</label>
            <select class="form-control" id="ddl_year" name="ddl_year">
                <option value="0">-- All --</option>
                @foreach ($fiscal_year as $year)
                    <option value="{{ $year->year }}" @if(now()->year == $year->year) selected @endif>{{ $year->year }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>