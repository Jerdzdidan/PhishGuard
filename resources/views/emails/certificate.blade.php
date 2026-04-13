<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #696cff 0%, #8592ff 100%);
            padding: 30px;
            text-align: center;
            color: #fff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .message {
            font-size: 15px;
            color: #555;
            margin-bottom: 20px;
        }
        .cert-details {
            background: #f8f9ff;
            border: 1px solid #e0e3ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .cert-details h3 {
            margin: 0 0 15px;
            color: #696cff;
            font-size: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eef0ff;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #888;
            font-size: 13px;
        }
        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .badge {
            display: inline-block;
            background: #696cff;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Certificate of Completion</h1>
            <p>CyberWais - Phishing Awareness Training</p>
        </div>

        <div class="content">
            <p class="greeting">Congratulations, {{ $user->first_name }}! 🎉</p>

            <p class="message">
                You have successfully completed all lessons in the CyberWais Phishing Awareness Training program. 
                Your certificate of completion is attached to this email as a PDF.
            </p>

            <div class="cert-details">
                <h3>📋 Certificate Details</h3>

                <div class="detail-row">
                    <span class="detail-label">Name</span>
                    <span class="detail-value">{{ $user->first_name }} {{ $user->last_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Certificate No.</span>
                    <span class="detail-value">{{ $certificate->certificate_number }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Issue Date</span>
                    <span class="detail-value">{{ $certificate->issued_at->format('F d, Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Lessons Completed</span>
                    <span class="detail-value">{{ $certificate->total_lessons_completed }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Average Quiz Score</span>
                    <span class="detail-value">{{ number_format($certificate->average_quiz_score, 2) }}%</span>
                </div>

                @if($certificate->average_simulation_score)
                <div class="detail-row">
                    <span class="detail-label">Average Simulation Score</span>
                    <span class="detail-value">{{ number_format($certificate->average_simulation_score, 2) }}%</span>
                </div>
                @endif
            </div>

            <p class="message">
                Keep up the great work in staying cyber-safe! Share your achievement and help spread awareness about phishing threats.
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message from CyberWais. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} CyberWais - Phishing Awareness Training</p>
        </div>
    </div>
</body>
</html>
