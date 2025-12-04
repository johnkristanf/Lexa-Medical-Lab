<!DOCTYPE html>
<html>
<head>
    <title>Test Results </title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
        }

        .info-table,
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 4px;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
            /* Slightly smaller */
        }

        .result-table th,
        .result-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            /* Slightly compressed */
            text-align: center;
        }

        .result-table th {
            background-color: #74c69d;
        }

        /* Optional: Force specific column widths */
        .result-table th:nth-child(1),
        .result-table td:nth-child(1) {
            width: 20%;
        }

        .result-table th:nth-child(2),
        .result-table td:nth-child(2) {
            width: 20%;
        }

        .result-table th:nth-child(3),
        .result-table td:nth-child(3) {
            width: 20%;
        }

        .result-table th:nth-child(4),
        .result-table td:nth-child(4) {
            width: 20%;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .footer .sig {
            width: 45%;
            text-align: center;
        }

        .footer .sig:first-child {
            text-align: left;
        }

        .footer .sig:last-child {
            text-align: right;
        }

        .remarks {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        @if(isset($logoBase64) && $logoBase64)
            <img src="{{ $logoBase64 }}" alt="Lexa Medical Laboratory Logo">
        @else
            <div style="height: 60px; margin-bottom: 10px; font-weight: bold; font-size: 16px; color: #74c69d;">
                LEXA MEDICAL LABORATORY
            </div>
        @endif
        <p><strong>CLINICAL LABORATORY DEPARTMENT</strong></p>
        <p><strong>{{ $testCategory->name}}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Name:</strong> {{ $patientDetails->first_name}}&nbsp;{{ $patientDetails->middle_name}}&nbsp;{{$patientDetails->last_name}}</td>
            <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($testDetail->test_schedule)->format('m/d/Y') }}</td>
        </tr>

        <tr>
            <td><strong>Gender:</strong> {{ $patientDetails->gender }}</td>
            <td><strong>OR Number:</strong> {{ $testDetail->or_number }}</td>
        </tr>
    </table>

    <table class="result-table">
        <thead>
            <tr>
                <th>TEST</th>
                <th>RESULTS</th>
                <th>REFERENCE VALUE</th>
                <th>UNIT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($testTypes as $testType)
            <tr>
                <td>{{ $testType->name ?? 'N/A' }}</td>
                <td>{{ $testType->pivot->results ?? 'N/A' }}</td>
                <td>
                    <pre class="whitespace-pre-wrap font-mono text-sm">{{ $testType->reference_range ?? 'N/A' }}</pre>

                </td>
                <td>{{ $testType->unit ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="remarks">
        <strong>Remarks:</strong> {{ $testDetail->remarks ?? '_______________________' }}
    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="text-decoration: underline;"><strong>JANE R. MOLDEZ, RMT</strong></p>
                    <p>Lic. No.: 0115085</p>
                    <p>Medical Technologist</p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="text-decoration: underline;"><strong>DR. OSCAR P. GRAGEDA, FSCP, APCP</strong></p>
                    <p>Lic. No.: 0047205</p>
                    <p>Pathologist</p>
                </td>
            </tr>
        </table>
    </div>


</body>

</html>
