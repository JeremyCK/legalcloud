<div class="row">
    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
        <div class="form-group row">
            <label class="col-md-4 col-form-label" for="bank_name"><b>Staff Name:</b></label>
            <div class="col-md-8">
                <label class=" col-form-label" id="bank_name">
                    @if (isset($staff))
                        {{ $staff->name }}
                    @else
                        -
                    @endif
                </label>
            </div>
        </div>

    </div>




</div>

<hr />

<div class="col-12">
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
                <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <h4 class="text-center">Estimation</span>
                </div>
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="fs-5 fw-semibold">RM <span id="span_claimed_2_per">
                                @if (isset($bonus))
                                    {{ number_format($bonus->staff_bonus_2_per, 2, '.', ',') }}
                                @else
                                    0.00
                                @endif
                            </span> </div>
                        <div class="text-uppercase text-medium-emphasis ">(2%)</div>
                    </div>
                    <div class="vr"></div>
                    <div class="col">
                        <div class="fs-5 fw-semibold">RM <span id="span_claimed_2_per">
                                @if (isset($bonus))
                                    {{ number_format($bonus->staff_bonus_3_per, 2, '.', ',') }}
                                @else
                                    0.00
                                @endif
                            </span> </div>
                        <div class="text-uppercase text-medium-emphasis ">(3%)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4" style="--cui-card-cap-bg: #00aced">
                <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <h4 class="text-center"> Claimed</span>
                </div>
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="fs-5 fw-semibold">RM <span id="span_accrued_2_per"> 0.00 </span> </div>
                        <div class="text-uppercase text-medium-emphasis ">(2%)</div>
                    </div>
                    <div class="vr"></div>
                    <div class="col">
                        <div class="fs-5 fw-semibold">RM <span id="span_accrued_2_per"> 0.00 </span> </div>
                        <div class="text-uppercase text-medium-emphasis ">(3%)</div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
<hr />

{{-- <table class="table  datatable" style="overflow-x: auto; width:100%;"">
    <tr style="background-color: #d8dbe0">
        <th class="text-center">Action</th>
          <th class="text-center">Ref No</th>
          <th class="text-center">Bill No</th>
          <th class="text-center">P1</th>
          <th class="text-center">P2</th>
          <th class="text-center">Agreed Fee</th>
          <th class="text-center">Targeted Collection Amt</th>
          <th class="text-center">Discount</th>
          <th class="text-center">Bonus 2%</th>
          <th class="text-center">Bonus 3%</th>
      </tr>
</table> --}}
@php
$total_staff_bonus_2_per = 0;
$total_staff_bonus_3_per = 0;
$total_staff_bonus_2_per_p1 = 0;
$total_staff_bonus_3_per_p1 = 0;
@endphp
<div style="height: 600px; overflow: auto">
    {{-- <table class="table  datatable" style="overflow-x: auto; width:100%;height:500px;"> --}}
    <table class="table  datatable">
        <thead style="background-color: #d8dbe0">
            <tr style="background-color: #d8dbe0;position:sticky">
                <th class="text-center">No</th>
                <th class="text-center">Claim ready</th>
                <th class="text-center">Bill No</th>
                <th class="text-center">Ref No</th>
                <th class="text-center">P1</th>
                {{-- <th class="text-center">P2</th> --}}
                {{-- <th class="text-center">Agreed Fee</th>
                <th class="text-center">Targeted Collection Amt</th>
                <th class="text-center">Extra/Discount</th> --}}
                <th class="text-center">Bonus 2%</th>
                <th class="text-center">Bonus 3%</th>
            </tr>
        </thead>
        <tbody>
            @if (count($rows))

                @foreach ($rows as $index => $row)
                    <tr>

                        {{-- <td class="text-right">
                    <a href="javascript:void(0)" onclick="loadBonusDetails('{{ $row->id }}')"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modalBonusDetail"
                        class="btn btn-primary">View Details</a>
                </td> --}}
                        <td>{{ $index + 1 }}</td>
                        <td class="text-center">
                            @if ($row->claim_ready == 1)
                                {{-- <span class=" badge badge-pill badge-success">Yes</span> --}}
                                <span class="badge-pill badge-success">Yes</span>
                            @else
                            <span class="badge-pill badge-warning">No</span>
                            {{-- <span class=" badge badge-pill badge-warnings">No</span> --}}
                            @endif
                        </td>
                        <td class="text-left">

                            <a href="javascript:void(0)" onclick="loadCaseQuotation('{{ $row->bill_id }}')"
                                data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                data-target="#modalBillDetail" class="btn btn-info">{{ $row->bill_no }} <i
                                    class="cil-zoom"></i> </a>
                        </td>
                        <td class="text-left"> <a href="/case/{{ $row->case_id }}"
                                target="_blank">[{{ $row->case_ref_no }}] <br />{{ $row->name }}</a>
                        </td>

                        <td class="text-right">{{ number_format($row->pfee1, 2, '.', ',') }}</td>
                        {{-- <td class="text-right">{{ number_format($row->pfee2, 2, '.', ',') }}</td> --}}
                        {{-- <td class="text-right">{{ number_format($row->agreed_fee, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format($row->targeted_collect_amount, 2, '.', ',') }}</td> --}}
                        {{-- @php
                            $discount = $row->targeted_collect_amount - $row->agreed_fee;
                        @endphp
                        <td class="text-right">
                            @if ($discount > 0)
                                <span class="text-success">{{ number_format($discount, 2, '.', ',') }}</span>
                            @elseif($discount < 0)
                                <span class="text-danger">{{ number_format($discount, 2, '.', ',') }}</span>
                            @else
                                <span>{{ number_format($discount, 2, '.', ',') }}</span>
                            @endif
                        </td> --}}
                        <td class="text-right">{{ number_format($row->bonus_2_percent, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format($row->bonus_3_percent, 2, '.', ',') }}</td>
                    </tr>
                    @php
                        $total_staff_bonus_2_per += $row->bonus_2_percent;
                        $total_staff_bonus_3_per += $row->bonus_3_percent;
                    @endphp
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="14">No data</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr style="background-color: #d8dbe0">
                <th class="text-left" colspan="9">Total</th>
                <th class="text-right">{{ number_format($total_staff_bonus_2_per, 2, '.', ',') }}</th>
                <th class="text-right">{{ number_format($total_staff_bonus_3_per, 2, '.', ',') }}</th>
            </tr>
        </tfoot>


    </table>
</div>
