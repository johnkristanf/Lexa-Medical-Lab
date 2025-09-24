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
        <h1>Supply Request Report</h1>
        <p>Generated on {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Quanity</th>
                <th>Unit</th>
                <th>Item Description</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($supplies_requested as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->quantity }}</td>
                    <td>{{ $request->unit }}</td>
                    <td>{{ $request->item_description }}</td>
                    <td class="!text-left">{{ $request->unit_price }}</td>
                    <td class="!text-left">{{ $request->total_price }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No supply requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
