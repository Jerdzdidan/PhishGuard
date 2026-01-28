@extends('user.layout.base')

@section('title')
MY CERTIFICATE
@endsection

@section('nav_title')
MY CERTIFICATE
@endsection

@section('style')
<style>
.certificate-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0;
}

.certificate {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 20px solid;
    border-image: linear-gradient(135deg, #1E7F5C, #28c76f) 1;
    padding: 60px;
    position: relative;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
}

.certificate::before {
    content: '';
    position: absolute;
    top: 30px;
    left: 30px;
    right: 30px;
    bottom: 30px;
    border: 2px solid #1E7F5C;
    pointer-events: none;
}

.certificate-header {
    text-align: center;
    margin-bottom: 40px;
}

.certificate-logo {
    font-size: 60px;
    margin-bottom: 20px;
}

.certificate-title {
    font-size: 48px;
    font-weight: 700;
    color: #1E7F5C;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.certificate-subtitle {
    font-size: 20px;
    color: #666;
    font-weight: 400;
}

.certificate-body {
    text-align: center;
    margin: 40px 0;
}

.certificate-text {
    font-size: 18px;
    color: #333;
    line-height: 1.8;
    margin-bottom: 30px;
}

.recipient-name {
    font-size: 42px;
    font-weight: 700;
    color: #1E7F5C;
    margin: 30px 0;
    text-transform: uppercase;
    border-bottom: 3px solid #1E7F5C;
    display: inline-block;
    padding-bottom: 10px;
}

.certificate-details {
    display: flex;
    justify-content: space-around;
    margin: 40px 0;
    padding: 30px 0;
    border-top: 2px solid #e0e0e0;
    border-bottom: 2px solid #e0e0e0;
}

.detail-item {
    text-align: center;
}

.detail-label {
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.detail-value {
    font-size: 20px;
    font-weight: 600;
    color: #1E7F5C;
}

.certificate-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 60px;
    padding-top: 30px;
}

.signature-section {
    text-align: center;
    flex: 1;
}

.signature-line {
    border-top: 2px solid #333;
    margin: 0 20px 10px 20px;
    padding-top: 10px;
}

.signature-title {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.signature-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.certificate-number {
    text-align: center;
    margin-top: 30px;
    font-size: 12px;
    color: #999;
    font-family: 'Courier New', monospace;
}

.action-buttons {
    text-align: center;
    margin-top: 40px;
}

@media print {
    .action-buttons,
    .navbar,
    footer {
        display: none !important;
    }
    
    .certificate-container {
        margin: 0;
        max-width: 100%;
    }
    
    .certificate {
        box-shadow: none;
        page-break-inside: avoid;
    }
}
</style>
@endsection

@section('content')
<div class="certificate-container">
    <div class="certificate">
        <div class="certificate-header">
            <div class="certificate-logo">🛡️</div>
            <h1 class="certificate-title">Certificate of Completion</h1>
            <p class="certificate-subtitle">Cybersecurity Awareness Training</p>
        </div>

        <div class="certificate-body">
            <p class="certificate-text">This is to certify that</p>
            
            <div class="recipient-name">
                {{ $user->first_name }} {{ $user->last_name }}
            </div>

            <p class="certificate-text">
                has successfully completed the comprehensive<br>
                <strong>Cybersecurity Awareness Training Program</strong><br>
                demonstrating proficiency in identifying and responding to cyber threats
            </p>

            <div class="certificate-details">
                <div class="detail-item">
                    <div class="detail-label">Lessons Completed</div>
                    <div class="detail-value">{{ $certificate->total_lessons_completed }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Quiz Average</div>
                    <div class="detail-value">{{ $certificate->average_quiz_score ? number_format($certificate->average_quiz_score, 1) . '%' : 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Simulation Average</div>
                    <div class="detail-value">{{ $certificate->average_simulation_score ? number_format($certificate->average_simulation_score, 1) . '%' : 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date Issued</div>
                    <div class="detail-value">{{ $certificate->issued_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="certificate-number">
            Certificate No: {{ $certificate->certificate_number }}
        </div>
    </div>

    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary btn-lg me-2">
            <i class="ri-printer-line me-2"></i> Print Certificate
        </button>
        <a href="{{ route('user.home') }}" class="btn btn-label-secondary btn-lg">
            <i class="ri-arrow-left-line me-2"></i> Back to Home
        </a>
    </div>
</div>
@endsection