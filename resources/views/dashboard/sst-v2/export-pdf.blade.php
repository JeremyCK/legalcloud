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
        th:nth-child(1), td:nth-child(1) { width: 4%; }  /* No */
        th:nth-child(2), td:nth-child(2) { width: 10%; } /* Ref No */
        th:nth-child(3), td:nth-child(3) { width: 12%; } /* Client Name */
        th:nth-child(4), td:nth-child(4) { width: 8%; }  /* Invoice No */
        th:nth-child(5), td:nth-child(5) { width: 7%; }  /* Invoice Date */
        th:nth-child(6), td:nth-child(6) { width: 8%; }  /* Total amt */
        th:nth-child(7), td:nth-child(7) { width: 8%; }  /* P1+P2 (excl SST) */
        th:nth-child(8), td:nth-child(8) { width: 8%; }  /* Reimbursement (excl SST) */
        th:nth-child(9), td:nth-child(9) { width: 8%; }  /* Collected amt */
        th:nth-child(10), td:nth-child(10) { width: 6%; } /* SST */
        th:nth-child(11), td:nth-child(11) { width: 7%; } /* Reimb SST */
        th:nth-child(12), td:nth-child(12) { width: 7%; } /* Total SST */
        th:nth-child(13), td:nth-child(13) { width: 7%; } /* Payment Date */
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
                <th>Ref No</th>
                <th>Client Name</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Total amt</th>
                <th>P1+P2 (excl SST)</th>
                <th>Reimbursement (excl SST)</th>
                <th>Collected amt</th>
                <th>SST</th>
                <th>Reimb SST</th>
                <th>Total SST</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr class="{{ $row['No'] === 'TOTAL' ? 'totals-row' : '' }}">
                    <td class="text-center">{{ $row['No'] }}</td>
                    <td>{{ $row['Ref No'] }}</td>
                    <td>{{ $row['Client Name'] }}</td>
                    <td>{{ $row['Invoice No'] }}</td>
                    <td>{{ $row['Invoice Date'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Total amt']) ? number_format($row['Total amt'], 2) : $row['Total amt'] }}</td>
                    <td class="text-right">{{ is_numeric($row['P1+P2 (excl SST)']) ? number_format($row['P1+P2 (excl SST)'], 2) : $row['P1+P2 (excl SST)'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Reimbursement (excl SST)']) ? number_format($row['Reimbursement (excl SST)'], 2) : $row['Reimbursement (excl SST)'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Collected amt']) ? number_format($row['Collected amt'], 2) : $row['Collected amt'] }}</td>
                    <td class="text-right">{{ is_numeric($row['SST']) ? number_format($row['SST'], 2) : $row['SST'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Reimb SST']) ? number_format($row['Reimb SST'], 2) : $row['Reimb SST'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Total SST']) ? number_format($row['Total SST'], 2) : $row['Total SST'] }}</td>
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
