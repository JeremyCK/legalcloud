<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SST Payment Record - {{ $SSTMain->id ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        /* Column width adjustments */
        th:nth-child(1), td:nth-child(1) { width: 5%; }  /* No */
        th:nth-child(2), td:nth-child(2) { width: 12%; } /* Case Ref No */
        th:nth-child(3), td:nth-child(3) { width: 15%; } /* Client Name */
        th:nth-child(4), td:nth-child(4) { width: 10%; } /* Invoice No */
        th:nth-child(5), td:nth-child(5) { width: 8%; }  /* Invoice Date */
        th:nth-child(6), td:nth-child(6) { width: 10%; } /* Total Amount */
        th:nth-child(7), td:nth-child(7) { width: 8%; }  /* Pfee1 */
        th:nth-child(8), td:nth-child(8) { width: 8%; }  /* Pfee2 */
        th:nth-child(9), td:nth-child(9) { width: 10%; } /* Collected Amount */
        th:nth-child(10), td:nth-child(10) { width: 8%; } /* SST Amount */
        th:nth-child(11), td:nth-child(11) { width: 8%; } /* Payment Date */
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SST Payment Record</h1>
        <p><strong>SST ID:</strong> {{ $SSTMain->id ?? 'N/A' }}</p>
        <p><strong>Payment Date:</strong> {{ $SSTMain->payment_date ?? 'N/A' }}</p>
        <p><strong>Transaction ID:</strong> {{ $SSTMain->transaction_id ?? 'N/A' }}</p>
        <p><strong>Branch:</strong> {{ $branchName ?? 'N/A' }}</p>
        <p><strong>Remark:</strong> {{ $SSTMain->remark ?? 'N/A' }}</p>
        <p><strong>Generated:</strong> {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Case Ref No</th>
                <th>Client Name</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Total Amount</th>
                <th>Pfee1</th>
                <th>Pfee2</th>
                <th>Collected Amount</th>
                <th>SST Amount</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr class="{{ $row['No'] === 'TOTAL' ? 'totals-row' : '' }}">
                    <td class="text-center">{{ $row['No'] }}</td>
                    <td>{{ $row['Case Ref No'] }}</td>
                    <td>{{ $row['Client Name'] }}</td>
                    <td>{{ $row['Invoice No'] }}</td>
                    <td>{{ $row['Invoice Date'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Total Amount']) ? number_format($row['Total Amount'], 2) : $row['Total Amount'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Pfee1']) ? number_format($row['Pfee1'], 2) : $row['Pfee1'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Pfee2']) ? number_format($row['Pfee2'], 2) : $row['Pfee2'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Collected Amount']) ? number_format($row['Collected Amount'], 2) : $row['Collected Amount'] }}</td>
                    <td class="text-right">{{ is_numeric($row['SST Amount']) ? number_format($row['SST Amount'], 2) : $row['SST Amount'] }}</td>
                    <td>{{ $row['Payment Date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated on {{ date('Y-m-d H:i:s') }} by {{ auth()->user()->name ?? 'System' }}</p>
        <p>LH YEO & CO - SST Management System</p>
    </div>
</body>
</html>
