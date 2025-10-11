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

<table id="tbl-report" class="table table-bordered yajra-datatable" style="width:100%;">
    <thead style="background-color: black;color:white;position:sticky">
      <tr><td colspan="22">Total: {{count($CaseBill)}} records</td></tr>
        <tr class="text-center">

            <th>Ref No</th>
            <th>P1</th>
            <th>P2</th>
            <th>SST</th>
            <th>Uncollected</th>
            <th>R1</th>
            <th>R1 Details</th>
            <th>R2</th>
            <th>R2 Details</th>
            <th>R3</th>
            <th>R3 Details</th>
            <th>R4</th>
            <th>R4 Details</th>
            <th>Finaced Fees (RM)</th>
            <th>Finaced Sum (RM)</th>
            <th>Prof Balance (Forecast)</th>
            <th>Staff Bonus(2%)</th>
            <th>Staff Bonus(3%)</th>
            <th>Disbursement</th>
            <th>Disbursement Used</th>
            <th>Balance Disbursement</th>
            <th>Actual Balance</th>

        </tr>
    </thead>
    <tbody>
      @php
        $total_pfee1 = 0;
        $total_pfee2 = 0;
        $total_sst = 0;
        $total_referral_a1 = 0;
        $total_referral_a2 = 0;
        $total_referral_a3 = 0;
        $total_referral_a4 = 0;
        $total_uncollected = 0;
        $total_financed_fee = 0;
        $total_financed_fee = 0;
        $total_prof_bal = 0;
        $total_disb = 0;
        $total_disb_used = 0;
        $total_disb_bal = 0;
        $total_actual_bal = 0;
        $total_bonus_2 = 0;
        $total_bonus_3 = 0;
      @endphp
        @if (count($CaseBill))
         
            @foreach ($CaseBill as $index => $record)
              @php
                $total_pfee1 += $record->pfee1;
                $total_pfee2 += $record->pfee2;
                $total_sst += $record->sst;
                $total_referral_a1 += $record->referral_a1;
                $total_referral_a2 += $record->referral_a2;
                $total_referral_a3 += $record->referral_a3;
                $total_referral_a4 += $record->referral_a4;
                $total_financed_fee += $record->financed_fee;

                // $prof_bal = $record->pfee1_inv + $record->pfee2_inv - $record->referral_a1 - $record->referral_a2 - $record->referral_a3 - $record->referral_a4 - $record->marketing - $record->uncollected;
                $prof_bal = $record->pfee1 + $record->pfee2 - $record->referral_a1 - $record->referral_a2 - $record->referral_a3 - $record->referral_a4 - $record->marketing - $record->uncollected;
                $disb_bal = $record->disb - $record->used_amt;
                $actual_bal = $prof_bal + $disb_bal;
                
                $actual_bal_deduct_bonus = $prof_bal - ($record->total_staff_bonus_2_per + $record->total_staff_bonus_3_per );
                // $actual_bal_deduct_bonus = $prof_bal;
                // $actual_bal_deduct_bonus = $prof_bal - ($record->bonus_3 + $record->bonus_5 );
                $actual_bal_deduct_p1_bonus = $actual_bal - ($record->staff_bonus_2_per_p1 + $record->staff_bonus_3_per_p1 + $record->lawyer_bonus_2_per_p1 + $record->lawyer_bonus_3_per_p1);
                    
                $total_prof_bal += $prof_bal;
                $total_disb += $record->disb;
                $total_disb_used += $record->used_amt;
                $total_disb_bal += $disb_bal;
                $total_actual_bal += $actual_bal_deduct_bonus;
                $total_bonus_2 += $record->total_staff_bonus_2_per;
                $total_bonus_3 += $record->total_staff_bonus_3_per;
              @endphp
                <tr>
                    <td class="text-left"> <a target="_blank" href="/case/{{$record->case_id}}" class="  " >{{$record->case_ref_no}} </a><br/> [{{ $record->invoice_no }}]<br/>{{ $record->name}}</td>
                    
                    <td class="text-right">{{ number_format($record->pfee1, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->pfee2, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->sst, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->uncollected, 2, '.', ',') }}</td>
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
                    <td class="text-right">{{ number_format($record->financed_fee, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->financed_sum, 2, '.', ',') }}</td>
                  
                    <td class="text-right">{{ number_format($prof_bal, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->total_staff_bonus_2_per, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->total_staff_bonus_3_per, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->disb, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($record->used_amt, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($disb_bal, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($actual_bal_deduct_bonus, 2, '.', ',') }}</td>
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
            <th>{{ number_format($total_pfee1, 2, '.', ',') }}</th>
            <th>{{ number_format($total_pfee2, 2, '.', ',') }}</th>
            <th>{{ number_format($total_sst, 2, '.', ',') }}</th>
            <th>{{ number_format($total_uncollected, 2, '.', ',') }}</th>
            <th>{{ number_format($total_referral_a1, 2, '.', ',') }}</th>
            <th>-</th>
            <th>{{ number_format($total_referral_a2, 2, '.', ',') }}</th>
            <th>-</th>
            <th>{{ number_format($total_referral_a3, 2, '.', ',') }}</th>
            <th>-</th>
            <th>{{ number_format($total_referral_a4, 2, '.', ',') }}</th>
            <th>-</th>
            <th>{{ number_format($total_financed_fee, 2, '.', ',') }}</th>
            <th>{{ number_format($total_financed_fee, 2, '.', ',') }}</th>
            <th>{{ number_format($total_prof_bal, 2, '.', ',') }}</th>
            <th>{{ number_format($total_bonus_2, 2, '.', ',') }}</th>
            <th>{{ number_format($total_bonus_3, 2, '.', ',') }}</th>
            <th>{{ number_format($total_disb, 2, '.', ',') }}</th>
            <th>{{ number_format($total_disb_used, 2, '.', ',') }}</th>
            <th>{{ number_format($total_disb_bal, 2, '.', ',') }}</th>
            <th>{{ number_format($total_actual_bal, 2, '.', ',') }}</th>

        </tr>
    </tfoot>
</table>
