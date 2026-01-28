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
    max-width: 1100px;
    margin: 0 auto;
}

.certificate-preview {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.certificate-image {
    width: 100%;
    height: auto;
    border: 4px solid #1E7F5C;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}

.certificate-details {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #28c76f;
    border-radius: 12px;
    padding: 30px;
    margin-top: 30px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #666;
}

.detail-value {
    color: #333;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-download {
    flex: 1;
    padding: 15px;
    font-size: 16px;
    font-weight: 600;
}

.certificate-badge {
    display: inline-block;
    background: linear-gradient(135deg, #1E7F5C, #28c76f);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
}
</style>
@endsection

@section('content')
<div class="certificate-container">
    <div class="text-center mb-4">
        <span class="certificate-badge">
            <i class="ri-award-fill me-2"></i> CERTIFICATE OF COMPLETION
        </span>
        <h2 class="mb-2">Congratulations, {{ $user->first_name }}!</h2>
        <p class="text-muted">You've successfully completed the CyberWais Cybersecurity Training Program</p>
    </div>

    <div class="certificate-preview">
        <img src="{{ asset('img/certificate-preview.png') }}" alt="Certificate Preview" class="certificate-image" id="certificatePreview">
    </div>

    <div class="certificate-details">
        <h5 class="mb-3">
            <i class="ri-file-list-line me-2"></i> Certificate Details
        </h5>
        <div class="detail-row">
            <span class="detail-label">Certificate Number:</span>
            <span class="detail-value">{{ $certificate->certificate_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Recipient:</span>
            <span class="detail-value">{{ $user->first_name }} {{ $user->last_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Issue Date:</span>
            <span class="detail-value">{{ $certificate->issued_at->format('F d, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Lessons Completed:</span>
            <span class="detail-value">{{ $certificate->total_lessons_completed }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Average Quiz Score:</span>
            <span class="detail-value">{{ number_format($certificate->average_quiz_score, 2) }}%</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Average Simulation Score:</span>
            <span class="detail-value">{{ number_format($certificate->average_simulation_score, 2) }}%</span>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('certificate.download') }}" class="btn btn-success btn-download" target="_blank">
            <i class="ri-download-line me-2"></i> Download PDF Certificate
        </a>
        <a href="{{ route('user.home') }}" class="btn btn-label-secondary btn-download">
            <i class="ri-arrow-left-line me-2"></i> Back to Lessons
        </a>
    </div>

    <div class="alert alert-info mt-4">
        <i class="ri-information-line me-2"></i>
        <strong>Note:</strong> Your certificate is ready for download in PDF format (11" x 8.5" landscape). 
        You can print it or share it digitally to showcase your achievement in cybersecurity awareness!
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show success message on first view
    @if(session('certificate_earned'))
        Swal.fire({
            icon: 'success',
            title: 'Certificate Earned!',
            html: 'Congratulations on completing all lessons! Your certificate is now available.',
            confirmButtonColor: '#1E7F5C',
        });
    @endif
});
</script>
@endsection
