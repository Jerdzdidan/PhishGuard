{{-- 
    Certificate Preview Component
    Renders the certificate in HTML/CSS with the student's name imprinted.
    
    Props:
    - $userName: Full name of the student
    - $certificate: Certificate model instance (optional, for details)
    - $compact: boolean, if true renders a smaller version (for dashboard)
--}}

@props([
    'userName',
    'certificate' => null,
    'compact' => false,
])

@php
    $wrapperClass = $compact ? 'certificate-render certificate-render--compact' : 'certificate-render';
@endphp

<div class="{{ $wrapperClass }}">
    <div class="cert-outer-frame">
        <div class="cert-inner-frame">
            {{-- Top logos and branding --}}
            <div class="cert-header">
                <div class="cert-logo-group">
                    <img src="{{ asset('img/landing/logo.png') }}" alt="CyberWais Logo" class="cert-logo-img" />
                    <span class="cert-brand-name">CYBERWAIS</span>
                    <img src="{{ asset('img/landing/logo.png') }}" alt="CyberWais Shield" class="cert-logo-img" />
                </div>
            </div>

            {{-- Title --}}
            <div class="cert-title-block">
                <h1 class="cert-title-main">CERTIFICATE</h1>
                <h2 class="cert-title-sub">OF COMPLETION</h2>
            </div>

            {{-- Presents to --}}
            <p class="cert-presents">PROUDLY PRESENTS TO</p>

            {{-- Student Name --}}
            <div class="cert-name-area">
                <h2 class="cert-student-name">{{ $userName }}</h2>
                <div class="cert-name-underline"></div>
            </div>

            {{-- Body text --}}
            <p class="cert-body-text">
                for having successfully completed all the requirements of this cybersecurity learning
                program. This certificate is awarded in recognition of the recipient's commitment to
                developing foundational knowledge and practical awareness in cybersecurity principles,
                best practices, and responsible digital conduct. It serves as an acknowledgment of the
                skills and understanding acquired through active participation and successful completion
                of the program.
            </p>

            {{-- Certificate details (only if certificate model is provided) --}}
            @if($certificate)
            <div class="cert-details-row">
                <span class="cert-detail">Certificate No.: {{ $certificate->certificate_number }}</span>
                <span class="cert-detail-sep">•</span>
                <span class="cert-detail">Issued: {{ $certificate->issued_at->format('F d, Y') }}</span>
                <span class="cert-detail-sep">•</span>
                <span class="cert-detail">{{ $certificate->total_lessons_completed }} Lessons</span>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* ═══════════════════════════════════════════════════════
   Certificate Render Component Styles
   ═══════════════════════════════════════════════════════ */
.certificate-render {
    --cert-primary: #1a5c45;
    --cert-accent: #1E7F5C;
    --cert-bg-dark: #0d2b3e;
    --cert-bg-accent: #14473a;
    --cert-text-body: #3a3a3a;
    --cert-gold: #c9a84c;
    width: 100%;
    aspect-ratio: 11 / 8.5;
    font-family: 'Georgia', 'Times New Roman', serif;
    user-select: none;
}

.cert-outer-frame {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0a2a3c 0%, #0d3a32 30%, #0a2a3c 70%, #0d3a32 100%);
    border-radius: 4px;
    padding: 3%;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

/* Subtle circuit-board pattern overlay */
.cert-outer-frame::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 5% 10%, rgba(30, 127, 92, 0.15) 0%, transparent 30%),
        radial-gradient(circle at 95% 10%, rgba(30, 127, 92, 0.15) 0%, transparent 30%),
        radial-gradient(circle at 5% 90%, rgba(30, 127, 92, 0.15) 0%, transparent 30%),
        radial-gradient(circle at 95% 90%, rgba(30, 127, 92, 0.15) 0%, transparent 30%),
        radial-gradient(circle at 50% 0%, rgba(100, 200, 200, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

/* Corner decorations */
.cert-outer-frame::after {
    content: '';
    position: absolute;
    inset: 8px;
    border: 1px solid rgba(30, 127, 92, 0.3);
    border-radius: 2px;
    pointer-events: none;
}

.cert-inner-frame {
    width: 100%;
    height: 100%;
    background: #ffffff;
    border: 2px solid rgba(30, 127, 92, 0.2);
    border-radius: 2px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 5% 8%;
    position: relative;
}

/* Inner decorative border */
.cert-inner-frame::before {
    content: '';
    position: absolute;
    inset: 6px;
    border: 1px solid rgba(30, 127, 92, 0.08);
    pointer-events: none;
}

/* Header logos */
.cert-header {
    margin-bottom: 2%;
}

.cert-logo-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cert-logo-img {
    width: 42px;
    height: 42px;
    object-fit: contain;
}

.cert-brand-name {
    font-family: 'Courier New', Courier, monospace;
    font-size: clamp(14px, 2vw, 22px);
    font-weight: 700;
    letter-spacing: 6px;
    color: var(--cert-primary);
}

/* Title */
.cert-title-block {
    text-align: center;
    margin-bottom: 1%;
    line-height: 1;
}

.cert-title-main {
    font-family: 'Courier New', Courier, monospace;
    font-size: clamp(28px, 5.5vw, 64px);
    font-weight: 900;
    color: var(--cert-accent);
    letter-spacing: 8px;
    margin: 0;
    text-shadow: 2px 2px 0 rgba(30, 127, 92, 0.1);
    line-height: 1.1;
}

.cert-title-sub {
    font-family: 'Courier New', Courier, monospace;
    font-size: clamp(16px, 3vw, 38px);
    font-weight: 400;
    color: var(--cert-accent);
    letter-spacing: 10px;
    margin: 0;
    line-height: 1.3;
}

.cert-presents {
    font-size: clamp(9px, 1.2vw, 14px);
    letter-spacing: 4px;
    color: #666;
    margin: 1.5% 0 2%;
    font-weight: 400;
}

/* Student Name */
.cert-name-area {
    text-align: center;
    margin-bottom: 3%;
    min-height: 2em;
}

.cert-student-name {
    font-family: 'Georgia', serif;
    font-size: clamp(20px, 3.5vw, 42px);
    font-weight: 700;
    color: var(--cert-accent);
    margin: 0 0 4px;
    letter-spacing: 1px;
    line-height: 1.2;
}

.cert-name-underline {
    width: 60%;
    max-width: 350px;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--cert-accent), transparent);
    margin: 0 auto;
}

/* Body text */
.cert-body-text {
    font-size: clamp(8px, 1.1vw, 13px);
    color: var(--cert-text-body);
    text-align: center;
    line-height: 1.7;
    max-width: 85%;
    margin: 0 auto 3%;
}

/* Details row */
.cert-details-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.cert-detail {
    font-size: clamp(7px, 0.9vw, 11px);
    color: #666;
    font-family: 'Helvetica', 'Arial', sans-serif;
}

.cert-detail-sep {
    color: #ccc;
    font-size: 8px;
}

/* ── Compact variant (for dashboard) ── */
.certificate-render--compact {
    max-width: 100%;
    border-radius: 12px;
    overflow: hidden;
}

.certificate-render--compact .cert-outer-frame {
    border-radius: 12px;
}

/* ── Responsive adjustments ── */
@media (max-width: 768px) {
    .cert-logo-img {
        width: 28px;
        height: 28px;
    }
    
    .cert-brand-name {
        letter-spacing: 3px;
    }
    
    .cert-title-main {
        letter-spacing: 4px;
    }
    
    .cert-title-sub {
        letter-spacing: 5px;
    }
    
    .cert-presents {
        letter-spacing: 2px;
    }
}
</style>
