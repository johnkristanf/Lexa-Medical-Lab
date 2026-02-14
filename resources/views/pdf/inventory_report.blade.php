<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Request Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2d5016;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background-color: #2d5016;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e8f5e8;
        }

        .text-center {
            text-align: center;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>

<body>
    <div class="header">
        @if (isset($logobaselexa) && $logobaselexa)
            <img src="{{ $logobaselexa }}" alt="Lexa Medical Laboratory Logo">
        @else
            <div style="height: 60px; margin-bottom: 10px; font-weight: bold; font-size: 16px; color: #74c69d;">
                LEXA MEDICAL LABORATORY
            </div>
        @endif
        <h1>INVENTORY REPORT</h1>
        <p>Generated on {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Brand Name</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Manufacturing Date</th>
                <th>Expiration Date</th>
                <th>Lot Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
                <tr>
                    <td>{{ $supply->participants }}</td>
                    <td>{{ $supply->brand_name }}</td>
                    <td>{{ $supply->unit }}</td>
                    <td>{{ $supply->quantity }}</td>
                    <td>{{ \Carbon\Carbon::parse($supply->manufacture_date)->toFormattedDateString() }}</td>
                    <td>{{ \Carbon\Carbon::parse($supply->expiration_date)->toFormattedDateString() }}</td>
                    <td>{{ $supply->lot_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; page-break-inside: avoid;">
        <p style="font-weight: bold;">Prepared by:</p>
        <div style="margin-top: 30px; border-bottom: 1px solid #333; width: 200px;"></div>
    </div>
</body>

</html>
