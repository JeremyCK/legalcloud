<div class="row test" style="border-bottom: 1px solid #0066CC; margin-bottom:20px ">

    <div class="col-4">
        <address class="print-formal">
            <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>

            @if ($Branch->sst_no)
                <b>SST No:</b> {{ $Branch->sst_no }}<br>
            @endif

            Advocates & Solicitors<br>
            {!! $Branch->address !!}<br>
            <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
            {{-- <b>Email</b>: {{ $Branch->email }} --}}
        </address>
    </div>

    <div class="col-4">

        <h2 class="text-center" style="position:absolute;bottom:0;left:40%;font-weight:bold">
            Invoice
        </h2>

    </div>

    <div class="col-4">
        <h2 class="text-center" style="position:absolute;bottom:0;right:0">
            <small class="pull-right">Date: {{ date('d-m-Y', strtotime($LoanCaseBillMain->created_at)) }} </small>
        </h2>
    </div>

</div>


<div class="row invoice-info" style="margin-top:20px;">
    <div class="col-6 invoice-col">
        <address class="row party_info">
            <div class="col-2">
                <b>To:</b>
            </div>
            <div class="col-10 ">
                <strong id="p-quo-client-name" class="text-blue">{{ $LoanCaseBillMain->bill_to }} </strong>
            </div>

            @if (isset($LoanCaseBillMain->bill_to_tax_no))
                <div class="col-2">
                    <b>Tax No:</b>
                </div>
                <div class="col-10 ">
                    {{ $LoanCaseBillMain->bill_to_tax_no }}
                </div>
            @endif

            @if (isset($LoanCaseBillMain->bill_to_address))
                <div class="col-2">
                    <b>Address:</b>
                </div>
                <div class="col-10 ">
                    {!! nl2br(htmlspecialchars($LoanCaseBillMain->bill_to_address)) !!}
                </div>
            @endif
            <br>
        </address>
    </div>

    <div class="col-6 invoice-col ">

        <address class="row party_info">
            <div class="col-4">
                <b>Case Ref No:</b>
            </div>
            <div class="col-6 ">
                #{{ $case->case_ref_no }}
            </div>

            <div class="col-4">
                <b>Quotation No:</b>
            </div>
            <div class="col-6 ">
                {{ $LoanCaseBillMain->bill_no }}
            </div>
        </address>


    </div>
    @if ($index == 0)
        <div class="col-sm-12 invoice-col div-info-print-section">
            @include('dashboard.case.section.d-print-info')
        </div>
    @endif
</div>



