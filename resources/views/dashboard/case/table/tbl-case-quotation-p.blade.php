<!-- <thead>
          <tr>
            <th>#</th>
            <th>Description</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Subtotal</th>
          </tr>
        </thead> -->
<tbody id="tbl-print-quotationds">

  <tr style="padding:0px !important;border: 1px solid black;padding-left:10px;">
    <!-- <th>#</th> -->
    <th width="60%">Description</th>
    <th class="text-right">Amount (RM)</th>
    <th class="text-right">SST ({{ round($LoanCaseBillMain->sst_rate, 0)  }}%)</th>
    <th class="text-right">Total (RM)</th>
  </tr>
  <?php
  $total = 0;
  $subtotal = 0;
  $totalSST = 0;
  $pf_total = 0;
  $stamP_duties_count = 0;

  $sst_rate = $LoanCaseBillMain->sst_rate *0.01;
  ?>
  @if(count($quotation))


  @foreach($quotation as $index => $cat)
  <tr id="{{$cat['category']->code}}">
    <td colspan="5" style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
      <span><b style="color:white;font-size:15px">{{$cat['category']->category}}</b></span>
    </td>
    <?php $subtotal = 0 ?>

  </tr>
  <!-- @if($cat['category']->id == 1)
  <tr>
    <td colspan="3">
      {{$LoanCaseBillMain->pf_desc}}
    </td>
    <td colspan="2">
    </td>
  </tr>
  @endif -->

  <?php $category_amount = 0 ?>
  @foreach($cat['account_details'] as $index => $details)
  <!-- <?php $subtotal += $details->quo_amount_no_sst ?> -->

  <?php
  $row_sst = 0;
  if ($cat['category']->taxable == "1") {
    // $row_sst = number_format((float)($details->quo_amount * 0.06), 2, '.', ',');
    $row_sst = round(($details->quo_amount_no_sst * $sst_rate), 2);
    $totalSST += $row_sst;
    $pf_total += $details->quo_amount_no_sst;
  } elseif ($cat['category']->taxable == "2") {
    $stamP_duties_count += 1;
  }
  $subtotal += $row_sst;
  $row_total = $details->quo_amount_no_sst + $row_sst;
  ?>

  <tr>
    <td style="border-left: 1px solid black;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;padding-bottom:10px !important;">{{ $index + 1 }}. {{ $details->account_name }} @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
      @if ($cat['category']->id == 1)
  

      @if($details->item_remark)
          <hr style="margin-top:1px !important;margin-bottom:1px !important" />
          {!! $details->item_remark !!}
      @else
          @if($details->item_desc)
          <hr style="margin-top:1px !important;margin-bottom:1px !important" />
          {!! $details->item_desc !!}
          @endif
      @endif
  
  @endif
    </td>
    <td style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">{{ number_format((float)($details->quo_amount_no_sst), 2, '.', ',')}}</td>
    <td style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">

      @if($cat['category']->taxable == "1")
      {{ number_format((float)($row_sst), 2, '.', ',') }}
      @else
      -
      @endif
    </td>
    <td style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">{{ number_format((float)($row_total), 2, '.', ',') }}</td>

  </tr>
  @endforeach

  <?php $total += $subtotal ?>
  @if($cat['category']->taxable == "1")
  <!-- <tr>
  <td colspan="1">{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
  <td style="text-align:right" colspan="5">{{ number_format((float)($subtotal*0.06), 2, '.', '') }}</td>
</tr> -->
  @endif

  @if($cat['category']->id == 1)
  <tr style="padding:0px !important;border: 1px solid black">
    <td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;">Subtotal:</td>
    <td style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">{{ number_format((float)$pf_total, 2, '.', ',')}}</td>
    <td style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">{{ number_format((float)$totalSST, 2, '.', ',');}}</td>
    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;">{{ number_format((float)$subtotal, 2, '.', ',');}}</td>
  </tr>
  @elseif($cat['category']->id == 2)
    <!-- @if ($stamP_duties_count >0)
    <tr style="padding:0px !important;border: 1px solid black">
    <td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="3">{{ number_format((float)$subtotal, 2, '.', ',');}}</td>
  </tr>
  <?php
  if ($stamP_duties_count <=0)
  {
    $eleId = "SD";
    $dom = new DOMDocument();
    $dom->loadHTML($pageUrl);
    $target_elem = $dom->getElementById($eleId);
    if ($target_elem) {
        $target_elem-> setAttribute('style','display: none;');
    }
  }
     
  ?>
    @endif -->

    <tr style="padding:0px !important;border: 1px solid black">
    <td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;" colspan="3">{{ number_format((float)$subtotal, 2, '.', ',');}}</td>
  </tr>
  @else
  <tr style="padding:0px !important;border: 1px solid black">
    <td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;" colspan="3">{{ number_format((float)$subtotal, 2, '.', ',');}}</td>
  </tr>
  @endif


  @endforeach



  <tr style="padding:0px !important;border: 1px solid black;padding-top:10px !important;">
    <td style="padding:0px !important;padding-left:10px !important"><span><b style="font-size:15px">Total :</b></span> </td>
    <td style="text-align:right;padding:0px !important;padding-right:10px !important;" colspan="5"><b style="font-size:15px"> {{ number_format((float)$total, 2, '.', ',');  }}</b></td>

  </tr>
  @else
  <tr>
    <td class="text-center" colspan="5">No data</td>
  </tr>
  @endif

</tbody>