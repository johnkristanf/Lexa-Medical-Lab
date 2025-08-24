<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #208b3a; color: white; }
    </style>
</head>
<body>
    <h1>Medical Supply Inventory Report</h1>

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
            @foreach($supplies as $supply)
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
</body>
</html>
