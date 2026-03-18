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
            margin-bottom: 5px;
        }

        .header h2 {
            margin: 0;
        }

        .info-table,
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .info-table td {
            padding: 2px;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .result-table th,
        .result-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }

        .result-table pre {
            margin: 0;
            padding: 0;
            font-family: inherit;
        }

        .result-table tr {
            page-break-inside: avoid;
        }

        /* Applied when a category has 6 or more test rows */
        .compact-table th,
        .compact-table td {
            padding: 2px 2px;
            font-size: 10px;
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
            margin-top: 20px;
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

    @foreach ($categoriesData as $index => $data)
        @if ($index > 0)
            <div style="page-break-before: always;"></div>
        @endif

        <div class="header">
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" alt="Lexa Medical Laboratory Logo">
            @else
                <div style="height: 60px; margin-bottom: 10px; font-weight: bold; font-size: 16px; color: #74c69d;">
                    LEXA MEDICAL LABORATORY
                </div>
            @endif
            <p><strong>CLINICAL LABORATORY DEPARTMENT</strong></p>
            <p><strong>{{ $data['name'] }}</strong></p>
        </div>

        <table class="info-table">
            <tr>
                <td><strong>Name:</strong> {{ $patientDetails->first_name}}&nbsp;{{ $patientDetails->middle_name}}&nbsp;{{$patientDetails->last_name}}</td>
                <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($testDetail->test_schedule)->format('m/d/Y') }}</td>
            </tr>

            <tr>
                <td><strong>Gender:</strong> {{ $patientDetails->gender }}</td>
                <td><strong>Referrers Name:</strong> {{ $testDetail->or_number }}</td>
            </tr>
        </table>

        @php $isCompact = count($data['tests']) >= 6; @endphp
        <table class="result-table {{ $isCompact ? 'compact-table' : '' }}">
            <thead>
                <tr>
                    <th>TEST</th>
                    <th>RESULTS</th>
                    <th>REFERENCE VALUE</th>
                    <th>UNIT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['tests'] as $testType)
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
                        <p style="text-decoration: underline;"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
                        <p></p>
                        <p>Medical Technologist</p>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: bottom;">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/signature.png'))) }}" style="display: block; margin: 0 auto -30px auto; height: 50px; position: relative; z-index: 0;" alt="Signature">
                        <p style="text-decoration: underline; position: relative; z-index: 1;"><strong>Dr. Oscar P. Grageda, FPSP, APCP</strong></p>
                        <p>Lic. No.: 0047205</p>
                        <p>Pathologist</p>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach


</body>

</html>
