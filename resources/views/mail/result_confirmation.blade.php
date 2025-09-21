<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Medical Test Result Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f0f9f0;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(34, 139, 34, 0.1);
            border: 2px solid #e8f5e8;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #228b22 0%, #32cd32 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header-icon {
            width: 48px;
            height: 48px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .header-icon svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .greeting-section {
            background-color: #f0f9f0;
            border-left: 4px solid #228b22;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
        }

        .greeting-section h5 {
            font-size: 20px;
            font-weight: bold;
            color: #1f5f1f;
            margin-bottom: 10px;
        }

        .greeting-section p {
            color: #2d5a2d;
            line-height: 1.7;
        }

        .instructions-section {
            background-color: #ffffff;
            border: 1px solid #d1e7d1;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .instructions-header {
            font-weight: 600;
            color: #1f5f1f;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .instructions-header svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            fill: currentColor;
        }

        .instructions-section p {
            color: #374151;
            line-height: 1.7;
        }

        .thank-you {
            text-align: center;
            margin: 20px 0;
        }

        .thank-you p {
            color: #2d5a2d;
            font-weight: 500;
        }

        .clinic-info {
            background: linear-gradient(135deg, #228b22 0%, #32cd32 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
        }

        .clinic-info h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .clinic-info h3 svg {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            fill: currentColor;
        }

        .contact-info {
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 10px;
        }

        .contact-info svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            fill: currentColor;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 20px;
            }

            .header {
                padding: 25px 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .greeting-section h5 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ 'Medical Test Result Available' }}</h1>
            <p class="subtitle">{{ 'Ready for Collection' }}</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <div class="greeting-section">
                <h5>{{ 'Greetings for the result reminder!' }}</h5>
                <p>
                    {{ 'We are pleased to inform you that your medical test result is now available.' }}
                    <br><br>
                    {{ 'Kindly proceed to retrieve your result and report to work as required.' }}
                </p>
            </div>

            <div class="instructions-section">
                <h6 class="instructions-header">
                    <svg viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Important Instructions
                </h6>
                <p>
                    Please ensure you bring a valid ID when claiming your result,<br>
                    instructions provided and follow any additional<br>
                    by your employer or supervisor.<br><br>
                    If you have any questions or need assistance, feel free to contact us.
                </p>
            </div>

            <div class="thank-you">
                <p>Thank you.</p>
            </div>

            {{-- Clinic Information --}}
            <div class="clinic-info">
                <h3>
                    <svg viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1h-6a1 1 0 01-1-1V8z" clip-rule="evenodd"></path>
                    </svg>
                    {{ 'Lexa Medical Laboratory' }}
                </h3>
                <div class="contact-info">
                    <svg viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                    </svg>
                    <span>{{ '+63-917-1234-5678' }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
