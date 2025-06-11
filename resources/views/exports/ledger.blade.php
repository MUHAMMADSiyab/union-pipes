<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            margin: 10mm;
            color: #333;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin: 0 0 5px 0;
        }

        h2 {
            text-align: center;
            font-size: 16px;
            margin: 0 0 10px 0;
            font-weight: normal;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: right;
            font-size: 10px;
        }

        th {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
        }

        td:first-child,
        th:first-child {
            text-align: center;
        }

        td:nth-child(2),
        th:nth-child(2) {
            text-align: center;
        }

        td:nth-child(3),
        th:nth-child(3) {
            text-align: left;
        }

        td:nth-child(4),
        th:nth-child(4) {
            text-align: left;
        }

        .total-row td {
            font-weight: bold;
            background-color: #e6e6e6;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }
    </style>
</head>

<body>
    <h1>Karachi Super Delux</h1>
    <h2>{{ $company->name ?? ($customer->name ?? $bank->name) }}</h2>
    @if ($from_date && $to_date)
        <div class="subtitle">
            From {{ \Carbon\Carbon::parse($from_date)->format('d/m/Y') }} to
            {{ \Carbon\Carbon::parse($to_date)->format('d/m/Y') }}
        </div>
    @endif

    <table>
        <tr>
            <th>S#</th>
            <th>Date</th>
            @if (isset($bank))
                <th>Particular</th>
            @else
                <th>Invoice No.</th>
            @endif
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        @foreach ($entries as $index => $entry)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                @if (isset($bank))
                    <td class="text-left">{!! strip_tags($entry['particular']) !!}</td>
                @else
                    <td class="text-left">{{ $entry['invoice_no'] }}</td>
                @endif
                <td class="text-left">{!! strip_tags($entry['description']) !!}</td>
                <td>{{ number_format($entry['debit'], 2) }}</td>
                <td>{{ number_format($entry['credit'], 2) }}</td>
                <td>{{ number_format($entry['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="{{ isset($bank) ? 4 : 4 }}" class="text-center">Total</td>
            <td>{{ number_format($totalDebit, 2) }}</td>
            <td>{{ number_format($totalCredit, 2) }}</td>
            <td></td>
        </tr>
    </table>
</body>

</html>
