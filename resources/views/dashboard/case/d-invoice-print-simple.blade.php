<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice_{{ $case->case_ref_no }}</title>
    <style>
        @page {
            margin: 8mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .header-table {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .header-table td {
            vertical-align: top;
            padding: 2px;
        }
        .header-table .col-left {
            width: 33%;
        }
        .header-table .col-center {
            width: 34%;
            text-align: center;
        }
        .header-table .col-right {
            width: 33%;
            text-align: right;
        }
        .info-table {
            width: 100%;
            margin-top: 8px;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 3px;
        }
        .info-table .col-left {
            width: 50%;
        }
        .info-table .col-right {
            width: 50%;
        }
        .info-row {
            margin-bottom: 3px;
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .info-label {
            width: 25%;
            font-weight: bold;
            display: table-cell;
            vertical-align: top;
            padding-right: 5px;
        }
        .info-value {
            width: 75%;
            display: table-cell;
            vertical-align: top;
        }
        .info-label-right {
            width: 30%;
            font-weight: bold;
            display: table-cell;
            vertical-align: top;
            padding-right: 5px;
        }
        .info-value-right {
            width: 70%;
            display: table-cell;
            vertical-align: top;
        }
        .invoice-table {
            width: 100%;
            border: 1px solid black;
            margin-top: 8px;
            margin-bottom: 5px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid black;
            padding: 2px 3px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #0066CC;
            color: white;
            text-align: center;
        }
        .invoice-table .col-desc {
            width: 40%;
        }
        .invoice-table .col-amount {
            width: 20%;
            text-align: right;
        }
        .invoice-table .col-sst {
            width: 20%;
            text-align: right;
        }
        .invoice-table .col-total {
            width: 20%;
            text-align: right !important;
        }
        .invoice-table td.text-right {
            text-align: right !important;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-blue {
            color: #2d659d;
        }
        .footer-box {
            border: 1px solid black;
            padding: 6px;
            margin-top: 8px;
            font-size: 8px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    @php
        $total_amount_main = 0;
        $total_sub_main = 0;
        $total_sst_main = 0;
    @endphp

    @foreach ($pieces_inv as $index => $row)
        @php
            $total_page = count($pieces_inv);
            $rowItem = $row;
        @endphp

        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td class="col-left">
                    <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>
                    @if ($Branch->sst_no)
                        <b>SST No:</b> {{ $Branch->sst_no }}<br>
                    @endif
                    Advocates & Solicitors<br>
                    {!! $Branch->address !!}<br>
                    <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
                    <b>Email</b>: {{ $Branch->email }}
                </td>
                <td class="col-center">
                    <h2 style="margin: 0; font-size: 16px; font-weight: bold;">Invoice</h2>
                </td>
                <td class="col-right">
                    <b>Date:</b> {{ date('d-m-Y', strtotime($LoanCaseBillMain->invoice_date)) }}
                </td>
            </tr>
        </table>

        <!-- Info Section - 2 Columns -->
        <table class="info-table">
            <tr>
                <td class="col-left">
                    <!-- Left Column: To, Tax No, Address -->
                    <div class="info-row">
                        <span class="info-label">To:</span>
                        <span class="info-value">
                            <strong class="text-blue">
                                @if(isset($InvoiceBillingParty) && $InvoiceBillingParty)
                                    {{ $InvoiceBillingParty->customer_name }}
                                @else
                                    {{ $LoanCaseBillMain->invoice_to }}
                                @endif
                            </strong>
                        </span>
                    </div>
                    
                    @if (isset($InvoiceBillingParty) && $InvoiceBillingParty && isset($InvoiceBillingParty->tin) && $InvoiceBillingParty->tin)
                        <div class="info-row">
                            <span class="info-label">Tax No:</span>
                            <span class="info-value">{{ $InvoiceBillingParty->tin }}</span>
                        </div>
                    @elseif (isset($LoanCaseBillMain->invoice_to_tax_no))
                        <div class="info-row">
                            <span class="info-label">Tax No:</span>
                            <span class="info-value">{{ $LoanCaseBillMain->invoice_to_tax_no }}</span>
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
                            
                            $locationLine = trim(implode(' ', array_filter([$postcode, $city, $state])));
                            
                            $locationAlreadyIncluded = false;
                            if ($locationLine) {
                                $locationLower = strtolower($locationLine);
                                foreach ($addressParts as $part) {
                                    $partLower = strtolower(trim($part));
                                    if ($partLower === $locationLower || 
                                        ($postcode && strpos($partLower, strtolower($postcode)) !== false && 
                                         (($city && strpos($partLower, strtolower($city)) !== false) || 
                                          ($state && strpos($partLower, strtolower($state)) !== false)))) {
                                        $locationAlreadyIncluded = true;
                                        break;
                                    }
                                }
                            }
                            
                            $fullAddress = implode("\n", $addressParts);
                            if ($locationLine && !$locationAlreadyIncluded) {
                                $fullAddress .= ($fullAddress ? "\n" : '') . $locationLine;
                            }
                        @endphp
                        @if($fullAddress)
                            <div class="info-row">
                                <span class="info-label">Address:</span>
                                <span class="info-value">{!! nl2br(htmlspecialchars($fullAddress)) !!}</span>
                            </div>
                        @endif
                    @elseif (isset($LoanCaseBillMain->invoice_to_address))
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{!! nl2br(htmlspecialchars($LoanCaseBillMain->invoice_to_address)) !!}</span>
                        </div>
                    @endif
                </td>
                <td class="col-right">
                    <!-- Right Column: Case Ref No, Invoice No -->
                    <div class="info-row">
                        <span class="info-label-right">Case Ref No:</span>
                        <span class="info-value-right">{{ $case->case_ref_no }}</span>
                    </div>
                    
                    @if(isset($purchaser_financier_ref_no) && $purchaser_financier_ref_no && $purchaser_financier_ref_no->value)
                        <div class="info-row">
                            <span class="info-label-right">Bank Ref No:</span>
                            <span class="info-value-right">{{ $purchaser_financier_ref_no->value }}</span>
                        </div>
                    @endif

                    <div class="info-row">
                        <span class="info-label-right">Invoice No:</span>
                        <span class="info-value-right">
                            @if(isset($invoiceMain) && $invoiceMain)
                                {{ $invoiceMain->invoice_no }}
                            @else
                                {{ $LoanCaseBillMain->invoice_no }}
                            @endif
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Invoice Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="col-desc">Description</th>
                    <th class="col-amount">Amount (RM)</th>
                    <th class="col-sst">SST ({{ round($LoanCaseBillMain->sst_rate, 0) }}%)</th>
                    <th class="col-total">Total (RM)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $subtotal = 0;
                    $totalSST = 0;
                    $pf_total = 0;
                    $stamP_duties_count = 0;
                    $row_count = 0;
                    $sst_rate = $LoanCaseBillMain->sst_rate * 0.01;
                @endphp

                @if (count($rowItem))
                    @foreach ($rowItem as $index2 => $row)
                        @if ($row['row'] == 'title')
                            @php
                                $total_sub_main = 0;
                                $row_count = 0; // Reset row count for each category
                            @endphp
                            <tr>
                                <td colspan="4" style="background-color:#0066CC; color:white; padding:5px;">
                                    <b>{{ $row['category']->category }}</b>
                                </td>
                            </tr>
                            @php
                                $subtotal = 0;
                                $totalSST = 0;
                                $pf_total = 0;
                            @endphp
                        @elseif($row['row'] == 'item')
                            @php
                                $details = $row['account_details'];
                                $row_sst = 0;
                                $row_total = 0;

                                if ($row['category']->taxable == '1') {
                                    $sst_calculation = $details->amount * $sst_rate;
                                    $sst_string = number_format($sst_calculation, 3, '.', '');
                                    
                                    if (substr($sst_string, -1) == '5') {
                                        $row_sst = floor($sst_calculation * 100) / 100;
                                    } else {
                                        $row_sst = round($sst_calculation, 2);
                                    }
                                    
                                    $totalSST += $row_sst;
                                    $pf_total += $details->amount;
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
                            <tr>
                                <td class="col-desc">
                                    {{ $row_count }}. {{ $details->account_name }}
                                    @if($LoanCaseBillMain->isChinese == 1) {{ $details->account_name_cn }} @endif
                                    @if ($row['category']->id == 1)
                                        @if ($details->item_remark)
                                            <br>{!! $details->item_remark !!}
                                        @else
                                            @if ($details->item_desc)
                                                <br>{!! $details->item_desc !!}
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td class="col-amount">{{ number_format((float) $details->amount, 2, '.', ',') }}</td>
                                <td class="col-sst">
                                    @if ($row['category']->taxable == '1')
                                        {{ number_format((float) $row_sst, 2, '.', ',') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="col-total">{{ number_format((float) $row_total, 2, '.', ',') }}</td>
                            </tr>
                        @elseif($row['row'] == 'subtotal')
                            @if ($row['category']->id == 1 || $row['category']->id == 4)
                                <tr>
                                    <td><b>Subtotal:</b></td>
                                    <td class="col-amount">{{ number_format((float) $pf_total, 2, '.', ',') }}</td>
                                    <td class="col-sst">{{ number_format((float) $totalSST, 2, '.', ',') }}</td>
                                    <td class="col-total">{{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td><b>Subtotal:</b></td>
                                    <td class="col-amount">{{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                    <td class="col-sst">-</td>
                                    <td class="col-total">{{ number_format((float) $total_sub_main, 2, '.', ',') }}</td>
                                </tr>
                            @endif
                        @endif
                    @endforeach

                    @if ($index + 1 == $total_page)
                        <tr>
                            <td colspan="3"><b style="font-size:12px">Grand Total:</b></td>
                            <td class="text-right" style="text-align: right !important;"><b style="font-size:12px">{{ number_format($total_amount_main, 2, '.', ',') }}</b></td>
                        </tr>
                    @endif
                @endif
            </tbody>
        </table>

        @if ($index + 1 == $total_page)
            <div class="footer-box">
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
                    } elseif ($case->branch_id == 7) {
                        $bank_account = '12050003750713';
                        $bank = 'Bank Muamalat';
                    }
                @endphp
                Please note that payment can be made by way of cheque / bank draft / online transfer to our firm details as follow :<br />
                <b>Name</b> : {{ $firm_name }}<br />
                <b>Bank</b> : {{ $bank }}<br />
                <b>Account No</b>: {{ $bank_account }}<br /><br />
                <b>We DO NOT accept payment by cash</b> <br /><br />
                <i>* Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of one (1) month from the date of the bill until the date of the actual payment in accordance with clause 6 of the Solicitors' Remuneration Order 1991 made to the Legal Profession Act 1976. E & OE *</i>
            </div>
        @endif

        @if ($index < count($pieces_inv) - 1)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>

