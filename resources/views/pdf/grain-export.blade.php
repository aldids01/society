<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grain Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 3mm;
            background-color: #f4f4f4;
        }
        .invoice-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 2mm;
            max-width: 800px;
            margin: auto;
        }
        .header {
            background-color: #800080; /* Purple */
            color: #ffffff;
            text-align: center;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .details {
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
            color: purple;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #800080; /* Purple */
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #800080; /* Purple */
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #800080;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>GECHAAN STAFF COOPERATIVE AND MULTIPURPOSE SOCIETY <br/>17 Dabon Warwar Street, Taraba State, Sardauna LGA<br/>{{ $record->applicant->name }} Grain Request Form</h1>
            <p>Date: {{ $record->start_date->format('jS F, Y g:ia') }} Reference No: {{ $record->slug }}</p>
        </div>

        <div class="details">
            <table>
                <tr>
                    <th>Status</th>
                    <td>{{ ucfirst($record->status) }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>NGN {{ number_format($record->amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Terms</th>
                    <td>{{ $record->terms }} {{ Str::plural('Month', $record->terms) }}</td>
                </tr>
                <tr>
                    <th>Rate</th>
                    <td>{{ $record->rate }}%</td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td>{{ $record->start_date->format('F, Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="section-title">Amortization</div>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Period</th>
                    <th>Interest</th>
                    <th>Principal</th>
                    <th>Payment</th>
                    <th>Start Balance</th>
                    <th>End Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalInterest = 0;
                    $totalPrincipal = 0;
                    $totalPayment = 0;
                @endphp
                @foreach ($record->amorts as $amort)
                <tr>
                    <td>{{ $amort->annual }}</td>
                    <td>{{ $amort->period }}</td>
                    <td>{{ number_format($amort->interest, 2) }}</td>
                    <td>{{ number_format($amort->principal, 2) }}</td>
                    <td>{{ number_format($amort->payment, 2) }}</td>
                    <td>{{ number_format($amort->start_balance, 2) }}</td>
                    <td>{{ number_format($amort->end_balance, 2) }}</td>
                    <td>{{ ucfirst($amort->status) }}</td>
                </tr>
                @php
                    $totalInterest += $amort->interest;
                    $totalPrincipal += $amort->principal;
                    $totalPayment += $amort->payment;
                @endphp
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold;">Totals:</td>
                    <td style="font-weight: bold;">{{ number_format($totalInterest, 2) }}</td>
                    <td style="font-weight: bold;">{{ number_format($totalPrincipal, 2) }}</td>
                    <td style="font-weight: bold;">{{ number_format($totalPayment, 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Office Use Only</div>
        <table>
            <tr>
                <th>Checked By</th>
                <td>{{ optional($record->approvals->first())->check->name }}</td>
                <th>Checked Date</th>
                <td>{{ optional($record->approvals->first())->checkeddate->format('F jS, Y g:ia') }}</td>
            </tr>
            <tr>
                <th>Approved By</th>
                <td>{{ optional($record->approvals->first())->approve->name }}</td>
                <th>Approved Date</th>
                <td>{{ optional($record->approvals->first())->approveddate->format('F jS, Y g:ia') }}</td>
            </tr>
            <tr>
                <th>Disbursed By</th>
                <td>{{ optional($record->approvals->first())->disburse->name }}</td>
                <th>Disbursed Date</th>
                <td>{{ optional($record->approvals->first())->disburseddate->format('F jS, Y g:ia') }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
