<div class="col-sm-6 col-lg-4">
    <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
        <div class="card-header position-relative d-flex justify-content-center align-items-center">
            <h4 class="text-center">Referral 1</span>
        </div>
        <div class="card-body row text-center">
            <div class="col">
                <div class="fs-5 fw-semibold">RM <span>
                        @if (isset($rows))
                            {{ number_format($rows->referral_a1, 2, '.', ',') }}
                        @else
                            0.00
                        @endif
                    </span> </div>
                <div class="text-uppercase text-medium-emphasis ">
                    @if ($rows->referral_a1_ref_id != 0)
                        {{ $rows->referral_name_1 }}
                    @elseif($rows->referral_a1_id != '' && $rows->referral_a1_id != 0)
                        {{ $rows->referral_a1_id }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
        <div class="card-header position-relative d-flex justify-content-center align-items-center">
            <h4 class="text-center">Referral 2</span>
        </div>
        <div class="card-body row text-center">
            <div class="col">
                <div class="fs-5 fw-semibold">RM <span>
                        @if (isset($rows))
                            {{ number_format($rows->referral_a2, 2, '.', ',') }}
                        @else
                            0.00
                        @endif
                    </span> </div>
                <div class="text-uppercase text-medium-emphasis ">
                    @if ($rows->referral_a2_ref_id != 0)
                        {{ $rows->referral_name_2 }}
                    @elseif($rows->referral_a2_id != '' && $rows->referral_a2_id != 0)
                        {{ $rows->referral_a2_id }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
        <div class="card-header position-relative d-flex justify-content-center align-items-center">
            <h4 class="text-center">Referral 3</span>
        </div>
        <div class="card-body row text-center">
            <div class="col">
                <div class="fs-5 fw-semibold">RM <span>
                        @if (isset($rows))
                            {{ number_format($rows->referral_a3, 2, '.', ',') }}
                        @else
                            0.00
                        @endif
                    </span> </div>
                <div class="text-uppercase text-medium-emphasis ">
                    @if ($rows->referral_a3_ref_id != 0)
                        {{ $rows->referral_name_3 }}
                    @elseif($rows->referral_a3_id != '' && $rows->referral_a3_id != 0)
                        {{ $rows->referral_a3_id }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
        <div class="card-header position-relative d-flex justify-content-center align-items-center">
            <h4 class="text-center">Misc</span>
        </div>
        <div class="card-body row text-center">
            <div class="col">
                <div class="fs-5 fw-semibold">RM <span>
                        @if (isset($rows))
                            {{ number_format($rows->referral_a4, 2, '.', ',') }}
                        @else
                            0.00
                        @endif
                    </span> </div>
                <div class="text-uppercase text-medium-emphasis ">
                    @if ($rows->referral_a4_ref_id != 0)
                        {{ $rows->referral_name_4 }}
                    @elseif($rows->referral_a4_id != '' && $rows->referral_a4_id != 0)
                        {{ $rows->referral_a4_id }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
        <div class="card-header position-relative d-flex justify-content-center align-items-center">
            <h4 class="text-center">Marketing</span>
        </div>
        <div class="card-body row text-center">
            <div class="col">
                <div class="fs-5 fw-semibold">RM <span>
                        @if (isset($rows))
                            {{ number_format($rows->marketing, 2, '.', ',') }}
                        @else
                            0.00
                        @endif
                    </span> </div>
                    <div class="text-uppercase text-medium-emphasis ">
                        @if ($rows->marketing_id != 0)
                            {{ $rows->marketing_name }}
                        @endif
    
                    </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-4">
    <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
        <div class="card-header position-relative d-flex justify-content-center align-items-center">
            <h4 class="text-center">Uncollected</span>
        </div>
        <div class="card-body row text-center">
            <div class="col">
                <div class="fs-5 fw-semibold">RM <span>
                        @if (isset($rows))
                            {{ number_format($rows->uncollected, 2, '.', ',') }}
                        @else
                            0.00
                        @endif
                    </span> </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <hr />
</div> 


<div class="col-12 " >
    <table class="table  datatable">
        <tr>
             <td><h4 >(P1 + P2)</h4></td>
        </tr>
        <tr>
            <td> <span><b>Formula:</b> (P1 + P2) - Referral 1 - Referral 2 - Referral 3 - Referral 4 - Marketing - uncollected = Prof Balance</span></td>
        </tr>
        <tr>
            <td> 
                <span>( {{ number_format($rows->pfee1, 2, '.', ',') }} +  {{ number_format($rows->pfee2, 2, '.', ',') }}) - {{ number_format($rows->referral_a1, 2, '.', ',') }} - 
                    {{ number_format($rows->referral_a2, 2, '.', ',') }} - {{ number_format($rows->referral_a3, 2, '.', ',') }} - {{ number_format($rows->referral_a4, 2, '.', ',') }} - 
                    {{ number_format($rows->marketing, 2, '.', ',') }} - {{ number_format($rows->uncollected, 2, '.', ',') }} = {{ number_format($rows->prof_balance, 2, '.', ',') }}</span>
            </td>
        </tr>
        <tr>
            <td> 
                <span>2(%): </span> {{ number_format($rows->prof_balance, 2, '.', ',') }} * 2 (%) = {{ number_format($rows->staff_bonus_2_per, 2, '.', ',') }} <br/>
    <span>3(%): </span> {{ number_format($rows->prof_balance, 2, '.', ',') }} * 3 (%) = {{ number_format($rows->staff_bonus_3_per, 2, '.', ',') }}
            </td>
        </tr>
    </table>
    
    
</div>


@php
$p1_prof_bal = $rows->pfee1 - $rows->referral_a1 - $rows->referral_a2 - $rows->referral_a3 - $rows->referral_a4 - $rows->marketing - $rows->uncollected;

@endphp

<div class="col-12 " >
    <table class="table  datatable">
        <tr>
             <td><h4 >(P1)</h4></td>
        </tr>
        <tr>
            <td> <span>Formula: (P1) - Referral 1 - Referral 2 - Referral 3 - Referral 4 - Marketing - uncollected = Prof Balance</span><br/></td>
        </tr>
        <tr>
            <td> 
                <span>({{ number_format($rows->pfee1, 2, '.', ',') }}) - {{ number_format($rows->referral_a1, 2, '.', ',') }} - 
                    {{ number_format($rows->referral_a2, 2, '.', ',') }} - {{ number_format($rows->referral_a3, 2, '.', ',') }} - {{ number_format($rows->referral_a4, 2, '.', ',') }} - 
                    {{ number_format($rows->marketing, 2, '.', ',') }} - {{ number_format($rows->uncollected, 2, '.', ',') }} = {{ number_format($p1_prof_bal, 2, '.', ',') }}</span>
            </td>
        </tr>
        <tr>
            <td> 
                <span>2(%): </span> {{ number_format($p1_prof_bal, 2, '.', ',') }} * 2 (%) = {{ number_format($rows->staff_bonus_2_per_p1, 2, '.', ',') }} <br/>
                <span>3(%): </span> {{ number_format($p1_prof_bal, 2, '.', ',') }} * 3 (%) = {{ number_format($rows->staff_bonus_3_per_p1, 2, '.', ',') }}
            </td>
        </tr>
    </table>
    
    
</div>

