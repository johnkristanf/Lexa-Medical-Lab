<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .header .icon {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #374151;
        }
        
        .success-message {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        
        .success-message p {
            margin: 0;
            font-size: 16px;
            color: #065f46;
            font-weight: 500;
        }
        
        .appointment-details {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        
        .appointment-details h2 {
            color: #047857;
            margin: 0 0 20px 0;
            font-size: 22px;
            font-weight: 700;
        }
        
        .detail-item {
            margin: 15px 0;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            border: 1px solid #d1fae5;
        }
        
        .detail-label {
            font-weight: 600;
            color: #047857;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .appointment-number {
            font-size: 24px !important;
            color: #059669 !important;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px dashed #10b981;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        
        .schedule-time {
            font-size: 20px !important;
            color: #047857 !important;
            background-color: #f0fdf4;
            padding: 8px 16px;
            border-radius: 6px;
        }
        
        .important-notice {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .important-notice h3 {
            color: #92400e;
            margin: 0 0 15px 0;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        
        .important-notice h3::before {
            content: "⚠️";
            margin-right: 8px;
            font-size: 20px;
        }
        
        .important-notice ul {
            margin: 15px 0;
            padding-left: 0;
            list-style: none;
        }
        
        .important-notice li {
            background-color: white;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 6px;
            border-left: 3px solid #f59e0b;
            position: relative;
            padding-left: 40px;
        }
        
        .important-notice li::before {
            content: "✓";
            position: absolute;
            left: 15px;
            top: 12px;
            color: #059669;
            font-weight: bold;
            font-size: 16px;
        }
        
        .footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .thank-you {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
        }
        
        .message-text {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #166534;
            font-style: italic;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            .appointment-details {
                padding: 20px 15px;
            }
            
            .appointment-number {
                font-size: 20px !important;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Appointment Confirmation</h1>
        </div>
        
        <!-- Content -->
        <div class="content">
            <p class="greeting">Dear {{ $data['email'] }},</p>
            
            <div class="success-message">
                <p>✅ Your appointment has been successfully booked!</p>
            </div>
            
            <!-- Appointment Details -->
            <div class="appointment-details">
                <h2>📋 Appointment Details</h2>
                
                <div class="detail-item">
                    <div class="detail-label">Appointment Number</div>
                    <div class="detail-value appointment-number">{{ $data['appointment_number'] }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Scheduled Date & Time</div>
                    <div class="detail-value schedule-time">{{ $data['schedule'] }}</div>
                </div>
                
                @if(isset($data['message']) && $data['message'])
                <div class="message-text">
                    {{ $data['message'] }}
                </div>
                @endif
            </div>
            
            <!-- Important Instructions -->
            <div class="important-notice">
                <h3>Important Instructions - Please Read Carefully</h3>
                <ul>
                    <li><strong>Save this confirmation:</strong> Take a screenshot or note down your appointment code for your records.</li>
                    <li><strong>Required documents:</strong> Bring a valid Government-issued ID and your VACCINATION CARD.</li>
                    <li><strong>Appointment code:</strong> Have your appointment code ready upon arrival at the facility.</li>
                    <li><strong>Health protocol:</strong> Face mask is mandatory - No face mask, NO ENTRY.</li>
                    <li><strong>Arrival time:</strong> Please arrive at least 30 minutes before your scheduled appointment time.</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="thank-you">
            🙏 Thank you for choosing our services!
        </div>
        
        <div class="footer">
            <p>If you have any questions or need to reschedule, please contact us immediately.</p>
            <p style="margin-top: 10px; font-size: 12px; color: #9ca3af;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>