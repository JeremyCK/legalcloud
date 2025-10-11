    
<div style="margin:5px;">
    
    @if(isset($masterlistValue))
    
        @if (count($masterlistValue) > 0)
        <hr style="margin:5px;" />  
            @foreach ($masterlistValue['CaseMasterListMainCat'] as $masterlist)
                @php
                    $count = 1;
                @endphp

                @if (count($masterlist['details']) > 0)
               
                    <div class="row">
                        <div class="col-3">
                            <b>{{ $masterlist->name }}:</b>
                        </div>
                        <div class="col-9">
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

