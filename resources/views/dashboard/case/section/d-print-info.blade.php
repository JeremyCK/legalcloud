    
<div style="margin:5px;">
    
    @if(isset($masterlistValue))
    
        @if (count($masterlistValue) > 0)
        <hr style="margin:5px;" />  
            @foreach ($masterlistValue['CaseMasterListMainCat'] as $masterlist)
                @php
                    $count = 1;
                @endphp

                @if (count($masterlist['details']) > 0)
               
                    <div class="row print-info-row" style="display: table; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <div class="col-3 print-info-label" style="width: 25%; font-weight: bold; display: table-cell; vertical-align: top; padding-right: 5px;">
                            <b>{{ $masterlist->name }}:</b>
                        </div>
                        <div class="col-9 print-info-value" style="width: 75%; display: table-cell; vertical-align: top;">
                            @foreach ($masterlist['details'] as $details)
                                {{ $details->value }} 
                                @if (count($masterlist['details']) > 1)
                                    @if ($count < count($masterlist['details']) && $details->value != '')
                                        <b>&</b>
                                    @endif
                                @endif

                                @php
                                    $count += 1;
                                @endphp
                            @endforeach
                        </div>

                    </div>
                @endif
            @endforeach

        @endif
    @endif

    

</div>

