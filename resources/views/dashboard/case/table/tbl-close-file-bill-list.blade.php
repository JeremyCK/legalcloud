@php
$total_collected_amt = 0;
$total_used_amt = 0;
$total_bal = 0;
$count=0;
@endphp

@if (count($LoanCaseBillMain))
    @foreach ($LoanCaseBillMain as $index => $bill)
        <tr>
            <td>
                <div class="checkbox" style="display: none">
                    <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked >
                    <label for="chk_close_file_{{$count}}"></label>
                </div>
                <b>Bill No:</b> {{ $bill->bill_no }}
            </td>
            <td>
              <b>Collected amount:</b> {{ number_format($bill->collected_amt, 2, '.', ',') }} <br/>
              <b>CA Used Amount:</b> {{ number_format($bill->total_disb, 2, '.', ',') }} <br/>
              @if (isset($bill->total_oa_disb) && $bill->total_oa_disb > 0.01)
              <b style="color: red;">OA Used Amount:</b> <span style="color: red;">{{ number_format($bill->total_oa_disb, 2, '.', ',') }}</span> <br/>
              @endif
            </td>
            {{-- <td class="text-right">{{ number_format($bill->collected_amt, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($bill->used_amt, 2, '.', ',') }}</td> --}}
            <td class="text-right">{{ number_format($bill->collected_amt - $bill->used_amt, 2, '.', ',') }}
              <input type="hidden" id="sum_close_file_{{$count}}" value="{{$bill->collected_amt - $bill->total_disb}}" />
              <input type="hidden" id="bill_no_close_file_{{$count}}" value="{{$bill->id}}" />
            </td>
        </tr>
        @php
        $total_collected_amt += $bill->collected_amt;
        $total_used_amt += $bill->total_disb;
        $total_bal += ($bill->collected_amt - $bill->total_disb );

        $count += 1;
        @endphp
    @endforeach
@endif

@if (count($LoanCaseTrustMain))
    @foreach ($LoanCaseTrustMain as $index => $bill)
        <tr>
            <td>
                <div class="checkbox" style="display: none">
                    <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked>
                    <label for="chk_close_file_{{$count}}">Trust fund</label>
                </div>
                <b>Trust Fund</b>
            </td>
            <td>
              <b>Collected amount:</b> {{ number_format($bill->total_received, 2, '.', ',') }} <br/>
              <b>Used amount:</b> {{ number_format($bill->total_used, 2, '.', ',') }} <br/>
            </td>
            {{-- <td class="text-right">{{ number_format($bill->total_received, 2, '.', ',') }}</td>
            <td class="text-right">{{ number_format($bill->total_used, 2, '.', ',') }}</td> --}}
            <td class="text-right">{{ number_format($bill->total_received - $bill->total_used, 2, '.', ',') }}
              <input type="hidden" id="sum_close_file_{{$count}}" value="{{$bill->total_received - $bill->total_used}}" />
              <input type="hidden" id="bill_no_close_file_{{$count}}" value="{{$bill->id}}" />
            </td>
        </tr>
        @php
        $total_collected_amt += $bill->total_received;
        $total_used_amt += $bill->total_used;
        $total_bal += ($bill->total_received - $bill->total_used);

        $count += 1;
        @endphp
    @endforeach
@endif

@if (count($TransferFeeDetails))
    @foreach ($TransferFeeDetails as $index => $bill)
        <tr>
            <td>
                <div class="checkbox" style="display: none">
                    <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked>
                    <label for="chk_close_file_{{$count}}">Transfer Fee </label>
                </div>
                <b>Transfer Fee: </b>  <a target="_blank" href="/transfer-fee/{{ $bill->transfer_id }}">{{ $bill->transaction_id }} >></a>
            </td>
            <td >
              {{ $bill->purpose }}
            </td>
            <td class="text-right">({{ number_format($bill->transfer_amount, 2, '.', ',') }})
              <input type="hidden" id="sum_close_file_{{$count}}" value="-{{$bill->transfer_amount}}" />
            </td>
        </tr>

        
        @php

        $count += 1;
        @endphp
        <tr>
          <td>
              <div class="checkbox" style="display: none">
                  <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked>
                  <label for="chk_close_file_{{$count}}">SST</label>
              </div>
              <b>SST</b> 
          </td>
          <td class="text-right">-</td>
          <td class="text-right">({{ number_format($bill->sst_amount, 2, '.', ',') }})
            <input type="hidden" id="sum_close_file_{{$count}}" value="-{{$bill->sst_amount}}" />
          </td>
      </tr>
        @php
        $total_bal -= ($bill->transfer_amount + $bill->sst_amount);

        $count += 1;
        @endphp
    @endforeach
  @endif

@if (isset($ReimbursementDetails) && count($ReimbursementDetails))
    @foreach ($ReimbursementDetails as $index => $reimb)
        {{-- Show Reimbursement entry - same concept as Transfer Fee --}}
        {{-- If already transferred, show in brackets (negative) like Transfer Fee --}}
        @if ($reimb->reimbursement_amount > 0.01)
        <tr>
            <td>
                <div class="checkbox" style="display: none">
                    <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked>
                    <label for="chk_close_file_{{$count}}">Reimbursement</label>
                </div>
                <b>Reimbursement: </b> Bill No: {{ $reimb->bill_no }}
                @if (isset($reimb->transaction_id))
                    <a target="_blank" href="/transfer-fee/{{ $reimb->transfer_id }}">{{ $reimb->transaction_id }} >></a>
                @endif
            </td>
            <td>
                Reimbursement
            </td>
            <td class="text-right">
                @if (isset($reimb->is_transferred) && $reimb->is_transferred)
                    ({{ number_format($reimb->reimbursement_amount, 2, '.', ',') }})
                    <input type="hidden" id="sum_close_file_{{$count}}" value="-{{$reimb->reimbursement_amount}}" />
                @else
                    {{ number_format($reimb->reimbursement_amount, 2, '.', ',') }}
                    <input type="hidden" id="sum_close_file_{{$count}}" value="{{$reimb->reimbursement_amount}}" />
                @endif
              <input type="hidden" id="bill_no_close_file_{{$count}}" value="{{$reimb->bill_id}}" />
            </td>
        </tr>
        @php
        if (isset($reimb->is_transferred) && $reimb->is_transferred) {
            $total_bal -= $reimb->reimbursement_amount; // Subtract if transferred (negative)
        } else {
            $total_bal += $reimb->reimbursement_amount; // Add if not transferred (positive)
        }
        $count += 1;
        @endphp
        @endif
        
        {{-- Show Reimbursement SST entry - same concept as Transfer Fee SST --}}
        {{-- If already transferred, show in brackets (negative) like Transfer Fee SST --}}
        @if ($reimb->reimbursement_sst > 0.01)
        <tr>
            <td>
                <div class="checkbox" style="display: none">
                    <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked>
                    <label for="chk_close_file_{{$count}}">Reimbursement SST</label>
                </div>
                <b>Reimbursement SST</b> @if ($reimb->reimbursement_amount <= 0.01) - Bill No: {{ $reimb->bill_no }} @endif
            </td>
            <td>
                @if ($reimb->reimbursement_amount <= 0.01)
                    Reimbursement SST
                @else
                    -
                @endif
            </td>
            <td class="text-right">
                @if (isset($reimb->is_transferred_sst) && $reimb->is_transferred_sst)
                    ({{ number_format($reimb->reimbursement_sst, 2, '.', ',') }})
                    <input type="hidden" id="sum_close_file_{{$count}}" value="-{{$reimb->reimbursement_sst}}" />
                @else
                    {{ number_format($reimb->reimbursement_sst, 2, '.', ',') }}
                    <input type="hidden" id="sum_close_file_{{$count}}" value="{{$reimb->reimbursement_sst}}" />
                @endif
              @if ($reimb->reimbursement_amount <= 0.01)
                <input type="hidden" id="bill_no_close_file_{{$count}}" value="{{$reimb->bill_id}}" />
              @endif
            </td>
        </tr>
        @php
        if (isset($reimb->is_transferred_sst) && $reimb->is_transferred_sst) {
            $total_bal -= $reimb->reimbursement_sst; // Subtract if transferred (negative)
        } else {
            $total_bal += $reimb->reimbursement_sst; // Add if not transferred (positive)
        }
        $count += 1;
        @endphp
        @endif
    @endforeach
@endif

  @if (count($JournalEntry))
      @foreach ($JournalEntry as $index => $bill)
          <tr>
              <td>
                  <div class="checkbox" style="display: none">
                      <input onchange="updateCloseFileTotalAmt({{$count}})" type="checkbox" name="close_file_bill" value="{{$count}}" id="chk_close_file_{{$count}}" checked>
                      <label for="chk_close_file_{{$count}}">Journal Entry </label>
                  </div>
                  <b>Journal Entry: </b>  <a target="_blank" href="/journal-entry/{{ $bill->id }}">{{ $bill->journal_no }} >></a>
              </td>
              <td >
                {{ $bill->name }}
              </td>
              @php
              $je_amt = $bill->total_credit - $bill->total_debit;
              @endphp

              <td class="text-right">
                @if($bill->transaction_type == 'C')
                ({{ number_format($bill->amount, 2, '.', ',') }})
                @else
                {{ number_format($bill->amount, 2, '.', ',') }}
                @endif
              
              
              @if($bill->transaction_type == 'C')
                <input type="hidden" id="sum_close_file_{{$count}}" value="-{{$bill->amount}}" />
              @else
                <input type="hidden" id="sum_close_file_{{$count}}" value="{{$bill->amount}}" />
              @endif
              
             
              </td>

             
              {{-- <td class="text-right">
                 @if($je_amt >= 0)
                 {{ number_format($je_amt, 2, '.', ',') }}
                  @else
                  ({{ number_format($je_amt, 2, '.', ',') }})
                  @endif
                <input type="hidden" id="sum_close_file_{{$count}}" value="-{{$je_amt}}" />
              </td> --}}
          </tr>

      

          @php
          if($bill->transaction_type == 'C')
          {
            $total_bal -= $bill->amount;
          }
          else
          {
            $total_bal += $bill->amount;
          }
          
          $count += 1;
          @endphp
      @endforeach
  @endif



<tr style="background-color: gray;color:white">
  <td colspan="2">
      Total Bal to transfer
  </td>
  <td class="text-right">{{ number_format($total_bal, 2, '.', ',') }}
  </td>
</tr>

<tr >
  <td colspan="2">
      Ledger amount
  </td>
  <td class="text-right">{{ number_format($total_credit-$total_debit, 2, '.', ',') }}
  </td>
</tr>

@php
$text_style='';

if (number_format( $total_bal - ($total_credit-$total_debit), 2, '.', ',') != 0)
{
  $text_style='text-danger';
}
@endphp

<tr style="border: 1px solid black !important;">
  <td colspan="2" style="border: 1px solid black !important;">
      Ledger after close file
  </td>
  <input type="hidden" id="cf_ledger_bal" value="{{ number_format( $total_bal - ($total_credit-$total_debit), 2, '.', ',') }}" />
  <td style="border: 1px solid black !important;" class="text-right {{ $text_style }}">{{ number_format( $total_bal - ($total_credit-$total_debit), 2, '.', ',') }}
  </td>
</tr>

@if (count($VoucherMain))
<tr>
  <td colspan="3"></td>
</tr>
<tr>
  <td colspan="3"></td>
</tr>


<tr>
  <td colspan="3"> <h4>{{count($VoucherMain)}} Inprogress Voucher</h2> <span style="color: red">* There are {{count($VoucherMain)}} inprogress voucher(s) haven't process </span></td>
</tr>
<tr style="background-color: black;color:white">
  <td >Voucher No</td>
  <td >Remark</td>
  <td  class="text-right" >Amount</td>
</tr>

  @foreach ($VoucherMain as $index => $bill)
  <tr>
    <td ><a target="_blank" href="/voucher/{{ $bill->id }}/edit">{{ $bill->voucher_no }} >></a>  </td>
    <td >{{ $bill->remark }}</td>
    <td  class="text-right"> {{ number_format($bill->total_amount, 2, '.', ',') }}</td>
  </tr>
  @endforeach
@endif