<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bank Report</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Bank Report</h2>
        <p>
            @if($year != 0)
                Year: {{ $year }}
            @else
                Year: All
            @endif
            @if($month != 0)
                | Month: {{ date('F', mktime(0, 0, 0, $month, 1)) }}
            @else
                | Month: All
            @endif
        </p>
        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Date</th>
                <th style="width: 12%;">Ref No</th>
                <th style="width: 12%;">Bank</th>
                <th style="width: 20%;">Property Address</th>
                <th style="width: 15%;">Customer Name</th>
                <th style="width: 10%;">Purchase Price</th>
                <th style="width: 10%;">Loan Sum</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($LoanCase))
                @foreach ($LoanCase as $record)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($record->created_at)) }}</td>
                        <td>{{ $record->case_ref_no }}</td>
                        <td>{{ $record->portfolio_name ?? 'N/A' }}</td>
                        <td>{{ $record->property_address ?? '-' }}</td>
                        <td>{{ $record->customer_name ?? '-' }}</td>
                        <td class="text-right">{{ number_format($record->purchase_price ?? 0, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format($record->loan_sum ?? 0, 2, '.', ',') }}</td>
                        <td class="text-center">
                            @if($record->status == 0)
                                Closed
                            @elseif($record->status == 1)
                                In progress
                            @elseif($record->status == 2)
                                Open
                            @elseif($record->status == 3)
                                KIV
                            @elseif($record->status == 4)
                                Pending Close
                            @elseif($record->status == 7)
                                Reviewing
                            @elseif($record->status == 99)
                                Aborted
                            @else
                                Unknown ({{ $record->status }})
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="5" class="text-right">Total Cases:</td>
                    <td colspan="3" class="text-center">{{ count($LoanCase) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="8" class="text-center">No data found</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>

