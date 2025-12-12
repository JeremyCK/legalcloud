<div id="dInvoice-p" class="div2 invoice printableArea d_operation"
    style="display: none;padding:30px;background-color:white !important; ">
    <title>invoice_{{ $case->case_ref_no }}</title>

    <div class="row no-print" style="margin-bottom:30px;">

        <div class="col-12 div_inv_bill_to hide">
            <div class="row">
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col">
                            <label>Bill To</label>
                            <select class="form-control  ddl-party" id="ddl_party_inv" name="ddl_party_inv">

                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col">
                            <label>Formal</label>
                            <select class="form-control" id="ddlPrintFormalInv" name="ddlPrintFormalInv">
                                <option value="1">Formal</option>
                                <option value="0">Non Formal</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


        </div>



        <div class="col-12 hide">
            <a href="javascript:void(0);" onclick="updateInvoiceTo()" class="btn btn-info float-right no-print">Update
                Invoice To</a>


            {{-- <a class="btn btn-success float-left no-print" href="javascript:void(0)" style="color:white;margin:0"
                data-backdrop="static" data-keyboard="false" data-target="#modalCloseFileUpdate" data-toggle="modal">
                <i style="margin-right: 10px;" class="cil-list"></i>Quick Update Master List</a> --}}
        </div>

        
        <div class="col-12">
            <hr />
        </div>


        <div class="col-12">
            <button type="button" class="btn btn-warning pull-left " onclick="printloinv()" style="margin-right: 5px;">
                <span><i class="fa fa-print"></i> Print</span>
            </button>
            <a href="javascript:void(0);" onclick="cancelInvoicePrintMode()"
                class="btn btn-danger float-right no-print">Cancel</a>

        </div>

    </div>

    @php
        $total_amount_main = 0;
        $total_sub_main = 0;
        $total_sst_main = 0;
        $row_count = 0;
    @endphp

    @foreach ($pieces_inv as $index => $row)
        {{-- @include('dashboard.case.d-invoice-print-part', [
        'LoanCaseBillMain' => $LoanCaseBillMain,
        'current_user' => $current_user,
        'quotation' => $invoice,
        'rowItem' => $row,
        'case' => $case,
        'index' => $index,
        'total_page' => count($pieces),
        'Branch' => $Branch,
    ]) --}}

        @php
            $total_page = count($pieces_inv);
            $rowItem = $row;
        @endphp

        <div class="row" style="border-bottom: 1px solid #0066CC ">

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
                    Invoice
                </h2>

            </div>

            <div class="col-4">
                <h2 class="text-center" style="position:absolute;bottom:0;right:0">
                    <small class="pull-right">Date: <span
                            class="invoice-date">{{ date('d-m-Y', strtotime($LoanCaseBillMain->invoice_date)) }}</span>
                    </small>
                </h2>
            </div>

        </div>


        <div class="row invoice-info" style="margin-top:20px;">
            <div class="col-6 invoice-col">

                <address class="row party_invoice_info">
                    <div class="col-2">
                        <b>To:</b>
                    </div>
                    <div class="col-10 ">
                        <strong id="p-quo-client-name" class="text-blue">
                            @if(isset($InvoiceBillingParty) && $InvoiceBillingParty)
                                {{ $InvoiceBillingParty->customer_name }}
                            @else
                                {{ $LoanCaseBillMain->invoice_to }}
                            @endif
                        </strong>
                    </div>

                    @if (isset($InvoiceBillingParty) && $InvoiceBillingParty && isset($InvoiceBillingParty->tin) && $InvoiceBillingParty->tin)
                        <div class="col-2">
                            <b>Tax No:</b>
                        </div>
                        <div class="col-10 ">
                            {{ $InvoiceBillingParty->tin }}
                        </div>
                    @elseif (isset($LoanCaseBillMain->invoice_to_tax_no))
                        <div class="col-2">
                            <b>Tax No:</b>
                        </div>
                        <div class="col-10 ">
                            {{ $LoanCaseBillMain->invoice_to_tax_no }}
                        </div>
                    @endif

                    @if (isset($InvoiceBillingParty) && $InvoiceBillingParty)
                        @php
                            $addressParts = array_filter([
                                $InvoiceBillingParty->address_1 ?? '',
                                $InvoiceBillingParty->address_2 ?? '',
                                $InvoiceBillingParty->address_3 ?? '',
                                $InvoiceBillingParty->address_4 ?? '',
                            ]);
                            $postcode = trim($InvoiceBillingParty->postcode ?? '');
                            $city = trim($InvoiceBillingParty->city ?? '');
                            $state = trim($InvoiceBillingParty->state ?? '');
                            
                            // Build the postcode/city/state line
                            $locationLine = trim(implode(' ', array_filter([$postcode, $city, $state])));
                            
                            // Check if the location line is already included in any address part
                            $locationAlreadyIncluded = false;
                            if ($locationLine) {
                                $locationLower = strtolower($locationLine);
                                foreach ($addressParts as $part) {
                                    $partLower = strtolower(trim($part));
                                    // Check if the full location line or its components are already in the address part
                                    if ($partLower === $locationLower || 
                                        ($postcode && strpos($partLower, strtolower($postcode)) !== false && 
                                         ($city && strpos($partLower, strtolower($city)) !== false || 
                                          $state && strpos($partLower, strtolower($state)) !== false))) {
                                        $locationAlreadyIncluded = true;
                                        break;
                                    }
                                }
                            }
                            
                            $fullAddress = implode("\n", $addressParts);
                            // Only add location line if it's not already included in address parts
                            if ($locationLine && !$locationAlreadyIncluded) {
                                $fullAddress .= ($fullAddress ? "\n" : '') . $locationLine;
                            }
                        @endphp
                        @if($fullAddress)
                            <div class="col-2">
                                <b>Address:</b>
                            </div>
                            <div class="col-10 ">
                                {!! nl2br(htmlspecialchars($fullAddress)) !!}
                            </div>
                        @endif
                    @elseif (isset($LoanCaseBillMain->invoice_to_address))
                        <div class="col-2">
                            <b>Address:</b>
                        </div>
                        <div class="col-10 ">
                            {!! nl2br(htmlspecialchars($LoanCaseBillMain->invoice_to_address)) !!}
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

            <div class="col-6 invoice-cols ">

                <address class="row party_info">
                    <div class="col-4">
                        <b>Case Ref No:</b>
                    </div>
                    <div class="col-6 ">
                        {{ $case->case_ref_no }}
                    </div>
                    @if(isset($purchaser_financier_ref_no) && $purchaser_financier_ref_no && $purchaser_financier_ref_no->value)
                        <div class="col-4 div_bank_ref_no">
                            <b>Bank Ref No:</b>
                        </div>
                        <div class="col-6 div_bank_ref_no" id="purchaser_file_ref">
                            {{ $purchaser_financier_ref_no->value }}
                        </div>
                    @endif


                    <div class="col-4">
                        <b>Invoice No:</b>
                    </div>
                    <div class="col-6 " id="inv_no_print">
                        @if(isset($invoiceMain) && $invoiceMain)
                            {{ $invoiceMain->invoice_no }}
                        @else
                            {{ $LoanCaseBillMain->invoice_no }}
                        @endif
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
                <table class="table table-border" id="tbl-print-invoice" style="border-bottom: 1px solid black">
                    <tbody id="tbl-print-invoices">

                        <tr style="padding:0px !important;border: 1px solid black !important;padding-left:10px;">
                            <th style="border-top: 1px solid black !important;border-bottom: 1px solid black !important"
                                width="60%"><b>Description</b></th>
                            <th style="border-top: 1px solid black !important;border-bottom: 1px solid black !important"
                                class="text-right"><b>Amount (RM)</b></th>
                            <th style="border-top: 1px solid black !important;border-bottom: 1px solid black !important"
                                class="text-right"><b>SST
                                    ({{ round($LoanCaseBillMain->sst_rate, 0) }}%)</b></th>
                            <th style="border-top: 1px solid black !important;border-bottom: 1px solid black !important"
                                class="text-right"><b>Total (RM)</b></th>
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
                                    @php
                                        $total_sub_main = 0;
                                    @endphp
                                    <tr id="{{ $row['category']->code }}">
                                        <td colspan="5"
                                            style="padding:0px !important;border: 1px solid black;padding-left:10px  !important;background-color:#0066CC !important">
                                            <span><b
                                                    style="color:white;font-size:13px">{{ $row['category']->category }}</b></span>
                                        </td>
                                        <?php $subtotal = 0; 
                                            $totalSST = 0;
                                            $pf_total = 0;
                                        ?>

                                    </tr>
                                @elseif($row['row'] == 'item')
                                    @php
                                        $details = $row['account_details'];
                                        $row_sst = 0;
                                        $row_total = 0;

                                        // if ($row['category']->taxable == '1') {
                                        //     $row_sst = round($details->quo_amount_no_sst * $sst_rate, 2);
                                        //     $totalSST += $row_sst;
                                        //     $pf_total += $details->quo_amount_no_sst;
                                        //     $total_sub_main += $details->quo_amount_no_sst + round($details->quo_amount_no_sst * $sst_rate, 2);
                                        // } elseif ($row['category']->taxable == '2') {
                                        //     $stamP_duties_count += 1;

                                        // }

                                        if ($row['category']->taxable == '1') {
                                            // Simple rule: always round down when third decimal is 5
                                            $sst_calculation = $details->amount * $sst_rate;
                                            $sst_string = number_format($sst_calculation, 3, '.', '');
                                            
                                            // Check if third decimal is 5 and round down
                                            if (substr($sst_string, -1) == '5') {
                                                $row_sst = floor($sst_calculation * 100) / 100; // Round down
                                            } else {
                                                $row_sst = round($sst_calculation, 2); // Normal rounding
                                            }
                                            
                                            $totalSST += $row_sst;
                                            $pf_total += $details->amount;
                                            
                                            // For totals, use the rounded individual SST
                                            $total_sub_main += $details->amount + $row_sst;
                                            $total_amount_main += $details->amount + $row_sst;
                                        } else {
                                            $stamP_duties_count += 1;
                                            $total_sub_main += $details->amount;
                                            $total_amount_main += $details->amount;
                                        }

                                        $subtotal += $row_sst;
                                        $row_total = $details->amount + $row_sst;

                                        $row_count += 1;
                                    @endphp
                                    <tr style="">
                                        <td
                                            style="border-left: 1px solid black !important;border-right: 1px solid black;padding:0px !important;height:25px;padding-left:10px !important;vertical-align: middle !important ">
                                            {{ $row_count }}. {{ $details->account_name }} @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
                                            @if ($row['category']->id == 1)
                                                @if ($details->item_remark)
                                                    <hr
                                                        style="margin-top:1px !important;margin-bottom:1px !important" />
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
                                            style="text-align: right;border-left: 1px solid black !important;;padding:0px !important;height:25px;padding-right:10px !important;vertical-align: middle !important">
                                            {{ number_format((float) $details->amount, 2, '.', ',') }}</td>
                                        <td
                                            style="text-align: right;border-left: 1px solid black !important;;padding:0px !important;height:25px;padding-right:10px !important;vertical-align: middle !important">

                                            @if ($row['category']->taxable == '1')
                                                {{ number_format((float) $row_sst, 2, '.', ',') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td
                                            style="text-align: right;border-left: 1px solid black !important;border-right: 1px solid black !important;padding:0px !important;height:25px;padding-right:10px !important;vertical-align: middle !important">
                                            {{ number_format((float) $row_total, 2, '.', ',') }}</td>

                                    </tr>
                                @elseif($row['row'] == 'subtotal')
                                    @if ($row['category']->id == 1 || $row['category']->id == 4)
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
                                                {{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                        </tr>
                                        
                                    {{-- @elseif($row['category']->id == 2)
                                        <tr style="padding:0px !important;border: 1px solid black">
                                            <td class="text-left"
                                                style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;"
                                                colspan="2">Subtotal:</td>
                                            <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;"
                                                colspan="3">
                                                {{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                        </tr> --}}
                                    @else
                                        {{-- <tr style="padding:0px !important;border: 1px solid black">
                                            <td class="text-left"
                                                style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;"
                                                colspan="2">Subtotal:</td>
                                            <td style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;"
                                                colspan="3">
                                                {{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                        </tr> --}}

                                         <tr style="padding:0px !important;border: 1px solid black">
                                            <td class="text-left"
                                                style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;">
                                                Subtotal:</td>
                                            <td
                                                style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">
                                                {{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                            <td
                                                style="text-align: right;border-right: 1px solid black;;border-top: 1px solid black;border-bottom: 1px solid black;">-</td>
                                            <td
                                                style="text-align:right;border-top: 1px solid black;border-bottom: 1px solid black;;border-right: 1px solid black;">
                                                {{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach

                            @if ($index + 1 == $total_page)
                                <tr
                                    style="padding:0px !important;border: 1px solid black;padding-top:10px !important;">
                                    <td style="padding:0px !important;padding-left:10px !important"><span><b
                                                style="font-size:15px">Grand Total :</b></span> </td>
                                    <td style="text-align:right;padding:0px !important;padding-right:10px !important;"
                                        colspan="5"><b style="font-size:15px">
                                            {{ number_format($total_amount_main, 2, '.', ',') }}</b></td>

                                </tr>
                            @endif
                        @endif


                    </tbody>
                </table>
            </div>

            @if ($index + 1 == $total_page)
                <div class="col-12  print-formal">
                    <div class="col-12" style="border: 1px solid black;">
                        @php
                            $firm_name = 'L H YEO & CO';
                            $bank_account = '';
                            $bank = '';

                            if ($case->branch_id == 1) {
                                $bank_account = '3212995518';
                                $bank = 'PUBLIC BANK BERHAD';
                            } elseif ($case->branch_id == 2) {
                                $bank_account = '8605606123';
                                $bank = 'CIMB ISLAMIC BANK BERHAD';
                            } elseif ($case->branch_id == 3) {
                                $bank_account = '3230791418';
                                $bank = 'PBB CLIENT ACC';
                            } elseif ($case->branch_id == 4) {
                                $bank_account = '3231832819';
                                $bank = 'PBB CLIENT ACC';
                                $firm_name = 'Ramakrishnan & Co';
                            } elseif ($case->branch_id == 5) {
                                $bank_account = '3232880915';
                                $bank = 'PBB CLIENT ACC';
                            } elseif ($case->branch_id == 6) {
                                $bank_account = '8605514414';
                                $bank = 'CIMB BANK BERHAD';
                                $firm_name = 'ISMAIL & LIM';
                            }elseif ($case->branch_id == 7) {
                                $bank_account = '12050003750713';
                                $bank = 'Bank Muamalat';
                            }

                        @endphp
                        Please note that payment can be made by way of cheque / bank draft / online transfer to our firm
                        details as follow :<br />
                        <b>Name</b> : {{ $firm_name }}<br />
                        <b>Bank</b> : {{ $bank }}<br />
                        <b>Account No</b>: {{ $bank_account }}<br /><br />
                        <b>We DO NOT accept payment by cash</b> <br /><br />
                        <i>* Interest at 8% per annum on the aforesaid amount shall be charged with effect from the
                            expiration
                            of one (1) month from the date of the bill until the date of the actual payment in
                            accordance with
                            clause 6 of the Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976. E
                            & OE
                            *</i>

                    </div>
                </div>
            @endif
        </div>

        @if ($index < count($pieces_inv) - 1)
            <div class="page-break-print">

            </div>

            <div class="page-break text-center">
                Page Divider
            </div>
        @endif
    @endforeach

</div>