<div class="row">
    <?php $total_amt = 0; ?>
    <div class="col-12 table-responsive">
        <table class="table table-border" id="tbl-print-quotation" style="border-bottom: 1px solid black">
            <tbody id="tbl-print-quotationds">

                <tr style="padding:0px !important;border: 1px solid black !important;padding-left:10px;">
                    <th style="border-top: 1px solid black !important" width="60%">Description</th>
                    <th style="border-top: 1px solid black !important" class="text-right">Amount (RM)</th>
                    <th style="border-top: 1px solid black !important" class="text-right">SST
                        ({{ round($LoanCaseBillMain->sst_rate, 0) }}%)</th>
                    <th style="border-top: 1px solid black !important" class="text-right">Total (RM)</th>
                </tr>
                <?php
                $total = 0;
                $subtotal = 0;
                $totalSST = 0;
                $pf_total = 0;
                $stamP_duties_count = 0;
                
                $row_count = 0;
                $sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
                ?>

                @if (count($rowItem))

                    @foreach ($rowItem as $index2 => $row)
                        @if ($row['row'] == 'title')
                            <tr id="{{ $row['category']->code }}">
                                <td colspan="5"
                                    style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
                                    <span><b
                                            style="color:white;font-size:13px">{{ $row['category']->category }}</b></span>
                                </td>
                                <?php $subtotal = 0; ?>

                            </tr>
                        @elseif($row['row'] == 'item')
                            @php
                                $details = $row['account_details'];
                                $row_sst = 0;
                                $row_total = 0;

                                if ($row['category']->taxable == '1') {
                                    $row_sst = round($details->quo_amount_no_sst * $sst_rate, 2);
                                    $totalSST += $row_sst;
                                    $pf_total += $details->quo_amount_no_sst;
                                } elseif ($row['category']->taxable == '2') {
                                    $stamP_duties_count += 1;
                                }
                                $subtotal += $row_sst;
                                $row_total = $details->quo_amount_no_sst + $row_sst;

                                $row_count += 1;
                            @endphp
                            <tr style="">
                                <td
                                    style="border: 1px solid black !important;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;vertical-align: middle !important ">
                                    {{ $row_count }}. {{ $details->account_name }}
                                    @if ($row['category']->id == 1)
                                        @if ($details->item_remark)
                                            <hr style="margin-top:1px !important;margin-bottom:1px !important" />
                                            {!! $details->item_remark !!}
                                        @else
                                            @if ($details->item_desc)
                                                <hr style="margin-top:1px !important;margin-bottom:1px !important" />
                                                {!! $details->item_desc !!}
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td
                                    style="text-align: right;border: 1px solid black !important;;padding:0px !important;height:25px;padding-right:10px !important;vertical-align: middle !important">
                                    {{ number_format((float) $details->quo_amount_no_sst, 2, '.', ',') }}</td>
                                <td
                                    style="text-align: right;border: 1px solid black !important;;padding:0px !important;height:25px;padding-right:10px !important;vertical-align: middle !important">

                                    @if ($row['category']->taxable == '1')
                                        {{ number_format((float) $row_sst, 2, '.', ',') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td
                                    style="text-align: right;border: 1px solid black !important;;padding:0px !important;height:25px;padding-right:10px !important;vertical-align: middle !important">
                                    {{ number_format((float) $row_total, 2, '.', ',') }}</td>

                            </tr>
                        @elseif($row['row'] == 'subtotal')
                            @if ($row['category']->id == 1)
                                <tr style="padding:0px !important;border: 1px solid black">
                                    <td class="text-left"
                                        style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;">
                                        Subtotal:</td>
                                    <td
                                        style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">
                                        {{ number_format((float) $pf_total, 2, '.', ',') }}</td>
                                    <td
                                        style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">
                                        {{ number_format((float) $totalSST, 2, '.', ',') }}</td>
                                    <td
                                        style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;">
                                        {{ number_format((float) $subtotal, 2, '.', ',') }}</td>
                                </tr>
                            @elseif($row['category']->id == 2)
                                <tr style="padding:0px !important;border: 1px solid black">
                                    <td class="text-left"
                                        style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;"
                                        colspan="2">Subtotal:</td>
                                    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;"
                                        colspan="3">{{ number_format((float) $subtotal, 2, '.', ',') }}</td>
                                </tr>
                            @else
                                <tr style="padding:0px !important;border: 1px solid black">
                                    <td class="text-left"
                                        style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;"
                                        colspan="2">Subtotal:</td>
                                    <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;"
                                        colspan="3">{{ number_format((float) $subtotal, 2, '.', ',') }}</td>
                                </tr>
                            @endif
                        @endif
                    @endforeach

                @endif


            </tbody>
        </table>
    </div>

    @if (($index+1) == $total_page)
        <div class="row print-formal">
            <div class="col-12" style="border: 1px solid black;">
                @php
                    $firm_name = 'L H YEO & CO';
                    if ($case->branch_id == '4') {
                        $firm_name = 'Ramakrishnan & Co';
                    } elseif ($case->branch_id == '6') {
                        $firm_name = 'ISMAIL & LIM';
                    }
                @endphp

                @if ($case->branch_id == 1)
                    Please note that payment can be made by way of cheque / bank draft / online transfer to our firm
                    details
                    as follow :<br />
                    <b>Name</b> : {{ $firm_name }}<br />
                    <b>Bank</b> : PUBLIC BANK BERHAD<br />
                    <b>Account No</b>: 3212995518 <br /><br />
                    <b>We DO NOT accept payment by cash</b> <br /><br />
                    <i>* Interest at 8% per annum on the aforesaid amount shall be charged with effect from the
                        expiration
                        of one (1) month from the date of the bill until the date of the actual payment in accordance
                        with
                        clause 6 of the Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976. E &
                        OE
                        *</i>
                @else
                    Please Note:
                    1. Please issue cheque/bank draft in favour of "{{ $firm_name }}" or deposit to:
                    <br />
                    Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of
                    one
                    (1)
                    month from the date of the bill until the date of the actual payment in accordance with clause 6 of
                    the
                    Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976.
                    E & OE<br />

                    2. Payment by cheque should be crossed and made payable to "{{ $firm_name }}" or transfer to
                    @if ($case->branch_id == '1')
                        Public Bank Berhad 3212995518.
                    @elseif($case->branch_id == '2')
                        CIMB Bank Berhad 8605606123
                    @elseif($case->branch_id == '3')
                        PBB CLIENT ACC 3230791418
                    @elseif($case->branch_id == '4')
                        PBB CLIENT ACC 3231832819
                    @elseif($case->branch_id == '5')
                        PBB CLIENT ACC 3232880915
                    @elseif($case->branch_id == '6')
                        CIMB Bank Berhad 8605514414
                    @endif
                @endif

            </div>
        </div>
    @endif
    {{-- <div class="divFooter" style="width: 100%">

        <div class=" row">
            <div class="col-6">
                <span class=" float-left" style="margin-left:3%">Ref No: {{ $case->case_ref_no }}</span>
            </div>
            <div class="col-6">
                <span class=" float-right" style="margin-right:3%">Page {{ $index+1 }}/{{ $total_page }}</span>
            </div>
        </div>


    </div> --}}
</div>
