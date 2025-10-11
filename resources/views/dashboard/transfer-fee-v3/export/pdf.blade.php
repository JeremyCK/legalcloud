<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Fee Invoices - {{ $transferFee->transaction_id ?? 'N/A' }}</title>
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
            padding: 4px;
            text-align: left;
            font-size: 8px;
            word-wrap: break-word;
            overflow: hidden;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
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
        
        /* Column width optimization for better fit */
        .col-no { width: 3%; }
        .col-ref { width: 12%; }
        .col-invoice { width: 10%; }
        .col-date { width: 8%; }
        .col-amount { width: 7%; }
        .col-fee { width: 6%; }
        .col-sst { width: 5%; }
        .col-reimb { width: 6%; }
        .col-reimb-sst { width: 6%; }
        .col-transfer { width: 5%; }
        .col-payment { width: 8%; }
        
        /* Additional optimizations */
        .col-ref, .col-invoice {
            font-size: 7px;
        }
        
        /* Make numbers more compact */
        .text-right {
            font-size: 7px;
            padding: 2px;
        }
        
        /* Optimize header text */
        th {
            font-size: 7px;
            padding: 3px;
            line-height: 1.1;
        }
        
        /* Alternative: Two-row header for better space usage */
        .header-row-1 {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .header-row-2 {
            background-color: #f8f9fa;
            font-size: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transfer Fee Invoices Report</h1>
        <p><strong>Transaction ID:</strong> {{ $transferFee->transaction_id ?? 'N/A' }}</p>
        <p><strong>Transfer Date:</strong> {{ $transferFee->transfer_date ?? 'N/A' }}</p>
        <p><strong>Purpose:</strong> {{ $transferFee->purpose ?? 'N/A' }}</p>
        @if(isset($sortBy) && isset($sortOrder))
        <p><strong>Sorted by:</strong> {{ ucfirst(str_replace('_', ' ', $sortBy)) }} ({{ strtoupper($sortOrder) }})</p>
        @endif
        <p><strong>Generated:</strong> {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr class="header-row-1">
                <th class="col-no" rowspan="2">No</th>
                <th class="col-ref" rowspan="2">Case Ref No</th>
                <th class="col-invoice" rowspan="2">Invoice No</th>
                <th class="col-date" rowspan="2">Invoice Date</th>
                <th class="col-amount" colspan="2">Amounts</th>
                <th class="col-fee" colspan="2">Fees</th>
                <th class="col-reimb" colspan="2">Reimbursement</th>
                <th class="col-transfer" colspan="4">To Transfer</th>
                <th class="col-amount" colspan="4">Transferred</th>
                <th class="col-payment" rowspan="2">Payment Date</th>
            </tr>
            <tr class="header-row-2">
                <th class="col-amount">Total</th>
                <th class="col-amount">Collected</th>
                <th class="col-fee">Pfee</th>
                <th class="col-sst">SST</th>
                <th class="col-reimb">Reimb</th>
                <th class="col-reimb-sst">Reimb SST</th>
                <th class="col-transfer">Pfee</th>
                <th class="col-transfer">SST</th>
                <th class="col-transfer">Reimb</th>
                <th class="col-transfer">Reimb SST</th>
                <th class="col-amount">Balance</th>
                <th class="col-amount">SST</th>
                <th class="col-amount">Reimb</th>
                <th class="col-amount">Reimb SST</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr class="{{ $row['No'] === 'TOTAL' ? 'totals-row' : '' }}">
                    <td class="text-center">{{ $row['No'] }}</td>
                    <td>{{ $row['Case Ref No'] }}</td>
                    <td>{{ $row['Invoice No'] }}</td>
                    <td>{{ $row['Invoice Date'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Total Amount']) ? number_format($row['Total Amount'], 2) : $row['Total Amount'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Collected Amount']) ? number_format($row['Collected Amount'], 2) : $row['Collected Amount'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Professional Fee']) ? number_format($row['Professional Fee'], 2) : $row['Professional Fee'] }}</td>
                    <td class="text-right">{{ is_numeric($row['SST']) ? number_format($row['SST'], 2) : $row['SST'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Reimbursement']) ? number_format($row['Reimbursement'], 2) : $row['Reimbursement'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Reimbursement SST']) ? number_format($row['Reimbursement SST'], 2) : $row['Reimbursement SST'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Pfee to Transfer']) ? number_format($row['Pfee to Transfer'], 2) : $row['Pfee to Transfer'] }}</td>
                    <td class="text-right">{{ is_numeric($row['SST to Transfer']) ? number_format($row['SST to Transfer'], 2) : $row['SST to Transfer'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Reimbursement to Transfer']) ? number_format($row['Reimbursement to Transfer'], 2) : $row['Reimbursement to Transfer'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Reimbursement SST to Transfer']) ? number_format($row['Reimbursement SST to Transfer'], 2) : $row['Reimbursement SST to Transfer'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Transferred Balance']) ? number_format($row['Transferred Balance'], 2) : $row['Transferred Balance'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Transferred SST']) ? number_format($row['Transferred SST'], 2) : $row['Transferred SST'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Transferred Reimbursement']) ? number_format($row['Transferred Reimbursement'], 2) : $row['Transferred Reimbursement'] }}</td>
                    <td class="text-right">{{ is_numeric($row['Transferred Reimbursement SST']) ? number_format($row['Transferred Reimbursement SST'], 2) : $row['Transferred Reimbursement SST'] }}</td>
                    <td>{{ $row['Payment Date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated on {{ date('Y-m-d H:i:s') }} by {{ auth()->user()->name ?? 'System' }}</p>
        <p>LH YEO & CO - Transfer Fee Management System</p>
    </div>
</body>
</html>
