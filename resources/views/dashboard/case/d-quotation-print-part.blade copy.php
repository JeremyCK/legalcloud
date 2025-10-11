{{-- <div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation "
    style="display: none;padding:50px;background-color:white !important; border:1px solid black"> --}}
<div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation "
style="display: none;padding:50px;background-color:white !important; ">
    <div class="row no-print" style="margin-bottom:30px;"> 

        <div class="col-12 ">
            <div class="row">
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col">
                            <label>Bill To</label>
                            <select class="form-control ddl-party" id="ddl_party" name="ddl_party">
                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col">
                            <label>Formal</label>
                            <select class="form-control" id="ddlPrintFormal" name="ddlPrintFormal">
                                <option value="1">Formal</option>
                                <option value="0">Non Formal</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12">
            <a href="javascript:void(0);" onclick="updateQuotationBillTo()"
                class="btn btn-info float-right no-print">Update Bill To</a>

            <a class="btn btn-success float-left no-print" href="javascript:void(0)" style="color:white;margin:0"
                data-backdrop="static" data-keyboard="false" data-target="#modalCloseFileUpdate" data-toggle="modal">
                <i style="margin-right: 10px;" class="cil-list"></i>Quick Update Master List</a>
        </div>

        <div class="col-12">
            <hr />
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-warning pull-left " onclick="printlo()" style="margin-right: 5px;">
                <span><i class="fa fa-print"></i> Print</span>
            </button>
            <a href="javascript:void(0);" onclick="cancelQuotationPrintMode()"
                class="btn btn-danger float-right no-print">Cancel</a>

        </div>

    </div>

    <div class="row " style="border-bottom: 1px solid #0066CC ">

        <div class="col-4">
            <address class="print-formal">
                <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>

                @if ($Branch->sst_no)
                    <b>SST No:</b> {{ $Branch->sst_no }}<br>
                @endif

                Advocates & Solicitors<br>
                {!! $Branch->address !!}<br>
                <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
                <b>Email</b>: {{ $Branch->email }}
            </address>
        </div>

        <div class="col-4">

            <h2 class="text-center" style="position:absolute;bottom:0;left:40%;font-weight:bold">
                Quotation
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

                {{-- <b>To:</b> <strong id="p-quo-client-name" class="text-blue">{{ $LoanCaseBillMain->bill_to }} </strong>
                @if (isset($LoanCaseBillMain->bill_to_tax_no))
                    <br /><b>Tax No</b>: {{ $LoanCaseBillMain->bill_to_tax_no }}
                @endif

                @if (isset($LoanCaseBillMain->bill_to_address))
                    <br />
                    <b>Address:</b><br />{!! nl2br(htmlspecialchars($LoanCaseBillMain->bill_to_address)) !!}
                @endif --}}
                <br>
            </address>
        </div>

        <div class="col-sm-12 invoice-col div-info-print-section">
            @include('dashboard.case.section.d-print-info')
        </div>

        <div class="col-sm-12 invoice-col ">
            <div class="invoice-details row no-margin">
                <div class="col-6"><b>Case Ref No: </b>#{{ $case->case_ref_no }} </div>
                <div class="col-6 pull-right">
                    <span class="pull-right"><b>Quotation No: {{ $LoanCaseBillMain->bill_no }}</b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?php $total_amt = 0; ?>
        <div class="col-12 table-responsive">
            <table class="table table-border" id="tbl-print-quotation">
                <tbody id="tbl-print-quotationds">

                    <tr style="padding:0px !important;border: 1px solid black;padding-left:10px;">
                        <th width="60%">Description</th>
                        <th class="text-right">Amount (RM)</th>
                        <th class="text-right">SST ({{ round($LoanCaseBillMain->sst_rate, 0) }}%)</th>
                        <th class="text-right">Total (RM)</th>
                    </tr>
                    <?php
                    $total = 0;
                    $subtotal = 0;
                    $totalSST = 0;
                    $pf_total = 0;
                    $stamP_duties_count = 0;
                    
                    $sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
                    ?>
                    @if (count($quotation))


                        @foreach ($quotation as $index => $cat)
                            <tr id="{{ $cat['category']->code }}">
                                <td colspan="5"
                                    style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
                                    <span><b
                                            style="color:white;font-size:15px">{{ $cat['category']->category }}</b></span>
                                </td>
                                <?php $subtotal = 0; ?>

                            </tr>

                            <?php $category_amount = 0; ?>
                            @foreach ($cat['account_details'] as $index => $details)

                            @if($index < 27)
                            @endif

                                <?php
                                $row_sst = 0;
                                if ($cat['category']->taxable == '1') {
                                    $row_sst = round($details->quo_amount_no_sst * $sst_rate, 2);
                                    $totalSST += $row_sst;
                                    $pf_total += $details->quo_amount_no_sst;
                                } elseif ($cat['category']->taxable == '2') {
                                    $stamP_duties_count += 1;
                                }
                                $subtotal += $row_sst;
                                $row_total = $details->quo_amount_no_sst + $row_sst;
                                ?>

                                <tr >
                                    <td
                                        style="border-left: 1px solid black;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;padding-bottom:10px !important;">
                                        {{ $index + 1 }}. {{ $details->account_name }}
                                        @if ($cat['category']->id == 1)
                                            @if ($details->item_remark)
                                                <hr style="margin-top:1px !important;margin-bottom:1px !important" />
                                                {!! $details->item_remark !!}
                                            @else
                                                @if ($details->item_desc)
                                                    <hr
                                                        style="margin-top:1px !important;margin-bottom:1px !important" />
                                                    {!! $details->item_desc !!}
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td
                                        style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">
                                        {{ number_format((float) $details->quo_amount_no_sst, 2, '.', ',') }}</td>
                                    <td
                                        style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">

                                        @if ($cat['category']->taxable == '1')
                                            {{ number_format((float) $row_sst, 2, '.', ',') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td
                                        style="text-align: right;border-right: 1px solid black;;padding:0px !important;height:25px;padding-right:10px !important;">
                                        {{ number_format((float) $row_total, 2, '.', ',') }}</td>

                                </tr>
                            @endforeach

                            <?php $total += $subtotal; ?>
                            @if ($cat['category']->taxable == '1')
                            @endif

                            @if ($cat['category']->id == 1)
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
                            @elseif($cat['category']->id == 2)
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
                        @endforeach

                        <tr style="padding:0px !important;border: 1px solid black;padding-top:10px !important;">
                            <td style="padding:0px !important;padding-left:10px !important"><span><b
                                        style="font-size:15px">Total :</b></span> </td>
                            <td style="text-align:right;padding:0px !important;padding-right:10px !important;"
                                colspan="5"><b style="font-size:15px">
                                    {{ number_format((float) $total, 2, '.', ',') }}</b></td>

                        </tr>
                    @else
                        <tr>
                            <td class="text-center" colspan="5">No data</td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>

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
                Please note that payment can be made by way of cheque / bank draft / online transfer to our firm details
                as follow :<br />
                Name : {{ $firm_name }}<br />
                Bank : PUBLIC BANK BERHAD<br />
                Account No. 3212995518 <br /><br />
                <b>We DO NOT accept payment by cash</b> <br /><br />
                <i>* Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration
                    of one (1) month from the date of the bill until the date of the actual payment in accordance with
                    clause 6 of the Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976. E & OE
                    *</i>
            @else
                Please Note:
                1. Please issue cheque/bank draft in favour of "{{ $firm_name }}" or deposit to:
                <br />
                Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of one
                (1)
                month from the date of the bill until the date of the actual payment in accordance with clause 6 of the
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

</div>
