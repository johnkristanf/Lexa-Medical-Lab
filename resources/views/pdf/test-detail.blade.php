<!DOCTYPE html>
<html>
<head>
    <title>Test Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #333;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .label {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h2>Test Details</h2>
    <table>
        <tr>
            <th>Label</th>
            <th>Value</th>
        </tr>
        <tr>
            <td class="label">Referrer Full Name</td>
            <td>{{ $testDetail->referer_fullname }}</td>
        </tr>
        <tr>
            <td class="label">Doctor License No</td>
            <td>{{ $testDetail->doctor_license_no }}</td>
        </tr>
        <tr>
            <td class="label">Reason for Test</td>
            <td>{{ $testDetail->reason_for_test }}</td>
        </tr>
        <tr>
            <td class="label">Test Schedule</td>
            <td>{{ $testDetail->test_schedule }}</td>
        </tr>
        <tr>
            <td class="label">Total Price</td>
            <td>&#8369;{{ number_format($testDetail->total_price, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Created At</td>
            <td>{{ $testDetail->created_at->format('F d, Y h:i A') }}</td>
        </tr>
    </table>
</body>
</html>
