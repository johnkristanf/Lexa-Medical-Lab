<!DOCTYPE html>
<html>
<head>
    <title>Test Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        h3 {
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
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Test Details</h2>
    <table>
        <thead>
            <tr>
                <th>Referrer Full Name</th>
                <th>Doctor License No</th>
                <th>Reason for Test</th>
                <th>Test Schedule</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $testDetail->referer_fullname }}</td>
                <td>{{ $testDetail->doctor_license_no }}</td>
                <td>{{ $testDetail->reason_for_test }}</td>
                <td>{{ \Carbon\Carbon::parse($testDetail->test_schedule)->format('m/d/Y')}}</td>
                <td>&#8369;{{ number_format($testDetail->total_price, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
