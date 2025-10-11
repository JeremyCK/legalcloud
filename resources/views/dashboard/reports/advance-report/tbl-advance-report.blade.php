<style>
  table thead,
  table tfoot {
      position: sticky;
  }

  table thead {
      inset-block-start: 0;
      /* "top" */
  }

  table tfoot {
      inset-block-end: 0;
      /* "bottom" */
  }
</style>

@php

$adv = 0;

  for ($i=0;$i<count($CaseBill);$i++)
  {
    $adv += $CaseBill[$i]->total_trust_used + $CaseBill[$i]->used_amt;
  }

@endphp

<div class="col-sm-6 col-lg-4">
  <div class="card mb-4" style="--cui-card-cap-bg: #00aced">
      <div class="card-header position-relative d-flex justify-content-center align-items-center">
          <h4 class="text-center"> Total Advance</span>
      </div>
      <div class="card-body row text-center">
          <div class="col">
              <div class="fs-5 fw-semibold">RM <span id="">{{ number_format($adv, 2, '.', ',') }} </span> </div>
          </div>
      </div>
  </div>
</div>

<table id="tbl_referral_report" class="table table-bordered yajra-datatable" style="width:100%;">
    <thead style="background-color: black;color:white;position:sticky">
      <tr><td colspan="22">Total: {{count($CaseBill)}} records</td></tr>
        <tr class="text-center">

            <th>Ref No</th>
            <th>Quotation</th>
            <th>Total Collected Bill</th>
            <th>Disbursement</th>
            {{-- <th>Trust</th> --}}
            <th>R1</th>
            <th>R1 Details</th>
            <th>R2</th>
            <th>R2 Details</th>
            <th>R3</th>
            <th>R3 Details</th>
            <th>R4</th>
            <th>R4 Details</th>
            <th>marketing</th>
            <th>marketing Details</th>
            <th>Uncollected</th>
            <th>Advance</th>

        </tr>
    </thead>
    <tbody>
      @php
      $total_disb = 0;
      $total_quotation = 0;
      $total_collected_bill = 0;
      $total_trust_receive = 0;
      $total_referral_a1 = 0;
      $total_referral_a2 = 0;
      $total_referral_a3 = 0;
      $total_referral_a4 = 0;
      $total_uncollected = 0;
      $total_financed_fee = 0;
      $total_marketing = 0;
      $total_adv = 0;
    @endphp
        @if (count($CaseBill))
         
            @foreach ($CaseBill as $index => $record)
              @php
                $total_disb += $record->disb;
                $total_quotation += $record->total_amt;
                $total_collected_bill += $record->collected_amt;
                $total_trust_receive += $record->total_trust_receive;
                $total_referral_a1 += $record->referral_a1;
                $total_referral_a2 += $record->referral_a2;
                $total_referral_a3 += $record->referral_a3;
                $total_referral_a4 += $record->referral_a4;
                $total_marketing += $record->marketing;
                $total_uncollected += $record->uncollected;

                // $adv = $record->total_trust_used + $record->used_amt;
                $adv = ($record->total_amt - $record->collected_amt) + $record->disb + $record->referral_a1 + $record->referral_a2 + $record->referral_a3 + $record->referral_a4 + $record->marketing;
                $total_adv += $adv;
              @endphp
                <tr>
                    <td class="text-left"> <a target="_blank" href="/case/{{$record->case_id}}" class="  " >{{$record->case_ref_no}} </a><br/> [{{ $record->bill_no }}]<br/>{{ $record->name}}</td>
                    
                    <td class="text-right">{{ number_format($record->total_amt, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->collected_amt, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->disb, 2, '.', ',') }}</td>
                    {{-- <td class="text-right">{{ number_format($record->total_trust_receive, 2, '.', ',') }}</td> --}}
                    <td class="text-right">{{ number_format($record->referral_a1, 2, '.', ',') }}</td>
                    <td class="text-left"> 
                      <b>Agent: </b>@if($record->referral_a1_id == 0) - @else {{$record->referral_a1_id}} @endif  <br/> 
                      <b>Payment Date: </b>{{$record->referral_a1_payment_date}} <br/> 
                      <b>TRX ID: </b>{{$record->referral_a1_trx_id}} <br/> 
                    </td>
                    <td class="text-right">{{ number_format($record->referral_a2, 2, '.', ',') }}</td>
                    <td class="text-left"> 
                      <b>Agent: </b>@if($record->referral_a2_id == 0) - @else {{$record->referral_a2_id}} @endif <br/> 
                      <b>Payment Date: </b>{{$record->referral_a2_payment_date}} <br/> 
                      <b>TRX ID: </b>{{$record->referral_a2_trx_id}} <br/> 
                    </td>
                    <td class="text-right">{{ number_format($record->referral_a3, 2, '.', ',') }}</td>
                    <td class="text-left"> 
                      <b>Agent: </b>@if($record->referral_a3_id == 0) - @else {{$record->referral_a3_id}} @endif <br/> 
                      <b>Payment Date: </b>{{$record->referral_a3_payment_date}} <br/> 
                      <b>TRX ID: </b>{{$record->referral_a3_trx_id}} <br/> 
                    </td>
                    <td class="text-right">{{ number_format($record->referral_a4, 2, '.', ',') }}</td>
                    <td class="text-left"> 
                      <b>Agent: </b>@if($record->referral_a4_id == 0) - @else {{$record->referral_a4_id}} @endif  <br/> 
                      <b>Payment Date: </b>{{$record->referral_a4_payment_date}} <br/> 
                      <b>TRX ID: </b>{{$record->referral_a4_trx_id}} <br/> 
                    </td>
                    
                    <td class="text-right">{{ number_format($record->marketing, 2, '.', ',') }}</td>
                    <td class="text-left"> 
                      <b>Agent: </b>{{$record->sales_name}} <br/> 
                      <b>Payment Date: </b>{{$record->marketing_payment_date}} <br/> 
                      <b>TRX ID: </b>{{$record->marketing_trx_id}} <br/> 
                    </td>
                    <td class="text-right">{{ number_format($record->uncollected, 2, '.', ',') }}</td>
                    @php
                    
                    // $adv = $record->total_trust_used + $record->used_amt;;
                    $prof_bal = $record->pfee1 + $record->pfee2 - $record->referral_a1 - $record->referral_a2 - $record->referral_a3 - $record->referral_a4 - $record->marketing - $record->uncollected;
                    $disb_bal = $record->disb - $record->used_amt;
                    $actual_bal = $prof_bal + $disb_bal;

                    // $adv = ($record->total_amt - $record->collected_amt) + $record->disb + $record->referral_a1 + $record->referral_a2 + $record->referral_a3 + $record->referral_a4;
                    
                    $actual_bal_deduct_bonus = $actual_bal - ($record->total_staff_bonus_2_per + $record->total_staff_bonus_3_per );
                    $actual_bal_deduct_p1_bonus = $actual_bal - ($record->staff_bonus_2_per_p1 + $record->staff_bonus_3_per_p1 + $record->lawyer_bonus_2_per_p1 + $record->lawyer_bonus_3_per_p1);
                    @endphp
                    <td class="text-right">{{ number_format($adv, 2, '.', ',') }}</td>

                </tr>
                
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="20">No record found</td>
            </tr>
        @endif
    </tbody>

    <tfoot style="background-color: black;color:white;position:sticky">
      <tr class="text-center">

          <th></th>
          <th>{{ number_format($total_quotation, 2, '.', ',') }}</th>
          <th>{{ number_format($total_collected_bill, 2, '.', ',') }}</th>
          <th>{{ number_format($total_disb, 2, '.', ',') }}</th>
          <th>{{ number_format($total_referral_a1, 2, '.', ',') }}</th>
          <th>-</th>
          <th>{{ number_format($total_referral_a2, 2, '.', ',') }}</th>
          <th>-</th>
          <th>{{ number_format($total_referral_a3, 2, '.', ',') }}</th>
          <th>-</th>
          <th>{{ number_format($total_referral_a4, 2, '.', ',') }}</th>
          <th>-</th>
          <th>{{ number_format($total_marketing, 2, '.', ',') }}</th>
          <th>-</th>
          <th>{{ number_format($total_uncollected, 2, '.', ',') }}</th>
          <th>{{ number_format($total_adv, 2, '.', ',') }}</th>
      

      </tr>
  </tfoot>
</table>
