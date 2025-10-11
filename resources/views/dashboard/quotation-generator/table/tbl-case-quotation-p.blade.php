
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
  
  <tr>
    <th>#1</th>
    <th>Description</th>
    <th class="text-right">RM</th>
    <th class="text-right">SST (6%)</th>
    <th class="text-right">RM</th>
  </tr>
  <?php
  $total = 0;
  $subtotal = 0;
  ?>
  @if(count($quotation))


  @foreach($quotation as $index => $cat)
  <tr >
    <td colspan="5"><h4><b>{{$cat['category']->category}}</b></h4></td>
    <?php $subtotal = 0 ?>

  </tr>
  @if($cat['category']->id == 1)
  <tr>
    <td colspan="3">
      <!-- To our professional charges in taking instructions to act. To attend to client and advising generally. To prepare peruse finalise the Sale and Purchase Agreement, the Memorandum of Transfer/Deed of Assignment. To attend to adjudication and stamping of the same and to all other attendances not specifically mentioned. -->
    
    </td>
    <td colspan="2">
    </td>
  </tr>
  @endif

  <?php $category_amount = 0 ?>
  @foreach($cat['account_details'] as $index => $details)
  <!-- <?php $subtotal += $details->amount ?> -->

  <?php
  $row_sst = 0;
  if($cat['category']->taxable == "1")
  {
    // $row_sst = number_format((float)($details->amount * 0.06), 2, '.', ',');
    $row_sst = (float)($details->amount * 0.06);
  }
   $subtotal += $row_sst ;
  $row_total = $details->amount + $row_sst;
  ?>

  <tr>
    <td class="text-center" style="border-right: 1px solid black;">{{ $index + 1 }}</td>
    <td style="border-right: 1px solid black;">{{ $details->account_name }}
      @if ($cat['category']->id == 1)
          @if($details->item_desc)
          <hr/>
          {!! $details->item_desc !!}
          @endif
      
      @endif
    </td>
    <td style="text-align: right;border-right: 1px solid black;">{{ number_format((float)($details->amount), 2, '.', ',')}}</td>
    <td style="text-align: right;border-right: 1px solid black;">
   
    @if($cat['category']->taxable == "1")
    {{  number_format((float)($row_sst), 2, '.', ',') }}
    @else
    -
    @endif</td>
    <td style="text-align: right;border-right: 1px solid black;">{{ number_format((float)($row_total), 2, '.', ',') }}</td>

  </tr>
  @endforeach

  <?php $total += $subtotal ?>
  @if($cat['category']->taxable == "1")
  <!-- <tr>
  <td colspan="1">{{$cat['category']->percentage}}% GOVERNMENT TAX </td>
  <td style="text-align:right" colspan="5">{{ number_format((float)($subtotal*0.06), 2, '.', '') }}</td>
</tr> -->
  @endif

  <tr style="">
    <td class="text-left" style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="2">Subtotal:</td>
    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;" colspan="3">{{ number_format((float)$subtotal, 2, '.', ',');}}</td>
  </tr>
  @endforeach



  <tr>
    <td><h4><b>Total :</b></h4> </td>
    <td style="text-align:right" colspan="5"> {{ number_format((float)$total, 2, '.', ',');  }}</td>

  </tr>
  @else
  <tr>
    <td class="text-center" colspan="5">No data</td>
  </tr>
  @endif

</tbody>