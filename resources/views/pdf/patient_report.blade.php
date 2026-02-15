<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24pt;
            color: #15803d;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #16a34a;
        }

        .report-info {
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .report-info span {
            font-weight: bold;
            color: #15803d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #16a34a;
            color: white;
        }

        thead th {
            padding: 10px 6px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #16a34a;
        }

        tbody td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            font-size: 8.5pt;
        }

        .remarks {
            margin-top: 65px;
            margin-left: 35px;
            font-size: 15px
        }

        tbody tr:nth-child(even) {
            background-color: #f0fdf4;
        }

        tbody tr:hover {
            background-color: #dcfce7;
        }

        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .total-count {
            margin-top: 15px;
            padding: 10px;
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        @if (isset($logoLexa) && $logoLexa)
            <img src="{{ $logoLexa }}" alt="Lexa Medical Laboratory Logo">
        @else
            <div style="height: 60px; margin-bottom: 10px; font-weight: bold; font-size: 16px; color: #74c69d;">
                Medical Laboratory
            </div>
        @endif
        <h1>Patient Report</h1>
        <p>Complete Patient Registry</p>
    </div>

    <div class="report-info">
        <span>Generated:</span> {{ date('F d, Y h:i A') }} |
        <span>Total Patients:</span> {{ count($patients) }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Patient ID</th>
                <th style="width: 15%;">Full Name</th>
                <th style="width: 6%;">Gender</th>
                <th style="width: 10%;">Date of Birth</th>
                <th style="width: 20%;">Address</th>
                <th style="width: 12%;">Contact</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 8%;">Registered</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($patients as $patient)
                <tr>
                    <td>{{ $patient->patient_id }}</td>
                    <td>
                        {{ $patient->first_name }}
                        @if ($patient->middle_name)
                            {{ substr($patient->middle_name, 0, 1) }}.
                        @endif
                        {{ $patient->last_name }}
                    </td>
                    <td>{{ $patient->gender }}</td>
                    <td>{{ date('m/d/Y', strtotime($patient->date_of_birth)) }}</td>
                    <td>{{ $patient->address ?? 'N/A' }}</td>
                    <td>{{ $patient->contact_number ?? 'N/A' }}</td>
                    <td style="font-size: 8pt;">{{ $patient->email ?? 'N/A' }}</td>
                    <td>{{ date('m/d/Y', strtotime($patient->created_at)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-count">
        Total Number of Patients: {{ count($patients) }}
    </div>

    <div style="margin-top: 50px; page-break-inside: avoid;">
        <p style="font-weight: bold;">Prepared by:</p>
        <div style="margin-top: 30px; border-bottom: 1px solid #333; width: 200px;"></div>
    </div>

    <div class="footer">
        <p>This is a computer-generated report. Page {{ $loop->iteration ?? '1' }} | Confidential Patient Information
        </p>
    </div>
</body>

</html>
